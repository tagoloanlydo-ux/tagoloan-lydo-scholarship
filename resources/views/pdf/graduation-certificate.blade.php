<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Graduation Certificates</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 portrait;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #fff;

            position: relative;
        }
        .certificates-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }
        .certificates-table td {
            vertical-align: top;
            padding: 6px; /* gutter between certificates */
            width: 50%; /* two columns per sheet */
        }
        .certificate-cell {
            page-break-inside: avoid;
            break-inside: avoid;
            /* ensure cell doesn't force its own full-page width */
            width: 50%;
        }
        .certificate-container {
            /* make each certificate half the page width (minus padding) and full page height */
          width: 100%;
min-height: auto;
page-break-after: always;
            margin: 0 auto;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 10px solid transparent;
            border-image: linear-gradient(135deg, #7c3aed, #4c1d95, #06b6d4);
            border-image-slice: 1;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: inline-block;
            vertical-align: top;
        }
        .certificate-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #7c3aed;
            background: 
                radial-gradient(circle at 10% 10%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 90% 90%, rgba(6, 182, 212, 0.1) 0%, transparent 50%),
                linear-gradient(135deg, #ffffff 0%, #fdfbff 100%);
            box-shadow: 
                inset 0 0 30px rgba(124, 58, 237, 0.1),
                0 5px 15px rgba(0,0,0,0.05);
        }
        .header-section {
            text-align: center;
            padding: 25px 0 15px 0;
            background: linear-gradient(135deg, #7c3aed, #4c1d95);
            color: white;
            margin-bottom: 5px;
            position: relative;
            overflow: hidden;
        }
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }
        .logo-container {
            margin-bottom: 5px;
        }
        .logo {
            height: 80px;
            width: auto;
            display: inline-block;
        }
        .certificate-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            position: relative;
        }
        .certificate-subtitle {
            font-size: 16px;
            font-weight: 300;
            opacity: 0.95;
            letter-spacing: 0.5px;
            position: relative;
        }
        .seal-container {
            text-align: center;
            margin: 5px 0;
            position: relative;
        }
        .seal {
            display: inline-block;
            width: 100px;
            height: 100px;
            border: 3px solid #7c3aed;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #f0f9ff 0%, #e0f2fe 100%);
            line-height: 100px;
            font-size: 14px;
            font-weight: bold;
            color: #7c3aed;
            box-shadow: 
                0 5px 15px rgba(124, 58, 237, 0.2),
                inset 0 0 15px rgba(255,255,255,0.8);
            position: relative;
            z-index: 2;
        }
        .seal::before {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 28px;
            color: #10b981;
        }
        .seal::after {
            content: 'LYDO';
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            font-weight: bold;
            color: #7c3aed;
        }
        .content {
            padding: 0 50px;
            text-align: center;
            position: relative;
        }
        .congratulations {
            font-size: 18px;
            color: #7c3aed;
            margin-bottom: 25px;
            font-weight: 300;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
        }
        .congratulations::after {
            content: '';
            display: block;
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #7c3aed, transparent);
            margin: 10px auto;
        }
        .scholar-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            margin: 20px 0;
            padding: 15px 30px;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 
                0 5px 15px rgba(0,0,0,0.08),
                inset 0 1px 0 rgba(255,255,255,0.8);
            position: relative;
            z-index: 1;
        }
        .scholar-name::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(135deg, #7c3aed, #06b6d4);
            border-radius: 12px;
            z-index: -1;
            opacity: 0.1;
        }
        .certificate-text {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin: 20px 0;
            font-style: italic;
            position: relative;
        }
        .details {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            padding: 20px;
            margin: 25px 0;
            border-radius: 10px;
            border-left: 4px solid #7c3aed;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: left;
            position: relative;
            overflow: hidden;
        }
        .details::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), transparent);
            border-radius: 0 0 0 60px;
        }
        .detail-item {
            margin: 10px 0;
            font-size: 13px;
            color: #374151;
            display: flex;
            align-items: center;
        }
        .detail-item strong {
            color: #7c3aed;
            min-width: 150px;
            font-weight: 600;
        }
        .detail-item::before {
           
            color: #7c3aed;
            margin-right: 8px;
            font-weight: bold;
        }
        .signatures {
    margin-top: 35px;
    padding: 0 20px;
    position: relative;
}

.signature-table {
    width: 100%;
    border-collapse: collapse;
}

.signature-cell {
    width: 50%;
    text-align: center;
    vertical-align: top;
    padding: 0 10px;
}

.signature {
    text-align: center;
}

.signature-line {
    width: 80px;
    border-top: 1px solid #7c3aed;
    margin: 20px auto 5px auto;
    position: relative;
}

.signature-line::before {
    content: '';
    position: absolute;
    top: -4px;
    left: 50%;
    transform: translateX(-50%);
    width: 8px;
    height: 8px;
    background: #7c3aed;
    border-radius: 50%;
}

.signature-name {
    font-weight: bold;
    color: #7c3aed;
    font-size: 10px;
    margin-top: 5px;
}

