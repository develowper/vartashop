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

class NotificationRequest extends FormRequest
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
                'subject' => ['required', 'max:100',],
                'link' => ['nullable', 'max:512', 'url'],
                'type' => ['nullable',
                    Rule::in(Variable::NOTIFICATION_TYPES)],
                'description' => ['required', 'max:65535'],
            ]);


        if ($this->cmnd)
            $tmp = array_merge($tmp, [

            ]);
        return $tmp;
    }

    public function messages()
    {

        return [
            'lang.required' => sprintf(__("validator.required"), __('lang')),
            'lang.in' => sprintf(__("validator.invalid"), __('lang')),

            'subject.required' => sprintf(__("validator.required"), __('subject')),
            'subject.max' => sprintf(__("validator.max_len"), __('subject'), 100, mb_strlen($this->subject)),


            'link.required' => sprintf(__("validator.required"), __('link')),
            'link.max' => sprintf(__("validator.max_len"), __('link'), 512, mb_strlen($this->link)),
            'link.url' => sprintf(__("validator.invalid"), __('link')),
            'link.starts_with' => sprintf(__("validator.starts_with"), __('link'), "http://"),


            'description.required' => sprintf(__("validator.required"), __('message')),
            'description.max' => sprintf(__("validator.max_len"), __('description'), 65535, mb_strlen($this->description)),


        ];
    }
}
