<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultType extends Model
{
    use HasFactory;

    protected $table = 'result_types';

    protected $fillable = [
        'designation',
    ];
}
