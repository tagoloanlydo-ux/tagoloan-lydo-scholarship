<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/screening.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <audio id="notificationSound" src="{{ asset('notification/blade.wav') }}" preload="auto"></audio>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>
<style>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.scale-0 {
    transform: scale(0);
    transition: transform 0.3s ease;
}

#notifCount {
    transition: all 0.3s ease;
}

#notifDropdown {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
<body class="bg-gray-50">
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
                            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
</div>

    <div class="dashboard-grid">
        <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                
                <!-- Notification Bell with Improved System -->
                <div class="relative hidden">
                    <button id="notifBell" class="relative focus:outline-none">
                        <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                        @if($badgeCount > 0)
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center transition-all duration-300">
                                {{ $badgeCount }}
                            </span>
                        @else
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center hidden transition-all duration-300"></span>
                        @endif
                    </button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b font-semibold text-gray-700 flex justify-between items-center">
                            <span>Notifications</span>
                            <button id="markAllRead" class="text-xs text-blue-600 hover:text-blue-800 transition-colors">Mark all as read</button>
                        </div>
                        <ul id="notifList" class="max-h-60 overflow-y-auto">
                            @forelse($notifications as $notif)
                            <li class="px-4 py-2 hover:bg-gray-50 text-sm border-b transition-colors">
                                @if($notif->initial_screening == 'Approved')
                                <p class="text-green-600 font-medium"> ✅ {{ $notif->name }} passed initial screening </p>
                                @elseif($notif->status == 'Renewed')
                                <p class="text-blue-600 font-medium"> 🔄 {{ $notif->name }} submitted renewal </p>
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
       <div class="flex flex-1 overflow-hidden">
            <div class="w-20 md:w-80 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                         <ul class="side-menu top space-y-4">
                        <li>
                            <a href="/lydo_staff/dashboard"  class="flex items-center  p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/screening" class="flex items-center justify-between p-3 rounded-lg text-gray-700 bg-violet-600 text-white">
                                <div class="flex items-center">
                                    <i class="bx bxs-file-blank text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Applicant Interview</span>
                                </div>
                                @if($pendingScreening > 0) <span id="pendingScreeningBadge" class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                    {{ $pendingScreening }}
                                </span> @endif
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/renewal" class=" flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Renewals</span>
                                </div>
                                @if($pendingRenewals > 0) <span class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                    {{ $pendingRenewals }}
                                </span> @endif
                            </a>
                        </li>
                       <li>
                            <a href="/lydo_staff/disbursement" class=" flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                 <div class="flex items-center">
                                    <i class="bx bx-wallet text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Disbursement</span>
                                </div>
                            </a>
                        </li>
                  <li>
                            <a href="/lydo_staff/walk_in" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="fas fa-walking text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Walk-in Applicants</span>
                            </a>
                        </li>
                    </ul>
                    <ul class="side-menu space-y-1">
                        <li>
                            <a href="/lydo_staff/settings" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bxs-cog text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Settings</span>
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
            <div class="flex-1 p-4 md:p-2 text-[16px] content-scrollable">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-3xl font-bold text-gray-800">Applicants for Interview</h5>
                    </div>
                    <!-- ✅ Applicants -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex gap-2">
                            <div onclick="showTable()" class="tab active" id="tab-screening">
                                <i class="fas fa-table mr-1"></i> Pending Interviews
                            </div>
                            <div onclick="showList()" class="tab tab-green" id="tab-reviewed">
                                <i class="fas fa-list mr-1"></i> Reviewed Applicants
                            </div>
                        </div>
                    </div>

                    <!-- Pending Remarks Tab -->
                    <div id="tableView" class="overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-violet-50 p-3 rounded-lg border border-violet-200">
                            📋 Pending Remarks: View applicants awaiting remarks assignment.
                            </h3>
                        </div>
                        <div class="flex gap-2 mb-4">
                            <input type="text" id="nameSearch" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                            <select id="barangayFilter" class="border rounded px-3 py-2">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-green-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Intake Sheet</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($tableApplicants as $index => $app)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        {{ ucfirst(strtolower($app->applicant_lname)) }},
                                        {{ ucfirst(strtolower($app->applicant_fname)) }}

                                        @if(!empty($app->applicant_mname))
                                            {{ strtoupper(substr($app->applicant_mname, 0, 1)) }}.
                                        @endif

                                        @if(!empty($app->applicant_suffix))
                                            {{ ucfirst(strtolower($app->applicant_suffix)) }}
                                        @endif
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_course }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_school_name }}</td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <button
                                            type="button"
                                            title="Assign Remarks"
                                            class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow"
                                            data-id="{{ $app->application_personnel_id }}"
                                            data-remarks=""
                                            data-name="{{ $app->applicant_fname }} {{ $app->applicant_lname }}"
                                            data-fname="{{ $app->applicant_fname }}"
                                            data-mname="{{ $app->applicant_mname }}"
                                            data-lname="{{ $app->applicant_lname }}"
                                            data-suffix="{{ $app->applicant_suffix }}"
                                            data-bdate="{{ $app->applicant_bdate }}"
                                            data-brgy="{{ $app->applicant_brgy }}"
                                            data-gender="{{ $app->applicant_gender }}"                                            onclick="openEditRemarksModal(this)">
                                            <i class="fas fa-plus mr-1"></i> Intake Sheet
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 border border-gray-200 text-gray-500">No applicants pending remarks.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Pagination for Table View -->
                        <div class="pagination-container" id="tablePagination"></div>
                    </div>

                    <!-- Reviewed Applicants Tab -->
                    <div id="listView" class="overflow-x-auto hidden">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-white p-3 rounded-lg border border-gray-200">
                            ✅ Reviewed Applicants: View applicants with assigned remarks (Poor, Non Poor, Ultra Poor).
                            </h3>
                        </div>
                        <div class="flex gap-2 mb-4">
                            <input type="text" id="listNameSearch" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                            <select id="listBarangayFilter" class="border rounded px-3 py-2">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-green-800 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Intake Sheet</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listApplicants as $index => $app)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        {{ ucfirst(strtolower($app->applicant_lname)) }},
                                        {{ ucfirst(strtolower($app->applicant_fname)) }}

                                        @if(!empty($app->applicant_mname))
                                            {{ strtoupper(substr($app->applicant_mname, 0, 1)) }}.
                                        @endif

                                        @if(!empty($app->applicant_suffix))
                                            {{ ucfirst(strtolower($app->applicant_suffix)) }}
                                        @endif
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_course }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_school_name }}</td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <button
                                            type="button"
                                            title="Review Intake Sheet"
                                            class="px-3 py-1 text-sm bg-green-500 hover:bg-green-600 text-white rounded-lg shadow"
                                            data-id="{{ $app->application_personnel_id }}"
                                            onclick="openReviewModal(this)">
                                            <i class="fas fa-eye mr-1"></i> Review
                                        </button>
                                    </td>
                                                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->remarks == 'Poor') bg-yellow-100 text-yellow-800
                                            @elseif($app->remarks == 'Non Poor') bg-red-100 text-green-800
                                            @elseif($app->remarks == 'Ultra Poor') bg-green-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->remarks }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 border border-gray-200 text-gray-500">No reviewed applicants.</td>
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

        <!-- Edit Intake Sheet Modal -->
