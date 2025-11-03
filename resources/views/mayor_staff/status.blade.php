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

    <style>
        /* Fixed header + sidebar + content layout (match application.blade.php) */
        body { height: 100vh; overflow: hidden; }
        header { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; }
        .sidebar-fixed { position: fixed; top: 80px; left: 0; bottom: 0; width: 64px; overflow-y: auto; z-index: 999; background: white; }
        @media (min-width: 768px) { .sidebar-fixed { width: 256px; } }
        .main-content-fixed { position: fixed; top: 80px; left: 64px; right: 0; bottom: 0; overflow-y: auto; padding: 1rem 1.25rem; }
        @media (min-width: 768px) { .main-content-fixed { left: 256px; } }
        /* modal + swal z-index fixes */
        .modal-overlay { z-index: 1100; }
        .swal2-container { z-index: 1200 !important; }

        /* Loading Spinner Styles */
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
            opacity: 1;
            animation: fadeIn 1s ease forwards;
        }

        .loading-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }

        .spinner {
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: 0 auto 2rem;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            border-radius: 50%;
             background: rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner img {
            width: 80%;
            height: 100%;
            border-radius: 50%;
        }

        .text-line {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 1s ease forwards 0.5s both;
            color: white;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
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

        /* Responsive design */
        @media (max-width: 768px) {
            .text-line {
                font-size: 1.8rem;
            }
            .spinner {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 480px) {
            .text-line {
                font-size: 1.5rem;
            }
            .spinner {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    @php
        // normalize variables
        // controller provides the paginator as $tableApplicants — prefer that if $applications is not set
        $applications = $applications ?? ($tableApplicants ?? []);
        $listApplications = $listApplications ?? [];
        $notifications = $notifications ?? collect();
        $showBadge = $showBadge ?? false;

        // Build a filtered collection for the table view to ensure only
        // records with initial_screening = 'Reviewed', status = 'Pending'
        // and remarks in ['Poor', 'Ultra Poor'] are shown.
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
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center">
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
            <!-- Sidebar (fixed) -->
<div class="sidebar-fixed w-72 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4  space-y-1">
                    <ul class="side-menu top space-y-4">
                        <li>
                            <a href="/mayor_staff/dashboard" class="w-full flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white focus:outline-none">
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
                                    <i class="bx bx-check-circle mr-2"></i> Scholarship Approval
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
                    </ul>
                </nav>
                
                <div class="p-2 md:p-4 border-t">
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm"> 
                        @csrf 
                        <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
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
                            <div onclick="showTable()" class="tab active" id="tab-pending">
                                <i class="fas fa-table mr-1"></i> Pending Status
                            </div>
                            <div onclick="showList()" class="tab" id="tab-approved-rejected">
                                <i class="fas fa-list mr-1"></i> Approved/Rejected
                            </div>
                        </div>
                    </div>

                    <!-- Pending Status Tab -->
                    <div id="tableView" class="overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-violet-50 p-3 rounded-lg border border-violet-200">
                            📋 Pending Status: View applicants awaiting status assignment.
                            </h3>
                        </div>
                        <div class="flex gap-4 mb-6">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="searchInputTable" placeholder="Search by name..." class="search-input-enhanced pl-10 pr-4 py-3 w-80 border-2 border-gray-200 rounded-lg focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all duration-200">
                            </div>
                            <div class="relative">
                                <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <select id="barangaySelectTable" class="filter-select-enhanced pl-10 pr-4 py-3 w-64 border-2 border-gray-200 rounded-lg focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all duration-200 bg-white">
                                    <option value="">All Barangays</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                                    @endforeach
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
                                <tr class="hover:bg-gray-50 border-b">
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
                                                <i class="fas fa-eye mr-1"></i> View
                                            </button>
                                            <!-- Approve/Reject buttons removed from table row.
                                                 Approval / Rejection will be handled inside the modal only. -->
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
                        <div class="mt-4">
                            <div id="tablePagination" class="flex justify-center mt-4"></div>
                        </div>
                    </div>

                    <!-- Approved/Rejected Tab -->
                    <div id="listView" class="overflow-x-auto hidden">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-white p-3 rounded-lg border border-gray-200">
                            ✅ Approved/Rejected: View applicants with assigned status.
                            </h3>
                        </div>
                        <div class="flex gap-4 mb-6">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="listNameSearch" placeholder="Search by name..." class="search-input-enhanced pl-10 pr-4 py-3 w-80 border-2 border-gray-200 rounded-lg focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all duration-200">
                            </div>
                            <div class="relative">
                                <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <select id="listBarangayFilter" class="filter-select-enhanced pl-10 pr-4 py-3 w-64 border-2 border-gray-200 rounded-lg focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all duration-200 bg-white">
                                    <option value="">All Barangays</option>
                                    @foreach($barangays ?? [] as $brgy)
                                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative">
                                <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <select id="listStatusFilter" class="filter-select-enhanced pl-10 pr-4 py-3 w-64 border-2 border-gray-200 rounded-lg focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition-all duration-200 bg-white">
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
                                <tr class="hover:bg-gray-50 border-b">
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
                        <div class="mt-4">
        <!-- Add this line for list view pagination -->
        <div id="listPagination" class="flex justify-center mt-4"></div>
    </div>
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


                
                <div id="reviewArea" class="review-columns">
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
                                        <th>Date</th>
                                        <th>Problem/Need</th>
                                        <th>Action/Assistance Given</th>
                                        <th>Remarks</th>
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
                        <h3 class="intake-section-title">Lydo Personeel Name</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                            <!-- Left column: Worker -->
                            <div class="text-center">
                                <div id="modal-worker-photo" class="mb-2">
                                    <img id="modal-worker-photo-img" src="" alt="worker photo"
                                         style="max-width:180px;height:120px;object-fit:cover;border:1px solid #e5e7eb;display:none;"
                                         onerror="this.style.display='none'">
                                </div>
                                <!-- signature image above name, centered -->
                                <div id="modal-worker-signature" class="mb-2 flex items-center justify-center">

                                </div>
                                <p><strong id="modal-worker-fullname">-</strong></p>
                                <p class="mt-1 text-sm text-gray-600">Lydo Staff Name</p>
                            </div>

                            <!-- Right column: Officer -->
                            <div class="text-center">
                                <div id="modal-officer-photo" class="mb-2">
                                    <img id="modal-officer-photo-img" src="" alt="officer photo"
                                         style="max-width:180px;height:120px;object-fit:cover;border:1px solid #e5e7eb;display:none;"
                                         onerror="this.style.display='none'">
                                </div>
                                <!-- signature image above name, centered -->
                                <div id="modal-officer-signature" class="mb-2 flex items-center justify-center">
                                    
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
                    <div class="intake-section">
                        <h3 class="intake-section-title">Documents</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="modal-documents">
                            <!-- Documents will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="modal-actions">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600" onclick="closeIntakeSheetModal()">
                        Close
                    </button>
                    <button type="button" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600" onclick="confirmApprove()">
                        <i class="fas fa-check mr-2"></i> Approve
                    </button>
                    <button type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600" onclick="confirmReject()">
                        <i class="fas fa-times mr-2"></i> Reject
                    </button>
                    <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600" onclick="printIntakeSheet()">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Spinner Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-container">
                <div class="spinner">
                    <img src="{{ asset('images/LYDO.png') }}" alt="Loading Logo">
                </div>
                <div class="text-line">Loading...</div>
            </div>
        </div>

        <script>
            // Hide loading spinner when page loads
            window.addEventListener('load', function() {
                const loadingOverlay = document.getElementById('loadingOverlay');
                if (loadingOverlay) {
                    loadingOverlay.classList.add('fade-out');
                    setTimeout(() => {
                        loadingOverlay.style.display = 'none';
                    }, 1000); // Match fade-out animation duration
                }
            });

            let currentApplicationId = null;
            let currentApplicationDocuments = null;
            // convenience: server-side view template name to ask backend to send to applicant
            const NOTIFY_TEMPLATE_REGISTRATION = 'scholar-registration-link';

            // --- Pagination state & helpers (client-side pagination, 15 per page) ---
            const paginationState = {
                table: { perPage: 15, currentPage: 1, totalPages: 1, rows: [] },
                list:  { perPage: 15, currentPage: 1, totalPages: 1, rows: [] }
            };

            function isDataRow(row) {
                const td = row.querySelector('td');
                if (!td) return false;
                return !td.hasAttribute('colspan');
            }

            function updatePagination(view) {
                const state = paginationState[view];
                const selector = view === 'table' ? '#tableView tbody tr' : '#listView tbody tr';
                const allRows = Array.from(document.querySelectorAll(selector)).filter(isDataRow);

                // rows that passed the filter (visible)
                const visibleRows = allRows.filter(r => r.style.display !== 'none');
                state.rows = visibleRows;
                state.totalPages = Math.max(1, Math.ceil(state.rows.length / state.perPage));
                if (state.currentPage > state.totalPages) state.currentPage = state.totalPages;

                // hide all, then show slice for current page
                allRows.forEach(r => r.style.display = 'none');
                const start = (state.currentPage - 1) * state.perPage;
                const end = start + state.perPage;
                state.rows.slice(start, end).forEach(r => r.style.display = '');

                renderPaginationControls(view);
            }

            function renderPaginationControls(view) {
                const state = paginationState[view];
                const container = document.getElementById(view === 'table' ? 'tablePagination' : 'listPagination');
                if (!container) return;

                const createBtn = (text, disabled = false, cls = '') => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `mx-1 px-3 py-1 rounded border ${cls} ${disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'}`;
                    btn.textContent = text;
                    if (disabled) btn.disabled = true;
                    return btn;
                };

                container.innerHTML = '';
                // Previous
                const prev = createBtn('Prev', state.currentPage === 1);
                prev.addEventListener('click', () => goToPage(view, state.currentPage - 1));
                container.appendChild(prev);

                // page numbers (compact: show up to 7 pages with current in middle)
                const maxButtons = 7;
                let startPage = Math.max(1, state.currentPage - Math.floor(maxButtons / 2));
                let endPage = Math.min(state.totalPages, startPage + maxButtons - 1);
                if (endPage - startPage + 1 < maxButtons) {
                    startPage = Math.max(1, endPage - maxButtons + 1);
                }

                for (let p = startPage; p <= endPage; p++) {
                    const cls = p === state.currentPage ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-gray-700';
                    const btn = createBtn(p, false, cls);
                    btn.addEventListener('click', () => goToPage(view, p));
                    container.appendChild(btn);
                }

                // Next
                const next = createBtn('Next', state.currentPage === state.totalPages);
                next.addEventListener('click', () => goToPage(view, state.currentPage + 1));
                container.appendChild(next);
            }

            function goToPage(view, page) {
                const state = paginationState[view];
                if (!page || page < 1) page = 1;
                if (page > state.totalPages) page = state.totalPages;
                state.currentPage = page;
                updatePagination(view);
            }
            // --- end pagination helpers ---

            // confirm + send Approved status (will tell server to send the scholar registration link via email & SMS)
            function confirmApprove() {
                if (!currentApplicationId) {
                    Swal.fire({ icon: 'warning', title: 'No application', text: 'No application selected.' });
                    return;
                }
                Swal.fire({
                    title: 'Approve applicant?',
                    text: 'Approving will send the scholar registration link via Email & SMS to the applicant.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, approve',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) {
                        sendStatusUpdate(currentApplicationId, 'Approved', null, { notify_template: NOTIFY_TEMPLATE_REGISTRATION });
                    }
                });
            }


            // prompt for rejection reason then send Rejected status
            function confirmReject() {
                if (!currentApplicationId) {
                    Swal.fire({ icon: 'warning', title: 'No application', text: 'No application selected.' });
                    return;
                }
                Swal.fire({
                    title: 'Reject applicant',
                    text: 'Please provide a reason why this application is being rejected (this will be sent to the applicant).',
                    input: 'textarea',
                    inputPlaceholder: 'Type rejection reason here...',
                    inputAttributes: { 'aria-label': 'Rejection reason' },
                    showCancelButton: true,
                    confirmButtonText: 'Submit rejection',
                    cancelButtonText: 'Cancel',
                    preConfirm: (value) => {
                        const reason = (value || '').trim();
                        if (!reason) {
                            Swal.showValidationMessage('A rejection reason is required');
                        }
                        return reason;
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const reason = result.value;
                        sendStatusUpdate(currentApplicationId, 'Rejected', reason);
                    }
                });
            }


            // centralised helper to POST status updates to server
            async function sendStatusUpdate(applicationId, status, reason = null, extra = {}) {
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;

                // Ensure reason is always a string (server expects string)
                const normalizedReason = (reason === null || reason === undefined) ? '' : String(reason);

                // Build payload without undefined values
                const payload = {
                    status: status,
                    ...extra
                };

                // Only include reason for rejection
                if (status === 'Rejected') {
                    payload.reason = normalizedReason;
                }

                Swal.fire({
                    title: status + ' — please wait',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                try {
                    const res = await fetch(`/mayor_staff/status/${applicationId}`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
                        },
                        body: JSON.stringify(payload)
                    });

                    // read as text then try parse JSON to avoid json() throwing on HTML responses
                    const text = await res.text();
                    let data = null;
                    try { data = text ? JSON.parse(text) : null; } catch (e) { data = null; }

                    Swal.close();

                    if (!res.ok) {
                        if (res.status === 419) {
                            return Swal.fire({ icon: 'error', title: 'Session Expired', text: 'Your session expired. Please reload the page and try again.' });
                        }
                        const message = (data && data.message) ? data.message : `Server error (${res.status})`;
                        return Swal.fire({ icon: 'error', title: 'Error', text: message });
                    }

                    if (data && data.success) {
                        Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Status updated.' });
                        
                        closeIntakeSheetModal();
                        document.querySelectorAll('#tableView .view-intake-btn').forEach(btn => {
                            if (btn.getAttribute('data-id') == applicationId) {
                                const tr = btn.closest('tr');
                                if (tr) tr.remove();
                            }
                        });
                        // refresh pagination after row removal
                        updatePagination('table');
                        return;
                    }

                    const errMsg = (data && data.message) ? data.message : 'Failed to update status.';
                    Swal.fire({ icon: 'error', title: 'Error', text: errMsg });
                } catch (err) {
                    console.error('Update status error:', err);
                    Swal.close();
                    Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while updating status.' });
                }
            }

            // Inline update status for table buttons
            function updateStatus(applicationId, name, status) {
                if (status === 'Approved') {
                    Swal.fire({
                        title: 'Approve applicant?',
                        text: 'Approving will send the scholar registration link via Email & SMS to the applicant.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, approve',
                        cancelButtonText: 'Cancel'
                    }).then(result => {
                        if (result.isConfirmed) {
                            sendStatusUpdate(applicationId, 'Approved', null, { notify_template: NOTIFY_TEMPLATE_REGISTRATION });
                        }
                    });
                } else if (status === 'Rejected') {
                    Swal.fire({
                        title: 'Reject applicant',
                        text: 'Please provide a reason why this application is being rejected (this will be sent to the applicant).',
                        input: 'textarea',
                        inputPlaceholder: 'Type rejection reason here...',
                        inputAttributes: { 'aria-label': 'Rejection reason' },
                        showCancelButton: true,
                        confirmButtonText: 'Submit rejection',
                        cancelButtonText: 'Cancel',
                        preConfirm: (value) => {
                            const reason = (value || '').trim();
                            if (!reason) {
                                Swal.showValidationMessage('A rejection reason is required');
                            }
                            return reason;
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            const reason = result.value;
                            sendStatusUpdate(applicationId, 'Rejected', reason);
                        }
                    });
                }
            }

            function showTable() {
                const tableViewEl = document.getElementById("tableView");
                const listViewEl = document.getElementById("listView");
                if (tableViewEl) tableViewEl.classList.remove("hidden");
                if (listViewEl) listViewEl.classList.add("hidden");
                const activeTab = document.querySelector('.tab.active');
                if (activeTab) {
                    activeTab.classList.remove('active');
                    activeTab.classList.remove('tab-green');
                }
                const tabs = document.querySelectorAll('.tab');
                if (tabs && tabs[0]) tabs[0].classList.add('active');
                localStorage.setItem("viewMode", "table");
                if (typeof filterTable === 'function') filterTable();
            }

            function showList() {
                const tableViewEl = document.getElementById("tableView");
                const listViewEl = document.getElementById("listView");
                if (listViewEl) listViewEl.classList.remove("hidden");
                if (tableViewEl) tableViewEl.classList.add("hidden");
                const activeTab = document.querySelector('.tab.active');
                if (activeTab) {
                    activeTab.classList.remove('active');
                    activeTab.classList.remove('tab-green');
                }
                const tabs = document.querySelectorAll('.tab');
                if (tabs && tabs[1]) {
                    tabs[1].classList.add('active');
                    tabs[1].classList.add('tab-green');
                }
                localStorage.setItem("viewMode", "list");
                if (typeof filterList === 'function') filterList();
            }

            // View Intake Sheet Modal Functions
            function openIntakeSheetModal(applicationId) {
                if (!applicationId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No application ID provided'
                    });
                    return;
                }

                // Store current application ID for approve/reject actions
                currentApplicationId = applicationId;

                // Show loading state
                const modal = document.getElementById('intakeSheetModal');
                if (modal) {
                    modal.style.display = 'block';
                    const content = modal.querySelector('.modal-body');
                    if (content) {
                        content.innerHTML = `
                            <div class="loading-container">
                                <div class="loading-spinner">
                                    <img src="{{ asset('images/LYDO.png') }}" alt="Loading">
                                </div>
                                <div class="loading-text">Loading Intake Sheet...</div>
                            </div>`;
                    }
                }

                // Fetch intake sheet data
                fetch(`/mayor_staff/intake-sheet/${applicationId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('Intake sheet data:', data);
                        if (!data || !data.success) throw new Error(data?.message || 'No data received');

                        // Populate modal with data
                        populateIntakeSheetModal(data.intakeSheet);

                        // Show the modal
                        if (modal) {
                            modal.style.display = 'block';
                            modal.scrollTop = 0;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching intake sheet:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load intake sheet data'
                        });
                        closeIntakeSheetModal();
                    });
            }

            function closeIntakeSheetModal() {
                const modal = document.getElementById('intakeSheetModal');
                if (modal) modal.style.display = 'none';
            }

            function populateIntakeSheetModal(intake) {
                // normalize & accept both intakeSheet / intake_sheet shapes
                const payload = intake || {};
                const d = normalizeData(payload);

                const setText = (id, value) => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.textContent = value === null || value === undefined || value === '' ? '-' : value;
                };

                // Applicant / Head info
                const applicantName = d.applicant_name || [d.applicant_fname, d.applicant_mname, d.applicant_lname, d.applicant_suffix].filter(Boolean).join(' ');
                setText('modal-applicant-name', applicantName || '-');
                setText('modal-applicant-gender', d.applicant_gender || '-');
                setText('modal-serial-number', d.serial_number || '-');
                setText('modal-head-4ps', d.head_4ps || '-');
                setText('modal-head-ipno', d.head_ipno || '-');
                setText('modal-head-address', d.head_address || '-');
                setText('modal-head-zone', d.head_zone || '-');
                setText('modal-head-barangay', d.head_barangay || '-');
                setText('modal-head-pob', d.head_pob || '-');
                setText('modal-head-dob', d.head_dob ? formatDate(d.head_dob) : '-');
                setText('modal-head-educ', d.head_educ || '-');
                setText('modal-head-occ', d.head_occ || '-');
                setText('modal-head-religion', d.head_religion || '-');

                // Household / Income
                setText('modal-other-income', d.other_income || '-');
                // Display house with rent if applicable (accepts many rent key variants and formats)
                let houseDisplay = d.house_house || '-';
                if (d.house_house && String(d.house_house).toLowerCase().includes('rent')) {
                    const rentRaw = d.house_house_rent ?? d.house_rent ?? d.house_rental ?? d.house_rent_amount;
                    if (rentRaw) {
                        const rentNum = parseFloat(String(rentRaw).replace(/[^0-9.-]+/g, '')) || 0;
                        const rentText = rentNum ? `₱${rentNum.toLocaleString()}` : escapeHtml(String(rentRaw));
                        houseDisplay += ` (Rent: ${rentText})`;
                    }
                }
                document.getElementById('modal-house-house').innerHTML = houseDisplay;

                setText('modal-house-electric', d.house_electric || '-');
                setText('modal-remarks', d.remarks || '-');
                setText('modal-house-total-income', d.house_total_income ?? '-');

                // Display lot with rent if applicable (accepts many rent key variants and formats)
                let lotDisplay = d.house_lot || '-';
                if (d.house_lot && String(d.house_lot).toLowerCase().includes('rent')) {
                    const lotRentRaw = d.house_lot_rent ?? d.lot_rent ?? d.house_lot_rental ?? d.house_lot_rent_amount ?? d.lot_rent_amount;
                    if (lotRentRaw) {
                        const lotRentNum = parseFloat(String(lotRentRaw).replace(/[^0-9.-]+/g, '')) || 0;
                        const lotRentText = lotRentNum ? `₱${lotRentNum.toLocaleString()}` : escapeHtml(String(lotRentRaw));
                        lotDisplay += ` (Rent: ${lotRentText})`;
                    }
                }
                document.getElementById('modal-house-lot').innerHTML = lotDisplay;

                // Family members - support array or JSON string
                let family = d.family_members || [];
                if (typeof family === 'string') {
                    try { family = JSON.parse(family); } catch (e) { family = []; }
                }
                if (!Array.isArray(family)) family = [];

                const fmBody = document.getElementById('modal-family-members');
                if (fmBody) {
                    if (family.length === 0) {
                        fmBody.innerHTML = '<tr><td colspan="10" class="text-center">No family members recorded</td></tr>';
                    } else {
                        fmBody.innerHTML = family.map(m => {
                            const name = escapeHtml(m.name || m.fullname || m.full_name || `${m.first_name||''} ${m.last_name||''}`.trim());
                            const relation = escapeHtml(m.relationship || m.relation || m.rel || '');
                            const bdate = m.birthdate || m.dob || m.birth || '';
                            const age = m.age || m.years || (bdate ? Math.max(0, new Date().getFullYear() - new Date(bdate).getFullYear()) : '');
                            const sex = escapeHtml(m.sex || m.gender || '');
                            const civil = escapeHtml(m.civil_status || m.civil || '');
                            const educ = escapeHtml(m.education || m.educational_attainment || '');
                            const occ = escapeHtml(m.occupation || m.occ || '');
                            // Format income with peso sign and thousands separator
                            const rawIncome = m.income || m.monthly_income || 0;
                            const income = rawIncome ? `₱${Number(rawIncome).toLocaleString()}` : '-';
                            const remarks = escapeHtml(m.remarks || '');
                            return `<tr>
                                <td>${name}</td>
                                <td>${relation}</td>
                                <td>${bdate ? escapeHtml(formatDate(bdate)) : ''}</td>
                                <td>${age}</td>
                                <td>${sex}</td>
                                <td>${civil}</td>
                                <td>${educ}</td>
                                <td>${occ}</td>
                                <td class="text-right">${income}</td>
                                <td>${remarks}</td>
                            </tr>`;
                        }).join('');
                    }
                }

                // Service records - support array or JSON string
                let services = d.rv_service_records || d.rv_service_records || d.social_service_records || [];
                if (typeof services === 'string') {
                    try { services = JSON.parse(services); } catch (e) { services = []; }
                }
                if (!Array.isArray(services)) services = [];

                const srBody = document.getElementById('modal-service-records');
                if (srBody) {
                    if (services.length === 0) {
                        srBody.innerHTML = '<tr><td colspan="4" class="text-center">No service records</td></tr>';
                    } else {
                        srBody.innerHTML = services.map(s => {
                            const date = s.date || s.record_date || s.created_at || '';
                            const problem = escapeHtml(s.problem || s.need || s.issue || '');
                            const action = escapeHtml(s.action || s.assistance || s.service || '');
                            const remarks = escapeHtml(s.remarks || '');
                            return `<tr>
                                <td>${date ? escapeHtml(formatDate(date)) : ''}</td>
                                <td>${problem}</td>
                                <td>${action}</td>
                                <td>${remarks}</td>
                            </tr>`;
                        }).join('');
                    }
                }

                // Worker / Officer names + date
                setText('modal-worker-fullname', d.worker_name || '-');
                setText('modal-officer-fullname', d.officer_name || '-');
                setText('modal-date-entry', d.date_entry ? formatDate(d.date_entry) : '-');

                const setImage = (imgId, url) => {
                    const imgEl = document.getElementById(imgId);
                    if (!imgEl) return;
                    let u = '';
                    if (!url) { imgEl.style.display = 'none'; return; }
                    if (typeof url === 'string') u = url.trim();
                    // if url given as object {url: '...'} or {path: '...'}
                    if (!u && typeof url === 'object') {
                        u = (url.url || url.path || url.src || '') + '';
                    }
                    if (!u || u === '-') { imgEl.style.display = 'none'; return; }
                    try {
                        const resolved = resolveUrl(u);
                        imgEl.onerror = () => { imgEl.style.display = 'none'; };
                        imgEl.onload = () => { imgEl.style.display = ''; };
                        imgEl.src = resolved;
                    } catch (e) {
                        imgEl.style.display = 'none';
                    }
                };

                const renderSignatureContainer = (containerId, url) => {
                    const el = document.getElementById(containerId);
                    if (!el) return;
                    let u = '';
                    if (!url) { el.innerHTML = '<p class="text-xs text-gray-500">No signature</p>'; return; }
                    if (typeof url === 'string') u = url.trim();
                    if (!u && typeof url === 'object') {
                        // handle {url:...} or arrays
                        if (Array.isArray(url)) {
                            u = (url[0] && (url[0].url || url[0].path || url[0])) || '';
                        } else {
                            u = (url.url || url.path || url.src || '') + '';
                        }
                    }
                    if (!u || u === '-') {
                        el.innerHTML = '<p class="text-xs text-gray-500">No signature</p>';
                        return;
                    }
                    const resolved = resolveUrl(u);
                    const img = document.createElement('img');
                    img.style.maxWidth = '220px';
                    img.style.height = '80px';
                    img.style.objectFit = 'contain';
                    img.style.border = '1px solid #e5e7eb';
                    img.alt = 'signature';
                    img.onerror = () => { el.innerHTML = '<p class="text-xs text-gray-500">No signature</p>'; };
                    img.onload = () => { /* keep image */ };
                    img.src = resolved;
                    el.innerHTML = '';
                    // center image inside container
                    const wrapper = document.createElement('div');
                    wrapper.style.display = 'flex';
                    wrapper.style.justifyContent = 'center';
                    wrapper.style.alignItems = 'center';
                    wrapper.appendChild(img);
                    el.appendChild(wrapper);
                };

                // try worker/officer photo fields, fall back to signature if no photo available
                setImage('modal-worker-photo-img', d.worker_photo || d.worker_picture || d.signature_worker);
                setImage('modal-officer-photo-img', d.officer_photo || d.officer_picture || d.signature_officer);

                // render signatures
                renderSignatureContainer('modal-worker-signature', d.signature_worker);
                renderSignatureContainer('modal-officer-signature', d.signature_officer);
                renderSignatureContainer('modal-client-signature-large', d.signature_client);

                // Documents
                let documents = d.documents || {};
                if (typeof documents === 'string') {
                    try { documents = JSON.parse(documents); } catch (e) { documents = {}; }
                }
                const docContainer = document.getElementById('modal-documents');
                if (docContainer) {
                    const docKeys = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];
                    const docLabels = {
                        application_letter: 'Application Letter',
                        cert_of_reg: 'Certificate of Registration',
                        grade_slip: 'Grade Slip',
                        brgy_indigency: 'Barangay Indigency',
                        student_id: 'Student ID'
                    };
                    docContainer.innerHTML = docKeys.map(key => {
                        const url = documents[key];
                        if (!url) return '';
                        const label = docLabels[key] || key;
                        const resolvedUrl = resolveUrl(url);
                        return `<div class="document-item p-2 border rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                            <a href="${resolvedUrl}" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">${label}</a>
                        </div>`;
                    }).join('');
                    if (docContainer.innerHTML.trim() === '') {
                        docContainer.innerHTML = '<p class="text-gray-500">No documents available</p>';
                    }
                }

                // ensure modal visible
                const modal = document.getElementById('intakeSheetModal');
                if (modal) {
                    modal.style.display = 'block';
                    modal.scrollTop = 0;
                }
            }

            function printIntakeSheet() {
                // simple print of modal content
                const content = document.querySelector('#intakeSheetModal .modal-content').innerHTML;
                const win = window.open('', '_blank');
                win.document.write('<html><head><title>Print Intake Sheet</title></head><body>' + content + '</body></html>');
                win.document.close();
                win.print();
                win.close();
            }

            // small helper
            function escapeHtml(s) {
                if (s === null || s === undefined) return '';
                return String(s)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Normalize server payload to expected keys (fallbacks)
            function normalizeData(d) {
                if (!d || typeof d !== 'object') return {};
                const get = (keys, def = '') => {
                    for (const k of keys) {
                        if (d[k] !== undefined && d[k] !== null) return d[k];
                    }
                    return def;
                };

                const normalized = {
                    serial_number: get(['serial_number', 'serial_no', 'serial']),
                    applicant_fname: get(['applicant_fname', 'fname', 'first_name', 'firstName']),
                    applicant_mname: get(['applicant_mname', 'mname', 'middle_name', 'middleName']),
                    applicant_lname: get(['applicant_lname', 'lname', 'last_name', 'lastName']),
                    applicant_suffix: get(['applicant_suffix', 'suffix']),
                    applicant_gender: get(['applicant_gender', 'gender', 'sex']),
                    head_4ps: get(['head_4ps', '4ps', 'four_ps']),
                    head_ipno: get(['head_ipno', 'ip_no', 'ipno']),
                    head_address: get(['head_address', 'address']),
                    head_zone: get(['head_zone', 'zone']),
                    head_barangay: get(['head_barangay', 'barangay', 'head_brgy']),
                    head_dob: get(['head_dob', 'dob', 'birthdate']),
                    head_pob: get(['head_pob', 'pob', 'place_of_birth']),
                    head_educ: get(['head_educ', 'education', 'educ']),
                    head_occ: get(['head_occ', 'occupation', 'occ']),
                    head_religion: get(['head_religion', 'religion']),
                    family_members: get(['family_members', 'members', 'family']),
                    rv_service_records: get(['rv_service_records', 'service_records', 'serviceRecords']),
                    other_income: get(['other_income', 'other_source_income', 'otherIncome', 'other']),
                    house_total_income: get(['house_total_income', 'total_income']),
                    house_net_income: get(['house_net_income', 'net_income']),
                    house_house: get(['house_house']),
                    house_house_rent: get(['house_house_rent']),
                    house_lot: get(['house_lot']),
                    house_lot_rent: get(['house_lot_rent']),
                    house_water: get(['house_water']),
                    house_electric: get(['house_electric']),
                    remarks: get(['remarks', 'final_remarks', 'assessment']),
                    signature_client: get(['signature_client', 'signature_client_url', 'client_signature']),
                    signature_worker: get(['signature_worker', 'signature_worker_url', 'worker_signature']),
                    signature_officer: get(['signature_officer', 'signature_officer_url', 'officer_signature']),
                    worker_name: get(['worker_name', 'social_worker', 'worker']),
                    officer_name: get(['officer_name', 'officer']),
                    date_entry: get(['date_entry', 'created_at', 'date']),
                };

                ['signature_client', 'signature_worker', 'signature_officer'].forEach(k => {
                    if (normalized[k] && typeof normalized[k] === 'string' && normalized[k].startsWith('/')) {
                        normalized[k] = resolveUrl(normalized[k]);
                    }
                });

                return normalized;
            }

            // Normalize document data
            function normalizeDocumentData(d) {
                if (!d || typeof d !== 'object') return {};
                const get = (keys, def = '') => {
                    for (const k of keys) {
                        if (d[k] !== undefined && d[k] !== null) return d[k];
                    }
                    return def;
                };

                return {
                    application_letter: get(['application_letter', 'letter']),
                    cert_of_reg: get(['cert_of_reg', 'registration_certificate']),
                    grade_slip: get(['grade_slip', 'grades']),
                    brgy_indigency: get(['brgy_indigency', 'indigency']),
                    student_id: get(['student_id', 'id']),
                };
            }

            function resolveUrl(path) {
                try {
                    if (!path) return path;
                    if (path.startsWith('http://') || path.startsWith('https://')) return path;
                    const base = window.location.origin;
                    return base + (path.startsWith('/') ? '' : '/') + path;
                } catch (e) { return path; }
            }

            function formatDate(dateString) {
                if (!dateString) return "-";
                const date = new Date(dateString);
                if (isNaN(date)) return dateString;
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            }



            // Filter functions
            function filterTable() {
                const nameSearchEl = document.getElementById('searchInputTable');
                const barangayFilterEl = document.getElementById('barangaySelectTable');
                const nameSearchValue = nameSearchEl ? nameSearchEl.value.toLowerCase().trim() : '';
                const barangayFilterValue = barangayFilterEl ? barangayFilterEl.value.toLowerCase().trim() : '';

                const tableViewRows = document.querySelectorAll('#tableView tbody tr');
                tableViewRows.forEach(row => {
                    // skip header/empty rows
                    if (!row.cells || row.cells.length < 3) return;
                    const nameCell = (row.cells[1].textContent || '').toLowerCase();
                    const barangayCell = (row.cells[2].textContent || '').toLowerCase();

                    const matchesName = nameCell.includes(nameSearchValue);
                    const matchesBarangay = barangayFilterValue === '' || barangayCell.includes(barangayFilterValue);

                    row.style.display = (matchesName && matchesBarangay) ? '' : 'none';
                });
               // update pagination so only matching rows are paged and visible
               // reset to first page when user filters
               paginationState.table.currentPage = 1;
               updatePagination('table');
            }

            function filterList() {
                const nameSearchEl = document.getElementById('listNameSearch');
                const barangayFilterEl = document.getElementById('listBarangayFilter');
                const statusFilterEl = document.getElementById('listStatusFilter');
                const nameSearchValue = nameSearchEl ? nameSearchEl.value.toLowerCase().trim() : '';
                const barangayFilterValue = barangayFilterEl ? barangayFilterEl.value.toLowerCase().trim() : '';
                const statusFilterValue = statusFilterEl ? statusFilterEl.value.toLowerCase().trim() : '';

                const listViewRows = document.querySelectorAll('#listView tbody tr');
                listViewRows.forEach(row => {
                    if (!row.cells || row.cells.length < 4) return;
                    const nameCell = (row.cells[1].textContent || '').toLowerCase();
                    const barangayCell = (row.cells[2].textContent || '').toLowerCase();
                    const statusCell = (row.cells[4].textContent || '').toLowerCase();

                    const matchesName = nameCell.includes(nameSearchValue);
                    const matchesBarangay = barangayFilterValue === '' || barangayCell.includes(barangayFilterValue);
                    const matchesStatus = statusFilterValue === '' || statusCell.includes(statusFilterValue);

                    row.style.display = (matchesName && matchesBarangay && matchesStatus) ? '' : 'none';
                });
               // update pagination for list view
               paginationState.list.currentPage = 1;
               updatePagination('list');
            }

            // Add this to the existing script
            function bindViewButtons() {
                document.querySelectorAll('.view-intake-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        if (!id) {
                            console.error('No application ID found on button');
                            return;
                        }
                        openIntakeSheetModal(id);
                    });
                });
            }

            // Safe event wiring on DOM ready
            document.addEventListener("DOMContentLoaded", function() {
                bindViewButtons();
                let viewMode = localStorage.getItem("viewMode") || "table";
                if(viewMode === "list") {
                    showList();
                } else {
                    showTable();
                }

                // initialize pagination for both tables
                // small timeout to ensure table DOM available (blade may render large HTML)
                setTimeout(() => {
                    // make sure filters applied initially
                    filterTable();
                    filterList();
                }, 50);

                const nameSearch = document.getElementById('searchInputTable');
                const barangayFilter = document.getElementById('barangaySelectTable');
                const listNameSearch = document.getElementById('listNameSearch');
                const listBarangayFilter = document.getElementById('listBarangayFilter');
                const listStatusFilter = document.getElementById('listStatusFilter');

                if (nameSearch) nameSearch.addEventListener('input', filterTable);
                if (barangayFilter) barangayFilter.addEventListener('change', filterTable);
                if (listNameSearch) listNameSearch.addEventListener('input', filterList);
                if (listBarangayFilter) listBarangayFilter.addEventListener('change', filterList);
                if (listStatusFilter) listStatusFilter.addEventListener('change', filterList);



                // Add event listeners for approve/reject buttons
                document.querySelectorAll('.approve-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');
                       
                        updateStatus(id, name, 'Approved');
                    });
                });
                
                document.querySelectorAll('.reject-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');
                        updateStatus(id, name, 'Rejected');
                    });
                });



                // Restore dropdown open state
                document.querySelectorAll("ul[id]").forEach(menu => {
                    const state = localStorage.getItem(menu.id);
                    if (state === "open") menu.classList.remove("hidden");
                });
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

        <script src="{{ asset('js/paginate.js') }}"></script>
    </body>
</html>