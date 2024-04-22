<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'created_at',
    ];

    public static function getValue(string $key)
    {

//        $val = Setting::where(['key' => $key, 'lang' => app()->getLocale()])->firstOrNew()->value;
//        if (!$val)
            $val = Setting::where(['key' => $key,])->firstOrNew()->value;
        return $val;
    }

    public static function setWallet($addSubValue, $country = 'iran')
    {
        Setting::where('key', "{$country}_wallet")->update(['value' => DB::raw("value + $addSubValue")]);


    }
}
