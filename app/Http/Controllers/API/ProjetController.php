<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;

class ProjetController extends Controller
{   
    public function __construct(
        private readonly ActivityLogService $logService
    ) {
    }

    public function index()
    {
        $projets = Projet::with([
            'status',
            'classifications'])
                    ->get();

        return response()->json($projets);
    }
   public function getPaginatedProjects(Request $request) { 
    $perPage = $request->per_page ?? 15; $projets = Projet::with([ 'status', 
    'classifications', 
    'entiteAccreditees', 
    'domainesIntervention', 
    ]) ->withSum( ['financements as budget_total' => function ($query) { $query->selectRaw('COALESCE(SUM( budget_approuve), 0)'); }], \DB::raw(' budget_approuve') ) ->when( $request->filled('search'),
     fn ($q) => $q->where(function ($s) use ($request) { 
        $s->where('titre', 'ilike', "%{$request->search}%") 
        ->orWhere('description', 'ilike', "%{$request->search}%"); }) ) 
        ->when( $request->filled('statut'), fn ($q) => $q->whereHas('status', function ($query) use ($request) { $query->where('designation', $request->statut); }) )
        ->paginate($perPage); return response()->json($projets); }

    public function show($id) {
    $projet = Projet::with([
        'status', 
        'classifications', 
        'entiteAccreditees', 
        'domainesIntervention', 
    ])->findOrFail($id);

    return response()->json($projet);
}

    /*public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
            'classification_id' => 'required|integer',
            'status_id' => 'required|integer',
            'region_id' => 'required|integer',
            'district_id' => 'required|integer',
            'commune_id' => 'required|integer',
            'fokontany_id' => 'required|integer',
            'province_id' => 'required|integer',
            'entite_accreditee_id' => 'required|integer',
            'domaine_intervention_id' => 'required|integer',
            'is_published' => 'required|boolean',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $projet = Projet::create($request->all());

        return response()->json([
            'message' => 'Créé',
            'data' => $projet,
        ]);
    }*/
    private function sanitize(Request $request): void
    {
        $nullableFields = [
            'code_projet',
            'description',
            'status_id',
            'date_debut',
            'date_fin',
            'latitude',
            'longitude',
            'province_id',
            'region_id',
            'district_id',
            'commune_id',
            'fokontany_id',
            'zone_description',
            'geo_address',
            'objectifs',
            'impact',
            'problematique_climatique',
            'nombre_beneficiaires',
        ];

        $data = $request->all();

        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        // is_published : normalise true/false/1/0/"true"/"false" -> booléen PHP
        if (array_key_exists('is_published', $data)) {
            $val = $data['is_published'];
            $data['is_published'] = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        $request->replace($data);
    }

    private function validationRules(bool $isUpdate = false): array
    {
        // Utiliser une variable pour éviter les problèmes avec les accents dans in:
        $statutValues = implode(',', ['Concept Note', 'Funding Proposal', 'En cours', 'Clôturé']);

        $rules = [
            'code_projet'                => 'nullable|string|max:255',
            'titre'                    => 'string|max:255',
            'status_id'                => 'required|integer|exists:statuses,id_status',
            'classification_ids' => 'required|array|min:0',
            'classification_ids.*' => 'exists:classifications,id_classification',

            'entite_accreditee_ids' => 'required|array|min:0',
            'entite_accreditee_ids.*' => 'exists:entite_accreditees,id_entite_accreditee',

            'domaine_intervention_ids' => 'required|array|min:0',
            'domaine_intervention_ids.*' => 'exists:domaine_interventions,id_domaine_intervention',

            'description'              => 'nullable|string',
            'date_debut'               => 'nullable|date',
            'date_fin'                 => 'nullable|date',
            'latitude'                 => 'nullable|numeric|between:-90,90',
            'longitude'                => 'nullable|numeric|between:-180,180',
            'province_id'              => 'nullable|integer|exists:provinces,id',
            'region_id'                => 'nullable|integer|exists:regions,id',
            'district_id'              => 'nullable|integer|exists:districts,id',
            'commune_id'               => 'nullable|integer|exists:communes,id',
            'fokontany_id'             => 'nullable|integer|exists:fokontany,id',
            'zone_description'         => 'nullable|string',
            'geo_address'              => 'nullable|string|max:500',
            'objectifs'                => 'nullable|string',
            'impact'                   => 'nullable|string',
            'problematique_climatique' => 'nullable|string',
            'is_published'             => 'nullable|boolean',
            'nombre_beneficiaires'     => 'nullable|integer',
        ];


        if ($isUpdate) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
            $rules['titre']  = 'sometimes|string|max:255';
        } else {
            $rules['titre']             = 'required|string|max:255';
        }

        return $rules;
    }

    // ADMIN: Créer
    public function store(Request $request)
    {
        $this->sanitize($request);
        $validated = $request->validate($this->validationRules(false));
        $validated['is_published'] = $validated['is_published'] ?? false;

        $classifications = $validated['classification_ids'];
        $entites = $validated['entite_accreditee_ids'];
        $domaines = $validated['domaine_intervention_ids'];

        unset(
            $validated['classification_ids'],
            $validated['entite_accreditee_ids'],
            $validated['domaine_intervention_ids']
        );

        $project = Projet::create($validated);

        $project->classifications()->sync($classifications);
        $project->entiteAccreditees()->sync($entites);
        $project->domainesIntervention()->sync($domaines);
        $this->logService->log(
            'create', 'projet',
            "Projet créé : {$project->titre}",
            $project->id_projet
        );

        return response()->json([
            'message' => 'Créé',
            'data' => $project,
        ]);
    }

