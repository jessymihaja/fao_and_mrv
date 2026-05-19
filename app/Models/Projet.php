<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Status;
use App\Models\Classification;
use App\Models\ZoneGeographique;
use App\Models\EntiteAccreditee;
use App\Models\DomaineIntervention;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Projet extends Model
{
    protected $table = 'projets';

    protected $primaryKey = 'id_projet';

    public $timestamps = false;

    protected $fillable = [
        'titre',
        'status_id',
        'classification_id',
        'entite_accreditee_id',
        'description',
        'domaine_intervention_id',
        'date_debut',
        'date_fin',
        'latitude',
        'longitude',
        'zone_description',
        'is_published',
        'province_id',
        'region_id',
        'district_id',
        'commune_id',
        'fokontany_id'
        

    ];

    public function status() : BelongsTo {
        return $this->belongsTo(Status::class, 'status_id', 'id_status');
    }

    public function classification() : BelongsTo {
        return $this->belongsTo(Classification::class, 'classification_id', 'id_classification');
    }

    public function entiteAccreditee() : BelongsTo {
        return $this->belongsTo(EntiteAccreditee::class, 'entite_accreditee_id', 'id_entite_accreditee');
    }

    public function domaineIntervention(): BelongsTo {
        return $this->belongsTo(DomaineIntervention::class, 'domaine_intervention_id', 'id_domaine_intervention');
    }
    public function province():  BelongsTo { return $this->belongsTo(Province::class); }
    public function region():    BelongsTo { return $this->belongsTo(Region::class); }
    public function district():  BelongsTo { return $this->belongsTo(District::class); }
    public function commune():   BelongsTo { return $this->belongsTo(Commune::class); }
    public function fokontany(): BelongsTo { return $this->belongsTo(Fokontany::class); }

    public function financements(): HasMany { return $this->hasMany(Financement::class,'projet_id', 'id_projet'); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }

}
