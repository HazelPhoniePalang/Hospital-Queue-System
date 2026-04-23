@extends('layouts.app')

@section('content')
<div class="container py-lg-1">
    <div class="row g-4 align-items-center min-vh-100">
        <div class="col-lg-7">
            <span class="eyebrow mb-3">Patient Queue Registration</span>
            <h1 class="hero-title mb-4">Welcome Patients.</h1>
            <p class="lede mb-4">
                Reduce patient frustration with a system designed for speed and organization. Hospital queues become predictable, manageable, and stress-free.
            </p>

            <div class="feature-strip mb-4">
                <div class="feature-tile">
                    <div class="fw-semibold mb-2">Fast intake</div>
                    <div class="text-body-tertiary small">Name, birth date, gender, contact number, and address in one guided form.</div>
                </div>
                <div class="feature-tile">
                    <div class="fw-semibold mb-2">Clear queue ticket</div>
                    <div class="text-body-tertiary small">Queue number, department, service, and fee appear immediately after registration.</div>
                </div>
                <div class="feature-tile">
                    <div class="fw-semibold mb-2">Display-ready flow</div>
                    <div class="text-body-tertiary small">Patients simply wait until their number is called on the queue board.</div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('kiosk.form') }}" class="btn btn-primary btn-lg px-4">
                    Start Registration
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>
                <a href="{{ route('display') }}" class="btn btn-outline-dark btn-lg px-4">Open Queue Display</a>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="glass-panel p-4 p-lg-5">
                <span class="subtle-chip mb-4">Counter workflow</span>
                <div class="timeline-note">
                    <div class="fw-semibold">1. Register patient details</div>
                    <div class="text-body-tertiary small">Counter staff fills in the patient's personal and contact information.</div>
                </div>
                <div class="timeline-note">
                    <div class="fw-semibold">2. Select department and service</div>
                    <div class="text-body-tertiary small">The system uses the selected service price for the generated ticket.</div>
                </div>
                <div class="timeline-note">
                    <div class="fw-semibold">3. Generate queue ticket</div>
                    <div class="text-body-tertiary small">A queue number appears on screen and is ready to print for the patient.</div>
                </div>
                <div class="timeline-note">
                    <div class="fw-semibold">4. Wait for call</div>
                    <div class="text-body-tertiary small">The patient watches the display board until their number is announced.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
