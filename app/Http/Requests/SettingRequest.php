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

class SettingRequest extends FormRequest
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

        $tmp = array_merge($tmp, [
            'key' => ['required', 'max:30', 'regex:/^[a-z0-9]+(_[a-z0-9]+)*$/', "unique:settings,key,$this->id"],
            'value' => ['required', 'max:1024',],

        ]);

        return $tmp;
    }

    public function messages()
    {

        return [

            'key.required' => sprintf(__("validator.required"), __('key')),
            'key.max' => sprintf(__("validator.max_len"), __('key'), 30, mb_strlen($this->key)),
            'key.regex' => __("validator.key_contains"),
            'key.unique' => sprintf(__("validator.unique"), __('key')),

            'value.required' => sprintf(__("validator.required"), __('value')),
            'value.max' => sprintf(__("validator.max_len"), __('value'), 1024, mb_strlen($this->value)),


        ];
    }
}
