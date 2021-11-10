<?php

namespace App\Providers;

use App\Views\Composers\GitTokenComposer;
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
        $gitTokenComposerViews = ['home', 'profile', 'reminder', 'project', 'note', 'task', 'repositories', 'specific_repository'];
        view()->composer($gitTokenComposerViews, GitTokenComposer::class);
    }
}
