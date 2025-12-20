@extends('layouts.auth_layout')

@section('content')
   <div class="text-center mt-1">
        <h4 class="font-size-18">Welcome Back !</h4>
        <p class="text-muted">Sign in to continue .</p>
    </div>
    <div class="row">
        <div class="col">
            @foreach (['email', 'password', 'password_confirmation'] as $field)
                @foreach ($errors->get($field) as $message)
                    <div class="alert alert-sm alert-info">
                        {{ $message }}
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="input-group mb-3">

            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                value="{{ $email ?? old('email') }}" required autocomplete="email" placeholder="Enter the email" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password" required autocomplete="new-password" placeholder="Password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <input id="password-confirm" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password_confirmation" required autocomplete="new-password" placeholder="Confirmation password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('Reset Password') }}
                </button>
            </div>

        </div>
    </form>
@endsection
