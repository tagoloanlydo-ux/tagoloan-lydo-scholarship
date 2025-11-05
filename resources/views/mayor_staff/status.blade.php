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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
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
            .spinner {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 480px) {
            .spinner {
                width: 60px;
                height: 60px;
            }
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

.pagination-page-input:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

        /* Responsive design for pagination */
@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        gap: 0.75rem;
    }

    .pagination-buttons {
        justify-content: center;
    }

    .pagination-btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }

    .pagination-info {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .pagination-buttons {
        gap: 0.25rem;
    }

    .pagination-btn {
        padding: 0.35rem 0.7rem;
        font-size: 0.75rem;
    }

    .pagination-page-info {
        font-size: 0.8rem;
    }

    .pagination-page-input {
        width: 3rem;
        padding: 0.3rem;
    }
}

/* Tab Styles */
/* Update the Tab Styles section */
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

/* Default active state */
.tab.active {
    color: white;
    border-color: transparent;
}

/* Purple for Pending Status tab */
#tab-pending.active {
    background-color: #7c3aed;
    border-color: #7c3aed;
}

#tab-pending.active:hover {
    background-color: #6d28d9;
}

/* Green for Approved/Rejected tab */
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

.filter-input {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    min-width: 200px;
}

.filter-input:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    background-color: white;
    transition: all 0.2s;
    min-width: 200px;
}

.filter-select:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.clear-filters-btn {
    padding: 0.5rem 1rem;
    background-color: #6b7280;
    color: white;
    border: none;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: background-color 0.2s;
    font-size: 0.875rem;
    font-weight: 500;
    align-self: flex-end;
    margin-bottom: 0.5rem;
}

.clear-filters-btn:hover {
    background-color: #4b5563;
}

@media (max-width: 768px) {
    .filter-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-input, .filter-select {
        min-width: 100%;
    }
    
    .clear-filters-btn {
        align-self: stretch;
    }
}
    </style>
</head>
<body class="bg-gray-50">
    <!-- Add this loading overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner">
            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
        </div>
    </div>
    <!-- Rest of your content -->

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

            return $screening === 'Approved'
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
    <button type="button" onclick="toggleDropdown('scholarMenu')"
        class="w-full flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white focus:outline-none">
        <div class="flex items-center">
            <i class="bx bxs-graduation text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Applicants</span>
        </div>
        <i class="bx bx-chevron-down ml-2"></i>
    </button>

    <!-- Dropdown Menu - changed to be in-flow so it pushes following items down -->
    <ul id="scholarMenu" class="mt-2 w-full bg-white shadow-lg border border-gray-200 rounded-lg space-y-2 hidden transition-all duration-200 overflow-hidden">
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
                    </div>

                    <!-- Pending Status Tab -->
                    <div id="tableView" class="overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-violet-50 p-3 rounded-lg border border-violet-200">
                            📋 Pending Status: View applicants awaiting status assignment.
                            </h3>
                        </div>
                        
  <!-- Add this before your table -->
<div class="filter-container">
    <div class="filter-group">
        <label for="searchInputTable" class="filter-label">Search by Name</label>
        <input type="text" id="searchInputTable" class="filter-input" placeholder="Type to search...">
    </div>

    <div class="filter-group">
        <label for="barangaySelectTable" class="filter-label">Filter by Barangay</label>
        <select id="barangaySelectTable" class="filter-select">
            <option value="">All Barangays</option>
            <!-- Add your barangay options dynamically -->
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
                        
                        <!-- Enhanced Filter Section for List View -->
        <!-- Add this before your table -->
<div class="filter-container">
    <div class="filter-group">
        <label for="listNameSearch" class="filter-label">Search by Name</label>
        <input type="text" id="listNameSearch" class="filter-input" placeholder="Type to search...">
    </div>

    <div class="filter-group">
        <label for="listBarangayFilter" class="filter-label">Filter by Barangay</label>
        <select id="listBarangayFilter" class="filter-select">
            <option value="">All Barangays</option>
            <!-- Add your barangay options dynamically -->
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p><strong>Barangay Clearance:</strong> <span id="modal-doc-brgy-clearance">-</span></p>
                                <p><strong>Certificate of Indigency:</strong> <span id="modal-doc-cert-indigency">-</span></p>
                                <p><strong>Certificate of Enrollment:</strong> <span id="modal-doc-cert-enrollment">-</span></p>
                                <p><strong>Certificate of Grades:</strong> <span id="modal-doc-cert-grades">-</span></p>
                            </div>
                            <div>
                                <p><strong>Birth Certificate:</strong> <span id="modal-doc-birth-cert">-</span></p>
                                <p><strong>Valid ID:</strong> <span id="modal-doc-valid-id">-</span></p>
                                <p><strong>Picture (2x2):</strong> <span id="modal-doc-picture">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-4 mt-6">
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

    <script>
 // Global variables
