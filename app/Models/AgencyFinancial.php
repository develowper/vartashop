<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyFinancial extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'wallet',
        'card',
        'sheba',
        'parent_debit',
    ];
}
