<?php

namespace App\Http\Controllers;

use App\Models\SystemManager;
use App\Models\UserReport;
use App\Models\PostReport;
use App\Models\CommentReport;
use App\Models\BlockedUser;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function listReports()
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        }
        if (!Auth::user()->system_managers) {
            return redirect()->back()
                ->with('error', 'Permission denied.')
                ->setStatusCode(403);
        }

        $userReports = UserReport::all();
        $postReports = PostReport::all();
        $commentReports = CommentReport::all();

        $reports = $userReports
            ->concat($postReports)
            ->concat($commentReports)
            ->sortBy(function ($report) {
                return [
                    $report->resolved_time ?? PHP_INT_MIN,
                    $report->time
                ];
            });

        $blocked = BlockedUser::all();

        return view('pages.reports', [
            'reports' => $reports,
            'blocked' => $blocked
        ]);
    }

    /**
     * Blocks a specific user.
     */
    public function blockUser(Request $request, $id)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');

        }
        if (!Auth::user()->system_managers) {
            return redirect()->back()
                ->with('error', 'Permission denied.')
                ->setStatusCode(403);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
            'report_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $exists = UserReport::where('report_id', $value)->exists() ||
                              PostReport::where('report_id', $value)->exists() ||
                              CommentReport::where('report_id', $value)->exists();
        
                    if (!$exists) {
                        $fail('The selected ' . $attribute . ' is invalid.');
                    }
                }
            ],
        ]);

        $user = User::findOrFail($id);

        // Check if the user is already blocked.
        if ($user->blocked()) {
            return redirect()->back()->withErrors(['error' => 'User is already blocked.']);
        }

        // Block the user.
        $blocked = new BlockedUser();
        $blocked->blocked_id = $id;
        $blocked->reason = $request->input('reason');
        $blocked->report_id = $request->input('report_id');

        return redirect()->back()
                ->with('success', 'User successfully blocked.')
                ->setStatusCode(204);
    }

    /**
     * Unblocks a specific user.
     */
    public function unblockUser($id)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }

        // Check if the user is a system manager.
        if (!Auth::user()->system_managers) {
            return redirect()->back()
                ->with('error', 'Permission denied.')
                ->setStatusCode(403);
        }

        $user = User::findOrFail($id);

        // Check if the user is actually blocked.
        $blockedUser = BlockedUser::where('blocked_id', $id)->first();
        if (!$blockedUser) {
            return redirect()->back()->withErrors(['error' => 'User is not blocked.']);
        }

        // Unblock the user.
        $blockedUser->delete();

        return redirect()->back()
            ->with('success', 'User successfully unblocked.')
            ->setStatusCode(204);
    }

    /**
     * Resolves a specific report.
     */
    public function resolveReport($report_id)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }

        // Check if the user is a system manager.
        if (!Auth::user()->system_managers) {
            return redirect()->back()
                ->with('error', 'Permission denied.')
                ->setStatusCode(403);
        }

        // Find the report (check all report tables).
        $report = UserReport::find($report_id)
            ?? PostReport::find($report_id)
            ?? CommentReport::find($report_id);

        if (!$report) {
            return redirect()->back()
                ->with('error', 'Report not found.')
                ->setStatusCode(404);
        }

        // Mark the report as resolved.
        $report->resolved_time = now();
        $report->save();

        return redirect()->back()
            ->with('success', 'Report successfully resolved.')
            ->setStatusCode(204);
    }

    /**
     * Adds a new category.
     */
    public function addCategory(Request $request)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }

        // Check if the user is a system manager.
        if (!Auth::user()->system_managers) {
            return redirect()->back()
                ->with('error', 'Permission denied.')
                ->setStatusCode(403);
        }

        $request->validate([
            'name' => 'required|string|unique:categories|max:255',
        ]);

        // Create the category.
        $category = new Category();
        $category->name = $request->input('name');
        $category->save();

        return redirect()->back()
            ->with('success', 'Category successfully created.')
            ->setStatusCode(201);
    }

    /**
     * Updates an existing category.
     */
    public function updateCategory(Request $request, $category_id)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }

        // Check if the user is a system manager.
        if (!Auth::user()->system_managers) {
            return redirect()->back()
                ->with('error', 'Permission denied.')
                ->setStatusCode(403);
        }

        $category = Category::findOrFail($category_id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        // Update the category name.
        $category->name = $request->input('name');
        $category->save();

        return redirect()->back()
            ->with('success', 'Category successfully updated.')
            ->setStatusCode(204);
    }

    /**
     * Deletes an existing category.
     */
    public function deleteCategory($category_id)
    {
        // Check if the user is logged in.
        if (!Auth::check()) {
            // Not logged in, redirect to login.
            return redirect('/login');
        }

        // Check if the user is a system manager.
        if (!Auth::user()->system_managers) {
            return redirect()->back()
                ->with('error', 'Permission denied.')
                ->setStatusCode(403);
        }

        // Find the category.
        $category = Category::findOrFail($category_id);

        // Check if the category has associated posts or users before deletion
        if ($category->posts()->exists() || $category->users()->exists()) {
            return redirect()->back()
                ->withErrors(['error' => 'Category cannot be deleted because it is associated with posts or users.']);
        }

        // Delete the category.
        $category->delete();

        return redirect()->back()
            ->with('success', 'Category successfully deleted.')
            ->setStatusCode(204);
    }

}
