@extends('layouts.head')

@section('body')
<body>
    <main class="pink-border auth">
        <form class="auth" method="POST" action="{{ route('login.authenticate') }}">
            @csrf

            <x-logo.large />

            <!-- Email/username & password-->
            <div class="input-container">
                <input id="email" class="text-input" type="email" name="email" placeholder="Email" autofocus />
                <input id="password" class="text-input" type="password" name="password" placeholder="Password" />
            </div>

            <!-- Remember Me -->
            <div class="checkbox-container">
                <label for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember">
                    Remember me
                </label>
            </div>

            <!-- Confirm -->
            <button class="action-button">Sign in</button>

            <!-- Forgot password -->
            <div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <!-- Error list -->
            <ul class="error-list">
                @if ($errors->any()) 
                    @foreach ($errors->all() as $error)
                        <x-error-item message="{{ $error }}" />
                    @endforeach
                @endif
            </ul>
        </form>
    </main>
</body>
@endsection