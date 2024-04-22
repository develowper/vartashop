<?php

namespace App\Http\Helpers;


use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;


class SMSHelper
{
    private $apiKey, $client, $server_number, $register_pattern, $forget_password_pattern;

    public function __construct()
    {
//        $this->register_pattern = 'i5gqousv1o5ndev'; //hamsignal
//        $this->forget_password_pattern = '43z2ci5l612ro0x';
//        $this->register_pattern = '50ybp75b5mlrjx8'; //vartashop
        $this->register_pattern = 'bttts8dws9c6hqz'; //varta
        $this->forget_password_pattern = '9s8sh1c0ukq2e3m';
        $this->server_number = '+985000125475';
//        $this->server_number = '+983000505';
//        $this->server_number = '+9850004150400040';
//        $this->server_number = '+9850004150001232';
        $this->apiKey = env('SMS_API');

        $this->client = new \IPPanel\Client($this->apiKey);

    }


    public static function deleteCode(mixed $phone)
    {
        DB::table('sms_verify')->where('phone', $phone)->delete();
    }

    public static function addCode($phone, $code)
    {
        self::deleteCode($phone);
        DB::table('sms_verify')->insert(
            ['code' => $code, 'phone' => $phone]
        );
    }


    public static function checkRepeatedSMS($phone, $min)
    {
        return DB::table('sms_verify')->where('phone', $phone)->where('created_at', '>', Carbon::now()->subMinutes($min))->exists();
    }

    const URL = "https://ippanel.com/services.jspd";

    public static function createCode($phone)
    {
        $code = Util::generateRandomNumber(5);
        return DB::table('sms_verify')->insert(
            ['code' => $code, 'phone' => $phone]
        );
    }

    public function send($to, $msg, $cmnd = 'register')
    {
//        if ($to == "09018945844" || $to == "9018945844") return;
        $name = "ورتا شاپ";
        $pattern = $this->register_pattern;
        $code = null;

        $patternVariables = [
            "name" => "string",
            "code" => "integer",
        ];
        if ($cmnd == 'forget') {
            $pattern = $this->forget_password_pattern;
            $send = "رمز یکبار مصرف: " . "%code%" . PHP_EOL . "%name%";
        } else {

            $send = "خوش آمدید: کد تایید شما " . "%code%" . PHP_EOL . "%name%";
        }
//
//        try {
//            $code = $this->client->createPattern("$send", "otp $msg send to $to",
//                $patternVariables, '%', False);
//        } catch (\IPPanel\Errors\Error $e) {
//            echo $e->getMessage();
//        } catch (\IPPanel\Errors\HttpException $e) {
//            echo $e->getMessage();
//        }
//        echo $code;
        $patternValues = [
//            "name" => $name,
            "code" => "$msg",
        ];
//        if ($code) {
        $messageId = null;
        try {
            $messageId = $this->client->sendPattern(
                "$pattern",    // pattern code
                $this->server_number,      // originator
                "$to",  // recipient
                $patternValues  // pattern values
            );
        } catch (\IPPanel\Errors\Error $e) {
            Telegram::sendMessage(Telegram::LOGS[0], $e->getMessage());
        } catch (\IPPanel\Errors\HttpException $e) {
            Telegram::sendMessage(Telegram::LOGS[0], $e->getMessage());


        }
//        Telegram::sendMessage(Helper::$logs[0], $messageId);

//        echo $messageId;
//        }
        return (bool)$messageId;
    }


    public function getCredit($type)
    {
        if ($type == 'sms.ir')
            return (new SmsIR_UltraFastSend())->getCredit();
        return $this->client->getCredit();
    }

    /**
     * @param $messageId string returns from send
     * @return object
     */
    public function getMessageInfo($messageId)
    {
        try {
            $message = $this->client->getMessage($messageId);
        } catch (\IPPanel\Errors\Error $e) {

        } catch (\IPPanel\Errors\HttpException $e) {
        }
        // get message status
        // get message cost
        // get message payback
        return (object)['state' => $message->state, 'cost' => $message->cost, 'returnCost' => $message->returnCost];

    }

    public function getMessageStatus($messageId)
    {
        $statuses = [];
        $paginationInfo = (object)['total' => 0];
        try {
            list($statuses, $paginationInfo) = $this->client->fetchStatuses($messageId, 0, 10);
        } catch (\IPPanel\Errors\Error $e) {
        } catch (\IPPanel\Errors\HttpException $e) {
        }
        return ['total' => $paginationInfo->total, 'statuses' => $statuses];


//        foreach ($statuses as status)
//          $status->recipient, $status->status
//

    }

    public function smsIR($number, $code)
    {
        try {
            date_default_timezone_set("Asia/Tehran");


            // message data
            $data = array(
                "ParameterArray" => array(
                    array(
                        "Parameter" => "VerificationCode",
                        "ParameterValue" => $code
                    )
                ),
                "Mobile" => $number,
                "TemplateId" => "56441"
            );
            $data2 = array(
                "ParameterArray" => array(
                    array(
                        "Parameter" => "VerificationCode",
                        "ParameterValue" => $code
                    )
                ),
                "Mobile" => $number,
                "TemplateId" => "79949"
            );

            $SmsIR_UltraFastSend = new SmsIR_UltraFastSend();
            $SmsIR_UltraFastSend->UltraFastSend($data2);
            return true;
        } catch (Exception $e) {
            echo 'Error SendMessage : ' . $e->getMessage();
            return false;
        }
    }
}
