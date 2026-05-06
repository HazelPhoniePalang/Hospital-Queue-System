<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $payment->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .receipt {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #999;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .receipt-title {
            font-size: 12px;
            margin-bottom: 3px;
        }
        .receipt-number {
            font-size: 10px;
            color: #666;
        }
        .section {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #999;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .label {
            color: #666;
        }
        .value {
            font-weight: bold;
        }
        .total {
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #999;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="company-name">HQMS</div>
            <div class="receipt-title">Hospital Queue Management System</div>
            <div class="receipt-number">Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="receipt-number">{{ $payment->paid_at->format('M d, Y H:i A') }}</div>
        </div>

        <div class="section">
            <div class="row">
                <span class="label">Patient:</span>
                <span class="value">{{ $payment->patient->first_name }} {{ $payment->patient->last_name }}</span>
            </div>
            <div class="row">
                <span class="label">Contact:</span>
                <span class="value">{{ $payment->patient->contact_no }}</span>
            </div>
            <div class="row">
                <span class="label">Queue #:</span>
                <span class="value">{{ $payment->queue->queue_no }}</span>
            </div>
        </div>

        <div class="section">
            <div class="row">
                <span class="label">Department:</span>
                <span class="value">{{ $payment->queue->department->name }}</span>
            </div>
            <div class="row">
                <span class="label">Service:</span>
                <span class="value">{{ $payment->queue->service->service_name }}</span>
            </div>
            @if($payment->queue->symptoms)
            <div class="row">
                <span class="label">Symptoms:</span>
                <span class="value" style="font-size: 10px;">{{ $payment->queue->symptoms }}</span>
            </div>
            @endif
        </div>

        <div class="section">
            <div class="row total">
                <span class="label">Amount:</span>
                <span class="value">PHP {{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <div class="section">
            <div class="row">
                <span class="label">Method:</span>
                <span class="value">{{ strtoupper($payment->payment_method) }}</span>
            </div>
            <div class="row">
                <span class="label">Status:</span>
                <span class="value">PAID</span>
            </div>
        </div>

        <div class="footer">
            <div>Thank you for visiting</div>
            <div>Please come again</div>
        </div>
    </div>
</body>
</html>
