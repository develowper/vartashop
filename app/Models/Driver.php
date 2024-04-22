<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'national_code',
        'wallet',
        'phone',
        'card',
        'sheba',
        'agency_id',

    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}
