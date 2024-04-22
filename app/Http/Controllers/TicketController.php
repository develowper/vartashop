<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\TicketRequest;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Notification;
use App\Models\Ticket;
use App\Models\TicketChat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function edit(Request $request, $ticket)
    {
        $user = $request->user();
        $data = Ticket::whereId($ticket)->with('chats.owner:id,fullname,role')->first();
        $this->authorize('edit', [get_class($user), $data]);

        if ($user instanceof Admin) {
            if ($data->from_id == $user->id && $data->from_type == 'admin')
                $data->chats()->where(['from_seen' => false, 'from_type' => 'admin'])->update(['from_seen' => true]);
            if ($data->to_id == $user->id && $data->to_type == 'admin')
                $data->chats()->where(['to_seen' => false, 'from_type' => 'admin'])->update(['to_seen' => true]);

        } else
            if ($data->from_id == $user->id && $data->from_type == 'user')
                $data->chats()->where(['from_seen' => false, 'from_type' => 'user'])->update(['from_seen' => true]);
        if ($data->to_id == $user->id && $data->to_type == 'user')
            $data->chats()->where(['to_seen' => false, 'from_type' => 'user'])->update(['to_seen' => true]);

        $attachments = array_map(fn($e) => basename($e), File::glob(Storage::path("public/" . Variable::IMAGE_FOLDERS[Ticket::class]) . "/$data->id/*"));

        return Inertia::render('Panel/Ticket/Edit', [
            'statuses' => Variable::TICKET_STATUSES,
            'data' => $data,
            'attachments' => $attachments,
            'attachment_allowed_mimes' => implode(',.', Variable::TICKET_ATTACHMENT_ALLOWED_MIMES),
        ]);
    }

    public function update(TicketRequest $request)
    {
        $user = $request->user();
//        $response = ['message' => __('done_successfully')];
        $response = ['message' => __('response_error')];
        $errorStatus = Variable::ERROR_STATUS;
        $successStatus = Variable::SUCCESS_STATUS;

        $id = $request->id;
        $cmnd = $request->cmnd;
        $data = Ticket::find($id);
        if (!starts_with($cmnd, 'bulk'))
            $this->authorize('edit', [get_class($user), $data]);

        if ($cmnd) {
            switch ($cmnd) {
                case 'close':
                    $data->status = 'closed';
                    $data->save();
                    $res = ['status' => 'closed', 'flash_status' => 'success', 'flash_message' => __('updated_successfully')];
                    return back()->with($res);

                case 'del-chat':
                    $data = Ticket::whereId($request->ticket_id)->with('chats.owner:id,fullname,role')->first();
                    $chats = $data->getRelation('chats');
                    if (!$data || !$chats)
                        return back()->withErrors(['flash_message' => __('item_not_found')]);
                    foreach ($chats as $idx => $chat) {
                        if ($chat->id == $request->chat_id) {
                            foreach (File::glob(Storage::path("public/" . Variable::IMAGE_FOLDERS[Ticket::class]) . "/$data->id/$chat->id-*") as $path) {
                                File::delete($path);
                            }

                            $chat->delete();
                            $chats->forget($idx);
                            $len = count($chats);
                            $firstChat = $chats->first();
                            if ($len == 0)
                                $data->status = 'closed';
                            elseif ($len > 0 && $data->isResponse($user))
                                $data->status = 'responded';
                            else
                                $data->status = 'review';
                            $data->save();
                        }
                    }
                    $res = ['chats' => $chats, 'status' => $data->status, 'flash_status' => 'success', 'flash_message' => __('updated_successfully')];
                    return back()->with($res);
                    break;
                case 'add-chat':
                    $data->status = $data->isResponse($user) ? 'responded' : 'review';
                    $ticketChat = TicketChat::create([
                        'ticket_id' => $data->id,
                        'from_id' => $user->id,
                        'from_type' => ($user instanceof Admin) ? 'admin' : 'user',
                        'message' => $request->message,
                        'from_seen' => true,
                        'to_seen' => false,
                    ]);
                    foreach ($request->file('attachments') ?? [] as $idx => $item)
                        if ($item)
                            Util::createFile($item, Variable::IMAGE_FOLDERS[Ticket::class] . "/$data->id", "$ticketChat->id-$idx");
                    $data->updated_at = Carbon::now();
                    $data->save();
                    $res = ['chats' => $data->chats, 'status' => $data->status, 'flash_status' => 'success', 'flash_message' => __('updated_successfully')];
                    if ($data->isResponse($user)) {
                        Notification::create([
                            'data_id' => $data->id,
                            'owner_id' => $data->from_id,
                            'owner_type' => $data->from_type,
                            'subject' => __('ticket_answer'),
                            'description' => __('ticket_answered'),
                            'type' => 'ticket_answer',

                        ]);
                        Variable::PAYER_TYPES[$data->from_type]::where('id', $data->from_id)->increment('notifications');
                    }
                    Telegram::log(null, 'ticket_updated', $data);
                    return back()->with($res);


            }
        } elseif ($data) {


            $request->merge([
            ]);
//            $data->name = $request->tags;
//            $data->tags = $request->tags;
//            dd($request->tags);
            if ($data->update($request->all())) {

                $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
//                dd($request->all());
                Telegram::log(null, 'ticket_edited', $data);
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

        $query = Ticket::query();
        $query = $query->select();


        if ($user instanceof Admin) {
            $query->whereIntegerInRaw('agency_id', $user->allowedAgencies(Agency::find($user->agency_id))->pluck('id'));
        }
        if ($user instanceof User) {
            $query = $query->where(function ($query) use ($user) {
                return $query->where(['from_id' => $user->id, 'from_type' => 'user'])
                    ->orWhere(['to_id' => $user->id, 'to_type' => 'user']);
            });
        }
        if ($search)
            $query = $query->where('subject', 'like', "%$search%");

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);
    }

    public function create(TicketRequest $request)
    {


        $user = auth()->user()/* ?? auth('api')->user()*/
        ;

        if (!$user) {
            $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
            return back()->with($res);
        }
//        if ($user->is_block) {
//            $res = ['flash_status' => 'danger', 'flash_message' => __('user_is_blocked')];
//            return back()->with($res);
//        }
        if (!$request->uploading) { //send again for uploading images

            return back()->with(['resume' => true]);
        }

        $fromType = ($user instanceof Admin) ? 'admin' : 'user';

        $ticket = Ticket::create([
            'subject' => $request->subject,
            'agency_id' => ($user instanceof Admin) ? $user->agency_id : 1,
            'from_id' => $user->id,
            'from_type' => $fromType,
            'to_id' => Admin::where(['agency_id' => 1, 'role' => 'owner'])->first()->id ?? 1,
            'to_type' => 'admin',
            'status' => ($user instanceof Admin) && $user->agency_id == 1 ? 'responded' : 'review',
        ]);

        if ($ticket) {
            $res = ['flash_status' => 'success', 'flash_message' => __('created_successfully')];

            $ticketChat = TicketChat::create([
                'ticket_id' => $ticket->id,
                'from_id' => $user->id,
                'from_type' => $fromType,
                'from_seen' => true,
                'to_seen' => false,
                'message' => $request->message,
            ]);
            foreach ($request->file('attachments') ?? [] as $idx => $item)
                if ($item)
                    Util::createFile($item, Variable::IMAGE_FOLDERS[Ticket::class] . "/$ticket->id", "$ticketChat->id-$idx");

            Telegram::log(null, 'ticket_created', $ticket);
        } else    $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return to_route("$fromType.panel.ticket.index")->with($res);
    }
}
