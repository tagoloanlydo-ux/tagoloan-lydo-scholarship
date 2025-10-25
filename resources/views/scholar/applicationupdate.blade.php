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
  <title>LYDO Scholarship Application Update</title>

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
      line-height: 1.6;
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
      z-index: 10;
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
      width: 100%;
      box-sizing: border-box;
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
      width: 100%;
      max-width: 300px;
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

    /* Document Modal Styles */
    .document-modal-content {
      max-height: calc(100vh - 200px);
      overflow-y: auto;
    }

    .document-viewer-container {
      height: 70vh;
      overflow: hidden;
    }

    .document-viewer {
      width: 100%;
      height: 100%;
      border: none;
      border-radius: 8px;
      background: #f8fafc;
    }

    /* Custom scrollbar for document modal */
    .document-modal-content::-webkit-scrollbar {
      width: 8px;
    }

    .document-modal-content::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 4px;
    }

    .document-modal-content::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }

    .document-modal-content::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    /* New Styles for 3-column layout */
    .documents-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .document-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      padding: 1.5rem;
      transition: all 0.3s ease;
      border: 1px solid #e5e7eb;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .document-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .document-card-header {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
    }

    .document-card-icon {
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
      border-radius: 10px;
      margin-right: 1rem;
      font-size: 1.25rem;
      flex-shrink: 0;
    }

    .document-card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #374151;
      margin: 0;
    }

    .document-card-description {
      color: #6b7280;
      font-size: 0.875rem;
      margin-bottom: 1.5rem;
      flex-grow: 1;
    }

    .document-actions {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .view-document-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      background-color: #f0f4ff;
      color: var(--primary);
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      text-align: center;
    }

    .view-document-btn:hover {
      background-color: #e0e7ff;
      border-color: var(--primary-light);
    }

    /* Enhanced Banner Styles */
    .banner-container {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      position: relative;
      overflow: hidden;
    }

    .banner-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 1rem 1.5rem 1rem 0.5rem;
      display: flex;
      align-items: center;
      justify-content: flex-start;
    }

    .banner-logo-section {
      display: flex;
     
      gap: 1rem;
    }

    .banner-logo {
      height: 50px;
      width: auto;
    }

    .banner-text {
      color: white;
    }

    .banner-title {
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0;
      line-height: 1.2;
    }

    .banner-subtitle {
      font-size: 0.75rem;
      letter-spacing: 0.1em;
      margin: 0;
      opacity: 0.9;
    }

    .banner-decoration {
      position: absolute;
      top: 0;
      right: 0;
      height: 100%;
      width: 30%;
      background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
      clip-path: polygon(100% 0, 100% 100%, 0 100%);
    }

    /* Mobile Responsive Styles */
    @media (max-width: 1024px) {
      .documents-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      }
    }

    @media (max-width: 768px) {
      .container-wrapper {
        padding: 0 0.75rem;
      }
      
      .tab-container {
        padding: 1.5rem 1rem;
        margin-bottom: 1rem;
      }
      
      .back-btn {
        top: 1rem;
        left: 1rem;
        width: 36px;
        height: 36px;
      }
      
      .tab-container h1 {
        font-size: 1.5rem;
        margin-top: 0.5rem;
      }
      
      .subtitle {
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
      }
      
      .input-row {
        flex-direction: column;
        gap: 1rem;
      }

      .input-group {
        min-width: 100%;
      }
      
      .documents-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .document-card {
        padding: 1.25rem;
      }
      
      .document-card-header {
        margin-bottom: 0.75rem;
      }
      
      .document-card-icon {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
      }
      
      .document-card-title {
        font-size: 1rem;
      }
      
      .document-card-description {
        font-size: 0.85rem;
        margin-bottom: 1rem;
      }
      
      .requirements-info {
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
      }
      
      .banner-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
        padding: 1rem;
      }
      
      .banner-logo-section {
        flex-direction: column;
        gap: 0.75rem;
      }
      
      .banner-title {
        font-size: 1.25rem;
      }
      
      .banner-subtitle {
        font-size: 0.7rem;
      }
      
      .banner-decoration {
        width: 50%;
      }
      
      .button-row {
        margin-top: 1.5rem;
        padding-top: 1rem;
      }
      
      .nav-btn {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
      }
    }

    @media (max-width: 480px) {
      .container-wrapper {
        padding: 0 0.5rem;
      }
      
      .tab-container {
        padding: 1.25rem 0.75rem;
        border-radius: 8px;
      }
      
      .back-btn {
        top: 0.75rem;
        left: 0.75rem;
        width: 32px;
        height: 32px;
      }
      
      .tab-container h1 {
        font-size: 1.3rem;
        padding: 0 0.5rem;
      }
      
      .subtitle {
        font-size: 0.85rem;
        padding: 0 0.5rem;
      }
      
      .document-card {
        padding: 1rem;
      }
      
      .document-card-header {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
      }
      
      .document-card-icon {
        margin-right: 0;
      }
      
      .requirements-info {
        padding: 0.75rem;
        font-size: 0.85rem;
      }
      
      .alert {
        padding: 0.75rem;
        font-size: 0.9rem;
      }
      
      .input-file {
        padding: 0.6rem;
        font-size: 0.9rem;
      }
      
      .view-document-btn {
        padding: 0.6rem 0.75rem;
        font-size: 0.85rem;
      }
    }

    /* Animation for better mobile experience */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .document-card {
      animation: fadeIn 0.5s ease-out;
    }
    
    /* Touch-friendly improvements */
    @media (hover: none) {
      .document-card:hover {
        transform: none;
      }
      
      .back-btn:hover {
        transform: none;
      }
      
      .submit-btn:hover:not(:disabled) {
        transform: none;
      }
      
      .input-file:hover {
        border-color: #d1d5db;
        background-color: #f9fafb;
      }
      
      .view-document-btn:hover {
        background-color: #f0f4ff;
        border-color: #d1d5db;
      }
    }

    /* Focus styles for accessibility */
    .back-btn:focus,
    .nav-btn:focus,
    .input-file:focus,
    .view-document-btn:focus {
      outline: 2px solid var(--primary);
      outline-offset: 2px;
    }
  </style>
