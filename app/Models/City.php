<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'cities';

    protected $fillable = [
        'id',
        'parent_id',
        'name',
        'latitude',
        'longitude',
        'slug',
        'level',
        'tags',
        'bbox',
        'radius',

    ];
    protected $casts = [
        'tags' => 'array',
        'bbox' => 'array',
    ];
}
