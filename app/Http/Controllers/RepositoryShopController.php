<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\User;
use App\Models\Variation;
use Illuminate\Http\Request;

class RepositoryShopController extends Controller
{
    public
    function search(Request $request)
    {
        //disable ONLY_FULL_GROUP_BY
//        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
//        $user = auth()->user();
        $admin = $request->user();
        $search = $request->search;
        $inShop = $request->in_shop;
        $parentIds = $request->parent_ids;
        $cityId = $request->city_id;
        $provinceId = $request->province_id;
        $page = $request->page ?: 1;
        $orderBy = 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $agency = Agency::find($admin->agency_id);

        $query = Variation::join('repositories', function ($join) use ($parentIds, $agency) {
            $join->on('variations.repo_id', '=', 'repositories.id')
                ->where('repositories.status', 'active')
                ->where('variations.agency_level', '<', '3')
                ->where('repositories.agency_id', optional($agency)->parent_id)
                ->where(function ($query) use ($parentIds) {
                    if ($parentIds && is_array($parentIds) && count($parentIds) > 0)
                        $query->whereIntegerInRaw('variations.product_id', $parentIds);
                })->where(function ($query) {
                    $query->where('variations.in_shop', '>', 0);
                });

        })->select('variations.id', 'variations.product_id',
            'repositories.id as repo_id',
            'variations.name as name',
            'repositories.name as repo_name',
            'variations.pack_id as pack_id',
            'variations.grade as grade',
            'variations.price as price',
            'variations.auction_price as auction_price',
            'variations.auction_price as auction_price',
            'variations.weight as weight',
            'variations.in_auction as in_auction',
            'variations.in_shop as in_shop',
            'variations.product_id as parent_id',
            'repositories.province_id as province_id',

        )
            ->orderBy('variations.updated_at', 'DESC')//
            //            ->orderByRaw("IF(articles.charge >= articles.view_fee, articles.view_fee, articles.id) DESC")
        ;

        if (!$agency)
            $query->where('variations.id', 0);

        if ($search)
            $query->where('variations.name', 'like', "%$search%");

        $res = $query->paginate($paginate, ['*'], 'page', $page)//            ->getCollection()->groupBy('parent_id')
        ;
        return $res;
    }

