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
        /* Fixed header + sidebar + content layout */
        body { height: 100vh; overflow: hidden; }
        header { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; }
        .sidebar-fixed { position: fixed; top: 80px; left: 0; bottom: 0; width: 64px; overflow-y: auto; z-index: 999; background: white; }
        @media (min-width: 768px) { .sidebar-fixed { width: 256px; } }
        .main-content-fixed { position: fixed; top: 80px; left: 64px; right: 0; bottom: 0; overflow-y: auto; padding: 1rem 1.25rem; }
        @media (min-width: 768px) { .main-content-fixed { left: 256px; } }
        
        /* Modal styles */
 /* Simplified Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content {
    background-color: #ffffff;
    margin: 2% auto;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    animation: slideIn 0.3s ease-in-out;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem;
    background: #7c3aed;
    color: white;
    flex-shrink: 0;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.1);
}

.modal-body {
    padding: 0;
    flex: 1;
    overflow-y: auto;
    background: #fafafa;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.25rem;
    border-top: 1px solid #e5e7eb;
    background: white;
    flex-shrink: 0;
}

/* Simplified Section Styles */
.intake-section {
    margin: 1.25rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 1.25rem;
    background: white;
}

.intake-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #374151;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

/* Clean Grid Layout */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    font-weight: 600;
    color: #4b5563;
    font-size: 0.875rem;
}

.info-item span {
    color: #1f2937;
    font-size: 0.95rem;
    line-height: 1.4;
}

/* Clean Table Styles */
.intake-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
    background: white;
}

.intake-table th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    padding: 0.75rem;
    text-align: left;
    border: 1px solid #e5e7eb;
}

.intake-table td {
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    color: #4b5563;
}

.intake-table tr:nth-child(even) {
    background: #fafafa;
}

/* Clean Signature Section */
.signature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.signature-item {
    text-align: center;
}

.signature-container {
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 1rem;
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f9fafb;
    margin-bottom: 0.5rem;
}

.signature-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.signature-role {
    color: #6b7280;
    font-size: 0.75rem;
}

