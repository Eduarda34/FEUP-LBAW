<article class="card" data-id="{{ $post->post_id }}">
    <header>
        <h2><a href="/posts/{{ $post->post_id }}">{{ $post->title }}</a></h2>
    </header>
    <div class="card">
        <p class="category">{{ $post->categories }}</p>
        <p>{{ $post->body }}</p>
    </div>
</article>