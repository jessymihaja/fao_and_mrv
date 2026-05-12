<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Decaissement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['financement_id', 'date', 'montant', 'reference'];

    protected function casts(): array
    {
        return ['date' => 'date', 'montant' => 'decimal:2'];
    }

    public function financement(): BelongsTo
    {
        return $this->belongsTo(Financement::class);
    }
}
