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
        view()->composer(['home'], GitTokenComposer::class);
    }
}
