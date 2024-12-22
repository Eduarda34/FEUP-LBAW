<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Post;

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

            // -------------------- STATIC PAGES -------------------- //

            // About page
            if (request()->routeIs('about')) {
                $breadcrumbs[] = ['title' => 'About', 'url' => null];
            }

            // Contacts page
            if (request()->routeIs('contacts')) {
                $breadcrumbs[] = ['title' => 'Contacts', 'url' => null];
            }

            // -------------------- USER PAGES -------------------- //

            // User list (meet new authors)
            if (request()->routeIs('users.list')) {
                $breadcrumbs[] = ['title' => 'Authors', 'url' => null];
            }

            // User profile
            if (request()->routeIs('user.profile')) {
                $breadcrumbs[] = ['title' => 'Your profile', 'url' => null];
            }

            // User notifications
            if (request()->routeIs('user.notifications')) {
                $breadcrumbs[] = ['title' => 'Users', 'url' => route('users.list')];
                $breadcrumbs[] = ['title' => 'Notifications', 'url' => null];
            }

            // -------------------- POST -------------------- //

            // Main page
            if (request()->routeIs('posts.show')) {
                $postId = request()->route('post_id');
                $post = Post::find($postId);
                if ($post) {
                    $breadcrumbs[] = ['title' => $post->title, 'url' => null];
                }
            }

            // Create post
            if (request()->routeIs('posts.create')) {
                $breadcrumbs[] = ['title' => 'Create your post', 'url' => null];
            }

            // -------------------- ADMIN -------------------- //

            // Reports
            if (request()->routeIs('system.reports.list')) {
                $breadcrumbs[] = ['title' => 'Reports', 'url' => null];
            }

            $view->with('breadcrumbs', $breadcrumbs);

        });
    }
}
