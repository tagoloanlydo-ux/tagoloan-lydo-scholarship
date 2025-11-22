<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Applicants List - {{ date('Y-m-d') }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 20px;
        }
        /* Print-specific styles */
@media print {
    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
    }
    
    .footer::after {
        content: "Page " counter(page);
        position: absolute;
        right: 15px;
        bottom: 8px;
        font-size: 9px;
        color: #666;
    }
    
    body {
        counter-reset: page;
    }
    
    /* Force page breaks if needed for long tables */
    table { 
        page-break-inside: auto;
    }
    tr { 
        page-break-inside: avoid; 
        page-break-after: auto;
    }
}
        /* Header Styles */
        .header { 
            text-align: center; 
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        
        .header .subtitle {
            font-size: 14px;
            margin: 0 0 3px 0;
            font-weight: bold;
        }
        
        .header .address {
            font-size: 11px;
            margin: 0;
            line-height: 1.3;
        }
        
        /* Filters Section */
        .filters { 
            margin-bottom: 20px; 
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #4c1d95;
        }
        
        .filters strong {
            color: #4c1d95;
        }
        
        .filters ul { 
            margin: 5px 0 0 0; 
            padding: 0; 
            list-style: none; 
        }
        
        .filters li { 
            display: inline-block; 
            margin-right: 15px;
            font-size: 11px;
        }
        
        /* Table Styles */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        
        th { 
            background-color: #c9c9c9ff; 
            color: white; 
            font-weight: bold;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #d6d6d6ff;
            font-size: 11px;
        }
        
        td { 
            border: 1px solid #333; 
            padding: 8px;
            text-align: left;
            font-size: 11px;
            vertical-align: top;
        }
        
        /* Column specific styles */
        .col-number { width: 5%; text-align: center; }
        .col-name { width: 25%; }
        .col-barangay { width: 15%; text-align: center; }
        .col-school { width: 20%; }
        .col-course { width: 20%; }
        .col-sy { width: 15%; text-align: center; }
        
        /* Row styling */
        tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        
        .applicant-name {
            font-weight: bold;
        }
        
        /* Footer Styles */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #333;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .footer .system-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .footer .timestamp {
            margin-bottom: 5px;
        }
        
        .footer .total-count {
            font-weight: bold;
            color: #4c1d95;
        }
        
        /* Page break avoidance */
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Republic of the Philippines</h1>
        <p class="subtitle">PROVINCE OF MISAMIS ORIENTAL</p>
        <p class="subtitle">MUNICIPALITY OF TAGOLOAN</p>
        <p class="address" style="margin-top: 15px;">
            <strong>LOCAL YOUTH DEVELOPMENT OFFICE</strong><br>
            <strong>SCHOLARSHIP MANAGEMENT SYSTEM</strong>
        </p>
    </div>

    <!-- Filters Section -->
    @if(!empty($filters))
    <div class="filters">
        <strong>Applied Filters:</strong>
        <ul>
            @foreach($filters as $filter)
            <li>{{ $filter }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Applicants Table -->
    <table>
        <thead>
            <tr>
                <th class="col-number">#</th>
                <th class="col-name">NAME</th>
                <th class="col-barangay">BARANGAY</th>
                <th class="col-school">SCHOOL</th>
                <th class="col-course">COURSE</th>
                <th class="col-sy">ACADEMIC YEAR</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applicants as $index => $applicant)
            <tr>
                <td class="col-number">{{ $index + 1 }}</td>
                <td class="col-name">
                    <span class="applicant-name">
                        {{-- Format: Last Name, First Name Middle Initial Suffix --}}
                        {{ ucfirst(strtolower($applicant->applicant_lname)) }},
                        {{ ucfirst(strtolower($applicant->applicant_fname)) }}
                        @if(!empty($applicant->applicant_mname))
                            {{ ' ' . strtoupper(substr($applicant->applicant_mname, 0, 1)) . '.' }}
                        @endif
                        @if(!empty($applicant->applicant_suffix))
                            {{ ' ' . $applicant->applicant_suffix }}
                        @endif
                    </span>
                </td>
                <td class="col-barangay">{{ $applicant->applicant_brgy }}</td>
                <td class="col-school">{{ $applicant->applicant_school_name }}</td>
                <td class="col-course">{{ $applicant->applicant_course ?? 'Not Specified' }}</td>
                <td class="col-sy">{{ $applicant->applicant_acad_year }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    No applicants found matching the selected criteria.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div class="system-name">
            Report generated by LYDO Scholarship Management System
        </div>
        <div class="timestamp">
            {{ \Carbon\Carbon::now()->format('F d, Y — h:i A') }}
        </div>
        <div class="total-count">
            Total Applicants: {{ $applicants->count() }}
        </div>
    </div>
</body>
</html>