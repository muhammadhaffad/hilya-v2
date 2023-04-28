<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $result = $this->authService->register($request);
        if ($result['code'] == 201) {
            return redirect()->to('/login')->with('message', 'Berhasil mendaftar, silahkan login...');
        } else {
            return redirect()->back()->withErrors($result['errors']);
        }
    }

    public function login(Request $request)
    {
        $result = $this->authService->login($request);
        if ($result['code'] == 200) {
            if ($result['data']->role === 'customer') {
                return redirect()->intended(route('customer.dashboard'));
            } else if ($result['data']->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
        } else if ($result['code'] == 422) {
            return back()->withErrors($result['errors'])->onlyInput('username');
        } else {
            return back()->withErrors(['auth' => $result['message']])->onlyInput('username');
        }
    }

    public function logout(Request $request) 
    {
        $result = $this->authService->logout($request);
        if (@$result['code'] == 302) {
            return redirect('/');
        } else {
            abort(500);
        }
    }

    public function checkUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (User::where('username', $request->username)->first()) {
            return response()->json([
                'code' => 200,
                'message' => 'Username tidak tersedia'
            ]);
        } else {
            return response()->json([
                'code' => 200,
                'message' => 'Username tersedia'
            ]);
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
