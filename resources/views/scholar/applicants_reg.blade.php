x  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      <link rel="stylesheet" href="{{ asset('css/application_reg.css') }}">
      <style>
        /* Enhanced input focus styles */
        input:focus, select:focus {
          border-color: #7B2CBF;
          box-shadow: 0 0 8px rgba(123, 44, 191, 0.3);
          outline: none;
          transition: all 0.3s ease;
        }

        /* Icon positioning inside inputs */
        .input-with-icon {
          position: relative;
        }

        .input-icon {
          position: absolute;
          right: 10px;
          top: 50%;
          transform: translateY(-50%);
          color: #9CA3AF;
          pointer-events: none;
        }

        /* Circular Progress Indicator */
        .progress-container {
          display: flex;
          align-items: center;
          justify-content: center;
          margin-bottom: 30px;
          position: relative;
        }

        .progress-steps {
          display: flex;
          align-items: center;
          gap: 20px;
        }

        .progress-step {
          display: flex;
          flex-direction: column;
          align-items: center;
          position: relative;
        }

        .progress-circle {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          background: #E5E7EB;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #9CA3AF;
          font-size: 18px;
          transition: all 0.3s ease;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .progress-circle.active {
          background: linear-gradient(135deg, #7b2cbf, #9d4edd);
          color: white;
          transform: scale(1.1);
          box-shadow: 0 4px 12px rgba(123, 44, 191, 0.4);
        }

        .progress-line {
          position: absolute;
          top: 25px;
          left: 50px;
          width: 40px;
          height: 2px;
          background: #E5E7EB;
          z-index: -1;
          transition: background 0.3s ease;
        }

        .progress-line.active {
          background: linear-gradient(to right, #7b2cbf, #9d4edd);
        }

        .progress-label {
          margin-top: 8px;
          font-size: 12px;
          color: #6B7280;
          text-align: center;
          font-weight: 500;
        }

        /* Modern Tab Design */
        .tab-navigation {
          display: flex;
          background: linear-gradient(135deg, #f8fafc, #e2e8f0);
          border-radius: 12px;
          padding: 6px;
          margin-bottom: 30px;
          box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .tab-button {
          flex: 1;
          padding: 12px 16px;
          border-radius: 8px;
          background: transparent;
          border: none;
          color: #6B7280;
          font-weight: 500;
          transition: all 0.3s ease;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          cursor: pointer;
        }

        .tab-button:hover {
          background: rgba(255,255,255,0.8);
          transform: translateY(-2px);
          box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .tab-button.active {
          background: linear-gradient(135deg, #7b2cbf, #9d4edd);
          color: white;
          box-shadow: 0 4px 12px rgba(123, 44, 191, 0.3);
        }

        /* Card-based Layout */
        .form-card {
          background: white;
          border-radius: 16px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.1);
          padding: 32px;
          margin-bottom: 24px;
          transition: all 0.3s ease;
        }

        .form-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        /* Enhanced button styles */
        .enhanced-btn {
          background: linear-gradient(135deg, #7b2cbf, #9d4edd);
          border: none;
          color: white;
          padding: 12px 24px;
          border-radius: 8px;
          font-weight: 600;
          transition: all 0.3s ease;
          box-shadow: 0 4px 12px rgba(123, 44, 191, 0.3);
          cursor: pointer;
        }

        .enhanced-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(123, 44, 191, 0.4);
        }

        .enhanced-btn:disabled {
          opacity: 0.6;
          cursor: not-allowed;
          transform: none;
        }

        /* Required field asterisk */
        .required-asterisk {
          color: #EF4444;
        }

        /* Tooltip styles */
        .tooltip {
          position: relative;
          display: inline-block;
        }

        .tooltip .tooltiptext {
          visibility: hidden;
          width: 200px;
          background-color: #555;
          color: #fff;
          text-align: center;
          border-radius: 6px;
          padding: 5px;
          position: absolute;
          z-index: 1;
          bottom: 125%;
          left: 50%;
          margin-left: -100px;
          opacity: 0;
          transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
          visibility: visible;
          opacity: 1;
        }

        /* Input row improvements */
        .input-row {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 20px;
          margin-bottom: 24px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
          .input-row {
            grid-template-columns: 1fr;
          }
        }

        .input-group {
          position: relative;
        }

        .input-group label {
          display: block;
          margin-bottom: 6px;
          font-weight: 600;
          color: #374151;
          font-size: 14px;
          transition: 0.2s ease all;
        }

        .input-group input,
        .input-group select {
          width: 100%;
          padding: 0.75rem 1rem;
          border: 2px solid #E5E7EB;
          border-radius: 8px;
          font-size: 14px;
          transition: all 0.3s ease;
          box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .input-group input:focus,
        .input-group select:focus {
          border-color: #7b2cbf;
          box-shadow: 0 0 6px rgba(123, 44, 191, 0.3);
          outline: none;
        }

        .input-group input:hover,
        .input-group select:hover {
          border-color: #9d4edd;
        }



        /* Animation keyframes */
        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .tab-content {
          animation: fadeInUp 0.5s ease-out;
        }
      </style>
      <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <title>Personal Information Form</title>
    </head>
    <body>
<div class="banner-grad flex items-center w-full h-16 px-6 text-white relative">
  <div class="flex items-center space-x-3">
    <button class="back-btn text-xl" onclick="history.back()">←</button>
    <img src="/images/LYDO.png" alt="LYDO Logo" class="h-10" />
    <div>
      <h1 class="text-xl font-bold leading-tight">LYDO SCHOLARSHIP</h1>
      <p class="text-[10px] uppercase tracking-widest">
        PARA SA KABATAAN, PARA SA KINABUKASAN.
      </p>
    </div>
  </div>
</div>


      <div class="w-full overflow-y-auto" style="height: calc(100vh - 4rem);">
        <div class="p-6">
          <h1 class="text-2xl font-bold text-center mb-2">Applicants Registration</h1>
          <p class="text-center text-gray-600 mb-6">Fill out the required details below</p>
          <p class="text-center text-red-500 text-sm mb-4">* Indicates required fields</p>

          @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger mb-4">
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Circular Progress Indicator -->
          <div class="progress-container">
            <div class="progress-steps">
              <div class="progress-step">
                <div class="progress-circle active" id="step1-circle">
                  <i class="fas fa-user"></i>
                </div>
                <div class="progress-label">Personal Info</div>
              </div>
              <div class="progress-line" id="line1"></div>
              <div class="progress-step">
                <div class="progress-circle" id="step2-circle">
                  <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="progress-label">Education</div>
              </div>
              <div class="progress-line" id="line2"></div>
              <div class="progress-step">
                <div class="progress-circle" id="step3-circle">
                  <i class="fas fa-file-upload"></i>
                </div>
                <div class="progress-label">Requirements</div>
              </div>
            </div>
          </div>

          <!-- Modern Tab Navigation -->
          <div class="tab-navigation">
            <button id="tab1-btn" class="tab-button active" data-tab="1">
              <i class="fas fa-user"></i>
              Personal Information
            </button>
            <button id="tab2-btn" class="tab-button" data-tab="2">
              <i class="fas fa-graduation-cap"></i>
              Educational Attainment
            </button>
            <button id="tab3-btn" class="tab-button" data-tab="3">
              <i class="fas fa-file-upload"></i>
              Application Requirements
            </button>
          </div>

          <form id="applicationForm" method="POST" action="{{ route('applicants.register') }}" enctype="multipart/form-data">
          @csrf

            <!-- Tab 1: Personal Information -->
            <div id="tab1" class="tab-content active">
              <!-- Name Fields -->
              <div class="input-row name-fields-row">
                <div class="input-group">
                  <label for="fname">First Name<span class="required-asterisk">*</span></label>
                  <input type="text" id="fname" name="applicant_fname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="First Name" />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="mname">Middle Name</label>
                  <input type="text" id="mname" name="applicant_mname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Middle Name" />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="lname">Last Name<span class="required-asterisk">*</span></label>
                  <input type="text" id="lname" name="applicant_lname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="Last Name" />
                  <small class="error-message"></small>
                </div>
                <div class="input-group suffix-group">
                  <label for="suffix">Suffix</label>
                  <input type="text" id="suffix" name="applicant_suffix" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Suffix" />
                  <small class="error-message"></small>
                </div>
              </div>

              <!-- Personal Details -->
              <div class="input-row">
                <div class="input-group">
                  <label for="gender">Gender<span class="required-asterisk">*</span></label>
                  <select id="gender" name="applicant_gender"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"  required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                  </select>
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="bdate">Birth Date<span class="required-asterisk">*</span></label>
                  <input type="date" id="bdate" name="applicant_bdate"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"  required />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="civil_status">Civil Status<span class="required-asterisk">*</span></label>
                  <select id="civil_status" name="applicant_civil_status"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"  required>
                    <option value="">Select Civil Status</option>
                    <option value="single">Single</option>
                    <option value="married">Married</option>
                    <option value="widowed">Widowed</option>
                    <option value="divorced">Divorced</option>
                  </select>
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="brgy">Barangay<span class="required-asterisk">*</span></label>
                  <select id="brgy" name="applicant_brgy"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"  required>
                    <option value="">-- Select Barangay --</option>
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
                  <input type="email" id="email" name="applicant_email"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="Email" />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="contact">Contact Number</label>
                  <input type="tel" id="contact" name="applicant_contact_number"
                     class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="Contact Number" />
                  <small class="error-message"></small>
                </div>
              </div>

              <div class="flex justify-end mt-6">
                <button type="button" class="next-btn bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700" onclick="nextTab(1)">Next</button>
              </div>
            </div>

            <!-- Tab 2: Educational Attainment -->
            <div id="tab2" class="tab-content hidden">
              <div class="input-row">
                <div class="input-group" style="width: 100%">
                  <label for="school_name">School Name</label>
                  <select id="school_name" name="applicant_school_name"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"  required>
                    <option value="">-- Select School --</option>
                    <!-- State Universities -->
                    <option value="USTP CDO">
                      University of Science and Technology of Southern Philippines
                      (USTP) – Cagayan de Oro
                    </option>
                    <option value="USTP Claveria">
                      University of Science and Technology of Southern Philippines
                      (USTP) – Claveria
                    </option>
                    <option value="USTP Villanueva">
                      University of Science and Technology of Southern Philippines
                      (USTP) – Villanueva
                    </option>
                    <option value="MSU Naawan">
                      Mindanao State University – Naawan (MSU-Naawan)
                    </option>
                    <option value="MOSCAT">
                      Misamis Oriental State College of Agriculture and Technology
                      (MOSCAT), Claveria
                    </option>
                    <!-- Community Colleges -->
                    <option value="Opol Community College">
                      Opol Community College
                    </option>
                    <option value="Tagoloan Community College">
                      Tagoloan Community College
                    </option>
                    <option value="Bugo Community College">
                      Bugo Community College
                    </option>
                    <option value="Initao Community College">
                      Initao Community College
                    </option>
                    <option value="Magsaysay College">
                      Magsaysay College, Misamis Oriental
                    </option>
                    <!-- Private Colleges & Universities -->
                    <option value="Liceo de Cagayan University">
                      Liceo de Cagayan University, CDO
                    </option>
                    <option value="PHINMA COC">
                      PHINMA Cagayan de Oro College
                    </option>
                    <option value="Capitol University">
                      Capitol University, CDO
                    </option>
                    <option value="Lourdes College">Lourdes College, CDO</option>
                    <option value="Blessed Mother College">
                      Blessed Mother College, CDO
                    </option>
                    <option value="Pilgrim Christian College">
                      Pilgrim Christian College, CDO
                    </option>
                    <option value="Gingoog Christian College">
                      Gingoog Christian College
                    </option>
                    <option value="Christ the King College">
                      Christ the King College, Gingoog City
                    </option>
                    <option value="St. Rita’s College">
                      St. Rita’s College of Balingasag
                    </option>
                    <option value="St. Peter’s College">
                      St. Peter’s College of Balingasag
                    </option>
                    <option value="Saint John Vianney Seminary">
                      Saint John Vianney Theological Seminary, CDO
                    </option>
                    <option value="Asian College of Science and Technology">
                      Asian College of Science and Technology, CDO
                    </option>
                    <!-- Others -->
                    <option value="Others">Others</option>
                  </select>
                  <input type="text" id="school_name_other" name="applicant_school_name_other" placeholder="Please specify your school" style=" display: none; margin-top: 8px; padding: 10px; border: 1px solid black; border-radius: 8px; font-size: 14px; outline: none; width: 100%;"/>
                  <small class="error-message"></small>
                </div>
              </div>

              <!-- Academic Details -->
              <div class="input-row">
                <div class="input-group">
                  <label for="year_level">Year Level</label>
                  <select id="year_level" name="applicant_year_level"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"  required>
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
                  <input type="text" id="course" name="applicant_course"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"  required placeholder="Course" />
                  <small class="error-message"></small>
                </div>
                <div class="input-group">
                  <label for="acad_year">Academic Year</label>
                  <input type="text" id="acad_year" name="applicant_acad_year" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="e.g., 2023-2024" readonly />
                  <small class="error-message"></small>
                </div>
              </div>

              <div class="flex justify-between mt-6">
                <button type="button" class="prev-btn bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600" onclick="prevTab(2)">Previous</button>
                <button type="button" class="next-btn bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700" onclick="nextTab(2)">Next</button>
              </div>
            </div>

            <!-- Tab 3: Application Requirements -->
            <div id="tab3" class="tab-content hidden">
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
                  <input type="file" id="student_id" name="student_id" accept="application/pdf"required class="input-file"/>
                  <small class="error-message"></small>
                </div>
              </div>

              <div class="flex justify-between mt-6">
                <button type="button" class="prev-btn bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600" onclick="prevTab(3)">Previous</button>
                <button type="submit" class="login-btn flex justify-center items-center bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700" id="submitBtn">
                  <span id="submitBtnText">Submit</span>
                  <svg id="submitBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75"fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                  </svg>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

  <script>
  const applicationForm = document.getElementById("applicationForm");
  const submitBtn = document.getElementById("submitBtn");
  const submitBtnText = document.getElementById('submitBtnText');
  const submitBtnSpinner = document.getElementById('submitBtnSpinner');

  const rules = {
    name: /^[A-Za-z\s]+$/, // letters and spaces only
    contact: /^(09\d{9}|\+639\d{9})$/,
    gmail: /^[a-zA-Z0-9._%+-]+@gmail\.com$/  // PH number format
  };

  function validateInput(input) {
    const id = input.id;
    const value = input.value.trim();
    const errorEl = input.nextElementSibling;
    let errorMsg = "";
    let valid = true;

    // name validation (fname, mname, lname)
    if (["fname", "mname", "lname"].includes(id)) {
      if (value && !rules.name.test(value)) {
        errorMsg = "Only letters are allowed";
        valid = false;
      }
    }

    // contact validation
    if (id === "contact") {
      if (value && !rules.contact.test(value)) {
        errorMsg = "Format: 09XXXXXXXXX or +639XXXXXXXXX";
        valid = false;
      }
    }
    if (id === "email") {
      if (value && !rules.gmail.test(value)) {
        errorMsg = "Email must end with @gmail.com";
        valid = false;
      }
    }
    // birthdate validation
    if (id === "bdate") {
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

    // update UI
    if (!valid) {
      input.classList.add("error");
      input.classList.remove("valid");
      errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>' + errorMsg;
    } else {
      input.classList.remove("error");
      input.classList.add("valid");
      errorEl.innerHTML = "";
    }

    toggleButton();
    return valid;
  }



  function toggleButton() {
    const hasErrorMessage = Array.from(
      applicationForm.querySelectorAll(".error-message")
    ).some((msg) => msg.textContent.trim() !== "");

    const hasEmptyRequired = Array.from(
      applicationForm.querySelectorAll("input[required], select[required]")
    ).some((input) => {
      if (input.type === "file") return input.files.length === 0;
      return !input.value.trim();
    });

    // Removed disabling of submit button to allow form submission
  }

  // Attach events
  applicationForm.querySelectorAll("input, select").forEach((input) => {
    if (input.type === "file") {
      input.addEventListener("change", () => validateFile(input)); // ✅ real-time file validation
    } else {
      input.addEventListener("blur", () => validateInput(input));
      if (input.tagName === "SELECT") {
        input.addEventListener("change", () => validateInput(input));
      } else {
        input.addEventListener("input", () => validateInput(input));
      }
    }
  });

  // Initial disable on load
  toggleButton();

  // Submit event removed to allow form submission

  // Prevent form submission on Enter key press
  applicationForm.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
    }
  });
  </script>
  <script>
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
    return valid;
  }

  // Attach sa lahat ng file inputs
  ["application_letter", "grade_slip", "certificate_of_registration", "barangay_indigency", "student_id"]
    .forEach(id => {
      const input = document.getElementById(id);
      input.addEventListener("change", function () {
        validateFile(this);
      });
    });

  // Email duplicate check
  function checkEmailDuplicate(emailInput) {
    const email = emailInput.value.trim();
    if (!email) return;

    fetch('/check-applicant-email', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
      if (data.exists) {
        updateUI(emailInput, false, "this email is already taken please try another email");
      } else {
        // Clear any previous duplicate error
        const errorEl = getErrorEl(emailInput);
        if (errorEl && errorEl.textContent === "this email is already taken please try another email") {
          updateUI(emailInput, true);
        }
      }
      toggleButton();
    })
    .catch(error => {
      console.error('Error checking email:', error);
    });
  }

  // Attach to email input
  const emailInput = document.getElementById('email');
  emailInput.addEventListener('blur', function() {
    checkEmailDuplicate(this);
  });
  </script>

  <script>
  applicationForm.addEventListener("submit", function (e) {
    e.preventDefault(); // I-stop muna ang default submission

    // Check kung may error messages o empty required fields
    const hasErrorMessage = Array.from(
      applicationForm.querySelectorAll(".error-message")
    ).some((msg) => msg.textContent.trim() !== "");

    const hasEmptyRequired = Array.from(
      applicationForm.querySelectorAll("input[required], select[required]")
    ).some((input) => {
      if (input.type === "file") return input.files.length === 0;
      return !input.value.trim();
    });

    if (hasErrorMessage || hasEmptyRequired) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Please complete all required fields before submitting.",
      });
      return; // wag ituloy ang submit
    }

    // Kung valid lahat, ipakita ang confirmation
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
        applicationForm.submit(); // tuloy ang form submit
      }
    });
  });
  </script>

  <script>
  // Tab switching functionality
  function showTab(tabNumber) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.add('hidden'));

    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(btn => {
      btn.classList.remove('active', 'text-purple-600', 'border-purple-600', 'bg-purple-50');
      btn.classList.add('text-gray-500');
    });

    // Show selected tab
    document.getElementById('tab' + tabNumber).classList.remove('hidden');

    // Activate selected tab button
    const activeBtn = document.getElementById('tab' + tabNumber + '-btn');
    activeBtn.classList.add('active', 'text-purple-600', 'border-purple-600', 'bg-purple-50');
    activeBtn.classList.remove('text-gray-500');

    // Update progress indicator
    updateProgress(tabNumber);
  }

  function updateProgress(tabNumber) {
    // Update circular progress indicators
    for (let i = 1; i <= 3; i++) {
      const circle = document.getElementById(`step${i}-circle`);
      const line = document.getElementById(`line${i - 1}`);

      if (i <= tabNumber) {
        circle.classList.add('active');
        if (line) line.classList.add('active');
      } else {
        circle.classList.remove('active');
        if (line) line.classList.remove('active');
      }
    }
  }

  function nextTab(currentTab) {
    // Validate current tab before proceeding
    if (!validateTab(currentTab)) {
      return;
    }
    showTab(currentTab + 1);
  }

  function prevTab(currentTab) {
    showTab(currentTab - 1);
  }

  function validateTab(tabNumber) {
    let isValid = true;
    const tab = document.getElementById('tab' + tabNumber);
    const requiredInputs = tab.querySelectorAll('input[required], select[required]');

    requiredInputs.forEach(input => {
      if (input.type === 'file') {
        if (input.files.length === 0) {
          isValid = false;
          validateFile(input);
        }
      } else if (!input.value.trim()) {
        isValid = false;
        validateInput(input);
      }
    });

    return isValid;
  }

  // Initialize first tab as active
  showTab(1);

  // Add click event listeners to tab buttons
  document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', function() {
      const tabNumber = parseInt(this.getAttribute('data-tab'));
      showTab(tabNumber);
    });
  });
  </script>

  <script>
  const schoolSelect = document.getElementById("school_name");
  const schoolOtherInput = document.getElementById("school_name_other");

  schoolSelect.addEventListener("change", function () {
    if (this.value === "Others") {
      schoolOtherInput.style.display = "block";
      schoolOtherInput.setAttribute("required", "required");
    } else {
      schoolOtherInput.style.display = "none";
      schoolOtherInput.removeAttribute("required");
      schoolOtherInput.value = "";
    }
  });
  </script>

  <script>
  // Kunin ang current year
  const currentYear = new Date().getFullYear();
  const acadYearInput = document.getElementById("acad_year");
  // Auto-set sa current acad year (e.g., 2025-2026)
  acadYearInput.value = `${currentYear}-${currentYear + 1}`;
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
