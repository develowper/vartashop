<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;


    protected $fillable = [
        'parent_id',
        'name',
        'phone',
        'postal_code',
        'access',
        'level',
        'wallet',
//        'has_shop',
//        'owner_id',
        'parent_id',
        'province_id',
        'county_id',
        'district_id',
        'address',
        'location',
        'status',
        'order_profit_percent',
    ];
    protected $casts = [
        'access' => 'array'
    ];

    public function repositories()
    {
        return $this->hasMany(Repository::class, 'agency_id');
    }

    public function owner()
    {
        return $this->hasOne(Admin::class, 'agency_id');
    }

    public function financial()
    {
        return $this->hasOne(AgencyFinancial::class, 'agency_id');
    }
}
