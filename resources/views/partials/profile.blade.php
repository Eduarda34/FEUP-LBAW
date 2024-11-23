<article class="card" data-id="{{ $user->id }}">
    <header>
        <h2><a href="/users/{{ $user->id }}">Username: {{ $user->username }}</a></h2>
        <a href="#" class="delete">&#10761;</a>
    </header>
    <ul>
        @each('partials.post', $user->posts()->orderBy('post_id')->get(), 'post')
    </ul>
</article>