<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants List Report</title>
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
            padding: 15px;
            position: relative;
        }

        .container {
            width: 100%;
            background: #fff;
            margin: 0 auto;
            padding: 0;
        }

        /* IMPROVED HEADER - PORTRAIT STYLE */
        .header-portrait {
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #324b7a;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
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
            font-size: 10px;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .name-section strong {
            font-size: 11px;
            font-weight: 700;
        }

        /* FILTERS BOX */
        .filters-info {
            background: #eef3ff;
            padding: 6px 10px;
            margin: 10px 0 0;
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

        /* DATA TABLE - PORTRAIT OPTIMIZED */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9px;
            border: 1px solid #b8c3d6;
        }

        .data-table th {
            background: gray;
            color: white;
            padding: 5px 3px;
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

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }

        .status-approved {
            color: #059669;
            font-weight: 600;
        }

        .status-rejected {
            color: #dc2626;
            font-weight: 600;
        }

        /* Column widths optimized for portrait */
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
        .col-screening {
            width: 15%;
        }

        /* Compact styling */
        .compact-row td {
            padding: 3px 2px;
        }

        .name-format {
            font-weight: 600;
            font-size: 8px;
        }

        /* PRINT STYLES WITH PAGE NUMBERS */
        @media print {
            body {
                margin: 0;
                padding: 15px;
                counter-reset: page;
            }
            
            .container {
                width: 100%;
            }
            
            /* Page break handling */
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
            
            tfoot { 
                display: table-footer-group; 
            }
            
            /* Footer with page numbers */
            .page-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: left;
                font-size: 9px;
                color: #666;
                padding: 5px 15px;
                background: white;
            }
            
            .page-footer::after {
                counter-increment: page;
                content: "Page " counter(page);
                float: right;
            }
            
            /* Hide regular footer in print */
            .footer {
                display: none;
            }
            
            /* Ensure content doesn't overlap with footer */
            .container {
                margin-bottom: 30px;
            }
        }

        /* Page footer for screen view */
        .page-footer {
            display: none;
        }

        @media screen {
            .page-footer {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Page footer for printing -->
    <div class="page-footer"></div>

    <div class="container">

        <!-- IMPROVED HEADER - PORTRAIT STYLE -->
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

                        @if(!empty($filters))
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

        <!-- TABLE CONTENT - PORTRAIT OPTIMIZED -->
        @if($applicants->count() > 0)
        
        @php
            // Sort the collection by last name alphabetically
            $sortedApplicants = $applicants->sortBy(function($item) {
                return $item->applicant_lname;
            });
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
                </tr>
            </thead>

            <tbody>
                @foreach($sortedApplicants as $index => $applicant)
                <tr class="compact-row">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center name-format">
                        {{ $applicant->applicant_lname }}{{ $applicant->applicant_suffix ? ' ' . $applicant->applicant_suffix : '' }}, 
                        {{ $applicant->applicant_fname }} 
                        {{ $applicant->applicant_mname ? $applicant->applicant_mname . ' ' : '' }}
                    </td>
                    <td class="text-center">{{ $applicant->applicant_brgy }}</td>
                    <td class="text-center">{{ $applicant->applicant_email }}</td>
                    <td class="text-center">{{ $applicant->applicant_school_name }}</td>
                    <td class="text-center">{{ $applicant->applicant_acad_year ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <div class="text-center" style="padding: 30px;">
            <h3 style="color: #555; font-size: 14px;">No Applicants Found</h3>
            <p style="color: #777; font-size: 12px;">No applicants match the current filter criteria.</p>
        </div>
        @endif

        <!-- Regular footer for screen view -->
        <div class="footer">
            Report generated by LYDO Scholarship Management System <br>
            {{ \Carbon\Carbon::now()->format('F d, Y — h:i A') }} | 
            Total Applicants: {{ $applicants->count() }}
        </div>

    </div>

    <script>
    // Add page numbers functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Function to calculate and update page numbers for print
        function updatePageNumbers() {
            const pageFooter = document.querySelector('.page-footer');
            if (pageFooter) {
                // This will be handled by CSS counters during print
                console.log('Page numbering enabled for printing');
            }
        }

        // Update page numbers when printing
        window.addEventListener('beforeprint', function() {
            updatePageNumbers();
        });

        // Initial update
        updatePageNumbers();
    });
    </script>
</body>
</html>