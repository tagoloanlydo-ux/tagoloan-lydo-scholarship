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
                <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} Lydo Admin</span>
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
           class="flex items-center p-2 rounded-lg text-black-700 bg-violet-600 text-white">
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
          <a href="/lydo_admin/settings" class=" flex items-center p-3 rounded-lg text-black-600 hover:bg-violet-600 hover:text-white">
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
            <div class="flex-1 overflow-hidden p-4 md:p-5 text-[16px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">List of Scholars</h2>
                </div>

                <!-- Filter Section -->
                <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                    <form method="GET" action="{{ route('LydoAdmin.scholar') }}" class="flex flex-col md:flex-row gap-4" id="filterForm">
                        <div class="flex-1">
                            <input type="text" name="search" placeholder="Search by name..." 
                                   value="{{ request('search') }}" 
                                   class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                        </div>
                        <div class="flex-1">
                            <select name="barangay" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                        {{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <select name="academic_year" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                <option value="">All Academic Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Scholars Table -->
                <div class="bg-white  shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-black-800">Scholars List</h3>
                            <p class="text-sm text-black-600 mt-1">This table contains the list of active scholars currently enrolled in the scholarship program.</p>
                        </div>
                    <div class="flex space-x-2">
                            <button id="generateAnnouncementBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                Copy Names
                            </button>
                            <button id="sendEmailBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                Email
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg rounded-lg overflow-hidden border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Email</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Academic Year</th>
                                 </tr>
                            </thead>
                            <tbody>
                                @forelse($scholars as $scholar)
                                    <tr class="scholar-row hover:bg-gray-50 border-b">
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <input type="checkbox" name="selected_scholars" value="{{ $scholar->applicant_email }}" data-scholar-id="{{ $scholar->scholar_id }}" class="scholar-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $scholar->applicant_fname }} {{ $scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '' }}{{ $scholar->applicant_lname }}{{ $scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : '' }}
                                            </div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm text-gray-900">{{ $scholar->applicant_brgy }}</div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm text-gray-900">{{ $scholar->applicant_email }}</div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm text-gray-900">{{ $scholar->applicant_school_name }}</div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm text-gray-900">{{ $scholar->applicant_course }}</div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                        <div class="text-sm text-gray-900">{{ $scholar->applicant_acad_year ?? 'N/A' }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 border border-gray-200 text-gray-500">No scholars found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        {{ $scholars->links() }}
                    </div>
                </div>

            </div>

            <!-- Email Modal -->
            <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 mx-auto p-6 border w-full max-w-3xl shadow-2xl rounded-xl bg-white">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-envelope text-blue-600 mr-3"></i>
                            Send Email
                        </h3>
                        <button type="button" id="closeEmailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form id="emailForm" method="POST" action="{{ route('LydoAdmin.sendEmail') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Recipients</label>
                            <input type="text" id="emailTo" name="email" readonly class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-medium">
                        </div>



                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                                <input type="text" name="subject" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Enter email subject">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                                <select name="email_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="plain">Plain Email</option>
                                    <option value="account_creation">Resend Registration Link</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                            <textarea name="message" required rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical" placeholder="Enter your message"></textarea>
                        </div>

                        <input type="hidden" id="scholarId" name="scholar_id" value="">

                        <!-- Loading Indicator -->
                        <div id="emailLoading" class="hidden flex items-center justify-center py-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span class="ml-3 text-base text-gray-600 font-medium">Sending email...</span>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                            <button type="button" id="cancelEmail" class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" id="sendEmailButton" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                <i class="fas fa-paper-plane mr-2"></i>Send Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Announcement Modal -->
            <div id="announcementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-2xl rounded-xl bg-white">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-bullhorn text-green-600 mr-3"></i>
                            Generated Announcement
                        </h3>
                        <button type="button" id="closeAnnouncementModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-info-circle text-green-600 mr-2"></i>
                                <span class="text-sm font-medium text-green-800">Announcement Preview</span>
                            </div>
                            <p class="text-sm text-green-700">This announcement groups scholars by barangay with numbered lists for easy reference.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                                Announcement Content
                            </label>
                            <textarea id="announcementContent" readonly rows="12" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 font-mono text-sm leading-relaxed resize-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="Announcement content will appear here..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                            <button type="button" id="copyAnnouncement" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow-sm flex items-center">
                                <i class="fas fa-copy mr-2"></i>Copy to Clipboard
                            </button>
                            <button type="button" id="closeAnnouncement" class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>




            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const selectAll = document.getElementById('selectAll');
                    const checkboxes = document.querySelectorAll('.scholar-checkbox');
                    const sendEmailBtn = document.getElementById('sendEmailBtn');
                    const generateAnnouncementBtn = document.getElementById('generateAnnouncementBtn');
                    const emailModal = document.getElementById('emailModal');
                    const announcementModal = document.getElementById('announcementModal');
                    const announcementContent = document.getElementById('announcementContent');
                    const cancelEmail = document.getElementById('cancelEmail');
                    const closeAnnouncement = document.getElementById('closeAnnouncement');
                    const copyAnnouncement = document.getElementById('copyAnnouncement');
                    const emailForm = document.getElementById('emailForm');
                    const emailLoading = document.getElementById('emailLoading');
                    const sendEmailButton = document.getElementById('sendEmailButton');
                    let allFilteredScholarEmails = new Set(); // Store all filtered

                    // Select All checkbox functionality
                    selectAll.addEventListener('change', async function() {
                        if (this.checked) {
                            selectAll.disabled = true;
                            selectAll.nextElementSibling?.classList.add('hidden');
                            const loadingSpan = document.createElement('span');
                            loadingSpan.className = 'ml-2 text-sm text-gray-500';
                            loadingSpan.textContent = 'Loading...';
                            selectAll.parentNode.appendChild(loadingSpan);

                            try {
                                // Get current filter parameters
                                const search = document.querySelector('input[name="search"]').value;
                                const barangay = document.querySelector('select[name="barangay"]').value;
                                const academicYear = document.querySelector('select[name="academic_year"]').value;

                                // Fetch all filtered scholar emails from server
                                const response = await fetch(`/lydo_admin/get-all-filtered-scholars?search=${encodeURIComponent(search)}&barangay=${encodeURIComponent(barangay)}&academic_year=${encodeURIComponent(academicYear)}`);
                                const data = await response.json();

                                // Store all filtered scholar emails
                                allFilteredScholarEmails = new Set(data.scholar_emails);

                                // Check all checkboxes that match the filtered emails
                                checkboxes.forEach(checkbox => {
                                    const scholarEmail = checkbox.value;
                                    checkbox.checked = allFilteredScholarEmails.has(scholarEmail);
                                });

                                updateSendButton();
                            } catch (error) {
                                console.error('Error fetching filtered scholars:', error);
                                // Fallback: just select visible checkboxes
                                checkboxes.forEach(checkbox => {
                                    checkbox.checked = true;
                                });
                            } finally {
                                // Remove loading state
                                selectAll.disabled = false;
                                loadingSpan.remove();
                                selectAll.nextElementSibling?.classList.remove('hidden');
                            }
                        } else {
                            // Uncheck all checkboxes
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = false;
                            });
                            allFilteredScholarEmails.clear();
                            updateSendButton();
                        }
                    });

                    // Individual checkbox change
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            updateSendButton();
                            
                            // Update selectAll checkbox state
                            const allChecked = [...checkboxes].every(cb => cb.checked);
                            const someChecked = [...checkboxes].some(cb => cb.checked);
                            
                            selectAll.checked = allChecked;
                            selectAll.indeterminate = someChecked && !allChecked;
                        });
                    });

                    // Update send button state
                    function updateSendButton() {
                        const selectedCount = document.querySelectorAll('.scholar-checkbox:checked').length;
                        sendEmailBtn.disabled = selectedCount === 0;
                        generateAnnouncementBtn.disabled = selectedCount === 0;

                        // Show or hide buttons based on selection
                        if (selectedCount > 0) {
                            sendEmailBtn.classList.remove('hidden');
                            generateAnnouncementBtn.classList.remove('hidden');
                        } else {
                            sendEmailBtn.classList.add('hidden');
                            generateAnnouncementBtn.classList.add('hidden');
                        }
                    }



                    // Open email modal
                    sendEmailBtn.addEventListener('click', function() {
                        const selectedEmails = Array.from(document.querySelectorAll('.scholar-checkbox:checked'))
                            .map(checkbox => checkbox.value)
                            .join(', ');

                        const selectedScholarIds = Array.from(document.querySelectorAll('.scholar-checkbox:checked'))
                            .map(checkbox => checkbox.getAttribute('data-scholar-id'))
                            .join(', ');

                        emailTo.value = selectedEmails;
                        document.getElementById('scholarId').value = selectedScholarIds;

                        emailModal.classList.remove('hidden');
                    });

                    // Close email modal
                    cancelEmail.addEventListener('click', function() {
                        emailModal.classList.add('hidden');
                    });

                    // Close email modal with close button
                    document.getElementById('closeEmailModal').addEventListener('click', function() {
                        emailModal.classList.add('hidden');
                    });

                    // Handle email form submission
                    emailForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Show loading indicator
                        emailLoading.classList.remove('hidden');
                        sendEmailButton.disabled = true;
                        
                        const formData = new FormData(this);
                        formData.append('email', emailTo.value);

                        fetch('{{ route("LydoAdmin.sendEmail") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide loading indicator
                            emailLoading.classList.add('hidden');
                            sendEmailButton.disabled = false;
                            
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Email sent successfully!',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                emailModal.classList.add('hidden');
                                emailForm.reset();
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to send email: ' + data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            // Hide loading indicator
                            emailLoading.classList.add('hidden');
                            sendEmailButton.disabled = false;
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error sending email: ' + error.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                    });

                    // Close modal when clicking outside
                    window.addEventListener('click', function(e) {
                        if (e.target === emailModal) {
                            emailModal.classList.add('hidden');
                        }
                        if (e.target === announcementModal) {
                            announcementModal.classList.add('hidden');
                        }
                    });

                    // Auto-submit filter form when any filter changes
                    const filterForm = document.getElementById('filterForm');
                    const filterInputs = filterForm.querySelectorAll('input, select');
                    
                    filterInputs.forEach(input => {
                        input.addEventListener('change', function() {
                            filterForm.submit();
                        });
                    });

                    // Also submit on search input (for typing)
                    const searchInput = filterForm.querySelector('input[name="search"]');
                    let searchTimeout;
                    
                    searchInput.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            filterForm.submit();
                        }, 500); // Submit after 500ms of no typing
                    });


                    // Generate Announcement button functionality
