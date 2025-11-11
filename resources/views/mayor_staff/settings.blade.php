<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <link rel="icon" type="image/x-icon" href="/img/LYDO.png">
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #passwordRequirements div,
    #passwordMatchError,
    #passwordMatchSuccess {
        transition: all 0.3s ease;
    }
    
    /* Style for requirement items */
    .requirement-item {
        display: flex;
        align-items: center;
        margin-bottom: 2px;
    }
    
    /* Success state colors */
    .text-green-500 {
        color: #10b981;
    }
    
    .text-red-500 {
        color: #ef4444;
    }
        /* Loading Spinner Styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
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

        /* Responsive design */
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

<body class="bg-gray-50">
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner">
            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
        </div>
    </div>
    <div class="dashboard-grid">
        <!-- Header -->
<header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
               <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Mayor Staff</span>     
            <div class="relative">
                <!-- 🔔 Bell Icon -->
                <button id="notifBell" class="relative focus:outline-none">
                    <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                    @if($notifications->count() > 0)
                        <span id="notifCount"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $notifications->count() }}
                        </span>
                    @endif
                </button>

                <!-- 🔽 Dropdown -->
                <div id="notifDropdown"
                    class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="p-3 border-b font-semibold text-gray-700">Notifications</div>
                    <ul class="max-h-60 overflow-y-auto">
                        @forelse($notifications as $notif)
                            <li class="px-4 py-2 hover:bg-gray-50 text-base border-b">
                                {{-- New Application --}}
                                @if($notif->type === 'application')
                                    <p class="text-blue-600 font-medium">
                                        📝 {{ $notif->name }} submitted a new application
                                    </p>
                                {{-- New Remark --}}
                                @elseif($notif->type === 'remark')
                                    <p class="text-purple-600 font-medium">
                                        💬 New remark for {{ $notif->name }}:
                                        <b>{{ $notif->remarks }}</b>
                                    </p>
                                @endif

                                {{-- Time ago --}}
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                </p>
                            </li>
                        @empty
                            <li class="px-4 py-3 text-gray-500 text-sm">No new notifications</li>
                        @endforelse
                    </ul>
                </div>
            </div>
          </div>

    </header>
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#8b5cf6'
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#8b5cf6'
        });
    </script>
    @endif
        <!-- Main Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
