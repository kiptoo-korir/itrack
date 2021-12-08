<?php

namespace App\Providers;

use App\Views\Composers\GitTokenComposer;
use App\Views\Composers\UserDataComposer;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $gitTokenComposerViews = ['home', 'profile', 'reminder', 'project', 'note', 'task', 'repositories', 'specific_repository', 'all-notifications', 'reports-home'];
        view()->composer($gitTokenComposerViews, GitTokenComposer::class);
        view()->composer($gitTokenComposerViews, UserDataComposer::class);
    }
}
