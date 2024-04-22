<?php

namespace App\Http\Helpers;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;

class Pay
{
    const BANK_DEFAULT = 'zarinpal';
    const LINKS = [
        'nextpay' => [
            'PAY' => 'https://nextpay.org/nx/gateway/payment/',
            'TOKEN' => 'https://nextpay.org/nx/gateway/token',
            'VERIFY' => 'https://nextpay.org/nx/gateway/verify',

        ],
    ];
    const ERROR_MESSAGE = 'مشکلی در پرداخت پیش آمد';
    const ERROR_CONFIRM_MESSAGE = 'مشکلی در تایید پرداخت پیش آمد';

    static function makeUri($orderId, $price, $payerName, $phone, $mail, $description, $userId, $bank = null)
    {
        $bank = $bank ?? Variable::$BANK ?? self::BANK_DEFAULT;

//        $data = (object)$data;
        try {
            switch ($bank) {
                case    'nextpay':
                    $params = [
                        'api_key' => env('NEXPAY_TOKEN'),
                        'order_id' => $orderId,
                        'amount' => $price,
                        'callback_uri' => env('APP_URL') . "/api/payment/done",
                        'currency' => 'IRR',
                        'customer_phone' => $phone,
                        'payer_name' => $payerName,
                        'auto_verify' => false,
                        'custom_json_fields' => json_encode(['user_id' => $userId, 'type' => $description,]),];
                    try {
                        $response = Http::post(self::LINKS['nextpay']['TOKEN'], $params);
                    } catch (Exception $e) {
                        return ['status' => 'danger', 'message' => self::ERROR_MESSAGE];

                    }
                    $response = $response->object() ?? null;

                    if ($response && $response->code == -1) { //send user to bank page
                        return ['status' => 'success', 'order_id' => $orderId, 'url' => self::LINKS['nextpay']['PAY'] . $response->trans_id];

                    } else {
                        if (!$response)
                            return ['status' => 'danger', 'message' => self::ERROR_MESSAGE];

                        $message = self::MESSAGES['nextpay'][$response->code];

                        return ['status' => 'danger', 'message' => $message];


                    }
                    break;
                case 'payping':
                    $response = Http::withHeaders(['authorization' => 'Bearer ' . env('PAYPING_TOKEN'), 'Content-Type' => 'application/json',])
                        ->post("https://api.payping.ir/v2/pay",
                            [
                                'clientRefId' => $orderId,
                                'Amount' => $price,
                                'ReturnUrl' => env('APP_URL') . "/api/payment/done",
                                'payerName' => $payerName,
                                'payerIdentity' => $phone,
                                'mail' => $mail,
                                'description' => $description,
                            ]);

                    $data = json_decode($response->body());
                    if ($response->status() == 200)
                        return ['status' => 'success', 'order_id' => $orderId, 'url' => "https://api.payping.ir/v2/pay/gotoipg/$data->code"];
                    elseif ($response->status() == 400)
                        return ['status' => 'danger', 'message' => $data];
                    else
                        return ['status' => 'danger', 'message' => $response->status()];
                    break;
                case 'zarinpal':
                    $data = array(
                        "merchant_id" => env('ZARINPAL_TOKEN'),
                        "amount" => $price,
                        "callback_url" => env('APP_URL') . "/api/payment/done",
                        "description" => $description,
                        "mobile" => $phone,
                        "email" => $mail,
                        "order_id" => "$orderId",
//                        "metadata" => ["order_id" => $orderId, "email" => $mail, 'fullname' => $payerName, "mobile" => $phone],
                    );

                    $response = Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json', /*,'Content-Length' => strlen(json_encode($data)),*/])
                        ->withUserAgent('ZarinPal Rest Api v4')
                        ->post("https://api.zarinpal.com/pg/v4/payment/request.json",
                            $data);
                    $result = json_decode($response->body(), true);
                    if (empty($result['errors']) && $result['data']['code'] == 100)
                        return ['status' => 'success', 'order_id' => $result['data']["authority"], 'url' => "https://www.zarinpal.com/pg/StartPay/" . $result['data']["authority"]];
                    elseif (!empty($result['errors']))
                        return ['status' => 'danger', 'message' => $result['errors']['message']];
                    else
                        return ['status' => 'danger', 'message' => $response->status()];
                    break;
            }
        } catch (\Exception $e) {
            return ['status' => 'danger', 'message' => 'مشکلی در دریافت لینک پرداخت پیش آمد'];
        }

    }

