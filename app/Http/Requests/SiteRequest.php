<?php

namespace App\Http\Requests;

use App\Http\Helpers\Variable;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteRequest extends FormRequest
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

        $types = Category::pluck('id');
        $request = $this;
        $user = auth()->user()/* ?? auth('api')->user()*/
        ;
        if ($this->cmnd)
            return [
                'charge' => ['required_if:cmnd,charge', 'numeric', 'gt:0'],
                'view_fee' => ['required_if:cmnd,view-fee', 'numeric', 'gt:0'],
            ];
        return [
            'fullname' => $user ? ['sometimes'] : ['required', 'min:3', 'max:100',],
            'phone' => $user ? ['sometimes'] : 'required|numeric|digits:11|regex:/^09[0-9]+$/',
            'phone_verify' => ['required_with:phone',
                Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                    return $query->where('phone', $request->phone);
                }),
            ],
            'password' => [$user ? '' : 'required', 'regex:/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/'],

            'lang' => ['required', Rule::in(Variable::LANGS)],
            'name' => ['required', 'min:3', 'max:100', Rule::unique('sites', 'name')->ignore($this->id)],
            'link' => ['required', 'starts_with:https://', 'url', 'max:1024', Rule::unique('sites', 'link')->ignore($this->id)],
            'tags' => ['nullable', 'max:1024'],
            'category' => ['nullable', Rule::in($types)],
            'description' => ['nullable', 'max:2048'],
            'img' => ['required', 'base64_image_size:' . Variable::SITE_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::SITE_ALLOWED_MIMES)],

        ];
    }

    public function messages()
    {
        if ($this->cmnd)
            return [
                'charge.numeric' => sprintf(__("validator.invalid"), __('charge_amount')),
                'charge.gt' => sprintf(__("validator.invalid"), __('charge_amount')),
                'charge.required_if' => sprintf(__("validator.invalid"), __('charge_amount')),

                'view_fee.numeric' => sprintf(__("validator.invalid"), __('view_fee')),
                'view_fee.gt' => sprintf(__("validator.invalid"), __('view_fee')),
                'view_fee.required_if' => sprintf(__("validator.invalid"), __('view_fee')),

            ];
        return [

            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.numeric' => 'شماره تماس باید عدد باشد',
            'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.unique' => 'شماره تماس تکراری است',

            'phone_verify.required' => 'کد تایید شماره تماس ضروری است',
            'phone_verify.required_with' => 'کد تایید شماره تماس ضروری است',
            'phone_verify.required_if' => 'کد تایید شماره تماس ضروری است',
            'phone_verify.exists' => 'کد تایید شماره تماس نامعتبر است',

            'password.required' => 'رمزعبور ضروری است',
            'password_verify.same' => 'رمزعبور با تایید رمز عبور یکسان نیست',
            'password.regex' => 'رمزعبور حداقل 6 کاراکتر و شامل حروف و عدد باشد',

            'lang.required' => sprintf(__("validator.required"), __('lang')),
            'lang.in' => sprintf(__("validator.invalid"), __('lang')),

            'fullname.required' => sprintf(__("validator.required"), __('fullname')),
            'fullname.max' => sprintf(__("validator.max_len"), __('fullname'), 100, mb_strlen($this->fullname)),
            'fullname.min' => sprintf(__("validator.min_len"), 3, mb_strlen($this->fullname)),

            'name.required' => sprintf(__("validator.required"), __('name')),
            'name.max' => sprintf(__("validator.max_len"), __('name'), 100, mb_strlen($this->name)),
            'name.min' => sprintf(__("validator.min_len"), 3, mb_strlen($this->name)),
            'name.unique' => sprintf(__("validator.unique"), __('name')),

            'link.required' => sprintf(__("validator.required"), __('link')),
            'link.max' => sprintf(__("validator.max_len"), __('link'), 1024, mb_strlen($this->link)),
            'link.url' => sprintf(__("validator.invalid"), __('link')),
            'link.starts_with' => sprintf(__("validator.starts_with"), __('link'), "https://"),
            'link.unique' => sprintf(__("validator.unique"), __('link')),

            'category.in' => sprintf(__("validator.invalid"), __('lang')),

            'tags.max' => sprintf(__("validator.max_len"), __('tags'), 1024, mb_strlen($this->tags)),

            'description.max' => sprintf(__("validator.max_len"), __('description'), 2048, mb_strlen($this->description)),

            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::SITE_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::SITE_ALLOWED_MIMES)),
        ];
    }
}
