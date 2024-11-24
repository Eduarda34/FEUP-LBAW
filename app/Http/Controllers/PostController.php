<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostVote;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        if (!Auth::check()) {
            $feedType = 'recent';
        } else {
            // Determine the feed type from the query parameter.(Default 'recent')
            $feedType = $request->query('feed', 'recent');
        }

        // Authorize viewing posts.
        $this->authorize('list', Post::class);

        // Fetch posts based on the feed type.
        switch ($feedType) {
            case 'popular':
                $posts = Post::withCount([
                    'votes as likes_count' => function ($query) {
                        $query->where('is_like', true);
                    },
                    'votes as dislikes_count' => function ($query) {
                        $query->where('is_like', false);
                    },
                    'comments'
                ])
                ->with('owner')
                ->get()
                ->sortByDesc(function ($post) {
                    return ($post->likes_count - $post->dislikes_count) + $post->comments_count + $post->owner->reputation;
                });
                break;
            case 'recommended':
                // TODO
                break;
            case 'recent':
            default:
                // Sort by most recent.
                $posts = Post::orderBy('created_at', 'desc')->get();
                break;
        }

        // Render the posts view.
        return view('pages.posts', [
            'posts' => $posts,
            'feedType' => $feedType,
        ]);
    }

    /**
     * Display a listing of the posts by category.
     */
    public function listByCategory(int $category_id)
    {   
        // Get category
        $category = Category::findOrFail($category_id);
        
        // Get all posts from the category.
        $posts = $category->posts()->orderBy('post_id', 'desc')->get();

        // Check if the current user can list the posts.
        $this->authorize('listByCategory', Post::class);

        // The current user is authorized to list posts.

        // Use the pages.posts template to display all posts.
        return view('pages.posts', [
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function showPostCreatorForm()
    {
        $this->authorize('showPostCreatorForm', Post::class);
        return view('pages.postCreator');
    }

    /**
     * Create a new resource.
     */
    public function create(Request $request)
    {
        // Create a blank new post.
        $post = new Post();

        // Check if the current user is authorized to create this post.
        $this->authorize('create', $post);

        $request->validate([
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);
        
        // Set post details.
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = Auth::user()->id;

        // Save the post and return it as JSON.
        $post->save();
        $post->categories()->attach($request->input('categories'));
        return response()->json($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $post_id)
    {
        // Get the post.
        $post = Post::findOrFail($post_id);

        // Check if the current user can see (show) the post.
        $this->authorize('show', $post);  

        // Use the pages.post template to display the post.
        return view('pages.post', [
            'post' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function showPostEditorForm(int $post_id)
    {
        // Get the post.
        $post = Post::findOrFail($post_id);
        
        $this->authorize('showPostEditorForm', $post);
        
        return view('pages.postEditor', [
            'post' => $post, 
            'old' => [
                'title' => $post->title,
                'body' => $post->body,
                'categories' => $post->categories
            ] 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $post_id)
    {
        // Get the post.
        $post = Post::findOrFail($post_id);

        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:50',
            'body' => 'required|string|max:10000',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $post->title = $request->input('title');
        $post->body = $request->input('body');

        $post->save();
        $post->categories()->attach($request->input('categories'));
        return redirect('posts/'.$post->post_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $post_id)
    {
        // Find the post.
        $post = Post::findOrFail($post_id);

        // Check if the current user is authorized to delete this post.
        $this->authorize('delete', $post);

        // Check if the post has votes or comments.
        if ($post->comments()->exists() || $post->votes()->exists()) {
            return response()->json([
                'error' => 'The post cannot be deleted because it has associated votes or comments.'
            ], 409);
        }

        // Delete the post and return it as JSON.
        $post->delete();
        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(int $post_id)
    {
        // Find the post.
        $post = Post::findOrFail($post_id);

        // Check if the current user is authorized to delete this post.
        $this->authorize('forceDelete', Post::class);

        // Delete the post and return it as JSON.
        $post->delete();
        return response()->json($post);
    }

    /**
     * Vote the specific resource.
     */
    public function vote(Request $request, int $post_id)
    {
        $post = Post::findOrFail($post_id);

        // Create a blank new vote.
        $vote = new PostVote();

        // Check if the current user is authorized to vote in this post.
        $this->authorize('vote', $post);

        $request->validate([
            'is_like' => 'required|boolean',
        ]);
        
        // Set vote details.
        $vote->is_like = $request->input('is_like');
        $vote->user_id = Auth::user()->id;
        $vote->post_id = $post->post_id;

        // Save the vote and return it as JSON.
        $vote->save();
        return response()->json($vote);
    }

    /**
     * Edit the specific resource.
     */
    public function editVote(Request $request, int $post_id)
    {
        $post = Post::findOrFail($post_id);

        
        $request->validate([
            'is_like' => 'required|boolean',
        ]);
        
        $vote = PostVote::firstOrCreate(
            [
                'user_id' => Auth::user()->id,
                'post_id' => $post_id
            ],
            [
                'is_like' => $request->input('is_like')
            ]
            );
            
        // Check if the current user is authorized to edit this vote.
        $this->authorize('editVote', $post, $vote);

        // Save the vote and return it as JSON.
        $vote->save();
        return response()->json($vote);
    }

    /**
     * Remove the specific resource.
     */
    public function removeVote(Request $request, int $post_id)
    {
        $post = Post::findOrFail($post_id);
        
        $vote = PostVote::where('post_id', $post_id)
                        ->where('user_id', Auth::user()->id)
                        ->first();

        if($vote) {
            // Check if the current user is authorized to vote in this post.
            $this->authorize('removeVote', $post, $vote);

            // Delete the vote and return it as JSON.
            $vote->delete();
            return response()->json($vote);
        }
        
        return response()->json([
            'message' => 'Vote not found.',
            'post_id' => $post_id,
            'user_id' => Auth::user()->id,
        ], 404);
    }

    /**
     * Display user favorite posts.
     */
    public function favorites()
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } else {
            // The user is logged in.

            // Get all posts.
            $posts = Auth::user()->favorites()->get();

            // The current user is authorized to list posts.

            // Use the pages.posts template to display all posts.
            return view('pages.posts', [
                'posts' => $posts
            ]);
        }
    }

    /**
     * Add post to favorites.
     */
    public function addToFavorites(Request $request, int $post_id)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } else {
            // The user is logged in.

            $post = Post::findOrFail($post_id);

            // Check if the post is already in the user's favorites.
            if (Auth::user()->favorites()->where('post_id', $post_id)->exists()) {
                return response()->json(['message' => 'This post is already in your favorites.'], 400);
            }
    
            // Add the post to the user's favorites.
            Auth::user()->favorites()->attach($post_id);

            return response()->json(['message' => 'Post added to favorites successfully.']);
        }
    }

    /**
     * Remove post from favorites.
     */
    public function removeFromFavorites(Request $request, int $post_id)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } else {
            // The user is logged in.

            $post = Post::findOrFail($post_id);

            // Check if the post is already in the user's favorites.
            if (!Auth::user()->favorites()->where('post_id', $post_id)->exists()) {
                return response()->json(['message' => 'This post is not in your favorites.'], 400);
            }
    
            // Add the post to the user's favorites.
            Auth::user()->favorites()->detach($post_id);

            return response()->json(['message' => 'Post removed from favorites successfully.']);
        }
    }
}
