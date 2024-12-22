@extends('layouts.app')

@section('title', $post->title)

@section('content')

<section id="content_body">
    <section id="post" class="left-panel" data-id="{{ $post->post_id }}">
        @if ($post->image)
            <div class="news-image-container">
                <img src="{{ asset('storage/' . $post->image) }}" alt="News Cover Image" class="news-image">
            </div>
        @endif
        <!-- Title, Edit Link and Date in the same line -->
        <header class="post-header">
            <div>
                <h2>{{ $post->title }}</h2>
                @if (Auth::check() && Auth::id() === $post->user_id)
                    <a href="{{ route('posts.edit', $post->post_id) }}" class="edit">[edit]</a>
                @endif
            <div>
            <p class="post-time">{{ \Carbon\Carbon::parse($post->created_at)->format('h:i A | F d') }}</p>
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
                    class="vote-icon @if ($post->votes()->where('user_id', Auth::id())->where('is_like', true)->exists()) filled @endif" 
                    data-id="{{ $post->post_id }}"
                    data-is-like="1">🡅</span>
                    <span>{{ $post->votes()->where('is_like', true)->count() }}</span>
                </div>
                <div class="votes">
                    <span 
                    class="vote-icon @if ($post->votes()->where('user_id', Auth::id())->where('is_like', false)->exists()) filled @endif"
                    data-id="{{ $post->post_id }}"
                    data-is-like="0">🡇</span>
                    <span>{{ $post->votes()->where('is_like', false)->count() }}</span>
                </div>
                <div class="comments">
                    <span class="vote-icon">&#128172;</span> <span>{{$post->comments()->count()}}</span>
                </div>
            </div>
        </footer>
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
                        <div>
                        <span class="comment-time">{{ \Carbon\Carbon::parse($comment->created_at)->format('h:i A | F d') }}</span>
                    </header>

                    <!-- Comment Body -->
                    <p>{{ $comment->body }}</p>

                    <!-- Comment Footer with Stats -->
                    <footer class="comment-footer">
                        <div class="votes">
                            <span 
                            class="vote-icon @if ($comment->votes()->where('user_id', Auth::id())->where('is_like', true)->exists()) filled @endif"
                            data-id="{{ $comment->comment_id }}"
                            data-is-like="1">🡅</span>
                            <span>{{ $comment->votes()->where('is_like', true)->count() }}</span>
                        </div>
                        <div class="votes">
                            <span 
                            class="vote-icon @if ($comment->votes()->where('user_id', Auth::id())->where('is_like', false)->exists()) filled @endif"
                            data-id="{{ $comment->comment_id }}"
                            data-is-like="0">🡇</span>
                            <span>{{ $comment->votes()->where('is_like', false)->count() }}</span>
                        </div>
                        <div class="comments">
                            <span class="vote-icon">&#128172;</span> <span>{{$comment->replies()->count()}}</span>
                        </div>
                    </footer>
                </div>
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