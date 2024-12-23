@if ($notification->follow)
    <a href="{{ route('user.profile', $notification->follow->follower_id) }}">
        <li class="notification" data-id="{{ $notification->notification_id }}">
            <p>The user {{ $notification->follow->follower->username }} started to follow you.</p>
            <!-- Notification buttons -->
            <div class="notification-buttons">
                <!-- Checkbox to mark as viewed -->
                <label>
                    <input title="Mark as viewed" type="checkbox" class="mark-as-viewed" data-id="{{ $notification->notification_id }}" {{ $notification->viewed ? 'checked' : '' }}>
                </label>

                <!-- Delete button -->
                <span class="delete-notification" data-id="{{ $notification->notification_id }}">&times;</span>
            </div>
        </li>
    </a> 
@elseif ($notification->vote)
    <a href="{{ $notification->vote->comment_id ? route('posts.show', $notification->vote->post_id) . '#comment-' . $notification->vote->comment_id : route('posts.show', $notification->vote->post_id) }}">
        <li class="notification" data-id="{{ $notification->notification_id }}">
            <p>Your {{ $notification->vote->comment_id ? 'comment' : 'post' }} received a vote.</p>
            <!-- Notification buttons -->
            <div class="notification-buttons">
                <!-- Checkbox to mark as viewed -->
                <label>
                    <input title="Mark as viewed" type="checkbox" class="mark-as-viewed" data-id="{{ $notification->notification_id }}" {{ $notification->viewed ? 'checked' : '' }}>
                </label>

                <!-- Delete button -->
                <span class="delete-notification" data-id="{{ $notification->notification_id }}">&times;</span>
            </div>
        </li>
    </a>
@elseif ($notification->comment)
    @if ($notification->comment->parent_comment)
        <a href="{{ route('comments.show', $notification->comment->parent_comment_id) }}">
            <li class="notification" data-id="{{ $notification->notification_id }}">
                <p>Your comment received a reply.</p>
                <!-- Notification buttons -->
                <div class="notification-buttons">
                    <!-- Checkbox to mark as viewed -->
                    <label>
                        <input title="Mark as viewed" type="checkbox" class="mark-as-viewed" data-id="{{ $notification->notification_id }}" {{ $notification->viewed ? 'checked' : '' }}>
                    </label>

                    <!-- Delete button -->
                    <span class="delete-notification" data-id="{{ $notification->notification_id }}">&times;</span>
                </div>
            </li>
        </a> 
    @else
        <a href="{{ route('posts.show', $notification->comment->post_id) }}#comment-{{ $notification->comment->comment_id }}">
            <li class="notification" data-id="{{ $notification->notification_id }}">
                <p>Your post received a comment.</p>
                <!-- Notification buttons -->
                <div class="notification-buttons">
                    <!-- Checkbox to mark as viewed -->
                    <label>
                        <input title="Mark as viewed" type="checkbox" class="mark-as-viewed" data-id="{{ $notification->notification_id }}" {{ $notification->viewed ? 'checked' : '' }}>
                    </label>

                    <!-- Delete button -->
                    <span class="delete-notification" data-id="{{ $notification->notification_id }}">&times;</span>
                </div>
            </li>
        </a> 
    @endif
@elseif ($notification->post)
    <a href="{{ route('user.profile', $notification->post->author_id) }}">
        <li class="notification" data-id="{{ $notification->notification_id }}">
            <p>
                The user 
                <a href="{{ route('user.profile', $notification->post->author_id) }}">
                    {{ $notification->post->author->username }}
                </a> created a new 
                <a href="{{ route('post.show', $notification->post->post_id) }}">
                    post
                </a>.
            </p>
            <!-- Notification buttons -->
            <div class="notification-buttons">
                <!-- Checkbox to mark as viewed -->
                <label>
                    <input title="Mark as viewed" type="checkbox" class="mark-as-viewed" data-id="{{ $notification->notification_id }}" {{ $notification->viewed ? 'checked' : '' }}>
                </label>

                <!-- Delete button -->
                <span class="delete-notification" data-id="{{ $notification->notification_id }}">&times;</span>
            </div>
        </li>
    </a>
@endif