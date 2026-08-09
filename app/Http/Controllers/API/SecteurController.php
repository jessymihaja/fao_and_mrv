<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Secteur;

class SecteurController extends Controller
{
    public function index()
    {
        return response()->json(Secteur::all(), Response::HTTP_OK);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $secteur = Secteur::create($validated);

        return response()->json($secteur, Response::HTTP_CREATED);
    }
    public function show($id)
    {
        $secteur = Secteur::find($id);

        if (!$secteur) {
            return response()->json(['message' => 'Secteur not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($secteur, Response::HTTP_OK);
    }
    public function update(Request $request, $id)
    {
        $secteur = Secteur::find($id);

        if (!$secteur) {
            return response()->json(['message' => 'Secteur not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $secteur->update($validated);

        return response()->json($secteur, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $secteur = Secteur::find($id);

        if (!$secteur) {
            return response()->json(['message' => 'Secteur not found'], Response::HTTP_NOT_FOUND);
        }

        $secteur->delete();

        return response()->json(['message' => 'Secteur deleted successfully'], Response::HTTP_OK);
    }
}
