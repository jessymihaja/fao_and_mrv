<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'titre',
        'type',
        'fichier',
        'fichier_original',
        'taille',
        'mime_type',
        'project_id',
        'composante_id',
        'financement_id',
        'description',
        'uploaded_by',
    ];

    protected $casts = [
        'taille' => 'integer',
        'project_id' => 'integer',
        'composante_id' => 'integer',
        'financement_id' => 'integer',
        'uploaded_by' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Projet::class, 'project_id', 'id_projet');
    }

    public function composante()
    {
        return $this->belongsTo(Composante::class);
    }

    public function financement()
    {
        return $this->belongsTo(Financement::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}