@extends('layouts.app')

@section('title', 'Edit' . $user->username . 'profile')

@section('content')
    <section id="profile-editor-container">
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
            <div class="image-form">
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

            <!-- Bio Field -->
            <div>
                <label for="bio">Bio</label>
                <textarea 
                    id="bio" 
                    name="bio" 
                    rows="5"  
                    maxlength="1000" 
                    placeholder="Enter the bio to show in your profile (optional)"
                >{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <div class="error text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit">Save Changes</button>
            </div>          
        </form>

        <!-- Delete Account Button -->
        <form action="{{ url('/api/users/'.$user->id)}}" method="POST" class="delete-account-form">
            @csrf
            @method('DELETE')

            <button type="delete-account-button">Delete Account</button>

            <label for="password">Confirm your password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            @if ($errors->has('password'))
                <div class="alert">{{ $errors->first('password') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        </form>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

    </section>
@endsection