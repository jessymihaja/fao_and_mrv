<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RapportNational;
use App\Models\Projet;
use App\Models\Region;
use App\Models\Secteur;
use App\Models\DomaineIntervention;
use App\Models\EntiteAccreditee;
use App\Models\BudgetApprobation;
use App\Models\BudgetEngagement;
use App\Models\BudgetDecaissement;
use App\Models\Financement;
use Illuminate\Http\Request;
use App\Exports\RapportNationalExport;

class RapportNationalController extends Controller
{
    public function index(Request $request)
    {
        $query = RapportNational::query();

        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('search')) {
            $query->where('titre', 'like', '%' . $request->search . '%');
        }

        $rapports = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($rapports);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'annee' => 'nullable|integer',
            'region_id' => 'nullable|exists:regions,id',
            'secteur_climatique' => 'nullable|string',
            'accredited_entity' => 'nullable|string',
            'source_financement' => 'nullable|string',
            'statut_projet' => 'nullable|string',
            'statut' => 'in:brouillon,genere,publie',
            'contenu' => 'nullable|array',
        ]);

        $validated['created_by'] = auth()->id();

        $rapport = RapportNational::create($validated);

        return response()->json($rapport, 201);
    }

    public function show($id)
    {
        $rapport = RapportNational::findOrFail($id);
        return response()->json($rapport);
    }

    /**
     * Génère dynamiquement le contenu aggregé du rapport national
     */