    public function updateCart(Request $request)
    {

//        DB::listen(function ($query) {
//            Log::info($query->sql);
//        });
        $user = $request->user();

        $ip = $request->ip();
        $productId = $request->product_id;
        $qty = $request->qty;
        $cmnd = $request->cmnd;
        $cityId = session()->get('city_id');
        $addressIdx = $request->address_idx;
        $needAddress = false;
        $needSelfReceive = false;

//        if ($cmnd == 'count') {
//            $product = Product::with('repository')->find($productId);
//            if (isset($qty) && !$product)
//                return response()->json(['message' => __('product_not_found')], Variable::ERROR_STATUS);
//
//            $repository = $product->getRelation('repository');
//            if (!$repository)
//                return response()->json(['message' => __('repository_not_found')], Variable::ERROR_STATUS);
//            if (!$cityId || !is_int($cityId))
//                return response()->json(['message' => __('select_city_from')], Variable::ERROR_STATUS);
//            if (!in_array($cityId, $repository->cities))
//                return response()->json(['message' => __('repository_not_support_city')], Variable::ERROR_STATUS);
//        }
        $cols = 'items.product:id,name,repo_id,price,auction_price,in_shop,weight,pack_id,grade';
        $carts = Cart::where(function ($query) use ($ip) {
            $query->whereNotNull('ip')->where('ip', $ip);
        })->orWhere(function ($query) use ($user) {
            if ($user)
                $query->whereNotNull('user_id')->where('user_id', optional($user)->id);
        })->with($cols)->get();

        $cItems = $carts->pluck('items')->flatten();

        $lastCart = $carts->count() > 0 ? $carts->pop() : null;

        if ($carts->count() > 0) {
            CartItem::whereIn('cart_id', $carts->pluck('id'))->update(['cart_id' => $lastCart->id]);
            $cItems->each(function ($e) use ($lastCart) {
                $e->cart_id = $lastCart->id;
            });
            Cart::whereIn('id', $carts->pluck('id'))->delete();
            $lastCart->setRelation('items', $cItems);
        }
//        dd($lastCart->getRelation('items'));
        $cart = $lastCart;

        $cartItems = $cart ? $cart->getRelation('items') : collect([]);
        $cart = $cart ?? Cart::create([
            'user_id' => optional($user)->id,
            'ip' => $ip,
            'last_activity' => Carbon::now(),
            'order_id' => null,
        ]);
        //set cart address
        $addresses = $user->addresses ?? [];
        //clear address
        if ($request->exists('address_idx') && $request->address_idx == null) {
            $cart->address_idx = null;
            $cart->save();
        }
        $addressIdx = $addressIdx ?? $cart->address_idx;
        $addressIdx = $addressIdx !== null ? intval($addressIdx) : null;
        $address = null;
        if ($user && is_int($addressIdx) && $addressIdx >= 0 && count($addresses) > $addressIdx) {
            $address = $addresses[$addressIdx];
            $cityId = $address['district_id'] ?? $address['county_id'] ?? $cityId;
            $cityId = intval($cityId);
            if (isset($request->address_idx)) {
                session()->put('city_id', $cityId);
                $cart->update(['address_idx' => $addressIdx]);
            }
        }
        $cart->address = $address;


//        $productRepositories = Repository::whereIn('id', $cartItems->pluck('product.repo_id'))->get();

        $beforeQty = 0;
        $isAuction = Setting::getValue('is_auction');

        //add/remove/update an item
        if ($productId && is_int($qty)) {

            $cartItem = $cartItems->where('product_id', $productId)->first();
            $product = optional($cartItem)->getRelation('product') ?? Variation::find($productId);
            $inShopQty = optional($product)->in_shop ?? 0;
            $minAllowed = optional($product)->min_allowed ?? 0;
            if ($qty < 0)
                return response()->json(['message' => sprintf(__('validator.invalid'), __('requested_qty'))], Variable::ERROR_STATUS);
            elseif ($qty > $inShopQty)
                return response()->json(['message' => sprintf(__('validator.max_items'), __('product'), $inShopQty, $qty)], Variable::ERROR_STATUS);
            elseif ($qty < $minAllowed)
                return response()->json(['message' => sprintf(__('validator.min_order_product'), $minAllowed)], Variable::ERROR_STATUS);

            if ($cartItem) {
                if ($qty == 0) {
                    optional(CartItem::find($cartItem->id))->delete();

                    $cartItems = $cartItems->reject(function ($element) use ($cartItem) {
//                        dd($element->id . ' ' . $cartItem->id);
                        return $element->id == $cartItem->id;
                    });


                }
                $cartItem->qty = $qty;
                $cartItem->save();
//                dd($cartItems);

            } elseif ($qty > 0) {
                $cartItem = CartItem::create([
                    'name' => $product->name,
                    'repo_id' => $product->repo_id,
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'qty' => $qty,
                ]);
                $cartItem->setRelation('product', $product);
                $cartItems->push($cartItem);
            }
            $cart->setRelation('items', $cartItems);

        }
        $cart->total_items_price = 0;

        $errors = $cart->errors ?? [];
        foreach ($cartItems as $cartItem) {

//            dd($cartItems);
            $product = $cartItem->getRelation('product');
            if ($cartItem->qty > $product->in_shop) {
//                $cartItem->qty = $product->in_shop;
//                $cartItem->save();
                $cartItem->error_message = $product->in_shop > 0 ? sprintf(__('validator.max_items'), __('product'), $product->in_shop, $cartItem->qty) : __('this_item_finished');
                $errors[] = ['key' => $product->name, 'type' => 'product', 'message' => $cartItem->error_message];
            } elseif ($cartItem->qty < $product->min_allowed) {
//                $cartItem->qty = $product->in_shop;
//                $cartItem->save();
                $cartItem->error_message = sprintf(__('validator.min_order_product'), $product->min_allowed);
                $errors[] = ['key' => $product->name, 'type' => 'product', 'message' => $cartItem->error_message];
            }
            $itemTotalPrice = $cartItem->qty * ($isAuction ? $product->auction_price : $product->price);
//            $cartItem->save();
            $cartItem->total_price = $itemTotalPrice;
            $cart->total_items_price += $itemTotalPrice;
        }
        $cart->setRelation('items', $cartItems);
        $cart->errors = $errors;
        //select shipping
        $repos = Repository::whereIn('id', $cartItems->pluck('repo_id'))->with('shippingMethods')->get();


        $shipments = [];
        foreach ($cartItems->all() as $idx => $cartItem) {
            $repo = $repos->find($cartItem->repo_id);

            if ($repo && $repo->status == 'active') {
                $shippingMethods = $repo->getRelation('shippingMethods')->where('status', 'active');
                $supportCity = in_array($cityId, $repo->cities);
                $cityProductRestrict = $supportCity ? $shippingMethods
                    ->filter(function ($e) use ($cartItem, $cityId, $repo) {
                        $products = $e->products ?? [];
                        $cities = $e->cities ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($products) > 0 && count($cities) > 0 && in_array($cartItem->product_id, $products) && in_array($cityId, $cities);
                    })->first() : null;
                if ($cityProductRestrict) {
                    $shipments[$idx] = ['method_id' => $cityProductRestrict->id, 'cart_item' => $cartItem, 'shipping' => $cityProductRestrict, 'repo_name' => $repo->name];
                    $needAddress = true;
                    continue;
                }
                $productRestrict = $supportCity ? $shippingMethods
                    ->filter(function ($e) use ($cartItem) {
                        $products = $e->products ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($products) > 0 && in_array($cartItem->product_id, $products);
                    })->first() : null;
                if ($productRestrict) {
                    $shipments[$idx] = ['method_id' => $productRestrict->id, 'cart_item' => $cartItem, 'shipping' => $productRestrict, 'repo_name' => $repo->name];
                    $needAddress = true;
                    continue;
                }
                $cityRestrict = $supportCity ? $shippingMethods
                    ->filter(function ($e) use ($cartItem, $cityId) {
                        $cities = $e->cities ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($cities) > 0 && in_array($cityId, $cities);
                    })->first() : null;
                if ($cityRestrict) {
                    $shipments[$idx] = ['method_id' => $cityRestrict->id, 'cart_item' => $cartItem, 'shipping' => $cityRestrict, 'repo_name' => $repo->name];
                    $needAddress = true;
                    continue;
                }
                $noRestrict = $supportCity ? $shippingMethods
                    ->filter(function ($e) use ($cartItem, $cityId, $repo) {
                        $products = $e->products ?? [];
                        $cities = $e->cities ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($products) == 0 && count($cities) == 0;
                    })->first() : null;
                if ($noRestrict) {
                    $shipments[$idx] = ['method_id' => $noRestrict->id, 'cart_item' => $cartItem, 'shipping' => $noRestrict, 'repo_name' => $repo->name];
                    $needAddress = true;
                    continue;
                }
            }
            //use default shipping (go to repo)

            $default = collect(Variable::getDefaultShippingMethods()[0]);
            $default['address'] = optional($repo)->address;
            $default['location'] = optional($repo)->location;
            $default['province_id'] = optional($repo)->province_id;
            $default['county_id'] = optional($repo)->county_id;
            $errors = $cart->errors ?? [];
            $methodId = 'rand-' . time();
            $errorMessage = __('repo_is_inactive');

            if (!$repo) {
            } elseif ($repo->status != 'active') {
                $methodId = 'repo-inactive-' . $repo->id;
            } elseif (!$repo->allow_visit) {
                $methodId = 'repo-no-visit-' . $repo->id;
                $errorMessage = __('repo_not_support_location');
            } else {
                $methodId = 'repo-' . $repo->id;
                $errorMessage = null;
                $needSelfReceive = true;
            }
            $default['id'] = $methodId;
            if ($errorMessage) {
                $errors[] = ['key' => $methodId, 'type' => 'shipping', 'message' => $errorMessage];
                $default['error_message'] = $errorMessage;
            }
            $shipments[$idx] = ['method_id' => $methodId, 'cart_item' => $cartItem, 'shipping' => $default, 'repo_name' => $repo->name, 'error_message' => $errorMessage];

        }
        $needAddress = $needAddress && in_array($request->current, ['checkout.payment', 'checkout.shipping']);

        if ($needAddress && $address == null) {
            $errors[] = ['key' => 'address', 'type' => 'address', 'message' => sprintf(__('validator.required'), __('address'))];

        }

        $cart->shipments = collect($shipments)->groupBy('method_id');
        $cart->total_shipping_price = 0;
        $cart->total_items = 0;
        $shipments = [];
        foreach ($cart->shipments as $items) {
            $totalWeight = 0;
            $totalPrice = 0;
            $basePrice = 0;
            $shipping = null;
            foreach ($items as $idx => $item) {
                $cartItem = $item['cart_item'];
                $product = $cartItem->getRelation('product');
                $totalWeight += $product->weight * $cartItem->qty;
                $totalPrice += $product->weight * $cartItem->qty * $item['shipping']['per_weight_price'];
                $basePrice = $basePrice > 0 ? $basePrice : $item['shipping']['base_price'];
                $cart->total_items += $cartItem->qty ?? 0;
                $shipping = $item['shipping'];
                unset($item['shipping']);
                $items[$idx] = $item;
            }
            $errorMessage = null;
            if ($totalWeight < $shipping['min_order_weight']) {
                $errorMessage = sprintf(__('validator.min_order_weight'), $shipping['min_order_weight'] . ' ' . __('kg'), $totalWeight);
                $shipping['error_message'] = $shipping['error_message'] ?? $errorMessage;
                $errors[] = ['key' => 'min-order-weight', 'type' => 'shipping', 'message' => $errorMessage];
            }
            $shipments[] = ['items' => $items, 'method' => $shipping, 'total_price' => $basePrice + $totalPrice];
            $cart->total_shipping_price += $basePrice + $totalPrice;
        }
        $cart->errors = $errors ?? [];
        $cart->shipments = $shipments;
        $cart->total_price = $cart->total_items_price + $cart->total_shipping_price;
        $cart->need_address = $needAddress;
        $cart->need_self_receive = $needSelfReceive;
        $cart->payment_methods = Variable::getPaymentMethods();
//        if ($user) {
//            $res = User::getLocation(Variable::$CITIES);
//            $addresses = $user->addresses;
//            $cart->address = $addresses && is_int($addressIdx) && count($addresses) > $idx ? $addresses[$idx] : null;
//        }
//        dd($cart);
//        dd($cart);
//        dd($cartItems->pluck('repo_id'));
//        dd(ShippingMethod::whereIn('repo_id', $cartItems->pluck('repo_id'))->get());
        unset ($cart->items);
        if ($request->cmnd == 'create_order_and_pay')
            return (new OrderController())->create(new OrderRequest(['cart' => $cart]));
        else return response()->json(['message' => __('cart_updated'), 'cart' => $cart], Variable::SUCCESS_STATUS);
    }
}
