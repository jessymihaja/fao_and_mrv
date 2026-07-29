<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resultat_mrv extends Model
{
    protected $table = 'resultat_mrvs';
    protected $fillable = ['id','projet_id','composante_id','activite_id', 'indicateur_mrv_id', 'valeur_cible', 'valeur_realise', 'annee'];

    public function indicateur_mrv(): BelongsTo
    {
        return $this->belongsTo(Indicateur_mrv::class);
    }
    public function projet(): BelongsTo
    {
        return $this->belongsTo(
        Projet::class,
        'projet_id',
        'id_projet'
    );
    }
}
