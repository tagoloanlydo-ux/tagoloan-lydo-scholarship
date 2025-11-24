<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LYDO Reviewed Applicants Report</title>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #fff;
            padding: 0;
            margin: 0;
            font-size: 9px;
        }

        .page {
            position: relative;
            min-height: 100vh;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .container {
            width: 100%;
            background: #fff;
            margin: 0 auto;
            padding: 0;
        }

        /* HEADER - FIXED POSITION FOR EACH PAGE */
        .header-portrait {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 8px 10mm 5px 10mm;
            background: white;
            z-index: 1000;
            height: 20mm;
            border-bottom: none;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            margin-top: 10px;
            padding: 0 8px;
        }

        .header-table .logo {
            width: 20%;
            text-align: center;
        }

        .header-table .logo img {
            width: 60px;
            height: auto;
            object-fit: contain;
        }

        .header-table .name-section {
            width: 60%;
            text-align: center;
        }

        .name-section div {
            font-size: 9px;
            line-height: 1.1;
            margin-bottom: 1px;
        }

        .name-section strong {
            font-size: 10px;
            font-weight: 700;
        }

        .page-title {
            margin-top: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #324b7a;
        }

        /* FILTERS BOX - Only show on first page */
        .filters-info {
            background: #eef3ff;
            padding: 6px 10px;
            margin: 8px 0 0;
            border-left: 4px solid #3f6ad8;
            font-size: 9px;
            border-radius: 4px;
            width: 100%;
        }

        .filters-info h3 {
            margin-bottom: 2px;
            font-size: 10px;
            font-weight: 600;
            color: #324b7a;
        }

        .filters-info p {
            margin: 1px 0;
            color: #555;
        }

        /* CONTENT AREA - ADJUST MARGIN FOR HEADER/FOOTER */
        .content-wrapper {
            margin-top: 40mm;
            margin-bottom: 20mm;
            padding: 0 15mm;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            border: 1px solid #b8c3d6;
        }

        .data-table th {
            background: white;
            color: black;
            padding: 10px 3px;
            text-transform: uppercase;
            font-size: 8px;
            border: 1px solid #000000ff;
            font-weight: 600;
        }

        .data-table td {
            padding: 4px 3px;
            border: 1px solid #000000ff;
            font-size: 8px;
            line-height: 1.2;
        }

        .data-table tr:nth-child(even) {
            background: #f7f9fc;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        /* FOOTER - FIXED POSITION FOR EACH PAGE */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding: 8px 15mm;
            background: white;
            height: 15mm;
        }

        .footer-left {
            float: left;
            text-align: left;
        }

        .footer-right {
            float: right;
            text-align: right;
        }

        .remarks-poor {
            color: #dc2626;
            font-weight: 600;
        }

        .remarks-non-poor {
            color: #059669;
            font-weight: 600;
        }

        .remarks-ultra-poor {
            color: #7c3aed;
            font-weight: 600;
        }

        /* Column widths */
        .col-number {
            width: 5%;
        }
        .col-name {
            width: 20%;
        }
        .col-barangay {
            width: 12%;
        }
        .col-email {
            width: 18%;
        }
        .col-school {
            width: 18%;
        }
        .col-year {
            width: 12%;
        }
        .col-remarks {
            width: 15%;
        }

        .compact-row td {
            padding: 3px 2px;
        }

        .name-format {
            font-weight: 600;
            font-size: 8px;
        }

        /* PRINT STYLES - CRITICAL FOR MULTIPLE PAGES */
        @media print {
            @page {
                margin: 35mm 0mm 20mm 0mm;
                size: A4 portrait;
            }

            body {
                margin: 0;
                padding: 0;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                font-size: 9px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page {
                min-height: 100vh;
                position: relative;
            }

            .header-portrait {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 35mm;
                border-bottom: none;
            }

            .page-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 15mm;
            }

            .content-wrapper {
                margin-top: 38mm;
                margin-bottom: 20mm;
            }

            /* Ensure table breaks properly across pages */
            table { 
                page-break-inside: auto;
            }

            tr { 
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead { 
                display: table-header-group;
            }

            tbody { 
                display: table-row-group;
            }
        }

        .no-data {
            text-align: center;
            padding: 50px 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>
    @php
        $perPage = 48;
        $chunks = $applicants->chunk($perPage);
        $totalPages = $chunks->count();
        $totalApplicants = $applicants->count();
    @endphp

    @foreach($chunks as $page => $pageApplicants)
    <div class="page">
        <div class="container">
            <!-- HEADER - REPEATS ON EVERY PAGE -->
            <div class="header-portrait">
                <table class="header-table">
                    <tr>
                        <td class="logo">
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture2.png'))) }}">
                        </td>
                        <td class="name-section">
                            <div><strong>Republic of the Philippines</strong></div>
                            <div>PROVINCE OF MISAMIS ORIENTAL</div>
                            <div>MUNICIPALITY OF TAGOLOAN</div>
                            <div><strong>LOCAL YOUTH DEVELOPMENT OFFICE</strong></div>
                            <div><strong>SCHOLARSHIP MANAGEMENT SYSTEM</strong></div>
                            <div class="page-title">{{ $title }} - Page {{ $page + 1 }}</div>
                            
                            <!-- FILTERS ONLY ON FIRST PAGE -->
                            @if($page === 0 && !empty($filters))
                            <div class="filters-info">
                                <h3>Applied Filters:</h3>
                                @foreach($filters as $filter)
                                    <p>{{ $filter }}</p>
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td class="logo">
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture3.png'))) }}">
                        </td>
                    </tr>
                </table>
            </div>

            <!-- CONTENT -->
            <div class="content-wrapper">
                @if($pageApplicants->count() > 0)
                @php
                    $sortedApplicants = $pageApplicants->sortBy('applicant_lname');
                @endphp
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-center col-number">#</th>
                            <th class="text-center col-name">Name</th>
                            <th class="text-center col-barangay">Barangay</th>
                            <th class="text-center col-email">Email</th>
                            <th class="text-center col-school">School</th>
                            <th class="text-center col-year">Academic Year</th>
                            <th class="text-center col-remarks">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sortedApplicants as $index => $applicant)
                        <tr class="compact-row">
                            <td class="text-center">{{ ($page * $perPage) + $loop->iteration }}</td>
                            <td class="text-center name-format">
                                {{ $applicant->applicant_lname }}{{ $applicant->applicant_suffix ? ' ' . $applicant->applicant_suffix : '' }}, 
                                {{ $applicant->applicant_fname }} 
                                {{ $applicant->applicant_mname ? $applicant->applicant_mname . ' ' : '' }}
                            </td>
                            <td class="text-center">{{ $applicant->applicant_brgy }}</td>
                            <td class="text-center">{{ $applicant->applicant_email }}</td>
                            <td class="text-center">{{ $applicant->applicant_school_name }}</td>
                            <td class="text-center">{{ $applicant->applicant_acad_year ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($applicant->remarks === 'Poor')
                                    <span class="remarks-poor">Poor</span>
                                @elseif($applicant->remarks === 'Non-Poor')
                                    <span class="remarks-non-poor">Non-Poor</span>
                                @elseif($applicant->remarks === 'Ultra Poor')
                                    <span class="remarks-ultra-poor">Ultra Poor</span>
                                @else
                                    {{ $applicant->remarks ?? 'N/A' }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="no-data">
                    <h3>No LYDO Reviewed Applicants Found</h3>
                    <p>No applicants match the current filter criteria.</p>
                </div>
                @endif
            </div>

            <!-- FOOTER - INDIVIDUAL FOR EACH PAGE -->
            <div class="page-footer">
                <div class="footer-left">
                    Report generated by LYDO Scholarship Management System<br>
                    {{ \Carbon\Carbon::now()->format('F d, Y — h:i A') }}
                </div>
                <div class="footer-right">
                    Page {{ $page + 1 }} of {{ $totalPages }}<br>
                    Total Applicants: {{ $totalApplicants }}
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>