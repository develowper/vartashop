<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'tags',
        'order_count',
        'in_shop',
        'charged_at',
        'rate',
        'status',
        'description',
    ];
    protected $casts = [

    ];

}
