<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryCartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'visit_checked',
        'repo_id',
        'cart_id',
        'variation_id',
        'shipping_id',
        'qty',
    ];

    public function product()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
