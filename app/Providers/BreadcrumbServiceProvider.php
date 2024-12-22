<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BreadcrumbServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        view()->composer('*', function ($view){
            $breadcrumbs = [];

            // Home breadcrumb (trending news) -> always included
            $breadcrumbs[] = ['title' => 'Home', 'url' => route('posts')];

            // About page
            if (request()->routeIs('about')) {
                $breadcrumbs[] = ['title' => 'About', 'url' => null];
            }

            // Contacts page
            if (request()->routeIs('contacts')) {
                $breadcrumbs[] = ['title' => 'Contacts', 'url' => null];
            }

            // User list (meet new authors)
            if (request()->routeIs('users.list')) {
                $breadcrumbs[] = ['title' => 'Authors', 'url' => null];
            }

            $view->with('breadcrumbs', $breadcrumbs);

        });
    }
}
