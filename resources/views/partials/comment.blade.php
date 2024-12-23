<div class="comment" id="comment-{{ $comment->comment_id }}" data-id="{{ $comment->comment_id }}">
    <!-- Comment Header -->
    <header>
        <div>
            <a href="{{ route('user.profile', $comment->owner->id) }}"><strong>{{ $comment->owner->username }}</strong></a>
            <!-- Edit Link if the Comment Belongs to the Authenticated User -->
            @if (Auth::check() && Auth::id() === $comment->user_id)
                <a href="{{ route('comments.edit', $comment->comment_id) }}" class="edit">[edit]</a>
            @endif
        </div>
        <span class="comment-time">{{ \Carbon\Carbon::parse($comment->created_at)->format('h:i A | F d') }}</span>
    </header>

    <!-- Comment Body -->
    <p>{{ $comment->body }}</p>

    <!-- Comment Footer with Stats -->
    <footer class="comment-footer">
        @if (Auth::check() && !(Auth::id() === $comment->user_id))
            <div class="comment-actions">                                
                <!-- Report Icon for Bad Content -->
                <span 
                    class="report-icon" 
                    data-id="{{ $comment->comment_id }}" 
                    title="Report Comment">
                    <a href="{{ route('comments.report', $comment->comment_id) }}">🛈</a>
                </span>
            </div>
        @endif
        <div class="votes">
            <span 
            title="Like Comment"
            class="vote-icon @if ($comment->votes()->where('user_id', Auth::id())->where('is_like', true)->exists()) filled @endif"
            data-id="{{ $comment->comment_id }}"
            data-is-like="1">🡅</span>
            <span title="Number of Likes">{{ $comment->votes()->where('is_like', true)->count() }}</span>
        </div>
        <div class="votes">
            <span 
            title="Dislike Comment"
            class="vote-icon @if ($comment->votes()->where('user_id', Auth::id())->where('is_like', false)->exists()) filled @endif"
            data-id="{{ $comment->comment_id }}"
            data-is-like="0">🡇</span>
            <span title="Number of Dislikes">{{ $comment->votes()->where('is_like', false)->count() }}</span>
        </div>
        <div class="comments">
            <span title="Replies" class="vote-icon">&#128172;</span> <span title="Number of Replies">{{$comment->replies()->count()}}</span>
        </div>
    </footer>

    @if (Auth::check() && Auth::id() === $comment->user_id)
        <button class="delete-comment-btn">Delete</button>
        
        <div class="delete-comment-modal" style="display: none;">
            <h3 class="delete-comment-modal-title">Delete Comment</h3>
            <p class="delete-comment-modal-message">Are you sure you want to delete this comment? This action cannot be undone.</p>
            <div class="delete-comment-modal-actions">
                <button class="delete-comment-modal-cancel">Cancel</button>
                <button class="delete-comment-btn">Delete</button>
            </div>
        </div>

        <div class="delete-comment-error" style="display: none;">
            The comment cannot be deleted because it has associated votes or replies.
        </div>
    @endif
</div> 
<!-- Replies -->
<div class="replies" data-id="{{ $comment->comment_id }}" style="display: none;">
    @if ($comment->replies)
        @foreach ($comment->replies as $reply)
            @include('partials.comment', ['comment' => $reply->reply])
        @endforeach
    @endif
    <!-- Comment Form -->
    <div class="comment-form">
        <form action="{{ route('comments.reply', $comment->comment_id) }}" method="POST">
            @csrf
            <label for="body" class="form-label">New Reply</label>
            <textarea name="body" placeholder="Write a comment..." required></textarea>
            <button type="submit">Reply to Comment</button>
        </form>
    </div>
</div>