@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <a href="{{ route('dashboard') }}" class="text-decoration-none text-body-tertiary d-inline-flex align-items-center mb-3">
                <i class="bi bi-arrow-left me-2"></i>
                Return to doctor dashboard
            </a>
            <span class="eyebrow mb-3">Consultation Form</span>
            <h1 class="section-title mb-3">{{ $queue->patient->full_name }}</h1>
            <p class="lede mb-0">
                Review patient details, confirm the linked queue entry, and save the notes and diagnosis required
                to complete the visit record.
            </p>
        </div>
        <div class="glass-panel p-4" style="max-width: 360px;">
            <div class="info-list">
                <div class="info-item">
                    <span class="info-label">Queue no.</span>
                    <span class="info-value">{{ $queue->queue_no }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Service</span>
                    <span class="info-value">{{ $queue->service->service_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Payment status</span>
                    <span class="info-value">Handled by staff counter</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="app-card p-4 p-lg-5">
                <form action="{{ route('visits.store', $queue->id) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Clinical notes</label>
                        <textarea id="notes" name="notes" rows="8" required class="form-control" placeholder="Enter patient complaints, observations, and relevant consultation notes...">{{ old('notes', $queue->visit?->notes) }}</textarea>
                        @error('notes')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="diagnosis" class="form-label fw-semibold">Diagnosis</label>
                        <textarea id="diagnosis" name="diagnosis" rows="4" required class="form-control" placeholder="Enter diagnosis or clinical impression...">{{ old('diagnosis', $queue->visit?->diagnosis) }}</textarea>
                        @error('diagnosis')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Visit status</label>
                            <select id="status" name="status" required class="form-select">
                                @foreach(['ongoing' => 'Ongoing', 'follow-up' => 'Needs Follow-up', 'completed' => 'Completed'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $queue->visit?->status ?? 'completed') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <button type="submit" class="btn btn-primary btn-lg px-4">Save & Export</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-panel p-4 mb-4">
                <div class="fw-semibold mb-3">Patient information</div>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Gender</span>
                        <span class="info-value">{{ $queue->patient->gender }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Age</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($queue->patient->birth_date)->age }} years</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact</span>
                        <span class="info-value">{{ $queue->patient->contact_no }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address</span>
                        <span class="info-value">{{ $queue->patient->address ?: 'Not provided' }}</span>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-4">
                <div class="fw-semibold mb-3">Previous visits</div>
                @forelse($pastVisits as $v)
                    <div class="timeline-note">
                        <div class="fw-semibold">{{ $v->visit_date->format('F d, Y') }}</div>
                        <div class="text-body-tertiary small mb-1">{{ ucfirst($v->status) }}</div>
                        <div class="small">{{ $v->diagnosis ?: 'No diagnosis recorded' }}</div>
                    </div>
                @empty
                    <div class="text-body-tertiary small">No previous visit history found for this patient.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
