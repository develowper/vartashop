<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\OrderRequest;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\RepositoryCartItem;
use App\Models\Variation;
use App\Models\Repository;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;
use PHPUnit\Framework\Constraint\Count;

class CartController extends Controller
{

    public function update(Request $request)
    {

//        DB::listen(function ($query) {
//            Log::info($query->sql);
//        });
        $user = auth('sanctum')->user();

        $ip = $request->ip();
        $productId = $request->variation_id;
        $qty = $request->qty;
        $cmnd = $request->cmnd;
        $cityId = session()->get('city_id');
        $addressIdx = $request->address_idx;
        $needAddress = false;
        $needSelfReceive = false;

        if (($user instanceof Admin) && ($productId || in_array($request->current, ['checkout.payment', 'checkout.shipping'])))
            return response()->json(['message' => __('admin_can_not_order')], Variable::ERROR_STATUS);
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
            foreach ($cartItems as $cartItem) {
                $cartItem->delivery_date = null;
                $cartItem->delivery_timestamp = null;
                $cartItem->save();
            }
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
        $taxPercent = Setting::getValue('tax_percent') ?? 0;

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
        $cart->total_weight = 0;

        $errors = $cart->errors ?? [];
        foreach ($cartItems as $cartItem) {
//            dd($cartItems);
            $product = $cartItem->getRelation('product');
            if (($cartItem->qty ?? 0) > ($product->in_shop ?? 0)) {
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
            $isAuctionItem = $isAuction && $product->auction_price;

            $itemTotalPrice = $cartItem->qty * ($isAuctionItem ? $product->auction_price : $product->price);
//            $cartItem->save();
            $cartItem->total_discount = $isAuctionItem ? ($cartItem->qty * ($product->price - $product->auction_price)) : 0;
            $cartItem->total_price = $itemTotalPrice;
            $cartItem->total_weight = $cartItem->qty * $product->weight;
            $cart->total_items_price += $itemTotalPrice;
            $cart->total_items_discount += $cartItem->total_discount;
            $cart->total_weight += $cartItem->total_weight;

        }
        $cart->setRelation('items', $cartItems);
//        $cart->errors = $errors;


        //select shipping


        $repos = Repository::whereIn('id', $cartItems->pluck('repo_id'))->with('shippingMethods')->get();


        $shipments = [];
        foreach ($cartItems->all() as $idx => $cartItem) {
            $repo = $repos->find($cartItem->repo_id);

            //if user checked visit
            if ($request->exists('visit_repo_' . $cartItem->repo_id)) {
                $cartItem->visit_checked = $request->{"visit_repo_$cartItem->repo_id"} ?? false;
                $cartItem->delivery_timestamp = null;
                $cartItem->delivery_date = null;
                CartItem::where('id', $cartItem->id)->update(['visit_checked' => boolval($cartItem->visit_checked), 'delivery_timestamp' => null, 'delivery_date' => null]);
                $needAddress = !$cartItem->visit_checked;
            }


            if ($repo && $repo->status == 'active') {
                $shippingMethods = $repo->getRelation('shippingMethods')->where('status', 'active');

                $shipments[$idx] = null;
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
                        'shipping' => clone $cityProductRestrict,
                        'repo_id' => $repo->id,
                        'repo_location' => $repo->location,
                        'agency_id' => $repo->agency_id,
                        'repo_name' => $repo->name,
                        'allow_visit' => optional($repo)->allow_visit ?? false,
                        'visit_checked' => boolval($cartItem->visit_checked) ?? false,
                        'has_available_shipping' => true,
                    ];
                    $needAddress = true;
//                    if (!$cartItem->visit_checked)
//                        continue;
                }

                $productRestrict = $supportCity && !$shipments[$idx] ? $shippingMethods
                    ->filter(function ($e) use ($cartItem) {
                        $products = $e->products ?? [];

                        return $e->repo_id == $cartItem->repo_id && count($products) > 0 && in_array($cartItem->variation_id, $products);
                    })->first() : null;
                if ($productRestrict) {
                    $shipments[$idx] = [
                        'method_id' => $productRestrict->id,
                        'cart_item' => $cartItem,
                        'shipping' => clone $productRestrict,
                        'repo_id' => $repo->id,
                        'repo_location' => $repo->location,
                        'agency_id' => $repo->agency_id,
                        'repo_name' => $repo->name,
                        'allow_visit' => optional($repo)->allow_visit ?? false,
                        'visit_checked' => boolval($cartItem->visit_checked) ?? false,
                        'has_available_shipping' => true,
                    ];
                    $needAddress = true;
//                    if (!$cartItem->visit_checked)
//                        continue;
                }
                $cityRestrict = $supportCity && !$shipments[$idx] ? $shippingMethods
                    ->filter(function ($e) use ($cartItem, $cityId) {
                        $cities = $e->cities ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($cities) > 0 && in_array($cityId, $cities);
                    })->first() : null;
                if ($cityRestrict) {
                    $shipments[$idx] = [
                        'method_id' => $cityRestrict->id,
                        'cart_item' => $cartItem,
                        'shipping' => clone $cityRestrict,
                        'repo_id' => $repo->id,
                        'repo_location' => $repo->location,
                        'agency_id' => $repo->agency_id,
                        'repo_name' => $repo->name,
                        'allow_visit' => optional($repo)->allow_visit ?? false,
                        'visit_checked' => boolval($cartItem->visit_checked) ?? false,
                        'has_available_shipping' => true,
                    ];
                    $needAddress = true;
//                    if (!$cartItem->visit_checked)
//                        continue;
                }
                $noRestrict = $supportCity && !$shipments[$idx] ? $shippingMethods
                    ->filter(function ($e) use ($cartItem, $cityId, $repo) {
                        $products = $e->products ?? [];
                        $cities = $e->cities ?? [];
                        return $e->repo_id == $cartItem->repo_id && count($products) == 0 && count($cities) == 0;
                    })->first() : null;
                if ($noRestrict) {
                    $shipments[$idx] = [
                        'method_id' => $noRestrict->id,
                        'cart_item' => $cartItem,
                        'shipping' => clone $noRestrict,
                        'repo_id' => $repo->id,
                        'repo_location' => $repo->location,
                        'agency_id' => $repo->agency_id,
                        'repo_name' => $repo->name,
                        'allow_visit' => optional($repo)->allow_visit ?? false,
                        'visit_checked' => boolval($cartItem->visit_checked) ?? false,
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
            $default['timestamps'] = Variable::TIMESTAMPS;
//            $errors = $cart->errors ?? [];
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
                    'shipping' => clone $default,
                    'repo_name' => $repo->name,
                    'repo_id' => $repo->id,
                    'repo_location' => $repo->location,
                    'agency_id' => $repo->agency_id,
                    'error_message' => $errorMessage,
                    'has_available_shipping' => boolval($shipments[$idx]['has_available_shipping'] ?? false),
                    'allow_visit' => optional($repo)->allow_visit ?? false,
                    'visit_checked' => boolval($cartItem->visit_checked) ?? false,
                ];
                $needAddress = false;
            }
            //if user checked timestamp
            if ($request->exists('timestamp_shipping_' . $shipments[$idx]['method_id'])) {
                $timestampIdx = $request->{"timestamp_shipping_" . $shipments[$idx]['method_id']};
                $day = 0;
                $selected = 0;
                $deliveryDate = null;
                $deliveryTimestamp = null;
                if (isset($shipments[$idx]['shipping']['timestamps'][$timestampIdx])) {
                    foreach ($shipments[$idx]['shipping']['timestamps'] as $ix => $timestamp) {
                        if ($ix == $timestampIdx) {
                            $deliveryTimestamp = $timestamp['from'] . '-' . $timestamp['to'];
                            $deliveryDate = Carbon::now()->addDays($day)->toDate();
                            if ($deliveryDate && $deliveryTimestamp)
                                CartItem::where('id', $cartItem->id)->update(['delivery_date' => $deliveryDate, 'delivery_timestamp' => $deliveryTimestamp]);
                            $cartItem->delivery_date = $deliveryDate;
                            $cartItem->delivery_timestamp = $deliveryTimestamp;

                        }
                        if (count($shipments[$idx]['shipping']['timestamps']) > $ix + 1) {
                            if ($timestamp['to'] > $shipments[$idx]['shipping']['timestamps'][$ix + 1]['from']) {
                                $day++;
                            }
                        }
                    }
                }
            }


            //prepare and deactive next timestamp and check before selected
            $now = Carbon::now();
            $date = $cartItem->delivery_date;
            $timestamp = $cartItem->delivery_timestamp;
            $selectedFrom = $timestamp ? explode('-', $timestamp)[0] : 0;
            $selectedDay = $date ? Carbon::createFromDate($now)->startOfDay()->diffInDays($date, false) : null;
            $day = 0;
            $editedTimestamps = [];
//            $cart->errors = $errors ?? [];
            foreach ($shipments[$idx]['shipping']['timestamps'] ?? [] as $ix => $timestamp) {

                $jalali = Jalalian::fromCarbon($now->addDays($day));
                $timestamp['day'] = $jalali->format('%A');
                $timestamp['group'] = $day;
                $timestamp['selected'] = $selectedFrom == $timestamp['from'] && $selectedDay == $day;


                //error for past times
                if ($day == 0) {
                    $timestamp['active'] = $timestamp['active'] && $timestamp['from'] > $jalali->getHour();
                    if ($selectedDay == $day && $selectedFrom <= $jalali->getHour() && !$cartItem->visit_checked && $needAddress) {
                        $errors[] = ['key' => $shipments[$idx]['method_id'], 'type' => 'timestamp', 'message' => __('timestamp_is_inactive')];
                        $shipments[$idx]['error_message'] = __('timestamp_is_inactive');
                    }
                }
                //error for inactive times
                if ($selectedDay == $day && $selectedFrom == $timestamp['from'] && !$timestamp['active']) {
                    $errors[] = ['key' => $shipments[$idx]['method_id'], 'type' => 'timestamp', 'message' => __('timestamp_is_inactive')];
                    $shipments[$idx]['error_message'] = __('timestamp_is_inactive');
                }

                $editedTimestamps[] = $timestamp;
                //count days [next time lower than before]
                if (count($shipments[$idx]['shipping']['timestamps']) > $ix + 1) {
                    if ($timestamp['to'] > $shipments[$idx]['shipping']['timestamps'][$ix + 1]['from']) {
                        $day++;
                    }
                }

            }


            $shipments[$idx]['shipping']['timestamps'] = collect($editedTimestamps)->groupBy('group')->toArray();

        }

        $needAddress = $needAddress && in_array($request->current, ['checkout.payment', 'checkout.shipping']);

        if ($needAddress && $address == null) {
            $errors[] = ['key' => 'address', 'type' => 'address', 'message' => sprintf(__('validator.required'), __('address'))];

        }
//group order items by shipment method
        $cart->shipments = collect($shipments)->groupBy('method_id');
        $cart->total_shipping_price = 0;
        $cart->total_items = 0;
        $cart->total_weight = 0;

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
            $errorMessage = null;
            $deliveryDate = null;
            $deliveryTimestamp = null;
            $distance = null;
            foreach ($items as $idx => $item) {
                $cartItem = $item['cart_item'];
                $repoLocation = $item['repo_location'];
                $distance = Util::distance($cart->address['lat'] ?? null, $cart->address['lon'] ?? null, explode(',', $repoLocation)[0] ?? null, explode(',', $repoLocation)[1] ?? null, 'k');
                $product = $cartItem->getRelation('product');
                $totalWeight += $product->weight * $cartItem->qty;
                $totalShippingPrice += ($product->weight * $cartItem->qty * ($item['shipping']['per_weight_price'] ?? 0));
                $basePrice = $basePrice > 0 ? $basePrice : ($item['shipping']['base_price'] ?? 0) + ($distance * ($item['shipping']['per_distance_price'] ?? 0));
                $cart->total_items += $cartItem->qty ?? 0;
                $totalItems += $cartItem->qty ?? 0;
                $repoId = $item['repo_id'];
                $visitChecked = $item['visit_checked'];
                $errorMessage = $item['error_message'] ?? null;
                $hasAvailableShipping = boolval($item['has_available_shipping']);
                $agencyId = $item['agency_id'];
                $deliveryDate = $cartItem->delivery_date;
                $deliveryTimestamp = $cartItem->delivery_timestamp;
                $totalItemsPrice += $cartItem->total_price;
                $totalItemsDiscount += $cartItem->total_discount;

                $shipping = $item['shipping'];
                unset($item['shipping']);
                $items[$idx] = $item;
            }

            if ($totalWeight < ($shipping['min_order_weight'] ?? 0)) {
                $errorMessage = sprintf(__('validator.min_order_weight'), $shipping['min_order_weight'] . ' ' . __('kg'), $totalWeight);
                $shipping['error_message'] = $shipping['error_message'] ?? $errorMessage;
                $errors[] = ['key' => 'min-order-weight', 'type' => 'shipping', 'message' => $errorMessage];
            }

            if (is_numeric($i) && empty($cart->address['lat'] ?? null)) {
                $errorMessage = sprintf(__('validator.required'), __('location'));
                $shipping['error_message'] = $shipping['error_message'] ?? $errorMessage;
                $errors[] = ['key' => 'location', 'type' => 'shipping', 'message' => $errorMessage];
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
                'error_message' => $errorMessage,
                'total_weight' => $totalWeight,
                'total_items' => $totalItems,
                'total_items_price' => $totalItemsPrice,
                'tax_price' => round($totalItemsPrice * $taxPercent / 100),
                'total_items_discount' => $totalItemsDiscount,
                'has_available_shipping' => $hasAvailableShipping,
                'total_shipping_price' => $basePrice + $totalShippingPrice
            ];
            $cart->total_shipping_price += $basePrice + $totalShippingPrice;
            $cart->total_weight += $totalWeight;
        }
        $cart->errors = $errors ?? [];
        $cart->shipments = $shipments;
        $cart->tax_price = round($cart->total_items_price * $taxPercent / 100);
        $cart->total_discount = $cart->total_items_discount;
        $cart->total_price = $cart->total_items_price + $cart->total_shipping_price + $cart->tax_price - $cart->total_discount;
        $cart->need_address = $needAddress;
        $cart->need_self_receive = $needSelfReceive;
        $cart->payment_methods = collect(Variable::getPaymentMethods())->where('active', true)->all();
//        if ($user) {
//            $res = User::getLocation(Variable::$CITIES);
//            $addresses = $user->addresses;
//            $cart->address = $addresses && is_int($addressIdx) && count($addresses) > $idx ? $addresses[$idx] : null;
//        }
//        dd($cart);
//        dd($cart);
//        dd($cartItems->pluck('repo_id'));
//        dd(ShippingMethod::whereIn('repo_id', $cartItems->pluck('repo_id'))->get());
//split orders base repo
        $orders = collect();
        foreach (collect($cart->shipments)->groupBy('method_id') as $methodId => $shipments) {
            $tmpCart = clone $cart;
            $tmpCart->shipping_method_id = str_starts_with($methodId, 'repo-') ? 1 : $methodId; //visit-repo [change id to 1]
            $tmpCart->total_items_discount = 0;
            $tmpCart->tax_price = 0;
            $tmpCart->total_discount = 0;
            $tmpCart->total_items_price = 0;
            $tmpCart->total_shipping_price = 0;
            $tmpCart->total_items = 0;
            $tmpCart->total_price = 0;
            $tmpCart->total_weight = 0;
            $tmpCart->distance = null;
            $tmpShipments = collect();
            foreach ($shipments as $shipment) {
                $tmpCart->delivery_timestamp = $shipment['delivery_timestamp'];
                $tmpCart->delivery_date = $shipment['delivery_date'];
                $tmpCart->repo_id = $shipment['repo_id'];
                $tmpCart->agency_id = $shipment['agency_id'];
                $tmpCart->distance = $shipment['distance'];
                $tmpShipments->add($shipment);
                $tmpCart->total_items_discount += $shipment['total_items_discount'];
                $tmpCart->total_discount += $shipment['total_items_discount'];
                $tmpCart->total_items_price += $shipment['total_items_price'];
                $tmpCart->total_shipping_price += $shipment['total_shipping_price'];
                $tmpCart->tax_price += $shipment['tax_price'];
                $tmpCart->total_weight += $shipment['total_weight'];

                $tmpCart->total_items += $shipment['total_items'];
                $tmpCart->total_price += ($shipment['total_items_price'] + $shipment['total_shipping_price'] + $shipment['tax_price'] - $shipment['total_items_discount']);
            }
            $tmpCart->shipments = $tmpShipments;
            $orders->add(clone $tmpCart);
        }

        $cart->orders = $orders;
        unset ($cart->items);
        unset ($cart->shipments);

        if ($request->cmnd == 'create_order_and_pay')
            return (new OrderController())->create(new OrderRequest(['cart' => $cart]));
        else return response()->json(['message' => __('cart_updated'), 'cart' => $cart], Variable::SUCCESS_STATUS);
    }

    public
    function createCart()
    {

    }
}
