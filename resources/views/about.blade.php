@extends('layouts.app')

@section('content')
<div class="container py-5">
    <section class="row align-items-center g-5 mb-5">
        <div class="col-lg-7">
            <span class="eyebrow mb-3">About Palang Hospital</span>
            <h1 class="section-title mb-3">Care that keeps every patient moving with clarity and dignity.</h1>
            <p class="lede mb-0">
                Palang Hospital is built around accessible, organized, and compassionate healthcare service.
                This website supports that mission by helping patients register quickly, helping staff manage queues
                smoothly, and helping doctors receive the right patient information at the right time.
            </p>
        </div>
        <div class="col-lg-5">
            <div class="dark-panel p-4 p-lg-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="brand-mark">PH</span>
                    <div>
                        <div class="fw-semibold fs-4">Palang Hospital</div>
                        <div class="text-white-50">Hospital Queue Management System</div>
                    </div>
                </div>
                <p class="mb-0 text-white-50">
                    A calm digital front desk for patient registration, queue display, payment processing,
                    consultation assignment, and administrative monitoring.
                </p>
            </div>
        </div>
    </section>

    <section class="app-card p-4 p-lg-5 mb-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <span class="eyebrow mb-3">Hospital Background</span>
                <h2 class="h1 mb-0">Serving patients through organized hospital flow.</h2>
            </div>
            <div class="col-lg-7">
                <p class="lede mb-3">
                    The hospital focuses on giving patients a more orderly experience from arrival to consultation.
                    Instead of relying only on manual listing and verbal calls, the system records patient details,
                    assigns queue numbers, routes patients to the right department, and keeps staff updated on each
                    patient status.
                </p>
                <p class="lede mb-0">
                    By using one shared queue management platform, Palang Hospital can reduce confusion at the front
                    desk, make department responsibilities clearer, and provide a more reliable service experience for
                    both patients and healthcare workers.
                </p>
            </div>
        </div>
    </section>

    <section class="row g-4">
        <div class="col-md-4">
            <div class="metric-card h-100">
                <i class="bi bi-person-lines-fill fs-2 mb-3 d-inline-block"></i>
                <div class="fw-semibold fs-4 mb-2">Patient Registration</div>
                <div class="text-body-tertiary">Patients can enter their information and receive a queue number for the correct department.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card h-100">
                <i class="bi bi-display fs-2 mb-3 d-inline-block"></i>
                <div class="fw-semibold fs-4 mb-2">Queue Visibility</div>
                <div class="text-body-tertiary">Staff can call patients in order while the display board shows active queue updates.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card h-100">
                <i class="bi bi-heart-pulse fs-2 mb-3 d-inline-block"></i>
                <div class="fw-semibold fs-4 mb-2">Coordinated Care</div>
                <div class="text-body-tertiary">Doctors and staff can coordinate consultation, payment, and patient records in one workflow.</div>
            </div>
        </div>
    </section>
</div>
@endsection
