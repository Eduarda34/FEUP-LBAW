<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\Report;
use App\Models\UserReport;
use App\Models\Notification;
use App\Models\BlockedUser;
use App\Models\SystemManager;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        if ($user->blocked && !Auth::user()->system_managers) {
            return abort(403, 'User unavailable.');
        }

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
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }
        
        $users = User::whereNotIn('id', BlockedUser::query()->select('blocked_id'))
            ->whereNotIn('id', SystemManager::query()->select('sm_id'))
            ->orderBy('reputation', 'desc')
            ->get();

        $post_controller = new PostController();

        // Render the users view.
        return view('pages.users', [
            'users' => $users,
            'suggested_news' => $post_controller->getPopularPosts(),
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
            'bio' => 'nullable|string|max:300',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        // Update the user's information
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->bio = $request->input('bio');
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('user', 'public'); // Store in the "storage/app/public/user" directory
            $user->profile_picture = $path;
        }
        $user->save();

        // Redirect to the user's profile
        return redirect('/users/' . $user->id)->with('success', 'Profile updated successfully!');
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

        $isFollowed = $user->followers()->where('id', Auth::id())->exists();

        if (!$isFollowed) {
            Auth::user()->following()->attach($user->id);
        }

        return response()->json([
            'user_id' => $user->id,
        ], 201);
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

        $isFollowed = $user->followers()->where('id', Auth::id())->exists();
        
        if ($isFollowed) {
            Auth::user()->following()->detach($user->id);
        }

        return response()->json([
            'user_id' => $user->id,
        ], 200);
    }

    /**
     * Follow a specific category.
     */
    public function followCategory(int $category_id) 
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

        return response()->json([
            'category_id' => $category_id,
        ], 201);
    }

    /**
     * Unfollow a specific category.
     */
    public function unfollowCategory(int $category_id) 
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
            Auth::user()->followed_categories()->detach($category->category_id);
        }

        return response()->json([
            'category_id' => $category_id,
        ], 200);
    }

    /**
     * Show the form for reporting the specified user.
     */
    public function showReportForm(int $id)
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

        return view('pages.reportCreator', [ 'user' => $user ]);
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

        return redirect('/users/'.$reportedUser->id)->with('success', 'Report created successfully.')->setStatusCode(201);
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
        $unviewedNotifications = Auth::user()->notifications()->where('viewed', false)->orderBy('time', 'desc')->get();
        $viewedNotifications = Auth::user()->notifications()->where('viewed', true)->orderBy('time', 'desc')->get();

        return view('pages.notifications', [
            'unviewedNotifications' => $unviewedNotifications,
            'viewedNotifications' => $viewedNotifications,
        ]);
    }

    /**
     * Mark a specific notification as viewed/not viewed.
     */
    public function viewNotification(Request $request, int $notification_id)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the notification by ID
        $notification = Notification::findOrFail($notification_id);

        // Authorize the update action
        if (Auth::id() !== $notification->user_id) {
            return abort(403, 'Forbidden request');
        }

        // Validate the input
        $request->validate([
            'viewed' => 'required|boolean',
        ]);

        // Update the notification's information
        $notification->viewed = $request->input('viewed');
        $notification->save();

        // Return JSON response
        return response()->json([
            'notification_id' => $notification->notification_id,
            'viewed' => $request->input('viewed')
        ], 200);
    }

    /**
     * Mark all user notifications as viewed/not viewed.
     */
    public function viewAllNotifications(Request $request)
    {   
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get user notifications
        $notifications = Auth::user()->notifications();

        // Validate the input
        $request->validate([
            'viewed' => 'required|boolean',
        ]);

        // Update notifications' information
        foreach ($notifications as $notification) {
            // Authorize the update action
            $this->authorize('viewNotification', $notification);

            $notification->viewed = $request->input('viewed');
            $notification->save();
        }

        // Redirect to the notifications page
        return redirect()->back()->with('success', 'Notifications status updated successfully.')->setStatusCode(200);
    }

    /**
     * Remove a specific notification.
     */
    public function deleteNotification(int $notification_id)
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get the notification by ID
        $notification = Notification::findOrFail($notification_id);
        
        // Check if the current user is authorized to delete this notification.
        if (Auth::id() !== $notification->user_id) {
            return abort(403, 'Forbidden request');
        }

        $notification->delete();

        // Return JSON response
        return response()->json([
            'notification_id' => $notification->notification_id,
        ], 200);
    }

    /**
     * Remove all user notifications.
     */
    public function deleteAllNotifications()
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }
        if (Auth::user()->blocked) {
            // User blocked, redirect to logout.
            return redirect('/logout');
        }

        // Get user notifications
        $notifications = Auth::user()->notifications();

        foreach ($notifications as $notification) {
            // Check if the current user is authorized to delete this notification.
            $this->authorize('deleteNotification', $notification);

            $notification->delete();
        }

        // Redirect to the notifications page.
        return response()->back()->with('success', 'Notifications deleted successfully.')->setStatusCode(204);
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate(['password' => 'required',]);

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['password' => 'Password is incorrect.']);
        }

        $user->delete();

        Auth::logout();
        return redirect('/')->with('success', 'Your account has been deleted successfully.');
    }
}