@extends('layouts.head')

@section('body')
<body>
    <main class="pink-border auth">
        <form class="auth" method="POST" action="{{ route('register.store', ['uuid' => $invitation->id]) }}">
            @csrf

            <x-logo.large />

            <!-- Email/username & password-->
            <p>Welcome, new user, to a refreshing and exciting new chapter in your life. Enter your secret token and choose a password to get started.</p>
            <div class="input-container">
                <input id="secret-token" class="text-input" type="email" name="email" placeholder="Enter your secret token" required autofocus />
            </div>
            <hr>
            <div class="input-container">
                <input id="password" class="text-input" type="password" name="password" placeholder="Choose your password" required />
                <input id="confirm-password" class="text-input" type="password" name="confirm-password" placeholder="Confirm your password" required />
            </div>

            <!-- Confirm -->
            <button class="action-button">Create account</button>

            <!-- Error list -->
            @if ($errors->any()) 
                <div class="error-list">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif 

        </form>
    </main>
</body>
@endsection