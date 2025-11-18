
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>
<style>
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

    <div class="dashboard-grid">
        <!-- Header -->
<header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <!-- FIXED: Dynamic name update -->
                <span class="text-white font-semibold" id="headerUserName">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Admin</span>
        </header>
        <!-- Main Content -->
      <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <div class="w-16 md:w-72 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
        <li>
          <a href="/lydo_admin/dashboard" class="idebar-item flex items-center p-3 rounded-lg text-black-600 hover:bg-violet-600 hover:text-white">
            <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Dashboard</span>
          </a>
        </li>
<!-- Staff Dropdown -->
<li class="relative">
    <button onclick="toggleDropdown('staffMenu')"
        class="w-full flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white focus:outline-none">
        <div class="flex items-center">
            <i class="bx bxs-user-detail text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Staff</span>
        </div>
<i class="bx bx-chevron-down ml-2"></i>
</button>

<!-- Dropdown Menu -->
<ul id="staffMenu" class="ml-10 mt-2 space-y-2 hidden">
    <li>
        <a href="/lydo_admin/lydo"
           class="flex items-center p-2 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-user mr-2"></i> LYDO Staff
        </a>
    </li>
    <li>
        <a href="/lydo_admin/mayor"
           class="flex items-center p-2 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-building-house mr-2"></i> Mayor Staff
        </a>
    </li>
</ul>


<script>
    function toggleDropdown(id) {
        const menu = document.getElementById(id);
        menu.classList.toggle("hidden");
    }
</script>


<li >
    <a href="/lydo_admin/applicants" 
     class=" flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
         <div class="flex items-center">
            <i class="bx bxs-user text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Applicants</span>
        </div>
    </a>
</li>
<!-- Scholar Dropdown -->
<li class="relative">
    <button onclick="toggleDropdown('scholarMenu')"
        class="w-full flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white focus:outline-none">
        <div class="flex items-center">
            <i class="bx bxs-graduation text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Scholar</span>
        </div>
        <i class="bx bx-chevron-down ml-2"></i>
    </button>

    <!-- Dropdown Menu -->
<ul id="scholarMenu" class="ml-10 mt-2 space-y-2 hidden">
    <li>
        <a href="/lydo_admin/scholar"
           class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-list-ul mr-2"></i> List of Scholars
        </a>
    </li>
    <li>
        <a href="/lydo_admin/status"
           class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-check-circle mr-2"></i> Status
        </a>
    </li>
    <li>
        <a href="/lydo_admin/disbursement"
           class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-wallet mr-2"></i> Disbursement
        </a>
    </li>
</ul>

</li>

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

<li>
    <a href="/lydo_admin/announcement"
       class=" flex items-center justify-between p-3 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
        <div class="flex items-center">
            <i class="bx bxs-megaphone text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Announcement</span>
        </div>
    </a>
</li>
      </ul>

      <ul class="side-menu space-y-1">
        <li>
          <a href="/lydo_admin/settings" class=" flex items-center p-3 rounded-lg text-black-600 bg-violet-600 e text-white">
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

<script>
    document.getElementById('logoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });
</script>
</div>
            </div>
            <div class="flex-1 overflow-auto p-4 md:p-2 text-[14px] bg-violet-50">

  <section class="flex-grow overflow-y-auto">
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

<script>
  const profileImage = document.getElementById("profileImage");
  const headerProfileImage = document.getElementById("headerProfileImage");
  const fileInput = document.getElementById("fileInput");

  // --- Use unique key per user ---
  const userId = "{{ session('lydopers')->lydopers_id }}"; 
  const storageKey = "profileImage_" + userId;

  // --- Load saved image when page loads ---
  window.addEventListener("load", function() {
    const savedImage = localStorage.getItem(storageKey);
    if (savedImage) {
      profileImage.src = savedImage;
      if (headerProfileImage) {
        headerProfileImage.src = savedImage;
      }
    } else {
      // default kung walang naka-save
      profileImage.src = "{{ asset('images/LYDO.png') }}";
      if (headerProfileImage) {
        headerProfileImage.src = "{{ asset('images/LYDO.png') }}";
      }
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
          if (headerProfileImage) {
            headerProfileImage.src = e.target.result;
          }
          localStorage.setItem(storageKey, e.target.result); // save per user
        };
        reader.readAsDataURL(file);
      }
    });
  }
</script>


      <!-- FIXED: Dynamic name update in profile card -->
      <h2 class="font-semibold text-base text-gray-800" id="profileUserName">
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
  <button id="btnDeadlines" type="button"
    class="flex items-center gap-2 py-2 px-4 rounded-xl hover:bg-violet-50 transition">
    <i class="fas fa-calendar-alt"></i> Set Deadlines
  </button>
  <button id="btnChangePassword" type="button"
    class="flex items-center gap-2 py-2 px-4 rounded-xl hover:bg-violet-50 transition">
    <i class="fas fa-lock"></i> Change Password
  </button>
