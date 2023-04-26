<?php
namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthServiceImplement implements AuthService {
    public function register(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'email' => 'email|unique:users,email|required|string',
            'phonenumber' => 'numeric|required',
            'username' => 'required|unique:users,username|string',
            'password' => 'required|string|confirmed'
        ]);
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ];
        }
        DB::beginTransaction();
        try {
            $user = User::create([
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phonenumber' => $request->phonenumber,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'customer'
            ]);
            DB::commit();
            return [
                'code' => 201,
                'message' => 'Berhasil membuat akun',
                'data' => $user
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    public function login(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        if ($validator->fails()) {
            return [
                'code' => 422,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ];
        }
        $credentials = $validator->validate();
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return [
                'code' => 200,
                'message' => 'Berhasil masuk',
                'data' => Auth::user()
            ];
        }
        return [
            'code' => 401,
            'message' => 'Username atau password salah'
        ];
    }
    public function logout(Request $request): array
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return [
            'code' => 302,
            'message' => 'Berhasil logout'
        ];
    }
}