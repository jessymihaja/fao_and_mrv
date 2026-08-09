<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ContributionCategorie;

class ContributionCategorieController extends Controller
{
    /**
     * GET /contribution-categories
     */
    public function index(Request $request)
    {
        $query = ContributionCategorie::query();

        if ($request->filled('search')) {
            $query->where('designation', 'like', "%{$request->search}%");
        }

        return response()->json($query->get());
    }

    /**
     * POST /contribution-categories
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $categorie = ContributionCategorie::create($validated);

        return response()->json($categorie, 201);
    }
}