.signature-title {
    font-size: 8px;
    color: #64748b;
    font-style: italic;
}
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #64748b;
            font-size: 11px;
            padding: 15px;
            border-top: 1px solid #e2e8f0;
            background: rgba(248, 250, 252, 0.8);
        }
        .certificate-id {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #7c3aed;
            letter-spacing: 0.5px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(124, 58, 237, 0.03);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
            pointer-events: none;
        }
        .decoration-corner {
            position: absolute;
            width: 50px;
            height: 50px;
        }
        .corner-tl {
            top: 15px;
            left: 15px;
            border-top: 2px solid #7c3aed;
            border-left: 2px solid #7c3aed;
        }
        .corner-tr {
            top: 15px;
            right: 15px;
            border-top: 2px solid #7c3aed;
            border-right: 2px solid #7c3aed;
        }
        .corner-bl {
            bottom: 15px;
            left: 15px;
            border-bottom: 2px solid #7c3aed;
            border-left: 2px solid #7c3aed;
        }
        .corner-br {
            bottom: 15px;
            right: 15px;
            border-bottom: 2px solid #7c3aed;
            border-right: 2px solid #7c3aed;
        }
    </style>
</head>
<body>
    <table class="certificates-table">
        <tr>
            @foreach($graduatedScholars as $index => $scholar)
            <td class="certificate-cell">
                <div class="certificate-container">
                    <!-- Decorative Corners -->
                    <div class="decoration-corner corner-tl"></div>
                    <div class="decoration-corner corner-tr"></div>
                    <div class="decoration-corner corner-bl"></div>
                    <div class="decoration-corner corner-br"></div>
                    
                    <!-- Watermark -->
                    <div class="watermark">LYDO SCHOLARSHIP</div>
                    
                    <div class="certificate-border">
                        <div class="header-section">
                            <div class="seal-container">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Picture3.png'))) }}" alt="LYDO Logo" class="logo">
                            </div>
                            <div class="certificate-title">Certificate of Completion</div>
                            <div class="certificate-subtitle">LYDO Scholarship Program</div>
                        </div>
                        
                        <div class="content">
                            <div class="congratulations">This Certificate is Proudly Awarded To</div>
                            
                            <div class="scholar-name">
                                {{-- Format the name: Last Name, First Name Middle Initial and Suffix --}}
                                @php
                                    $name = $scholar['name'];
                                    $nameParts = explode(' ', $name);
                                    $formattedName = '';
                                    
                                    // Check if name has at least 2 parts (last name and first name)
                                    if (count($nameParts) >= 2) {
                                        $lastName = ucfirst(strtolower($nameParts[count($nameParts) - 1])); // Last part is last name
                                        $firstName = ucfirst(strtolower($nameParts[0])); // First part is first name
                                        
                                        // Get middle name parts (everything between first and last)
                                        $middleParts = array_slice($nameParts, 1, count($nameParts) - 2);
                                        $middleInitial = '';
                                        
                                        if (!empty($middleParts)) {
                                            // Get the first letter of the first middle name
                                            $middleInitial = ' ' . strtoupper(substr($middleParts[0], 0, 1)) . '.';
                                        }
                                        
                                        // Check for suffix
                                        $suffix = '';
                                        $lastPart = end($nameParts);
                                        $suffixes = ['jr', 'sr', 'ii', 'iii', 'iv'];
                                        
                                        if (in_array(strtolower($lastPart), $suffixes)) {
                                            $suffix = ' ' . ucfirst(strtolower($lastPart));
                                            // Remove suffix from last name
                                            $lastName = ucfirst(strtolower($nameParts[count($nameParts) - 2]));
                                        }
                                        
                                        $formattedName = $lastName . ', ' . $firstName . $middleInitial . $suffix;
                                    } else {
                                        // If name doesn't follow expected format, use original with proper capitalization
                                        $formattedName = ucwords(strtolower($name));
                                    }
                                @endphp
                                {{ $formattedName }}
                            </div>
                            
                            <div class="certificate-text">
                                in recognition of outstanding dedication and successful completion
                                of the LYDO Scholarship Program, demonstrating exceptional
                                academic excellence and commitment to personal growth.
                            </div>
                            
                            <div class="details">
                                <div class="detail-item">
                                    <strong>Scholar ID:</strong> {{ $scholar['scholar_id'] }}
                                </div>
                                <div class="detail-item">
                                    <strong>Educational Institution:</strong> {{ $scholar['school'] }}
                                </div>
                                <div class="detail-item">
                                    <strong>Course Program:</strong> {{ $scholar['course'] }}
                                </div>
                                <div class="detail-item">
                                    <strong>Year Level Completed:</strong> {{ $scholar['year_level'] }}
                                </div>
                                <div class="detail-item">
                                    <strong>Date of Graduation:</strong> {{ now()->format('F d, Y') }}
                                </div>
                            </div>
                            
                            <div class="certificate-text">
                                This achievement stands as a testament to perseverance, diligence,
                                and unwavering commitment to educational excellence.
                            </div>
                        </div>
                        
<div class="signatures">
    <table class="signature-table">
        <tr>
            <td class="signature-cell">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $lydoAdminName ?? 'LYDO Program Director' }}</div>
                    <div class="signature-title">LYDO Scholarship Program</div>
                </div>
            </td>
            <td class="signature-cell">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $mayorStaffName ?? 'City Mayor' }}</div>
                    <div class="signature-title">City Government</div>
                </div>
            </td>
        </tr>
    </table>
</div>
                        
                        <div class="footer">
                            <div class="certificate-id">
                                Certificate ID: GRAD-{{ $scholar['scholar_id'] }}-{{ now()->format('Ymd') }}
                            </div>
                            <div style="margin-top: 5px; font-size: 9px; color: #94a3b8;">
                                Issued electronically and verifiable through LYDO Scholarship Portal
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            
            @if(($index + 1) % 2 == 0 && !$loop->last)
                </tr><tr>
            @endif
            @endforeach
        </tr>
    </table>
</body>
</html>