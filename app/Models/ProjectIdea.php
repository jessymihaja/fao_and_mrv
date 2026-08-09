<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectIdea extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'lien', 'acronyme', 'description', 'contexte', 'justification',
        'objectif_general', 'objectifs_specifiques', 'resultats_attendus',
        'duree_prevue_mois', 'date_debut_estimee', 'date_fin_estimee', 'porteur_projet',
        'latitude', 'longitude', 'province_id', 'region_id', 'district_id', 'commune_id', 'fokontany_id',
        'zone_description', 'geo_address',
        'nombre_beneficiaires', 'beneficiaires_hommes', 'beneficiaires_femmes',
        'beneficiaires_jeunes', 'beneficiaires_vulnerables',
        'budget_total_estime', 'devise', 'contribution_nationale',
        'contribution_partenaires', 'cofinancement_prive', 'autres_financements',
        'statut', 'converted_project_id', 'converted_at', 'created_by'
    ];

    protected $casts = [
        'duree_prevue_mois' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'budget_total_estime' => 'float',
        'contribution_nationale' => 'float',
        'contribution_partenaires' => 'float',
        'cofinancement_prive' => 'float',
        'autres_financements' => 'float',
        'converted_at' => 'datetime',
    ];

    protected $appends = [
        'total_contributions',
        'pourcentage_cofinancement',
        'bailleur_cible'
    ];

    // Accessors
    public function getTotalContributionsAttribute(): float
    {
        return (float) ($this->contribution_nationale + $this->contribution_partenaires + $this->cofinancement_prive + $this->autres_financements);
    }

    public function getPourcentageCofinancementAttribute(): ?float
    {
        if (!$this->budget_total_estime || $this->budget_total_estime == 0) {
            return 0;
        }
        return round(($this->total_contributions / $this->budget_total_estime) * 100, 2);
    }

    public function getBailleurCibleAttribute(): ?string
    {
        $firstFinancement = $this->financements->first();
        return $firstFinancement ? $firstFinancement->bailleur : null;
    }

    // Relations
    public function secteurs()
    {
        return $this->belongsToMany(Secteur::class, 'project_idea_secteur');
    }

    public function province() { return $this->belongsTo(Province::class); }
    public function region() { return $this->belongsTo(Region::class); }
    public function district() { return $this->belongsTo(District::class); }
    public function commune() { return $this->belongsTo(Commune::class); }
    public function fokontany() { return $this->belongsTo(Fonkotany::class); }

    public function financements()
    {
        return $this->hasMany(ProjectIdeaFinancement::class);
    }

    public function documents()
    {
        return $this->hasMany(ProjectIdeaDocument::class);
    }

    public function status_history()
    {
        return $this->hasMany(ProjectIdeaStatusHistory::class)->orderBy('created_at', 'desc');
    }
}