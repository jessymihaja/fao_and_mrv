<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetMobilisation extends Model
{
    protected $guarded = ['id'];
    protected $with = ['organismeContributeur', 'categorieContribution', 'composante', 'activite'];

    public function financement() { return $this->belongsTo(Financement::class); }
    public function composante() { return $this->belongsTo(Composante::class); }
    public function activite() { return $this->belongsTo(Activite::class); }
    public function organismeContributeur() { return $this->belongsTo(OrganismeContributeur::class, 'organisme_contributeur_id'); }
    public function categorieContribution() { return $this->belongsTo(ContributionCategorie::class, 'categorie_contribution_id'); }
}