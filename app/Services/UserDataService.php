<?php

namespace App\Services;

use App\Models\AccessToken;

class UserDataService
{
    public function checkGithubTokenStatus(int $userId): bool
    {
        $validTokenCount = AccessToken::where(['platform' => 1, 'owner' => $userId, 'verified' => true])
            ->count()
        ;

        if (0 === $validTokenCount) {
            return false;
        }

        return true;
    }

    public function checkForPreviousTokens(int $userId): bool
    {
        $validTokenCount = AccessToken::where(['platform' => 1, 'owner' => $userId, 'verified' => true])
            ->withTrashed()
            ->count()
        ;

        if (0 === $validTokenCount) {
            return false;
        }

        return true;
    }
}
