<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\SmsHelper;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Variable;
use App\Http\Requests\ProfileRequest;
use App\Models\Hire;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{


    /**
     * Display the registration view.
     */
    public function create(): Response
    {

        return Inertia::render('Auth/Register', [

        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ProfileRequest $request): RedirectResponse
    {
//        $request->validate([
//            'name' => 'required|string|max:255',
//            'email' => 'required|string|email|max:255|unique:'.User::class,
//            'password' => ['required', 'confirmed', Rules\Password::defaults()],
//        ]);

        $user = User::create([
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'phone_verified' => true,
            'ref_id' => User::makeRefCode($request->phone),
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);
        Telegram::log(null, 'user_created', $user);
        SmsHelper::deleteCode($user->phone);
        return redirect(RouteServiceProvider::HOME);
    }
}
