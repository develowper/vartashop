<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\File;
use App\Http\Helpers\Variable;
use App\Models\Business;
use App\Models\Category;
use App\Models\County;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $editMode = (bool)$this->id;
        $tmp = [];

        if (!$this->cmnd)
            $tmp = array_merge($tmp, [
                'fullname' => ['required', 'max:100', Rule::unique('users', 'fullname')->ignore($this->id)],
                'card' => ['nullable', 'digits:16', Rule::unique('users', 'card')->ignore($this->id)],
                'phone' => ['required', 'numeric', 'digits:11', 'regex:/^09[0-9]+$/', Rule::unique('users', 'phone')->ignore($this->id)],
                'email' => ['nullable', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->id)],
                'wallet' => ['numeric', 'gte:0'],
                'password' => [Rule::requiredIf(!$editMode)],
            ]);


        if ($this->cmnd)

            $tmp = array_merge($tmp, [
                'img' => ['required_if:cmnd,upload-img', 'base64_image_size:' . Variable::SITE_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::BANNER_ALLOWED_MIMES)],
                'wallet' => ['required_if:cmnd,wallet', 'numeric', 'gt:0'],

            ]);
        return $tmp;
    }

    public function messages()
    {

        return [

            'fullname.required' => sprintf(__("validator.required"), __('fullname')),
            'fullname.max' => sprintf(__("validator.max_len"), __('fullname'), 100, mb_strlen($this->fullname)),
            'fullname.unique' => sprintf(__("validator.unique"), __('fullname')),

            'phone.required' => sprintf(__("validator.required"), __('phone')),
            'phone.unique' => sprintf(__("validator.unique"), __('phone')),
            'phone_verify.required' => sprintf(__("validator.required"), __('phone_verify')),
            'phone_verify.exists' => sprintf(__("validator.invalid"), __('phone_verify')),

            'email.email' => sprintf(__("validator.invalid"), __('email')),
            'email.max' => sprintf(__("validator.email"), 100, mb_strlen($this->email)),
            'email.unique' => sprintf(__("validator.unique"), __('email')),

            'card.digits' => sprintf(__("validator.digits"), __('card'), 16),
            'card.unique' => sprintf(__("validator.unique"), __('card')),

            'wallet.numeric' => sprintf(__("validator.numeric"), __('wallet')),

            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::SITE_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::BANNER_ALLOWED_MIMES)),

            'password.required' => sprintf(__("validator.required"), __('password')),


        ];
    }
}
