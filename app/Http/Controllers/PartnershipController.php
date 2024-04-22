<?php

namespace App\Http\Controllers;

use App\Http\Helpers\SMSHelper;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Models\City;
use App\Models\Partnership;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PartnershipController extends Controller
{

    public function create(Request $request)
    {
        $this->validate($request, [
            'fullname' => ['required', 'max:100'],
            'meterage' => [Rule::requiredIf(in_array($request->type, ['gardener', 'farmer'])), 'numeric', 'digits_between:0,10'],
            'province_id' => ['required', Rule::in(City::where('level', 1)->pluck('id'))],
            'county_id' => 'required', Rule::in(City::where('parent_id', $request->province_id)->pluck('id')),
            'address' => ['required', 'max:2048'],
            'description' => ['required'],
            'products' => in_array($request->type, ['gardener', 'farmer']) ? ['required', 'array'] : [],
            'products.*.name' => in_array($request->type, ['gardener', 'farmer']) ? ['required', 'max:50'] : [],
            'products.*.weight' => in_array($request->type, ['gardener', 'farmer']) ? ['required', 'max:30'] : [],
            'phone' => ['required', 'numeric', 'digits:11', 'regex:/^09[0-9]+$/'],
            'phone_verify' => ['required', Rule::exists('sms_verify', 'code')->where('phone', $request->phone)],
        ], [
            'fullname.required' => sprintf(__("validator.required"), __('fullname')),
            'fullname.max' => sprintf(__("validator.max_len"), __('fullname'), 100, mb_strlen($request->fullname)),
            'fullname.unique' => sprintf(__("validator.unique"), __('fullname')),

            'meterage.required' => sprintf(__("validator.required"), __('meterage')),
            'meterage.numeric' => sprintf(__("validator.numeric"), __('meterage')),
            'meterage.digits_between' => sprintf(__("validator.max_len"), __('meterage'), 10, mb_strlen($request->meterage)),

            'phone.required' => sprintf(__("validator.required"), __('phone')),
            'phone_verify.required' => sprintf(__("validator.required"), __('phone_verify')),
            'phone_verify.exists' => sprintf(__("validator.invalid"), __('phone_verify')),
            'phone.regex' => sprintf(__("validator.invalid"), __('phone')),
            'phone.numeric' => sprintf(__("validator.numeric"), __('phone')),
            'phone.digits' => sprintf(__("validator.digits"), __('phone'), 11),

            'province_id.required' => sprintf(__("validator.required"), __('province')),
            'province_id.in' => sprintf(__("validator.invalid"), __('province')),

            'county_id.required' => sprintf(__("validator.required"), __('county')),
            'county_id.in' => sprintf(__("validator.invalid"), __('county')),

            'description.max' => sprintf(__("validator.max_len"), __('description'), 2048, mb_strlen($request->description)),

            'address.required' => sprintf(__("validator.required"), __('address')),
            'address.max' => sprintf(__("validator.max_len"), __('address'), 2048, mb_strlen($request->address)),

            'products.required' => sprintf(__("validator.required"), __('product')),
            'products.array' => sprintf(__("validator.invalid"), __('product')),
            'products.min' => sprintf(__("validator.min"), __('product'), 1),
            'products.*.name.required' => sprintf(__("validator.required"), __('product_name')),
            'products.*.weight.required' => sprintf(__("validator.required"), __('product_weight')),
            'products.*.name.max' => sprintf(__("validator.max"), __('name'), 50),
            'products.*.weight.max' => sprintf(__("validator.max"), __('weight'), 30),

        ]);

        $request->merge([
            'products' => in_array($request->type, ['gardener', 'farmer']) ? $request->products : null,
            'meterage' => in_array($request->type, ['gardener', 'farmer']) ? $request->meterage : null,
        ]);
        $data = Partnership::create($request->all());

        if ($data) {
            Telegram::log(null, 'partnership_created', $data);
            SMSHelper::deleteCode($data->phone);
            return response()->json(['message' => __('your_request_registered_successfully'),], Variable::SUCCESS_STATUS);

        }
    }
}
