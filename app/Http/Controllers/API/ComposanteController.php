<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Composante;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class ComposanteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $composantes = Composante::with(['projet'])
            ->withCount('activites','indicateurs')
            ->when($request->projet_id, function ($query, $projet_id) {
                $query->where('projet_id', $projet_id);
            })
            ->orderByDesc('id')
            ->paginate($request->per_page ?? 15);

        return response()->json($composantes);
    }

    public function show(Composante $composante): JsonResponse
    {
        return response()->json($composante);
    }

    public function store(Request $request, $projet_id): JsonResponse
{
    $request->validate([
        'nom' => 'required',
        'description' => 'nullable',
    ]);

    Composante::create([
        'projet_id'  => $projet_id,
        'nom'        => $request->nom,
        'description'=> $request->description,
    ]);

    return response()->json(Composante::all());
}

    public function update(Request $request, Composante $composante): JsonResponse
    {
        $request->validate([
            'projet_id'         => 'required',
            'nom'               => 'required',
            'description'       => 'nullable',
        ]);

        $composante->update($request->all());

        return response()->json($composante);
    }

    public function destroy(Composante $composante): JsonResponse
    {
        $composante->delete();

        return response()->json(null, 204);
    }
    public function getByProjet($projet_id): JsonResponse
    {
        $composantes = Composante::where('projet_id', $projet_id)
        ->withCount('activites','indicateurs')
            ->orderByDesc('id')
            ->get();

        return response()->json($composantes);
    }
}
