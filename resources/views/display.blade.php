@extends('layouts.app')

@section('content')
<div class="display-board py-5">
    <div class="container-fluid px-4 px-lg-5 text-white">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-4 mb-5">
            <div>
                <span class="eyebrow text-white-50 mb-3">Public Queue Display</span>
                <h1 class="hero-title mb-2" style="color: #f8f4eb;">Now serving</h1>
                <p class="mb-0" style="color: rgba(248, 244, 235, 0.7);">Patients should proceed to the indicated counter when their queue number appears.</p>
            </div>
            <div class="text-lg-end">
                <div class="display-font" id="current-time" style="font-size: clamp(2rem, 6vw, 3.8rem); color: #f8f4eb;">{{ now()->format('h:i:s A') }}</div>
                <div style="color: rgba(248, 244, 235, 0.65);">{{ now()->format('l, F d, Y') }}</div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="dark-panel p-4 p-lg-5 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="h1 mb-1" style="color: #f8f4eb;">Active counters</h2>
                            <div style="color: rgba(248, 244, 235, 0.68);">Live calls from staff counters</div>
                        </div>
                        <span class="subtle-chip " style="background: rgba(255,255,255,0.08); color: #f8f4eb;">Please proceed</span>
                    </div>

                    <div class="queue-board-grid">
                        @forelse($counters as $counter)
                            @if($counter->currentQueue)
                                <div class="counter-card">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="counter-ticket">
                                            <div class="small text-uppercase fw-bold mb-2">Queue</div>
                                            <strong>{{ $counter->currentQueue->queue_no }}</strong>
                                        </div>
                                        <div>
                                            <div class="fw-semibold fs-3">{{ $counter->name }}</div>
                                            <div style="color: rgba(248, 244, 235, 0.68);">{{ $counter->department->name }}</div>
                                        </div>
                                    </div>
                                    <span class="status-chip waiting" style="background: rgba(255,255,255,0.08); color: #f8f4eb;">Now serving</span>
                                </div>
                            @endif
                        @empty
                            <div class="empty-state" style="color: rgba(248, 244, 235, 0.65);">No active calls are on screen right now.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="dark-panel p-4 p-lg-5 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="h1 mb-1" style="color: #f8f4eb;">Recently called</h2>
                            <div style="color: rgba(248, 244, 235, 0.68);">Latest queue numbers shown by the system</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0 text-white">
                            <thead>
                                <tr style="color: rgba(248, 244, 235, 0.58);">
                                    <th class="text-uppercase small" style = "color: white">Queue no.</th>
                                    <th class="text-uppercase small" style = "color: white">Department</th>
                                    <th class="text-uppercase small" style = "color: white">Service</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($calledQueues as $q)
                                    <tr>
                                        <td class="fw-semibold" style="color: #f8f4eb;">{{ $q->queue_no }}</td>
                                        <td class="fw-semibold" style="color: #f8f4eb;">{{ $q->department->name }}</td>
                                        <td class="fw-semibold" style="color: #f8f4eb;">{{ $q->service->service_name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="empty-state" style="color: rgba(248, 244, 235, 0.65);">No recent calls to display yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    setInterval(() => {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString();
    }, 1000);

    setInterval(() => {
        window.location.reload();
    }, 5000);
</script>
@endsection
