<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        // Get the user.
        $user = User::findOrFail($id);

        // Check if the current user can see (show) the user profile.
        $this->authorize('show', User::class);  

        $posts = $user->posts()->get();
        $following = $user->following()->get();
        $followers = $user->followers()->get();

        // Use the pages.user template to display the user.
        return view('pages.user', [
            'user' => $user, 
            'posts' => $posts, 
            'following' => $following, 
            'followers' => $followers
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function showProfileEditorForm(User $user)
    {
        $this->authorize('showProfileEditorForm', $user);
        return view('pages.profileEditor', [
            'user' => $user, 
            'old' => [
                'username' => $user->username,
                'email' => $user->email
            ] 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', User::class);
        $user = Auth::user();

        $request->validate([
            'username' => 'max:50|unique:users,username,'.$user->id,
            'email' => 'email|max:250|unique:users,email,'.$user->id
        ]);

        $user->username = $request->input('username');
        $user->email = $request->input('email');

        $user->save();
        return redirect('users/'.$user->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(User $id)
    {
        // Find the user.
        $user = User::find($id);

        // Check if the current user is authorized to delete this user.
        $this->authorize('delete', $user);

        // Delete the user and return it as JSON.
        $user->delete();
        return response()->json($user);
    }

    /**
     * Follow a specific user.
     */
    public function follow(Request $request, User $user) {

        $this->authorize('follow', User::class);

        $followed = User::findOrFail($user->id);

        $isFollowed = $followed->followers()->where('id', Auth::user()->id)->exists();

        if (!$isFollowed) {
            Auth::user()->followers()->attach($user->id);
        }
    }

    /**
     * Unfollow a specific user.
     */
    public function unfollow(Request $request, User $user) {

        $this->authorize('unfollow', User::class);

        $followed = User::findOrFail($user->id);

        $isFollowed = $followed->followers()->where('id', Auth::user()->id)->exists();

        if ($isFollowed) {
            Auth::user()->followers()->detach($user->id);
        }
    }
}
