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

<body class="bg-gray-50">
    <div class="dashboard-grid">
        <!-- Header -->
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Admin</span>
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
         class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-white-200 z-50">
        <div class="p-3 border-b font-semibold text-white-700">Notifications</div>
<ul class="max-h-60 overflow-y-auto">
    @forelse($notifications as $notif)
        <li class="px-4 py-2 hover:bg-white-50 text-base border-b">
            {{-- Application --}}
            @if($notif->type === 'application')
                <p class="font-medium
                    {{ $notif->status === 'Approved' ? 'text-green-600' : 'text-red-600' }}">
                    📌 Application of {{ $notif->name }} was {{ $notif->status }}
                </p>
            @elseif($notif->type === 'renewal')
                <p class="font-medium
                    {{ $notif->status === 'Approved' ? 'text-green-600' : 'text-red-600' }}">
                    🔄 Renewal of {{ $notif->name }} was {{ $notif->status }}
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

<!-- ⚡ JS -->
<script>
    document.getElementById("notifBell").addEventListener("click", function () {
        let dropdown = document.getElementById("notifDropdown");
        dropdown.classList.toggle("hidden");

        // remove badge when opened
        let notifCount = document.getElementById("notifCount");
        if (notifCount) {
            notifCount.remove();
        }
    });
</script>


            </div>

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
    <a href="/lydo_admin/applicants" 
     class=" flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
         <div class="flex items-center">
            <i class="bx bxs-user text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Applicants</span>
        </div>
    </a>
</li>
<li>
    <a href="/lydo_admin/announcement"
       class=" flex items-center justify-between p-3 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
        <div class="flex items-center">
            <i class="bx bxs-megaphone text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Announcement</span>
        </div>
    </a>
</li>

        <li>
          <a href="/lydo_admin/report" class=" flex items-center p-3 rounded-lg text-black-600 hover:bg-violet-600 hover:text-white">
            <i class="bx bxs-report text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Reports</span>
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
            <div class="flex-1 overflow-hidden p-4 md:p-2 text-[14px]">

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
    class="rounded-full object-cover w-full h-full ring-4 ring-orange-100 hover:ring-orange-400 transition"
  />

  <!-- Hidden file input -->
  <input type="file" id="fileInput" accept="image/*" class="hidden">

  <!-- Edit Icon -->
  <button aria-label="Edit Profile Picture" title="Edit Profile Picture"
    class="absolute bottom-0 right-0 bg-orange-500 p-2 rounded-full border-2 border-white hover:bg-orange-600 transition text-white shadow-md"
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
      headerProfileImage.src = savedImage;
    } else {
      // default kung walang naka-save
      profileImage.src = "{{ asset('images/default-profile.png') }}";
      headerProfileImage.src = "{{ asset('images/default-profile.png') }}";
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
          headerProfileImage.src = e.target.result;
          localStorage.setItem(storageKey, e.target.result); // save per user
        };
        reader.readAsDataURL(file);
      }
    });
  }
