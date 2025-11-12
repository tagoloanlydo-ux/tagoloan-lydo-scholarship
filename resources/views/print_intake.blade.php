<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Intake Sheet - PDF Print View</title>
    <style>
        /* PDF Print View - print-ready template optimized for exporting to PDF */
        /* use US Legal paper in landscape and tighten margins to fit one page */
        @page { size: legal landscape; margin: 6mm 6mm; }
        html, body { height:100%; overflow:hidden; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px; /* reduced font to fit one page */
            color: #000;
            margin: 0;
            padding: 6px;
            -webkit-print-color-adjust: exact;
        }
        
        /* Markdown Preview Concept - INVISIBLE IMPLEMENTATION */
        .data-rendered { /* This class indicates rendered data fields */
            /* No visual changes - maintains original appearance */
        }
        
        .currency {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .page-break {
            page-break-before: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Ensure background colors print */
        @media print {
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            th {
                background-color: #f0f0f0 !important;
            }
        }
        
        /* overall two-column layout */
        .wrapper {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }
        
        .left { width: 68%; }
        .right { width: 32%; }

        /* header with logo on left */
        .top-header {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-bottom: 4px;
        }
        
        .logo {
            width:56px;
            height:56px;
            background: transparent;
        }
        
        .org {
            flex: 1;
            text-align: left;
            line-height: 1;
        }
        
        .org h1 { font-size:13px; margin:0; font-weight:700; }
        .org h2 { font-size:11px; margin:0; font-weight:700; }
        
        .serial {
            font-size:10px;
            text-align:right;
        }

        .small-note { font-size:9px; }

        /* form blocks */
        .card { border:1px solid #ffffffff; padding:4px; margin-bottom:6px; }
        
        .checkbox-row { display:flex; gap:12px; align-items:center; margin-bottom:4px; }
        .checkbox-row label { display:flex; gap:6px; align-items:center; font-weight:600; font-size:10px; }

        .grid-3 {
            display:flex;
            gap:8px;
        }
        
        .col { flex:1; }

        table { width:100%; border-collapse: collapse; font-size:10px; }
        th, td { border:1px solid #000; padding:3px 4px; vertical-align:middle; }
        th { background:#f0f0f0; font-weight:700; text-align:left; }

        /* family composition table — mimic large grid with numbered rows */
        .family-table th, .family-table td { border:1px solid #000; padding:3px; font-size:9.5px; }
        .family-table thead th { background:#f7f7f7; font-weight:700; text-align:center; }
        .family-table tbody tr { height:20px; }

        /* right column Social Services table */
        .ss-table th, .ss-table td { border:1px solid #000; padding:4px; font-size:10px; }
        .ss-table thead th { background:#f7f7f7; text-align:center; }
        .ss-table tbody tr { height:26px; }

        .code-table td, .code-table th { border:1px solid #000; padding:3px; font-size:9.5px; }

        /* signature lines */
        .sig-row { display:flex; justify-content:space-between; gap:8px; margin-top:6px; }
        .sig-box { width:48%; text-align:center; }
        .sig-line { border-bottom:1px solid #000; height:36px; margin-bottom:4px; }

        /* reduce spacing of inline blocks */
        input[readonly] { padding:2px; font-size:10px; }

        @media print {
            .no-print { display:none !important; }
            /* tighten a bit more when printing */
            body { padding:4px; font-size:10px; }
        }
    </style>
</head>
<body>
    <!-- MARKDOWN PREVIEW CONCEPT: RENDERED DATA VIEW -->
    <!-- This entire template is a "rendered preview" of the form data -->
    
    <div class="wrapper">
        <!-- LEFT: RENDERED INTAKE FORM DATA -->
        <div class="left">
            <div class="top-header">
                <div class="logo">
                    <!-- RENDERED LOGO -->
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture2.png'))) }}" alt="logo" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <div class="org">
                    <h1>Republic of the Philippines</h1>
                    <h2>Municipality of Tagoloan, Lanao del Norte</h2>
                    <div class="small-note">MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</div>
                    <div style="margin-top:6px;font-weight:700;font-size:13px;text-align:left;">FAMILY INTAKE SHEET</div>
                    <div style="font-size:11px;font-weight:600;color:#333;margin-top:2px;">PDF Print View — Rendered Data Preview</div>
                </div>
                <div class="serial">
                    <div>Serial No.:</div>
                    <div style="margin-top:6px;font-weight:700;">{{ $serialNumber ?? '__________' }}</div>
                </div>
            </div>

            <div class="card">
                <div class="checkbox-row">
                    <label><input type="checkbox" disabled {{ (isset($head['within_tagoloan']) && $head['within_tagoloan']) ? 'checked' : '' }}> Within Tagoloan</label>
                    <label><input type="checkbox" disabled {{ (isset($head['outside_tagoloan']) && $head['outside_tagoloan']) ? 'checked' : '' }}> Outside Tagoloan</label>
                    <label style="margin-left:auto;"><input type="checkbox" disabled {{ ($head['_4ps'] ?? '') === 'Yes' ? 'checked' : '' }}> 4P's Beneficiary</label>
                    <label><strong>IP:</strong> ____________________</label>
                </div>

                <div class="grid-3" style="margin-top:6px;">
                    <div class="col">
                        <div style="font-weight:700;margin-bottom:4px;">HEAD OF THE FAMILY:</div>
                        <div style="display:flex;gap:6px;margin-bottom:8px;">
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:9px;margin-bottom:2px;">SURNAME</div>
                                <input type="text" value="{{ ($head['lname'] ?? '') }}" style="width:100%;border:none;border-bottom:1px solid #000;padding:3px;box-sizing:border-box;" readonly class="data-rendered">
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:9px;margin-bottom:2px;">FIRST NAME</div>
                                <input type="text" value="{{ ($head['fname'] ?? '') }}" style="width:100%;border:none;border-bottom:1px solid #000;padding:3px;box-sizing:border-box;" readonly class="data-rendered">
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:700;font-size:9px;margin-bottom:2px;">MIDDLE NAME</div>
                                <input type="text" value="{{ ($head['mname'] ?? '') }}" style="width:100%;border:none;border-bottom:1px solid #000;padding:3px;box-sizing:border-box;" readonly class="data-rendered">
                            </div>
                        </div>
                    </div>
                    <div class="col" style="max-width:120px;">
                        <div style="font-weight:700;margin-bottom:6px;">Sex / Age</div>
                        <div style="display:flex;gap:6px;">
                            <div style="border:1px solid #000;padding:6px;text-align:center;" class="data-rendered">{{ $head['sex'] ?? '-' }}</div>
                            <div style="border:1px solid #000;padding:6px;text-align:center;" class="data-rendered">{{ $head['age'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div style="margin-top:8px;">
                    <div style="display:flex;gap:10px;">
                        <div style="flex:2;">
                            <div style="font-weight:700;">Address</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">{{ $head['address'] ?? '-' }}</div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700;">Zone</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">{{ $head['zone'] ?? '-' }}</div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700;">Barangay</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">{{ $head['barangay'] ?? '-' }}</div>
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;margin-top:8px;">
                        <div style="flex:1;">
                            <div style="font-weight:700;">Date of Birth</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">
                                @if(!empty($head['dob'])){{ \Carbon\Carbon::parse($head['dob'])->format('m/d/Y') }}@else - @endif
                            </div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700;">Place of Birth</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">{{ $head['pob'] ?? '-' }}</div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700;">Civil Status</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">{{ $head['civil'] ?? '-' }}</div>
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;margin-top:8px;">
                        <div style="flex:1;">
                            <div style="font-weight:700;">Educational Attainment</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">{{ $head['educ'] ?? '-' }}</div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700;">Occupation</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">{{ $head['occ'] ?? '-' }}</div>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700;">Religion</div>
                            <div style="border-bottom:1px solid #000;padding:4px;" class="data-rendered">{{ $head['religion'] ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RENDERED FAMILY COMPOSITION DATA -->
            <div class="card">
                <div style="font-weight:700;margin-bottom:6px;">FAMILY COMPOSITION</div>
                <table class="family-table">
                    <thead>
                        <tr>
                            <th style="width:28px;">#</th>
                            <th>Name</th>
                            <th style="width:78px;">Relation</th>
                            <th style="width:78px;">Date of Birth</th>
                            <th style="width:40px;">Age</th>
                            <th style="width:40px;">Sex</th>
                            <th style="width:70px;">Civil Status</th>
                            <th>Educational Attainment</th>
                            <th>Occupation</th>
                            <th style="width:70px;">Income</th>
                            <th style="width:70px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 12; $i++)
                            @php $member = $family[$i-1] ?? null; @endphp
                            <tr>
                                <td style="text-align:center;">{{ $i }}</td>
                                <td class="data-rendered">{{ $member['name'] ?? '' }}</td>
                                <td class="data-rendered">{{ $member['relation'] ?? '' }}</td>
                                <td style="text-align:center;" class="data-rendered">
                                    @if(!empty($member['birth'])){{ \Carbon\Carbon::parse($member['birth'])->format('m/d/Y') }}@endif
                                </td>
                                <td style="text-align:center;" class="data-rendered">{{ $member['age'] ?? '' }}</td>
                                <td style="text-align:center;" class="data-rendered">{{ $member['sex'] ?? '' }}</td>
                                <td style="text-align:center;" class="data-rendered">{{ $member['civil'] ?? '' }}</td>
                                <td class="data-rendered">{{ $member['educ'] ?? '' }}</td>
                                <td class="data-rendered">{{ $member['occ'] ?? '' }}</td>
                                <td style="text-align:right;" class="data-rendered currency">
                                    @if(!empty($member['income']))₱{{ number_format($member['income'],2) }}@endif
                                </td>
                                <td style="text-align:center;" class="data-rendered">{{ $member['remarks'] ?? '' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <div style="flex:1;">
                        <div style="display:flex;gap:6px;">
                            <strong>Total Family Income:</strong>
                            <div style="border-bottom:1px solid #000;flex:1;padding:3px;" class="data-rendered">
                                @if(!empty($house['total_income']))₱{{ number_format($house['total_income'],2) }}@endif
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;margin-top:6px;">
                            <strong>Other Source of Income:</strong>
                            <div style="border-bottom:1px solid #000;flex:1;padding:3px;" class="data-rendered">{{ $house['other_income'] ?? '' }}</div>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;gap:6px;">
                            <strong>House:</strong>
                            <div style="border-bottom:1px solid #000;flex:1;padding:3px;" class="data-rendered">{{ $house['house'] ?? '' }}</div>
                        </div>
                        <div style="display:flex;gap:6px;margin-top:6px;">
                            <strong>Lot:</strong>
                            <div style="border-bottom:1px solid #000;flex:1;padding:3px;" class="data-rendered">{{ $house['lot'] ?? '' }}</div>
                        </div>
                        <div style="display:flex;gap:6px;margin-top:6px;">
                            <strong>Water:</strong>
                            <div style="border-bottom:1px solid #000;flex:1;padding:3px;" class="data-rendered">
                                @if(!empty($house['water']))₱{{ number_format($house['water'],2) }}@endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sig-row">
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <div><strong>Signature/Thumbmark of Family Head / Client</strong></div>
                    </div>
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <div><strong>Worker: Program Staff - Position/Designation</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: RENDERED SOCIAL SERVICES DATA -->
        <div class="right">
            <div class="card" style="padding:6px 8px;">
                <div style="text-align:center;font-weight:700;margin-bottom:6px;font-size:12px;">SOCIAL SERVICES RECORD</div>
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th style="width:12%;">DATE</th>
                            <th style="width:42%;">PROBLEM PRESENTED<br><small>(to be filled by Support Staff)</small></th>
                            <th style="width:36%;">ASSISTANCE PROVIDED<br><small>(to be filled by Program Implementer)</small></th>
                            <th style="width:10%;">REMARKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($r=0;$r<10;$r++)
                            <tr style="height:34px;">
                                <td></td><td></td><td></td><td></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <div style="margin-top:8px;">
                    <table class="code-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width:42%;">CODE</th>
                                <th style="width:58%;">HEALTH CONDITION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>A. Out of School Youth (OSY)</td><td>A. DEAD</td></tr>
                            <tr><td>B. Solo Parent (SP)</td><td>B. INJURED</td></tr>
                            <tr><td>C. Person with Disabilities (PWD)</td><td>C. MISSING</td></tr>
                            <tr><td>D. Senior Citizen (SC)</td><td>D. With Illness</td></tr>
                            <tr><td>E. Lactating Mother</td><td></td></tr>
                            <tr><td>F. Pregnant Mother</td><td></td></tr>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:8px;font-weight:700;">ESTIMATED COST: Php ____________________</div>

                <div style="margin-top:12px;">
                    <div style="display:flex;justify-content:space-between;">
                        <div style="text-align:left;font-size:11px;">Date of Entry (MM/DD/ YEAR): ____________________</div>
                        <div style="text-align:right;font-size:11px;">Verified: MSWD Officer ____________________</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
window.onload = function(){
    window.print();
    setTimeout(function(){ window.close(); }, 600);
};
</script>
</body>
</html>