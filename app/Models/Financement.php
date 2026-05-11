<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Projet;
use App\Models\Devise;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Financement extends Model
{
    protected $table = 'financements';

    use HasFactory;

    protected $fillable = [
        'projet_id',
        'source_financement',
        'budget_approuve',
        'devise',
        'montant_mga',
        'date_approbation',
    ];

    protected function casts(): array
    {
        return [
            'date_approbation' => 'date',
            'budget_approuve'  => 'decimal:2',
            'montant_mga'      => 'decimal:2',
        ];
    }

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

}
