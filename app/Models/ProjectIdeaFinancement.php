<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectIdeaFinancement extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_idea_id', 'organisme_contributeur_id', 'bailleur',
        'bailleur_autre', 'montant_demande', 'devise', 'type_financement', 'statut'
    ];

    protected $casts = [
        'montant_demande' => 'float',
    ];

    public function projectIdea()
    {
        return $this->belongsTo(ProjectIdea::class);
    }
}