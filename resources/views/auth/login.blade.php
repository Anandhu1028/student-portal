@extends('layouts.auth_layout')


@section('content')
    <div class="text-center mt-1">
        <h4 class="font-size-18">Welcome Back !</h4>
        <p class="text-muted">Sign in to continue .</p>
    </div>
    <div class="row">
        <div class="col">
            @foreach (['email', 'password'] as $field)
                @foreach ($errors->get($field) as $message)
                    <div class="alert alert-sm alert-info">
                        {{ $message }}
                    </div>
                @endforeach
            @endforeach

            @error('email')
                <div class="fv-plugins-message-container invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <form action="{{ route('login') }}" method="post">

        @csrf

        <div class="mb-2">
            <label for="username" class="form-label">Email</label>
            <input type="text" class="form-control" name="email" placeholder="Enter email">
        </div>
        <div class="mb-3">
            <label class="form-label" for="password-input">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter password">
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
            <label class="form-check-label" for="auth-remember-check">Remember me</label>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary w-100" type="submit">Sign
                In</button>
        </div>

    </form>
    <p class="pt-2">
        <a href="{{ route('password.request') }}">I forgot my password</a>
    </p>
@endsection
