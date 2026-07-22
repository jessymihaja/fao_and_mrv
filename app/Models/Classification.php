<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Projet;

class Classification extends Model
{
    protected $table = 'classifications';

    protected $primaryKey = 'id_classification';

    public $timestamps = false;

    protected $fillable = [
        'designation',
    ];

    public function projets(): BelongsToMany
{
    return $this->belongsToMany(
        Projet::class,
        'classification_projet',
        'classification_id',
        'projet_id',
        'id_classification',
        'id_projet'
    );
}
}
