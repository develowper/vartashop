<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\NotificationRequest;
use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function delete(Request $request, $id)
    {
        if (Notification::where('id', $id)->delete()) {
            return response()->json(['message' => __('done_successfully')], Variable::SUCCESS_STATUS);
        }
        return response()->json(['message' => __('item_not_found')], Variable::ERROR_STATUS);
    }

    public function edit(Request $request, $notification)
    {
        $data = Notification::whereId($notification)->with('owner:id,fullname,phone')->first();

        return Inertia::render('Panel/Notification/Edit', [
            'types' => Variable::NOTIFICATION_TYPES,
            'data' => $data,
        ]);
    }

    public function update(NotificationRequest $request)
    {
        $id = $request->id;
        $cmnd = $request->cmnd;
        $user = auth()->user();
        if ($cmnd == 'reset') {
            if ($user->notifications == 0) return;
            $user->notifications = 0;
            $user->save();
            return response()->json(['message' => __('updated_successfully')], Variable::SUCCESS_STATUS);

        }

        $data = Notification::find($id);


        if (!$data)
            return back()->withErrors(['errors' => __('item_not_found')]);

        $oldOwner = $data->owner_id;
        if ($data->update($request->all())) {

            if ($request->owner_id != $oldOwner) {
                if ($data->owner_id)
                    User::where('id', $oldOwner)->where('notifications', '>', 0)->decrement('notifications');
                if ($request->notification)
                    if ($request->owner_id)
                        User::where('id', $request->owner_id)->increment('notifications');
                    else
                        User::increment('notifications');

            }
            $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
            Telegram::log(null, 'notification_edited', $data);
            return back()->with($res);
        }

    }

    public function create(NotificationRequest $request)
    {


        $notification = Notification::create([
            'link' => $request->link,
            'subject' => $request->subject,
            'description' => $request->description,
            'owner_id' => $request->owner_id,
        ]);
        if ($notification) {
            if ($request->owner_id)
                User::where('id', $request->owner_id)->increment('notifications');
            else
                User::increment('notifications');
            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];
            Telegram::log(null, 'notification_created', $notification);
            return to_route('panel.admin.notification.index')->with($res);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return back()->with($res);
    }


    public function searchPanel(Request $request)
    {
        $user = $request->user();
        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;

        $query = Notification::query();
        $query = $query->select();
        if ($user->role == 'us')
            $query = $query->where('owner_id', $user->id);

        if ($search)
            $query = $query->where('subject', 'like', "%$search%");

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);
    }
}
