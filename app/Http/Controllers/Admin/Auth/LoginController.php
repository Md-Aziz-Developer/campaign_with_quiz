<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Auth\LoginController as BaseLoginController;
use Illuminate\Http\Request;

class LoginController extends BaseLoginController
{
    protected $redirectTo = '/admin';

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? response()->json([], 204)
            : redirect()->route('admin.login');
    }
}
