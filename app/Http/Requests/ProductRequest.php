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

class ProductRequest extends FormRequest
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
                'name' => ['required', 'max:200', Rule::unique('products', 'name')->ignore($this->id)],
                'tags' => ['nullable', 'string', 'max:1024'],
                'category_id' => ['required', Rule::in(Category::pluck('id'))],

            ]);
        }
        if ($this->uploading)
            $tmp = array_merge($tmp, [
                'img' => ['required', 'base64_image_size:' . Variable::PRODUCT_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::PRODUCT_ALLOWED_MIMES)],

            ]);
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

            'tags.max' => sprintf(__("validator.max_len"), __('tags'), 1024, mb_strlen($this->tags)),

            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::PRODUCT_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::PRODUCT_ALLOWED_MIMES)),

            'category_id.required' => sprintf(__("validator.required"), __('category')),
            'category_id.in' => sprintf(__("validator.invalid"), __('category')),

        ];
    }
}
