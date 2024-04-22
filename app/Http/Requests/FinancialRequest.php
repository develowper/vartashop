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

class FinancialRequest extends FormRequest
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

            ]);
        }

        if ($this->cmnd)
            $tmp = array_merge($tmp, [
                'amount' => ['required', 'integer', 'gt:0'],
                'cmnd' => ['required', Rule::in(Variable::TRANSACTION_TYPES)],
                'type' => ['required', Rule::in(array_keys(Variable::FINANCIALS))],

            ]);
        return $tmp;
    }

    public function messages()
    {

        return [


            "amount.gt" => sprintf(__("validator.gt"), __('amount'), 0),

            'type.required' => sprintf(__("validator.required"), __('type')),
            'type.in' => sprintf(__("validator.invalid"), __('type')),

            'cmnd.required' => sprintf(__("validator.required"), __('item')),
            'cmnd.in' => sprintf(__("validator.invalid"), __('item')),

        ];
    }
}
