@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">Counter Management</span>
            <h1 class="section-title mb-3">Assign counters to staff</h1>
            <p class="lede mb-0">Create and manage service counters, assigning them to hospital staff for efficient queue management.</p>
        </div>
        <div class="d-flex align-items-start gap-3">
            <form method="GET" action="{{ route('admin.counters') }}" class="d-flex gap-2">
                <select name="department_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->getKey() }}" {{ request('department_id') == $dept->getKey() ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </form>
            <button onclick="new bootstrap.Modal(document.getElementById('counter-modal')).show()" class="btn btn-primary px-4">Add Counter</button>
        </div>
    </div>

    <div class="app-card p-4 p-lg-5">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-body-tertiary text-uppercase small">Counter</th>
                        <th class="text-body-tertiary text-uppercase small">Department</th>
                        <th class="text-body-tertiary text-uppercase small">Assigned Staff</th>
                        <th class="text-body-tertiary text-uppercase small">Status</th>
                        <th class="text-body-tertiary text-uppercase small">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($counters as $counter)
                        <tr>
                            <td class="fw-semibold">{{ $counter->name }}</td>
                            <td>{{ $counter->department->name }}</td>
                            <td>{{ $counter->assignedStaff ? $counter->assignedStaff->name . ' (' . $counter->assignedStaff->role->name . ')' : 'Not assigned' }}</td>
                            <td><span class="status-chip {{ $counter->status }}">{{ $counter->status }}</span></td>
                            <td>
                                <button type="button"
                                    data-counter-id="{{ $counter->getKey() }}"
                                    data-counter-name="{{ $counter->name }}"
                                    data-counter-dept="{{ $counter->department_id }}"
                                    data-counter-staff="{{ $counter->assigned_staff_id }}"
                                    onclick="editCounter(this)"
                                    class="btn btn-sm btn-outline-secondary" title="Edit">✏️</button>
                                <form action="{{ route('admin.counters.delete', $counter->getKey()) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Archive this counter?')" title="Archive">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No counters have been created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('admin.counters.archive') }}" class="text-decoration-none text-body-tertiary small">View archived counters</a>
    </div>

    <!-- Counter Modal -->
    <div class="modal fade" id="counter-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h2 class="h3 mb-0" id="counter-modal-title">Create counter</h2>
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
                    <form id="counter-form" action="{{ route('admin.counters.store') }}" method="POST">
                        @csrf
                        <div id="counter-method-field"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Counter Name</label>
                            <input type="text" name="name" id="counter-name" required class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Counter 1">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" id="counter-department" required class="form-select @error('department_id') is-invalid @enderror" onchange="filterStaffByDepartment()">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->getKey() }}" @if(old('department_id') == $dept->getKey() || request('department_id') == $dept->getKey()) selected @endif>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Assign Staff (Optional)</label>
                            <select name="assigned_staff_id" id="counter-staff" class="form-select @error('assigned_staff_id') is-invalid @enderror">
                                <option value="">Not assigned</option>
                                @foreach($staff as $person)
                                    <option value="{{ $person->getKey() }}" @if(old('assigned_staff_id') == $person->getKey()) selected @endif>{{ $person->name }} ({{ $person->role->name }})</option>
                                @endforeach
                            </select>
                            @error('assigned_staff_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Counter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    const allStaff = @json($staff);
    const allDepartments = @json($departments);

    function filterStaffByDepartment() {
        const departmentId = document.getElementById('counter-department').value;
        const staffSelect = document.getElementById('counter-staff');
        
        // Clear current options
        staffSelect.innerHTML = '<option value="">Not assigned</option>';
        
        // Filter staff by department
        const filteredStaff = allStaff.filter(s => s.department_id == departmentId);
        
        filteredStaff.forEach(person => {
            const option = document.createElement('option');
            option.value = person.id;
            option.textContent = person.name + ' (' + (person.role ? person.role.name : 'Staff') + ')';
            staffSelect.appendChild(option);
        });
        
        // Reset selection
        staffSelect.value = '';
    }

    function editCounter(btn) {
        const counterId = btn.getAttribute('data-counter-id');
        const counterName = btn.getAttribute('data-counter-name');
        const counterDept = btn.getAttribute('data-counter-dept');
        const counterStaff = btn.getAttribute('data-counter-staff');

        // Update modal title
        document.getElementById('counter-modal-title').textContent = 'Edit counter: ' + counterName;

        // Set form action
        const updateUrl = '{{ route("admin.counters.update", ":id") }}'.replace(':id', counterId);
        document.getElementById('counter-form').action = updateUrl;

        // Add PUT method field
        document.getElementById('counter-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Populate form fields
        document.getElementById('counter-name').value = counterName;
        document.getElementById('counter-department').value = counterDept;

        // Filter staff by selected department first
        filterStaffByDepartment();
        
        // Set staff selection after filtering
        setTimeout(() => {
            document.getElementById('counter-staff').value = counterStaff || '';
        }, 50);

        // Mark as edit mode
        document.getElementById('counter-form').dataset.editMode = 'true';

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('counter-modal'));
        modal.show();
    }

    document.getElementById('counter-modal').addEventListener('hide.bs.modal', function() {
        const form = document.getElementById('counter-form');
        form.reset();
        form.action = '{{ route("admin.counters.store") }}';
        document.getElementById('counter-method-field').innerHTML = '';
        document.getElementById('counter-modal-title').textContent = 'Create counter';
        delete form.dataset.editMode;
        
        // Reset staff dropdown to show all for the default department
        filterStaffByDepartment();
    });

    // Initialize staff filter on page load
    document.addEventListener('DOMContentLoaded', function() {
        filterStaffByDepartment();
    });
    </script>
</div>
@endsection
