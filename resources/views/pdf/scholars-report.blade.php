<!DOCTYPE html>
<link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
<html>
<head>
    <meta charset="utf-8">
    <title>Scholars Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            counter-reset: page;
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

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: bold;
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
            border: 1px solid #fff; /* white border */
        }

        .signature-table td {
            border: 1px solid #fff; /* white border for cells */
            padding: 5px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto 5px auto;
        }

        /* Footer style */
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

        /* Page numbering for print */
        @media print {
            body {
                margin: 0;
                padding: 15px;
                counter-reset: page;
            }
            
            /* Page footer for printing */
            .page-footer {
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
            
            /* Hide regular footer in print */
            .footer {
                display: none;
            }
            
            /* Ensure content doesn't overlap with footer */
            body {
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

        /* Page break handling */
        .page-break {
            page-break-after: always;
            break-after: page;
        }
    </style>
</head>
<body>
    <!-- Page footer for printing -->
    <div class="page-footer"></div>

    <div class="header">
        <div class="header-content">
            <h1>LYDO Scholarship Scholars Report</h1>
            <p>Tagoloan, Misamis Oriental</p>
        </div>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <strong>Applied Filters:</strong>
        {{ implode(' | ', $filters) }}
    </div>
    @endif

    @if($scholars->count() > 0)
    
    @php
        // Calculate how many rows per page (adjust based on your content)
        $rowsPerPage = 15; // Reduced to accommodate signature section
        $totalPages = ceil($scholars->count() / $rowsPerPage);
    @endphp
    
    @for($page = 0; $page < $totalPages; $page++)
        @php
            $currentPageScholars = $scholars->slice($page * $rowsPerPage, $rowsPerPage);
            $currentPageNumber = $page + 1;
            $totalScholarsCount = $scholars->count();
            $startNumber = ($page * $rowsPerPage) + 1;
            $endNumber = min(($page + 1) * $rowsPerPage, $totalScholarsCount);
        @endphp
    
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Full Name</th>
                    <th style="width: 15%;">School</th>
                    <th style="width: 10%;">Course</th>
                    <th style="width: 8%;">Year Level</th>
                    <th style="width: 10%;">Barangay</th>
                    <th style="width: 10%;">Academic Year</th>
                    <th style="width: 8%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($currentPageScholars as $index => $scholar)
                <tr>
                    <td class="text-center">{{ $startNumber + $index }}</td>
                    <td>
                        {{ $scholar->applicant_fname }}
                        @if($scholar->applicant_mname)
                            {{ $scholar->applicant_mname }}
                        @endif
                        {{ $scholar->applicant_lname }}
                        @if($scholar->applicant_suffix)
                            {{ $scholar->applicant_suffix }}
                        @endif
                    </td>
                    <td>{{ $scholar->applicant_school_name }}</td>
                    <td>{{ $scholar->applicant_course }}</td>
                    <td class="text-center">{{ $scholar->applicant_year_level }}</td>
                    <td>{{ $scholar->applicant_brgy }}</td>
                    <td class="text-center">{{ $scholar->applicant_acad_year }}</td>
                    <td class="text-center">
                        <span class="status-{{ strtolower($scholar->scholar_status) }}">
                            {{ ucfirst($scholar->scholar_status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signature section only on the last page -->
        @if($page == $totalPages - 1)
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
        @endif

        <!-- Footer for current page -->
        <div class="footer">
            <strong>Lydo Scholarship System</strong><br>
            Generated on: {{ date('F d, Y') }} at {{ date('h:i A') }}<br>
            Showing {{ $startNumber }}-{{ $endNumber }} of {{ $totalScholarsCount }} Scholars
            @if($totalPages > 1)
                | Page {{ $currentPageNumber }} of {{ $totalPages }}
            @endif
        </div>

        @if($page < $totalPages - 1)
            <div class="page-break"></div>
        @endif
        
    @endfor

    @else
    <div class="no-data">
        <p>No scholar records found matching the specified criteria.</p>
    </div>
    @endif

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