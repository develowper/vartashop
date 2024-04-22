<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Pay;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\OrderRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Car;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Repository;
use App\Models\RepositoryOrder;
use App\Models\Setting;
use App\Models\Shipping;
use App\Models\ShippingMethod;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserFinancial;
use App\Models\Variation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;

class OrderController extends Controller
{

    public function factor(Request $request, $id)
    {
        $user = $request->user();

        $data = Order::with('items')->find($id);

        $this->authorize('edit', [get_class($user), $data]);

        $agency = Agency::find($data->agency_id);

        if ($agency && !$agency->address)
            $agency->address = optional(Agency::find($agency->parent_id))->address;
        if (!$agency)
            Agency::find(1);
        if (($user instanceof Admin) && !$user->allowedAgencies(Agency::find($user->agency_id))->where('id', $data->agency_id)->exists())
            return response()->json(['message' => __('order_not_found'),], Variable::ERROR_STATUS);

        $data->order_id = "$data->id";
        $data->shipping_method = ShippingMethod::select('id', 'name', 'description')->find($data->shipping_method_id);
        $data->transaction = Transaction::where([
            'for_type' => 'order',
            'for_id' => $data->id,
            'type' => 'pay'
        ])->whereNotNull('payed_at')->select('title', 'pay_id', 'payed_at', 'pay_gate')->first();
        $data->from = $agency;
        $data->to = (object)[
            'name' => $data->receiver_fullname,
            'phone' => $data->receiver_phone,
            'province_id' => $data->province_id,
            'county_id' => $data->province_id,
            'district_id' => $data->province_id,
            'postal_code' => $data->postal_code,
            'address' => $data->address,
        ];
        return Inertia::render('Panel/Order/Factor', [
            'statuses' => Variable::STATUSES,
            'data' => $data,
            'error_message' => __('order_not_found'),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $user = $request->user();

        $data = Order::with('items')->find($id);

        $this->authorize('edit', [get_class($user), $data]);

        $data->repository = Repository::with('shippingMethods')->find($data->repo_id);
        if ($data->repository && $data->repository->allow_visit) {
            $methods = $data->repository->getRelation('shippingMethods');
            $methods->prepend(ShippingMethod::find(1));
            $data->repository->setRelation('shippingMethods', $methods);
        }
        $data->delivery_date = $data->delivery_date ? Jalalian::fromDateTime($data->delivery_date)->format('Y/m/d') : null;
        $items = $data->getRelation('items');
        foreach ($items as $item) {
            $item->id = $item->variation_id;
            $item->qty = floatval($item->qty);
            $item->price = $item->total_price / ($item->qty ?? 1);
            $item->weight = $item->total_weight / ($item->qty ?? 1);
        }
        $data->setRelation('products', $items);
        return Inertia::render('Panel/' . ($user instanceof Admin ? 'Admin/Order/User' : 'Order') . '/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function userUpdate(Request $request)
    {
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $user = $request->user();
        $data = Order::find($request->id);
        if (!$data || $data->user_id != $user->id)
            return response()->json(['message' => __('order_not_found'), 'status' => $data->status,], $errorStatus);

        switch ($request->cmnd) {
            case 'pay':
                if ($data->status != 'pending')
                    return response()->json(['message' => __('order_not_in_pay_status'), 'status' => $data->status,], $errorStatus);

                $description = sprintf(__('pay_orders_*_*'), $data->id, $user->phone);

                $response = Pay::makeUri(Carbon::now()->getTimestampMs(), "{$data->total_price}0", $user->fullname, $user->phone, $user->email, $description, $user->id, Variable::$BANK);

                $t = Transaction::where('for_type', 'order')
                    ->where('for_id', $data->id)
                    ->where('from_type', 'user')
                    ->where('from_id', $user->id);
                if ($t) $t->update(['pay_id' => $response['order_id'], 'amount' => $data->total_price,]);
                if (!$t) {
                    $t = Transaction::create([
                        'title' => sprintf(__('pay_orders_*_*'), $data->id, $user->phone),
                        'type' => "pay",
                        'pay_gate' => Variable::$BANK,
                        'for_type' => 'order',
                        'for_id' => $data->id,
                        'from_type' => 'user',
                        'from_id' => $user->id,
                        'to_type' => 'agency',
                        'to_id' => 1,
                        'info' => null,
                        'coupon' => null,
                        'payed_at' => null,
                        'amount' => $data->total_price,
                        'pay_id' => $response['order_id'],
                    ]);
                }
                return response(['status' => $data->status, 'message' => __('redirect_to_payment_page'), 'url' => $response['url']], Variable::SUCCESS_STATUS);

                break;
        }
    }

    public function update(OrderRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $status = $request->status;
        $user = $request->user();

        $data = $request->data ?? Order::find($id);

        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case 'status':
                    $availableStatuses = $data->getAvailableStatuses();

                    if (!$availableStatuses->where('name', $status)->first())
                        return response()->json(['message' => __('action_not_allowed'), 'status' => $data->status,], $errorStatus);

                    $shipping = Shipping::find($data->shipping_id);
                    if ($data->shipping_id && !$shipping)
                        return response()->json(['message' => __('shipping_not_found'), 'status' => $data->status,], $errorStatus);

                    if ($data->shipping_id)
                        if ($data->status == 'sending' && !in_array($status, ['canceled', 'refunded', 'rejected', 'delivered']))
                            return response()->json(['message' => __('shipping_orders_cant_be_edited'), 'status' => $data->status,], $errorStatus);

                    if ($status == 'delivered') {

                        $data->done_at = Carbon::now();
                        if ($shipping)
                            $shipping->order_delivered_qty++;

                        Transaction::splitProfits($data, $shipping);

                        //add to products sells
                        foreach ($data->items()->get() as $item) {
                            $product = Product::find(Variation::find($item->variation_id)->product_id ?? 0);
                            if ($product) {
                                $product->order_count += 1;
                                $product->save();
                            }
                        }
                    }

                    $data->status = $status;

                    if ($status == 'refunded' || $status == 'rejected' || $status == 'canceled') {

//                        $data->status = 'canceled';
                        $data->status = $status;
                        if ($status == 'refunded' || $status == 'rejected') {
                            $userF = UserFinancial::firstOrNew(['user_id' => $data->user_id]);
                            if (!$data->user_id)
                                return response()->json(['message' => __('user_not_found'), 'status' => $data->status,], $errorStatus);
                            $userF->wallet += $data->total_price;
                            $userF->save();
                        }
                        //return order to repo

                        foreach ($data->items()->get() as $item) {
                            $variation = Variation::find($item->variation_id);
                            if ($variation) {
                                $variation->in_repo += $item->qty;
                                $variation->save();
                            }
                        }


                    }
                    $data->save();

                    //change status to done if no shipping order

                    if ($shipping) {
                        if (Order::where('shipping_id', $shipping->id)->where('status', 'sending')->count() == 0
                            && RepositoryOrder::where('shipping_id', $shipping->id)->where('status', 'sending')->count() == 0) {
                            $shipping->status = 'done';
                        }
                        $shipping->save();
                    }
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status, 'statuses' => $data->getAvailableStatuses()], $successStatus);


            }
        } elseif ($data) {


            $request->validate(
                [
                    'new_in_repo' => in_array($cmnd, ['change-repo', 'change-grade-pack-weight']) ? [Rule::requiredIf(in_array($cmnd, ['change-repo', 'change-grade-pack-weight'])), 'numeric', "max:$data->in_repo", 'min:0'] : [],
                ],
                [
                    'agency_id.required' => __('access_denied'),
                    'agency_id.in' => __('access_denied'),

                    'new_in_repo.required' => sprintf(__('validator.required'), __('get_from_repo')),
                    'new_in_repo.min' => sprintf(__('validator.min_items'), __('get_from_repo'), 0, $request->new_in_repo),
                    'new_in_repo.max' => sprintf(__('validator.max_items'), __('get_from_repo'), floatval($data->in_repo), $request->new_in_repo),

                ],
            );

            $request->merge([
//                'cities' => json_encode($request->cities ?? [])
            ]);
            $beforeItems = $data->getRelation('items');
            $databaseProducts = $request->database_products;

            //delete removed order items
            $returnItemsToShop = OrderItem::where('order_id', $data->id)->whereNotIn('variation_id', array_column($request->products, 'id'))->get();
            foreach ($returnItemsToShop as $item) {
                $variation = $databaseProducts->where('id', $item->variation_id)->first();
                if ($variation) {
                    $variation->in_shop += $item->qty;
                    $variation->save();
                }
                OrderItem::whereId($item->id)->delete();
            }
            foreach ($request->products as $p) {
                if ($p['qty'] == 0) {
                    OrderItem::where('variation_id', $p['id'])->delete();
                    continue;
                }
                $beforeItem = $beforeItems->where('variation_id', $p['id'])->first();
                $product = $databaseProducts->where('id', $p['id'])->first();
                if ($beforeItem) {
                    $dif = $beforeItem->qty - $p['qty'];


                    $beforeItem->qty = $p['qty'];
                    $beforeItem->total_price = $p['total_price'];

                    $beforeItem->save();
                    $item = $beforeItem;
                } else {
                    $dif = -$p['qty'];
                    DB::table('order_items')->insert([
                        'title' => "$product->name ( " . floatval($p['qty']) . " " . optional(Pack::find($product->pack_id))->name . " " . __('grade') . " $product->grade " . floatval($product->weight) . " " . __('kg') . " )",
                        'name' => $product->name,
                        'order_id' => $data->id,
                        'variation_id' => $product->id,
                        'qty' => $p['qty'],
                        'pack_id' => $product->pack_id,
                        'total_weight' => $product->weight * $p['qty'],
                        'grade' => $product->grade,
                        'repo_id' => $data->repo_id,
                        'total_price' => $p['total_price'],
                        'discount_price' => $p['discount_price'],
                        'created_at' => Carbon::now(),
                    ]);
                    $item = (object)['variation_id' => $product->id];
                }
                if ($dif != 0) {
                    $variation = $databaseProducts->where('id', $item->variation_id)->first();

                    if ($variation) {
                        $variation->in_shop += $dif;
                        $variation->save();
                    }

                }
            }


            $order = $data;
            $repository = $data->repository;
            unset($data->repository);
            if ($data->update($request->except(['repository', 'database_products', 'data', 'agency_id', 'products', 'allowed_agencies']))) {
                $items = OrderItem::where('order_id', $data->id)->get();

                $order->setRelation('items', $items);
                foreach ($items as $item) {
                    $item->id = $item->variation_id;
                    $item->qty = floatval($item->qty);
                    $item->price = $item->qty ? $item->total_price / $item->qty : 0;
                    $item->weight = $item->qty ? $item->total_weight / $item->qty : 0;
                }
                $order->setRelation('products', $items);
                $order->setRelation('repository', $repository);

                Telegram::log(null, 'order_edited', $order);
                $order->delivery_date = $request->delivery_date_shamsi;
//                dd($request->all());
                return response()->json(['message' => __('updated_successfully'), 'order' => $order], $successStatus);
            } else
                return response()->json($response, $errorStatus);
        }

        return response()->json($response, $errorStatus);
    }

