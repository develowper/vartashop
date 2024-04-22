<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\DriverRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Driver;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DriverController extends Controller
{
    public function edit(Request $request, $id)
    {

        $data = Driver::with('agency')->find($id);

        $this->authorize('edit', [Admin::class, $data]);

        return Inertia::render('Panel/Admin/Shipping/Driver/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function create(DriverRequest $request)
    {
        if (!$request->uploading) { //send again for uploading images
            return back()->with(['resume' => true]);
        }
//        $request->merge([
//            'status' => 'active',
//        ]);
        $data = Driver::create($request->all());

        if ($data) {
            if ($request->img)
                Util::createImage($request->img, Variable::IMAGE_FOLDERS[Driver::class], $data->id);

            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];
            Telegram::log(null, 'driver_created', $data);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('admin.panel.shipping.driver.index')->with($res);

    }

    protected
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $orderBy = $orderBy == 'agency' ? 'agency_id' : $orderBy;
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $agencyId = $request->agency_id;

        $query = Driver::query()->select('*');

        $myAgency = Agency::find($admin->agency_id);

        $agencies = $admin->allowedAgencies($myAgency)->select('id', 'name')->get();
        $query->whereIntegerInRaw('agency_id', $agencies->pluck('id'));
        if ($search)
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('fullname', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        if ($agencyId)
            $query->where('agency_id', $agencyId);

        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies) {
                    $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());


                    return $item;
                }

            );
        });


    }

    public function update(DriverRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Driver::find($id);
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case  'upload-img' :

                    if (!$request->img) //  add extra image
                        return response()->json(['errors' => [__('file_not_exists')], 422]);
                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Driver::class], $id);
                    return response()->json(['message' => __('updated_successfully')], $successStatus);


            }
        } elseif ($data) {


            $request->merge([
//                'cities' => json_encode($request->cities ?? [])
            ]);


            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'driver_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }
}
