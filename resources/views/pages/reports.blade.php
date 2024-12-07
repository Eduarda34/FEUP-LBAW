@extends('layouts.app')

@section('title', 'Reports')

@section('content')

<h2>Reports</h2>
<section id="content_body">
    <section id="cards">
        @each('partials.report', $reports, 'report')
    </section>

    <section id="categories">
        <h2>Blocked</h2>
        @if ($blocked)
        <ul>
            @foreach ($blocked as $user)
                <li>
                    <a href="{{ route('user.profile', $user->user->id) }}">{{ $user->user->username }}</a>
                    <small>({{ $user->user->email }})</small>
                </li>
            @endforeach
        </ul>
    @else
        <p>No blocked users at the moment.</p>
    @endif
    </section>
</section>

@endsection