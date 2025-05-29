<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (isset($user)) {
            if (auth()->attempt(array('email' => $request['email'], 'password' => $request['password']))) {
                // if ($user->role == 0 && $user->status != 1) {
                //     Auth::logout();
                //     return redirect('/login')->with('error', 'Your account has been deactivated. Please contact your manager.');
                // } elseif ($user->role == 0) {
                    Session::put('user_role', $user->role);
                    return redirect()->route('dashboard');
                // } else {
                //     Auth::logout();
                //     return redirect('/login');
                // }

                // if ($user->status != 1) {
                //     Auth::logout();
                //     return redirect('/login')->with('error', 'Your account has been deactivated. Please contact your manager.');
                // } else {
                //     Session::put('user_role', $user->role);
                //     return redirect()->route('dashboard');
                // }
            }
            return redirect('/login');
        }
    }
    public function logout()
    {
        // if (Auth::check()) {
        //     $role = Auth::user()->role;
        //     Auth::logout();
        //     session()->invalidate();
        //     session()->regenerateToken();

        //     if ($role == 0) {
        //         return redirect('/login');
        //     } else {
        //         return redirect('/');
        //     }
        // }
        // else{
        //     return redirect('/');
        // }
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    }
}