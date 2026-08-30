<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ResultPieceJointe extends Model
{
   use HasFactory;

    protected $table = 'result_pieces_jointes';

    protected $fillable = [
        'result_id',
        'fichier',
        'nom_original',
        'taille',
    ];

    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class, 'result_id');
    }
}
