<?php

namespace App\Providers;

use App\View\Composers\QuoteComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
	    Paginator::useBootstrap();
	    \Carbon\Carbon::setLocale('tr');
	    View::composer('layouts.app-frontend', QuoteComposer::class);


    }
}
