<?php

namespace App\Http\Requests;

use App\Models\Agency;
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
use stdClass;

class PackRequest extends FormRequest
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
        if (!$this->cmnd) {

            $tmp = array_merge($tmp, [
                'name' => ['required', 'max:200', Rule::unique('packs', 'name')->ignore($this->id)],
                'weight' => ['required', 'integer', 'min:0'],
                'height' => ['required', 'integer', 'min:0'],
                'width' => ['required', 'integer', 'min:0'],
                'length' => ['required', 'integer', 'min:0'],
                'price' => ['required', 'integer', 'min:0'],
            ]);
        }

        if ($this->cmnd)
            $tmp = array_merge($tmp, [
            ]);
        return $tmp;
    }

    public function messages()
    {

        return [


            'name.required' => sprintf(__("validator.required"), __('name')),
            'name.unique' => sprintf(__("validator.unique"), __('name')),
            'name.max' => sprintf(__("validator.max_len"), __('name'), 200, mb_strlen($this->name)),

            'weight.required' => sprintf(__("validator.required"), __('weight')),
            'weight.integer' => sprintf(__("validator.numeric"), __('weight')),
            'weight.min' => sprintf(__("validator.min"), __('weight'), 0),

            'height.required' => sprintf(__("validator.required"), __('height')),
            'height.integer' => sprintf(__("validator.numeric"), __('height')),
            'height.min' => sprintf(__("validator.min"), __('height'), 0),

            'width.required' => sprintf(__("validator.required"), __('width')),
            'width.integer' => sprintf(__("validator.numeric"), __('width')),
            'width.min' => sprintf(__("validator.min"), __('width'), 0),

            'length.required' => sprintf(__("validator.required"), __('length')),
            'length.integer' => sprintf(__("validator.numeric"), __('length')),
            'length.min' => sprintf(__("validator.min"), __('length'), 0),

            'price.required' => sprintf(__("validator.required"), __('price')),
            'price.integer' => sprintf(__("validator.numeric"), __('price')),
            'price.min' => sprintf(__("validator.min"), __('price'), 0),

        ];
    }
}
