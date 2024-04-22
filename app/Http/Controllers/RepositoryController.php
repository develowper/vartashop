<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\RepositoryRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\City;
use App\Models\Repository;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RepositoryController extends Controller
{

    public function edit(Request $request, $id)
    {

        $data = Repository::with('agency:id,name,level')->with('admin:id,fullname,phone')->find($id);
        $this->authorize('edit', [Admin::class, $data]);

        return Inertia::render('Panel/Admin/Repository/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,

        ]);
    }

    public function update(RepositoryRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Repository::find($id);
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

            //add county parents for level3
            $withParentLevel3 = City::whereIn('id', $request->cities ?? [])->where('level', 3)->distinct('parent_id')->pluck('parent_id')->merge($request->cities ?? [])->toArray();

            $request->merge([
                'cities' => $withParentLevel3
            ]);


            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'repository_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }

    protected function create(RepositoryRequest $request)
    {
//        $user = $request->user();

        //add county parents for level3
        $withParentLevel3 = City::whereIn('id', $request->cities ?? [])->where('level', 3)->distinct('parent_id')->pluck('parent_id')->merge($request->cities ?? [])->toArray();
        $request->merge([
            'cities' => $withParentLevel3,
            'status' => 'active',

        ]);


        $data = Repository::create($request->all());

        if ($data) {


            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];

//            SMSHelper::deleteCode($phone);
            Telegram::log(null, 'repository_created', $data);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('admin.panel.repository.index')->with($res);

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
        $isShop = $request->is_shop;
        $with = $request->with;
        $visitRepoMethod = null;
        $query = Repository::query()->select('id', 'address', 'name', 'phone', 'is_shop', 'status', 'allow_visit', 'agency_id', 'postal_code', 'province_id', 'county_id', 'district_id');

        $myAgency = Agency::find($admin->agency_id);

        $agencies = $admin->allowedAgencies($myAgency)->select('id', 'name')->get();

        $query->whereIntegerInRaw('agency_id', $agencies->pluck('id'));
        if ($search)
            $query = $query->where('name', 'like', "%$search%");
        if ($status)
            $query = $query->where('status', $status);
        if ($isShop)
            $query = $query->where('is_shop', $isShop);
        if ($with == 'shipping_methods') {
            $query = $query->with('shippingMethods');
            $visitRepoMethod = ShippingMethod::find(1);
        }
        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies, $with, $visitRepoMethod) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies, $with, $visitRepoMethod) {
                    $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());

                    if ($with == 'shipping_methods' && $item->allow_visit) {
                        $methods = $item->getRelation('shippingMethods');
                        $methods->add($visitRepoMethod);
                        $item->setRelation('shippingMethods', $methods);
                    }

                    return $item;
                }

            );
        });


    }
}