<div id="editRemarksModal" class="fixed inset-0 hidden bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8">
    <div class="bg-white w-full max-w-7xl rounded-2xl shadow-2xl p-8 max-h-[90vh] overflow-y-auto relative">
        <!-- Close button -->
        <button type="button" onclick="closeEditRemarksModal()" class="absolute top-6 right-6 text-gray-500 hover:text-gray-700 z-10 transition-colors duration-200 p-1 rounded-full hover:bg-gray-100">
            <i class="fas fa-times text-2xl"></i>
        </button>

        <!-- Header with logo -->
        <div class="flex items-center text-2xl font-bold mb-6 text-gray-800">
            <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Logo" class="h-10 w-auto mr-3">
            Family Intake Sheet - <span id="applicant_full_name"></span>
        </div>

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200 mb-8 overflow-x-auto">
            <button type="button" id="tab-family" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-semibold text-violet-700 border-b-2 border-violet-700 bg-violet-50 rounded-t-lg transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-home mr-2"></i>
                    Family Details
                </span>
            </button>
            <button type="button" id="tab-family-members" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Family Members
                </span>
            </button>
            <button type="button" id="tab-additional" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Additional Info
                </span>
            </button>
            <button type="button" id="tab-social-service" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Social Service
                </span>
            </button>
            <button type="button" id="tab-health" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-file-signature mr-2"></i>
                    Lydo Staff
                </span>
            </button>
            <button type="button" id="tab-remarks" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-tags mr-2"></i>
                    Final Remarks
                </span>
            </button>
        </div>

        <!-- Progress indicator -->
        <div class="mb-6">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span>Step <span id="current-step">1</span> of 6</span>
                <span id="step-title">Family Details</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-violet-600 h-2 rounded-full transition-all duration-500" style="width: 16%"></div>
            </div>
        </div>

