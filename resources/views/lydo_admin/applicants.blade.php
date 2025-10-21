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
<li class="text-blue-600 bg-blue-50">
    <a href="/lydo_admin/applicants" 
     class=" flex items-center justify-between p-3 rounded-lg text-white-700 bg-violet-600 text-white">
         <div class="flex items-center">
            <i class="bx bxs-user text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Applicants</span>
        </div>
    </a>
</li>
<li>
    <a href="/lydo_admin/announcement"
       class=" flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
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
            <div class="flex-1 overflow-y-auto p-4 md:p-5 text-[16px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-black-800">List of Applicants</h2>
                </div>

                <!-- Filter Section -->
                <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                    <form method="GET" action="{{ route('LydoAdmin.applicants') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="filterForm">
                        <!-- Search Input -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Enter name..."
                                   class="w-full p-2 border border-black rounded-md focus:ring-blue-500 focus:border-blue-500 placeholder:text-black"
                                   id="searchInput">
                        </div>

                        <!-- Barangay Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                            <select name="barangay" class="w-full p-2 border border-black rounded-md focus:ring-blue-500 focus:border-blue-500" id="barangaySelect">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                        {{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Year Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Academic Year</label>
                            <select name="academic_year" class="w-full p-2 border border-black rounded-md focus:ring-blue-500 focus:border-blue-500" id="academicYearSelect">
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
                                 </tr>
                            </thead>
                            <tbody>
                                @forelse($applicants as $applicant)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <input type="checkbox" name="selected_applicants" value="{{ $applicant->applicant_id }}" class="applicant-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm font-medium text-gray-900 whitespace-nowrap">
                                                {{ $applicant->applicant_fname }} {{ $applicant->applicant_mname ? $applicant->applicant_mname . ' ' : '' }}{{ $applicant->applicant_lname }}{{ $applicant->applicant_suffix ? ' ' . $applicant->applicant_suffix : '' }}
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-2 text-center text-sm text-gray-500">
                                            No applicants found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                 
                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        {{ $applicants->links() }}
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

</body>

</html>
