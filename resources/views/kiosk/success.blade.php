@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="text-center mb-4">
                <span class="eyebrow mb-3">Registration complete</span>
                <h1 class="section-title mb-3">Queue ticket generated successfully</h1>
                <p class="lede mb-0">Print or hand over this ticket to the patient, then direct them to wait until the queue number is called on the display screen.</p>
            </div>

            <div class="app-card p-4 p-lg-5 mb-4 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 mt-4 me-4 text-body-tertiary opacity-25">
                    <i class="bi bi-ticket-perforated" style="font-size: 7rem;"></i>
                </div>

                <div class="row align-items-center g-4 position-relative">
                    <div class="col-lg-5 text-center text-lg-start">
                        <div class="info-label mb-2">Queue number</div>
                        <div class="ticket-no">{{ $queue->queue_no }}</div>
                    </div>
                    <div class="col-lg-7">
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Patient</span>
                                <span class="info-value">{{ $queue->patient->full_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Department</span>
                                <span class="info-value">{{ $queue->department->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Service</span>
                                <span class="info-value">{{ $queue->service->service_name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Service fee</span>
                                <span class="info-value">PHP {{ number_format((float) $queue->service->cost, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-4 mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="fw-semibold mb-1">1. Save</div>
                        <div class="text-body-tertiary small">Patient record and queue number are already stored in the system.</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold mb-1">2. Print</div>
                        <div class="text-body-tertiary small">Use this screen as the print-ready summary for the patient ticket.</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold mb-1">3. Wait for call</div>
                        <div class="text-body-tertiary small">The patient proceeds when the queue board updates with their number.</div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                <a href="{{ route('kiosk.index') }}" class="btn btn-primary btn-lg px-4">Register another patient</a>
                <a href="{{ route('display') }}" class="btn btn-outline-dark btn-lg px-4">View display board</a>
            </div>
        </div>
    </div>
</div>
@endsection
