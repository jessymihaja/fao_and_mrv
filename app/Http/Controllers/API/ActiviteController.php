<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activite;
use App\Models\Composante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $activites = Activite::with(['projet', 'composante'])
            ->when($request->projet_id, function ($query, $projet_id) {
                $query->where('projet_id', $projet_id);
            })
            ->when($request->composante_id, function ($query, $composante_id) {
                $query->where('composante_id', $composante_id);
            })
            ->orderByDesc('id')
            ->paginate($request->per_page ?? 15);

        return response()->json($activites);
    }

    public function show(Activite $activite): JsonResponse
    {
        return response()->json($activite);
    }

   public function store(Request $request): JsonResponse
    {
        $composanteId = $request->route('composante');
        $projetId = $request->route('project');

        $request->validate([
            'nom' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $data = $request->only(['nom', 'description']);

        if ($composanteId) {
            $composante = Composante::findOrFail($composanteId);

            $data['composante_id'] = $composanteId;
            $data['projet_id'] = $composante->projet_id;
        } else {
            $data['projet_id'] = $projetId;
            $data['composante_id'] = null;
        }

        $activite = Activite::create($data);

        return response()->json($activite, 201);
    }

    public function update(Request $request, Activite $activite): JsonResponse
    {
        $request->validate([
            'projet_id'         => 'required',
            'composante_id'     => 'nullable',
            'nom'               => 'required',
            'description'       => 'nullable',
        ]);

        $activite->update($request->all());

        return response()->json($activite);
    }

    public function destroy(Activite $activite): JsonResponse
    {
        $activite->delete();

        return response()->json(null, 204);
    }
    public function getByProjet($projet_id): JsonResponse
    {
        $activites = Activite::where('projet_id', $projet_id)
            ->orderByDesc('id')
            ->get();

        return response()->json($activites);
    }
    public function getByComposante($composante_id): JsonResponse
    {
        $activites = Activite::where('composante_id', $composante_id)
            ->orderByDesc('id')
            ->get();

        return response()->json($activites);
    }
}