/* Clean Button Styles */
.btn {
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.btn-close {
    background: #6b7280;
    color: white;
}

.btn-close:hover {
    background: #4b5563;
}

.btn-approve {
    background: #10b981;
    color: white;
}

.btn-approve:hover {
    background: #059669;
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn-reject:hover {
    background: #dc2626;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        width: 95% !important;
        margin: 5% auto;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .signature-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .modal-footer .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .intake-section {
        margin: 1rem;
        padding: 1rem;
    }
    
    .intake-table {
        font-size: 0.75rem;
    }
}
/* Improved Button Styles */
.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-close {
    background: #6b7280;
    color: white;
}

.btn-close:hover:not(:disabled) {
    background: #4b5563;
    transform: translateY(-1px);
}

.btn-approve {
    background: #10b981;
    color: white;
}

.btn-approve:hover:not(:disabled) {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn-reject:hover:not(:disabled) {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Status Badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-poor {
    background: #fef3c7;
    color: #92400e;
}

.status-ultra-poor {
    background: #fee2e2;
    color: #991b1b;
}

.status-approved {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

/* Scrollbar Styling */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #7c3aed;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        width: 95% !important;
        margin: 5% auto;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .signature-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .modal-footer .btn {
        width: 100%;
        justify-content: center;
    }
    
    .intake-section {
        margin: 1rem;
        padding: 1rem;
    }
    
    .intake-table {
        font-size: 0.75rem;
    }
    
    .intake-table th,
    .intake-table td {
        padding: 0.5rem;
    }
}

@media (max-width: 480px) {
    .modal-header {
        padding: 1rem;
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .modal-header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .modal-title {
        font-size: 1.25rem;
    }
}
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

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
            animation: fadeIn 1s ease forwards;
        }

        .spinner {
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        .spinner img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .fade-out {
            animation: fadeOut 1s ease forwards;
        }

        @keyframes fadeOut {
            to { opacity: 0; visibility: hidden; }
        }

        /* Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .pagination-info {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        .pagination-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 0.5rem 1rem;
            background-color: #7c3aed;
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .pagination-btn:hover:not(:disabled) {
            background-color: #6d28d9;
            transform: translateY(-1px);
        }

        .pagination-btn:disabled {
            background-color: #d1d5db;
            cursor: not-allowed;
            transform: none;
        }

        .pagination-page-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #374151;
        }

        .pagination-page-input {
            width: 3.5rem;
            padding: 0.4rem;
            text-align: center;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            background-color: white;
        }

        /* Tab Styles */
        .tab {
            padding: 0.5rem 1rem;
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .tab:hover {
            background-color: #e5e7eb;
        }

        .tab.active {
            color: white;
            border-color: transparent;
        }

        #tab-pending.active {
            background-color: #7c3aed;
            border-color: #7c3aed;
        }

        #tab-pending.active:hover {
            background-color: #6d28d9;
        }

        #tab-approved-rejected.active {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
        }

        #tab-approved-rejected.active:hover {
            background-color: #059669 !important;
        }

        /* Enhanced Filter Styles */
        .filter-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #4b5563;
        }

        .filter-input, .filter-select {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            min-width: 200px;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .spinner { width: 80px; height: 80px; }
            .pagination-container { flex-direction: column; gap: 0.75rem; }
            .filter-container { flex-direction: column; align-items: stretch; }
            .filter-input, .filter-select { min-width: 100%; }
            .modal-content { width: 95% !important; margin: 5% auto; }
        }

        @media (max-width: 480px) {
            .spinner { width: 60px; height: 60px; }
            .pagination-buttons { gap: 0.25rem; }
            .intake-table { font-size: 0.75rem; }
            .intake-table th, .intake-table td { padding: 0.5rem; }
        }

        .grid-cols-auto { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner">
            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
        </div>
    </div>

    @php
        $applications = $applications ?? ($tableApplicants ?? []);
        $listApplications = $listApplications ?? [];
        $notifications = $notifications ?? collect();
        $showBadge = $showBadge ?? false;

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
                    <!-- Notification Bell -->
                    <button id="notifBell" class="relative focus:outline-none">
                        <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                        @if($showBadge && $notifications->count() > 0)
                            <span id="notifCount"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $notifications->count() }}
                            </span>
                        @endif
                    </button>
                    <!-- Notification Dropdown -->
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
                <nav class="flex-1 p-2 md:p-4 space-y-1">
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
                                    <i class="bx bxs-graduation text-center mx-auto md:mx-0 text-xl text-white-700"></i>
                                    <span class="ml-4 hidden md:block text-lg">Applicants</span>
                                </div>
                                <i class="bx bx-chevron-down ml-2"></i>
                            </button>
                            <!-- Dropdown Menu -->
                            <ul id="scholarMenu" class="ml-10 mt-2 space-y-2 hidden">
                                <li>
                                    <a href="/mayor_staff/application" class="flex items-center p-2 rounded-lg text-black hover:bg-violet-600">
                                    <i class="bx bx-search-alt mr-2 text-white-700"></i> Review Applications
                                    </a>
                                </li>
                                <li>
                                    <a href="/mayor_staff/status" class="flex items-center p-2 rounded-lg text-white bg-violet-600 hover:text-white">
                                    <i class="bx bx-check-circle mr-2 text-white-700"></i> Scholarship Approval
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
            
            <!-- Main content area -->
            <div class="main-content-fixed text-[16px]">
                <div class="p-6 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-3xl font-bold text-gray-800">Applicant Status Management</h5>
                    </div>

                    <!-- Tabs -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex gap-2">
                            <button type="button" onclick="showTable()" class="tab active" id="tab-pending">
                                <i class="fas fa-table mr-1"></i> Pending Status
                            </button>
                            <button type="button" onclick="showList()" class="tab" id="tab-approved-rejected">
                                <i class="fas fa-list mr-1"></i> Approved/Rejected
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
                                    @foreach($barangays as $barangay)
                                        <option value="{{ $barangay }}">{{ $barangay }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Table -->
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
                                                <i class="fas fa-eye mr-1"></i> View
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

                        <!-- Pagination -->
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
                                    @foreach($barangays as $barangay)
                                        <option value="{{ $barangay }}">{{ $barangay }}</option>
                                    @endforeach
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
                        
                        <!-- Table -->
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-green-800 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Actions</th>
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
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <button
                                                title="View Intake Sheet"
                                                class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow view-intake-btn"
                                                data-id="{{ $app->application_personnel_id }}"
                                                data-name="{{ $app->fname }} {{ $app->mname }} {{ $app->lname }} {{ $app->suffix }}">
                                                <i class="fas fa-eye mr-1"></i> View
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
                        
                        <!-- Pagination -->
                        <div class="pagination-container" id="listPagination"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Intake Sheet Modal -->
        <div id="intakeSheetModal" class="modal">
            <div class="modal-content" style="max-width: 95%; width: 95%; margin: 2% auto; border-radius: 20px; box-shadow: 0 32px 64px -12px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px);">
                <div class="modal-header flex items-center justify-between bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 p-8 rounded-t-2xl shadow-2xl relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                    <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-white/5 rounded-full blur-lg"></div>
                    <div class="flex items-center space-x-5 relative z-10">
                        <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-md shadow-lg border border-white/20">
                            <img src="{{ asset('images/lydo.png') }}" alt="LYDO Logo" class="w-10 h-10 object-contain">
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-white mb-2 tracking-tight">Family Intake Sheet</h2>
                            <p class="text-violet-100 text-base font-medium">Comprehensive Application Details & Documentation</p>
                        </div>
                    </div>
                    <button type="button" class="modal-close text-white hover:text-gray-200 hover:bg-white/20 p-3 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg backdrop-blur-sm border border-white/10" onclick="closeIntakeSheetModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="modal-body p-6 max-h-[80vh] overflow-y-auto">
                    <!-- Applicant Basic Information -->
                    <div class="intake-section mb-6">
                        <h3 class="intake-section-title">Applicant Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="info-item">
                                <label>Full Name:</label>
                                <span id="modal-applicant-name">-</span>
                            </div>
                            <div class="info-item">
                                <label>Gender:</label>
                                <span id="modal-applicant-gender">-</span>
                            </div>
                            <div class="info-item">
                                <label>Remarks:</label>
                                <span id="modal-remarks">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Head of Family Section -->
                    <div class="intake-section mb-6">
                        <h3 class="intake-section-title">Head of Family Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="info-item">
                                <label>Date of Birth:</label>
                                <span id="modal-head-dob">-</span>
                            </div>
                            <div class="info-item">
                                <label>Place of Birth:</label>
                                <span id="modal-head-pob">-</span>
                            </div>
                            <div class="info-item">
                                <label>Address:</label>
                                <span id="modal-head-address">-</span>
                            </div>
                            <div class="info-item">
                                <label>Zone:</label>
                                <span id="modal-head-zone">-</span>
                            </div>
                            <div class="info-item">
                                <label>Barangay:</label>
                                <span id="modal-head-barangay">-</span>
                            </div>
                            <div class="info-item">
                                <label>Religion:</label>
                                <span id="modal-head-religion">-</span>
                            </div>
                            <div class="info-item">
                                <label>Serial No.:</label>
                                <span id="modal-serial-number">-</span>
                            </div>
                            <div class="info-item">
                                <label>4Ps:</label>
                                <span id="modal-head-4ps">-</span>
                            </div>
                            <div class="info-item">
                                <label>IP No.:</label>
                                <span id="modal-head-ipno">-</span>
                            </div>
                            <div class="info-item">
                                <label>Education:</label>
                                <span id="modal-head-educ">-</span>
                            </div>
                            <div class="info-item">
                                <label>Occupation:</label>
                                <span id="modal-head-occ">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Household Information Section -->
                    <div class="intake-section mb-6">
                        <h3 class="intake-section-title">Household Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="info-item">
                                <label>Total Family Income:</label>
                                <span id="modal-house-total-income">-</span>
                            </div>
                            <div class="info-item">
                                <label>Total Family Net Income:</label>
                                <span id="modal-house-net-income">-</span>
                            </div>
                            <div class="info-item">
                                <label>Other Source of Income:</label>
                                <span id="modal-other-income">-</span>
                            </div>
                            <div class="info-item">
                                <label>House (Owned/Rented):</label>
                                <span id="modal-house-house">-</span>
                            </div>
                            <div class="info-item">
                                <label>Lot (Owned/Rented):</label>
                                <span id="modal-house-lot">-</span>
                            </div>
                            <div class="info-item">
                                <label>Electricity Source:</label>
                                <span id="modal-house-electric">-</span>
                            </div>
                            <div class="info-item">
                                <label>Water Source:</label>
                                <span id="modal-house-water">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Family Members Section -->
                    <div class="intake-section mb-6">
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
                                        <th>Education</th>
                                        <th>Occupation</th>
                                        <th>Income</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-family-members">
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-gray-500">Loading family members...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Service Records Section -->
                    <div class="intake-section mb-6">
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
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-gray-500">Loading service records...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Application Documents Section -->
                    <div class="intake-section mb-6">
                        <h3 class="intake-section-title">Application Documents</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div class="info-item">
                                    <label>Application Letter:</label>
                                    <span id="modal-doc-application-letter">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Certificate of Registration:</label>
                                    <span id="modal-doc-cert-reg">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Grade Slip:</label>
                                    <span id="modal-doc-grade-slip">-</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="info-item">
                                    <label>Barangay Indigency:</label>
                                    <span id="modal-doc-brgy-indigency">-</span>
                                </div>
                                <div class="info-item">
                                    <label>Student ID:</label>
                                    <span id="modal-doc-student-id">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signatures Section -->
                    <div class="intake-section mb-6">
                        <h3 class="intake-section-title">Signatures</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="signature-container mb-2">
                                    <div id="modal-client-signature">
                                        <span class="text-gray-500">Client Signature</span>
                                    </div>
                                </div>
                                <p class="font-semibold">Applicant</p>
                            </div>
                            <div class="text-center">
                                <div class="signature-container mb-2">
                                    <div id="modal-worker-signature">
                                        <span class="text-gray-500">Worker Signature</span>
                                    </div>
                                </div>
                                <p class="font-semibold" id="modal-worker-name">-</p>
                                <p class="text-sm text-gray-600">Lydo Staff</p>
                            </div>
                            <div class="text-center">
                                <div class="signature-container mb-2">
                                    <div id="modal-officer-signature">
                                        <span class="text-gray-500">Officer Signature</span>
                                    </div>
                                </div>
                                <p class="font-semibold" id="modal-officer-name">-</p>
                                <p class="text-sm text-gray-600">Officer</p>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <p><strong>Date Entry:</strong> <span id="modal-date-entry" class="text-gray-900">-</span></p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="modal-footer">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition" onclick="closeIntakeSheetModal()">
                        <i class="fas fa-times mr-2"></i> Close
                    </button>
                    <button type="button" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition" id="approveBtn">
                        <i class="fas fa-check mr-2"></i> Approve
                    </button>
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition" id="rejectBtn">
                        <i class="fas fa-times mr-2"></i> Reject
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Global variables
        let currentApplicationPersonnelId = null;
        let currentApplicationName = null;
        let currentView = 'table';

        // Pagination state
        const paginationState = {
            table: {
                currentPage: 1,
                rowsPerPage: 15,
                allRows: [],
                filteredRows: []
            },
            list: {
                currentPage: 1,
                rowsPerPage: 15,
                allRows: [],
                filteredRows: []
            }
        };

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            initializeData();
            initializePagination();
            initializeFiltering();
            initializeModalEvents();
            initializeNotificationDropdown();
            initializeSidebarDropdown();
            
            // Hide loading overlay after page load
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);
            }, 1000);
        });

        // Initialize modal events
        function initializeModalEvents() {
            // View intake sheet buttons
            document.querySelectorAll('.view-intake-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    openIntakeSheetModal(id, name);
                });
            });

            // Approve button
            const approveBtn = document.getElementById('approveBtn');
            if (approveBtn) {
                approveBtn.addEventListener('click', function() {
                    if (currentApplicationPersonnelId && currentApplicationName) {
                        approveApplication(currentApplicationPersonnelId, currentApplicationName);
                    }
                });
            }

