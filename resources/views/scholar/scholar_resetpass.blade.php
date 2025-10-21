<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>LYDO Scholarship - Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/scholar.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <!-- MAIN RESET SECTION -->
    <main class="flex flex-1 flex-col md:flex-row items-center justify-center px-6 py-10 gap-12 flex-nowrap" >
      <!-- LEFT SIDE -->
      <div class="flex flex-col items-center text-center md:text-left md:items-start max-w-lg min-w-0 md:min-w-[400px]" >
        <h2 class="text-5xl font-extrabold mb-4 text-purple-700 leading-tight">
          Reset Your Password
        </h2>
        <p class="text-xl leading-relaxed text-gray-700 mb-4">
          Enter your new password below to complete the reset process.
        </p>
      </div>

      <!-- RIGHT SIDE (RESET FORM) -->
      <div class="w-full max-w-sm space-y-6">
        <div class="flex justify-center mb-6">
          <img src="{{ asset('images/reset.gif') }}" alt="Reset Password Animation" class="w-30 h-20 object-contain bg-transparent" style="background: transparent;"/>
        </div>
        <form method="POST" action="{{ route('scholar.password.update') }}" novalidate id="resetForm">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}" />
          <div>
            <label for="password" class="block text-lg font-medium text-gray-700">New Password</label>
            <div class="relative">
              <input
                id="password"
                name="password"
                type="password"
                required
                autofocus
                class="mt-2 w-full bg-white rounded-lg pl-12 pr-10 py-3 mb-5 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('password') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
                placeholder="Enter your new password"
              />
              <i class="fa-solid fa-lock absolute left-4 transform -translate-y-1/2 text-purple-500" style="margin-top:35px;"></i>
              <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center mt-2" onclick="togglePassword('password')">
                <svg id="password-eye" style="margin-bottom:20px;" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
            @error('password')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
           <div id="passwordError" class="text-red-500 text-sm mt-1 flex items-center" style="display:none;"><i class="fa-solid fa-circle-exclamation mr-1"></i><span></span></div>
          </div>

          <div class="mb-6">
            <label for="password_confirmation" class="block text-lg font-medium text-gray-700">Confirm Password</label>
            <div class="relative">
              <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                class="mt-2 w-full bg-white rounded-lg pl-12 pr-10 py-3 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('password_confirmation') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
                placeholder="Confirm your new password"
              />
              <i class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>
              <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center mt-2" onclick="togglePassword('password_confirmation')">
                <svg id="confirm-eye" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
            @error('password_confirmation')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
            <div id="confirmError" class="text-red-500 text-sm mt-2 flex items-center" style="display:none;"><i class="fa-solid fa-circle-exclamation mr-1"></i><span></span></div>
          </div>

          <button type="submit" id="resetBtn" class="w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-700 transition shadow-md text-lg flex justify-center items-center">
            <span id="resetBtnText">Reset Password</span>
            <svg id="resetBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
              <circle class="opacity-25" cx="12" cy="12"r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
          </button>
        </form>
        <div class="text-center">
          <a href="{{ route('scholar.login') }}" class="text-purple-600 hover:text-purple-800 font-medium">
            Back to Login
          </a>
        </div>
      </div>
    </main>

    <!-- FOOTER -->
    <footer class="text-center py-4 text-sm text-gray-500">
      © 2025 LYDO Scholarship. All rights reserved.
    </footer>

    <script>
      // SweetAlert for session messages
      @if(session('success'))
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: '{{ session('success') }}',
          timer: 3000,
          showConfirmButton: false
        });
      @endif

      @if($errors->any())
        @if(session('showInactiveAlert'))
          <script>
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: '{{ $errors->first() }}',
              timer: 4000,
              showConfirmButton: false
            });
          </script>
        @else
          <script>
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: '{{ $errors->first() }}',
              timer: 4000,
              showConfirmButton: false
            });
          </script>
        @endif
      @endif

      // Password validation
      const passwordInput = document.getElementById('password');
      const confirmInput = document.getElementById('password_confirmation');
      const passwordError = document.getElementById('passwordError');
      const confirmError = document.getElementById('confirmError');
      const resetBtn = document.getElementById('resetBtn');
      const resetBtnText = document.getElementById('resetBtnText');
      const resetBtnSpinner = document.getElementById('resetBtnSpinner');
      function validatePassword() {
        const val = passwordInput.value;
        let message = '';

        if (val.length === 0) {
          passwordError.style.display = 'none';
          passwordInput.classList.remove('border-red-500');
          return;
        }

        if (val.length < 8) {
          message = 'Password must be at least 8 characters.';
        } else if (!/[A-Z]/.test(val)) {
          message = 'Password must contain at least 1 uppercase letter.';
        } else if (!/[a-z]/.test(val)) {
          message = 'Password must contain at least 1 lowercase letter.';
        } else if (!/[0-9]/.test(val)) {
          message = 'Password must contain at least 1 number.';
        } else if (!/[@$!%*?&]/.test(val)) {
          message = 'Password must contain at least 1 special character (@$!%*?&).';
        }

        if (message) {
          passwordError.querySelector('span').textContent = message;
          passwordError.style.display = 'flex';
          passwordInput.classList.add('border-red-500');
        } else {
          passwordError.style.display = 'none';
          passwordInput.classList.remove('border-red-500');
        }

        validateConfirmPassword();
      }

      function validateConfirmPassword() {
        if (confirmInput.value.length > 0 && confirmInput.value !== passwordInput.value) {
          confirmError.querySelector('span').textContent = 'Passwords do not match';
          confirmError.style.display = 'flex';
          confirmInput.classList.add('border-red-500');
        } else {
          confirmError.style.display = 'none';
          confirmInput.classList.remove('border-red-500');
        }

        // Disable reset button if there are errors
        const hasError = passwordError.style.display === 'flex' || confirmError.style.display === 'flex';
        resetBtn.disabled = hasError;
      }

      passwordInput.addEventListener('input', validatePassword);
      confirmInput.addEventListener('input', validateConfirmPassword);

      resetForm.addEventListener('submit', (e) => {
        let valid = true;

        resetBtn.disabled = true;
        resetBtnText.textContent = 'Resetting...';
        resetBtnSpinner.classList.remove('hidden');

        if (passwordInput.value.trim() === '') {
          passwordError.querySelector('span').textContent = 'Password is required.';
          passwordError.style.display = 'flex';
          valid = false;
        }

        if (confirmInput.value.trim() === '') {
          confirmError.querySelector('span').textContent = 'Confirm password is required.';
          confirmError.style.display = 'flex';
          valid = false;
        } else if (confirmInput.value !== passwordInput.value) {
          confirmError.querySelector('span').textContent = 'Passwords do not match.';
          confirmError.style.display = 'flex';
          valid = false;
        }

        if (!valid) {
          e.preventDefault();
          resetBtn.disabled = false;
          resetBtnText.textContent = 'Reset Password';
          resetBtnSpinner.classList.add('hidden');
        }
      });

      function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(fieldId === 'password' ? 'password-eye' : 'confirm-eye');
        if (input.type === 'password') {
          input.type = 'text';
          eyeIcon.classList.add('text-purple-600');
        } else {
          input.type = 'password';
          eyeIcon.classList.remove('text-purple-600');
        }
      }
    </script>
  </body>
</html>
