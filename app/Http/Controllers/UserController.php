<?php

namespace App\Http\Controllers;

use App\Http\Helpers\SMSHelper;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\UserRequest;
use App\Models\City;
use App\Models\Hire;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function create(UserRequest $request)
    {

        $request->merge([
            'email_verified_at' => $request->email && $request->email_verified ? Carbon::now() : null,
            'phone_verified' => true,
            'is_active' => $request->status == 'active',
            'is_block' => $request->status == 'block',
            'ref_id' => User::makeRefCode($request->phone),
        ]);
        $user = User::create($request->all());
        if ($user) {
            if ($request->img)
                Util::createImage($request->img, Variable::IMAGE_FOLDERS[User::class], $user->id);
            $res = ['flash_status' => 'success', 'flash_message' => __('done_successfully')];
            Telegram::log(null, 'user_created', $user);

            return to_route('panel.admin.user.edit', $user->id)->with($res);

        }
        $res = ['flash_status' => 'danger', 'flash_message' => __('response_error')];
        return back()->with($res);
    }

    public function new(Request $request)
    {

        return Inertia::render('Panel/Admin/User/Create', [
        ]);
    }

    public function edit(Request $request, $id): Response
    {
        $me = $request->user();
        $data = User::with('financial')->find($id);
        $this->authorize('edit', [get_class($me), $data]);

        return Inertia::render('Panel/Admin/User/Edit', [
            'data' => $data,
        ]);
    }

    public function searchPanel(Request $request)
    {
        $user = $request->user();
        $search = $request->search;
        $page = $request->page ?: 1;
        $orderBy = $request->order_by ?: 'id';
        $dir = $request->dir ?: 'DESC';
        $paginate = $request->paginate ?: 24;

        $query = User::query();
        $query->select();

        if ($search)
            $query = $query->where('fullname', 'like', "%$search%")->orWhere('phone', 'like', "%$search%");

        return $query->orderBy($orderBy, $dir)->paginate($paginate, ['*'], 'page', $page);
    }

    public function update(UserRequest $request)
    {

        $id = $request->id;
        $user = isset($id) ? User::find($id) : auth('sanctum')->user();
        $cmnd = $request->cmnd;
        if (isset($id))
            $this->authorize('edit', [User::class, $user]);

        switch ($cmnd) {
            case   'upload-img':
                if (!$request->img) //  add extra image
                    return response()->json(['errors' => [__('file_not_exists')], 422]);

                Util::createImage($request->img, Variable::IMAGE_FOLDERS[User::class], $user->id);
//                $user->is_active = false;
//                $user->save();
                Telegram::log(null, 'user_edited', $user);
                return response()->json(['message' => __('updated_successfully')], 200);
            case 'active':
            case 'inactive':
            case 'block':
                $user->is_block = $cmnd == 'block';
                $user->is_active = $cmnd == 'active';
                $user->save();
                Telegram::log(null, 'user_edited', $user);
                return response()->json(['message' => __('updated_successfully'), 'is_active' => $user->is_active, 'is_block' => $user->is_block], 200);
            case 'role-ad':
            case 'role-us':
            case 'role-go':
                if (auth()->user()->role != 'go' && $cmnd == 'role-go')
                    return response()->json(['message' => __('just_god_can_set_god'), 'role' => $user->role], 422);
                $user->role = $cmnd == 'role-ad' ? 'ad' : ($cmnd == 'role-go' ? 'go' : 'us');
                $user->save();
                return response()->json(['message' => __('updated_successfully'), 'role' => $user->role], 200);

            case 'access':

                if (Hire::isEdited($user->access, $request->accesses)) {
                    $user->access = join(',', $request->accesses ?? []) ?: null;
                    $n = Notification::create(
                        [
                            'type' => 'access_change',
                            'subject' => __('your_roles_changed'),
                            'description' => null,
                            'owner_id' => $user->id
                        ],
                    );
                    if ($n)
                        $user->notifications++;
                    $user->save();
                    Telegram::log(null, 'user_edited', $user);


                }
                return response()->json(['message' => __('updated_successfully'), 'access' => $user->access,], Variable::SUCCESS_STATUS);

            case 'wallet':

                $user->wallet = $request->wallet;
                $user->save();
                return response()->json(['message' => __('updated_successfully'), 'wallet' => $user->wallet,], Variable::SUCCESS_STATUS);
            case 'add-address':
                $address = $request->address;
                $addresses = $user->addresses ?? [];
                $addresses[] = $address;
                $user->update(['addresses' => $addresses]);
                return response()->json(['message' => __('updated_successfully'), 'addresses' => $user->addresses], Variable::SUCCESS_STATUS);

        }
        if ($request->password)
            $user->password = Hash::make($request->password);
        if ($request->password)
            $user->password = Hash::make($request->password);
        if ($request->email && $request->email != $user->email && $request->email_verified)
            $user->email_verified_at = Carbon::now();
        if (!$request->email && !$user->email)
            $user->email_verified_at = null;
        $user->phone_verified = ($request->phone || $user->phone) && $request->phone_verified;
        $user->wallet_active = ($request->card || $user->card) && $request->wallet_active;
        $user->is_block = $request->status == 'block';
        $user->is_active = $request->status == 'active';
        $user->fullname = $request->fullname;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->card = $request->card;
        $user->wallet = $request->wallet;
        $user->save();
        $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
        Telegram::log(null, 'user_edited', $user);
        return back()->with($res);
    }

    public function updateLocation(Request $request)
    {
        if (!is_numeric($request->city_id)) return;
        session()->put('city_id', $request->city_id);


        $user = auth('sanctum')->user();
        if ($user)
            $user->update(['city_id' => $request->city_id]);


        return response()->json(['location' => User::getLocation(City::select('id', 'name', 'level', 'parent_id')->get())]);
    }

}
