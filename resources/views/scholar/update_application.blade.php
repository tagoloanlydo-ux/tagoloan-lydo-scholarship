<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
  <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
  <title>LYDO Scholarship Application</title>
  
  <style>
    :root {
      --primary: #6d53d3;
      --primary-light: #8b7bd8;
      --primary-dark: #5540b0;
      --secondary: #f8f9fa;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f7fa;
      color: #333;
    }
    
    .banner-grad {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .container-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }
    
    .tab-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin-bottom: 2rem;
      position: relative;
    }
    
    .back-btn {
      position: absolute;
      top: 1.5rem;
      left: 1.5rem;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .back-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }
    
    .tab-container h1 {
      color: var(--primary);
      text-align: center;
      margin-bottom: 0.5rem;
      font-size: 1.8rem;
      font-weight: 700;
    }
    
    .subtitle {
      text-align: center;
      color: #6b7280;
      margin-bottom: 2rem;
    }
    
    .input-row {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .input-group {
      flex: 1;
      min-width: 200px;
    }
    
    .input-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #374151;
    }
    
    .input-group input, 
    .input-group select {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }
    
    .input-group input:focus, 
    .input-group select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(109, 83, 211, 0.1);
    }
    
    .input-group input.error, 
    .input-group select.error {
      border-color: var(--danger);
    }
    
    .input-group input.valid, 
    .input-group select.valid {
      border-color: var(--success);
    }
    
    .error-message {
      display: block;
      margin-top: 0.5rem;
      color: var(--danger);
      font-size: 0.875rem;
    }
    
    .input-file {
      padding: 0.75rem;
      border: 1px dashed #d1d5db;
      border-radius: 8px;
      background-color: #f9fafb;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    .input-file:hover {
      border-color: var(--primary);
      background-color: #f0f4ff;
    }
    
    .button-row {
      display: flex;
      justify-content: center;
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid #e5e7eb;
    }
    
    .nav-btn {
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
    }
    
    .submit-btn {
      background: var(--primary);
      color: white;
    }
    
    .submit-btn:hover:not(:disabled) {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .nav-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }
    
    .alert {
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
    }
    
    .alert-success {
      background-color: #d1fae5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }
    
    .alert-danger {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }
    
    .requirements-info {
      background-color: #f0f4ff;
      border-left: 4px solid var(--primary);
      padding: 1rem 1.5rem;
      margin-bottom: 2rem;
      border-radius: 0 8px 8px 0;
    }
    
    .requirement-item {
      display: flex;
      align-items: flex-start;
      margin-bottom: 1rem;
      padding: 1rem;
      background-color: #f9fafb;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
    }
    
    .requirement-icon {
      color: var(--primary);
      margin-right: 1rem;
      font-size: 1.25rem;
      margin-top: 0.25rem;
    }
    
    .requirement-details h3 {
      margin: 0 0 0.5rem 0;
      font-size: 1rem;
      font-weight: 600;
    }
    
    .requirement-details p {
      margin: 0;
      color: #6b7280;
      font-size: 0.875rem;
    }
    
    @media (max-width: 768px) {
      .input-row {
        flex-direction: column;
        gap: 1rem;
      }
      
      .input-group {
        min-width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="banner-grad flex flex-col md:flex-row items-center justify-center md:justify-between w-full h-25 px-4 md:px-6 text-white">
    <div class="flex flex-col md:flex-row items-center text-center md:text-left">
      <img src="/images/LYDO.png" alt="LYDO Logo" class="h-8 md:h-10 mb-2 md:mb-0 md:mr-4"/>
      <div>
        <h1 class="text-xl md:text-2xl font-bold">LYDO SCHOLARSHIP</h1>
        <p class="text-xs tracking-widest">
          PARA SA KABATAAN, PARA SA KINABUKASAN.
        </p>
      </div>
    </div>
  </div>
  
  <div class="container-wrapper mt-5">
    <div class="tab-container">
      <button class="back-btn" onclick="history.back()" aria-label="Go back">←</button>
      <h1>Update Application Requirements</h1>
      <p class="subtitle">Please upload the required documents that need to be updated</p>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      
      <div class="requirements-info">
        <p><i class="fas fa-info-circle mr-2"></i>Please upload all required documents in PDF format. Each file should not exceed 5MB.</p>
      </div>

      <form id="applicationForm" method="POST" action="{{ route('scholar.updateApplication', ['applicant_id' => $applicant->applicant_id]) }}" enctype="multipart/form-data">
        @csrf

        <!-- Application Requirements -->
        @php
          $documentMapping = [
            'application_letter' => ['title' => 'Application Letter', 'description' => 'A formal letter expressing your interest in the scholarship program', 'icon' => 'fas fa-file-alt'],
            'grade_slip' => ['title' => 'Grade Slip', 'description' => 'Your most recent grade slip or transcript of records', 'icon' => 'fas fa-chart-line'],
            'cert_of_reg' => ['title' => 'Certificate of Registration', 'description' => 'Your current certificate of registration from your school', 'icon' => 'fas fa-file-certificate'],
            'brgy_indigency' => ['title' => 'Barangay Indigency', 'description' => 'Certificate of indigency from your barangay', 'icon' => 'fas fa-home'],
            'student_id' => ['title' => 'Student ID', 'description' => 'A clear copy of your valid student identification card', 'icon' => 'fas fa-id-card']
          ];
        @endphp

        @foreach($documentMapping as $docKey => $docInfo)
          @if(in_array($docKey, $issues))
            <div class="requirement-item">
              <div class="requirement-icon">
                <i class="{{ $docInfo['icon'] }}"></i>
              </div>
              <div class="requirement-details">
                <h3>{{ $docInfo['title'] }}</h3>
                <p>{{ $docInfo['description'] }}</p>
                <div class="input-group mt-2">
                  <input type="file" id="{{ $docKey }}" name="{{ $docKey }}" accept="application/pdf" required class="input-file"/>
                  <small class="error-message"></small>
                </div>
              </div>
            </div>
          @endif
        @endforeach

        <!-- Submit Button -->
        <div class="button-row">
          <button type="submit" id="submitBtn" class="nav-btn submit-btn">
            <span id="submitBtnText">Update Documents</span>
            <svg id="submitBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const applicationForm = document.getElementById("applicationForm");
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnSpinner = document.getElementById('submitBtnSpinner');

    function getErrorEl(input) {
      return input.parentElement.querySelector(".error-message");
    }

    function updateUI(input, valid, errorMsg = "") {
      const errorEl = getErrorEl(input);
      if (!valid) {
        input.classList.add("error");
        input.classList.remove("valid");
        if (errorEl) errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>' + errorMsg;
      } else {
        input.classList.remove("error");
        input.classList.add("valid");
        if (errorEl) errorEl.innerHTML = "";
      }
    }

    function validateFile(input) {
      const file = input.files[0];
      let valid = true;
      let errorMsg = "";

      if (!file) {
        valid = false;
        errorMsg = "This file is required";
      } else {
        const isPdf = file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");
        if (!isPdf) {
          valid = false;
          errorMsg = "Only PDF files are allowed";
        } else if (file.size > 5 * 1024 * 1024) {
          valid = false;
          errorMsg = "File size must not exceed 5MB";
        }
      }

      updateUI(input, valid, errorMsg);
      updateSubmitButton();
      return valid;
    }

    function updateSubmitButton() {
      const hasErrorMessage = Array.from(
        applicationForm.querySelectorAll(".error-message")
      ).some((msg) => msg.textContent.trim() !== "");

      const hasEmptyRequired = Array.from(
        applicationForm.querySelectorAll("input[required]")
      ).some((input) => input.files.length === 0);

      submitBtn.disabled = hasErrorMessage || hasEmptyRequired;
    }

    // Attach validation to all file inputs
    ["application_letter", "grade_slip", "certificate_of_registration", "barangay_indigency", "student_id"]
      .forEach(id => {
        const input = document.getElementById(id);
        input.addEventListener("change", function () {
          validateFile(this);
        });
      });

    // Initial validation
    updateSubmitButton();

    // Form submission
    applicationForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Validate all files
      const fileInputs = ["application_letter", "grade_slip", "certificate_of_registration", "barangay_indigency", "student_id"];
      let allValid = true;
      
      fileInputs.forEach(id => {
        const input = document.getElementById(id);
        if (!validateFile(input)) {
          allValid = false;
        }
      });

      if (!allValid) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Please fix all errors before submitting.",
        });
        return;
      }

      Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update your documents?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#6d53d3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!"
      }).then((result) => {
        if (result.isConfirmed) {
          submitBtn.disabled = true;
          submitBtnText.textContent = 'Updating...';
          submitBtnSpinner.classList.remove('hidden');
          applicationForm.submit();
        }
      });
    });

    // Prevent form submission on Enter key press
    applicationForm.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
      }
    });
  </script>

  <script>
    @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'You successfully submitted the Application',
      text: 'Stay tuned for the Announcement!',
      confirmButtonColor: '#6d53d3'
    });
    @endif
  </script>
</body>
</html>