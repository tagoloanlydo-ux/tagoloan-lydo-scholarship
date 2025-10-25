<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="/img/LYDO.png">
    <link rel="stylesheet" href="{{ asset('css/mayor_status.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>
<body class="bg-gray-50">
    @php
        // Provide safe defaults so the view doesn't error if controller omitted these
        $applications = $applications ?? [];
        $listApplications = $listApplications ?? [];
        $notifications = $notifications ?? collect();
        $showBadge = $showBadge ?? false;
    @endphp
    <div class="dashboard-grid">
        <!-- Header -->
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center">
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
                    @if($showBadge && $notifications->count() > 0)
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
        <!-- Main Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <div class="w-100 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
                        <li>
                        <a href="/mayor_staff/dashboard"  class="w-full flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white focus:outline-none">
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
                                        class="flex items-center p-2 rounded-lg text-gray-700 bg-violet-600 text-white">
                                    <i class="bx bx-check-circle mr-2"></i> Update Status
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <ul class="side-menu space-y-1">
                            <li>
                                <a href="/mayor_staff/settings" class="w-full flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
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

            <div class="flex-1 main-content-area p-4 md:p-5  text-[16px]">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                                    <div class="flex justify-between items-center mb-6">
                                    <h5 class="text-3xl font-bold text-gray-800">Applicant Status Management</h5>
                                </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span class="text-lg font-medium">Updating status...</span>
                        </div>
                    </div>

                    <!-- 🔎 View Switch -->
                    <div class="flex justify-start items-center mb-6 gap-4">
          <!-- Tab Switch -->
            <div class="flex gap-2">
                <div class="tab active" onclick="showTable()">Pending Review</div>
                <div class="tab" onclick="showList()">Reviewed Applications</div>
            </div>
        </div>

        <!-- ✅ Table View (Applicants without remarks) -->
        <div id="tableView" class="overflow-x-auto">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-200">
                <i class="fas fa-clock mr-2"></i>Pending Applications - Awaiting Review and Status Update
                </h3>
            </div>
            <!-- Search and Filter Section -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <!-- Search by Name -->
                    <div class="flex-1">
                        <label for="searchInputTable" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                        <input type="text" id="searchInputTable" placeholder="Enter applicant name..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter by Barangay -->
                    <div class="flex-1">
                        <label for="barangaySelectTable" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                        <select id="barangaySelectTable"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Barangays</option>
                            @php
                                $uniqueBarangaysTable = collect($applications)->pluck('barangay')->unique()->sort();
                            @endphp
                            @foreach($uniqueBarangaysTable as $barangay)
                                <option value="{{ $barangay }}">{{ $barangay }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="flex-shrink-0">
                        <button onclick="clearFiltersTable()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </button>
                    </div>
                </div>
            </div>
            <table class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
            <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white uppercase text-sm">
                <tr>
                    <th class="px-6 py-4 text-left">#</th>
                    <th class="px-6 py-4 text-left">Name</th>
                    <th class="px-6 py-4 text-left">Barangay</th>
                    <th class="px-6 py-4 text-left">School</th>
                    <th class="px-6 py-4 text-left">Remarks</th>
                    <th class="px-6 py-4 text-left">Status</th>
                </tr>
            </thead>
        <tbody class="bg-white">
        @forelse($applications as $index => $app)
            @if(in_array($app->remarks, ['Poor', 'Ultra Poor']))
            <tr class="border-b border-gray-200 hover:bg-blue-50 transition-colors duration-200">
                <td class="px-6 py-4">{{ $index + 1 }}</td>
                <td class="px-6 py-4 font-medium">
                    {{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}
                </td>
                <td class="px-6 py-4">{{ $app->barangay }}</td>
                <td class="px-6 py-4">{{ $app->school }}</td>
        <td class="px-6 py-4">
            @php
                $badgeColor = match($app->remarks) {
                    'Poor' => 'bg-red-100 text-red-800 border border-red-200',
                    'Non Poor' => 'bg-green-100 text-green-800 border border-green-200',
                    'Ultra Poor' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                    'Non Indigenous' => 'bg-gray-100 text-gray-800 border border-gray-200',
                    default => 'bg-blue-100 text-blue-800 border border-blue-200',
                };
            @endphp

            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                {{ $app->remarks }}
            </span>
        </td>

                <td class="px-6 py-4">
                    <form method="POST" action="{{ route('MayorStaff.updateStatus', $app->application_personnel_id) }}">
                        @csrf
                        <div class="flex flex-col space-y-2">
                            <select name="status" class="border border-gray-300 rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 status-select">
                            <option >Set Status</option>
                                <option value="Approved" {{ $app->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Rejected" {{ $app->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </form>
                </td>
            </tr>
            @endif
        @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500 bg-gray-50">No pending applications found.</td>
            </tr>
        @endforelse
        </tbody>
        </table>
    </div>
        <!-- ✅ List View (Approved and Rejected applications) -->
    <div id="listView" class="hidden overflow-x-auto">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-700 bg-green-50 p-3 rounded-lg border border-green-200">
            <i class="fas fa-check-circle mr-2"></i>Processed Applications - Approved and Rejected
            </h3>
        </div>
        <!-- Search and Filter Section -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <!-- Search by Name -->
        <table class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                <tr>
                    <th class="px-6 py-4 text-left">#</th>
                    <th class="px-6 py-4 text-left">Name</th>
                    <th class="px-6 py-4 text-left">Barangay</th>
                    <th class="px-6 py-4 text-left">School</th>
                    <th class="px-6 py-4 text-left">Remarks</th>
                    <th class="px-6 py-4 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white">
            @forelse($listApplications as $index => $app)
                <tr class="border-b border-gray-200 hover:bg-green-50 transition-colors duration-200">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-medium">{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}</td>
                    <td class="px-6 py-4">{{ $app->barangay }}</td>
                    <td class="px-6 py-4">{{ $app->school }}</td>
                    <td class="px-6 py-4">
                        @php
                            $badgeColor = match($app->remarks) {
                                'Poor' => 'bg-red-100 text-red-800 border border-red-200',
                                'Non Poor' => 'bg-green-100 text-green-800 border border-green-200',
                                'Ultra Poor' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                'Non Indigenous' => 'bg-gray-100 text-gray-800 border border-gray-200',
                                default => 'bg-blue-100 text-blue-800 border border-blue-200',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ $app->remarks }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusBadgeColor = match($app->status) {
                                'Approved' => 'bg-green-100 text-green-800 border border-green-200',
                                'Rejected' => 'bg-red-100 text-red-800 border border-red-200',
                                default => 'bg-gray-100 text-gray-800 border border-gray-200',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadgeColor }}">
                            {{ $app->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 bg-gray-50">No approved or rejected applications found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
</div>
</div>
</div>

<script>
    function showTable() {
        document.getElementById("tableView").classList.remove("hidden");
        document.getElementById("listView").classList.add("hidden");
        document.querySelector('.tab.active').classList.remove('active');
        document.querySelectorAll('.tab')[0].classList.add('active');
        localStorage.setItem("viewMode", "table"); // save preference
        // run filter after showing
        if (typeof filterTableView === 'function') filterTableView();
    }

    function showList() {
        document.getElementById("listView").classList.remove("hidden");
        document.getElementById("tableView").classList.add("hidden");
        document.querySelector('.tab.active').classList.remove('active');
        document.querySelectorAll('.tab')[1].classList.add('active');
        localStorage.setItem("viewMode", "list"); // save preference
        // run filter after showing
        if (typeof filterListView === 'function') filterListView();
    }

    // ✅ Kapag nag-load ang page, i-apply yung last view
    document.addEventListener("DOMContentLoaded", function() {
        let viewMode = localStorage.getItem("viewMode") || "table"; // default table
        if(viewMode === "list") {
            showList();
        } else {
            showTable();
        }

        // Add SweetAlert confirmation for dropdown status changes
        const statusSelects = document.querySelectorAll('select[name="status"]');
        statusSelects.forEach(select => {
            // Remove any existing event listeners to prevent duplicates
            select.removeEventListener('change', handleStatusChange);

            select.addEventListener('change', handleStatusChange);

            // Store original value
            select.setAttribute('data-original-value', select.value);
        });

        function handleStatusChange(e) {
            e.preventDefault();
            e.stopPropagation();

            const select = this;
            const form = select.closest('form');
            const selectedValue = select.value;
            const selectedText = select.options[select.selectedIndex].text;
            const originalValue = select.getAttribute('data-original-value');

            // Don't show confirmation if value hasn't actually changed
            if (selectedValue === originalValue) {
                return;
            }

            // If Rejected, show SweetAlert with input for reason
            if (selectedValue === 'Rejected') {
                Swal.fire({
                    title: 'Reject Application',
                    text: 'Please provide a reason for rejection:',
                    input: 'textarea',
                    inputPlaceholder: 'Enter the reason for rejection...',
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return 'Reason is required!';
                        }
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Reject Application',
                    cancelButtonText: 'Cancel',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        const reason = result.value.trim();
                        submitForm(form, selectedValue, reason);
                    } else {
                        // Reset to previous value
                        select.value = originalValue || 'Set Status';
                    }
                });
                return; // Exit early for rejected case
            }

            // For Approved status, show regular confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to update the status to "${selectedText}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm(form, selectedValue);
                } else {
                    // Reset to previous value
                    select.value = originalValue || 'Set Status';
                }
            });
        }

        function submitForm(form, statusValue, reason = null) {
            // Show loading spinner
            document.getElementById('loadingSpinner').classList.remove('hidden');

            // Collect form data
            const formData = new FormData(form);
            formData.set('status', statusValue);

            // Set reason if provided
            if (reason) {
                formData.set('reason', reason);
            }

            // Submit via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                document.getElementById('loadingSpinner').classList.add('hidden');

                if (data.success) {
                    // Show success SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Status updated successfully!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reload page to reflect changes
                        window.location.reload();
                    });
                } else {
                    // Show error if success is false
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to update status.',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                // Hide loading spinner
                document.getElementById('loadingSpinner').classList.add('hidden');

                // Show error SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while updating the status.',
                    confirmButtonText: 'OK'
                });
                console.error('Error:', error);
            });
        }

        // Add SweetAlert confirmation for modal form submission
        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const statusSelect = document.getElementById('modalStatus');
                const selectedValue = statusSelect.value;
                const selectedText = statusSelect.options[statusSelect.selectedIndex].text;

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to update the status to "${selectedText}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        }

        // Debounce helper
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

        // Add event listeners for table view filters (debounced on input)
        const searchInputTable = document.getElementById('searchInputTable');
        const barangaySelectTable = document.getElementById('barangaySelectTable');

        if (searchInputTable) {
            searchInputTable.addEventListener('input', debounce(filterTableView, 150));
        }
        if (barangaySelectTable) {
            barangaySelectTable.addEventListener('change', filterTableView);
        }

        // Add event listeners for list view filters (debounced on input)
        const searchInputList = document.getElementById('searchInputList');
        const barangaySelectList = document.getElementById('barangaySelectList');

        if (searchInputList) {
            searchInputList.addEventListener('input', debounce(filterListView, 150));
        }
        if (barangaySelectList) {
            barangaySelectList.addEventListener('change', filterListView);
        }

        // Run initial filters to ensure rows reflect any pre-filled values
        if (typeof filterTableView === 'function') filterTableView();
        if (typeof filterListView === 'function') filterListView();
    });

