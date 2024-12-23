<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\CommentVote;
use App\Models\Reply;
use App\Models\Report;
use App\Models\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(int $post_id)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to post.
            return redirect('/posts/'.$post_id);

        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // The user is logged in.

        // Get post.
        $post = Post::findOrFail($post_id);

        $comments = $post->comments()->orderBy('created_at', 'desc')->get();

        // Check if the current user can list the comments.
        $this->authorize('list', Comment::class);

        // The current user is authorized to list comments.

        // Use the pages.comments template to display all comments.
        return view('pages.comments', [
            'comments' => $comments
        ]);
    }

    /**
     * Create a new resource.
     */
    public function create(Request $request, int $post_id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Create a blank new comment.
        $comment = new Comment();

        // Check if the current user is authorized to create this comment.
        $this->authorize('create', $comment);

        $request->validate([
            'body' => 'required|max:2000'
           ]);
        
        // Set comment details.
        $comment->body = $request->input('body');
        $comment->user_id = Auth::user()->id;
        $comment->post_id = $post_id;

        // Save the comment and return it as JSON.
        $comment->timestamps = false; // Disable timestamps temporarily
        $comment->save();
        $comment->timestamps = true;
        return redirect('posts/'.$comment->post_id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function showCommentEditorForm(int $comment_id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the comment.
        $comment = Comment::findOrFail($comment_id);

        $this->authorize('showCommentEditorForm', $comment);
        return view('pages.commentEditor', [
            'comment' => $comment, 
            'old' => [
                'body' => $comment->body
            ] 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $comment_id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the comment.
        $comment = Comment::findOrFail($comment_id);

        $this->authorize('update', $comment);

        $request->validate([
            'body' => 'required|string|max:2000'
        ]);

        $comment->body = $request->input('body');

        $comment->save();
        return redirect('posts/'.$comment->post_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $comment_id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Find the comment.
        $comment = Comment::findOrFail($comment_id);

        // Check if the current user is authorized to delete this comment.
        $this->authorize('delete', $comment);

        // Check if the comment has votes or replies.
        if ($comment->replies()->exists() || $comment->votes()->exists()) {
            return response()->json([
                'error' => 'The comment cannot be deleted because it has associated votes or replies.'
            ], 409);
        }

        // Delete the comment and return it as JSON.
        $comment->delete();
        return response()->json($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(int $comment_id)
    {
        // Find the comment.
        $comment = Comment::findOrFail($comment_id);

        // Check if the current user is authorized to delete this comment.
        $this->authorize('forceDelete', Comment::class);

        // Delete the comment and return it as JSON.
        $comment->delete();
        return response()->json($comment);
    }

    /**
     * Vote the specific resource.
     */
    public function vote(Request $request, int $comment_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        $comment = Comment::findOrFail($comment_id);

        $existingVote = CommentVote::where('user_id', Auth::id())
                                ->where('comment_id', $comment->comment_id)
                                ->first();
        
        $isLike = $request->input('is_like') === '1' ? true : false;

        if(!$existingVote) {
            // Create a blank new vote.
            $vote = new CommentVote();

            // Check if the current user is authorized to vote in this comment.
            $this->authorize('vote', $comment);

            $request->validate([
                'is_like' => 'required|boolean',
            ]);
            
            // Set vote details.
            $vote->is_like = $isLike;
            $vote->user_id = Auth::user()->id;
            $vote->comment_id = $comment->comment_id;

            // Save the vote and return it as JSON.
            $vote->save();
            $like_count = $comment->votes()->where('is_like', true)->count();
            $dislike_count = $comment->votes()->where('is_like', false)->count();
            return response()->json([
                'comment_id' => $vote->comment_id,
                'is_like' => $isLike,
                'vote_count' => [
                    'up' => $like_count,
                    'down' => $dislike_count
                ]
            ], 201);
        } else if($existingVote->is_like !== $isLike) {
            return $this->editVote($request, $comment_id);
        } else if($existingVote->is_like === $isLike) {
            return $this->removeVote($comment_id);
        } else {
            return response()->json([
                'error' => 'Request is invalid or malformed.'
            ], 400);
        }
        
    }

    /**
     * Edit the specific resource.
     */
    public function editVote(Request $request, int $comment_id)
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
        $comment = Comment::findOrFail($comment_id);

        
        $request->validate([
            'is_like' => 'required|boolean',
        ]);
            
        $vote = CommentVote::where('user_id', Auth::user()->id)
                    ->where('comment_id', $comment_id)
                    ->first();

        if (!$vote) {
            $vote = new CommentVote();
            $vote->user_id = Auth::user()->id;
            $vote->comment_id = $comment_id;
        }

        // Check if the current user is authorized to edit this vote.
        $this->authorize('editVote', [$comment, $vote]);

        $vote->is_like = $isLike;

        // Save the vote and return it as JSON.
        $vote->save();

        $like_count = $comment->votes()->where('is_like', true)->count();
        $dislike_count = $comment->votes()->where('is_like', false)->count();
        return response()->json([
            'comment_id' => $vote->comment_id,
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
    public function removeVote(int $comment_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        $comment = Comment::findOrFail($comment_id);
        
        $vote = CommentVote::where('comment_id', $comment_id)
                        ->where('user_id', Auth::user()->id)
                        ->first();

        if($vote) {
            // Check if the current user is authorized to remove a vote in this comment.
            $this->authorize('removeVote', [$comment, $vote]);

            // Delete the vote and return it as JSON.
            $vote->delete();
            $like_count = $comment->votes()->where('is_like', true)->count();
            $dislike_count = $comment->votes()->where('is_like', false)->count();
            return response()->json([
                'comment_id' => $comment_id,
                'vote_count' => [
                    'up' => $like_count,
                    'down' => $dislike_count
                ]
            ], 200);
        }
        
        return response()->json([
            'message' => 'Vote not found.',
            'comment_id' => $comment_id,
            'user_id' => Auth::user()->id,
        ], 404);
    }

    /**
     * Reply to a comment.
     */
    public function reply(Request $request, int $comment_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }
        
        // Get parent comment.
        $comment = Comment::findOrFail($comment_id);
        
        // Check if the current user is authorized to create this reply.
        $this->authorize('reply', $comment);
        
        $request->validate([
            'body' => 'required|max:2000'
        ]);
        
        // Create a blank new comment.
        $comment_reply = new Comment();
        // Set reply details.
        $comment_reply->body = $request->input('body');
        $comment_reply->user_id = Auth::user()->id;
        $comment_reply->post_id = $comment->post_id;
        $comment_reply->timestamps = false; // Disable timestamps temporarily
        $comment_reply->save();
        $comment_reply->timestamps = true;
        
        $reply = new Reply();
        $reply->parent_comment_id = $comment->comment_id;
        $reply->comment_id = $comment_reply->comment_id;
        $reply->save();

        return redirect('posts/'.$comment_reply->post_id);
    }
    
    /**
     * Show the form for reporting the specified comment.
     */
    public function showReportForm(int $comment_id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the comment.
        $comment = Comment::findOrFail($comment_id);

        return view('pages.reportCreator', [ 'comment' => $comment ]);
    }

    /**
     * Report a specific comment.
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

        // Find the comment being reported.
        $reportedComment = Comment::findOrFail($id);

        // Prevent reporting oneself.
        if ($reportedComment->user_id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot report your own comment.')->setStatusCode(403);
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

        // Add the report to the `comment_report` table.
        $commentReport = new CommentReport();
        $commentReport->report_id = $report->report_id;
        $commentReport->comment_id = $reportedComment->comment_id;
        $commentReport->save();

        return redirect('/posts/'.$reportedComment->post_id)->with('success', 'Report created successfully.')->setStatusCode(201);;
    }
}
