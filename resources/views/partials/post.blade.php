<article class="card" data-id="{{ $post->post_id }}">
    @if ($post->image)
        <div class="news-image-container">
            <img src="{{ asset('storage/' . $post->image) }}" alt="News Cover Image" class="news-image">
        </div>
    @endif
    @foreach ($post->categories as $category)
        <p class="category">{{ $category->name }}</p>
    @endforeach
    <h2><a href="/posts/{{ $post->post_id }}">{{ $post->title }}</a></h2>
    <p>{{ $post->synopsis }}</p>
</article>