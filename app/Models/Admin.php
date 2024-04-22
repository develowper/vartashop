<?php

namespace App\Models;

use App\Http\Helpers\Util;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
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
        'fullname',
        'agency_id',
        'agency_level',
        'email',
        'email_verified_at',
        'phone',
        'phone_verified',
        'ref_id',
        'push_id',
        'telegram_id',
        'bale_id',
        'is_active',
        'is_block',
        'wallet_active',
        'national_code',
        'password',
        'status',
        'role',
        'access',
//        'card',
//        'sheba',
        'notifications',
        'wallet',
        'meta_wallet',
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
        'access' => 'array',
    ];

    public static function makeRefCode2()
    {
        $original = implode("", array_merge(range(0, 9), range('a', 'z')));


        $ref = Util::randomString(5, $original);
        for ($i = 5; $i <= 10; $i++) {
            for ($j = 0; $j < 100; $j++) {
                if (Admin::where('ref_id', $ref)->exists())
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
            if (Admin::where('ref_id', $ref)->exists())
                $ref = self::randomString($i, $original, $phone);
            else
                break;
//            }
//            if ($j < 100)
//                break;
        }
        return "{$ref}a"; //for admins added a
    }

    public function isAdmin()
    {
        return in_array($this->role, ['go', 'ad',]);
    }

    public function setReferral($re = null)
    {
        $ref = $re ?: session('ref');

        $u = Admin::whereNotNull('ref_id')->where('ref_id', $ref)->first();
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

    public function hasAccess($item)
    {
        if (in_array($this->role, ['god']))
            return true;

        if (in_array($item, ['create_pack', 'edit_pack', 'create_product', 'edit_product', 'edit_repository_order', 'edit_setting', 'edit_financial', 'view_user'])) {
            return $this->agency_id == 1 && (in_array($this->role, ['owner']) || in_array($item, $this->access));
        }
        if (in_array($item, ['create_repository_order'])) {
            return $this->agency_level < '3' && (in_array($this->role, ['owner']) || in_array($item, $this->access));
        }
        if (in_array($item, ['create_variation'])) {
            return $this->agency_level <= '3' && (in_array($this->role, ['owner']) || in_array($item, $this->access));
        }
        return in_array($this->role, ['owner']) || in_array($item, $this->access ?? []);
    }

    public function accesses()
    {

        if ($this->role == 'god' || ($this->agency_id == 1 && (in_array($this->role, ['owner'])))) return 'all';

        if (in_array($this->role, ['owner'])) return [
            'view_agency',
            'view_admin',
            'view_repository',
            'view_shipping',
            'view_shipping-method',
            'view_finantial',
            'view_variation',
            $this->agency_level < 3 ? 'create_repository_order' : '',
            $this->agency_level <= 3 ? 'create_variation' : '',
            $this->agency_level < 3 ? 'view_agency_order' : '',
            'view_user_order',
            'create_shipping',
            'create_order',
        ];
        return $this->access;

    }

    public function financial()
    {
        return $this->hasOne(AdminFinancial::class, 'admin_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function allowedAgencies($myAgency)
    {
        $data = Agency::query();
        $data = $data->select('*');
        if ($this->role != 'god') {

            if (!$myAgency)
                $data->where('id', 0);
            elseif ($myAgency->level == '1')
                $data->whereIn('province_id', $myAgency->access);
            elseif ($myAgency->level == '2')
                $data->whereIn('id', $myAgency->access);
            elseif ($myAgency->level == '3')
                $data->where('id', $myAgency->id);
            if ($myAgency)
                $data->where('level', '>', $myAgency->level)
                    ->orWhere(function ($query) use ($myAgency) {
                        $query->whereIn('id', $myAgency->level == '0' ? [$myAgency->id, null] : [$myAgency->id]);
                    });

        }
        return $data;

    }
}