<form id="updateRemarksForm" method="POST" action="">
            @csrf
            <input type="hidden" name="id" id="remarks_id">
            <input type="hidden" id="modal_mode" value="edit">

            <!-- Family Details Tab -->
            <div id="tab-family-content" class="tab-content">
                <!-- Head of Family Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-violet-600"></i>
                        Head of Family
                    </h3>
                    
                    <!-- Row 1: 4Ps and IP No. -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">4Ps</label>
                            <select name="head_4ps" id="head_4ps" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">IP No.</label>
                            <input type="text" name="head_ipno" id="head_ipno" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="Optional">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Serial Number</label>
                            <input type="text" name="serial_number" id="serial_number" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                        </div>
                    </div>
                    
                    <!-- Row 2: Applicant Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                            <!-- name removed so it won't be submitted; kept id for display -->
                            <input type="text" id="applicant_fname" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Middle Name</label>
                            <!-- name removed so it won't be submitted; kept id for display -->
                            <input type="text" id="applicant_mname" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                            <!-- name removed so it won't be submitted; kept id for display -->
                            <input type="text" id="applicant_lname" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Suffix</label>
                            <!-- name removed so it won't be submitted; kept id for display -->
                            <input type="text" id="applicant_suffix" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" readonly>
                        </div>
                    </div>
                    
                    <!-- Row 3: Address, Zone, Barangay -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="head_address" id="head_address" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Zone</label>
                            <input type="text" name="head_zone" id="head_zone" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Barangay <span class="text-red-500">*</span></label>
                            <input type="text" name="head_barangay" id="head_barangay" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                        </div>
                    </div>
                    
                    <!-- Row 4: Date of Birth, Place of Birth, Gender -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth <span class="text-red-500">*</span></label>
                            <input type="date" name="head_dob" id="head_dob" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Place of Birth</label>
                            <input type="text" name="head_pob" id="head_pob" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                        <div  class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
                            <select name="applicant_gender" id="applicant_gender" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Row 5: Education, Occupation, Religion -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Education</label>
                            <select name="head_educ" id="head_educ" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Select Education</option>
                                <option value="Elementary">Elementary</option>
                                <option value="High School">High School</option>
                                <option value="Vocational">Vocational</option>
                                <option value="College">College</option>
                                <option value="Post Graduate">Post Graduate</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Occupation</label>
                            <select name="head_occ" id="head_occ" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Select Occupation</option>
                                <option value="Farmer">Farmer</option>
                                <option value="Teacher">Teacher</option>
                                <option value="Driver">Driver</option>
                                <option value="Business Owner">Business Owner</option>
                                <option value="Employee">Employee</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Student">Student</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Religion</label>
                            <select name="head_religion" id="head_religion" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Select Religion</option>
                                <option value="Catholic">Catholic</option>
                                <option value="Protestant">Protestant</option>
                                <option value="Islam">Islam</option>
                                <option value="Buddhist">Buddhist</option>
                                <option value="Atheist">Atheist</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('family-members')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Family Members Tab -->
            <div id="tab-family-members-content" class="tab-content hidden">
                <!-- Family Members Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-users mr-2 text-violet-600"></i>
                        Family Members
                    </h3>
                    <p class="text-sm text-gray-600 mb-4 bg-blue-50 p-3 rounded-lg">Please fill up all required fields in the family members table. Remarks should be selected based on the categories listed below.</p>
                    
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table id="family_members_table" class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Name</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Relation</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Birthdate</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Age</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Sex</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Civil Status</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Education</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Occupation</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Income</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Remarks</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="family_members_tbody" class="table-input-styling">
                                <!-- Rows will be added dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <button type="button" onclick="addFamilyMemberRow()" class="mt-4 bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Family Member
                    </button>
                    
                    <textarea name="family_members" id="family_members" class="hidden"></textarea>
                    
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-tags mr-2 text-violet-600"></i>
                            Remarks Categories:
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Out of School Youth (OSY)</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Solo Parent (SP)</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Person with Disability (PWD)</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Senior Citizen (SC)</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Lactating Mother</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Pregnant Mother</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('family')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <button type="button" onclick="showTab('additional')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Additional Info Tab -->
            <div id="tab-additional-content" class="tab-content hidden">
                <!-- Household Info Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-home mr-2 text-violet-600"></i>
                        Household Information
                    </h3>
                    
                    <!-- Income Section -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Income Calculation
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Other Income</label>
                                <input type="number" step="0.01" name="other_income" id="other_income" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="0.00" oninput="calculateIncomes()">
                                <p class="text-xs text-gray-500 mt-2">Additional income not from family members</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Total Income</label>
                                <input type="number" step="0.01" name="house_total_income" id="house_total_income" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                                <p class="text-xs text-gray-500 mt-2">Family Members Income + Other Income</p>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses Section -->
                    <div class="mb-6 p-4 bg-red-50 rounded-xl border border-red-200">
                        <h4 class="font-semibold text-red-800 mb-3 flex items-center">
                            <i class="fas fa-receipt mr-2"></i>
                            Expenses
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">House</label>
                                <select name="house_house" id="house_house" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" onchange="toggleHouseRent()">
                                    <option value="">Select</option>
                                    <option value="Owned">Owned</option>
                                    <option value="Rent">Rented</option>
                                </select>
