@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">Service Pricing</span>
            <h1 class="section-title mb-3">Department services and amount due</h1>
            <p class="lede mb-0">These service fees are automatically used when staff registers a patient and generates a queue ticket.</p>
        </div>
        <div class="d-flex align-items-start">
            <button onclick="new bootstrap.Modal(document.getElementById('service-modal')).show()" class="btn btn-primary px-4">Add Service</button>
        </div>
    </div>

    <div class="app-card p-4 p-lg-5">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-body-tertiary text-uppercase small">Service</th>
                        <th class="text-body-tertiary text-uppercase small">Department</th>
                        <th class="text-body-tertiary text-uppercase small">Average duration</th>
                        <th class="text-body-tertiary text-uppercase small">Price</th>
                        <th class="text-body-tertiary text-uppercase small">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $s)
                        <tr>
                            <td class="fw-semibold">{{ $s->service_name }}</td>
                            <td>{{ $s->department->name }}</td>
                            <td>{{ $s->average_duration }} minutes</td>
                            <td class="fw-semibold">PHP {{ number_format((float) $s->cost, 2) }}</td>
                            <td>
                                <button type="button"
                                    data-service-id="{{ $s->getKey() }}"
                                    data-service-name="{{ $s->service_name }}"
                                    data-service-dept="{{ $s->department_id }}"
                                    data-service-duration="{{ $s->average_duration }}"
                                    data-service-cost="{{ $s->cost }}"
                                    onclick="editService(this)"
                                    class="btn btn-sm btn-outline-secondary" title="Edit">✏️</button>
                                <form action="{{ route('admin.services.delete', $s->getKey()) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Archive this service?')" title="Archive">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No services have been configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('admin.services.archive') }}" class="text-decoration-none text-body-tertiary small">View archived services</a>
    </div>

    <div class="modal fade" id="service-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h2 class="h3 mb-0" id="service-modal-title">Create service</h2>
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
                    <form id="service-form" action="{{ route('admin.services.store') }}" method="POST">
                        @csrf
                        <div id="service-method-field"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" id="service-dept" required class="form-select @error('department_id') is-invalid @enderror">
                                @foreach($departments as $d)
                                    <option value="{{ $d->getKey() }}" @if(old('department_id') == $d->getKey()) selected @endif>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Service name</label>
                            <input type="text" name="service_name" id="service-name" required class="form-control @error('service_name') is-invalid @enderror" placeholder="General consultation" value="{{ old('service_name') }}">
                            @error('service_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Average duration</label>
                                <input type="number" name="average_duration" id="service-duration" required class="form-control @error('average_duration') is-invalid @enderror" value="{{ old('average_duration', 15) }}">
                                @error('average_duration') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Price</label>
                                <input type="number" step="0.01" name="cost" id="service-cost" required class="form-control @error('cost') is-invalid @enderror" placeholder="0.00" value="{{ old('cost') }}">
                                @error('cost') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Service</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function editService(btn) {
        const serviceId = btn.dataset.serviceId;
        const serviceName = btn.dataset.serviceName;
        const serviceDept = btn.dataset.serviceDept;
        const serviceDuration = btn.dataset.serviceDuration;
        const serviceCost = btn.dataset.serviceCost;
        
        document.getElementById('service-modal-title').textContent = 'Edit service';
        document.getElementById('service-form').action = '/admin/services/' + serviceId;
        document.getElementById('service-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('service-dept').value = serviceDept;
        document.getElementById('service-name').value = serviceName;
        document.getElementById('service-duration').value = serviceDuration;
        document.getElementById('service-cost').value = serviceCost;
        new bootstrap.Modal(document.getElementById('service-modal')).show();
    }
    
    document.getElementById('service-modal').addEventListener('hide.bs.modal', function() {
        const form = document.getElementById('service-form');
        form.reset();
        form.action = '{{ route('admin.services.store') }}';
        document.getElementById('service-method-field').innerHTML = '';
        document.getElementById('service-modal-title').textContent = 'Create service';
        document.getElementById('service-duration').value = '15';
    });
    </script>
</div>
@endsection
