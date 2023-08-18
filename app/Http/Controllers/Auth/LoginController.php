<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    function __invoke(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $username = $request->username;

            $user = User::where(function ($query) use ($username) {
                $query->where('username', 'like', '%' . $username . '%')
                    ->orWhere('email', 'like', '%' . $username . '%');
            })->first();

            if ($user) {
                if (Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
                    return redirect()->route('dashboard')->with('success', "Login berhasil");
                } else {
                    return back()->with('error', "Username atau Password salah");
                }
            } else {
                $user = DB::connection('kmi_server')->table('musers')->where(function ($query) use ($username) {
                    $query->where('txtusername', 'like', '%' . $username . '%')
                        ->orWhere('txtemail', 'like', '%' . $username . '%');
                })->first();

                if (Auth::attempt(['username' => $user->txtusername, 'password' => $request->password])) {
                    return redirect()->route('dashboard')->with('success', "Login berhasil");
                } else {
                    return back()->with('error', "Username atau Password salah");
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', "Username atau Password salah");
        }
    }

    // /*
    // |--------------------------------------------------------------------------
    // | Login Controller
    // |--------------------------------------------------------------------------
    // |
    // | This controller handles authenticating users for the application and
    // | redirecting them to your home screen. The controller uses a trait
    // | to conveniently provide its functionality to your applications.
    // |
    // */

    // use AuthenticatesUsers;

    // /**
    //  * Where to redirect users after login.
    //  *
    //  * @var string
    //  */
    // protected $redirectTo = RouteServiceProvider::HOME;

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
    // }

    // public function username()
    // {
    //     return 'username';
    // }
}
