@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create a New Post</h1>
    
    <!-- Post Creation Form -->
    <form action="{{ url('/api/posts') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- News Cover Image Field -->
        <div class="image-form">
            <label for="image">News Cover Image:</label>
            <input 
                type="file" 
                id="news-image" 
                name="image" 
                class="form-control"
                accept="image/*"
            >
            @error('image')
                <div class="error text-danger">{{ $message }}</div>
            @enderror

            <!-- Image Preview -->
            <div class="mt-3">
                <div class="news-image-container">
                    <img id="preview-image" src="#" alt="Preview" class="image-preview">
                </div>
            </div>
        </div>

        <!-- Title Field -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <!-- Body Field -->
        <div class="mb-3">
            <label for="synopsis" class="form-label">Synopsis</label>
            <textarea name="synopsis" id="synopsis" class="form-control" rows="5">{{ old('synopsis') }}</textarea>
        </div>

        <!-- Body Field -->
        <div class="mb-3">
            <label for="body" class="form-label">Body</label>
            <textarea name="body" id="body" class="form-control" rows="10" required>{{ old('body') }}</textarea>
        </div>

        <!-- Categories Field -->
        <div class="mb-3">
            <label for="categories" class="form-label">Categories</label>
            <select name="categories[]" id="categories" class="form-select" multiple required>
                @foreach ($categories as $category)
                    <option value="{{ $category->category_id }}">{{ $category->name }}</option> <!-- Ensure this matches the column in your DB -->
                @endforeach
            </select>
            <small class="form-text text-muted">Hold Ctrl (Cmd on Mac) to select multiple categories.</small>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Post</button>
    </form>
</div>
@endsection

