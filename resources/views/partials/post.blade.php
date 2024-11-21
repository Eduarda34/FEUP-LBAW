<article class="card" data-id="{{ $post->post_id }}">
    <header>
        <h2><a href="/posts/{{ $post->post_id }}">{{ $post->title }}</a></h2>
        <a href="#" class="delete">&#10761;</a>
    </header>
    <ul>
        @each('partials.comment', $post->comments()->orderBy('comment_id')->get(), 'comment')
    </ul>
    <form class="new_item">
        <input type="text" name="description" placeholder="new item">
    </form>
</article>