<!DOCTYPE html>
<html>
<link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
<head>
    <meta charset="utf-8">
    <title>Applicants Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-top: 20px;
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
            background-color: #f5f5f5;
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

        .text-right {
            text-align: right;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            font-size: 14px;
            color: #666;
        }

        .signature-section {
            margin-top: 50px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            text-align: center;
        }

        .signature-table td {
            padding: 30px 10px;
            vertical-align: bottom;
            border: none;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto 5px auto;
        }

        /* ✅ Footer for every page */
        @page {
            margin: 20mm;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
        }

        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-content">
            <h1>LYDO Scholarship Applicants Report</h1>
            <p>Tagoloan, Misamis Oriental</p>
        </div>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <strong>Applied Filters:</strong>
        {{ implode(' | ', $filters) }}
    </div>
    @endif

    @if($applicants->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Full Name</th>
                <th style="width: 15%;">Email</th>
                <th style="width: 12%;">Contact Number</th>
                <th style="width: 10%;">Barangay</th>
                <th style="width: 10%;">Academic Year</th>
                <th style="width: 28%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applicants as $index => $applicant)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    {{ $applicant->applicant_fname }}
                    @if($applicant->applicant_mname)
                        {{ $applicant->applicant_mname }}
                    @endif
                    {{ $applicant->applicant_lname }}
                    @if($applicant->applicant_suffix)
                        {{ $applicant->applicant_suffix }}
                    @endif
                </td>
                <td>{{ $applicant->applicant_email }}</td>
                <td>{{ $applicant->applicant_contact_number }}</td>
                <td>{{ $applicant->applicant_brgy }}</td>
                <td class="text-center">{{ $applicant->applicant_acad_year }}</td>
                <td>
                    @php
                        $remarksClass = 'remarks-other';
                        $remarksValue = $applicant->remarks;
                        if (is_string($remarksValue)) {
                            $lowerRemarks = strtolower($remarksValue);
                            if (strpos($lowerRemarks, 'approved') !== false || strpos($lowerRemarks, 'passed') !== false) {
                                $remarksClass = 'remarks-approved';
                            } elseif (strpos($lowerRemarks, 'rejected') !== false || strpos($lowerRemarks, 'failed') !== false) {
                                $remarksClass = 'remarks-rejected';
                            } elseif (strpos($lowerRemarks, 'pending') !== false || strpos($lowerRemarks, 'review') !== false) {
                                $remarksClass = 'remarks-pending';
                            }
                        }
                    @endphp
                    <span class="{{ $remarksClass }}">
                        {{ $remarksValue ?: 'No remarks' }}
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

    @else
    <div class="no-data">
        <p>No applicant records found matching the specified criteria.</p>
    </div>
    @endif

    <!-- ✅ Footer on EVERY PAGE -->
    <div class="footer">
        <p style="margin: 5px 0; font-weight: bold;">Lydo Scholarship System</p>
        <p style="margin: 5px 0;">Generated on: {{ date('F d, Y \a\t h:i A') }}</p>
        <p style="margin: 5px 0;">Page <span class="pagenum"></span></p>
    </div>

</body>
</html>
