
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

    // Clear error if password is empty
    if (!password) {
      errorDiv.style.display = 'none';
      return false;
    }

    if (password.length < 8) {
      span.textContent = 'Password must be at least 8 characters long.';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
      return false;
    } else if (!/[a-z]/.test(password)) {
      span.textContent = 'Password must contain at least one lowercase letter.';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
      return false;
    } else if (!/[A-Z]/.test(password)) {
      span.textContent = 'Password must contain at least one uppercase letter.';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
      return false;
    } else if (!/\d/.test(password)) {
      span.textContent = 'Password must contain at least one number.';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
      return false;
    } else if (!/[@$!%*?&]/.test(password)) {
      span.textContent = 'Password must contain at least one special character (@$!%*?&).';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
      return false;
    } else {
      // Password is valid - hide the error div completely
      errorDiv.style.display = 'none';
      return true;
    }
  }

  // Validate username format
  function validateUsernameFormat() {
    const username = document.getElementById('scholar_username').value;
    const errorDiv = document.getElementById('usernameError');
    const span = errorDiv.querySelector('span');

    // Clear error if username is empty
    if (!username) {
      errorDiv.style.display = 'none';
      return false;
    }

    if (!/^[a-zA-Z0-9]+$/.test(username)) {
      span.textContent = 'Only letters and numbers are accepted, no spaces or symbols.';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
      return false;
    } else if (username.length < 3) {
      span.textContent = 'Username must be at least 3 characters long.';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
      return false;
    } else {
      // Format is valid, but we don't set success here - wait for availability check
      return true;
    }
  }

  // Check username availability
  const checkUsername = debounce(function() {
    const username = document.getElementById('scholar_username').value;
    const errorDiv = document.getElementById('usernameError');
    const span = errorDiv.querySelector('span');

    // First validate format
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
        // Username is available - hide the error div completely
        errorDiv.style.display = 'none';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      span.textContent = 'Error checking username availability.';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
    });
  }, 500);

  // Check confirm password match
  function checkConfirmPassword() {
    const password = document.getElementById('scholar_pass').value;
    const confirm = document.getElementById('confirm_password').value;
    const errorDiv = document.getElementById('confirmError');
    const span = errorDiv.querySelector('span');

    // Clear error if confirm password is empty
    if (!confirm) {
      errorDiv.style.display = 'none';
      return false;
    }

    if (password !== confirm) {
      span.textContent = 'Passwords do not match.';
      errorDiv.style.display = 'flex';
      errorDiv.className = 'text-red-500 text-sm flex items-center';
      return false;
    } else {
      errorDiv.style.display = 'none';
      return true;
    }
  }

  // Check if form is valid - SIMPLIFIED VERSION
  function isFormValid() {
    const username = document.getElementById('scholar_username').value;
    const password = document.getElementById('scholar_pass').value;
    const confirm = document.getElementById('confirm_password').value;
    
    const usernameError = document.getElementById('usernameError');
    const passwordError = document.getElementById('passwordError');
    const confirmError = document.getElementById('confirmError');

    // Basic validation
    const hasValidUsername = username.length >= 3 && /^[a-zA-Z0-9]+$/.test(username);
    const hasValidPassword = password.length >= 8 && validatePassword();
    const passwordsMatch = password === confirm && password.length > 0;

    // Check if any red error messages are visible
    const hasVisibleErrors = 
      (usernameError.style.display === 'flex' && usernameError.classList.contains('text-red-500')) ||
      (passwordError.style.display === 'flex') ||
      (confirmError.style.display === 'flex');

    console.log('Form Validation Status:', {
      username: hasValidUsername,
      password: hasValidPassword,
      passwordsMatch: passwordsMatch,
      hasVisibleErrors: hasVisibleErrors,
      isFormValid: hasValidUsername && hasValidPassword && passwordsMatch && !hasVisibleErrors
    });

    return hasValidUsername && hasValidPassword && passwordsMatch && !hasVisibleErrors;
  }

  // Event listeners
  document.getElementById('scholar_username').addEventListener('input', checkUsername);
  document.getElementById('scholar_pass').addEventListener('input', validatePassword);
  document.getElementById('confirm_password').addEventListener('input', checkConfirmPassword);

  // Form submit - FIXED VERSION
  document.getElementById('registerForm').addEventListener('submit', function(e) {
    console.log('Form submission attempted...');
    
    // Force validation checks
    validateUsernameFormat();
    validatePassword();
    checkConfirmPassword();

    const isValid = isFormValid();
    console.log('Is form valid?', isValid);

    if (!isValid) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'Please fix the errors before submitting.',
      });
    } else {
      console.log('Form is valid, submitting...');
      document.getElementById('registerBtn').disabled = true;
      document.getElementById('btnText').textContent = 'Creating Account...';
      document.getElementById('btnSpinner').classList.remove('hidden');
      // Form will submit normally
    }
  });

  // Initialize Feather icons
  document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
  });
