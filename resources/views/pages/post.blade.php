@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <section id="posts">
        @include('partials.post', ['post' => $post])
    </section>
@endsection