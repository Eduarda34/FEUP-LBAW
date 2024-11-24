<div class="suggested-news">
    <h3>SUGGESTED NEWS</h3>
    @foreach ($suggested_news as $news)
        <div class="suggested-item">
            <h4>
                <a href="{{ route('posts.show', $news->post_id) }}" class="suggested-title">{{ strtoupper($news->title) }}</a>
                <span class="suggested-author">By {{ $news->owner->username }}</span>
            </h4>
        </div>
    @endforeach
</div>