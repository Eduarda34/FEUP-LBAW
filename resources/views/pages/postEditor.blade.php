@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="container">
    <h1>Edit Post</h1>

    <form action="{{ route('posts.update', $post->post_id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- News Cover Image Field -->
        <div class="image-form">
            <!-- Image Preview -->
            <div class="mt-3">
                <div class="news-image-container">
                    <img id="preview-image" src="{{ asset('storage/' . $post->image) }}" alt="Preview" class="updated-image-preview">
                </div>
            </div>

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
        </div>

        <!-- Title Field -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                class="form-control" 
                value="{{ old('title', $post->title) }}" 
                required 
                maxlength="255" 
                placeholder="Enter the title of the post" 
            />
            @error('title')
                <div class="error text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Synopsis Field -->
        <div class="mb-3">
            <label for="synopsis" class="form-label">Synopsis</label>
            <textarea 
                id="synopsis" 
                name="synopsis" 
                class="form-control" 
                rows="5"  
                maxlength="300" 
                placeholder="Enter the synopsis of your post (optional)"
            >{{ old('synopsis', $post->synopsis) }}</textarea>
            @error('synopsis')
                <div class="error text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Body Field -->
        <div class="mb-3">
            <label for="body" class="form-label">Body</label>
            <textarea 
                id="body" 
                name="body" 
                class="form-control" 
                rows="10" 
                required 
                maxlength="10000" 
                placeholder="Enter the content of your post"
            >{{ old('body', $post->body) }}</textarea>
            @error('body')
                <div class="error text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Categories Field -->
        <div class="mb-3">
            <label for="categories" class="form-label">Categories</label>
            <select id="categories" name="categories[]" class="form-select" multiple required>
                @foreach ($categories as $category)
                    <option 
                        value="{{ $category->category_id }}"
                        @if (in_array(
                            $category->category_id, 
                            old('categories', $post->categories->pluck('category_id')->toArray())
                        )) 
                            selected 
                        @endif
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Hold Ctrl (Cmd on Mac) to select multiple categories.</small>
            @error('categories')
                <div class="error text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection
