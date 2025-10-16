<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

/**
 * Handle an incoming new password request.
 *
 * @throws \Illuminate\Validation\ValidationException
 */
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // Here is the fix
    $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (Usuario $user, string $password) {
        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();

        event(new PasswordReset($user));
    });

    if ($status === Password::PASSWORD_RESET) {
        return redirect()->route('login')->with('status', __($status));
    }

    throw ValidationException::withMessages([
        'email' => [__($status)],
    ]);
}
}