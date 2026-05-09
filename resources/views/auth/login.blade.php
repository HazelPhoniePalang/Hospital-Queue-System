@extends('layouts.app')

@section('content')

<div class="auth-wrap">
    <div class="auth-panel app-card mx-auto">
        <div class="auth-side app-card">
            <span class="eyebrow text-white-50 mb-3">Staff Login</span>
            <h1 class="hero-title mb-4">Access your dashboard</h1>
            <p class="mb-4" style="color: rgba(248, 244, 235, 0.78); line-height: 1.8;">
                Log in to manage patients, view reports, and oversee hospital operations.
            </p>

            <div class="feature-strip">
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Patient Management</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Handle patient records and queue management.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Dashboard Access</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Monitor real-time operations and statistics.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Reports & Exports</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Generate and export detailed system reports.</div>
                </div>
            </div>
        </div>

        <div class="auth-form">
            <span class="eyebrow mb-3">Login</span>
            <h2 class="section-title mb-3">Sign in to your account</h2>
            <br>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control border-start-0" placeholder="Enter your email">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock"></i></span>
                        <input id="password" type="password" name="password" required class="form-control border-start-0" placeholder="Enter your password">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input">
                        <span class="form-check-label">Remember me</span>
                    </label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Log in</button>
                </div>

                @if (Route::has('password.request'))
                    <div class="mt-3 text-center">
                        <a href="{{ route('password.request') }}" class="text-decoration-none small">Forgot your password?</a>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection