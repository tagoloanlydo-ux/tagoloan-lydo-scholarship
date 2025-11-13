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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <link rel="stylesheet" href="{{ asset('css/mayor_status.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/status-modal.css') }}" />
</head>
<body class="bg-gray-50">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner">
            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
        </div>
    </div>

    @php
        // normalize variables
        $applications = $applications ?? ($tableApplicants ?? []);
        $listApplications = $listApplications ?? [];
        $notifications = $notifications ?? collect();
        $showBadge = $showBadge ?? false;

        // Build a filtered collection for the table view
        if (is_object($applications) && method_exists($applications, 'items')) {
            $appItems = collect($applications->items());
        } else {
            $appItems = collect($applications);
        }

        $filteredApplications = $appItems->filter(function ($a) {
            $screening = data_get($a, 'screening') ?? data_get($a, 'initial_screening') ?? data_get($a, 'initialScreening');
            $status = data_get($a, 'status');
            $remarks = data_get($a, 'remarks');

            return $screening === 'Reviewed'
                && $status === 'Pending'
                && in_array($remarks, ['Poor', 'Ultra Poor']);
        })->values();
    @endphp

    <div class="dashboard-grid">
        <!-- Header -->
<header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg">
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
                                    @if($notif->type === 'application')
                                        <p class="text-blue-600 font-medium">
                                            📝 {{ $notif->name }} submitted a new application
                                        </p>
                                    @elseif($notif->type === 'remark')
                                        <p class="text-purple-600 font-medium">
                                            💬 New remark for {{ $notif->name }}:
                                            <b>{{ $notif->remarks }}</b>
                                        </p>
                                    @endif
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
            <div class="sidebar-fixed w-72 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
                        <li>
                            <a href="/mayor_staff/dashboard" class="w-full flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
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
                                    <a href="/mayor_staff/application" class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                        <i class="bx bx-search-alt mr-2"></i> Review Applications
                                    </a>
                                </li>
                                <li>
                                    <a href="/mayor_staff/status" class="flex items-center p-2 rounded-lg text-gray-700 bg-violet-600 text-white">
                                        <i class="bx bx-check-circle mr-2"></i> Scholarship Approval
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
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
                        <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                            @csrf
                                <button type="button" onclick="confirmLogout()" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                                    <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                                    <span class="hidden md:block text-red-600">Logout</span>
                                </button>
                        </form>
                    </div>
                </div>
            
            <!-- Main content (fixed, scrollable area) -->
            <div class="main-content-fixed text-[16px]">
                <div class="p-10 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-3xl font-bold text-gray-800">Applicant Status Management</h5>
                    </div>

                    <!-- ✅ Applicants -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex gap-2">
                            <button type="button" onclick="showTable()" class="tab active" id="tab-pending">
                                <i class="fas fa-table mr-1"></i> Pending Status
                            </button>
                            <button type="button" onclick="showList()" class="tab" id="tab-approved-rejected">
                                <i class="fas fa-list mr-1"></i> Approved/Rejected
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="printCurrentTable()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition duration-200">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                        </div>
                    </div>

                    <!-- Pending Status Tab -->
                    <div id="tableView" class="overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-violet-50 p-3 rounded-lg border border-violet-200">
                            📋 Pending Status: View applicants awaiting status assignment.
                            </h3>
                        </div>
                        
                        <!-- Filters -->
                        <div class="filter-container">
                            <div class="filter-group">
                                <label for="searchInputTable" class="filter-label">Search by Name</label>
                                <input type="text" id="searchInputTable" class="filter-input" placeholder="Type to search...">
                            </div>

                            <div class="filter-group">
                                <label for="barangaySelectTable" class="filter-label">Filter by Barangay</label>
                                <select id="barangaySelectTable" class="filter-select">
                                    <option value="">All Barangays</option>
                                    <!-- Barangay options will be populated dynamically -->
                                </select>
                            </div>
                        </div>
                        
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-violet-600 to-violet-800 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Remarks</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($filteredApplications ?? [] as $index => $app)
                                <tr class="hover:bg-gray-50 border-b" data-name="{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}" data-barangay="{{ $app->barangay }}" data-remarks="{{ $app->remarks }}">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        {{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->barangay }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        {{ $app->school ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->remarks == 'Ultra Poor') bg-red-100 text-red-800
                                            @elseif($app->remarks == 'Poor') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->remarks }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <button
                                                title="View Intake Sheet"
                                                class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow view-intake-btn"
                                                data-id="{{ $app->application_personnel_id }}"
                                                data-name="{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}">
                                                <i class="fas fa-eye mr-1"></i> Review Application
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">
                                        0 results
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination for Table View -->
                        <div class="pagination-container" id="tablePagination"></div>
                    </div>

                    <!-- Approved/Rejected Tab -->
                    <div id="listView" class="overflow-x-auto hidden">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-white p-3 rounded-lg border border-gray-200">
                            ✅ Approved/Rejected: View applicants with assigned status.
                            </h3>
                        </div>
                        
                        <!-- Filters -->
                        <div class="filter-container">
                            <div class="filter-group">
                                <label for="listNameSearch" class="filter-label">Search by Name</label>
                                <input type="text" id="listNameSearch" class="filter-input" placeholder="Type to search...">
                            </div>

                            <div class="filter-group">
                                <label for="listBarangayFilter" class="filter-label">Filter by Barangay</label>
                                <select id="listBarangayFilter" class="filter-select">
                                    <option value="">All Barangays</option>
                                    <!-- Barangay options will be populated dynamically -->
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="listStatusFilter" class="filter-label">Filter by Status</label>
                                <select id="listStatusFilter" class="filter-select">
                                    <option value="">All Status</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-green-800 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listApplications ?? [] as $index => $app)
                                <tr class="hover:bg-gray-50 border-b" data-name="{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}" data-barangay="{{ $app->barangay }}" data-status="{{ $app->status }}">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        {{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->barangay }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        {{ $app->school ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->status == 'Approved') bg-green-100 text-green-800
                                            @elseif($app->status == 'Rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 border border-gray-200 text-gray-500">
                                        0 results
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        <!-- Pagination for List View -->
                        <div class="pagination-container" id="listPagination"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Intake Sheet Modal -->
        <div id="intakeSheetModal" class="modal">
            <div class="modal-content">
                <div class="modal-header flex items-center justify-between bg-indigo-600 p-4 rounded-t-lg">
                    <!-- Left side: LYDO Logo -->
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/lydo.png') }}" alt="LYDO Logo" class="w-10 h-10 object-contain">
                        <h2 class="text-xl font-bold text-white">Family Intake Sheet</h2>
                    </div>

                    <!-- Right side: Close Button -->
                    <button type="button" class="modal-close text-white hover:text-gray-300" onclick="closeIntakeSheetModal()">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div id="reviewArea" class="review-columns" style="max-height: 80vh; overflow-y: auto;">
                    
                    <!-- Head of Family Section -->
                    <div class="intake-section">
                        <h3 class="intake-section-title">Head of Family</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div>
                                <p><strong>Name:</strong> <span id="modal-applicant-name">-</span></p>
                                <p><strong>Sex:</strong> <span id="modal-applicant-gender">-</span></p>
                                <p><strong>Remarks:</strong> <span id="modal-remarks">-</span></p>
                                <p><strong>Date of Birth:</strong> <span id="modal-head-dob">-</span></p>
                                <p><strong>Place of Birth:</strong> <span id="modal-head-pob">-</span></p>
                            </div>
                            <div>
                                <p><strong>Address:</strong> <span id="modal-head-address">-</span></p>
                                <p><strong>Zone:</strong> <span id="modal-head-zone">-</span></p>
                                <p><strong>Barangay:</strong> <span id="modal-head-barangay">-</span></p>
                                <p><strong>Religion:</strong> <span id="modal-head-religion">-</span></p>
                            </div>
                            <div>
                                <p><strong>Serial No.:</strong> <span id="modal-serial-number">-</span></p>
                                <p><strong>4Ps:</strong> <span id="modal-head-4ps">-</span></p>
                                <p><strong>IP No.:</strong> <span id="modal-head-ipno">-</span></p>
                                <p><strong>Education:</strong> <span id="modal-head-educ">-</span></p>
                                <p><strong>Occupation:</strong> <span id="modal-head-occ">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Household Information Section -->
                    <div class="intake-section">
                        <h3 class="intake-section-title">Household Information</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <p><strong>Total Family Income:</strong> <span id="modal-house-total-income">-</span></p>
                                <p><strong>Total Family Net Income:</strong> <span id="modal-house-net-income">-</span></p>
                                <p><strong>Other Source of Income:</strong> <span id="modal-other-income">-</span></p>
                            </div>
                            <div>
                                <p><strong>House (Owned/Rented):</strong> <span id="modal-house-house">-</span></p>
                                <p><strong>Lot (Owned/Rented):</strong> <span id="modal-house-lot">-</span></p>
                                <p><strong>Electricity Source:</strong> <span id="modal-house-electric">-</span></p>
                                <p><strong>Water:</strong> <span id="modal-house-water">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Family Members Section -->
                    <div class="intake-section">
                        <h3 class="intake-section-title">Family Members</h3>
                        <div class="overflow-x-auto">
                            <table class="intake-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Relation</th>
                                        <th>Birthdate</th>
                                        <th>Age</th>
                                        <th>Sex</th>
                                        <th>Civil Status</th>
                                        <th>Educational Attainment</th>
                                        <th>Occupation</th>
                                        <th>Income</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-family-members" class="text-center align-middle">
                                    <!-- Family members will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Service Records Section -->
                    <div class="intake-section">
                        <h3 class="intake-section-title">Social Service Records</h3>
                        <div class="overflow-x-auto">
                            <table class="intake-table">
                                <thead>
                                    <tr>
                                                    <th style="text-align: center; vertical-align: middle;">Date</th>
            <th style="text-align: center; vertical-align: middle;">Problem/Need</th>
            <th style="text-align: center; vertical-align: middle;">Action/Assistance Given</th>
            <th style="text-align: center; vertical-align: middle;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-service-records" class="text-center align-middle">
                                    <!-- Service records will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Health & Signatures Section -->
                    <div class="intake-section">
                        <h3 class="intake-section-title">Lydo Personnel Name</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <!-- Left column: Worker -->
                            <div class="text-center">
                                <div id="modal-worker-photo" class="mb-2">
                                  
                                </div>
                                <!-- signature image above name, centered -->
                                <div id="modal-worker-signature" class="mb-2 flex items-center justify-center">
                                    <!-- Signature will be populated by JavaScript -->
                                </div>
                                <p><strong id="modal-worker-fullname">-</strong></p>
                                <p class="mt-1 text-sm text-gray-600">Lydo Staff Name</p>
                            </div>

                            <!-- Right column: Officer -->
                            <div class="text-center">
                                <div id="modal-officer-photo" class="mb-2">
                            
                                </div>
                                <!-- signature image above name, centered -->
                                <div id="modal-officer-signature" class="mb-2 flex items-center justify-center">
                                    <!-- Signature will be populated by JavaScript -->
                                </div>
                                <p><strong id="modal-officer-fullname">-</strong></p>
                                <p class="mt-1 text-sm text-gray-600">Officer Name</p>
                            </div>
                        </div>

                        <!-- Centered Family Head Signature + Date -->
                        <div class="mt-6 text-center">
                            <div id="modal-client-signature-large" class="mt-2">
                            </div>
                            <p class="mt-4"><strong>Date Entry:</strong> <span id="modal-date-entry">-</span></p>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="intake-section" id="documents-section">
                        <h3 class="intake-section-title">Documents</h3>
                        <div id="modal-documents-container">
                            <!-- Documents will be populated here by JavaScript -->
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-4 mt-6 mb-6">
                        <button type="button" class="btn btn-danger" onclick="closeIntakeSheetModal()">
                            <i class="fas fa-times mr-2"></i> Close
                        </button>
                       <button type="button" class="btn btn-success" id="approveBtn">
                          <i class="fas fa-check mr-2"></i> Approve
                        </button>
                       <button type="button" class="btn btn-danger" id="rejectBtn">
                            <i class="fas fa-times mr-2"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="{{ asset('js/status-script.js') }}"></script>

</body>
</html>