@extends('layouts.app')

@section('title', 'Posts')

@section('content')

<section id="cards">
    @each('partials.post', $posts, 'post')
    <article class="cards">
        <form class="new_card">
            <input type="text" name="title" placeholder="title">
            <input type="text" name="body" placeholder="body">
        </form>
    </article>
</section>

@endsection