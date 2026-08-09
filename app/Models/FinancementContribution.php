<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancementContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'financement_id',
        'organisme_contributeur_id',
        'mode_contribution',
        'montant',
        'devise',
        'montant_mga',
        'date_contribution',
        'categorie_contribution_id',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date_contribution' => 'date:Y-m-d',
            'montant'           => 'decimal:2',
            'montant_mga'       => 'decimal:2',
        ];
    }

    public function financement(): BelongsTo
    {
        return $this->belongsTo(Financement::class);
    }

    public function organismeContributeur(): BelongsTo
    {
        return $this->belongsTo(OrganismeContributeur::class, 'organisme_contributeur_id');
    }

    public function categorieContribution(): BelongsTo
    {
        return $this->belongsTo(ContributionCategorie::class, 'categorie_contribution_id');
    }
}