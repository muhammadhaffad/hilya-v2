<?php

namespace App\Http\Controllers\LoginPage;

use Illuminate\Support\Facades\Auth;

class LoginForm
{
    public static function signIn($request) {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'failed' => 'Login gagal!'
        ]);
    }
}
