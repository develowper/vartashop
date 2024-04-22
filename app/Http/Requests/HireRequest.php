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

class HireRequest extends FormRequest
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
        $editMode = (bool)optional($user)->id;
        $request = $this;
        $tmp = [];
        $roles = array_column(Variable::ACCESS, 'role');
        $tmp = array_merge($tmp, [
            'accesses' => ['nullable', 'array', 'max:' . count($roles),],
            'accesses.*' => ['distinct', Rule::in($roles)],
        ]);


        return $tmp;
    }

    public function messages()
    {

        return [

            'accesses.array' => sprintf(__("validator.invalid"), __('accesses')),
            'accesses.*.distinct' => sprintf(__("validator.invalid"), __('accesses')),
            'accesses.*.in' => sprintf(__("validator.invalid"), __('accesses')),


        ];
    }
}
