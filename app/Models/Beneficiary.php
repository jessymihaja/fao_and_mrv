<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiary extends Model
{
    use HasFactory;
    public static $snakeAttributes = false;

    protected $fillable = [
        'project_id',
        'beneficiary_type_id',
        'beneficiary_category_id',
        'region_id',
        'district_id',
        'commune_id',
        'fokontany_id',
        'description',
        'planned_count',
        'achieved_count',
        'women_count',
        'men_count',
        'youth_count',
        'vulnerable_count',
        'reference_year',
        'monitoring_year',
        'source',
        'observations',
    ];

    protected $casts = [
        'planned_count' => 'integer',
        'achieved_count' => 'integer',
        'women_count' => 'integer',
        'men_count' => 'integer',
        'youth_count' => 'integer',
        'vulnerable_count' => 'integer',
        'reference_year' => 'integer',
        'monitoring_year' => 'integer',
    ];

    protected $appends = ['taux_atteinte'];

    // Attribut calculé automatiquement
    public function getTauxAtteinteAttribute(): float
    {
        if (!$this->planned_count || $this->planned_count == 0) {
            return 0;
        }

        return round(($this->achieved_count / $this->planned_count) * 100, 2);
    }

    // Relations
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id_projet');
    }

    public function beneficiaryType()
    {
        return $this->belongsTo(BeneficiaryType::class, 'beneficiary_type_id');
    }

    public function beneficiaryCategory()
    {
        return $this->belongsTo(BeneficiaryCategory::class, 'beneficiary_category_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function fokontany()
    {
        return $this->belongsTo(Fonkotany::class);
    }
}
