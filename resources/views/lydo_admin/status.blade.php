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
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        imageUrl: 'https://cdn-icons-png.flaticon.com/512/190/190411.png', // You can use your own image URL
        imageWidth: 80,
        imageHeight: 80,
        imageAlt: 'Success image',
        showConfirmButton: false,
        timer: 2500
    });
</script>
@endif
    <div class="dashboard-grid">
        <!-- Header -->
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans fixed top-0 left-0 right-0 z-50">
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
      <div class="flex flex-1 overflow-hidden mt-16">
            <!-- Sidebar -->
            <div class="w-16 md:w-72 bg-white shadow-md flex flex-col transition-all duration-300 fixed left-0 top-16 bottom-0 z-40">
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
           class="flex items-center p-2 rounded-lg text-black-700 bg-violet-600 text-white">
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
            <div class="flex-1 overflow-auto ml-16 md:ml-72 mt-4 p-5 md:p-6 text-[17px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Scholar Status Management</h2>
                </div>

                <!-- Filter Section -->
                <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                    <div class="flex flex-col md:flex-row gap-4" id="filterForm">
                        <div class="flex-1">
                            <input type="text" id="searchInput" placeholder="Search by name..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 placeholder-gray-500">
                        </div>
                        <div class="flex-1">
                            <select id="barangaySelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}">
                                        {{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Scholars without renewal applications -->
                <div class="p-5 bg-white rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Active Scholars Without Renewal Applications</h3>
                        <div class="flex space-x-2">
                            <button type="button" id="copyNamesBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                Copy Names
                            </button>
                            <button type="button" id="sendEmailBtn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                Email
                            </button>
                            <button type="button" id="updateStatusBtn" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                Update Status
                            </button>
                        </div>
                    </div>

                    @if($scholarsWithoutRenewal->count() > 0)
                    <form id="scholarForm" action="{{ route('LydoAdmin.updateScholarStatus') }}" method="POST">
                        @csrf

                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border-collapse text-[17px] shadow-lg overflow-hidden border border-gray-200">
                                <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                                    <tr>
                                        <th class="px-4 py-3 border border-gray-200 text-center">
                                            <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                        </th>
                                        <th class="px-4 py-3 border align-middle border-gray-200 text-center">Name</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Barangay</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Email</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">School</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Year Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scholarsWithoutRenewal as $scholar)
                                    <tr class="scholar-row hover:bg-gray-50 border-b">
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <input type="checkbox" name="selected_scholars[]" value="{{ $scholar->scholar_id }}" data-scholar-id="{{ $scholar->scholar_id }}" class="scholar-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            {{ $scholar->full_name }}
                                            @if($scholar->applicant_suffix)
                                                {{ $scholar->applicant_suffix }}
                                            @endif
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center barangay-cell">{{ $scholar->applicant_brgy }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $scholar->applicant_email }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $scholar->applicant_school_name }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $scholar->applicant_year_level }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 bg-white border-t border-gray-200 mt-4">
                            <div class="pagination-container" id="paginationContainer"></div>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No active scholars found without renewal applications.</p>
                    </div>
                    @endif
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

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Enter email subject">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                            <textarea name="message" required rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical" placeholder="Enter your message"></textarea>
                        </div>

                        <input type="hidden" id="scholarId" name="scholar_id" value="">
                        <input type="hidden" name="email_type" value="plain">

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

            <!-- Report Modal -->
            <div id="reportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Generated Report</h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Report Content</label>
                            <textarea id="reportContent" readonly rows="10" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 font-mono text-sm"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="copyReport" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Copy to Clipboard
                            </button>
                            <button type="button" id="closeReport" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
// Global pagination state for scholars
const scholarPaginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Function to get full name for sorting
function getScholarFullNameForSorting(row) {
    const nameCell = row.cells[1];
    if (!nameCell) return '';
    return nameCell.textContent.trim().toLowerCase();
}

// Function to sort rows alphabetically by name
function sortScholarRowsAlphabetically(rows) {
    return rows.sort((a, b) => {
        const nameA = getScholarFullNameForSorting(a);
        const nameB = getScholarFullNameForSorting(b);
        return nameA.localeCompare(nameB);
    });
}

// Initialize data from the table
function initializeScholarData() {
    const tableRows = Array.from(document.querySelectorAll('.scholar-row'));
    scholarPaginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by name
    scholarPaginationState.allRows = sortScholarRowsAlphabetically(scholarPaginationState.allRows);
    scholarPaginationState.filteredRows = [...scholarPaginationState.allRows];
}

// Initialize pagination
function initializeScholarPagination() {
    updateScholarPagination();
}

// Update pagination display
function updateScholarPagination() {
    const state = scholarPaginationState;
    const container = document.getElementById('paginationContainer');
    
    if (!container) return;
    
    // Hide all rows first
    state.allRows.forEach(row => {
        row.style.display = 'none';
    });
    
    // Calculate pagination for filtered rows
    const startIndex = (state.currentPage - 1) * state.rowsPerPage;
    const endIndex = startIndex + state.rowsPerPage;
    const pageRows = state.filteredRows.slice(startIndex, endIndex);
    
    // Show only rows for current page
    pageRows.forEach(row => {
        row.style.display = '';
    });
    
    // Update pagination controls
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    const startItem = state.filteredRows.length === 0 ? 0 : Math.min((state.currentPage - 1) * state.rowsPerPage + 1, state.filteredRows.length);
    const endItem = Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length);
    
    container.innerHTML = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> scholars
            </div>
            
            <div class="flex items-center space-x-1">
                <!-- First Page -->
                <button onclick="changeScholarPage(1)" 
                    class="px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 ${
                        state.currentPage === 1 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>
                
                <!-- Previous Page -->
                <button onclick="changeScholarPage(${state.currentPage - 1})" 
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === 1 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-left"></i>
                </button>
                
                <!-- Page Info -->
                <div class="flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 bg-white">
                    Page 
                    <input type="number" 
                           class="mx-2 w-12 text-center border border-gray-300 rounded px-1 py-1 text-sm" 
                           value="${state.currentPage}" 
                           min="1" 
                           max="${totalPages}" 
                           onchange="goToScholarPage(this.value)">
                    of ${totalPages}
                </div>
                
                <!-- Next Page -->
                <button onclick="changeScholarPage(${state.currentPage + 1})" 
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === totalPages 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>
                
                <!-- Last Page -->
                <button onclick="changeScholarPage(${totalPages})" 
                    class="px-3 py-2 text-sm font-medium rounded-r-md border border-gray-300 ${
                        state.currentPage === totalPages 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
        </div>
    `;
}

// Change page
function changeScholarPage(page) {
    const state = scholarPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateScholarPagination();
    updateCheckboxReferences();
}

// Go to specific page
function goToScholarPage(page) {
    const state = scholarPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateScholarPagination();
    updateCheckboxReferences();
}

// Initialize filtering functionality
function initializeScholarFiltering() {
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');

    function filterScholarTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedBarangay = barangaySelect.value;

        const filteredRows = scholarPaginationState.allRows.filter(row => {
            const nameCell = row.cells[1];
            const barangayCell = row.cells[2];

            if (!nameCell || !barangayCell) return false;

            const name = nameCell.textContent.toLowerCase();
            const barangay = barangayCell.textContent.trim();

            const nameMatch = name.includes(searchTerm);
            const barangayMatch = !selectedBarangay || barangay === selectedBarangay;

            return nameMatch && barangayMatch;
        });

        // Sort filtered results alphabetically
        const sortedFilteredRows = sortScholarRowsAlphabetically(filteredRows);

        // Update filtered rows and reset to page 1
        scholarPaginationState.filteredRows = sortedFilteredRows;
        scholarPaginationState.currentPage = 1;
        updateScholarPagination();
        updateCheckboxReferences();
        
        // Reset select all checkbox
        document.getElementById('selectAll').checked = false;
        document.getElementById('selectAll').indeterminate = false;
        updateButtons();
    }

    // Add event listeners with debouncing
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterScholarTable, 300));
    }
    if (barangaySelect) {
        barangaySelect.addEventListener('change', filterScholarTable);
    }
}

// Debounce function for search
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

// Update checkbox references after pagination/filtering
function updateCheckboxReferences() {
    const checkboxes = document.querySelectorAll('.scholar-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateButtons();
            
            // Update selectAll checkbox state
            const allChecked = [...checkboxes].every(cb => cb.checked);
            const someChecked = [...checkboxes].some(cb => cb.checked);

            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        });
    });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeScholarData();
    initializeScholarPagination();
    initializeScholarFiltering();

    // Your existing JavaScript code
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.scholar-checkbox');
    const copyNamesBtn = document.getElementById('copyNamesBtn');
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const updateStatusBtn = document.getElementById('updateStatusBtn');
    const emailModal = document.getElementById('emailModal');
    const closeEmailModal = document.getElementById('closeEmailModal');
    const cancelEmail = document.getElementById('cancelEmail');
    const emailForm = document.getElementById('emailForm');
    const emailTo = document.getElementById('emailTo');
    const scholarId = document.getElementById('scholarId');

    // Select all checkbox functionality
    selectAll.addEventListener('change', function() {
        const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateButtons();
    });

    // Update button states
    function updateButtons() {
        const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
        const selectedCount = Array.from(visibleCheckboxes).filter(cb => cb.checked).length;
        copyNamesBtn.disabled = selectedCount === 0;
        sendEmailBtn.disabled = selectedCount === 0;
        updateStatusBtn.disabled = selectedCount === 0;
        copyNamesBtn.classList.toggle('hidden', selectedCount === 0);
        sendEmailBtn.classList.toggle('hidden', selectedCount === 0);
        updateStatusBtn.classList.toggle('hidden', selectedCount === 0);
    }

    // Individual checkbox change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('scholar-checkbox')) {
            updateButtons();

            // Update selectAll checkbox state
            const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
            const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(visibleCheckboxes).some(cb => cb.checked);

            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        }
    });

    // Copy Names button functionality
    copyNamesBtn.addEventListener('click', function() {
        const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
        const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

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
            const barangay = row.querySelector('td.barangay-cell')
                ? row.querySelector('td.barangay-cell').textContent.trim()
                : '';
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

    // Send Email button functionality
    sendEmailBtn.addEventListener('click', function() {
        const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
        const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

        if (selectedCheckboxes.length === 0) {
            Swal.fire({
                title: 'No Selection!',
                text: 'Please select at least one scholar to send email.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Collect selected emails and scholar IDs
        let emails = [];
        let scholarIds = [];

        selectedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const email = row.querySelector('td:nth-child(4)').textContent.trim();
            emails.push(email);
            scholarIds.push(checkbox.getAttribute('data-scholar-id'));
        });

        emailTo.value = emails.join(', ');
        scholarId.value = scholarIds.join(',');

        // Show modal
        emailModal.classList.remove('hidden');
    });

    // Close modal functionality
    closeEmailModal.addEventListener('click', function() {
        emailModal.classList.add('hidden');
    });

    cancelEmail.addEventListener('click', function() {
        emailModal.classList.add('hidden');
    });

    // Close modal on outside click
    emailModal.addEventListener('click', function(e) {
        if (e.target === emailModal) {
            emailModal.classList.add('hidden');
        }
    });

    // Email form submission
    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(emailForm);
        const submitButton = emailForm.querySelector('#sendEmailButton');
        const loadingIndicator = document.getElementById('emailLoading');

        // Show loading
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
        loadingIndicator.classList.remove('hidden');

        fetch(emailForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
                emailModal.classList.add('hidden');
                emailForm.reset();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        })
        .finally(() => {
            // Hide loading
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Email';
            loadingIndicator.classList.add('hidden');
        });
    });

    // Update Status button functionality
    updateStatusBtn.addEventListener('click', function() {
        const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
        const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

        if (selectedCheckboxes.length === 0) {
            Swal.fire({
                title: 'No Selection!',
                text: 'Please select at least one scholar to update status.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Confirm bulk update
        Swal.fire({
            title: 'Confirm Update',
            text: `Are you sure you want to set inactive this account?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                const form = document.getElementById('scholarForm');
                const submitButton = updateStatusBtn;

                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';

                form.submit();
            }
        });
    });

    // Initialize button states
    updateButtons();
});

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
</body>

</html>