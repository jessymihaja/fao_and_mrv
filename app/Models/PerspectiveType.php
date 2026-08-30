<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerspectiveType extends Model
{
    protected $fillable = ['designation'];

    public function perspectives(): HasMany
    {
        return $this->hasMany(ProjectPerspective::class, 'type_id');
    }
}