<div class="sidebar-fixed w-72 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
                      <li>
                        <a href="/mayor_staff/dashboard" class=" flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                          <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
                          <span class="ml-4 hidden md:block text-lg">Dashboard</span>
                        </a>
                      </li>
                      <li class="relative">
                          <button onclick="toggleDropdown('scholarMenu')"
                              class="w-full flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white focus:outline-none">
                              <div class="flex items-center">
                                  <i class="bx bxs-graduation text-center mx-auto md:mx-0 text-xl"></i>
                                  <span class="ml-4 hidden md:block text-lg">Applicants</span>
                              </div>
                              <i class="bx bx-chevron-down ml-2"></i>
                          </button>

                          <!-- Dropdown Menu -->
                          <ul id="scholarMenu" class="ml-10 mt-2 space-y-2 hidden">
                              <li>
                                  <a href="/mayor_staff/application"
                                    class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                    <i class="bx bx-search-alt mr-2"></i> Review Applications
                                  </a>
                              </li>
                              <li>
                                  <a href="/mayor_staff/status"
                                    class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                    <i class="bx bx-check-circle mr-2"></i> Scholarship Approval
                                  </a>
                              </li>
                          </ul>
                      </li>
                    </ul>
                    <ul class="side-menu space-y-1">
                      <li>
                        <a href="/mayor_staff/settings" class="w-ful flex items-center p-3 rounded-lg text-white bg-violet-600 hover:bg-violet-700">
                          <i class="bx bxs-cog text-center mx-auto md:mx-0 text-xl"></i>
                          <span class="ml-4 hidden md:block text-base">Settings</span>
                        </a>
                      </li>
                    </ul>
                </nav>
               <div class="p-2 md:p-4 border-t">
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm"> @csrf <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                            <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                            <span class="hidden md:block text-red-600">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="flex-1 main-content-area p-10 md:p-2 text-[14px]">
           <section class="flex-grow">
            <div class="flex flex-col md:flex-row md:space-x-1 max-full-5xl mx-full">
              <!-- Profile Card -->
        <aside class="flex-shrink-0 rounded-2xl bg-white p-10 mb-10 md:mb-0 w-full md:w-80 text-center shadow-lg border border-gray-100">
                    <!-- Profile Picture -->

              <!-- Profile Picture Section -->
              <div class="relative inline-block mx-auto w-32 h-32 mb-4">
                <!-- Profile Picture -->
                <img 
                  id="profileImage"
                  src="{{ asset('images/LYDO.png') }}" 
                  alt="Profile Picture"
     x             class="rounded-full object-cover w-full h-full ring-4 ring-violet-100 hover:ring-violet-400 transition"
                />

                <!-- Hidden file input -->
                <input type="file" id="fileInput" accept="image/*" class="hidden">

                <!-- Edit Icon -->
                <button aria-label="Edit Profile Picture" title="Edit Profile Picture"
                  class="absolute bottom-0 right-0 bg-violet-500 p-2 rounded-full border-2 border-white hover:bg-violet-600 transition text-white shadow-md"
                  onclick="document.getElementById('fileInput').click();"
                >
                  <i class="fas fa-pen text-sm"></i>
                </button>
              </div>

          <h2 class="font-semibold text-base text-gray-800">
            {{ session('lydopers')->lydopers_fname }} 
            {{ session('lydopers')->lydopers_mname ? session('lydopers')->lydopers_mname . ' ' : '' }}
            {{ session('lydopers')->lydopers_lname }}
            {{ session('lydopers')->lydopers_suffix ? session('lydopers')->lydopers_suffix : '' }}
          </h2>
          <p class="text-gray-500 text-base mt-1 mb-6">
            {{ ucfirst(str_replace('_', ' ', session('lydopers')->lydopers_role)) }}
          </p>

          <nav class="flex flex-col gap-2 text-sm font-medium">
            <button id="btnPersonal" type="button"
              class="flex items-center gap-2 py-2 px-4 rounded-xl bg-violet-100 text-violet-600 transition">
              <i class="fas fa-user-circle"></i> Personal Information
            </button>
            <button id="btnPassword" type="button"
              class="flex items-center gap-2 py-2 px-4 rounded-xl hover:bg-violet-50 transition">
              <i class="fas fa-lock"></i> Login & Password
            </button>
          </nav>
        </aside>

 <form id="personalForm" method="POST" action="{{ route('MayorStaff.update', session('lydopers')->lydopers_id) }}" class="flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
    @csrf
    @method('PUT')
    <h1 class="text-base font-semibold text-gray-800 mb-8">Update Personal Information</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-base text-gray-600 mb-1">First Name</label>
            <input type="text" name="lydopers_fname" value="{{ session('lydopers')->lydopers_fname }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            <!-- Error message will be inserted here by JavaScript -->
        </div>
        <div>
            <label class="block text-base text-gray-600 mb-1">Last Name</label>
            <input type="text" name="lydopers_lname" value="{{ session('lydopers')->lydopers_lname }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            <!-- Error message will be inserted here by JavaScript -->
        </div>

        <div class="md:col-span-2">
            <label class="block text-base text-gray-600 mb-1">Email</label>
            <input type="email" name="lydopers_email" value="{{ session('lydopers')->lydopers_email }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            <!-- Error message will be inserted here by JavaScript -->
        </div>
        <div class="md:col-span-2">
            <label class="block text-base text-gray-600 mb-1">Address</label>
            <input type="text" name="lydopers_address" value="{{ session('lydopers')->lydopers_address }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
        </div>
        <div>
            <label class="block text-base text-gray-600 mb-1">Phone Number</label>
            <input type="tel" name="lydopers_contact_number" value="{{ session('lydopers')->lydopers_contact_number }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            <!-- Error message will be inserted here by JavaScript -->
        </div>
        <div>
            <label class="block text-base text-gray-600 mb-1">Date of Birth</label>
            <input
                type="date"
                name="lydopers_bdate"
                value="{{ session('lydopers')->lydopers_bdate }}"
                class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
            />
        </div>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-4">
        <button type="reset" class="px-6 py-3 border border-violet-500 rounded-xl font-semibold text-violet-600 hover:bg-violet-50 transition">
            Discard
        </button>
        <button type="submit" class="px-6 py-3 bg-violet-500 rounded-xl font-semibold text-white hover:bg-violet-600 transition">
            Save
        </button>
    </div>
