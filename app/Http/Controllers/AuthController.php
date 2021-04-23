<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ThrottlesLogins;

    public function login_view()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // validation
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:6|max:18|string',
        ]);

        if (method_exists($this, 'hasTooManyLoginAttempts')
            && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // redirect to the right route
            return redirect()->route('home');
        }
        $this->incrementLoginAttempts($request);

        return redirect()->back()
            ->withInput($request->all())
            ->withErrors(['error' => 'Please check your username / password.'])
            ;
    }

    public function register_view()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email|string',
            'password' => 'required|min:6|max:18|confirmed|string',
            'name' => 'required|string',
        ]);

        $ac_type = AccountType::select('id')->where('name', 'developer')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'ac_type' => $ac_type,
        ]);

        if ($user) {
            event(new Registered($user));
            // if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            //     return redirect()->route('home');
            // }
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }
    }
}
