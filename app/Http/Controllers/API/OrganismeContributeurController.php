<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganismeContributeur;
use Illuminate\Http\Request;

class OrganismeContributeurController extends Controller
{
    /**
     * GET /organismes-contributeurs
     */
    public function index(Request $request)
    {
        $query = OrganismeContributeur::query();

        if ($request->filled('search')) {
            $query->where('designation', 'like', "%{$request->search}%");
        }

        return response()->json($query->get());
    }

    /**
     * POST /organismes-contributeurs
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $organisme = OrganismeContributeur::create($validated);

        return response()->json($organisme, 201);
    }
}
