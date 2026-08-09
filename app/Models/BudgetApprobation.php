<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetApprobation extends Model
{
    protected $guarded = ['id'];
    protected $with = ['organisme', 'composante', 'activite'];

    public function financement() { return $this->belongsTo(Financement::class); }
    public function composante() { return $this->belongsTo(Composante::class); }
    public function activite() { return $this->belongsTo(Activite::class); }
    public function organisme() { return $this->belongsTo(OrganismeContributeur::class, 'organisme_id'); }
}