<!-- In the House Rent field, fix the name attribute -->
<div id="house_rent_group" style="display: none;" class="mt-3">
    <label class="block text-sm font-semibold text-gray-700 mb-2">House Rent</label>
    <!-- Change name from "hhouse_rent" to "house_rent" -->
    <input type="number" step="0.01" name="house_rent" id="house_rent" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="0.00" oninput="calculateIncomes()">
</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lot</label>
                                <select name="house_lot" id="house_lot" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" onchange="toggleLotRent()">
                                    <option value="">Select</option>
                                    <option value="Owned">Owned</option>
                                    <option value="Rent">Rented</option>
                                </select>
                                <div id="lot_rent_group" style="display: none;" class="mt-3">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lot Rent</label>
                                    <input type="number" step="0.01" name="lot_rent" id="lot_rent" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="0.00" oninput="calculateIncomes()">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Water</label>
                                <input type="number" step="0.01" name="house_water" id="house_water" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="0.00" oninput="calculateIncomes()">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Electric</label>
                                <input type="number" step="0.01" name="house_electric" id="house_electric" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="0.00" oninput="calculateIncomes()">
                            </div>
                        </div>
                    </div>

                    <!-- Net Income Section -->
                    <div class="mb-6 p-4 bg-green-50 rounded-xl border border-green-200">
                        <h4 class="font-semibold text-green-800 mb-3 flex items-center">
                            <i class="fas fa-calculator mr-2"></i>
                            Net Income Calculation
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Net Income</label>
                                <input type="number" step="0.01" name="house_net_income" id="house_net_income" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                                <p class="text-xs text-gray-500 mt-2">Total Income - Total Expenses</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('family-members')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <button type="button" id="additional-next-btn" onclick="showTab('social-service')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Social Service Records Tab -->
            <div id="tab-social-service-content" class="tab-content hidden">
                <!-- Social Service Records Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-clipboard-list mr-2 text-violet-600"></i>
                        Social Service Records
                    </h3>
                    
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table id="rv_service_records_table" class="w-full border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-3 py-3 text-left font-semibold text-gray-700">Date</th>
                                    <th class="border border-gray-300 px-3 py-3 text-left font-semibold text-gray-700">Problem/Need</th>
                                    <th class="border border-gray-300 px-3 py-3 text-left font-semibold text-gray-700">Action/Assistance Given</th>
                                    <th class="border border-gray-300 px-3 py-3 text-left font-semibold text-gray-700">Remarks</th>
                                    <th class="border border-gray-300 px-3 py-3 text-center font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody id="rv_service_records_tbody">
                                <!-- Rows will be added dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <button type="button" onclick="addRvServiceRecordRow()" class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Record
                    </button>
                    <textarea name="rv_service_records" id="rv_service_records" class="hidden"></textarea>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('additional')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <button type="button" onclick="showTab('health')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Health & Signatures Tab -->
            <div id="tab-health-content" class="tab-content hidden">
                <!-- Health & Signatures Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-file-signature mr-2 text-violet-600"></i>
                        Lydo Staff
                    </h3>
                    
                    <!-- Worker Name and Officer Name in one line -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Worker Name</label>
                            <input type="text" name="worker_name" id="worker_name" value="{{ session('lydopers') ? (session('lydopers')->lydopers_fname . ' ' . session('lydopers')->lydopers_lname) : '' }}" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Officer Name</label>
                            <input type="text" name="officer_name" id="officer_name" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                    </div>
                    
                    <!-- Date Entry and Signature Client -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Date Entry</label>
        <input type="date" name="date_entry" id="date_entry" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
    </div>

                    </div>
                    
               </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('social-service')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <button type="button" onclick="showTab('remarks')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

   <!-- Final Remarks Tab -->
