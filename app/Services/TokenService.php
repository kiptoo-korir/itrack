<?php

namespace App\Services;

use App\Models\Platform;
use App\Models\User;
use Cache;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class TokenService
{
    public function github_token($id)
    {
        $user_id = isset($id) ? $id : Auth::id();
        $key = 'github-'.$user_id;
        $platform = Platform::where('name', 'Github')->select('id')->get()[0]->id;
        $token = Cache::get($key, $this->get_token($platform, $user_id));

        if ('error-400' == $token) {
            // invalidate current token
        } elseif ('' == $token) {
            // Cache::forget($key);
        }

        return unserialize($token);
    }

    public function client($user_id = null)
    {
        return Http::withToken($this->github_token($user_id));
    }

    private function get_token($platform, $user_id)
    {
        $user = User::findOrFail($user_id);
        $token_record = $user->access_token()->where(['platform' => $platform, 'verified' => true])->first();
        $token = $token_record->access_token;

        try {
            $decrypted = isset($token) ? Crypt::decryptString($token) : '';
        } catch (DecryptException $e) {
            $decrypted = 'error-400';
        }

        return $decrypted;
    }
}
