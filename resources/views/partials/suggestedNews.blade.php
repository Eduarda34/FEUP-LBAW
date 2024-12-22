<h3>SUGGESTED NEWS</h3>
@foreach ($suggested_news as $news)
    <div id="suggested-item">
        @if ($news->image)
            <div class="news-image-container">
                <img src="{{ asset('storage/' . $news->image) }}" alt="News Cover Image" class="news-image-small">
            </div>
        @endif
        <div class="news-information">
            <a href="{{ route('posts.show', $news->post_id) }}" class="suggested-title">{{ strtoupper($news->title) }}</a>
            <p class="author">
                <a href="{{ route('user.profile', $news->owner->id) }}" class="author-link">By <span>{{ $news->owner->username }}</span></a>
            </p>
        </div>
    </div>
@endforeach