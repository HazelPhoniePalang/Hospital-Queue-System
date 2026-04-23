@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <span class="eyebrow mb-3">Payment Processing</span>
                    <h1 class="section-title mb-3">Confirm payment and complete transaction</h1>
                </div>
            </div>

            <div class="app-card p-4 p-lg-5 mb-4">
                <h3 class="h2 mb-4">Patient & Service Details</h3>
                
                <div class="info-list mb-4">
                    <div class="info-item">
                        <span class="info-label">Queue Number</span>
                        <span class="info-value fw-bold text-primary">{{ $queue->queue_no }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Patient Name</span>
                        <span class="info-value">{{ $queue->patient->first_name }} {{ $queue->patient->last_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact Number</span>
                        <span class="info-value">{{ $queue->patient->contact_no }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Department</span>
                        <span class="info-value">{{ $queue->department->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Service Availed</span>
                        <span class="info-value">{{ $queue->service->service_name }}</span>
                    </div>
                    @if($queue->symptoms)
                    <div class="info-item">
                        <span class="info-label">Symptoms / Notes</span>
                        <span class="info-value">{{ $queue->symptoms }}</span>
                    </div>
                    @endif
                </div>

                <hr class="mb-4">

                <form action="{{ route('payment.process', $queue->id) }}" method="POST">
                    @csrf

                    <h3 class="h5 mb-4">Payment Details</h3>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Amount Due</label>
                        <div class="input-group input-group-lg mb-3">
                            <span class="input-group-text">PHP</span>
                            <input type="text" class="form-control" value="{{ number_format($amount, 2) }}">
                        </div>
                        <input type="hidden" name="amount" value="{{ $amount }}" required>
                    </div>

                    <div class="mb-4">
                        <label for="payment_method" class="form-label fw-semibold">Payment Method</label>
                        <select id="payment_method" name="payment_method" required class="form-select form-select-lg">
                            <option value="">-- Select payment method --</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="card">Card</option>
                        </select>
                        @error('payment_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-dark flex-grow-1">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                            Confirm Payment
                            <i class="bi bi-check-circle ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="glass-panel p-4">
                <div class="fw-semibold mb-2">Payment Instructions</div>
                <ul class="small text-body-tertiary mb-0">
                    <li>Verify patient information above</li>
                    <li>Confirm the amount due with the patient</li>
                    <li>Select the payment method used</li>
                    <li>Click "Confirm Payment" to complete the transaction</li>
                    <li>A receipt will be generated automatically</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
