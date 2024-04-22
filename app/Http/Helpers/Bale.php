<?php

namespace App\Http\Helpers;

use App\Models\Category;
use App\Models\County;
use App\Models\Province;
use Carbon\Carbon;
use DateTimeZone;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\Http;

class Bale
{

    const LOGS = [733349193 /* my id*/,];

    static function sendMessage($chat_id, $text, $mode = null, $reply = null, $keyboard = null, $disable_notification = false, $app_id = null)
    {

        return self::creator('sendMessage', [

            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => $mode,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard,
            'disable_notification' => $disable_notification,
        ]);


    }

    static function deleteMessage($chatid, $massege_id)
    {
        return self::creator('DeleteMessage', [
            'chat_id' => $chatid,
            'message_id' => $massege_id
        ]);
    }

    static function sendPhoto($chat_id, $photo, $caption, $reply = null, $keyboard = null)
    {


        return self::creator('sendPhoto', [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);

    }


    static function sendMediaGroup($chat_id, $media, $keyboard = null, $reply = null)
    {
//2 to 10 media can be send

        return self::creator('sendMediaGroup', [
            'chat_id' => $chat_id,
            'media' => json_encode($media),
            'reply_to_message_id' => $reply,

        ]);

    }

    static function sendSticker($chat_id, $file_id, $keyboard, $reply = null, $set_name = null)
    {
        return self::creator('sendSticker', [
            'chat_id' => $chat_id,
            'sticker' => $file_id,
            "set_name" => $set_name,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);
    }


    static function logAdmins($msg, $mode = null)
    {
        foreach (self::LOGS as $log)
            self::sendMessage($log, $msg, $mode);

    }

    static function creator($method, $datas = [])
    {
//        if (!str_contains(url('/'), '.com') && !str_contains(url('/'), '.ir')) return;

        $url = "https://tapi.bale.ai/" . env('BALE_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $method;
        return Http::asForm()->post($url, $datas);


    }

    static function MarkDown($string)
    {
        $string = str_replace(["_",], '\_', $string);
        $string = str_replace(["`",], '\`', $string);
        $string = str_replace(["*",], '\*', $string);
        $string = str_replace(["~",], '\~', $string);


        return $string;
    }


}
