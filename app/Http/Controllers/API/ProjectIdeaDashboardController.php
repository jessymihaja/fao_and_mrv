<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\ProjectIdea;
use Illuminate\Support\Facades\DB;

class ProjectIdeaDashboardController extends Controller
{
    public function index()
    {
        $statuts = ['brouillon', 'soumis', 'en_etude', 'approuve', 'converti'];
        $statutLabels = [
            'brouillon' => 'Brouillon',
            'soumis' => 'Soumis',
            'en_etude' => 'En étude',
            'approuve' => 'Approuvé',
            'converti' => 'Converti'
        ];

        // 1. Décompte par statut
        $rawParStatut = ProjectIdea::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();

        $parStatut = [];
        $repartitionStatut = [];
        foreach ($statuts as $st) {
            $count = $rawParStatut[$st] ?? 0;
            $parStatut[$st] = $count;
            $repartitionStatut[] = [
                'statut' => $statutLabels[$st],
                'total' => $count
            ];
        }

        // 2. Budget global cumulé
        $budgetTotalEstime = (float) ProjectIdea::sum('budget_total_estime');

        // 3. Budget par Secteur
        $budgetParSecteur = DB::table('project_idea_secteur')
            ->join('secteurs', 'project_idea_secteur.secteur_id', '=', 'secteurs.id')
            ->join('project_ideas', 'project_idea_secteur.project_idea_id', '=', 'project_ideas.id')
            ->select('secteurs.designation as secteur', DB::raw('SUM(project_ideas.budget_total_estime) as montant'))
            ->groupBy('secteurs.designation')
            ->get()
            ->map(fn($item) => ['secteur' => $item->secteur, 'montant' => (float) $item->montant]);

        // 4. Budget par Bailleur
        $budgetParBailleur = DB::table('project_idea_financements')
            ->select('bailleur', DB::raw('SUM(montant_demande) as montant'))
            ->groupBy('bailleur')
            ->get()
            ->map(fn($item) => ['bailleur' => $item->bailleur, 'montant' => (float) $item->montant]);

        // 5. Répartition par Région
        $repartitionRegion = DB::table('project_ideas')
            ->leftJoin('regions', 'project_ideas.region_id', '=', 'regions.id')
            ->select(DB::raw('COALESCE(regions.nom, \'Non spécifié\') as region'), DB::raw('count(*) as total'))
            ->groupBy('region')
            ->get()
            ->map(fn($item) => ['region' => $item->region, 'total' => (int) $item->total]);

        return response()->json([
            'total' => array_sum($parStatut),
            'par_statut' => $parStatut,
            'par_statut_labels' => $statutLabels,
            'budget_total_estime' => $budgetTotalEstime,
            'budget_par_secteur' => $budgetParSecteur,
            'budget_par_bailleur' => $budgetParBailleur,
            'repartition_region' => $repartitionRegion,
            'repartition_statut' => $repartitionStatut,
        ]);
    }
}