@extends('layouts.app')

@section('title', 'Posts')

@section('content')

<h2>TRENDING NEWS</h2>
<section id="content_body">
    <section id="cards">
        @each('partials.post', $posts, 'post')

        @if (Auth::check())
            <!-- Button to link to the create post page -->
            <div class="create-post-btn">
                <a href="{{ url('/posts/create') }}" class="button">Create Post</a>
            </div>
        @endif
    </section>

    <section id="categories">
        <h2>Categories</h2>
        <!-- Category content here -->
    </section>
</section>

@endsection