<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapportNational extends Model
{
    use HasFactory;

    protected $table = 'rapports_nationaux';

    protected $fillable = [
        'titre',
        'annee',
        'region_id',
        'secteur_climatique',
        'accredited_entity',
        'source_financement',
        'statut_projet',
        'statut',
        'contenu',
        'created_by',
    ];

    protected $casts = [
        'annee' => 'integer',
        'contenu' => 'array', // Cast automatique du JSON en Array/Object JS
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $table->belongsTo(Utilisateur::class, 'created_by');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}