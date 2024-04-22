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

class TransferRequest extends FormRequest
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
        if (!$this->cmnd)
            $tmp = array_merge($tmp, [
                'price' => ['required', 'numeric', 'min:' . Variable::MIN_SELL_PRICE,],
                'type' => ['required', Rule::in(Variable::TRANSFER_TYPES),],
                'item_id' => ['required_with:item_type', 'numeric'],
                'item_type' => ['required_with:item_type',],
                'expires_at' => ['required', 'numeric', 'min:0'],
                'password' => [Rule::requiredIf(!$editMode && $this->type == 'private'), $this->type == 'private' && !$editMode ? 'min:6' : ''],
//                'owner_id' => ['required_if:' . in_array($this->owner_id, ['ad', 'go']), 'numeric',],

            ]);

        return $tmp;
    }

    public function messages()
    {

        return [

            'owner_id.required' => sprintf(__("validator.required"), __('user')),

            'password.required' => sprintf(__("validator.required"), __('transfer_password')),
            'password.min' => sprintf(__("validator.min"), __('transfer_password'), 6),

            'price.required' => sprintf(__("validator.required"), __('price')),
            'price.numeric' => sprintf(__("validator.numeric"), __('price')),
            'price.min' => sprintf(__("validator.min"), __('price'), Variable::MIN_SELL_PRICE . " " . __('currency')),

            'type.required' => sprintf(__("validator.required"), __('transfer_type')),
            'type.in' => sprintf(__("validator.invalid"), __('transfer_type')),

            'item_id.required' => sprintf(__("validator.invalid"), __('item')),
            'item_id.numeric' => sprintf(__("validator.invalid"), __('item')),
            'item_type.required' => sprintf(__("validator.invalid"), __('item')),

            'expires_at.required' => sprintf(__("validator.required"), __('validity')),
            'expires_at.numeric' => sprintf(__("validator.numeric"), __('validity')),
            'expires_at.min' => sprintf(__("validator.min"), __('validity'), 0),

        ];
    }
}
