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

class TextRequest extends FormRequest
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
        $types = Category::pluck('id');
        $editMode = (bool)$this->id;
        $request = $this;
        $tmp = [];
        if (!$this->cmnd)
            $tmp = array_merge($tmp, [
                'lang' => ['required', Rule::in(Variable::LANGS)],
                'author' => ['required', 'max:200',],
                'title' => ['required', 'max:1024',/* Rule::unique('articles', 'title')->ignore($this->id)*/],
                'tags' => ['nullable', 'max:1024'],
                'content' => ['nullable'],
                'category_id' => ['nullable', Rule::in($types)],
                'description' => ['nullable', 'max:2048'],
            ]);

        if ($request->uploading)
            $tmp = array_merge($tmp, [
                'img' => ['required', 'base64_image_size:' . Variable::SITE_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::SITE_ALLOWED_MIMES)],

            ]);
        if ($this->cmnd)
            $tmp = array_merge($tmp, [
                'img' => ['sometimes', 'base64_image_size:' . Variable::SITE_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::SITE_ALLOWED_MIMES)],
                'charge' => ['required_if:cmnd,charge', 'numeric', 'gt:0'],
                'view_fee' => ['required_if:cmnd,view-fee', 'numeric', 'gt:0'],
            ]);
        return $tmp;
    }

    public function messages()
    {

        return [
            'lang.required' => sprintf(__("validator.required"), __('lang')),
            'lang.in' => sprintf(__("validator.invalid"), __('lang')),

            'title.required' => sprintf(__("validator.required"), __('title')),
            'title.max' => sprintf(__("validator.max_len"), __('title'), 1024, mb_strlen($this->title)),

            'author.required' => sprintf(__("validator.required"), __('author')),
            'author.max' => sprintf(__("validator.max_len"), __('author'), 200, mb_strlen($this->author)),

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

            'description.max' => sprintf(__("validator.max_len"),__('description'), 2048, mb_strlen($this->description)),

            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::SITE_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::SITE_ALLOWED_MIMES)),

            'charge.numeric' => sprintf(__("validator.invalid"), __('charge_amount')),
            'charge.gt' => sprintf(__("validator.invalid"), __('charge_amount')),
            'charge.required_if' => sprintf(__("validator.invalid"), __('charge_amount')),

            'view_fee.numeric' => sprintf(__("validator.invalid"), __('view_fee')),
            'view_fee.gt' => sprintf(__("validator.invalid"), __('view_fee')),
            'view_fee.required_if' => sprintf(__("validator.invalid"), __('view_fee')),

        ];
    }
}