</form>
<script>
// Name validation - no numbers or symbols
function validateName(input, fieldName) {
    const value = input.value.trim();
    const nameRegex = /^[A-Za-z\s]+$/;
    const errorElement = document.getElementById(`${fieldName}Error`);
    
    if (value && !nameRegex.test(value)) {
        errorElement.textContent = `${fieldName.replace('_', ' ')} should not contain numbers or symbols`;
        errorElement.classList.remove('hidden');
        return false;
    } else {
        errorElement.classList.add('hidden');
        return true;
    }
}

// Email validation - check for duplicates (this would need backend integration)
function validateEmail(input) {
    const value = input.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const errorElement = document.getElementById('emailError');
    
    if (value && !emailRegex.test(value)) {
        errorElement.textContent = 'Please enter a valid email address';
        errorElement.classList.remove('hidden');
        return false;
    } else {
        errorElement.classList.add('hidden');
        // For duplicate checking, you would need to make an AJAX request to backend
        return true;
    }
}

// Contact number validation
function validateContact(input) {
    const value = input.value.trim();
    const contactRegex = /^(9\d{9}|\+639\d{9})$/;
    const errorElement = document.getElementById('contactError');
    
    if (value && !contactRegex.test(value)) {
        errorElement.textContent = 'Please enter a valid contact number (09XXXXXXXXX or +639XXXXXXXXX)';
        errorElement.classList.remove('hidden');
        return false;
    } else {
        errorElement.classList.add('hidden');
        return true;
    }
}

// Add event listeners when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Name fields
    const fnameInput = document.querySelector('input[name="lydopers_fname"]');
    const mnameInput = document.querySelector('input[name="lydopers_mname"]');
    const lnameInput = document.querySelector('input[name="lydopers_lname"]');
    const emailInput = document.querySelector('input[name="lydopers_email"]');
    const contactInput = document.querySelector('input[name="lydopers_contact_number"]');
    
    // Add error message elements after each input
    addErrorMessage(fnameInput, 'fnameError');
    addErrorMessage(mnameInput, 'mnameError');
    addErrorMessage(lnameInput, 'lnameError');
    addErrorMessage(emailInput, 'emailError');
    addErrorMessage(contactInput, 'contactError');
    
    // Add event listeners
    fnameInput.addEventListener('blur', () => validateName(fnameInput, 'First Name'));
    mnameInput.addEventListener('blur', () => validateName(mnameInput, 'Middle Name'));
    lnameInput.addEventListener('blur', () => validateName(lnameInput, 'Last Name'));
    emailInput.addEventListener('blur', validateEmail);
    contactInput.addEventListener('blur', validateContact);
    
    // Enhanced form submission validation
    document.getElementById('personalForm').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate all fields
        isValid = validateName(fnameInput, 'First Name') && isValid;
        isValid = validateName(mnameInput, 'Middle Name') && isValid;
        isValid = validateName(lnameInput, 'Last Name') && isValid;
        isValid = validateEmail(emailInput) && isValid;
        isValid = validateContact(contactInput) && isValid;
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fix the errors in the form before submitting',
                confirmButtonColor: '#8b5cf6'
            });
        }
    });
});

