<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disbursement Report - Print</title>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header .subtitle {
            font-size: 16px;
            margin: 5px 0;
            font-weight: normal;
        }

        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .filters strong {
            display: inline-block;
            margin-right: 10px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }

        th {
            border: 1px solid #333;
            padding: 8px 6px;
            background-color: #343a40;
            color: white;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            border: 1px solid #333;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .total-row td {
            border-top: 2px solid #333;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #666;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            font-size: 14px;
            color: #666;
            font-style: italic;
        }

        @media print {
            body {
                padding: 15px;
            }

            .header {
                margin-bottom: 20px;
            }

            table {
                font-size: 10px;
            }

            th, td {
                padding: 6px 4px;
            }

            .footer {
                margin-top: 30px;
                page-break-inside: avoid;
            }
        }

        @page {
            margin: 0.5in;
            size: A4 landscape;
        }
    </style>
</head>
<body>

<div class="header" style="text-align: center; margin-bottom: 30px; border-bottom: 3px solid #333; padding-bottom: 20px;">
    <h1 style="margin: 0 0 10px 0; font-size: 24px; font-weight: bold; text-transform: uppercase;">
        LYDO Scholarship Disbursement Report
    </h1>
    <div class="subtitle" style="font-size: 16px; margin: 5px 0;">Tagoloan, Misamis Oriental</div>
    <div class="subtitle" style="font-size: 16px; margin: 5px 0;">Local Youth Development Office</div>
</div>

@if(!empty($filters))
<div class="filters">
    <strong>Applied Filters:</strong>
    {{ implode(' | ', $filters) }}
</div>
@endif

@if($disbursements->count() > 0)
<table>
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 25%;">Scholar Name</th>
            <th style="width: 15%;">Barangay</th>
            <th style="width: 12%;">Academic Year</th>
            <th style="width: 13%;">Semester</th>
            <th style="width: 15%;">Amount</th>
            <th style="width: 15%;">Disbursement Date</th>
            <th style="width: 15%;">Signature</th>
        </tr>
    </thead>
    <tbody>
        @foreach($disbursements as $index => $disbursement)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $disbursement->full_name }}</td>
            <td class="text-center">{{ $disbursement->applicant_brgy }}</td>
            <td class="text-center">{{ $disbursement->disburse_acad_year }}</td>
            <td class="text-center">{{ $disbursement->disburse_semester }}</td>
            <td class="text-right">₱{{ number_format($disbursement->disburse_amount, 2) }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($disbursement->disburse_date)->format('M d, Y') }}</td>
            <td style="padding: 15px 6px; text-align: center;">
                <div style="border-bottom: 1px solid #ffffff; width: 120px; margin: 0 auto;"></div>
            </td>
        </tr>
        @endforeach

        <tr class="total-row">
            <td colspan="6" class="text-right" style="font-weight: bold; font-size: 12px;">TOTAL AMOUNT:</td>
            <td class="text-right" style="font-weight: bold; font-size: 12px;">
                ₱{{ number_format($disbursements->sum('disburse_amount'), 2) }}
            </td>
            <td class="text-center" style="font-weight: bold; font-size: 12px;">-</td>
        </tr>
    </tbody>
</table>

<!-- Signature Section -->
<div class="signature-section" style="margin-top: 60px; page-break-inside: avoid;">
    <table style="width: 100%; border: none; margin-top: 40px;">
        <tr>
            <td style="width: 33%; text-align: center; border: none; padding: 20px;">
                <div style="border-bottom: 1px solid #333; width: 200px; margin: 0 auto 10px auto;"></div>
                <p style="margin: 5px 0; font-size: 11px; font-weight: bold;">Verified By:</p>
                <p style="margin: 5px 0; font-size: 10px;">LYDO Administrator</p>
                <p style="margin: 5px 0; font-size: 10px;">Date: ________________</p>
            </td>
            <td style="width: 33%; text-align: center; border: none; padding: 20px;">
                <div style="border-bottom: 1px solid #333; width: 200px; margin: 0 auto 10px auto;"></div>
                <p style="margin: 5px 0; font-size: 11px; font-weight: bold;">Approved By:</p>
                <p style="margin: 5px 0; font-size: 10px;">Municipal Mayor</p>
                <p style="margin: 5px 0; font-size: 10px;">Date: ________________</p>
            </td>
        </tr>
    </table>
</div>

<!-- ✅ ONLY the NEW footer remains -->
<div class="footer" style="text-align: center; margin-top: 20px; font-size: 11px;">
    <p style="margin: 5px 0; font-weight: bold;">Lydo Scholarship System</p>
    <p style="margin: 5px 0;">
        Generated on: {{ date('F d, Y \a\t h:i A') }}
    </p>
    <p style="margin: 5px 0;">
        Page <span class="page-number"></span>
    </p>
</div>

@else
<div class="no-data">
    <p>No disbursement records found matching the specified criteria.</p>
    <p>Please adjust your filters and try again.</p>
</div>
@endif

<script>
    window.onload = function() {
        window.print();
    };

    window.onbeforeprint = function() {
        const spans = document.querySelectorAll('.page-number');
        spans.forEach((span, index) => {
            span.textContent = index + 1;
        });
    };
</script>

</body>
</html>
