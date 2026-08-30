<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BeneficiaryType;

class BeneficiaryTypeController extends Controller
{
    public function index()
    {
        return response()->json(BeneficiaryType::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $type = BeneficiaryType::create($validated);

        return response()->json($type, 201);
    }
}
