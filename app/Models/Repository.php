<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'agency_id',
        'admin_id',
        'province_id',
        'county_id',
        'district_id',
        'address',
        'location',
        'cities',
        'is_shop',
        'allow_visit',
        'phone',
        'status',
        'postal_code',
    ];

    protected $casts = [
        'cities' => 'array',

        'allow_visit' => 'boolean',
        'is_shop' => 'boolean',
    ];

    public function shippingMethods()
    {
        return $this->hasMany(ShippingMethod::class, 'repo_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
