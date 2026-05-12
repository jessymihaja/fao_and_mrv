<?php
namespace App\Http\Controllers\API;

use App\Models\Decaissement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Financement;


class DecaissementController extends Controller
{
     public function decaissements(int $financementId): JsonResponse
    {
        Financement::findOrFail($financementId);
        $items = Decaissement::where('financement_id', $financementId)
            ->orderByDesc('date')->get();
        return response()->json($items);
    }

    public function storeDecaissement(Request $request, int $financementId): JsonResponse
    {
        Financement::findOrFail($financementId);
        $validated = $request->validate([
            'date'      => 'required|date',
            'montant'   => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:255',
        ]);
        $dec = Decaissement::create([...$validated, 'financement_id' => $financementId]);
        return response()->json($dec, 201);
    }

    public function updateDecaissement(Request $request, int $id): JsonResponse
    {
        $dec       = Decaissement::findOrFail($id);
        $validated = $request->validate([
            'date'      => 'sometimes|date',
            'montant'   => 'sometimes|numeric|min:0',
            'reference' => 'nullable|string|max:255',
        ]);
        $dec->update($validated);
        return response()->json($dec);
    }

    public function destroyDecaissement(int $id): JsonResponse
    {
        Decaissement::findOrFail($id)->delete();
        return response()->json(['message' => 'Supprimé.']);
    }

}