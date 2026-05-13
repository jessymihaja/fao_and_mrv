<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Document extends Model
{
     use HasFactory;

    protected $fillable = [
        'titre', 'type', 'fichier', 'fichier_original', 'taille',
        'mime_type', 'project_id', 'financement_id', 'description', 'uploaded_by',
    ];

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class , 'project_id', 'id_projet');
    }

    public function financement(): BelongsTo
    {
        return $this->belongsTo(Financement::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