    static function confirmPay($request, $bank = null)
    {
        $bank = $bank ?? Variable::$BANK ?? self::BANK_DEFAULT;
//        $data = (object)$data;
        try {
            switch ($bank) {
                case 'nextpay':
                    if (isset($request->np_status)) {
                        if ($request->np_status == 'Unsuccessful') {
                            return ['status' => 'danger', 'message' => self::ERROR_CONFIRM_MESSAGE];
                        } else {
                            $params = [
                                'api_key' => env('NEXPAY_TOKEN'),
                                'trans_id' => $request->trans_id,
                                'amount' => $request->amount,
                                'currency' => 'IRR',
                            ];
                            if (!Payment::where("order_id", $request->order_id)->exists())
                                return ['status' => 'danger', 'message' => self::ERROR_CONFIRM_MESSAGE];


                            $response = Http::post(self::LINKS['nextpay']['VERIFY'], $params);
                            $response = $response->object() ?? null;

                            if (!$response) return ['status' => 'danger', 'message' => self::ERROR_CONFIRM_MESSAGE];
                            if ($response && $response->code == 0) { //verify success
                                return ['status' => 'success', 'order_id' => $response->order_id, 'info' => json_encode($response)];
                            }
                            return ['status' => 'danger', 'message' => self::ERROR_CONFIRM_MESSAGE];
                        }
                    }
                    break;
                case 'idpay':
                    $response = Http::withHeaders(['X-API-KEY' => env('IDPAY_TOKEN'), 'Content-Type' => 'application/json',])
                        ->post(
                            "https://api.idpay.ir/v1.1/payment/verify",
                            [
                                'id' => $request->id,
                                'order_id' => $request->order_id,
                            ]
                        );
                    break;
                case 'zarinpal':
                    $result = [];
                    if ($request && $request->Status == 'OK') {
                        $data = array(
                            "merchant_id" => env('ZARINPAL_TOKEN'),
                            "amount" => (Transaction::where('pay_id', $request->Authority)->sum('amount') ?? 0) * 10,
                            "authority" => $request->Authority,
                        );

                        $response = Http::withHeaders(['Content-Type' => 'application/json', 'Accept' => 'application/json',])
                            ->withUserAgent('ZarinPal Rest Api v4')
                            ->post('https://api.zarinpal.com/pg/v4/payment/verify.json',
                                $data);
                        $result = json_decode($response->body(), true);
                    }
                    if (empty($result['errors']) && $result['data']['code'] == 100)
                        return ['status' => 'success', 'order_id' => $request->Authority, 'info' => $response->body()];
                    if (empty($result['errors']) && $result['data']['code'] == 101)
                        return ['status' => 'danger', 'message' => __('factor_payed_before')];
                    elseif (!empty($result['errors']))
                        return ['status' => 'danger', 'message' => $result['errors']['message']];
                    else
                        return ['status' => 'danger', 'message' => $result['data'] ?? $result];
                    break;
            }
        } catch (\Exception $e) {
            return ['status' => 'danger', 'message' => 'مشکلی در دریافت لینک پرداخت پیش آمد'];
        }

    }

