<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Display a listing of the full-text post search results.
     */
    public function searchPosts(Request $request)
    {   
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $query = $request->input('query');

        $posts = Post::whereRaw("to_tsvector('english', title || ' ' || body) @@ plainto_tsquery('english', ?)", [$query])->get();

        $categories = Category::all();
        // Render the posts view.
        return view('pages.posts', [
            'posts' => $posts,
            'feedType' => 'Search:"'.$query.'"',
            'categories' => $categories
        ]);
    }

    /**
     * Display a user profile.
     */
    public function searchUsers(Request $request)
    {   
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        $request->validate([
            'query' => 'required|string',
        ]);

        // Search for users by username or email
        $user = User::where('username', $request->input('query'))
                    ->orWhere('email', $request->input('query'))
                    ->first();

        if(!$user) {
            return response()->json([
                'error' => 'User not found.'
            ], 404);
        }
        $posts = $user->posts()->get();
        // Render the posts view.
        return view('pages.user', [
            'user' => $user,
            'posts' =>$posts
        ]);
    }
}
