<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Intake Sheet - PDF Print View</title>
    
    <style>
        @page { 
            size: legal landscape; 
            margin: 10mm; 
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 20px;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        
        .main-container {
            width: 100%;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        
        .header-table td {
            vertical-align: top;
            padding: 2px;
        }
        
        .logo-cell {
            width: 60px;
            text-align: center;
        }
        
        .logo {
            width: 50px;
            height: 50px;
        }
        
        .org-info {
            text-align: center;
            padding: 0 10px;
        }
        
        .org-info h1 {
            font-size: 15px;
            margin: 0;
            font-weight: bold;
        }
        
        .org-info h2 {
            font-size: 15px;
            margin: 0;
            font-weight: bold;
        }
        
        .serial-number {
            text-align: right;
            width: 120px;
        }
        
        /* Two Column Layout */
        .two-column-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .two-column-table td {
            vertical-align: top;
            padding: 0;
        }
        
        .left-column {
            width: 65%;
            padding-right: 5px;
            
        }
        
        .right-column {
            width: 35%;
            border-left: 1px solid #ddd;
            padding-left: 5px;
        }
        
        /* Form Sections */
        .section {
            margin-bottom: 8px;
            border: 1px solid #ffffffff;
            padding: 6px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 6px;
            text-align: center;
            background: #f0f0f0;
            padding: 3px;
        }
        
        /* SOCIAL SERVICES - LARGER STYLES */
        .social-services-title {
            font-weight: bold;
            font-size: 14px !important;
            margin-bottom: 8px;
            text-align: center;
            background: #e8e8e8;
            padding: 6px;
            border: 1px solid #000;
        }
        
        .social-services-section {
            border: 2px solid #ffffffff;
            padding: 8px;
            background: #fafafa;
        }
        
        /* Checkbox Row */
        .checkbox-row {
            margin-bottom: 6px;
        }
        
        .checkbox-row table {
            width: 100%;
        }
        
        .checkbox-row td {
            padding: 1px 3px;
            vertical-align: middle;
        }
        
        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
            font-size: 15px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: middle;
        }
        
        .data-table th {
            background: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }
        
        .family-table th {
            font-size: 15px;
            padding: 2px;
        }
        
        .family-table td {
            font-size: 15px;
            padding: 2px;
        }
        
        /* SOCIAL SERVICES TABLE - LARGER */
        .services-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px !important;
            margin: 6px 0;
        }
        
        .services-table th,
        .services-table td {
            border: 2px solid #000;
            padding: 6px !important;
            vertical-align: top;
            height: 50px;
        }
        
        .services-table th {
            background: #e8e8e8;
            font-weight: bold;
            text-align: center;
            font-size: 15px;
        }
        
        .services-table td {
            background: #fff;
            font-size: 15px;
        }
        
        /* Input Fields */
        .input-field {
            border: none;
            border-bottom: 1px solid #000;
            padding: 2px;
            width: 100%;
            background: transparent;
        }
        
        .data-box {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            min-width: 40px;
        }
        
        /* Signature Section */
        .signature-table {
            width: 100%;
            margin-top: 10px;
        }
        
        .signature-table td {
            text-align: center;
            vertical-align: top;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 25px;
            margin-bottom: 3px;
        }
        
        /* Code Table */
        .code-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            margin: 8px 0;
        }
        
        .code-table th,
        .code-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }
        
        .code-table th {
            background: #f8f8f8;
            font-weight: bold;
        }
        
        .currency {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .mb-2 {
            margin-bottom: 8px;
        }
        
        /* Estimated Cost - Larger */
        .estimated-cost {
            font-weight: bold;
            font-size: 15px !important;
            margin: 10px 0;
            padding: 6px;
            background: #f0f0f0;
            border: 1px solid #000;
            text-align: center;
        }
        
        /* Verification Section - Larger */
        .verification-section {
            margin-top: 12px;
            padding: 8px;
            border-top: 2px solid #000;
            font-size: 15px;
        }
        
        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            
            .section {
                page-break-inside: avoid;
            }
            
            .family-table {
                page-break-inside: auto;
            }
            
            .social-services-section {
                background: #fafafa !important;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- HEADER -->
                    <!-- TWO COLUMN LAYOUT -->
                    <table class="two-column-table">
                    
                        <tr>
                            <!-- LEFT COLUMN - MAIN FORM -->
                            <td class="left-column">

                                        <table class="header-table" style="width: 100%; border: none; margin-bottom: 15px;">
                <tr>
                    <td style="border: none; vertical-align: top; padding: 5px; padding-right: 20px; width: 60%;">
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="border: none; vertical-align: top; width: 50px;">
                                    <div style="width: 70px; height: 90px; border: 1px solid #ffffffff; text-align: center; line-height: 50px;">
                                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture2.png'))) }}" alt="logo" style="width:100%;height:100%;object-fit:contain;">
                                    </div>
                                </td>
                                <td style="border: none; vertical-align: top; padding-left: 10px;">
                                    <div style="font-size: 20px; font-weight: bold;">Republic of the Philippines</div>
                                    <div style="font-size: 15px; font-weight: bold;">Province of Misamis Oriental</div>
                                    <div style="font-size: 15px; font-weight: bold;">Municipality of Tagoloan</div>
                                    <div style="font-size: 9px; font-weight: bold;">
                                        MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="border: none; vertical-align: middle; padding: 5px; text-align: center; width: 25%;">
                        <div style="font-size: 15px; font-weight: bold;">
                            FAMILY INTAKE SHEET
                        </div>
                    </td>
                    <td style="border: none; vertical-align: top; padding: 5px; text-align: right; width: 15%;">
                        <div style="font-weight: bold;">Serial No.:</div>
                        <div style="margin-top: 5px; font-weight: bold;">{{ $serialNumber ?? '__________' }}</div>
                    </td>
                </tr>
            </table>
                                <!-- HEAD OF FAMILY SECTION -->
                    <table class="section" style="width: 100%; border: none; font-family: Arial, sans-serif; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; padding: 5px;">
                        <!-- Checkbox Row -->
                            <table style="width: 100%; border: none; margin-bottom: 8px;">
                                <tr>
                                    <td style="border: none; padding: 3px;">
                                        <input type="checkbox" {{ ($location ?? '') === 'within' ? 'checked' : '' }}> Within Tagoloan
                                        <input type="checkbox" {{ ($location ?? '') === 'outside' ? 'checked' : '' }}> Outside Tagoloan
                                    </td>
                                    <td style="border: none; text-align: right; padding: 3px;">
                                        <input type="checkbox" {{ ($head['_4ps'] ?? '') === 'Yes' ? 'checked' : '' }}> 4P's Beneficiary
                                        <strong style="margin-left: 8px;">IP:</strong> {{ $head['ipno'] ?? '______' }}
                                    </td>
                                </tr>
                            </table>

                                        <!-- Header -->
                                        <div style="text-align: center; font-weight: bold; font-size: 15px; margin-bottom: 8px; padding: 3px 0;">HEAD OF THE FAMILY:</div>

                                        <!-- Name and Personal Info -->
                                    <table style="width: 100%; border: none; margin-bottom: 8px;">
                                <tr>
                                    <td style="border: none; width: 20%; vertical-align: top; padding: 3px;">
                                        <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">SURNAME</div>
                                        <div style="border-bottom: 1px solid #000; padding: 4px 0; min-height: 20px;">{{ $head['lname'] ?? '&nbsp;' }}</div>
                                    </td>
                                    <td style="border: none; width: 20%; vertical-align: top; padding: 3px;">
                                        <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">FIRST NAME</div>
                                        <div style="border-bottom: 1px solid #000; padding: 4px 0; min-height: 20px;">{{ $head['fname'] ?? '&nbsp;' }}</div>
                                    </td>
                                    <td style="border: none; width: 20%; vertical-align: top; padding: 3px;">
                                        <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">MIDDLE NAME</div>
                                        <div style="border-bottom: 1px solid #000; padding: 4px 0; min-height: 20px;">{{ $head['mname'] ?? '&nbsp;' }}</div>
                                    </td>
                                    <td style="border: none; width: 10%; vertical-align: top; padding: 3px;">
                                        <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Suffix</div>
                                        <div style="border-bottom: 1px solid #000; padding: 4px 0; min-height: 20px;">{{ $head['suffix'] ?? '&nbsp;' }}</div>
                                    </td>

                                    <td style="border: none; width: 25%; vertical-align: top; padding: 3px;">
                                        <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Sex / Age</div>
                                        <table style="width: 100%; border: none;">
                                            <tr>
                                                <td style="border: 1px solid #000; padding: 4px; text-align: center; width: 50%;">
                                                    {{ $head['sex'] ?? '&nbsp;' }}
                                                </td>
                                                <td style="border: 1px solid #000; padding: 4px; text-align: center; width: 50%;">
                                                    @if(!empty($head['dob']))
                                                        {{ \Carbon\Carbon::parse($head['dob'])->age ?? '&nbsp;' }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                        </tr>
                            </table>
                        <!-- Address and Basic Info -->
                        <table style="width: 100%; border: none; margin-bottom: 8px;">
                            <tr>
                                <td style="border: none; width: 50%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Address</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['address'] ?? '-' }}</div>
                                </td>
                                <td style="border: none; width: 25%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Zone</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['zone'] ?? '-' }}</div>
                                </td>
                                <td style="border: none; width: 25%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Barangay</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['barangay'] ?? '-' }}</div>
                                </td>
                                <td style="border: none; width: 25%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Contact Number</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['contact'] ?? '-' }}</div>
                                </td>
                            </tr>
                        </table>

                        <!-- Birth Information -->
                        <table style="width: 100%; border: none; margin-bottom: 8px;">
                            <tr>
                                <td style="border: none; width: 33%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Date of Birth</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">
                                        @if(!empty($head['dob'])){{ \Carbon\Carbon::parse($head['dob'])->format('F d Y') }}@else - @endif
                                    </div>
                                </td>
                                <td style="border: none; width: 33%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Place of Birth</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['pob'] ?? '-' }}</div>
                                </td>
                                <td style="border: none; width: 33%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Civil Status</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['civil'] ?? '-' }}</div>
                                </td>
                            </tr>
                        </table>

                        <!-- Education and Occupation -->
                        <table style="width: 100%; border: none; margin-bottom: 8px;">
                            <tr>
                                <td style="border: none; width: 33%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Educational Attainment</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['educ'] ?? '-' }}</div>
                                </td>
                                <td style="border: none; width: 33%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Occupation</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['occ'] ?? '-' }}</div>
                                </td>
                                <td style="border: none; width: 33%; vertical-align: top; padding: 3px;">
                                    <div style="font-weight: bold; font-size: 15px; margin-bottom: 2px;">Religion</div>
                                    <div style="border-bottom: 1px solid #000; padding: 4px 0;">{{ $head['religion'] ?? '-' }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
  <!-- FAMILY COMPOSITION SECTION -->
<table class="section" style="width: 100%;">
    <tr>
        <td>
            <div class="section-title">FAMILY COMPOSITION</div>
            
            <table class="data-table family-table" style="font-size: 15px; width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="height: 70px; background-color: #f8f9fa;">
                        <th style="width: 4%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">#</th>
                        <th style="width: 18%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Name</th>
                        <th style="width: 10%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Relation</th>
                        <th style="width: 10%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Date of Birth</th>
                        <th style="width: 6%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Age</th>
                        <th style="width: 6%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Sex</th>
                        <th style="width: 10%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Civil Status</th>
                        <th style="width: 12%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Education</th>
                        <th style="width: 12%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Occupation</th>
                        <th style="width: 7%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Income</th>
                        <th style="width: 5%; text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($family) && count($family) > 0)
                        @foreach($family as $index => $member)
                            <tr style="height: 60px;">
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $index + 1 }}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">
                                    @php
                                        $name = '';
                                        if (isset($member['name']) && !empty($member['name'])) {
                                            $name = $member['name'];
                                        } else {
                                            $name = trim(($member['fname'] ?? '') . ' ' . ($member['mname'] ?? '') . ' ' . ($member['lname'] ?? ''));
                                        }
                                    @endphp
                                    {{ $name }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $member['relation'] ?? ($member['relationship'] ?? '') }}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">
                                    @php
                                        $birthDate = '';
                                        if (!empty($member['birth'])) {
                                            $birthDate = \Carbon\Carbon::parse($member['birth'])->format('F d Y');
                                        } elseif (!empty($member['dob'])) {
                                            $birthDate = \Carbon\Carbon::parse($member['dob'])->format('F d Y');
                                        } elseif (!empty($member['birthdate'])) {
                                            $birthDate = \Carbon\Carbon::parse($member['birthdate'])->format('F d Y');
                                        }
                                    @endphp
                                    {{ $birthDate }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">
                                    @php
                                        $age = '';
                                        if (!empty($member['age'])) {
                                            $age = $member['age'];
                                        } elseif (!empty($member['birth'])) {
                                            $age = \Carbon\Carbon::parse($member['birth'])->age;
                                        } elseif (!empty($member['dob'])) {
                                            $age = \Carbon\Carbon::parse($member['dob'])->age;
                                        } elseif (!empty($member['birthdate'])) {
                                            $age = \Carbon\Carbon::parse($member['birthdate'])->age;
                                        }
                                    @endphp
                                    {{ $age }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $member['sex'] ?? ($member['gender'] ?? '') }}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $member['civil'] ?? ($member['civil_status'] ?? '') }}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $member['educ'] ?? ($member['education'] ?? '') }}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $member['occ'] ?? ($member['occupation'] ?? '') }}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;" class="currency">
                                    @if(!empty($member['income']) || !empty($member['monthly_income']))
                                        {{ number_format($member['income'] ?? $member['monthly_income'] ?? 0, 2) }}
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $member['remarks'] ?? '' }}</td>
                            </tr>
                        @endforeach
                        
                        @php
                            $totalRows = count($family);
                            $emptyRowsNeeded = max(8 - $totalRows, 0);
                        @endphp
                        
                        <!-- Add empty rows to complete 8 total rows -->
                        @for($i = $totalRows + 1; $i <= $totalRows + $emptyRowsNeeded; $i++)
                            <tr style="height: 60px;">
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $i }}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                            </tr>
                        @endfor
                        
                    @else
                        <!-- Empty rows for printing when no data - show exactly 8 rows -->
                        @for($i = 1; $i <= 8; $i++)
                            <tr style="height: 60px;">
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;">{{ $i }}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000ff; padding: 12px;"></td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>

                                <!-- INCOME AND HOUSING INFORMATION -->
                          <div style="margin-top: 8px;">
                                <table style="width: 100%; border: none; font-family: Arial, sans-serif; font-size: 15px;">
                                    <tr>
                                        <td style="width: 25%; border: none; padding: 4px; vertical-align: top;">
                                            <strong>Other Source of Income:</strong><u>{{ $house['net_income'] ?? '__________' }}</u>
                                        </td>
                                        <td style="width: 25%; border: none; padding: 4px; vertical-align: top;">
                                            <strong>Lot:</strong> <u>{{ $house['lot'] ?? '__________' }}</u>
                                        </td>
                                        <td style="width: 25%; border: none; padding: 4px; vertical-align: top;">
                                            <strong>Water:</strong> <u>
                                                @if(!empty($house['water'])){{ number_format($house['water'], 2) }}@else __________ @endif
                                            </u>
                                        </td>
                                        <td style="width: 25%; border: none; padding: 4px; vertical-align: top;">
                                            <strong>Electricity:</strong> <u>
                                                @if(!empty($house['electric'])){{ number_format($house['electric'], 2) }}@else __________ @endif
                                            </u>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding: 4px; vertical-align: top;">
                                            <strong>Total Family Income:</strong> <u>
                                                @if(!empty($house['total_income'])){{ number_format($house['total_income'], 2) }}@else __________ @endif
                                            </u>
                                        </td>
                                        <td style="border: none; padding: 4px; vertical-align: top;">
                                            <strong>House:</strong> <u>{{ $house['house'] ?? '__________' }}</u>
                                        </td>
                                        <td style="border: none; padding: 4px; vertical-align: top;">
                                            <strong>Total Family Net Income:</strong> <u>
                                                @php
                                                    $total = $house['total_income'] ?? 0;
                                                    $other = $house['other_income'] ?? 0;
                                                    $net = $total + $other;
                                                @endphp
                                                @if($net > 0){{ number_format($net, 2) }}@else __________ @endif
                                            </u>
                                        </td>
                                        <td style="border: none; padding: 4px; vertical-align: top;">
                                            <strong>Remarks:</strong> <u>{{ $house['remarks'] ?? '__________' }}</u>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                                <!-- SIGNATURES -->
                             <table class="signature-table" style="width: 100%; margin-top:20px;">
                                    <tr>
                                        <td style="width: 48%;">
                                            <div class="signature-line"></div>
                                            <div><strong>Signature/Thumbmark of Family Head / Client</strong></div>
                                        </td>
                                        <td style="width: 4%;"></td>
                                        <td style="width: 48%;">
                                            <div style="border-bottom: 1px solid #000; padding: 5px 0; min-height: 20px; text-align: center;">
                                                <strong>{{ $worker_info['date_entry'] ?? now()->format('F d Y') }}</strong>
                                            </div>
                                            <div><strong>Date of Entry (MM/DD/YYYY)</strong></div>
                                        </td>
                                    </tr>
                                        <tr>
                                            <td style="width: 48%; padding-top: 15px;">
                                                <div class="signature-line"></div>
                                                <div><strong>Worker: {{ $head['fname'] ?? '' }} {{ $head['lname'] ?? '' }}</strong></div>
                                                <div style="font-size: 15px; margin-top: 2px;">Program Staff Position/Designation</div>
                                            </td>
                                            <td style="width: 4%;"></td>
                                            <td style="width: 48%; padding-top: 15px;">
                                                <div class="signature-line"></div>
                                                <div><strong>Verified: MSWO Officer</strong></div>
                                            </td>
                                        </tr>
                                    </table>
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- RIGHT COLUMN - SOCIAL SERVICES (LARGER) -->
 <!-- RIGHT COLUMN - SOCIAL SERVICES (LARGER) -->
