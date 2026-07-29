<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Resultat_mrv;
use App\Models\Composante;
use App\Models\Activite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Resultat_mrvController extends Controller
{
    public function index(Request $request): JsonResponse
{
    $resultat_mrvs = Resultat_mrv::with(['indicateur_mrv', 'projet'])
        ->when($request->projet_id, function ($query, $projet_id) {
            $query->where('projet_id', $projet_id);
        })
        ->when($request->annee, function ($query, $annee) {
            $query->where('annee', $annee);
        })
        ->when($request->frequence, function ($query, $frequence) {
            $query->whereHas('indicateur_mrv', function ($q) use ($frequence) {
                $q->where('frequence', $frequence);
            });
        })
        ->orderByDesc('annee')
        ->paginate($request->per_page ?? 15);

    return response()->json($resultat_mrvs);
}

    public function show(Resultat_mrv $resultat_mrv): JsonResponse
    {
        return response()->json($resultat_mrv);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'projet_id'         => 'nullable',
            'composante_id'     => 'nullable',
            'activite_id'       => 'nullable',
            'indicateur_mrv_id' => 'required',
            'valeur_cible'      => 'required',
            'valeur_realise'    => 'required',
            'annee'             => 'required',
        ]);

        $data = $request->all();

        // Création depuis une composante
        if (!$request->projet_id && $request->composante_id) {
            $composante = Composante::findOrFail($request->composante_id);
            $data['projet_id'] = $composante->projet_id;
        }

        // Création depuis une activité
        if (!$request->projet_id && $request->activite_id) {
            $activite = Activite::with('composante')->findOrFail($request->activite_id);
            $data['projet_id'] = $activite->projet_id;
        }

        // Sécurité
        if (!$data['projet_id']) {
            return response()->json([
                'message' => 'Impossible de déterminer le projet associé'
            ], 422);
        }

        $resultat = Resultat_mrv::create($data);

        return response()->json($resultat);
    }

    public function update(Request $request, $id): JsonResponse
{
    $request->validate([
        'projet_id'         => 'required',
        'indicateur_mrv_id' => 'required',
        'valeur_cible'      => 'required',
        'valeur_realise'    => 'required',
        'annee'             => 'required',
    ]);

    $resultat_mrv = Resultat_mrv::findOrFail($id);

    $resultat_mrv->update($request->all());

    return response()->json($resultat_mrv->fresh());
}

    public function destroy(Resultat_mrv $resultat_mrv): JsonResponse
    {
        $resultat_mrv->delete();

        return response()->json(null, 204);
    }

    public function getByIndicateurMrv($indicateur_mrv_id): JsonResponse
    {
        $resultat_mrvs = Resultat_mrv::where('indicateur_mrv_id', $indicateur_mrv_id)
            ->with(['indicateur_mrv'])
            ->orderByDesc('annee')
            ->get();

        return response()->json($resultat_mrvs);
    }
    public function getByProjet($projet_id): JsonResponse
    {
        $resultat_mrvs = Resultat_mrv::where('projet_id', $projet_id)
            ->with(['indicateur_mrv'])
            ->orderByDesc('annee')
            ->get();

        return response()->json($resultat_mrvs);
    }
    public function getByComposite($composante_id): JsonResponse
    {
        $resultat_mrvs = Resultat_mrv::where('composante_id', $composante_id)
            ->with(['indicateur_mrv'])
            ->orderByDesc('annee')
            ->get();
        return response()->json($resultat_mrvs);
    }
    public function getByActivite($activite_id): JsonResponse
    {
        $resultat_mrvs = Resultat_mrv::where('activite_id', $activite_id)
            ->with(['indicateur_mrv'])
            ->orderByDesc('annee')
            ->get();
        return response()->json($resultat_mrvs);
    }

    public function getKpis() : JsonResponse
    {
        $total = Resultat_mrv::count();
       $taux_moyen = Resultat_mrv::selectRaw(
            'ROUND(AVG((valeur_realise * 100) / valeur_cible), 2) as taux'
        )->value('taux');

        $nb_excellent = Resultat_mrv::whereColumn(
            'valeur_realise',
            '>=',
            'valeur_cible'
        )->count();

        $nb_faible = Resultat_mrv::whereRaw(
            '(valeur_realise * 100) / valeur_cible < ?',
            [50]
        )->count();

        return response()->json([
            'total' => $total,
            'taux_moyen' => $taux_moyen,
            'nb_excellent' => $nb_excellent,
            'nb_faible' => $nb_faible
        ]);
   }
}