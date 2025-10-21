<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>LYDO Scholarship - Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
 <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
  </head>
  <body class="bg-gray-50 min-h-screen flex flex-col">
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

  @if ($errors->any() && ($errors->has('error') || $errors->count() > 1 || (!$errors->has('lydopers_username') && !$errors->has('lydopers_pass'))))
    Swal.fire({
      title: 'Validation Error',
      html: "{!! implode('<br>', $errors->all()) !!}",
      icon: 'warning',
      confirmButtonText: 'OK'
    });
  @endif
</script>
    <header class="banner-grad flex items-center px-6 text-white shadow-md">
      <img src="/images/LYDO.png" alt="LYDO Logo" class="h-10 mr-4"/>
      <div>
        <h1 class="text-3xl font-extrabold">LYDO SCHOLARSHIP</h1>
        <p class="text-sm tracking-widest">
          PARA SA KABATAAN, PARA SA KINABUKASAN.
        </p>
      </div>
    </header>

    <!-- MAIN LOGIN SECTION -->
    <main class="flex flex-1 flex-col md:flex-row items-center justify-center px-6 py-10 gap-12 flex-nowrap">
          <!-- LEFT SIDE -->
    <div class="flex flex-col items-center text-center md:text-left md:items-start max-w-lg min-w-0 md:min-w-[400px]" >
      <h2 class="text-5xl font-extrabold mb-2 text-violet-700 leading-tight">
        Welcome Back!
      </h2>

      <h3 class="text-5xl font-extrabold mb-2 text-violet-700 leading-tight">
        LYDO Team
      </h3>

      <p class="text-xl leading-relaxed text-purple-700 mb-4">
        Access your dashboard, manage scholarships, and oversee operations.
      </p>
        <button onclick="window.location='{{ route('home') }}'" class="flex items-center gap-2 text-purple-600 hover:text-purple-800 font-semibold mt-4">
          <i class="fa-solid fa-arrow-left"></i>Back to Portal
        </button>
    </div>


      <!-- RIGHT SIDE (LOGIN FORM) -->
      <div class="w-full max-w-sm space-y-6">

        <form method="POST" action="{{ route('login.submit') }}" novalidate>
          @csrf
          <div>
            <label for="lydopers_username" class="block text-lg font-medium " style="color: #3b0066;">Username</label>
            <div class="relative mt-2">
              <input id="lydopers_username" name="lydopers_username" type="text" value="{{ old('lydopers_username') }}" required autofocus class="w-full bg-white rounded-lg pl-12 pr-4 py-3 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('lydopers_username') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" placeholder="Enter your username" />
              <i id="username-icon" class="fa-solid fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>
            </div>
            @error('lydopers_username')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message == 'Invalid username.' ? "username doesn't exist" : $message }}</p>
            @enderror
          </div>

          <div class="relative">
            <label for="lydopers_pass" class="block text-lg mt-5 font-medium " style="color: #3b0066;">Password</label>
            <input id="lydopers_pass" name="lydopers_pass" type="password" required class="mt-2 w-full bg-white rounded-lg pl-12 pr-12 py-3 text-gray-700 shadow-sm text-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200 @error('lydopers_pass') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror" placeholder="Enter your password"/>
              <i id="password-icon" style="margin-top:20px;" class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-500"></i>
              <button type="button" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-purple-600" style="margin-top:20px;" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility">
                <i data-feather="eye" id="lydopers-pass-eye-icon" class="w-5 h-5"></i>
              </button>
            @error('lydopers_pass')
              <p class="text-red-600 text-sm mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message == 'Incorrect password.' ? 'incorrect password' : $message }}</p>
            @enderror
          </div>

          <div class="flex items-center justify-between mt-3">
            <label class="flex items-center">
              <input type="checkbox" name="remember" class="mr-2">
              <span class="text-sm text-gray-700">Remember Me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-purple-600 hover:underline">
              Forgot Password?
            </a>
          </div>

          <button type="submit" id="loginBtn" class="w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-700 transition shadow-md text-lg mt-4 flex justify-center items-center">
            <span id="btnText">Log In</span>
            <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
          </button>
      </form>

        <div class="flex items-center my-6">
          <div class="flex-grow border-t border-gray-300"></div>
          <span class="mx-4 text-gray-500 font-semibold">OR</span>
          <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <div class="flex justify-center mt-2 w-full">
          <a href="{{ route('lydopers.registration') }}" class="w-full">
            <button
              class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition text-lg">
              Create Account
            </button>
          </a>
        </div>
      </div>
    </main>

    <!-- FOOTER -->
    <footer class="text-center py-4 text-sm text-gray-500">
      © 2025 LYDO Scholarship. All rights reserved.
    </footer>

    <script>
      function togglePasswordVisibility() {
        const passwordInput = document.getElementById('lydopers_pass');
        const eyeIcon = document.getElementById('lydopers-pass-eye-icon');

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
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const rememberCheckbox = document.querySelector('input[name="remember"]');
    const usernameInput = document.getElementById("lydopers_username");
    const passwordInput = document.getElementById("lydopers_pass");
    const usernameIcon = document.getElementById("username-icon");
    const passwordIcon = document.getElementById("password-icon");

    // ✅ Load saved credentials if "Remember Me" was checked
    if (localStorage.getItem("rememberMe") === "true") {
      usernameInput.value = localStorage.getItem("savedUsername") || "";
      // If you want to remember password too, uncomment the next line:
      // passwordInput.value = localStorage.getItem("savedPassword") || "";
      rememberCheckbox.checked = true;
    }



    // ✅ When submitting the form
    document.querySelector("form").addEventListener("submit", function () {
      if (rememberCheckbox.checked) {
        localStorage.setItem("rememberMe", "true");
        localStorage.setItem("savedUsername", usernameInput.value);
        // If you want to remember password too, uncomment:
        // localStorage.setItem("savedPassword", passwordInput.value);
      } else {
        // ✅ Clear storage if unchecked
        localStorage.removeItem("rememberMe");
        localStorage.removeItem("savedUsername");
        // localStorage.removeItem("savedPassword");
      }
    });
  });
</script>

  </body>
</html>
