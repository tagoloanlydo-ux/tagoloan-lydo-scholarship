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
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
  <style>
  /* Toggle Password Button Styles */
.toggle-password {
    transition: color 0.3s ease;
}

.toggle-password:hover {
    color: #7c3aed;
}

.toggle-password.active {
    color: #7c3aed;
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

<body class="bg-gray-50">
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
                            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
</div>
@php
    $badgeCount = ($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0) ? $notifications->where('initial_screening', 'Approved')->count() : 0;
@endphp
 <div class="dashboard-grid">
<header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                   <!-- Navbar -->
                   <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                <div class="relative">
                    <button id="notifBell" class="relative focus:outline-none">
                        <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                        @if($badgeCount > 0)
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $badgeCount }}
                            </span>
                        @endif
                    </button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b font-semibold text-gray-700">Notifications</div>
                        <ul class="max-h-60 overflow-y-auto"> @forelse($notifications as $notif) <li class="px-4 py-2 hover:bg-gray-50 text-sm border-b"> @if($notif->initial_screening == 'Approved') <p class="text-green-600 font-medium"> ✅ {{ $notif->name }} passed initial screening </p> @elseif($notif->status == 'Renewed') <p class="text-blue-600 font-medium"> 🔄 {{ $notif->name }} submitted renewal </p> @endif <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                </p>
                            </li> @empty <li class="px-4 py-3 text-gray-500 text-sm">No new notifications</li> @endforelse </ul>
                    </div>
                </div>
                <script>
                    document.getElementById("notifBell").addEventListener("click", function() {
                        document.getElementById("notifDropdown").classList.toggle("hidden");
                        let notifCount = document.getElementById("notifCount");
                        if (notifCount) {
                            notifCount.innerText = '0'; // magiging zero ang count
                            // Mark notifications as viewed
                            fetch('/lydo_staff/mark-notifications-viewed', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json'
                                }
                            });
                        }
                    });
                </script>

            </div>
        </header>
        
        <div class="flex flex-1 overflow-hidden"> 
            <div class="w-20 md:w-80 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
                        <li>
                          <a href="/lydo_staff/dashboard"  class="flex items-center  p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                              <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
                              <span class="ml-4 hidden md:block text-lg">Dashboard</span>
                          </a>
                      </li>
                      <li>
                          <a href="/lydo_staff/screening" class="flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                              <div class="flex items-center">
                                  <i class="bx bxs-file-blank text-center mx-auto md:mx-0 text-xl"></i>
                                  <span class="ml-4 hidden md:block text-lg">Screening Applicants</span>
                              </div>
                              @if($pendingScreening > 0) <span class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                  {{ $pendingScreening }}
                              </span> @endif
                          </a>
                      </li>
                      <li>
                          <a href="/lydo_staff/renewal" class=" flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                              <div class="flex items-center">
                                  <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                  <span class="ml-4 hidden md:block text-lg">Renewals</span>
                              </div>
                              @if($pendingRenewals > 0) <span class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                  {{ $pendingRenewals }}
                              </span> @endif
                          </a>
                      </li>
                         <li>
                            <a href="/lydo_staff/disbursement" class=" flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                 <div class="flex items-center">
                                    <i class="bx bx-wallet text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Disbursement</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <ul class="side-menu space-y-1">
                        <li>
                            <a href="/lydo_staff/settings" class="flex items-center p-3 rounded-lg text-gray-700 bg-violet-600 text-white">
                                <i class="bx bxs-cog text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Settings</span>
                            </a>
                        </li>
                    </ul>
                                </nav>
               <div class="p-2 md:p-4 border-t">
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                       @csrf 
                        <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                            <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                            <span class="hidden md:block text-red-600">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-4 md:p-2 text-[14px]">

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
              class="rounded-full object-cover w-full h-full ring-4 ring-violet-100 hover:ring-violet-400 transition"
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
<form id="personalForm" method="POST" action="{{ route('lydo_staff.update', session('lydopers')->lydopers_id) }}" class="flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
    @csrf
    @method('PUT')
    <h1 class="text-base font-semibold text-gray-800 mb-8">Update Personal Information</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- First Name -->
        <div>
            <label class="block text-base text-gray-600 mb-1">First Name</label>
            <input type="text" name="lydopers_fname" value="{{ session('lydopers')->lydopers_fname }}"
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validateName(this, 'fnameError')"/>
            <div id="fnameError" class="text-red-500 text-sm mt-1 hidden"></div>
            @error('lydopers_fname')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Middle Name -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Middle Name</label>
            <input type="text" name="lydopers_mname" value="{{ session('lydopers')->lydopers_mname }}"
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validateName(this, 'mnameError')"/>
            <div id="mnameError" class="text-red-500 text-sm mt-1 hidden"></div>
            @error('lydopers_mname')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Last Name -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Last Name</label>
            <input type="text" name="lydopers_lname" value="{{ session('lydopers')->lydopers_lname }}"
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validateName(this, 'lnameError')"/>
            <div id="lnameError" class="text-red-500 text-sm mt-1 hidden"></div>
            @error('lydopers_lname')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Suffix -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Suffix</label>
            <input type="text" name="lydopers_suffix" value="{{ session('lydopers')->lydopers_suffix }}"
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            @error('lydopers_suffix')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="md:col-span-2">
            <label class="block text-base text-gray-600 mb-1">Email</label>
            <input type="email" name="lydopers_email" value="{{ session('lydopers')->lydopers_email }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validateEmail(this)"/>
            <div id="emailError" class="text-red-500 text-sm mt-1 hidden"></div>
            @error('lydopers_email')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
            <label class="block text-base text-gray-600 mb-1">Address</label>
            <input type="text" name="lydopers_address" value="{{ session('lydopers')->lydopers_address }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            @error('lydopers_address')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

