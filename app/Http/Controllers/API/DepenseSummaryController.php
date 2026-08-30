<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\BudgetDepense;
use Carbon\Carbon;

class DepenseSummaryController extends Controller
{
    public function summaryByProject($projectId)
    {
        // 1. Charger le projet avec ses composantes et activités
        $projet = Projet::with(['composantes.activites'])->findOrFail($projectId);

        // 2. Charger toutes les dépenses du projet
        $depenses = BudgetDepense::where('project_id', $projectId)->get();

        // 3. Calcul par période (Semestre) & Année globales
        $parPeriodeMap = [];
        $parAnneeMap = [];

        foreach ($depenses as $depense) {
            if (!$depense->date) continue;

            $carbonDate = Carbon::parse($depense->date);
            $annee = $carbonDate->year;
            $semestre = $carbonDate->month <= 6 ? 'S1' : 'S2';
            $montant = (float) $depense->montant;

            // Groupement par semestre
            $keyPeriode = "{$annee}_{$semestre}";
            if (!isset($parPeriodeMap[$keyPeriode])) {
                $parPeriodeMap[$keyPeriode] = ['annee' => $annee, 'semestre' => $semestre, 'montant' => 0];
            }
            $parPeriodeMap[$keyPeriode]['montant'] += $montant;

            // Groupement par année
            if (!isset($parAnneeMap[$annee])) {
                $parAnneeMap[$annee] = ['annee' => $annee, 'montant' => 0];
            }
            $parAnneeMap[$annee]['montant'] += $montant;
        }

        // 4. Traitement des composantes & activités
        $composantesSummary = $projet->composantes->map(function ($composante) use ($depenses) {
            $activiteIds = $composante->activites->pluck('id')->toArray();

            $activitesSummary = $composante->activites->map(function ($activite) use ($depenses) {
                $depensesAct = $depenses->where('activite_id', $activite->id);
                $totalDepense = (float) $depensesAct->sum('montant');
                $budget = $activite->budget ? (float) $activite->budget : null;

                // Groupement par période au niveau de l'activité
                $periodeActMap = [];
                foreach ($depensesAct as $d) {
                    if (!$d->date) continue;
                    $cDate = Carbon::parse($d->date);
                    $ann = $cDate->year;
                    $sem = $cDate->month <= 6 ? 'S1' : 'S2';
                    $k = "{$ann}_{$sem}";

                    if (!isset($periodeActMap[$k])) {
                        $periodeActMap[$k] = ['annee' => $ann, 'semestre' => $sem, 'montant' => 0];
                    }
                    $periodeActMap[$k]['montant'] += (float) $d->montant;
                }

                return [
                    'id' => $activite->id,
                    'nom' => $activite->nom ?? $activite->libelle ?? "Activité #{$activite->id}",
                    'budget' => $budget,
                    'total_depense' => $totalDepense,
                    'solde' => $budget !== null ? $budget - $totalDepense : null,
                    'taux_execution' => ($budget && $budget > 0) ? round(($totalDepense / $budget) * 100, 2) : null,
                    'par_periode' => array_values($periodeActMap),
                ];
            });

            // Récupérer les dépenses directement liées à la composante OU à une de ses activités
            $depensesComp = $depenses->filter(function ($d) use ($composante, $activiteIds) {
                return $d->composante_id == $composante->id || in_array($d->activite_id, $activiteIds);
            });

            $totalDepenseComp = (float) $depensesComp->sum('montant');
            $budgetComp = $composante->budget ? (float) $composante->budget : null;

            return [
                'id' => $composante->id,
                'nom' => $composante->nom ?? $composante->libelle ?? "Composante #{$composante->id}",
                'budget' => $budgetComp,
                'total_depense' => $totalDepenseComp,
                'solde' => $budgetComp !== null ? $budgetComp - $totalDepenseComp : null,
                'taux_execution' => ($budgetComp && $budgetComp > 0) ? round(($totalDepenseComp / $budgetComp) * 100, 2) : null,
                'activites' => $activitesSummary->values()->toArray(),
            ];
        });

        // 5. Dépenses non ventilées (sans composante ni activité)
        $nonVentilees = $depenses->whereNull('composante_id')->whereNull('activite_id');
        
        // 6. Totaux Projet
        $totalDepenseProjet = (float) $depenses->sum('montant');
        $budgetProjet = $projet->budget ? (float) $projet->budget : null;

        return response()->json([
            'composantes' => $composantesSummary->values()->toArray(),
            'non_ventilees' => [
                'count' => $nonVentilees->count(),
                'total_depense' => (float) $nonVentilees->sum('montant'),
            ],
            'project' => [
                'budget' => $budgetProjet,
                'total_depense' => $totalDepenseProjet,
                'solde' => $budgetProjet !== null ? $budgetProjet - $totalDepenseProjet : null,
                'taux_execution' => ($budgetProjet && $budgetProjet > 0) ? round(($totalDepenseProjet / $budgetProjet) * 100, 2) : null,
            ],
            'par_periode' => array_values($parPeriodeMap),
            'par_annee' => array_values($parAnneeMap),
        ]);
    }
}