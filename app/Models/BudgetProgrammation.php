<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetProgrammation extends Model
{
    protected $table = 'budget_programmations';
    protected $guarded = ['id'];
    protected $with = ['composante', 'activite'];

    public function financement() { return $this->belongsTo(Financement::class); }
    public function composante() { return $this->belongsTo(Composante::class); }
    public function activite() { return $this->belongsTo(Activite::class); }
}