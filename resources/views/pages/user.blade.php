@extends('layouts.app')

@section('title', $user->username . 'profile')

@section('content')
    <section id="profile-container">
        <h2>{{ $user->username }}</h2>

        <div class="profile-info">
            <img src="{{ $user->getProfilePicture() }}" alt="Profile Picture" class="profile-pic">

            <!--User information-->
            @if (Auth::check() && Auth::id() === $user->id)
                <p>{{ $user->username}} <a href="/users/{{ $user->id }}/edit">[edit]</a></p>
            @endif
            <p>Followers: {{ $user->followers }}</p>
            <p>Following: {{ $user->following }}</p>
            <p>Reputation: {{$user->reputation }}</p>

            <h2>Who is Admin?</h2>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sed volutpat purus. 
                Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
                Nam feugiat, mauris sodales sagittis semper, eros velit aliquam lacus, non luctus ligula nisi in mi.
            </p>
            
        </div>
    </section>
@endsection