<?php

namespace App\Http\Requests;

use App\Http\Helpers\Variable;
use App\Models\Agency;
use App\Models\City;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Repository;
use App\Models\ShippingMethod;
use App\Models\Variation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepositoryOrderRequest extends FormRequest
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
        $regexLocation = "/^[-+]?[0-9]{1,7}(\\.[0-9]+)?,[-+]?[0-9]{1,7}(\\.[0-9]+)?$/";
        $admin = $this->user();
        $allowedRepositories = Repository::whereIntegerInRaw('agency_id', $admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id'))->pluck('id');
        $allowedTypes = $admin->hasAccess('create_variation') ? [__('internal'), __('external')] : [__('internal')];
        $counties = City::where('parent_id', $this->from_county_id)->pluck('id');
        if (!$this->cmnd) {

            $tmp = array_merge($tmp, [
                'order_type' => ['required', Rule::in($allowedTypes)],
                'to_repo_id' => ['required', Rule::in($allowedRepositories)],
                'from_repo_id' => $this->order_type == __('internal') ? ['required', Rule::in($allowedRepositories), Rule::notIn([$this->to_repo_id])] : [],
                'from_shipping_method_id' => $this->order_type == __('internal') ? ['nullable', Rule::in(ShippingMethod::where('repo_id', $this->from_repo_id)->pluck('id'))] : [],
                'products' => ['required', 'array', 'min:1'],
                'status' => ['required', Rule::in(array_column(Variable::ORDER_STATUSES, 'name'))],
            ]);
            if ($this->order_type == __('external'))
                $tmp = array_merge($tmp, [

                    'from_fullname' => ['required', 'max:200',],
                    'from_phone' => ['required', 'max:30',],
                    'from_address' => ['required', 'max:2048',],
                    'from_province_id' => ['required', 'numeric',],
                    'from_county_id' => ['required', 'numeric',],
                    'from_district_id' => [Rule::requiredIf(count($counties) > 0), Rule::in($counties)],
                    'from_postal_code' => ['required', 'numeric',],
                    'from_location' => ['nullable', "regex:$regexLocation",],
                ]);
            $products = $this->order_type == __('internal') ? Variation::where('repo_id', $this->from_repo_id)->get() : Product::get();
            $packs = Pack::pluck('id');
            $grades = Variable::GRADES;
            foreach ($this->products as $idx => $product)
                $tmp = array_merge($tmp, [
                    "products.$idx.id" => ['required', Rule::in($products->pluck('id'))],
                    "products.$idx.qty" => ['required', 'numeric', 'gt:0', $this->order_type == __('internal') ? ('max:' . ($products->where('id', $product['id'] ?? 0)->first()->in_repo ?? 0)) : ''],
                    "products.$idx.pack_id" => ['required', Rule::in($packs)],
                    "products.$idx.grade" => ['required', Rule::in($grades)],
                    "products.$idx.weight" => ['required', 'numeric', 'gt:0', $this->order_type == __('external') && $this->pack_id == 1 ? Rule::in(1) : ''],
                    "products.$idx.price" => ['required', 'numeric', 'gt:0'],

                ]);

            $totalItemsPrice = collect($this->products)->map(fn($e) => ($e['qty'] ?? 0) * ($e['price'] ?? 0))->sum();
            $totalItems = collect($this->products)->map(fn($e) => $e['qty'] ?? 0)->sum();

            $this->merge([
                'database_products' => $products,
                'total_items' => $totalItems,
                'total_items_price' => $totalItemsPrice,
                'total_price' => $totalItemsPrice + $this->total_shipping_price - $this->total_discount
            ]);
            $tmp = array_merge($tmp, [
                'total_shipping_price' => ['required', 'numeric', 'min:0'],
                'total_discount' => ['required', 'numeric', 'min:0', 'max:' . ($totalItemsPrice + $this->total_shipping_price)],

            ]);
        }


        return $tmp;
    }

    public function messages()
    {
        $tmp = [];
        $tmp = array_merge($tmp, [
            'products.required' => sprintf(__("validator.required"), __('product')),

            'order_type.required' => sprintf(__("validator.required"), __('order_type')),
            'order_type.in' => sprintf(__("validator.invalid"), __('order_type')),

            'status.required' => sprintf(__("validator.required"), __('status')),
            'status.in' => sprintf(__("validator.invalid"), __('status')),

            'to_repo_id.required' => sprintf(__("validator.required"), __('destination_repository')),
            'to_repo_id.in' => sprintf(__("validator.invalid"), __('destination_repository')),

            'from_repo_id.required' => sprintf(__("validator.required"), __('origin_repository')),
            'from_repo_id.in' => sprintf(__("validator.invalid"), __('origin_repository')),
            'from_repo_id.not_in' => sprintf(__("validator.must_not_equal"), __('origin_repository'), __('destination_repository')),


            'from_address.required' => sprintf(__("validator.required"), __('address')),
            'from_address.max' => sprintf(__("validator.max_len"), __('address'), 2048, mb_strlen($this->address)),
            'from_province_id.required' => sprintf(__("validator.required"), __('province')),
            'from_county_id.required' => sprintf(__("validator.required"), __('county')),
            'from_district_id.required' => sprintf(__("validator.required"), __('district/city')),
            'from_district_id.in' => sprintf(__("validator.invalid"), __('district/city')),
            'from_postal_code.required' => sprintf(__("validator.required"), __('postal_code')),
            'from_postal_code.numeric' => sprintf(__("validator.numeric"), __('postal_code')),

            'from_fullname.required' => sprintf(__("validator.required"), __('fullname')),
            'from_fullname.max' => sprintf(__("validator.max_len"), __('fullname'), 100, mb_strlen($this->from_fullname)),
            'from_fullname.min' => sprintf(__("validator.min_len"), 3, mb_strlen($this->from_fullname)),

            'from_phone.required' => sprintf(__("validator.required"), __('phone')),
            'from_phone.numeric' => sprintf(__("validator.numeric"), __('phone')),
            'from_phone.digits' => sprintf(__("validator.digits"), __('phone'), 11),

            'from_location.regex' => sprintf(__("validator.invalid"), __('location')),

            'total_shipping_price.min' => sprintf(__("validator.min"), __('shipping_price'), 0),

            'total_discount.min' => sprintf(__("validator.min"), __('discount'), 0),
            'total_discount.max' => sprintf(__("validator.max_amount"), __('discount'), $this->total_price + $this->total_discount, $this->total_discount),

        ]);
        foreach ($this->products as $idx => $product)
            $tmp = array_merge($tmp, [
                "products.$idx.id.required" => sprintf(__("validator.required"), __('product')),

                "products.$idx.qty.required" => sprintf(__("validator.required"), __('qty')),
                "products.$idx.qty.min" => sprintf(__('validator.min_items'), '', 1, $product['qty'] ?? 0),
                "products.$idx.qty.max" => sprintf(__('validator.max_items'), '', floatval($product['in_repo'] ?? 0), $product['qty'] ?? 0),
                "products.$idx.qty.gt" => sprintf(__("validator.gt"), __('qty'), 0),

                "products.$idx.grade.required" => sprintf(__("validator.required"), __('grade')),

                "products.$idx.pack_id.required" => sprintf(__("validator.required"), __('pack')),
                "products.$idx.pack_id.in" => sprintf(__("validator.invalid"), __('pack')),

                "products.$idx.weight.required" => sprintf(__("validator.required"), __('weight')),
                "products.$idx.weight.gt" => sprintf(__("validator.gt"), __('weight'), 0),
                "products.$idx.weight.in" => sprintf(__('validator.must_be'), __('weight'), 1),

                "products.$idx.price.required" => sprintf(__("validator.required"), __('fee')),
                "products.$idx.price.gt" => sprintf(__("validator.gt"), __('fee'), 0),

            ]);
        return $tmp;
    }
}
