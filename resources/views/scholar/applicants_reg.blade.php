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
      <link rel="stylesheet" href="{{ asset('css/application_reg.css') }}">
      <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
      <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
      <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <title>Personal Information Form</title>
        <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
            transition: opacity 0.3s ease;
            animation: fadeIn 1s ease forwards;
        }

        .spinner {
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        .spinner img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-out {
            animation: fadeOut 1s ease forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        /* Responsive spinner size */
        @media (max-width: 768px) {
            .spinner {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 480px) {
            .spinner {
                width: 60px;
                height: 60px;
            }
        }    
    </style>
    </head>
      <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
      <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
  </div>
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
     
        <div class="tab-container ">
           <!-- Back button -->
        <button class="back-btn" onclick="history.back()">←</button>
          <h1>Applicant Registration</h1>
          <p class="subtitle">Fill out the required details below</p>

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

          <!-- Tab Navigation -->
          <div class="tab-nav">
            <button class="tab-button active" data-tab="personal">Personal Information</button>
            <button class="tab-button" data-tab="education">Educational Attainment</button>
            <button class="tab-button" data-tab="requirements">Application Requirements</button>
          </div>

          <form id="applicationForm" method="POST" action="{{ route('applicants.register') }}" enctype="multipart/form-data">
          @csrf

            <!-- Tab Content: Personal Information -->
            <div id="personal" class="tab-content active">
              <!-- Name Fields -->
              <div class="input-row">
                <div class="input-group">
                  <label for="fname">First Name</label>
                  <input type="text" id="fname" name="applicant_fname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="mname">Middle Name</label>
                  <input type="text" id="mname" name="applicant_mname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="lname">Last Name</label>
                  <input type="text" id="lname" name="applicant_lname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                  <small class="error-message"></small>
                </div>
                <div class="input-group" style="width: 10px">
                  <label for="suffix">Suffix</label>
                  <input type="text" id="suffix" name="applicant_suffix" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                  <small class="error-message"></small>
                </div>
              </div>

              <!-- Personal Details -->
              <div class="input-row">
                <div class="input-group">
                  <label for="gender">Gender</label>
                  <select id="gender" name="applicant_gender" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    <option value=""></option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="bdate">Birth Date</label>
                  <input type="date" id="bdate" name="applicant_bdate" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="civil_status">Civil Status</label>
                  <select id="civil_status" name="applicant_civil_status" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    <option value=""></option>
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widowed">Widowed</option>
                    <option value="divorced">Divorced</option>
                  </select>
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="brgy">Barangay</label>
                  <select id="brgy" name="applicant_brgy" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    <option value=""></option>
                    <option value="Sugbong cogon">Sugbong cogon</option>
                    <option value="Baluarte">Baluarte</option>
                    <option value="Casinglot">Casinglot</option>
                    <option value="Gracia">Gracia</option>
                    <option value="Mohon">Mohon</option>
                    <option value="Natumolan">Natumolan</option>
                    <option value="Poblacion">Poblacion</option>
                    <option value="Rosario">Rosario</option>
                    <option value="Santa Ana">Santa Ana</option>
                    <option value="Santa Cruz">Santa Cruz</option>
                  </select>
                  <small class="error-message"></small>
                </div>
              </div>

              <!-- Contact Details -->
              <div class="input-row">
                <div class="input-group">
                  <label for="email">Email</label>
                  <input type="email" id="email" name="applicant_email" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="contact">Contact Number</label>
                  <input type="tel" id="contact" name="applicant_contact_number" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                  <small class="error-message"></small>
                </div>
              </div>
            </div>

            <!-- Tab Content: Educational Attainment -->
            <div id="education" class="tab-content">
              <div class="input-row">
                <div class="input-group" style="width: 100%">
                  <label for="school_name">School Name</label>
                  <select id="school_name" name="applicant_school_name" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 select2" required>
                    <option value=""></option>
                    <optgroup label="State Universities">
                      <option value="USTP CDO">University of Science and Technology of Southern Philippines (USTP) – Cagayan de Oro</option>
                      <option value="USTP Claveria">University of Science and Technology of Southern Philippines (USTP) – Claveria</option>
                      <option value="USTP Villanueva">University of Science and Technology of Southern Philippines (USTP) – Villanueva</option>
                      <option value="MSU Naawan">Mindanao State University – Naawan (MSU-Naawan)</option>
                      <option value="MOSCAT">Misamis Oriental State College of Agriculture and Technology (MOSCAT), Claveria</option>
                    </optgroup>
                    <optgroup label="Community Colleges">
                      <option value="Opol Community College">Opol Community College</option>
                      <option value="Tagoloan Community College">Tagoloan Community College</option>
                      <option value="Bugo Community College">Bugo Community College</option>
                      <option value="Initao Community College">Initao Community College</option>
                      <option value="Magsaysay College">Magsaysay College, Misamis Oriental</option>
                    </optgroup>
                    <optgroup label="Private Colleges & Universities">
                      <option value="Liceo de Cagayan University">Liceo de Cagayan University, CDO</option>
                      <option value="PHINMA COC">PHINMA Cagayan de Oro College</option>
                      <option value="Capitol University">Capitol University, CDO</option>
                      <option value="Lourdes College">Lourdes College, CDO</option>
                      <option value="Blessed Mother College">Blessed Mother College, CDO</option>
                      <option value="Pilgrim Christian College">Pilgrim Christian College, CDO</option>
                      <option value="Gingoog Christian College">Gingoog Christian College</option>
                      <option value="Christ the King College">Christ the King College, Gingoog City</option>
                      <option value="St. Rita's College">St. Rita's College of Balingasag</option>
                      <option value="St. Peter's College">St. Peter's College of Balingasag</option>
                      <option value="Saint John Vianney Seminary">Saint John Vianney Theological Seminary, CDO</option>
                      <option value="Asian College of Science and Technology">Asian College of Science and Technology, CDO</option>
                    </optgroup>
                    <optgroup label="Others">
                      <option value="Others">Others (Please specify below)</option>
                    </optgroup>
                  </select>
                  <input type="text" id="school_name_other" name="applicant_school_name_other" placeholder="Please specify your school" style="display: none; margin-top: 8px; padding: 10px; border: 1px solid black; border-radius: 8px; font-size: 14px; outline: none; width: 100%;"/>
                  <small class="error-message"></small>
                </div>
              </div>

              <!-- Academic Details -->
              <div class="input-row">
                <div class="input-group">
                  <label for="year_level">Year Level</label>
                  <select id="year_level" name="applicant_year_level" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    <option value="">Select Year Level</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                    <option value="5th Year">5th Year</option>
                  </select>
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="course">Course</label>
                  <input type="text" id="course" name="applicant_course" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="Course" />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="acad_year">Academic Year</label>
                  <input type="text" id="acad_year" name="applicant_acad_year" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="e.g., 2023-2024" readonly />
                  <small class="error-message"></small>
                </div>
              </div>
            </div>

            <!-- Tab Content: Application Requirements -->
            <div id="requirements" class="tab-content">
              <div class="input-row">
                <div class="input-group">
                  <label for="application_letter">Application Letter</label>
                  <input type="file" id="application_letter" name="application_letter" accept="application/pdf" required class="input-file"/>
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="grade_slip">Grade Slip</label>
                  <input type="file" id="grade_slip" name="grade_slip" accept="application/pdf" required class="input-file" />
                  <small class="error-message"></small>
                </div>
              </div>

              <div class="input-row">
                <div class="input-group">
                  <label for="certificate_of_registration">Certificate of Registration</label>
                  <input type="file" id="certificate_of_registration" name="certificate_of_registration" accept="application/pdf" required class="input-file"/>
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="barangay_indigency">Barangay Indigency</label>
                  <input type="file" id="barangay_indigency" name="barangay_indigency" accept="application/pdf" required class="input-file"/>
                  <small class="error-message"></small>
                </div>
              </div>

              <div class="input-row">
                <div class="input-group">
                  <label for="student_id">Student ID</label>
                  <input type="file" id="student_id" name="student_id" accept="application/pdf" required class="input-file"/>
                  <small class="error-message"></small>
                </div>
              </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="button-row">
              <button type="button" id="prevBtn" class="nav-btn prev-btn" style="display: none;">Previous</button>
              <button type="button" id="nextBtn" class="nav-btn next-btn">Next</button>
              <button type="submit" id="submitBtn" class="nav-btn submit-btn" style="display: none;">
                <span id="submitBtnText">Submit</span>
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
// Tab switching logic
const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const submitBtn = document.getElementById('submitBtn');
let currentTab = 0;
let select2Initialized = false;

function showTab(index) {
  tabContents.forEach(content => content.classList.remove('active'));
  tabButtons.forEach(button => button.classList.remove('active'));
  tabContents[index].classList.add('active');
  tabButtons[index].classList.add('active');

  prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
  nextBtn.style.display = index === tabContents.length - 1 ? 'none' : 'inline-block';
  submitBtn.style.display = index === tabContents.length - 1 ? 'inline-block' : 'none';

  // Initialize Select2 when education tab is shown
  if (index === 1 && !select2Initialized) {
    $('#school_name').select2({
      placeholder: 'Search and select your school...',
      allowClear: true,
      minimumInputLength: 1,
      width: '100%',
      dropdownParent: $('#education')
    });
    
    // Handle "Others" option
    $('#school_name').on('change', function() {
      const schoolOtherInput = document.getElementById("school_name_other");
      if (this.value === "Others") {
        schoolOtherInput.style.display = "block";
        schoolOtherInput.setAttribute("required", "required");
      } else {
        schoolOtherInput.style.display = "none";
        schoolOtherInput.removeAttribute("required");
        schoolOtherInput.value = "";
      }
      validateInput(this);
    });
    
    select2Initialized = true;
  }

  updateButtonStates();
}

function updateButtonStates() {
  const currentTabContent = tabContents[currentTab];
  const hasErrorMessage = Array.from(
    currentTabContent.querySelectorAll(".error-message")
  ).some((msg) => msg.textContent.trim() !== "");

  const hasEmptyRequired = Array.from(
    currentTabContent.querySelectorAll("input[required], select[required]")
  ).some((input) => {
    if (input.type === "file") return input.files.length === 0;
    return !input.value.trim();
  });

  nextBtn.disabled = hasErrorMessage || hasEmptyRequired;
}

tabButtons.forEach((button, index) => {
  button.addEventListener('click', () => {
    // Validate current tab before allowing switch to another tab
    const currentTabContent = tabContents[currentTab];
    const hasErrorMessage = Array.from(
      currentTabContent.querySelectorAll(".error-message")
    ).some((msg) => msg.textContent.trim() !== "");

    const hasEmptyRequired = Array.from(
      currentTabContent.querySelectorAll("input[required], select[required]")
    ).some((input) => {
      if (input.type === "file") return input.files.length === 0;
      return !input.value.trim();
    });

    if (index !== currentTab && (hasErrorMessage || hasEmptyRequired)) {
      return; // Prevent switching to other tabs if current tab has errors
    }

    currentTab = index;
    showTab(currentTab);
  });
});

prevBtn.addEventListener('click', () => {
  if (currentTab > 0) {
    currentTab--;
    showTab(currentTab);
  }
});

nextBtn.addEventListener('click', () => {
  // Validate current tab before proceeding
  const currentTabContent = tabContents[currentTab];
  const hasErrorMessage = Array.from(
    currentTabContent.querySelectorAll(".error-message")
  ).some((msg) => msg.textContent.trim() !== "");

  const hasEmptyRequired = Array.from(
    currentTabContent.querySelectorAll("input[required], select[required]")
  ).some((input) => {
    if (input.type === "file") return input.files.length === 0;
    return !input.value.trim();
  });

  if (hasErrorMessage || hasEmptyRequired) {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Please complete all required fields and fix any errors before proceeding to the next tab.",
    });
    return; // Prevent tab switch
  }

  if (currentTab < tabContents.length - 1) {
    currentTab++;
    showTab(currentTab);
  }
});

// Initialize first tab
showTab(currentTab);

const applicationForm = document.getElementById("applicationForm");
const submitBtnText = document.getElementById('submitBtnText');
const submitBtnSpinner = document.getElementById('submitBtnSpinner');

const rules = {
  name: /^[A-Za-z\s]+$/, // letters and spaces only
  contact: /^(09\d{9}|\+639\d{9})$/,
  gmail: /^[a-zA-Z0-9._%+-]+@gmail\.com$/  // Gmail format
};

// Store validation states for email
const validationStates = {
  email: { valid: false, duplicate: false, checking: false }
};

let emailDebounceTimer;
let lastCheckedEmail = "";

// FIXED EMAIL VALIDATION FUNCTION
function validateEmailInput(input) {
  const value = input.value.trim();
  const errorEl = getErrorEl(input);
  let errorMsg = "";
  let valid = true;

  // Clear previous timer
  if (emailDebounceTimer) {
    clearTimeout(emailDebounceTimer);
  }

  // Required field validation
  if (input.hasAttribute("required") && !value) {
    errorMsg = "This field cannot be empty";
    valid = false;
    validationStates.email.valid = false;
    validationStates.email.duplicate = false;
    validationStates.email.checking = false;
  }
  // Email format validation
  else if (value && !rules.gmail.test(value)) {
    errorMsg = "Email must end with @gmail.com";
    valid = false;
    validationStates.email.valid = false;
    validationStates.email.duplicate = false;
  }
  // Valid format - check for duplicate
  else if (value) {
    validationStates.email.valid = true;
    validationStates.email.checking = true;
    
    // Show checking state
    input.classList.remove("error", "valid");
    if (errorEl) errorEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Checking email availability...';
    
    // Debounce duplicate check
    emailDebounceTimer = setTimeout(() => {
      checkEmailDuplicate(input);
    }, 800);
    return; // Exit early, UI will be updated by checkEmailDuplicate
  }

  // Update UI for non-duplicate-check cases
  updateUI(input, valid, errorMsg);
  toggleButton();
  return valid;
}

// FIXED EMAIL DUPLICATE CHECK FUNCTION
function checkEmailDuplicate(emailInput) {
  const email = emailInput.value.trim();
  const errorEl = getErrorEl(emailInput);
  
  if (!email) {
    validationStates.email.duplicate = false;
    validationStates.email.checking = false;
    updateUI(emailInput, true);
    toggleButton();
    return;
  }

  console.log('Checking email duplicate for:', email);
  
  fetch("{{ route('check.applicant.email') }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({ email: email })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    console.log('Email check response:', data);
    validationStates.email.checking = false;
    
    if (data.exists) {
      // DUPLICATE FOUND - Show error immediately
      validationStates.email.duplicate = true;
      emailInput.classList.add("error");
      emailInput.classList.remove("valid");
      if (errorEl) {
        errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>This email is already taken. Please try another email.';
      }
    } else {
      // No duplicate - show success
      validationStates.email.duplicate = false;
      emailInput.classList.remove("error");
      emailInput.classList.add("valid");
      if (errorEl) errorEl.innerHTML = '<i class="fa-solid fa-circle-check mr-1"></i>Email is available';
      
      // Clear success message after 2 seconds
      setTimeout(() => {
        if (errorEl && validationStates.email.valid && !validationStates.email.duplicate) {
          errorEl.innerHTML = "";
        }
      }, 2000);
    }
    toggleButton();
  })
  .catch(error => {
    console.error('Error checking email:', error);
    validationStates.email.checking = false;
    validationStates.email.duplicate = false;

    // Show network error but don't block the form
    emailInput.classList.remove("error", "valid");
    if (errorEl) errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>Unable to verify email. Please try again.';

    toggleButton();
  });
}

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
  toggleButton();
  return valid;
}

