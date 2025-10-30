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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.tailwindcss.min.css">
    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.tailwindcss.min.js"></script>

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
                 
</div>
            </div>
            <div class="flex-1 overflow-hidden p-4 md:p-5 text-[16px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">List of Scholars</h2>
                </div>

                <!-- Filter Section -->
                <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                    <form method="GET" action="{{ route('LydoAdmin.scholar') }}" id="filterForm">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <input type="text" name="search" id="searchInput" placeholder="Search by name..."
                                       class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="flex-1">
                                <select name="barangay" id="barangayFilter"
        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
        <option value="">All Barangays</option>
        @foreach($barangays as $barangay)
            <option value="{{ $barangay }}" {{ ($selectedBarangay ?? '') == $barangay ? 'selected' : '' }}>
                {{ $barangay }}
            </option>
        @endforeach
    </select>
</div>

<div class="flex-1">
    <select name="academic_year" id="academicYearFilter"
        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
        <option value="">All Academic Years</option>
        @foreach($academicYears as $year)
            <option value="{{ $year }}" {{ ($selectedAcademicYear ?? '') == $year ? 'selected' : '' }}>
                {{ $year }}
            </option>
        @endforeach
    </select>
           </div>
                        </div>
                    </form>
                </div>

            
              <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Scholars List</h3>
        <div class="flex space-x-2">
            <button id="copyNamesBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                Copy Names
            </button>
            <button id="emailSelectedBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                Email
            </button>
            <button id="printPdfBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                <i class="fas fa-print mr-2"></i> Print PDF
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
     <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
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
    
    <tbody id="scholarsTableBody">
        @include('lydo_admin.partials.scholars_table', ['scholars' => $scholars])
    </tbody>
</table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-white border-t border-gray-200">
        {{ $scholars->links() }}
    </div>
