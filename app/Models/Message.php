<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'fullname',
        'phone',
        'type',
        'description',
        'created_at',
        'updated_at',
    ];
}
