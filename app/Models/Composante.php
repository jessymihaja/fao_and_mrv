<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Projet;
use App\Models\Activite;
use App\Models\Resultat_mrv;
class Composante extends Model
{
    protected $table = 'composantes';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'projet_id', 'nom', 'description'];

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id', 'id_projet');
    }
    public function activites()
    {
        return $this->hasMany(Activite::class, 'composante_id', 'id');
    }
    public function indicateurs()
    {
        return $this->hasMany(Resultat_mrv::class, 'composante_id', 'id');
    }
}
