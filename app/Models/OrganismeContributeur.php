<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganismeContributeur extends Model
{
    use HasFactory;

    protected $table = 'organisme_contributeurs';

    protected $fillable = [
        'designation',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function financements()
    {
        return $this->hasMany(ProjectIdeaFinancement::class, 'organisme_contributeur_id');
    }
}
