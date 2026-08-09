<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectIdea;
use App\Models\ProjectIdeaStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectIdeaController extends Controller
{
    private function applyFilters(Request $request)
    {
        $query = ProjectIdea::with(['secteurs', 'region', 'financements']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('acronyme', 'like', "%{$search}%")
                  ->orWhere('porteur_projet', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('secteur_id')) {
            $query->whereHas('secteurs', function ($q) use ($request) {
                $q->where('secteurs.id', $request->secteur_id);
            });
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->filled('bailleur_id')) {
            $query->whereHas('financements', function ($q) use ($request) {
                $q->where('organisme_contributeur_id', $request->bailleur_id);
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $ideas = $this->applyFilters($request)->latest()->paginate($perPage);

        return response()->json($ideas);
    }

    public function exportData(Request $request)
    {
        $ideas = $this->applyFilters($request)->latest()->get();
        return response()->json(['data' => $ideas]);
    }

    public function show($id)
    {
        $idea = ProjectIdea::with([
            'secteurs', 'province', 'region', 'district', 'commune', 'fokontany',
            'financements', 'documents', 'status_history'
        ])->findOrFail($id);

        return response()->json($idea);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'devise' => 'required|string',
            'secteur_ids' => 'nullable|array',
            'secteur_ids.*' => 'exists:secteurs,id',
        ]);

        return DB::transaction(function () use ($request) {
            $data = $request->except('secteur_ids');
            $data['created_by'] = auth()->id();
            $data['statut'] = 'brouillon';

            $idea = ProjectIdea::create($data);

            if ($request->has('secteur_ids')) {
                $idea->secteurs()->sync($request->secteur_ids);
            }

            // Entrée initiale dans l'historique
            ProjectIdeaStatusHistory::create([
                'project_idea_id' => $idea->id,
                'ancien_statut' => null,
                'nouveau_statut' => 'brouillon',
                'commentaire' => 'Création de l\'idée de projet',
                'auteur' => auth()->user()?->name ?? 'Système'
            ]);

            return response()->json($idea->load('secteurs'), 201);
        });
    }

    public function update(Request $request, $id)
    {
        $idea = ProjectIdea::findOrFail($id);

        return DB::transaction(function () use ($request, $idea) {
            $idea->update($request->except('secteur_ids'));

            if ($request->has('secteur_ids')) {
                $idea->secteurs()->sync($request->secteur_ids);
            }

            return response()->json($idea->load(['secteurs', 'financements', 'documents']));
        });
    }

    public function destroy($id)
    {
        $idea = ProjectIdea::findOrFail($id);
        $idea->delete();
        return response()->json(null, 204);
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'nouveau_statut' => 'required|in:brouillon,soumis,en_etude,approuve,converti',
            'commentaire' => 'nullable|string'
        ]);

        $idea = ProjectIdea::findOrFail($id);
        $ancienStatut = $idea->statut;

        if ($ancienStatut === $request->nouveau_statut) {
            return response()->json($idea);
        }

        DB::transaction(function () use ($idea, $ancienStatut, $request) {
            $idea->update(['statut' => $request->nouveau_statut]);

            ProjectIdeaStatusHistory::create([
                'project_idea_id' => $idea->id,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => $request->nouveau_statut,
                'commentaire' => $request->commentaire,
                'auteur' => auth()->user()?->name ?? 'Admin'
            ]);
        });

        return response()->json($idea->load('status_history'));
    }

    public function convert($id)
    {
        $idea = ProjectIdea::findOrFail($id);

        if ($idea->statut === 'converti') {
            return response()->json(['message' => 'Cette idée a déjà été convertie.'], 400);
        }

        return DB::transaction(function () use ($idea) {
            // Exemple : création d'une entrée dans votre table 'projects' existante
            $projectId = DB::table('projects')->insertGetId([
                'titre' => $idea->titre,
                'description' => $idea->description,
                'budget' => $idea->budget_total_estime,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $idea->update([
                'statut' => 'converti',
                'converted_project_id' => $projectId,
                'converted_at' => now()
            ]);

            ProjectIdeaStatusHistory::create([
                'project_idea_id' => $idea->id,
                'ancien_statut' => 'approuve',
                'nouveau_statut' => 'converti',
                'commentaire' => 'Conversion automatique en projet',
                'auteur' => auth()->user()?->name ?? 'Système'
            ]);

            return response()->json([
                'message' => 'Idée de projet convertie en projet avec succès.',
                'project' => ['id' => $projectId, 'titre' => $idea->titre],
                'idea' => $idea
            ]);
        });
    }
}