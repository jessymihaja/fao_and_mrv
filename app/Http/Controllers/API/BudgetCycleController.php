<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Financement, Projet, BudgetPledge, BudgetMobilisation, BudgetEngagement, BudgetApprobation, BudgetProgrammation, BudgetDecaissement, BudgetDepense};

class BudgetCycleController extends Controller
{
    private function applyFilters($query, Request $request) {
        if ($request->has('composante_id')) {
            $query->where('composante_id', $request->composante_id);
        }
        if ($request->has('activite_id')) {
            $query->where('activite_id', $request->activite_id);
        }
        return $query;
    }

    public function forFinancement(Request $request, $financementId) {
        $financement = Financement::findOrFail($financementId);
        
        $pledges = $this->applyFilters(BudgetPledge::where('financement_id', $financementId), $request)->get();
        $mobilisations = $this->applyFilters(BudgetMobilisation::where('financement_id', $financementId), $request)->get();
        $engagements = $this->applyFilters(BudgetEngagement::where('financement_id', $financementId), $request)->get();
        $approbations = $this->applyFilters(BudgetApprobation::where('financement_id', $financementId), $request)->get();
        $programmations = $this->applyFilters(BudgetProgrammation::where('financement_id', $financementId), $request)->get();
        $decaissements = $this->applyFilters(BudgetDecaissement::where('financement_id', $financementId), $request)->get();
        $depenses = $this->applyFilters(BudgetDepense::where('financement_id', $financementId), $request)->get();

        return response()->json($this->buildSummary([$financement], $pledges, $mobilisations, $engagements, $approbations, $programmations, $decaissements, $depenses));
    }

    public function forProject(Request $request, $projectId) {
        $project = Projet::with('financements')->findOrFail($projectId);
        $financementIds = $project->financements->pluck('id');

        if ($request->has('financement_id')) {
            $financementIds = collect([$request->financement_id]);
        }

        $pledges = $this->applyFilters(BudgetPledge::whereIn('financement_id', $financementIds), $request)->get();
        $mobilisations = $this->applyFilters(BudgetMobilisation::whereIn('financement_id', $financementIds), $request)->get();
        $engagements = $this->applyFilters(BudgetEngagement::whereIn('financement_id', $financementIds), $request)->get();
        $approbations = $this->applyFilters(BudgetApprobation::whereIn('financement_id', $financementIds), $request)->get();
        $programmations = $this->applyFilters(BudgetProgrammation::whereIn('financement_id', $financementIds), $request)->get();
        $decaissements = $this->applyFilters(BudgetDecaissement::whereIn('financement_id', $financementIds), $request)->get();
        $depenses = $this->applyFilters(BudgetDepense::where('project_id', $projectId), $request)->get();

        return response()->json($this->buildSummary($project->financements, $pledges, $mobilisations, $engagements, $approbations, $programmations, $decaissements, $depenses));
    }

    private function buildSummary($financements, $pledges, $mobilisations, $engagements, $approbations, $programmations, $decaissements, $depenses) {
        $totPledge = $pledges->sum('montant');
        $totMobilise = $mobilisations->sum('montant');
        $totEngage = $engagements->sum('montant');
        $totApprouve = $approbations->sum('montant');
        $totProgramme = $programmations->sum('montant');
        $totDecaisse = $decaissements->sum('montant');
        $totDepense = $depenses->sum('montant');
        $totAudite = $depenses->where('statut', 'audite')->sum('montant_audite');

        $baseRef = $totApprouve > 0 ? $totApprouve : ($totPledge > 0 ? $totPledge : 1);

        return [
            'financements' => collect($financements)->map(fn($f) => [
                'id' => $f->id,
                'type_financement' => $f->type_financement ?? 'subvention',
                'source_financement' => $f->source_financement ?? 'international',
                'budget_approuve' => $totApprouve,
                'devise' => $f->devise ?? 'MGA',
            ]),
            'stages' => [
                'pledge'    => ['label' => 'Annoncé', 'total_mga' => $totPledge, 'count' => $pledges->count(), 'items' => $pledges],
                'mobilise'  => ['label' => 'Mobilisé', 'total_mga' => $totMobilise, 'count' => $mobilisations->count(), 'items' => $mobilisations],
                'engage'    => ['label' => 'Engagé', 'total_mga' => $totEngage, 'count' => $engagements->count(), 'items' => $engagements],
                'approuve'  => ['label' => 'Approuvé', 'total_mga' => $totApprouve, 'count' => $approbations->count(), 'items' => $approbations],
                'programme' => ['label' => 'Programmé', 'total_mga' => $totProgramme, 'count' => $programmations->count(), 'items' => $programmations],
                'decaisse'  => ['label' => 'Décaissé', 'total_mga' => $totDecaisse, 'count' => $decaissements->count(), 'items' => $decaissements],
                'audite'    => [
                    'label' => 'Audité / Dépensé',
                    'total_mga' => $totDepense,
                    'total_audite_mga' => $totAudite,
                    'count' => $depenses->count(),
                    'items' => $depenses
                ],
            ],
            'rates' => [
                'taux_mobilisation' => $totPledge > 0 ? round(($totMobilise / $totPledge) * 100, 2) : null,
                'taux_engagement'   => round(($totEngage / $baseRef) * 100, 2),
                'taux_decaissement' => $totEngage > 0 ? round(($totDecaisse / $totEngage) * 100, 2) : null,
                'taux_execution'    => $totDecaisse > 0 ? round(($totDepense / $totDecaisse) * 100, 2) : null,
            ],
            'cascade' => [
                ['stage' => 'Annoncé', 'montant' => $totPledge],
                ['stage' => 'Mobilisé', 'montant' => $totMobilise],
                ['stage' => 'Approuvé', 'montant' => $totApprouve],
                ['stage' => 'Engagé', 'montant' => $totEngage],
                ['stage' => 'Programmé', 'montant' => $totProgramme],
                ['stage' => 'Décaissé', 'montant' => $totDecaisse],
                ['stage' => 'Dépensé', 'montant' => $totDepense],
            ]
        ];
    }
}