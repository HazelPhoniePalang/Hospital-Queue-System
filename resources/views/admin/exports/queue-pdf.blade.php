<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Queue Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        h1 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Queue Report</h1>
    <table>
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
            @foreach($queues as $q)
            <tr>
                <td>{{ $q->queue_no }}</td>
                <td>{{ $q->patient?->name ?? 'N/A' }}</td>
                <td>{{ $q->department?->name ?? 'N/A' }}</td>
                <td>{{ $q->service?->name ?? 'N/A' }}</td>
                <td>{{ $q->status }}</td>
                <td>{{ $q->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $q->called_at?->format('Y-m-d H:i') ?? '-' }}</td>
                <td>{{ $q->completed_at?->format('Y-m-d H:i') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