// Reject button
const rejectBtn = document.getElementById('rejectBtn');
if (rejectBtn) {
    rejectBtn.addEventListener('click', function() {
        if (currentApplicationPersonnelId && currentApplicationName) {
            showRejectionReasonModal();
        }
    });
}

function showRejectionReasonModal() {
    Swal.fire({
        title: 'Reason for Rejection',
        html: `
            <textarea 
                id="rejectionReasonText" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                rows="4" 
                placeholder="Please provide detailed reason for rejection..."
                style="min-height: 120px; resize: vertical;"
            ></textarea>
            <div class="mt-3 text-sm text-gray-600">
                This reason will be sent to the applicant via email.
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Submit Rejection',
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        preConfirm: () => {
            const reason = document.getElementById('rejectionReasonText').value.trim();
            if (!reason) {
                Swal.showValidationMessage('Please provide a reason for rejection');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            updateApplicationStatus('Rejected', result.value);
        }
    });
}
        // Initialize data from the tables
        function initializeData() {
            // Get ALL table rows (not just visible ones)
            const tableRows = Array.from(document.querySelectorAll('#tableView tbody tr'));
            paginationState.table.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
            paginationState.table.filteredRows = [...paginationState.table.allRows];
            
            // Get ALL list rows
            const listRows = Array.from(document.querySelectorAll('#listView tbody tr'));
            paginationState.list.allRows = listRows.filter(row => !row.querySelector('td[colspan]'));
            paginationState.list.filteredRows = [...paginationState.list.allRows];
        }

        // Initialize pagination
        function initializePagination() {
            updatePagination('table');
            updatePagination('list');
        }

        // Update pagination display
        function updatePagination(viewType) {
            const state = paginationState[viewType];
            const containerId = viewType === 'table' ? 'tablePagination' : 'listPagination';
            const container = document.getElementById(containerId);
            
            if (!container) return;
            
            // Hide all rows first
            state.allRows.forEach(row => {
                row.style.display = 'none';
            });
            
            // Calculate pagination for filtered rows
            const startIndex = (state.currentPage - 1) * state.rowsPerPage;
            const endIndex = startIndex + state.rowsPerPage;
            const pageRows = state.filteredRows.slice(startIndex, endIndex);
            
            // Show only rows for current page
            pageRows.forEach(row => {
                row.style.display = '';
            });
            
            // Update pagination controls
            const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
            const startItem = state.filteredRows.length === 0 ? 0 : Math.min((state.currentPage - 1) * state.rowsPerPage + 1, state.filteredRows.length);
            const endItem = Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length);
            
            container.innerHTML = `
                <div class="pagination-info">
                    Showing ${startItem} to ${endItem} of ${state.filteredRows.length} entries
                </div>
                <div class="pagination-buttons">
                    <button class="pagination-btn" onclick="changePage('${viewType}', 1)" ${state.currentPage === 1 ? 'disabled' : ''}>
                        <i class="fas fa-angle-double-left"></i>
                    </button>
                    <button class="pagination-btn" onclick="changePage('${viewType}', ${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''}>
                        <i class="fas fa-angle-left"></i>
                    </button>
                    <div class="pagination-page-info">
                        Page <input type="number" class="pagination-page-input" value="${state.currentPage}" min="1" max="${totalPages}" onchange="goToPage('${viewType}', this.value)"> of ${totalPages}
                    </div>
                    <button class="pagination-btn" onclick="changePage('${viewType}', ${state.currentPage + 1})" ${state.currentPage === totalPages ? 'disabled' : ''}>
                        <i class="fas fa-angle-right"></i>
                    </button>
                    <button class="pagination-btn" onclick="changePage('${viewType}', ${totalPages})" ${state.currentPage === totalPages ? 'disabled' : ''}>
                        <i class="fas fa-angle-double-right"></i>
                    </button>
                </div>
            `;
        }

        // Change page
        function changePage(viewType, page) {
            const state = paginationState[viewType];
            const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
            
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            state.currentPage = page;
            updatePagination(viewType);
        }

        // Go to specific page
        function goToPage(viewType, page) {
            const state = paginationState[viewType];
            const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
            
            page = parseInt(page);
            if (isNaN(page) || page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            state.currentPage = page;
            updatePagination(viewType);
        }

        // Initialize filtering functionality
        function initializeFiltering() {
            const tableNameSearch = document.getElementById('searchInputTable');
            const tableBarangayFilter = document.getElementById('barangaySelectTable');
            
            const listNameSearch = document.getElementById('listNameSearch');
            const listBarangayFilter = document.getElementById('listBarangayFilter');
            const listStatusFilter = document.getElementById('listStatusFilter');

            // Table View Filtering
            function filterTableView() {
                const searchTerm = tableNameSearch.value.toLowerCase();
                const selectedBarangay = tableBarangayFilter.value;

                const filteredRows = paginationState.table.allRows.filter(row => {
                    const nameCell = row.cells[1];
                    const barangayCell = row.cells[2];

                    if (!nameCell || !barangayCell) return false;

                    const name = nameCell.textContent.toLowerCase();
                    const barangay = barangayCell.textContent.trim();

                    const nameMatch = name.includes(searchTerm);
                    const barangayMatch = !selectedBarangay || barangay === selectedBarangay;

                    return nameMatch && barangayMatch;
                });

                // Update filtered rows and reset to page 1
                paginationState.table.filteredRows = filteredRows;
                paginationState.table.currentPage = 1;
                updatePagination('table');
            }

            // List View Filtering
            function filterListView() {
                const searchTerm = listNameSearch.value.toLowerCase();
                const selectedBarangay = listBarangayFilter.value;
                const selectedStatus = listStatusFilter.value;

                const filteredRows = paginationState.list.allRows.filter(row => {
                    const nameCell = row.cells[1];
                    const barangayCell = row.cells[2];
                    const statusCell = row.cells[4];

                    if (!nameCell || !barangayCell || !statusCell) return false;

                    const name = nameCell.textContent.toLowerCase();
                    const barangay = barangayCell.textContent.trim();
                    const status = statusCell.textContent.trim();

                    const nameMatch = name.includes(searchTerm);
                    const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
                    const statusMatch = !selectedStatus || status.toLowerCase() === selectedStatus.toLowerCase();

                    return nameMatch && barangayMatch && statusMatch;
                });

                // Update filtered rows and reset to page 1
                paginationState.list.filteredRows = filteredRows;
                paginationState.list.currentPage = 1;
                updatePagination('list');
            }

            // Add event listeners with debouncing
            if (tableNameSearch) {
                tableNameSearch.addEventListener('input', debounce(filterTableView, 300));
            }
            if (tableBarangayFilter) {
                tableBarangayFilter.addEventListener('change', filterTableView);
            }
            
            if (listNameSearch) {
                listNameSearch.addEventListener('input', debounce(filterListView, 300));
            }
            if (listBarangayFilter) {
                listBarangayFilter.addEventListener('change', filterListView);
            }
            if (listStatusFilter) {
                listStatusFilter.addEventListener('change', filterListView);
            }
        }

        // Debounce function for search
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

        // Tab switching functions
        function showTable() {
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('loadingOverlay').classList.remove('fade-out');

            setTimeout(() => {
                document.getElementById('tableView').classList.remove('hidden');
                document.getElementById('listView').classList.add('hidden');
                document.getElementById('tab-pending').classList.add('active');
                document.getElementById('tab-approved-rejected').classList.remove('active');
                currentView = 'table';
                
                // Reset to first page
                paginationState.table.currentPage = 1;
                updatePagination('table');
                
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);
            }, 300);
        }

        function showList() {
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('loadingOverlay').classList.remove('fade-out');

            setTimeout(() => {
                document.getElementById('tableView').classList.add('hidden');
                document.getElementById('listView').classList.remove('hidden');
                document.getElementById('tab-pending').classList.remove('active');
                document.getElementById('tab-approved-rejected').classList.add('active');
                currentView = 'list';
                
                // Reset to first page
                paginationState.list.currentPage = 1;
                updatePagination('list');
                
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);
            }, 300);
        }

        // Modal functions
        function openIntakeSheetModal(applicationPersonnelId, applicantName) {
            currentApplicationPersonnelId = applicationPersonnelId;
            currentApplicationName = applicantName;
            
            const modal = document.getElementById('intakeSheetModal');
            modal.style.display = 'block';
            
            // Show loading state
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('loadingOverlay').classList.remove('fade-out');
            
            fetchIntakeSheetData(applicationPersonnelId);
        }

        function closeIntakeSheetModal() {
            const modal = document.getElementById('intakeSheetModal');
            modal.style.display = 'none';
            currentApplicationPersonnelId = null;
            currentApplicationName = null;
        }


        function fetchIntakeSheetData(applicationPersonnelId) {
    console.log('Fetching intake sheet for ID:', applicationPersonnelId);

    // Show loading
    document.getElementById('loadingOverlay').style.display = 'flex';
    document.getElementById('loadingOverlay').classList.remove('fade-out');

    fetch(`/mayor_staff/intake-sheet/${applicationPersonnelId}`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.success) {
                populateIntakeSheetModal(data.intakeSheet);
            } else {
                console.error('Error from server:', data.message);
                console.error('Debug info:', data.debug);
                Swal.fire('Error', data.message || 'Failed to load intake sheet data.', 'error');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.getElementById('loadingOverlay').classList.add('fade-out');
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
            }, 1000);
            Swal.fire('Error', 'Failed to load intake sheet data.', 'error');
        });
        }



        function approveApplication(id, name) {
            Swal.fire({
                title: 'Approve Application?',
                text: `Are you sure you want to approve ${name}'s application?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateApplicationStatus('Approved');
                }
            });
        }

        function updateApplicationStatus(status, reason = '') {
            if (!currentApplicationPersonnelId) return;
            
            const formData = new FormData();
            formData.append('status', status);
            formData.append('reason', reason);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Show loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('loadingOverlay').classList.remove('fade-out');
            
            fetch(`/mayor_staff/update-status/${currentApplicationPersonnelId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);
                
                if (data.success) {
                    Swal.fire('Success!', `Application ${status.toLowerCase()} successfully!`, 'success')
                        .then(() => {
                            closeIntakeSheetModal();
                            location.reload();
                        });
                } else {
                    Swal.fire('Error!', data.message || `Failed to ${status.toLowerCase()} application.`, 'error');
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);
                Swal.fire('Error!', `Failed to ${status.toLowerCase()} application.`, 'error');
            });
        }

        // Utility functions
        function setElementText(elementId, text) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = text || '-';
            }
        }

        function formatDate(dateString) {
            if (!dateString) return null;
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } catch (e) {
                return dateString;
            }
        }

        function formatCurrency(amount) {
            if (!amount) return null;
            if (typeof amount === 'string' && amount.includes('₱')) return amount;
            const num = parseFloat(amount);
            if (isNaN(num)) return amount;
            return `₱${num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        function setDocumentLink(elementId, url) {
            const element = document.getElementById(elementId);
            if (element && url) {
                element.innerHTML = `<a href="${url}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">View Document</a>`;
            } else {
                element.innerHTML = '<span class="text-gray-500">Not provided</span>';
            }
        }

        function setSignatureImage(elementId, imageUrl) {
            const element = document.getElementById(elementId);
            if (element && imageUrl) {
                element.innerHTML = `<img src="${imageUrl}" alt="Signature" class="max-w-full max-h-20 object-contain mx-auto">`;
            } else {
                element.innerHTML = '<span class="text-gray-500">No signature</span>';
            }
        }

        function escapeHtml(text) {
            if (!text) return '-';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Populate intake sheet modal with data
        function populateIntakeSheetModal(intakeSheet) {
            console.log('Populating modal with intake sheet data:', intakeSheet);

            // Applicant Basic Information
            setElementText('modal-applicant-name', intakeSheet.applicant_name);
            setElementText('modal-applicant-gender', intakeSheet.applicant_gender);
            setElementText('modal-remarks', intakeSheet.remarks);

            // Head of Family Information
            setElementText('modal-head-dob', formatDate(intakeSheet.head_dob));
            setElementText('modal-head-pob', intakeSheet.head_pob);
            setElementText('modal-head-address', intakeSheet.head_address);
            setElementText('modal-head-zone', intakeSheet.head_zone);
            setElementText('modal-head-barangay', intakeSheet.head_barangay);
            setElementText('modal-head-religion', intakeSheet.head_religion);
            setElementText('modal-serial-number', intakeSheet.serial_number);
            setElementText('modal-head-4ps', intakeSheet.head_4ps);
            setElementText('modal-head-ipno', intakeSheet.head_ipno);
            setElementText('modal-head-educ', intakeSheet.head_educ);
            setElementText('modal-head-occ', intakeSheet.head_occ);

            // Household Information
            setElementText('modal-house-total-income', formatCurrency(intakeSheet.house_total_income));
            setElementText('modal-house-net-income', formatCurrency(intakeSheet.house_net_income));
            setElementText('modal-other-income', intakeSheet.other_income);
            setElementText('modal-house-house', intakeSheet.house_house);
            setElementText('modal-house-lot', intakeSheet.house_lot);
            setElementText('modal-house-electric', intakeSheet.house_electric);
            setElementText('modal-house-water', intakeSheet.house_water);

            // Family Members Table
            const familyMembersTbody = document.getElementById('modal-family-members');
            if (familyMembersTbody && intakeSheet.family_members) {
                familyMembersTbody.innerHTML = '';
                if (Array.isArray(intakeSheet.family_members) && intakeSheet.family_members.length > 0) {
                    intakeSheet.family_members.forEach(member => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${escapeHtml(member.name || '-')}</td>
                            <td>${escapeHtml(member.relation || '-')}</td>
                            <td>${formatDate(member.birthdate) || '-'}</td>
                            <td>${member.age || '-'}</td>
                            <td>${escapeHtml(member.sex || '-')}</td>
                            <td>${escapeHtml(member.civil_status || '-')}</td>
                            <td>${escapeHtml(member.education || '-')}</td>
                            <td>${escapeHtml(member.occupation || '-')}</td>
                            <td>${formatCurrency(member.income) || '-'}</td>
                            <td>${escapeHtml(member.remarks || '-')}</td>
                        `;
                        familyMembersTbody.appendChild(row);
                    });
                } else {
                    familyMembersTbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-gray-500">No family members data available</td></tr>';
                }
            }

            // Service Records Table
            const serviceRecordsTbody = document.getElementById('modal-service-records');
            if (serviceRecordsTbody && intakeSheet.service_records) {
                serviceRecordsTbody.innerHTML = '';
                if (Array.isArray(intakeSheet.service_records) && intakeSheet.service_records.length > 0) {
                    intakeSheet.service_records.forEach(record => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${formatDate(record.date) || '-'}</td>
                            <td>${escapeHtml(record.problem_need || '-')}</td>
                            <td>${escapeHtml(record.action_assistance || '-')}</td>
                            <td>${escapeHtml(record.remarks || '-')}</td>
                        `;
                        serviceRecordsTbody.appendChild(row);
                    });
                } else {
                    serviceRecordsTbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500">No service records available</td></tr>';
                }
            }

            // Application Documents
            setDocumentLink('modal-doc-application-letter', intakeSheet.doc_application_letter);
            setDocumentLink('modal-doc-cert-reg', intakeSheet.doc_cert_reg);
            setDocumentLink('modal-doc-grade-slip', intakeSheet.doc_grade_slip);
            setDocumentLink('modal-doc-brgy-indigency', intakeSheet.doc_brgy_indigency);
            setDocumentLink('modal-doc-student-id', intakeSheet.doc_student_id);

            // Signatures
            setSignatureImage('modal-client-signature', intakeSheet.client_signature);
            setSignatureImage('modal-worker-signature', intakeSheet.worker_signature);
            setSignatureImage('modal-officer-signature', intakeSheet.officer_signature);
            setElementText('modal-worker-name', intakeSheet.worker_name);
            setElementText('modal-officer-name', intakeSheet.officer_name);
            setElementText('modal-date-entry', formatDate(intakeSheet.date_entry));

            // Hide loading overlay
            document.getElementById('loadingOverlay').classList.add('fade-out');
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
            }, 1000);
        }

        // Notification dropdown
        function initializeNotificationDropdown() {
            const notifBell = document.getElementById('notifBell');
            const notifDropdown = document.getElementById('notifDropdown');

            if (notifBell && notifDropdown) {
                notifBell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('hidden');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    notifDropdown.classList.add('hidden');
                });

                // Prevent dropdown from closing when clicking inside
                notifDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        }

        // Sidebar dropdown
        function toggleDropdown(id) {
            const el = document.getElementById(id);
            if (!el) return;

            const btn = document.querySelector(`button[onclick="toggleDropdown('${id}')"]`);
            const chevron = btn ? btn.querySelector('i.bx-chevron-down') : null;

            const willOpen = el.classList.contains('hidden');
            el.classList.toggle('hidden');

            if (chevron) {
                chevron.classList.toggle('rotate-180', willOpen);
            }

            try {
                localStorage.setItem(`sidebarDropdown_${id}`, willOpen ? 'open' : 'closed');
            } catch (e) {
                console.warn('Could not persist dropdown state:', e);
            }
        }

        function initializeSidebarDropdown() {
            try {
                Object.keys(localStorage).forEach(key => {
                    if (!key.startsWith('sidebarDropdown_')) return;
                    const id = key.replace('sidebarDropdown_', '');
                    const state = localStorage.getItem(key);
                    const el = document.getElementById(id);
                    if (!el) return;

                    const btn = document.querySelector(`button[onclick="toggleDropdown('${id}')"]`);
                    const chevron = btn ? btn.querySelector('i.bx-chevron-down') : null;

                    if (state === 'open') {
                        el.classList.remove('hidden');
                        if (chevron) chevron.classList.add('rotate-180');
                    } else {
                        el.classList.add('hidden');
                        if (chevron) chevron.classList.remove('rotate-180');
                    }
                });
            } catch (e) {
                console.warn('initializeSidebarDropdown error:', e);
            }
        }

        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const intakeModal = document.getElementById('intakeSheetModal');
            const rejectionModal = document.getElementById('rejectionModal');
            
            if (event.target === intakeModal) {
                closeIntakeSheetModal();
            }
            if (event.target === rejectionModal) {
                closeRejectionModal();
            }
        }
    </script>
</body>
</html>