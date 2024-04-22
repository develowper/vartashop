<?php

namespace App\Models;

use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Termwind\Components\Dd;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'fullname',
        'email',
        'email_verified_at',
        'phone',
        'phone_verified',
        'ref_id',
        'push_id',
        'city_id',
        'telegram_id',
        'bale_id',
        'is_active',
        'is_block',
        'wallet_active',
        'password',
        'role',
        'access',
        'card',
        'notifications',
        'wallet',
        'meta_wallet',
        'addresses',
        'settings',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_block' => 'boolean',
        'wallet_active' => 'boolean',
        'addresses' => 'array',
    ];

    public function financial()
    {
        return $this->hasOne(UserFinancial::class, 'user_id');
    }

    public static function makeRefCode2()
    {
        $original = implode("", array_merge(range(0, 9), range('a', 'z')));


        $ref = Util::randomString(5, $original);
        for ($i = 5; $i <= 10; $i++) {
            for ($j = 0; $j < 100; $j++) {
                if (User::where('ref_id', $ref)->exists())
                    $ref = Util::randomString($i, $original);
                else
                    break;
            }
            if ($j < 100)
                break;
        }
        return $ref;
    }

    public static function makeRefCode($phone)
    {
        $original = implode("", array_merge(range(0, 9)/*, range('a', 'z')*/));


        $ref = self::randomString(5, $original, $phone);
        for ($i = 6; $i <= 10; $i++) {
//            for ($j = 0; $j < 100; $j++) {
            if (User::where('ref_id', $ref)->exists())
                $ref = self::randomString($i, $original, $phone);
            else
                break;
//            }
//            if ($j < 100)
//                break;
        }
        return $ref;
    }

    public static function getLocation($cities)
    {

        $user = auth('sanctum')->user();
//        $res = [
//            ['id' => 904, 'name' => 'تهران'],
//            ['id' => 1, 'name' => 'تهران'],
//            ['id' => 61, 'name' => 'تجریش'],
//        ];
//        session()->put('city_id', null);
        $res = null;
        $city = $cities->where('id', optional($user)->city_id ?? session('city_id', Variable::CITY_ID))->first();
//        dd(session('city_id', Variable::CITY_ID));
        if ($city) {
            if ($city->level == 1)
                $res = [['id' => $city->id, 'name' => $city->name]];
            elseif ($city->level == 2) {
                $city0 = $cities->where('id', $city->parent_id)->first();
                $res = [
                    ['id' => optional($city0)->id, 'name' => optional($city0)->name],
                    ['id' => $city->id, 'name' => $city->name],
                ];
            } elseif ($city->level == 3) {
                $city1 = $cities->where('id', $city->parent_id)->first();
                $city0 = $city1 ? $cities->where('id', $city1->parent_id)->first() : null;
                $res = [
                    ['id' => optional($city0)->id, 'name' => optional($city0)->name],
                    ['id' => optional($city1)->id, 'name' => optional($city1)->name],
                    ['id' => $city->id, 'name' => $city->name],
                ];
            }
        }
        session()->put('city_id', is_array($res) ? $res[count($res) - 1]['id'] : null);
        return $res;
    }

    public function isAdmin()
    {
        return in_array($this->role, ['go', 'ad',]);
    }

    public function setReferral($re = null)
    {
        $ref = $re ?: session('ref');

        $u = User::whereNotNull('ref_id')->where('ref_id', $ref)->first();
        $id = $u ? $u->id : null;
        if ($ref && $id && $this->id != $id && Ref::where('invited_id')) {

            $r = Ref::firstOrCreate([
                'inviter_id' => $id,
                'invited_id' => $this->id,
                'type' => 'register',
            ]);
            if (!$r->done) {
                $r->update(['done' => true]);
                $register_commission = Setting::getValue('register_c');
                if ($register_commission) {
                    $u->wallet += $register_commission;
                    $u->save();
                    $storeUser = UserTransaction::firstOrCreate(request()->ip(), $id);
                    $storeUser->sum += $register_commission;
                    $storeUser->save();
                    Transaction::create([
                        'title' => __('invite_user') . $this->id,
                        'owner_id' => $id,
                        'source_id' => "$this->id",
                        'type' => "invite",
                        'amount' => $register_commission,
                    ]);
                }
            }
        }

    }

    static function randomString($length = 5, $original, $phone)
    {
        if ($phone && strlen($phone) >= $length)
            return substr($phone, -$length);
        return substr(str_shuffle($original), 0, $length);
    }
}
