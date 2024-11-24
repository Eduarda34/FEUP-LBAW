@extends('layouts.app')

@section('title', 'Posts')

@section('content')

<section id="cards">
    @each('partials.post', $posts, 'post')
</section>

<section id="categories">
    <h2>Categories</h2>
    <!-- Category content here -->
</section>

@endsection