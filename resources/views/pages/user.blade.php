@extends('layouts.app')

@section('title', $user->username . 'profile')

@section('content')

<section id="content_body">
    <section id="profile-container" class="left-panel" data-id="{{ $user->id }}">
        <header>
            <div id="user-name">
            <h2>{{ $user->username }}</h2>
            @if (Auth::check() && Auth::id() === $user->id)
                <a href="/users/{{ $user->id }}/edit" class="edit">[edit]</a>
            </div>
            @else
            </div>
                <div id="icons">
                    @if ($user->followers()->where('follower_id', Auth::id())->exists())
                        <span id="follow-btn" class="btn inverted" data-id="{{ $user->id }}">Unfollow</span>
                    @else
                        <span id="follow-btn" class="btn" data-id="{{ $user->id }}">Follow</span>
                    @endif
                    <span 
                        class="report-icon" 
                        data-id="{{ $user->id }}" 
                        title="Report User">
                        <a href="{{ route('users.report', $user->id) }}">🛈</a>
                    </span>
                </div>
            @endif
        </header>
        <div class="profile-info">
            <img src="{{ $user->getProfilePicture() }}" alt="Profile Picture" class="profile-pic">

            <div id="user-details">
                <!--User information-->
                <p>Followers: {{ $user->followers()->count() }}</p>
                <p>Following: {{ $user->following()->count() }}</p>
                <p>Reputation: {{$user->reputation }}</p>
                <div id="user-bio">
                    <h3>Who is {{ $user->username }}?</h3>
                    <p>{{ $user->bio }}</p>
                </div> 
            </div>
        </div>
        @if ($user->blocked && Auth::user()->system_managers)
            <div class="blocked-report">
                @include('partials.report', ['report' => $user->blocked->report])
            </div>
        @endif
    </section>
    <section id="user-posts" class="right-panel">
        @include('partials.userPosts', ['posts' => $posts])
    </section>
</section>

@endsection