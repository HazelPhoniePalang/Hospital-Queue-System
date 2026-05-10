@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">Reports</span>
            <h1 class="section-title mb-3">Generate and export reports</h1>
            <p class="lede mb-0">Filter by date range, department, and status.</p>
        </div>
    </div>

    <div class="app-card p-4 mb-4">
        <form action="{{ route('admin.reports') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="type" class="form-label fw-semibold">Report Type</label>
                <select name="type" id="type" class="form-select">
                    <option value="queue" {{ $type === 'queue' ? 'selected' : '' }}>Queue</option>
                    <option value="payment" {{ $type === 'payment' ? 'selected' : '' }}>Payment</option>
                    <option value="visit" {{ $type === 'visit' ? 'selected' : '' }}>Visit</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="department" class="form-label fw-semibold">Department</label>
                <select name="department" id="department" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->getKey() }}" {{ $departmentId == $dept->getKey() ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="called" {{ $status === 'called' ? 'selected' : '' }}>Called</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label fw-semibold">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-select" value="{{ $startDate }}">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label fw-semibold">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-select" value="{{ $endDate }}">
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                </div>
            </div>
        </form>

        <div class="d-flex gap-2 mt-4 pt-3 border-top">
            
            <form action="{{ route('admin.reports.export') }}" method="GET" target="_blank" class="d-flex gap-2">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="department" value="{{ $departmentId }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <input type="hidden" name="format" value="pdf">
                <button type="submit" class="btn btn-outline-dark">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                </button>
            </form>
        </div>
    </div>

    <div class="app-card p-4">
        <h2 class="h1 mb-4">Results</h2>
        
        @if($type === 'queue')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Queue No</th>
                            <th>Patient</th>
                            <th>Department</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Called</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queues as $q)
                            <tr>
                                <td class="fw-semibold">{{ $q->queue_no }}</td>
                                <td>{{ $q->patient?->first_name . ' ' . $q->patient?->last_name ?? 'N/A' }}</td>
                                <td>{{ $q->department?->name ?? 'N/A' }}</td>
                                <td>{{ $q->service?->service_name ?? 'N/A' }}</td>
                                <td>
                                    @switch($q->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pending</span>
                                            @break
                                        @case('called')
                                            <span class="badge bg-info">Called</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Completed</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $q->status }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $q->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $q->called_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $q->completed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-body-tertiary">No queue records found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($type === 'payment')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Queue</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queues as $p)
                            <tr>
                                <td class="fw-semibold">#{{ $p->getKey() }}</td>
                                <td>{{ $p->patient?->first_name . ' ' . $p->patient?->last_name ?? 'N/A' }}</td>
                                <td>{{ $p->queue?->queue_no ?? 'N/A' }}</td>
                                <td>₱{{ number_format($p->amount, 2) }}</td>
                                <td>{{ ucfirst($p->payment_method) }}</td>
                                <td>
                                    @if($p->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning">Unpaid</span>
                                    @endif
                                </td>
                                <td>{{ $p->paid_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-body-tertiary">No payment records found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif($type === 'visit')
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Queue</th>
                            <th>Doctor Notes</th>
                            <th>Diagnosis</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queues as $v)
                            <tr>
                                <td class="fw-semibold">#{{ $v->getKey() }}</td>
                                <td>{{ $v->queue?->patient?->first_name . ' ' . $v->queue?->patient?->last_name ?? 'N/A' }}</td>
                                <td>{{ $v->queue?->queue_no ?? 'N/A' }}</td>
                                <td>{{ Str::limit($v->doctor_notes, 50) }}</td>
                                <td>{{ $v->diagnosis ?? '-' }}</td>
                                <td>{{ $v->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-body-tertiary">No visit records found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
