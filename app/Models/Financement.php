<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Financement extends Model
{
    use HasFactory;

    protected $table = 'financements';

    protected $fillable = [
        'project_id',
        'type_financement',
        'mode_contribution',
        'source_financement',
        'budget_approuve',
        'devise',
        'montant_mga',
        'date_approbation',
        'description',
        'categorie_contribution_id',
    ];

    protected function casts(): array
    {
        return [
            'date_approbation' => 'date:Y-m-d',
            'budget_approuve'  => 'decimal:2',
            'montant_mga'      => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'project_id', 'id_projet');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(FinancementContribution::class);
    }

    public function categorieContribution(): BelongsTo
    {
        return $this->belongsTo(ContributionCategorie::class, 'categorie_contribution_id');
    }
}