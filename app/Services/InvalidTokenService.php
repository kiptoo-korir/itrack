<?php

namespace App\Services;

use App\Models\AccessToken;
use App\Notifications\InvalidTokenNotification;
use Illuminate\Support\Facades\Auth;

class InvalidTokenService
{
    public function responseHandler(int $statusCode)
    {
        $functionName = $this->responseHandlerLookup($statusCode);

        if (isset($functionName)) {
            $this->{$functionName}();
        }
    }

    public function invalidateToken(int $userId): void
    {
        AccessToken::where(['platform' => 1, 'owner' => $userId])
            ->delete()
        ;
    }

    private function unauthorizedResponseHandler()
    {
        $user = Auth::user();
        $user->notify(new InvalidTokenNotification());
        $this->invalidateToken($user->id);
    }

    private function responseHandlerLookup(int $statusCode): ?string
    {
        $functionsArray = [
            403 => 'unauthorizedResponseHandler',
        ];

        return $functionsArray[$statusCode] ?? null;
    }
}