    public function create(OrderRequest $request)
    {

//        $cart = (new CartController())->update($request);
//        $cart = $cart ? $cart->getData() : null;
//        $cart = optional($cart)->cart ?? null;
        $user = auth('sanctum')->user();
        $cart = $request->cart;
        if (!$cart) {
            return response()->json(['message' => __('problem_in_create_order'), 'cart' => $cart], Variable::ERROR_STATUS);
        }

        if ($cart->errors) {
            return response()->json(['message' => __('please_correct_errors'), 'cart' => $cart], Variable::ERROR_STATUS);

        }
        if (count($cart->orders) == 0) {
            return response()->json(['message' => __('cart_is_empty'), 'cart' => $cart], Variable::ERROR_STATUS);
        }

//split cart to each repo
        $orders = collect([]);
        //create order for each repo
        foreach ($cart->orders as $cart) {

            $order = Order::create([
                'user_id' => $user->id,
                'province_id' => $cart->address['province_id'] ?? null,
                'county_id' => $cart->address['county_id'] ?? null,
                'district_id' => $cart->address['district_id'] ?? null,
                'receiver_fullname' => $cart->address['receiver_fullname'] ?? null,
                'receiver_phone' => $cart->address['receiver_phone'] ?? $user->phone ?? null,
                'postal_code' => $cart->address['postal_code'] ?? null,
                'address' => $cart->address['address'] ?? null,
                'location' => ($cart->address['lat'] ?? false) && ($cart->address['lon'] ?? false) ? ($cart->address['lat'] . "," . $cart->address['lon']) : null,
                'status' => 'pending',
                'repo_id' => $cart->repo_id,
                'agency_id' => $cart->agency_id,
                'total_shipping_price' => $cart->total_shipping_price,
                'total_items_price' => $cart->total_items_price,
                'total_items' => $cart->total_items,
                'total_discount' => $cart->total_discount,
                'total_price' => $cart->total_price,
                'shipping_method_id' => $cart->shipping_method_id,
                'shipping_id' => null,
                'delivery_date' => $cart->delivery_date,
                'delivery_timestamp' => $cart->delivery_timestamp,
                'distance' => $cart->distance,
                'tax_price' => $cart->tax_price,
                'total_weight' => $cart->total_weight,

            ]);
            if ($order) {

                $orders->add(['id' => $order->id,
                    'total_items_price' => $cart->total_items_price,
                    'total_price' => $cart->total_price,
                    'user_id' => $cart->user_id,
                    'agency_id' => $cart->agency_id]);

                $items = [];
                $shippings = [];
                foreach ($cart->shipments as $shipment) {
                    foreach ($shipment['items'] as $item) {
                        $cartItem = $item ['cart_item'];
                        $methodId = $item ['method_id'];
                        $product = Variation::find($cartItem->variation_id);
                        if (!$product)
                            return response()->json(['message' => __('problem_in_create_order')], Variable::ERROR_STATUS);
                        $product->in_shop -= $cartItem->qty;
                        $product->save();
                        if (str_starts_with($methodId, 'repo-')) //visit-repo [change id to 1]
                            $methodId = 1;
                        $items[] = [
                            'title' => "$product->name ( " . floatval($cartItem->qty) . " " . optional(Pack::find($product->pack_id))->name . " " . __('grade') . " $product->grade " . floatval($product->weight) . " " . __('kg') . " )",
                            'name' => $cartItem->name,
                            'order_id' => $order->id,
                            'variation_id' => $cartItem->variation_id,
                            'qty' => $cartItem->qty,
                            'pack_id' => $product->pack_id,
                            'total_weight' => $product->weight * $cartItem->qty,
                            'grade' => $product->grade,
                            'repo_id' => $cartItem->repo_id,
                            'total_price' => $cartItem->total_price ?? 0,
                            'discount_price' => $cartItem->discount_price ?? 0,
                            'created_at' => Carbon::now(),

                        ];
                    }

                }
                if (!DB::table('order_items')->insert($items))
                    return response()->json(['message' => __('problem_in_create_order')], Variable::ERROR_STATUS);

                if (CartItem::where('cart_id', $cart->id)->delete())
                    Cart::find($cart->id)->delete();

                $orderLog = $order;

                $orderLog->setRelation('items', collect($items)->map(fn($e) => (object)$e));

                Telegram::log(null, 'order_created', $orderLog);
            }

        }
//        $order_id = Carbon::now()->getTimestampMs();
        $order_ids_string = $orders->pluck('id')->join('-');
        $price = $orders->sum('total_price');

        $description = sprintf(__('pay_orders_*_*'), $order_ids_string, $user->phone);

        $response = Pay::makeUri($order_ids_string, "{$price}0", $user->fullname, $user->phone, $user->email, $description, optional($user)->id, Variable::$BANK);

        if ($response['status'] != 'success')
            return response()->json(['status' => 'danger', 'message' => $response['message']], Variable::ERROR_STATUS);

        else { //success
            foreach ($orders as $o) {

                $t = Transaction::create([
                    'title' => sprintf(__('pay_orders_*_*'), $o['id'], $user->phone),
                    'type' => "pay",
                    'pay_gate' => Variable::$BANK,
                    'for_type' => 'order',
                    'for_id' => $o['id'],
                    'from_type' => 'user',
                    'from_id' => $o['user_id'],
                    'to_type' => 'agency',
                    'to_id' => 1,
                    'info' => null,
                    'coupon' => null,
                    'payed_at' => null,
                    'amount' => $o['total_price'],
                    'pay_id' => $response['order_id'],
                ]);
                Order::where('id', $o['id'])->update(['transaction_id' => $t->id]);

            }
            return response(['status' => 'success', 'message' => __('redirect_to_payment_page'), 'url' => $response['url']], Variable::SUCCESS_STATUS);
        }
    }

