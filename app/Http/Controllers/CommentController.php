<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\CommentVote;
use Illuminate\Http\Request;

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

        } else {
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
    }

    /**
     * Create a new resource.
     */
    public function create(Request $request, int $post_id)
    {
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
        $comment->save();
        return response()->json($comment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function showCommentEditorForm(int $comment_id)
    {
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
        // Get the comment.
        $comment = Comment::findOrFail($post_id);

        $this->authorize('update', $comment);

        $request->validate([
            'body' => 'required|string|max:2000'
        ]);

        $comment->body = $request->input('body');

        $comment->save();
        return redirect('posts/'.$comment->post_id.'/comments');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $comment_id)
    {
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
        $comment = Comment::findOrFail($comment_id);

        // Create a blank new vote.
        $vote = new CommentVote();

        // Check if the current user is authorized to vote in this comment.
        $this->authorize('vote', $comment);

        $request->validate([
            'is_like' => 'required|boolean',
        ]);
        
        // Set vote details.
        $vote->is_like = $request->input('is_like');
        $vote->user_id = Auth::user()->id;
        $vote->comment_id = $comment->comment_id;

        // Save the vote and return it as JSON.
        $vote->save();
        return response()->json($vote);
    }

    /**
     * Edit the specific resource.
     */
    public function editVote(Request $request, int $comment_id)
    {
        $comment = Comment::findOrFail($comment_id);

        
        $request->validate([
            'is_like' => 'required|boolean',
        ]);
        
        $vote = CommentVote::firstOrCreate(
            [
                'user_id' => Auth::user()->id,
                'comment_id' => $comment_id
            ],
            [
                'is_like' => $request->input('is_like')
            ]
            );
            
        // Check if the current user is authorized to edit this vote.
        $this->authorize('editVote', $comment, $vote);

        // Save the vote and return it as JSON.
        $vote->save();
        return response()->json($vote);
    }

    /**
     * Remove the specific resource.
     */
    public function removeVote(Request $request, int $comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        
        $vote = CommentVote::where('comment_id', $comment_id)
                        ->where('user_id', Auth::user()->id)
                        ->first();

        if($vote) {
            // Check if the current user is authorized to vote in this comment.
            $this->authorize('removeVote', $comment, $vote);

            // Delete the vote and return it as JSON.
            $vote->delete();
            return response()->json($vote);
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
        // Create a blank new comment.
        $reply = new Comment();

        // Get parent comment.
        $comment = Comment::findOrFail($comment_id);

        // Check if the current user is authorized to create this reply.
        $this->authorize('reply', $reply);

        $request->validate([
            'body' => 'required|max:2000'
           ]);
        
        // Set reply details.
        $reply->body = $request->input('body');
        $reply->user_id = Auth::user()->id;
        $reply->post_id = $comment->post_id;
        $reply->parent_comment_id = $comment->comment_id;

        // Save the comment and return it as JSON.
        $reply->save();
        return response()->json($reply);
    }
}
