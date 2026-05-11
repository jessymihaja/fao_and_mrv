<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Commune extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'code',
        'district_id',
    ];

    public function district(): BelongsTo
    {
        return $this->hasMany(District::class);
    }

    public function fonkotany(): HasMany
    {
        return $this->hasMany(Fonkotany::class);
    }
}
