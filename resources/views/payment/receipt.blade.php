@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="text-center mb-5">
                <div class="mb-3">
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h1 class="section-title mb-2">Payment Successful</h1>
                <p class="text-body-tertiary">Transaction completed and receipt generated</p>
            </div>

            <!-- Receipt Container -->
            <div class="app-card p-5 mb-4" style="border: 2px solid #e9ecef;">
                <div class="text-center mb-4 pb-4" style="border-bottom: 2px dashed #dee2e6;">
                    <div class="fw-bold mb-2" style="font-size: 1.5rem;">HQMS Receipt</div>
                    <div class="text-body-tertiary small">Hospital Queue Management System</div>
                </div>

                <div class="mb-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-body-tertiary small">Receipt #</div>
                            <div class="fw-semibold">{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="text-body-tertiary small">Date & Time</div>
                            <div class="fw-semibold">{{ $payment->paid_at->format('M d, Y H:i A') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4 pb-4" style="border-bottom: 2px dashed #dee2e6;">
                    <div class="mb-3">
                        <div class="text-body-tertiary small">Patient Name</div>
                        <div class="fw-semibold">{{ $payment->patient->first_name }} {{ $payment->patient->last_name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-body-tertiary small">Contact Number</div>
                        <div class="fw-semibold">{{ $payment->patient->contact_no }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-body-tertiary small">Queue Number</div>
                        <div class="fw-semibold text-primary" style="font-size: 1.25rem;">{{ $payment->queue->queue_no }}</div>
                    </div>
                </div>

                <div class="mb-4 pb-4" style="border-bottom: 2px dashed #dee2e6;">
                    <div class="mb-3">
                        <div class="text-body-tertiary small">Department</div>
                        <div class="fw-semibold">{{ $payment->queue->department->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-body-tertiary small">Service</div>
                        <div class="fw-semibold">{{ $payment->queue->service->service_name }}</div>
                    </div>
                    @if($payment->queue->symptoms)
                    <div class="mb-3">
                        <div class="text-body-tertiary small">Symptoms / Notes</div>
                        <div class="fw-semibold">{{ $payment->queue->symptoms }}</div>
                    </div>
                    @endif
                </div>

                <div class="mb-4 pb-4" style="border-bottom: 2px dashed #dee2e6;">
                    <div class="row align-items-end">
                        <div class="col-6">
                            <div class="text-body-tertiary small">Amount Paid</div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="fw-bold" style="font-size: 1.5rem;">PHP {{ number_format($payment->amount, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-body-tertiary small">Payment Method</div>
                            <div class="fw-semibold text-uppercase">
                                @if($payment->payment_method === 'cash')
                                    <i class="bi bi-cash-coin me-2"></i>Cash
                                @elseif($payment->payment_method === 'gcash')
                                    <i class="bi bi-wallet2 me-2"></i>GCash
                                @elseif($payment->payment_method === 'card')
                                    <i class="bi bi-credit-card me-2"></i>Card
                                @endif
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="text-body-tertiary small">Status</div>
                            <span class="status-chip completed">PAID</span>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-3 text-body-tertiary small">
                    <div>Thank you for visiting</div>
                    <div class="fw-semibold">{{ config('app.name', 'Hospital Management System') }}</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-dark flex-grow-1">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('payment.receipt.export', $payment->id) }}" target="_blank" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
