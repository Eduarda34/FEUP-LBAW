@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<section id="content_body">
    <section id="notifications" class="left-panel">
        <h2>Notifications</h2>
        @if (!($unviewedNotifications || $viewedNotifications))
            <p>There are no notifications</p>
        @endif
        <h3>Not Viewed</h3>
        <ul id="notifications-not-viewed">
            @each('partials.notification', $unviewedNotifications, 'notification')
        </ul>
        <h3>Viewed</h3>
        <ul id="notifications-viewed">
            @each('partials.notification', $viewedNotifications, 'notification')
        </ul>
    </section>
</section>

@endsection