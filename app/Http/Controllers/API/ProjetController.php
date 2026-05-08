<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use Illuminate\Http\Request;

class ProjetController extends Controller
{
    public function index()
    {
        $projets = Projet::with([
            'utilisateur',
            'status',
            'classification', 
            'zoneGeographique',
            'updater'])
                    ->get();

        return response()->json($projets);
    }
    public function getPaginatedProjects(Request $request)
{
    $perPage = $request->per_page ?? 15;

    $projets = Projet::with([
        'utilisateur',
        'status',
        'classification',
        'zoneGeographique',
        'updater'
    ])->paginate($perPage);

    $projets->getCollection()->transform(function ($projet) {

        return [
            'id' => $projet->id_projet,
            'titre' => $projet->nom,

            // IMPORTANT : string et non objet
            'classification' => $projet->classification?->designation,

            'status' => $projet->status?->designation,

            'zoneGeographique' => $projet->zoneGeographique?->designation,

            'utilisateur' => $projet->utilisateur,
            'updater' => $projet->updater,

            'created_at' => $projet->created_at,
        ];
    });

    return response()->json($projets);
}

    public function show($id) {
    // On ajoute toutes les relations manquantes dans le 'with'
    $projet = Projet::with([
        'utilisateur', 
        'status', 
        'classification', 
        'zoneGeographique', 
        'entiteAccreditee', 
        'domaineIntervention', 
        'updater'
    ])->findOrFail($id);

    // Retourne l'objet directement
    return response()->json($projet);
}

    public function store(Request $request)
    {
        $request->validate([
            'id_utilisateur' => 'required|integer',
            'nom' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
            'classification_id' => 'required|integer',
            'status_id' => 'required|integer',
            'zone_geographique_id' => 'required|integer',
            'entite_accreditee_id' => 'required|integer',
            'domaine_intervention_id' => 'required|integer',
        ]);

        $projet = Projet::create($request->all());

        return response()->json([
            'message' => 'Créé',
            'data' => $projet,
        ]);
    }

    public function update(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);

        $request->validate([
            'id_utilisateur_updater' => 'required|integer',
            'nom' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
            'classification_id' => 'required|integer',
            'status_id' => 'required|integer',
            'zone_geographique_id' => 'required|integer',
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
}
