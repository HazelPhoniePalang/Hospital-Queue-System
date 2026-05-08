@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">Patient Management</span>
            <h1 class="section-title mb-3">Patient information records</h1>
            <p class="lede mb-0">Add, edit, and archive patient details used by queues, visits, payments, and reports.</p>
        </div>
        <div class="d-flex align-items-start">
            <button type="button" class="btn btn-primary px-4" onclick="openCreatePatientModal()">
                <i class="bi bi-person-plus me-2"></i>Add Patient
            </button>
            <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary px-4 ms-2">Reports</a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.patients') }}" class="d-flex flex-column flex-md-row gap-2 mb-4">
        <input type="search" name="search" class="form-control" value="{{ $search }}" placeholder="Search patient name, contact, gender, address...">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary px-4">Search</button>
            @if($search)
                <a href="{{ route('admin.patients') }}" class="btn btn-outline-secondary px-4">Clear</a>
            @endif
        </div>
    </form>

    <div class="app-card p-4 p-lg-5">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-body-tertiary text-uppercase small">Patient</th>
                        <th class="text-body-tertiary text-uppercase small">Birth Date</th>
                        <th class="text-body-tertiary text-uppercase small">Gender</th>
                        <th class="text-body-tertiary text-uppercase small">Contact</th>
                        <th class="text-body-tertiary text-uppercase small">Address</th>
                        <th class="text-body-tertiary text-uppercase small">Activity</th>
                        <th class="text-body-tertiary text-uppercase small">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td class="fw-semibold">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                            <td>{{ $patient->birth_date?->format('M d, Y') ?? '-' }}</td>
                            <td>{{ $patient->gender }}</td>
                            <td>{{ $patient->contact_no ?: '-' }}</td>
                            <td>{{ $patient->address ? Str::limit($patient->address, 42) : '-' }}</td>
                            <td>
                                <span class="subtle-chip px-2 py-1 rounded-pill small">{{ $patient->queues_count }} queues</span>
                                <span class="subtle-chip px-2 py-1 rounded-pill small">{{ $patient->visits_count }} visits</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="Edit patient"
                                        data-patient-id="{{ $patient->getKey() }}"
                                        data-first-name="{{ $patient->first_name }}"
                                        data-last-name="{{ $patient->last_name }}"
                                        data-birth-date="{{ $patient->birth_date?->format('Y-m-d') }}"
                                        data-gender="{{ $patient->gender }}"
                                        data-contact-no="{{ $patient->contact_no }}"
                                        data-address="{{ $patient->address }}"
                                        onclick="openEditPatientModal(this)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.patients.delete', $patient->getKey()) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Archive patient" onclick="return confirm('Delete this patient record?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">{{ $search ? 'No patient records match your search.' : 'No patient records have been created yet.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('admin.patients.archive') }}" class="text-decoration-none text-body-tertiary small">View deleted patients</a>
    </div>

    <div class="modal fade" id="patient-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h2 class="h3 mb-0" id="patient-modal-title">Create patient</h2>
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

                    <form id="patient-form" action="{{ route('admin.patients.store') }}" method="POST">
                        @csrf
                        <div id="patient-method-field"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="patient-first-name" class="form-label fw-semibold">First name</label>
                                <input type="text" name="first_name" id="patient-first-name" required class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}">
                                @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="patient-last-name" class="form-label fw-semibold">Last name</label>
                                <input type="text" name="last_name" id="patient-last-name" required class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}">
                                @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="patient-birth-date" class="form-label fw-semibold">Birth date</label>
                                <input type="date" name="birth_date" id="patient-birth-date" required class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}">
                                @error('birth_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="patient-gender" class="form-label fw-semibold">Gender</label>
                                <select name="gender" id="patient-gender" required class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">Select gender</option>
                                    @foreach(['Female', 'Male', 'Other'] as $gender)
                                        <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                                    @endforeach
                                </select>
                                @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="patient-contact-no" class="form-label fw-semibold">Contact number</label>
                                <input type="text" name="contact_no" id="patient-contact-no" class="form-control @error('contact_no') is-invalid @enderror" value="{{ old('contact_no') }}">
                                @error('contact_no') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <label for="patient-address" class="form-label fw-semibold">Address</label>
                                <textarea name="address" id="patient-address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                                @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-4">Save Patient</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function openCreatePatientModal() {
        resetPatientForm();
        new bootstrap.Modal(document.getElementById('patient-modal')).show();
    }

    function openEditPatientModal(button) {
        resetPatientForm();

        const patientId = button.getAttribute('data-patient-id');
        document.getElementById('patient-modal-title').textContent = 'Edit patient';
        document.getElementById('patient-form').action = '/admin/patients/' + patientId;
        document.getElementById('patient-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('patient-first-name').value = button.getAttribute('data-first-name') || '';
        document.getElementById('patient-last-name').value = button.getAttribute('data-last-name') || '';
        document.getElementById('patient-birth-date').value = button.getAttribute('data-birth-date') || '';
        document.getElementById('patient-gender').value = button.getAttribute('data-gender') || '';
        document.getElementById('patient-contact-no').value = button.getAttribute('data-contact-no') || '';
        document.getElementById('patient-address').value = button.getAttribute('data-address') || '';

        new bootstrap.Modal(document.getElementById('patient-modal')).show();
    }

    function resetPatientForm() {
        const form = document.getElementById('patient-form');
        form.reset();
        form.action = '{{ route("admin.patients.store") }}';
        document.getElementById('patient-method-field').innerHTML = '';
        document.getElementById('patient-modal-title').textContent = 'Create patient';
    }

    document.getElementById('patient-modal').addEventListener('hidden.bs.modal', resetPatientForm);

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            new bootstrap.Modal(document.getElementById('patient-modal')).show();
        });
    @endif
    </script>
</div>
@endsection
