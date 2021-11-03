<?php

namespace App\Views\Composers;

use App\Services\UserDataService;
use Illuminate\Support\Facades\Auth;
use illuminate\View\View;

class GitTokenComposer
{
    protected $userDataService;

    public function __construct(UserDataService $userDataService)
    {
        // Dependencies are automatically resolved by the service container
        $this->userDataService = $userDataService;
    }

    public function compose(View $view)
    {
        $userId = Auth::id();
        $client = env('GITHUB_CLIENT_ID');
        $isTokenValid = $this->userDataService->checkGithubTokenStatus($userId);
        $previousTokens = $this->userDataService->checkForPreviousTokens($userId);
        $githubOauthUrl = "https://github.com/login/oauth/authorize?client_id={$client}&scope=repo%20notifications%20user";

        $view->with([
            'isTokenValid' => $isTokenValid,
            'previousTokens' => $previousTokens,
            'githubOauthUrl' => $githubOauthUrl,
        ]);
    }
}
