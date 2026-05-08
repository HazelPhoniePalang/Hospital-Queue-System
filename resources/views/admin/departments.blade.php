@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">Department Management</span>
            <h1 class="section-title mb-3">Maintain departments and service locations</h1>
            <p class="lede mb-0">Departments created here are used in patient registration, queue routing, and dashboard assignment.</p>
        </div>
        <div class="d-flex align-items-start">
            <button onclick="new bootstrap.Modal(document.getElementById('dept-modal')).show()" class="btn btn-primary px-4">Add Department</button>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.departments') }}" class="d-flex flex-column flex-md-row gap-2 mb-4">
        <input type="search" name="search" class="form-control" value="{{ $search }}" placeholder="Search department name, code, location...">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary px-4">Search</button>
            @if($search)
                <a href="{{ route('admin.departments') }}" class="btn btn-outline-secondary px-4">Clear</a>
            @endif
        </div>
    </form>

    <div class="row g-4">
        @forelse($departments as $d)
            <div class="col-md-6 col-xl-4">
                <div class="app-card p-4 h-100 position-relative">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <span class="subtle-chip">{{ $d->code }}</span>
                        <span class="text-body-tertiary small">{{ $d->services_count }} services</span>
                    </div>
                    <div class="fw-semibold fs-4 mb-2">{{ $d->name }}</div>
                    <div class="text-body-tertiary mb-4">{{ $d->location ?: 'Location not yet assigned' }}</div>
                    <div class="d-flex gap-2">
                        <button type="button"
                            data-dept-id="{{ $d->getKey() }}"
                            data-dept-name="{{ $d->name }}"
                            data-dept-code="{{ $d->code }}"
                            data-dept-location="{{ $d->location }}"
                            onclick="editDept(this)"
                            class="btn btn-sm btn-outline-secondary flex-grow-1" title="Edit">Edit</button>
                        <form action="{{ route('admin.departments.delete', $d->getKey()) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Archive this department?')" title="Archive">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">{{ $search ? 'No departments match your search.' : 'No departments have been created yet.' }}</div>
            </div>
        @endforelse
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('admin.departments.archive') }}" class="text-decoration-none text-body-tertiary small">View archived departments</a>
    </div>

    <div class="modal fade" id="dept-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h2 class="h3 mb-0" id="dept-modal-title">Create department</h2>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form id="dept-form" action="{{ route('admin.departments.store') }}" method="POST">
                        @csrf
                        <div id="dept-method-field"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Department name</label>
                            <input type="text" name="name" id="dept-name" required class="form-control @error('name') is-invalid @enderror" placeholder="Cardiology" value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Code</label>
                            <input type="text" name="code" id="dept-code" required class="form-control @error('code') is-invalid @enderror" placeholder="CARD" value="{{ old('code') }}">
                            @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Location</label>
                            <input type="text" name="location" id="dept-location" class="form-control @error('location') is-invalid @enderror" placeholder="Building A, Level 2" value="{{ old('location') }}">
                            @error('location') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Department</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function editDept(btn) {
        const deptId = btn.dataset.deptId;
        const deptName = btn.dataset.deptName;
        const deptCode = btn.dataset.deptCode;
        const deptLocation = btn.dataset.deptLocation;
        
        document.getElementById('dept-modal-title').textContent = 'Edit department';
        document.getElementById('dept-form').action = '/admin/departments/' + deptId;
        document.getElementById('dept-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('dept-name').value = deptName;
        document.getElementById('dept-code').value = deptCode;
        document.getElementById('dept-location').value = deptLocation || '';
        new bootstrap.Modal(document.getElementById('dept-modal')).show();
    }
    
    document.getElementById('dept-modal').addEventListener('hide.bs.modal', function() {
        const form = document.getElementById('dept-form');
        form.reset();
        form.action = '{{ route('admin.departments.store') }}';
        document.getElementById('dept-method-field').innerHTML = '';
        document.getElementById('dept-modal-title').textContent = 'Create department';
    });
    </script>
</div>
@endsection
