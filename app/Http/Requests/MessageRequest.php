<?php

namespace App\Http\Requests;

use App\Rules\Recaptcha;
use Illuminate\Validation\Rules\File;
use App\Http\Helpers\Variable;
use App\Models\Business;
use App\Models\Category;
use App\Models\County;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
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
                'fullname' => ['required', 'max:100',],
                'phone' => ['required', 'numeric', 'digits:11', 'regex:/^09[0-9]+$/',],
//            'type' => ['nullable', 'in:order,referral'],
                'description' => ['required', 'max:65535'],
                'recaptcha' => [new ReCaptcha],
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
            'phone.regex' => sprintf(__("validator.invalid"), __('phone')),
            'phone.numeric' => sprintf(__("validator.numeric"), __('phone')),
            'phone.digits' => sprintf(__("validator.digits"), __('phone'), 11),

            'phone_verify.required' => sprintf(__("validator.required"), __('phone_verify')),
            'phone_verify.exists' => sprintf(__("validator.invalid"), __('phone_verify')),


            'password.required' => sprintf(__("validator.required"), __('password')),

            'description.required' => sprintf(__("validator.required"), __('description'),),
            'description.max' => sprintf(__("validator.max_len"), __('description'), 65535, mb_strlen($this->description)),

        ];
    }
}
