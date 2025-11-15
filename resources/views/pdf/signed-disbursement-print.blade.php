<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signed Disbursements Report</title>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Set page to landscape */
        @page {
            size: landscape;
            margin: 0.5in;
        }

        body {
            background: #ffffff;
            padding: 20px;
            width: 100%;
            height: 100%;
        }

        .container {
            width: 100%;
            max-width: none;
            background: #fff;
            margin: 0 auto;
            padding: 0 20px; /* Added equal padding left and right */
        }

        /* HEADER TABLE (3-column layout) */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
        }

        .logo img {
            width: 85px;
            height: 85px;
            object-fit: contain;
        }

        .name-section div {
            font-size: 13px;
            line-height: 1.3;
            margin-bottom: 2px;
        }

        .name-section .republic {
            font-weight: bold;
            font-size: 14px;
        }

        .name-section .province {
            font-size: 13px;
        }

        .name-section .municipality {
            font-size: 13px;
        }

        .name-section .office {
            font-size: 14px;
            font-weight: bold;
            color: #2c5aa0;
        }

        .name-section .system {
            font-size: 14px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 8px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        /* FILTERS BOX */
        .filters-info {
            background: #f8f9fa;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #2c5aa0;
            font-size: 12px;
        }

        .filters-info h3 {
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #2c5aa0;
        }

        .filters-info p {
            margin-bottom: 3px;
        }

        /* TABLE CONTAINER FOR BALANCED MARGINS */
        .table-container {
            width: 100%;
            margin: 20px 0;
        }

        /* DATA TABLE - Optimized for landscape */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #b8c3d6;
            table-layout: fixed;
            margin: 0 auto; /* Center the table */
        }

        .data-table th {
            background: #2c5aa0;
            color: white;
            padding: 10px 8px; /* Balanced padding */
            text-transform: uppercase;
            font-size: 10px;
            font-weight: 600;
            border: 1px solid #1e3d6d;
            word-wrap: break-word;
        }

        .data-table td {
            padding: 10px 8px; /* Balanced padding */
            border: 1px solid #e0e0e0;
            text-align: center;
            word-wrap: break-word;
        }

        .data-table td:first-child {
            text-align: center;
            width: 5%;
        }

        .data-table td:nth-child(2) {
            text-align: left;
            width: 18%;
            padding-left: 12px; /* Slightly more padding for text alignment */
        }

        .data-table td:nth-child(3) {
            width: 12%;
        }

        .data-table td:nth-child(4) {
            width: 12%;
        }

        .data-table td:nth-child(5) {
            width: 12%;
        }

        .data-table td:nth-child(6) {
            width: 12%;
        }

        .data-table td:nth-child(7) {
            width: 14%;
        }

        .data-table td:nth-child(8) {
            width: 15%;
        }

        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* NO DATA STYLING */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 30px 0; /* Equal margins */
            border: 1px solid #dee2e6;
        }

        .no-data h3 {
            color: #6c757d;
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .no-data p {
            color: #868e96;
            font-size: 14px;
        }

        .no-data-icon {
            font-size: 48px;
            color: #dee2e6;
            margin-bottom: 15px;
        }

        /* FOOTER */
        .footer {
            margin: 40px 0 0 0; /* Equal margins */
            text-align: center;
            font-size: 11px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        /* SIGNATURE STYLING */
        .signature-img {
            max-width: 70px;
            max-height: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
        }

        /* AMOUNT STYLING */
        .amount {
            font-weight: 600;
            color: #28a745;
        }

        /* DATE STYLING */
        .date {
            font-size: 10px;
            color: #666;
        }

        /* Landscape-specific optimizations */
        @media print {
            body {
                padding: 0;
            }
            .container {
                width: 100%;
                padding: 0 15px; /* Equal padding for print */
            }
            .data-table {
                font-size: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- HEADER TABLE (3-column layout) -->
        <table class="header-table">
            <tr>
                <!-- Left Logo -->
                <td class="logo" width="100">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture2.png'))) }}">
                </td>

                <!-- Center Text -->
                <td class="name-section">
                    <div class="republic">Republic of the Philippines</div>
                    <div class="province">PROVINCE OF MISAMIS ORIENTAL</div>
                    <div class="municipality">MUNICIPALITY OF TAGOLOAN</div>
                    <div class="office">LOCAL YOUTH DEVELOPMENT OFFICE</div>
                    <div class="system">SCHOLARSHIP MANAGEMENT SYSTEM</div>
                    <div class="report-title">SIGNED DISBURSEMENTS REPORT</div>

                    <!-- Filters inside the center column -->
                    @if(!empty($filters))
                    <div class="filters-info" style="margin-top: 10px;">
                        <h3>📊 Applied Filters</h3>
                        @if(isset($filters['search']))
                            <p><strong>Search:</strong> "{{ $filters['search'] }}"</p>
                        @endif
                        @if(isset($filters['barangay']))
                            <p><strong>Barangay:</strong> {{ $filters['barangay'] }}</p>
                        @endif
                        @if(isset($filters['academic_year']))
                            <p><strong>Academic Year:</strong> {{ $filters['academic_year'] }}</p>
                        @endif
                        @if(isset($filters['semester']))
                            <p><strong>Semester:</strong> {{ $filters['semester'] }}</p>
                        @endif
                    </div>
                    @endif
                </td>

                <!-- Right Logo -->
                <td class="logo" width="100">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture3.png'))) }}">
                </td>
            </tr>
        </table>

        <!-- CONTENT -->
        @if($signedDisbursements->count() > 0)
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Scholar Name</th>
                        <th>Barangay</th>
                        <th>Semester</th>
                        <th>Academic Year</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Signature</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($signedDisbursements as $disburse)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-left">{{ $disburse->full_name }}</td>
                        <td>{{ $disburse->applicant_brgy }}</td>
                        <td>{{ $disburse->disburse_semester }}</td>
                        <td>{{ $disburse->disburse_acad_year }}</td>
                        <td class="amount">PHP {{ number_format($disburse->disburse_amount, 2) }}</td>
                        <td class="date">{{ \Carbon\Carbon::parse($disburse->disburse_date)->format('F d, Y') }}</td>
                        <td>
                            @if($disburse->disburse_signature)
                                <img src="{{ $disburse->disburse_signature }}" class="signature-img" alt="Signature">
                            @else
                                <span style="color: #28a745; font-weight: bold; font-size: 10px;">✓ SIGNED</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @else
        <div class="no-data">
            <div class="no-data-icon">📄</div>
            <h3>No Signed Disbursements Found</h3>
            <p>There are no signed disbursement records matching your current filter criteria.</p>
        </div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            Report generated by LYDO Scholarship Management System • 
            {{ \Carbon\Carbon::now()->format('F d, Y \\a\\t h:i A') }}
        </div>

    </div>
</body>
</html>