</script>


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
    class="flex items-center gap-2 py-2 px-4 rounded-xl bg-orange-100 text-orange-600 transition">
    <i class="fas fa-user-circle"></i> Personal Information
  </button>
  <button id="btnDeadlines" type="button"
    class="flex items-center gap-2 py-2 px-4 rounded-xl hover:bg-orange-50 transition">
    <i class="fas fa-calendar-alt"></i> Set Deadlines
  </button>
  <button id="btnChangePassword" type="button"
    class="flex items-center gap-2 py-2 px-4 rounded-xl hover:bg-orange-50 transition">
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
        <div>
          <label class="block text-base text-gray-600 mb-1">First Name</label>
          <input type="text" name="lydopers_fname" value="{{ session('lydopers')->lydopers_fname }}" class="w-full bg-gray-50 border rounded-xl  px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
        </div>
        <div>
          <label class="block text-base text-gray-600 mb-1">Last Name</label>
          <input type="text" name="lydopers_lname" value="{{ session('lydopers')->lydopers_lname }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
        </div>
        <div class="md:col-span-2">
          <label class="block text-base text-gray-600 mb-1">Email</label>
          <input type="email" name="lydopers_email" value="{{ session('lydopers')->lydopers_email }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
        </div>
        <div class="md:col-span-2">
          <label class="block text-base text-gray-600 mb-1">Address</label>
          <input type="text" name="lydopers_address" value="{{ session('lydopers')->lydopers_address }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
        </div>
        <div>
          <label class="block text-base text-gray-600 mb-1">Phone Number</label>
          <input type="text" name="lydopers_contact_number" value="{{ session('lydopers')->lydopers_contact_number }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
        </div>
        <div>
          <label class="block text-base text-gray-600 mb-1">Date of Birth</label>
        </div>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end gap-4">
        <button type="reset" class="px-6 py-3 border border-orange-500 rounded-xl font-semibold text-orange-600 hover:bg-orange-50 transition">
          Discard
        </button>
        <button type="submit" class="px-6 py-3 bg-orange-500 rounded-xl font-semibold text-white hover:bg-orange-600 transition">
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
        <input type="date" name="application_start_date" value="{{ $settings->application_start_date ? $settings->application_start_date->format('Y-m-d') : '' }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
      </div>
      <div>
        <label class="block text-base text-gray-600 mb-1">Application Deadline</label>
        <input type="date" name="application_deadline" value="{{ $settings->application_deadline ? $settings->application_deadline->format('Y-m-d') : '' }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
      </div>
    </div>
  </div>

  <div class="mb-8">
    <h2 class="text-lg font-medium text-gray-700 mb-4">Scholar Renewal</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div>
        <label class="block text-base text-gray-600 mb-1">Renewal Semester</label>
        <select name="renewal_semester" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition">
          <option value="">Select Semester</option>
          <option value="1st Semester" {{ $settings->renewal_semester == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
          <option value="2nd Semester" {{ $settings->renewal_semester == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
          <option value="Summer" {{ $settings->renewal_semester == 'Summer' ? 'selected' : '' }}>Summer</option>
        </select>
      </div>
      <div>
        <label class="block text-base text-gray-600 mb-1">Renewal Start Date</label>
        <input type="date" name="renewal_start_date" value="{{ $settings->renewal_start_date ? $settings->renewal_start_date->format('Y-m-d') : '' }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
      </div>
      <div>
        <label class="block text-base text-gray-600 mb-1">Renewal Deadline</label>
        <input type="date" name="renewal_deadline" value="{{ $settings->renewal_deadline ? $settings->renewal_deadline->format('Y-m-d') : '' }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition"/>
      </div>
    </div>
  </div>

  <!-- Buttons -->
  <div class="flex justify-end gap-4">
    <button type="reset" class="px-6 py-3 border border-orange-500 rounded-xl font-semibold text-orange-600 hover:bg-orange-50 transition">
      Reset
    </button>
    <button type="submit" class="px-6 py-3 bg-orange-500 rounded-xl font-semibold text-white hover:bg-orange-600 transition">
      Update Deadlines
    </button>
  </div>
</form>

    <form id="changePasswordForm" method="POST" action="{{ route('LydoAdmin.updatePassword') }}"
      class="hidden flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
  @csrf
  @method('PUT')
<h1 class="text-base font-semibold text-gray-800 mb-8">Change Password</h1>
<p class="text-sm text-gray-500 mb-6">Update your account password. Make sure to choose a strong password.</p>

  <div class="grid grid-cols-1 gap-6 mb-6">
    <div>
      <label class="block text-base text-gray-600 mb-1">Current Password</label>
      <input type="password" name="current_password" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition" required/>
    </div>
    <div>
      <label class="block text-base text-gray-600 mb-1">New Password</label>
      <input type="password" name="new_password" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition" required/>
    </div>
    <div>
      <label class="block text-base text-gray-600 mb-1">Confirm New Password</label>
      <input type="password" name="new_password_confirmation" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-orange-400 transition" required/>
    </div>
  </div>

  <!-- Buttons -->
  <div class="flex justify-end gap-4">
    <button type="reset" class="px-6 py-3 border border-orange-500 rounded-xl font-semibold text-orange-600 hover:bg-orange-50 transition">
      Discard
    </button>
    <button type="submit" class="px-6 py-3 bg-orange-500 rounded-xl font-semibold text-white hover:bg-orange-600 transition">
      Change Password
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
  btnPersonal.classList.remove("bg-orange-100", "text-orange-600");
  btnDeadlines.classList.remove("bg-orange-100", "text-orange-600");
  btnChangePassword.classList.remove("bg-orange-100", "text-orange-600");
}

btnPersonal.addEventListener("click", () => {
  personalForm.classList.remove("hidden");
  deadlinesForm.classList.add("hidden");
  changePasswordForm.classList.add("hidden");

  resetButtons();
  btnPersonal.classList.add("bg-orange-100", "text-orange-600");
});

btnDeadlines.addEventListener("click", () => {
  deadlinesForm.classList.remove("hidden");
  personalForm.classList.add("hidden");
  changePasswordForm.classList.add("hidden");

  resetButtons();
  btnDeadlines.classList.add("bg-orange-100", "text-orange-600");
});

btnChangePassword.addEventListener("click", () => {
  changePasswordForm.classList.remove("hidden");
  personalForm.classList.add("hidden");
  deadlinesForm.classList.add("hidden");

  resetButtons();
  btnChangePassword.classList.add("bg-orange-100", "text-orange-600");
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
    // Handle personal information form submission with AJAX and SweetAlert
    document.getElementById('personalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
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
                // Success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                // Update session data in the form if needed
                if (data.updated_data) {
                    Object.keys(data.updated_data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.value = data.updated_data[key];
                        }
                    });
                }
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
    </script>
</body>

</html>
