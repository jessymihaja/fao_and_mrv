<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Indicateur_mrv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Indicateur_mrvController extends Controller
{
    public function index(): JsonResponse
    {
        $indicateur_mrvs = Indicateur_mrv::all();

        return response()->json($indicateur_mrvs);
    }

    public function show(Indicateur_mrv $indicateur_mrv): JsonResponse
    {
        return response()->json($indicateur_mrv);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom'          => 'required',
            'unite'        => 'required',
            'frequence'    => 'required',
        ]);

        Indicateur_mrv::create($request->all());

        return response()->json(Indicateur_mrv::all());
    }

    public function update(Request $request, Indicateur_mrv $indicateur_mrv): JsonResponse
    {
        $request->validate([
            'nom'          => 'required',
            'unite'        => 'required',
            'frequence'    => 'required',
        ]);

        $indicateur_mrv->update($request->all());

        return response()->json($indicateur_mrv);
    }

    public function destroy(Indicateur_mrv $indicateur_mrv): JsonResponse
    {
        $indicateur_mrv->delete();

        return response()->json(null, 204);
    }
}