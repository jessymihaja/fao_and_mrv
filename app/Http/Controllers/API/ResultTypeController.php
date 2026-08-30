<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ResultType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResultTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $resultTypes = ResultType::all();

        return response()->json($resultTypes, 200);
    }

    /**
     * Crée un nouveau type de résultat.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $resultType = ResultType::create($validated);

        return response()->json($resultType, 201);
    }
}
