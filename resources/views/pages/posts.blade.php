@extends('layouts.app')

@section('title', 'Posts')

@section('content')

<section id="cards">
    @each('partials.post', $posts, 'post')
</section>

@endsection