<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFinancial extends Model
{
    use HasFactory;

    protected $table = 'user_financials';

    protected $fillable = [
        'user_id',
        'card',
        'sheba',
    ];
}
