@extends('layouts.app')

@section('content')
<div class="container py-2" style= "min-height: 100vh;  linear-gradient(180deg, #080808 0%, #171717 100%); ">
    <div class="auth-panel app-card mx-auto">
        <div class="auth-side">
            <span class="eyebrow text-white-50 mb-3">Hospital Staff Access</span>
            <h1 class="hero-title mb-4">Secure access for staff, doctors, and administrators.</h1>
            <p class="mb-4" style="color: rgba(248, 244, 235, 0.78); line-height: 1.8;">
                Staff can manage the live queue and counter activity. Doctors can review assigned patients
                and record consultations. Administrators can maintain department pricing, users, and system oversight.
            </p>

            <div class="feature-strip">
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Queue handling</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Call next, update status, and keep the display board synchronized.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Consultation records</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Doctors can record notes and diagnosis for each completed visit.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Pricing control</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Admin screens maintain department services and service fees.</div>
                </div>
            </div>
        </div>

        <div class="auth-form">
            <span class="eyebrow mb-3">Login</span>
            <h2 class="section-title mb-3">Enter your account credentials</h2>
            <br>            

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" required class="form-control border-start-0" value="{{ old('email') }}" placeholder="staff@hospital.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" required class="form-control border-start-0" placeholder="Enter password">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember" class="form-check-input">
                        <label for="remember" class="form-check-label text-body-tertiary">Remember me</label>
                    </div>
                    <div>
                        <a href="{{ route('password.request') }}" class="text-decoration-none small me-3">Forgot password?</a>
                        
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Log In</button>
            </form>
        </div>
    </div>
</div>
@endsection
