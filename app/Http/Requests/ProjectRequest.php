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

class ProjectRequest extends FormRequest
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
        $request = $this;
        $tmp = [];
        $user = auth()->user();

        if ($this->cmnd == 'create-order')
            $tmp = array_merge($tmp, [
                'fullname' => $user ? ['sometimes'] : ['required', 'min:3', 'max:100',],
                'phone' => $user ? ['sometimes'] : 'required|numeric|digits:11|regex:/^09[0-9]+$/',
                'phone_verify' => [$user ? 'nullable' : 'required_with:phone',
                    Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                        return $query->where('phone', $request->phone);
                    }),
                ],

                'title' => ['required', 'max:1024',/* Rule::unique('articles', 'title')->ignore($this->id)*/],
                'description' => ['nullable', 'max:2048'],

                'items' => ['required', 'array', 'min:1'],
                'items.*' => ['required', 'distinct', Rule::in(array_column(Variable::PROJECT_ITEMS, 'name'))],
            ]);

        if ($this->cmnd == 'create-order')
            $tmp = array_merge($tmp, [

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


            'title.required' => sprintf(__("validator.required"), __('title')),
            'title.max' => sprintf(__("validator.max_len"), __('title'), 1024, mb_strlen($this->title)),

            'description.max' => sprintf(__("validator.max_len"),__('description'), 2048, mb_strlen($this->description)),


            'items.required' => sprintf(__("validator.required"), __('article_items')),
            'items.array' => sprintf(__("validator.invalid"), __('article_items')),
            'items.*.distinct' => sprintf(__("validator.invalid"), __('article_items')),
            'items.*.in' => sprintf(__("validator.invalid"), __('article_items')),


        ];
    }
}
