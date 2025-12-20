@extends('layouts.auth_layout')

@section('content')
    <p class="login-box-msg">{{ __('Verify Your Email Address') }}</p>
   
    @if (session('resent'))
        <div class="row">
            <div class="col">
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            </div>
        </div>
    @endif
    <p class="login-box-msg text-danger">{{ __('Before proceeding, please check your email for a verification link.') }}</p>
    <hr />
    <form class="text-center" method="POST" action="{{ route('verification.resend') }}">
        @csrf
       
        @if (session('resent'))
        <p class="login-box-msg"> {{ __('If you did not receive the email') }},</p>
        <button type="submit" class="btn btn-link link-primary">{{ __('Click here to request another') }}</button>.
       @else 
       
       <button type="submit" class="btn btn-link link-primary">{{ __('Click here to request an email') }}</button>.
        @endif
    </form>
@endsection
