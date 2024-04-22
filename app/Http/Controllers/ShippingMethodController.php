<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\ShippingMethodRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Variation;
use App\Models\Repository;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShippingMethodController extends Controller
{


    public function edit(Request $request, $id)
    {

        $data = ShippingMethod::with('repository:id,name')->with('shippingAgency:id,name')->find($id);

        $this->authorize('edit', [Admin::class, $data]);

        $data->products = Variation::whereIntegerInRaw('id', $data->products ?? [])->select('id', 'name', 'pack_id', 'grade', 'weight')->get();

        return Inertia::render('Panel/Admin/Shipping/Method/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function create(ShippingMethodRequest $request)
    {
        $admin = $request->user();
        $request->merge([

            'status' => 'active',
            'shipping_agency_id' => $admin->hasAccess('edit_setting') ? $request->shipping_agency_id : null,
        ]);
        $data = ShippingMethod::create($request->all());

        if ($data) {


            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];

//            SMSHelper::deleteCode($phone);
            Telegram::log(null, 'shipping-method_created', $data);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('admin.panel.shipping-method.index')->with($res);

    }

    protected
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by && $request->order_by != 'agency' ? $request->order_by : 'agency_id';

        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;

        $query = ShippingMethod::query()->select();

        $myAgency = Agency::find($admin->agency_id);

        $agencies = $admin->allowedAgencies($myAgency)->select('id', 'name')->get();
        $query->whereIntegerInRaw('agency_id', $agencies->pluck('id'));
        if ($search)
            $query = $query->where('name', 'like', "%$search%");
        if ($status)
            $query = $query->where('status', $status);

        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies) {
                    $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());
                    return $item;
                }
            );
        });


    }

    public function update(ShippingMethodRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = $request->data;
        $admin = $request->user();
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case 'inactive':
                    $data->status = 'inactive';
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status,], $successStatus);

                case 'activate':
                    $data->status = 'active';
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status,], $successStatus);


            }
        } elseif ($data) {


            $request->merge([
//                'cities' => json_encode($request->cities ?? [])
                'shipping_agency_id' => $admin->hasAccess('edit_setting') ? $request->shipping_agency_id : $data->shipping_agency_id,

            ]);


            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'shipping-method_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }
}
