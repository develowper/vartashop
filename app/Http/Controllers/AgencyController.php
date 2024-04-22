<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\AgencyRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\AgencyFinancial;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgencyController extends Controller
{

    public function edit(Request $request, $id)
    {

        $data = Agency::with('owner:id,fullname,phone,agency_id')->with('financial')->find($id);
        $this->authorize('edit', [Admin::class, $data]);

        $data->type_id = $data->level;
        $data->supported_provinces = $data->level == '1' ? $data->access : [];

        return Inertia::render('Panel/Admin/Agency/Edit', [
            'statuses' => Variable::STATUSES,
            'data' => $data,
            'parent_agencies' => Agency::whereNot('level', Variable::AGENCY_TYPES[count(Variable::AGENCY_TYPES) - 1]['level'])->whereNotNull('level')->select('id', 'name', 'province_id', 'level', 'access')->get(),

        ]);
    }

    public function update(AgencyRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $admin = $request->user();
        $data = Agency::find($id);
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
                'level' => strval($request->type_id),
                'access' => $request->type_id == 1 && $request->supported_provinces ? $request->supported_provinces : ($data->access ?? []),

            ]);
            if ($request->level != $data->level) {
                Admin::where('agency_id', $data->id)->update(['agency_level' => strval($request->level)]);
            }
            if (!$admin->hasAccess('edit_setting'))
                $request->merge([
                    'order_profit_percent' => $data->order_profit_percent,
                ]);
            //change parent if changed
            if ($data->level == '3' && $data->parent_id != $request->parent_id) {
                //add branch to parent access
                $oldParentAgency = Agency::find($data->parent_id);
                $newParentAgency = Agency::find($request->parent_id);
                if ($newParentAgency && $newParentAgency->level == '2') {
                    $access = $newParentAgency->access ?? [];
                    if (!in_array($data->id, $access)) {
                        $access[] = $data->id;
                        $newParentAgency->access = $access;
                        $newParentAgency->save();
                    }
                }
                if ($oldParentAgency && $oldParentAgency->level == '2') {
                    $access = $oldParentAgency->access ?? [];
                    if (in_array($data->id, $access)) {
                        $pos = array_search($data->id, $access);
                        if ($pos !== false)
                            unset($access[$pos]);
                        $oldParentAgency->access = $access;
                        $oldParentAgency->save();
                    }
                }
            }

//            $data->name = $request->tags;
//            $data->tags = $request->tags;
//            dd($request->tags);
            if ($data->update($request->all())) {

                AgencyFinancial::updateOrCreate(['agency_id' => $data->id,],
                    [
                        'card' => $request->card,
                        'sheba' => $request->sheba,
                    ]);

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'agency_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }

    protected function create(AgencyRequest $request)
    {
        $admin = $request->user();


        $request->merge([
            'order_profit_percent' => !$admin->hasAccess('edit_setting') ? null : $request->order_profit_percent,
            'status' => 'active',
            'level' => strval($request->type_id),
            'access' => $request->type_id == 1 && $request->supported_provinces ? $request->supported_provinces : [],
            'created_at' => Carbon::now(),
        ]);

        $data = Agency::create($request->all());

        if ($data) {

            AgencyFinancial::create([
                'agency_id' => $data->id,
                'card' => $request->card,
                'sheba' => $request->sheba,
                'wallet' => 0,
            ]);

            if ($data->level == '3') {
                //add branch to parent access
                $parentAgency = Agency::find($data->parent_id);
                if ($parentAgency && $parentAgency->level == '2') {
                    $access = $parentAgency->access ?? [];
                    if (!in_array($data->id, $access)) {
                        $access[] = $data->id;
                        $parentAgency->access = $access;
                        $parentAgency->save();
                    }
                }
            }

            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];
            $cities = City::whereIn('id', [$data->province_id, $data->county_id, $data->district_id])->get();
            $data->province = $cities->where('id', $data->province_id)->first()->name ?? '';
            $data->county = $cities->where('id', $data->county_id)->first()->name ?? '';
            $data->district = $cities->where('id', $data->district_id)->first()->name ?? '';
//            SmsHelper::deleteCode($phone);
            Telegram::log(null, 'agency_created', $data);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route('admin.panel.agency.index')->with($res);

    }

    public
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;

        $myAgency = Agency::find($admin->agency_id);
        $query = $admin->allowedAgencies($myAgency);

        if ($search)
            $query = $query->where('name', 'like', "%$search%");

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);
    }


}
