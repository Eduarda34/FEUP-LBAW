@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <section id="post" class="post-details">
        <div class="post-main">
            <div class="post-left">
                @if ($post->image)
                    <div class="news-image-container">
                        <img src="{{ asset('storage/' . $post->image) }}" alt="News Cover Image" class="news-image">
                    </div>
                @endif
                <!-- Title, Edit Link and Date in the same line -->
                <div class="post-header">
                    <h2>{{ strtoupper($post->title) }}</h2>
                    @if (Auth::check() && Auth::id() === $post->user_id)
                        <a href="{{ route('posts.edit', $post->post_id) }}" class="edit">[edit]</a>
                    @endif
                    <p class="post-time">{{ \Carbon\Carbon::parse($post->created_at)->format('h:i A | F d') }}</p>
                </div>

                <p class="post-content">{{ $post->body }}</p>

                <!-- Author and Count Values in the same line -->
                <div class="post-footer">
                    <p class="author">
                        <a href="{{ route('user.profile', $post->owner->id) }}" class="author">
                            By <b>{{ $post->owner->username }}</b>
                        </a>
                    </p>
                    <div class="stats">
                        <div class="votes">
                            <form action="{{ route('posts.vote', $post->post_id) }}" method="POST">
                                @csrf
                                <button type="submit" name="is_like" value="1" class="vote-btn">
                                    <span class="vote-icon @if ($post->votes()->where('user_id', Auth::id())->where('is_like', true)->exists()) filled @endif">&#8593;</span>
                                </button>
                                <span>{{ $post->votes()->where('is_like', true)->count() }}</span>
                            </form>
                        </div>
                        <div class="votes">
                            <form action="{{ route('posts.vote', $post->post_id) }}" method="POST">
                                @csrf
                                <button type="submit" name="is_like" value="0" class="vote-btn">
                                    <span class="vote-icon @if ($post->votes()->where('user_id', Auth::id())->where('is_like', false)->exists()) filled @endif">&#8595;</span>
                                </button>
                                <span>{{ $post->votes()->where('is_like', false)->count() }}</span>
                            </form>
                        </div>
                        <div class="votes">
                            <span class="vote-icon">&#128172;</span> <span>{{$post->comments()->count()}}</span>
                        </div>
                    </div>
                </div>
                <div class="comments-section">
                    <h3>Comments</h3>
                    @foreach ($post->comments as $comment)
                        @if ($comment->parent)
                            @continue
                        @endif
                        <div class="comment">
                            <!-- Comment Header -->
                            <p>
                                <strong>{{ $comment->owner->username }}</strong>
                                <span class="comment-time">{{ \Carbon\Carbon::parse($comment->created_at)->format('h:i A | F d') }}</span>
                                <!-- Edit Link if the Comment Belongs to the Authenticated User -->
                                @if (Auth::check() && Auth::id() === $comment->user_id)
                                    <a href="{{ route('comments.edit', $comment->comment_id) }}" class="edit-link">[Edit]</a>
                                @endif
                            </p>

                            <!-- Comment Body -->
                            <p>{{ $comment->body }}</p>

                            <!-- Comment Footer with Stats -->
                            <div class="comment-footer">
                                <div class="comment-stats">
                                    <span class="comment-votes">
                                        <span class="vote-icon">&#8593;</span> {{ $comment->votes()->where('is_like', true)->count() }}
                                    </span>
                                    <span class="comment-votes">
                                        <span class="vote-icon">&#8595;</span> {{ $comment->votes()->where('is_like', false)->count() }}
                                    </span>
                                    <span class="comment-votes">
                                        <span class="vote-icon">&#128172;</span> <span>{{$comment->replies()->count()}}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Comment Form -->
                <div class="comment-form">
                    <form action="{{ route('comments.create', $post->post_id) }}" method="POST">
                        @csrf
                        <textarea name="body" placeholder="Write a comment..." required></textarea>
                        <button type="submit">Post Comment</button>
                    </form>
                </div>
            </div>
            <div class="post-right">
                @include('partials.suggestedNews', ['suggested_news' => $suggested_news])
            </div>
        </div>
    </section>
@endsection