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

        /* Fix for modal display */
        .modal-open {
            overflow: hidden;
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
                                    <th class="px-4py-3 border border-gray-200 text-center">Intake Sheet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tableApplicants as $index => $app)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
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
            <div class="bg-white w-full max-w-6xl rounded-2xl shadow-2xl p-6 max-h-screen overflow-y-auto relative">
                <!-- Close button -->
                <button type="button" onclick="closeEditRemarksModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
                    <i class="fas fa-times text-2xl"></i>
                </button>

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
                                        <!-- Rows will be added dynamically -->
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" onclick="addFamilyMemberRow()" class="mt-2 bg-purple-600 text-white px-4 py-2 rounded">+ Add Member</button>
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
                            
                            <!-- Income Section -->
                            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">Income Calculation</h4>
                                <!-- Row 1: Other Income and Total Income -->
                                <div class="flex gap-4 mb-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Other Income</label>
                                        <input type="number" step="0.01" name="other_income" id="other_income" class="mt-1 block w-full border rounded-lg p-2" placeholder="0.00">
                                        <p class="text-xs text-gray-500 mt-1">Additional income not from family members</p>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Total Income</label>
                                        <input type="number" step="0.01" name="house_total_income" id="house_total_income" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100" readonly>
                                        <p class="text-xs text-gray-500 mt-1">Family Members Income + Other Income</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Expenses Section -->
                            <div class="mb-4 p-3 bg-red-50 rounded-lg">
                                <h4 class="font-semibold text-red-800 mb-2">Expenses</h4>
                                <!-- Row 2: House, Lot, Water, Electric -->
                                <div class="flex gap-4 mb-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">House</label>
                                        <select name="house_house" id="house_house" class="mt-1 block w-full border rounded-lg p-2">
                                            <option value="">Select</option>
                                            <option value="Owned">Owned</option>
                                            <option value="Rent">Rent</option>
                                        </select>
                                        <div id="house_rent_group" style="display: none;" class="mt-2">
                                            <label class="block text-sm font-medium text-gray-700">House Rent</label>
                                            <input type="number" step="0.01" name="house_house_rent" id="house_house_rent" class="mt-1 block w-full border rounded-lg p-2" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Lot</label>
                                        <select name="house_lot" id="house_lot" class="mt-1 block w-full border rounded-lg p-2">
                                            <option value="">Select</option>
                                            <option value="Owned">Owned</option>
                                            <option value="Rent">Rent</option>
                                        </select>
                                        <div id="lot_rent_group" style="display: none;" class="mt-2">
                                            <label class="block text-sm font-medium text-gray-700">Lot Rent</label>
                                            <input type="number" step="0.01" name="house_lot_rent" id="house_lot_rent" class="mt-1 block w-full border rounded-lg p-2" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Water</label>
                                        <input type="number" step="0.01" name="house_water" id="house_water" class="mt-1 block w-full border rounded-lg p-2" placeholder="0.00">
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Electric</label>
                                        <input type="number" step="0.01" name="house_electric" id="house_electric" class="mt-1 block w-full border rounded-lg p-2" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <!-- Net Income Section -->
                            <div class="mb-4 p-3 bg-green-50 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">Net Income Calculation</h4>
                                <!-- Row 3: Net Income and Remarks -->
                                <div class="flex gap-4 mb-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Net Income</label>
                                        <input type="number" step="0.01" name="house_net_income" id="house_net_income" class="mt-1 block w-full border rounded-lg p-2 bg-gray-100" readonly>
                                        <p class="text-xs text-gray-500 mt-1">Total Income - Total Expenses</p>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">Remarks <span class="text-red-500">*</span></label>
                                        <select name="remarks" id="remarks" class="mt-1 block w-full border rounded-lg p-2" required>
                                            <option value="">Select Remarks</option>
                                            <option value="Poor">Poor</option>
                                            <option value="Non Poor">Non Poor</option>
                                            <option value="Ultra Poor">Ultra Poor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="showTab('family-members')" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Previous</button>
                            <button type="button" id="additional-next-btn" onclick="showTab('social-service')" class="px-4 py-2 bg-blue-600 text-white rounded-lg" disabled>Next</button>
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
                                    <!-- Rows will be added dynamically -->
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
                            <div class="flex gap-2">
                                <button type="button" onclick="printIntakeSheet()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Print</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Submit</button>
                            </div>
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

        <!-- Signature Modal -->
        <div id="signatureModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800" id="signatureModalTitle">Signature</h3>
                    <button type="button" onclick="closeSignatureModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50">
                        <canvas id="signatureCanvas" width="400" height="200" class="border border-gray-300 rounded bg-white"></canvas>
                    </div>
                </div>

                <div class="flex justify-between gap-3">
                    <button type="button" onclick="clearSignature()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        <i class="fas fa-eraser mr-2"></i>Clear
                    </button>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeSignatureModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="button" onclick="saveSignature()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Save Signature
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Add this function to set current date
            function setCurrentDate() {
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('date_entry').value = today;
            }

            // Main Tab switching functionality
            function showTable() {
                document.getElementById('tableView').classList.remove('hidden');
                document.getElementById('listView').classList.add('hidden');
                document.getElementById('tab-screening').classList.add('active');
                document.getElementById('tab-reviewed').classList.remove('active');
                filterTable();
            }

            function showList() {
                document.getElementById('tableView').classList.add('hidden');
                document.getElementById('listView').classList.remove('hidden');
                document.getElementById('tab-screening').classList.remove('active');
                document.getElementById('tab-reviewed').classList.add('active');
                filterList();
            }

            // Modal Tab switching functionality
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
                const activeTab = document.getElementById('tab-' + tabName);
                activeTab.classList.add('active', 'text-violet-600', 'border-b-2', 'border-violet-600');
                activeTab.classList.remove('text-gray-500');
            }

            // Open Edit Remarks Modal
            function openEditRemarksModal(button) {
                const id = button.getAttribute("data-id");
                const name = button.getAttribute("data-name");
                const fname = button.getAttribute("data-fname");
                const mname = button.getAttribute("data-mname");
                const lname = button.getAttribute("data-lname");
                const suffix = button.getAttribute("data-suffix");
                const bdate = button.getAttribute("data-bdate");
                const brgy = button.getAttribute("data-brgy");
                const gender = button.getAttribute("data-gender");
                const pob = button.getAttribute("data-pob");

                // Set the values in the modal form
                document.getElementById('remarks_id').value = id;
                document.getElementById('applicant_fname').value = fname || '';
                document.getElementById('applicant_mname').value = mname || '';
                document.getElementById('applicant_lname').value = lname || '';
                document.getElementById('applicant_suffix').value = suffix || '';
                document.getElementById('head_dob').value = bdate || '';
                document.getElementById('head_barangay').value = brgy || '';
                document.getElementById('applicant_gender').value = gender || '';
                document.getElementById('head_pob').value = pob || '';

                // Generate serial number and location
                document.getElementById('serial_number').value = 'SN-' + Date.now();


                // Set current date for Date Entry
                setCurrentDate();

                // Clear previous family members and service records
                document.getElementById('family_members_tbody').innerHTML = '';
                document.getElementById('rv_service_records_tbody').innerHTML = '';

                // Fetch existing intake sheet data and populate form
                fetch(`/lydo_staff/intake-sheet/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            populateEditModal(data);
                        }
                    })
                    .catch(err => console.error('Error fetching intake sheet data:', err))
                    .finally(() => {
                        // Show the modal
                        document.getElementById('editRemarksModal').classList.remove('hidden');
                        document.body.classList.add('modal-open');

                        // Reset to first tab
                        showTab('family');
                    });
            }

            // Close Edit Remarks Modal
            function closeEditRemarksModal() {
                document.getElementById('editRemarksModal').classList.add('hidden');
                document.body.classList.remove('modal-open');
            }

            // Open Review Modal
            function openReviewModal(button) {
                const id = button.getAttribute("data-id");
                
                // Fetch intake sheet data
                fetch(`/lydo_staff/intake-sheet/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            populateReviewModal(data);
                            document.getElementById('reviewModal').style.display = 'block';
                            document.body.classList.add('modal-open');
                        }
                    })
                    .catch(err => console.error('Error fetching intake sheet data:', err));
            }

            // Close Review Modal
            function closeReviewModal() {
                document.getElementById('reviewModal').style.display = 'none';
                document.body.classList.remove('modal-open');
            }

            // Populate Review Modal with data
            function populateReviewModal(d) {
                const modalContent = document.getElementById('modalReviewContent');
                
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
                                        <td><strong>House (Owned/Rented):</strong> ${d.house_house || "-"}</td>
                                        <td><strong>Lot (Owned/Rented):</strong> ${d.house_lot || "-"}</td>
                                        <td><strong>Water:</strong> ₱${d.house_water || "-"}</td>
                                        <td><strong>Electricity Source:</strong> ₱${d.house_electric || "-"}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="print-box p-4">
                                <h4 class="font-semibold">Social Service Records</h4>
                                <table class="min-w-full text-sm thin-border">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border px-2 py-1">Date</th>
                                            <th class="border px-2 py-1">Problem/Need</th>
                                            <th class="border px-2 py-1">Action/Assistance Given</th>
                                            <th class="border px-2 py-1">Remarks</th>
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
                                            return Array.isArray(serviceRecords) && serviceRecords.length > 0 ? serviceRecords.map(r => `
                                                <tr>
                                                    <td class="border px-2 py-1 text-left">${formatDate(r.date)}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(r.problem || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(r.action || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(r.remarks || '')}</td>
                                                </tr>
                                            `).join('') : '<tr><td colspan="4" class="border px-2 py-1 text-center text-gray-500">No social service records found</td></tr>';
                                        })()}
                                    </tbody>
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

            // Utility functions
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

            // Family Members Functions
            function addFamilyMemberRow() {
                const tbody = document.getElementById('family_members_tbody');
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="border px-2 py-1">
                        <input type="text" name="family_member_name[]" class="w-full border-none focus:ring-0" placeholder="Full Name">
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_relation[]" class="w-full border-none focus:ring-0">
                            <option value="">Select Relation</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Child">Child</option>
                            <option value="Parent">Parent</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Other">Other</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <input type="date" name="family_member_birthdate[]" class="w-full border-none focus:ring-0">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="number" name="family_member_age[]" class="w-full border-none focus:ring-0" placeholder="Age">
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_sex[]" class="w-full border-none focus:ring-0">
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_civil_status[]" class="w-full border-none focus:ring-0">
                            <option value="">Select Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Separated">Separated</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_education[]" class="w-full border-none focus:ring-0">
                            <option value="">Select Education</option>
                            <option value="Elementary">Elementary</option>
                            <option value="High School">High School</option>
                            <option value="College">College</option>
                            <option value="Vocational">Vocational</option>
                            <option value="Post Graduate">Post Graduate</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" name="family_member_occupation[]" class="w-full border-none focus:ring-0" placeholder="Occupation">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="number" step="0.01" name="family_member_income[]" class="w-full border-none focus:ring-0" placeholder="0.00">
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_remarks[]" class="w-full border-none focus:ring-0">
                            <option value="">Select Remarks</option>
                            <option value="OSY">Out of School Youth (OSY)</option>
                            <option value="SP">Solo Parent (SP)</option>
                            <option value="PWD">Person with Disability (PWD)</option>
                            <option value="SC">Senior Citizen (SC)</option>
                            <option value="Lactating">Lactating Mother</option>
                            <option value="Pregnant">Pregnant Mother</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            }

            // Service Records Functions
            function addRvServiceRecordRow() {
                const tbody = document.getElementById('rv_service_records_tbody');
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="border px-2 py-1">
                        <input type="date" name="service_record_date[]" class="w-full border-none focus:ring-0" value="${new Date().toISOString().split('T')[0]}">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" name="service_record_problem[]" class="w-full border-none focus:ring-0" placeholder="Problem/Need">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" name="service_record_action[]" class="w-full border-none focus:ring-0" placeholder="Action/Assistance">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" name="service_record_remarks[]" class="w-full border-none focus:ring-0" placeholder="Remarks">
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            }

            // Filtering functions
            function filterTable() {
                const nameSearchValue = document.getElementById('nameSearch').value.toLowerCase().trim();
                const barangayFilterValue = document.getElementById('barangayFilter').value.toLowerCase().trim();

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

            function filterList() {
                const nameSearchValue = document.getElementById('listNameSearch').value.toLowerCase().trim();
                const barangayFilterValue = document.getElementById('listBarangayFilter').value.toLowerCase().trim();

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

            // Update the calculateIncomes function with proper calculation
            function calculateIncomes() {
                // Calculate total family income from family members
                let totalFamilyIncome = 0;
                const incomeInputs = document.querySelectorAll('input[name="family_member_income[]"]');
                incomeInputs.forEach(input => {
                    totalFamilyIncome += parseFloat(input.value) || 0;
                });

                // Add other income
                const otherIncome = parseFloat(document.getElementById('other_income').value) || 0;
                const houseTotalIncome = totalFamilyIncome + otherIncome;
                
                // Set total income
                document.getElementById('house_total_income').value = houseTotalIncome.toFixed(2);

                // Calculate expenses (house rent, lot rent, water, electric)
                const houseRent = parseFloat(document.getElementById('house_house_rent').value) || 0;
                const lotRent = parseFloat(document.getElementById('house_lot_rent').value) || 0;
                const houseWater = parseFloat(document.getElementById('house_water').value) || 0;
                const houseElectric = parseFloat(document.getElementById('house_electric').value) || 0;
                
                // Total expenses (house rent + lot rent + water + electric)
                const totalExpenses = houseRent + lotRent + houseWater + houseElectric;
                
                // Calculate net income (total income minus total expenses)
                const netIncome = houseTotalIncome - totalExpenses;
                
                document.getElementById('house_net_income').value = netIncome.toFixed(2);
            }

            // Form submission handling
            document.addEventListener('DOMContentLoaded', function() {
                // Set current date when page loads
                setCurrentDate();

                // Add event listeners for house and lot toggles
                const houseSelect = document.getElementById('house_house');
                const lotSelect = document.getElementById('house_lot');
                const houseRentGroup = document.getElementById('house_rent_group');
                const lotRentGroup = document.getElementById('lot_rent_group');

                function toggleHouseFields() {
                    const value = houseSelect.value;
                    if (value === 'Rent') {
                        houseRentGroup.style.display = 'block';
                    } else {
                        houseRentGroup.style.display = 'none';
                        document.getElementById('house_house_rent').value = '';
                        calculateIncomes(); // Recalculate when field is hidden
                    }
                }

                function toggleLotFields() {
                    const value = lotSelect.value;
                    if (value === 'Rent') {
                        lotRentGroup.style.display = 'block';
                    } else {
                        lotRentGroup.style.display = 'none';
                        document.getElementById('house_lot_rent').value = '';
                        calculateIncomes(); // Recalculate when field changes
                    }
                }

                if (houseSelect) {
                    houseSelect.addEventListener('change', toggleHouseFields);
                    // Initialize on page load
                    toggleHouseFields();
                }
                if (lotSelect) {
                    lotSelect.addEventListener('change', toggleLotFields);
                    // Initialize on page load
                    toggleLotFields();
                }

                // Add event listeners for all income and expense fields
                document.addEventListener('input', function(e) {
                    if (e.target.name === 'family_member_income[]' ||
                        e.target.id === 'other_income' ||
                        e.target.id === 'house_house_rent' ||
                        e.target.id === 'house_lot_rent' ||
                        e.target.id === 'house_water' ||
                        e.target.id === 'house_electric') {
                        calculateIncomes();
                    }
                });

                // Enable/disable next button based on remarks selection
                const remarksSelect = document.getElementById('remarks');
                const additionalNextBtn = document.getElementById('additional-next-btn');

                function checkRemarksSelection() {
                    if (remarksSelect && remarksSelect.value) {
                        additionalNextBtn.disabled = false;
                        additionalNextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        additionalNextBtn.classList.add('hover:bg-blue-700');
                    } else {
                        additionalNextBtn.disabled = true;
                        additionalNextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        additionalNextBtn.classList.remove('hover:bg-blue-700');
                    }
                }

                if (remarksSelect) {
                    remarksSelect.addEventListener('change', checkRemarksSelection);
                    // Initial check
                    checkRemarksSelection();
                }

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

                        // Serialize family members data
                        let familyMembers = [];
                        const familyRows = document.querySelectorAll('#family_members_tbody tr');
                        familyRows.forEach(row => {
                            const cells = row.cells;
                            familyMembers.push({
                                name: cells[0].querySelector('input').value || '',
                                relationship: cells[1].querySelector('select').value || '',
                                birthdate: cells[2].querySelector('input').value || '',
                                age: cells[3].querySelector('input').value || '',
                                sex: cells[4].querySelector('select').value || '',
                                civil_status: cells[5].querySelector('select').value || '',
                                education: cells[6].querySelector('select').value || '',
                                occupation: cells[7].querySelector('input').value || '',
                                monthly_income: cells[8].querySelector('input').value || '',
                                remarks: cells[9].querySelector('select').value || '',
                            });
                        });
                        document.getElementById('family_members').value = JSON.stringify(familyMembers);

                        // Serialize service records data
                        let serviceRecords = [];
                        const serviceRows = document.querySelectorAll('#rv_service_records_tbody tr');
                        serviceRows.forEach(row => {
                            const cells = row.cells;
                            serviceRecords.push({
                                date: cells[0].querySelector('input').value || '',
                                problem: cells[1].querySelector('input').value || '',
                                action: cells[2].querySelector('input').value || '',
                                remarks: cells[3].querySelector('input').value || '',
                            });
                        });
                        document.getElementById('rv_service_records').value = JSON.stringify(serviceRecords);

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

                // Close modals when clicking outside
                window.addEventListener('click', function(event) {
                    const editModal = document.getElementById('editRemarksModal');
                    const reviewModal = document.getElementById('reviewModal');
                    
                    if (event.target === editModal) {
                        closeEditRemarksModal();
                    }
                    if (event.target === reviewModal) {
                        closeReviewModal();
                    }
                });

                // Initial calculation
                calculateIncomes();
            });

            // Populate Edit Modal with existing data
            function populateEditModal(data) {
                // Populate head of family details
                document.getElementById('head_4ps').value = data.head_4ps || '';
                document.getElementById('head_ipno').value = data.head_ipno || '';
                document.getElementById('head_address').value = data.head_address || '';
                document.getElementById('head_zone').value = data.head_zone || '';
                document.getElementById('head_educ').value = data.head_educ || '';
                document.getElementById('head_occ').value = data.head_occ || '';
                document.getElementById('head_religion').value = data.head_religion || '';

                // Populate household information
                document.getElementById('other_income').value = data.other_income || '';
                document.getElementById('house_total_income').value = data.house_total_income || '';
                document.getElementById('house_net_income').value = data.house_net_income || '';
                document.getElementById('house_house').value = data.house_house || '';
                document.getElementById('house_lot').value = data.house_lot || '';
                document.getElementById('house_water').value = data.house_water || '';
                document.getElementById('house_electric').value = data.house_electric || '';

                // Handle conditional fields for house and lot
                const houseSelect = document.getElementById('house_house');
                const lotSelect = document.getElementById('house_lot');
                
                if (data.house_house === 'Rent') {
                    document.getElementById('house_rent_group').style.display = 'block';
                    document.getElementById('house_house_rent').value = data.house_house_rent || '';
                }
                
                if (data.house_lot === 'Rent') {
                    document.getElementById('lot_rent_group').style.display = 'block';
                    document.getElementById('house_lot_rent').value = data.house_lot_rent || '';
                }

                // Trigger change events to update UI
                if (houseSelect) {
                    houseSelect.dispatchEvent(new Event('change'));
                }
                if (lotSelect) {
                    lotSelect.dispatchEvent(new Event('change'));
                }

                // Populate remarks
                document.getElementById('remarks').value = data.remarks || '';

                // Populate health & signatures
                document.getElementById('worker_name').value = data.worker_name || '';
                document.getElementById('officer_name').value = data.officer_name || '';
                // Don't overwrite date_entry if it's already set to current date
                if (!document.getElementById('date_entry').value) {
                    document.getElementById('date_entry').value = data.date_entry || '';
                }
                document.getElementById('signature_client').value = data.signature_client || '';
                document.getElementById('signature_worker').value = data.signature_worker || '';
                document.getElementById('signature_officer').value = data.signature_officer || '';

                // Populate family members
                if (data.family_members) {
                    let familyMembers = data.family_members;
                    if (typeof familyMembers === 'string') {
                        try {
                            familyMembers = JSON.parse(familyMembers);
                        } catch (e) {
                            familyMembers = [];
                        }
                    }
                    if (Array.isArray(familyMembers)) {
                        familyMembers.forEach(member => {
                            addFamilyMemberRow();
                            const rows = document.querySelectorAll('#family_members_tbody tr');
                            const lastRow = rows[rows.length - 1];
                            lastRow.cells[0].querySelector('input').value = member.name || '';
                            lastRow.cells[1].querySelector('select').value = member.relationship || '';
                            lastRow.cells[2].querySelector('input').value = member.birthdate || '';
                            lastRow.cells[3].querySelector('input').value = member.age || '';
                            lastRow.cells[4].querySelector('select').value = member.sex || '';
                            lastRow.cells[5].querySelector('select').value = member.civil_status || '';
                            lastRow.cells[6].querySelector('select').value = member.education || '';
                            lastRow.cells[7].querySelector('input').value = member.occupation || '';
                            lastRow.cells[8].querySelector('input').value = member.monthly_income || '';
                            lastRow.cells[9].querySelector('select').value = member.remarks || '';
                        });
                    }
                }

                // Populate service records
                if (data.rv_service_records) {
                    let serviceRecords = data.rv_service_records;
                    if (typeof serviceRecords === 'string') {
                        try {
                            serviceRecords = JSON.parse(serviceRecords);
                        } catch (e) {
                            serviceRecords = [];
                        }
                    }
                    if (Array.isArray(serviceRecords)) {
                        serviceRecords.forEach(record => {
                            addRvServiceRecordRow();
                            const rows = document.querySelectorAll('#rv_service_records_tbody tr');
                            const lastRow = rows[rows.length - 1];
                            // Only set the date if it's not already set to current date
                            if (!lastRow.cells[0].querySelector('input').value) {
                                lastRow.cells[0].querySelector('input').value = record.date || '';
                            }
                            lastRow.cells[1].querySelector('input').value = record.problem || '';
                            lastRow.cells[2].querySelector('input').value = record.action || '';
                            lastRow.cells[3].querySelector('input').value = record.remarks || '';
                        });
                    }
                }

                // Calculate incomes after populating data
                setTimeout(calculateIncomes, 100);
            }

            // Signature modal variables
            let signaturePad = null;
            let currentSignatureType = '';

            // Signature modal functions
            function openSignatureModal(type) {
                currentSignatureType = type;
                document.getElementById('signatureModalTitle').textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' Signature';
                document.getElementById('signatureModal').classList.remove('hidden');
                document.body.classList.add('modal-open');

                // Initialize signature pad
                const canvas = document.getElementById('signatureCanvas');
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 1,
                    maxWidth: 3
                });

                // Resize canvas for proper rendering
                resizeCanvas(canvas);
            }

            function closeSignatureModal() {
                document.getElementById('signatureModal').classList.add('hidden');
                document.body.classList.remove('modal-open');
                if (signaturePad) {
                    signaturePad.clear();
                }
            }

            function clearSignature() {
                if (signaturePad) {
                    signaturePad.clear();
                }
            }

            function saveSignature() {
                if (!signaturePad) return;

                if (signaturePad.isEmpty()) {
                    Swal.fire({
                        title: 'No Signature',
                        text: 'Please provide a signature before saving.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const signatureData = signaturePad.toDataURL();
                document.getElementById('signature_' + currentSignatureType).value = signatureData;

                // Update button text to show signature is saved
                const button = document.querySelector(`button[onclick="openSignatureModal('${currentSignatureType}')"]`);
                if (button) {
                    button.textContent = 'Signature Saved ✓';
                    button.classList.remove('bg-gray-100', 'hover:bg-gray-200');
                    button.classList.add('bg-green-100', 'hover:bg-green-200', 'text-green-800');
                }

                closeSignatureModal();

                Swal.fire({
                    title: 'Signature Saved',
                    text: 'The signature has been saved successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }

            function resizeCanvas(canvas) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                if (signaturePad) {
                    signaturePad.clear();
                }
            }

            // Print Intake Sheet function
            function printIntakeSheet() {
                // Collect form data
                const formData = {
                    serial_number: document.getElementById('serial_number').value,
                    applicant_fname: document.getElementById('applicant_fname').value,
                    applicant_mname: document.getElementById('applicant_mname').value,
                    applicant_lname: document.getElementById('applicant_lname').value,
                    applicant_suffix: document.getElementById('applicant_suffix').value,
                    head_4ps: document.getElementById('head_4ps').value,
                    head_ipno: document.getElementById('head_ipno').value,
                    head_address: document.getElementById('head_address').value,
                    head_zone: document.getElementById('head_zone').value,
                    head_barangay: document.getElementById('head_barangay').value,
                    head_dob: document.getElementById('head_dob').value,
                    head_pob: document.getElementById('head_pob').value,
                    applicant_gender: document.getElementById('applicant_gender').value,
                    head_educ: document.getElementById('head_educ').value,
                    head_occ: document.getElementById('head_occ').value,
                    head_religion: document.getElementById('head_religion').value,
                    other_income: document.getElementById('other_income').value,
                    house_total_income: document.getElementById('house_total_income').value,
                    house_net_income: document.getElementById('house_net_income').value,
                    house_house: document.getElementById('house_house').value,
                    house_house_rent: document.getElementById('house_house_rent').value,
                    house_lot: document.getElementById('house_lot').value,
                    house_lot_rent: document.getElementById('house_lot_rent').value,
                    house_water: document.getElementById('house_water').value,
                    house_electric: document.getElementById('house_electric').value,
                    remarks: document.getElementById('remarks').value,
                    worker_name: document.getElementById('worker_name').value,
                    officer_name: document.getElementById('officer_name').value,
                    date_entry: document.getElementById('date_entry').value,
                    signature_client: document.getElementById('signature_client').value,
                    signature_worker: document.getElementById('signature_worker').value,
                    signature_officer: document.getElementById('signature_officer').value
                };

                // Collect family members data
                let familyMembers = [];
                const familyRows = document.querySelectorAll('#family_members_tbody tr');
                familyRows.forEach(row => {
                    const cells = row.cells;
                    familyMembers.push({
                        name: cells[0].querySelector('input').value || '',
                        relationship: cells[1].querySelector('select').value || '',
                        birthdate: cells[2].querySelector('input').value || '',
                        age: cells[3].querySelector('input').value || '',
                        sex: cells[4].querySelector('select').value || '',
                        civil_status: cells[5].querySelector('select').value || '',
                        education: cells[6].querySelector('select').value || '',
                        occupation: cells[7].querySelector('input').value || '',
                        monthly_income: cells[8].querySelector('input').value || '',
                        remarks: cells[9].querySelector('select').value || '',
                    });
                });

                // Collect service records data
                let serviceRecords = [];
                const serviceRows = document.querySelectorAll('#rv_service_records_tbody tr');
                serviceRows.forEach(row => {
                    const cells = row.cells;
                    serviceRecords.push({
                        date: cells[0].querySelector('input').value || '',
                        problem: cells[1].querySelector('input').value || '',
                        action: cells[2].querySelector('input').value || '',
                        remarks: cells[3].querySelector('input').value || '',
                    });
                });

                // Create printable HTML
                const printWindow = window.open('', '_blank');
                const printContent = `
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Family Intake Sheet - Print</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                font-size: 12px;
                                line-height: 1.4;
                                margin: 0;
                                padding: 20px;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 20px;
                                border-bottom: 2px solid #333;
                                padding-bottom: 10px;
                            }
                            .header h1 {
                                margin: 0;
                                font-size: 18px;
                            }
                            .section {
                                margin-bottom: 20px;
                                border: 1px solid #e5e7eb;
                                padding: 15px;
                                border-radius: 5px;
                            }
                            .section h3 {
                                margin: 0 0 10px 0;
                                font-size: 14px;
                                border-bottom: 1px solid #e5e7eb;
                                padding-bottom: 5px;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-top: 10px;
                            }
                            th, td {
                                border: 1px solid #e5e7eb;
                                padding: 5px;
                                text-align: left;
                            }
                            th {
                                background-color: #f9fafb;
                                font-weight: 600;
                            }
                            .info-table {
                                margin-bottom: 15px;
                            }
                            .info-table td {
                                padding: 3px 5px;
                            }
                            .signature-section {
                                border-top: 1px solid #333;
                                margin-top: 30px;
                                padding-top: 20px;
                            }
                            .signature-line {
                                border-top: 1px solid #333;
                                width: 200px;
                                margin-top: 30px;
                                text-align: center;
                                display: inline-block;
                            }
                            .remarks-grid {
                                display: grid;
                                grid-template-columns: repeat(2, 1fr);
                                gap: 10px;
                                margin-top: 10px;
                            }
                            .remark-item {
                                padding: 5px;
                                border: 1px solid #e5e7eb;
                                border-radius: 3px;
                                font-size: 11px;
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
                                @page {
                                    size: landscape;
                                    margin: 4mm;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1>LYDO Scholarship</h1>
                            <h2>Family Intake Sheet</h2>
                        </div>

                        <div class="section">
                            <h3>Head of Family</h3>
                            <table class="info-table">
                                <tr>
                                    <td><strong>Serial No.:</strong> ${formData.serial_number || "AUTO_GENERATED"}</td>
                                    <td><strong>Name:</strong> ${[formData.applicant_fname, formData.applicant_mname, formData.applicant_lname, formData.applicant_suffix].filter(Boolean).join(" ")}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sex:</strong> ${formData.applicant_gender || "-"}</td>
                                    <td><strong>4Ps:</strong> ${formData.head_4ps || "-"}</td>
                                    <td><strong>IP No.:</strong> ${formData.head_ipno || "-"}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong> ${formData.head_address || "-"}</td>
                                    <td><strong>Zone:</strong> ${formData.head_zone || "-"}</td>
                                    <td><strong>Barangay:</strong> ${formData.head_barangay || "-"}</td>
                                    
                                </tr>
                                <tr>
                                    <td><strong>Date of Birth:</strong> ${formatDate(formData.head_dob) || "-"}</td>
                                    <td><strong>Place of Birth:</strong> ${formData.head_pob || "-"}</td>
                                </tr>
                                <tr>
                                    <td><strong>Educational Attainment:</strong> ${formData.head_educ || "-"}</td>
                                    <td><strong>Occupation:</strong> ${formData.head_occ || "-"}</td>
                                    <td><strong>Religion:</strong> ${formData.head_religion || "-"}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="section">
                            <h3>Family Members</h3>
                            <table>
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
                                        <th>Monthly Income</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${familyMembers.map(f => `
                                        <tr>
                                            <td>${escapeHtml(f.name || '')}</td>
                                            <td>${escapeHtml(f.relationship || '')}</td>
                                            <td>${formatDate(f.birthdate) || ''}</td>
                                            <td>${escapeHtml(f.age || '')}</td>
                                            <td>${escapeHtml(f.sex || '')}</td>
                                            <td>${escapeHtml(f.civil_status || '')}</td>
                                            <td>${escapeHtml(f.education || '')}</td>
                                            <td>${escapeHtml(f.occupation || '')}</td>
                                            <td>₱${escapeHtml(f.monthly_income || '')}</td>
                                            <td>${escapeHtml(f.remarks || '')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                            <div class="remarks-grid">
                                <div class="remark-item">Out of School Youth (OSY)</div>
                                <div class="remark-item">Solo Parent (SP)</div>
                                <div class="remark-item">Person with Disability (PWD)</div>
                                <div class="remark-item">Senior Citizen (SC)</div>
                                <div class="remark-item">Lactating Mother</div>
                                <div class="remark-item">Pregnant Mother</div>
                            </div>
                        </div>

                        <div class="section">
                            <h3>Household Information</h3>
                            <table class="info-table">
                                <tr>
                                    <td><strong>Other Source of Income:</strong> ₱${formData.other_income || "-"}</td>
                                    <td><strong>Total Family Income:</strong> ₱${formData.house_total_income || "-"}</td>
                                    <td><strong>Total Family Net Income:</strong> ₱${formData.house_net_income || "-"}</td>
                                </tr>
                                <tr>
                                    <td><strong>House (Owned/Rented):</strong> ${formData.house_house || "-"} ${formData.house_house_rent ? `(Rent: ₱${formData.house_house_rent})` : ''}</td>
                                    <td><strong>Lot (Owned/Rented):</strong> ${formData.house_lot || "-"} ${formData.house_lot_rent ? `(Rent: ₱${formData.house_lot_rent})` : ''}</td>
                                    <td><strong>Water:</strong> ₱${formData.house_water || "-"}</td>
                                    <td><strong>Electricity Source:</strong> ₱${formData.house_electric || "-"}</td>
                                </tr>
                                <tr>
                                    <td><strong>Remarks:</strong> ${formData.remarks || "-"}</td>
                                </tr>
                            </table>
                        </div>

                        ${serviceRecords.length > 0 ? `
                        <div class="section">
                            <h3>Social Service Records</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Problem/Need</th>
                                        <th>Action/Assistance Given</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${serviceRecords.map(r => `
                                        <tr>
                                            <td>${formatDate(r.date) || ''}</td>
                                            <td>${escapeHtml(r.problem || '')}</td>
                                            <td>${escapeHtml(r.action || '')}</td>
                                            <td>${escapeHtml(r.remarks || '')}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        ` : ''}

                        <div class="section signature-section">
                            <h3>Signatures</h3>
                            <table class="info-table">
                                <tr>
                                    <td><strong>Worker Name:</strong> ${formData.worker_name || "-"}</td>
                                    <td><strong>Officer Name:</strong> ${formData.officer_name || "-"}</td>
                                    <td><strong>Date Entry:</strong> ${formatDate(formData.date_entry) || "-"}</td>
                                </tr>
                            </table>
                            <div style="margin-top: 40px;">
                                <div style="display: inline-block; margin-right: 100px;">
                                    <p><strong>Family Head Signature:</strong></p>
                                    <div class="signature-line"></div>
                                </div>
                                <div style="display: inline-block; margin-right: 100px;">
                                    <p><strong>Social Worker Signature:</strong></p>
                                    <div class="signature-line"></div>
                                </div>
                                <div style="display: inline-block;">
                                    <p><strong>Officer Signature:</strong></p>
                                    <div class="signature-line"></div>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>
                `;

                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
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
    </div>
</body>
</html>