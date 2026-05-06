@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mb-5">
        <div>
            <span class="eyebrow mb-3">Administrator</span>
            <h1 class="section-title mb-3">Department pricing, users, and operational overview</h1>
            <p class="lede mb-0">
                Manage department structures, configure service fees, maintain staff and doctor accounts,
                and monitor overall queue activity from one place.
            </p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="metric-card h-100">
                <div class="info-label pr-2">Total users</div>
                <div class="metric-value">{{ $stats['total_users'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card h-100">
                <div class="info-label mb-2">Departments</div>
                <div class="metric-value">{{ $stats['total_departments'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card h-100">
                <div class="info-label mb-2">Services</div>
                <div class="metric-value">{{ $stats['total_services'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card h-100">
                <div class="info-label mb-2">Queues today</div>
                <div class="metric-value">{{ $stats['total_queues_today'] }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="app-card p-4 p-lg-5 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h1 mb-1">Department overview</h2>
                        <div class="text-body-tertiary">Service pricing and queue demand can be managed per department.</div>
                    </div>
                    <a href="{{ route('admin.departments') }}" class="btn btn-outline-dark btn-sm px-3">Manage departments</a>
                </div>

                <div class="row g-3">
                    @foreach($departments as $dept)
                        <div class="col-md-6">
                            <div class="glass-panel p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="subtle-chip">{{ $dept->code }}</span>
                                    <span class="text-body-tertiary small">{{ $dept->queues_count }} queues</span>
                                </div>
                                <div class="fw-semibold fs-5 mb-1">{{ $dept->name }}</div>
                                <div class="text-body-tertiary small">{{ $dept->location ?: 'Location not yet assigned' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="d-grid gap-4">
                <a href="{{ route('admin.services') }}" class="app-card p-4 text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold fs-4">Services</div>
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <div class="text-body-tertiary">Update service fees that are automatically used during patient registration.</div>
                </a>

                <a href="{{ route('admin.counters') }}" class="app-card p-4 text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold fs-4">Counter Assignment</div>
                        <i class="bi bi-display fs-3"></i>
                    </div>
                    <div class="text-body-tertiary">Create and manage counters per department. Assign counters to specific staff members for queue management.</div>
                </a>

                <a href="{{ route('admin.users') }}" class="app-card p-4 text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold fs-4">User management</div>
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div class="text-body-tertiary">Add and maintain staff, doctor, and admin accounts.</div>
                </a>

                <a href="{{ route('admin.reports') }}" class="app-card p-4 text-decoration-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold fs-4">Reports module</div>
                        <i class="bi bi-file-earmark-bar-graph fs-3"></i>
                    </div>
                    <div class="text-body-tertiary">Filterable reports with PDF or CSV export. Select date range, department, report type, and payment status.</div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
