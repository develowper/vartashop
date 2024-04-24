<?php

namespace App\Http\Requests;

use App\Models\Admin;
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

class RepositoryRequest extends FormRequest
{
    public mixed $myAgency;


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
        $regexLocation = "/^[-+]?[0-9]{1,7}(\\.[0-9]+)?,[-+]?[0-9]{1,7}(\\.[0-9]+)?$/";
        $editMode = (bool)$this->id;
        $request = $this;
        $tmp = [];
        if (!$this->cmnd) {
            $typeId = $this->type_id ?? -1;
            $provinceId = $this->province_id ?? -1;
            $user = $this->user();
            if (!$this->agency_id && $this->myAgency->level == '3')
                $this->merge(['agency_id' => $this->myAgency->id]);
            $availableAgencies = $user->allowedAgencies($this->myAgency)->pluck('id');
            $childCities = City::whereNot('level', 1)->pluck('id')->toArray();
            $tmp = array_merge($tmp, [
                'agency_id' => ['required', Rule::in($availableAgencies)],
                'admin_id' => ['required', $user->hasAccess('edit_setting') ? null : Rule::in(Admin::where('agency_id', $this->agency_id)->pluck('id'))],
                'name' => ['required', 'max:200'],
                'phone' => ['required', "unique:repositories,phone,$this->id", 'max:20'],
                'address' => ['required', 'max:2048'],
                'cities' => ['nullable', 'array',
                    function ($attribute, $value, $fail) use ($childCities) {
                        if (array_diff($value, $childCities))
                            return $fail(sprintf(__("validator.invalid"), __('supported_districts')));

                    }],
//                'cities.*' => [Rule::in($childCities)],
                'province_id' => ['required', Rule::in(City::where('level', 1)->pluck('id'))],
                'county_id' => ['required', Rule::in(City::where('level', 2)->pluck('id'))],
                'postal_code' => ['nullable', 'max:20'],
                'location' => ['required', "regex:$regexLocation",],
                'is_shop' => ['required', 'boolean'],
                'allow_visit' => [Rule::requiredIf($this->is_shop), 'boolean'],
            ]);


        }
        if ($this->uploading)
            $tmp = array_merge($tmp, [

            ]);
        if ($this->cmnd)
            $tmp = array_merge($tmp, [
            ]);
        return $tmp;
    }

    public function messages()
    {

        $tmp = [
            'agency_id.required' => sprintf(__("validator.required"), __('agency')),
            'agency_id.in' => sprintf(__("validator.invalid"), __('agency')),

            'admin_id.required' => sprintf(__("validator.required"), __('repo_owner/admin')),
            'admin_id.in' => sprintf(__("validator.invalid"), __('repo_owner/admin')),


            'name.required' => sprintf(__("validator.required"), __('name')),
            'name.max' => sprintf(__("validator.max_len"), __('name'), 200, mb_strlen($this->name)),

            'phone.required' => sprintf(__("validator.required"), __('phone')),
            'phone.max' => sprintf(__("validator.max_len"), __('phone'), 20, mb_strlen($this->phone)),
            'phone.unique' => sprintf(__("validator.unique"), __('phone')),

            'address.required' => sprintf(__("validator.required"), __('address')),
            'address.max' => sprintf(__("validator.max_len"), __('address'), 2048, mb_strlen($this->address)),

            'province_id.required' => sprintf(__("validator.required"), __('province')),
            'province_id.in' => sprintf(__("validator.invalid"), __('province')),

            'county_id.required' => sprintf(__("validator.required"), __('county')),
            'county_id.in' => sprintf(__("validator.invalid"), __('county')),

            'postal_code.required' => sprintf(__("validator.required"), __('postal_code')),
            'postal_code.max' => sprintf(__("validator.max_len"), __('postal_code'), 20, mb_strlen($this->postal_code)),

            'supported_provinces.required_if' => sprintf(__("validator.required"), __('supported_provinces')),

            'location.required' => sprintf(__("validator.required"), __('location')),
            'location.regex' => sprintf(__("validator.invalid"), __('location')),

            'cities.required' => sprintf(__("validator.required"), __('supported_districts')),
            'cities.array' => sprintf(__("validator.invalid"), __('supported_districts')),
            'cities.*.in' => sprintf(__("validator.invalid"), __('supported_districts')),

            'is_shop.required' => sprintf(__("validator.required"), __('connect_shop')),
            'is_shop.boolean' => sprintf(__("validator.invalid"), __('connect_shop')),

            'allow_visit.required' => sprintf(__("validator.required"), __('allow_visit')),
            'allow_visit.boolean' => sprintf(__("validator.invalid"), __('allow_visit')),

        ];

        return $tmp;
    }
}
