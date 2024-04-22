<?php

namespace App\Http\Requests;

use App\Models\Agency;
use App\Models\City;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Repository;
use App\Models\Variation;
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

class VariationRequest extends FormRequest
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
        $admin = $this->user();
        $allowedRepositories = Repository::whereIntegerInRaw('agency_id', $admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id'))->pluck('id');
        $products = Product::select('id', 'name')->get();
        $packs = Pack::pluck('id');
        $grades = Variable::GRADES;
        $tmp = [];
        if (!$this->cmnd) {

            $tmp = array_merge($tmp, [
                'name' => ['required', 'max:200'],
                'repo_ids' => ['required', 'array', 'min:1'],
                'repo_ids.*' => [Rule::in($allowedRepositories)],
                "product_id" => ['required', Rule::in($products->pluck('id'))],
                "in_repo" => ['required', 'numeric', 'gte:0', 'lt:99999'],
                "in_shop" => ['required', 'numeric', 'gte:0', 'lt:99999'],
                "pack_id" => ['required', Rule::in($packs)],
                "grade" => ['required', Rule::in($grades)],
                "weight" => ['required', 'numeric', 'gte:0', 'lt:99999', $this->pack_id == 1 ? Rule::in(1) : ''],
                "price" => ['required', 'numeric', 'gte:0'],
//                'tags' => ['nullable', 'max:1024'],
//                'category_id' => ['required', Rule::in(Category::pluck('id'))],

            ]);
        }
        if ($this->uploading)
            $tmp = array_merge($tmp, [
                'img' => ['nullable', 'base64_image_size:' . Variable::PRODUCT_IMAGE_LIMIT_MB * 1024, 'base64_image_mime:' . implode(",", Variable::PRODUCT_ALLOWED_MIMES)],

            ]);
        if ($this->cmnd) {

//            $admin = $this->user();

            $tmp = array_merge($tmp, [

            ]);
        }
        return $tmp;
    }

    public function messages()
    {

        return [
            'name.required' => sprintf(__("validator.required"), __('name')),
            'name.unique' => sprintf(__("validator.unique"), __('name')),
            'name.max' => sprintf(__("validator.max_len"), __('name'), 200, mb_strlen($this->name)),

            'repo_ids.required' => sprintf(__("validator.required"), __('repository')),
            'repo_ids.*.in' => sprintf(__("validator.invalid"), __('repository')),

            "product_id.required" => sprintf(__("validator.required"), __('product')),
            "product_id.in" => sprintf(__("validator.invalid"), __('product')),

            "in_repo.required" => sprintf(__("validator.required"), __('repository_count')),
            "in_repo.numeric" => sprintf(__("validator.numeric"), __('repository_count')),
            "in_repo.gte" => sprintf(__("validator.gt"), __('repository_count'), 0),

            "in_shop.required" => sprintf(__("validator.required"), __('shop_count')),
            "in_shop.numeric" => sprintf(__("validator.numeric"), __('shop_count')),
            "in_shop.gte" => sprintf(__("validator.gt"), __('shop_count'), 0),

            "pack_id.required" => sprintf(__("validator.required"), __('pack')),
            "pack_id.in" => sprintf(__("validator.invalid"), __('pack')),

            "grade.required" => sprintf(__("validator.required"), __('grade')),
            "grade.in" => sprintf(__("validator.invalid"), __('grade')),


            "weight.required" => sprintf(__("validator.required"), __('weight')),
            "weight.numeric" => sprintf(__("validator.numeric"), __('weight')),
            "weight.gte" => sprintf(__("validator.gt"), __('weight'), 0),

            "price.required" => sprintf(__("validator.required"), __('price')),
            "price.numeric" => sprintf(__("validator.numeric"), __('price')),
            "price.gte" => sprintf(__("validator.gt"), __('price'), 0),

            'img.required' => sprintf(__("validator.required"), __('image')),
            'img.base64_image_size' => sprintf(__("validator.max_size"), __("image"), Variable::PRODUCT_IMAGE_LIMIT_MB),
            'img.base64_image_mime' => sprintf(__("validator.invalid_format"), __("image"), implode(",", Variable::PRODUCT_ALLOWED_MIMES)),

        ];
    }
}
