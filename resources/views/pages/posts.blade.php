@extends('layouts.app')

@section('title', 'Posts')

@section('content')

<section id="content_body">
    <section id="posts" class="left-panel">
        <header>
            @if ($feedType == 'category')
                <div id="category-title">
                    <h2>{{ $category->name }}</h2>
                    @if ($category->users()->where('user_id', Auth::id())->exists())
                        <span id="follow-category-btn" class="btn inverted" data-id="{{ $category->category_id }}">Unfollow</span>
                    @else
                        <span id="follow-category-btn" class="btn" data-id="{{ $category->category_id }}">Follow</span>
                    @endif
                </div>
            @elseif ($feedType == 'favorites') 
                <h2>FAVORITE NEWS</h2>
            @else
                <h2>TRENDING NEWS</h2>
            @endif

            <!-- Filter Bar -->
            <nav id="feed-filter">
                <ul>
                    @if (!in_array($feedType, ['popular', 'custom', 'recent']))
                        <li>
                            <span class="active">{{ ucfirst($feedType) }}</span>
                        </li>
                    @endif
                    <li>
                        <a href="{{ url('/posts?feed=popular') }}" 
                           class="{{ $feedType == 'popular' ? 'active' : '' }}">
                            Popular
                        </a>
                    </li>
                    @if (Auth::check())
                        <li>
                            <a href="{{ url('/posts?feed=custom') }}" 
                            class="{{ $feedType == 'custom' ? 'active' : '' }}">
                                Custom
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ url('/posts?feed=recent') }}" 
                           class="{{ $feedType == 'recent' ? 'active' : '' }}">
                            Recent
                        </a>
                    </li>
                </ul>
            </nav>
        </header>

        @if (Auth::check())
            <!-- Button to link to the create post page -->
            <div class="create-post-btn">
                <a href="{{ url('/posts/create') }}" class="button">Create Post</a>
            </div>
        @endif

        @each('partials.post', $posts, 'post')
    </section>

    <section id="categories" class="right-panel">
        <!-- Button to link to the users page -->
        <div class="meet-authors-btn">
            <a href="{{ route('users.list') }}" class="button">MEET NEW AUTHORS</a>
        </div>

        <h3>Categories</h3>
        @foreach ($categories as $category)
            <p><a href="{{ route('posts.category', $category->category_id)}}">{{$category->name}}</a></p>
        @endforeach
    </section>
</section>

@endsection