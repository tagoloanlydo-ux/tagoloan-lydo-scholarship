<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renewal History - {{ $scholar->applicant_lname }}, {{ $scholar->applicant_fname }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4c1d95;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4c1d95;
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            color: #7e22ce;
            margin: 5px 0 0 0;
            font-size: 18px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h3 {
            background-color: #f8fafc;
            padding: 10px;
            border-left: 4px solid #4c1d95;
            margin: 0 0 15px 0;
            color: #4c1d95;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
            display: block;
            margin-bottom: 2px;
        }
        .info-value {
            color: #1f2937;
        }
        .document-section {
            margin-top: 20px;
        }
        .document-item {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: #f9fafb;
        }
        .document-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-submitted {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-approved {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .timestamp {
            text-align: right;
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="timestamp">
        Printed on: {{ $print_date }}
    </div>

    <div class="header">
        <h1>LYDO SCHOLARSHIP PROGRAM</h1>
        <h2>Renewal History Document</h2>
    </div>

    <!-- Scholar Information -->
    <div class="info-section">
        <h3>Scholar Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Full Name:</span>
                <span class="info-value">
                    {{ $scholar->applicant_lname }}{{ $scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : '' }}, 
                    {{ $scholar->applicant_fname }} 
                    {{ $scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '' }}
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Scholar ID:</span>
                <span class="info-value">{{ $scholar->scholar_id }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Barangay:</span>
                <span class="info-value">{{ $scholar->applicant_brgy }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">School:</span>
                <span class="info-value">{{ $scholar->applicant_school_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Course:</span>
                <span class="info-value">{{ $scholar->applicant_course }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Year Level:</span>
                <span class="info-value">{{ $scholar->applicant_year_level }}</span>
            </div>
        </div>
    </div>

    <!-- Renewal Information -->
    <div class="info-section">
        <h3>Renewal Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Academic Year:</span>
                <span class="info-value">{{ $renewal->renewal_acad_year }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Semester:</span>
                <span class="info-value">{{ $renewal->renewal_semester }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date Submitted:</span>
                <span class="info-value">
                    @if($renewal->date_submitted)
                        {{ \Carbon\Carbon::parse($renewal->date_submitted)->format('F d, Y') }}
                    @else
                        Not submitted
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Renewal Status:</span>
                <span class="info-value">
                    <span class="document-status status-{{ strtolower($renewal->renewal_status) }}">
                        {{ ucfirst($renewal->renewal_status) }}
                    </span>
                </span>
            </div>
        </div>
    </div>

    <!-- Document Status -->
    <div class="info-section">
        <h3>Document Status</h3>
        <div class="document-section">
            <div class="info-grid">
                <div class="document-item">
                    <span class="info-label">Certificate of Registration:</span>
                    <span class="info-value">
                        @if($renewal->renewal_cert_of_reg)
                            <span class="document-status status-submitted">Submitted</span>
                        @else
                            <span class="document-status status-pending">Pending</span>
                        @endif
                    </span>
                </div>
                <div class="document-item">
                    <span class="info-label">Grade Slip:</span>
                    <span class="info-value">
                        @if($renewal->renewal_grade_slip)
                            <span class="document-status status-submitted">Submitted</span>
                        @else
                            <span class="document-status status-pending">Pending</span>
                        @endif
                    </span>
                </div>
                <div class="document-item">
                    <span class="info-label">Barangay Indigency:</span>
                    <span class="info-value">
                        @if($renewal->renewal_brgy_indigency)
                            <span class="document-status status-submitted">Submitted</span>
                        @else
                            <span class="document-status status-pending">Pending</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Notes -->
    @if($renewal->renewal_remarks)
    <div class="info-section">
        <h3>Remarks</h3>
        <div class="info-item">
            <span class="info-value">{{ $renewal->renewal_remarks }}</span>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This is an official document generated by the LYDO Scholarship System</p>
        <p>© {{ date('Y') }} LYDO Scholarship Program. All rights reserved.</p>
    </div>
</body>
</html>