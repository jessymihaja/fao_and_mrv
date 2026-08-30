<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Result extends Model
{
    public static $snakeAttributes = false;
    use HasFactory;

    protected $fillable = [
        'project_id',
        'composante_id',
        'activite_id',
        'indicateur_id',
        'result_type_id',
        'titre',
        'description',
        'reference_year',
        'target_year',
        'statut',
        'valeur_reference',
        'source_verification',
        'methode_collecte',
        'observations',
    ];

    // --- Relations directes aux noms exacts du TS ---

    public function resultType(): BelongsTo
    {
        return $this->belongsTo(ResultType::class, 'result_type_id');
    }

    public function indicateur(): BelongsTo
    {
        return $this->belongsTo(Resultat_mrv::class, 'indicateur_id');
    }

    public function composante(): BelongsTo
    {
        return $this->belongsTo(Composante::class, 'composante_id')->select(['id', 'nom']);
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class, 'activite_id')->select(['id', 'nom']);
    }

    public function pieces_jointes(): HasMany
    {
        return $this->hasMany(ResultPieceJointe::class, 'result_id');
    }
}
