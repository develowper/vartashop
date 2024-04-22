<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\FinancialRequest;
use App\Models\Admin;
use App\Models\AdminFinancial;
use App\Models\Agency;
use App\Models\AgencyFinancial;
use App\Models\Order;
use App\Models\RepositoryOrder;
use App\Models\Transaction;
use App\Models\UserFinancial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class  FinancialController extends Controller
{
    public
    function searchMerged(Request $request)
    {


        $admin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $type = $request->type;


        $query1 = UserFinancial::query()->join('users', function ($join) {
            $join->on('users.id', '=', 'user_financials.user_id');
        })
            ->select(
                'user_financials.wallet As wallet',
                'user_financials.card As card',
                'user_financials.sheba As sheba',
                DB::raw('NULL as agency_id'),
                'users.id As id',
                'users.fullname As name',

                DB::raw('"user" as type'),
            );

        $query2 = AdminFinancial::query()->join('admins', function ($join) {
            $join->on('admins.id', '=', 'admin_financials.admin_id');
        })
            ->select(
                'admin_financials.wallet As wallet',
                'admin_financials.card As card',
                'admin_financials.sheba As sheba',
                'admins.agency_id As agency_id',
                'admins.id As id',
                'admins.fullname As name',
                DB::raw('"admin" as type'),
            );
        $query3 = AgencyFinancial::query()->join('agencies', function ($join) {
            $join->on('agencies.id', '=', 'agency_financials.agency_id');
        })->select(
            'agency_financials.wallet',
            'agency_financials.card',
            'agency_financials.sheba',
            'agencies.id As agency_id',
            'agencies.id As id',
            'agencies.name As name',
            DB::raw('"agency" as type'),
        );
        if (!$admin->hasAccess('edit_setting')) {
            $myAgency = Agency::find($admin->agency_id);
            $agencyIds = $admin->allowedAgencies($myAgency)->pluck('id');
            //cant see users
            $query1->where('users.id', 0);
            $query2->whereIntegerInRaw('agency_id', $agencyIds);
            $query3->whereIntegerInRaw('agency_id', $agencyIds);
        }


        if ($search) {
            $query1->where('users.fullname', 'like', "%$search%")->orWhere('users.phone', 'like', "%$search");
            $query2->where('admins.fullname', 'like', "%$search%")->orWhere('admins.phone', 'like', "%$search");
            $query3->where('agencies.name', 'like', "%$search%")->orWhere('agencies.phone', 'like', "%$search");
        }


        $res = $query1->union($query2)->union($query3)->orderBy($orderBy, $dir);

        return $res->paginate($paginate, ['*'], 'page', $page);


    }

    public function update(FinancialRequest $request)
    {
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $amount = $request->amount;
        $type = $request->type;
        $user = $request->user();
        $data = Variable::FINANCIALS [$type]::where("{$type}_id", $id)->first();
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [Admin::class, $data]);
        if ($type) {
            $model = Variable::TRANSACTION_MODELS[$type]::find($id);
            $modelName = $model->name ?? $model->fullname;
        }
        if ($cmnd) {
            switch ($cmnd) {
                case  'settlement' :
                    if ($data->wallet < $amount)
                        return response()->json(['message' => sprintf(__('validator.max_amount'), __('settlement'), $data->wallet, $amount)], $errorStatus);

                    $t = Transaction::create([
                        'title' => sprintf(__('settlement_*_*_*'), number_format($amount), __($type), "$modelName ($id)"),
                        'type' => 'settlement',
                        'for_type' => $type,
                        'for_id' => $id,
                        'from_type' => 'agency',
                        'from_id' => 1,
                        'to_type' => $type,
                        'to_id' => $id,
                        'is_success' => true,
                        'info' => null,
                        'coupon' => null,
                        'payed_at' => Carbon::now(),
                        'amount' => $amount,
                        'pay_id' => null,
                    ]);
                    if ($t) {
                        $data->wallet -= $amount;
                        $data->save();
                        $t->user = $user;
                        Telegram::log(null, 'transaction_created', $t);
                    }
                    return response()->json(['message' => __('updated_successfully'), 'wallet' => $data->wallet], $successStatus);

                case  'charge' :

                    $t = Transaction::create([
                        'title' => sprintf(__('charge_*_*_*'), number_format($amount), __($type), "$modelName ($id)"),
                        'type' => 'charge',
                        'for_type' => $type,
                        'for_id' => $id,
                        'from_type' => 'agency',
                        'from_id' => 1,
                        'to_type' => $type,
                        'to_id' => $id,
                        'is_success' => true,
                        'info' => null,
                        'coupon' => null,
                        'payed_at' => Carbon::now(),
                        'amount' => $amount,
                        'pay_id' => null,
                    ]);
                    if ($t) {
                        $data->wallet += $amount;
                        $data->save();
                        $t->user = $user;
                        Telegram::log(null, 'transaction_created', $t);
                    }
                    return response()->json(['message' => __('updated_successfully'), 'wallet' => $data->wallet], $successStatus);


            }
        } elseif ($data) {


        }

        return response()->json($response, $errorStatus);
    }
}
