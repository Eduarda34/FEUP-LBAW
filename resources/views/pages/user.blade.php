@extends('layouts.app')

@section('title', $user->username . 'profile')

@section('content')
    <section id="profile-container">
        <h2>{{ $user->username }}</h2>

        <div class="profile-info">
            <p>{{ $user->username}}<a href="/users/{{ $user->id }}/edit">[edit]</a></p>
            <p>{{ $user->followers }}</p>
            <p>{{ $user->following }}</p>
        </div>
    </section>
@endsection