@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Comment</h1>

    <!-- Comment Edit Form -->
    <form action="{{ route('comments.update', $comment->comment_id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Body Field -->
        <div class="mb-3">
            <label for="body" class="form-label">Comment Body</label>
            <textarea name="body" id="body" class="form-control" rows="5" required>{{ old('body', $comment->body) }}</textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Update Comment</button>
    </form>
</div>
@endsection
