<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $table = 'shipping_methods';

    protected $fillable = [
        'repo_id',
        'agency_id',
        'status',
        'products',
        'cities',
        'per_weight_price',
        'per_distance_price',
        'min_order_weight',
        'base_price',
        'name',
        'pay_type',
        'description',
        'free_from_price',
        'timestamps',
        'shipping_agency_id',
    ];

    protected $casts = [
        'products' => 'array',
        'cities' => 'array',
        'timestamps' => 'array',
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class, 'repo_id');
    }

    public function shippingAgency()
    {
        return $this->belongsTo(Agency::class, 'shipping_agency_id');
    }
}
