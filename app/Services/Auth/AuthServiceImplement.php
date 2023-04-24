<?php
namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceImplement implements AuthService {
    public function register(Request $request): array
    {
        /* TODO : Buat fungsi register */
        return [];
    }
    public function login(Request $request): array
    {
        /* TODO : Buat fungsi login */
        return [];
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