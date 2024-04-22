<?php

namespace App\Http\Controllers;

use App\eblagh\Helpers\Helper;
use App\Http\Helpers\Pay;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Car;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;

class TransactionController extends Controller
{
    private $bazaar_client_id;
    private $bazaar_client_secret;
    private $myket_access_token;

    public function __construct()
    {
        $this->bazaar_client_id = env('EBLAGH_BAZAAR_CLIENT_ID');
        $this->bazaar_client_secret = env('EBLAGH_BAZAAR_CLIENT_SECRET');

        $this->myket_access_token = env('EBLAGH_MYKET_ACCESS_TOKEN');
    }

    public function payDone(Request $request)
    {
        $p = null;
        $status = 'error';
        $lang = 'en';
        $user_id = $request->user_id;
        $market = $request->market;
        $token = $request->token;
        $sku = $request->sku;
        $amount = $request->amount;
        $appVersion = $request->app_version;
        $info = $request->info;
        $order_id = $request->order_id ?? "$user_id\$$sku\$" . Carbon::now()->getTimestampMs();


        if (isset($market) && in_array($market, ['bazaar', 'myket'])) {

            if (!$this->checkPayment($sku, $token, $market)) {
                $response['status'] = 'danger';
                $response['message'] = 'اطلاعات ارسالی خرید صحیح نیست!';
                return $response;
            }
            $t = Transaction::create([

            ]);

            if ($t) {
                $status = 'success';
                $user = User::find($user_id);

            }
            return response()->json(['status' => $status]);
        } else {

            $response = Pay::confirmPay($request);

//            Telegram::sendMessage(Telegram::LOGS[0], print_r(Transaction::first()));
            $transactions = (!empty($response) && $response['status'] == 'success') ? Transaction::where('pay_id', $response['order_id'])->get() : collect([]);
            $now = Carbon::now();
            foreach ($transactions as $transaction) {
                $transaction->info = $response['info'];
                $transaction->payed_at = $now;
                $status = 'success';
                $token = $response['order_id'];
                $user = Variable::TRANSACTION_MODELS[$transaction->from_type]::select('id', 'fullname', 'phone')->find($transaction->from_id);
                $user_id = $transaction->user_id;
                if ($transaction->for_type == 'order') {
                    Order::where('id', $transaction->for_id)->update(['payed_at' => $now, 'status' => 'processing']);
                }
                $transaction->save();
                $transaction->user = $user;
                Telegram::log(null, 'transaction_created', $transaction);
            }

            return Inertia::render('Invoice', [
                'lang' =>
                    [
                        'title' => $response['status'] == 'success' ? __('payment_success') : __('payment_fail'),
                        'pay_id' => __('pay_id'),
                        'pay_time' => __('time'),
                        'pay_type' => __('pay_type'),
                        'amount' => __('amount'),
                        'return' => __('return'),
                        'currency' => __('currency'),
                    ],
                'now' => Jalalian::now()->format('%A, %d %B %Y ⏰ H:i'),
                'status' => $response['status'] ?? 'danger',
                'pay_id' => $token ?? '_',
                'amount' => $transactions->sum('amount') ?? '_',
                'type' => $transactions->count() > 0 ? (__('order') . " " . $transactions->pluck('for_id')->join(',')) : '_',
                'link' => url(''),
                'message' => $response['message'] ?? '',
            ]);

        }


    }

    private function getCafeBazaarDiscountPayload($data)
    {
        $payload = [
            'price' => $data['price'],
            'package_name' => $data['package'],
            'sku' => $data['sku'],
            'exp' => Carbon::now()->addDays(30)->timestamp,
            'nonce' => random_int(100000, 9999999999),
//            'account_id' => ''
        ];

        $enc = JWT::encode($payload, env('EBLAGH_BAZAAR_JWT'), 'HS256');

        return $enc;

    }

    public function checkPayment($productId, $purchaseToken, $market)
    {
        if ($market == 'bazaar')
            return $this->checkCafePayment($productId, $purchaseToken);
        if ($market == 'myket')
            return $this->checkMyketPayment($productId, $purchaseToken);
    }

    public function checkMyketPayment($productId, $purchaseToken)
    {
//        $this->cafRefresh();
//        $setting = Setting::first();
        $response = Http::withHeaders([
            'X-Access-Token' => env('EBLAGH_MYKET_ACCESS_TOKEN')
        ])
            ->get("https://developer.myket.ir/api/applications/" . Helper::$PACKAGE . "/purchases/products/$productId/tokens/$purchaseToken");
        if ($response->status() == 200) {
            return true;
        } else {
            return false;
        }
    }

    public function checkCafePayment($productId, $purchaseToken)
    {
        $accessToken = $this->getBazaarToken(new Request());
        $response = Http::withHeaders([
            'access_token' => "$accessToken"
        ])
            ->get('https://pardakht.cafebazaar.ir/devapi/v2/api/validate/' . Helper::$PACKAGE . '/inapp/' . $productId . '/purchases/' . $purchaseToken . '/?access_token=' . $accessToken);

        if ($response->status() == 200) {
            return true;
        } else {
            return false;
        }
    }

