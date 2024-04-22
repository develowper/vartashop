<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip',
        'last_activity',
        'order_id',
        'address_idx',
    ];

    public static function getData()
    {
        $user = auth('sanctum')->user();
        $ip = request()->ip();

        if ($user)
            return Cart::where('user_id', $user->id)->with('items')->first();
        if ($ip)
            return Cart::where('ip', $ip)->with('items')->first();
        return [];
    }

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}
