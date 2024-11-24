@extends('layouts.app')

@section('title', 'Edit' . $user->username . 'profile')

@section('content')
    <section id="profile-container">
        <h2>Edit Profile: {{ $user->username }}</h2>

        <form action="{{ url('/api/users/' . $user->id . '/edit') }}" method="POST">
            @csrf
            @method('PUT')

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