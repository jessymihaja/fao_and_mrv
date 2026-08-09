<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetPledge extends Model
{
    protected $guarded = ['id'];
    protected $with = ['bailleur', 'composante', 'activite'];

    public function financement() { return $this->belongsTo(Financement::class); }
    public function composante() { return $this->belongsTo(Composante::class); }
    public function activite() { return $this->belongsTo(Activite::class); }
    public function bailleur() { return $this->belongsTo(OrganismeContributeur::class, 'bailleur_id'); }
}