</head>
<body>
  <!-- Enhanced Banner -->
  <div class="banner-container">
    <div class="banner-content">
      <div class="banner-logo-section">
        <img src="/images/LYDO.png" alt="LYDO Logo" class="banner-logo"/>
        <div class="banner-text">
          <h1 class="banner-title">LYDO SCHOLARSHIP</h1>
          <p class="banner-subtitle">PARA SA KABATAAN, PARA SA KINABUKASAN.</p>
        </div>
      </div>
      <div class="banner-decoration"></div>
    </div>
  </div>

  <div class="container-wrapper mt-5">
    <div class="tab-container">
      <button class="back-btn" onclick="history.back()" aria-label="Go back">
        <i class="fas fa-arrow-left"></i>
      </button>
      <h1>Update Required Documents</h1>
      <p class="subtitle">Please update the documents that were flagged during review</p>

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
        <p><i class="fas fa-info-circle mr-2"></i>The following documents were flagged during review and need to be updated. Please upload new versions of these documents in PDF format. Each file should not exceed 5MB.</p>
      </div>

      <form id="applicationForm" method="POST" action="{{ route('scholar.updateApplication', ['applicant_id' => $applicant->applicant_id]) }}" enctype="multipart/form-data">
        @csrf

        <!-- Application Requirements - Only show bad documents -->
        @php
          $documentMapping = [
            'Application Letter' => ['key' => 'application_letter', 'description' => 'A formal letter expressing your interest in the scholarship program', 'icon' => 'fas fa-file-alt'],
            'Certificate of Registration' => ['key' => 'cert_of_reg', 'description' => 'Your current certificate of registration from your school', 'icon' => 'fas fa-file-certificate'],
            'Grade Slip' => ['key' => 'grade_slip', 'description' => 'Your most recent grade slip or transcript of records', 'icon' => 'fas fa-chart-line'],
            'Barangay Indigency' => ['key' => 'brgy_indigency', 'description' => 'Certificate of indigency from your barangay', 'icon' => 'fas fa-home'],
            'Student ID' => ['key' => 'student_id', 'description' => 'A clear copy of your valid student identification card', 'icon' => 'fas fa-id-card']
          ];
        @endphp

        <div class="documents-grid">
          @foreach($issues as $badDoc)
            @if(isset($documentMapping[$badDoc]))
              @php $docInfo = $documentMapping[$badDoc]; @endphp
              <div class="document-card">
                <div class="document-card-header">
                  <div class="document-card-icon">
                    <i class="{{ $docInfo['icon'] }}"></i>
                  </div>
                  <h3 class="document-card-title">{{ $badDoc }}</h3>
                </div>
                <p class="document-card-description">{{ $docInfo['description'] }}</p>
                <div class="input-group">
                  <input type="file" id="{{ $docInfo['key'] }}" name="{{ $docInfo['key'] }}" accept="application/pdf" required class="input-file"/>
                  <small class="error-message"></small>
                </div>
                <div class="document-actions mt-3">
                  @php
                    $dbField = $docInfo['key'];
                  @endphp
                  @if($application && $application->$dbField)
                    <button type="button" onclick="openDocumentModal('{{ asset('storage/' . $application->$dbField) }}', '{{ $badDoc }}')" class="view-document-btn">
                      <i class="fas fa-eye"></i>View Current Document
                    </button>
                  @endif
                </div>
              </div>
            @endif
          @endforeach
        </div>

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

  <!-- Document Viewer Modal -->
  <div id="documentModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-6xl max-h-[90vh] rounded-2xl shadow-2xl animate-fadeIn flex flex-col">

      <!-- Header -->
      <div class="flex items-center justify-between px-4 py-3 border-b sm:px-6 sm:py-4">
        <h2 id="documentModalTitle" class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2 truncate">
          <i class="fas fa-file-alt text-blue-600"></i>
          <span class="truncate">Document Viewer</span>
        </h2>
        <button onclick="closeDocumentModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
          <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
      </div>

      <!-- Body - Improved with better scrolling -->
      <div class="document-modal-content p-4 sm:p-6 flex-1">
        <div class="document-viewer-container mb-4">
          <iframe id="documentViewer" src="" class="document-viewer"></iframe>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex justify-end gap-3 px-4 py-3 border-t bg-gray-50 rounded-b-2xl sm:px-6 sm:py-4">
        <button onclick="closeDocumentModal()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm sm:text-base">
          Close
        </button>
      </div>
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
    @foreach($issues as $badDoc)
      @if(isset($documentMapping[$badDoc]))
        @php $docInfo = $documentMapping[$badDoc]; @endphp
        const input{{ $docInfo['key'] }} = document.getElementById('{{ $docInfo['key'] }}');
        if (input{{ $docInfo['key'] }}) {
          input{{ $docInfo['key'] }}.addEventListener("change", function () {
            validateFile(this);
          });
        }
      @endif
    @endforeach

    // Initial validation
    updateSubmitButton();

    // Form submission
    applicationForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Validate all files
      let allValid = true;

      @foreach($issues as $badDoc)
        @if(isset($documentMapping[$badDoc]))
          @php $docInfo = $documentMapping[$badDoc]; @endphp
          const input{{ $docInfo['key'] }} = document.getElementById('{{ $docInfo['key'] }}');
          if (input{{ $docInfo['key'] }} && !validateFile(input{{ $docInfo['key'] }})) {
            allValid = false;
          }
        @endif
      @endforeach

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
    // Document Viewer Functions
    function openDocumentModal(documentUrl, documentTitle) {
      const modal = document.getElementById('documentModal');
      const viewer = document.getElementById('documentViewer');
      const title = document.getElementById('documentModalTitle');

      title.innerHTML = `<i class="fas fa-file-alt text-blue-600"></i> <span class="truncate">${documentTitle}</span>`;
      viewer.src = documentUrl;
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeDocumentModal() {
      const modal = document.getElementById('documentModal');
      const viewer = document.getElementById('documentViewer');

      modal.classList.add('hidden');
      viewer.src = '';
      document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('documentModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeDocumentModal();
      }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !document.getElementById('documentModal').classList.contains('hidden')) {
        closeDocumentModal();
      }
    });

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