// IMPROVED TOGGLE BUTTON FUNCTION
function toggleButton() {
  // Specifically check email validation state
  const emailInput = document.getElementById('email');
  if (emailInput && emailInput.value.trim()) {
    const isEmailValid = validationStates.email.valid && !validationStates.email.duplicate && !validationStates.email.checking;
    
    // If email is invalid and has value, disable buttons
    if (!isEmailValid) {
      nextBtn.disabled = true;
      if (submitBtn) submitBtn.disabled = true;
      return;
    }
  }
  
  updateButtonStates();
}

// Attach events to all inputs - FIXED EVENT HANDLING
applicationForm.querySelectorAll("input, select").forEach((input) => {
  if (input.type === "file") {
    input.addEventListener("change", () => validateFile(input));
  } else if (input.id === 'email') {
    // SPECIAL HANDLING FOR EMAIL - REAL-TIME VALIDATION
    input.addEventListener("input", function() {
      validateEmailInput(this);
    });
    input.addEventListener("blur", function() {
      validateEmailInput(this);
    });
  } else {
    input.addEventListener("blur", function() {
      validateInput(this);
    });
    if (input.tagName === "SELECT") {
      input.addEventListener("change", function() {
        validateInput(this);
      });
    } else {
      input.addEventListener("input", function() {
        validateInput(this);
      });
    }
  }
});

