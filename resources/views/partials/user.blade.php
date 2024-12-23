<article class="trending-author" data-id="{{ $user->id }}">
    <img src="{{ $user->getProfilePicture() }}" alt="Profile Picture" class="profile-pic">
    <div id="user-content">
        <div class="user-header">
            <h2><a href="{{ route('user.profile', $user->id) }}">{{ $user->username }}</a></h2>
            @if (Auth::check() && !(Auth::id() === $user->id))
                @if ($user->followers()->where('follower_id', Auth::id())->exists())
                    <span id="follow-btn" class="btn inverted" data-id="{{ $user->id }}">Unfollow</span>
                @else
                    <span id="follow-btn" class="btn" data-id="{{ $user->id }}">Follow</span>
                @endif
            @endif
        </div>
        <p>Reputation: {{$user->reputation }}</p>
    </div>
</article>