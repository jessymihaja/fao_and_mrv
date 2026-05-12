<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'financement_id',
        'note',
        'montant',
        'date',
        'beneficiaire',
        'categorie',
        'reference',
        'justification_path',
        'justification_name',
        'created_by',
    ];

    protected function casts(): array
    {
        return ['date' => 'date', 'montant' => 'decimal:2'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }

    public function financement(): BelongsTo
    {
        return $this->belongsTo(Financement::class);
    }
}