let currentApplicationId = null;
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
});

// Initialize modal events
function initializeModalEvents() {
    // View intake sheet buttons
    document.querySelectorAll('.view-intake-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            openIntakeSheetModal(id, name, 'intake');
        });
    });

    // Review renewal buttons
    document.querySelectorAll('.review-renewal-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            openIntakeSheetModal(id, name, 'renewal');
        });
    });

    // Approve button
    const approveBtn = document.getElementById('approveBtn');
    if (approveBtn) {
        approveBtn.addEventListener('click', function() {
            if (currentApplicationId && currentApplicationName) {
                approveApplication(currentApplicationId, currentApplicationName);
            }
        });
    }

    // Reject button
    const rejectBtn = document.getElementById('rejectBtn');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            if (currentApplicationId && currentApplicationName) {
                rejectApplication(currentApplicationId, currentApplicationName);
            }
        });
    }
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
    populateBarangayFilters();
    
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

// Populate barangay filters (keep your existing function)
function populateBarangayFilters() {
    const barangays = getUniqueBarangays();
    
    const tableBarangayFilter = document.getElementById('barangaySelectTable');
    const listBarangayFilter = document.getElementById('listBarangayFilter');

    const populateDropdown = (dropdown) => {
        if (!dropdown) return;
        dropdown.innerHTML = '<option value="">All Barangays</option>';
        barangays.forEach(barangay => {
            const option = document.createElement('option');
            option.value = barangay;
            option.textContent = barangay;
            dropdown.appendChild(option);
        });
    };

    populateDropdown(tableBarangayFilter);
    populateDropdown(listBarangayFilter);
}

// Get unique barangays (keep your existing function)
function getUniqueBarangays() {
    const barangays = new Set();
    
    document.querySelectorAll('#tableView tbody tr, #listView tbody tr').forEach(row => {
        const barangayCell = row.cells[2];
        if (barangayCell) {
            const barangay = barangayCell.textContent.trim();
            if (barangay) barangays.add(barangay);
        }
    });
    
    return Array.from(barangays).sort();
}

// Tab switching functions - UPDATED
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

