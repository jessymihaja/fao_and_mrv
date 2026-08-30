<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BeneficiaryCategory;

class BeneficiaryCategoryController extends Controller
{
    public function index()
    {
        return response()->json(BeneficiaryCategory::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $category = BeneficiaryCategory::create($validated);

        return response()->json($category, 201);
    }
}