</div>

                    <!-- Email Modal -->
                    <div id="emailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Send Email to Selected Scholars</h3>
                                    <button id="closeEmailModal" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>

                                <form id="emailForm">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Subject</label>
                                        <input type="text" id="emailSubject" name="subject" required
                                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter email subject...">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Message</label>
                                        <textarea id="emailMessage" name="message" rows="6" required
                                                  class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                  placeholder="Enter your email message..."></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipients Preview</label>
                                        <div id="recipientsPreview" class="p-3 bg-gray-50 border border-gray-200 rounded-md max-h-32 overflow-y-auto text-sm text-gray-600">
                                            No recipients selected
                                        </div>
                                    </div>

                                    <div class="flex justify-end space-x-3">
                                        <button type="button" id="cancelEmailBtn"
                                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit" id="sendEmailBtn"
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                            <span id="sendEmailText">Send Email</span>
                                            <span id="sendEmailLoading" class="hidden">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Sending...
                                            </span>
                                        </button>
                                    </div>
                                </form>
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
    const searchInput = document.getElementById('searchInput');
    const barangayFilter = document.getElementById('barangayFilter');
    const academicYearFilter = document.getElementById('academicYearFilter');
    const scholarsTableBody = document.getElementById('scholarsTableBody'); // Target tbody only
    const selectAll = document.getElementById('selectAll');
    const generateAnnouncementBtn = document.getElementById('generateAnnouncementBtn');
    const sendEmailBtn = document.getElementById('sendEmailBtn');

    let searchTimeout;

    // Handle search input with debouncing
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 500);
    });

    // Prevent form submission to avoid page refresh
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
    });

    // Handle filter changes
    barangayFilter.addEventListener('change', function() {
        performSearch(searchInput.value.trim());
    });

    academicYearFilter.addEventListener('change', function() {
        performSearch(searchInput.value.trim());
    });

    // Perform AJAX search
    function performSearch(query = '') {
        const barangay = barangayFilter.value;
        const academicYear = academicYearFilter.value;

        // Show loading state
        if (scholarsTableBody) {
            scholarsTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4" style="font-size: 15px; font-weight: bold;">
                        Loading...
                    </td>
                </tr>
            `;
        }

        // Build URL with all filters
        let url = '{{ route("LydoAdmin.search") }}?type=scholars';
        if (query) url += `&query=${encodeURIComponent(query)}`;
        if (barangay) url += `&barangay=${encodeURIComponent(barangay)}`;
        if (academicYear) url += `&academic_year=${encodeURIComponent(academicYear)}`;

        fetch(url)
            .then(response => response.text())
            .then(html => {
                if (scholarsTableBody) {
                    scholarsTableBody.innerHTML = html;
                }
                reattachEventListeners();
                updateButtonStates(); // Update button states after loading new content
            })
            .catch(err => {
                console.error('Search error:', err);
                if (scholarsTableBody) {
                    scholarsTableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-danger py-4">
                                Failed to load data.
                            </td>
                        </tr>
                    `;
                }
            });
    }

    // Handle pagination links
    function handlePaginationClick(e) {
        e.preventDefault();
        const url = e.target.getAttribute('href');
        performSearchWithURL(url);
    }

    function performSearchWithURL(url) {
        if (scholarsTableBody) {
            scholarsTableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Loading...
                    </td>
                </tr>
            `;
        }

        fetch(url)
            .then(response => response.text())
            .then(html => {
                if (scholarsTableBody) {
                    scholarsTableBody.innerHTML = html;
                }
                reattachEventListeners();
            })
            .catch(err => {
                console.error('Pagination error:', err);
            });
    }

    // Reattach event listeners
    function reattachEventListeners() {
        // Reattach select all checkbox
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.scholar-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateButtonStates();
            });
        }

        // Reattach individual checkbox listeners
        const checkboxes = document.querySelectorAll('.scholar-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = checkboxes.length > 0 && 
                    Array.from(checkboxes).every(cb => cb.checked);
                if (selectAll) {
                    selectAll.checked = allChecked;
                }
                updateButtonStates();
            });
        });

        // Reattach pagination links
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', handlePaginationClick);
        });

        updateButtonStates();
    }

    function updateButtonStates() {
        const checkedCount = document.querySelectorAll('.scholar-checkbox:checked').length;
        
        if (generateAnnouncementBtn) {
            generateAnnouncementBtn.classList.toggle('hidden', checkedCount === 0);
        }
        
        if (sendEmailBtn) {
            sendEmailBtn.classList.toggle('hidden', checkedCount === 0);
        }
    }

    // Initialize
    reattachEventListeners();
});
 </script>
                  <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const selectAll = document.getElementById('selectAll');
                        const checkboxes = document.querySelectorAll('.scholar-checkbox');
                        const copyNamesBtn = document.getElementById('copyNamesBtn');
                        const emailSelectedBtn = document.getElementById('emailSelectedBtn');

                        // Email modal elements
                        const emailModal = document.getElementById('emailModal');
                        const closeEmailModal = document.getElementById('closeEmailModal');
                        const cancelEmailBtn = document.getElementById('cancelEmailBtn');
                        const emailForm = document.getElementById('emailForm');
                        const emailSubject = document.getElementById('emailSubject');
                        const emailMessage = document.getElementById('emailMessage');
                        const recipientsPreview = document.getElementById('recipientsPreview');
                        const sendEmailBtn = document.getElementById('sendEmailBtn');
                        const sendEmailText = document.getElementById('sendEmailText');
                        const sendEmailLoading = document.getElementById('sendEmailLoading');

                        // Select all checkbox functionality
                        selectAll.addEventListener('change', function() {
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                            updateButtons();
                            updateRecipientsPreview();
                        });

                        // Update button states
                        function updateButtons() {
                            const selectedCount = document.querySelectorAll('.scholar-checkbox:checked').length;
                            const hasSelection = selectedCount > 0;

                            if (copyNamesBtn) {
                                copyNamesBtn.disabled = !hasSelection;
                                copyNamesBtn.classList.toggle('hidden', !hasSelection);
                            }
                            if (emailSelectedBtn) {
                                emailSelectedBtn.disabled = !hasSelection;
                                emailSelectedBtn.classList.toggle('hidden', !hasSelection);
                            }
                        }

                        // Individual checkbox change
                        checkboxes.forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                updateButtons();
                                updateRecipientsPreview();

                                // Update selectAll checkbox state
                                const allChecked = [...checkboxes].every(cb => cb.checked);
                                const someChecked = [...checkboxes].some(cb => cb.checked);

                                selectAll.checked = allChecked;
                                selectAll.indeterminate = someChecked && !allChecked;
                            });
                        });

                        // Update recipients preview
                        function updateRecipientsPreview() {
                            const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');

                            if (selectedCheckboxes.length === 0) {
                                if (recipientsPreview) recipientsPreview.innerHTML = 'No recipients selected';
                                return;
                            }

                            const recipients = Array.from(selectedCheckboxes).map(checkbox => {
                                const row = checkbox.closest('tr');
                                const name = row.querySelector('td:nth-child(2)').textContent.trim();
                                const email = row.querySelector('td:nth-child(4)').textContent.trim();
                                return `${name} (${email})`;
                            });

                            if (recipientsPreview) recipientsPreview.innerHTML = recipients.join('<br>');
                        }

                        // Copy Names button functionality
                        if (copyNamesBtn) {
                            copyNamesBtn.addEventListener('click', function() {
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
                                    const name = row.querySelector('td:nth-child(2)').textContent.trim();
                                    const barangay = row.querySelector('td:nth-child(3)').textContent.trim();
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
                        }

                        // Email Selected button functionality
                        if (emailSelectedBtn) {
                            emailSelectedBtn.addEventListener('click', function() {
                                const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');

                                if (selectedCheckboxes.length === 0) {
                                    Swal.fire({
                                        title: 'No Selection!',
                                        text: 'Please select at least one scholar to send email.',
                                        icon: 'warning',
                                        confirmButtonText: 'OK'
                                    });
                                    return;
                                }

                                updateRecipientsPreview();
                                if (emailModal) emailModal.classList.remove('hidden');
                                if (emailSubject) emailSubject.focus();
                            });
                        }

                        // Close email modal
                        function closeEmailModalHandler() {
                            if (emailModal) emailModal.classList.add('hidden');
                            if (emailForm) emailForm.reset();
                            if (sendEmailText) sendEmailText.classList.remove('hidden');
                            if (sendEmailLoading) sendEmailLoading.classList.add('hidden');
                            if (sendEmailBtn) sendEmailBtn.disabled = false;
                        }

                        if (closeEmailModal) closeEmailModal.addEventListener('click', closeEmailModalHandler);
                        if (cancelEmailBtn) cancelEmailBtn.addEventListener('click', closeEmailModalHandler);

                        // Close modal when clicking outside
                        if (emailModal) {
                            emailModal.addEventListener('click', function(e) {
                                if (e.target === emailModal) {
                                    closeEmailModalHandler();
                                }
                            });
                        }

                        // Send email form submission
                        if (emailForm) {
                            emailForm.addEventListener('submit', function(e) {
                                e.preventDefault();

                                const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');
                                const subject = emailSubject ? emailSubject.value.trim() : '';
                                const message = emailMessage ? emailMessage.value.trim() : '';

                                if (!subject || !message) {
                                    Swal.fire({
                                        title: 'Missing Information!',
                                        text: 'Please fill in both subject and message fields.',
                                        icon: 'warning',
                                        confirmButtonText: 'OK'
                                    });
                                    return;
                                }

                                if (selectedCheckboxes.length === 0) {
                                    Swal.fire({
                                        title: 'No Recipients!',
                                        text: 'No scholars selected to send email to.',
                                        icon: 'warning',
                                        confirmButtonText: 'OK'
                                    });
                                    return;
                                }

                                // Collect recipient data
                                const recipients = Array.from(selectedCheckboxes).map(checkbox => {
                                    const row = checkbox.closest('tr');
                                    return {
                                        id: checkbox.getAttribute('data-scholar-id'),
                                        name: row.querySelector('td:nth-child(2)').textContent.trim(),
                                        email: row.querySelector('td:nth-child(4)').textContent.trim()
                                    };
                                });

                                // Show loading state
                                if (sendEmailText) sendEmailText.classList.add('hidden');
                                if (sendEmailLoading) sendEmailLoading.classList.remove('hidden');
                                if (sendEmailBtn) sendEmailBtn.disabled = true;

                                // Send email via AJAX
                                fetch('/lydo_admin/send-email', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                    },
                                    body: JSON.stringify({
                                        email: recipients.map(r => r.email).join(','),
                                        subject: subject,
                                        message: message,
                                        email_type: 'plain'
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: `Email sent successfully to ${recipients.length} scholar(s)!`,
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        });
                                        closeEmailModalHandler();
                                    } else {
                                        throw new Error(data.message || 'Failed to send email');
                                    }
                                })
                                .catch(error => {
                                    console.error('Email sending error:', error);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to send email: ' + error.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                })
                                .finally(() => {
                                    // Reset loading state
                                    if (sendEmailText) sendEmailText.classList.remove('hidden');
                                    if (sendEmailLoading) sendEmailLoading.classList.add('hidden');
                                    if (sendEmailBtn) sendEmailBtn.disabled = false;
                                });
                            });
                        }

                        // Initialize button states
                        updateButtons();
                    });
                    </script>

                    <script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const scholarsTableBody = document.getElementById('scholarsTableBody');
    const barangayFilter = document.getElementById('barangayFilter');
    const academicYearFilter = document.getElementById('academicYearFilter');
    let searchTimeout;

    // When typing in search bar
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Clear previous timeout (debounce)
        clearTimeout(searchTimeout);

        // Immediately show "Searching..." in the table body
        scholarsTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-gray-500 py-4 text-[15px] font-semibold">
                    <div class="flex justify-center items-center space-x-2">
                        <span class="loader"></span>
                        <span>Searching...</span>
                    </div>
                </td>
            </tr>
        `;

        // Wait 500ms before performing search
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 500);
    });

    // Also apply to filters
    [barangayFilter, academicYearFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', function() {
                performSearch(searchInput.value.trim());
            });
        }
    });

    // Function to perform AJAX search
    function performSearch(query = '') {
        const barangay = barangayFilter?.value || '';
        const academicYear = academicYearFilter?.value || '';

        let url = '{{ route("LydoAdmin.search") }}?type=scholars';
        if (query) url += `&query=${encodeURIComponent(query)}`;
        if (barangay) url += `&barangay=${encodeURIComponent(barangay)}`;
        if (academicYear) url += `&academic_year=${encodeURIComponent(academicYear)}`;

        fetch(url)
            .then(response => response.text())
            .then(html => {
                scholarsTableBody.innerHTML = html;
            })
            .catch(err => {
                console.error('Search error:', err);
                scholarsTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-red-500 py-4 font-semibold">
                            Failed to load data.
                        </td>
                    </tr>
                `;
            });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const scholarsTableBody = document.getElementById('scholarsTableBody');
    const barangayFilter = document.getElementById('barangayFilter');
    const academicYearFilter = document.getElementById('academicYearFilter');
    const scholarsTableContainer = document.getElementById('scholarsTableContainer');
    let searchTimeout;

    function showLoading() {
        scholarsTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-gray-500 py-4 text-[15px] font-semibold">
                    <div class="flex justify-center items-center space-x-2">
                        <span class="loader"></span>
                        <span>Searching...</span>
                    </div>
                </td>
            </tr>
        `;
    }

    function performSearch(query = '', pageUrl = null) {
        const barangay = barangayFilter?.value || '';
        const academicYear = academicYearFilter?.value || '';

        // Construct URL
        let url = pageUrl || '{{ route("LydoAdmin.search") }}?type=scholars';
        const params = new URLSearchParams();
        if (query) params.append('query', query);
        if (barangay) params.append('barangay', barangay);
        if (academicYear) params.append('academic_year', academicYear);

        // Add params to URL (without overwriting ?page)
        if (!url.includes('?')) url += '?' + params.toString();
        else url += '&' + params.toString();

        // Fetch table content
        fetch(url)
            .then(res => res.text())
            .then(html => {
                scholarsTableContainer.innerHTML = html;
            })
            .catch(err => {
                console.error('Search error:', err);
                scholarsTableBody.innerHTML = `
                    <tr><td colspan="7" class="text-center text-red-500 py-4 font-semibold">
                        Failed to load data.
                    </td></tr>`;
            });
    }

    // Handle typing (debounce)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        showLoading();
        searchTimeout = setTimeout(() => {
            performSearch(this.value.trim());
        }, 500);
    });

    // Handle filter change
    [barangayFilter, academicYearFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', function() {
                performSearch(searchInput.value.trim());
            });
        }
    });

    // Handle pagination click dynamically (AJAX)
    document.addEventListener('click', function(e) {
        if (e.target.matches('.pagination a')) {
            e.preventDefault();
            const pageUrl = e.target.getAttribute('href');
            performSearch(searchInput.value.trim(), pageUrl);
        }
    });
});
</script>


    <script>
    function toggleDropdown(id) {
        const menu = document.getElementById(id);
        menu.classList.toggle("hidden");
    }
