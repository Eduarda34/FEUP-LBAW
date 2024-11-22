@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Top News</h1>

    @if ($newsItems->isEmpty())
        <p>No news available.</p>
    @else
        <ul class="news-list">
            @foreach ($newsItems as $news)
                <li class="news-item">
                    <h2><a href="{{ route('news.show', $news->id) }}">{{ $news->title }}</a></h2>
                    <p>By {{ $news->user->name }} | Votes: {{ $news->votes }}</p>
                </li>
            @endforeach
        </ul>

        <!-- Pagination Links -->
        <div class="pagination">
            {{ $newsItems->links() }}
        </div>
    @endif
</div>
@endsection
