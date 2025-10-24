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
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mx-4">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mx-4">
        {{ session('error') }}
    </div>
    @endif
        <!-- Main Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <div class="w-16 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
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
                                    <i class="bx bx-check-circle mr-2"></i> Update Status
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
            <div class="flex-1 main-content-area p-4 md:p-2 text-[14px]">
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

          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div>
              <label class="block text-base text-gray-600 mb-1">First Name</label>
              <input type="text" name="lydopers_fname" value="{{ session('lydopers')->lydopers_fname }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            </div>
                        <div>
              <label class="block text-base text-gray-600 mb-1">Middle Name</label>
              <input type="text" name="lydopers_mname" value="{{ session('lydopers')->lydopers_mname }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            </div>
            <div>
              <label class="block text-base text-gray-600 mb-1">Last Name</label>
              <input type="text" name="lydopers_lname" value="{{ session('lydopers')->lydopers_lname }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            </div>
            <div>
              <label class="block text-base text-gray-600 mb-1">Suffix</label>
              <input type="text" name="lydopers_suffix" value="{{ session('lydopers')->lydopers_suffix }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            </div>
            <div class="md:col-span-2">
              <label class="block text-base text-gray-600 mb-1">Email</label>
              <input type="email" name="lydopers_email" value="{{ session('lydopers')->lydopers_email }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            </div>
            <div class="md:col-span-2">
              <label class="block text-base text-gray-600 mb-1">Address</label>
              <input type="text" name="lydopers_address" value="{{ session('lydopers')->lydopers_address }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
            </div>
            <div>
              <label class="block text-base text-gray-600 mb-1">Phone Number</label>
              <input type="tel" name="lydopers_contact_number" value="{{ session('lydopers')->lydopers_contact_number }}" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
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

          <form id="passwordForm" method="POST" action="{{ route('MayorStaff.updatePassword') }}"
            class="hidden flex-grow bg-white rounded-2xl px-10 py-8 shadow-lg border border-gray-100">
        @csrf
      <h1 class="text-base font-semibold text-gray-800 mb-8">Change Password</h1>
      <p class="text-sm text-gray-500 mb-4">Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.</p>

        <div class="mb-6">
          <label class="block text-base text-gray-600 mb-1">Current Password</label>
          <input type="password" name="current_password" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
        </div>
        <div class="mb-6">
          <label class="block text-base text-gray-600 mb-1">New Password</label>
          <input type="password" name="new_password" id="new_password" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
        </div>
        <div class="mb-6">
          <label class="block text-base text-gray-600 mb-1">Confirm New Password</label>
          <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="w-full bg-gray-50 border rounded-xl py-3 px-4 text-base outline-none focus:ring-2 focus:ring-violet-400 transition"/>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-4">
          <button type="reset" class="px-6 py-3 border border-violet-500 rounded-xl font-semibold text-violet-600 hover:bg-violet-50 transition">
            Cancel
          </button>
          <button type="submit" class="px-6 py-3 bg-violet-500 rounded-xl font-semibold text-white hover:bg-violet-600 transition">
            Update Password
          </button>
        </div>
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
        </script>
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
 <script src="{{ asset('js/logout.js') }}"></script>
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
</body>

</html>