<div id="tab-remarks-content" class="tab-content hidden">
    <!-- Final Remarks Section -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
            <i class="fas fa-tags mr-2 text-violet-600"></i>
            Final Remarks
        </h3>
        
        <div class="mb-6 p-4 bg-green-50 rounded-xl border border-green-200">
            <h4 class="font-semibold text-green-800 mb-3 flex items-center">
                <i class="fas fa-calculator mr-2"></i>
                Financial Summary
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Family Income</label>
                    <input type="number" step="0.01" name="house_total_income" id="house_total_income_final" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Expenses</label>
                    <input type="number" step="0.01" id="total_expenses_final" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Net Income</label>
                    <input type="number" step="0.01" name="house_net_income" id="house_net_income_final" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                </div>
            </div>
        </div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Family Members</label>
        <input type="number" id="number_of_family_members" class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-gray-600" readonly>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Per Person</label>
        <input type="number" step="0.01" id="per_capita_income" class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-gray-600" readonly>
    </div>
</div>

<!-- Optional: Hidden fields for form submission -->
<input type="hidden" name="number_of_family_members" id="number_of_family_members_hidden">
<input type="hidden" name="per_capita_income" id="per_capita_income_hidden">

        <!-- Remarks Selection -->
        <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
            <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                <i class="fas fa-tags mr-2"></i>
                Final Assessment
            </h4>
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Remarks <span class="text-red-500">*</span></label>
                    <select name="remarks" id="remarks" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required onchange="validateRemarks()">
                        <option value="">Select Remarks</option>
                        <option value="Poor">Poor</option>
                        <option value="Non Poor">Non Poor</option>
                        <option value="Ultra Poor">Ultra Poor</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-2">Please select a remark based on the financial assessment</p>
                </div>
            </div>
        </div>

        <!-- Compact Version -->
<div class="mb-6 p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
    <h4 class="font-bold text-gray-800 mb-3 flex items-center">
        <i class="fas fa-scale-balanced mr-2 text-purple-600"></i>
        Income Classification Guidelines
    </h4>
    
    <table class="w-full border-collapse text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-3 py-2 text-left font-semibold text-gray-700 border-b">Category</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700 border-b">Monthly Income</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700 border-b">Description</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b">
                <td class="px-3 py-2">
                    <span class="inline-flex items-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        <span class="font-medium text-green-700">Ultra Poor</span>
                    </span>
                </td>
                <td class="px-3 py-2 text-center font-mono text-red-600">Below ₱1,910</td>
                <td class="px-3 py-2 text-gray-600 text-xs">Cannot meet basic food needs</td>
            </tr>
            <tr class="border-b">
                <td class="px-3 py-2">
                    <span class="inline-flex items-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        <span class="font-medium text-yellow-700">Poor</span>
                    </span>
                </td>
                <td class="px-3 py-2 text-center font-mono text-yellow-600">₱1,910 – ₱2,759</td>
                <td class="px-3 py-2 text-gray-600 text-xs">Can meet food but not non-food needs</td>
            </tr>
            <tr>
                <td class="px-3 py-2">
                    <span class="inline-flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        <span class="font-medium text-red-700">Non-Poor</span>
                    </span>
                </td>
                <td class="px-3 py-2 text-center font-mono text-green-600">Above ₱2,759</td>
                <td class="px-3 py-2 text-gray-600 text-xs">Can meet both food and non-food needs</td>
            </tr>
        </tbody>
    </table>
