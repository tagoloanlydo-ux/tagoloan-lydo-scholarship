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
        /* Modern Design System */
        :root {
            --primary-color: #7c3aed;
            --primary-dark: #6d28d9;
            --secondary-color: #059669;
            --danger-color: #dc2626;
            --warning-color: #ea580c;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --border-radius: 8px;
            --border-radius-lg: 12px;
            --transition: all 0.2s ease-in-out;
        }

        /* Enhanced Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            overflow-y: auto;
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 0;
            border-radius: var(--border-radius-lg);
            width: 95%;
            max-width: 1200px;
            box-shadow: var(--shadow-xl);
            animation: modalSlideIn 0.3s ease-out;
            overflow: hidden;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-bottom: 1px solid var(--gray-200);
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
            padding: 0.5rem;
            border-radius: 50%;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 2rem;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            padding: 1.5rem 2rem;
            background-color: var(--gray-50);
            border-top: 1px solid var(--gray-200);
        }

        /* Enhanced Tab Styles */
        .tab {
            padding: 12px 24px;
            cursor: pointer;
            border-radius: var(--border-radius);
            background: var(--gray-100);
            color: var(--gray-600);
            transition: var(--transition);
            font-weight: 500;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab:hover {
            background: var(--gray-200);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .tab.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: var(--shadow);
        }

        .tab-green.active {
            background: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }

        /* Enhanced Table Styles */
        .main-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            background: white;
        }

        .main-table thead {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .main-table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .main-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .main-table tbody tr {
            transition: var(--transition);
        }

        .main-table tbody tr:hover {
            background-color: var(--gray-50);
            transform: scale(1.01);
        }

        .main-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Enhanced Button Styles */
        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-success {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-success:hover {
            background: #047857;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-danger:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-secondary:hover {
            background: var(--gray-300);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        /* Enhanced Intake Section Styles */
        .intake-section {
            margin-bottom: 2rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            background: white;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .intake-section:hover {
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .intake-section-title {
            font-weight: 700;
            margin-bottom: 2rem;
            border-bottom: 4px solid var(--primary-color);
            padding-bottom: 1rem;
            color: var(--gray-800);
            font-size: 1.5rem;
            line-height: 1.3;
            letter-spacing: 0.025em;
        }

        .intake-section p {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 0.75rem;
            color: var(--gray-700);
            font-weight: 500;
        }

        .intake-section strong {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 1.125rem;
        }

        /* Enhanced Intake Table */
        .intake-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            background: white;
            font-size: 0.875rem;
        }

        .intake-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .intake-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-700);
            font-weight: 500;
        }

        .intake-table tbody tr:hover {
            background-color: var(--gray-50);
        }

        .intake-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background-color: #fef2f2;
            color: #991b1b;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-poor {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .status-ultra-poor {
            background-color: #fed7aa;
            color: #ea580c;
        }

        /* Enhanced Search and Filter */
        .search-container {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        .search-input {
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: var(--transition);
            width: 100%;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .filter-select {
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: var(--transition);
            background: white;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        /* Enhanced Info Box */
        .info-box {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
            border-radius: var(--border-radius);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .info-box.success {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            border-color: #16a34a;
        }

        .info-box p {
            margin: 0;
            font-weight: 500;
            color: var(--gray-800);
        }

        /* Responsive Design Improvements */
        @media (max-width: 768px) {
            .modal-content {
                width: 98%;
                margin: 1% auto;
            }

            .modal-body {
                padding: 1rem;
            }

            .intake-section {
                padding: 1rem;
            }

            .main-table th,
            .main-table td {
                padding: 0.75rem 1rem;
            }

            .tab {
                padding: 10px 16px;
                font-size: 0.875rem;
            }

            .btn-primary,
            .btn-success,
            .btn-danger,
            .btn-secondary {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }
        }

        /* Print Styles */
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

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Animation for new elements */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
                                    <th class="px-4 py-3 border border-gray-200 text-center">4Ps</th>
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
                                        {{ $app->head_4ps ?? 'N/A' }}
                                    </td>
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
                                        <div class="flex gap-2 justify-center">
                                            <button
                                                title="View Intake Sheet"
                                                class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow view-intake-btn"
                                                data-id="{{ $app->application_personnel_id }}"
                                                data-name="{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </button>
                                            <button
                                                title="Approve Application"
                                                class="px-3 py-1 text-sm bg-green-500 hover:bg-green-600 text-white rounded-lg shadow approve-btn"
                                                data-id="{{ $app->application_personnel_id }}"
                                                data-name="{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                            <button
                                                title="Reject Application"
                                                class="px-3 py-1 text-sm bg-red-500 hover:bg-red-600 text-white rounded-lg shadow reject-btn"
                                                data-id="{{ $app->application_personnel_id }}"
                                                data-name="{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}">
                                                <i class="fas fa-times mr-1"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">
                                        No applicants pending status matching the selected criteria.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            @if(isset($tableApplicants) && is_object($tableApplicants) && method_exists($tableApplicants, 'appends'))
                                {{ $tableApplicants->appends(request()->query())->links() }}
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
                                    <th class="px-4 py-3 border border-gray-200 text-center">4Ps</th>
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
                                        {{ $app->head_4ps ?? 'N/A' }}
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

        <!-- Family Intake Sheet Modal -->
        <div id="intakeSheetModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="text-xl font-bold text-gray-800">Family Intake Sheet</h2>
                    <button type="button" class="modal-close" onclick="closeIntakeSheetModal()">
                        <i class="fas fa-times"></i>
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
                                <p><strong>Remarks:</strong> <span id="modal-remarks">-</span></p>
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
                                <tbody id="modal-family-members">
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
                                <tbody id="modal-service-records">
                                    <!-- Service records will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Health & Signatures Section -->
                    <div class="intake-section">
                        <h3 class="intake-section-title">Health & Signatures</h3>

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
                                    <p class="text-xs text-gray-500">No signature</p>
                                </div>
                                <p><strong id="modal-worker-fullname">-</strong></p>
                                <p class="mt-1 text-sm text-gray-600">Worker Signature</p>
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
                                    <p class="text-xs text-gray-500">No signature</p>
                                </div>
                                <p><strong id="modal-officer-fullname">-</strong></p>
                                <p class="mt-1 text-sm text-gray-600">Officer Signature</p>
                            </div>
                        </div>

                        <!-- Centered Family Head Signature + Date -->
                        <div class="mt-6 text-center">
                            <p><strong>Family Head Signature:</strong></p>
                            <div id="modal-client-signature-large" class="mt-2">
                                <p class="text-xs text-gray-500">No signature</p>
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

                <div class="modal-actions">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600" onclick="closeIntakeSheetModal()">
                        Close
                    </button>
                    <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600" onclick="printIntakeSheet()">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <script>
            let currentApplicationId = null;
            let currentApplicationDocuments = null;

            function showTable() {
                const tableViewEl = document.getElementById("tableView");
                const listViewEl = document.getElementById("listView");
                if (tableViewEl) tableViewEl.classList.remove("hidden");
                if (listViewEl) listViewEl.classList.add("hidden");
                const activeTab = document.querySelector('.tab.active');
                if (activeTab) activeTab.classList.remove('active');
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
                if (activeTab) activeTab.classList.remove('active');
                const tabs = document.querySelectorAll('.tab');
                if (tabs && tabs[1]) tabs[1].classList.add('active');
                localStorage.setItem("viewMode", "list");
                if (typeof filterList === 'function') filterList();
            }

            // View Intake Sheet Modal Functions
            function openIntakeSheetModal(applicationId) {
                if (!applicationId) return;
                fetch(`/mayor_staff/intake-sheet/${applicationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.success) {
                            populateIntakeSheetModal(data.intakeSheet || data.intake_sheet || data);
                            document.getElementById('intakeSheetModal').style.display = 'block';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: (data && data.message) ? data.message : 'Failed to load intake sheet data.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while loading intake sheet data.'
                        });
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
                setText('modal-house-house', d.house_house || '-');
                setText('modal-house-electric', d.house_electric || '-');
                setText('modal-remarks', d.remarks || '-');
                setText('modal-house-total-income', d.house_total_income ?? '-');
                setText('modal-house-lot', d.house_lot || '-');
                setText('modal-house-net-income', d.house_net_income ?? '-');
                setText('modal-house-water', d.house_water || '-');

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
                            const income = escapeHtml(m.income ?? '');
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
                                <td>${income}</td>
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
                const nameSearchEl = document.getElementById('nameSearch');
                const barangayFilterEl = document.getElementById('barangayFilter');
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
            }

            function filterList() {
                const nameSearchEl = document.getElementById('listNameSearch');
                const barangayFilterEl = document.getElementById('listBarangayFilter');
                const nameSearchValue = nameSearchEl ? nameSearchEl.value.toLowerCase().trim() : '';
                const barangayFilterValue = barangayFilterEl ? barangayFilterEl.value.toLowerCase().trim() : '';

                const listViewRows = document.querySelectorAll('#listView tbody tr');
                listViewRows.forEach(row => {
                    if (!row.cells || row.cells.length < 3) return;
                    const nameCell = (row.cells[1].textContent || '').toLowerCase();
                    const barangayCell = (row.cells[2].textContent || '').toLowerCase();

                    const matchesName = nameCell.includes(nameSearchValue);
                    const matchesBarangay = barangayFilterValue === '' || barangayCell.includes(barangayFilterValue);

                    row.style.display = (matchesName && matchesBarangay) ? '' : 'none';
                });
            }

            // Safe event wiring on DOM ready
            document.addEventListener("DOMContentLoaded", function() {
                let viewMode = localStorage.getItem("viewMode") || "table";
                if(viewMode === "list") {
                    showList();
                } else {
                    showTable();
                }

                const nameSearch = document.getElementById('nameSearch');
                const barangayFilter = document.getElementById('barangayFilter');
                const listNameSearch = document.getElementById('listNameSearch');
                const listBarangayFilter = document.getElementById('listBarangayFilter');

                if (nameSearch) nameSearch.addEventListener('input', filterTable);
                if (barangayFilter) barangayFilter.addEventListener('change', filterTable);
                if (listNameSearch) listNameSearch.addEventListener('input', filterList);
                if (listBarangayFilter) listBarangayFilter.addEventListener('change', filterList);

                // Add event listeners for view intake sheet buttons
                document.querySelectorAll('.view-intake-btn').forEach(button => {
                    button.removeEventListener?.('click', null);
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        openIntakeSheetModal(id);
                    });
                });

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

                // Notification bell (guarded)
                const notifBell = document.getElementById("notifBell");
                if (notifBell) {
                    notifBell.addEventListener("click", function () {
                        let dropdown = document.getElementById("notifDropdown");
                        if (dropdown) dropdown.classList.toggle("hidden");
                        let notifCount = document.getElementById("notifCount");
                        if (notifCount) {
                            notifCount.remove();
                            // Mark notifications as viewed on the server (use meta csrf if present)
                            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                            const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;
                            fetch('/mayor_staff/mark-notifications-viewed', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    ...(csrf ? {'X-CSRF-TOKEN': csrf} : {})
                                }
                            }).then(response => response.json())
                            .then(data => {
                                if (data.success) console.log('Notifications marked as viewed');
                            }).catch(error => {
                                console.error('Error marking notifications as viewed:', error);
                            });
                        }
                    });
                }

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

        <script src="{{ asset('js/logout.js') }}"></script>
    </body>
</html>