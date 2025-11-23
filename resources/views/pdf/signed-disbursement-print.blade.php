<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signed Disbursements Report</title>
    <style>
        /* DOMpdf compatible styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #ffffff;
            counter-reset: page;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            background: #ffffff;
        }

        /* HEADER TABLE */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-table td {
            text-align: center;
            padding: 5px;
            vertical-align: middle;
        }

        .logo img {
            width: 80px;
            height: 80px;
        }

        .name-section div {
            font-size: 12px;
            line-height: 1.2;
        }

        .name-section strong {
            font-size: 13px;
            font-weight: bold;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-top: 5px;
        }

        /* FILTERS BOX */
        .filters-info {
            background: #eef3ff;
            padding: 8px;
            margin-top: 8px;
            border-left: 4px solid #3f6ad8;
            font-size: 11px;
        }

        .filters-info h3 {
            margin-bottom: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
            border: 1px solid #000000;
        }

        .data-table th {
            background: #666666;
            color: white;
            padding: 6px;
            text-transform: uppercase;
            font-size: 10px;
            border: 1px solid #000000;
            font-weight: bold;
        }

        .data-table td {
            padding: 6px;
            border: 1px solid #000000;
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
            margin-top: 25px;
            text-align: center;
            font-size: 10px;
            color: #666666;
            border-top: 1px solid #cccccc;
            padding-top: 8px;
        }

        /* SIGNATURE STYLING */
        .signature-img {
            max-width: 60px;
            max-height: 25px;
            border: 1px solid #cccccc;
        }

        /* AMOUNT STYLING */
        .amount {
            font-weight: bold;
            color: #000000;
        }

        /* DATE STYLING */
        .date {
            font-size: 9px;
            color: #666666;
        }

        /* Page break avoidance */
        .avoid-break {
            page-break-inside: avoid;
        }

        /* Page numbering styles */
        .page-number {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: left;
            font-size: 10px;
            color: #666666;
            padding: 5px 15px;
            background: white;
        }

        /* Force landscape in DOMpdf */
        @page {
            size: landscape;
            margin: 10mm;
            
            @bottom-left {
                content: "Page " counter(page);
                font-size: 10px;
                color: #666666;
                font-family: "DejaVu Sans", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            }
        }

        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 15px;
                counter-reset: page;
            }
            
            .container {
                margin-bottom: 20px;
            }
            
            /* Ensure proper page breaks */
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
            
            /* Hide regular footer in print */
            .footer {
                display: none;
            }
        }

        /* Page break handling */
        .page-break {
            page-break-after: always;
            break-after: page;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="logo" style="width: 100px;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture2.png'))) }}" style="width: 80px; height: 80px;">
                </td>

                <td class="name-section">
                    <div><strong>Republic of the Philippines</strong></div>
                    <div>PROVINCE OF MISAMIS ORIENTAL</div>
                    <div>MUNICIPALITY OF TAGOLOAN</div>
                    <div><strong>LOCAL YOUTH DEVELOPMENT OFFICE</strong></div>
                    <div><strong>SCHOLARSHIP MANAGEMENT SYSTEM</strong></div>
                    <div class="report-title">SIGNED DISBURSEMENTS REPORT</div>

                    @if(!empty($filters))
                    <div class="filters-info">
                        <h3>Applied Filters:</h3>
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

                <td class="logo" style="width: 100px;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture3.png'))) }}" style="width: 80px; height: 80px;">
                </td>
            </tr>
        </table>

        <!-- TABLE -->
        @if($signedDisbursements->count() > 0)
        
        @php
            // Calculate how many rows per page for landscape orientation
            $rowsPerPage = 15;
            $totalPages = ceil($signedDisbursements->count() / $rowsPerPage);
        @endphp
        
        @for($page = 0; $page < $totalPages; $page++)
            @php
                $currentPageDisbursements = $signedDisbursements->slice($page * $rowsPerPage, $rowsPerPage);
                $currentPageNumber = $page + 1;
                $totalDisbursementsCount = $signedDisbursements->count();
                $startNumber = ($page * $rowsPerPage) + 1;
                $endNumber = min(($page + 1) * $rowsPerPage, $totalDisbursementsCount);
            @endphp
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">Scholar Name</th>
                        <th style="width: 15%;">Barangay</th>
                        <th style="width: 10%;">Semester</th>
                        <th style="width: 15%;">Academic Year</th>
                        <th style="width: 10%;" class="text-center">Amount</th>
                        <th style="width: 10%;" class="text-center">Date</th>
                        <th style="width: 10%;" class="text-center">Signature</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($currentPageDisbursements as $index => $disburse)
                    <tr class="avoid-break">
                        <td class="text-center">{{ $startNumber + $index }}</td>
                        <td class="text-left">{{ $disburse->full_name }}</td>
                        <td class="text-center">{{ $disburse->applicant_brgy }}</td>
                        <td class="text-center">{{ $disburse->disburse_semester }}</td>
                        <td class="text-center">{{ $disburse->disburse_acad_year }}</td>
                        <td class="text-center amount">Php {{ number_format($disburse->disburse_amount, 2) }}</td>
                        <td class="text-center date">{{ \Carbon\Carbon::parse($disburse->disburse_date)->format('F d, Y') }}</td>
                        <td class="text-center">
                            @if($disburse->disburse_signature)
                                <img src="{{ $disburse->disburse_signature }}" class="signature-img" alt="Signature" style="max-width: 60px; max-height: 25px;">
                            @else
                                <span style="color: #000000; font-weight: bold; font-size: 9px;">✓ SIGNED</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Footer for current page -->
            <div class="footer">
                Report generated by LYDO Scholarship Management System <br>
                {{ \Carbon\Carbon::now()->format('F d, Y — h:i A') }} | 
                Showing {{ $startNumber }}-{{ $endNumber }} of {{ $totalDisbursementsCount }} Signed Disbursements
                @if($totalPages > 1)
                    | Page {{ $currentPageNumber }} of {{ $totalPages }}
                @endif
            </div>

            @if($page < $totalPages - 1)
                <div class="page-break"></div>
            @endif
            
        @endfor

        @else
        <div class="text-center" style="padding: 30px;">
            <h3 style="color: #555555; font-size: 14px;">No Signed Disbursements Found</h3>
            <p style="color: #777777; font-size: 12px;">No signed disbursements match the current filter criteria.</p>
        </div>
        @endif

    </div>

    <script>
    // Add page numbers functionality for browser printing
    document.addEventListener('DOMContentLoaded', function() {
        // Function to calculate and update page numbers for print
        function updatePageNumbers() {
            console.log('Page numbering enabled for printing - Bottom Left Corner');
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