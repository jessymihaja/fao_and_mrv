<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneGeographique extends Model
{
    protected $table = 'zone_geographiques';

    protected $primaryKey = 'id_zone_geographique';

    public $timestamps = false;

    protected $fillable = [
        'nom',
        'code',
        'latitude',
        'longitude'
    ];
}
