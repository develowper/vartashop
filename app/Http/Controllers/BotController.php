<?php

namespace App\Http\Controllers;


use App\Events\ChatEvent;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Models\Admin;
use App\Models\User;


use Carbon\Carbon;
use DateTime;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use PhpParser\Node\Stmt\Else_;


class BotController extends Controller
{
    protected $logs, $user, $bot, $app_link;

    public function __construct()
    {
        error_reporting(1);
        set_time_limit(-1);
        header("HTTP/1.0 200 OK");
        date_default_timezone_set('Asia/Tehran');
//--------[Your Config]--------//

//-----------------------------//
    }


    public function getupdates(Request $request)
    {

        try {
            $update = json_decode(file_get_contents('php://input'));
            $this->bot = '@' . Variable::TELEGRAM_BOT;
            $this->app_link = url('');
            $this->tut_link = url('');
            if (isset($update->message)) {
                $message = $update->message;
                $chat_id = $message->chat->id;
                $thread_id = $message->message_thread_id;
                $chat_username = '@' . $message->chat->username;
                $text = $message->text;
                $message_id = $message->message_id;
                $from_id = $message->from->id;
                $tc = $message->chat->type;
                $title = isset($message->chat->title) ? $message->chat->title : "";
                $first_name = isset($message->from->first_name) ? $message->from->first_name : "";
                $last_name = isset($message->from->last_name) ? $message->from->last_name : "";
                $username = isset($message->from->username) ? '@' . $message->from->username : "";
                $reply = isset($message->reply_to_message) ? $message->reply_to_message : "";
                $reply_id = isset($message->reply_to_message->from->id) ? $message->reply_to_message->from->id : "";
                $new_chat_member = $update->message->new_chat_member; # id,is_bot,first_name,last_name,username
                $new_chat_members = $update->message->new_chat_members; #id,is_bot,first_name,last_name,username
                $left_chat_member = $update->message->left_chat_member; #id,is_bot,first_name,username
                $new_chat_participant = $update->message->new_chat_participant; #id,username

//            $animation = $update->message->animation;  #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
//            $sticker = $update->message->sticker;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
//            $photo = $update->message->photo; # file_id,file_unique_id,file_size,width,height] array of different photo sizes
//            $document = $update->message->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
//            $video = $update->message->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
//            $audio = $update->message->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
//            $voice = $update->message->voice; #duration,mime_type,file_id,file_unique_id,file_size
//            $video_note = $update->message->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
                $caption = $message->caption;

            }
            if (isset($update->callback_query)) {
                $Data = $update->callback_query->data;
                $data_id = $update->callback_query->id;
                $chat_id = $update->callback_query->message->chat->id;
                $from_id = $update->callback_query->from->id;
                $first_name = $update->callback_query->from->first_name;
                $last_name = $update->callback_query->from->last_name;
                $username = '@' . $update->callback_query->from->username;
                $tc = $update->callback_query->message->chat->type;
                $message_id = $update->callback_query->message->message_id;

            }
            if (isset($update->channel_post)) {
                $tc = $update->channel_post->chat->type;
                $text = $update->channel_post->text;
                $chat_id = $update->channel_post->chat->id;
                $chat_username = '@' . $update->channel_post->chat->username;
                $chat_title = $update->channel_post->chat->title;
                $message_id = $update->channel_post->message_id;
//            $from_id = json_encode($update);
//            $from_id = Telegram::LOGS;
                $caption = $update->channel_post->caption;
                $photo = $update->channel_post->photo; # file_id,file_unique_id,file_size,width,height] array of different photo sizes
                $document = $update->channel_post->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
                $video = $update->channel_post->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
                $audio = $update->channel_post->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
                $voice = $update->channel_post->voice; #duration,mime_type,file_id,file_unique_id,file_size
                $video_note = $update->channel_post->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]


            }
//        return json_encode($from_id);
            // if ($new_chat_members || $new_chat_member || $left_chat_member){
            //     Storage::prepend('file.log', json_encode($update->message));

//           Telegram::sendMessage("871016407", "$message", "Markdown", null, null);
//           Telegram::sendMessage("72534783", print_r($update, true), null, null, null);

            // }


//------------------------------------------------------------------------------
//        $rank = $this->user_in_chat($this->channel, $from_id, $tc);// $get['result']['status'];

//        $this->bot_id = Telegram::creator('GetMe', [])->result->id;
//        $INSTALL_ICON = '🥒';
//        $ABOUT_ICON = '🤖';
//        $USER_EDIT_ICON = "✏";
//        $USER_REGISTER_ICON = "✅";
//        $CANCEL_REGISTER_ICON = "❌";
//
//        $INSTALL_BOT = " نصب ربات";
//        $ABOUT_BOT = " درباره ربات";
//        $USER_EDIT = "ویرایش اطلاعات";
//        $USER_REGISTER = " ثبت نام ";
//        $CANCEL_REGISTER = "لغو ثبت نام";
//            Telegram::sendMessage(Telegram::LOGS[0], print_r($thread_id, true));

            if ($tc == 'private') {
                $this->user = Admin::where('telegram_id', $from_id)->first();

                if ($text == 'hi') {
                    Telegram::sendMessage($from_id, print_r($update, true));
                    return;
                }

//                Telegram::sendMessage($from_id, "در حال بروزرسانی هستیم...");
////
//                return;
//            return (string)($USER_REGISTER . "\xE2\x9C\x85" == $text);
//            return (string)(0 == null);
//            return $this->user_in_channel("@lamassaba", $from_id);// == 'administrator' or 'creator'
//            return $this->user_in_channel("@twitterfarsi", $from_id);// Bad Request: user not found
//            return $this->user_in_channel("@twitteddrfarsi", $from_id);// Bad Request: chat not found

//            return json_encode($this->inviteToChat($this->channel));
                $market_button = json_encode(['inline_keyboard' => [
                    [['text' => "📪 کافه بازار 📪", 'url' => Variable::LINKS['bazaar']]],
                    [['text' => "📪 مایکت 📪", 'url' => Variable::LINKS['myket']]],
                    [['text' => "📪 پلی استور 📪", 'url' => Variable::LINKS['playstore']]],

                ], 'resize_keyboard' => true]);

                $buy_button = json_encode(['inline_keyboard' => [
                    [['text' => "📪 ارتباط با ما 📪", 'url' => "telegram.me/" . 'develowper']],
                    [['text' => "📌 دریافت بنر تبلیغاتی 📌", 'callback_data' => "بنر"]],
                ], 'resize_keyboard' => true]);

                $divar_button = json_encode(['keyboard' => [

                    [['text' => 'امتیاز من💰']],
                    [['text' => 'منوی اصلی⬅']],
                ], 'resize_keyboard' => true]);
                $button = json_encode(['keyboard' => [
                    in_array($from_id, Telegram::LOGS) ? [['text' => 'پنل مدیران🚧']] : [],

                    [['text' => $this->user ? "ویرایش اطلاعات✏" : "ثبت نام✅"]],
                    [['text' => "📱 دریافت اپلیکیشن 📱"]],
                    [['text' => "🎴 ساخت دکمه شیشه ای 🎴"]],
                    [['text' => "📌 دریافت بنر تبلیغاتی 📌"]],
                    [['text' => 'امتیاز من💰']],
                    [['text' => 'درباره ربات🤖']],
                ], 'resize_keyboard' => true]);
                $cancel_button = json_encode(['keyboard' => [
                    [['text' => "لغو ثبت نام❌"]],
                ], 'resize_keyboard' => true]);
                $return_button = json_encode(['inline_keyboard' => [
                    [['text' => "بازگشت⬅", 'callback_data' => "edit_cancel"]],
                ], 'resize_keyboard' => true]);
                $edit_button = json_encode(['inline_keyboard' => [
                    [['text' => 'ویرایش نام کاربری', 'callback_data' => "edit_username"]],
                    [['text' => 'ویرایش ایمیل', 'callback_data' => "edit_email"]],
                    [['text' => 'ویرایش گذرواژه', 'callback_data' => "edit_password"]],
                    [['text' => 'ویرایش تصویر پروفایل', 'callback_data' => "edit_image"]],
                ], 'resize_keyboard' => true]);
                $admin_button = json_encode(['inline_keyboard' => [
                    [['text' => "📬 ارسال همگانی به کاربران", 'callback_data' => 'send_to_users']],
                    [['text' => "📬 ارسال همگانی به گروه ها", 'callback_data' => 'send_to_chats']],
                    [['text' => "🚶 مشاهده کاربران", 'callback_data' => 'see_users']],
                    [['text' => "🚶 مشاهده فالورها", 'callback_data' => 'see_followers']],
                    [['text' => "❓ راهنمای دستورات", 'callback_data' => 'admin_help']],
                    [['text' => "📊 آمار", 'callback_data' => 'statistics']],
                ], 'resize_keyboard' => true]);
                $send_cancel_button = json_encode(['inline_keyboard' => [
                    [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                ], 'resize_keyboard' => true]);

                if (preg_match('/^\/(start)$/i', $text)) {

                    if (!$this->user) Telegram::sendMessage($chat_id, "■ سلام $first_name خوش آمدید\n\n■ برای استفاده از تمامی امکانات ربات و اپلیکیشن ابتدا ثبت نام کنید :", null, $message_id, $button);
                    else Telegram::sendMessage($chat_id, "■ سلام $first_name خوش آمدید✋\n\n■ چه کاری براتون انجام بدم؟ ", null, $message_id, $button);
//                $first_name = $this->MarkDown($first_name);
                    Telegram::sendMessage($chat_id, " \n آموزش ربات\n" . $this->tut_link, null, $message_id, null);
                    Telegram::logAdmins("■  کاربر [$first_name](tg://user?id=$from_id) ربات دبل چین را استارت زد.", 'MarkDown');

                }
//            elseif ($rank != 'creator' && $rank != 'administrator' && $rank != 'member') {
//                Telegram::sendMessage($chat_id, "■ برای استفاده از ربات و همچنین حمایت از ما ابتدا وارد کانال\n● $this->channel\n■ شده سپس به ربات برگشته و /start را بزنید.", null, $message_id, json_encode(['KeyboardRemove' => [], 'remove_keyboard' => true]));
//
//            }
                elseif ($text == 'منوی اصلی⬅') {
                    Telegram::sendMessage($chat_id, "منوی اصلی", 'MarkDown', $message_id, $button);

                } elseif ($text == 'امتیاز من💰') {
                    $score = $this->user->score ?? 0;

                    Telegram::sendMessage($from_id, "💰 امتیاز فعلی شما:$score \n  برای دریافت امتیاز می توانید بنر تبلیغاتی مخصوص خود را تولید کرده و یا در اپلیکیشن ویدیو تماشا کنید و یا از طریق دکمه ارتباط با ما اقدام به خرید امتیاز نمایید ", 'Markdown', $message_id, $buy_button);


                } elseif ($text == "منوی اصلی💬") {

                    Telegram::sendMessage($chat_id, "منوی اصلی", null, $message_id, $button);

                } elseif ($text == "لغو ❌") {
                    if ($this->user) {
                        $this->user->step = null; // for register channel
                        $this->user->save();
                    }
                    Telegram::sendMessage($chat_id, "با موفقیت لغو شد!", null, $message_id, $button);

                } elseif ($text == 'درباره ربات🤖') {
                    Telegram::sendMessage($chat_id, "✅دبل چین: بازار میوه تره بار آنلاین ایران✅", 'MarkDown', $message_id);
                    Telegram::sendMessage($chat_id, " \n لینک دریافت اپلیکیشن:\n  $this->app_link \n", 'MarkDown', $message_id);
                    Telegram::sendMessage($chat_id, "$this->info\n" . " \n آموزش ربات\n  $this->tut_link \n", 'Markdown', $message_id, $button);
                } elseif ($text == "لغو ثبت نام❌") {
                    $button = json_encode(['keyboard' => [
                        [['text' => "ثبت نام✅"]],
                        [['text' => 'درباره ربات🤖']],
                    ], 'resize_keyboard' => true]);# user is registering
                    if ($this->user) {
                        $this->user->step = null; // for register channel
                        $this->user->save();
                    }
//                if ($this->user) $this->user->delete();
                    Telegram::sendMessage($chat_id, "ثبت نام شما لغو شد", 'MarkDown', $message_id, $button);

                } elseif ($text == "ویرایش اطلاعات✏") {

                    if (!$this->user) Telegram::sendMessage($chat_id, "شما  ثبت نام نکرده اید", 'MarkDown', $message_id, $button);
                    else {
                        Telegram::sendMessage($chat_id, "■ برای مدیریت تنظیمات از کلید های زیر استفاده کنید :", null, $message_id, $edit_button);
//                    $this->user->step = 0;
//                    $this->user->save();
//                    Telegram::sendMessage($chat_id, "نام کاربری را وارد کنید", 'MarkDown', $message_id, $button);
                    }
                } elseif ($Data == "help_add_bot_channel") {
                    $txt = "\n*اضافه کردن ربات در کانال :*\n🔸 ابتدا وارد کانال خود شده و روی اسم آن کلیک کرده تا اطلاعات آن نمایش داده شود\n🔸 در نسخه دسکتاپ روی گزینه سه نقطه و سپس گزینه *add members* کلیک کنید.\n🔸 در نسخه موبایل روی  *subscribers* و سپس *add subscriber* کلیک کنید . \n در این مرحله اسم ربات($this->bot) را جستجو نموده و به کانال اضافه کنید\n 🔸 *ربات در کانال حتما باید به عنوان ادمین اضافه شود* . \n 🔸سپس در کانال دستور 'نصب' را وارد کنید تا کانال شما ثبت شود🌹";
                    Telegram::sendMessage($chat_id, $txt, 'MarkDown', null);

                } elseif ($Data == "edit_username") {
                    $name = $this->user->username;
                    $this->user->step = 6;
                    $this->user->save();
                    Telegram::sendMessage($chat_id, "نام  فعلی: $name \n  نام  جدید را وارد کنید:", 'MarkDown', null, $return_button);

                } elseif ($Data == "edit_email") {
                    $name = $this->user->email;
                    $this->user->step = 7;
                    $this->user->save();
                    Telegram::sendMessage($chat_id, "ایمیل  فعلی: $name \n  ایمیل  جدید را وارد کنید:", 'MarkDown', null, $return_button);

                } elseif ($Data == "edit_password") {
                    $this->user->step = 8;
                    $this->user->save();
                    Telegram::sendMessage($chat_id, "    \n  گذرواژه جدید را وارد کنید:", 'MarkDown', null, $return_button);

                } elseif ($Data == "edit_image") {
//                $this->user->step = 8;
//                $this->user->save();
                    $this->createUserImage($this->user->telegram_id);
                    Telegram::sendMessage($chat_id, "تصویر فعلی تلگرام شما بعنوان عکس پروفایل در اپلیکیشن تنظیم شد", 'MarkDown', null, $return_button);

                } elseif ($Data == "edit_cancel") {
                    $this->user->step = null;
                    $this->user->save();
                    Telegram::sendMessage($chat_id, "■ برای مدیریت تنظیمات از کلید های زیر استفاده کنید :", null, null, $edit_button);


                } elseif ($text == "پنل مدیران🚧") {
//
                    Telegram::sendMessage($chat_id, "🚧فقط مدیران ربات به این پنل دسترسی دارند. گزینه مورد نظر خود را انتخاب کنید:", "Markdown", null, $admin_button);
                } elseif ($Data == "send_to_users") {
                    $this->user->step = 9;
                    $this->user->save();
                    Telegram::sendMessage($chat_id, "■ متن یا فایل ارسالی را وارد کنید :", null, null, $send_cancel_button);

                } elseif ($Data == "send_to_chats") {
                    $this->user->step = 91;
                    $this->user->save();
                    Telegram::sendMessage($chat_id, "■ متن یا فایل ارسالی را وارد کنید :", null, null, $send_cancel_button);


                } elseif ($Data == "send_cancel") {
                    $this->user->step = null;
                    $this->user->save();
                    Telegram::sendMessage($chat_id, "با موفقیت لغو شد ", null, null, null);


                } elseif ($Data == "see_users") {
                    $txt = "";
                    $txt .= "\n-------- لیست کاربران-----\n";
                    if (in_array($from_id, Telegram::LOGS))


                        foreach (User::get(['id', 'fullname', 'telegram_username', 'telegram_id', 'score']) as $idx => $user) {

                            $txt .= "id: $user->id\n";
                            $txt .= "name: $user->fullname\n";
                            $txt .= "telegram_username: $user->telegram_username\n";
                            $txt .= "telegram_id: $user->telegram_id\n";
//                        $txt .= "channels:" . json_encode($user->channels) . "\n";
//                        $txt .= "groups: " . json_encode($user->groups) . "\n";
                            $txt .= "score: $user->score\n";
                            $txt .= "\n-----------------------\n";
                            if ($idx % 3 == 0) {
                                Telegram::sendMessage($chat_id, $txt, null, null, null);
                                $txt = "";
                            }
                        }


                } elseif ($Data == "send_to_users_ok") {
                    $this->user->step = null;
                    $this->user->save();

                    if (in_array($from_id, Telegram::LOGS))
                        foreach (User::pluck('telegram_id')->toArray() as $id) {

                            Telegram::sendFile($id, Storage::get('message.txt'), null);
                        }
                    Telegram::deleteMessage($chat_id, $message_id);
                    Telegram::sendMessage($chat_id, "■ با موفقیت به کاربران ارسال شد!", null, null, null);


                } elseif ($Data == "send_to_chats_ok") {
                    $this->user->step = null;
                    $this->user->save();

                    if (in_array($from_id, Telegram::LOGS))

                        foreach (Telegram::LOGS as $id => $data) {

                            $channel = $data['channel'];
                            Telegram::sendFile($channel, Storage::get('message.txt'), null);
                        }

//                Telegram::deleteMessage($chat_id, $message_id);
                    Telegram::sendMessage($chat_id, "■ با موفقیت به گروه ها ارسال شد!", null, null, null);


                } elseif ($Data == "statistics") {


                    $txt = "";
                    $txt .= "تعداد کاربران" . PHP_EOL;
                    $txt .= User::count() . PHP_EOL;
                    $txt .= "-------------------" . PHP_EOL;
                    $txt .= "تعداد سیگنال ها" . PHP_EOL;
                    $txt .= Signal::count() . PHP_EOL;
                    $txt .= "-------------------" . PHP_EOL;
                    $txt .= "تعداد اخبار" . PHP_EOL;
                    $txt .= News::count() . PHP_EOL;

//                Telegram::deleteMessage($chat_id, $message_id);
                    Telegram::sendMessage($chat_id, $txt, null, null, null);


                } elseif ($Data == "admin_help") {
                    $txt = "اضافه کردن امتیاز به کاربر" . "\n";
                    $txt .= "<user_id>:score:<score>" . "\n";
                    $txt .= "اضافه کردن به دیوار" . "\n";
                    $txt .= "<@chat_username>:divar:<hours>" . "\n";
                    $txt .= "حذف از دیوار" . "\n";
                    $txt .= "<@chat_username>:divar:delete" . "\n";
                    $txt .= "ساخت بنر" . "\n";
                    $txt .= "banner:<متن پیام>" . "\n";
                    $txt .= "ساخت متن با کلید شیشه ای" . "\n";
                    $txt .= "inline:<متن پیام>\nمتن1\nلینک1\n ..." . "\n";
                    $txt .= "تبلیغ انتهای پیام ارسالی" . "\n";
                    $txt .= "banner=name=link" . "\n";
                    Telegram::sendMessage($chat_id, $txt, null, null, null);

                } elseif ((strpos($text, ":score:") !== false)) {


                    $id = explode(":", $text)[0];
                    $score = explode(":", $text)[2];
                    if (in_array($from_id, Telegram::LOGS)) {
                        $u = User::where('id', $id)->first();
                        $u->score += $score;
                        $u->save();
                        Telegram::sendMessage($u->telegram_id, "🙌 تبریک! \n $score  امتیاز به شما اضافه شد!  \n  امتیاز فعلی : $u->score", null, null, null);
                        Telegram::sendMessage($chat_id, "$score  امتیاز به $u->telegram_username  اضافه شد.", null, null, null);
                    }

                } elseif ((strpos($text, "banner:") !== false)) {
                    if (!in_array($from_id, Telegram::LOGS)) return;
                    $txt = " سلام   \n *دبل چین* هستم . با من میتونی برای گروه یا کانال خودت *فالور جذب کنی*. \n *من یه ربات شبیه دیوارم که گروه/کانال تو رو تبلیغ میکنم و بقیه از فالو کردن اون امتیاز میگیرند و میتونن کانال/گروه خودشونو تبلیغ کنن*  \n آموزش ربات\n  $this->tut_link  $this->bot ";
                    $buttons = [[['text' => '👈 دانلود اپلیکیشن 👉', 'url' => $this->app_link]]];
                    $tmp = explode(":", $text);
                    if (count($tmp) >= 2 && $tmp[1] != '')
                        $txt = $tmp[1];

                    Telegram::sendMessage($chat_id, $txt, "Markdown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]));


                } elseif ((strpos($text, "inline:") !== false)) {
                    if (!in_array($from_id, Telegram::LOGS)) return;
                    $buttons = [];
                    $inlines = explode("\n", $text);
                    $txt = explode(":", array_shift($inlines))[1]; //remove first (inline)
                    $len = count($inlines);
                    foreach ($inlines as $idx => $item) {

                        if ($idx % 2 == 0 && $idx + 1 < $len)
                            array_push($buttons, [['text' => $inlines[$idx], 'url' => $inlines[$idx + 1]]]);

                    }


                    Telegram::sendMessage($chat_id, $txt, null, null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]));


                } elseif ($text == "ثبت نام✅") {

                    if ($this->user) Telegram::sendMessage($chat_id, "شما قبلا ثبت نام کرده اید", 'MarkDown', $message_id, $button);
                    else if ($username == "@" || $username == "") Telegram::sendMessage($chat_id, "لطفا قبل از ثبت نام, از منوی تنظیمات تلگرام خود, یک نام کاربری به اکانت خود تخصیص دهید!", 'MarkDown', $message_id, $button);
                    else {
                        Telegram::sendMessage($chat_id, "لطفا در اپلیکیشن ثبت نام کرده و از قسمت پروفایل، اتصال به تلگرام را بزنید", 'MarkDown', $message_id, $market_button);
//                    $this->user = User::create(['telegram_id' => $from_id, 'telegram_username' => $username, 'score' => $this->init_score, 'step' => 0]);
//                    Telegram::sendMessage($chat_id, "نام کاربری خود را وارد کنید \n(حداقل 6 حرف)", 'MarkDown', $message_id, $cancel_button);
                    }
                } elseif ($text == "📱 دریافت اپلیکیشن 📱") {
                    Telegram::sendMessage($chat_id, "لطفا در اپلیکیشن ثبت نام کرده و از قسمت پروفایل، اتصال به تلگرام را بزنید", 'MarkDown', $message_id, $market_button);

//                Telegram::sendMessage($chat_id, "📱لینک دریافت اپلیکیشن📱" . "\n" . $this->app_link, 'MarkDown', $message_id, $button);

                } elseif ($text == "🎴 ساخت دکمه شیشه ای 🎴") {
                    if (!$this->user) Telegram::sendMessage($chat_id, "■  $first_name \n\n■  ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);

                    else {
                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ❌"]],
                        ], 'resize_keyboard' => true]);
                        $this->user->step = 10;

                        $this->user->save();

                        Telegram::sendMessage($chat_id, "متن یا فایل خود را وارد کنید", 'MarkDown', $message_id, $cancel_button);
                    }
                } elseif (!$Data && $this->user && $this->user->step !== null && $this->user->step >= 0) {
                    # user is registering

                    switch ($this->user->step) {
                        case  0:
                            if ($this->check('username', $text, $chat_id, $message_id, $cancel_button)) {
                                $this->user->step++;
                                $this->user->username = $text;
                                $this->user->save();
                                Telegram::sendMessage($chat_id, "ایمیل خود را وارد کنید", 'MarkDown', $message_id);

                            }
                            break;
                        case  1:
                            if ($this->check('email', $text, $chat_id, $message_id, $cancel_button)) {
                                $this->user->step = 5;
                                $this->user->email = $text;
                                $this->user->save();
                                Telegram::sendMessage($chat_id, "رمز عبور را وارد کنید\n(حداقل 6 حرف)", 'MarkDown', $message_id);

                            }
                            break;
                        //check email or phone later
                        case  5:
                            if ($this->check('password', $text, $chat_id, $message_id, $cancel_button)) {

                                $this->user->password = Hash::make($text);
                                $this->user->step = null;
                                $this->user->ref_id = User::makeRefCode($this->user->phone);
                                $this->user->save();
                                $this->createUserImage($this->user->telegram_id);
                                Telegram::sendMessage($chat_id, "با موفقیت ثبت نام شدید! و امتیاز به حساب شما افزوده شد.\nشما می توانید با اشتراک گذاری بنر خود و یا دیدن ویدیو در اپلیکیشن، امتیاز خود را افزایش دهید.", 'MarkDown', $message_id, $button);

                                $ref = Invite::where('invited_id', $from_id)->first();
                                if ($ref) {
                                    $user = User::where('ref_id', $ref->inviter_id)->first();
                                    if ($user) {
                                        $user->score += $this->ref_score;
                                        $user->save();
                                        Telegram::sendMessage($user->telegram_id, "■  کاربر [$first_name](tg://user?id=$from_id)  با لینک دعوت شما ثبت نام کرد و $this->ref_score امتیاز به شما اضافه شد!   .", 'MarkDown', null, null);
                                    }
                                }
                            }
                            break;
// edit section
                        case  6:
                            if ($this->check('username', $text, $chat_id, $message_id, $return_button)) {
                                $this->user->step = null;
                                $this->user->username = $text;
                                $this->user->save();
                                Telegram::sendMessage($chat_id, "با موفقیت ویرایش شد!", 'MarkDown', $message_id, $edit_button);

                            }
                            break;
                        case  7:
                            if ($this->check('email', $text, $chat_id, $message_id, $return_button)) {
                                $this->user->step = null;
                                $this->user->email = $text;
                                $this->user->save();
                                Telegram::sendMessage($chat_id, "با موفقیت ویرایش شد!", 'MarkDown', $message_id, $edit_button);

                            }
                            break;
                        case  8:
                            if ($this->check('password', $text, $chat_id, $message_id, $return_button)) {
                                $this->user->password = Hash::make($text);
                                $this->user->step = null;
                                $this->user->save();
                                Telegram::sendMessage($chat_id, "با موفقیت ویرایش شد!", 'MarkDown', $message_id, $edit_button);
                            }
                            break;
                        //send to users
                        case  9:
//                        if (!in_array($from_id, Telegram::LOGS))
//                    return;
                            $send_or_cancel = json_encode(['inline_keyboard' => [
                                [['text' => "ارسال شود✨", 'callback_data' => "send_to_users_ok"]],
                                [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                            ], 'resize_keyboard' => true]);
                            $this->user->step = null;
                            $this->user->save();
                            Storage::put('message.txt', json_encode($message));
                            Telegram::sendMessage($chat_id, "*از ارسال به کاربران اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                            break;
                        //send to groups
                        case  91:
//                        if (!in_array($from_id, Telegram::LOGS))
//                    return;
                            $send_or_cancel = json_encode(['inline_keyboard' => [
                                [['text' => "ارسال شود✨", 'callback_data' => "send_to_chats_ok"]],
                                [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                            ], 'resize_keyboard' => true]);
                            $this->user->step = null;
                            $this->user->save();
                            Storage::put('message.txt', json_encode($message));
                            Telegram::sendMessage($chat_id, "*از ارسال به گروهها اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                            break;
                        //send to groups

                        //get banner button link
                        case  10:
                            $cancel_button = json_encode(['keyboard' => [
                                [['text' => "لغو ❌"]],
                            ], 'resize_keyboard' => true]);
                            if ($text && strlen($text) > 1000) {
                                Telegram::sendMessage($chat_id, "*طول پیام از 1000 حرف کمتر باشد*", 'MarkDown', $message_id, $cancel_button);
                                return;
                            }
                            $this->user->step++;
                            $this->user->save();
                            Storage::put("$from_id.txt", json_encode($message));
                            Telegram::sendMessage($chat_id, "لینک دکمه را وارد کنید", 'MarkDown', $message_id, $cancel_button);

                            break;
                        //get banner button name
                        case  11:
                            $cancel_button = json_encode(['keyboard' => [
                                [['text' => "لغو ❌"]],
                            ], 'resize_keyboard' => true]);
                            if ($text && strlen($text) > 50) {
                                Telegram::sendMessage($chat_id, "*طول لینک از 50 حرف کمتر باشد*", 'MarkDown', $message_id, $cancel_button);
                                return;
                            }
                            $this->user->step++;
                            $this->user->save();
                            $txt = Storage::get("$from_id.txt");
                            Storage::put("$from_id.txt", json_encode(['message' => $txt, 'link' => $text]));
                            Telegram::sendMessage($chat_id, "متن دکمه را وارد کنید", 'MarkDown', $message_id, $cancel_button);

                            break;
                        //send banner
                        case  12:
                            $cancel_button = json_encode(['keyboard' => [
                                [['text' => "لغو ❌"]],
                            ], 'resize_keyboard' => true]);
                            if ($text && strlen($text) > 50) {
                                Telegram::sendMessage($chat_id, "*متن دکمه از 50 حرف کمتر باشد*", 'MarkDown', $message_id, $cancel_button);
                                return;
                            }
                            $this->user->step = null;
                            $this->user->save();
                            $txt = json_decode(Storage::get("$from_id.txt"));
                            Storage::put("$from_id.txt", json_encode(['message' => $txt->message, 'link' => $txt->link, 'name' => $text,]));
                            $this->sendBanner($from_id, Storage::get("$from_id.txt"));
                            Telegram::sendMessage($chat_id, "با موفقیت تولید شد!", 'MarkDown', $message_id, $button);


                            break;
                    }

                }

            } elseif ($tc == 'channel') {

                if ($text && strpos($text, "@boorsaman") == false) {

                    $text .= PHP_EOL . "\xD8\x9C" . "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️";
                    $text .= PHP_EOL . "@boorsaman" . PHP_EOL;
                    $text .= "📉 🅱🅾🅾🆁🆂🅰🅼🅰🅽 📈" . PHP_EOL;

                    Telegram::editMessageText($chat_id, $message_id, $text);
                    Telegram::sendMessage(Telegram::LOGS[0], json_encode($update), null);


                } elseif (($caption && strpos($caption, "@boorsaman" == false)) || $photo || $document || $document || $video || $audio || $voice || $video_note) {

                    if ($caption)
                        $caption .= PHP_EOL . "\xD8\x9C" . "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️";
                    $caption .= PHP_EOL . "@boorsaman" . PHP_EOL;
                    $caption .= "📉 🅱🅾🅾🆁🆂🅰🅼🅰🅽 📈" . PHP_EOL;

                    Telegram::editMessageCaption($chat_id, $message_id, $caption);
                }

//            if (preg_match('/^\/?(add|نصب)$/ui', $text, $match)) {
//                if (!$this->Admin($chat_id, $from_id, $tc, $chat_username))
//                    return;
//                if ($chat_username == '@') {
//                    Telegram::sendMessage($chat_id, "🔹کانال شما باید در حالت  *public* باشد.\n 🔸روی نام کانال کلیک کنید\n 🔸 در تلگرام موبایل از قسمت بالا *آیکن مداد* را انتخاب کنید.\n 🔸در تلگرام دسکتاپ از گزینه سه نقطه بالا گزینه  *Manage Channel* را انتخاب کنید \n\n 🔸 قسمت  *Channel type*  را به حالت *public*  تغییر دهید.\n 🔸سپس یک نام عمومی به کانال خود تخصیص دهید. *ربات کانال شما را توسط این نام شناسایی می کند*. \n 🔼 در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ", 'Markdown', $message_id);
//                    return;
//                }
//
//                $this->user = User::where('channels', 'like', "%\"$chat_username\"%")->first();
//                if (!$this->user) {
//                    Telegram::sendMessage($chat_id, "🔸 ابتدا باید کانال را در ربات ثبت نمایید!\n🔸 *منوی اصلی ⬅ ثبت کانال💥* \n  $this->bot", 'Markdown', $message_id);
//                    return;
//                }
//
//                Telegram::sendMessage($chat_id, "■ ربات با موفقیت نصب شد !💫\n\n● برای تبلیغ کانال  به قسمت دیوار📈 سپس درج کانال/گروه در دیوار📌 مراجعه کنید\n \n آموزش ربات \n $this->tut_link  * $this->bot *    \n ", 'MarkDown', $message_id, $this->button);
//
//
//            }
            } elseif
            ($tc == 'supergroup' || $tc == 'group') {


//            if (preg_match('/^\/?(add|نصب)$/ui', $text, $match)) {
//
//                if (!$this->Admin($chat_id, $from_id, $tc, $chat_username))
//                    return;
//                if (!$this->Admin($chat_id, $this->bot_id, $tc, $chat_username)) {
//                    Telegram::sendMessage($chat_id, "🔹*ابتدا ربات را در گروه ادمین کنید و مجدد تلاش نمایید*", 'Markdown', $message_id);
//                    return;
//                }
//                if ($chat_username == '@') {
//                    Telegram::sendMessage($chat_id, "🔹کانال شما باید در حالت  *public* باشد.\n 🔸روی نام کانال کلیک کنید\n 🔸 در تلگرام موبایل از قسمت بالا *آیکن مداد* را انتخاب کنید.\n 🔸در تلگرام دسکتاپ از گزینه سه نقطه بالا گزینه  *Manage Channel* را انتخاب کنید \n\n 🔸 قسمت  *Channel type*  را به حالت *public*  تغییر دهید.\n 🔸سپس یک نام عمومی به کانال خود تخصیص دهید. *ربات کانال شما را توسط این نام شناسایی می کند*. \n 🔼 در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ", 'Markdown', $message_id);
//                    return;
//                }
//                $this->user = User::where('groups', 'like', "%\"$chat_username\"%")->first();
//                if (!$this->user) {
//                    Telegram::sendMessage($chat_id, "🔸 ابتدا باید گروه را در ربات ثبت نمایید!\n🔸 *منوی اصلی ⬅ ثبت گروه💥* \n  $this->bot", 'Markdown', $message_id);
//                    return;
//                }
//
//                Telegram::sendMessage($chat_id, "🔷 *ربات با موفقیت نصب شد. اکنون می توانید گروه خود را در دیوار ربات تبلیغ نمایید!* \n \n آموزش ربات \n $this->tut_link  $this->info", 'MarkDown', $message_id, $this->button);
//
//
//            }
                if ($new_chat_member && ($chat_username == "@lamassaba" || $chat_username == "@magnetgramsupport" || $chat_username == "@magnetgramadvs")) {
                    $txt = "سلام $first_name\n";
                    $link = "https://t.me/" . str_replace("@", "", $this->bot);
                    $buttons = [[['text' => '👈 دانلود اپلیکیشن 👉', 'url' => $this->app_link]], [['text' => '👈 ورود به ربات 👉', 'url' => $link]]];
                    $txt .= " 🔔 " . "  📌ربات بورسی دبل چین \n💫 منتخب بهترین سیگنال های بورس  \n دبل چین 👑 همیار بورسی شما " . " \n👇👇👇 لینک ربات و اپلیکیشن 👇👇👇  \n" . "  \n$link \n\n";


                    Telegram::deleteMessage($chat_id, $message_id);
                    Telegram::sendMessage($chat_id, $txt, null, null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), true);


                }

//            elseif ($new_chat_members) {
//
//            } //
//            elseif ($left_chat_member) {
//
//
//            }
            }
            if ($text == "/start$this->bot") {
                Telegram::deleteMessage($chat_id, $message_id);
                $buttons = [[['text' => '👈 ورود به ربات 👉', 'url' => "https://t.me/" . str_replace("@", "", $this->bot)]]];
//                Telegram::sendMessage($chat_id, " $first_name " . "  \n برای دریافت آخرین سیگنال های بورسی و دریافت اپلیکیشن، وارد ربات شوید.", "Markdown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), true);

            }
            if ($text == 'بنر' || $Data == 'بنر' || $text == "📌 دریافت بنر تبلیغاتی 📌") {
                $this->user = User::where('telegram_id', $from_id)->first();
                if (!$this->user) {
                    Telegram::sendMessage($chat_id, "برای دریافت بنر ابتدا در ربات ثبت نام کنید\n $this->bot", null, $message_id, null);
                    return;
                }
                if ($tc == 'private') {
                    Telegram::sendMessage($from_id, "بنر زیر را فوروارد کنید و در صورت ثبت نام افراد دعوت شده, $this->ref_score امتیاز دریافت نمایید. ", "Markdown", null, null, true);

                }
                $ref_link = "https://t.me/" . str_replace("@", "", $this->bot) . "?start=" . base64_encode("$from_id");
                $buttons = [[['text' => '👈 دانلود اپلیکیشن 👉', 'url' => $this->app_link]], [['text' => '👈 ورود به ربات 👉', 'url' => $ref_link]]];
                Telegram::sendMessage($chat_id, " 🔔 " . "  📌*ربات دبل چین* \n💫 *ما در دبل چین تلاش می کنیم بازار میوه و تره بار اینترنتی را با قیمت مناسب و بدون واسطه برای تمام مردم ایران فراهم کنیم  \n دبل چین 👑 بازار میوه تره بار آنلاین ایرانیان " . " \n👇👇👇 لینک ربات و اپلیکیشن 👇👇👇  \n" . "  \n$ref_link \n\n" . "$this->bot", null, null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), false);

            }
//referral & connect
            if ((strpos($text, "/start ") !== false) || ($tc != 'private' && strpos($text, "?start=admin") !== false)) {
                // agar ebarate /start ersal shod
                $this->user = Admin::where('telegram_id', $from_id)->first();
                $this->user = $this->user ?? User::where('telegram_id', $from_id)->first();
                $button = json_encode(['keyboard' => [
                    in_array($from_id, Telegram::LOGS) ? [['text' => 'پنل مدیران🚧']] : [],

                    [['text' => "📱 دریافت اپلیکیشن 📱"]],
                    [['text' => "🎴 ساخت دکمه شیشه ای 🎴"]],
                    [['text' => "📌 دریافت بنر تبلیغاتی 📌"]],
                    [['text' => 'امتیاز من💰']],


                    [['text' => $this->user ? "ویرایش اطلاعات✏" : "ثبت نام✅"]],
                    [['text' => 'درباره ربات🤖']],
                ], 'resize_keyboard' => true]);

                if ($this->user) Telegram::sendMessage($chat_id, "■ سلام $first_name خوش آمدید✋\n\n■ چه کاری براتون انجام بدم؟ ", null, $message_id, $button);
                else Telegram::sendMessage($chat_id, "■ سلام $first_name خوش آمدید✋  ", null, $message_id, $button);
                Telegram::sendMessage($chat_id, " 🔔 " . "  📌*ربات دبل چین* \n💫 *نخستین بازار مجازی میوه و تره بار  \n دبل چین 👑 بازار میوه ایرانیان " . " \n👇👇👇 لینک ربات و اپلیکیشن 👇👇👇  \n" . " \n" . "$this->bot", null, null, $button, false);


                Telegram::logAdmins("■  کاربر [$first_name](tg://user?id=$from_id) ربات دبل چین را استارت کرد.", 'MarkDown');
                if ($tc == 'private')
                    $code = substr($text, 7); // joda kardan id kasi ke rooye linke davatesh click shode
//            Telegram::sendMessage($chat_id, $code);
                else
                    $code = explode('?start=', $text)[1];

                if (!empty($code)) {

                    if (str_starts_with($code, 'admin') || str_starts_with($code, 'user')) { //connect to telegram
                        if (str_starts_with($code, 'admin'))
                            $user = Admin::where('remember_token', str_replace_first('admin', '', $code))->first();
                        else
                            $user = User::where('remember_token', str_replace_first('admin', '', $code))->first();
                        if ($user) {
                            $this->user = $user;
                            $user->remember_token = null;
                            $user->telegram_id = $chat_id;
                            $user->save();
                            $button = json_encode(['keyboard' => [
                                in_array($from_id, Telegram::LOGS) ? [['text' => 'پنل مدیران🚧']] : [],

                                [['text' => "📱 دریافت اپلیکیشن 📱"]],
                                [['text' => "🎴 ساخت دکمه شیشه ای 🎴"]],
                                [['text' => "📌 دریافت بنر تبلیغاتی 📌"]],
                                [['text' => 'امتیاز من💰']],


                                [['text' => $this->user ? "ویرایش اطلاعات✏" : "ثبت نام✅"]],
                                [['text' => 'درباره ربات🤖']],
                            ], 'resize_keyboard' => true]);
                            Telegram::sendMessage($chat_id, "\n🔔\nتبریک!" . " [$first_name](tg://user?id=$from_id)  " . " عزیز، با موفقیت به تلگرام متصل شدید", "Markdown", null, $tc == "private" ? $button : null, false);
                            if ($tc == 'private')
                                Telegram::logAdmins("\n🔔\nیک اکانت به تلگرام متصل شد " . " [$first_name](tg://user?id=$chat_id)  ", "Markdown", null, null, false);
                            else
                                Telegram::logAdmins("\n🔔\nیک گروه به تلگرام متصل شد " . " [$first_name](tg://user?id=$chat_id)  ", "Markdown", null, null, false);
                        } else {
                            Telegram::sendMessage($from_id, __('user_not_found'), null, null, null, false);

                        }
                    } else { //referral
                        return;
                        $ref = $code;
                        $u = User::where('ref_id', $ref)->first();
                        if ($ref != $user->ref_id && $u) {
                            Invite::updateOrCreate(['invited_id' => $from_id], ['invited_id' => $from_id, 'inviter_id' => $u->ref_id]);
                            Telegram::sendMessage($ref, "\n🔔\nهم اکنون" . " [$first_name](tg://user?id=$from_id)  " . " با لینک دعوت شما وارد ربات شد. در صورت ثبت نام$this->ref_score امتیاز به شما اضافه خواهد شد   ", "Markdown", null, null, false);
                        }
                    }
                }

            }
            if ($reply) {
//                sendTelegramMessage($from_id, json_encode($reply), null, null);

                $repText = $reply->text;
                if ($repText) {

                    if (str_starts_with($repText, 'ip:')) {
                        $tmp = explode("\n", $repText);
                        if (count($tmp) > 1) {
                            $pusherChannel = $tmp[0];
                            $txt = $tmp[1];
                            if ($pusherChannel && str_contains($pusherChannel, 'ip:')) {
                                $ip = str_replace('ip:', '', $pusherChannel);
                                $t = Carbon::now()->timestamp;

                                event(new ChatEvent('support' . $chat_id, $ip, $text, $ip, $t));

                            }
                        }

                    }
                }
            }

//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
//        unlink("error_log");
        } catch (\Exception $e) {
            Telegram::sendMessage(Telegram::LOGS[0], print_r([$e->getMessage(), $e->getLine()], true));
        }
    }


    private
    function check($what, $text, $chat_id, $message_id, $cancel_button)
    {
        $message = null;
        if ($what == 'username') {
            if (strlen($text) < 6)
                $message = "نام کاربری  حداقل  ۶ حرف باشد";
            elseif (strlen($text) > 50)
                $message = "نام کاربری  حداکثر 50 حرف باشد";
            elseif (User::where("username", $text)->exists())
                $message = "نام کاربری تکراری است";
        } else if ($what == 'password') {
            if (strlen($text) < 6)
                $message = "طول گذرواژه حداقل ۶";
            elseif (strlen($text) > 50)
                $message = "طول گذرواژه حداکثر 50";

        } else if ($what == 'email') {

            if (!filter_var($text, FILTER_VALIDATE_EMAIL))
                $message = "ایمیل نامعتبر است";

            else if (User::where('email', $text)->exists())
                $message = "این ایمیل از قبل ثبت شده است!";


        }

        if ($message) {
            Telegram::sendMessage($chat_id, $message, 'MarkDown', $message_id, $cancel_button);
            return false;
        } else {
            return true;
        }

    }


    private
    function sendBanner($chat_id, $storage)
    {


        $storage = json_decode($storage);
        $message = json_decode($storage->message);
        $link = $storage->link;
        $name = $storage->name;
        $poll = $message->poll;
        $text = $message->text;
        $sticker = $message->sticker;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
        $animation = $message->animation;  #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,

        $photo = $message->photo; # file_id,file_unique_id,file_size,width,height] array of different photo sizes
        $document = $message->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
        $video = $message->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $audio = $message->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $voice = $message->voice; #duration,mime_type,file_id,file_unique_id,file_size
        $video_note = $message->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $caption = $message->caption;


        $buttons = [[['text' => "👈 $name 👉", 'url' => $link]]];

        $keyboard = json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]);
        Storage::put("log.txt", $text);

        if ($text)
            Telegram::creator('SendMessage', [
                'chat_id' => $chat_id,
                'text' => $text /*. "\n $this->bot"*/,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($photo)
            Telegram::creator('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo[count($photo) - 1]->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($audio)
            Telegram::creator('sendAudio', [
                'chat_id' => $chat_id,
                'audio' => $audio->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'duration' => $audio->duration,
                'performer' => $audio->performer,
                'title' => $audio->title,
                'thumb' => $audio->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($document)
            Telegram::creator('sendDocument', [
                'chat_id' => $chat_id,
                'document' => $document->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $document->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($video)
            Telegram::creator('sendVideo', [
                'chat_id' => $chat_id,
                'video' => $video->file_id,
                'duration' => $video->duration,
                'width' => $video->width,
                'height' => $video->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $video->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($animation)
            Telegram::creator('sendAnimation', [
                'chat_id' => $chat_id,
                'animation' => $animation->file_id,
                'duration' => $animation->duration,
                'width' => $animation->width,
                'height' => $animation->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $animation->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($voice)
            Telegram::creator('sendVoice', [
                'chat_id' => $chat_id,
                'voice' => $voice->file_id,
                'duration' => $voice->duration,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($video_note)
            Telegram::creator('sendVideoNote', [
                'chat_id' => $chat_id,
                'video_note' => $video_note->file_id,
                'duration' => $video_note->duration,
                'length' => $video_note->length,
                'thumb' => $video_note->thumb,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($sticker)
            Telegram::creator('sendSticker', [
                'chat_id' => $chat_id,
                'sticker' => $sticker->file_id,
                "set_name" => "DaisyRomashka",
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($poll)
            Telegram::creator('sendPoll', [
                'chat_id' => $chat_id,
                'question' => "",
                'options' => json_encode(["1", "2", "3"]),
                'type' => "regular",//quiz
                'allows_multiple_answers' => false,
                'correct_option_id' => 0, // index of correct answer for quiz
//            'open_period' => 5-600,   this or close_date
//            'close_date' => 5, 5 - 600,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);

//        Storage::delete("$chat_id.txt");
    }

    private
    function createChatImage($photo, $chat_id)
    {
        if (!isset($photo) || !isset($photo->big_file_id)) return;
        $client = new \GuzzleHttp\Client();
        $res = Telegram::creator('getFile', [
            'file_id' => $photo->big_file_id,

        ])->result->file_path;

        $image = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $res;
        Storage::put("public/chats/$chat_id.jpg", $client->get($image)->getBody());

    }

    static function creator($method, $datas = [])
    {
        $url = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN', '') . "/" . $method;

        $res = Http::asForm()->post($url, $datas);
        if ($res->status() != 200)
            Telegram::sendMessage(Telegram::LOGS[0], $res->body() . PHP_EOL . print_r($datas, true));
        return json_decode($res->body());


    }

    private
    function createUserImage($user_id)
    {

        $client = new \GuzzleHttp\Client();
        $res = Telegram::creator('getUserProfilePhotos', [
            'user_id' => $user_id,

        ])->result->photos;
        // return json_encode($res);
        if (!isset($res) || count($res) == 0) return;
        $res = Telegram::creator('getFile', [
            'file_id' => $res[0][count($res[0]) - 1]->file_id,

        ])->result->file_path;

        $image = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $res;
        Storage::put("public/users/$user_id.jpg", $client->get($image)->getBody());

    }

}
