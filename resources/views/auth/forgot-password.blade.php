@extends('layouts.app')

@section('content')
<div class="container py-5" style="min-height: 100vh; background: radial-gradient(circle at top right, rgba(255,255,255,0.05), transparent 20rem), linear-gradient(180deg, #080808 0%, #171717 100%);">
    <div class="auth-panel app-card mx-auto">
        <div class="auth-side">
            <span class="eyebrow text-white-50 mb-3">Password Reset</span>
            <h1 class="hero-title mb-4">Recover your account access.</h1>
            <p class="mb-4" style="color: rgba(248, 244, 235, 0.78); line-height: 1.8;">
                Enter your registered email address and we'll send you a secure link to reset your password.
            </p>

            <div class="feature-strip">
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Check your inbox</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Look for the email with the subject "Reset Password".</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Secure link</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">The link expires after 60 minutes for security.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">New password</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Choose a strong password you can remember.</div>
                </div>
            </div>
        </div>

        <div class="auth-form">
            <span class="eyebrow mb-3">Forgot Password</span>
            <h2 class="section-title mb-3">Reset your password</h2>
            <p class="lede mb-5">Enter your email address and we'll send you a link to create a new password.</p>

            @if(session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" required class="form-control border-start-0" value="{{ old('email') }}" placeholder="staff@hospital.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Send Password Reset Link</button>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-decoration-none small">Remember your password? Log in</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection