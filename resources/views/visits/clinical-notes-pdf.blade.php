<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Clinical Notes & Diagnosis - {{ $visit->queue->patient->full_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 30px;
            background: #f8f9fa;
        }
        .page {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 1px solid #ddd;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px double #333;
        }
        .hospital-name {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .subtitle {
            font-size: 14px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .patient-info {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 120px;
        }
        .value {
            color: #333;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #3498db;
        }
        .notes-box, .diagnosis-box {
            background: #fff;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            line-height: 1.8;
        }
        .notes-box {
            background: #fdfdfd;
        }
        .diagnosis-box {
            background: #fef9e7;
            border-color: #f1c40f;
        }
        .doctor-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        .doctor-name {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .print-note {
            font-size: 10px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="hospital-name">Hospital Queue Management System</div>
            <div class="subtitle">Clinical Summary Report</div>
            <div style="margin-top: 5px; font-size: 10px; color: #999;">
                Generated: {{ now()->format('F d, Y - h:i A') }}
            </div>
        </div>

        <div class="patient-info">
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Patient Name:</span>
                    <span class="value">{{ $visit->queue->patient->full_name }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Queue Number:</span>
                    <span class="value">#{{ $visit->queue->queue_no }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Department:</span>
                    <span class="value">{{ $visit->queue->department->name }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Service:</span>
                    <span class="value">{{ $visit->queue->service->service_name }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Visit Date:</span>
                    <span class="value">{{ $visit->visit_date->format('F d, Y - h:i A') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Visit Status:</span>
                    <span class="value">{{ ucfirst($visit->status) }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Clinical Notes</div>
            <div class="notes-box">
                {{ $visit->notes }}
            </div>
        </div>

        <div class="section">
            <div class="section-title">Diagnosis</div>
            <div class="diagnosis-box">
                {{ $visit->diagnosis }}
            </div>
        </div>

        @if($visit->queue->symptoms)
        <div class="section">
            <div class="section-title">Patient-Reported Symptoms</div>
            <div class="notes-box">
                {{ $visit->queue->symptoms }}
            </div>
        </div>
        @endif

        <div class="doctor-info">
            <div>
                <div class="label">Attending Physician:</div>
                <div class="doctor-name">Dr. {{ $visit->doctor->name ?? 'N/A' }}</div>
            </div>
            <div style="text-align: right;">
                <div class="label">Visit ID:</div>
                <div>#{{ $visit->id }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="print-note">This is a computer-generated document. No signature required.</div>
            <div>Hospital Queue Management System &copy; {{ date('Y') }}</div>
        </div>
    </div>
</body>
</html>