</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session("success") }}',
        confirmButtonText: 'OK'
    });
</script>
@endif

<!-- ⚡ JS -->
<script>
   document.getElementById("notifBell").addEventListener("click", function () {
         let dropdown = document.getElementById("notifDropdown");
        dropdown.classList.toggle("hidden");
        // remove badge when opened
        let notifCount = document.getElementById("notifCount");
         if (notifCount) {
        notifCount.remove();
        // Mark notifications as viewed on the server
        fetch('/mayor_staff/mark-notifications-viewed', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Notifications marked as viewed');
            }
        }).catch(error => {
            console.error('Error marking notifications as viewed:', error);
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
    // Function to clear filters for table view
    function clearFiltersTable() {
        document.getElementById('searchInputTable').value = '';
        document.getElementById('barangaySelectTable').value = '';
        filterTableView();
    }

    // Function to filter the table view table
    function filterTableView() {
        const searchValue = document.getElementById('searchInputTable').value.toLowerCase().trim();
        const barangayValue = document.getElementById('barangaySelectTable').value.toLowerCase().trim();
        const tableBody = document.querySelector('#tableView tbody');
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            const barangayCell = row.querySelector('td:nth-child(3)');

            if (nameCell && barangayCell) {
                const name = nameCell.textContent.toLowerCase().trim();
                const barangay = barangayCell.textContent.toLowerCase().trim();

                // Split search value into terms and check if all are present in the name
                const searchTerms = searchValue.split(' ').filter(term => term.length > 0);
                const nameMatch = searchTerms.length === 0 || searchTerms.every(term => name.includes(term));
                const barangayMatch = barangayValue === '' || barangay === barangayValue;

                if (nameMatch && barangayMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    // Function to clear filters for list view
    function clearFiltersList() {
        document.getElementById('searchInputList').value = '';
        document.getElementById('barangaySelectList').value = '';
        filterListView();
    }

    // Function to filter the list view table
    function filterListView() {
        const searchValue = document.getElementById('searchInputList').value.toLowerCase().trim();
        const barangayValue = document.getElementById('barangaySelectList').value;
        const tableBody = document.querySelector('#listView tbody');
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            const barangayCell = row.querySelector('td:nth-child(3)');

            if (nameCell && barangayCell) {
                const name = nameCell.textContent.toLowerCase().trim();
                const barangay = barangayCell.textContent.trim();

                // Split search value into terms and check if all are present in the name
                const searchTerms = searchValue.split(' ').filter(term => term.length > 0);
                const nameMatch = searchTerms.length === 0 || searchTerms.every(term => name.includes(term));
                const barangayMatch = barangayValue === '' || barangay === barangayValue;

                if (nameMatch && barangayMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }


</script>

<script>
// Real-time updates for new applications and status changes
let lastUpdateApps = new Date().toISOString();
let lastUpdateStatus = new Date().toISOString();

function pollForNewApplications() {
    fetch(`/mayor_staff/application/updates?last_update=${encodeURIComponent(lastUpdateApps)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Filter for applications with remarks 'Poor' or 'Ultra Poor'
                const newPoorApps = data.filter(app => app.remarks === 'Poor' || app.remarks === 'Ultra Poor');
                if (newPoorApps.length > 0) {
                    // Update lastUpdateApps to the latest created_at
                    const latest = newPoorApps.reduce((max, app) => app.created_at > max ? app.created_at : max, lastUpdateApps);
                    lastUpdateApps = latest;

                    // Append new rows to tableView
                    const tableBody = document.querySelector('#tableView tbody');
                    if (tableBody) {
                        newPoorApps.forEach(app => {
                            const row = document.createElement('tr');
                            row.className = 'border-b border-gray-200 hover:bg-blue-50 transition-colors duration-200';
                            const badgeColor = app.remarks === 'Poor' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                            row.innerHTML = `
                                <td class="px-6 py-4">${tableBody.rows.length + 1}</td>
                                <td class="px-6 py-4 font-medium">${app.fname} ${app.mname} ${app.lname} ${app.suffix}</td>
                                <td class="px-6 py-4">${app.barangay}</td>
                                <td class="px-6 py-4">${app.school}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${badgeColor}">
                                        ${app.remarks}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="/mayor_staff/status/${app.application_personnel_id}">
                                        @csrf
                                        <div class="flex flex-col space-y-2">
                                            <select name="status" class="border border-gray-300 rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 status-select">
                                                <option>Set Status</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Rejected">Rejected</option>
                                            </select>
                                        </div>
                                    </form>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });
                        // Apply current filters to newly appended rows
                        if (typeof filterTableView === 'function') filterTableView();
                    }
                }
            }
        })
        .catch(err => console.error('Polling new apps error:', err));
}

function pollForStatusUpdates() {
    fetch(`/mayor_staff/status/updates?last_update=${encodeURIComponent(lastUpdateStatus)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Update lastUpdateStatus to the latest updated_at
                const latest = data.reduce((max, app) => app.updated_at > max ? app.updated_at : max, lastUpdateStatus);
                lastUpdateStatus = latest;

                data.forEach(app => {
                    // Remove from tableView if present
                    const tableBody = document.querySelector('#tableView tbody');
                    if (tableBody) {
                        const rows = tableBody.querySelectorAll('tr');
                        rows.forEach(row => {
                            const nameCell = row.querySelector('td:nth-child(2)');
                            if (nameCell && nameCell.textContent.trim() === `${app.fname} ${app.mname} ${app.lname} ${app.suffix}`.trim()) {
                                row.remove();
                            }
                        });
                    }

                    // Append to listView if not already there
                    const listBody = document.querySelector('#listView tbody');
                    if (listBody) {
                        // Check if already exists
                        const existingRows = listBody.querySelectorAll('tr');
                        let exists = false;
                        existingRows.forEach(row => {
                            const nameCell = row.querySelector('td:nth-child(2)');
                            if (nameCell && nameCell.textContent.trim() === `${app.fname} ${app.mname} ${app.lname} ${app.suffix}`.trim()) {
                                exists = true;
                            }
                        });
                        if (!exists) {
                            const row = document.createElement('tr');
                            row.className = 'border-b border-gray-200 hover:bg-green-50 transition-colors duration-200';
                            const badgeColor = app.remarks === 'Poor' ? 'bg-red-100 text-red-800 border border-red-200' : app.remarks === 'Ultra Poor' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-blue-100 text-blue-800 border border-blue-200';
                            const statusBadgeColor = app.status === 'Approved' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200';
                            row.innerHTML = `
                                <td class="px-6 py-4">${listBody.rows.length + 1}</td>
                                <td class="px-6 py-4 font-medium">${app.fname} ${app.mname} ${app.lname} ${app.suffix}</td>
                                <td class="px-6 py-4">${app.barangay}</td>
                                <td class="px-6 py-4">${app.school}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${badgeColor}">
                                        ${app.remarks}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusBadgeColor}">
                                        ${app.status}
                                    </span>
                                </td>
                            `;
                            listBody.appendChild(row);
                        }
                        // Apply current filters to newly appended rows in the processed list
                        if (typeof filterListView === 'function') filterListView();
                    }
                });
            }
        })
        .catch(err => console.error('Polling status updates error:', err));
}

// Poll every 10 seconds
setInterval(pollForNewApplications, 10000);
setInterval(pollForStatusUpdates, 10000);
</script>

</body>
</html>
