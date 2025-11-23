<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disbursement Records Report</title>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f4f6f9;
            padding: 30px;
            counter-reset: page;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            background: #fff;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Page numbering for print */
        @page {
            margin: 1cm;
            size: A4;
            
            @bottom-left {
                content: "Page " counter(page);
                font-size: 10px;
                color: #666;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            }
        }

        /* Page footer for print */
        .page-footer {
            display: none;
        }

        /* Force page breaks for print */
        .page-break {
            page-break-after: always;
            break-after: page;
        }

        /* Print-specific styles */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                counter-reset: page;
            }
            
            .container {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            
            /* Page footer for printing */
            .page-footer {
                display: block;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: left;
                font-size: 10px;
                color: #666;
                padding: 8px 15px;
                background: white;
            }
            
            .page-footer::after {
                counter-increment: page;
                content: "Page " counter(page);
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
            
            tfoot {
                display: table-footer-group;
            }
            
            /* Hide non-essential elements in print */
            .no-print {
                display: none;
            }
            
            /* Ensure content doesn't overlap with footer */
            .container {
                margin-bottom: 30px;
            }
        }

        /* HEADER TABLE (no outline) */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-table td {
            text-align: center;
            padding: 10px;
            vertical-align: middle;
        }

        .logo img {
            width: 95px;
            height: 100px;
            object-fit: contain;
        }

        .name-section div {
            font-size: 13px;
            line-height: 1.3;
        }

        .name-section strong {
            font-size: 14px;
            font-weight: 700;
        }

        /* FILTERS BOX */
        .filters-info {
            background: #eef3ff;
            padding: 10px;
            margin-top: 8px;
            border-left: 4px solid #3f6ad8;
            font-size: 12px;
        }

        .filters-info h3 {
            margin-bottom: 5px;
            font-size: 13px;
            font-weight: 600;
        }

        /* DATA TABLE (with outline & professional style) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            font-size: 13px;
            border: 1px solid #b8c3d6; /* ✅ clean border */
        }

        .data-table th {
            background: gray;
            color: white;
            padding: 8px;
            text-transform: uppercase;
            font-size: 11px;
            border: 1px solid #2d4170;
        }

        .data-table td {
            padding: 8px;
            border: 1px solid #cbd4e1; /* ✅ subtle borders */
        }

        .data-table tr:nth-child(even) {
            background: #f7f9fc;
        }

        .data-table tr:hover {
            background: #eef3ff;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 35px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        .status-pending {
            color: #d97706;
            font-weight: 600;
        }
        
        /* Print button styling */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3f6ad8;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background: #2d55b5;
        }
    </style>
</head>

<body>
    <!-- Page footer for printing -->
    <div class="page-footer"></div>

    <!-- Print Button -->
    <button class="print-button no-print" onclick="window.print()">Print Report</button>

    <div class="container">
        <!-- HEADER (NO OUTLINE) -->
        <table class="header-table">
            <tr>
                <td class="logo h-3xl">
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

        <!-- TABLE WITH OUTLINE -->
        @if($unsignedDisbursements->count() > 0)
        
        @php
            // Sort the collection by last name alphabetically
            $sortedDisbursements = $unsignedDisbursements->sortBy(function($item) {
                // Extract last name from full_name (assuming format: "Lastname, Firstname Middle")
                $nameParts = explode(',', $item->full_name);
                return trim($nameParts[0]); // Return last name for sorting
            });
            
            // Calculate how many rows per page (adjust based on your content)
            $rowsPerPage = 20;
            $totalPages = ceil($sortedDisbursements->count() / $rowsPerPage);
        @endphp
        
        @for($page = 0; $page < $totalPages; $page++)
            @php
                $currentPageDisbursements = $sortedDisbursements->slice($page * $rowsPerPage, $rowsPerPage);
            @endphp
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Barangay</th>
                        <th>Semester</th>
                        <th>Academic Year</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Signature</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($currentPageDisbursements as $index => $disburse)
                    <tr>
                        <td class="text-center">{{ ($page * $rowsPerPage) + $loop->iteration }}</td>
                        <td class="text-center">{{ $disburse->full_name }}</td>
                        <td class="text-center">{{ $disburse->applicant_brgy }}</td>
                        <td class="text-center">{{ $disburse->disburse_semester }}</td>
                        <td class="text-center">{{ $disburse->disburse_acad_year }}</td>
                        <td class="text-center">Php {{ number_format($disburse->disburse_amount, 0) }}</td>
                        <td class="text-center status-pending"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($page < $totalPages - 1)
                <div class="page-break"></div>
            @endif
        @endfor

        @else
        <div class="text-center" style="padding: 40px;">
            <h3 style="color: #555;">No Pending Disbursements Found</h3>
            <p style="color: #777;">No unsigned disbursements match the current filter criteria.</p>
        </div>
        @endif

        <div class="footer no-print">
            Report generated by LYDO Scholarship Management System <br>
            {{ \Carbon\Carbon::now()->format('F d, Y — h:i A') }}
        </div>

    </div>
    
    <script>
    // Add page numbers functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Function to calculate and update page numbers for print
        function updatePageNumbers() {
            const pageFooter = document.querySelector('.page-footer');
            if (pageFooter) {
                console.log('Page numbering enabled for printing - Bottom Left Corner');
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