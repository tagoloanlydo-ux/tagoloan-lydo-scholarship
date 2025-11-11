<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>LYDO Scholarship - Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/scholar.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
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
    <body class="bg-gray-50 min-h-screen flex flex-col">
      <!-- HEADER -->
    <header class="banner-grad flex items-center px-4 md:px-6 py-3 text-white shadow-md">
      <img src="/images/LYDO.png" alt="LYDO Logo" class="h-8 md:h-10 mr-3 md:mr-4"/>
      <div>
        <h1 class="text-xl md:text-3xl font-extrabold">LYDO SCHOLARSHIP</h1>
        <p class="text-xs md:text-sm tracking-widest">
          PARA SA KABATAAN, PARA SA KINABUKASAN.
        </p>
      </div>
    </header>
  
      <!-- MAIN LOGIN SECTION -->
      <main
        class="flex flex-1 flex-col md:flex-row items-center justify-center px-6 py-6 gap-8 flex-nowrap"
      >
      <!-- LEFT SIDE -->
      <div class="flex flex-col items-center text-center md:text-left md:items-start max-w-lg min-w-0 md:min-w-[400px]">
        <h2 class="text-5xl font-extrabold mb-4 text-purple-700 leading-tight">
          Forgot Your Password?
        </h2>
        <p class="text-xl leading-relaxed text-gray-700 mb-4">
          Enter your email address and we'll send you a link to reset your password.
        </p>
      </div>

      <!-- RIGHT SIDE (FORGOT FORM) -->
      <div class="w-full max-w-sm space-y-6">
        <div class="flex justify-center mb-6">
          <img src="{{ asset('images/password.gif') }}" alt="Forgot Password Animation" class="w-30 h-20 object-contain bg-transparent" style="background: transparent;"/>
        </div>
        <form method="POST" action="{{ route('scholar.password.email') }}" novalidate id="forgotForm">
          @csrf
          <div>
            <label for="email" class="block text-lg font-medium text-gray-700">Email Address</label>
            <div class="relative mt-2">
              <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full bg-white rounded-lg pl-12 pr-4 py-3 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('email') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" placeholder="Enter your email address"/>
              <i class="fa-solid fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>
            </div>
            @error('email')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
            @enderror
          </div>
          <button type="submit" id="sendBtn" class="w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-700 transition shadow-md text-lg mt-4 flex justify-center items-center">
            <span id="sendBtnText">Send Reset Link</span>
            <svg id="sendBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
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

    <!-- OTP Modal -->
    <div id="otpModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
      <div class="bg-white p-8 rounded-lg w-full max-w-md text-center">
        <span class="absolute top-4 right-4 text-2xl cursor-pointer" onclick="closeModal()">&times;</span>
        <h2 class="text-2xl font-bold mb-4">Enter OTP</h2>
        <p class="mb-4">Enter the 6-digit OTP sent to your email.</p>
        <div class="flex justify-center space-x-2 mb-4">
          <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-300 rounded focus:outline-none focus:border-purple-500" id="otp1">
          <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-300 rounded focus:outline-none focus:border-purple-500" id="otp2">
          <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-300 rounded focus:outline-none focus:border-purple-500" id="otp3">
          <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-300 rounded focus:outline-none focus:border-purple-500" id="otp4">
          <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-300 rounded focus:outline-none focus:border-purple-500" id="otp5">
          <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-300 rounded focus:outline-none focus:border-purple-500" id="otp6">
        </div>
        <div id="otpError" class="text-red-500 text-sm mb-4" style="display:none;"></div>
        <div id="countdown" class="mb-4">Resend OTP in 60 seconds</div>
        <div class="flex justify-center space-x-4">
          <button id="resendBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded disabled:opacity-50 flex justify-center items-center" disabled>
            <span id="resendBtnText">Send Another OTP</span>
             <svg id="resendBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
              <circle class="opacity-25" cx="12" cy="12"r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
          </button>
          <button id="verifyBtn" class="bg-purple-600 text-white px-4 py-2 rounded flex justify-center items-center">
            <span id="verifyBtnText">Verify OTP</span>
            <svg id="verifyBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
              <circle class="opacity-25" cx="12" cy="12"r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

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

      let countdownInterval;
      let userEmail = '';

      const sendBtnText = document.getElementById('sendBtnText');
      const sendBtnSpinner = document.getElementById('sendBtnSpinner');
      const resendBtnText = document.getElementById('resendBtnText');
      const resendBtnSpinner = document.getElementById('resendBtnSpinner');
      const verifyBtnText = document.getElementById('verifyBtnText');

      // Form submission
      document.getElementById('forgotForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        userEmail = email;

        const sendBtn = document.getElementById('sendBtn');
        sendBtn.disabled = true;
        sendBtnText.textContent = 'Sending...';
        sendBtnSpinner.classList.remove('hidden');

        fetch('{{ route("scholar.password.email") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
          sendBtn.disabled = false;
          sendBtnText.textContent = 'Send Reset Link';
          sendBtnSpinner.classList.add('hidden');

          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'OTP Sent!',
              text: data.message,
              timer: 2000,
              showConfirmButton: false
            });
            openModal();
            startCountdown();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.message,
              timer: 3000,
              showConfirmButton: false
            });
          }
        })
        .catch(error => {
          sendBtn.disabled = false;
          sendBtnText.textContent = 'Send Reset Link';
          sendBtnSpinner.classList.add('hidden');

          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong. Please try again.'
          });
        });
      });

      // OTP input handling
      const otpInputs = document.querySelectorAll('.otp-input');
      otpInputs.forEach((input, index) => {
        input.addEventListener('input', function() {
          if (this.value.length === 1 && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
          }
        });

        input.addEventListener('keydown', function(e) {
          if (e.key === 'Backspace' && this.value === '' && index > 0) {
            otpInputs[index - 1].focus();
          }
        });
      });

      // Verify OTP
 document.getElementById('verifyBtn').addEventListener('click', function() {
    const otp = Array.from(otpInputs).map(input => input.value).join('');
    if (otp.length !== 6) {
      document.getElementById('otpError').textContent = 'Please enter all 6 digits.';
      document.getElementById('otpError').style.display = 'block';
      return;
    }

    fetch('{{ route("scholar.password.verifyOtp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ email: userEmail, otp: otp })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.href = '{{ route("scholar.password.reset", ":token") }}'.replace(':token', data.token);
      } else {
        document.getElementById('otpError').textContent = data.message;
        document.getElementById('otpError').style.display = 'block';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Something went wrong. Please try again.'
      });
    });
  });

  // Resend OTP
  document.getElementById('resendBtn').addEventListener('click', function() {
    const resendBtn = document.getElementById('resendBtn');
    resendBtn.disabled = true;
    resendBtnText.textContent = 'Sending...';
    resendBtnSpinner.classList.remove('hidden');

    fetch('{{ route("scholar.password.resendOtp") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ email: userEmail })
    })
    .then(response => response.json())
    .then(data => {
      resendBtn.disabled = false;
      resendBtnText.textContent = 'Send Another OTP';
      resendBtnSpinner.classList.add('hidden');

      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'OTP Resent!',
          text: data.message,
          timer: 2000,
          showConfirmButton: false
        });
        startCountdown();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to resend OTP.'
        });
      }
    })
    .catch(error => {
      resendBtn.disabled = false;
      resendBtnText.textContent = 'Send Another OTP';
      resendBtnSpinner.classList.add('hidden');

      console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Something went wrong. Please try again.'
      });
    });
  });

      function openModal() {
        document.getElementById('otpModal').style.display = 'flex';
        document.getElementById('otp1').focus();
      }

      function closeModal() {
        document.getElementById('otpModal').style.display = 'none';
        clearInterval(countdownInterval);
        otpInputs.forEach(input => input.value = '');
        document.getElementById('otpError').style.display = 'none';
      }

      function startCountdown() {
        let timeLeft = 60;
        const countdownElement = document.getElementById('countdown');
        const resendBtn = document.getElementById('resendBtn');

        resendBtn.disabled = true;
        countdownElement.textContent = `Resend OTP in ${timeLeft} seconds`;

        countdownInterval = setInterval(() => {
          timeLeft--;
          countdownElement.textContent = `Resend OTP in ${timeLeft} seconds`;

          if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            countdownElement.textContent = 'You can now resend the OTP';
            resendBtn.disabled = false;
          }
        }, 1000);
      }
    </script>
    <script src="{{ asset('js/spinner.js') }}"></script>

  </body>
</html>
