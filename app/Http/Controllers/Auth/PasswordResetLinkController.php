<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\SmsHelper;
use App\Http\Requests\ProfileRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/PasswordEdit', [
            'status' => session('status'),
        ]);
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ProfileRequest $request): RedirectResponse
    {

//        dd($request->is_admin);
        if ($request->is_admin)
            $user = Admin::where('phone', $request->phone)->first();
        else
            $user = User::where('phone', $request->phone)->first();

        if (!$user)
            return back()->withErrors(['message' => __('user_not_found')]);

        $password = $request->new_password;
        $user->password = Hash::make($password);
        SmsHelper::deleteCode($user->phone);
        $user->save();
        $res = ['flash_status' => 'success', 'flash_message' => __('updated_successfully')];
        return to_route($request->is_admin ? 'admin.login' : 'login')->with($res);


        $request->validate([
            'email' => 'required|email',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
