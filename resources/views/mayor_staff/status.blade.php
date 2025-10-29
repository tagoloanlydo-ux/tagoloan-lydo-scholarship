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

    <style>
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

                    <!-- ✅ Applicants -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex gap-2">
                            <div onclick="showTable()" class="tab active" id="tab-pending">
                                <i class="fas fa-table mr-1"></i> Pending Status
                            </div>
                            <div onclick="showList()" class="tab tab-green" id="tab-approved-rejected">
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
                        <div class="flex gap-2 mb-4">
                            <input type="text" id="nameSearch" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                            <select id="barangayFilter" class="border rounded px-3 py-2">
                                <option value="">All Barangays</option>
                                @foreach($barangays ?? [] as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-violet-600 to-violet-800 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Remarks</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Intake Sheet</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($applications ?? [] as $index => $app)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->barangay }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->remarks == 'Poor') bg-red-100 text-red-800
                                            @elseif($app->remarks == 'Ultra Poor') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->remarks }}
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
                                    <td colspan="5" class="text-center py-4 border border-gray-200 text-gray-500">No applicants pending status.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            @if(isset($applications) && is_object($applications) && method_exists($applications, 'appends'))
                                {{ $applications->appends(request()->query())->links() }}
                            @endif
                        </div>
                    </div>

                    <!-- Approved/Rejected Tab -->
                    <div id="listView" class="overflow-x-auto hidden">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-white p-3 rounded-lg border border-gray-200">
                            ✅ Approved/Rejected: View applicants with assigned status.
                            </h3>
                        </div>
                        <div class="flex gap-2 mb-4">
                            <input type="text" id="listNameSearch" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                            <select id="listBarangayFilter" class="border rounded px-3 py-2">
                                <option value="">All Barangays</option>
                                @foreach($barangays ?? [] as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-green-800 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Intake Sheet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listApplications ?? [] as $index => $app)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->barangay }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->status == 'Approved') bg-green-100 text-green-800
                                            @elseif($app->status == 'Rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->status }}
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
                                    <td colspan="5" class="text-center py-4 border border-gray-200 text-gray-500">No approved/rejected applicants.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            @if(isset($listApplications) && is_object($listApplications) && method_exists($listApplications, 'appends'))
                                {{ $listApplications->appends(request()->query())->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Modal for Intake Sheet -->
        <div id="reviewModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto">
            <div class="bg-white w-full max-w-6xl rounded-2xl shadow-2xl p-6 max-h-screen overflow-y-auto relative">
                <!-- Close button -->
                <button type="button" onclick="closeReviewModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
                    <i class="fas fa-times text-2xl"></i>
                </button>

                <div class="flex items-center text-xl font-semibold mb-4">
                    <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Logo" class="h-8 w-auto mr-2">
                    Family Intake Sheet - Review
                </div>

                <!-- Tab Navigation -->
                <div class="flex border-b border-gray-200 mb-6">
                    <button type="button" id="tab-family" class="tab-button active px-4 py-2 text-sm font-medium text-violet-600 border-b-2 border-violet-600">Family Details</button>
                    <button type="button" id="tab-family-members" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600">Family Members</button>
                    <button type="button" id="tab-additional" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600">Additional Info</button>
                    <button type="button" id="tab-social-service" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600">Social Service Records</button>
                    <button type="button" id="tab-health" class="tab-button px-4 py-2 text-sm font-medium text-gray-500 hover:text-violet-600">Health & Signatures</button>
                </div>

                <div id="modalReviewContent">
                    <!-- Content will be populated here -->
                </div>

                <div class="modal-actions">
                    <form id="modalStatusForm" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="status" id="modalStatus">
                        <div class="flex gap-2">
                            <select name="status" id="statusDropdown" class="border border-gray-300 rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Set Status</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Update Status</button>
                        </div>
                    </form>
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
            let currentApplicationId = null;

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

            // Open review modal
            function openReviewModal(button) {
                const id = button.getAttribute("data-id");
                currentApplicationId = id;
                
                // Fetch intake sheet data
                fetch(`/mayor_staff/intake-sheet/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            populateReviewModal(data);
                            document.getElementById('reviewModal').style.display = 'block';
                            
                            // Show the status form for pending applications
                            const modalStatusForm = document.getElementById('modalStatusForm');
                            const statusDropdown = document.getElementById('statusDropdown');
                            
                            // Check if this application is in the pending table
                            const isPending = Array.from(document.querySelectorAll('#tableView [data-id]'))
                                .some(btn => btn.getAttribute('data-id') === id);
                            
                            if (isPending) {
                                modalStatusForm.style.display = 'block';
                                modalStatusForm.action = `/mayor_staff/status/${id}`;
                                // Set current status if available
                                if (data.status) {
                                    statusDropdown.value = data.status;
                                }
                            } else {
                                modalStatusForm.style.display = 'none';
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching intake sheet data:', err));
            }

            // Close review modal
            function closeReviewModal() {
                document.getElementById('reviewModal').style.display = 'none';
                currentApplicationId = null;
            }

            // Populate review modal with data
            function populateReviewModal(d) {
                const modalContent = document.getElementById('modalReviewContent');

                // Family Details Tab Content
                const familyDetailsContent = `
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
                    </div>
                `;

                // Family Members Tab Content
                const familyMembersContent = `
                    <div class="space-y-4">
                        <div class="print-box p-4">
                            <h4 class="font-semibold mb-4">Family Members</h4>
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
                                        `).join('') : '<tr><td colspan="10" class="text-center py-4">No family members data available</td></tr>';
                                    })()}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                // Additional Info Tab Content
                const additionalInfoContent = `
                    <div class="space-y-4">
                        <div class="print-box p-4">
                            <h4 class="font-semibold mb-4">Household Information</h4>
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
                    </div>
                `;

                // Social Service Records Tab Content
                const socialServiceContent = `
                    <div class="space-y-4">
                        <div class="print-box p-4">
                            <h4 class="font-semibold mb-4">SOCIAL SERVICE RECORD</h4>
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
                    </div>
                `;

                // Health & Signatures Tab Content
                const healthSignaturesContent = `
                    <div class="space-y-4">
                        <div class="print-box p-4">
                            <h4 class="font-semibold mb-4">HEALTH CONDITION & CODES</h4>
                            <p><strong>Health Condition:</strong></p>
                            <p class="ml-2">
                                A. DEAD • B. INJURED • C. MISSING • D. With Illness
                            </p>
                        </div>

                        <div class="print-box p-4">
                            <h4 class="font-semibold mb-4">Signatures</h4>
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
                `;

                // Set initial content to Family Details
                modalContent.innerHTML = familyDetailsContent;

                // Store content for each tab
                modalContent.dataset.familyDetails = familyDetailsContent;
                modalContent.dataset.familyMembers = familyMembersContent;
                modalContent.dataset.additionalInfo = additionalInfoContent;
                modalContent.dataset.socialService = socialServiceContent;
                modalContent.dataset.healthSignatures = healthSignaturesContent;

                // Add tab switching functionality
                setupTabSwitching();
            }

            function setupTabSwitching() {
                const tabButtons = document.querySelectorAll('.tab-button');
                const modalContent = document.getElementById('modalReviewContent');

                tabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        // Remove active class from all tabs
                        tabButtons.forEach(btn => {
                            btn.classList.remove('active', 'text-violet-600', 'border-violet-600');
                            btn.classList.add('text-gray-500');
                            btn.classList.remove('border-b-2');
                        });

                        // Add active class to clicked tab
                        this.classList.add('active', 'text-violet-600', 'border-violet-600');
                        this.classList.remove('text-gray-500');
                        this.classList.add('border-b-2');

                        // Switch content based on tab
                        const tabId = this.id;
                        switch(tabId) {
                            case 'tab-family':
                                modalContent.innerHTML = modalContent.dataset.familyDetails;
                                break;
                            case 'tab-family-members':
                                modalContent.innerHTML = modalContent.dataset.familyMembers;
                                break;
                            case 'tab-additional':
                                modalContent.innerHTML = modalContent.dataset.additionalInfo;
                                break;
                            case 'tab-social-service':
                                modalContent.innerHTML = modalContent.dataset.socialService;
                                break;
                            case 'tab-health':
                                modalContent.innerHTML = modalContent.dataset.healthSignatures;
                                break;
                        }
                    });
                });
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

                // Add event listener for modal status form
                const modalStatusForm = document.getElementById('modalStatusForm');
                if (modalStatusForm) {
                    modalStatusForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const statusSelect = document.getElementById('statusDropdown');
                        const selectedValue = statusSelect.value;
                        const selectedText = statusSelect.options[statusSelect.selectedIndex].text;

                        if (!selectedValue) {
                            Swal.fire('Error', 'Please select a status before updating.', 'error');
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
                                    submitForm(modalStatusForm, selectedValue, reason);
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
                                submitForm(modalStatusForm, selectedValue);
                            }
                        });
                    });
                }

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
                                // Close modal if it's open
                                closeReviewModal();
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
                const nameSearch = document.getElementById('nameSearch');
                const barangayFilter = document.getElementById('barangayFilter');

                if (nameSearch) {
                    nameSearch.addEventListener('input', debounce(filterTableView, 150));
                }
                if (barangayFilter) {
                    barangayFilter.addEventListener('change', filterTableView);
                }

                // Add event listeners for list view filters (debounced on input)
                const listNameSearch = document.getElementById('listNameSearch');
                const listBarangayFilter = document.getElementById('listBarangayFilter');

                if (listNameSearch) {
                    listNameSearch.addEventListener('input', debounce(filterListView, 150));
                }
                if (listBarangayFilter) {
                    listBarangayFilter.addEventListener('change', filterListView);
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

        <!-- Rest of your existing polling scripts remain the same -->
    </body>
</html>