<?php

namespace App\Models;

use App\Http\Helpers\Variable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_admin_id',
        'to_admin_id',
        'from_repo_id',
        'from_agency_id',
        'to_repo_id',
        'to_agency_id',
        'from_province_id',
        'to_province_id',
        'from_county_id',
        'to_county_id',
        'from_district_id',
        'to_district_id',
        'to_fullname',
        'to_phone',
        'from_fullname',
        'from_phone',
        'from_location',
        'to_location',
        'from_postal_code',
        'to_postal_code',
        'from_address',
        'to_address',
        'status',
        'total_shipping_price',
        'total_items_price',
        'total_items',
        'total_price',
        'total_discount',
        'shipping_id',
        'shipping_method_id',
        'change_price',
        'coupon',
        'tax_price',
        'total_weight',
    ];

    public function items()
    {
        return $this->hasMany(RepositoryOrderItem::class, 'order_id');
    }

    public function getAvailableStatuses()
    {
        $statuses = collect(Variable::ORDER_STATUSES)->map(function ($e) {
            if ($e['name'] == 'canceled')
                $e['message'] = __('accept_cancel_order');
            elseif ($e['name'] == 'ready')
                $e['message'] = __('order_will_be_ready_send');
            elseif ($e['name'] == 'refunded')
                $e['message'] = __('order_will_cancel_and_refunded');
            elseif ($e['name'] == 'sending')
                $e['message'] = __('order_will_be_sending');
            elseif ($e['name'] == 'processing')
                $e['message'] = __('order_will_be_processing');
            elseif ($e['name'] == 'delivered')
                $e['message'] = __('order_will_be_delivered');
            elseif ($e['name'] == 'rejected')
                $e['message'] = __('order_will_cancel_and_refunded');

            return $e;
        });
        switch ($this->status) {
            case    'pending':
                return $statuses->whereIn('name', ['canceled']);
            case    'processing':
                return $statuses->whereIn('name', ['ready', 'refunded']);
            case    'ready':
                return $statuses->whereIn('name', ['processing', 'delivered', 'refunded']);
            case    'sending':
                return $statuses->whereIn('name', ['ready', 'delivered', 'rejected']);
            case    'delivered':
            case    'canceled':
                return $statuses->whereIn('name', []);
            case    'rejected':
                return $statuses->whereIn('name', []);
            case    'refunded':
                return $statuses->whereIn('name', []);
            default:
                return $statuses->whereIn('name', []);
        }
    }
}
