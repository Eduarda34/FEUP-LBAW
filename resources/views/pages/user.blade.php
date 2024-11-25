@extends('layouts.app')

@section('title', $user->username . 'profile')

@section('content')
    <section id="profile-container">
        <h2>{{ $user->username }}</h2>

        <div class="profile-info">
            @if (Auth::check() && Auth::id() === $user->id)
                <p>{{ $user->username}} <a href="/users/{{ $user->id }}/edit">[edit]</a></p>
            @endif
            <p>Followers: {{ $user->followers }}</p>
            <p>Following: {{ $user->following }}</p>
            <p>Reputation: {{$user->reputation }}</p>
        </div>
    </section>

    <!-- Search Bar Section -->
    <section id="search-bar">
        <form action="{{ route('search.users') }}" method="GET" >
            <input type="text" name="query" placeholder="Search users..." required>
            <button type="submit">Search</button>
        </form>
    </section>
@endsection