// Regular validation for other fields
function validateInput(input) {
  const id = input.id;
  const value = input.value.trim();
  const errorEl = getErrorEl(input);
  let errorMsg = "";
  let valid = true;

  // Skip email validation as it has its own function
  if (id === 'email') return validateEmailInput(input);

  // Required field validation
  if (input.hasAttribute("required") && !value) {
    errorMsg = "This field cannot be empty";
    valid = false;
  }

  // Name validation (fname, mname, lname)
  if (valid && ["fname", "mname", "lname"].includes(id)) {
    if (value && !rules.name.test(value)) {
      errorMsg = "Only letters are allowed";
      valid = false;
    }
  }

  // Contact validation
  if (valid && id === "contact") {
    if (value && !rules.contact.test(value)) {
      errorMsg = "Format: 09XXXXXXXXX or +639XXXXXXXXX";
      valid = false;
    }
  }

  // Birthdate validation
  if (valid && id === "bdate") {
    if (value) {
      const date = new Date(value);
      const today = new Date();
      if (isNaN(date.getTime())) {
        errorMsg = "Invalid date";
        valid = false;
      } else if (date > today) {
        errorMsg = "Birth date cannot be in the future";
        valid = false;
      }
    }
  }

  // Select validation
  if (valid && input.tagName === 'SELECT' && !value) {
    errorMsg = "This field is required";
    valid = false;
  }

  // Update UI
  updateUI(input, valid, errorMsg);
  toggleButton();

  return valid;
}