</nav>


    </aside>
 <form id="personalForm" method="POST" action="{{ route('LydoAdmin.updatePersonalInfo', session('lydopers')->lydopers_id) }}" class="flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
    @csrf
    @method('PUT')
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <h1 class="text-base font-semibold text-gray-800 mb-8">Update Personal Information</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- First Name -->
        <div>
            <label class="block text-base text-gray-600 mb-1">First Name</label>
            <input type="text" name="lydopers_fname" value="{{ session('lydopers')->lydopers_fname }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validateName(this, 'fnameError')"/>
            <div id="fnameError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Middle Name -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Middle Name</label>
            <input type="text" name="lydopers_mname" value="{{ session('lydopers')->lydopers_mname }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validateName(this, 'mnameError')"/>
            <div id="mnameError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Last Name -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Last Name</label>
            <input type="text" name="lydopers_lname" value="{{ session('lydopers')->lydopers_lname }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validateName(this, 'lnameError')"/>
            <div id="lnameError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Suffix -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Suffix</label>
            <input type="text" name="lydopers_suffix" value="{{ session('lydopers')->lydopers_suffix }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
        </div>

        <!-- Email -->
        <div class="md:col-span-2">
            <label class="block text-base text-gray-600 mb-1">Email</label>
            <input type="email" id="emailInput" name="lydopers_email" value="{{ session('lydopers')->lydopers_email }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validateEmail(this)"/>
            <div id="emailError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
            <label class="block text-base text-gray-600 mb-1">Address</label>
            <input type="text" name="lydopers_address" value="{{ session('lydopers')->lydopers_address }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
        </div>

        <!-- Phone Number -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Phone Number</label>
            <input type="tel" name="lydopers_contact_number" value="{{ session('lydopers')->lydopers_contact_number }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"
                   oninput="validatePhone(this)"/>
            <div id="phoneError" class="text-red-500 text-sm mt-1 hidden"></div>
        </div>

        <!-- Date of Birth -->
        <div>
            <label class="block text-base text-gray-600 mb-1">Date of Birth</label>
            <input type="date" name="lydopers_bdate" value="{{ session('lydopers')->lydopers_bdate }}" 
                   class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
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

    <form id="deadlinesForm" method="POST" action="{{ route('LydoAdmin.updateDeadlines') }}"
      class="hidden flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
  @csrf
  @method('PUT')
