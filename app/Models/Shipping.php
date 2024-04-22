<?php

namespace App\Models;

use App\Http\Helpers\Variable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'car_id',
        'agency_id',
        'status',
        'order_qty',
        'order_delivered_qty',
    ];

    public function getAvailableStatuses()
    {
        $statuses = collect(Variable::SHIPPING_STATUSES)->map(function ($e) {
            if ($e['name'] == 'canceled')
                $e['message'] = __('accept_cancel_shipping');
            elseif ($e['name'] == 'preparing')
                $e['message'] = __('shipping_will_be_preparing');
            elseif ($e['name'] == 'sending')
                $e['message'] = __('shipping_will_be_sending');
            elseif ($e['name'] == 'ended')
                $e['message'] = __('shipping_will_be_ended');

            return $e;
        });
        switch ($this->status) {
            case    'preparing':
                return $statuses->whereIn('name', ['sending', 'canceled']);
            case    'sending':
                return $statuses->whereIn('name', ['preparing', 'canceled']);

            default:
                return $statuses->whereIn('name', []);
        }
    }
}
