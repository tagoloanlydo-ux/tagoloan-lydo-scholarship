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
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">

    <style>
        .tab {
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: white;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .tab.active {
            background-color: #7c3aed;
            color: white;
            border-color: #7c3aed;
        }

        .tab:hover:not(.active) {
            background-color: #f3f4f6;
        }

        #listView table {
            width: 100%;
            border-collapse: collapse;
        }

        #listView table th,
        #listView table td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            text-align: center;
        }

        /* Professional view mode styling */
        .view-mode input[readonly],
        .view-mode select[disabled],
        .view-mode textarea[readonly] {
            background-color: transparent !important;
            border: none !important;
            color: #374151 !important;
            font-weight: 500 !important;
            padding: 0 !important;
            cursor: default !important;
        }

        .view-mode input[readonly]:focus,
        .view-mode select[disabled]:focus,
        .view-mode textarea[readonly]:focus {
            outline: none !important;
        }

        /* Intake Sheet Layout Styling */
        .intake-header {
            text-align: center;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #333;
            padding-bottom: 1rem;
        }

        .intake-section {
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .intake-section-title {
            font-weight: bold;
            margin-bottom: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }

        .intake-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }

        .intake-table th,
        .intake-table td {
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            text-align: left;
        }

        .intake-table th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .intake-signature-area {
            border-top: 1px solid #333;
            margin-top: 2rem;
            padding-top: 1rem;
        }

        .intake-signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 2.5rem;
            text-align: center;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="dashboard-grid">
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <!-- Navbar -->
                    <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                @php
                    $badgeCount = ($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0) ? $notifications->where('initial_screening', 'Approved')->count() : 0;
                @endphp
                <div class="relative">
                    <button id="notifBell" class="relative focus:outline-none">
                        <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                        @if($badgeCount > 0)
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $badgeCount }}
                            </span>
                        @endif
                    </button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b font-semibold text-gray-700">Notifications</div>
                        <ul class="max-h-60 overflow-y-auto"> @forelse($notifications as $notif) <li class="px-4 py-2 hover:bg-gray-50 text-sm border-b"> @if($notif->initial_screening == 'Approved') <p class="text-green-600 font-medium"> ✅ {{ $notif->name }} passed initial screening </p> @elseif($notif->status == 'Renewed') <p class="text-blue-600 font-medium"> 🔄 {{ $notif->name }} submitted renewal </p> @endif <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                </p>
                            </li> @empty <li class="px-4 py-3 text-gray-500 text-sm">No new notifications</li> @endforelse </ul>
                    </div>
                </div>
                @if($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0)
                <script>
                    if (localStorage.getItem('notificationsViewed') !== 'true') {
                        const audio = new Audio('/notification/blade.wav');
                        audio.play().catch(e => console.log('Audio play failed', e));
                    }
                </script>
                @endif
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        if (localStorage.getItem('notificationsViewed') === 'true') {
                            let notifCount = document.getElementById("notifCount");
                            if (notifCount) {
                                notifCount.style.display = 'none';
                            }
                        }
                    });
                </script>
                <script>
                    document.getElementById("notifBell").addEventListener("click", function() {
                        document.getElementById("notifDropdown").classList.toggle("hidden");
                        localStorage.setItem('notificationsViewed', 'true');
                        let notifCount = document.getElementById("notifCount");
                        if (notifCount) {
                            notifCount.innerText = '0';
                        }
                    });
                </script>

            </div>
        </header>
        <div class="flex flex-1 overflow-hidden"> 
            <div class="w-16 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
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
                                    <span class="ml-4 hidden md:block text-lg">Screening</span>
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
                         <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                            <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                            <span class="hidden md:block text-red-600">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="flex-1 main-content-area p-4 md:p-5 text-[16px]">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-3xl font-bold text-gray-800">Screening Applicants</h5>
                    </div>
                    <!-- ✅ Applicants -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex gap-2">
                            <div onclick="showTable()" class="tab active" id="tab-screening">
                                <i class="fas fa-table mr-1"></i> Assign Intake Sheet
                            </div>
                            <div onclick="showList()" class="tab" id="tab-reviewed">
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
                            <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white uppercase text-sm">
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
                                    <td class="px-4 border border-gray-200 py-2 text-center">Firstname: {{ $app->applicant_fname }} Lastname: {{ $app->applicant_lname }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_course }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_school_name }}</td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <button
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
                                            data-gender="{{ $app->applicant_gender }}"
                                            data-pob="{{ $app->applicant_pob }}"
                                            onclick="openEditRemarksModal(this)">
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
                        <div class="mt-4">
                            {{ $tableApplicants->appends(request()->query())->links() }}
                        </div>
                    </div>

                    <!-- Reviewed Applicants Tab -->
                    <div id="listView" class="overflow-x-auto hidden">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-green-50 p-3 rounded-lg border border-green-200">
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
                            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Remarks</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listApplicants as $index => $app)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_course }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_school_name }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->remarks == 'Poor') bg-red-100 text-red-800
                                            @elseif($app->remarks == 'Non Poor') bg-yellow-100 text-yellow-800
                                            @elseif($app->remarks == 'Ultra Poor') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->remarks }}
                                        </span>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->initial_screening == 'Reviewed') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->initial_screening }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <button
                                            title="View Intake Sheet"
                                            class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow"
                                            data-id="{{ $app->application_personnel_id }}"
                                            onclick="openViewIntakeSheetModal(this)">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 border border-gray-200 text-gray-500">No reviewed applicants.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $listApplicants->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Intake Sheet Modal -->
        <div id="editRemarksModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto">
            <div class="bg-white w-full max-w-6xl rounded-2xl shadow-2xl p-6 max-h-screen overflow-y-auto">
                <div class="flex items-center text-xl font-semibold mb-4">
                    <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Logo" class="h-8 w-auto mr-2">
                    Family Intake Sheet
                </div>

                <!-- Tab Navigation -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button type="button" id="tab-family" class="tab-button active px-4 py-2 text-sm font-medium text-violet-600 border-b-2 border-violet-600" onclick="showTab('family')">Family Details</button>
                    <button type="button" id="tab-family-members" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showTab('family-members')">Family Members</button>
                    <button type="button" id="tab-additional" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showTab('additional')">Additional Info</button>
                    <button type="button" id="tab-social-service" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showTab('social-service')">Social Service Records</button>
                    <button type="button" id="tab-health" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showTab('health')">Health & Signatures</button>
                </div>

                <form id="updateRemarksForm" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="remarks_id">
                    <input type="hidden" id="modal_mode" value="edit">

                    <!-- Family Details Tab -->
                    <div id="tab-family-content" class="tab-content">
                        <!-- Head of Family Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Head of Family</h3>
                            <!-- Row 1: 4Ps and IP No. -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">4Ps</label>
                                    <select name="head_4ps" id="head_4ps" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">IP No.</label>
                                    <input type="text" name="head_ipno" id="head_ipno" class="mt-1 block w-full border rounded-lg p-2" placeholder="Optional">
                                </div>
                            </div>
                            <!-- Row 2: Applicant Name Fields -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input type="text" name="applicant_fname" id="applicant_fname" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                                    <input type="text" name="applicant_mname" id="applicant_mname" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input type="text" name="applicant_lname" id="applicant_lname" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Suffix</label>
                                    <input type="text" name="applicant_suffix" id="applicant_suffix" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                            </div>
                            <!-- Row 3: Address, Zone, Barangay -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" name="head_address" id="head_address" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Zone</label>
                                    <input type="text" name="head_zone" id="head_zone" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Barangay</label>
                                    <input type="text" name="head_barangay" id="head_barangay" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                            </div>
                            <!-- Row 4: Date of Birth, Place of Birth, Gender -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input type="date" name="head_dob" id="head_dob" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Place of Birth</label>
                                    <input type="text" name="head_pob" id="head_pob" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select name="applicant_gender" id="applicant_gender" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Row 5: Education, Occupation, Religion -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Education</label>
                                    <select name="head_educ" id="head_educ" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select Education</option>
                                        <option value="Elementary">Elementary</option>
                                        <option value="High School">High School</option>
                                        <option value="Vocational">Vocational</option>
                                        <option value="College">College</option>
                                        <option value="Post Graduate">Post Graduate</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Occupation</label>
                                    <select name="head_occ" id="head_occ" class="mt-1 block w-full border rounded-lg p-2">
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
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Religion</label>
                                    <select name="head_religion" id="head_religion" class="mt-1 block w-full border rounded-lg p-2">
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
                            <!-- Row 6: Serial Number, Location -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                                    <input type="text" name="serial_number" id="serial_number" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100" readonly>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Location</label>
                                    <input type="text" name="location" id="location" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-end mt-4">
                            <button type="button" onclick="showTab('family-members')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                        </div>
                    </div>

                    <!-- Family Members Tab -->
                    <div id="tab-family-members-content" class="tab-content hidden">
                        <!-- Family Members Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Family Members</h3>
                            <p class="text-sm text-gray-600 mb-3">Please fill up all required fields in the family members table. Remarks should be selected based on the categories listed below.</p>
                            <div class="overflow-x-auto">
                                <table id="family_members_table" class="min-w-full text-sm thin-border">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border px-2 py-1">Name</th>
                                            <th class="border px-2 py-1">Relation</th>
                                            <th class="border px-2 py-1">Birthdate</th>
                                            <th class="border px-2 py-1">Age</th>
                                            <th class="border px-2 py-1">Sex</th>
                                            <th class="border px-2 py-1">Civil Status</th>
                                            <th class="border px-2 py-1">Educational Attainment</th>
                                            <th class="border px-2 py-1">Occupation</th>
                                            <th class="border px-2 py-1">Monthly Income</th>
                                            <th class="border px-2 py-1">Remarks</th>
                                            <th class="border px-2 py-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="family_members_tbody">
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" onclick="addFamilyMemberRow()" class="bg-purple-600 text-white px-4 py-2 rounded">+ Add Member</button>
                            <textarea name="family_members" id="family_members" class="hidden"></textarea>
                            <div class="mt-4">
                                <h4 class="font-semibold mb-3 text-gray-800">Remarks Categories:</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Out of School Youth (OSY)</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Solo Parent (SP)</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Person with Disability (PWD)</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Senior Citizen (SC)</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Lactating Mother</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Pregnant Mother</div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('family')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="button" onclick="showTab('additional')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                        </div>
                    </div>

                    <!-- Additional Info Tab -->
                    <div id="tab-additional-content" class="tab-content hidden">
                        <!-- Household Info Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Household Information</h3>
                            <!-- Row 1: Other Income and Total Income -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Other Income</label>
                                    <input type="number" step="0.01" name="other_income" id="other_income" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Total Income</label>
                                    <input type="number" step="0.01" name="house_total_income" id="house_total_income" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100" readonly>
                                </div>
                            </div>
                            <!-- Row 2: House, Lot, Water, Electric -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">House</label>
                                    <select name="house_house" id="house_house" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select</option>
                                        <option value="Owned">Owned</option>
                                        <option value="Rent">Rent</option>
                                    </select>
                                    <div id="house_value_group" style="display: none;" class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700">House Value</label>
                                        <input type="number" step="0.01" name="house_house_value" id="house_house_value" class="mt-1 block w-full border rounded-lg p-2">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Lot</label>
                                    <select name="house_lot" id="house_lot" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select</option>
                                        <option value="Owned">Owned</option>
                                        <option value="Rent">Rent</option>
                                    </select>
                                    <div id="lot_value_group" style="display: none;" class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700">Lot Value</label>
                                        <input type="number" step="0.01" name="house_lot_value" id="house_lot_value" class="mt-1 block w-full border rounded-lg p-2">
                                    </div>
                                    <div id="lot_rent_group" style="display: none;" class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700">Lot Rent</label>
                                        <input type="number" step="0.01" name="house_lot_rent" id="house_lot_rent" class="mt-1 block w-full border rounded-lg p-2">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Water</label>
                                    <input type="number" step="0.01" name="house_water" id="house_water" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Electric</label>
                                    <input type="number" step="0.01" name="house_electric" id="house_electric" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                            </div>
                            <!-- Row 3: Net Income and Remarks -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Net Income</label>
                                    <input type="number" step="0.01" name="house_net_income" id="house_net_income" class="mt-1 block w-full border rounded-lg p-2" readonly>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Remarks</label>
                                    <select name="remarks" id="remarks" class="mt-1 block w-full border rounded-lg p-2" required>
                                        <option value="">Select Remarks</option>
                                        <option value="Poor">Poor</option>
                                        <option value="Non Poor">Non Poor</option>
                                        <option value="Ultra Poor">Ultra Poor</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('family-members')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="button" onclick="showTab('social-service')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                        </div>
                    </div>

                    <!-- Social Service Records Tab -->
                    <div id="tab-social-service-content" class="tab-content hidden">
                        <!-- Social Service Records Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Social Service Records</h3>
                            <table id="rv_service_records_table" class="data-table w-full border border-gray-300 mt-1">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-gray-300 px-2 py-1 text-left">Date</th>
                                        <th class="border border-gray-300 px-2 py-1 text-left">Problem/Need</th>
                                        <th class="border border-gray-300 px-2 py-1 text-left">Action/Assistance Given</th>
                                        <th class="border border-gray-300 px-2 py-1 text-left">Remarks</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="rv_service_records_tbody">
                                </tbody>
                            </table>
                            <button type="button" onclick="addRvServiceRecordRow()" class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Add Record</button>
                            <textarea name="rv_service_records" id="rv_service_records" class="hidden"></textarea>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('additional')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="button" onclick="showTab('health')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                        </div>
                    </div>

                    <!-- Health & Signatures Tab -->
                    <div id="tab-health-content" class="tab-content hidden">
                        <!-- Health & Signatures Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Health & Signatures</h3>
                            <!-- Worker Name and Officer Name in one line -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Worker Name</label>
                                    <input type="text" name="worker_name" id="worker_name" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Officer Name</label>
                                    <input type="text" name="officer_name" id="officer_name" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                            </div>
                            <!-- Date Entry and Signature Client -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Date Entry</label>
                                    <input type="date" name="date_entry" id="date_entry" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Signature Client</label>
                                    <input type="text" name="signature_client" id="signature_client" class="mt-1 block w-full border rounded-lg p-2" readonly>
                                </div>
                            </div>
                            <!-- Signature Worker and Signature Officer with modals -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Signature Worker</label>
                                    <button type="button" onclick="openSignatureModal('worker')" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100 text-left hover:bg-gray-200">Click to Sign</button>
                                    <input type="hidden" name="signature_worker" id="signature_worker">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Signature Officer</label>
                                    <button type="button" onclick="openSignatureModal('officer')" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100 text-left hover:bg-gray-200">Click to Sign</button>
                                    <input type="hidden" name="signature_officer" id="signature_officer">
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('social-service')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Intake Sheet Modal for Reviewed Applicants -->
        <div id="viewIntakeSheetModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto">
            <div class="bg-white w-full max-w-6xl rounded-2xl shadow-2xl p-6 max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between text-xl font-semibold mb-4">
                    <div class="flex items-center">
                        <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Logo" class="h-8 w-auto mr-2">
                        Family Intake Sheet - View Mode
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="printIntakeSheet()" class="text-gray-500 hover:text-gray-700 text-xl" title="Print Intake Sheet">
                            <i class="fas fa-print"></i>
                        </button>
                        <button onclick="closeViewIntakeSheetModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button type="button" id="view-tab-family" class="view-tab-button active px-4 py-2 text-sm font-medium text-violet-600 border-b-2 border-violet-600" onclick="showViewTab('family')">Family Details</button>
                    <button type="button" id="view-tab-family-members" class="view-tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showViewTab('family-members')">Family Members</button>
                    <button type="button" id="view-tab-additional" class="view-tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showViewTab('additional')">Additional Info</button>
                    <button type="button" id="view-tab-social-service" class="view-tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showViewTab('social-service')">Social Service Records</button>
                    <button type="button" id="view-tab-health" class="view-tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showViewTab('health')">Health & Signatures</button>
                </div>

                <div id="view-tab-family-content" class="view-tab-content">
                    <!-- Head of Family Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Head of Family</h3>
                        <!-- Row 1: 4Ps and IP No. -->
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">4Ps</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_4ps"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">IP No.</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_ipno"></div>
                            </div>
                        </div>
                        <!-- Row 2: Applicant Name Fields -->
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">First Name</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-applicant_fname"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-applicant_mname"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-applicant_lname"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Suffix</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-applicant_suffix"></div>
                            </div>
                        </div>
                        <!-- Row 3: Address, Zone, Barangay -->
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_address"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Zone</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_zone"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Barangay</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_barangay"></div>
                            </div>
                        </div>
                        <!-- Row 4: Date of Birth, Place of Birth, Gender -->
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_dob"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Place of Birth</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_pob"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Gender</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-applicant_gender"></div>
                            </div>
                        </div>
                        <!-- Row 5: Education, Occupation, Religion -->
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Education</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_educ"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Occupation</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_occ"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Religion</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-head_religion"></div>
                            </div>
                        </div>
                        <!-- Row 6: Serial Number, Location -->
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-serial_number"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Location</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-location"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="showViewTab('family-members')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                    </div>
                </div>

                <!-- Family Members Tab -->
                <div id="view-tab-family-members-content" class="view-tab-content hidden">
                    <!-- Family Members Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Family Members</h3>
                        <div class="overflow-x-auto">
                            <table id="view_family_members_table" class="min-w-full text-sm thin-border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-2 py-1">Name</th>
                                        <th class="border px-2 py-1">Relation</th>
                                        <th class="border px-2 py-1">Birthdate</th>
                                        <th class="border px-2 py-1">Age</th>
                                        <th class="border px-2 py-1">Sex</th>
                                        <th class="border px-2 py-1">Civil Status</th>
                                        <th class="border px-2 py-1">Educational Attainment</th>
                                        <th class="border px-2 py-1">Occupation</th>
                                        <th class="border px-2 py-1">Monthly Income</th>
                                        <th class="border px-2 py-1">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="view_family_members_tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-4">
                        <button type="button" onclick="showViewTab('family')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                        <button type="button" onclick="showViewTab('additional')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                    </div>
                </div>

                <!-- Additional Info Tab -->
                <div id="view-tab-additional-content" class="view-tab-content hidden">
                    <!-- Household Info Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Household Information</h3>
                        <!-- Row 1: Other Income and Total Income -->
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Other Income</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-other_income"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Total Income</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-house_total_income"></div>
                            </div>
                        </div>
                        <!-- Row 2: House, Lot, Water, Electric -->
                        <div class="flex gap-4 mb-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">House</label>
                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-house_house"></div>
                <div id="view-house_value_group" style="display: none;" class="mt-2">
                    <label class="block text-sm font-medium text-gray-700">House Value</label>
                    <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-house_house_value"></div>
                </div>
            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Lot</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-house_lot"></div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Water</label>
                                <div class="mt-1 block w-full border rounded-lg p-2 bg-gray-50 view-field" id="view-house_water"></div>
                            </div>
                            <div class="flex-1">

                <!-- Tab Navigation -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button type="button" id="tab-family" class="tab-button active px-4 py-2 text-sm font-medium text-violet-600 border-b-2 border-violet-600" onclick="showTab('family')">Family Details</button>
                    <button type="button" id="tab-family-members" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showTab('family-members')">Family Members</button>
                    <button type="button" id="tab-additional" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showTab('additional')">Additional Info</button>
                    <button type="button" id="tab-social-service" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showTab('social-service')">Social Service Records</button>
                    <button type="button" id="tab-health" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600" onclick="showTab('health')">Health & Signatures</button>
                </div>

                <form id="updateRemarksForm" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="remarks_id">
                    <input type="hidden" id="modal_mode" value="edit">

                    <!-- Family Details Tab -->
                    <div id="tab-family-content" class="tab-content">
                        <!-- Head of Family Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Head of Family</h3>
                            <!-- Row 1: 4Ps and IP No. -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">4Ps</label>
                                    <select name="head_4ps" id="head_4ps" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">IP No.</label>
                                    <input type="text" name="head_ipno" id="head_ipno" class="mt-1 block w-full border rounded-lg p-2" placeholder="Optional">
                                </div>
                            </div>
                            <!-- Row 2: Applicant Name Fields -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">First Name</label>
                                    <input type="text" name="applicant_fname" id="applicant_fname" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                                    <input type="text" name="applicant_mname" id="applicant_mname" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                    <input type="text" name="applicant_lname" id="applicant_lname" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Suffix</label>
                                    <input type="text" name="applicant_suffix" id="applicant_suffix" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                            </div>
                            <!-- Row 3: Address, Zone, Barangay -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Address</label>
                                    <input type="text" name="head_address" id="head_address" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Zone</label>
                                    <input type="text" name="head_zone" id="head_zone" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Barangay</label>
                                    <input type="text" name="head_barangay" id="head_barangay" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                            </div>
                            <!-- Row 4: Date of Birth, Place of Birth, Gender -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <input type="date" name="head_dob" id="head_dob" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Place of Birth</label>
                                    <input type="text" name="head_pob" id="head_pob" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select name="applicant_gender" id="applicant_gender" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Row 5: Education, Occupation, Religion -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Education</label>
                                    <select name="head_educ" id="head_educ" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select Education</option>
                                        <option value="Elementary">Elementary</option>
                                        <option value="High School">High School</option>
                                        <option value="Vocational">Vocational</option>
                                        <option value="College">College</option>
                                        <option value="Post Graduate">Post Graduate</option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Occupation</label>
                                    <select name="head_occ" id="head_occ" class="mt-1 block w-full border rounded-lg p-2">
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
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Religion</label>
                                    <select name="head_religion" id="head_religion" class="mt-1 block w-full border rounded-lg p-2">
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
                            <!-- Row 6: Serial Number, Location -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                                    <input type="text" name="serial_number" id="serial_number" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100" readonly>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Location</label>
                                    <input type="text" name="location" id="location" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-end mt-4">
                            <button type="button" onclick="showTab('family-members')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                        </div>
                    </div>

                    <!-- Family Members Tab -->
                    <div id="tab-family-members-content" class="tab-content hidden">
                        <!-- Family Members Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Family Members</h3>
                            <p class="text-sm text-gray-600 mb-3">Please fill up all required fields in the family members table. Remarks should be selected based on the categories listed below.</p>
                            <div class="overflow-x-auto">
                                <table id="family_members_table" class="min-w-full text-sm thin-border">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border px-2 py-1">Name</th>
                                            <th class="border px-2 py-1">Relation</th>
                                            <th class="border px-2 py-1">Birthdate</th>
                                            <th class="border px-2 py-1">Age</th>
                                            <th class="border px-2 py-1">Sex</th>
                                            <th class="border px-2 py-1">Civil Status</th>
                                            <th class="border px-2 py-1">Educational Attainment</th>
                                            <th class="border px-2 py-1">Occupation</th>
                                            <th class="border px-2 py-1">Monthly Income</th>
                                            <th class="border px-2 py-1">Remarks</th>
                                            <th class="border px-2 py-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="family_members_tbody">
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" onclick="addFamilyMemberRow()" class="bg-purple-600 text-white px-4 py-2 rounded">+ Add Member</button>
                            <textarea name="family_members" id="family_members" class="hidden"></textarea>
                            <div class="mt-4">
                                <h4 class="font-semibold mb-3 text-gray-800">Remarks Categories:</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Out of School Youth (OSY)</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Solo Parent (SP)</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Person with Disability (PWD)</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Senior Citizen (SC)</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Lactating Mother</div>
                                    <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Pregnant Mother</div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('family')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="button" onclick="showTab('additional')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                        </div>
                    </div>

                    <!-- Additional Info Tab -->
                    <div id="tab-additional-content" class="tab-content hidden">
                        <!-- Household Info Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Household Information</h3>
                            <!-- Row 1: Other Income and Total Income -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Other Income</label>
                                    <input type="number" step="0.01" name="other_income" id="other_income" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Total Income</label>
                                    <input type="number" step="0.01" name="house_total_income" id="house_total_income" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100" readonly>
                                </div>
                            </div>
                            <!-- Row 2: House, Lot, Water, Electric -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">House</label>
                                    <select name="house_house" id="house_house" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select</option>
                                        <option value="Owned">Owned</option>
                                        <option value="Rent">Rent</option>
                                    </select>
                                    <div id="house_value_group" style="display: none;" class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700">House Value</label>
                                        <input type="number" step="0.01" name="house_house_value" id="house_house_value" class="mt-1 block w-full border rounded-lg p-2">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Lot</label>
                                    <select name="house_lot" id="house_lot" class="mt-1 block w-full border rounded-lg p-2">
                                        <option value="">Select</option>
                                        <option value="Owned">Owned</option>
                                        <option value="Rent">Rent</option>
                                    </select>
                                    <div id="lot_value_group" style="display: none;" class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700">Lot Value</label>
                                        <input type="number" step="0.01" name="house_lot_value" id="house_lot_value" class="mt-1 block w-full border rounded-lg p-2">
                                    </div>
                                    <div id="lot_rent_group" style="display: none;" class="mt-2">
                                        <label class="block text-sm font-medium text-gray-700">Lot Rent</label>
                                        <input type="number" step="0.01" name="house_lot_rent" id="house_lot_rent" class="mt-1 block w-full border rounded-lg p-2">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Water</label>
                                    <input type="number" step="0.01" name="house_water" id="house_water" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Electric</label>
                                    <input type="number" step="0.01" name="house_electric" id="house_electric" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                            </div>
                            <!-- Row 3: Net Income and Remarks -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Net Income</label>
                                    <input type="number" step="0.01" name="house_net_income" id="house_net_income" class="mt-1 block w-full border rounded-lg p-2" readonly>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Remarks</label>
                                    <select name="remarks" id="remarks" class="mt-1 block w-full border rounded-lg p-2" required>
                                        <option value="">Select Remarks</option>
                                        <option value="Poor">Poor</option>
                                        <option value="Non Poor">Non Poor</option>
                                        <option value="Ultra Poor">Ultra Poor</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('family-members')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="button" onclick="showTab('social-service')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                        </div>
                    </div>

                    <!-- Social Service Records Tab -->
                    <div id="tab-social-service-content" class="tab-content hidden">
                        <!-- Social Service Records Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Social Service Records</h3>
                            <table id="rv_service_records_table" class="data-table w-full border border-gray-300 mt-1">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-gray-300 px-2 py-1 text-left">Date</th>
                                        <th class="border border-gray-300 px-2 py-1 text-left">Problem/Need</th>
                                        <th class="border border-gray-300 px-2 py-1 text-left">Action/Assistance Given</th>
                                        <th class="border border-gray-300 px-2 py-1 text-left">Remarks</th>
                                        <th class="border border-gray-300 px-2 py-1 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="rv_service_records_tbody">
                                </tbody>
                            </table>
                            <button type="button" onclick="addRvServiceRecordRow()" class="mt-2 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Add Record</button>
                            <textarea name="rv_service_records" id="rv_service_records" class="hidden"></textarea>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('additional')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="button" onclick="showTab('health')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Next</button>
                        </div>
                    </div>

                    <!-- Health & Signatures Tab -->
                    <div id="tab-health-content" class="tab-content hidden">
                        <!-- Health & Signatures Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Health & Signatures</h3>
                            <!-- Worker Name and Officer Name in one line -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Worker Name</label>
                                    <input type="text" name="worker_name" id="worker_name" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Officer Name</label>
                                    <input type="text" name="officer_name" id="officer_name" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                            </div>
                            <!-- Date Entry and Signature Client -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Date Entry</label>
                                    <input type="date" name="date_entry" id="date_entry" class="mt-1 block w-full border rounded-lg p-2">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Signature Client</label>
                                    <input type="text" name="signature_client" id="signature_client" class="mt-1 block w-full border rounded-lg p-2" readonly>
                                </div>
                            </div>
                            <!-- Signature Worker and Signature Officer with modals -->
                            <div class="flex gap-4 mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Signature Worker</label>
                                    <button type="button" onclick="openSignatureModal('worker')" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100 text-left hover:bg-gray-200">Click to Sign</button>
                                    <input type="hidden" name="signature_worker" id="signature_worker">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Signature Officer</label>
                                    <button type="button" onclick="openSignatureModal('officer')" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100 text-left hover:bg-gray-200">Click to Sign</button>
                                    <input type="hidden" name="signature_officer" id="signature_officer">
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('social-service')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Tab switching functionality for main view
            function showTable() {
                document.getElementById('tableView').classList.remove('hidden');
                document.getElementById('listView').classList.add('hidden');
                document.getElementById('tab-screening').classList.add('active');
                document.getElementById('tab-reviewed').classList.remove('active');
                filterTable(); // Apply current filters to the active view
            }

            function showList() {
                document.getElementById('tableView').classList.add('hidden');
                document.getElementById('listView').classList.remove('hidden');
                document.getElementById('tab-screening').classList.remove('active');
                document.getElementById('tab-reviewed').classList.add('active');
                filterList(); // Apply current filters to the active view
            }

            // Tab switching functionality for modal
            function showTab(tabName) {
                // Hide all tab contents
                const tabContents = document.querySelectorAll('.tab-content');
                tabContents.forEach(content => content.classList.add('hidden'));

                // Remove active class from all tab buttons
                const tabButtons = document.querySelectorAll('.tab-button');
                tabButtons.forEach(button => {
                    button.classList.remove('active');
                    button.classList.remove('text-violet-600');
                    button.classList.add('text-gray-500');
                    button.classList.remove('border-b-2', 'border-violet-600');
                });

                // Show the selected tab content
                document.getElementById('tab-' + tabName + '-content').classList.remove('hidden');

                // Add active class to the selected tab button
                document.getElementById('tab-' + tabName).classList.add('active', 'text-violet-600', 'border-b-2', 'border-violet-600');
                document.getElementById('tab-' + tabName).classList.remove('text-gray-500');
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Add confirmation for modal form submit
                const modalForm = document.getElementById('updateRemarksForm');
                if (modalForm) {
                    modalForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const selectedRemarks = document.getElementById('remarks').value;
                        if (!selectedRemarks) {
                            Swal.fire('Error', 'Please select a remark before updating.', 'error');
                            return;
                        }

                        const id = document.getElementById('remarks_id').value;
                        modalForm.action = "/lydo_staff/update-intake-sheet/" + id;

                        Swal.fire({
                            title: 'Confirm Intake Sheet Update',
                            text: `Are you sure you want to update the intake sheet?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, update it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                modalForm.submit();
                            }
                        });
                    });
                }

                // Add event listeners for real-time filtering
                document.getElementById('nameSearch').addEventListener('input', filterTable);
                document.getElementById('barangayFilter').addEventListener('change', filterTable);
                document.getElementById('listNameSearch').addEventListener('input', filterList);
                document.getElementById('listBarangayFilter').addEventListener('change', filterList);
            });

            function openEditRemarksModal(button) {
                let id = button.getAttribute("data-id");
                let fname = button.getAttribute("data-fname");
                let mname = button.getAttribute("data-mname");
                let lname = button.getAttribute("data-lname");
                let suffix = button.getAttribute("data-suffix");
                let gender = button.getAttribute("data-gender");

                document.getElementById("remarks_id").value = id;
                document.getElementById("modal_mode").value = "edit";

                // Populate applicant name fields from button data
                document.getElementById("applicant_fname").value = fname || '';
                document.getElementById("applicant_mname").value = mname || '';
                document.getElementById("applicant_lname").value = lname || '';
                document.getElementById("applicant_suffix").value = suffix || '';
                document.getElementById("applicant_gender").value = gender || '';

                let form = document.getElementById('updateRemarksForm');
                form.action = "/lydo_staff/update-intake-sheet/" + id;

                // Fetch existing intake sheet data and populate the form
                fetch(`/lydo_staff/intake-sheet/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            // Populate Head of Family fields
                            document.getElementById("head_4ps").value = data.head_4ps || '';
                            document.getElementById("head_ipno").value = data.head_ipno || '';
                            document.getElementById("head_address").value = data.head_address || '';
                            document.getElementById("head_zone").value = data.head_zone || '';
                            document.getElementById("head_barangay").value = data.head_barangay || '';
                            document.getElementById("head_pob").value = data.head_pob || '';
                            document.getElementById("head_dob").value = data.head_dob || '';
                            document.getElementById("applicant_gender").value = data.head_gender || '';
                            document.getElementById("head_educ").value = data.head_educ || '';
                            document.getElementById("head_occ").value = data.head_occ || '';
                            document.getElementById("head_religion").value = data.head_religion || '';
                            document.getElementById("serial_number").value = data.serial_number || '';

                            // Populate Household Info fields
                            document.getElementById("house_total_income").value = data.house_total_income || '';
                            document.getElementById("house_net_income").value = data.house_net_income || '';
                            document.getElementById("other_income").value = data.other_income || '';
                            document.getElementById("house_house").value = data.house_house || '';
                            document.getElementById("house_house_value").value = data.house_house_value || '';
                            document.getElementById("house_lot").value = data.house_lot || '';
                            document.getElementById("house_lot_value").value = data.house_lot_value || '';
                            document.getElementById("house_house_rent").value = data.house_house_rent || '';
                            document.getElementById("house_lot_rent").value = data.house_lot_rent || '';
                            document.getElementById("house_water").value = data.house_water || '';
                            document.getElementById("house_electric").value = data.house_electric || '';
                            document.getElementById("house_remarks").value = data.house_remarks || '';

                            // Populate JSON fields
                            document.getElementById("family_members").value = data.family_members ? JSON.stringify(data.family_members, null, 2) : '';
                            document.getElementById("rv_service_records").value = data.rv_service_records ? JSON.stringify(data.rv_service_records, null, 2) : '';

                            // Populate Family Members table
                            if (data.family_members && Array.isArray(data.family_members)) {
                                data.family_members.forEach(member => {
                                    addFamilyMemberRow(member.name || '', member.relationship || '', member.birthdate || '', member.age || '', member.sex || '', member.civil_status || '', member.education || '', member.occupation || '', member.monthly_income || '', member.remarks || '');
                                });
                            }

                            // Populate Social Service Records table
                            if (data.rv_service_records && Array.isArray(data.rv_service_records)) {
                                data.rv_service_records.forEach(record => {
                                    addRvServiceRecordRow(record.date || '', record.problem_need || '', record.action_assistance || '', record.remarks || '');
                                });
                            }

                            // Populate Remarks field
                            document.getElementById("remarks").value = data.remarks || '';

                            // Populate Health & Signatures fields
                            document.getElementById("hc_estimated_cost").value = data.hc_estimated_cost || '';
                            document.getElementById("worker_name").value = data.worker_name || '';
                            document.getElementById("officer_name").value = data.officer_name || '';
                            document.getElementById("date_entry").value = data.date_entry || '';
                            document.getElementById("signature_client").value = data.signature_client || '';
                            document.getElementById("signature_worker").value = data.signature_worker || '';
                            document.getElementById("signature_officer").value = data.signature_officer || '';

                            // Toggle rent inputs based on populated values
                            toggleRentInputs();
                            // Recalculate family totals and net income after populating rows/fields
                            if (typeof updateFamilyMembersJSON === 'function') {
                                updateFamilyMembersJSON();
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching intake sheet data:', err));

                document.getElementById("editRemarksModal").classList.remove("hidden");
                setModalMode("edit");
            }

            function openPreviewModal(button) {
                let id = button.getAttribute("data-id");
                let fname = button.getAttribute("data-fname");
                let mname = button.getAttribute("data-mname");
                let lname = button.getAttribute("data-lname");
                let suffix = button.getAttribute("data-suffix");
                let gender = button.getAttribute("data-gender");

                document.getElementById("remarks_id").value = id;
                document.getElementById("modal_mode").value = "view";

                // Populate applicant name fields from button data
                document.getElementById("applicant_fname").value = fname || '';
                document.getElementById("applicant_mname").value = mname || '';
                document.getElementById("applicant_lname").value = lname || '';
                document.getElementById("applicant_suffix").value = suffix || '';
                document.getElementById("applicant_gender").value = gender || '';

                // Fetch existing intake sheet data and populate the form
                fetch(`/lydo_staff/intake-sheet/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            // Populate Head of Family fields
                            document.getElementById("head_4ps").value = data.head_4ps || '';
                            document.getElementById("head_ipno").value = data.head_ipno || '';
                            document.getElementById("head_address").value = data.head_address || '';
                            document.getElementById("head_zone").value = data.head_zone || '';
                            document.getElementById("head_barangay").value = data.head_barangay || '';
                            document.getElementById("head_pob").value = data.head_pob || '';
                            document.getElementById("head_dob").value = data.head_dob || '';
                            document.getElementById("applicant_gender").value = data.head_gender || '';
                            document.getElementById("head_educ").value = data.head_educ || '';
                            document.getElementById("head_occ").value = data.head_occ || '';
                            document.getElementById("head_religion").value = data.head_religion || '';
                            document.getElementById("serial_number").value = data.serial_number || '';

                            // Populate Household Info fields
                            document.getElementById("house_total_income").value = data.house_total_income || '';
                            document.getElementById("house_net_income").value = data.house_net_income || '';
                            document.getElementById("other_income").value = data.other_income || '';
                            document.getElementById("house_house").value = data.house_house || '';
                            document.getElementById("house_house_value").value = data.house_house_value || '';
                            document.getElementById("house_lot").value = data.house_lot || '';
                            document.getElementById("house_lot_value").value = data.house_lot_value || '';
                            document.getElementById("house_house_rent").value = data.house_house_rent || '';
                            document.getElementById("house_lot_rent").value = data.house_lot_rent || '';
                            document.getElementById("house_water").value = data.house_water || '';
                            document.getElementById("house_electric").value = data.house_electric || '';
                            document.getElementById("house_remarks").value = data.house_remarks || '';

                            // Populate JSON fields
                            document.getElementById("family_members").value = data.family_members ? JSON.stringify(data.family_members, null, 2) : '';
                            document.getElementById("rv_service_records").value = data.rv_service_records ? JSON.stringify(data.rv_service_records, null, 2) : '';

                            // Populate Family Members table
                            if (data.family_members && Array.isArray(data.family_members)) {
                                data.family_members.forEach(member => {
                                    addFamilyMemberRow(member.name || '', member.relationship || '', member.birthdate || '', member.age || '', member.sex || '', member.civil_status || '', member.education || '', member.occupation || '', member.monthly_income || '', member.remarks || '');
                                });
                            }

                            // Populate Social Service Records table
                            if (data.rv_service_records && Array.isArray(data.rv_service_records)) {
                                data.rv_service_records.forEach(record => {
                                    addRvServiceRecordRow(record.date || '', record.problem_need || '', record.action_assistance || '', record.remarks || '');
                                });
                            }

                            // Populate Remarks field
                            document.getElementById("remarks").value = data.remarks || '';

                            // Populate Health & Signatures fields
                            document.getElementById("hc_estimated_cost").value = data.hc_estimated_cost || '';
                            document.getElementById("worker_name").value = data.worker_name || '';
                            document.getElementById("officer_name").value = data.officer_name || '';
                            document.getElementById("date_entry").value = data.date_entry || '';
                            document.getElementById("signature_client").value = data.signature_client || '';
                            document.getElementById("signature_worker").value = data.signature_worker || '';
                            document.getElementById("signature_officer").value = data.signature_officer || '';

                            // Toggle rent inputs based on populated values
                            toggleRentInputs();
                            // Recalculate family totals and net income after populating rows/fields
                            if (typeof updateFamilyMembersJSON === 'function') {
                                updateFamilyMembersJSON();
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching intake sheet data:', err));

                document.getElementById("editRemarksModal").classList.remove("hidden");
                setModalMode("view");
            }

            function setModalMode(mode) {
                const modalMode = document.getElementById("modal_mode").value;
                const submitBtn = document.querySelector('#editRemarksModal button[type="submit"]');
                const allInputs = document.querySelectorAll('#editRemarksModal input, #editRemarksModal select, #editRemarksModal textarea');
                const addButtons = document.querySelectorAll('#editRemarksModal button:not([type="submit"]):not(.tab-button)');

                if (modalMode === "view") {
                    // Make all inputs readonly
                    allInputs.forEach(input => {
                        input.setAttribute('readonly', true);
                        input.setAttribute('disabled', true);
                    });
                    // Hide add buttons
                    addButtons.forEach(btn => btn.style.display = 'none');
                    // Change submit button to Close
                    if (submitBtn) {
                        submitBtn.textContent = 'Close';
                        submitBtn.onclick = () => closeEditRemarksModal();
                        submitBtn.type = 'button';
                    }
                } else {
                    // Edit mode: remove readonly/disabled
                    allInputs.forEach(input => {
                        input.removeAttribute('readonly');
                        input.removeAttribute('disabled');
                    });
                    // Show add buttons
                    addButtons.forEach(btn => btn.style.display = '');
                    // Reset submit button
                    if (submitBtn) {
                        submitBtn.textContent = 'Submit';
                        submitBtn.type = 'submit';
                        submitBtn.onclick = null;
                    }
                }
            }

            function closeEditRemarksModal() {
                document.getElementById("editRemarksModal").classList.add("hidden");
                // Clear form fields when closing
                document.getElementById('updateRemarksForm').reset();
                // Clear family members table
                document.getElementById('family_members_tbody').innerHTML = '';
                // Clear social service records table
                document.getElementById('rv_service_records_tbody').innerHTML = '';
            }

            function addFamilyMemberRow(name = '', relation = '', birthdate = '', age = '', sex = '', civil_status = '', education = '', occupation = '', monthly_income = '', remarks = '') {
                const tbody = document.getElementById('family_members_tbody');
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="border px-2 py-1">
                        <input type="text" class="w-full border-none outline-none" placeholder="Name" value="${name}">
                    </td>
                    <td class="border px-2 py-1">
                        <select class="w-full border-none outline-none">
                            <option value="">Select Relation</option>
                            <option value="Father" ${relation === 'Father' ? 'selected' : ''}>Father</option>
                            <option value="Mother" ${relation === 'Mother' ? 'selected' : ''}>Mother</option>
                            <option value="Son" ${relation === 'Son' ? 'selected' : ''}>Son</option>
                            <option value="Daughter" ${relation === 'Daughter' ? 'selected' : ''}>Daughter</option>
                            <option value="Brother" ${relation === 'Brother' ? 'selected' : ''}>Brother</option>
                            <option value="Sister" ${relation === 'Sister' ? 'selected' : ''}>Sister</option>
                            <option value="Grandfather" ${relation === 'Grandfather' ? 'selected' : ''}>Grandfather</option>
                            <option value="Grandmother" ${relation === 'Grandmother' ? 'selected' : ''}>Grandmother</option>
                            <option value="Uncle" ${relation === 'Uncle' ? 'selected' : ''}>Uncle</option>
                            <option value="Aunt" ${relation === 'Aunt' ? 'selected' : ''}>Aunt</option>
                            <option value="Other" ${relation === 'Other' ? 'selected' : ''}>Other</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <input type="date" class="w-full border-none outline-none birthdate-input" value="${birthdate}">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="number" class="w-full border-none outline-none age-input" placeholder="Age" value="${age}" readonly>
                    </td>
                    <td class="border px-2 py-1">
                        <select class="w-full border-none outline-none">
                            <option value="">Select</option>
                            <option value="Male" ${sex === 'Male' ? 'selected' : ''}>Male</option>
                            <option value="Female" ${sex === 'Female' ? 'selected' : ''}>Female</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <select class="w-full border-none outline-none">
                            <option value="">Select</option>
                            <option value="Single" ${civil_status === 'Single' ? 'selected' : ''}>Single</option>
                            <option value="Married" ${civil_status === 'Married' ? 'selected' : ''}>Married</option>
                            <option value="Widowed" ${civil_status === 'Widowed' ? 'selected' : ''}>Widowed</option>
                            <option value="Separated" ${civil_status === 'Separated' ? 'selected' : ''}>Separated</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <select class="w-full border-none outline-none">
                            <option value="">Select Education</option>
                            <option value="Elementary" ${education === 'Elementary' ? 'selected' : ''}>Elementary</option>
                            <option value="High School" ${education === 'High School' ? 'selected' : ''}>High School</option>
                            <option value="Vocational" ${education === 'Vocational' ? 'selected' : ''}>Vocational</option>
                            <option value="College" ${education === 'College' ? 'selected' : ''}>College</option>
                            <option value="Post Graduate" ${education === 'Post Graduate' ? 'selected' : ''}>Post Graduate</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <select class="w-full border-none outline-none">
                            <option value="">Select Occupation</option>
                            <option value="Farmer" ${occupation === 'Farmer' ? 'selected' : ''}>Farmer</option>
                            <option value="Teacher" ${occupation === 'Teacher' ? 'selected' : ''}>Teacher</option>
                            <option value="Driver" ${occupation === 'Driver' ? 'selected' : ''}>Driver</option>
                            <option value="Business Owner" ${occupation === 'Business Owner' ? 'selected' : ''}>Business Owner</option>
                            <option value="Employee" ${occupation === 'Employee' ? 'selected' : ''}>Employee</option>
                            <option value="Unemployed" ${occupation === 'Unemployed' ? 'selected' : ''}>Unemployed</option>
                            <option value="Student" ${occupation === 'Student' ? 'selected' : ''}>Student</option>
                            <option value="Other" ${occupation === 'Other' ? 'selected' : ''}>Other</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <input type="number" step="0.01" class="w-full border-none outline-none" placeholder="₱ Monthly Income" value="${monthly_income}">
                    </td>
                    <td class="border px-2 py-1">
                        <select class="w-full border-none outline-none fm-remarks">
                            <option value="">Select</option>
                            <option value="CIC" ${remarks === 'CIC' ? 'selected' : ''}>CIC</option>
                            <option value="OSY" ${remarks === 'OSY' ? 'selected' : ''}>OSY</option>
                            <option value="SP" ${remarks === 'SP' ? 'selected' : ''}>SP</option>
                            <option value="PWD" ${remarks === 'PWD' ? 'selected' : ''}>PWD</option>
                            <option value="SC" ${remarks === 'SC' ? 'selected' : ''}>SC</option>
                            <option value="None" ${remarks === 'None' ? 'selected' : ''}>None</option>
                            <option value="Lactating Mother" ${remarks === 'Lactating Mother' ? 'selected' : ''}>Lactating Mother</option>
                            <option value="Pregnant Mother" ${remarks === 'Pregnant Mother' ? 'selected' : ''}>Pregnant Mother</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" onclick="removeFamilyMemberRow(this)" class="text-red-500 hover:text-red-700">Remove</button>
                    </td>
                `;
                tbody.appendChild(row);

                // Add event listener for birthdate to calculate age
                const birthdateInput = row.querySelector('.birthdate-input');
                const ageInput = row.querySelector('.age-input');
                birthdateInput.addEventListener('change', function() {
                    calculateAge(this, ageInput);
                });

                // Add listener for monthly income input to update totals
                try {
                    const cells = row.querySelectorAll('td');
                    const monthlyInput = cells[8].querySelector('input');
                    if (monthlyInput) {
                        monthlyInput.addEventListener('input', function() {
                            updateFamilyMembersJSON();
                        });
                    }
                } catch (e) {
                    // ignore if structure differs
                }

                updateFamilyMembersJSON();
            }

            function removeFamilyMemberRow(button) {
                button.closest('tr').remove();
                updateFamilyMembersJSON();
            }

            function calculateAge(birthdateInput, ageInput) {
                const birthdateValue = birthdateInput.value;
                if (!birthdateValue) {
                    ageInput.value = '';
                    return;
                }
                const birthdate = new Date(birthdateValue);
                const today = new Date();
                let age = today.getFullYear() - birthdate.getFullYear();
                const monthDiff = today.getMonth() - birthdate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
                    age--;
                }
                ageInput.value = age;
            }

            function updateFamilyMembersJSON() {
                const rows = document.querySelectorAll('#family_members_tbody tr');
                const members = [];
                let sumMonthly = 0;
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const name = cells[0].querySelector('input').value.trim();
                    const relationship = cells[1].querySelector('select').value.trim();
                    const birthdate = cells[2].querySelector('input').value.trim();
                    const age = cells[3].querySelector('input').value.trim();
                    const sex = cells[4].querySelector('select').value.trim();
                    const civil_status = cells[5].querySelector('select').value.trim();
                    const education = cells[6].querySelector('select').value.trim();
                    const occupation = cells[7].querySelector('select').value.trim();
                    const monthly_income = cells[8].querySelector('input').value.trim();
                    const remarks = cells[9].querySelector('select').value.trim();
                    if (name || relationship || age) {
                        members.push({ name, relationship, birthdate, age, sex, civil_status, education, occupation, monthly_income, remarks });
                        // accumulate monthly income (treat empty or invalid as 0)
                        const num = parseFloat(monthly_income) || 0;
                        sumMonthly += num;
                    }
                });
                document.getElementById('family_members').value = JSON.stringify(members);
                // include Other Income in total
                const otherIncomeEl = document.getElementById('other_income');
                const otherIncome = otherIncomeEl ? (parseFloat(otherIncomeEl.value) || 0) : 0;
                const total = sumMonthly + otherIncome;
                const totalEl = document.getElementById('house_total_income');
                if (totalEl) {
                    totalEl.value = total.toFixed(2);
                    // trigger input event so Net Income recalculates (calculateNetIncome is attached to this event)
                    totalEl.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }

            function addRvServiceRecordRow(date = '', problem_need = '', action_assistance = '', remarks = '') {
                const tbody = document.getElementById('rv_service_records_tbody');
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="border border-gray-300 px-2 py-1">
                        <input type="date" class="w-full border-none outline-none" value="${date}">
                    </td>
                    <td class="border border-gray-300 px-2 py-1">
                        <input type="text" class="w-full border-none outline-none" placeholder="Problem/Need" value="${problem_need}">
                    </td>
                    <td class="border border-gray-300 px-2 py-1">
                        <input type="text" class="w-full border-none outline-none" placeholder="Action/Assistance Given" value="${action_assistance}">
                    </td>
                    <td class="border border-gray-300 px-2 py-1">
                        <input type="text" class="w-full border-none outline-none" placeholder="Remarks" value="${remarks}">
                    </td>
                    <td class="border border-gray-300 px-2 py-1 text-center">
                        <button type="button" onclick="removeRvServiceRecordRow(this)" class="text-red-500 hover:text-red-700">Remove</button>
                    </td>
                `;
                tbody.appendChild(row);
                updateRvServiceRecordsJSON();
            }

            function removeRvServiceRecordRow(button) {
                button.closest('tr').remove();
                updateRvServiceRecordsJSON();
            }

            function updateRvServiceRecordsJSON() {
                const rows = document.querySelectorAll('#rv_service_records_tbody tr');
                const records = [];
                rows.forEach(row => {
                    const inputs = row.querySelectorAll('input');
                    const date = inputs[0].value.trim();
                    const problem_need = inputs[1].value.trim();
                    const action_assistance = inputs[2].value.trim();
                    const remarks = inputs[3].value.trim();
                    if (date || problem_need || action_assistance || remarks) {
                        records.push({ date, problem_need, action_assistance, remarks });
                    }
                });
                document.getElementById('rv_service_records').value = JSON.stringify(records);
            }

            // Function to filter the table view based on name and barangay
            function filterTable() {
                const nameSearchValue = document.getElementById('nameSearch').value.toLowerCase().trim();
                const barangayFilterValue = document.getElementById('barangayFilter').value.toLowerCase().trim();

                // Filter tableView
                const tableViewRows = document.querySelectorAll('#tableView tbody tr');
                tableViewRows.forEach(row => {
                    const nameCell = row.cells[1].textContent.toLowerCase();
                    const barangayCell = row.cells[2].textContent.toLowerCase();

                    const matchesName = nameCell.includes(nameSearchValue);
                    const matchesBarangay = barangayFilterValue === '' || barangayCell.includes(barangayFilterValue);

                    if (matchesName && matchesBarangay) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Function to filter the list view based on name and barangay
            function filterList() {
                const nameSearchValue = document.getElementById('listNameSearch').value.toLowerCase().trim();
                const barangayFilterValue = document.getElementById('listBarangayFilter').value.toLowerCase().trim();

                // Filter listView
                const listViewRows = document.querySelectorAll('#listView tbody tr');
                listViewRows.forEach(row => {
                    const nameCell = row.cells[1].textContent.toLowerCase();
                    const barangayCell = row.cells[2].textContent.toLowerCase();

                    const matchesName = nameCell.includes(nameSearchValue);
                    const matchesBarangay = barangayFilterValue === '' || barangayCell.includes(barangayFilterValue);

                    if (matchesName && matchesBarangay) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Toggle rent inputs based on house and lot selections
            function toggleRentInputs() {
                const houseSelect = document.getElementById('house_house');
                const lotSelect = document.getElementById('house_lot');
                const houseValueGroup = document.getElementById('house_value_group');
                const lotValueGroup = document.getElementById('lot_value_group');
                const lotRentGroup = document.getElementById('lot_rent_group');

                if (houseSelect) {
                    houseSelect.addEventListener('change', function() {
                        if (this.value === 'Owned') {
                            houseValueGroup.style.display = 'block';
                        } else {
                            houseValueGroup.style.display = 'none';
                        }
                    });
                }

                if (lotSelect) {
                    lotSelect.addEventListener('change', function() {
                        if (this.value === 'Owned') {
                            lotValueGroup.style.display = 'block';
                            lotRentGroup.style.display = 'none';
                        } else if (this.value === 'Rent') {
                            lotValueGroup.style.display = 'none';
                            lotRentGroup.style.display = 'block';
                        } else {
                            lotValueGroup.style.display = 'none';
                            lotRentGroup.style.display = 'none';
                        }
                    });
                }
            }

            // Initialize toggleRentInputs on page load
            document.addEventListener('DOMContentLoaded', function() {
                toggleRentInputs();
            });

            // Function to open the view intake sheet modal
            function openViewIntakeSheetModal(button) {
                const id = button.getAttribute("data-id");

                // Fetch intake sheet data
                fetch(`/lydo_staff/intake-sheet/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            // Populate Head of Family fields
                            document.getElementById("view-head_4ps").textContent = data.head_4ps || '';
                            document.getElementById("view-head_ipno").textContent = data.head_ipno || '';
                            document.getElementById("view-applicant_fname").textContent = data.applicant_fname || '';
                            document.getElementById("view-applicant_mname").textContent = data.applicant_mname || '';
                            document.getElementById("view-applicant_lname").textContent = data.applicant_lname || '';
                            document.getElementById("view-applicant_suffix").textContent = data.applicant_suffix || '';
                            document.getElementById("view-head_address").textContent = data.head_address || '';
                            document.getElementById("view-head_zone").textContent = data.head_zone || '';
                            document.getElementById("view-head_barangay").textContent = data.head_barangay || '';
                            document.getElementById("view-head_pob").textContent = data.head_pob || '';
                            document.getElementById("view-head_dob").textContent = data.head_dob || '';
                            document.getElementById("view-applicant_gender").textContent = data.head_gender || '';
                            document.getElementById("view-head_educ").textContent = data.head_educ || '';
                            document.getElementById("view-head_occ").textContent = data.head_occ || '';
                            document.getElementById("view-head_religion").textContent = data.head_religion || '';
                            document.getElementById("view-serial_number").textContent = data.serial_number || '';
                            document.getElementById("view-location").textContent = data.location || '';

                            // Populate Household Info fields
                            document.getElementById("view-house_total_income").textContent = data.house_total_income || '';
                            document.getElementById("view-house_net_income").textContent = data.house_net_income || '';
                            document.getElementById("view-other_income").textContent = data.other_income || '';
                            document.getElementById("view-house_house").textContent = data.house_house || '';
                            document.getElementById("view-house_lot").textContent = data.house_lot || '';
                            document.getElementById("view-house_water").textContent = data.house_water || '';
                            document.getElementById("view-house_electric").textContent = data.house_electric || '';
                            document.getElementById("view-remarks").textContent = data.remarks || '';

                            // Populate Family Members table
                            const familyTbody = document.getElementById('view_family_members_tbody');
                            familyTbody.innerHTML = '';
                            if (data.family_members && Array.isArray(data.family_members)) {
                                data.family_members.forEach(member => {
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td class="border px-2 py-1">${member.name || ''}</td>
                                        <td class="border px-2 py-1">${member.relationship || ''}</td>
                                        <td class="border px-2 py-1">${member.birthdate || ''}</td>
                                        <td class="border px-2 py-1">${member.age || ''}</td>
                                        <td class="border px-2 py-1">${member.sex || ''}</td>
                                        <td class="border px-2 py-1">${member.civil_status || ''}</td>
                                        <td class="border px-2 py-1">${member.education || ''}</td>
                                        <td class="border px-2 py-1">${member.occupation || ''}</td>
                                        <td class="border px-2 py-1">${member.monthly_income || ''}</td>
                                        <td class="border px-2 py-1">${member.remarks || ''}</td>
                                    `;
                                    familyTbody.appendChild(row);
                                });
                            }

                            // Populate Social Service Records table
                            const serviceTbody = document.getElementById('view_rv_service_records_tbody');
                            serviceTbody.innerHTML = '';
                            if (data.rv_service_records && Array.isArray(data.rv_service_records)) {
                                data.rv_service_records.forEach(record => {
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td class="border border-gray-300 px-2 py-1">${record.date || ''}</td>
                                        <td class="border border-gray-300 px-2 py-1">${record.problem_need || ''}</td>
                                        <td class="border border-gray-300 px-2 py-1">${record.action_assistance || ''}</td>
                                        <td class="border border-gray-300 px-2 py-1">${record.remarks || ''}</td>
                                    `;
                                    serviceTbody.appendChild(row);
                                });
                            }

                            // Populate Health & Signatures fields
                            document.getElementById("view-worker_name").textContent = data.worker_name || '';
                            document.getElementById("view-officer_name").textContent = data.officer_name || '';
                            document.getElementById("view-date_entry").textContent = data.date_entry || '';
                            document.getElementById("view-signature_client").textContent = data.signature_client || '';
                            document.getElementById("view-signature_worker").textContent = data.signature_worker || '';
                            document.getElementById("view-signature_officer").textContent = data.signature_officer || '';
                        }
                    })
                    .catch(err => console.error('Error fetching intake sheet data:', err));

                document.getElementById("viewIntakeSheetModal").classList.remove("hidden");
            }

            // Function to close the view intake sheet modal
            function closeViewIntakeSheetModal() {
                document.getElementById("viewIntakeSheetModal").classList.add("hidden");
            }

            // Tab switching functionality for view modal
            function showViewTab(tabName) {
                // Hide all tab contents
                const tabContents = document.querySelectorAll('.view-tab-content');
                tabContents.forEach(content => content.classList.add('hidden'));

                // Remove active class from all tab buttons
                const tabButtons = document.querySelectorAll('.view-tab-button');
                tabButtons.forEach(button => {
                    button.classList.remove('active');
                    button.classList.remove('text-violet-600');
                    button.classList.add('text-gray-500');
                    button.classList.remove('border-b-2', 'border-violet-600');
                });

                // Show the selected tab content
                document.getElementById('view-tab-' + tabName + '-content').classList.remove('hidden');

                // Add active class to the selected tab button
                document.getElementById('view-tab-' + tabName).classList.add('active', 'text-violet-600', 'border-b-2', 'border-violet-600');
                document.getElementById('view-tab-' + tabName).classList.remove('text-gray-500');
            }

            // Function to print the intake sheet
            function printIntakeSheet() {
                const modal = document.getElementById('viewIntakeSheetModal');
                const printWindow = window.open('', '_blank');
                const printContent = modal.innerHTML;

                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Family Intake Sheet</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; }
                                .intake-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                                .intake-section { margin-bottom: 20px; border: 1px solid #e5e7eb; padding: 15px; border-radius: 5px; }
                                .intake-section-title { font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
                                .intake-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                                .intake-table th, .intake-table td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
                                .intake-table th { background-color: #f9fafb; font-weight: 600; }
                                .flex { display: flex; }
                                .gap-4 > * + * { margin-left: 1rem; }
                                .flex-1 { flex: 1; }
                                .mb-4 { margin-bottom: 1rem; }
                                .text-sm { font-size: 0.875rem; }
                                .font-medium { font-weight: 500; }
                                .text-gray-700 { color: #374151; }
                                .hidden { display: none !important; }
                                .border { border: 1px solid #e5e7eb; }
                                .rounded-lg { border-radius: 0.5rem; }
                                .p-2 { padding: 0.5rem; }
                                .bg-gray-50 { background-color: #f9fafb; }
                                .view-field { background-color: #f9fafb !important; border: 1px solid #e5e7eb !important; padding: 0.5rem !important; border-radius: 0.5rem; }
                                .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
                                .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
                                .border-b { border-bottom: 1px solid #e5e7eb; }
                                .border-gray-300 { border-color: #d1d5db; }
                                .text-center { text-align: center; }
                                .text-xl { font-size: 1.25rem; }
                                .font-semibold { font-weight: 600; }
                                .mb-3 { margin-bottom: 0.75rem; }
                                .overflow-x-auto { overflow-x: auto; }
                                .min-w-full { min-width: 100%; }
                                .text-left { text-align: left; }
                                .w-full { width: 100%; }
                                .mt-1 { margin-top: 0.25rem; }
                                .block { display: block; }
                                .label { display: block; font-weight: 500; color: #374151; margin-bottom: 0.25rem; }
                                .flex-1 { flex: 1; }
                                .gap-4 { gap: 1rem; }
                                .justify-between { justify-content: space-between; }
                                .items-center { align-items: center; }
                                .mb-6 { margin-bottom: 1.5rem; }
                                .border-b-2 { border-bottom-width: 2px; }
                                .border-violet-600 { border-color: #7c3aed; }
                                .text-violet-600 { color: #7c3aed; }
                                .font-medium { font-weight: 500; }
                                .px-4 { padding-left: 1rem; padding-right: 1rem; }
                                .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
                                .border-b { border-bottom: 1px solid #e5e7eb; }
                                .text-gray-500 { color: #6b7280; }
                                .hover\:text-violet-600:hover { color: #7c3aed; }
                                .border-b-2 { border-bottom-width: 2px; }
                                .border-violet-600 { border-color: #7c3aed; }
                                .text-violet-600 { color: #7c3aed; }
                                .text-gray-500 { color: #6b7280; }
                                .active { background-color: #7c3aed; color: white; border-color: #7c3aed; }
                                .tab-button { cursor: pointer; padding: 10px 20px; border-radius: 8px; transition: all 0.3s ease; background-color: white; color: #6b7280; border: 1px solid #e5e7eb; display: inline-flex; align-items: center; justify-content: center; }
                                .tab-button.active { background-color: #7c3aed; color: white; border-color: #7c3aed; }
                                .tab-button:hover:not(.active) { background-color: #f3f4f6; }
                                .tab-content { display: block; }
                                .tab-content.hidden { display: none; }
                                .view-tab-content { display: block; }
                                .view-tab-content.hidden { display: none; }
                                .view-tab-button { cursor: pointer; padding: 10px 20px; border-radius: 8px; transition: all 0.3s ease; background-color: white; color: #6b7280; border: 1px solid #e5e7eb; display: inline-flex; align-items: center; justify-content: center; }
                                .view-tab-button.active { background-color: #7c3aed; color: white; border-color: #7c3aed; }
                                .view-tab-button:hover:not(.active) { background-color: #f3f4f6; }
                                @media print {
                                    body { margin: 0; }
                                    .no-print { display: none !important; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="intake-header">
                                <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Logo" style="height: 40px; width: auto; margin-right: 10px;">
                                <h1 style="display: inline; font-size: 24px; font-weight: bold;">Family Intake Sheet</h1>
                            </div>
                            ${printContent.replace(/class="hidden"/g, 'style="display: none;"').replace(/class="view-tab-content hidden"/g, 'style="display: none;"').replace(/class="tab-content hidden"/g, 'style="display: none;"')}
                        </body>
                    </html>
                `);

                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }
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

        <script src="{{ asset('js/logout.js') }}"></script>
    </div>
</body>
</html>