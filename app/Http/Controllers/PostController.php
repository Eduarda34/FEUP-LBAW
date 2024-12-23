<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostVote;
use App\Models\Category;
use App\Models\BlockedUser;
use App\Models\Report;
use App\Models\PostReport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{   

    public function getPopularPosts()
    {
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
        ->whereNotIn('user_id', BlockedUser::query()->select('blocked_id'))
        ->get()
        ->sortByDesc(function ($post) {
            return ($post->likes_count - $post->dislikes_count) + $post->comments_count + $post->owner->reputation;
        });
        return $posts;
    }

    private function getCustomPosts()
    {
        $userId = Auth::id();

        return Post::query()
            // Join with post categories
            ->leftJoin('post_categories', 'posts.post_id', '=', 'post_categories.post_id')
            // Join with categories the user follows
            ->leftJoin('user_category', function ($join) use ($userId) {
                $join->on('post_categories.category_id', '=', 'user_category.category_id')
                    ->where('user_category.user_id', '=', $userId);
            })
            // Join with followed users
            ->leftJoin('follows', function ($join) use ($userId) {
                $join->on('posts.user_id', '=', 'follows.followed_id')
                    ->where('follows.follower_id', '=', $userId);
            })
            ->select('posts.*')
            ->whereNotIn('posts.user_id', BlockedUser::query()->select('blocked_id'))
            ->orderByRaw('
                CASE 
                    WHEN follows.follower_id IS NOT NULL AND user_category.user_id IS NOT NULL THEN 1 -- Followed user + followed category
                    WHEN follows.follower_id IS NOT NULL THEN 2 -- Followed user
                    WHEN user_category.user_id IS NOT NULL THEN 3 -- Followed category
                    ELSE 4 -- Everything else
                END
            ')
            ->orderBy('posts.created_at', 'desc') // Fallback to recency
            ->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        // Determine the feed type from the query parameter.(Default 'recent')
        if (!Auth::check()) {
            if ($request->query('feed') == 'recent' || $request->query('feed') == 'popular') {
                $feedType = $request->query('feed');
            } else {
                $feedType = 'recent';
            }
        } else {
            if (Auth::user()->blocked) {
                // User blocked, redirect to logout.
                return redirect('/logout');
            }
            $feedType = $request->query('feed', 'custom');
        }

        // Fetch posts based on the feed type.
        switch ($feedType) {
            case 'popular':
                $posts = $this->getPopularPosts();
                break;
            case 'custom':
                $posts = $this->getCustomPosts();
                break;
            case 'recent':
            default:
                // Sort by most recent.
                $posts = Post::whereNotIn('user_id', BlockedUser::query()->select('blocked_id'))
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
        }

        $categories = Category::all();

        // Render the posts view.
        return view('pages.posts', [
            'posts' => $posts,
            'feedType' => $feedType,
            'categories' => $categories,
        ]);
    }

    /**
     * Display a listing of the posts by category.
     */
    public function listByCategory(int $category_id)
    {   
        if (Auth::check()) {
            if (Auth::user()->blocked) {
                // User blocked, redirect to logout.
                return redirect('/logout');
            }
        }

        // Get category
        $category = Category::findOrFail($category_id);

        $categories = Category::all();
        
        // Get all posts from the category.
        $posts = $category->posts()->orderBy('post_id', 'desc')->get();

        // Use the pages.posts template to display all posts.
        return view('pages.posts', [
            'posts' => $posts,
            'feedType' => 'category',
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function showPostCreatorForm()
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        $this->authorize('showPostCreatorForm', Post::class);
        
        // Fetch all categories to display in the form.
        $categories = Category::all();

        return view('pages.postCreator', compact('categories'));
    }

    /**
     * Extract the first sentence from a given text.
     */
    private function extractFirstSentence(string $text): string
    {
        $sentences = preg_split('/(?<!\w\.\w.)(?<![A-Z][a-z]\.)(?<=\.|\?|!)\s/', $text, 2);
        return $sentences[0] ?? $text;
    }

    /**
     * Create a new resource.
     */
    public function create(Request $request)
    {        
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Create a blank new post.
        $post = new Post();

        // Check if the current user is authorized to create this post.
        $this->authorize('create', $post);

        $request->validate([
            'title' => 'required|unique:posts|max:255',
            'synopsis' => 'nullable|string|max:300',
            'body' => 'required|string|max:10000',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,category_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);
        
        // Set post details.
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = Auth::user()->id;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('post', 'public'); // Store in the "storage/app/public/post" directory
            $post->image = $path;
        }

        // Use the provided synopsis or the first sentence of the body.
        $post->synopsis = $request->input('synopsis') ?? $this->extractFirstSentence($request->input('body'));

        // Save the post and redirect.
        $post->timestamps = false; // Disable timestamps temporarily
        $post->save();
        $post->timestamps = true;
        $post->categories()->attach($request->input('categories'));
        return redirect('posts/'.$post->post_id)->with('success', 'Redirect after creating new post.')->setStatusCode(302);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $post_id)
    {
        if (Auth::check()) {
            if (Auth::user()->blocked) {
                // User blocked, redirect to logout.
                return redirect('/logout');
            }
        }

        // Get the post.
        $post = Post::findOrFail($post_id);
        if ($post->owner->blocked) {
            return abort(403, 'Post unavailable.');
        }

        $suggestedNews = $this->getPopularPosts()->reject(function ($news) use ($post_id) {
            return $news->post_id === $post_id;
        });

        // Use the pages.post template to display the post.
        return view('pages.post', [
            'post' => $post,
            'suggested_news' => $suggestedNews,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function showPostEditorForm(int $post_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the post.
        $post = Post::findOrFail($post_id);
        
        $this->authorize('showPostEditorForm', $post);

        // Fetch all categories to display in the form.
        $categories = Category::all();
        
        return view('pages.postEditor', [
            'post' => $post, 
            'categories' => $categories,
            'old' => [
                'title' => $post->title,
                'body' => $post->body,
                'categories' => $post->categories,
                'image' => $post->image
            ] 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $post_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the post.
        $post = Post::findOrFail($post_id);

        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:255',
            'synopsis' => 'nullable|string|max:300',
            'body' => 'required|string|max:10000',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,category_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $post->title = $request->input('title');
        $post->body = $request->input('body');
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('post', 'public'); // Store in the "storage/app/public/post" directory
            $post->image = $path;
        }
        // Use the provided synopsis or the first sentence of the body.
        $post->synopsis = $request->input('synopsis') ?? $this->extractFirstSentence($request->input('body'));

        $post->save();
        $post->categories()->sync($request->input('categories'));
        return redirect('posts/'.$post->post_id)->with('success', 'Post updated successfully!')->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $post_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

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
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        $post = Post::findOrFail($post_id);

        $existingVote = PostVote::where('user_id', Auth::id())
                                ->where('post_id', $post->post_id)
                                ->first();

        $isLike = $request->input('is_like') === '1' ? true : false;

        if(!$existingVote){
            // Create a blank new vote.
            $vote = new PostVote();

            // Check if the current user is authorized to vote in this post.
            $this->authorize('vote', $post);

            $request->validate([
                'is_like' => 'required|boolean',
            ]);
            
            // Set vote details.
            $vote->is_like = $isLike;
            $vote->user_id = Auth::user()->id;
            $vote->post_id = $post->post_id;

            // Save the vote and return it as JSON.
            $vote->save();
            $like_count = $post->votes()->where('is_like', true)->count();
            $dislike_count = $post->votes()->where('is_like', false)->count();
            return response()->json([
                'post_id' => $vote->post_id,
                'is_like' => $isLike,
                'vote_count' => [
                    'up' => $like_count,
                    'down' => $dislike_count
                ]
            ], 201);
        } else if($existingVote->is_like !== $isLike) {
            return $this->editVote($request, $post_id);
        } else if($existingVote->is_like === $isLike) {
            return $this->removeVote($post_id);
        } else {
            return response()->json([
                'error' => 'Request is invalid or malformed.'
            ], 400);
        }
    }

    /**
     * Edit the specific resource.
     */
    public function editVote(Request $request, int $post_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        $isLike = $request->input('is_like') === '1' ? true : false;
        $post = Post::findOrFail($post_id);

        
        $request->validate([
            'is_like' => 'required|boolean',
        ]);
        
        $vote = PostVote::where('user_id', Auth::user()->id)
                        ->where('post_id', $post_id)
                        ->first();

        if (!$vote) {
            $vote = new PostVote();
            $vote->user_id = Auth::user()->id;
            $vote->post_id = $post_id;
        }

        $this->authorize('editVote', [$post, $vote]);

        $vote->is_like = $isLike;

        // Save the vote and redirect
        $vote->save();
        $like_count = $post->votes()->where('is_like', true)->count();
        $dislike_count = $post->votes()->where('is_like', false)->count();
        return response()->json([
            'post_id' => $vote->post_id,
            'is_like' => $isLike,
            'vote_count' => [
                'up' => $like_count,
                'down' => $dislike_count
            ]
        ], 200);
    }

    /**
     * Remove the specific resource.
     */
    public function removeVote(int $post_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        $post = Post::findOrFail($post_id);
        
        $vote = PostVote::where('post_id', $post_id)
                        ->where('user_id', Auth::user()->id)
                        ->first();

        if($vote) {
            // Check if the current user is authorized to vote in this post.
            $this->authorize('removeVote', [$post, $vote]);

            // Delete the vote and return it as JSON.
            $vote->delete();
            $like_count = $post->votes()->where('is_like', true)->count();
            $dislike_count = $post->votes()->where('is_like', false)->count();
            return response()->json([
                'post_id' => $post_id,
                'vote_count' => [
                    'up' => $like_count,
                    'down' => $dislike_count
                ]
            ], 200);
        }
        
        return response()->json([
            'error' => 'Vote not found.',
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
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // The user is logged in.

        // Get all posts.
        $posts = Auth::user()->favorites()->get();

        $categories = Category::all();

        // Use the pages.posts template to display all posts.
        return view('pages.posts', [
            'posts' => $posts,
            'feedType' => 'favorites',
            'categories' => $categories
        ]);
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
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // The user is logged in.

        $post = Post::findOrFail($post_id);

        // Check if the post is already in the user's favorites.
        if (Auth::user()->favorites()->where('user_favorites.post_id', $post_id)->exists()) {
            return response()->json(['message' => 'This post is already in your favorites.'], 400);
        }

        // Add the post to the user's favorites.
        Auth::user()->favorites()->attach($post_id);

        return response()->json([
            'post_id' => $post_id,
        ], 201);
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
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // The user is logged in.

        $post = Post::findOrFail($post_id);

        // Check if the post is already in the user's favorites.
        if (!Auth::user()->favorites()->where('user_favorites.post_id', $post_id)->exists()) {
            return response()->json(['message' => 'This post is not in your favorites.'], 400);
        }

        // Add the post to the user's favorites.
        Auth::user()->favorites()->detach($post_id);

        return response()->json([
            'post_id' => $post_id
        ], 200);
    }
    
    /**
     * Show the form for reporting the specified post.
     */
    public function showReportForm(int $post_id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the post.
        $post = Post::findOrFail($post_id);

        return view('pages.reportCreator', [ 'post' => $post ]);
    }

    /**
     * Report a specific post.
     */
    public function report(Request $request, int $id) 
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Find the post being reported.
        $reportedPost = Post::findOrFail($id);

        // Prevent reporting oneself.
        if ($reportedPost->user_id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot report your own post.')->setStatusCode(403);
        }

        // Validate the request.
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        // Create a new report.
        $report = new Report();
        $report->reporter_id = Auth::id();
        $report->reason = $request->input('reason');
        $report->save();

        // Add the report to the `post_report` table.
        $postReport = new PostReport();
        $postReport->report_id = $report->report_id;
        $postReport->post_id = $reportedPost->post_id;
        $postReport->save();

        return redirect('/posts/'.$reportedPost->post_id)->with('success', 'Report created successfully.')->setStatusCode(201);;
    }
}