// Helper function to add error message elements
function addErrorMessage(input, id) {
    const errorElement = document.createElement('div');
    errorElement.id = id;
    errorElement.className = 'mt-1 text-sm text-red-500 hidden';
    input.parentNode.appendChild(errorElement);
}
</script>

<form id="passwordForm" method="POST" action="{{ route('MayorStaff.updatePassword') }}"
    class="hidden flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
    @csrf
    @method('PUT')
    
    <h1 class="text-base font-semibold text-gray-800 mb-8">Change Password</h1>
    <p class="text-sm text-gray-500 mb-4">Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.</p>

    <div class="mb-6">
        <label class="block text-base text-gray-600 mb-1">Current Password</label>
        <input type="password" name="current_password" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
    </div>
    
    <div class="mb-6">
        <label class="block text-base text-gray-600 mb-1">New Password</label>
        <input type="password" name="new_password" id="new_password" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
        <!-- Password Requirements -->
        <div id="passwordRequirements" class="mt-2 text-sm space-y-1 hidden">
            <div id="lengthReq" class="flex items-center text-red-500">
                <i class="fas fa-times mr-2"></i>
                <span>At least 8 characters</span>
            </div>
            <div id="uppercaseReq" class="flex items-center text-red-500">
                <i class="fas fa-times mr-2"></i>
                <span>One uppercase letter (A-Z)</span>
            </div>
            <div id="lowercaseReq" class="flex items-center text-red-500">
                <i class="fas fa-times mr-2"></i>
                <span>One lowercase letter (a-z)</span>
            </div>
            <div id="numberReq" class="flex items-center text-red-500">
                <i class="fas fa-times mr-2"></i>
                <span>One number (0-9)</span>
            </div>
            <div id="specialReq" class="flex items-center text-red-500">
                <i class="fas fa-times mr-2"></i>
                <span>One special character (@$!%*?&)</span>
            </div>
        </div>
    </div>
    
    <div class="mb-6">
        <label class="block text-base text-gray-600 mb-1">Confirm New Password</label>
        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
        <!-- Password Match Error -->
        <div id="passwordMatchError" class="mt-2 text-sm text-red-500 hidden">
            <i class="fas fa-times mr-2"></i>
            <span>Passwords do not match</span>
        </div>
        <!-- Password Match Success -->
        <div id="passwordMatchSuccess" class="mt-2 text-sm text-green-500 hidden">
            <i class="fas fa-check mr-2"></i>
            <span>Passwords match</span>
        </div>
    </div>

    <!-- Overall Form Error -->
    <div id="formError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg hidden">
        <div class="flex items-center text-red-700">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span id="formErrorMessage">Please fix the errors above</span>
        </div>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-4">
        <button type="reset" id="cancelBtn" class="px-6 py-3 border border-violet-500 rounded-xl font-semibold text-violet-600 hover:bg-violet-50 transition">
            Cancel
        </button>
        <button type="submit" id="submitBtn" class="px-6 py-3 bg-violet-500 rounded-xl font-semibold text-white hover:bg-violet-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
            Update Password
        </button>
    </div>
</form>
  </section>
</div>


<script>
const btnPersonal = document.getElementById("btnPersonal");
const btnPassword = document.getElementById("btnPassword");

const personalForm = document.getElementById("personalForm");
const passwordForm = document.getElementById("passwordForm");

function resetButtons() {
  btnPersonal.classList.remove("bg-violet-100", "text-violet-600");
  btnPassword.classList.remove("bg-violet-100", "text-violet-600");
}

btnPersonal.addEventListener("click", () => {
  personalForm.classList.remove("hidden");
  passwordForm.classList.add("hidden");

  resetButtons();
  btnPersonal.classList.add("bg-violet-100", "text-violet-600");
});

