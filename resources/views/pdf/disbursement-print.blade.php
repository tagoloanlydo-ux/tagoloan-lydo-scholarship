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

        body {
            background: #f4f6f9;
            padding: 30px;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            background: #fff;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
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
            height: 100 px;
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
            background: #324b7a;
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

        img.signature-img {
            max-width: 90px;
            max-height: 45px;
        }
    </style>
</head>

<body>
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

                    @if(request('search') || request('barangay') || request('academic_year') || request('semester'))
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
                    </div>
                    @endif
                </td>

                <td class="logo">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture3.png'))) }}">
                </td>
            </tr>
        </table>


        <!-- TABLE WITH OUTLINE -->
        @if($signedDisbursements->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Barangay</th>
                    <th>Semester</th>
                    <th>Academic Year</th>
                    <th class="text-center">Amount</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Signature</th>
                </tr>
            </thead>

            <tbody>
                @foreach($signedDisbursements as $index => $disburse)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $disburse->full_name }}</td>
                    <td class="text-center">{{ $disburse->applicant_brgy }}</td>
                    <td class="text-center">{{ $disburse->disburse_semester }}</td>
                    <td class="text-center" >{{ $disburse->disburse_acad_year }}</td>
                    <td class="text-center">{{ number_format($disburse->disburse_amount, 0) }}</td>
                    <td class="date-column text-center">{{ \Carbon\Carbon::parse($disburse->disburse_date)->format('M d, Y') }}</td>
                    <td class="text-center">
                        @if($disburse->disburse_signature)
                            <img src="{{ $disburse->disburse_signature }}" class="signature-img">
                        @else
                            Signed
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <div class="text-center" style="padding: 40px;">
            <h3 style="color: #555;">No Signed Disbursements Found</h3>
            <p style="color: #777;">No data matches the current filter criteria.</p>
        </div>
        @endif


        <div class="footer">
            Report generated by LYDO Scholarship Management System <br>
            {{ \Carbon\Carbon::now()->format('F d, Y — h:i A') }}
        </div>

    </div>
</body>
</html>
