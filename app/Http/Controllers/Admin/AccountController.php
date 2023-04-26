<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Account\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $accountService;
    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }
    public function index()
    {
        return view('v2.admin.account.index');
    }
    public function update(Request $request)
    {
        $result = $this->accountService->updateProfile($request->all());
        if ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors']);
        }
        return redirect()->back()->with('message', $result['message']);
    }
    public function changePassword(Request $request)
    {
        $result = $this->accountService->changePassword($request->all());
        if ($result['code'] == 422) {
            return redirect()->back()->withErrors($result['errors']);
        }
        return redirect()->back()->with('message', $result['message']);
    }
}
