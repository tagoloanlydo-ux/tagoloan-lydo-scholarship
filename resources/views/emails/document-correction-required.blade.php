<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Correction Required - LYDO Scholarship</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', Arial, sans-serif;
            line-height: 1.7;
            color: #374151;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #3b82f6, #8b5cf6);
        }
        
        .logo {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .tagline {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
            margin-bottom: 20px;
        }
        
        .header h2 {
            font-size: 24px;
            font-weight: 600;
            margin-top: 15px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 25px;
            color: #1f2937;
        }
        
        .intro-text {
            font-size: 16px;
            margin-bottom: 30px;
            color: #6b7280;
        }
        
        .document-list {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .document-list h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .document-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .document-item:hover {
            border-color: #8b5cf6;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
        }
        
        .document-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .document-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 16px;
        }
        
        .badge {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .reason {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }
        
        .reason-title {
            font-weight: 600;
            color: #92400e;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .reason-text {
            color: #b45309;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .steps-section {
            background: linear-gradient(135deg, #dbeafe, #e0e7ff);
            border: 1px solid #c7d2fe;
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .steps-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .steps-list {
            list-style: none;
            counter-reset: step-counter;
        }
        
        .steps-list li {
            counter-increment: step-counter;
            margin-bottom: 15px;
            padding-left: 45px;
            position: relative;
            font-size: 15px;
        }
        
        .steps-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background: #4f46e5;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .requirements {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .requirements-title {
            font-size: 18px;
            font-weight: 600;
            color: #065f46;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .requirements-list {
            list-style: none;
        }
        
        .requirements-list li {
            margin-bottom: 12px;
            padding-left: 30px;
            position: relative;
            font-size: 15px;
        }
        
        .requirements-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            top: 0;
            color: #10b981;
            font-weight: 600;
        }
        
        .urgent-notice {
            background: linear-gradient(135deg, #fef3c7, #fed7aa);
            border: 1px solid #fdba74;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        
        .urgent-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .urgent-text {
            font-weight: 600;
            color: #92400e;
            font-size: 16px;
        }
        
        .contact-info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        
        .contact-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
        }
        
        .closing {
            margin: 30px 0;
            text-align: center;
        }
        
        .signature {
            font-weight: 600;
            color: #1f2937;
            margin-top: 10px;
        }
        
        .footer {
            background: #1f2937;
            color: #9ca3af;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            line-height: 1.6;
        }
        
        .footer a {
            color: #60a5fa;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 20px 10px;
            }
            
            .email-container {
                border-radius: 12px;
            }
            
            .header, .content {
                padding: 25px 20px;
            }
            
            .logo {
                font-size: 24px;
            }
            
            .header h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">LYDO SCHOLARSHIP</div>
            <div class="tagline">PARA SA KABATAAN, PARA SA KINABUKASAN</div>
            <h2>Document Correction Required</h2>
        </div>
        
        <div class="content">
            <div class="greeting">Dear {{ $applicant_fname }} {{ $applicant_lname }},</div>
            
            <p class="intro-text">Your scholarship renewal application has been reviewed and requires some document corrections to proceed with processing.</p>

            <div class="document-list">
                <h3>📋 Documents Requiring Correction</h3>
                <ul>
                    @foreach($bad_documents as $document)
                        <li class="document-item">
                            <div class="document-header">
                                <div class="document-name">{{ $document['name'] }}</div>
                                <span class="badge">Action Required</span>
                            </div>
                            <div class="reason">
                                <div class="reason-title">📝 Required Corrections</div>
                                <div class="reason-text">{{ $document['reason'] }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="steps-section">
                <h3 class="steps-title">🛠️ How to Update Your Documents</h3>
                <ol class="steps-list">
                    <li><strong>Log in</strong> to your LYDO Scholarship account at <strong>https://scholarship.lydo.gov.ph</strong></li>
                    <li>Navigate to the <strong>"Renewal"</strong> section in your dashboard</li>
                    <li>Click on <strong>"Update Application"</strong> button</li>
                    <li>Re-upload the corrected documents for the items mentioned above</li>
                    <li>Review all information and <strong>submit</strong> your updated application</li>
                </ol>
            </div>

            <div class="requirements">
                <h3 class="requirements-title">📄 Document Requirements</h3>
                <ul class="requirements-list">
                    <li>All documents must be <strong>clear, readable, and high-quality</strong></li>
                    <li>Accepted formats: <strong>PDF, JPG, PNG</strong> (PDF preferred)</li>
                    <li>Maximum file size: <strong>5MB per document</strong></li>
                    <li>Ensure all text and details are <strong>visible and legible</strong></li>
                    <li>Documents must be <strong>current and valid</strong> (not expired)</li>
                </ul>
            </div>

            <div class="urgent-notice">
                <div class="urgent-icon">⏰</div>
                <div class="urgent-text">Please complete these corrections within 7 days to avoid processing delays in your scholarship renewal.</div>
            </div>

            <div class="contact-info">
                <div class="contact-title">Need Assistance?</div>
                <p>Email: tagoloanlydo@gmail.com<br>
            
                Office Hours: Monday-Friday, 8:00 AM - 5:00 PM</p>
            </div>

            <div class="closing">
                <p>Thank you for your prompt attention to this matter. We're here to support your educational journey.</p>
                <div class="signature">
                    Best regards,<br>
                    <strong>LYDO Scholarship Review Committee</strong>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>LYDO Scholarship Office • Tagoloan, Philippines</p>
            <p>© 2024 LYDO Scholarship Program. All rights reserved.</p>
        </div>
    </div>
</body>
</html>