</script>
    <script>
                  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const barangayFilter = document.getElementById('barangayFilter');
    const academicYearFilter = document.getElementById('academicYearFilter');
    const scholarsTableBody = document.getElementById('scholarsTableBody');
    const selectAll = document.getElementById('selectAll');
    const generateAnnouncementBtn = document.getElementById('generateAnnouncementBtn');
    const sendEmailBtn = document.getElementById('sendEmailBtn');

    // Handle search input with debouncing (similar to pregnant search)
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 500);
    });

    // Handle filter changes
    barangayFilter.addEventListener('change', function() {
        performSearch(searchInput.value.trim());
    });

    academicYearFilter.addEventListener('change', function() {
        performSearch(searchInput.value.trim());
    });

    // Perform AJAX search
    function performSearch(query = '') {
        const barangay = barangayFilter.value;
        const academicYear = academicYearFilter.value;
        
        // Show loading state
        scholarsTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted" style="font-size: 15px; font-weight: bold;">
                    Loading...
                </td>
            </tr>
        `;

        // Build URL with all filters
        let url = '{{ route("LydoAdmin.search") }}?type=scholars';
        if (query) url += `&query=${encodeURIComponent(query)}`;
        if (barangay) url += `&barangay=${encodeURIComponent(barangay)}`;
        if (academicYear) url += `&academic_year=${encodeURIComponent(academicYear)}`;

        fetch(url)
            .then(response => response.text())
            .then(html => {
                scholarsTableBody.innerHTML = html;
                updateButtonStates(); // Reinitialize button states after content load
                reattachEventListeners(); // Reattach event listeners to new elements
            })
            .catch(err => {
                console.error('Search error:', err);
                scholarsTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger">
                            Failed to load data.
                        </td>
                    </tr>
                `;
            });
    }

    // Handle select all checkbox
    function handleSelectAll() {
        const checkboxes = document.querySelectorAll('.scholar-checkbox');
        const allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        if (selectAll) {
            selectAll.checked = allChecked;
        }
        updateButtonStates();
    }

    // Handle individual checkboxes
    function handleIndividualCheckbox() {
        const checkboxes = document.querySelectorAll('.scholar-checkbox');
        const allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        if (selectAll) {
            selectAll.checked = allChecked;
        }
        updateButtonStates();
    }

    // Update button states
    function updateButtonStates() {
        const checkedCount = document.querySelectorAll('.scholar-checkbox:checked').length;
        
        if (generateAnnouncementBtn) {
            if (checkedCount > 0) {
                generateAnnouncementBtn.classList.remove('hidden');
            } else {
                generateAnnouncementBtn.classList.add('hidden');
            }
        }
        
        if (sendEmailBtn) {
            if (checkedCount > 0) {
                sendEmailBtn.classList.remove('hidden');
            } else {
                sendEmailBtn.classList.add('hidden');
            }
        }
    }

    // Reattach event listeners after AJAX content load
    function reattachEventListeners() {
        // Reattach select all checkbox
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.scholar-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateButtonStates();
            });
        }

        // Reattach individual checkbox listeners
        const checkboxes = document.querySelectorAll('.scholar-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', handleIndividualCheckbox);
        });

        // Update button states
        updateButtonStates();
    }

    // Initialize on page load
    reattachEventListeners();
    
    // Remove the DataTable initialization since we're using custom AJAX now
    // const table = $('#scholarsTable').DataTable({ ... }); // Remove this

    // If you need to keep some DataTable features, you can initialize a simple one:
    $('#scholarsTable').DataTable({
        paging: true,
        searching: false, // We handle search via AJAX
        info: true,
        ordering: true,
        responsive: true,
        dom: 'rtip',
        language: {
            emptyTable: "No scholars found."
        }
    });
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
</body>

</html>
