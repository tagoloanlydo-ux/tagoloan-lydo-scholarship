<!DOCTYPE html>
<html>
<link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
<head>
    <meta charset="utf-8">
    <title>Renewal Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .header-content h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .header-content p {
            margin: 2px 0;
            font-size: 12px;
        }

        .filters {
            margin-bottom: 20px;
            font-size: 11px;
        }

        .filters strong {
            display: inline-block;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            border: 1px solid #333;
            padding: 6px;
            background-color: #7c3aed; /* violet-600 */
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }

        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            font-size: 14px;
            color: #666;
        }

        .status-approved {
            color: #28a745;
            font-weight: bold;
        }

        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }

        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }

        .status-other {
            color: #6c757d;
        }

        /* Signature section */
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .signature-table td {
            padding: 30px 10px;
            vertical-align: bottom;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto 5px auto;
        }

        /* Footer section */
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 10px;
            color: #000;
        }

        .footer p {
            margin: 3px 0;
        }

        .footer strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
    
        <div class="header-content">
            <h1>LYDO Scholarship Renewal Report</h1>
            <p>Tagoloan, Misamis Oriental</p>
        </div>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <strong>Applied Filters:</strong>
        {{ implode(' | ', $filters) }}
    </div>
    @endif

    @if($renewals->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Full Name</th>
                <th style="width: 15%;">School</th>
                <th style="width: 12%;">Course</th>
                <th style="width: 8%;">Year Level</th>
                <th style="width: 10%;">Barangay</th>
                <th style="width: 10%;">Academic Year</th>
                <th style="width: 10%;">Semester</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($renewals as $index => $renewal)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    {{ $renewal->applicant_fname }}
                    @if($renewal->applicant_mname)
                        {{ $renewal->applicant_mname }}
                    @endif
                    {{ $renewal->applicant_lname }}
                    @if($renewal->applicant_suffix)
                        {{ $renewal->applicant_suffix }}
                    @endif
                </td>
                <td>{{ $renewal->applicant_school_name }}</td>
                <td>{{ $renewal->applicant_course }}</td>
                <td class="text-center">{{ $renewal->applicant_year_level }}</td>
                <td>{{ $renewal->applicant_brgy }}</td>
                <td class="text-center">{{ $renewal->renewal_acad_year }}</td>
                <td class="text-center">{{ $renewal->renewal_semester }}</td>
                <td class="text-center">
                    @php
                        $statusClass = 'status-other';
                        $statusValue = $renewal->renewal_status;
                        if (is_string($statusValue)) {
                            $lowerStatus = strtolower($statusValue);
                            if (strpos($lowerStatus, 'approved') !== false) {
                                $statusClass = 'status-approved';
                            } elseif (strpos($lowerStatus, 'rejected') !== false) {
                                $statusClass = 'status-rejected';
                            } elseif (strpos($lowerStatus, 'pending') !== false) {
                                $statusClass = 'status-pending';
                            }
                        }
                    @endphp
                    <span class="{{ $statusClass }}">
                        {{ $statusValue ?: 'No status' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <p>Verified By</p>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <p>Approved By</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <strong>Lydo Scholarship System</strong><br>
        Generated on: {{ date('F d, Y') }} at {{ date('h:i A') }}<br>
        Page 1
    </div>

    @else
    <div class="no-data">
        <p>No renewal records found matching the specified criteria.</p>
    </div>
    @endif
</body>
</html>