// Attach file validation to all file inputs
["application_letter", "grade_slip", "certificate_of_registration", "barangay_indigency", "student_id"]
  .forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("change", function () {
        validateFile(this);
      });
    }
  });

// Academic year auto-fill
const currentYear = new Date().getFullYear();
const acadYearInput = document.getElementById('acad_year');
if (acadYearInput) {
  acadYearInput.value = `${currentYear}-${currentYear + 1}`;
}

// FIXED FORM SUBMISSION HANDLER
applicationForm.addEventListener("submit", function (e) {
  e.preventDefault();

  // Final validation specifically for email
  const emailInput = document.getElementById('email');
  if (emailInput) {
    validateEmailInput(emailInput); // Force final validation
  }

  // Check if email is still checking or has duplicate
  if (validationStates.email.checking) {
    Swal.fire({
      icon: "warning",
      title: "Please wait",
      text: "Still checking email availability. Please wait a moment.",
    });
    return;
  }

  if (validationStates.email.duplicate) {
    Swal.fire({
      icon: "error",
      title: "Email Already Taken",
      text: "The email you entered is already registered. Please use a different email.",
    });
    emailInput.focus();
    return;
  }

  // Final validation before submission
  let hasErrors = false;
  const requiredInputs = applicationForm.querySelectorAll("input[required], select[required]");
  
  // Validate all fields first
  requiredInputs.forEach(input => {
    if (input.type === "file") {
      validateFile(input);
    } else if (input.id === 'email') {
      // Already validated above
    } else {
      validateInput(input);
    }
    
    const id = input.id;
    const isDuplicate = validationStates[id]?.duplicate;
    const isChecking = validationStates[id]?.checking;
    
    if (input.classList.contains("error") || !input.value.trim() || isDuplicate || isChecking) {
      hasErrors = true;
    }
  });

  if (hasErrors) {
    Swal.fire({
      icon: "error",
      title: "Validation Error",
      text: "Please complete all required fields and fix any errors before submitting.",
    });
    return;
  }

  // Show confirmation dialog
  Swal.fire({
    title: "Are you sure?",
    text: "Do you want to submit your application?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#6d53d3",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, submit it!"
  }).then((result) => {
    if (result.isConfirmed) {
      submitBtn.disabled = true;
      submitBtnText.textContent = 'Submitting...';
      submitBtnSpinner.classList.remove('hidden');
      applicationForm.submit();
    }
  });
});

