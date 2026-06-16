<?php

namespace App\Http\Controllers\API;

use App\Models\Decaissement;
use App\Models\Depense;
use App\Models\Document;
use App\Models\Engagement;
use App\Models\Financement;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  // Gardé uniquement pour updateManual (public_settings sans modèle)
use App\Http\Controllers\Controller;

class StatsController extends Controller
{
    // ── PUBLIC ────────────────────────────────────────────────────────────────
    public function public(): JsonResponse
    {
        return response()->json([
            'total_projets'      => Projet::where('is_published', true)->count(),
            'projets_actifs'     => Projet::where('is_published', true)->where('status_id', 1)->count(),
            'projets_termines'   => Projet::where('is_published', true)->where('status_id', 2)->count(),
            'projets_planifies'  => Projet::where('is_published', true)->where('status_id', 3)->count(),
            'projets_suspendus'  => 0,
            'budget_total'       => (float) Financement::sum(DB::raw('montant_mga * budget_approuve')),
            'total_financements' => Financement::count(),
        ]);
    }

    // ── ADMIN GLOBAL ──────────────────────────────────────────────────────────
    public function global(): JsonResponse
    {
        return response()->json([
            'total_projets'       => Projet::count(),
            'projets_actifs'      => Projet::where('status_id', 1)->count(),
            'projets_termines'    => Projet::where('status_id', 2)->count(),
            'projets_planifies'   => Projet::where('status_id', 3)->count(),
            'projets_suspendus'   => Projet::where('status_id', 4)->count(),
            'budget_total' => (float) Financement::sum(DB::raw('montant_mga * budget_approuve')),
            'budget_usd'          => (float) Financement::where('devise', 'USD')->sum('budget_approuve'),
            'budget_eur'          => (float) Financement::where('devise', 'EUR')->sum('budget_approuve'),
            'budget_ar'           => (float) Financement::where('devise', 'AR')->sum('budget_approuve'),
            'total_financements'  => Financement::count(),
            'total_documents'     => Document::count(),
            'total_users'         => User::count(),
            'total_depenses'      => (float) Depense::sum('montant'),
            'total_engagements'   => (float) Engagement::sum('montant'),
            'total_decaissements' => (float) Decaissement::sum('montant'),
        ]);
    }

    // ── PROJETS PAR STATUT ────────────────────────────────────────────────────
    // Eloquent applique automatiquement whereNull('deleted_at') via SoftDeletes.
    public function projectsByStatus(): JsonResponse
    {
        $statuts = [1, 2, 3, 4];
        $status_names = [
            1 => 'en cours',
            2 => 'cloturés',
            3 => 'Concept Note',
            4 => 'funding proposal',
        ];

        $counts = Projet::selectRaw('status_id, COUNT(*) AS nb')
            ->groupBy('status_id')
            ->pluck('nb', 'status_id')
            ->toArray();

        $data = collect($statuts)
            ->map(fn ($s) => ['name' => $status_names[$s] ?? "Inconnu", 'value' => (int) ($counts[$s] ?? 0)])
            ->filter(fn ($r) => $r['value'] > 0)
            ->values();

        return response()->json($data);
    }

    // ── BUDGET PAR ANNÉE ──────────────────────────────────────────────────────
    // Financement n'a PAS SoftDeletes → pas de deleted_at dans la table.
    // Eloquent::selectRaw fonctionne parfaitement pour les agrégats PostgreSQL.
    public function budgetByYear(): JsonResponse
    {
        $rows = Financement::selectRaw("
                EXTRACT(YEAR FROM date_approbation)::int                            AS annee,
                SUM(montant_mga)                                                    AS mga,
                SUM(CASE WHEN devise = 'USD' THEN budget_approuve ELSE 0 END)       AS usd,
                SUM(CASE WHEN devise = 'EUR' THEN budget_approuve ELSE 0 END)       AS eur
            ")
            ->whereNotNull('date_approbation')
            ->groupByRaw('EXTRACT(YEAR FROM date_approbation)')
            ->orderByRaw('EXTRACT(YEAR FROM date_approbation)')
            ->get()
            ->map(fn ($r) => [
                'year' => (int)   $r->annee,
                'MGA'  => (float) $r->mga,
                'USD'  => (float) $r->usd,
                'EUR'  => (float) $r->eur,
            ]);

        return response()->json($rows);
    }

    // ── PROJETS PAR RÉGION ────────────────────────────────────────────────────
    // Eloquent applique automatiquement whereNull('deleted_at') via SoftDeletes.
    //
    // "Non défini" apparaît quand region_id est NULL sur le projet.
    // → Corriger via : Modifier le projet → Zone géographique → choisir une région.
    // → Pour masquer "Non défini" dans le graphique : supprimer le bloc if ci-dessous.
    public function projectsByRegion(): JsonResponse
    {
        $withRegion = Projet::join('regions', 'projets.region_id', '=', 'regions.id')
            ->selectRaw('regions.nom AS region, COUNT(projets.id_projet) AS nb')
            ->whereNotNull('projets.region_id')
            ->groupBy('regions.nom')
            ->orderByRaw('COUNT(projets.id_projet) DESC')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'region' => $r->region,
                'count'  => (int) $r->nb,
            ])
            ->toArray();

        $withoutRegion = Projet::whereNull('region_id')->count();

        if ($withoutRegion > 0) {
            $withRegion[] = ['region' => 'Non défini', 'count' => $withoutRegion];
        }

        // Retour : tableau direct (compatible frontend existant)
        return response()->json($withRegion);
    }

    // ── DIAGNOSTIC : projets sans région ─────────────────────────────────────
    // Endpoint séparé GET /api/v1/stats/projects-sans-region
    // Permet à l'admin de voir quels projets n'ont pas de région assignée,
    // sans modifier le format de projectsByRegion utilisé par le graphique.
    public function projectsSansRegion(): JsonResponse
    {
        $projets = Projet::whereNull('region_id')
            ->select('id_projet', 'titre', 'status_id', 'created_at')
            ->orderBy('titre')
            ->get();

        return response()->json([
            'count'   => $projets->count(),
            'projets' => $projets,
            'message' => $projets->count() > 0
                ? "Ces {$projets->count()} projet(s) n'ont pas de région assignée. Modifiez-les via le formulaire → Zone géographique."
                : 'Tous les projets ont une région assignée.',
        ]);
    }

    // ── MISE À JOUR MANUELLE ──────────────────────────────────────────────────
    // DB::table() justifié ici : public_settings n'a pas de modèle Eloquent.
    public function updateManual(Request $request): JsonResponse
    {   
        $validated = $request->validate([
            'manual_total_projects'      => 'nullable|integer|min:0',
            'manual_total_budget'       => 'nullable|numeric|min:0',
            'manual_termines' => 'nullable|integer|min:0',
            'use_auto_calculation' => 'nullable|boolean',
        ]);
        foreach ($validated as $key => $value) {
            DB::table('public_settings')
                ->updateOrInsert(['key' => $key], ['value' => $value, 'type' => 'number']);
        }
    
        return response()->json(['message' => 'Statistiques mises à jour.']);
    }
}