    protected
    function searchPanel(Request $request)
    {
        $userAdmin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $isFromAgency = $request->is_from_agency;
        $isToAgency = $request->is_to_agency;
        $query = Order::query()->select('*');
        $agencies = [];
        if ($userAdmin instanceof Admin) {
            $myAgency = Agency::find($userAdmin->agency_id);
            $agencies = $userAdmin->allowedAgencies($myAgency)->get();
            $agencyIds = $agencies->pluck('id');
        }
        if ($search)
            $query = $query->whereIn('status', collect(Variable::ORDER_STATUSES)->filter(fn($e) => str_contains(__($e['name']), $search))->pluck('name'));
        if ($status)
            $query = $query->where('status', $status);

        if ($userAdmin instanceof Admin)
            $query->whereIntegerInRaw('agency_id', $agencyIds);
        if ($userAdmin instanceof User)
            $query->where('user_id', $userAdmin->id)->with('agency:id,name,phone');

//        $query->with('items.variation:id,name,weight,pack_id');
        $query->with('items');

        $timeout = Setting::getValue('order_reserve_minutes') ?? 0;
        $now = Carbon::now();
        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies, $userAdmin, $timeout, $now) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies, $userAdmin, $timeout, $now) {

                    if ($userAdmin instanceof Admin) {
                        $item->statuses = $item->getAvailableStatuses();
                        $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());
                    }
                    if ($timeout && $item->status == 'pending')
                        $item->pay_timeout = ($t = $now->diffInMinutes($item->created_at->addMinutes($timeout), false)) > 0 ? "$t " . __('minute') : null;

                    return $item;
                }

            );
        });
    }

    public
    function searchMerged(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by && $request->order_by != 'items' ? $request->order_by : 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $repoId = $request->repo_id;
        $notIn1 = $request->before_selected_user_orders;
        $notIn2 = $request->before_selected_agency_orders;
        $agencyId = $request->agency_id;
        $shippingId = $request->shipping_id;

        $query1 = Order::query()->select(
            'id',
            'agency_id AS from_agency_id',
            'repo_id AS from_repo_id',
            'user_id AS to_user_id',
            'province_id AS to_province_id',
            'county_id AS to_county_id',
            'district_id AS to_district_id',
            'receiver_fullname AS to_fullname',
            'receiver_phone AS to_phone',
            'postal_code AS to_postal_code',
            'address AS to_address',
            'location AS to_location',
            'status',
            'total_discount',
            'total_items',
            'total_price',
            'total_items_price',
            'total_shipping_price',
            'created_at',
            'updated_at',
            'done_at',
            'delivery_date',
            'delivery_timestamp',
            'shipping_id',
            'shipping_method_id',
            DB::raw('NULL AS to_admin_id'),
            DB::raw('NULL AS to_repo_id'),
            DB::raw('NULL AS to_agency_id'),
            DB::raw('NULL AS from_province_id'),
            DB::raw('NULL AS from_county_id'),
            DB::raw('NULL AS from_district_id'),
            DB::raw('NULL AS from_fullname'),
            DB::raw('NULL AS from_phone'),
            DB::raw('NULL AS from_location'),
            DB::raw('NULL AS from_postal_code'),
            DB::raw('NULL AS from_address'),
            DB::raw('"user" AS type'),

        );
        $query2 = RepositoryOrder::query()->select(
            'id',
            'from_agency_id',
            'from_repo_id',
            'to_admin_id AS to_user_id',
            'to_province_id',
            'to_county_id',
            'to_district_id',
            'to_fullname',
            'to_phone',
            'to_postal_code',
            'to_address',
            'to_location',
            'status',
            'total_discount',
            'total_items',
            'total_price',
            'total_items_price',
            'total_shipping_price',
            'created_at',
            'updated_at',
            'done_at',
            'delivery_date',
            'delivery_timestamp',
            'to_admin_id',
            'to_repo_id',
            'to_agency_id',
            'from_province_id',
            'from_county_id',
            'from_district_id',
            'from_fullname',
            'from_phone',
            'from_location',
            'from_postal_code',
            'from_address',
            'shipping_id',
            'shipping_method_id',
            DB::raw('"agency" AS type'),

        );

        $myAgency = Agency::find($admin->agency_id);

        $agencyIds = $admin->allowedAgencies($myAgency)->pluck('id');

        if ($search) {
            $query1->whereIn('status', collect(Variable::ORDER_STATUSES)->filter(fn($e) => str_contains(__($e['name']), $search))->pluck('name'));
            $query2->whereIn('status', collect(Variable::ORDER_STATUSES)->filter(fn($e) => str_contains(__($e['name']), $search))->pluck('name'));
        }
        if ($status) {
            $query1->where('status', $status);
            $query2->where('status', $status);
        }
        if ($repoId) {
            $query1->where('repo_id', $repoId);
            $query2->where('from_repo_id', $repoId);
        }
        if ($notIn1)
            $query1->whereNotIn('id', $notIn1);
        if ($notIn2)
            $query2->whereNotIn('id', $notIn2);

        $query1->whereIntegerInRaw('agency_id', $agencyIds);
        $query2->whereIntegerInRaw('from_agency_id', $agencyIds);

        if ($agencyId) {
            $query1->where('agency_id', $agencyId);
            $query2->where('from_agency_id', $agencyId);
        }
        if ($shippingId) {
            $query1->where('shipping_id', $shippingId);
            $query2->where('shipping_id', $shippingId);
        }
        $query1->with('items');
        $query2->with('items');


        $res = $query1->union($query2)->orderBy($orderBy, $dir);

        if ($request->for_edit)
            return $res->get()->map(function ($e) {
                $e->statuses = $e->getAvailableStatuses();
                return $e;
            });
        return tap($res->paginate($paginate, ['*'], 'page', $page), function ($paginated) {
            return $paginated->getCollection()->transform(
                function ($item) {
                    $item->statuses = $item->getAvailableStatuses();
                    return $item;
                }

            );
        });
    }

}
