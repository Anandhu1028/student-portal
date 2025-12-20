@extends('layouts.auth_layout')

@section('content')
    <div class="row">
        <div class="col">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @foreach (['email', 'name', 'password', 'emp_code'] as $field)
                @foreach ($errors->get($field) as $message)
                    <div class="alert alert-sm alert-info">
                        {{ ucwords($message) }}
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
    <form class="form w-100" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        <div class="input-group mb-3">
            <input type="text" class="form-control @error('name') error-outline @enderror" name="name"
                value="{{ old('name') }}" placeholder="Enter your name" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>

        <div class="input-group mb-3">
            <input type="email" class="form-control @error('email') error-outline @enderror" name="email"
                value="{{ old('email') }}" placeholder="Enter your email" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>

        <div class="input-group mb-3">
            <input type="password" class="form-control @error('password') error-outline @enderror" name="password"
                placeholder="Password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <input type="password" class="form-control @error('password_confirmation') error-outline @enderror"
                name="password_confirmation" placeholder="Password Confirmation">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <div class="input-group mb-3">
            <input type="text" class="form-control @error('emp_coode') error-outline @enderror" name="emp_code"
                placeholder="Employee code">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <select name="role" id="role" class="form-control form-control-sm" required>
                @php
                    $user_categories = App\Models\Roles::all();
                @endphp
                <option value="">Select Role</option>
                @foreach ($user_categories as $user_category)
                    <option value="{{ $user_category->id }}">{{ $user_category->role_name }}</option>
                @endforeach

            </select>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fa fa-id-badge"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <input id="profile_picture" type="file"
                class="form-control @error('profile_picture') error-outline @enderror" name="profile_picture">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fa fa-picture-o"></span>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>

        </div>
    </form>
@endsection
