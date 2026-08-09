<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ProjectIdeaFinancement;

class ProjectIdeaFinancementController extends Controller
{
    public function index($ideaId)
    {
        return response()->json(ProjectIdeaFinancement::where('project_idea_id', $ideaId)->get());
    }

    public function store(Request $request, $ideaId)
    {
        $validated = $request->validate([
            'bailleur' => 'nullable|string',
            'type_financement' => 'required|in:don,pret,cofinancement,assistance_technique',
            'statut' => 'required|in:en_preparation,soumis,en_negociation',
        ]);

        $financement = ProjectIdeaFinancement::create(array_merge(
            $request->all(),
            ['project_idea_id' => $ideaId]
        ));

        return response()->json($financement, 201);
    }

    public function update(Request $request, $id)
    {
        $financement = ProjectIdeaFinancement::findOrFail($id);
        $financement->update($request->all());

        return response()->json($financement);
    }

    public function destroy($id)
    {
        $financement = ProjectIdeaFinancement::findOrFail($id);
        $financement->delete();

        return response()->json(null, 204);
    }
}