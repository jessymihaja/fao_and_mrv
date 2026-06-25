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
            'classification'])
                    ->get();

        return response()->json($projets);
    }
   public function getPaginatedProjects(Request $request) { 
    $perPage = $request->per_page ?? 15; $projets = Projet::with([ 'status', 
    'classification', 
    'entiteAccreditee', 
    'domaineIntervention', 
    ]) ->withSum( ['financements as budget_total' => function ($query) { $query->selectRaw('COALESCE(SUM(montant_mga * budget_approuve), 0)'); }], \DB::raw('montant_mga * budget_approuve') ) ->when( $request->filled('search'),
     fn ($q) => $q->where(function ($s) use ($request) { 
        $s->where('titre', 'ilike', "%{$request->search}%") 
        ->orWhere('description', 'ilike', "%{$request->search}%"); }) ) 
        ->when( $request->filled('statut'), fn ($q) => $q->whereHas('status', function ($query) use ($request) { $query->where('designation', $request->statut); }) )
        ->paginate($perPage); return response()->json($projets); }

    public function show($id) {
    $projet = Projet::with([
        'status', 
        'classification', 
        'entiteAccreditee', 
        'domaineIntervention', 
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
            'domaine_intervention_id',
            'classification_id',
            'status_id',
            'entite_accreditee_id',
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
            'classification_id'        => 'required|integer|exists:classifications,id_classification',
            'entite_accreditee_id'     => 'required|integer|exists:entite_accreditees,id_entite_accreditee',
            'domaine_intervention_id'  => 'required|integer|exists:domaine_interventions,id_domaine_intervention',
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

        $project = Projet::create($validated);
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
            'classification_id' => 'required|integer',
            'status_id' => 'required|integer',
            'region_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'commune_id' => 'nullable|integer',
            'fokontany_id' => 'nullable|integer',
            'entite_accreditee_id' => 'required|integer',
            'domaine_intervention_id' => 'required|integer',
        ]);

        $projet->update($request->all());

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
        $projects = Projet::with('region', 'domaineIntervention', 'status')
            ->where('is_published', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(['id_projet', 'titre', 'status_id', 'latitude', 'longitude', 'region_id', 'domaine_intervention_id'])
            ->get()
            ->map(fn (Projet $p) => [
                'id'                 => $p->id_projet,
                'titre'              => $p->titre,
                'statut'             => $p->status?->designation,
                'latitude'           => (float) $p->latitude,
                'longitude'          => (float) $p->longitude,
                'region'             => $p->region?->nom,
                'secteur_climatique' => $p->domaineIntervention?->designation,
            ]);

        return response()->json($projects);
    }
}
