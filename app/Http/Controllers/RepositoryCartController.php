<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\RepositoryOrderRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Repository;
use App\Models\RepositoryCart;
use App\Models\RepositoryCartItem;
use App\Models\Setting;
use App\Models\Variation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RepositoryCartController extends Controller
{
    public function update(Request $request)
    {

//        DB::listen(function ($query) {
//            Log::info($query->sql);
//        });
        $admin = $request->user();
        $productId = $request->variation_id;
        $qty = $request->qty;
        $cmnd = $request->cmnd;
        $repoId = $request->repo_id;
        $needAddress = false;
        $needSelfReceive = false;
        $myAgency = Agency::find($admin->agency_id);
        if (!$myAgency || !$admin->hasAccess('create_repository_order'))
            return response()->json(['message' => __('access_denied')], Variable::ERROR_STATUS);

        $cols = 'items.product:id,name,repo_id,price,auction_price,in_shop,weight,pack_id,grade';
        $cart = RepositoryCart::where('to_agency_id', $myAgency->id)->with($cols)->first();

        $cartItems = $cart ? $cart->getRelation('items') : collect([]);
        $cart = $cart ?? RepositoryCart::create([
            'admin_id' => $admin->id,
            'to_agency_id' => $admin->agency_id,
            'to_repo_id' => $repoId,
        ]);

        //set cart address from repo
        //clear repo
        if ($request->exists('to_repo_id')) {
            $cart->to_repo_id = $request->to_repo_id;
            $cart->save();
        }
        $addressIdx = $repoId ?? $cart->to_repo_id;
        $address = null;
        if ($addressIdx) {
            $repo = Repository::find($addressIdx);
            if ($repo) {
                $cart->to_repo_id = $repo->id;
                $cart->to_repo = $repo;
                $address = [
                    'province_id' => $repo->province_id,
                    'county_id' => $repo->county_id,
                    'district_id' => $repo->district_id,
                    'address' => $repo->address,
                    'to_phone' => $repo->phone,
                    'postal_code' => $repo->postal_code,
                    'location' => $repo->location,
                ];
            }
        }
        $cart->to_address = $address;


//        $productRepositories = Repository::whereIn('id', $cartItems->pluck('product.repo_id'))->get();

        $beforeQty = 0;
        $isAuction = Setting::getValue('is_auction');

        //add/remove/update an item
        if ($productId && is_int($qty)) {

            $cartItem = $cartItems->where('variation_id', $productId)->first();
            $product = optional($cartItem)->getRelation('product') ?? Variation::find($productId);
            $inShopQty = optional($product)->in_shop ?? 0;
            $minAllowed = optional($product)->min_allowed ?? 0;
            if ($qty < 0)
                return response()->json(['message' => sprintf(__('validator.invalid'), __('requested_qty'))], Variable::ERROR_STATUS);
            elseif ($qty > $inShopQty)
                return response()->json(['message' => sprintf(__('validator.max_items'), __('product'), floatval($inShopQty), $qty)], Variable::ERROR_STATUS);
            elseif ($qty < $minAllowed)
                return response()->json(['message' => sprintf(__('validator.min_order_product'), $minAllowed)], Variable::ERROR_STATUS);

            if ($cartItem) {
                if ($qty == 0) {
                    optional(RepositoryCartItem::find($cartItem->id))->delete();

                    $cartItems = $cartItems->reject(function ($element) use ($cartItem) {
//                        dd($element->id . ' ' . $cartItem->id);
                        return $element->id == $cartItem->id;
                    });


                }
                $cartItem->qty = $qty;
                $cartItem->save();
//                dd($cartItems);

            } elseif ($qty > 0) {
                $cartItem = RepositoryCartItem::create([
                    'name' => $product->name,
                    'repo_id' => $product->repo_id,
                    'cart_id' => $cart->id,
                    'variation_id' => $productId,
                    'qty' => $qty,
                ]);
                $cartItem->setRelation('product', $product);
                $cartItems->push($cartItem);
            }
            $cart->setRelation('items', $cartItems);

        }
        $cart->total_items_price = 0;
        $cart->total_items_discount = 0;

        $errors = $cart->errors ?? [];
        foreach ($cartItems as $cartItem) {

//            dd($cartItems);
            $product = $cartItem->getRelation('product');
            if ($cartItem->qty > $product->in_shop) {
//                $cartItem->qty = $product->in_shop;
//                $cartItem->save();
                $cartItem->error_message = $product->in_shop > 0 ? sprintf(__('validator.max_items'), __('product'), floatval($product->in_shop), $cartItem->qty) : __('this_item_finished');
                $errors[] = ['key' => $product->name, 'type' => 'product', 'message' => $cartItem->error_message];
            } elseif ($cartItem->qty < $product->min_allowed) {
//                $cartItem->qty = $product->in_shop;
//                $cartItem->save();
                $cartItem->error_message = sprintf(__('validator.min_order_product'), $product->min_allowed);
                $errors[] = ['key' => $product->name, 'type' => 'product', 'message' => $cartItem->error_message];
            }
            $itemTotalPrice = $cartItem->qty * ($isAuction ? $product->auction_price : $product->price);
//            $cartItem->save();
            $cartItem->total_discount = $isAuction ? ($cartItem->qty * ($product->price - $product->auction_price)) : 0;
            $cartItem->total_price = $itemTotalPrice;
            $cart->total_items_price += $itemTotalPrice;
            $cart->total_items_discount += $cartItem->total_discount;
        }
        $cart->setRelation('items', $cartItems);
        $cart->errors = $errors;


        //select shipping


        $repos = Repository::whereIn('id', $cartItems->pluck('repo_id'))->with('shippingMethods')->get();

        $cityId = $address['district_id'] ?? $address['county_id'] ?? null;
        $shipments = [];
        foreach ($cartItems->all() as $idx => $cartItem) {
            $repo = $repos->find($cartItem->repo_id);

            //if user checked visit
            if ($request->exists('visit_repo_' . $cartItem->repo_id)) {
                $cartItem->visit_checked = $request->{"visit_repo_$cartItem->repo_id"} ?? false;
                RepositoryCartItem::where('id', $cartItem->id)->update(['visit_checked' => boolval($cartItem->visit_checked)]);
            }


            if ($repo && $repo->status == 'active') {
                $shippingMethods = $repo->getRelation('shippingMethods')->where('status', 'active');
                $supportCity = in_array($cityId, $repo->cities ?? []);
                $cityProductRestrict = $supportCity ? $shippingMethods
                    ->filter(function ($e) use ($cartItem, $cityId, $repo) {
                        $products = $e->products ?? [];
                        $cities = $e->cities ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($products) > 0 && count($cities) > 0 && in_array($cartItem->variation_id, $products) && in_array($cityId, $cities);
                    })->first() : null;
                if ($cityProductRestrict) {
                    $shipments[$idx] = [
                        'method_id' => $cityProductRestrict->id,
                        'cart_item' => $cartItem,
                        'shipping' => $cityProductRestrict,
                        'repo_id' => $repo->id,
                        'repo_location' => $repo->location,
                        'agency_id' => $repo->agency_id,
                        'repo_name' => $repo->name,
                        'allow_visit' => optional($repo)->allow_visit ?? false,
                        'visit_checked' => $cartItem->visit_checked ?? false,
                        'has_available_shipping' => true,
                    ];
                    $needAddress = true;

//                    if (!$cartItem->visit_checked)
//                        continue;
                }

                $productRestrict = $supportCity ? $shippingMethods
                    ->filter(function ($e) use ($cartItem) {
                        $products = $e->products ?? [];

                        return $e->repo_id == $cartItem->repo_id && count($products) > 0 && in_array($cartItem->variation_id, $products);
                    })->first() : null;
                if ($productRestrict) {
                    $shipments[$idx] = [
                        'method_id' => $productRestrict->id,
                        'cart_item' => $cartItem,
                        'shipping' => $productRestrict,
                        'repo_id' => $repo->id,
                        'repo_location' => $repo->location,
                        'agency_id' => $repo->agency_id,
                        'repo_name' => $repo->name,
                        'allow_visit' => optional($repo)->allow_visit ?? false,
                        'visit_checked' => $cartItem->visit_checked ?? false,
                        'has_available_shipping' => true,
                    ];

                    $needAddress = true;

//                    if (!$cartItem->visit_checked)
//                        continue;
                }
                $cityRestrict = $supportCity ? $shippingMethods
                    ->filter(function ($e) use ($cartItem, $cityId) {
                        $cities = $e->cities ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($cities) > 0 && in_array($cityId, $cities);
                    })->first() : null;
                if ($cityRestrict) {
                    $shipments[$idx] = [
                        'method_id' => $cityRestrict->id,
                        'cart_item' => $cartItem,
                        'shipping' => $cityRestrict,
                        'repo_id' => $repo->id,
                        'repo_location' => $repo->location,
                        'agency_id' => $repo->agency_id,
                        'repo_name' => $repo->name,
                        'allow_visit' => optional($repo)->allow_visit ?? false,
                        'visit_checked' => $cartItem->visit_checked ?? false,
                        'has_available_shipping' => true,
                    ];
                    $needAddress = true;
//                    if (!$cartItem->visit_checked)
//                        continue;
                }
                $noRestrict = $supportCity ? $shippingMethods
                    ->filter(function ($e) use ($cartItem, $cityId, $repo) {
                        $products = $e->products ?? [];
                        $cities = $e->cities ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($products) == 0 && count($cities) == 0;
                    })->first() : null;
                if ($noRestrict) {

                    $shipments[$idx] = [
                        'method_id' => $noRestrict->id,
                        'cart_item' => $cartItem,
                        'shipping' => $noRestrict,
                        'repo_id' => $repo->id,
                        'repo_location' => $repo->location,
                        'agency_id' => $repo->agency_id,
                        'repo_name' => $repo->name,
                        'allow_visit' => optional($repo)->allow_visit ?? false,
                        'visit_checked' => $cartItem->visit_checked ?? false,
                        'has_available_shipping' => true,
                    ];
                    $needAddress = true;
//                    if (!$cartItem->visit_checked)
//                        continue;
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
                $needSelfReceive = !$shipments[$idx] || $cartItem->visit_checked;
            }
            $default['id'] = $methodId;
            if ($errorMessage) {
                $errors[] = ['key' => $methodId, 'type' => 'shipping', 'message' => $errorMessage];
                $default['error_message'] = $errorMessage;
            }
            if (!$shipments[$idx] || $cartItem->visit_checked) {
                $shipments[$idx] = [
                    'method_id' => $methodId,
                    'cart_item' => $cartItem,
                    'shipping' => $default,
                    'repo_name' => $repo->name,
                    'repo_id' => $repo->id,
                    'repo_location' => $repo->location,
                    'agency_id' => $repo->agency_id,
                    'error_message' => $errorMessage,
                    'has_available_shipping' => $shipments[$idx]['has_available_shipping'] ?? false,
                    'allow_visit' => optional($repo)->allow_visit ?? false,
                    'visit_checked' => $cartItem->visit_checked ?? false,
                ];

                $needAddress = false;

            }
        }
        $needAddress = $needAddress && in_array($request->current, ['checkout.payment', 'checkout.shipping']);

        if ($needAddress && $address == null) {
            $errors[] = ['key' => 'address', 'type' => 'address', 'message' => sprintf(__('validator.required'), __('address'))];

        }

        $cart->shipments = collect($shipments)->groupBy('method_id');
        $cart->total_shipping_price = 0;
        $cart->total_items = 0;
        $shipments = [];
        foreach ($cart->shipments as $i => $items) {
            $totalWeight = 0;
            $totalShippingPrice = 0;
            $totalItemsPrice = 0;
            $totalItemsDiscount = 0;
            $totalItems = 0;
            $basePrice = 0;
            $shipping = null;
            $repoId = null;
            $agencyId = null;
            $visitChecked = false;
            $hasAvailableShipping = false;
            $deliveryDate = null;
            $deliveryTimestamp = null;
            $distance = null;
            foreach ($items as $idx => $item) {

                $cartItem = $item['cart_item'];
                $repoLocation = $item['repo_location'];
                $distance = Util::distance($cart->to_address['lat'] ?? null, $cart->to_address['lon'] ?? null, explode(',', $repoLocation)[0] ?? null, explode(',', $repoLocation)[1] ?? null, 'k');

                $product = $cartItem->getRelation('product');
                $totalWeight += $product->weight * $cartItem->qty;
                $totalShippingPrice += ($product->weight * $cartItem->qty * ($item['shipping']['per_weight_price'] ?? 0)) + ($distance * ($item['shipping']['per_distance_price'] ?? 0));
                $basePrice = $basePrice > 0 ? $basePrice : ($item['shipping']['base_price'] ?? 0);
                $cart->total_items += $cartItem->qty ?? 0;
                $totalItems += $cartItem->qty ?? 0;
                $repoId = $item['repo_id'];
                $visitChecked = $item['visit_checked'];
                $hasAvailableShipping = $item['has_available_shipping'];
                $agencyId = $item['agency_id'];
                $deliveryDate = $cartItem->delivery_date;
                $deliveryTimestamp = $cartItem->delivery_timestamp;
                $totalItemsDiscount += $cartItem->total_discount;
                $totalItemsPrice += $cartItem->total_price;

                $shipping = $item['shipping'];
                unset($item['shipping']);
                $items[$idx] = $item;
            }
            $errorMessage = null;
            if ($totalWeight < ($shipping['min_order_weight'] ?? 0)) {
                $errorMessage = sprintf(__('validator.min_order_weight'), $shipping['min_order_weight'] . ' ' . __('kg'), $totalWeight);
                $shipping['error_message'] = $shipping['error_message'] ?? $errorMessage;
                $errors[] = ['key' => 'min-order-weight', 'type' => 'shipping', 'message' => $errorMessage];
            }
            $shipments[] = [
                'delivery_timestamp' => $deliveryTimestamp,
                'delivery_date' => $deliveryDate,
                'repo_id' => $repoId,
                'visit_checked' => $visitChecked,
                'agency_id' => $agencyId,
                'items' => $items,
                'distance' => $distance,
                'method_id' => $i,
                'method' => $shipping,
                'total_items' => $totalItems,
                'total_items_price' => $totalItemsPrice,
                'total_items_discount' => $totalItemsDiscount,
                'has_available_shipping' => $hasAvailableShipping,
                'total_shipping_price' => $basePrice + $totalShippingPrice
            ];
            $cart->total_shipping_price += $basePrice + $totalShippingPrice;
        }
        $cart->errors = $errors ?? [];
        $cart->shipments = $shipments;
        $cart->total_discount = $cart->total_items_discount;
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
//        dd($cartItems->pluck('repo_id'));
//        dd(ShippingMethod::whereIn('repo_id', $cartItems->pluck('repo_id'))->get());
//split orders base repo
        $orders = collect();
        foreach (collect($cart->shipments)->groupBy('method_id') as $methodId => $shipments) {
            $tmpCart = clone $cart;
            $tmpCart->shipping_method_id = str_starts_with($methodId, 'repo-') ? 1 : $methodId; //visit-repo [change id to 1]
            $tmpCart->total_items_discount = 0;
            $tmpCart->total_items_price = 0;
            $tmpCart->total_shipping_price = 0;
            $tmpCart->total_items = 0;
            $tmpCart->total_price = 0;
            $tmpCart->total_discount = 0;
            $tmpCart->distance = null;
            $tmpShipments = collect();
            foreach ($shipments as $shipment) {
                $tmpCart->delivery_timestamp = $shipment['delivery_timestamp'];
                $tmpCart->delivery_date = $shipment['delivery_date'];
                $tmpCart->repo_id = $shipment['repo_id'];
                $tmpCart->agency_id = $shipment['agency_id'];
                $tmpCart->distance = $shipment['distance'];
                $tmpShipments->add($shipment);
                $tmpCart->total_items_price += $shipment['total_items_price'];
                $tmpCart->total_items_discount += $shipment['total_items_discount'];
                $tmpCart->total_discount += $shipment['total_items_discount'];
                $tmpCart->total_shipping_price += $shipment['total_shipping_price'];
                $tmpCart->total_items += $shipment['total_items'];
                $tmpCart->total_price += ($shipment['total_items_price'] + $shipment['total_shipping_price']);
            }
            $tmpCart->shipments = $tmpShipments;
            unset($tmpCart->items);
            $orders->add(clone $tmpCart);
        }

        $cart->orders = $orders;
        unset ($cart->items);
        unset ($cart->shipments);

        if ($request->cmnd == 'create_order_and_process')
            return (new RepositoryOrderController())->createFromCard(new  Request(['cart' => $cart]));
        else return response()->json(['message' => __('cart_updated'), 'cart' => $cart], Variable::SUCCESS_STATUS);
    }
}