</div>

        <!-- Submit Button -->
        <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
            <button type="button" onclick="showTab('health')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Previous
            </button>
            <div class="flex gap-3">
                <button type="button" onclick="saveAsDraft()" class=" hidden px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-medium transition-all duration-200">
                    Save as Draft
                </button>
                <button type="button" id="submitFormBtn" onclick="confirmSubmitForm()" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center" disabled>
                    <i class="fas fa-check mr-2"></i>
                    Submit Form
                </button>
            </div>
        </div>
    </div>
</div>
        </form>
    </div>
</div>

<!-- Review Modal for Reviewed Applicants (TOP LEVEL - NOT NESTED) -->
<div id="reviewModal" class="modal">
    <div class="modal-content">
        <div class="flex items-center text-2xl font-bold mb-6 text-gray-800">
            <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Logo" class="h-10 w-auto mr-3">
            Review Family Intake Sheet</span>
        </div>

        <div id="modalReviewContent">
            <!-- Content will be populated here -->
        </div>

        <div class="modal-actions">
            <button type="button" onclick="printScreeningPdf()" 
                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium flex items-center justify-center">
                <i class="fas fa-print mr-2"></i> Print PDF
            </button>
            <button type="button" onclick="closeReviewModal()" 
                class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium flex items-center justify-center">
                Close
            </button>
        </div>
    </div>
</div>


<script src="{{ asset('js/screening.js') }}"></script>
<script src="{{ asset('js/spinner.js') }}"></script>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifBell = document.getElementById('notifBell');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifCount = document.getElementById('notifCount');
    const markAllRead = document.getElementById('markAllRead');
    const notificationSound = document.getElementById('notificationSound');
    
    let notificationCheckInterval;
    let lastBadgeCount = {{ $badgeCount }};
    let isDropdownOpen = false;

    // Function to check for new notifications
    function checkNewNotifications() {
        fetch('{{ route("lydo_staff.notification_counts") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.badge_count > lastBadgeCount) {
                    // New notification arrived
                    playNotificationSound();
                    updateBadgeCount(data.badge_count);
                    lastBadgeCount = data.badge_count;
                    
                    // Show visual pulse effect
                    notifBell.classList.add('animate-pulse');
                    setTimeout(() => {
                        notifBell.classList.remove('animate-pulse');
                    }, 2000);
                } else if (data.badge_count !== lastBadgeCount) {
                    // Count changed (decreased)
                    updateBadgeCount(data.badge_count);
                    lastBadgeCount = data.badge_count;
                }
            })
            .catch(error => console.error('Error checking notifications:', error));
    }

    // Function to play notification sound
    function playNotificationSound() {
        if (notificationSound) {
            notificationSound.currentTime = 0;
            notificationSound.play().catch(e => console.log('Audio play failed:', e));
        }
    }

    // Function to update badge count
    function updateBadgeCount(count) {
        if (notifCount) {
            if (count > 0) {
                notifCount.textContent = count;
                notifCount.classList.remove('hidden');
                notifCount.classList.add('flex');
            } else {
                notifCount.classList.add('hidden');
                notifCount.classList.remove('flex');
            }
        }
    }

    // Toggle notification dropdown
    notifBell.addEventListener('click', function(e) {
        e.stopPropagation();
        isDropdownOpen = !notifDropdown.classList.contains('hidden');
        
        if (!isDropdownOpen) {
            // Opening dropdown - mark as read
            notifDropdown.classList.remove('hidden');
            markNotificationsAsRead();
            isDropdownOpen = true;
        } else {
            // Closing dropdown
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Mark all as read button
    if (markAllRead) {
        markAllRead.addEventListener('click', function(e) {
            e.stopPropagation();
            markNotificationsAsRead();
        });
    }

    // Function to mark notifications as read
    function markNotificationsAsRead() {
        fetch('{{ route("lydo_staff.mark_notifications_read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateBadgeCount(0);
                lastBadgeCount = 0;
                
                // Add visual feedback
                notifCount.classList.add('scale-0');
                setTimeout(() => {
                    notifCount.classList.remove('scale-0');
                }, 300);
            }
        })
        .catch(error => console.error('Error marking notifications as read:', error));
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!notifBell.contains(event.target) && !notifDropdown.contains(event.target)) {
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Close dropdown on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && isDropdownOpen) {
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Start checking for new notifications every 5 seconds
    notificationCheckInterval = setInterval(checkNewNotifications, 2000);

    // Initial check
    checkNewNotifications();

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(notificationCheckInterval);
    });

    // Handle page visibility changes (stop checking when tab is not active)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(notificationCheckInterval);
        } else {
            notificationCheckInterval = setInterval(checkNewNotifications, 5000);
            // Check immediately when returning to tab
            checkNewNotifications();
        }
    });
});

