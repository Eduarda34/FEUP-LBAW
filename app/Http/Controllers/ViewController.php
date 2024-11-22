<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    /**
     * Display the top news feed with caching and pagination.
     */
    public function topNews()
    {
        // Try to fetch the top news from cache
        $topNews = Cache::remember('top_news', now()->addMinutes(10), function () {
            // Fetch the top news sorted by vote count (descending) with pagination
            return News::with('user') // Eager load the related user
                       ->orderBy('votes', 'desc') // Sort by vote count
                       ->paginate(10); // Paginate with 10 items per page
        });

        // Return the view with the top news data
        return view('pages.top-news', ['newsItems' => $topNews]);
    }
}