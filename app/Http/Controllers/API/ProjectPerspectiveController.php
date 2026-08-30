<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectPerspective;
use App\Http\Requests\StoreUpdatePerspectiveRequest;
use App\Models\Projet;

class ProjectPerspectiveController extends Controller
{
    public function listByProject($projectId)
    {
        $perspectives = ProjectPerspective::with(['type', 'project:id_projet,titre,is_published'])
            ->where('project_id', $projectId)
            ->get();

        return response()->json($perspectives);
    }

    public function store(StoreUpdatePerspectiveRequest $request, $projectId)
    {
        Projet::findOrFail($projectId);

        $perspective = ProjectPerspective::create(array_merge(
            $request->validated(),
            [
                'project_id' => $projectId,
                'created_by' => auth()->id(),
            ]
        ));

        return response()->json($perspective->load(['type', 'project:id_projet,titre,is_published']), 201);
    }

    public function update(StoreUpdatePerspectiveRequest $request, $id)
    {
        $perspective = ProjectPerspective::findOrFail($id);
        $perspective->update($request->validated());

        return response()->json($perspective->load(['type', 'project:id_projet,titre,is_published']));
    }

    public function destroy($id)
    {
        $perspective = ProjectPerspective::findOrFail($id);
        $perspective->delete();

        return response()->json(['message' => 'Perspective supprimée avec succès']);
    }

    public function publicList(Request $request)
    {
        $query = ProjectPerspective::with(['type', 'project:id_projet,titre']);
          

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        $perPage = $request->get('per_page', 15);
        return response()->json($query->paginate($perPage));
    }

    public function publicStats()
    {
        $baseQuery = ProjectPerspective::whereHas('project');

        $stats = [
            'projets_en_preparation'   => (clone $baseQuery)->where('statut', 'a_l_etude')->count(),
            'projets_recherche_financement'   => (clone $baseQuery)->where('statut', 'planifie')->count(),
            'rojets_extension_envisagee'    => (clone $baseQuery)->where('statut', 'en_cours')->count(),
            'projets_perennisation_envisagee'    => (clone $baseQuery)->where('statut', 'realise')->count(),
            'total_perspectives'  => (clone $baseQuery)->count(),
            'apercu'              => (clone $baseQuery)
                ->with(['type', 'project'])
                ->latest()
                ->take(6)
                ->get()
                ->map(fn($p) => [
                    'id'                   => $p->id,
                    'titre'                => $p->titre,
                    'type_id'              => $p->type_id,
                    'type'                 => $p->type?->designation,
                    'impact_futur_attendu' => $p->impact_futur_attendu,
                    'projet'               => $p->project?->titre,
                    'project_id'           => $p->project_id,
                ]),
        ];

        return response()->json($stats);
    }
}
