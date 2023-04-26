<?php
namespace App\Services\Auth;

use Illuminate\Http\Request;

interface AuthService {
    public function register(Request $request) : array;
    public function login(Request $request) : array;
    public function logout(Request $request) : array;
}