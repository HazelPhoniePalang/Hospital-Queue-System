@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
                <div>
                    <span class="eyebrow mb-3">Counter Station</span>
                    <h1 class="section-title mb-3">Patient registration and queue ticket generation</h1>
                    <p class="lede mb-0">
                        Complete the patient profile, select the department and service required, and generate
                        the queue ticket with the corresponding service fee.
                    </p>
                </div>
                <div class="glass-panel p-4" style="max-width: 360px;">
                    <div class="fw-semibold mb-2">What appears on the ticket</div>
                    <div class="text-body-tertiary small">Queue number, department, service, and amount due based on department pricing.</div>
                </div>
            </div>

            <div class="app-card p-4 p-lg-5">
                <form action="{{ route('kiosk.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h3 class="h2 mb-4">Patient details</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label fw-semibold">First name</label>
                                    <input type="text" id="first_name" name="first_name" required class="form-control" value="{{ old('first_name') }}" placeholder="Enter first name">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label fw-semibold">Last name</label>
                                    <input type="text" id="last_name" name="last_name" required class="form-control" value="{{ old('last_name') }}" placeholder="Enter last name">
                                </div>
                                <div class="col-md-6">
                                    <label for="birth_date" class="form-label fw-semibold">Birth date</label>
                                    <input type="date" id="birth_date" name="birth_date" required class="form-control" value="{{ old('birth_date') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="gender" class="form-label fw-semibold">Gender</label>
                                    <select id="gender" name="gender" required class="form-select">
                                        <option value="">Select gender</option>
                                        @foreach(['Male', 'Female', 'Other'] as $gender)
                                            <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_no" class="form-label fw-semibold">Contact number</label>
                                    <input type="tel" id="contact_no" name="contact_no" required class="form-control" value="{{ old('contact_no') }}" placeholder="09xx xxx xxxx">
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label fw-semibold">Address</label>
                                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}" placeholder="Street, barangay, city">
                                </div>
                                <div>
                                    <label for="symptoms" class="form-label fw-semibold">Symptoms and notes</label>
                                    <textarea id="symptoms" name="symptoms" class="form-control" rows="3" required placeholder="Symptoms, information or notes" value="{{ old('symptoms') }}">{{ old('symptoms') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="h-100 d-flex flex-column gap-4">
                                <div>
                                    <h3 class="h2 mb-4">Department and service</h3>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="department_id" class="form-label fw-semibold">Department</label>
                                            <select id="department_id" name="department_id" required class="form-select" onchange="updateServices()">
                                                <option value="">Select department</option>
                                                @foreach($departments as $dept)
                                                    @php
                                                        $servicesJson = $dept->services
                                                            ->map(function ($service) {
                                                                return [
                                                                    'id' => $service->id,
                                                                    'service_name' => $service->service_name,
                                                                    'cost' => number_format((float) $service->cost, 2, '.'),
                                                                    'average_duration' => $service->average_duration,
                                                                ];
                                                            })
                                                            ->values()
                                                            ->toJson();
                                                    @endphp
                                                    <option
                                                        value="{{ $dept->id }}"
                                                        data-services='{{ $servicesJson }}'
                                                        @selected((string) old('department_id') === (string) $dept->id)
                                                    >
                                                        {{ $dept->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label for="service_id" class="form-label fw-semibold">Service</label>
                                            <select id="service_id" name="service_id" required class="form-select" onchange="updateSummary()">
                                                <option value="">Select service</option>
                                                @php
                                                    $selectedDeptId = old('department_id');
                                                    if ($selectedDeptId) {
                                                        $selectedDept = $departments->find($selectedDeptId);
                                                        if ($selectedDept) {
                                                            foreach ($selectedDept->services as $service) {
                                                                $selected = (string) old('service_id') === (string) $service->id ? 'selected' : '';
                                                                echo "<option value='{$service->id}' data-cost='" . number_format((float) $service->cost, 2, '.') . "' data-duration='{$service->average_duration}' {$selected}>{$service->service_name} · PHP " . number_format((float) $service->cost, 2, '.') . "</option>";
                                                            }
                                                        }
                                                    }
                                                @endphp
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="glass-panel p-4 mt-auto">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div>
                                            <div class="fw-semibold">Ticket preview</div>
                                            <div class="text-body-tertiary small">Generated after registration is submitted.</div>
                                        </div>
                                        <i class="bi bi-ticket-perforated fs-2 text-body-tertiary"></i>
                                    </div>
                                    <div class="info-list">
                                        <div class="info-item">
                                            <span class="info-label">Department</span>
                                            <span class="info-value" id="summary-department">Not selected</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Service</span>
                                            <span class="info-value" id="summary-service">Not selected</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Service fee</span>
                                            <span class="info-value" id="summary-fee">PHP 0.00</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Estimated duration</span>
                                            <span class="info-value" id="summary-duration">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger mt-4 mb-0 rounded-4 border-0">
                            Please review the form and complete all required fields correctly.
                        </div>
                    @endif

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 pt-4 mt-4 border-top">
                        <a href="{{ route('kiosk.index') }}" class="text-decoration-none text-body-tertiary">Return to registration home</a>
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            Generate Queue Ticket
                            <i class="bi bi-ticket-perforated ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const oldDepartmentId = @json(old('department_id'));
    const oldServiceId = @json(old('service_id'));

    function updateServices() {
        const deptSelect = document.getElementById('department_id');
        const serviceSelect = document.getElementById('service_id');
        const selectedOption = deptSelect.options[deptSelect.selectedIndex];

        serviceSelect.innerHTML = '<option value="">Select service</option>';

        if (!selectedOption || !selectedOption.value) {
            serviceSelect.disabled = false;
            updateSummary();
            return;
        }

        const services = JSON.parse(selectedOption.getAttribute('data-services') || '[]');
        services.forEach((service) => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = `${service.service_name} · PHP ${Number(service.cost).toFixed(2)}`;
            option.dataset.cost = service.cost;
            option.dataset.duration = service.average_duration;
            if (String(oldServiceId) === String(service.id)) {
                option.selected = true;
            }
            serviceSelect.appendChild(option);
        });

        serviceSelect.disabled = false;
        updateSummary();
    }

    function updateSummary() {
        const departmentSelect = document.getElementById('department_id');
        const serviceSelect = document.getElementById('service_id');
        const selectedDepartment = departmentSelect.options[departmentSelect.selectedIndex];
        const selectedService = serviceSelect.options[serviceSelect.selectedIndex];

        document.getElementById('summary-department').textContent = selectedDepartment && selectedDepartment.value ? selectedDepartment.textContent.trim() : 'Not selected';
        document.getElementById('summary-service').textContent = selectedService && selectedService.value ? selectedService.textContent.split('·')[0].trim() : 'Not selected';
        document.getElementById('summary-fee').textContent = selectedService && selectedService.dataset.cost ? `PHP ${Number(selectedService.dataset.cost).toFixed(2)}` : 'PHP 0.00';
        document.getElementById('summary-duration').textContent = selectedService && selectedService.dataset.duration ? `${selectedService.dataset.duration} minutes` : '-';
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (oldDepartmentId) {
            document.getElementById('department_id').value = oldDepartmentId;
        }
        updateServices();
    });
</script>
@endsection
