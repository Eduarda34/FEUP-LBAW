@extends('layouts.app')

@section('title', 'Reports')

@section('content')

<section id="content_body">
    <section id="reports" class="left-panel">
    <h2>Reports to resolve</h2>
        @if($unresolvedReports->isEmpty())
            <p>No unresolved reports.</p>
        @else
            @each('partials.report', $unresolvedReports, 'report')
        @endif

    <h2>Resolved reports</h2>
        @if($resolvedReports->isEmpty())
            <p>No resolved reports.</p>
        @else
            @each('partials.report', $resolvedReports, 'report')
        @endif
    </section>

    <section id="blocked-users" class="right-panel">
        <h2>Blocked</h2>
        @if ($blocked)
        <ul class= "blocked">
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