// Real-time updates using Laravel Echo and Pusher (if you have it set up)
document.addEventListener('DOMContentLoaded', function() {
    // If you have Laravel Echo configured for real-time updates
    @if(config('broadcasting.default') !== 'log')
    window.Echo.channel('lydo-staff-updates')
        .listen('.applicant.updated', (e) => {
            if (e.type === 'pending_initial') {
                // Trigger immediate notification check
                checkNewNotifications();
            }
        })
        .listen('.renewal.updated', (e) => {
            if (e.type === 'pending_renewals') {
                // Trigger immediate notification check
                checkNewNotifications();
            }
        });
    @endif
});

</script>
<script>
// Per Capita Income Calculation Functions
function calculatePerCapitaIncome() {
    // Get total net income
    const netIncome = parseFloat(document.getElementById('house_net_income').value) || 0;
    
    // Count number of family members (including head of family)
    const familyRows = document.querySelectorAll('#family_members_tbody tr');
    const numberOfFamilyMembers = familyRows.length + 1; // +1 for head of family
    
    // Calculate per capita income
    const perCapitaIncome = numberOfFamilyMembers > 0 ? (netIncome / numberOfFamilyMembers) : 0;
    
    // Update the readonly fields
    const numMembersField = document.getElementById('number_of_family_members');
    const perCapitaField = document.getElementById('per_capita_income');
    
    if (numMembersField) numMembersField.value = numberOfFamilyMembers;
    if (perCapitaField) perCapitaField.value = perCapitaIncome.toFixed(2);
    
    // Update hidden fields for form submission (if they exist)
    const numMembersHidden = document.getElementById('number_of_family_members_hidden');
    const perCapitaHidden = document.getElementById('per_capita_income_hidden');
    
    if (numMembersHidden) numMembersHidden.value = numberOfFamilyMembers;
    if (perCapitaHidden) perCapitaHidden.value = perCapitaIncome.toFixed(2);
}

// Modified calculateIncomes function to include per capita calculation
function calculateIncomes() {
    console.log('Calculating incomes...');
    
    // 1. Calculate Total Family Income from Family Members
    let totalFamilyIncome = 0;
    const incomeInputs = document.querySelectorAll('input[name="family_member_income[]"]');
    incomeInputs.forEach(input => {
        const incomeValue = parseFloat(input.value) || 0;
        totalFamilyIncome += incomeValue;
    });

    // 2. Get Other Income and add to Total Income
    const otherIncome = parseFloat(document.getElementById('other_income').value) || 0;
    
    // 3. Calculate Total Income (Family Members Income + Other Income)
    const houseTotalIncome = totalFamilyIncome + otherIncome;
    
    // Set total income
    document.getElementById('house_total_income').value = houseTotalIncome.toFixed(2);

    // 4. Calculate Total Expenses
    const houseRent = parseFloat(document.getElementById('house_rent').value) || 0;
    const lotRent = parseFloat(document.getElementById('lot_rent').value) || 0;
    const houseWater = parseFloat(document.getElementById('house_water').value) || 0;
    const houseElectric = parseFloat(document.getElementById('house_electric').value) || 0;
    
    // Total expenses (house rent + lot rent + water + electric)
    const totalExpenses = houseRent + lotRent + houseWater + houseElectric;
    
    // 5. Calculate Net Income (Total Income - Total Expenses)
    const netIncome = houseTotalIncome - totalExpenses;
    
    document.getElementById('house_net_income').value = netIncome.toFixed(2);

    // 6. Calculate Per Capita Income
    calculatePerCapitaIncome();

    // 7. Enable the Next button (removed disabled attribute)
    const additionalNextBtn = document.getElementById('additional-next-btn');
    if (additionalNextBtn) {
        additionalNextBtn.disabled = false;
    }
}

