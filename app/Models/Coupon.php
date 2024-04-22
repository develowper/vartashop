<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'coupons';
    protected $fillable = [
        'id',
        'user_id',
        'code',
        'discount_percent',
        'limit_discount',
        'min_price',
        'limit_use',
        'created_at',
        'used_at',
        'used_times',
        'expires_at',
    ];
    protected $casts = [

//        'expires_at' => 'timestamp',
        'used_at' => 'timestamp',
    ];


    public function makeDiscount($price)
    {

        if ($price == 0 || !$this)
            return $price;
        $user_id = optional(auth()->user() ?: auth('api')->user())->id;
        if (!$this || $this->used_at != null || $this->discount_percent == 0 || ($this->expires_at && $this->expires_at < \Carbon\Carbon::now()->timestamp) || \App\Models\Payment::whereNotNull('user_id')->where('user_id', $user_id)->where('coupon_id', $this->id)->exists())
            return $price;
        if ($this->discount_percent == 100)
            return 0;
        $discount = round($price * $this->discount_percent / 100);
        if ($this->limit_price && $this->limit_price < $discount)
            return $price - $this->limit_price;
        return $price - $discount;

    }
}
