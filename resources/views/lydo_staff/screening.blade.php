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

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 20px;
            border-radius: 8px;
            width: 95%;
            max-width: 1200px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }
        
        .modal-close:hover {
            color: #374151;
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        /* Clean printable box style */
        .print-box {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #fff;
        }
        .thin-border {
            border: 1px solid #e5e7eb;
        }

        /* Layout for review (full screen) */
        .review-columns {
            display: grid;
            grid-template-columns: 1fr; /* full width for responsive design */
            gap: 16px;
        }

        /* Print rules */
        @page {
            size: landscape;
            margin: 4mm;
        }
        @media print {
            body {
                background: white !important;
                color: #000;
                font-size: 10px;
            }
            .no-print {
                display: none !important;
            }
            /* Ensure review area spans the full printable page */
            .max-w-6xl {
                max-width: 100% !important;
                width: 100% !important;
            }
            #reviewArea {
                page-break-inside: avoid;
                padding: 0.125rem !important;
            }
            .review-columns {
                font-size: 9px;
                gap: 4px;
            }
            .thin-border {
                margin-bottom: 0.125rem;
                padding: 0.125rem;
            }
            table {
                font-size: 8px;
            }
            .text-sm {
                font-size: 8px !important;
            }
            .text-xs {
                font-size: 7px !important;
            }
            h2 {
                font-size: 12px !important;
            }
            h4 {
                font-size: 10px !important;
            }
        }

        /* Responsive review columns */
        @media (max-width: 768px) {
            .review-columns {
                grid-template-columns: 1fr;
            }
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
                                <i class="fas fa-table mr-1"></i> Pending Remarks
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
                                        <button
                                            title="View Intake Sheet"
                                            class="px-3 py-1 text-sm bg-green-500 hover:bg-green-600 text-white rounded-lg shadow ml-2"
                                            data-id="{{ $app->application_personnel_id }}"
                                            onclick="openReviewModal(this)">
                                            <i class="fas fa-eye mr-1"></i> View
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
                                    <th class="px-4 py-3 border border-gray-200 text-center">Action</th>
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
                                            onclick="openReviewModal(this)">
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

        <!-- Review Modal for Reviewed Applicants -->
        <div id="reviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="text-xl font-bold">Review Family Intake Sheet</h2>
                    <button class="modal-close" onclick="closeReviewModal()">&times;</button>
                </div>

                <div id="modalReviewContent">
                    <!-- Content will be populated here -->
                </div>

                <div class="modal-actions">
                    <button
                        type="button"
                        onclick="window.print()"
                        class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700"
                    >
                        Print
                    </button>
                    <button
                        type="button"
                        onclick="closeReviewModal()"
                        class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600"
                    >
                        Close
                    </button>
                </div>
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

            // Open review modal
            function openReviewModal(button) {
                const id = button.getAttribute("data-id");
                
                // Fetch intake sheet data
                fetch(`/lydo_staff/intake-sheet/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            populateReviewModal(data);
                            document.getElementById('reviewModal').style.display = 'block';
                        }
                    })
                    .catch(err => console.error('Error fetching intake sheet data:', err));
            }

            // Close review modal
            function closeReviewModal() {
                document.getElementById('reviewModal').style.display = 'none';
            }

            // Populate review modal with data
            function populateReviewModal(d) {
                const modalContent = document.getElementById('modalReviewContent');
                
                // Create the review content for the modal
                modalContent.innerHTML = `
                    <div class="review-columns">
                        <div class="space-y-4">
                            <div class="print-box p-4">
                                <p><strong>Serial No.:</strong> ${d.serial_number || "AUTO_GENERATED"}</p>
                                <p><strong>Name:</strong> ${[d.applicant_fname, d.applicant_mname, d.applicant_lname, d.applicant_suffix]
                                    .filter(Boolean)
                                    .join(" ")}</p>
                                <table class="min-w-full text-sm">
                                    <tr>
                                        <td><strong>Sex:</strong> ${d.head_gender || "-"}</td>
                                        <td><strong>4Ps:</strong> ${d.head_4ps || "-"}</td>
                                        <td><strong>IP No.:</strong> ${d.head_ipno || "-"}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Address:</strong> ${d.head_address || "-"}</td>
                                        <td><strong>Zone:</strong> ${d.head_zone || "-"}</td>
                                        <td><strong>Barangay:</strong> ${d.head_barangay || "-"}</td>
                                        <td><strong>Location:</strong> ${d.location || "-"}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date of Birth:</strong> ${d.head_dob || "-"}</td>
                                        <td><strong>Place of Birth:</strong> ${d.head_pob || "-"}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Educational Attainment:</strong> ${d.head_educ || "-"}</td>
                                        <td><strong>Occupation:</strong> ${d.head_occ || "-"}</td>
                                        <td><strong>Religion:</strong> ${d.head_religion || "-"}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="print-box p-4">
                                <h4 class="font-semibold">Family Members</h4>
                                <table class="min-w-full text-sm thin-border">
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
                                            <th class="border px-2 py-1">Income</th>
                                            <th class="border px-2 py-1">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${(() => {
                                            let familyMembers = d.family_members;
                                            if (typeof familyMembers === 'string') {
                                                try {
                                                    familyMembers = JSON.parse(familyMembers);
                                                } catch (e) {
                                                    familyMembers = [];
                                                }
                                            }
                                            return Array.isArray(familyMembers) ? familyMembers.map(f => `
                                                <tr>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.name || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.relationship || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${formatDate(f.birthdate)}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.age || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.sex || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.civil_status || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.education || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.occupation || '')}</td>
                                                    <td class="border px-2 py-1 text-left">₱${escapeHtml(f.monthly_income || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.remarks || '')}</td>
                                                </tr>
                                            `).join('') : '';
                                        })()}
                                    </tbody>
                                </table>

                                <!-- SOCIAL SERVICE RECORD -->
                                <div class="thin-border p-2 mb-3 mt-4">
                                    <h4 class="font-semibold mb-2">SOCIAL SERVICE RECORD</h4>
                                    <table class="min-w-full border text-sm">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border px-2 py-1 w-20 text-center">DATE</th>
                                                <th class="border px-2 py-1 text-center">
                                                    PROBLEM PRESENTED<br /><span
                                                        class="text-xs text-gray-500"
                                                        >(to be filled by support staff)</span
                                                    >
                                                </th>
                                                <th class="border px-2 py-1 text-center">
                                                    ASSISTANCE PROVIDED<br /><span
                                                        class="text-xs text-gray-500"
                                                        >(to be filled by program implementer)</span
                                                    >
                                                </th>
                                                <th class="border px-2 py-1 text-center">REMARKS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${(() => {
                                                let serviceRecords = d.rv_service_records;
                                                if (typeof serviceRecords === 'string') {
                                                    try {
                                                        serviceRecords = JSON.parse(serviceRecords);
                                                    } catch (e) {
                                                        serviceRecords = [];
                                                    }
                                                }
                                                return Array.isArray(serviceRecords) && serviceRecords.length > 0 ? 
                                                    serviceRecords.map(record => `
                                                        <tr>
                                                            <td class="border px-2 py-1 text-center">${formatDate(record.date)}</td>
                                                            <td class="border px-2 py-1 text-left">${escapeHtml(record.problem_need || '')}</td>
                                                            <td class="border px-2 py-1 text-left">${escapeHtml(record.action_assistance || '')}</td>
                                                            <td class="border px-2 py-1 text-left">${escapeHtml(record.remarks || '')}</td>
                                                        </tr>
                                                    `).join('') : 
                                                    Array(5).fill(`
                                                        <tr>
                                                            <td class="border h-8"></td>
                                                            <td class="border"></td>
                                                            <td class="border"></td>
                                                            <td class="border"></td>
                                                        </tr>
                                                    `).join('');
                                            })()}
                                        </tbody>
                                    </table>
                                </div>

                                <!-- HEALTH CONDITION & CODES -->
                                <div class="thin-border p-2">
                                    <h4 class="font-semibold mb-2">
                                        HEALTH CONDITION &amp; CODES
                                    </h4>
                                    <p><strong>Health Condition:</strong></p>
                                    <p class="ml-2">
                                        A. DEAD • B. INJURED • C. MISSING • D. With Illness
                                    </p>
                                </div>
                            </div>
                            
                            <div class="print-box p-4">
                                <h4 class="font-semibold">Household Information</h4>
                                <table class="min-w-full text-sm">
                                    <tr>
                                        <td><strong>Other Source of Income:</strong> ₱${d.other_income || "-"}</td>
                                        <td><strong>Total Family Income:</strong> ₱${d.house_total_income || "-"}</td>
                                        <td><strong>Total Family Net Income:</strong> ₱${d.house_net_income || "-"}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>House (Owned/Rented):</strong> ${d.house_house || "-"}<br>
                                            ${d.house_house === 'Rented' ? `<strong>House Rent:</strong> ₱${d.house_house_rent || '-'}` : ''}
                                        </td>
                                        <td><strong>Lot (Owned/Rented):</strong> ${d.house_lot || "-"}<br>
                                            ${d.house_lot === 'Rented' ? `<strong>Lot Rent:</strong> ₱${d.house_lot_rent || '-'}` : ''}
                                        </td>
                                        <td><strong>Water:</strong> ₱${d.house_water || "-"}</td>
                                        <td><strong>Electricity Source:</strong> ₱${d.house_electric || "-"}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="print-box p-4">
                                <h4 class="font-semibold">Signatures</h4>
                                <div>
                                    <p><strong>Family Head:</strong></p>
                                    <div>
                                        ${d.signature_client ? 
                                            `<img src="${d.signature_client}" style="max-width: 100%; height: 80px;" />` : 
                                            '<p class="text-xs text-gray-500">No signature</p>'
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            function formatDate(dateString) {
                if (!dateString) return "-";
                const date = new Date(dateString);
                if (isNaN(date)) return dateString;
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            }

            function escapeHtml(s) {
                if (!s) return "";
                return s.replace(
                    /[&<>"']/g,
                    (m) =>
                        ({
                            "&": "&amp;",
                            "<": "&lt;",
                            ">": "&gt;",
                            '"': "&quot;",
                            "'": "&#39;",
                        }[m])
                );
            }

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('reviewModal');
                if (event.target === modal) {
                    closeReviewModal();
                }
            });

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

            // Rest of your existing functions (openEditRemarksModal, addFamilyMemberRow, etc.) remain the same
            // ... [Keep all your existing functions for the edit modal]
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