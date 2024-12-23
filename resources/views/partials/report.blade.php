<article class="card" data-id="{{ $report->report_id }}">
    <div class="card">
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
                <a href="{{ route('posts.show', $report->comment->comment->post_id) }}">
                    [View Comment]
                </a>
            </p>
        @endif
        @if (isset($report->resolved_time))
            <p>Resolved at: {{ \Carbon\Carbon::parse($report->resolved_time)->format('d/m/Y H:i') }}</p>
        @endif

        <!-- Delete content when its not a parent to any value -->
        @if (!isset($report->comment) && !isset($report->post->comments) && !isset($report->post->likes))
            <form action="{{ route('sys.postDelete', $report->report_id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Content</button>
            </form>
        @endif

        <!-- Block user -->
        @if (isset($report->user))
            <form action="{{ route('system.users.block', $report->user->reported->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('POST')
                <button type="submit" class="btn btn-warning">Block User</button>
            </form>
        @endif
    </div>
</article>