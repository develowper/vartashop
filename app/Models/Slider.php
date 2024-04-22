<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'description',
        'is_active',
        'created_at',
        'updated_at',
    ];


}
