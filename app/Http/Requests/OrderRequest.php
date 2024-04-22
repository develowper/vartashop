<?php

namespace App\Http\Requests;

use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\City;
use App\Models\Order;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Repository;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\Variation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;

class OrderRequest extends FormRequest
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
        $user = $this->user();
        $editMode = (bool)$this->id;
        $id = $this->id;
        $regexLocation = "/^[-+]?[0-9]{1,7}(\\.[0-9]+)?,[-+]?[0-9]{1,7}(\\.[0-9]+)?$/";
        $allowedAgencies = [];
        $tmp = [];
        if ($user instanceof Admin) {

            $allowedAgencies = $user->allowedAgencies(Agency::find($user->agency_id))->pluck('id');
            $this->merge(['allowed_agencies' => $allowedAgencies]);
            array_merge($tmp, [
                'agency_id' => ['required', Rule::in($allowedAgencies),],
            ]);
        }

        if (!$this->cmnd && $editMode && $user instanceof Admin) {

            $data = Order::with('items')->findOrNew($id);
            $data->repository = Repository::with('shippingMethods')->findOrNew($data->repo_id);
            if ($data->repository && $data->repository->allow_visit) {
                $methods = $data->repository->getRelation('shippingMethods');
                $methods->prepend(ShippingMethod::find(1));
                $data->repository->setRelation('shippingMethods', $methods);
            }
            $this->merge(['data' => $data]);

            $counties = City::where('parent_id', $this->county_id)->pluck('id');

            $shippingMethods = ShippingMethod::where('repo_id', $data->repo_id)->get();
            $tmp = array_merge($tmp, [
                'shipping_method_id' => ['nullable', Rule::in($shippingMethods->pluck('id')->merge(1))],
                'products' => ['required', 'array', 'min:1'],

                'receiver_fullname' => ['required', 'max:200',],
                'receiver_phone' => ['required', 'max:30',],
                'address' => ['required', 'max:2048',],
                'province_id' => ['required', 'numeric', Rule::in(City::where('level', 1)->pluck('id'))],
                'county_id' => ['required', 'numeric', Rule::in(City::where('level', 2)->pluck('id'))],
                'district_id' => [Rule::requiredIf(count($counties) > 0), Rule::in($counties)],
                'postal_code' => ['required', 'numeric', 'digits_between:0,20'],
                'location' => ['nullable', "regex:$regexLocation",],
            ]);


            $products = Variation::where('repo_id', $data->repo_id)->get();
            $isAuction = Setting::getValue('is_auction');

            $totalPrice = 0;
            $totalWeight = 0;
            $totalItemsPrice = 0;
            $totalItemsDiscount = 0;
            $tmpProducts = [];
            foreach ($this->products ?? [] as $idx => $product) {
                $p = $products->where('id', $product['id'])->first();
                $maxQty = floatval(($p->in_shop ?? 0) + ($p ? ($data->getRelation('items')->where('variation_id', $product['id'])->first()->qty ?? 0) : 0));

                $isAuctionItem = $isAuction && $p->auction_price;

                $itemTotalPrice = $product['qty'] * ($isAuctionItem ? $p->auction_price : $p->price);
                $itemDiscountPrice = $isAuctionItem ? ($product['qty'] * ($p->price - $p->auction_price)) : 0;

                $totalItemsPrice += $itemTotalPrice;
                $totalItemsDiscount += $itemDiscountPrice;

                $product['discount_price'] = $itemDiscountPrice;
                $product['total_price'] = $itemTotalPrice;
                $product['max_allowed'] = $maxQty;
                $totalWeight += $p->weight * $product['qty'];

                $tmpProducts[] = $product;
                $tmp = array_merge($tmp, [
                    "products.$idx.id" => ['required', Rule::in($products->pluck('id'))],
                    "products.$idx.qty" => ['required', 'numeric', 'gt:0', 'max:' . $maxQty],

                ]);

            }
            $this->products = $tmpProducts;
            $totalShippingPrice = 0;

            $totalPrice = 0;
            $changePrice = 0;
            $distance = 0;
            $method = $shippingMethods->where('id', $this->shipping_method_id)->first();
            if ($method) {
                $repoLocation = $data->repository ? explode(',', $data->repository->location ?? ",") : [null, null];
                $distance = Util::distance($this->lat ?? null, $this->lon ?? null, $repoLocation [0] ?? null, $repoLocation [1] ?? null, 'k');
                $totalShippingPrice += ($method->base_price + ($distance ?? 0 * ($method->per_distance_price ?? 0)) + ($totalWeight * ($method->per_weight_price ?? 0)));

            }
            $mainTotalItemsPrice = $totalItemsPrice;
            $mainTotalShippingPrice = $totalShippingPrice;

            $totalItems = collect($this->products)->map(fn($e) => $e['qty'] ?? 0)->sum();
            $totalShippingPrice = $this->total_shipping_price != null && $this->total_shipping_price != $totalShippingPrice ? $this->total_shipping_price : $totalShippingPrice;
            $totalItemsPrice = $this->total_items_price != null && $this->total_items_price != $totalItemsPrice ? $this->total_items_price : $totalItemsPrice;
            $changePrice = ($totalItemsPrice - $mainTotalItemsPrice) + ($totalShippingPrice - $mainTotalShippingPrice);
            $this->merge([

                'change_price' => $changePrice,
                'distance' => $distance,
                'delivery_date_shamsi' => $this->delivery_date,
                'delivery_date' => $this->delivery_date ? Jalalian::fromFormat('Y/m/d', $this->delivery_date)->toCarbon() : null,
                'total_shipping_price' => $totalShippingPrice,
                'total_discount' => $totalItemsDiscount,
                'database_products' => $products->whereIn('id', collect($this->products)->pluck('id')),
                'total_items' => $totalItems,
                'total_items_price' => $totalItemsPrice,
                'total_price' => $totalItemsPrice + $totalShippingPrice - $totalItemsDiscount
            ]);
            $tmp = array_merge($tmp, [
                'total_shipping_price' => ['required', 'numeric', 'min:0'],
                'total_discount' => ['required', 'numeric', 'min:0',],
                'change_price' => ['nullable', 'numeric',],
                'total_price' => ['required', 'numeric', 'min:0'],

            ]);

//        if ($this->cmnd == 'status') {
//            $tmp = array_merge($tmp, [
//                'status' => ['required', Rule::in(collect(Variable::ORDER_STATUSES)->pluck('name'))],
//            ]);
//        }

        }
        return $tmp;

    }

    public function messages()
    {
        $tmp = [];
        $tmp = array_merge($tmp, [
            'products.required' => sprintf(__("validator.required"), __('product')),


            'address.required' => sprintf(__("validator.required"), __('address')),
            'address.max' => sprintf(__("validator.max_len"), __('address'), 2048, mb_strlen($this->address)),
            'province_id.required' => sprintf(__("validator.required"), __('province')),
            'county_id.required' => sprintf(__("validator.required"), __('county')),
            'district_id.required' => sprintf(__("validator.required"), __('district/city')),
            'district_id.in' => sprintf(__("validator.invalid"), __('district/city')),
            'postal_code.required' => sprintf(__("validator.required"), __('postal_code')),
            'postal_code.numeric' => sprintf(__("validator.numeric"), __('postal_code')),

            'receiver_fullname.required' => sprintf(__("validator.required"), __('receiver_fullname')),
            'receiver_fullname.max' => sprintf(__("validator.max_len"), __('receiver_fullname'), 100, mb_strlen($this->receiver_fullname)),
            'receiver_fullname.min' => sprintf(__("validator.min_len"), 3, mb_strlen($this->receiver_fullname)),

            'receiver_phone.required' => sprintf(__("validator.required"), __('receiver_phone')),
            'receiver_phone.numeric' => sprintf(__("validator.numeric"), __('receiver_phone')),
            'receiver_phone.digits' => sprintf(__("validator.digits"), __('receiver_phone'), 11),

            'from_location.regex' => sprintf(__("validator.invalid"), __('location')),

            'total_shipping_price.min' => sprintf(__("validator.min"), __('shipping_price'), 0),

            'total_discount.min' => sprintf(__("validator.min"), __('discount'), 0),
            'total_discount.max' => sprintf(__("validator.max_amount"), __('discount'), $this->total_price + $this->total_discount, $this->total_discount),

            'total_price.min' => sprintf(__("validator.min"), __('total_price'), 0),

        ]);
        foreach ($this->products ?? [] as $idx => $product)
            $tmp = array_merge($tmp, [
                "products.$idx.id.required" => sprintf(__("validator.required"), __('product')),

                "products.$idx.qty.required" => sprintf(__("validator.required"), __('qty')),
                "products.$idx.qty.min" => sprintf(__('validator.min_items'), '', 1, $product['qty'] ?? 0),
                "products.$idx.qty.max" => sprintf(__('validator.max_items'), '', floatval($product['max_allowed'] ?? 0), $product['qty']),
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
