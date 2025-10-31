<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intake Sheet Already Submitted - LYDO Scholarship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            text-align: center;
            padding: 2rem;
        }
        .card-body {
            padding: 3rem;
            text-align: center;
        }
        .checkmark {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0">LYDO Scholarship</h1>
                        <p class="mb-0">Family Intake Sheet</p>
                    </div>
                    <div class="card-body">
                        <div class="checkmark">✓</div>
                        <h4 class="card-title text-success mb-3">Intake Sheet Already Submitted</h4>
                        <p class="card-text text-muted mb-4">
                            Thank you, {{ $applicant->applicant_fname }} {{ $applicant->applicant_lname }}!
                            Your Family Intake Sheet has already been submitted successfully.
                        </p>
                        <p class="text-muted small">
                            Please wait for an announcement regarding the date and time for your face-to-face interview.
                            We will notify you via email once the schedule is finalized.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                Return to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
