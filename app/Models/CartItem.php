<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'repo_id',
        'cart_id',
        'variation_id',
        'shipping_id',
        'delivery_date',
        'delivery_timestamp',
        'qty',
    ];

    public function product()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
