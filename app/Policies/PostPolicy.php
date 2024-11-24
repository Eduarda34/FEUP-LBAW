<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Models\PostVote;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view any models by category.
     */
    public function listByCategory(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function show(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can access the post creator form.
     */
    public function showPostCreatorForm(): bool
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
     * Determine whether the user can access the post editor form.
     */
    public function showPostEditorForm(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
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
     * Determine whether the user can vote on a post.
     */
    public function vote(User $user, Post $post): bool
    {
        return $user->id !== $post->user_id;
    }

    /**
     * Determine whether the user can edit a vote.
     */
    public function editVote(User $user, Post $post, PostVote $vote): bool
    {
        return ($user->id !== $post->user_id && $user->id === $vote->user_id && $post->post_id === $vote->post_id);
    }

    /**
     * Determine whether the user can remove a vote.
     */
    public function removeVote(User $user, Post $post, PostVote $vote): bool
    {
        return ($user->id !== $post->user_id && $user->id === $vote->user_id && $post->post_id === $vote->post_id);
    }
}
