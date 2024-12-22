<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Models\CommentVote;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can access the comment editor form.
     */
    public function showCommentEditorForm(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        if($user->system_managers()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can vote on a comment.
     */
    public function vote(User $user, Comment $comment): bool
    {
        return $user->id !== $comment->user_id;
    }

    /**
     * Determine whether the user can edit a vote.
     */
    public function editVote(User $user, Comment $comment, CommentVote $vote): bool
    {
        return ($user->id !== $comment->user_id && $user->id === $vote->user_id && $comment->comment_id === $vote->comment_id);
    }

    /**
     * Determine whether the user can remove a vote.
     */
    public function removeVote(User $user, Comment $comment, CommentVote $vote): bool
    {
        return ($user->id !== $comment->user_id && $user->id === $vote->user_id && $comment->comment_id === $vote->comment_id);
    }

    /**
     * Determine whether the user can reply to a comment.
     */
    public function reply(User $user): bool
    {
        return true;
    }
}
