<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectIdeaStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_idea_id', 'ancien_statut', 'nouveau_statut', 'commentaire', 'auteur'
    ];

    public function projectIdea()
    {
        return $this->belongsTo(ProjectIdea::class);
    }
}