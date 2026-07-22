<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Projet;

class DomaineIntervention extends Model
{
    protected $table = 'domaine_interventions';

    protected $primaryKey = 'id_domaine_intervention';

    public $timestamps = false;

    protected $fillable = [
        'designation',
        'description',
    ];
    public function projets(): BelongsToMany
{
    return $this->belongsToMany(
        Projet::class,
        'domaine_intervention_projet',
        'domaine_intervention_id',
        'projet_id',
        'id_domaine_intervention',
        'id_projet'
    );
}
}
