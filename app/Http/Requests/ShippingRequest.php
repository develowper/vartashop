<?php

namespace App\Http\Requests;

use App\Http\Helpers\Variable;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Order;
use App\Models\RepositoryOrder;
use App\Models\Shipping;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShippingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->myAgency = Agency::find($this->user()->agency_id);
        if (!$this->myAgency)
            abort(403, __("access_denied"));
        if ($this->myAgency->status != 'active')
            abort(403, __("your_agency_inactive"));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $user = $this->user();
        $editMode = (bool)$this->id;
        $tmp = [];

//        $allowedAgencies = $user->allowedAgencies(Agency::find($user->agency_id))->pluck('id');

        if (!$this->cmnd) {

            $allowedAgencies = $user->allowedAgencies($this->myAgency)->pluck('id');
            $this->merge([
                'agency_id' => $this->myAgency->level == '3' ? $user->agency_id : $this->agency_id
            ]);
            $tmp = array_merge($tmp, [
                'agency_id' => ['required', Rule::in($allowedAgencies),],
                'driver_id' => ['required', Rule::in(Driver::where('agency_id', $this->agency_id)->pluck('id'))],
                'car_id' => ['required', Rule::in(Car::where('driver_id', $this->driver_id)->pluck('id'))],
                'orders' => ['required', 'array', 'min:1'],
            ]);

            $userOrders = Order::whereIn('id', collect($this->orders)->where('type', 'user')->pluck('id'))->get();
            $agencyOrders = RepositoryOrder::whereIn('id', collect($this->orders)->where('type', 'agency')->pluck('id'))->get();
            $this->merge(['user_orders' => $userOrders->pluck('id'), 'agency_orders' => $agencyOrders->pluck('id')]);

            foreach ($this->orders ?? [] as $idx => $item) {
                $order = $item['type'] == 'user' ? $userOrders->where('id', $item['id'])->first() : $agencyOrders->where('id', $item['id'])->first();
                $tmp = array_merge($tmp, [
                    "orders.$idx.type" => ['required', Rule::in(['user', 'agency'])],
                    "orders.$idx.from_agency_id" => ['required', Rule::in($allowedAgencies), Rule::in(optional($order)->agency_id),],
                    "orders.$idx.status" => ['required', $editMode ? null : Rule::in(['ready'], Rule::in([$order->status]))],
                ]);

            }
        }
//        if ($this->cmnd == 'status') {
//            $tmp = array_merge($tmp, [
//                'status' => ['required', Rule::in(collect(Variable::ORDER_STATUSES)->pluck('name'))],
//            ]);
//        }
        return $tmp;
    }

    public function messages()
    {
        $tmp = [
            'agency_id.required' => sprintf(__("validator.required"), __('agency')),
            'agency_id.in' => sprintf(__("validator.invalid"), __('agency')),

            'driver_id.required' => sprintf(__("validator.required"), __('driver')),
            'driver_id.in' => sprintf(__("validator.invalid"), __('driver')),

            'car_id.required' => sprintf(__("validator.required"), __('car')),
            'car_id.in' => sprintf(__("validator.invalid"), __('car')),

            'orders.required' => sprintf(__("validator.required"), __('orders')),
            'orders.array' => sprintf(__("validator.invalid"), __('orders')),
            'orders.min' => sprintf(__("validator.min_items"), __('orders'), 1, count($this->orders ?? [])),

            'orders.*.type.required' => sprintf(__("validator.required"), __('type')),
            'orders.*.type.in' => sprintf(__("validator.invalid"), __('type')),
            'orders.*.status.required' => sprintf(__("validator.required"), __('status')),
            'orders.*.status.in' => sprintf(__("validator.invalid"), __('status')),
            'orders.*.from_agency_id.required' => sprintf(__("validator.required"), __('agency')),
            'orders.*.from_agency_id.in' => sprintf(__("validator.invalid"), __('agency')),

        ];

        return $tmp;
    }


}
