<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Update Required - LYDO Scholarship</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background-color: #6B46C1;
            color: white;
            padding: 15px;
            text-align: left;
            border-radius: 5px 5px 0 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            font-size: 14px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
.button {
    display: inline-block;
    background-color: #ffffffff; /* lavender violet */
    color: white; /* white text */
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 5px;
    margin: 20px 0;
    font-weight: bold;
    border: 2px solid black; /* ✅ black outline/border */
}

.button:hover {
    background-color: #553C9A; /* darker lavender on hover */
}

.button:focus {
    outline: 2px solid black; /* ✅ black outline when focused (clicked/tabbed) */
    outline-offset: 2px;
}

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .document-list {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .document-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .document-list li {
            margin-bottom: 10px;
            padding: 10px;
            border-left: 4px solid #6B46C1;
            background-color: #f8f9fa;
        }
        .document-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .reason {
            font-style: italic;
            color: #d63384;
            margin-left: 10px;
            font-size: 0.9em;
            background-color: #fff3cd;
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #ffc107;
        }
        .badge {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 10px;
        }
        .instructions {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .urgent {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="text-align: left;">
            <h1 style="margin-bottom: 0;">LYDO Scholarship</h1>
            <p style="margin-top: 5px; font-size: 14px; font-style: italic;">PARA SA KABATAAN, PARA SA KINABUKASAN.</p>
        </div>
        <h2>Document Update Required</h2>
    </div>
    <div class="content">
        <p>Dear {{ $applicant_fname }} {{ $applicant_lname }},</p>
        
        <p>Your scholarship application has been reviewed and the following documents need to be updated:</p>

        <div class="document-list">
            <ul>
                @foreach($bad_documents as $document)
                    <li>
                        <div class="document-name">
                            {{ $document['name'] }}
                            <span class="badge">Needs Update</span>
                        </div>
                        @if(!empty($document['reason']))
                            <div class="reason">
                                <strong>Reason:</strong> {{ $document['reason'] }}
                            </div>
                        @else
                            <div class="reason">
                                <strong>Reason:</strong> Document does not meet requirements
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="instructions">
            <p><strong>Please follow these instructions:</strong></p>
            <ul>
                <li>Review the reasons provided for each document</li>
                <li>Prepare updated versions of the documents</li>
                <li>Ensure all documents are clear and readable</li>
                <li>Make sure documents meet the specified requirements</li>
            </ul>
        </div>

        <p>Please update your documents using the link below:</p>
        <p style="text-align: center;">
            <a href="{{ route('scholar.showUpdateApplication', ['applicant_id' => $applicant_id]) . '?token=' . $updateToken . '&issues=' . $document_types }}" class="button">Update Documents</a>
        </p>

        <p class="urgent"><strong>Important:</strong> This link will expire in 24 hours. If you encounter any issues, please contact the LYDO Scholarship office.</p>

        <p>Thank you for your cooperation.</p>
        <p>Best regards,<br>LYDO Scholarship Team</p>
    </div>
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>If you have any questions, please contact us at scholarship@lydo.gov.ph</p>
    </div>
</body>
</html>