<h1 class="text-base font-semibold text-gray-800 mb-8">Set Application and Renewal Deadlines</h1>
<p class="text-sm text-gray-500 mb-6">Configure the start dates and deadlines for scholar applications and renewals. Leave fields empty to disable restrictions.</p>

  <div class="mb-8">
    <h2 class="text-lg font-medium text-gray-700 mb-4">Scholar Application</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-base text-gray-600 mb-1">Application Start Date</label>
        <input type="date" name="application_start_date" value="{{ $settings->application_start_date ? $settings->application_start_date->format('Y-m-d') : '' }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
      </div>
      <div>
        <label class="block text-base text-gray-600 mb-1">Application Deadline</label>
        <input type="date" name="application_deadline" value="{{ $settings->application_deadline ? $settings->application_deadline->format('Y-m-d') : '' }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
      </div>
    </div>
  </div>

  <div class="mb-8">
    <h2 class="text-lg font-medium text-gray-700 mb-4">Scholar Renewal</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div>
        <label class="block text-base text-gray-600 mb-1">Renewal Semester</label>
        <select name="renewal_semester" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition">
          <option value="">Select Semester</option>
          <option value="1st Semester" {{ $settings->renewal_semester == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
          <option value="2nd Semester" {{ $settings->renewal_semester == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
          <option value="Summer" {{ $settings->renewal_semester == 'Summer' ? 'selected' : '' }}>Summer</option>
        </select>
      </div>
      <div>
        <label class="block text-base text-gray-600 mb-1">Renewal Start Date</label>
        <input type="date" name="renewal_start_date" value="{{ $settings->renewal_start_date ? $settings->renewal_start_date->format('Y-m-d') : '' }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
      </div>
      <div>
        <label class="block text-base text-gray-600 mb-1">Renewal Deadline</label>
        <input type="date" name="renewal_deadline" value="{{ $settings->renewal_deadline ? $settings->renewal_deadline->format('Y-m-d') : '' }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
      </div>
    </div>
  </div>

  <!-- Buttons -->
  <div class="flex justify-end gap-4">
    <button type="reset" class="px-6 py-3 border border-violet-500 rounded-xl font-semibold text-violet-600 hover:bg-violet-50 transition">
      Reset
    </button>
    <button type="submit" class="px-6 py-3 bg-violet-500 rounded-xl font-semibold text-white hover:bg-violet-600 transition">
      Update Deadlines
    </button>
  </div>
</form>

 <form id="changePasswordForm" method="POST" action="{{ route('LydoAdmin.updatePassword') }}"
      class="hidden flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
    @csrf
    @method('PUT')
    <h1 class="text-base font-semibold text-gray-800 mb-8">Change Password</h1>
    <p class="text-sm text-gray-500 mb-6">Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.</p>

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
  </section>

</div>


<script>
const btnPersonal = document.getElementById("btnPersonal");
const btnDeadlines = document.getElementById("btnDeadlines");
const btnChangePassword = document.getElementById("btnChangePassword");

const personalForm = document.getElementById("personalForm");
const deadlinesForm = document.getElementById("deadlinesForm");
const changePasswordForm = document.getElementById("changePasswordForm");

function resetButtons() {
  btnPersonal.classList.remove("bg-violet-100", "text-violet-600");
  btnDeadlines.classList.remove("bg-violet-100", "text-violet-600");
  btnChangePassword.classList.remove("bg-violet-100", "text-violet-600");
}

btnPersonal.addEventListener("click", () => {
  personalForm.classList.remove("hidden");
  deadlinesForm.classList.add("hidden");
  changePasswordForm.classList.add("hidden");

  resetButtons();
  btnPersonal.classList.add("bg-violet-100", "text-violet-600");
});

btnDeadlines.addEventListener("click", () => {
  deadlinesForm.classList.remove("hidden");
  personalForm.classList.add("hidden");
  changePasswordForm.classList.add("hidden");

  resetButtons();
  btnDeadlines.classList.add("bg-violet-100", "text-violet-600");
});

btnChangePassword.addEventListener("click", () => {
  changePasswordForm.classList.remove("hidden");
  personalForm.classList.add("hidden");
  deadlinesForm.classList.add("hidden");

  resetButtons();
  btnChangePassword.classList.add("bg-violet-100", "text-violet-600");
});
</script>


                        <script>
                    let notifCount = document.getElementById("notifCount");
                    if (notifCount) {
                        notifCount.style.display = "none"; // mawawala yung badge
                    }
                </script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Email duplicate check function
    function checkEmailDuplicate(email) {
        return fetch('/lydo_admin/check-email-duplicate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json());
    }

    // Handle email input blur event
    document.getElementById('emailInput').addEventListener('blur', function() {
        const email = this.value.trim();
        const errorDiv = document.getElementById('emailError');

        if (email) {
            checkEmailDuplicate(email).then(data => {
                if (data.duplicate) {
                    errorDiv.textContent = 'This email is already in use.';
                    errorDiv.classList.remove('hidden');
                } else {
                    errorDiv.classList.add('hidden');
                }
            }).catch(error => {
                console.error('Error checking email:', error);
            });
        } else {
            errorDiv.classList.add('hidden');
        }
    });

    // Function to update UI with new data
    function updateUserInterface(updatedData) {
        // Update header name
        const headerUserName = document.getElementById('headerUserName');
        if (headerUserName && updatedData.lydopers_fname && updatedData.lydopers_lname) {
            headerUserName.textContent = `${updatedData.lydopers_fname} ${updatedData.lydopers_lname} | Lydo Admin`;
        }

        // Update profile card name
        const profileUserName = document.getElementById('profileUserName');
        if (profileUserName && updatedData.lydopers_fname && updatedData.lydopers_lname) {
            let fullName = updatedData.lydopers_fname;
            if (updatedData.lydopers_mname) {
                fullName += ' ' + updatedData.lydopers_mname + ' ';
            }
            fullName += updatedData.lydopers_lname;
            if (updatedData.lydopers_suffix) {
                fullName += ' ' + updatedData.lydopers_suffix;
            }
            profileUserName.textContent = fullName;
        }

        // Update form fields to reflect the updated data
        if (updatedData.lydopers_fname) {
            document.querySelector('input[name="lydopers_fname"]').value = updatedData.lydopers_fname;
        }
        if (updatedData.lydopers_mname !== undefined) {
            document.querySelector('input[name="lydopers_mname"]').value = updatedData.lydopers_mname;
        }
        if (updatedData.lydopers_lname) {
            document.querySelector('input[name="lydopers_lname"]').value = updatedData.lydopers_lname;
        }
        if (updatedData.lydopers_suffix !== undefined) {
            document.querySelector('input[name="lydopers_suffix"]').value = updatedData.lydopers_suffix;
        }
        if (updatedData.lydopers_email) {
            document.querySelector('input[name="lydopers_email"]').value = updatedData.lydopers_email;
        }
        if (updatedData.lydopers_address) {
            document.querySelector('input[name="lydopers_address"]').value = updatedData.lydopers_address;
        }
        if (updatedData.lydopers_contact_number) {
            document.querySelector('input[name="lydopers_contact_number"]').value = updatedData.lydopers_contact_number;
        }
        if (updatedData.lydopers_bdate) {
            document.querySelector('input[name="lydopers_bdate"]').value = updatedData.lydopers_bdate;
        }
    }

    // Handle personal information form submission with AJAX and SweetAlert
    document.getElementById('personalForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const emailError = document.getElementById('emailError');
        if (!emailError.classList.contains('hidden')) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fix the email error before submitting.',
                timer: 3000
            });
            return;
        }

        const form = this;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;

        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI with new data
                if (data.updated_data) {
                    updateUserInterface(data.updated_data);
                }

                // Success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Force page reload after successful update to ensure session data is fresh
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                // Error notification
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false
                });

                // Show validation errors if any
                if (data.errors) {
                    let errorMessages = '';
                    Object.values(data.errors).forEach(error => {
                        errorMessages += error + '<br>';
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessages,
                        timer: 4000
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
                timer: 3000
            });
        })
        .finally(() => {
            // Restore button state
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        });
    });

    // Handle deadlines form submission with SweetAlert
    document.getElementById('deadlinesForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;

        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Updating...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                // Error notification
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message,
                    timer: 3000
                });

                // Show validation errors if any
                if (data.errors) {
                    let errorMessages = '';
                    Object.values(data.errors).forEach(error => {
                        errorMessages += error + '<br>';
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessages,
                        timer: 4000
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
                timer: 3000
            });
        })
        .finally(() => {
            // Restore button state
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        });
    });

    // Handle change password form submission with SweetAlert
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;

        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Changing...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Reset form on success
                form.reset();
            } else {
                // Error notification
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message,
                    timer: 3000
                });

                // Show validation errors if any
                if (data.errors) {
                    let errorMessages = '';
                    Object.values(data.errors).forEach(error => {
                        errorMessages += error + '<br>';
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessages,
                        timer: 4000
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
                timer: 3000
            });
        })
        .finally(() => {
            // Restore button state
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        });
    });

    // Show existing flash messages as SweetAlert notifications
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            timer: 3000
        });
    @endif
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

