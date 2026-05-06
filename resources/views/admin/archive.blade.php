@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-5">
        <div>
            <span class="eyebrow mb-3">{{ $eyebrow }}</span>
            <h1 class="section-title mb-3">{{ $title }}</h1>
            <p class="lede mb-0">{{ $description }}</p>
        </div>
        <div class="d-flex align-items-start">
            <a href="{{ $backRoute }}" class="btn btn-outline-dark px-4">Back to active records</a>
        </div>
    </div>

    <div class="app-card p-4 p-lg-5">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th class="text-body-tertiary text-uppercase small">{{ $column }}</th>
                        @endforeach
                        <th class="text-body-tertiary text-uppercase small">Archived at</th>
                        <th class="text-body-tertiary text-uppercase small">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            @switch($type)
                                @case('patients')
                                    <td class="fw-semibold">{{ $item->first_name }} {{ $item->last_name }}</td>
                                    <td>{{ $item->birth_date?->format('M d, Y') ?? '-' }}</td>
                                    <td>{{ $item->contact_no ?: '-' }}</td>
                                    @break

                                @case('users')
                                    <td class="fw-semibold">{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->role->name ?? 'Not assigned' }}</td>
                                    @break

                                @case('departments')
                                    <td class="fw-semibold">{{ $item->name }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->location ?: '-' }}</td>
                                    @break

                                @case('services')
                                    <td class="fw-semibold">{{ $item->service_name }}</td>
                                    <td>{{ $item->department->name ?? 'Not assigned' }}</td>
                                    <td>PHP {{ number_format((float) $item->cost, 2) }}</td>
                                    @break

                                @case('counters')
                                    <td class="fw-semibold">{{ $item->name }}</td>
                                    <td>{{ $item->department->name ?? 'Not assigned' }}</td>
                                    <td>{{ $item->status }}</td>
                                    @break
                            @endswitch
                            <td>{{ $item->deleted_at?->format('M d, Y h:i A') }}</td>
                            <td>
                                <form action="{{ route($restoreRouteName, $item->getKey()) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Restore</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 2 }}" class="empty-state">No archived records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
