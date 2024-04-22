<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected function calculate(Request $request)
    {
        $coupon = $request->coupon;
        $type = $request->type;
        $coupon = Coupon::where('code', $coupon)->first();
        $user_id = optional(auth()->user() ?: auth('api')->user())->id;
        if (!$coupon)
            return response()->json(['errors' => ['coupon' => ['کد تخفیف معتبر نیست']]], 422);
        if ($coupon->user_id != null && $coupon->user_id != $user_id)
            return response()->json(['errors' => ['coupon' => ['کد تخفیف برای شما معتبر نیست']]], 422);
        if (Payment::where('coupon_id', $coupon->id)->whereNotNull('user_id')->where('user_id', $user_id)->exists())
            return response()->json(['errors' => ['coupon' => ['کد تخفیف را قبلا استفاده کرده اید']]], 422);
        if ($coupon->used_at != null)
            return response()->json(['errors' => ['coupon' => ['کد تخفیف استفاده شده است']]], 422);

        if ($coupon->expires_at != null && Carbon::now()->timestamp > $coupon->expires_at)
            return response()->json(['errors' => ['coupon' => ['کد تخفیف منقضی شده است']]], 422);


        $settings = \App\Models\Setting::where('key', 'like', $type . '%')->where('key', 'like', '%_price')->get()->all();
        $res = [];
        foreach ($settings as $setting) {
            $discount = $setting->value * $coupon->discount_percent / 100;

            $res[$setting->key] = round($setting->value - ($coupon->limit_price && $discount > $coupon->limit_price ? $coupon->limit_price : $discount));
        }
        return response()->json($res, 200);

    }

    protected function create(Request $request)
    {
        if ($request->type == 'code') {
            $code = str_random(8);
            while (Coupon::where('code', $code)->exists())
                $code = str_random(8);
            return $code;
        }

        $request->validate([

            'code' => 'required|max:10|regex:/^[A-Za-z0-9_]+[A-Za-z0-9_]{1,10}$/|unique:coupons,code',
            'discount_percent' => 'required|numeric|min:1|max:100',
            'limit_price' => 'nullable|numeric|min:1|digitsbetween:1,10',
            'user' => 'nullable|numeric|' . Rule::exists('users', 'id'),
            'expires_at' => ['nullable', 'regex:/^\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])$/']
        ], [

            'code.required' => 'کد ضروری است',
            'code.max' => 'حداکثر طول کد 10 باشد',
            'code.regex' => 'کد حداقل دو حرف و با حروف انگلیسی شروع شود و می تواند شامل عدد و _  باشد',
            'code.unique' => 'کد تکراری است',

            'discount_percent.required' => 'درصد تخفیف ضروری است',
            'discount_percent.numeric' => 'درصد تخفیف عددی باشد',
            'discount_percent.min' => 'درصد تخفیف حداقل 1 باشد',
            'discount_percent.max' => 'درصد تخفیف حداکثر 100 باشد',

            'limit_price.numeric' => 'حد تخفیف عددی باشد',
            'limit_price.min' => 'حد تخفیف حداقل 1 باشد',
            'limit_price.digitsbetween' => 'حد تخفیف بین 1 تا 10 رقم باشد',

            'user_id.numeric' => 'شناسه کاربر عددی باشد',
            'user_id.exists' => 'شناسه کاربر نامعتبر است',
            'expires_at.regex' => 'تاریخ انقضا نامعتبر است'

        ]);
        $this->authorize('createItem', [User::class, Setting::class, true]);
//        dd($request->expires_at ? \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $request->expires_at)->toCarbon()->timestamp : null);
        Coupon::create([
            'code' => $request->code,
            'discount_percent' => $request->discount_percent,
            'limit_price' => $request->limit_price ?: null,
            'user_id' => $request->user ?: null,
            'expires_at' => $request->expires_at ? \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $request->expires_at)->toCarbon() : null,
        ]);

        return Coupon::all();

    }

    protected function remove(Request $request)
    {
        $request->validate([
            'id' => 'required|' . Rule::in(Coupon::pluck('id')),
        ], [
            'id.required' => 'شناسه ضروری است',
            'id.in' => 'شناسه نامعتبر است',

        ]);

        $this->authorize('editItem', [User::class, new Setting(), true]);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Coupon::where('id', $request->id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return Coupon::all();
    }
}
