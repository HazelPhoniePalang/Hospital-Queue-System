<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visit Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        h1 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Visit Report</h1>
    <table>
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
            @foreach($visits as $v)
            <tr>
                <td>#{{ $v->getKey() }}</td>
                <td>{{ $v->queue?->patient?->name ?? 'N/A' }}</td>
                <td>{{ $v->queue?->queue_no ?? 'N/A' }}</td>
                <td>{{ $v->notes }}</td>
                <td>{{ $v->diagnosis ?? '-' }}</td>
                <td>{{ $v->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