btnPassword.addEventListener("click", () => {
  passwordForm.classList.remove("hidden");
  personalForm.classList.add("hidden");

  resetButtons();
  btnPassword.classList.add("bg-violet-100", "text-violet-600");
});

</script>


                        <script>
                    let notifCount = document.getElementById("notifCount");
                    if (notifCount) {
                        notifCount.style.display = "none"; // mawawala yung badge
                    }
                </script>
          <script>
    document.getElementById("notifBell").addEventListener("click", function () {
        let dropdown = document.getElementById("notifDropdown");
        dropdown.classList.toggle("hidden");

        // remove badge when opened
        let notifCount = document.getElementById("notifCount");
        if (notifCount) {
            notifCount.remove();
            // Mark notifications as viewed
            fetch('/mayor_staff/mark-notifications-viewed', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
        }
    });
</script>

<script>
    // Toggle dropdown and save state
    function toggleDropdown(id) {
        const menu = document.getElementById(id);
        const isHidden = menu.classList.contains("hidden");

        if (isHidden) {
            menu.classList.remove("hidden");
            localStorage.setItem(id, "open");
        } else {
            menu.classList.add("hidden");
            localStorage.setItem(id, "closed");
        }
    }

    // Restore dropdown state on page load
    window.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("ul[id]").forEach(menu => {
            const state = localStorage.getItem(menu.id);
            if (state === "open") {
                menu.classList.remove("hidden");
            }
        });
    });
