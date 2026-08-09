<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectIdeaDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_idea_id', 'type', 'libelle', 'file_path', 'file_name', 'mime_type', 'size'
    ];

    protected $hidden = ['file_path'];

    public function projectIdea()
    {
        return $this->belongsTo(ProjectIdea::class);
    }
}