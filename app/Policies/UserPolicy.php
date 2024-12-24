<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function list(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function show(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can access the profile editor form.
     */
    public function showProfileEditorForm(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id == $model->id || $user->system_managers;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can follow other user.
     */
    public function follow(User $user, User $model): bool
    {
        return $user->id !== $model->id;
    }

    /**
     * Determine whether the user can unfollow other user.
     */
    public function unfollow(User $user, User $model): bool
    {
        return $user->id !== $model->id;
    }

    /**
     * Determine whether the user can follow a category.
     */
    public function followCategory(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can unfollow a category.
     */
    public function unfollowCategory(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can mark a notification as viewed/not viewed.
     */
    public function viewNotification(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine whether the user can delete a notification.
     */
    public function deleteNotification(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }
}