</script>
<script>
document.getElementById("logoutForm").addEventListener("submit", function (e) {
    e.preventDefault();
    Swal.fire({
        title: "Are you sure you want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, logout",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});
</script>
<script>
document.getElementById("personalForm").addEventListener("submit", function (e) {
    e.preventDefault();
    Swal.fire({
        title: "Are you sure you want to update your personal information?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#8b5cf6",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, update",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});
</script>
<script>
document.getElementById("passwordForm").addEventListener("submit", function (e) {
    e.preventDefault();
    Swal.fire({
        title: "Are you sure you want to update your password?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#8b5cf6",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, update",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});
</script>
 <script>
  const profileImage = document.getElementById("profileImage");
  const fileInput = document.getElementById("fileInput");

  // --- Use unique key per user ---
  const userId = "{{ session('lydopers')->lydopers_id }}";
  const storageKey = "profileImage_" + userId;

  // --- Load saved image when page loads ---
  window.addEventListener("load", function() {
    const savedImage = localStorage.getItem(storageKey);
    if (savedImage) {
      profileImage.src = savedImage;
    } else {
      // default kung walang naka-save
      profileImage.src = "{{ asset('images/default-profile.png') }}";
    }
  });

  // --- Save new image when uploaded ---
  if (fileInput) {
    fileInput.addEventListener("change", function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          profileImage.src = e.target.result;
          localStorage.setItem(storageKey, e.target.result); // save per user
        };
        reader.readAsDataURL(file);
      }
    });
  }
</script>
<script>
    // Show loading spinner during form submissions (exclude logout form)
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                // Skip spinner for logout form
                if (form.id === 'logoutForm') return;
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                    loadingOverlay.classList.remove('fade-out');
                }
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('new_password_confirmation');
    const passwordRequirements = document.getElementById('passwordRequirements');
    const passwordMatchError = document.getElementById('passwordMatchError');
    const passwordMatchSuccess = document.getElementById('passwordMatchSuccess');
    const formError = document.getElementById('formError');
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    // Password requirement elements
    const lengthReq = document.getElementById('lengthReq');
    const uppercaseReq = document.getElementById('uppercaseReq');
    const lowercaseReq = document.getElementById('lowercaseReq');
    const numberReq = document.getElementById('numberReq');
    const specialReq = document.getElementById('specialReq');

    let isPasswordValid = false;
    let isPasswordMatch = false;

    // Real-time password validation
    newPasswordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validatePasswordMatch);

    // Cancel button resets the form
    cancelBtn.addEventListener('click', function() {
        resetValidation();
    });

    function validatePassword() {
        const password = newPasswordInput.value;
        
        // Show requirements when user starts typing
        if (password.length > 0) {
            passwordRequirements.classList.remove('hidden');
        } else {
            passwordRequirements.classList.add('hidden');
        }

        // Check each requirement
        const hasLength = password.length >= 8;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[@$!%*?&]/.test(password);

        // Update requirement indicators
        updateRequirement(lengthReq, hasLength);
        updateRequirement(uppercaseReq, hasUppercase);
        updateRequirement(lowercaseReq, hasLowercase);
        updateRequirement(numberReq, hasNumber);
        updateRequirement(specialReq, hasSpecial);

        // Check if all requirements are met
        isPasswordValid = hasLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;
        
        // Re-validate password match when password changes
        validatePasswordMatch();
        updateSubmitButton();
    }

    function validatePasswordMatch() {
        const password = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (confirmPassword.length === 0) {
            passwordMatchError.classList.add('hidden');
            passwordMatchSuccess.classList.add('hidden');
            isPasswordMatch = false;
        } else if (password === confirmPassword) {
            passwordMatchError.classList.add('hidden');
            passwordMatchSuccess.classList.remove('hidden');
            isPasswordMatch = true;
        } else {
            passwordMatchError.classList.remove('hidden');
            passwordMatchSuccess.classList.add('hidden');
            isPasswordMatch = false;
        }

        updateSubmitButton();
    }

    function updateRequirement(element, isValid) {
        if (isValid) {
            element.classList.remove('text-red-500');
            element.classList.add('text-green-500');
            element.querySelector('i').className = 'fas fa-check mr-2';
        } else {
            element.classList.remove('text-green-500');
            element.classList.add('text-red-500');
            element.querySelector('i').className = 'fas fa-times mr-2';
        }
    }

    function updateSubmitButton() {
        if (isPasswordValid && isPasswordMatch) {
            submitBtn.disabled = false;
            formError.classList.add('hidden');
        } else {
            submitBtn.disabled = true;
            
            // Show form error if there are issues
            if (newPasswordInput.value.length > 0 || confirmPasswordInput.value.length > 0) {
                formError.classList.remove('hidden');
                let errorMessage = 'Please fix the following: ';
                const errors = [];
                
                if (!isPasswordValid) errors.push('password requirements');
                if (!isPasswordMatch) errors.push('password mismatch');
                
                document.getElementById('formErrorMessage').textContent = errorMessage + errors.join(' and ');
            } else {
                formError.classList.add('hidden');
            }
        }
    }

    function resetValidation() {
        // Reset all validation states
        passwordRequirements.classList.add('hidden');
        passwordMatchError.classList.add('hidden');
        passwordMatchSuccess.classList.add('hidden');
        formError.classList.add('hidden');
        submitBtn.disabled = false;
        
        // Reset requirement indicators
        const requirements = [lengthReq, uppercaseReq, lowercaseReq, numberReq, specialReq];
        requirements.forEach(req => {
            req.classList.remove('text-green-500');
            req.classList.add('text-red-500');
            req.querySelector('i').className = 'fas fa-times mr-2';
        });
        
        isPasswordValid = false;
        isPasswordMatch = false;
    }

    // Form submission handler
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        if (!isPasswordValid || !isPasswordMatch) {
            e.preventDefault();
            // Scroll to the first error
            if (!isPasswordValid) {
                newPasswordInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                newPasswordInput.focus();
            } else if (!isPasswordMatch) {
                confirmPasswordInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                confirmPasswordInput.focus();
            }
        }
    });
});
</script>
<script src="{{ asset('js/spinner.js') }}"></script>
</body>

</html>