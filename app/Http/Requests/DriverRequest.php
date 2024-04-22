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

class DriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->myAgency = Agency::find($this->user()->agency_id);
        if (!$this->myAgency)
            abort(403, __("access_denied"));
        if ($this->myAgency->status != 'active')
            abort(403, __("your_agency_inactive"));
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
            $user = $this->user();
            $this->merge([
                'agency_id' => $this->myAgency->level == '3' ? $user->agency_id : $this->agency_id
            ]);
            $availableAgencies = $user->allowedAgencies($this->myAgency)->pluck('id');
            $tmp = array_merge($tmp, [
                'agency_id' => ['required', Rule::in($availableAgencies)],

                'fullname' => ['required', 'string', 'max:200'],
                'national_code' => ['required', 'numeric'   /*, Rule::unique('drivers', 'national_code')->ignore($this->id)*/],
                'phone' => ['required', 'numeric', 'digits:11', Rule::unique('drivers')->where(function ($query) {
                    $query->where('agency_id', $this->agency_id)
                        ->where('phone', $this->phone);
                })->ignore($this->id)],
                'card' => ['nullable', 'numeric', 'digits:16'],
                'sheba' => ['nullable', 'numeric', 'digits:24'],


            ]);
        }
        if ($this->uploading)
            $tmp = array_merge($tmp, [
                'img' => $this->img ? ['nullable', 'base64_image_size:' . Variable::DRIVER_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::DRIVER_ALLOWED_MIMES)] : [],

            ]);
        if ($this->cmnd)
            $tmp = array_merge($tmp, [
            ]);
        return $tmp;
    }

    public function messages()
    {

        return [


            'fullname.required' => sprintf(__("validator.required"), __('fullname')),
            'fullname.unique' => sprintf(__("validator.unique"), __('name')),
            'fullname.max' => sprintf(__("validator.max_len"), __('name'), 200, mb_strlen($this->fullname)),
            'fullname.string' => sprintf(__("validator.string"), __('fullname')),


            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::PRODUCT_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::PRODUCT_ALLOWED_MIMES)),

            'phone.required' => sprintf(__("validator.required"), __('phone')),
            'phone.unique' => sprintf(__("validator.unique"), __('phone')),
            'phone.digits' => sprintf(__("validator.digits"), __('phone'), 11),

            'national_code.required' => sprintf(__("validator.required"), __('national_code')),
            'national_code.digits' => sprintf(__("validator.digits"), __('national_code'), 16),
            'national_code.unique' => sprintf(__("validator.unique"), __('national_code')),
            'national_code.numeric' => sprintf(__("validator.numeric"), __('national_code')),

            'card.required' => sprintf(__("validator.required"), __('card')),
            'card.digits' => sprintf(__("validator.digits"), __('card'), 16),
            'card.unique' => sprintf(__("validator.unique"), __('card')),
            'card.numeric' => sprintf(__("validator.numeric"), __('card')),

            'sheba.required' => sprintf(__("validator.required"), __('sheba')),
            'sheba.digits' => sprintf(__("validator.digits"), __('sheba'), 24),
            'sheba.unique' => sprintf(__("validator.unique"), __('sheba')),
            'sheba.numeric' => sprintf(__("validator.numeric"), __('sheba')),

        ];
    }
}