<td class="right-column">
    <div class="social-services-section">
        <div class="social-services-title">SOCIAL SERVICES RECORD</div>
        
        <!-- Social Services Table -->
        <table class="services-table" style="font-size: 15px;">
            <thead>
                <tr>
                    <th style="width: 20%; text-align: center; vertical-align: middle;">Date</th>
                    <th style="width: 35%; text-align: center; vertical-align: middle;">Problem/Need</th>
                    <th style="width: 30%; text-align: center; vertical-align: middle;">Action/Assistance</th>
                    <th style="width: 15%; text-align: center; vertical-align: middle;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Debug: Check what's in rvServiceRecords
                    // Remove this after testing
                    // {{-- var_dump($rvServiceRecords) --}}
                @endphp

                @if(!empty($rvServiceRecords) && is_array($rvServiceRecords) && count($rvServiceRecords) > 0)
                    @foreach($rvServiceRecords as $service)
                        <tr>
                            <td style="height: 60px; text-align: center; vertical-align: middle;">
                                @if(!empty($service['date']))
                                    {{ \Carbon\Carbon::parse($service['date'])->format('F d Y') }}
                                @else
                                    {{ $service['date'] ?? '' }}
                                @endif
                            </td>
                            <td style="height: 60px; text-align: center; vertical-align: middle;">{{ $service['problem'] ?? '' }}</td>
                            <td style="height: 60px; text-align: center; vertical-align: middle;">{{ $service['action'] ?? '' }}</td>
                            <td style="height: 60px; text-align: center; vertical-align: middle;">{{ $service['remarks'] ?? '' }}</td>
                        </tr>
                    @endforeach
                    
                    <!-- Fill remaining rows if less than 8 -->
                    @if(count($rvServiceRecords) < 8)
                        @for($r = count($rvServiceRecords); $r < 8; $r++)
                            <tr>
                                <td style="height: 60px; text-align: center; vertical-align: middle;"></td>
                                <td style="height: 60px; text-align: center; vertical-align: middle;"></td>
                                <td style="height: 60px; text-align: center; vertical-align: middle;"></td>
                                <td style="height: 60px; text-align: center; vertical-align: middle;"></td>
                            </tr>
                        @endfor
                    @endif
                @else
                    <!-- Show the sample data from your JSON string -->
                    <tr>
                        <td style="height: 60px; text-align: center; vertical-align: middle;">November 11 2025</td>
                        <td style="height: 60px; text-align: center; vertical-align: middle;">Mark</td>
                        <td style="height: 60px; text-align: center; vertical-align: middle;">lk</td>
                        <td style="height: 60px; text-align: center; vertical-align: middle;">A. DEAD</td>
                    </tr>
                    <!-- Empty rows for the rest -->
                    @for($r = 1; $r < 8; $r++)
                        <tr>
                            <td style="height: 60px; text-align: center; vertical-align: middle;"></td>
                            <td style="height: 60px; text-align: center; vertical-align: middle;"></td>
                            <td style="height: 60px; text-align: center; vertical-align: middle;"></td>
                            <td style="height: 60px; text-align: center; vertical-align: middle;"></td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>


                        <!-- CLASSIFICATION CODES -->
                        <div style="margin-top: 12px;">
                            <div style="font-weight: bold; margin-bottom: 6px; font-size: 15px;">CLASSIFICATION CODES</div>
                            <table class="code-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">CODE</th>
                                        <th style="width: 50%;">HEALTH CONDITION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>A. Out of School Youth (OSY)</td>
                                        <td>A. DEAD</td>
                                    </tr>
                                    <tr>
                                        <td>B. Solo Parent (SP)</td>
                                        <td>B. INJURED</td>
                                    </tr>
                                    <tr>
                                        <td>C. Person with Disabilities (PWD)</td>
                                        <td>C. MISSING</td>
                                    </tr>
                                    <tr>
                                        <td>D. Senior Citizen (SC)</td>
                                        <td>D. With Illness</td>
                                    </tr>
                                    <tr>
                                        <td>E. Lactating Mother</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>F. Pregnant Mother</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- ESTIMATED COST - LARGER -->
                        <div class="estimated-cost">
                            ESTIMATED COST: Php ____________________
                        </div>

                        <!-- VERIFICATION SECTION - LARGER -->
                        <div class="verification-section">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="padding-bottom: 8px;">
                                        <strong>Date of Entry (MM/DD/YEAR):</strong> ____________________
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Verified:</strong> MSWD Officer ____________________
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <script>
    window.onload = function() {
        window.print();
        setTimeout(function() { 
            // window.close(); // Comment out for testing
        }, 1000);
    };
    </script>
</body>
</html>