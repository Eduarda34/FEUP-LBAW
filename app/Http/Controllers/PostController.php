<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        } else {
            // The user is logged in.

            // Get posts for user ordered by id.
            $posts = Auth::user()->posts()->orderBy('post_id')->get();

            // Check if the current user can list the posts.
            $this->authorize('list', Post::class);

            // The current user is authorized to list posts.

            // Use the pages.posts template to display all posts.
            return view('pages.posts', [
                'posts' => $posts
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if the current user is authorized to create this post.
        $this->authorize('create', $post);

        $request->validate([
            'title' => 'required|unique:posts|max:255',
            'body' => 'required'
           ]);
        
        // Create a blank new post.
        $post = new Post();
        // Set post details.
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = Auth::user()->id;

        // Save the post and return it as JSON.
        $post->save();
        return response()->json($post);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $id)
    {
        // Get the post.
        $post = Post::findOrFail($id);

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
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Post $id)
    {
        // Find the post.
        $post = Post::find($id);

        // Check if the current user is authorized to delete this post.
        $this->authorize('delete', $post);

        // Delete the post and return it as JSON.
        $post->delete();
        return response()->json($post);
    }
}
