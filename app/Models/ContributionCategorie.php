<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContributionCategorie extends Model
{
    use HasFactory;

    protected $table = 'contribution_categories';

    protected $fillable = [
        'designation',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
