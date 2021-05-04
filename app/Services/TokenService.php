<?php

namespace App\Services;

use App\Models\Platform;
use Cache;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use function PHPUnit\Framework\isNull;

class TokenService
{
    public function github_token(Request $request)
    {
        $user_id = Auth::id();
        $key = 'github-'.$user_id;
        $platform = Platform::where('name', 'Github')->select('id')->get()[0]->id;
        $token = Cache::get($key, $this->get_token($platform));

        if ('error-400' == $token) {
            // invalidate current token
        } elseif ('' == $token) {
            // Cache::forget($key);
        }
    }

    private function get_token($platform)
    {
        $user = Auth::user();
        $token = $user->access_token()->where('platform', $platform)->access_token;

        try {
            $decrypted = isNull($token) ? '' : Crypt::decryptString($token);
        } catch (DecryptException $e) {
            $decrypted = 'error-400';
        }

        return $decrypted;
    }
}
