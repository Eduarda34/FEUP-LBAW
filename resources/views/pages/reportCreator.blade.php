@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Report Post</h1>
    <p>You are reporting the post: <strong>{{ $post->title }}</strong></p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ url('/api/posts/' . $post->post_id . '/report') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="reason" class="form-label">Reason for Reporting</label>
            <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Describe the issue..." required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Submit Report</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection