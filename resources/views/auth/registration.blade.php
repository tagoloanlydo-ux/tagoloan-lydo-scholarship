<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Personal Information Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/lydo_reg.css') }}" />
     <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
  </head>
  <style>
  .banner-grad {
        background: linear-gradient(90deg, #4c1d95 0%, #7e22ce 100%);
    height: 100px;
    position: relative;
}

/* Back button */
.back-btn {
    background: transparent;
    border: none;
    margin-right: 20px;
    font-size: 28px;
    color: rgb(0, 0, 0);
    font-size: 50px;
    cursor: pointer;
}   
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

  <body>
  <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
                            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
</div>
    <div class="banner-grad flex items-center justify-center md:justify-between w-full px-4 md:px-6 text-white">
      <div class="flex items-center">
        <img src="/images/LYDO.png" alt="LYDO Logo" class="h-8 md:h-10 mr-2 md:mr-4"/>
        <div>
          <h1 class="text-lg md:text-2xl font-bold">LYDO SCHOLARSHIP</h1>
          <p class="text-xs tracking-widest">
            PARA SA KABATAAN, PARA SA KINABUKASAN.
          </p>
        </div>
      </div>
    </div>

<form id="registrationForm" action="{{ route('lydopers.register') }}" method="POST" novalidate>
      @csrf
      <div class="container-wrapper mt-10">
        <!-- Personal Form -->
        <div class="login-container">
          <!-- Back button -->
        <button class="back-btn" type="button" onclick="window.location.href='{{ route('login') }}'">←</button>

          <h1>Personal Information</h1>
           <p class="subtitle">Fill out the required details below</p>

            <div class="input-row">
              <div class="input-group" >
                <label for="fname">First Name</label>
                <input type="text" id="fname" name="lydopers_fname"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="mname">Middle Name</label>
                <input type="text" id="mname" name="lydopers_mname"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"   required/>
                <small class="error-message"></small>
              </div>
                            <div class="input-group">
                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="lydopers_lname"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>

              <div class="input-group" style="width: 20px">
                <label for="suffix">Suffix</label>
                <input type="text" id="suffix" name="lydopers_suffix"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"  />
                <small class="error-message"></small>
              </div>
            </div>

            <div class="input-row">
              <div class="input-group">
                <label for="bdate">Birth Date</label>
                <input type="date" id="bdate" name="lydopers_bdate"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="contact">Contact Number</label>
                <input
                  type="tel"
                  id="contact"
                  name="lydopers_contact_number"
                   class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                  required
                />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="address">Address</label>
                <input
                  type="text"
                  id="address"
                   class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                  name="lydopers_address"
                  required
                />
                <small class="error-message"></small>
              </div>
            </div>

            <div class="input-row">
              <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="lydopers_email"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="role">Role</label>
                <select id="role" name="lydopers_role"  class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                  <option value="">Select Role</option>
                  <option value="lydo_admin" @if($lydoAdminExists) disabled title="Admin role is already taken" @endif>Admin</option>
                  <option value="lydo_staff">LYDO Staff</option>
                  <option value="mayor_staff">Mayor Staff</option>
                </select>
                <small class="error-message"></small>
                @if($lydoAdminExists)
                  <small style="color: #666; font-size: 12px;">Note: Admin role is already assigned to another user.</small>
                @endif
              </div>
            </div>
        </div>

        <!-- Credentials Form -->
     <div class="credentials-container">
          <h1>Credentials</h1>
          <p class="subtitle">Set your account details</p>
            <div class="credentials-row">
              <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="lydopers_username" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>

            <div class="input-group password-group">
              <label for="pass">Password</label>
              <div class="relative">
                <input type="password" id="pass" name="lydopers_pass" class="pl-2 pr-10 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <button type="button" class="absolute left-70 right-8 top-7 bottom-10 transform -translate-y-1/2 text-gray-500 hover:text-gray-700" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility">
                  <i data-feather="eye" id="pass-eye-icon" class="w-5 h-5"></i>
                </button>
              </div>
              <small class="error-message"></small>
            </div>
            <div class="input-group password-group">
              <label for="confirm_pass">Confirm</label>
              <div class="relative">
                <input type="password" id="confirm_pass" name="confirm_pass" class="pl-2 pr-10 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <button type="button" class="absolute left-70 right-8 top-7 bottom-10 transform -translate-y-1/2 text-gray-500 hover:text-gray-700" onclick="toggleConfirmPasswordVisibility()" aria-label="Toggle confirm password visibility">
                  <i data-feather="eye" id="confirm-pass-eye-icon" class="w-5 h-5"></i>
                </button>
              </div>
              <small class="error-message"></small>
            </div>

            <div class="input-group btn-group">
              <button type="submit" class="login-btn flex justify-center items-center bg-purple-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="btnText">Submit</span>
                <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
              </button>
            </div>
         </div>
        </div>
      </div>
    </form>

<script>
  const registrationForm = document.getElementById("registrationForm");
  const submitBtn = registrationForm.querySelector(".login-btn");

  const rules = {
    name: /^[A-Za-z\s]+$/,
    username: /^[a-zA-Z0-9._]*$/,
    contact: /^(09\d{9}|\+639\d{9})$/,
    password: {
      regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/,
      rules: [
        { regex: /.{8,}/, message: "At least 8 characters" },
        { regex: /[A-Z]/, message: "At least 1 uppercase letter" },
        { regex: /[a-z]/, message: "At least 1 lowercase letter" },
        { regex: /\d/, message: "At least 1 number" },
        { regex: /[@$!%*?&]/, message: "At least 1 special character" },
      ],
    },
  };

  function validateInput(input) {
    const id = input.id;
    const value = input.value.trim();
    
    // Find error message element
    let errorEl = input.nextElementSibling;
    if (input.closest('.password-group')) {
      const relativeDiv = input.closest('.relative');
      errorEl = relativeDiv.nextElementSibling;
    }
    
    let errorMsg = "";
    let valid = true;

    if (input.hasAttribute("required") && !value) {
      errorMsg = "This field cannot be empty";
      valid = false;
    }

    if (valid && ["fname", "mname", "lname"].includes(id)) {
      if (value && !rules.name.test(value)) {
        errorMsg = "Cannot accept numbers or symbols";
        valid = false;
      }
    }

    if (valid && id === "username") {
      if (value && !rules.username.test(value)) {
        errorMsg = "Only letters and numbers are accepted, no spaces or symbols";
        valid = false;
      }
    }

    if (valid && id === "contact") {
      if (value && !rules.contact.test(value)) {
        errorMsg = "Format: 09XXXXXXXXX or +639XXXXXXXXX";
        valid = false;
      }
    }

    if (valid && id === "bdate") {
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

    if (valid && id === "email") {
      if (!value.endsWith("@gmail.com")) {
        errorMsg = "Email must end with @gmail.com";
        valid = false;
      }
    }

    if (valid && id === "pass") {
      let unmet = rules.password.rules
        .filter((r) => !r.regex.test(value))
        .map((r) => r.message);
      if (unmet.length > 0) {
        errorMsg = "Password must have: " + unmet.join(", ");
        valid = false;
      }
    }

    if (valid && id === "confirm_pass") {
      const pass = document.getElementById("pass").value;
      if (value !== pass) {
        errorMsg = "Must match the entered password";
        valid = false;
      }
    }

    if (!valid) {
      input.classList.add("error");
      input.classList.remove("valid");
      errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1"></i>${errorMsg}`;
    } else {
      input.classList.remove("error");
      input.classList.add("valid");
      errorEl.innerHTML = "";
    }

    return valid;
  }

  let debounceTimers = {};

  function checkDuplicate(input) {
    const id = input.id;
    const value = input.value.trim();

    // Find the correct error message element
    let errorEl = input.nextElementSibling;
    if (input.closest('.password-group')) {
      const relativeDiv = input.closest('.relative');
      errorEl = relativeDiv.nextElementSibling;
    }
    
    // Clear previous timer for this specific field
    if (debounceTimers[id]) {
      clearTimeout(debounceTimers[id]);
    }
    
    // Immediately clear error if empty
    if (!value) {
      input.classList.remove("error");
      input.classList.remove("valid");
      errorEl.innerHTML = "";
      toggleButton();
      return;
    }

    // Only check duplicates for email and username
    if (id !== 'email' && id !== 'username') {
      return;
    }

    // First validate format
    const formatValid = validateInput(input);
    if (!formatValid) {
      toggleButton();
      return;
    }

    // Debounce duplicate check
    debounceTimers[id] = setTimeout(() => {
      const route = id === 'email' ? '/check-email' : '/check-username';

      fetch(route, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ value: value })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (!data.available) {
          // AUTOMATIC ERROR DISPLAY - Duplicate found
          input.classList.add("error");
          input.classList.remove("valid");
          errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1"></i>${
            id === 'email' 
              ? "This email is already registered. Please use a different email." 
              : "This username is already taken. Please choose another one."
          }`;
        } else {
          // No duplicate - mark as valid
          input.classList.remove("error");
          input.classList.add("valid");
          errorEl.innerHTML = "";
        }
        toggleButton();
      })
      .catch(error => {
        console.error('Error checking duplicate:', error);
        toggleButton();
      });
    }, 800); // Slightly longer debounce for duplicate checks
  }

  function toggleButton() {
    const requiredInputs = registrationForm.querySelectorAll("input[required], select[required]");
    let allValid = true;
    
    requiredInputs.forEach(input => {
      const hasValue = input.value.trim();
      const hasError = input.classList.contains("error");
      const hasValid = input.classList.contains("valid");
      
      if (!hasValue || hasError || !hasValid) {
        allValid = false;
      }
    });

    // Additional check for password confirmation
    const password = document.getElementById('pass');
    const confirmPassword = document.getElementById('confirm_pass');
    if (password && confirmPassword && password.value !== confirmPassword.value) {
      allValid = false;
    }

    submitBtn.disabled = !allValid;
    submitBtn.style.opacity = allValid ? 1 : 0.5;
    submitBtn.style.cursor = allValid ? 'pointer' : 'not-allowed';
  }

  // Enhanced event listeners for real-time validation
  function initializeEventListeners() {
    const inputs = registrationForm.querySelectorAll("input, select");
    
    inputs.forEach(input => {
      // Real-time validation on input
      input.addEventListener("input", function() {
        const id = this.id;
        
        // Validate format first
        validateInput(this);
        
        // Check duplicates for email and username
        if (id === 'email' || id === 'username') {
          checkDuplicate(this);
        } else {
          toggleButton();
        }
      });

      // Validate on blur as well
      input.addEventListener("blur", function() {
        const id = this.id;
        validateInput(this);
        
        if (id === 'email' || id === 'username') {
          checkDuplicate(this);
        } else {
          toggleButton();
        }
      });

      // Special handling for password fields
      if (input.id === 'pass' || input.id === 'confirm_pass') {
        input.addEventListener("input", function() {
          validateInput(this);
          // Validate the other password field too
          const otherField = this.id === 'pass' ? document.getElementById('confirm_pass') : document.getElementById('pass');
          if (otherField && otherField.value) {
            validateInput(otherField);
          }
          toggleButton();
        });
      }
    });
  }

  // Password toggle functionality
  function togglePasswordVisibility() {
    const passwordInput = document.getElementById('pass');
    const eyeIcon = document.getElementById('pass-eye-icon');

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      eyeIcon.setAttribute('data-feather', 'eye-off');
    } else {
      passwordInput.type = 'password';
      eyeIcon.setAttribute('data-feather', 'eye');
    }
    feather.replace();
  }

  function toggleConfirmPasswordVisibility() {
    const confirmPasswordInput = document.getElementById('confirm_pass');
    const eyeIcon = document.getElementById('confirm-pass-eye-icon');

    if (confirmPasswordInput.type === 'password') {
      confirmPasswordInput.type = 'text';
      eyeIcon.setAttribute('data-feather', 'eye-off');
    } else {
      confirmPasswordInput.type = 'password';
      eyeIcon.setAttribute('data-feather', 'eye');
    }
    feather.replace();
  }

  // Form submission handler
  registrationForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Final validation before submission
    let hasErrors = false;
    const requiredInputs = registrationForm.querySelectorAll("input[required], select[required]");
    
    requiredInputs.forEach(input => {
      validateInput(input);
      if (input.id === 'email' || input.id === 'username') {
        checkDuplicate(input);
      }
      
      if (input.classList.contains("error") || !input.value.trim()) {
        hasErrors = true;
      }
    });

    if (hasErrors) {
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please fix all errors before submitting.'
      });
      return;
    }

    // Show loading state
    const btnText = document.getElementById("btnText");
    const btnSpinner = document.getElementById("btnSpinner");
    
    submitBtn.disabled = true;
    btnText.textContent = "Creating...";
    btnSpinner.classList.remove("hidden");

    // Confirmation dialog
    Swal.fire({
      title: 'Are you sure you want to create account?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, create it!'
    }).then((result) => {
      if (result.isConfirmed) {
        registrationForm.submit();
      } else {
        // Reset button state if cancelled
        btnText.textContent = "Submit";
        btnSpinner.classList.add("hidden");
        toggleButton();
      }
    });
  });

  // Initialize everything when page loads
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    initializeEventListeners();
    toggleButton(); // Set initial button state
  });
</script>
<script src="{{ asset('js/spinner.js') }}"></script>

  </body>
</html>
