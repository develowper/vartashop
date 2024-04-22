<?php

namespace App\Http\Helpers;


use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;


class SmsHelper
{
    const  APIKey = "75f3092e3ed8167d7edd092e";
    const  SecretKey = "beheshti009351414815";
    const  LineNumber = "50002015700313";


    public static function StringRandom($length = 16)
    {
        $pool = '0123456789';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
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


    public function Send($number, $code)
    {
        try {
            date_default_timezone_set("Asia/Tehran");
            $APIKey = self::APIKey;
            $SecretKey = self::SecretKey;
            // message data
            $data = array(
                "ParameterArray" => array(
                    array(
                        "Parameter" => "VerificationCode",
                        "ParameterValue" => $code
                    )
                ),
                "Mobile" => $number,
                "TemplateId" => "80451"
            );

            $SmsIR_UltraFastSend = (new SmsIR_UltraFastSend($APIKey, $SecretKey))->UltraFastSend($data);
            return $SmsIR_UltraFastSend;
        } catch (Exception $e) {
//            echo 'Error SendMessage : ' . $e->getMessage();
            return false;
        }
    }

}
