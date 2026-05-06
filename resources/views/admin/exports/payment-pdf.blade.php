<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        h1 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Payment Report</h1>
    <table>
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
            @foreach($payments as $p)
            <tr>
                <td>#{{ $p->getKey() }}</td>
                <td>{{ $p->patient?->name ?? 'N/A' }}</td>
                <td>{{ $p->queue?->queue_no ?? 'N/A' }}</td>
                <td>₱{{ number_format($p->amount, 2) }}</td>
                <td>{{ ucfirst($p->payment_method) }}</td>
                <td>{{ $p->status }}</td>
                <td>{{ $p->paid_at?->format('Y-m-d H:i') ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
