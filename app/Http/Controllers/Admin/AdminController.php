<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\SMSHelper;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\UserRequest;
use App\Models\Admin;
use App\Models\AdminFinancial;
use App\Models\Agency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{

    public function update(AdminRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = $request->data;
        $status = $request->status;
        $role = $request->role;
        $admin = $request->user();
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case 'status':
                    $data->status = $status;
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status,], $successStatus);

                case 'role':
                    $data->role = $role;
                    $data->save();
                    return response()->json(['message' => __('updated_successfully'), 'status' => $data->status,], $successStatus);

                case  'upload-img' :

                    if (!$request->img) //  add extra image
                        return response()->json(['errors' => [__('file_not_exists')], 422]);
                    Util::createImage($request->img, Variable::IMAGE_FOLDERS[Admin::class], $id);
                    return response()->json(['message' => __('updated_successfully')], $successStatus);


            }
        } elseif ($data) {


            if ($request->password) {
                $request->merge([
                    'password' => Hash::make($request->password),
                ]);
            }
            if ($data->update($request->except($request->password ? [] : ['password']))) {

                AdminFinancial::updateOrCreate(['admin_id' => $data->id,],
                    [
//                        'agency_id' => $data->agency_id,
                        'card' => $request->card,
                        'sheba' => $request->sheba,
                    ]);

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'admin_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }

    public function create(AdminRequest $request)
    {

        $request->merge([
            'ref_id' => Admin::makeRefCode($request->phone),
            'access' => [],
            'phone_verified' => true,
            'password' => Hash::make($request->password),

        ]);
        $admin = Admin::create($request->all());
        if ($admin) {
            AdminFinancial::create([
                'admin_id' => $admin->id,
//                'agency_id' => $admin->agency_id,
                'card' => $request->card,
                'sheba' => $request->sheba,
                'wallet' => 0,
            ]);
            if ($request->img)
                Util::createImage($request->img, Variable::IMAGE_FOLDERS[Admin::class], $admin->id);
            $res = ['flash_status' => 'success', 'flash_message' => __('done_successfully')];
            Telegram::log(null, 'admin_created', $admin);
            SMSHelper::deleteCode($request->phone);
            return to_route('admin.panel.admin.index')->with($res);

        }
        $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return back()->with($res);

    }

    public function new(Request $request)
    {

        return Inertia::render('Panel/Admin/Admin/Create', [
        ]);
    }

    public function edit(Request $request, $id): Response
    {
        $data = Admin::with('financial')->with('agency')->find($id);
        $this->authorize('edit', [Admin::class, $data]);

        return Inertia::render('Panel/Admin/Admin/Edit', [
            'data' => $data,
            'roles' => array_values(array_filter(Variable::ADMIN_ROLES, fn($e) => $e != 'god')),
            'statuses' => collect(Variable::USER_STATUSES)->pluck('name'),

        ]);
    }

    public
    function searchPanel(Request $request)
    {
        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by && $request->order_by != 'agency' ? $request->order_by : 'agency_id';

        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $query = Admin::query()->select('*');

        $myAgency = Agency::find($admin->agency_id);

        $agencies = $admin->allowedAgencies($myAgency)->get();
        $agencyIds = $agencies->pluck('id');

        if ($search)
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('fullname', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        if ($status)
            $query = $query->where('status', $status);

        $query->whereIntegerInRaw('agency_id', $agencyIds);


        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies) {
                    $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());

                    return $item;
                }

            );
        });
    }
}
