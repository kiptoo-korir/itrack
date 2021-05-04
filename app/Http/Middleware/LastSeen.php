<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LastSeen
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }
        User::where('id', Auth::user()['id'])->update(['last_seen' => Carbon::now()]);

        return $next($request);
    }
}
