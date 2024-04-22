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

class PaymentRequest extends FormRequest
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

        $tmp = array_merge($tmp, [
            'amount' => ['numeric', 'min:1000']
        ]);


        if ($this->cmnd)
            $tmp = array_merge($tmp, [
            ]);
        return $tmp;
    }

    public function messages()
    {

        return [

            'amount.required' => sprintf(__("validator.required"), __('charge')),
            'amount.numeric' => sprintf(__("validator.numeric"), __('charge')),
            'amount.min' => sprintf(__("validator.min"), __('charge'), 1000 . " " . __('currency'),),


        ];
    }
}
