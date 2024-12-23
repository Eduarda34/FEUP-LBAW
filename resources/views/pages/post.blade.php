@extends('layouts.app')

@section('title', $post->title)

@section('content')

<section id="content_body">
    <section id="post" class="left-panel" data-id="{{ $post->post_id }}">
        <div class="news-image-container">
            <img src="{{ $post->getCoverImage() }}" alt="News Cover Image" class="news-image">
        </div>
        
        <!-- Title, Edit Link and Date in the same line -->
        <header class="post-header">
            <div>
                <h2>{{ $post->title }}</h2>
                @if (Auth::check() && Auth::id() === $post->user_id)
                    <a href="{{ route('posts.edit', $post->post_id) }}" class="edit">[edit]</a>
                @endif
            <div>
            @if (Auth::check() && !(Auth::id() === $post->user_id))
                <div class="post-actions">
                    <!-- Star Icon for Favorites -->
                    <span 
                        class="favorite-icon @if ($post->fans()->where('user_id', Auth::id())->exists()) filled @endif" 
                        data-id="{{ $post->post_id }}" 
                        title="Add to Favorites">
                        ★
                    </span>
                    
                    <!-- Report Icon for Bad Content -->
                    <span 
                        class="report-icon" 
                        data-id="{{ $post->post_id }}" 
                        title="Report Post">
                        <a href="{{ route('posts.report', $post->post_id) }}">🛈</a>
                    </span>
                </div>
            @endif
            <p class="post-time">
                @if ($post->updated_at)
                    Edited at {{ \Carbon\Carbon::parse($post->updated_at)->format('h:i A | F d') }}
                @else
                    {{ \Carbon\Carbon::parse($post->created_at)->format('h:i A | F d') }}
                @endif
            </p>
        </header>

        <p class="post-content">{{ $post->body }}</p>

        <!-- Author and Count Values in the same line -->
        <footer class="post-footer">
            <p class="author">
                <a href="{{ route('user.profile', $post->owner->id) }}" class="author-link">By <span>{{ $post->owner->username }}</span></a>
            </p>
            <div class="stats">
                <div class="votes">
                    <span 
                    title="Like Post"
                    class="vote-icon @if ($post->votes()->where('user_id', Auth::id())->where('is_like', true)->exists()) filled @endif" 
                    data-id="{{ $post->post_id }}"
                    data-is-like="1">🡅</span>
                    <span title="Number of Likes">{{ $post->votes()->where('is_like', true)->count() }}</span>
                </div>
                <div class="votes">
                    <span 
                    title="Dislike Post"
                    class="vote-icon @if ($post->votes()->where('user_id', Auth::id())->where('is_like', false)->exists()) filled @endif"
                    data-id="{{ $post->post_id }}"
                    data-is-like="0">🡇</span>
                    <span title="Number of Dislikes">{{ $post->votes()->where('is_like', false)->count() }}</span>
                </div>
                <div class="comments">
                    <span title="Comments" class="vote-icon">&#128172;</span> <span title="Number of Comments">{{$post->comments()->count()}}</span>
                </div>
            </div>
        </footer>

        @if (Auth::check() && Auth::id() === $post->user_id)
            <button class="delete-post-btn">Delete Post</button>

            <div class="delete-modal" style="display: none;">
                <h3 class="delete-modal-title">Delete Post</h3>
                <p class="delete-modal-message">Are you sure you want to delete this post? This action cannot be undone.</p>
                <div class="delete-modal-actions">
                    <button class="delete-modal-cancel">Cancel</button>
                    <button class="delete-post-btn">Delete</button>
                </div>
            </div>

            <div class="delete-error" style="display: none;">
                The post cannot be deleted because it has associated votes or comments.
            </div>
        @endif

        <section class="comment-section">
            <h3>Comments</h3>
            @foreach ($post->comments as $comment)
                @if ($comment->parent)
                    @continue
                @endif
                <div class="comment" data-id="{{ $comment->comment_id }}">
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

            @endforeach
        </section>

        <!-- Comment Form -->
        <div class="comment-form">
            <form action="{{ route('comments.create', $post->post_id) }}" method="POST">
                @csrf
                <label for="body" class="form-label">New Comment</label>
                <textarea name="body" placeholder="Write a comment..." required></textarea>
                <button type="submit">Post Comment</button>
            </form>
        </div>
    </section>
    <section id="suggested-news" class="right-panel">
        @include('partials.suggestedNews', ['suggested_news' => $suggested_news])
    </section>
</section>
@endsection