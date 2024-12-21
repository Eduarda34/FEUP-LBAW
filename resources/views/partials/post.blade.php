<article class="post" data-id="{{ $post->post_id }}">
    @if ($post->image)
        <div class="news-image-container">
            <img src="{{ asset('storage/' . $post->image) }}" alt="News Cover Image" class="news-image">
        </div>
    @endif
    <section id="categories">
        @foreach ($post->categories as $category)
            <a href="{{ route('posts.category', $category->category_id)}}">{{$category->name}}</a>
        @endforeach
    </section>
    <h2><a href="/posts/{{ $post->post_id }}">{{ $post->title }}</a></h2>
    <p>{{ $post->synopsis }}</p>
    <!-- Author and Count Values in the same line -->
    <footer>
        <p class="author">
            <a href="{{ route('user.profile', $post->owner->id) }}" class="author-link">By <span>{{ $post->owner->username }}</span></a>
        </p>
        <div class="stats">
            <div class="votes">
                <span class="vote-icon">🡅</span>
                <span>{{ $post->votes()->count() }}</span>
            </div>
            <div class="comments">
                <span class="vote-icon">&#128172;</span>
                <span>{{ $post->comments()->count() }}</span>
            </div>
        </div>
    </footer>
</article>