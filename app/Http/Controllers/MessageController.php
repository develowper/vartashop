<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\MessageRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function edit(Request $request, $id)
    {

        $data = Message::find($id);
        return Inertia::render('Panel/Admin/Message/Edit', [
            'data' => $data,

        ]);
    }

    public function create(MessageRequest $request)
    {
        $data = Message::create($request->all());
        if ($data->save()) {
            Telegram::log(null, 'message_created', $data);
            return response()->json(['message' => __('your_message_send_successfully'),], Variable::SUCCESS_STATUS);

        }
    }

    public function update(MessageRequest $request)
    {
        $user = auth()->user();
//        $response = ['message' => __('done_successfully')];
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;
        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Message::find($id);

        if ($cmnd) {
            switch ($cmnd) {

                case 'remove':

                    $data->delete();
                    return response()->json(['message' => __('updated_successfully'),], $successStatus);

            }
        } elseif ($data) {


            $request->merge([
                'is_active' => $request->status == 'active',
            ]);


//            $data->name = $request->tags;
//            $data->tags = $request->tags;
//            dd($request->tags);
            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
                Telegram::log(null, 'slider_edited', $data);
            } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }

        return response()->json($response, $errorStatus);
    }

    public function searchPanel(Request $request)
    {
        $user = $request->user();
        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;

        $query = Message::query();
//        $query = $query->select('id', 'title', 'is_active', 'created_at');


        if ($search)
            $query = $query->where('fullname', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);
    }
}