// Modified addFamilyMemberRow function to trigger income calculation when adding members
function addFamilyMemberRow() {
    const tbody = document.getElementById('family_members_tbody');

    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="border px-2 py-1">
            <input type="text" name="family_member_name[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="Full Name" oninput="calculateIncomes()">
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_relation[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
          <option value="">Select</option>
          <option value="Spouse">Spouse</option>
          <option value="Son">Son</option>
          <option value="Daughter">Daughter</option>
          <option value="Father">Father</option>
          <option value="Mother">Mother</option>
          <option value="Brother">Brother</option>
          <option value="Sister">Sister</option>
          <option value="Grandchild">Grandchild</option>
          <option value="Other">Other</option>
            </select>
        </td>
        <td class="border px-2 py-1">
            <input type="date" name="family_member_birthdate[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" onchange="calculateAge(this)">
        </td>
        <td class="border px-2 py-1">
            <input type="number" name="family_member_age[]" class="w-full border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-600" placeholder="Age" readonly>
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_sex[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                <option value="">Select Sex</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_civil_status[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
          <option value="">Select</option>
          <option value="Single">Single</option>
          <option value="Married">Married</option>
          <option value="Widowed">Widowed</option>
          <option value="Divorced">Divorced</option>
          <option value="Separated">Separated</option>
            </select>
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_education[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
          <option value="">Select</option>
          <option value="None">None</option>
          <option value="Elementary">Elementary</option>
          <option value="High School">High School</option>
          <option value="College">College</option>
          <option value="Vocational">Vocational</option>
          <option value="Graduate">Graduate</option>
            </select>
        </td>
        <td class="border px-2 py-1">
            <input type="text" name="family_member_occupation[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="Occupation">
        </td>
        <td class="border px-2 py-1">
            <input type="number" step="0.01" name="family_member_income[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="0.00" oninput="calculateIncomes()">
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_remarks[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                     <option value="">Select</option>
          <option value="CIC">CIC</option>
          <option value="OSY">OSY</option>
          <option value="SP">SP</option>
          <option value="PWD">PWD</option>
          <option value="SC">SC</option>
          <option value="None">None</option>
          <option value="Lactating Mother">Lactating Mother</option>
          <option value="Pregnant Mother">Pregnant Mother</option>
            </select>
        </td>
        <td class="border px-2 py-1 text-center">
            <button type="button" onclick="this.parentElement.parentElement.remove(); calculateIncomes();" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors duration-200">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    
    // Trigger calculation after adding new row
    calculateIncomes();
}

// Function to calculate age from birthdate
function calculateAge(birthdateInput) {
    const birthdate = new Date(birthdateInput.value);
    const today = new Date();

    if (isNaN(birthdate)) {
        return;
    }

    let age = today.getFullYear() - birthdate.getFullYear();
    const monthDiff = today.getMonth() - birthdate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
        age--;
    }

    // Find the age input in the same row
    const row = birthdateInput.closest('tr');
    const ageInput = row.querySelector('input[name="family_member_age[]"]');
    if (ageInput) {
        ageInput.value = age;
    }
}

// Initialize per capita calculation when modal opens
function initializePerCapitaCalculation() {
    // Recalculate when modal opens
    setTimeout(calculateIncomes, 100);
}

// Add event listeners for income-related fields
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to existing income fields
    const incomeFields = [
        'other_income', 'house_rent', 'lot_rent', 'house_water', 'house_electric'
    ];
    
    incomeFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', calculateIncomes);
        }
    });
    
    // Also add to house and lot selection changes
    const houseSelect = document.getElementById('house_house');
    const lotSelect = document.getElementById('house_lot');
    
    if (houseSelect) houseSelect.addEventListener('change', calculateIncomes);
    if (lotSelect) lotSelect.addEventListener('change', calculateIncomes);
});
</script>

<script src="{{ asset('js/screening_refresh.js') }}"></script>
<script src="{{ asset('js/remarks-classification.js') }}"></script>
</body>
</html>