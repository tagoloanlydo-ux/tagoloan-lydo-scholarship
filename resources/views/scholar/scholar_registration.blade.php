<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LYDO Scholarship - Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="{{ asset('css/scholar.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
  </head>
  <body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- HEADER -->
    <header class="banner-grad flex items-center px-6 text-white shadow-md">
       <img src="/images/LYDO.png" alt="LYDO Logo" class="h-10 mr-4"/>
      <div>
        <h1 class="text-3xl font-extrabold">LYDO SCHOLARSHIP</h1>
        <p class="text-sm tracking-widest">
          PARA SA KABATAAN, PARA SA KINABUKASAN.
        </p>
      </div>
    </header>

    <!-- MAIN REGISTRATION SECTION -->
    <main class="flex flex-1 flex-col md:flex-row items-center justify-center px-6 py-10 gap-12 flex-nowrap">
      <!-- LEFT SIDE -->
      <div class="flex flex-col items-center text-center md:text-left md:items-start max-w-lg min-w-0 md:min-w-[400px]">
        <h2 class="text-5xl font-extrabold mb-4 text-purple-700 leading-tight">
          Join Our Scholar Community!
        </h2>
        <p class="text-xl leading-relaxed text-gray-700 mb-4">
          Create your account to access scholarship opportunities, track your applications, and connect with fellow scholars.
        </p>
      </div>

      <!-- RIGHT SIDE (REGISTRATION FORM) -->
      <div class="w-full max-w-sm space-y-6">
     
        <form method="POST" action="{{ route('scholar.register') }}" id="registerForm" novalidate>
          @csrf
          <input type="hidden" name="scholar_id" value="{{ $scholar->scholar_id }}">

          <div>
            <label for="scholar_username" class="block text-lg font-medium " style="color: #3b0066;">Username</label>
            <div class="relative mt-2">
              <input id="scholar_username" name="scholar_username" type="text" value="{{ old('scholar_username') }}" required autofocus class="w-full bg-white rounded-lg pl-12 pr-4 py-3 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('scholar_username') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" placeholder="Enter your username" />
              <i id="username-icon" class="fa-solid fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>
            </div>
            <div id="usernameError" class="text-red-500 text-sm flex items-center" style="display:none;"><i class="fa-solid fa-circle-exclamation mr-1"></i><span></span></div>
            @error('scholar_username')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message == 'Invalid username.' ? "username doesn't exist" : $message }}</p>
            @enderror
          </div>

          <div class="relative">
            <label for="scholar_pass" class="block text-lg font-medium " style="color: #3b0066;">Password</label>
            <input id="scholar_pass" name="scholar_pass" type="password" required class="mt-2 w-full bg-white rounded-lg pl-12 pr-12 py-3 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('scholar_pass') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" placeholder="Enter your password"/>
            <i id="password-icon" style="margin-top:20px;" class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>
            <button type="button" style="margin-top:20px;" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-purple-600" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility">
              <i data-feather="eye" id="scholar-pass-eye-icon" class="w-5 h-5"></i>
            </button>
            @error('scholar_pass')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
          </div>
          <div id="passwordError" class="text-red-500 text-sm flex items-center" style="display:none;"><i class="fa-solid fa-circle-exclamation mr-1"></i><span></span></div>

          <div class="relative">
            <label for="confirm_password" class="block text-lg font-medium " style="color: #3b0066;">Confirm Password</label>
            <input id="confirm_password" name="confirm_password" type="password" required class="mt-2 w-full bg-white rounded-lg pl-12 pr-12 py-3 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('confirm_password') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" placeholder="Confirm your password"/>
            <i id="confirm-password-icon" style="margin-top:20px;" class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>
            <button type="button" style="margin-top:20px;" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-purple-600" onclick="toggleConfirmPasswordVisibility()" aria-label="Toggle password visibility">
              <i data-feather="eye" id="confirm-pass-eye-icon" class="w-5 h-5"></i>
            </button>
            @error('confirm_password')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
          </div>
          <div id="confirmError" class="text-red-500 text-sm flex items-center" style="display:none;"><i class="fa-solid fa-circle-exclamation mr-1"></i><span></span></div>

          <button type="submit" id="registerBtn" class="w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-700 transition shadow-md text-lg mt-4 flex justify-center items-center">
            <span id="btnText">Create Account</span>
            <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
              <circle class="opacity-25" cx="12" cy="12"r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
          </button>
        </form>
        <div class="flex justify-center">
        <button class="text-purple-600 hover:underline mb-4" type="button" onclick="window.location.href='{{ route('scholar.login') }}'">← Back to Login </button>
      </div>
    </div>
 </main>
    <!-- FOOTER -->
    <footer class="text-center py-4 text-sm text-gray-500">
      © 2025 LYDO Scholarship. All rights reserved.
    </footer>

    <script>
      function togglePasswordVisibility() {
        const passwordInput = document.getElementById('scholar_pass');
        const eyeIcon = document.getElementById('scholar-pass-eye-icon');

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
        const passwordInput = document.getElementById('confirm_password');
        const eyeIcon = document.getElementById('confirm-pass-eye-icon');

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

      // Debounce function
      function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
          const later = () => {
            clearTimeout(timeout);
            func(...args);
          };
          clearTimeout(timeout);
          timeout = setTimeout(later, wait);
        };
      }

      // Validate password and show first unmet requirement
      function validatePassword() {
        const password = document.getElementById('scholar_pass').value;
        const errorDiv = document.getElementById('passwordError');
        const span = errorDiv.querySelector('span');

        if (password.length < 8) {
          span.textContent = 'Password must be at least 8 characters long.';
          errorDiv.style.display = 'flex';
          return false;
        } else if (!/[a-z]/.test(password)) {
          span.textContent = 'Password must contain at least one lowercase letter.';
          errorDiv.style.display = 'flex';
          return false;
        } else if (!/[A-Z]/.test(password)) {
          span.textContent = 'Password must contain at least one uppercase letter.';
          errorDiv.style.display = 'flex';
          return false;
        } else if (!/\d/.test(password)) {
          span.textContent = 'Password must contain at least one number.';
          errorDiv.style.display = 'flex';
          return false;
        } else if (!/[@$!%*?&]/.test(password)) {
          span.textContent = 'Password must contain at least one special character (@$!%*?&).';
          errorDiv.style.display = 'flex';
          return false;
        } else {
          errorDiv.style.display = 'none';
          return true;
        }
      }

      // Validate username format
      function validateUsernameFormat() {
        const username = document.getElementById('scholar_username').value;
        const errorDiv = document.getElementById('usernameError');
        const span = errorDiv.querySelector('span');

        if (username && !/^[a-zA-Z0-9]+$/.test(username)) {
          span.textContent = 'Only letters and numbers are accepted, no spaces or symbols.';
          errorDiv.style.display = 'flex';
          errorDiv.className = 'text-red-500 text-sm flex items-center';
          return false;
        } else {
          errorDiv.style.display = 'none';
          return true;
        }
      }

      // Check username availability
      const checkUsername = debounce(function() {
        const username = document.getElementById('scholar_username').value;
        const errorDiv = document.getElementById('usernameError');
        const span = errorDiv.querySelector('span');

        if (!validateUsernameFormat()) {
          return;
        }

        if (username.length < 3) {
          errorDiv.style.display = 'none';
          return;
        }

        fetch('/check-scholar-username', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ username: username })
        })
        .then(response => response.json())
        .then(data => {
          if (data.exists) {
            span.textContent = 'Username is already taken.';
            errorDiv.style.display = 'flex';
            errorDiv.className = 'text-red-500 text-sm flex items-center';
          } else {
            span.textContent = 'Username is available.';
            errorDiv.style.display = 'flex';
            errorDiv.className = 'text-green-500 text-sm flex items-center';
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
      }, 500);

      // Check confirm password match
      function checkConfirmPassword() {
        const password = document.getElementById('scholar_pass').value;
        const confirm = document.getElementById('confirm_password').value;
        const errorDiv = document.getElementById('confirmError');
        const span = errorDiv.querySelector('span');

        if (confirm && password !== confirm) {
          span.textContent = 'Passwords do not match.';
          errorDiv.style.display = 'flex';
          return false;
        } else {
          errorDiv.style.display = 'none';
          return true;
        }
      }

      // Check if form is valid
      function isFormValid() {
        const username = document.getElementById('scholar_username').value;
        const password = document.getElementById('scholar_pass').value;
        const confirm = document.getElementById('confirm_password').value;
        const usernameError = document.getElementById('usernameError');
        const passwordError = document.getElementById('passwordError');
        const confirmError = document.getElementById('confirmError');

        return username.length >= 3 &&
               password.length >= 8 &&
               confirm === password &&
               usernameError.style.display === 'none' &&
               passwordError.style.display === 'none' &&
               confirmError.style.display === 'none';
      }

      // Event listeners
      document.getElementById('scholar_username').addEventListener('input', checkUsername);
      document.getElementById('scholar_pass').addEventListener('input', validatePassword);
      document.getElementById('confirm_password').addEventListener('input', checkConfirmPassword);

      // Form submit
      document.getElementById('registerForm').addEventListener('submit', function(e) {
        if (!isFormValid()) {
          e.preventDefault();
          Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fix the errors before submitting.',
          });
        } else {
          document.getElementById('registerBtn').disabled = true;
          document.getElementById('btnText').textContent = 'Creating Account...';
          document.getElementById('btnSpinner').classList.remove('hidden');
        }
      });
    </script>
<script>
  // Initialize Feather icons
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
  });
</script>
  </body>
</html>

