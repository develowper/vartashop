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

class TicketRequest extends FormRequest
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

                'message' => ['required', 'max:65535'],
            ]);


        if ($this->cmnd == 'add-chat')
            $tmp = array_merge($tmp, [
                'message' => ['required', 'min:1', 'max:65535'],
                'attachments' => ['nullable', 'array', 'max:5'],
                'attachments.*' => ['mimes:' . implode(",", Variable::TICKET_ATTACHMENT_ALLOWED_MIMES)],
            ]);
        return $tmp;
    }

    public function messages()
    {

        return [

            'subject.required' => sprintf(__("validator.required"), __('subject')),
            'subject.max' => sprintf(__("validator.max_len"), __('subject'), 100, mb_strlen($this->subject)),


            'message.required' => sprintf(__("validator.required"), __('message')),
            'message.max' => sprintf(__("validator.max_len"), __('message'), 65535, mb_strlen($this->message)),

            'attachments.max' => sprintf(__("validator.max_items"), __('attachments'), Variable::TICKET_ATTACHMENT_MAX_LEN, count($this->attachments ?? [])),
            'attachments.*.mimes' => sprintf(__("validator.invalid_format"), __("attachment"), implode(",", Variable::TICKET_ATTACHMENT_ALLOWED_MIMES)),

        ];
    }
}
