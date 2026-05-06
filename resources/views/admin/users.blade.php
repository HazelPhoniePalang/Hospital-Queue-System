@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">User Management</span>
            <h1 class="section-title mb-3">Staff, doctor, and admin accounts</h1>
            <p class="lede mb-0">Assign users to the right roles and departments so dashboards and queue permissions stay accurate.</p>
        </div>
        <div class="d-flex align-items-start gap-2">
            <button onclick="new bootstrap.Modal(document.getElementById('user-modal')).show()" class="btn btn-primary px-4">Add User</button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary px-4 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Export PDF
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.users.export.pdf') }}">Export All Users</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="app-card p-4 p-lg-5">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-body-tertiary text-uppercase small">Name</th>
                        <th class="text-body-tertiary text-uppercase small">Email</th>
                        <th class="text-body-tertiary text-uppercase small">Role</th>
                        <th class="text-body-tertiary text-uppercase small">Department</th>
                        <th class="text-body-tertiary text-uppercase small">Counter</th>
                        <th class="text-body-tertiary text-uppercase small">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td><span class="subtle-chip">{{ $u->role->name ?? 'Not assigned' }}</span></td>
                            <td>{{ $u->department->name ?? 'Not assigned' }}</td>
                            <td>
                                @php
                                    $assignedCounters = $u->assignedCounters ?? collect();
                                @endphp
                                @if($assignedCounters->count() > 0)
                                    @foreach($assignedCounters as $counter)
                                        <span class="subtle-chip">{{ $counter->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                    data-user-id="{{ $u->getKey() }}"
                                    data-user-name="{{ $u->name }}"
                                    data-user-email="{{ $u->email }}"
                                    data-user-role="{{ $u->role_id }}"
                                    data-user-dept="{{ $u->department_id }}"
                                    data-user-counter="{{ $assignedCounters->first()?->id }}"
                                    onclick="editUser(this)"
                                    class="btn btn-sm btn-outline-secondary" title="Edit">✏️</button>
                                <form action="{{ route('admin.users.delete', $u->getKey()) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Archive this user?')" title="Archive">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No users have been created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('admin.users.archive') }}" class="text-decoration-none text-body-tertiary small">View archived users</a>
    </div>

    <div class="modal fade" id="user-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h2 class="h3 mb-0" id="user-modal-title">Create user</h2>
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
                    <form id="user-form" action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div id="user-method-field"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full name</label>
                            <input type="text" name="name" id="user-name" required class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" id="user-email" required class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" id="user-password" class="form-control @error('password') is-invalid @enderror">
                            <small class="text-muted" id="user-password-help">Required for new users</small>
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role_id" id="user-role" required class="form-select @error('role_id') is-invalid @enderror">
                                @foreach($roles as $r)
                                    <option value="{{ $r->getKey() }}" @if(old('role_id') == $r->getKey()) selected @endif>{{ $r->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" id="user-department" class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">No department</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->getKey() }}" @if(old('department_id') == $d->getKey()) selected @endif>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-4" id="counter-assignment" style="display: none;">
                            <label class="form-label fw-semibold">Assign Counter (Optional)</label>
                            <select name="counter_id" id="user-counter" class="form-select @error('counter_id') is-invalid @enderror">
                                <option value="">No counter assigned</option>
                                @foreach($counters as $counter)
                                    <option value="{{ $counter->id }}"
                                            @if(old('counter_id') == $counter->id) selected @endif
                                            data-department="{{ $counter->department_id }}"
                                            data-assigned="{{ $counter->assigned_staff_id ? 'true' : 'false' }}">
                                        {{ $counter->department->name }} - {{ $counter->name }}
                                        @if($counter->assigned_staff_id)
                                            (Assigned to {{ $counter->assignedStaff->name ?? 'Unknown' }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Only shows counters for the selected department. Counters already assigned to other staff are shown but will be reassigned.</small>
                            @error('counter_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Function to toggle counter assignment visibility
    function toggleCounterAssignment() {
        const roleSelect = document.getElementById('user-role');
        const departmentSelect = document.getElementById('user-department');
        const counterDiv = document.getElementById('counter-assignment');

        const selectedRole = roleSelect.value;
        const selectedDepartment = departmentSelect.value;

        // Show counter assignment only for Hospital Staff (role_id = 14)
        const showCounter = (selectedRole == '1') && selectedDepartment;

        counterDiv.style.display = showCounter ? 'block' : 'none';

        // Filter counters by selected department
        if (showCounter) {
            filterCountersByDepartment(selectedDepartment);
        }
    }

    // Function to filter counters by department
    function filterCountersByDepartment(departmentId) {
        const counterSelect = document.getElementById('user-counter');
        const options = counterSelect.querySelectorAll('option');

        options.forEach(option => {
            if (option.value === '') {
                // Always show "No counter assigned" option
                option.style.display = 'block';
            } else {
                // Show only counters for the selected department
                const optionDept = option.getAttribute('data-department');
                option.style.display = (optionDept == departmentId) ? 'block' : 'none';
            }
        });

        // Reset selection if current selection is not available
        const currentValue = counterSelect.value;
        if (currentValue) {
            const currentOption = counterSelect.querySelector(`option[value="${currentValue}"]`);
            if (currentOption && currentOption.style.display === 'none') {
                counterSelect.value = '';
            }
        }
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('user-role');
        const departmentSelect = document.getElementById('user-department');

        roleSelect.addEventListener('change', toggleCounterAssignment);
        departmentSelect.addEventListener('change', toggleCounterAssignment);

        // Initial check
        toggleCounterAssignment();
    });

    function editUser(btn) {
        try {
            const userId = btn.getAttribute('data-user-id');
            const userName = btn.getAttribute('data-user-name');
            const userEmail = btn.getAttribute('data-user-email');
            const userRole = btn.getAttribute('data-user-role');
            const userDept = btn.getAttribute('data-user-dept');

            // Update modal title
            document.getElementById('user-modal-title').textContent = 'Edit user: ' + userName;

            // Set form action
            document.getElementById('user-form').action = '/admin/users/' + userId;

            // Add PUT method field
            document.getElementById('user-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Populate form fields
        document.getElementById('user-name').value = userName;
        document.getElementById('user-email').value = userEmail;
        document.getElementById('user-password').value = '';
        document.getElementById('user-password').required = false;
        document.getElementById('user-password-help').textContent = 'Leave blank to keep current password';

        // Set role selection
        document.getElementById('user-role').value = userRole;

        // Set department selection
        document.getElementById('user-department').value = userDept || '';

        // Set counter selection
        const userCounter = btn.getAttribute('data-user-counter');
        document.getElementById('user-counter').value = userCounter || '';

        // Mark as edit mode
        document.getElementById('user-form').dataset.editMode = 'true';

        // Trigger counter assignment visibility check
        setTimeout(toggleCounterAssignment, 100);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('user-modal'));
            modal.show();

        } catch (error) {
            console.error('Error in editUser function:', error);
            alert('Error opening edit form. Please check console for details.');
        }
    }
    
    document.getElementById('user-modal').addEventListener('hide.bs.modal', function() {
        const form = document.getElementById('user-form');
        form.reset();
        form.action = '{{ route("admin.users.store") }}';
        document.getElementById('user-method-field').innerHTML = '';
        document.getElementById('user-modal-title').textContent = 'Create user';
        document.getElementById('user-password').required = true;
        document.getElementById('user-password-help').textContent = 'Required for new users';
        delete form.dataset.editMode;

        // Reset counter assignment visibility
        document.getElementById('counter-assignment').style.display = 'none';
    });
    
    // Handle password requirement based on form mode
    document.getElementById('user-form').addEventListener('submit', function(e) {
        const passwordField = document.getElementById('user-password');
        const isEditMode = this.dataset.editMode === 'true';

        if (!isEditMode && !passwordField.value) {
            e.preventDefault();
            alert('Password is required for new users');
            passwordField.focus();
            return;
        }

        // Optional: Log for debugging (can be removed)
        // console.log('Submitting form in edit mode:', isEditMode);

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Saving...';
        submitBtn.disabled = true;

        // Re-enable after a delay (in case of error)
        setTimeout(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
    </script>
</div>
@endsection
