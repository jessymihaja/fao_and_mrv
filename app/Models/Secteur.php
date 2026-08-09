<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
    ];

    // Masquer les champs non nécessaires si le front n'attend que id et designation
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
