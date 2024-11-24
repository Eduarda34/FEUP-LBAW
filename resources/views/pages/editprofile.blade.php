@extends('layouts.app')

@section('title', 'edit' . $user->username . 'profile')

@section('content')
    <section id="profile-container">
        <h2>{{ $user->username }}</h2>

        <div class="profile-info">
            <p>{{ $user->username}}</p>
            <p>{{ $user->followers }}</p>
            <p>{{ $user->following }}</p>
        </div>
    </section>
@endsection