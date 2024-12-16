@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<h2>Notifications</h2>
<section id="content_body">
    <section id="cards">
        @foreach ($notifications as $notification)
            <article class="card" data-id="{{ $notification->notification_id }}">
                @if ($notification->follow)
                    <p>
                        The user 
                        <a href="{{ route('user.profile', $notification->follow->follower_id) }}">
                            {{ $notification->follow->follower->username }}
                        </a> 
                        started to follow you.
                    </p>
                @elseif ($notification->vote)
                    <p>
                        Your 
                        <a href="{{ $notification->vote->comment_id ? route('comments.show', $notification->vote->comment_id) : route('posts.show', $notification->vote->post_id) }}">
                        {{ $notification->vote->comment_id ? 'comment' : 'post' }}
                        </a> 
                        received a vote.
                    </p>
                @elseif ($notification->comment)
                    @if ($notification->comment->parent_comment)
                        <p>
                            Your 
                            <a href="{{ route('comments.show', $notification->comment->parent_comment_id) }}">
                                comment
                            </a> 
                            received a reply.
                        </p>
                    @else
                        <p>
                            Your 
                            <a href="{{ route('posts.show', $notification->comment->post_id) }}">
                                post
                            </a> 
                            received a comment.
                        </p>
                    @endif
                @elseif ($notification->post)
                    <p>
                        The user 
                        <a href="{{ route('user.profile', $notification->post->author_id) }}">
                            {{ $notification->post->author->username }}
                        </a> created a new 
                        <a href="{{ route('post.show', $notification->post->post_id) }}">
                            post
                        </a>.
                    </p>
                @endif
            </article>
        @endforeach
    </section>
</section>

@endsection