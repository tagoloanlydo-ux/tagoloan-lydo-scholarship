
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <link rel="stylesheet" href="{{ asset('css/mayor_status.css') }}" />

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

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            background: #e5e7eb;
            color: #6b7280;
            transition: all 0.3s;
        }

        .tab.active {
            background: #7c3aed;
            color: white;
        }

        .tab-green.active {
            background: #059669;
            color: white;
        }

        .debug-info {
            font-size: 0.7rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 2px 4px;
            border-radius: 3px;
            margin-top: 2px;
        }

        /* Enhanced Professional Styles */
        .print-box {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .print-box:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .thin-border {
            border: 1px solid #e5e7eb;
        }

        .review-columns {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .intake-section {
            margin-bottom: 2rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .intake-section:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .intake-section-title {
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 0.75rem;
            color: #0f172a;
            font-size: 1.1rem;
        }

        .intake-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .intake-table th,
        .intake-table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 16px;
            text-align: left;
        }

        .intake-table th {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .intake-table tbody tr:hover {
            background-color: #f8fafc;
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
    </style>
</head>
<body class="bg-gray-50">
    @php
        $applications = $applications ?? [];
        $listApplications = $listApplications ?? [];
        $notifications = $notifications ?? collect();
        $showBadge = $showBadge ?? false;
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
            <!-- Sidebar -->
            <div class="w-100 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
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
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        {{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}
                                        <!-- DEBUG INFO -->
                                        <div class="debug-info">
                                            Status: {{ $app->status }} | 
                                            Screening: {{ $app->initial_screening }} | 
                                            Role: {{ $app->role }}
                                        </div>
                                    </td>
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
                                            data-id="{{ $app->application_personnel_id }}">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 border border-gray-200 text-gray-500">
                                        No applicants pending status.
                                    </td>
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
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        {{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}
                                        <!-- DEBUG INFO -->
                                        <div class="debug-info">
                                            Screening: {{ $app->initial_screening }} | 
                                            Remarks: {{ $app->remarks }} | 
                                            Role: {{ $app->role }}
                                        </div>
                                    </td>
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



        <script>
            let currentApplicationId = null;

            function showTable() {
                document.getElementById("tableView").classList.remove("hidden");
                document.getElementById("listView").classList.add("hidden");
                document.querySelector('.tab.active').classList.remove('active');
                document.querySelectorAll('.tab')[0].classList.add('active');
                localStorage.setItem("viewMode", "table");
                if (typeof filterTable === 'function') filterTable();
            }

            function showList() {
                document.getElementById("listView").classList.remove("hidden");
                document.getElementById("tableView").classList.add("hidden");
                document.querySelector('.tab.active').classList.remove('active');
                document.querySelectorAll('.tab')[1].classList.add('active');
                localStorage.setItem("viewMode", "list");
                if (typeof filterList === 'function') filterList();
            }

            // Open review modal
            function openReviewModal(button) {
                const id = button.getAttribute("data-id");
                currentApplicationId = id;

                const modal = document.getElementById('reviewModal');
                const modalContent = document.getElementById('modalReviewContent');
                modalContent.innerHTML = '<p class="p-4 text-center">Loading intake sheet...</p>';
                modal.style.display = 'block';

                fetch(`/api/mayor-staff/intake-sheet/${id}`, { credentials: 'same-origin' })
                    .then(async response => {
                        const ct = response.headers.get('content-type') || '';
                        let data = null;
                        try {
                            if (ct.includes('application/json')) {
                                data = await response.json();
                            } else {
                                // server might return HTML or JSON embedded in HTML — try to parse
                                const text = await response.text();
                                try {
                                    data = JSON.parse(text);
                                } catch (e) {
                                    // try to extract JSON from a snippet in HTML (best-effort)
                                    const m = text.match(/\{[\s\S]*\}/);
                                    if (m) {
                                        try { data = JSON.parse(m[0]); } catch (e2) { data = null; }
                                    }
                                }
                            }
                        } catch (err) {
                            console.error('Error parsing response:', err);
                        }

                        console.log('intake-sheet response:', { status: response.status, contentType: ct, data });

                        if (response.status === 404) {
                            modalContent.innerHTML = '<p class="p-4 text-center text-blue-600">Intake sheet not yet submitted by the applicant.</p>';
                        } else if (data && !data.error) {
                            populateReviewModal(data);
                        } else {
                            modalContent.innerHTML = '<p class="p-4 text-center text-red-600">No intake sheet data found.</p>';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching intake sheet data:', err);
                        document.getElementById('modalReviewContent').innerHTML = '<p class="p-4 text-center text-red-600">Error loading intake sheet data.</p>';
                    });
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

            function resolveUrl(path) {
                try {
                    if (!path) return path;
                    if (path.startsWith('http://') || path.startsWith('https://')) return path;
                    const base = window.location.origin;
                    return base + (path.startsWith('/') ? '' : '/') + path;
                } catch (e) { return path; }
            }

            // Populate review modal with data - COMPLETE VERSION
            function populateReviewModal(rawData) {
                const modalContent = document.getElementById('modalReviewContent');
                const d = normalizeData(rawData || {});

                modalContent.innerHTML = `
                    <div class="review-columns">
                        <div class="space-y-4">
                            <!-- Header Section -->
                            <div class="intake-section">
                                <div class="text-center mb-4">
                                    <h2 class="text-2xl font-bold text-gray-800">LYDO Scholarship</h2>
                                    <h3 class="text-xl font-semibold text-gray-600">Family Intake Sheet</h3>
                                </div>
                                
                                <div class="print-box p-4">
                                    <h4 class="intake-section-title">Head of Family</h4>
                                    <table class="min-w-full text-sm">
                                        <tr>
                                            <td><strong>Serial No.:</strong> ${d.serial_number || "AUTO_GENERATED"}</td>
                                            <td><strong>Name:</strong> ${[d.applicant_fname, d.applicant_mname, d.applicant_lname, d.applicant_suffix]
                                                .filter(Boolean)
                                                .join(" ")}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sex:</strong> ${d.applicant_gender || "-"}</td>
                                            <td><strong>4Ps:</strong> ${d.head_4ps || "-"}</td>
                                            <td><strong>IP No.:</strong> ${d.head_ipno || "-"}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong> ${d.head_address || "-"}</td>
                                            <td><strong>Zone:</strong> ${d.head_zone || "-"}</td>
                                            <td><strong>Barangay:</strong> ${d.head_barangay || "-"}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date of Birth:</strong> ${formatDate(d.head_dob) || "-"}</td>
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
                            
                            <!-- Family Members Section -->
                            <div class="intake-section">
                                <h4 class="intake-section-title">Family Members</h4>
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
                                                <th>Monthly Income</th>
                                                <th>Remarks</th>
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
                                                        <td>${escapeHtml(f.name || '')}</td>
                                                        <td>${escapeHtml(f.relationship || '')}</td>
                                                        <td>${formatDate(f.birthdate)}</td>
                                                        <td>${escapeHtml(f.age || '')}</td>
                                                        <td>${escapeHtml(f.sex || '')}</td>
                                                        <td>${escapeHtml(f.civil_status || '')}</td>
                                                        <td>${escapeHtml(f.education || '')}</td>
                                                        <td>${escapeHtml(f.occupation || '')}</td>
                                                        <td>₱${escapeHtml(f.monthly_income || '')}</td>
                                                        <td>${escapeHtml(f.remarks || '')}</td>
                                                    </tr>
                                                `).join('') : '<tr><td colspan="10" class="text-center py-4 text-gray-500">No family members data</td></tr>';
                                            })()}
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Remarks Categories -->
                                <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                                    <div class="text-gray-600 border border-gray-300 rounded p-2">Out of School Youth (OSY)</div>
                                    <div class="text-gray-600 border border-gray-300 rounded p-2">Solo Parent (SP)</div>
                                    <div class="text-gray-600 border border-gray-300 rounded p-2">Person with Disability (PWD)</div>
                                    <div class="text-gray-600 border border-gray-300 rounded p-2">Senior Citizen (SC)</div>
                                    <div class="text-gray-600 border border-gray-300 rounded p-2">Lactating Mother</div>
                                    <div class="text-gray-600 border border-gray-300 rounded p-2">Pregnant Mother</div>
                                </div>
                            </div>
                            
                            <!-- Household Information Section -->
                            <div class="intake-section">
                                <h4 class="intake-section-title">Household Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-3">
                                        <div class="bg-blue-50 p-3 rounded-lg">
                                            <h5 class="font-semibold text-blue-800 mb-2">Income Calculation</h5>
                                            <p><strong>Other Source of Income:</strong> ₱${d.other_income || "0.00"}</p>
                                            <p><strong>Total Family Income:</strong> ₱${d.house_total_income || "0.00"}</p>
                                            <p><strong>Total Family Net Income:</strong> ₱${d.house_net_income || "0.00"}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="bg-red-50 p-3 rounded-lg">
                                            <h5 class="font-semibold text-red-800 mb-2">Expenses</h5>
                                            <p><strong>House:</strong> ${d.house_house || "-"} ${d.house_house_rent ? `(Rent: ₱${d.house_house_rent})` : ''}</p>
                                            <p><strong>Lot:</strong> ${d.house_lot || "-"} ${d.house_lot_rent ? `(Rent: ₱${d.house_lot_rent})` : ''}</p>
                                            <p><strong>Water:</strong> ₱${d.house_water || "0.00"}</p>
                                            <p><strong>Electricity:</strong> ₱${d.house_electric || "0.00"}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 bg-green-50 p-3 rounded-lg">
                                    <h5 class="font-semibold text-green-800 mb-2">Final Assessment</h5>
                                    <p><strong>Remarks:</strong> 
                                        <span class="px-2 py-1 text-sm rounded-lg ${
                                            d.remarks === 'Poor' ? 'bg-red-100 text-red-800' :
                                            d.remarks === 'Ultra Poor' ? 'bg-orange-100 text-orange-800' :
                                            d.remarks === 'Non Poor' ? 'bg-yellow-100 text-yellow-800' :
                                            'bg-gray-100 text-gray-800'
                                        }">
                                            ${d.remarks || "Not Assigned"}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Social Service Records Section -->
                            <div class="intake-section">
                                <h4 class="intake-section-title">Social Service Records</h4>
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
                                                        <td>${formatDate(r.date)}</td>
                                                        <td>${escapeHtml(r.problem || '')}</td>
                                                        <td>${escapeHtml(r.action || '')}</td>
                                                        <td>${escapeHtml(r.remarks || '')}</td>
                                                    </tr>
                                                `).join('') : '<tr><td colspan="4" class="text-center py-4 text-gray-500">No social service records found</td></tr>';
                                            })()}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3 text-sm text-gray-600">
                                    <p><strong>Health Condition Codes:</strong> A. DEAD • B. INJURED • C. MISSING • D. With Illness</p>
                                </div>
                            </div>
                            
                            <!-- Signatures Section -->
                            <div class="intake-section">
                                <h4 class="intake-section-title">Signatures</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <p class="font-semibold mb-2">Family Head</p>
                                        <div class="border-2 border-gray-300 rounded-lg p-4 h-32 flex items-center justify-center">
                                            ${d.signature_client ? 
                                                `<img src="${d.signature_client}" style="max-width: 100%; max-height: 80px;" />` : 
                                                '<p class="text-gray-500 text-sm">No signature</p>'
                                            }
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <p class="font-semibold mb-2">Social Worker</p>
                                        <div class="border-2 border-gray-300 rounded-lg p-4 h-32 flex items-center justify-center">
                                            ${d.signature_worker ? 
                                                `<img src="${d.signature_worker}" style="max-width: 100%; max-height: 80px;" />` : 
                                                '<p class="text-gray-500 text-sm">No signature</p>'
                                            }
                                        </div>
                                        <p class="mt-2 text-sm">${d.worker_name || "Not specified"}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="font-semibold mb-2">Officer</p>
                                        <div class="border-2 border-gray-300 rounded-lg p-4 h-32 flex items-center justify-center">
                                            ${d.signature_officer ? 
                                                `<img src="${d.signature_officer}" style="max-width: 100%; max-height: 80px;" />` : 
                                                '<p class="text-gray-500 text-sm">No signature</p>'
                                            }
                                        </div>
                                        <p class="mt-2 text-sm">${d.officer_name || "Not specified"}</p>
                                    </div>
                                </div>
                                <div class="mt-4 text-center">
                                    <p><strong>Date Entry:</strong> ${formatDate(d.date_entry) || "Not specified"}</p>
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

            // Filter functions
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

            // Load view mode preference
            document.addEventListener("DOMContentLoaded", function() {
                let viewMode = localStorage.getItem("viewMode") || "table";
                if(viewMode === "list") {
                    showList();
                } else {
                    showTable();
                }

                // Add event listeners for filtering
                document.getElementById('nameSearch').addEventListener('input', filterTable);
                document.getElementById('barangayFilter').addEventListener('change', filterTable);
                document.getElementById('listNameSearch').addEventListener('input', filterList);
                document.getElementById('listBarangayFilter').addEventListener('change', filterList);

                // Initial filter application
                if (typeof filterTableView === 'function') filterTableView();
                if (typeof filterListView === 'function') filterListView();
            });

            // Notification functionality
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

            // Close review modal
            function closeReviewModal() {
                document.getElementById('reviewModal').style.display = 'none';
                currentApplicationId = null;
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
    </body>
</html>