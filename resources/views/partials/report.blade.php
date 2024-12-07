<article class="card" data-id="{{ $report->report_id }}">
    <div class="card">
        <p>Reason: {{ $report->reason }}</p>
        <p>Reporter: 
            <a href="{{ route('user.profile', $report->reporter->id) }}">
                {{ $report->reporter->username }}
            </a>
        </p>
        <p>Reported at: {{ \Carbon\Carbon::parse($report->time)->format('d/m/Y H:i') }}</p>
        @if (isset($report->reported))
            <p>Reported User: 
                <a href="{{ route('user.profile', $report->reported->id) }}">
                    {{ $report->reported->username }}
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
                <a href="{{ route('posts.show', $report->comment->post_id) }}">
                    [View Comment]
                </a>
            </p>
        @endif
        @if (isset($report->resolved_time))
            <p>Resolved at: {{ \Carbon\Carbon::parse($report->resolved_time)->format('d/m/Y H:i') }}</p>
        @endif
    </div>
</article>