public function generate($id)
{
    $rapport = RapportNational::findOrFail($id);

    // 1. Filtrage dynamique selon les critères du rapport
    $query = Projet::query();

    // Filtre sur l'année (basé sur date_debut)
    if ($rapport->annee) {
        $query->whereYear('date_debut', $rapport->annee);
    }

    // Filtre sur la région (clé étrangère directe region_id)
    if ($rapport->region_id) {
        $query->where('region_id', $rapport->region_id);
    }

    // Filtre sur le secteur / domaine d'intervention (Relation BelongsToMany)
    if ($rapport->secteur_climatique) {
        $query->whereHas('domainesIntervention', function ($q) use ($rapport) {
            $q->where('designation', 'like', '%' . $rapport->secteur_climatique . '%');
        });
    }

    // Filtre sur l'entité accréditée (Relation BelongsToMany)
    if ($rapport->accredited_entity) {
        $query->whereHas('entiteAccreditees', function ($q) use ($rapport) {
            $q->where('designation', 'like', '%' . $rapport->accredited_entity . '%');
        });
    }

    // Filtre sur le statut du projet
    if ($rapport->statut_projet) {
        $query->whereHas('status', function ($q) use ($rapport) {
            $q->where('code', $rapport->statut_projet)
              ->orWhere('libelle', $rapport->statut_projet);
        });
    }

    // Charge les projets avec leurs relations et leurs financements
    $projets = $query->with(['domainesIntervention', 'entiteAccreditees', 'region', 'financements'])->get();
    $projetIds = $projets->pluck('id_projet')->toArray();

    // 2. Calculs Financiers (depuis la relation financements)
    // 1. Récupération des IDs de financements via la clé primaire 'id'
        $financementIds = Financement::whereIn('project_id', $projetIds)->pluck('id');

        // 2. Calcul des sommes budgétaires liées
        $budgetApprouve = BudgetApprobation::whereIn('financement_id', $financementIds)->sum('montant_approuve');
        $budgetEngage   = BudgetEngagement::whereIn('financement_id', $financementIds)->sum('montant');
        $budgetDecaisse = BudgetDecaissement::whereIn('financement_id', $financementIds)->sum('montant');

    // Fallback sur la relation financements si pas de tables de budgets dédiées
    if ($budgetApprouve == 0) {
        $budgetApprouve = Financement::whereIn('project_id', $projetIds)->sum('budget_approuve');
    }

    $tauxExecutionGlobal = $budgetApprouve > 0 ? round(($budgetDecaisse / $budgetApprouve) * 100, 2) : 0;

    // Répartition Financière par Domaines d'Intervention (Secteurs)
    $secteursData = [];
    foreach ($projets as $p) {
        $montantProjet = $p->financements->sum('budget_approuve');
        foreach ($p->domainesIntervention as $domaine) {
            $nomDomaine = $domaine->nom ?? 'Non défini';
            $secteursData[$nomDomaine] = ($secteursData[$nomDomaine] ?? 0) + $montantProjet;
        }
    }

    $parSecteur = collect($secteursData)->map(function ($montant, $secteur) {
        return ['secteur' => $secteur, 'montant' => (float)$montant];
    })->values();

    // Répartition Financière par Région
    $parRegion = $projets->groupBy(function ($p) {
        return $p->region->nom ?? 'Non renseigné';
    })->map(function ($group, $regionNom) {
        $sum = $group->sum(function ($p) {
            return $p->financements->sum('budget_approuve');
        });
        return ['region' => $regionNom, 'montant' => (float)$sum];
    })->values();

    // Top Projets
    $topProjets = $projets->map(function ($p) {
        $total = $p->financements->sum('budget_approuve');
        return [
            'titre' => $p->titre,
            'budget' => (float)$total,
            'taux' => 0,
        ];
    })->sortByDesc('budget')->take(5)->values();

    // 3. Assemblage du contenu
    $contenu = [
        'resume' => [
            'total_projets' => $projets->count(),
            'budget_total_approuve' => (float)$budgetApprouve,
            'budget_engage' => (float)$budgetEngage,
            'budget_decaisse' => (float)$budgetDecaisse,
            'taux_execution_global' => (float)$tauxExecutionGlobal,
        ],
        'financier' => [
            'par_secteur' => $parSecteur,
            'par_region' => $parRegion,
            'top_projets' => $topProjets,
        ],
            'physique' => [
                'total_beneficiaires' => (int)$projets->sum('nb_beneficiaires'),
                'infrastructures_realisees' => (int)$projets->sum('nb_infrastructures'),
                'surfaces_restaurees' => (float)$projets->sum('surface_restauree'),
                'formations_realisees' => (int)$projets->sum('nb_formations'),
            ],
            'climatique' => [
                'adaptation' => [
                    'population_resiliente' => (int)$projets->sum('pop_resiliente'),
                    'reduction_vulnerabilite' => (float)($projets->avg('score_reduction_vulnerabilite') ?? 0),
                    'systemes_alerte' => (int)$projets->sum('nb_systemes_alerte'),
                ],
                'attenuation' => [
                    'co2_evite' => (float)$projets->sum('co2_evite_tonnes'),
                    'energie_renouvelable' => (float)$projets->sum('energie_renouvelable_kwh'),
                    'reduction_energetique' => (float)$projets->sum('economie_energie_kwh'),
                ],
            ],
        ];

        // 4. Sauvegarde
        $rapport->update([
            'contenu' => $contenu,
            'statut' => 'genere',
        ]);

        return response()->json($rapport);
    }

    public function destroy($id)
    {
        $rapport = RapportNational::findOrFail($id);
        $rapport->delete();

        return response()->json(['message' => 'Rapport supprimé avec succès']);
    }

    public function exportPdf($id)
{
    $rapport = RapportNational::findOrFail($id);

    if (!$rapport->contenu) {
        return response()->json(['message' => 'Veuillez générer le rapport avant de l\'exporter'], 400);
    }

    return view('pdf.rapport_national_print', compact('rapport'));
}

    public function exportExcel($id)
{
    $rapport = RapportNational::findOrFail($id);

    if (!$rapport->contenu) {
        return response()->json(['message' => 'Veuillez générer le rapport avant de l\'exporter'], 400);
    }

    $fileName = "rapport_national_{$rapport->id}.csv";
    $resume = $rapport->contenu['resume'] ?? [];

    $headers = [
        "Content-type" => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $callback = function () use ($rapport, $resume) {
        $file = fopen('php://output', 'w');
        // Ajout du BOM UTF-8 pour qu'Excel gère correctement les accents
        fputs($file, "\xEF\xBB\xBF");

        fputcsv($file, ['Metrique', 'Valeur'], ';');
        fputcsv($file, ['Titre du rapport', $rapport->titre], ';');
        fputcsv($file, ['Annee', $rapport->annee ?? 'Toutes'], ';');
        fputcsv($file, ['Total Projets', $resume['total_projets'] ?? 0], ';');
        fputcsv($file, ['Budget Total Approuve', $resume['budget_total_approuve'] ?? 0], ';');
        fputcsv($file, ['Budget Engage', $resume['budget_engage'] ?? 0], ';');
        fputcsv($file, ['Budget Decaisse', $resume['budget_decaisse'] ?? 0], ';');

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}