// Success message
@if(session('success'))
Swal.fire({
  icon: 'success',
  title: 'You successfully submitted the Application',
  text: 'Stay tuned for the Announcement!',
  confirmButtonColor: '#6d53d3'
});
@endif

// Prevent form submission on Enter key press
applicationForm.addEventListener("keydown", function (e) {
  if (e.key === "Enter") {
    e.preventDefault();
  }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  // Add CSS for visual feedback
  const style = document.createElement('style');
  style.textContent = `
    .error { 
      border-color: #ef4444 !important; 
      background-color: #fef2f2 !important;
    }
    .valid { 
      border-color: #10b981 !important; 
      background-color: #f0fdf4 !important;
    }
    .error-message { 
      color: #ef4444; 
      font-size: 12px; 
      margin-top: 4px; 
      display: block; 
    }
    .fa-spinner {
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `;
  document.head.appendChild(style);
});
</script>

<style>
/* Select2 customization */
.select2-container--default .select2-selection--single {
  border: 1px solid #d1d5db !important;
  border-radius: 0.5rem !important;
  height: 42px !important;
  padding: 8px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 40px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 26px !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #7c3aed !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
  border: 1px solid #d1d5db !important;
  border-radius: 0.375rem !important;
}

.select2-dropdown {
  border: 1px solid #d1d5db !important;
  border-radius: 0.5rem !important;
}

/* Error and valid states */
.error { 
  border-color: #ef4444 !important; 
  background-color: #fef2f2 !important;
}
.valid { 
  border-color: #10b981 !important; 
  background-color: #f0fdf4 !important;
}
.error-message { 
  color: #ef4444; 
  font-size: 12px; 
  margin-top: 4px; 
  display: block; 
}
</style>
<script src="{{ asset('js/spinner.js') }}"></script>

</body>
</html>