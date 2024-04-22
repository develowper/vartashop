<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\ShippingRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Order;
use App\Models\RepositoryOrder;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShippingController extends Controller
{
    public function update(ShippingRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $orderId = $request->order_id;
        $orderType = $request->order_type;
        $cmnd = $request->cmnd;
        $status = $request->status;
        $admin = $request->user();
        $allowedAgencies = $admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id');
        $data = Shipping::whereIntegerInRaw('agency_id', $allowedAgencies)->find($id);

        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case 'remove-order':
                    $order = $orderType == 'user' ? Order::find($orderId) : RepositoryOrder::find($orderId);

                    if (!$order || $allowedAgencies->where('id', $order->agency_id)->first())
                        return response()->json(['message' => __('item_not_found'),], $errorStatus);

                    if (in_array($order->status, ['delivered', 'canceled', 'refunded', 'rejected']))
                        return response()->json(['message' => __('order_cant_be_remove'), 'status' => $data->status,], $errorStatus);

                    if ($order->status == 'sending') {
                        $order->status = 'ready';
                    }
                    $data->order_qty = $data->order_qty - 1;
                    $order->shipping_id = null;
                    $order->save();

                    //change status to done if no shipping order
                    if (Order::where('shipping_id', $data->id)->where('status', 'sending')->count() == 0
                        && RepositoryOrder::where('shipping_id', $data->id)->where('status', 'sending')->count() == 0) {
                        $data->status = 'done';
                    }

                    $data->save();

                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status, 'statuses' => $data->getAvailableStatuses()], $successStatus);

                case 'status':
                    $availableStatuses = $data->getAvailableStatuses();

                    if (!$availableStatuses->where('name', $status)->first())
                        return response()->json(['message' => __('action_not_allowed'), 'status' => $data->status,], $errorStatus);

                    $request2 = $request;
                    $request2->merge(['for_edit' => true, 'agency_id' => $data->agency_id, 'shipping_id' => $data->id, 'status' => null]);

                    $userOrders = Order::where('shipping_id', $data->id)->get();
                    $agencyOrders = RepositoryOrder::where('shipping_id', $data->id)->get();

                    if ($status == 'sending') {

                        if ($userOrders->where('status', 'ready')->count() == 0 && $agencyOrders->where('status', 'ready')->count() == 0)
                            return response()->json(['message' => __('min_1_ready_order_required'), 'status' => $data->status,], $errorStatus);

                        $unallowedSend = ['request', 'pending', 'processing', 'sending'];

                        if ($userOrders->whereIn('status', $unallowedSend)->count() > 0 || $agencyOrders->whereIn('status', $unallowedSend)->count() > 0)
                            return response()->json(['message' => sprintf(__('orders_*_not_allowed'), collect($unallowedSend)->map(fn($e) => __($e))->join(',')), 'status' => $data->status,], $errorStatus);


                        Order::where('shipping_id', $data->id)->where('status', 'ready')->update(['status' => 'sending']);
                        RepositoryOrder::where('shipping_id', $data->id)->where('status', 'ready')->update(['status' => 'sending']);

                    } elseif ($status == 'preparing') {
                        $deletedCount = 0;
                        foreach ($userOrders->merge($agencyOrders) as $order) {
                            if (in_array($order->status, ['sending', 'ready'])) {
                                $order->status = 'ready';
                                $order->save();
                            }
                        }

                    } elseif ($status == 'canceled') {
                        $deletedCount = 0;
                        foreach ($userOrders->merge($agencyOrders) as $order) {
                            if (in_array($order->status, ['sending', 'ready'])) {
                                $order->status = 'ready';
                            }
                            if (in_array($order->status, ['request', 'pending', 'processing', 'sending', 'ready'])) {
                                $order->shipping_id = null;
                                $deletedCount++;
                                $order->save();
                            }

                        }

                        $data->order_qty = $data->order_qty - $deletedCount;
                    }
                    $data->status = $status;
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status, 'statuses' => $data->getAvailableStatuses(), 'order_qty' => $data->order_qty], $successStatus);
            }
        } elseif ($data) {


            $request->merge([
                'order_qty' => count($request->orders),
            ]);
            $orders = Order::whereIn('id', $request->user_orders)->select('id', 'status', 'shipping_id')->get();
            $agencyOrders = Order::whereIn('id', $request->agency_orders)->select('id', 'status', 'shipping_id')->get();

            $done = true;
            //change status to done if no shipping order
            foreach ($orders->merge($agencyOrders) as $order) {
                $order->shipping_id = $data->id;
                if ($data->status != 'processing' && $order->status == 'ready') {
                    $request->merge([
                        'status' => 'sending',
                    ]);
                    $order->status = 'sending';
                }
                if (in_array($order->status, ['ready', 'sending']))
                    $done = false;

                $order->save();
            }
            if ($done)
                $request->merge([
                    'status' => 'done',
                ]);


            if ($data->update($request->all())) {
                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'shipping_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }

    public function edit(Request $request, $id)
    {

        $data = Shipping::find($id);
        $this->authorize('edit', [Admin::class, $data]);
        $request->merge(['for_edit' => true, 'agency_id' => $data->agency_id, 'shipping_id' => $data->id]);
        $orders = (new OrderController())->searchMerged($request);
        $data->orders = $orders;
        $data->agency = Agency::find($data->agency_id);
        $data->driver = Driver::find($data->driver_id);
        $data->car = Car::find($data->car_id);
        return Inertia::render('Panel/Admin/Shipping/Edit', [
            'data' => $data,
            'order_statuses' => Variable::ORDER_STATUSES,
        ]);
    }

    public function create(ShippingRequest $request)
    {

        $request->merge([
            'status' => 'preparing',
            'order_qty' => count($request->orders),
        ]);

        $data = Shipping::create($request->all());

        if ($data) {
            Order::whereIn('id', $request->user_orders)->update([/*'status' => 'sending', */ 'shipping_id' => $data->id]);
            RepositoryOrder::whereIn('id', $request->agency_orders)->update([/*'status' => 'sending',*/ 'shipping_id' => $data->id]);
            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];
            Telegram::log(null, 'shipping_created', $data);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('admin.panel.shipping.index')->with($res);

    }

    protected
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $isFromAgency = $request->is_from_agency;
        $isToAgency = $request->is_to_agency;
        $query = Shipping::query()->select('*');

        $myAgency = Agency::find($admin->agency_id);

        $agencies = $admin->allowedAgencies($myAgency)->get();
        $agencyIds = $agencies->pluck('id');

        if ($search)
            $query = $query->whereIn('status', collect(Variable::SHIPPING_STATUSES)->filter(fn($e) => str_contains(__($e['name']), $search))->pluck('name'));
        if ($status)
            $query = $query->where('status', $status);
        $query->whereIntegerInRaw('agency_id', $agencyIds);


        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies) {
                    $item->statuses = $item->getAvailableStatuses();
                    $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());
                    $item->setRelation('driver', Driver::find($item->driver_id));
                    $item->setRelation('car', Car::find($item->car_id));

                    return $item;
                }

            );
        });
    }
}
