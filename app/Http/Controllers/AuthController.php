<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ThrottlesLogins;

    public function login_view()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        // validation
        $request->validate([
            $this->username() => 'required|string|email',
            'password' => 'required|min:6|max:18|string',
        ]);

        if (method_exists($this, 'hasTooManyLoginAttempts')
            && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt([$this->username() => $email, 'password' => $password])) {
            // redirect to the right route
            $request->session()->regenerate();
            $this->log_signin_activity();

            return redirect()->intended('home');
        }

        $this->incrementLoginAttempts($request);

        return redirect()->back()
            ->withInput($request->all())
            ->withErrors(['email' => 'Please check the credentials you provided and try again.'])
            ;
    }

    public function register_view()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            $this->username() => 'required|email|unique:users,email|string',
            'password' => 'required|min:6|max:18|confirmed|string',
            'name' => 'required|string',
        ]);

        $ac_type = AccountType::select('id')->where('name', 'developer')->first();

        $user = User::create([
            'name' => $request->name,
            $this->username() => $request->email,
            'password' => Hash::make($request->password),
            'ac_type' => $ac_type->id,
        ]);

        if ($user) {
            event(new Registered($user));
            if (Auth::attempt([$this->username() => $request->email, 'password' => $request->password])) {
                $request->session()->regenerate();
                $this->log_signin_activity();

                return redirect()->route('home');
            }
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

    public function send_verification()
    {
        Auth::user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    }

    public function handle_verification_request(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect('/');
    }

    public function verification_view()
    {
        return view('auth.verify');
    }

    public function username()
    {
        return 'email';
    }

    private function log_signin_activity()
    {
        $user = Auth::user();
        activity('log in')
            ->causedBy($user)
            ->withProperties([
                'action' => 'Successful',
                'user' => $user,
            ])
            ->log("user {$user->name} has signed in.")
        ;
    }
}
