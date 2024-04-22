<?php

namespace App\Http\Requests;

use App\Models\City;
use Illuminate\Validation\Rules\File;
use App\Http\Helpers\Variable;
use App\Models\Business;
use App\Models\Category;
use App\Models\County;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $user = auth()->user();
        $editMode = (bool)optional($user)->id;
        $request = $this;
        $tmp = [];
        $phoneChanged = optional($user)->phone != $this->phone;

        $roles = Variable::USER_ROLES;
        if (!$this->cmnd || $this->cmnd == 'register')
            $tmp = array_merge($tmp, [
                'accesses' => ['nullable', 'array', 'max:' . count($roles),],
                'accesses.*' => ['distinct', Rule::in($roles)],
                'fullname' => ['required', 'min:3', 'max:100',/* Rule::unique('users', 'fullname')->ignore(optional($user)->id)*/],
                'card' => ['nullable', 'numeric', 'digits:16',],
                'phone' => ['required', 'numeric', 'digits:11', 'regex:/^09[0-9]+$/', Rule::unique('users', 'phone')->ignore(optional($user)->id)],
                'phone_verify' => [Rule::requiredIf(function () use ($request, $user, $phoneChanged, $editMode) {
                    return $phoneChanged
                        || !$user || (!$editMode && $request->phone != $user->phone);
                }), !$user || (!$editMode && $request->phone != $user->phone) ? Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                    return $query->where('phone', $request->phone);
                }) : '',],
                'sheba' => ['nullable', 'numeric', 'digits:24'],
            ]);


        if ($this->cmnd == 'upload-img')
            $tmp = array_merge($tmp, [
                'img' => ['required', 'base64_image_size:' . Variable::SITE_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::BANNER_ALLOWED_MIMES)],

            ]);
        if ($this->cmnd == 'password-reset') {

            if (!$user)
                $tmp = array_merge($tmp, [
                    'phone' => ['required', 'numeric', 'digits:11', 'regex:/^09[0-9]+$/', Rule::exists('users', 'phone')],
                ]);
            $tmp = array_merge($tmp, [
                'phone_verify' => ['required', Rule::exists('sms_verify', 'code')->where('phone', $user ? $user->phone : $this->phone)],
                'new_password' => ['required', 'min:6', 'confirmed', 'regex:/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/'],

            ]);
        }
        if ($this->cmnd == 'register')
            $tmp = array_merge($tmp, [
                'password' => ['required', 'min:6', 'confirmed', 'regex:/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/'],
            ]);
        if ($this->cmnd == 'add-address') {
            $counties = City::where('parent_id', $this->county_id)->pluck('id');
            $tmp = array_merge($tmp, [
                'address' => ['required', 'max:2048',],
                'receiver_fullname' => ['required', 'max:100',],
                'province_id' => ['required', 'numeric',],
                'county_id' => ['required', 'numeric',],
                'district_id' => [Rule::requiredIf(count($counties) > 0), Rule::in($counties)],
                'postal_code' => ['required', 'numeric',],
                'receiver_phone' => ['required', 'numeric', 'digits:11', 'regex:/^09[0-9]+$/'],
            ]);
        }
        return $tmp;
    }

    public function messages()
    {


        return [

            'accesses.array' => sprintf(__("validator.invalid"), __('accesses')),
            'accesses.*.distinct' => sprintf(__("validator.invalid"), __('accesses')),
            'accesses.*.in' => sprintf(__("validator.invalid"), __('accesses')),


            'fullname.required' => sprintf(__("validator.required"), __('fullname')),
            'fullname.max' => sprintf(__("validator.max_len"), __('fullname'), 100, mb_strlen($this->fullname)),
            'fullname.min' => sprintf(__("validator.min_len"), 3, mb_strlen($this->fullname)),

            'phone.required' => sprintf(__("validator.required"), __('phone')),
            'phone.unique' => sprintf(__("validator.unique"), __('phone')),
            'phone.numeric' => sprintf(__("validator.numeric"), __('phone')),
            'phone_verify.required' => sprintf(__("validator.required"), __('phone_verify')),
            'phone_verify.exists' => sprintf(__("validator.invalid"), __('phone_verify')),
            'phone.exists' => __('user_phone_not_found'),


            'card.digits' => sprintf(__("validator.digits"), __('card'), 16),
            'card.required' => sprintf(__("validator.required"), __('card')),
            'card.unique' => sprintf(__("validator.unique"), __('card')),
            'card.numeric' => sprintf(__("validator.numeric"), __('card')),

            'sheba.required' => sprintf(__("validator.required"), __('sheba')),
            'sheba.digits' => sprintf(__("validator.digits"), __('sheba'), 24),
            'sheba.unique' => sprintf(__("validator.unique"), __('sheba')),
            'sheba.numeric' => sprintf(__("validator.numeric"), __('sheba')),


            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::SITE_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::BANNER_ALLOWED_MIMES)),

            'password.required' => sprintf(__("validator.required"), __('password')),
            'password.regex' => sprintf(__("validator.password_regex"),),
            'password.confirmed' => sprintf(__("validator.password_confirmed"),),
            'new_password.required' => sprintf(__("validator.required"), __('new_password')),
            'new_password.min' => sprintf(__("validator.min_len"), 6, mb_strlen($this->new_password)),
            'new_password.regex' => sprintf(__("validator.password_regex"),),
            'new_password.confirmed' => sprintf(__("validator.password_confirmed"),),


            'address.max' => sprintf(__("validator.max_len"), __('address'), 2048, mb_strlen($this->address)),
            'province_id.required' => sprintf(__("validator.required"), __('province')),
            'county_id.required' => sprintf(__("validator.required"), __('county')),
            'district_id.required' => sprintf(__("validator.required"), __('district/city')),
            'district_id.in' => sprintf(__("validator.invalid"), __('district/city')),
            'postal_code.required' => sprintf(__("validator.required"), __('postal_code')),
            'postal_code.numeric' => sprintf(__("validator.numeric"), __('postal_code')),

            'receiver_fullname.required' => sprintf(__("validator.required"), __('fullname')),
            'receiver_fullname.max' => sprintf(__("validator.max_len"), __('fullname'), 100, mb_strlen($this->receiver_fullname)),
            'receiver_fullname.min' => sprintf(__("validator.min_len"), 3, mb_strlen($this->receiver_fullname)),

            'receiver_phone.required' => sprintf(__("validator.required"), __('phone')),
            'receiver_phone.unique' => sprintf(__("validator.unique"), __('phone')),
            'receiver_phone.numeric' => sprintf(__("validator.numeric"), __('phone')),
            'receiver_phone.digits' => sprintf(__("validator.digits"), __('phone'), 11),

        ];
    }
}