    public function update(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);

        $request->validate([
            'titre' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
            'status_id' => 'required|integer',
            'region_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'commune_id' => 'nullable|integer',
            'fokontany_id' => 'nullable|integer',
            'nombre_beneficiaires' => 'nullable|integer',

        ]);

        $projet->update($request->all());
        $projet->classifications()->sync($request->classification_ids);
        $projet->entiteAccreditees()->sync($request->entite_accreditee_ids);
        $projet->domainesIntervention()->sync($request->domaine_intervention_ids);

        return response()->json([
            'message' => 'Modifié',
            'data' => $projet,
        ]);
    }

    public function destroy($id)
    {
        $projet = Projet::findOrFail($id);
        $projet->delete();

        return response()->json([
            'message' => 'Supprimé',
        ]);
    }
    public function projectsNumber(){
        $count = Projet::count();
    
        return response()->json([
            'count' => $count
        ]); 
    }
   public function projectsNumberActive() {
    $count = Projet::where('status_id', 1)->count();
    
    return response()->json([
        'count' => $count
    ]);
}

    public function projectsfilter($request){
        $projets = Projet::with([
            'utilisateur',
            'status',
            'classification', 
            'zoneGeographique',
            'updater'])
                    ->get();
        return response()->json($projets);
    }   
    public function mapData(): JsonResponse
{
    // Chargement de tous les projets publiés
    $projects = Projet::with([
        'region', 
        'status', 
        'classifications', 
        'entiteAccreditees', 
        'domainesIntervention'
    ])
    ->where('is_published', true)
    ->get();

    // 1. Regroupement par Région (RegionMapPoint[])
    $regionsData = $projects
        ->whereNotNull('region_id')
        ->groupBy('region_id')
        ->map(function ($regionProjects) {
            $first = $regionProjects->first();
            $region = $first->region;

            return [
                'region_id' => (int) $first->region_id,
                'region'    => $region?->nom ?? 'Inconnue',
                'latitude'  => (float) ($region?->latitude ?? $first->latitude ?? 0),
                'longitude' => (float) ($region?->longitude ?? $first->longitude ?? 0),
                'projects'  => $regionProjects->map(fn (Projet $p) => [
                    'id'                    => (int) $p->id_projet,
                    'id_projet'             => (string) $p->id_projet,
                    'titre'                 => $p->titre,
                    'statut'                => $p->status?->designation ?? $p->status?->libelle,
                    'classifications'       => $p->classifications->pluck('nom')->toArray(),
                    'entites_accreditees'   => $p->entiteAccreditees->pluck('nom')->toArray(),
                    'domaines_intervention' => $p->domainesIntervention->pluck('nom')->toArray(),
                ])->values()->all(),
            ];
        })
        ->values()
        ->all();

    // 2. Extraction des zones / coordonnées géographiques spécifiques (ProjectZoneMapPoint[])
    $projectZonesData = $projects
        ->filter(fn (Projet $p) => !is_null($p->latitude) && !is_null($p->longitude))
        ->map(fn (Projet $p) => [
            'id'                    => (int) $p->id_projet,
            'id_projet'             => (string) $p->id_projet,
            'titre'                 => $p->titre,
            'statut'                => $p->status?->designation ?? $p->status?->libelle,
            'classifications'       => $p->classifications->pluck('nom')->toArray(),
            'entites_accreditees'   => $p->entiteAccreditees->pluck('nom')->toArray(),
            'domaines_intervention' => $p->domainesIntervention->pluck('nom')->toArray(),
            'zone_points'           => [
                [
                    'latitude'  => (float) $p->latitude,
                    'longitude' => (float) $p->longitude,
                ]
            ],
        ])
        ->values()
        ->all();

    // 3. Assemblage de la réponse conforme à MapDataResponse
    return response()->json([
        'regions'               => $regionsData,
        'project_zones'         => $projectZonesData,
        'total_projects_on_map' => $projects->count(),
    ]);
}
    public function advanceStep(Request $request, $id): JsonResponse
    {
        $request->validate([
            'step' => 'required|integer'
        ]);

        $projet = Projet::findOrFail($id);

        if ($request->step > $projet->wizard_step) {
            $projet->update([
                'wizard_step' => $request->step
            ]);
        }

        return response()->json([
            'message' => 'Étape mise à jour',
            'data' => $projet
        ]);
    }
    public function updateGeo(Request $request, $id): JsonResponse
{
    $data = $request->validate([
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'region_id' => 'nullable',
        'district_id' => 'nullable',
        'commune_id' => 'nullable',
        'fokontany_id' => 'nullable',
        'zone_description' => 'nullable|string',
    ]);

    $projet = Projet::findOrFail($id);

    $projet->update($data);

    return response()->json([
        'message' => 'Zone mise à jour',
        'data' => $projet
    ]);
}
}
