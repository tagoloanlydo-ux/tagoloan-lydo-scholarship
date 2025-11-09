<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>LYDO Scholarship - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
      /* Custom CSS for responsiveness */
      .banner-grad {
        background: linear-gradient(90deg, #4c1d95 0%, #7e22ce 100%);
      }
      
      @media (max-width: 768px) {
        .responsive-container {
          padding-left: 1rem;
          padding-right: 1rem;
        }
        
        .responsive-heading {
          font-size: 2.5rem;
        }
        
        .responsive-text {
          font-size: 1.125rem;
        }
        
        .responsive-form {
          width: 100%;
          max-width: 100%;
        }
      }
      
      @media (min-width: 769px) and (max-width: 1024px) {
        .responsive-container {
          padding-left: 2rem;
          padding-right: 2rem;
        }
        
        .responsive-heading {
          font-size: 3rem;
        }
        
        .responsive-text {
          font-size: 1.25rem;
        }
      }
      
      @media (min-width: 1025px) {
        .responsive-container {
          padding-left: 3rem;
          padding-right: 3rem;
        }
        
        .responsive-heading {
          font-size: 3.75rem;
        }
        
        .responsive-text {
          font-size: 1.25rem;
        }
      }
    </style>
  </head>
  <body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- HEADER -->
    <script>
      @if (session('success'))
        Swal.fire({
          title: 'Success!',
          text: "{{ session('success') }}",
          icon: 'success',
          confirmButtonText: 'OK'
        });
      @endif

      @if (session('error'))
        Swal.fire({
          title: 'Error!',
          text: "{{ session('error') }}",
          icon: 'error',
          confirmButtonText: 'Try Again'
        });
      @endif

      @if ($errors->any() && ($errors->has('error') || $errors->count() > 1 || (!$errors->has('scholar_username') && !$errors->has('scholar_pass'))))
        Swal.fire({
          title: 'Validation Error',
          html: "{!! implode('<br>', $errors->all()) !!}",
          icon: 'warning',
          confirmButtonText: 'OK'
        });
      @endif
    </script>

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
    <main class="flex flex-1 flex-col md:flex-row items-center justify-center responsive-container py-8 md:py-10 gap-8 md:gap-12">
      <!-- LEFT SIDE -->
      <div class="flex flex-col items-center text-center md:text-left md:items-start w-full md:max-w-lg md:min-w-[400px] px-4">
        <h2 class="responsive-heading font-extrabold mb-4 text-purple-700 leading-tight">
          Welcome Back, Scholars!
        </h2>
        <p class="responsive-text leading-relaxed text-gray-700 mb-4">
          Access your scholarship dashboard, track your application, and explore
          new opportunities for your future.
        </p>
        <button onclick="window.location='{{ route('home') }}'" class="flex items-center gap-2 text-purple-600 hover:text-purple-800 font-semibold mt-4">
          <i class="fa-solid fa-arrow-left"></i>Back to Portal
        </button>
      </div>

      <!-- RIGHT SIDE (LOGIN FORM) -->
      <div class="w-full responsive-form max-w-sm space-y-6 px-4">
        <form method="POST" action="{{ route('scholar.login.submit') }}" novalidate>
          @csrf
          <div>
            <label for="scholar_username" class="block text-lg font-medium " style="color: #3b0066;">Username</label>
            <div class="relative mt-2">
              <input id="scholar_username" name="scholar_username" type="text" value="{{ old('scholar_username') }}" required autofocus class="w-full bg-white rounded-lg pl-12 pr-4 py-3 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('scholar_username') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" placeholder="Enter your username" />
              <i id="username-icon" class="fa-solid fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>
            </div>
            @error('scholar_username')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message == 'Invalid username.' ? "username doesn't exist" : $message }}</p>
            @enderror
          </div>

          <div class="mt-5">
            <label for="scholar_pass" class="block text-lg font-medium" style="color: #3b0066;">Password</label>

            <div class="relative mt-2">
              <!-- Lock icon -->
              <i id="password-icon" class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>

              <!-- Password input -->
              <input
                id="scholar_pass"
                name="scholar_pass"
                type="password"
                required
                placeholder="Enter your password"
                class="w-full bg-white rounded-lg pl-12 pr-12 py-3 text-gray-700 shadow-sm text-lg border
                @error('scholar_pass') border-red-500 focus:border-red-500 focus:ring-red-200 @else border-gray-300 focus:border-purple-600 focus:ring-purple-200 @enderror
                focus:outline-none focus:ring-2"
              />

              <!-- Eye toggle button -->
              <button
                type="button"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-purple-600"
                onclick="togglePasswordVisibility()"
                aria-label="Toggle password visibility"
              >
                <i id="scholar-pass-eye-icon" class="fa-solid fa-eye"></i>
              </button>
            </div>

            <!-- Error message -->
            @error('scholar_pass')
              <p class="text-red-600 text-sm mt-1 flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $message == 'Incorrect password.' ? 'incorrect password' : $message }}
              </p>
            @enderror
          </div>

          <a href="{{ route('scholar.forgot-password') }}" class="text-sm text-purple-600 hover:underline mt-3 block text-right">
            Forgot Password?
          </a>
          <button type="submit" id="loginBtn" class="w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-700 transition shadow-md text-lg mt-4 flex justify-center items-center">
            <span id="btnText">Log In</span>
            <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
              <circle class="opacity-25" cx="12" cy="12"r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
          </button>
        </form>
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

      // Example SweetAlert usage
      function showSuccessAlert() {
        Swal.fire({
          title: 'Success!',
          text: 'Operation completed successfully',
          icon: 'success',
          confirmButtonText: 'OK'
        });
      }

      function showConfirmationAlert() {
        Swal.fire({
          title: 'Are you sure?',
          text: 'You won\'t be able to revert this!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            );
          }
        });
      }

      // You can call these functions from buttons or other events
      // Example: <button onclick="showSuccessAlert()">Show Success</button>
    </script>
    <script>
      const loginForm = document.querySelector("form");
      const loginBtn = document.getElementById("loginBtn");
      const btnText = document.getElementById("btnText");
      const btnSpinner = document.getElementById("btnSpinner");

      loginForm.addEventListener("submit", function () {
        // Disable button habang naglo-load
        loginBtn.disabled = true;
        loginBtn.classList.add("opacity-70", "cursor-not-allowed");

        // Palitan ang text at ipakita ang spinner
        btnText.textContent = "Logging in...";
        btnSpinner.classList.remove("hidden");
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