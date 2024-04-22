<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


    public $timestamps = true;
    protected $table = 'payments';
    protected $fillable = [
        'id', 'user_id', 'info', 'app_version', /*'inviter_user_id', 'coupon',*/
        'order_id', 'amount', 'pay_market', 'pay_for', 'pay_result', 'is_success', 'created_at', 'updated_at'
    ];
    protected $casts = [

    ];
}
