<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        $user = auth()->user();

        if (!$user) {
            return '/home';
        }

        if (!$user->role) {
            return '/home';
        }

        return match($user->role->nama_role) {
            'superadmin' => '/dashboard',
            'gudang' => '/gudang',
            'kendaraan' => '/kendaraan',
            default => '/home',
        };
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->with('error', 'Email atau password tidak sesuai.')
            ->withInput($request->only('email'));
    }

}