// Keep all your existing modal functions (openIntakeSheetModal, closeIntakeSheetModal, etc.)
// They should work without changes

        // Modal functions
        function openIntakeSheetModal(id, name, type = 'intake') {
            currentApplicationId = id;
            currentApplicationName = name;

            // Update modal title based on type
            const modalTitle = document.querySelector('#intakeSheetModal .modal-header h2');
            if (modalTitle) {
                modalTitle.textContent = type === 'renewal' ? 'Renewal Documents Review' : 'Family Intake Sheet';
            }

            // Show loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('loadingOverlay').classList.remove('fade-out');

            // Fetch intake sheet data
            fetch(`/mayor_staff/intake-sheet/${id}`)
                .then(response => response.json())
                .then(data => {
                    populateIntakeSheetModal(data, type);
                    document.getElementById('intakeSheetModal').style.display = 'block';
                    document.getElementById('loadingOverlay').classList.add('fade-out');
                    setTimeout(() => {
                        document.getElementById('loadingOverlay').style.display = 'none';
                    }, 1000);
                })
                .catch(error => {
                    console.error('Error fetching intake sheet:', error);
                    Swal.fire('Error', 'Failed to load intake sheet data.', 'error');
                    document.getElementById('loadingOverlay').classList.add('fade-out');
                    setTimeout(() => {
                        document.getElementById('loadingOverlay').style.display = 'none';
                    }, 1000);
                });
        }

        function closeIntakeSheetModal() {
            document.getElementById('intakeSheetModal').style.display = 'none';
            currentApplicationId = null;
            currentApplicationName = null;
        }

        function populateIntakeSheetModal(data) {
            // Populate head of family section
            document.getElementById('modal-applicant-name').textContent = data.applicant_name || '-';
            document.getElementById('modal-applicant-gender').textContent = data.gender || '-';
            document.getElementById('modal-remarks').textContent = data.remarks || '-';
            document.getElementById('modal-head-dob').textContent = data.dob || '-';
            document.getElementById('modal-head-pob').textContent = data.pob || '-';
            document.getElementById('modal-head-address').textContent = data.address || '-';
            document.getElementById('modal-head-zone').textContent = data.zone || '-';
            document.getElementById('modal-head-barangay').textContent = data.barangay || '-';
            document.getElementById('modal-head-religion').textContent = data.religion || '-';
            document.getElementById('modal-serial-number').textContent = data.serial_number || '-';
            document.getElementById('modal-head-4ps').textContent = data.four_ps || '-';
            document.getElementById('modal-head-ipno').textContent = data.ip_no || '-';
            document.getElementById('modal-head-educ').textContent = data.education || '-';
            document.getElementById('modal-head-occ').textContent = data.occupation || '-';

            // Populate household information
            document.getElementById('modal-house-total-income').textContent = data.total_income || '-';
            document.getElementById('modal-house-net-income').textContent = data.net_income || '-';
            document.getElementById('modal-other-income').textContent = data.other_income || '-';
            document.getElementById('modal-house-house').textContent = data.house || '-';
            document.getElementById('modal-house-lot').textContent = data.lot || '-';
            document.getElementById('modal-house-electric').textContent = data.electricity || '-';
            document.getElementById('modal-house-water').textContent = data.water || '-';

            // Populate family members
            const familyMembersTbody = document.getElementById('modal-family-members');
            familyMembersTbody.innerHTML = '';
            
            if (data.family_members && data.family_members.length > 0) {
                data.family_members.forEach(member => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${member.name || '-'}</td>
                        <td>${member.relation || '-'}</td>
                        <td>${member.birthdate || '-'}</td>
                        <td>${member.age || '-'}</td>
                        <td>${member.gender || '-'}</td>
                        <td>${member.civil_status || '-'}</td>
                        <td>${member.education || '-'}</td>
                        <td>${member.occupation || '-'}</td>
                        <td>${member.income || '-'}</td>
                        <td>${member.remarks || '-'}</td>
                    `;
                    familyMembersTbody.appendChild(row);
                });
            } else {
                familyMembersTbody.innerHTML = '<tr><td colspan="10" class="text-center py-4">No family members found</td></tr>';
            }

            // Populate service records
            const serviceRecordsTbody = document.getElementById('modal-service-records');
            serviceRecordsTbody.innerHTML = '';
            
            if (data.service_records && data.service_records.length > 0) {
                data.service_records.forEach(record => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${record.date || '-'}</td>
                        <td>${record.problem || '-'}</td>
                        <td>${record.action || '-'}</td>
                        <td>${record.remarks || '-'}</td>
                    `;
                    serviceRecordsTbody.appendChild(row);
                });
            } else {
                serviceRecordsTbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">No service records found</td></tr>';
            }

            // Populate documents
            document.getElementById('modal-doc-brgy-clearance').textContent = data.brgy_clearance || '-';
            document.getElementById('modal-doc-cert-indigency').textContent = data.cert_indigency || '-';
            document.getElementById('modal-doc-cert-enrollment').textContent = data.cert_enrollment || '-';
            document.getElementById('modal-doc-cert-grades').textContent = data.cert_grades || '-';
            document.getElementById('modal-doc-birth-cert').textContent = data.birth_cert || '-';
            document.getElementById('modal-doc-valid-id').textContent = data.valid_id || '-';
            document.getElementById('modal-doc-picture').textContent = data.picture || '-';

            // Populate signatures and photos
            document.getElementById('modal-worker-fullname').textContent = data.worker_name || '-';
            document.getElementById('modal-officer-fullname').textContent = data.officer_name || '-';
            document.getElementById('modal-date-entry').textContent = data.date_entry || '-';

            // Handle photos
            const workerPhotoImg = document.getElementById('modal-worker-photo-img');
            const officerPhotoImg = document.getElementById('modal-officer-photo-img');
            
            if (data.worker_photo) {
                workerPhotoImg.src = data.worker_photo;
                workerPhotoImg.style.display = 'block';
            } else {
                workerPhotoImg.style.display = 'none';
            }
            
            if (data.officer_photo) {
                officerPhotoImg.src = data.officer_photo;
                officerPhotoImg.style.display = 'block';
            } else {
                officerPhotoImg.style.display = 'none';
            }

            // Handle signatures
            const workerSignatureDiv = document.getElementById('modal-worker-signature');
            const officerSignatureDiv = document.getElementById('modal-officer-signature');
            const clientSignatureDiv = document.getElementById('modal-client-signature-large');
            
            workerSignatureDiv.innerHTML = data.worker_signature ? 
                `<img src="${data.worker_signature}" alt="Worker Signature" style="max-width:180px;height:60px;object-fit:contain;">` : 
                '<div style="height:60px;"></div>';
                
            officerSignatureDiv.innerHTML = data.officer_signature ? 
                `<img src="${data.officer_signature}" alt="Officer Signature" style="max-width:180px;height:60px;object-fit:contain;">` : 
                '<div style="height:60px;"></div>';
                
            clientSignatureDiv.innerHTML = data.client_signature ? 
                `<img src="${data.client_signature}" alt="Client Signature" style="max-width:300px;height:80px;object-fit:contain;">` : 
                '<div style="height:80px;"></div>';
        }

        // Application approval and rejection
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
                    // Show loading
                    document.getElementById('loadingOverlay').style.display = 'flex';
                    document.getElementById('loadingOverlay').classList.remove('fade-out');
                    
                    // Send approval request
                    fetch(`/mayor_staff/application/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loadingOverlay').classList.add('fade-out');
                        setTimeout(() => {
                            document.getElementById('loadingOverlay').style.display = 'none';
                        }, 1000);
                        
                        if (data.success) {
                            Swal.fire('Approved!', `Application for ${name} has been approved.`, 'success')
                                .then(() => {
                                    closeIntakeSheetModal();
                                    location.reload();
                                });
                        } else {
                            Swal.fire('Error!', data.message || 'Failed to approve application.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error approving application:', error);
                        document.getElementById('loadingOverlay').classList.add('fade-out');
                        setTimeout(() => {
                            document.getElementById('loadingOverlay').style.display = 'none';
                        }, 1000);
                        Swal.fire('Error!', 'Failed to approve application.', 'error');
                    });
                }
            });
        }

        function rejectApplication(id, name) {
            Swal.fire({
                title: 'Reject Application?',
                text: `Are you sure you want to reject ${name}'s application?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    document.getElementById('loadingOverlay').style.display = 'flex';
                    document.getElementById('loadingOverlay').classList.remove('fade-out');

                    // Send rejection request
                    fetch(`/mayor_staff/application/${id}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loadingOverlay').classList.add('fade-out');
                        setTimeout(() => {
                            document.getElementById('loadingOverlay').style.display = 'none';
                        }, 1000);

                        if (data.success) {
                            Swal.fire('Rejected!', `Application for ${name} has been rejected.`, 'success')
                                .then(() => {
                                    closeIntakeSheetModal();
                                    location.reload();
                                });
                        } else {
                            Swal.fire('Error!', data.message || 'Failed to reject application.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error rejecting application:', error);
                        document.getElementById('loadingOverlay').classList.add('fade-out');
                        setTimeout(() => {
                            document.getElementById('loadingOverlay').style.display = 'none';
                        }, 1000);
                        Swal.fire('Error!', 'Failed to reject application.', 'error');
                    });
                }
            });
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


    </script>

<script src="{{ asset('js/spinner.js') }}"></script>

// ...existing code...
<script>
function toggleDropdown(id) {
    const el = document.getElementById(id);
    if (!el) return;

    const btn = document.querySelector(`button[onclick="toggleDropdown('${id}')"]`);
    const chevron = btn ? btn.querySelector('i.bx') : null;

    const willOpen = el.classList.contains('hidden'); // if hidden now, will open
    // Toggle visibility
    el.classList.toggle('hidden');

    // Optional: rotate chevron for visual cue
    if (chevron) {
        chevron.classList.toggle('rotate-180', willOpen);
    }

    // Persist state in localStorage so it stays open across pages
    try {
        localStorage.setItem(`sidebarDropdown_${id}`, willOpen ? 'open' : 'closed');
    } catch (e) {
        console.warn('Could not persist dropdown state:', e);
    }
}

function initializeSidebarDropdown() {
    // Restore saved dropdown states on page load
    try {
        Object.keys(localStorage).forEach(key => {
            if (!key.startsWith('sidebarDropdown_')) return;
            const id = key.replace('sidebarDropdown_', '');
            const state = localStorage.getItem(key);
            const el = document.getElementById(id);
            if (!el) return;

            const btn = document.querySelector(`button[onclick="toggleDropdown('${id}')"]`);
            const chevron = btn ? btn.querySelector('i.bx') : null;

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

// Call initializeSidebarDropdown if DOM already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSidebarDropdown);
} else {
    initializeSidebarDropdown();
}
</script>
// ...existing code...
</body>
</html>
