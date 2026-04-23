@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">Staff Dashboard</span>
            <h1 class="section-title mb-3">{{ $department->name ?? 'All Departments' }} queue management</h1>
            <p class="lede mb-0">
                Review all patients with status waiting, call the next patient to the counter,
                and complete the service flow before handing off to consultation when needed.
            </p>
        </div>

        <div class="glass-panel p-4" style="max-width: 420px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="fw-semibold">Counter status</div>
                    <div class="text-body-tertiary small">{{ $counter?->name ?? 'No counter assigned' }}</div>
                </div>
                @if($counter)
                    <span class="status-chip {{ $counter->status }}">{{ strtoupper($counter->status) }}</span>
                @else
                    <span class="status-chip cancelled">Unavailable</span>
                @endif
            </div>

            @if($counter)
                <form action="{{ route('queue.toggle-counter') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-dark w-100">Toggle counter availability</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="metric-card h-100">
                <div class="info-label mb-2">Waiting</div>
                <div class="metric-value">{{ $stats['waiting'] }}</div>
                <div class="text-body-tertiary small mt-2">Patients ready to be called in queue order.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card h-100">
                <div class="info-label mb-2">Called</div>
                <div class="metric-value">{{ $stats['called'] }}</div>
                <div class="text-body-tertiary small mt-2">Patients already shown on the public display board.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card h-100">
                <div class="info-label mb-2">Completed today</div>
                <div class="metric-value">{{ $stats['served'] }}</div>
                <div class="text-body-tertiary small mt-2">Finished queue entries across all departments today.</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="app-card p-4 p-lg-5 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h1 mb-1">Current queue list</h2>
                        <div class="text-body-tertiary">Patients are sorted by queue order for today.</div>
                    </div>
                    <span class="subtle-chip">{{ $department->name ?? 'All departments' }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-body-tertiary text-uppercase small">Queue no.</th>
                                <th class="text-body-tertiary text-uppercase small">Patient</th>
                                <th class="text-body-tertiary text-uppercase small">Service</th>
                                <th class="text-body-tertiary text-uppercase small">Symptoms / Notes</th>
                                <th class="text-body-tertiary text-uppercase small">Amount due</th>
                                <th class="text-body-tertiary text-uppercase small">Status</th>
                                <th class="text-end text-body-tertiary text-uppercase small">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeQueues as $q)
                                <tr>
                                    <td class="fw-bold">{{ $q->queue_no }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $q->patient->full_name }}</div>
                                        <div class="text-body-tertiary small">{{ $q->patient->contact_no }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $q->service->service_name }}</div>
                                        <div class="text-body-tertiary small">{{ $q->department->name }}</div>
                                    </td>
                                    <td class="text-body-tertiary small">{{ $q->symptoms ? \Illuminate\Support\Str::limit($q->symptoms, 30) : '-' }}</td>
                                    <td class="fw-semibold">PHP {{ number_format((float) $q->service->cost, 2) }}</td>
                                    <td><span class="status-chip {{ $q->status }}">{{ $q->status }}</span></td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end flex-wrap gap-2">
                                            @if($q->status === 'waiting')
                                                <form action="{{ route('queue.call', $q->getKey()) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm px-3">Call Next</button>
                                                </form>
                                            @endif

                                            @if($q->status === 'called')
                                                <a href="{{ route('payment.form', $q->getKey()) }}" class="btn btn-primary btn-sm px-3">Process Payment</a>
                                            @endif

                                            <form action="{{ route('queue.cancel', $q->getKey()) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-3">Cancel</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state">No active queue entries found for this department today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paid Queues Section -->
            <div class="app-card p-4 p-lg-5 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h1 mb-1">Paid patients - Assign to Doctor</h2>
                        <div class="text-body-tertiary">Patients who have completed payment and are ready for doctor consultation.</div>
                    </div>
                    <span class="subtle-chip">Ready for assignment</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-body-tertiary text-uppercase small">Queue no.</th>
                                <th class="text-body-tertiary text-uppercase small">Patient</th>
                                <th class="text-body-tertiary text-uppercase small">Service</th>
                                <th class="text-body-tertiary text-uppercase small">Symptoms / Notes</th>
                                <th class="text-body-tertiary text-uppercase small">Paid at</th>
                                <th class="text-end text-body-tertiary text-uppercase small">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paidQueues as $q)
                                <tr>
                                    <td class="fw-bold">{{ $q->queue_no }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $q->patient->full_name }}</div>
                                        <div class="text-body-tertiary small">{{ $q->patient->contact_no }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $q->service->service_name }}</div>
                                        <div class="text-body-tertiary small">{{ $q->department->name }}</div>
                                    </td>
                                    <td class="text-body-tertiary small">{{ $q->symptoms ? \Illuminate\Support\Str::limit($q->symptoms, 30) : '-' }}</td>
                                    <td class="text-body-tertiary small">{{ $q->updated_at->format('h:i A') }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('queue.assign-doctor', $q->getKey()) }}" method="POST">
                                            @csrf
                                            <select name="doctor_id" class="form-select form-select-sm d-inline-block w-auto me-2" required>
                                                <option value="">Select Doctor</option>
                                                @php $doctors = $q->department->users->filter(fn($u) => $u->role && $u->role->name === 'Doctor') @endphp
                                                @foreach($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}">Dr. {{ $doctor->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-success btn-sm px-3">Assign</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">No paid patients waiting for doctor assignment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- NEW: All Patients Queue Section -->
            <div class="app-card p-4 p-lg-5 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h1 mb-1">Global queue overview</h2>
                        <div class="text-body-tertiary">Monitor patient volume across all departments in real-time.</div>
                    </div>
                    <span class="subtle-chip">All departments</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-body-tertiary text-uppercase small">Queue no.</th>
                                <th class="text-body-tertiary text-uppercase small">Patient</th>
                                <th class="text-body-tertiary text-uppercase small">Department</th>
                                <th class="text-body-tertiary text-uppercase small">Service</th>
                                <th class="text-body-tertiary text-uppercase small">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allActiveQueues as $q)
                                <tr>
                                    <td class="fw-bold">{{ $q->queue_no }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $q->patient->full_name }}</div>
                                        <div class="text-body-tertiary small">{{ $q->patient->contact_no }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $q->department->name }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $q->service->service_name }}</div>
                                    </td>
                                    <td><span class="status-chip {{ $q->status }}">{{ $q->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty-state">No active queue entries found across any department today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="glass-panel p-4 h-100">
                        <div class="fw-semibold mb-2">Payment Processing</div>
                        <div class="text-body-tertiary small mb-3">
                            When you click "Process Payment", the patient's amount due will be displayed along with payment method options (Cash, GCash, Card). After confirming payment, a receipt is generated automatically.
                        </div>
                        <div class="floating-note small">
                            <strong>Flow:</strong> Call Next → Process Payment → Receipt Generated
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="glass-panel p-4 h-100">
                        <div class="fw-semibold mb-3">Staff Workflow</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="timeline-note mb-0">
                                    <div class="fw-semibold">1. Call patient</div>
                                    <div class="text-body-tertiary small">Shown on public display.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="timeline-note mb-0">
                                    <div class="fw-semibold">2. Patient arrives</div>
                                    <div class="text-body-tertiary small">Provide service at counter.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="timeline-note mb-0">
                                    <div class="fw-semibold">3. Process Payment</div>
                                    <div class="text-body-tertiary small">Collect fee & receipt.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="timeline-note mb-0">
                                    <div class="fw-semibold">4. Consultation</div>
                                    <div class="text-body-tertiary small">Direct to doctor if needed.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    setInterval(function () {
        window.location.reload();
    }, 5000);
</script>
@endsection
