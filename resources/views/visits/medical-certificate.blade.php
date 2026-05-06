<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Medical Certificate</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times', serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }
        .certificate {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .certificate-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .certificate-number {
            font-size: 12px;
            color: #666;
        }
        .content {
            margin: 30px 0;
        }
        .patient-info {
            margin-bottom: 20px;
        }
        .patient-info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            width: 120px;
        }
        .value {
            flex: 1;
        }
        .body-text {
            margin: 30px 0;
            text-align: justify;
        }
        .diagnosis-box {
            border: 1px solid #333;
            padding: 15px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        .diagnosis-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .status-box {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #333;
        }
        .status-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #333;
            margin-right: 15px;
        }
        .checkbox-checked {
            background: #333;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }
        .date-block {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="hospital-name">Hospital Queue Management System</div>
            <div class="certificate-title">Medical Certificate</div>
            <div class="certificate-number">Certificate No: {{ $visit->id }}</div>
        </div>

        <div class="content">
            <div class="patient-info">
                <div class="patient-info-row">
                    <span class="label">Patient Name:</span>
                    <span class="value">{{ $patient->full_name }}</span>
                </div>
                <div class="patient-info-row">
                    <span class="label">Age:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($patient->birth_date)->age }} years old</span>
                </div>
                <div class="patient-info-row">
                    <span class="label">Gender:</span>
                    <span class="value">{{ $patient->gender }}</span>
                </div>
                <div class="patient-info-row">
                    <span class="label">Address:</span>
                    <span class="value">{{ $patient->address ?: 'Not provided' }}</span>
                </div>
                <div class="patient-info-row">
                    <span class="label">Date of Visit:</span>
                    <span class="value">{{ $visit->visit_date->format('F d, Y') }}</span>
                </div>
            </div>

            <div class="body-text">
                <p>This is to certify that the above-named patient was examined and treated at our facility on the date mentioned above.</p>
            </div>

            <div class="diagnosis-box">
                <div class="diagnosis-title">Clinical Notes:</div>
                <div>{{ $visit->notes }}</div>
            </div>

            <div class="diagnosis-box">
                <div class="diagnosis-title">Diagnosis:</div>
                <div>{{ $visit->diagnosis }}</div>
            </div>

            <div class="status-box">
                <div class="status-item">
                    <div class="checkbox @if($visit->status === 'completed') checkbox-checked @endif"></div>
                    <span>Completed - Patient discharged</span>
                </div>
                <div class="status-item">
                    <div class="checkbox @if($visit->status === 'ongoing') checkbox-checked @endif"></div>
                    <span>Ongoing - Further treatment required</span>
                </div>
                <div class="status-item">
                    <div class="checkbox @if($visit->status === 'follow-up') checkbox-checked @endif"></div>
                    <span>Follow-up - Return for check-up on: ___________</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="signature-block">
                <div class="signature-line">
                    <div>Doctor's Signature</div>
                </div>
            </div>
            <div class="signature-block">
                <div class="signature-line">
                    <div>Authorized Signature</div>
                </div>
            </div>
        </div>

        <div class="date-block">
            <div>Date Issued: {{ now()->format('F d, Y') }}</div>
        </div>
    </div>
</body>
</html>
