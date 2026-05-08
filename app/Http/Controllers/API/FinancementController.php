<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Financement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancementController extends Controller
{
    public function index()
    {
        $financements = Financement::with([
            'projet',
            'devise',
            'utilisateur',
        ])->get();

        return response()->json($financements);
    }
   public function getFinancements(Request $request)
{   
    $per_page = $request->per_page ?? 15;

    $financements = Financement::with([
        'projet',
        'devise',
        'utilisateur',
    ])->paginate($per_page);

    $financements->getCollection()->transform(function ($financement) {

        return [
            'id' => $financement->id_financement,

            'budget_approuve' => $financement->montant,

            'source_financement' => $financement->financeur,

            'montant_mga' => $financement->montant_MGA,

            // IMPORTANT : string au lieu d'objet
            'devise' => $financement->devise?->code,

            'projet' => $financement->projet?->nom_projet,

            'projet_id' => $financement->projet?->id_projet,

            'utilisateur' => $financement->utilisateur?->nom,

            'created_at' => $financement->created_at,
        ];
    });

    return response()->json($financements);
}

    public function show($id)
    {
        $financement = Financement::
        with([
            'projet',
            'devise',
            'utilisateur',])
            ->findOrFail($id);
            
        return response()->json([
            $financement,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|integer',
            'financeur' => 'required|string',
            'montant' => 'required|numeric',
            'devise_id' => 'required|integer',
            'montant_MGA' => 'required|numeric',
            'date_financement' => 'required|date',
            'utilisateur_id' => 'required|integer',
        ]);

        $financement = Financement::create($request->all());

        return response()->json([
            'message' => 'Créé',
            'data' => $financement,
        ]);
    }

    public function update(Request $request, $id)
    {
        $financement = Financement::findOrFail($id);

        $request->validate([
            'projet_id' => 'required|integer',
            'financeur' => 'required|string',
            'montant' => 'required|numeric',
            'devise_id' => 'required|integer',
            'montant_MGA' => 'required|numeric',
            'date_financement' => 'required|date',
            'id_utilisateur_updater' => 'required|integer',
        ]);

        $financement->update($request->all());

        return response()->json([
            'message' => 'Mis à jour',
            'data' => $financement,
        ]);

    }

    public function destroy($id)
    {
        $financement = Financement::findOrFail($id);
        $financement->delete();

        return response()->json([
            'message' => 'Supprimé',
        ]);
    }
    public function financementsNumber(){
        $count = Financement::count();
        return response()->json([
            'count' => $count
        ]);
    }
public function financementsTotauxMGA()
{
    $totaux = Financement::selectRaw('SUM("montant_MGA" * montant) as total')
        ->first();

    return response()->json([
        'total_count' => Financement::count(),
        'totaux_MGA' => $totaux->total
    ]);
}
        

}
