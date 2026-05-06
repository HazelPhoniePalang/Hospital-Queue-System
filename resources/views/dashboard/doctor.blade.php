@extends('layouts.app')

@section('content')
<div class="container py-5">
    @if(session('success') && session('download_pdf'))
    <div class="alert alert-success d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3 rounded-5">
        <div>
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('visits.download-pdf', session('download_pdf')) }}" target="_blank" class="btn btn-success">
                <i class="bi bi-file-earmark-pdf me-2"></i>Download Medical Certificate
            </a>
            <!-- @if(session('visit_id'))
            <a href="{{ route('visits.clinical-notes', session('visit_id')) }}" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-medical me-2"></i>Download Clinical Notes (Patient Copy)
            </a>
            @endif -->
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger mb-4">
        <i class="bi bi-exclamation-circle me-2"></i>
        {{ session('error') }}
    </div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">Doctor Dashboard</span>
            <h1 class="section-title mb-3">Consultation patients for {{ $department->name }}</h1>
            
        </div>

        <div class="d-flex flex-wrap gap-3">
            <div class="metric-card" style="min-width: 200px;">
                <div class="info-label mb-2">Seen today</div>
                <div class="metric-value">{{ $stats['today_patients'] }}</div>
            </div>
            <div class="metric-card" style="min-width: 200px;">
                <div class="info-label mb-2">Seen this week</div>
                <div class="metric-value">{{ $stats['week_patients'] }}</div>
            </div>
        </div>
    </div>

    <div class="app-card p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h1 mb-1">Assigned patients for consultation</h2>
                <div class="text-body-tertiary">Patients assigned to you for medical consultation.</div>
            </div>
            <span class="subtle-chip">Assigned consults</span>
        </div>

        <div class="row g-4">
            @forelse($assignedVisits as $visit)
                <div class="col-12">
                    <div class="glass-panel p-4">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-2">
                                <div class="ticket-no text-center" style="font-size: clamp(2.8rem, 8vw, 4.2rem);">{{ $visit->queue->queue_no }}</div>
                            </div>
                            <div class="col-lg-7">
                                <div class="fw-semibold fs-4 mb-1">{{ $visit->patient?->full_name ?? 'Unknown Patient' }}</div>
                                <div class="text-body-tertiary mb-2">
                                    {{-- ✅ Fixed --}}
                                    {{ $visit->patient?->gender ?? 'N/A' }} · 
                                    {{ $visit->patient?->birth_date ? \Carbon\Carbon::parse($visit->patient->birth_date)->age : 'N/A' }} years old · 
                                    {{ $visit->queue?->service?->service_name ?? 'N/A' }}
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="subtle-chip">Assigned at {{ $visit->visit_date->format('h:i A') }}</span>
                                    <span class="subtle-chip">Status: {{ ucfirst($visit->status) }}</span>
                                    <span class="subtle-chip">{{ $department->name }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3 text-lg-end">
                                <a href="{{ route('visits.consultation', $visit->queue_id) }}" class="btn btn-primary px-4 mb-2">Open Consultation</a>
                                @if($visit->notes && $visit->diagnosis)
                                <br>
                                <a href="{{ route('visits.clinical-notes', $visit->id) }}" target="_blank" class="btn btn-outline-success btn-sm mt-2">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>Patient Copy
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">No patients assigned for consultation right now.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
