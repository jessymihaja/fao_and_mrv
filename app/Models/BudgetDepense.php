<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetDepense extends Model
{
    protected $guarded = ['id'];
    protected $with = ['composante', 'activite'];

    public function project() { return $this->belongsTo(Project::class); }
    public function financement() { return $this->belongsTo(Financement::class); }
    public function composante() { return $this->belongsTo(Composante::class); }
    public function activite() { return $this->belongsTo(Activite::class); }
}