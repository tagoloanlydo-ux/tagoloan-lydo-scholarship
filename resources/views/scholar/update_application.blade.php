  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="{{ asset('css/application_reg.css') }}">
      <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
      <title>Update Your Application</title>
    </head>
    <body>
      <div class="banner-grad flex items-center justify-between w-full h-25  px-6 text-white">
        <div class="flex items-center">
            <img src="/images/LYDO.png" alt="LYDO Logo" class="h-10 mr-4"/>
          <div>
            <h1 class="text-2xl font-bold">LYDO SCHOLARSHIP</h1>
            <p class="text-xs tracking-widest">
              PARA SA KABATAAN, PARA SA KINABUKASAN.
            </p>
          </div>
        </div>
      </div>
      <div class="container-wrapper mt-5">
        <!-- Personal Form -->
        <div class="login-container">
          <!-- Back button -->
          <button class="back-btn" onclick="history.back()">←</button>
          <h1>Update Your Application</h1>
          <p class="subtitle">Update the required details below. Fields highlighted in red are mandatory for this update.</p>

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

          <form id="applicationForm" method="POST" action="{{ route('scholar.updateApplication', $applicant->applicant_id) }}" enctype="multipart/form-data">
          @csrf
            <!-- Name Fields -->
            <div class="input-row">
              <div class="input-group">
                <label for="fname">First Name</label>
                <input type="text" id="fname" name="applicant_fname" value="{{ old('applicant_fname', $applicant->applicant_fname) }}" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_fname', $issues ?? [])) border-red-500 @endif" required placeholder="First Name" class="input-field" />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="mname">Middle Name</label>
                <input type="text" id="mname" name="applicant_mname" value="{{ old('applicant_mname', $applicant->applicant_mname) }}" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_mname', $issues ?? [])) border-red-500 @endif" placeholder="Middle Name" class="input-field" />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="applicant_lname" value="{{ old('applicant_lname', $applicant->applicant_lname) }}" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_lname', $issues ?? [])) border-red-500 @endif" required placeholder="Last Name" class="input-field" />
                <small class="error-message"></small>
              </div>
              <div class="input-group" style="width: 10px">
                <label for="suffix">Suffix</label>
                <input type="text" id="suffix" name="applicant_suffix" value="{{ old('applicant_suffix', $applicant->applicant_suffix) }}" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_suffix', $issues ?? [])) border-red-500 @endif" placeholder="Suffix" class="input-field" />
                <small class="error-message"></small>
              </div>
            </div>

            <!-- Personal Details -->
            <div class="input-row">
              <div class="input-group">
                <label for="gender">Gender</label>
                <select id="gender" name="applicant_gender" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_gender', $issues ?? [])) border-red-500 @endif" required>
                  <option value="">Select Gender</option>
                  <option value="male" {{ old('applicant_gender', $applicant->applicant_gender) == 'male' ? 'selected' : '' }}>Male</option>
                  <option value="female" {{ old('applicant_gender', $applicant->applicant_gender) == 'female' ? 'selected' : '' }}>Female</option>
                  <option value="other" {{ old('applicant_gender', $applicant->applicant_gender) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="bdate">Birth Date</label>
                <input type="date" id="bdate" name="applicant_bdate" value="{{ old('applicant_bdate', $applicant->applicant_bdate) }}" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_bdate', $issues ?? [])) border-red-500 @endif" required />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="civil_status">Civil Status</label>
                <select id="civil_status" name="applicant_civil_status" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_civil_status', $issues ?? [])) border-red-500 @endif" required>
                  <option value="">Select Civil Status</option>
                  <option value="single" {{ old('applicant_civil_status', $applicant->applicant_civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                  <option value="married" {{ old('applicant_civil_status', $applicant->applicant_civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                  <option value="widowed" {{ old('applicant_civil_status', $applicant->applicant_civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                  <option value="divorced" {{ old('applicant_civil_status', $applicant->applicant_civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                </select>
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="brgy">Barangay</label>
                <select id="brgy" name="applicant_brgy" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_brgy', $issues ?? [])) border-red-500 @endif" required>
                  <option value="">-- Select Barangay --</option>
                  <option value="Amoros" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Amoros' ? 'selected' : '' }}>Amoros</option>
                  <option value="Baluarte" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Baluarte' ? 'selected' : '' }}>Baluarte</option>
                  <option value="Casinglot" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Casinglot' ? 'selected' : '' }}>Casinglot</option>
                  <option value="Gracia" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Gracia' ? 'selected' : '' }}>Gracia</option>
                  <option value="Mohon" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Mohon' ? 'selected' : '' }}>Mohon</option>
                  <option value="Natumulan" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Natumulan' ? 'selected' : '' }}>Natumulan</option>
                  <option value="Poblacion" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                  <option value="Rosario" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Rosario' ? 'selected' : '' }}>Rosario</option>
                  <option value="Santa Ana" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Santa Ana' ? 'selected' : '' }}>Santa Ana</option>
                  <option value="Santa Cruz" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                  <option value="San Vicente" {{ old('applicant_brgy', $applicant->applicant_brgy) == 'San Vicente' ? 'selected' : '' }}>San Vicente</option>
                </select>
                <small class="error-message"></small>
              </div>
            </div>

            <!-- Contact Details -->
            <div class="input-row">
              <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="applicant_email" value="{{ old('applicant_email', $applicant->applicant_email) }}" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_email', $issues ?? [])) border-red-500 @endif" required placeholder="Email" />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="contact">Contact Number</label>
                <input type="tel" id="contact" name="applicant_contact_number" value="{{ old('applicant_contact_number', $applicant->applicant_contact_number) }}" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_contact_number', $issues ?? [])) border-red-500 @endif" required placeholder="Contact Number"/>
                <small class="error-message"></small>
              </div>

              <div class="input-group" style="width: 100px">
                <label for="school_name">School Name</label>
                <select id="school_name" name="applicant_school_name" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_school_name', $issues ?? [])) border-red-500 @endif" required>
                  <option value="">-- Select School --</option>

                  <!-- State Universities -->
                  <option value="USTP CDO" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'USTP CDO' ? 'selected' : '' }}>
                    University of Science and Technology of Southern Philippines
                    (USTP) – Cagayan de Oro
                  </option>
                  <option value="USTP Claveria" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'USTP Claveria' ? 'selected' : '' }}>
                    University of Science and Technology of Southern Philippines
                    (USTP) – Claveria
                  </option>
                  <option value="USTP Villanueva" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'USTP Villanueva' ? 'selected' : '' }}>
                    University of Science and Technology of Southern Philippines
                    (USTP) – Villanueva
                  </option>
                  <option value="MSU Naawan" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'MSU Naawan' ? 'selected' : '' }}>
                    Mindanao State University – Naawan (MSU-Naawan)
                  </option>
                  <option value="MOSCAT" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'MOSCAT' ? 'selected' : '' }}>
                    Misamis Oriental State College of Agriculture and Technology
                    (MOSCAT), Claveria
                  </option>

                  <!-- Community Colleges -->
                  <option value="Opol Community College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Opol Community College' ? 'selected' : '' }}>
                    Opol Community College
                  </option>
                  <option value="Tagoloan Community College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Tagoloan Community College' ? 'selected' : '' }}>
                    Tagoloan Community College
                  </option>
                  <option value="Bugo Community College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Bugo Community College' ? 'selected' : '' }}>
                    Bugo Community College
                  </option>
                  <option value="Initao Community College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Initao Community College' ? 'selected' : '' }}>
                    Initao Community College
                  </option>
                  <option value="Magsaysay College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Magsaysay College' ? 'selected' : '' }}>
                    Magsaysay College, Misamis Oriental
                  </option>

                  <!-- Private Colleges & Universities -->
                  <option value="Liceo de Cagayan University" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Liceo de Cagayan University' ? 'selected' : '' }}>
                    Liceo de Cagayan University, CDO
                  </option>
                  <option value="PHINMA COC" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'PHINMA COC' ? 'selected' : '' }}>
                    PHINMA Cagayan de Oro College
                  </option>
                  <option value="Capitol University" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Capitol University' ? 'selected' : '' }}>
                    Capitol University, CDO
                  </option>
                  <option value="Lourdes College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Lourdes College' ? 'selected' : '' }}>Lourdes College, CDO</option>
                  <option value="Blessed Mother College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Blessed Mother College' ? 'selected' : '' }}>
                    Blessed Mother College, CDO
                  </option>
                  <option value="Pilgrim Christian College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Pilgrim Christian College' ? 'selected' : '' }}>
                    Pilgrim Christian College, CDO
                  </option>
                  <option value="Gingoog Christian College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Gingoog Christian College' ? 'selected' : '' }}>
                    Gingoog Christian College
                  </option>
                  <option value="Christ the King College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Christ the King College' ? 'selected' : '' }}>
                    Christ the King College, Gingoog City
                  </option>
                  <option value="St. Rita’s College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'St. Rita’s College' ? 'selected' : '' }}>
                    St. Rita’s College of Balingasag
                  </option>
                  <option value="St. Peter’s College" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'St. Peter’s College' ? 'selected' : '' }}>
                    St. Peter’s College of Balingasag
                  </option>
                  <option value="Saint John Vianney Seminary" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Saint John Vianney Seminary' ? 'selected' : '' }}>
                    Saint John Vianney Theological Seminary, CDO
                  </option>
                  <option value="Asian College of Science and Technology" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Asian College of Science and Technology' ? 'selected' : '' }}>
                    Asian College of Science and Technology, CDO
                  </option>

                  <!-- Others -->
                  <option value="Others" {{ old('applicant_school_name', $applicant->applicant_school_name) == 'Others' ? 'selected' : '' }}>Others</option>
                </select>
                <input type="text"
                  id="school_name_other"
                  name="applicant_school_name_other"
                  value="{{ old('applicant_school_name_other', $applicant->applicant_school_name_other) }}"
                  placeholder="Please specify your school"
                  style=" display: none; margin-top: 8px; padding: 10px; border: 1px solid black; border-radius: 8px; font-size: 14px; outline: none; width: 100%; "/>
                <small class="error-message"></small>
              </div>
            </div>
            <div class="input-row">
              <div class="input-group">
                <label for="year_level">Year Level</label>
                <select id="year_level" name="applicant_year_level" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_year_level', $issues ?? [])) border-red-500 @endif" required>
                  <option value="">Select Year Level</option>
                  <option value="1st Year" {{ old('applicant_year_level', $applicant->applicant_year_level) == '1st Year' ? 'selected' : '' }}>1st Year</option>
                  <option value="2nd Year" {{ old('applicant_year_level', $applicant->applicant_year_level) == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                  <option value="3rd Year" {{ old('applicant_year_level', $applicant->applicant_year_level) == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                  <option value="4th Year" {{ old('applicant_year_level', $applicant->applicant_year_level) == '4th Year' ? 'selected' : '' }}>4th Year</option>

                </select>
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="applicant_course" value="{{ old('applicant_course', $applicant->applicant_course) }}" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_course', $issues ?? [])) border-red-500 @endif" required placeholder="Course" />
                <small class="error-message"></small>
              </div>
            <div class="input-group">
              <label for="acad_year">Academic Year</label>
              <input
                type="text"
                id="acad_year"
                name="applicant_acad_year"
                value="{{ old('applicant_acad_year', $applicant->applicant_acad_year) }}"
                class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 @if(in_array('applicant_acad_year', $issues ?? [])) border-red-500 @endif"
                required
                placeholder="e.g., 2023-2024"
                readonly
              />
              <small class="error-message"></small>
            </div>
          </div>
        </div>
        <!-- Credentials Form -->
        <div class="credentials-container">
          <h1>Upload Documents</h1>
           <p class="subtitle">Submit required PDF files</p>
            <div class="credentials-row">
                <div class="input-row">
                <div class="input-group">
                  <label for="application_letter">Application Letter</label>
                    @if($application->application_letter) <a href="{{ asset('storage/' . $application->application_letter) }}" target="_blank">View existing</a> @endif
                    <input type="file" id="application_letter" name="application_letter" accept="application/pdf" @if(in_array('application_letter', $issues ?? [])) required @endif class="input-file @if(in_array('application_letter', $issues ?? [])) border-red-500 @endif"/>
                    <small class="error-message"></small>
                </div>

                <div class="input-group">
                  <label for="grade_slip">Grade Slip</label>
                  @if($application->grade_slip) <a href="{{ asset('storage/' . $application->grade_slip) }}" target="_blank">View existing</a> @endif
                  <input type="file"id="grade_slip"name="grade_slip" accept="application/pdf" @if(in_array('grade_slip', $issues ?? [])) required @endif class="input-file @if(in_array('grade_slip', $issues ?? [])) border-red-500 @endif"/>
                  <small class="error-message"></small>
                </div>
              </div>

              <div class="input-row">
                  <div class="input-group">
                    <label for="certificate_of_registration">Certificate of Registration</label>
                    @if($application->cert_of_reg) <a href="{{ asset('storage/' . $application->cert_of_reg) }}" target="_blank">View existing</a> @endif
                    <input type="file" id="certificate_of_registration" name="certificate_of_registration" accept="application/pdf"  @if(in_array('cert_of_reg', $issues ?? [])) required @endif class="input-file @if(in_array('cert_of_reg', $issues ?? [])) border-red-500 @endif"/>
                    <small class="error-message"></small>
                  </div>

                  <div class="input-group">
                    <label for="barangay_indigency">Barangay Indigency</label>
                    @if($application->brgy_indigency) <a href="{{ asset('storage/' . $application->brgy_indigency) }}" target="_blank">View existing</a> @endif
                    <input type="file" id="barangay_indigency" name="barangay_indigency" accept="application/pdf" @if(in_array('brgy_indigency', $issues ?? [])) required @endif class="input-file @if(in_array('brgy_indigency', $issues ?? [])) border-red-500 @endif"/>
                    <small class="error-message"></small>
                  </div>
                </div>

                  <div class="input-row">
                    <div class="input-group">
                      <label for="student_id">Student ID</label>
                      @if($application->student_id) <a href="{{ asset('storage/' . $application->student_id) }}" target="_blank">View existing Student ID</a> @endif
                      <input type="file" id="student_id" name="student_id" accept="application/pdf" @if(in_array('student_id', $issues ?? [])) required @endif class="input-file @if(in_array('student_id', $issues ?? [])) border-red-500 @endif"/>
                      <small class="error-message"></small>
                    </div>
                  </div>

                  <div class="input-group btn-group">
                    <button type="submit" class="login-btn flex justify-center items-center" id="submitBtn">
                      <span id="submitBtnText">Submit</span>
                      <svg id="submitBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
                        <circle class="opacity-25" cx="12" cy="12"r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
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
    contact: /^(09\d{9}|\+639\d{9})$/, // PH number format
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
      errorEl.textContent = errorMsg;
    } else {
      input.classList.remove("error");
      input.classList.add("valid");
      errorEl.textContent = "";
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
      if (errorEl) errorEl.textContent = errorMsg;
    } else {
      input.classList.remove("error");
      input.classList.add("valid");
      if (errorEl) errorEl.textContent = "";
    }
  }

  function validateFile(input) {
    const file = input.files[0];
    let valid = true;
    let errorMsg = "";

    if (!file) {
      if (input.hasAttribute('required')) {
        valid = false;
        errorMsg = "This file is required";
      }
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
  @if(session('success'))
  Swal.fire({
    icon: 'success',
    title: 'You successfully submitted the Application',
    text: 'Stay tuned for the Announcement!',
    confirmButtonColor: '#6d53d3'
  });
  @endif
  </script>
  <script>
    // Kunin ang current year
    const currentYear = new Date().getFullYear();
    const acadYearInput = document.getElementById("acad_year");

    // Auto-set sa current acad year (e.g., 2025-2026)
    acadYearInput.value = `${currentYear}-${currentYear + 1}`;
  </script>
  
            <script>
              const schoolSelect = document.getElementById("school_name");
              const schoolOtherInput =
                document.getElementById("school_name_other");

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

    </body>
  </html>
