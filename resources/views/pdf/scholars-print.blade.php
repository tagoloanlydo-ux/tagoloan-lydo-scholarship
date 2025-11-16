<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholars List Report</title>
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
            font-size: 12px; /* Added base font size */
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
            font-size: 12px; /* Changed from 10px to 14px */
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .name-section strong {
            font-size: 12px; /* Changed from 11px to 14px */
            font-weight: 700;
        }

        /* FILTERS BOX */
        .filters-info {
            background: #eef3ff;
            padding: 8px 12px; /* Increased padding */
            margin: 10px 0 0;
            border-left: 4px solid #3f6ad8;
            font-size: 12px; /* Changed from 9px to 14px */
            border-radius: 4px;
            width: 100%;
        }

        .filters-info h3 {
            margin-bottom: 4px; /* Increased margin */
            font-size: 12px; /* Changed from 10px to 14px */
            font-weight: 600;
            color: #324b7a;
        }

        .filters-info p {
            margin: 3px 0; /* Increased margin */
            color: #555;
            font-size: 14px; /* Added font size */
        }

        /* DATA TABLE - PORTRAIT OPTIMIZED */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px; /* Changed from 9px to 14px */
            border: 1px solid #b8c3d6;
        }

        .data-table th {
            background: #ddddddff;
            color: black;
            padding: 8px 5px; /* Increased padding */
            text-transform: uppercase;
            font-size: 13px; /* Changed from 8px to 13px */
            border: 1px solid #c7c7c9ff;
            font-weight: 600;
        }

        .data-table td {
            padding: 6px 4px; /* Increased padding */
            border: 1px solid #cacbccff;
            font-size: 14px; /* Changed from 8px to 14px */
            line-height: 1.3;
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
            font-size: 14px; /* Changed from 9px to 14px */
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px; /* Increased padding */
        }

        /* Column widths optimized for portrait */
        .col-number {
            width: 5%;
        }
        .col-name {
            width: 22%;
        }
        .col-barangay {
            width: 13%;
        }
        .col-email {
            width: 20%;
        }
        .col-school {
            width: 20%;
        }
        .col-course {
            width: 20%;
        }

        /* Compact styling */
        .compact-row td {
            padding: 5px 3px; /* Increased padding */
        }

        .name-format {
            font-weight: 600;
            font-size: 12px; /* Changed from 8px to 14px */
        }

        /* No scholars found styling */
        .no-scholars {
            padding: 40px; /* Increased padding */
            text-align: center;
        }

        .no-scholars h3 {
            color: #555;
            font-size: 16px; /* Increased font size */
            margin-bottom: 10px;
        }

        .no-scholars p {
            color: #777;
            font-size: 12px; /* Added font size */
        }
    </style>
</head>

<body>
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

                        @if(request('search') || request('barangay') || request('academic_year') || request('semester') || request('status'))
                        <div class="filters-info">
                            <h3>Applied Filters:</h3>
                            @if(request('search'))
                                <p><strong>Search:</strong> {{ request('search') }}</p>
                            @endif
                            @if(request('barangay'))
                                <p><strong>Barangay:</strong> {{ request('barangay') }}</p>
                            @endif
                            @if(request('academic_year'))
                                <p><strong>Academic Year:</strong> {{ request('academic_year') }}</p>
                            @endif
                            @if(request('semester'))
                                <p><strong>Semester:</strong> {{ request('semester') }}</p>
                            @endif
                            @if(request('status'))
                                <p><strong>Status:</strong> {{ ucfirst(request('status')) }}</p>
                            @endif
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
        @if($scholars->count() > 0)
        
        @php
            // Sort the collection by last name alphabetically
            $sortedScholars = $scholars->sortBy(function($item) {
                return $item->applicant_lname;
            });
        @endphp
        
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center col-number">#</th>
                    <th class="text-center col-name">Name</th>
                    <th class="text-center col-barangay">Barangay</th>
                    <th class="text-center col-school">School</th>
                    <th class="text-center col-course">Course</th>
                    <th class="text-center">S.Y</th>
                </tr>
            </thead>

            <tbody>
                @foreach($sortedScholars as $index => $scholar)
                <tr class="compact-row">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center name-format">
                        {{ $scholar->applicant_lname }}{{ $scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : '' }}, 
                        {{ $scholar->applicant_fname }} 
                        {{ $scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '' }}
                    </td>
                    <td class="text-center">{{ $scholar->applicant_brgy }}</td>
                    <td class="text-center">{{ $scholar->applicant_school_name }}</td>
                    <td class="text-center">{{ $scholar->applicant_course }}</td>
                    <td class="text-center">{{ $scholar->applicant_acad_year ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <div class="no-scholars">
            <h3>No Scholars Found</h3>
            <p>No scholars match the current filter criteria.</p>
        </div>
        @endif

        <div class="footer">
            Report generated by LYDO Scholarship Management System <br>
            {{ \Carbon\Carbon::now()->format('F d, Y — h:i A') }} | 
            Total Scholars: {{ $scholars->count() }}
        </div>

    </div>
</body>
</html>