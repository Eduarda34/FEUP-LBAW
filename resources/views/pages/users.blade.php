@extends('layouts.app')

@section('title', 'Users')

@section('content')

<section id="content_body">
    
    <section id="trending-authors">
        <!-- Search Bar Section -->
        <section id="search-bar">
            <form action="{{ route('search.users') }}" method="GET" >
                <input type="text" name="query" placeholder="Search users..." required>
                <button type="submit">Search</button>
            </form>
        </section>
        <h2>TRENDING AUTHORS</h2>

        @each('partials.user', $users, 'user')

    </section>

    <section id="suggested-news" class="right-panel">
        @include('partials.suggestedNews', ['suggested_news' => $suggested_news])
    </section>
</section>

@endsection