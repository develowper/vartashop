<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'weight',
        'height',
        'width',
        'length',
        'price',
    ];
}
