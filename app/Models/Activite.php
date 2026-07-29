<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    protected $table = 'activites';
    protected $fillable = ['id','projet_id', 'composante_id', 'nom', 'description'];

    public function composante()
    {
        return $this->belongsTo(Composante::class, 'composante_id', 'id');
    }
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id', 'id_projet');
    }
    public function indicateurs()
    {
        return $this->hasMany(Indicateur_mrv::class, 'activite_id', 'id');
    }
}
