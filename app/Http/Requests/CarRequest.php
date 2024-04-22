<?php

namespace App\Http\Requests;

use App\Models\Agency;
use App\Models\City;
use App\Models\Driver;
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

class CarRequest extends FormRequest
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
                'driver_id' => ['required', Rule::in(Driver::where('agency_id', $this->agency_id)->pluck('id'))],
                'name' => ['required', 'string', 'max:100'],
                'plate_number' => ['required', 'string', 'max:30', Rule::unique('cars')->where(function ($query) {
                    $query->where('agency_id', $this->agency_id)
                        ->where('plate_number', $this->plate_number);
                })->ignore($this->id)],

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


            'name.required' => sprintf(__("validator.required"), __('name')),
            'name.unique' => sprintf(__("validator.unique"), __('name')),
            'name.max' => sprintf(__("validator.max_len"), __('name'), 100, mb_strlen($this->name)),
            'name.string' => sprintf(__("validator.string"), __('name')),

            'driver_id.required' => sprintf(__("validator.required"), __('driver')),
            'driver_id.in' => sprintf(__("validator.invalid"), __('driver')),


            'plate_number.required' => sprintf(__("validator.required"), __('plate_number')),
            'plate_number.unique' => sprintf(__("validator.unique"), __('plate_number')),
            'plate_number.max' => sprintf(__("validator.max_len"), __('plate_number'), 30, mb_strlen($this->name)),
            'plate_number.string' => sprintf(__("validator.string"), __('plate_number')),


            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::PRODUCT_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::PRODUCT_ALLOWED_MIMES)),

        ];
    }
}
