<?php

namespace App\Views\Composers;

use App\Services\UserDataService;
use Illuminate\Support\Facades\Auth;
use illuminate\View\View;

class UserDataComposer
{
    protected $userDataService;

    public function __construct(UserDataService $userDataService)
    {
        // Dependencies are automatically resolved by the service container
        $this->userDataService = $userDataService;
    }

    public function compose(View $view)
    {
        $userData = Auth::user();
        $firstLetter = substr($userData->name, 0, 1);
        $userData->first_letter = $firstLetter;

        $view->with([
            'user_data' => $userData,
        ]);
    }
}
