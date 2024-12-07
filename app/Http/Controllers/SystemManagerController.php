<?php

namespace App\Http\Controllers;

use App\Models\SystemManager;
use App\Models\UserReport;
use App\Models\PostReport;
use App\Models\CommentReport;
use App\Models\BlockedUser;
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
        if (!Auth::user()->system_managers()) {
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemManager $systemManager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemManager $systemManager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemManager $systemManager)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemManager $systemManager)
    {
        //
    }
}
