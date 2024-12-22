<article class="trending-author" data-id="{{ $user->id }}">
    <img src="{{ $user->getProfilePicture() }}" alt="Profile Picture" class="profile-pic">
    <div id="user-content">
        <h2><a href="{{ route('user.profile', $user->id) }}">{{ $user->username }}</a></h2>
        <p>Reputation: {{$user->reputation }}</p>
    </div>
</article>