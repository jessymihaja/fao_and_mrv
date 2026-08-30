<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Resultat_mrv extends Model
{
    protected $table = 'resultat_mrvs';
    protected $fillable = ['id','projet_id','composante_id','activite_id', 'indicateur_mrv_id', 'valeur_cible', 'valeur_realise', 'annee'];

    public function indicateur_mrv(): BelongsTo
    {
        return $this->belongsTo(Indicateur_mrv::class);
    }
    public function resultType(): BelongsTo
    {
        return $this->belongsTo(ResultType::class, 'result_type_id');
    }
    public function projet(): BelongsTo
    {
        return $this->belongsTo(
        Projet::class,
        'projet_id',
        'id_projet'
    );
    }

    protected $appends = ['date_reference', 'valeur_realisee','pourcentage_atteinte'];

    // 1. Nom en CamelCase pour 'date_reference'
    protected function dateReference(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->annee ? "{$this->annee}-01-01" : null,
        );
    }

    // 2. Nom en CamelCase pour 'valeur_realisee'
    protected function valeurRealisee(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attributes['valeur_realise'] ?? null,
        );
    }


    protected function pourcentageAtteinte(): Attribute
    {
        return Attribute::make(
            get: function () {
                $cible = $this->attributes['valeur_cible'] ?? null;
                $realise = $this->attributes['valeur_realise'] ?? null;

                if ($cible && $realise && (float)$cible > 0) {
                    return round(((float)$realise / (float)$cible) * 100, 2);
                }

                return null;
            }
        );
    }
}
