<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProjectPerspective extends Model
{
    protected $fillable = [
        'project_id',
        'type_id',
        'titre',
        'description',
        'zone_extension_envisagee',
        'objectif_moyen_terme',
        'objectif_long_terme',
        'impact_futur_attendu',
        'statut',
        'created_by'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'project_id', 'id_projet');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PerspectiveType::class, 'type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'created_by', 'id_utilisateur');
    }
}
