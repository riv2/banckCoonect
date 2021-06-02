<?php

namespace App\Providers;

use App\Http\View\Composers\GitCommitComposer;
use App\Http\View\Composers\OnboardingComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.app', GitCommitComposer::class);
        View::composer('*', OnboardingComposer::class);
    }
}
