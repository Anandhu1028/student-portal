@extends('layouts.auth_layout')

@section('content')
  <div class="text-center mt-1">
        <h4 class="font-size-18">Hold On !</h4>
        <p class="text-muted">Reset Your Password .</p>
    </div>
    <div class="row">
        <div class="col">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col">
            @error('email')
                <div class="alert alert-sm alert-info">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="input-group mb-3">
            <input id="email" type="email" class="form-control @error('email') error-outline @enderror" name="email"
                value="{{ $email ?? old('email') }}" placeholder="Enter the email" required autocomplete="email" autofocus>

        </div>

        <div class="row">
            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary btn-block">Send Password Reset Link</button>
            </div>

        </div>
        <div class="row mt-2">
            <div class="col">
                <p>Already having an account? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>

    </form>
@endsection
