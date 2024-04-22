<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Http\Helpers\Telegram;
use Illuminate\Http\Request;

class PushController extends Controller
{

    public function fetchMessages()
    {
//        return Message::with('user')->get();
    }


    public function addChatSupportHistory($request)
    {
        $ch = session('support-chat') ?? [];
        array_push($ch, $request);
        session()->put('support-chat', $ch);
    }


    public function chatSupportHistory(Request $request)
    {
        switch ($request->cmnd) {
            case 'get':
                $starterChat = [
                    'message' => __('send_phone_request_for_start_chat'),
                    'from' => 'support-1',
                    'to' => $request->ip(),
                    'chatId' => $request->ip(),
                ];
                $chats = session('support-chat') ?? [$starterChat];

                return $chats;
                break;
            case 'clear':
                session()->put('support-chat', []);
                return [];
                break;
            case 'add':
                $ch = session('support-chat') ?? [];
                array_push($ch, $request->data);
                session()->put('support-chat', $ch);
                break;
        }

    }

    public function broadcast(Request $request)
    {
//        $user = Auth::user();
//
//        $message = $user->messages()->create([
//            'message' => $request->input('message')
//        ]);
//
//        return ['status' => 'Message Sent!'];

//        broadcast(new ChatEvent($request->from, $request->to, $request->message))->toOthers();
        event(new ChatEvent($request->from, $request->to, $request->message, $request->chatId, $request->msgId));
//        Telegram::sendMessage(Telegram::LOGS[0], 'ip:' . $request->from . PHP_EOL . $request->message);
        Telegram::log(null, 'chat_created', 'ip:' . $request->from . PHP_EOL . $request->message);
    }
}
