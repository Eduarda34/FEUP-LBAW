<h3>PORTFOLIO</h3>
@foreach ($posts as $post)
    <div id="suggested-item">
        <div class="news-image-container">
            <img src="{{ $post->getCoverImage() }}" alt="News Cover Image" class="news-image-small">
        </div>
        <div class="news-information">
            <a href="{{ route('posts.show', $post->post_id) }}" class="suggested-title">{{ strtoupper($post->title) }}</a>
        </div>
    </div>
@endforeach