<div>
    <label class="block text-base text-gray-600 mb-1">Phone Number</label>
    <input type="tel" name="lydopers_contact_number" value="{{ session('lydopers')->lydopers_contact_number }}" 
           class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
           oninput="validatePhone(this)"/>
    <div id="phoneError" class="text-red-500 text-sm mt-1 hidden"></div>
    @error('lydopers_contact_number')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>

        <!-- Date of Birth -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Date of Birth</label>
            <input type="date" name="lydopers_bdate" value="{{ session('lydopers')->lydopers_bdate }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            @error('lydopers_bdate')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-4">
        <button type="reset" class="px-6 py-3 border border-violet-500 rounded-xl font-semibold text-violet-600 hover:bg-violet-50 transition" onclick="resetValidation()">
            Discard
        </button>
        <button type="submit" class="px-6 py-3 bg-violet-500 rounded-xl font-semibold text-white hover:bg-violet-600 transition update-personal-btn">
            Save
        </button>
    </div>
</form>

<script>
// Validation functions
function validateName(input, errorId) {
    const value = input.value.trim();
    const errorElement = document.getElementById(errorId);
    const nameRegex = /^[a-zA-Z\s]*$/; // Only letters and spaces
    
    if (value === '') {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
    
    if (!nameRegex.test(value)) {
        errorElement.textContent = 'Numbers and symbols are not allowed';
        errorElement.classList.remove('hidden');
        input.classList.remove('border-gray-300');
        input.classList.add('border-red-500');
        return false;
    } else {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
}

function validateEmail(input) {
    const value = input.value.trim();
    const errorElement = document.getElementById('emailError');
    
    if (value === '') {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
        errorElement.textContent = 'Please enter a valid email address';
        errorElement.classList.remove('hidden');
        input.classList.remove('border-gray-300');
        input.classList.add('border-red-500');
        return false;
    } else {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
}

function validatePhone(input) {
    const value = input.value.trim();
    const errorElement = document.getElementById('phoneError');
    
    // Allow only numbers
    const phoneRegex = /^[0-9]+$/;
    
    if (value === '') {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
    
    if (!phoneRegex.test(value)) {
        errorElement.textContent = 'Please enter numbers only';
        errorElement.classList.remove('hidden');
        input.classList.remove('border-gray-300');
        input.classList.add('border-red-500');
        return false;
    } else if (value.length < 10 || value.length > 11) {
        errorElement.textContent = 'Phone number should be 10-11 digits';
        errorElement.classList.remove('hidden');
        input.classList.remove('border-gray-300');
        input.classList.add('border-red-500');
        return false;
    } else {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
}
// Reset validation on form reset
function resetValidation() {
    // Hide all error messages
    const errorElements = document.querySelectorAll('[id$="Error"]');
    errorElements.forEach(element => {
        element.classList.add('hidden');
    });
    
    // Remove red borders
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
    });
}

// Enhanced SweetAlert for Personal Information Update with validation
document.querySelector('.update-personal-btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Validate all fields before showing confirmation
    const fnameValid = validateName(document.querySelector('[name="lydopers_fname"]'), 'fnameError');
    const lnameValid = validateName(document.querySelector('[name="lydopers_lname"]'), 'lnameError');
    const mnameValid = validateName(document.querySelector('[name="lydopers_mname"]'), 'mnameError');
    const emailValid = validateEmail(document.querySelector('[name="lydopers_email"]'));
    const phoneValid = validatePhone(document.querySelector('[name="lydopers_contact_number"]'));
    
    // Check if all validations pass
    if (!fnameValid || !lnameValid || !mnameValid || !emailValid || !phoneValid) {
        Swal.fire({
            title: 'Validation Error',
            text: 'Please fix the errors in the form before submitting.',
            icon: 'error',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    
    // If validation passes, show confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to update your personal information.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('personalForm').submit();
        }
    });
});

// Real-time validation on page load for existing values
document.addEventListener('DOMContentLoaded', function() {
    // Validate existing values
    validateName(document.querySelector('[name="lydopers_fname"]'), 'fnameError');
    validateName(document.querySelector('[name="lydopers_mname"]'), 'mnameError');
    validateName(document.querySelector('[name="lydopers_lname"]'), 'lnameError');
    validateEmail(document.querySelector('[name="lydopers_email"]'));
    validatePhone(document.querySelector('[name="lydopers_contact_number"]'));
});
</script>

<form id="passwordForm" method="POST" action="{{ route('lydo_staff.updatePassword') }}"
      class="hidden flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
    @csrf
    <h1 class="text-base font-semibold text-gray-800 mb-8">Change Password</h1>
    <p class="text-sm text-gray-500 mb-4">Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.</p>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Current Password -->
    <div class="mb-6">
        <label class="block text-base text-gray-600 mb-1">Current Password</label>
        <div class="relative">
            <input type="password" name="current_password" id="current_password" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 pr-10 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password" data-target="current_password">
                <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
            </button>
        </div>
        @error('current_password')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- New Password -->
    <div class="mb-6">
        <label class="block text-base text-gray-600 mb-1">New Password</label>
        <div class="relative">
            <input type="password" name="new_password" id="new_password" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 pr-10 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validatePassword(this)"/>
            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password" data-target="new_password">
                <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
            </button>
        </div>
        <div id="passwordError" class="text-red-500 text-sm mt-1 hidden"></div>
        @error('new_password')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Confirm New Password -->
    <div class="mb-6">
        <label class="block text-base text-gray-600 mb-1">Confirm New Password</label>
        <div class="relative">
            <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 pr-10 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validatePasswordConfirmation(this)"/>
            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center toggle-password" data-target="new_password_confirmation">
                <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
            </button>
        </div>
        <div id="confirmPasswordError" class="text-red-500 text-sm mt-1 hidden"></div>
    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-4">
        <button type="reset" class="px-6 py-3 border border-violet-500 rounded-xl font-semibold text-violet-600 hover:bg-violet-50 transition" onclick="resetPasswordValidation()">
            Cancel
        </button>
        <button type="submit" class="px-6 py-3 bg-violet-500 rounded-xl font-semibold text-white hover:bg-violet-600 transition update-password-btn">
            Update Password
        </button>
    </div>
</form>
  <script>
    document.getElementById('passwordForm').addEventListener('submit', function(event) {
      const newPassword = document.getElementById('new_password').value;
      const confirmPassword = document.getElementById('new_password_confirmation').value;
      const passwordRequirements = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

      if (!passwordRequirements.test(newPassword)) {
        alert('New password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
        event.preventDefault();
      } else if (newPassword !== confirmPassword) {
        alert('New password and confirmation do not match.');
        event.preventDefault();
      }
    });
    // Password validation functions
function validatePassword(input) {
    const value = input.value;
    const errorElement = document.getElementById('passwordError');
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    
    if (value === '') {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
    
    if (!passwordRegex.test(value)) {
        errorElement.textContent = 'Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character';
        errorElement.classList.remove('hidden');
        input.classList.remove('border-gray-300');
        input.classList.add('border-red-500');
        return false;
    } else {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
}

function validatePasswordConfirmation(input) {
    const value = input.value;
    const passwordValue = document.getElementById('new_password').value;
    const errorElement = document.getElementById('confirmPasswordError');
    
    if (value === '') {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
    
    if (value !== passwordValue) {
        errorElement.textContent = 'Passwords do not match';
        errorElement.classList.remove('hidden');
        input.classList.remove('border-gray-300');
        input.classList.add('border-red-500');
        return false;
    } else {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
}

function resetPasswordValidation() {
    // Hide all password error messages
    const errorElements = document.querySelectorAll('#passwordError, #confirmPasswordError');
    errorElements.forEach(element => {
        element.classList.add('hidden');
    });
    
    // Remove red borders from password fields
    const passwordInputs = document.querySelectorAll('#current_password, #new_password, #new_password_confirmation');
    passwordInputs.forEach(input => {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
    });
}

// Enhanced SweetAlert for Password Update with validation
document.querySelector('.update-password-btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Validate password fields before showing confirmation
    const passwordValid = validatePassword(document.getElementById('new_password'));
    const confirmValid = validatePasswordConfirmation(document.getElementById('new_password_confirmation'));
    
    // Check current password field
    const currentPassword = document.getElementById('current_password').value;
    let currentPasswordValid = true;
    if (currentPassword === '') {
        currentPasswordValid = false;
        Swal.fire({
            title: 'Validation Error',
            text: 'Please enter your current password.',
            icon: 'error',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    
    // Check if all validations pass
    if (!passwordValid || !confirmValid) {
        Swal.fire({
            title: 'Validation Error',
            text: 'Please fix the errors in the form before submitting.',
            icon: 'error',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    
    // If validation passes, show confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to update your password.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('passwordForm').submit();
        }
    });
});

// Real-time validation on page load for password form
document.addEventListener('DOMContentLoaded', function() {
    // Validate password fields if they have values
    validatePassword(document.getElementById('new_password'));
    validatePasswordConfirmation(document.getElementById('new_password_confirmation'));
});
  </script>



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
  
  // Reset password fields when switching to password form
  resetPasswordValidation();
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
function openEditRemarksModal(button) {
    let id = button.getAttribute("data-id");
    let remarks = button.getAttribute("data-remarks");

    document.getElementById("remarks_id").value = id;
    document.getElementById("remarks_input").value = remarks;

    document.getElementById("editRemarksModal").classList.remove("hidden");
}

function closeEditRemarksModal() {
    document.getElementById("editRemarksModal").classList.add("hidden");
}
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
// Toggle Password Visibility
function setupPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.classList.add('active');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.classList.remove('active');
            }
        });
    });
}

// Password validation functions
function validatePassword(input) {
    const value = input.value;
    const errorElement = document.getElementById('passwordError');
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    
    if (value === '') {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
    
    if (!passwordRegex.test(value)) {
        errorElement.textContent = 'Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character';
        errorElement.classList.remove('hidden');
        input.classList.remove('border-gray-300');
        input.classList.add('border-red-500');
        return false;
    } else {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
}

function validatePasswordConfirmation(input) {
    const value = input.value;
    const passwordValue = document.getElementById('new_password').value;
    const errorElement = document.getElementById('confirmPasswordError');
    
    if (value === '') {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
    
    if (value !== passwordValue) {
        errorElement.textContent = 'Passwords do not match';
        errorElement.classList.remove('hidden');
        input.classList.remove('border-gray-300');
        input.classList.add('border-red-500');
        return false;
    } else {
        errorElement.classList.add('hidden');
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
        return true;
    }
}

function resetPasswordValidation() {
    // Hide all password error messages
    const errorElements = document.querySelectorAll('#passwordError, #confirmPasswordError');
    errorElements.forEach(element => {
        element.classList.add('hidden');
    });
    
    // Remove red borders from password fields
    const passwordInputs = document.querySelectorAll('#current_password, #new_password, #new_password_confirmation');
    passwordInputs.forEach(input => {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
    });
    
    // Reset password fields to hidden and reset icons
    const passwordFields = ['current_password', 'new_password', 'new_password_confirmation'];
    passwordFields.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        const toggleButton = document.querySelector(`[data-target="${fieldId}"]`);
        const icon = toggleButton?.querySelector('i');
        
        if (input && toggleButton && icon) {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            toggleButton.classList.remove('active');
        }
    });
}

// Enhanced SweetAlert for Password Update with validation
document.querySelector('.update-password-btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Validate password fields before showing confirmation
    const passwordValid = validatePassword(document.getElementById('new_password'));
    const confirmValid = validatePasswordConfirmation(document.getElementById('new_password_confirmation'));
    
    // Check current password field
    const currentPassword = document.getElementById('current_password').value;
    let currentPasswordValid = true;
    if (currentPassword === '') {
        currentPasswordValid = false;
        Swal.fire({
            title: 'Validation Error',
            text: 'Please enter your current password.',
            icon: 'error',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    
    // Check if all validations pass
    if (!passwordValid || !confirmValid) {
        Swal.fire({
            title: 'Validation Error',
            text: 'Please fix the errors in the form before submitting.',
            icon: 'error',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    
    // If validation passes, show confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to update your password.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#7c3aed',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('passwordForm').submit();
        }
    });
});

// Initialize password toggles when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setupPasswordToggles();
    
    // Validate password fields if they have values
    validatePassword(document.getElementById('new_password'));
    validatePasswordConfirmation(document.getElementById('new_password_confirmation'));
});
</script>
<script src="{{ asset('js/spinner.js') }}"></script>

</body>

</html>