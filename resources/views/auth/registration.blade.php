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
    background: #7c3aed; /* solid light purple */
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
  </style>
  <body>
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
        // Find error message element - for password fields, it's after the relative container
        let errorEl = input.nextElementSibling;
        if (input.closest('.password-group')) {
          // For password fields, error message is after the relative div
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

let debounceTimer;
function checkDuplicate(input) {
  const id = input.id;
  const value = input.value.trim();

  // Find the correct error message element
  let errorEl = input.nextElementSibling;
  if (input.closest('.password-group')) {
    const relativeDiv = input.closest('.relative');
    errorEl = relativeDiv.nextElementSibling;
  }
  if (!value) return;

  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    const route = id === 'email' ? '/check-email' : '/check-username';

    fetch(route, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ value })
    })
    .then(response => response.json())
    .then(data => {
      if (!data.available) {
        // Show inline error immediately
        input.classList.add("error");
        input.classList.remove("valid");
        errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1"></i>${id === 'email'
          ? "This email is already used. Please try another one."
          : "This username is already taken. Please choose another one."}`;
      } else {
        // Clear the error if now available
        input.classList.remove("error");
        input.classList.add("valid");
        errorEl.innerHTML = "";
      }
      toggleButton();
    })
    .catch(error => {
      console.error('Error:', error);
    });
  }, 500); // debounce to avoid rapid calls
}


      function toggleButton() {
        const requiredInputs = registrationForm.querySelectorAll("input[required], select[required]");
        let allValid = true;
        requiredInputs.forEach(input => {
          if (!input.value.trim() || input.classList.contains("error")) {
            allValid = false;
          }
        });
        submitBtn.disabled = !allValid;
        submitBtn.style.opacity = allValid ? 1 : 0.5;
      }

      registrationForm
        .querySelectorAll("input[required], select[required]")
        .forEach((input) => {
          input.addEventListener("blur", () => {
            validateInput(input);
            if (input.id === 'email' || input.id === 'username') {
              checkDuplicate(input);
            }
            toggleButton();
          });
          input.addEventListener("input", () => {
            validateInput(input);
            if (input.id === 'email' || input.id === 'username') {
              checkDuplicate(input);
            }
            toggleButton();
          });
        });

      registrationForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const requiredInputs = registrationForm.querySelectorAll("input[required], select[required]");
        let hasErrors = false;
        requiredInputs.forEach(input => {
          if (input.classList.contains("error") || !input.value.trim()) {
            hasErrors = true;
          }
        });
        if (hasErrors) {
          Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fix the errors before submitting.'
          });
          return;
        }
        const btn = registrationForm.querySelector(".login-btn");
        btn.innerHTML = '<span class="loading-spinner"></span>Creating...';
        btn.classList.add('Creating...');
        btn.disabled = true;

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
            btn.innerHTML = 'Submit';
            btn.classList.remove('loading');
            btn.disabled = false;
          }
        });
      });

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

        // Re-render the icon
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

        // Re-render the icon
        feather.replace();
      }

      // Initialize Feather icons
      document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
      });
    </script>
    <script>
  registrationForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const requiredInputs = registrationForm.querySelectorAll("input[required], select[required]");
    let hasErrors = false;
    requiredInputs.forEach(input => {
      if (input.classList.contains("error") || !input.value.trim()) {
        hasErrors = true;
      }
    });

    if (hasErrors) {
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please fix the errors before submitting.'
      });
      return;
    }

    // Button states
    const btn = registrationForm.querySelector(".login-btn");
    const btnText = document.getElementById("btnText");
    const btnSpinner = document.getElementById("btnSpinner");

    btn.disabled = true;
    btnText.textContent = "Creating...";
    btnSpinner.classList.remove("hidden");

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
        // Reset button state kung nag-cancel
        btn.disabled = false;
        btnText.textContent = "Submit";
        btnSpinner.classList.add("hidden");
      }
    });
  });
</script>
  </body>
</html>
