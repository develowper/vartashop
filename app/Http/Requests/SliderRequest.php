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

class SliderRequest extends FormRequest
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
        $editMode = (bool)$this->id;
        $request = $this;
        $tmp = [];
        if (!$this->cmnd)
            $tmp = array_merge($tmp, [
                'title' => ['nullable', 'max:1024',],
                'description' => ['nullable', 'max:2048'],
            ]);

        if ($request->uploading)
            $tmp = array_merge($tmp, [
                'img' => ['required', 'base64_image_size:' . Variable::BANNER_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::BANNER_ALLOWED_MIMES)],
            ]);
        if ($this->cmnd)
            $tmp = array_merge($tmp, [
                'img' => ['sometimes', 'base64_image_size:' . Variable::BANNER_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::BANNER_ALLOWED_MIMES)],
            ]);
        return $tmp;
    }

    public function messages()
    {

        return [
            'lang.required' => sprintf(__("validator.required"), __('lang')),
            'lang.in' => sprintf(__("validator.invalid"), __('lang')),

            'name.required' => sprintf(__("validator.required"), __('title')),
            'name.max' => sprintf(__("validator.max_len"), __('name'), 2048, mb_strlen($this->name)),

            'designer.required' => sprintf(__("validator.required"), __('designer')),
            'designer.max' => sprintf(__("validator.max_len"), __('designer'), 2048, mb_strlen($this->designer)),

            'phone.required' => sprintf(__("validator.required"), __('phone')),
            'phone.unique' => sprintf(__("validator.unique"), __('phone')),
            'phone_verify.required' => sprintf(__("validator.required"), __('phone_verify')),
            'phone_verify.exists' => sprintf(__("validator.invalid"), __('phone_verify')),


            'link.required' => sprintf(__("validator.required"), __('link')),
            'link.max' => sprintf(__("validator.max_len"), __('link'), 1024, mb_strlen($this->link)),
            'link.url' => sprintf(__("validator.invalid"), __('link')),
            'link.starts_with' => sprintf(__("validator.starts_with"), __('link'), "https://"),


            'category_id.in' => sprintf(__("validator.invalid"), __('category')),

            'province_id.required' => sprintf(__("validator.required"), __('province')),
            'province_id.in' => sprintf(__("validator.invalid"), __('province')),

            'county_id.required' => sprintf(__("validator.required"), __('county')),
            'county_id.in' => sprintf(__("validator.invalid"), __('county')),

            'tags.max' => sprintf(__("validator.max_len"), __('tags'), 1024, mb_strlen($this->tags)),

            'description.max' => sprintf(__("validator.max_len"), __('description'), 2048, mb_strlen($this->description)),

            'img.required' => sprintf(__("validator.required"), __('image_cover')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __('image_cover'), Variable::SITE_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __('image_cover'), implode(",", Variable::BANNER_ALLOWED_MIMES)),

            'banner.required' => sprintf(__("validator.required"), __('banner_file')),
            'banner.mimes' => sprintf(__("validator.invalid_format"), __("banner_file"), implode(",", Variable::BANNER_ALLOWED_MIMES)),

            'charge.numeric' => sprintf(__("validator.invalid"), __('charge_amount')),
            'charge.gt' => sprintf(__("validator.invalid"), __('charge_amount')),
            'charge.required_if' => sprintf(__("validator.invalid"), __('charge_amount')),

            'view_fee.numeric' => sprintf(__("validator.invalid"), __('view_fee')),
            'view_fee.gt' => sprintf(__("validator.invalid"), __('view_fee')),
            'view_fee.required_if' => sprintf(__("validator.invalid"), __('view_fee')),

        ];
    }
}
