<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\Report;
use App\Models\UserReport;
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
        if (Auth::check()) {
            if (Auth::user()->blocked) {
                // User blocked, redirect to logout.
                return redirect('/logout');
            }
        }

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
    public function showProfileEditorForm(int $id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the user.
        $user = User::findOrFail($id);

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
    public function update(Request $request, int $id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the user by ID
        $user = User::findOrFail($id);

        // Authorize the update action
        $this->authorize('update', $user);

        // Validate the input
        $request->validate([
            'username' => 'required|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:250|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        // Update the user's information
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('user', 'public'); // Store in the "storage/app/public/user" directory
            $user->profile_picture = $path;
        }
        $user->save();

        // Redirect to the user's profile
        return redirect('users/' . $user->id)->with('success', 'Profile updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

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
    public function follow(Request $request, int $id) 
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the user.
        $user = User::findOrFail($id);

        $this->authorize('follow', $user);

        $isFollowed = $user->followers()->where('follower_id', Auth::user()->id)->exists();

        if (!$isFollowed) {
            Auth::user()->followers()->attach($user->id);
        }
    }

    /**
     * Unfollow a specific user.
     */
    public function unfollow(Request $request, int $id) 
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the user.
        $user = User::findOrFail($id);
        
        $this->authorize('unfollow', $user);

        $isFollowed = $user->followers()->where('follower_id', Auth::user()->id)->exists();

        if ($isFollowed) {
            Auth::user()->followers()->detach($user->id);
        }
    }

    /**
     * Follow a specific category.
     */
    public function followCategory(Request $request, int $category_id) 
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // See if category exists
        $category = Category::findOrFail($category_id);

        $this->authorize('followCategory', User::class);

        // Check if category is already followed
        $isFollowed = $category->users()->where('user_id', Auth::user()->id)->exists();

        if (!$isFollowed) {
            Auth::user()->followed_categories()->attach($category->category_id);
        }
    }

    /**
     * Unfollow a specific category.
     */
    public function unfollowCategory(Request $request, int $category_id) 
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // See if category exists
        $category = Category::findOrFail($category_id);

        $this->authorize('unfollowCategory', User::class);

        // Check if category is already followed
        $isFollowed = $category->users()->where('user_id', Auth::user()->id)->exists();

        if ($isFollowed) {
            Auth::user()->followed_categories()->attach($category->category_id);
        }
    }

    /**
     * Report a specific user.
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

        // Find the user being reported.
        $reportedUser = User::findOrFail($id);

        // Prevent reporting oneself.
        if ($reportedUser->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot report yourself.')->setStatusCode(403);
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

        // Add the report to the `user_report` table.
        $userReport = new UserReport();
        $userReport->report_id = $report->report_id;
        $userReport->reported_id = $reportedUser->id;
        $userReport->save();

        return redirect()->back()->with('success', 'Report created successfully.')->setStatusCode(201);;
    }

    /**
     * Display a listing of the user notifications.
     */
    public function showNotifications(Request $request)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Sort by most recent.
        $notifications = Auth::user()->notifications()->orderBy('time', 'desc')->get();

        // Render the notifications view.
        return view('pages.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
