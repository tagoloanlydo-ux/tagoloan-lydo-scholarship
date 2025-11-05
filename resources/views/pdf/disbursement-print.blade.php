<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LYDO Scholarship Disbursement Report - Print</title>
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

        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
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

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }

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

        @page {
            margin: 0.5in;
            size: A4 landscape;
        }
    </style>
</head>
<body>

<div class="header">
    <div style="font-size: 18px; font-weight: bold; margin: 5px 0;">Republic of the Philippines</div>
    <div style="font-size: 16px; font-weight: bold; margin: 5px 0;">Province of Misamis Oriental</div>
    <div style="font-size: 16px; font-weight: bold; margin: 5px 0;">Municipality of Tagoloan</div>
    <div style="font-size: 16px; font-weight: bold; margin: 5px 0;">Local Youth Development Office</div>
    <h1>LYDO Scholarship Disbursement Report</h1>
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
            <th style="width: 20%;">Scholar Name</th>
            <th style="width: 12%;">Barangay</th>
            <th style="width: 10%;">Academic Year</th>
            <th style="width: 11%;">Semester</th>
            <th style="width: 12%;">Amount</th>
            <th style="width: 12%;">Disbursement Date</th>
            <th style="width: 18%;">Signature</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalAmount = 0;
        @endphp
        
        @foreach($disbursements as $index => $disbursement)
        @php
            $totalAmount += $disbursement->disburse_amount;
        @endphp
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-left">{{ $disbursement->full_name }}</td>
            <td class="text-center">{{ $disbursement->applicant_brgy }}</td>
            <td class="text-center">{{ $disbursement->disburse_acad_year }}</td>
            <td class="text-center">{{ $disbursement->disburse_semester }}</td>
            <td class="text-right">₱{{ number_format($disbursement->disburse_amount, 2) }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($disbursement->disburse_date)->format('M d, Y') }}</td>
            <td class="text-center">{{ $disbursement->disburse_signature ? 'Signed' : 'Unsigned' }}</td>
        @endforeach

        <tr style="background-color: #f8f9fa; font-weight: bold;">
            <td colspan="5" class="text-right">TOTAL AMOUNT:</td>
            <td class="text-right">₱{{ number_format($totalAmount, 2) }}</td>
            <td class="text-center">-</td>
        </tr>
    </tbody>
</table>

<div class="footer">
    <p style="margin: 5px 0; font-weight: bold;">Lydo Scholarship System</p>
    <p style="margin: 5px 0;">Generated on: {{ date('F d, Y \a\t h:i A') }}</p>
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
</script>

</body>
</html>