    const MESSAGES = [
        'nextpay' => [
            0 => 'پرداخت تکمیل و با موفقیت انجام شده است',
            -1 => 'منتظر ارسال تراکنش و ادامه پرداخت',
            -2 => 'پرداخت رد شده توسط کاربر یا بانک',
            -3 => 'پرداخت در حال انتظار جواب بانک',
            -4 => 'پرداخت لغو شده است',
            -20 => 'کد api_key ارسال نشده است',
            -21 => 'کد trans_id ارسال نشده است',
            -22 => 'مبلغ ارسال نشده',
            -23 => 'لینک ارسال نشده',
            -24 => 'مبلغ صحیح نیست',
            -25 => 'تراکنش قبلا انجام و قابل ارسال نیست',
            -26 => 'مقدار توکن ارسال نشده است',
            -27 => 'شماره سفارش صحیح نیست',
            -28 => 'مقدار فیلد سفارشی [custom_json_fields] از نوع json نیست',
            -29 => 'کد بازگشت مبلغ صحیح نیست',
            -30 => 'مبلغ کمتر از حداقل پرداختی است',
            -31 => 'صندوق کاربری موجود نیست',
            -32 => 'مسیر بازگشت صحیح نیست',
            -33 => 'کلید مجوز دهی صحیح نیست',
            -34 => 'کد تراکنش صحیح نیست',
            -35 => 'ساختار کلید مجوز دهی صحیح نیست',
            -36 => 'شماره سفارش ارسال نشد است',
            -37 => 'شماره تراکنش یافت نشد',
            -38 => 'توکن ارسالی موجود نیست',
            -39 => 'کلید مجوز دهی موجود نیست',
            -40 => 'کلید مجوزدهی مسدود شده است',
            -41 => 'خطا در دریافت پارامتر، شماره شناسایی صحت اعتبار که از بانک ارسال شده موجود نیست',
            -42 => 'سیستم پرداخت دچار مشکل شده است',
            -43 => 'درگاه پرداختی برای انجام درخواست یافت نشد',
            -44 => 'پاسخ دریافت شده از بانک نامعتبر است',
            -45 => 'سیستم پرداخت غیر فعال است',
            -46 => 'درخواست نامعتبر',
            -47 => 'کلید مجوز دهی یافت نشد [حذف شده]',
            -48 => 'نرخ کمیسیون تعیین نشده است',
            -49 => 'تراکنش مورد نظر تکراریست',
            -50 => 'حساب کاربری برای صندوق مالی یافت نشد',
            -51 => 'شناسه کاربری یافت نشد',
            -52 => 'حساب کاربری تایید نشده است',
            -60 => 'ایمیل صحیح نیست',
            -61 => 'کد ملی صحیح نیست',
            -62 => 'کد پستی صحیح نیست',
            -63 => 'آدرس پستی صحیح نیست و یا بیش از ۱۵۰ کارکتر است',
            -64 => 'توضیحات صحیح نیست و یا بیش از ۱۵۰ کارکتر است',
            -65 => 'نام و نام خانوادگی صحیح نیست و یا بیش از ۳۵ کاکتر است',
            -66 => 'تلفن صحیح نیست',
            -67 => 'نام کاربری صحیح نیست یا بیش از ۳۰ کارکتر است',
            -68 => 'نام محصول صحیح نیست و یا بیش از ۳۰ کارکتر است',
            -69 => 'آدرس ارسالی برای بازگشت موفق صحیح نیست و یا بیش از ۱۰۰ کارکتر است',
            -70 => 'آدرس ارسالی برای بازگشت ناموفق صحیح نیست و یا بیش از ۱۰۰ کارکتر است',
            -71 => 'موبایل صحیح نیست',
            -72 => 'بانک پاسخگو نبوده است لطفا با نکست پی تماس بگیرید',
            -73 => 'مسیر بازگشت دارای خطا میباشد یا بسیار طولانیست',
            -90 => 'بازگشت مبلغ بدرستی انجام شد',
            -91 => 'عملیات ناموفق در بازگشت مبلغ',
            -92 => 'در عملیات بازگشت مبلغ خطا رخ داده است',
            -93 => 'موجودی صندوق کاربری برای بازگشت مبلغ کافی نیست',
            -94 => 'کلید بازگشت مبلغ یافت نشد',
        ]
    ];
}
