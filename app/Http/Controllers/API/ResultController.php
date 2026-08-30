<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;

use App\Models\Result;
use App\Models\ResultPieceJointe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Resultat_mrv;

class ResultController extends Controller
{
    private array $relations = ['resultType', 'indicateur', 'composante', 'activite', 'pieces_jointes','indicateur.indicateur_mrv'];

    /**
     * Liste les résultats par projet.
     */
    public function listByProject(Request $request, int $projectId): JsonResponse
    {
        $query = Result::with($this->relations)->where('project_id', $projectId);

        if ($request->has('statut')) {
            $query->where('statut', $request->query('statut'));
        }

        return response()->json($query->get());
    }

    /**
     * Voir un résultat.
     */
    public function show(int $id): JsonResponse
    {
        $result = Result::with($this->relations)->findOrFail($id);
        return response()->json($result);
    }

    /**
     * Création.
     */
    public function store(Request $request, int $projectId): JsonResponse
    {
        $request->merge(['project_id' => $projectId]);

        $validated = $request->validate([
            'project_id'          => 'required|integer',
            'composante_id'       => 'nullable|integer',
            'activite_id'         => 'nullable|integer',
            'indicateur_id'       => 'nullable|integer',
            'result_type_id'      => 'required|integer',
            'titre'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'reference_year'      => 'required|integer',
            'target_year'         => 'required|integer',
            'statut'              => 'required|in:prevu,en_cours,atteint,partiellement_atteint,non_atteint',
            'valeur_reference'   => 'nullable|numeric',
            'source_verification' => 'nullable|string',
            'methode_collecte'    => 'nullable|string',
            'observations'        => 'nullable|string',
        ]);

        $result = Result::create($validated);

        $this->handleFileUploads($request, $result->id);

        return response()->json($result->load($this->relations), 201);
    }

    /**
     * Modification.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $result = Result::findOrFail($id);

        $validated = $request->validate([
            'composante_id'       => 'nullable|integer',
            'activite_id'         => 'nullable|integer',
            'indicateur_id'       => 'nullable|integer',
            'result_type_id'      => 'sometimes|required|integer',
            'titre'               => 'sometimes|required|string|max:255',
            'description'        => 'nullable|string',
            'reference_year'      => 'sometimes|required|integer',
            'target_year'         => 'sometimes|required|integer',
            'statut'              => 'sometimes|required|in:prevu,en_cours,atteint,partiellement_atteint,non_atteint',
            'valeur_reference'   => 'nullable|numeric',
            'source_verification' => 'nullable|string',
            'methode_collecte'    => 'nullable|string',
            'observations'        => 'nullable|string',
        ]);

        $result->update($validated);

        $this->handleFileUploads($request, $result->id);

        return response()->json($result->load($this->relations));
    }

    /**
     * Suppression d'un résultat.
     */
    public function destroy(int $id): JsonResponse
    {
        $result = Result::findOrFail($id);
        
        foreach ($result->pieces_jointes as $pj) {
            Storage::disk('public')->delete($pj->fichier);
        }

        $result->delete();

        return response()->json(['message' => 'Résultat supprimé avec succès']);
    }

    /**
     * Suppression d'une pièce jointe.
     */
    public function deletePieceJointe(int $resultId, int $pieceId): JsonResponse
    {
        $piece = ResultPieceJointe::where('result_id', $resultId)->findOrFail($pieceId);

        Storage::disk('public')->delete($piece->fichier);
        $piece->delete();

        return response()->json(['message' => 'Pièce jointe supprimée avec succès']);
    }

    private function handleFileUploads(Request $request, int $resultId): void
    {
        if ($request->hasFile('pieces_jointes')) {
            $files = $request->file('pieces_jointes');
            $filesArray = is_array($files) ? $files : [$files];

            foreach ($filesArray as $file) {
                $path = $file->store('results/pieces_jointes', 'public');
                ResultPieceJointe::create([
                    'result_id'    => $resultId,
                    'fichier'      => $path,
                    'nom_original' => $file->getClientOriginalName(),
                    'taille'       => $file->getSize(),
                ]);
            }
        }
    }
}