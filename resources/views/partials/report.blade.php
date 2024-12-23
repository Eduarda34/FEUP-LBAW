<article class="report" data-id="{{ $report->report_id }}">
        <p>Reason: {{ $report->reason }}</p>
        <p>Reporter: 
            <a href="{{ route('user.profile', $report->reporter->id) }}">
                {{ $report->reporter->username }}
            </a>
        </p>
        <p>Reported at: {{ \Carbon\Carbon::parse($report->time)->format('d/m/Y H:i') }}</p>
        @if (isset($report->user))
            <p>Reported User: 
                <a href="{{ route('user.profile', $report->user->reported->id) }}">
                    {{ $report->user->reported->username }}
                </a>
            </p>
        @endif
        @if (isset($report->post))
            <p>Post: 
                <a href="{{ route('posts.show', $report->post->post_id) }}">
                    [View Post]
                </a>
            </p>
        @endif
        @if (isset($report->comment))
            <p>Comment: 
                <a href="{{ route('posts.show', $report->comment->comment->post->post_id) }}">
                    [View Comment]
                </a>
            </p>
        @endif
        @if (isset($report->resolved_time))
            <p>Resolved at: {{ \Carbon\Carbon::parse($report->resolved_time)->format('d/m/Y H:i') }}</p>
        @endif
        
        <div class="report-buttons">
            <!-- Delete content when its not a parent to any value -->
            @if (!$report->resolved_time)
                @if ($report->post)
                    @if (!$report->post->post->comments && !$report->post->post->votes)
                        <form action="{{ route('sys.postDelete', $report->post_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete Content</button>
                        </form>
                    @endif
                @endif
                @if ($report->comment)
                    @if (!$report->comment->comment->replies && !$report->comment->comment->votes)
                        <form action="{{ route('sys.commentDelete', $report->comment_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete Content</button>
                        </form>
                    @endif
                @endif
            @endif

            <!-- Block user -->
            @if ($report->user)
                @if ($report->user->reported->blocked)
                    <form action="{{ route('system.users.unblock', $report->user->reported->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning inverted">Unblock User</button>
                    </form>
                @else
                    <form action="{{ route('system.users.block', $report->user->reported->id) }}" method="POST">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="reason" value="{{ $report->reason }}">
                        <input type="hidden" name="report_id" value="{{ $report->report_id }}">
                        <button type="submit" class="btn btn-warning">Block User</button>
                    </form>
                @endif
            @endif
            @if ($report->post)
                @if ($report->post->post->owner->block)
                    <form action="{{ route('system.users.unblock', $report->post->post->owner->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning inverted">Unblock User</button>
                    </form>
                @else
                    <form action="{{ route('system.users.block', $report->post->post->owner->id) }}" method="POST">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="reason" value="{{ $report->reason }}">
                        <input type="hidden" name="report_id" value="{{ $report->report_id }}">
                        <button type="submit" class="btn btn-warning">Block User</button>
                    </form>
                @endif
            @endif
            @if ($report->comment)
                @if ($report->comment->comment->owner->block)
                    <form action="{{ route('system.users.unblock', $report->comment->comment->owner->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning inverted">Unblock User</button>
                    </form>
                @else
                    <form action="{{ route('system.users.block', $report->comment->comment->owner->id) }}" method="POST">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="reason" value="{{ $report->reason }}">
                        <input type="hidden" name="report_id" value="{{ $report->report_id }}">
                        <button type="submit" class="btn btn-warning">Block User</button>
                    </form>
                @endif
            @endif
            @if (!$report->resolved_time)
                <form action="{{ route('system.reports.resolve', $report->report_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger">Resolve Report</button>
                </form>
            @endif
        </div>
</article>