<?php

namespace App\Listeners;

use App\Events\Viewed;
use App\Http\Helpers\Variable;
use App\Models\DataTransaction;
use App\Models\Setting;
use App\Models\UserTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ViewedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\Viewed $event
     * @return void
     */
    public function handle(Viewed $event)
    {
        $userReward = $event->userReward;
        $data = $event->data;
        $viewClass = $event->viewClass;
        $title = __(Variable::TRANSACTION_TYPES[$viewClass] . "_view");
        $query = $viewClass::query();
        $user = auth()->user();

        $userId = optional($user)->id;

        if ($userId == $data->owner_id) return;

        $ip = request()->ip();
        //check user viewed before
        $query->where(function ($query) use ($ip, $userId) {
            if ($ip)
                $query->orWhere('ip', $ip);
            if ($userId)
                $query->orWhere('owner_id', $userId);
        });

        $query->where('data_id', $data->id)->where('type', 'view');
        $viewTransaction = $query->first();
        $date = Carbon::now(/*'Asia/Tehran'*/)->setTime(0, 0);

        $storeData = DataTransaction::firstOrCreate([
            'data_type' => Variable::DATA_TYPES[get_class($data)],
            'owner_id' => $data->owner_id,
            'data_id' => $data->id,
            'date' => $date,

        ]);

//user transactions for data is zero for now

        $storeUser = UserTransaction::firstOrCreate($ip, $userId);

        if (!$viewTransaction) {
            $data->viewer++;
            $storeData->viewer++;
//            if ($data->charge > $data->view_fee) {
//                $data->charge -= $data->view_fee;
//                $storeData->sum += $data->view_fee;
//
//                Setting::setWallet($data->view_fee);
            if ($data->charge < $data->view_fee)
                $data->status = 'need_charge';
//            }
            $viewTransaction = $viewClass::create([
                'title' => $title,
                'ip' => $ip,
                'owner_id' => $userId,
                'data_id' => $data->id,
                'type' => 'view',
                'is_meta' => false,
                'amount' => 0,
            ]);
            $storeUser->view++;
            $storeUser->save();
        }
        $data->view++;
        $storeData->view++;
        if (!$viewTransaction->owner_id)
            $viewTransaction->owner_id = $userId;

        if (!$viewTransaction->ip)
            $viewTransaction->ip = $ip;

        $data->save();
        $storeData->save();
        $viewTransaction->save();


    }
}