// Reset validation functions
function resetValidation() {
    const errorElements = document.querySelectorAll('[id$="Error"]');
    errorElements.forEach(element => {
        element.classList.add('hidden');
    });
    
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
    });
}

function resetPasswordValidation() {
    const errorElements = document.querySelectorAll('#passwordError, #confirmPasswordError');
    errorElements.forEach(element => {
        element.classList.add('hidden');
    });
    
    const passwordInputs = document.querySelectorAll('#current_password, #new_password, #new_password_confirmation');
    passwordInputs.forEach(input => {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-300');
    });
}

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

// Enhanced SweetAlert for Personal Information Update
document.querySelector('.update-personal-btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Validate all fields before showing confirmation
    const fnameValid = validateName(document.querySelector('[name="lydopers_fname"]'), 'fnameError');
    const lnameValid = validateName(document.querySelector('[name="lydopers_lname"]'), 'lnameError');
    const mnameValid = validateName(document.querySelector('[name="lydopers_mname"]'), 'mnameError');
    const emailValid = validateEmail(document.querySelector('[name="lydopers_email"]'));
    const phoneValid = validatePhone(document.querySelector('[name="lydopers_contact_number"]'));
    
    if (!fnameValid || !lnameValid || !mnameValid || !emailValid || !phoneValid) {
        Swal.fire({
            title: 'Validation Error',
            text: 'Please fix the errors in the form before submitting.',
            icon: 'error',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    
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

// Enhanced SweetAlert for Password Update
document.querySelector('.update-password-btn').addEventListener('click', function(e) {
    e.preventDefault();
    
    const passwordValid = validatePassword(document.getElementById('new_password'));
    const confirmValid = validatePasswordConfirmation(document.getElementById('new_password_confirmation'));
    
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
    
    if (!passwordValid || !confirmValid) {
        Swal.fire({
            title: 'Validation Error',
            text: 'Please fix the errors in the form before submitting.',
            icon: 'error',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    
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
            document.getElementById('changePasswordForm').submit();
        }
    });
});

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setupPasswordToggles();
    
    // Validate existing values
    validateName(document.querySelector('[name="lydopers_fname"]'), 'fnameError');
    validateName(document.querySelector('[name="lydopers_mname"]'), 'mnameError');
    validateName(document.querySelector('[name="lydopers_lname"]'), 'lnameError');
    validateEmail(document.querySelector('[name="lydopers_email"]'));
    validatePhone(document.querySelector('[name="lydopers_contact_number"]'));
    validatePassword(document.getElementById('new_password'));
    validatePasswordConfirmation(document.getElementById('new_password_confirmation'));
});
    </script>
<script src="{{ asset('js/spinner.js') }}"></script>

</body>

</html>
