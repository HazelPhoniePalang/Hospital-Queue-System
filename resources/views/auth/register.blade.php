@extends('layouts.app')

@section('content')

<div class="auth-wrap">
    <div class="auth-panel app-card mx-auto">
        <div class="auth-side app-card">
            <span class="eyebrow text-white-50 mb-3">Administrator Registration</span>
            <h1 class="hero-title mb-4">Create your admin account</h1>
            <p class="mb-4" style="color: rgba(248, 244, 235, 0.78); line-height: 1.8;">
                Register as an administrator to manage the hospital system, including users, departments, and system settings.
            </p>

            <div class="feature-strip">
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">User Management</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Create and manage staff, doctors, and administrator accounts.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">System Oversight</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Maintain departments, services, and pricing configurations.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Reports & Analytics</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">View detailed reports on patient visits and system performance.</div>
                </div>
            </div>
        </div>

        <div class="auth-form">
            <span class="eyebrow mb-3">Register</span>
            <h2 class="section-title mb-3">Create administrator account</h2>
            <br>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-person"></i></span>
                        <input type="text" id="name" name="name" required class="form-control border-start-0" value="{{ old('name') }}" placeholder="Enter your full name">
                    </div>
                    @error('name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" required class="form-control border-start-0" value="{{ old('email') }}" placeholder="admin@hospital.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="role_id" class="form-label fw-semibold">Role</label>
                    <select id="role_id" name="role_id" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" required class="form-control border-start-0" placeholder="Create a strong password">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control border-start-0" placeholder="Confirm your password">
                    </div>
                    @error('password_confirmation')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Create Account</button>

                <div class="text-center mt-3">
                    <span class="text-body-tertiary small">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-decoration-none small">Log in here</a>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
