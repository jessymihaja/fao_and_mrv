<?php
namespace App\Http\Controllers\API;

use App\Models\Engagement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Financement;


class EngagementController extends Controller
{
    public function engagements(int $financementId): JsonResponse
    {
        $financement = Financement::findOrFail($financementId);
        $items = Engagement::where('financement_id', $financementId)
            ->orderByDesc('date')->get();
        return response()->json($items);
    }

    public function storeEngagement(Request $request, int $financementId): JsonResponse
    {
        Financement::findOrFail($financementId);
        $validated = $request->validate([
            'date'        => 'required|date',
            'montant'     => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $engagement = Engagement::create([...$validated, 'financement_id' => $financementId]);
        return response()->json($engagement, 201);
    }

    public function updateEngagement(Request $request, int $id): JsonResponse
    {
        $engagement = Engagement::findOrFail($id);
        $validated  = $request->validate([
            'date'        => 'sometimes|date',
            'montant'     => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        $engagement->update($validated);
        return response()->json($engagement);
    }

    public function destroyEngagement(int $id): JsonResponse
    {
        Engagement::findOrFail($id)->delete();
        return response()->json(['message' => 'Supprimé.']);
    }
}