<?php

namespace App\Http\Controllers\API;

use App\Models\Depense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
class DepenseController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $depenses = Depense::with('projet')
            ->when(
                $request->filled('project_id'),
                fn ($q) => $q->where('project_id', $request->integer('project_id'))
            )
            ->orderByDesc('date')
            ->paginate($request->integer('per_page', 15));

        return response()->json($depenses);
    }

    /** Créer une dépense */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id'   => 'required|integer|exists:projets,id_projet',
            'note'         => 'required|string',
            'montant'      => 'required|numeric|min:0',
            'date'         => 'required|date',
            'beneficiaire' => 'required|string|max:255',
        ]);

        $depense = Depense::create([
            'project_id'         => $validated['project_id'],
            'note'               => $validated['note'],
            'montant'            => $validated['montant'],
            'date'               => $validated['date'],
            'beneficiaire'       => $validated['beneficiaire'],
            'justification_path' => '',
            'justification_name' => '',
            'created_by'         => auth()->id(),
        ]);


        return response()->json($depense->load('project'), 201);
    }

    /** Détail d'une dépense */
    public function show(int $id): JsonResponse
    {
        $depense = Depense::with('projet')->findOrFail($id);
        return response()->json($depense);
    }

    /** Modifier une dépense */
    public function update(Request $request, int $id): JsonResponse
    {
        $depense = Depense::findOrFail($id);

        $validated = $request->validate([
            'note'         => 'sometimes|required|string',
            'montant'      => 'sometimes|required|numeric|min:0',
            'date'         => 'sometimes|required|date',
            'beneficiaire' => 'sometimes|required|string|max:255',
        ]);

        $depense->update($validated);

        return response()->json($depense->load('project'));
    }

    /** Supprimer une dépense et son fichier */
    public function destroy(int $id): JsonResponse
    {
        $depense = Depense::findOrFail($id);

        if (Storage::disk('local')->exists($depense->justification_path)) {
            Storage::disk('local')->delete($depense->justification_path);
        }

        $depense->delete();

        return response()->json(['message' => 'Dépense supprimée.']);
    }

     public function projectDepenses(Request $request, int $projectId): JsonResponse
    {
        $depenses = Depense::with('financement')
            ->where('project_id', $projectId)
            ->when($request->filled('financement_id'),
                fn ($q) => $q->where('financement_id', $request->integer('financement_id')))
            ->when($request->filled('categorie'),
                fn ($q) => $q->where('categorie', $request->categorie))
            ->when($request->filled('date_from'),
                fn ($q) => $q->whereDate('date', '>=', $request->date_from))
            ->when($request->filled('date_to'),
                fn ($q) => $q->whereDate('date', '<=', $request->date_to))
            ->orderByDesc('date')
            ->get();

        return response()->json($depenses);
    }

}