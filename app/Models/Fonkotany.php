<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fonkotany extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'commune_id'];

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }
}
