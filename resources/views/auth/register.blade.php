@extends('layouts.app')

@section('content')
<div class="container py-5" style="min-height: 100vh; background: radial-gradient(circle at top right, rgba(255,255,255,0.05), transparent 20rem), linear-gradient(180deg, #080808 0%, #171717 100%);">
    <div class="auth-panel app-card mx-auto">
        <div class="auth-side">
            <span class="eyebrow text-white-50 mb-3">Account Provisioning</span>
            <h1 class="hero-title mb-4">Create professional access for admin, doctors, and hospital staff.</h1>
            <p class="mb-4" style="color: rgba(248, 244, 235, 0.78); line-height: 1.8;">
                Use this registration page to onboard operational users. Assign the correct role and department
                so each account lands on the right dashboard and has the right queue responsibilities.
            </p>

            <div class="feature-strip">
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Admin accounts</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Manage users, departments, and service pricing.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Doctor accounts</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Review called patients and record consultations.</div>
                </div>
                <div class="feature-tile" style="background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.08);">
                    <div class="fw-semibold mb-2">Staff accounts</div>
                    <div class="small" style="color: rgba(248, 244, 235, 0.7);">Handle queues, call patients, and process payments.</div>
                </div>
            </div>
        </div>

        <div class="auth-form">
            <span class="eyebrow mb-3">Registration</span>
            <h2 class="section-title mb-3">Create a new staff account</h2>
            <p class="lede mb-4">Fill in account details carefully. Department is required for doctors.</p>

            @php($selectedRole = $roles->firstWhere('id', old('role_id')))
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Full name</label>
                    <input type="text" id="name" name="name" required autofocus autocomplete="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Juan Dela Cruz">
                    @error('name')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email address</label>
                    <input type="email" id="email" name="email" required autocomplete="username" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="staff@hospital.com">
                    @error('email')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="role_id" class="form-label fw-semibold">Register as</label>
                    <select id="role_id" name="role_id" required onchange="toggleDepartment(this.value)" class="form-select @error('role_id') is-invalid @enderror">
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3" id="department_container" style="{{ $selectedRole && !in_array($selectedRole->name, ['Admin']) ? '' : 'display: none;' }}">
                    <label for="department_id" class="form-label fw-semibold">Department</label>
                    <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">Select a department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" placeholder="Create password">
                    @error('password')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password">
                    @error('password_confirmation')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('login') }}" class="text-decoration-none text-body-tertiary">Already have an account?</a>
                    <a href="{{ route('kiosk.index') }}" class="text-decoration-none text-body-tertiary">Back to patient registration</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Create Account</button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleDepartment(roleId) {
        const roles = @json($roles->pluck('name', 'id'));
        const departmentContainer = document.getElementById('department_container');
        const departmentSelect = document.getElementById('department_id');

        if (roles[roleId] && roles[roleId] !== 'Admin') {
            departmentContainer.style.display = 'block';
            departmentSelect.required = roles[roleId] === 'Doctor';
        } else {
            departmentContainer.style.display = 'none';
            departmentSelect.required = false;
            departmentSelect.value = '';
        }
    }
</script>
@endsection