generateAnnouncementBtn.addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        Swal.fire({
            title: 'No Selection!',
            text: 'Please select at least one scholar to copy names.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Group selected scholars by barangay
    const barangayGroups = {};
    selectedCheckboxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const name = row.querySelector('td:nth-child(2) div').textContent.trim();
        const barangay = row.querySelector('td:nth-child(3) div').textContent.trim();
        if (!barangayGroups[barangay]) {
            barangayGroups[barangay] = [];
        }
        barangayGroups[barangay].push(name);
    });

    // Build the output string
    let output = '';
    Object.keys(barangayGroups).forEach(barangay => {
        output += `${barangay}\n`;
        barangayGroups[barangay].forEach((name, idx) => {
            output += `${idx + 1}. ${name}\n`;
        });
        output += '\n';
    });

    navigator.clipboard.writeText(output.trim()).then(() => {
        Swal.fire({
            title: 'Success!',
            text: 'Selected scholar names grouped by barangay copied to clipboard!',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }).catch(err => {
        Swal.fire({
            title: 'Error!',
            text: 'Failed to copy names: ' + err,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

                    // Copy announcement to clipboard
                    copyAnnouncement.addEventListener('click', function() {
                        announcementContent.select();
                        document.execCommand('copy');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Announcement copied to clipboard!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    });

                    // Close announcement modal
                    closeAnnouncement.addEventListener('click', function() {
                        announcementModal.classList.add('hidden');
                    });

                    // Initialize button states
                    updateSendButton();
                });
            </script>


        </div>
    </div>
</body>

</html>
