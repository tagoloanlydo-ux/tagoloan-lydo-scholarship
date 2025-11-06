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
    <style>
        input::placeholder, select::placeholder {
            color: black !important;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans flex-shrink-0">
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

<li class="text-blue-600 bg-blue-50">
    <a href="/lydo_admin/applicants" 
     class=" flex items-center justify-between p-3 rounded-lg text-white-700 bg-violet-600 text-white">
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
       class=" flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
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
            <div class="flex-1 overflow-y-auto p-4 md:p-5 text-[16px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-black-800">List of Applicants</h2>
                </div>
<!-- Filter Section -->
<div class="bg-white p-4 rounded-lg shadow-sm mb-6">
    <div class="flex flex-col md:flex-row gap-4" id="filterForm">
        <div class="flex-1">
            <input type="text" id="searchInput" placeholder="Search by name..." 
                   class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
        </div>
        <div class="flex-1">
            <select id="barangaySelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                <option value="">All Barangays</option>
                @foreach($barangays as $barangay)
                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                        {{ $barangay }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <select id="academicYearSelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                <option value="">All Academic Years</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <!-- Add Initial Screening Filter -->
        <div class="flex-1">
            <select id="initialScreeningSelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                <option value="all" {{ $initialScreeningStatus == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="Approved" {{ $initialScreeningStatus == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Rejected" {{ $initialScreeningStatus == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <!-- Print PDF Button -->
        <div class="flex-1">
            <button id="printPdfBtn" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                <i class="fas fa-file-pdf mr-2"></i> Print PDF
            </button>
        </div>
    </div>
</div>

                <!-- Applicants Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Applicants List</h3>
                        <div class="flex space-x-2">
                            <button id="copyNamesBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                Copy Names
                            </button>
                            <button id="emailSelectedBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                Email
                            </button>
                        </div>
                    </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Email</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Academic Year</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Initial Screening</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Application Requirement</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @forelse($applicants as $applicant)
                                        <tr class="hover:bg-gray-50 border-b">
                                            <td class="px-4 border border-gray-200 py-2 text-center">
                                                <input type="checkbox" name="selected_applicants" value="{{ $applicant->applicant_id }}" class="applicant-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            </td>
                                            <td class="px-4 border border-gray-200 py-2 text-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $applicant->applicant_lname }}{{ $applicant->applicant_suffix ? ' ' . $applicant->applicant_suffix : '' }}, 
                                                    {{ $applicant->applicant_fname }} 
                                                    {{ $applicant->applicant_mname ? $applicant->applicant_mname . ' ' : '' }}
                                                </div>
                                            </td>
                                            <td class="px-4 border border-gray-200 py-2 text-center">
                                                <div class="text-sm text-gray-900">{{ $applicant->applicant_brgy }}</div>
                                            </td>
                                            <td class="px-4 border border-gray-200 py-2 text-center">
                                                <div class="text-sm text-gray-900">{{ $applicant->applicant_email }}</div>
                                            </td>
                                            <td class="px-4 border border-gray-200 py-2 text-center">
                                                <div class="text-sm text-gray-900">{{ $applicant->applicant_school_name }}</div>
                                            </td>
                                            <td class="px-4 border border-gray-200 py-2 text-center">
                                                <div class="text-sm text-gray-900">{{ $applicant->applicant_acad_year }}</div>
                                            </td>
                                            <td class="px-4 border border-gray-200 py-2 text-center">
                                                <div class="text-sm font-medium {{ $applicant->initial_screening == 'Approved' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $applicant->initial_screening }}
                                                </div>
                                            </td>
                                            <td class="px-4 border border-gray-200 py-2 text-center">
                                                <button type="button" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors"
                                                        data-bs-toggle="modal" data-bs-target="#requirementsModal{{ $applicant->applicant_id }}">
                                                    View Requirements
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-2 text-center text-sm text-gray-500">
                                                No applicants found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                        </table>
                    </div>

                    <!-- Requirements Modals -->
                    @forelse($applicants as $applicant)
                    <div class="modal fade" id="requirementsModal{{ $applicant->applicant_id }}" tabindex="-1" aria-labelledby="requirementsModalLabel{{ $applicant->applicant_id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="requirementsModalLabel{{ $applicant->applicant_id }}">Application Requirements for {{ $applicant->applicant_lname }}, {{ $applicant->applicant_fname }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">1. Valid Identification Card (e.g., Passport, Driver's License)</li>
                                        <li class="list-group-item">2. Proof of Residency (Barangay Certificate)</li>
                                        <li class="list-group-item">3. Academic Records (Transcript of Records or Certificate of Enrollment)</li>
                                        <li class="list-group-item">4. Income Certificate or Proof of Financial Need</li>
                                        <li class="list-group-item">5. Recent 2x2 Photograph</li>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforelse

                 
                    <!-- Pagination -->
<!-- Pagination -->
<div class="px-6 py-4 bg-white border-t border-gray-200">
    <div class="pagination-container" id="paginationContainer"></div>
</div>
                </div>


            </div>

            <!-- Email Modal -->
            <div id="emailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Send Email to Selected Applicants</h3>
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

            <script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize pagination and filtering
    initializeApplicantData();
    initializeApplicantPagination();
    initializeApplicantFiltering();

    // Rest of your existing JavaScript code for checkboxes, buttons, etc.
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.applicant-checkbox');
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
                        const selectedCount = document.querySelectorAll('.applicant-checkbox:checked').length;
                        const hasSelection = selectedCount > 0;

                        copyNamesBtn.disabled = !hasSelection;
                        emailSelectedBtn.disabled = !hasSelection;
                        copyNamesBtn.classList.toggle('hidden', !hasSelection);
                        emailSelectedBtn.classList.toggle('hidden', !hasSelection);
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
                        const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');

                        if (selectedCheckboxes.length === 0) {
                            recipientsPreview.innerHTML = 'No recipients selected';
                            return;
                        }

                        const recipients = Array.from(selectedCheckboxes).map(checkbox => {
                            const row = checkbox.closest('tr');
                            const name = row.querySelector('td:nth-child(2)').textContent.trim();
                            const email = row.querySelector('td:nth-child(4)').textContent.trim();
                            return `${name} (${email})`;
                        });

                        recipientsPreview.innerHTML = recipients.join('<br>');
                    }

                    // Copy Names button functionality
  copyNamesBtn.addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        Swal.fire({
            title: 'No Selection!',
            text: 'Please select at least one applicant to copy names.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Group selected applicants by barangay
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
            text: 'Selected applicant names grouped by barangay copied to clipboard!',
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

                    // Email Selected button functionality
                    emailSelectedBtn.addEventListener('click', function() {
                        const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');

                        if (selectedCheckboxes.length === 0) {
                            Swal.fire({
                                title: 'No Selection!',
                                text: 'Please select at least one applicant to send email.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        updateRecipientsPreview();
                        emailModal.classList.remove('hidden');
                        emailSubject.focus();
                    });

                    // Close email modal
                    function closeEmailModalHandler() {
                        emailModal.classList.add('hidden');
                        emailForm.reset();
                        sendEmailText.classList.remove('hidden');
                        sendEmailLoading.classList.add('hidden');
                        sendEmailBtn.disabled = false;
                    }

                    closeEmailModal.addEventListener('click', closeEmailModalHandler);
                    cancelEmailBtn.addEventListener('click', closeEmailModalHandler);

                    // Close modal when clicking outside
                    emailModal.addEventListener('click', function(e) {
                        if (e.target === emailModal) {
                            closeEmailModalHandler();
                        }
                    });

                    // Send email form submission
                    emailForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');
                        const subject = emailSubject.value.trim();
                        const message = emailMessage.value.trim();

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
                                text: 'No applicants selected to send email to.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        // Collect recipient data
                        const recipients = Array.from(selectedCheckboxes).map(checkbox => {
                            const row = checkbox.closest('tr');
                            return {
                                id: checkbox.value,
                                name: row.querySelector('td:nth-child(2)').textContent.trim(),
                                email: row.querySelector('td:nth-child(4)').textContent.trim()
                            };
                        });

                        // Show loading state
                        sendEmailText.classList.add('hidden');
                        sendEmailLoading.classList.remove('hidden');
                        sendEmailBtn.disabled = true;

                        // Send email via AJAX
                        fetch('/lydo_admin/send-email-to-applicants', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                recipients: recipients,
                                subject: subject,
                                message: message
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: `Email sent successfully to ${recipients.length} applicant(s)!`,
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
                            sendEmailText.classList.remove('hidden');
                            sendEmailLoading.classList.add('hidden');
                            sendEmailBtn.disabled = false;
                        });
                    });

                    // Auto-submit form when filters change
                    const searchInput = document.getElementById('searchInput');
                    const barangaySelect = document.getElementById('barangaySelect');
                    const academicYearSelect = document.getElementById('academicYearSelect');
                    const filterForm = document.getElementById('filterForm');

                    // Function to submit form with debounce for search input
                    let searchTimeout;
                    function submitForm() {
                        filterForm.submit();
                    }

                    function debounceSubmit() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(submitForm, 500); // 500ms delay
                    }

                    // Event listeners for filter changes - filters apply automatically
                    searchInput.addEventListener('input', debounceSubmit);
                    barangaySelect.addEventListener('change', submitForm);
                    academicYearSelect.addEventListener('change', submitForm);

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
<script>
// Global pagination state
const paginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Function to get full name for sorting
function getFullNameForSorting(row) {
    const nameCell = row.cells[1];
    if (!nameCell) return '';
    return nameCell.textContent.trim().toLowerCase();
}

// Function to sort rows alphabetically by last name
function sortRowsAlphabetically(rows) {
    return rows.sort((a, b) => {
        const nameA = getFullNameForSorting(a);
        const nameB = getFullNameForSorting(b);
        return nameA.localeCompare(nameB);
    });
}

// Initialize data from the table
function initializeApplicantData() {
    const tableRows = Array.from(document.querySelectorAll('table tbody tr'));
    paginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by last name
    paginationState.allRows = sortRowsAlphabetically(paginationState.allRows);
    paginationState.filteredRows = [...paginationState.allRows];
}

// Initialize pagination
function initializeApplicantPagination() {
    updateApplicantPagination();
}

// Update pagination display
function updateApplicantPagination() {
    const state = paginationState;
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
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> applicants
            </div>
            
            <div class="flex items-center space-x-1">
                <!-- First Page -->
                <button onclick="changeApplicantPage(1)" 
                    class="px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 ${
                        state.currentPage === 1 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>
                
                <!-- Previous Page -->
                <button onclick="changeApplicantPage(${state.currentPage - 1})" 
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
                           onchange="goToApplicantPage(this.value)">
                    of ${totalPages}
                </div>
                
                <!-- Next Page -->
                <button onclick="changeApplicantPage(${state.currentPage + 1})" 
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === totalPages 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>
                
                <!-- Last Page -->
                <button onclick="changeApplicantPage(${totalPages})" 
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
function changeApplicantPage(page) {
    const state = paginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateApplicantPagination();
}

// Go to specific page
function goToApplicantPage(page) {
    const state = paginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateApplicantPagination();
}

// Initialize filtering functionality
function initializeApplicantFiltering() {
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');
    const academicYearSelect = document.getElementById('academicYearSelect');

    function filterApplicantTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedBarangay = barangaySelect.value;
        const selectedAcademicYear = academicYearSelect.value;

        const filteredRows = paginationState.allRows.filter(row => {
            const nameCell = row.cells[1];
            const barangayCell = row.cells[2];
            const academicYearCell = row.cells[5];

            if (!nameCell || !barangayCell || !academicYearCell) return false;

            const name = nameCell.textContent.toLowerCase();
            const barangay = barangayCell.textContent.trim();
            const academicYear = academicYearCell.textContent.trim();

            const nameMatch = name.includes(searchTerm);
            const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
            const academicYearMatch = !selectedAcademicYear || academicYear === selectedAcademicYear;

            return nameMatch && barangayMatch && academicYearMatch;
        });

        // Sort filtered results alphabetically
        const sortedFilteredRows = sortRowsAlphabetically(filteredRows);

        // Update filtered rows and reset to page 1
        paginationState.filteredRows = sortedFilteredRows;
        paginationState.currentPage = 1;
        updateApplicantPagination();
        
        // Reset select all checkbox
        document.getElementById('selectAll').checked = false;
        document.getElementById('selectAll').indeterminate = false;
    }

    // Add event listeners with debouncing
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterApplicantTable, 300));
    }
    if (barangaySelect) {
        barangaySelect.addEventListener('change', filterApplicantTable);
    }
    if (academicYearSelect) {
        academicYearSelect.addEventListener('change', filterApplicantTable);
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

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApplicantData();
    initializeApplicantPagination();
    initializeApplicantFiltering();
});

</script>
</body>

</html>
