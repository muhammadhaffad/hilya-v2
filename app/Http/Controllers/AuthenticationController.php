<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function logout(Request $request) {
        $result = $this->authService->logout($request);
        if (@$result['code'] == 302) {
            return redirect('/');
        } else {
            abort(500);
        }
    }

    /* public function signIn(SignInRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'failed' => 'Sign In failed!'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    } */
}