    public function getFirstBazaarToken($code)
    {

        $redirect = "https://qr-image-creator.com/hamsignal/api/eblagh/payment/bazaar/token";
        //type this link in browser
        // "https://pardakht.cafebazaar.ir/devapi/v2/auth/authorize/?response_type=code&access_type=offline&redirect_uri=https://qr-image-creator.com/hamsignal/api/payment/bazaar/token&client_id=" . $this->bazaar_client_id;
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post("https://pardakht.cafebazaar.ir/devapi/v2/auth/token/", [
            'grant_type' => 'authorization_code',
            'code' => "$code",
            'client_id' => $this->bazaar_client_id,
            'client_secret' => $this->bazaar_client_secret,
            'redirect_uri' => $redirect,
        ]);
        $response = $response->object() ?? null;
        if ($response && !empty($response->access_token)) {
            Setting::updateOrCreate(
                ['key' => 'EBLAGH_BAZAAR_ACCESS_TOKEN'],
                ['value' => $response->access_token]
            );
            Setting::updateOrCreate(
                ['key' => 'EBLAGH_BAZAAR_REFRESH_TOKEN'],
                ['value' => $response->refresh_token]
            );
            Setting::updateOrCreate(
                ['key' => 'EBLAGH_BAZAAR_EXPIRE'],
                ['value' => now()->addSeconds($response->expires_in)]
            );
            return $response->access_token;
        }
    }

    public function getBazaarToken(Request $request)
    {
        if ($request && $request->get('code')) {
            $this->getFirstBazaarToken($request->get('code'));
        }
        $access = Setting::firstOrNew(['key' => 'EBLAGH_BAZAAR_ACCESS_TOKEN']);
        $refresh = Setting::firstOrNew(['key' => 'EBLAGH_BAZAAR_REFRESH_TOKEN']);
        $expire = Setting::firstOrNew(['key' => 'EBLAGH_BAZAAR_EXPIRE']);

        //refresh token
        if ($refresh->value &&
            (!$expire->value || now()->gte(Carbon::createFromDate($expire->value)))) {

            $cafeRequest = Http::asForm()->post('https://pardakht.cafebazaar.ir/devapi/v2/auth/token/', [
                'grant_type' => 'refresh_token',
                'client_id' => $this->bazaar_client_id,
                'client_secret' => $this->bazaar_client_secret,
                'refresh_token' => $refresh->value,
            ]);
            $response = json_decode($cafeRequest->body());

            if ($response->access_token) {
                $access->value = $response->access_token;
                $expire->value = now()->addSeconds($response->expires_in);
                $access->save();
                $expire->save();
                return $response->access_token;
            }
        } //access token
        elseif ($access->value) {
            return $access->value;
        } //new manual access token
        else {
            //https://pardakht.cafebazaar.ir/devapi/v2/auth/authorize/?response_type=code&access_type=offline&redirect_uri=https://dabeladl.com/cafe&client_id=wVGLiWJMuFOkFSvS1vomKK2o9taKkR4yQgGMIkhn
            if (!request('code'))
                return Http::get('https://pardakht.cafebazaar.ir/devapi/v2/auth/authorize', ['response_type' => 'code', 'access_type' => 'offline', 'redirect_uri' => url('api/payment/bazaar/token'), 'client_id' => $this->bazaar_client_id]);
            $cafeRequest = Http::asForm()->post('https://pardakht.cafebazaar.ir/devapi/v2/auth/token/', [
                'grant_type' => 'authorization_code',
                'code' => request('code'),
                'client_id' => $this->bazaar_client_id,
                'client_secret' => $this->bazaar_client_secret,
                'redirect_uri' => url('api/payment/bazaar/token'),
            ]);
            $response = json_decode($cafeRequest->body());

            if (isset($response->access_token)) {
                $access->value = $response->access_token;
                $refresh->value = $response->refresh_token;
                $expire->value = now()->addSeconds($response->expires_in);
                $access->save();
                $refresh->save();
                $expire->save();
                return $response->access_token;
            }

        }
        return null;
    }

    protected
    function searchPanel(Request $request)
    {
        $userAdmin = $request->user();

        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $orderBy = $orderBy == 'agency' ? 'agency_id' : $orderBy;
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;
        $status = $request->status;
        $query = Transaction::query()->select('*');
        $agencies = [];

        if ($userAdmin instanceof Admin) {
            $myAgency = Agency::find($userAdmin->agency_id);
//            $agencies = $userAdmin->allowedAgencies($myAgency)->select('id', 'name')->get();
            $agencyIds = $userAdmin->allowedAgencies($myAgency)->pluck('id');
            $query->orWhere(function ($query) use ($agencyIds) {
                $query->where('from_type', 'agency')->whereIntegerInRaw('from_id', $agencyIds)->whereNotNull('payed_at');;
            })->orWhere(function ($query) use ($agencyIds) {
                $query->where('to_type', 'agency')->whereIntegerInRaw('to_id', $agencyIds)->whereNotNull('payed_at');;
            })->orWhere(function ($query) use ($agencyIds, $userAdmin) {
                $query->where('from_type', 'admin')->where('from_id', $userAdmin->id)->whereNotNull('payed_at');;
            })->orWhere(function ($query) use ($agencyIds, $userAdmin) {
                $query->where('to_type', 'admin')->where('to_id', $userAdmin->id)->whereNotNull('payed_at');;
            });
        } else {
            $query->orWhere(function ($query) use ($userAdmin) {
                $query->where('from_type', 'user')->where('from_id', $userAdmin->id)->whereNotNull('payed_at');;
            })->orWhere(function ($query) use ($userAdmin) {
                $query->where('to_type', 'user')->where('to_id', $userAdmin->id)->whereNotNull('payed_at');;
            });
        }
        if ($search)
            $query = $query->where(function ($query) use ($search) {
                $query->orWhere('title', 'like', "%$search%")
                    ->orWhere('pay_id', 'like', "%$search%");
            });


        return tap($query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($agencies, $userAdmin) {
            return $paginated->getCollection()->transform(
                function ($item) use ($agencies, $userAdmin) {
                    return $item;
                    if ($userAdmin instanceof Admin)
                        $item->setRelation('agency', $agencies->where('id', $item->agency_id)->first());


                }

            );
        });


    }
}
