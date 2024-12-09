@extends('layouts.app')

@section('title', 'Edit' . $user->username . 'profile')

@section('content')
    <section id="profile-container">
        <h2>Edit Profile: {{ $user->username }}</h2>

        <form action="{{ url('/api/users/' . $user->id . '/edit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile-picture-container">
                <img 
                    id="profile-pic-preview" 
                    src="{{ $user->getProfilePicture() }}" 
                    alt="Current Profile Picture" 
                    class="profile-pic"
                >
            </div>

            <!-- Profile Picture Field -->
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input 
                    type="file" 
                    id="profile_picture" 
                    name="profile_picture" 
                    class="form-control"
                    accept="image/*"
                >
                @error('profile_picture')
                    <div class="error text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Username Field -->
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                @error('username')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit">Save Changes</button>
            </div>
        </form>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

    </section>
@endsection