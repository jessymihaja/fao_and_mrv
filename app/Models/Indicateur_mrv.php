<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicateur_mrv extends Model
{
    protected $table = 'indicateur_mrvs';
    protected $fillable = ['id', 'nom', 'unite', 'frequence'];
}
