<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerspectiveType;

class PerspectiveTypeController extends Controller
{
    public function index()
    {
        return response()->json(PerspectiveType::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255|unique:perspective_types,designation'
        ]);

        $type = PerspectiveType::create($validated);

        return response()->json($type, 201);
    }
}
