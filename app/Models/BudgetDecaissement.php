<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetDecaissement extends Model
{
    protected $guarded = ['id'];
    protected $with = ['composante', 'activite'];

    public function financement() { return $this->belongsTo(Financement::class); }
    public function composante() { return $this->belongsTo(Composante::class); }
    public function activite() { return $this->belongsTo(Activite::class); }
}