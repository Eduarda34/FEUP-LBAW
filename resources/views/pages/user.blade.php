@extends('layouts.app')

@section('username', $user->username)

@section('content')
    <section id="cards">
        @include('partials.profile', ['user' => $user])
    </section>
@endsection