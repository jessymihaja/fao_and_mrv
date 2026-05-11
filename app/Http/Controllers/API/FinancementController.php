<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Financement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class FinancementController extends Controller
{
    public function index(Request $request)
    {
        $financements = Financement::with('projet')
            ->when($request->filled('projet_id'),         fn ($q) => $q->where('projet_id', $request->integer('projet_id')))
            ->when($request->filled('source_financement'), fn ($q) => $q->where('source_financement', 'ilike', "%{$request->source_financement}%"))
            ->orderByDesc('date_approbation')
            ->paginate($request->integer('per_page', 15));

            return response()->json($financements);
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|integer',
            'source_financement' => 'required|string',
            'budget_approuve' => 'required|numeric',
            'devise' => 'required|string',
            'montant_mga' => 'required|numeric',
            'date_approbation' => 'required|date',
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
            'source_financement' => 'required|string',
            'budget_approuve' => 'required|numeric',
            'devise' => 'required|string',
            'montant_mga' => 'required|numeric',
            'date_approbation' => 'required|date'
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
    $totaux = Financement::selectRaw('SUM("montant_mga" * budget_approuve) as total')
        ->first();
        $totalUSD = Financement::where('devise', 'USD')->sum('budget_approuve');
        $totalEUR = Financement::where('devise', 'EUR')->sum('budget_approuve');
        $totalAR  = Financement::where('devise', 'AR')->sum('budget_approuve');


    return response()->json([
        'total_count' => Financement::count(),
        'totaux_MGA' => $totaux->total,
        'totaux' => [
            'USD' => (float) $totalUSD,
            'EUR' => (float) $totalEUR,
            'AR'  => (float) $totalAR
        ],
    ]);
}

public function byProject(int $projectId): JsonResponse
{
    // On récupère les données brutes sans charger la relation 'projets'
    $financements = Financement::where('projet_id', $projectId)
        ->orderByDesc('date_approbation')
        ->get();

    return response()->json([
        // On passe directement la collection ici
        'data'   => $financements, 
        'totaux' => [
            'AR'  => (float) $financements->where('devise', 'AR')->sum('budget_approuve'),
            'USD' => (float) $financements->where('devise', 'USD')->sum('budget_approuve'),
            'EUR' => (float) $financements->where('devise', 'EUR')->sum('budget_approuve'),
            'MGA' => (float) $financements->sum('montant_mga'),
        ],
    ]);
}

        

}
