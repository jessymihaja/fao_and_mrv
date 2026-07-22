<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Projet;

class EntiteAccreditee extends Model
{
    protected $table = 'entite_accreditees';

    protected $primaryKey = 'id_entite_accreditee';

    public $timestamps = false;

    protected $fillable = [
        'designation',
        'sigle',
    ];

    public function projets(): BelongsToMany
{
    return $this->belongsToMany(
        Projet::class,
        'entite_accreditee_projet',
        'entite_accreditee_id',
        'projet_id',
        'id_entite_accreditee',
        'id_projet'
    );
}
}
