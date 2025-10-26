<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Enhanced UI Improvements */
        .document-loading {
            position: relative;
        }
        .document-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-feedback {
            transition: all 0.1s ease;
        }
        .btn-feedback:active {
            transform: scale(0.95);
        }

        .comment-counter {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: right;
            margin-top: 0.25rem;
        }
        .comment-counter.warning {
            color: #f59e0b;
        }
        .comment-counter.error {
            color: #ef4444;
        }

        .tooltip {
            position: relative;
            display: inline-block;
        }
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.75rem;
        }
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        .document-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .document-item {
                padding: 0.75rem !important;
            }
            .grid.grid-cols-1.md\\:grid-cols-5 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .max-w-6xl {
                max-width: 95vw !important;
            }
        }

        .auto-save-indicator {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 0.75rem;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .auto-save-indicator.visible {
            opacity: 1;
        }
        .auto-save-indicator.saving {
            color: #f59e0b;
        }
        .auto-save-indicator.saved {
            color: #10b981;
        }

        /* Improved Document Modal Styles */
        .document-modal-content {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .document-viewer-container {
            height: 70vh;
            overflow: hidden;
        }

        .document-viewer {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
            background: #f8fafc;
        }

        /* Custom scrollbar for document modal */
        .document-modal-content::-webkit-scrollbar {
            width: 8px;
        }

        .document-modal-content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .document-modal-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .document-modal-content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Document item styles */
        .document-item {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .document-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .document-icon {
            transition: transform 0.3s ease;
        }

        .document-item:hover .document-icon {
            transform: scale(1.1);
        }

        /* NEW: Document status badge styles */
        .document-status-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .badge-new {
            background-color: #3b82f6; /* blue */
        }

        .badge-good {
            background-color: #10b981; /* green */
        }

        .badge-bad {
            background-color: #ef4444; /* red */
        }

        .badge-updated {
            background-color: #f59e0b; /* amber/orange for updated */
        }

        .badge-new-inline {
            display: inline-block;
            background-color: #3b82f6; /* blue */
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 4px;
            border-radius: 4px;
            margin-left: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .document-item-wrapper {
            position: relative;
            display: inline-block;
        }

        /* Tab button styles */
        .tab {
            padding: 10px 20px;
            background-color: #f3f4f6;
            color: #6b7280;
            border: 1px solid #d1d5db;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .tab:hover {
            background-color: #e5e7eb;
            color: #374151;
        }

        .tab.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }

        .tab.active:hover {
            background-color: #2563eb;
        }

        /* Fixed Layout Styles */
        body {
            height: 100vh;
            overflow: hidden;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .sidebar-fixed {
            position: fixed;
            top: 80px; /* header height + padding */
            left: 0;
            bottom: 0;
            width: 64px; /* w-16 */
            overflow-y: auto;
            z-index: 999;
        }

        @media (min-width: 768px) {
            .sidebar-fixed {
                width: 256px; /* md:w-64 */
            }
        }

        .main-content-fixed {
            position: fixed;
            top: 80px; /* header height + padding */
            left: 64px; /* sidebar width on small screens */
            right: 0;
            bottom: 0;
            overflow-y: auto;
            padding: 1rem 1.25rem; /* p-4 md:p-5 */
        }

        @media (min-width: 768px) {
            .main-content-fixed {
                left: 256px; /* sidebar width on medium+ screens */
            }
        }

        /* Responsive Pagination Styles */
        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 1rem;
        }

        .pagination li {
            margin: 0 2px;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #e9ecef;
        }

        .pagination .active span {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        @media (max-width: 768px) {
            .pagination li {
                margin: 2px;
            }
            .pagination a, .pagination span {
                padding: 6px 8px;
                font-size: 14px;
            }
            .pagination {
                justify-content: center;
            }
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
        <div class="flex items-center">
            <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-2xl font-bold text-white ml-4">Lydo Scholarship</h1>
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
                    <div class="p-3 border-b font-semibold text-violet-600">Notifications</div>
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

    <!-- Sidebar -->
    <div class="sidebar-fixed bg-white shadow-md flex flex-col transition-all duration-300">
        <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
            <ul class="side-menu top space-y-4">
                <li>
                    <a href="/mayor_staff/dashboard" class="w-ful flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
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
                            <a href="/mayor_staff/application" class="flex items-center p-2 rounded-lg text-white bg-violet-600">
                            <i class="bx bx-search-alt mr-2 text-white-700"></i> Review Applications
                            </a>
                        </li>
                        <li>
                            <a href="/mayor_staff/status" class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                            <i class="bx bx-check-circle mr-2 text-white-700"></i> Update Status
                            </a>
                        </li>
                    </ul>
                </li>
             </ul>
                    <ul class="side-menu space-y-1">
                        <li>
                            <a href="/mayor_staff/settings" class="w-full flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bxs-cog text-center mx-auto md:mx-0 text-xl text-white-700"></i>
                                <span class="ml-4 hidden md:block text-base">Settings</span>
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

    <!-- Main Content -->
    <div class="main-content-fixed text-[16px]">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-3xl font-bold text-gray-800">Review Applicants Application</h5>
                </div>
                    <!-- 🔎 View Switch -->
                    <div class="flex justify-start items-center mb-6 gap-4">
                                    <!-- Tab Switch -->
                        <div class="flex gap-2">
                            <div class="tab active" onclick="showTable()">Pending Review</div>
                             <div class="tab" onclick="showList()">Reviewed Applications</div>
                            </div>
                         </div>

            <!-- ✅ Table View (Applicants without remarks) -->
            <div id="tableView">
                <!-- Search and Filter Section -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <!-- Search by Name -->
                        <div class="flex-1">
                            <label for="searchInputTable" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                            <input type="text" id="searchInputTable" placeholder="Enter applicant name..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Filter by Barangay -->
                        <div class="flex-1">
                            <label for="barangaySelectTable" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                            <select id="barangaySelectTable"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Clear Filters Button -->
                        <div class="flex-shrink-0">
                            <button onclick="clearFiltersTable()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-200">
                        The list below shows applicants who have submitted applications
                    </h3>
                </div>
            <table class="w-full table-auto border-collapse text-[17px] shadow-lg rounded-lg overflow-visible border border-gray-200">
                <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white uppercase text-sm">
                    <tr>
                        <th class="px-6 py-4 align-middle text-center">#</th>
                        <th class="px-6 py-4 align-middle text-center">Name</th>
                        <th class="px-6 py-4 align-middle text-center">Barangay</th>
                        <th class="px-6 py-4 align-middle text-center">Gender</th>
                        <th class="px-6 py-4 align-middle text-center">Birthday</th>
                        <th class="px-6 py-4 align-middle text-center">Applications</th>
                        <th class="px-6 py-4 align-middle text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @php $count = 1; @endphp
                    @forelse($tableApplicants as $index => $app)
                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-center">{{ $count++ }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                            <td class="px-6 py-4 text-center">{{ $app->applicant_brgy }}</td>
                            <td class="px-6 py-4 text-center">{{ $app->applicant_gender }}</td>
                            <td class="px-6 py-4 text-center date-format">{{ $app->applicant_bdate }}</td>
                            <td class="px-6 py-4 text-center">
                                <button
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm"
                                    onclick="openApplicationModal({{ $app->application_personnel_id }}, 'pending')">
                                    Review Applications
                                </button>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <form method="POST" action="/mayor_staff/application/{{ $app->application_personnel_id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDeletePending(this)" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 text-sm font-medium transition-colors duration-200 shadow-sm">
                                        <i class="fas fa-trash mr-2"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 bg-gray-50">No Application found for the current year.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $tableApplicants->links() }}
            </div>
        </div>

            <!-- ✅ List View (Approved and Rejected applications) -->
    <div id="listView" class="hidden overflow-x-auto">
        <!-- Filter controls specific to List View -->
        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <!-- Search by Name -->
                <div class="flex-1">
                    <label for="searchInputList" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                    <input type="text" id="searchInputList" placeholder="Enter applicant name..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filter by Barangay -->
                <div class="flex-1">
                    <label for="barangaySelectList" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                    <select id="barangaySelectList"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $brgy)
                            <option value="{{ $brgy }}">{{ $brgy }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filters Button -->
                <div class="flex-shrink-0">
                    <button onclick="clearFiltersList()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-700 bg-green-50 p-3 rounded-lg border border-green-200">
            The list below shows applicants who have approved and rejected screening
            </h3>
        </div>
            <table class="w-full table-auto border-collapse text-[17px] shadow-lg rounded-lg overflow-visible border border-gray-200">
        <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
            <tr>
            <th class="px-6 py-4 align-middle text-center">#</th>
                <th class="px-6 py-4 align-middle text-center">Name</th>
                <th class="px-6 py-4 align-middle text-center">Barangay</th>
                <th class="px-6 py-4 align-middle text-center">Gender</th>
                <th class="px-6 py-4 align-middle text-center">Birthday</th>
                <th class="px-6 py-4 align-middle text-center">Initial Screening</th>
                <th class="px-6 py-4 align-middle text-center">Application</th>
                <th class="px-6 py-4 align-middle text-center">Action</th>

            </tr>
        </thead>
                        <tbody class="bg-white">
            @php $count = 1; @endphp
            @forelse($listApplicants as $index => $app)
                <tr class="border-b border-gray-200 hover:bg-green-50 transition-colors duration-200">
                    <td class="px-6 py-4 text-center">{{ $count++ }}</td>
                    <td class="px-6 py-4 text-center font-medium">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                    <td class="px-6 py-4 text-center">{{ $app->applicant_brgy }}</td>
                    <td class="px-6 py-4 text-center">{{ $app->applicant_gender }}</td>
                    <td class="px-6 py-4 text-center date-format">{{ $app->applicant_bdate }}</td>
                    <td class="px-6 py-4 text-center">{{ $app->initial_screening }}</td>
                    <td class="px-6 py-4 text-center">
                        <button type="button"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm"
                            onclick="openApplicationModal({{ $app->application_personnel_id }}, 'reviewed')">
                            Review Requirements
                        </button>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 text-sm font-medium transition-colors duration-200 shadow-sm" onclick="openDeleteModal({{ $app->application_personnel_id }}, '{{ $app->applicant_fname }} {{ $app->applicant_lname }}', true)">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 bg-gray-50">No approved or rejected applications found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    </div>
    </div>


     <div id="applicationModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
         <div class="bg-white w-full max-w-6xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl animate-fadeIn">
        
        <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-folder-open text-blue-600"></i>
                    Application Requirements</h2>
            <button onclick="closeApplicationModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-times text-gray-500 text-lg"></i>
            </button>
        </div>
        <!-- Body -->
        <div id="applicationContent" class="p-6 space-y-4">
        <!-- Dynamic Content via JS -->
        </div>
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">

        </div>
    </div>
    </div>

    <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl animate-fadeIn">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
                Confirm Delete
            </h2>
            <button onclick="closeDeleteModal()"
                    class="p-2 rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-times text-gray-500 text-lg"></i>
            </button>
            </div>

        <!-- Body -->
        <div id="deleteModalContent" class="p-6 space-y-4">
        <p class="text-gray-700">Are you sure you want to delete the application for <strong id="deleteApplicantName"></strong>?</p>
        <p class="text-sm text-gray-500">This action cannot be undone.</p>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeDeleteModal()"
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Cancel
        </button>
        <form id="deleteForm" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
            <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </form>
        </div>
    </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl animate-fadeIn">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-times-circle text-red-600"></i>
            Reject Initial Screening
        </h2>
        <button onclick="closeRejectionModal()"
                class="p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
        <p class="text-gray-700">Please provide the reason for rejecting this application:</p>
        <form id="rejectionForm">
            <div class="mb-4">
            <label for="rejectionReason" class="block text-gray-700 font-medium mb-2">Reason for Rejection</label>
            <textarea id="rejectionReason" name="reason" rows="4" class="w-full border rounded px-3 py-2" placeholder="Enter the reason for rejection..." required></textarea>
            </div>
        </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeRejectionModal()"
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Cancel
        </button>
        <button id="rejectSubmitBtn" onclick="submitRejection()" class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition flex items-center gap-2">
            <i class="fas fa-times"></i>
            <span id="rejectSubmitBtnText">Reject Application</span>
            <div id="rejectSubmitBtnSpinner" class="hidden ml-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            </div>
        </button>
        </div>
    </div>
    </div>

    <!-- Edit Initial Screening Modal -->
    <div id="editInitialScreeningModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl animate-fadeIn">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-edit text-blue-600"></i>
            Edit Initial Screening
        </h2>
        <button onclick="closeEditInitialScreeningModal()"
                class="p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
        <p class="text-gray-700">Update the initial screening status for this application:</p>
        <form id="editInitialScreeningForm" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="application_personnel_id" id="editApplicationPersonnelId" />
        <div class="mb-4">
        <label for="initialScreeningStatus" class="block text-gray-700 font-medium mb-2">Initial Screening Status</label>
        <select id="initialScreeningStatus" name="initial_screening_status" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Status</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>
        </div>
        </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeEditInitialScreeningModal()"
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Cancel
        </button>
        <button onclick="submitEditInitialScreening()" class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
            <i class="fas fa-save mr-2"></i> Update
        </button>
        </div>
    </div>
    </div>

        <!-- Document Viewer Modal -->
    <div id="documentModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-6xl max-h-[90vh] rounded-2xl shadow-2xl animate-fadeIn">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 id="documentModalTitle" class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-file-alt text-blue-600"></i>
            Document Viewer
        </h2>
        <button onclick="closeDocumentModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
        </div>

        <!-- Body - Improved with better scrolling -->
        <div class="document-modal-content p-6">
            <div class="document-viewer-container mb-4">
                <iframe id="documentViewer" src="" class="document-viewer"></iframe>
            </div>
            <div id="documentReviewControls" class="mt-4"></div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeDocumentModal()" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Close
        </button>
        </div>
    </div>
    </div>

    <script>
        // Global variables
        let currentApplicationId = null;
        let currentSource = null;
        let ratedDocuments = new Set();
        let updatedDocuments = new Set();
        let openedDocuments = new Set();
        let previousDocumentStatus = {};

        // Filtering: separate inputs for Table and List views
        function filterRows(tableBodySelector, searchInputId, barangaySelectId) {
            try {
                const searchEl = document.getElementById(searchInputId);
                const barangayEl = document.getElementById(barangaySelectId);
                const searchValue = searchEl ? searchEl.value.toLowerCase() : '';
                const barangayValue = barangayEl ? barangayEl.value : '';

                const tableBody = document.querySelector(tableBodySelector);
                if (!tableBody) return;

                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const nameCell = row.cells[1]; // Name column
                    const barangayCell = row.cells[2]; // Barangay column

                    if (nameCell && barangayCell) {
                        const nameText = nameCell.textContent.toLowerCase();
                        const barangayText = barangayCell.textContent.trim();

                        const matchesSearch = searchValue === '' || nameText.includes(searchValue);
                        const matchesBarangay = barangayValue === '' || barangayText === barangayValue;

                        if (matchesSearch && matchesBarangay) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            } catch (e) {
                console.error('filterRows error', e);
            }
        }

        // Attach listeners (safe: only add if elements exist)
        const attachFilterListeners = () => {
            const debounceDelay = 150;

            const tableSearch = document.getElementById('searchInputTable');
            const tableBrgy = document.getElementById('barangaySelectTable');
            if (tableSearch) tableSearch.addEventListener('input', debounce(() => filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable'), debounceDelay));
            if (tableBrgy) tableBrgy.addEventListener('change', () => filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable'));

            const listSearch = document.getElementById('searchInputList');
            const listBrgy = document.getElementById('barangaySelectList');
            if (listSearch) listSearch.addEventListener('input', debounce(() => filterRows('#listView tbody', 'searchInputList', 'barangaySelectList'), debounceDelay));
            if (listBrgy) listBrgy.addEventListener('change', () => filterRows('#listView tbody', 'searchInputList', 'barangaySelectList'));
        };

        // Clear filters function for table view
        function clearFiltersTable() {
            document.getElementById('searchInputTable').value = '';
            document.getElementById('barangaySelectTable').value = '';
            filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable');
        }

        function showTable() {
            document.getElementById("tableView").classList.remove("hidden");
            document.getElementById("listView").classList.add("hidden");
            document.querySelector('.tab.active').classList.remove('active');
            document.querySelectorAll('.tab')[0].classList.add('active');
            localStorage.setItem("viewMode", "table"); // save preference
            // Run filter for table view after showing
            filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable');
        }

        function showList() {
            document.getElementById("listView").classList.remove("hidden");
            document.getElementById("tableView").classList.add("hidden");
            document.querySelector('.tab.active').classList.remove('active');
            document.querySelectorAll('.tab')[1].classList.add('active');
            localStorage.setItem("viewMode", "list"); // save preference
            // Run filter for list view after showing
            filterRows('#listView tbody', 'searchInputList', 'barangaySelectList');
        }

        // ✅ Kapag nag-load ang page, i-apply yung last view
        document.addEventListener("DOMContentLoaded", function() {
            let viewMode = localStorage.getItem("viewMode") || "table"; // default table
            if(viewMode === "list") {
                showList();
            } else {
                showTable();
            }
            // Attach filter listeners for both views and run initial filters
            attachFilterListeners();
            filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable');
            filterRows('#listView tbody', 'searchInputList', 'barangaySelectList');
        });

        // ✅ Application Modal Functions
        const applications = @json($applications);

        function openApplicationModal(applicationPersonnelId, source = 'pending') {
            // Store the current source globally
            currentSource = source;
            
            const contentDiv = document.getElementById('applicationContent');
            contentDiv.innerHTML = '';

            // Store the current application ID globally for approve/reject functions
            currentApplicationId = applicationPersonnelId;

            // Find the application by application_personnel_id
            let foundApp = null;
            for (let applicantId in applications) {
                if (applications[applicantId]) {
                    foundApp = applications[applicantId].find(app => app.application_personnel_id == applicationPersonnelId);
                    if (foundApp) break;
                }
            }

            if(foundApp) {
                contentDiv.innerHTML += `
                    <div class="border border-gray-200 rounded-xl shadow-lg bg-white p-6 mb-6">
                        <!-- Academic Details Row -->
                        <div class="mb-6">
                            <h4 class="text-gray-800 font-semibold mb-4 flex items-center">
                                <i class="fas fa-graduation-cap text-indigo-600 mr-2"></i>
                                Academic Information
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-school text-blue-600 text-xl mr-3"></i>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">School Name</h3>
                                        <p class="text-gray-700 font-medium">${foundApp.school_name || 'Not specified'}</p>
                                    </div>
                                </div>
                            </div>
                                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                                        <span class="text-sm font-semibold text-green-800">Academic Year</span>
                                    </div>
                                    <p class="text-gray-700 font-medium">${foundApp.academic_year || 'Not specified'}</p>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-4 rounded-lg border border-blue-200">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                                        <span class="text-sm font-semibold text-blue-800">Year Level</span>
                                    </div>
                                    <p class="text-gray-700 font-medium">${foundApp.year_level || 'Not specified'}</p>
                                </div>
                                <div class="bg-gradient-to-br from-purple-50 to-violet-50 p-4 rounded-lg border border-purple-200">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-book text-purple-600 mr-2"></i>
                                        <span class="text-sm font-semibold text-purple-800">Course</span>
                                    </div>
                                    <p class="text-gray-700 font-medium">${foundApp.course || 'Not specified'}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-6 border-gray-300">

                        <!-- Documents Section -->
                        <h4 class="text-gray-800 font-semibold mb-4 flex items-center">
                            <i class="fas fa-folder-open text-gray-600 mr-2"></i>
                            Submitted Documents
                        </h4>
                                            <p class="text-sm text-gray-600 mb-6 bg-white p-3 rounded-lg border-l-4 border-indigo-400">
                                <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                                Click one of the documents to view and review
                            </p>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4" id="documentsContainer">
            <!-- Documents will be dynamically generated here -->
        </div>

                    </div>
                `;
                
                // Generate document items with status badges
                generateDocumentItems(foundApp);
            } else {
                contentDiv.innerHTML = `<p class="text-gray-500">No applications found for this scholar.</p>`;
            }

            // Initially hide action buttons for pending applications
            const footerDiv = document.querySelector('.flex.justify-end.gap-3.px-6.py-4.border-t.bg-gray-50.rounded-b-2xl');
            if (source === 'pending') {
                footerDiv.innerHTML = `
                <div id="actionButtons" class="flex flex-row items-center gap-3 hidden">

            <!-- APPROVE BUTTON -->
            <button id="approveBtn" onclick="approveApplication()"
                class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition flex items-center gap-2">
                <i class="fas fa-check"></i>
                <span id="approveBtnText">Approved for Interview</span>
                <div id="approveBtnSpinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2
                            5.291A7.962 7.962 0 014 12H0c0
                            3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </button>

            <!-- REJECT BUTTON -->
            <button id="rejectBtn" onclick="rejectApplication()"
                class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-times"></i>
                <span id="rejectBtnText">Reject for Interview</span>
                <div id="rejectBtnSpinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0
                            5.373 0 12h4zm2 5.291A7.962 7.962 0
                            014 12H0c0 3.042 1.135 5.824 3
                            7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </button>

            <!-- SEND EMAIL BUTTON -->
            <button id="sendEmailBtn" onclick="sendDocumentEmail()"
                class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-envelope"></i>
                <span id="sendEmailBtnText">Send Email</span>
                <div id="sendEmailBtnSpinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </button>
        </div>

                    <div id="reviewMessage" class="text-gray-600 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>Please review all 5 documents before making a decision.
                    </div>
                `;
            } else {
                footerDiv.innerHTML = `
                    <button id="sendEmailBtn" onclick="sendDocumentEmail()"
                        class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-envelope"></i>
                        <span id="sendEmailBtnText">Send Email</span>
                        <div id="sendEmailBtnSpinner" class="hidden ml-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </button>
                `;
            }

            document.getElementById('applicationModal').classList.remove('hidden');

                        // Load existing comments and statuses
            loadDocumentComments(applicationPersonnelId, source);
            
            // NEW: Check for document updates to show NEW badges
            trackDocumentUpdates(applicationPersonnelId);
        }

        // NEW: Function to generate document items with status badges
        function generateDocumentItems(foundApp) {
            const documentsContainer = document.getElementById('documentsContainer');
            const documentTypes = [
                { type: 'application_letter', name: 'Application Letter', url: foundApp.application_letter },
                { type: 'cert_of_reg', name: 'Certificate of Registration', url: foundApp.cert_of_reg },
                { type: 'grade_slip', name: 'Grade Slip', url: foundApp.grade_slip },
                { type: 'brgy_indigency', name: 'Barangay Indigency', url: foundApp.brgy_indigency },
                { type: 'student_id', name: 'Student ID', url: foundApp.student_id }
            ];

            documentsContainer.innerHTML = '';
            
            documentTypes.forEach(doc => {
                documentsContainer.innerHTML += `
                    <div class="document-item-wrapper">
                        <div class="document-item bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200" 
                             data-document-type="${doc.type}" 
                             data-document-url="${doc.url}">
                            <div class="flex flex-col items-center justify-center">
                                <a href="#" onclick="openDocumentModal('${doc.url}', '${doc.name}', '${doc.type}')" class="flex flex-col items-center cursor-pointer w-full">
                                    <i class="fas fa-file-alt text-purple-600 text-3xl mb-3 document-icon" id="icon-${doc.type}"></i>
                                    <span class="text-sm font-medium text-gray-700 text-center">${doc.name}</span>
                                </a>
                            </div>
                        </div>
                        <div class="document-status-badge hidden" id="badge-${doc.type}"></div>
                    </div>
                `;
            });
        }

        // NEW: Function to update document status badges
        function updateDocumentBadges(documentType, status, isNew = false) {
            const badge = document.getElementById(`badge-${documentType}`);
            const icon = document.getElementById(`icon-${documentType}`);
            
            // Reset all styles first
            badge.classList.remove('badge-new', 'badge-good', 'badge-bad', 'badge-updated', 'hidden');
            icon.classList.remove('text-red-600', 'text-green-600', 'text-gray-500', 'text-purple-600');
            
            // Apply new status
            if (status === 'good') {
                badge.classList.add('badge-good');
                badge.innerHTML = '✓';
                icon.classList.add('text-green-600');
                badge.classList.remove('hidden');
            } else if (status === 'bad') {
                badge.classList.add('badge-bad');
                badge.innerHTML = '✗';
                icon.classList.add('text-red-600');
                badge.classList.remove('hidden');
            } else if (isNew) {
                badge.classList.add('badge-new');
                badge.innerHTML = 'NEW';
                badge.classList.remove('hidden');
                icon.classList.add('text-purple-600');
            } else {
                // No status, hide the badge
                badge.classList.add('hidden');
                icon.classList.add('text-purple-600');
            }
            
            // Special case: if document was bad but has been updated
            if (status === 'bad' && isNew) {
                badge.classList.remove('badge-bad');
                badge.classList.add('badge-updated');
                badge.innerHTML = 'NEW';
                icon.classList.remove('text-red-600');
                icon.classList.add('text-purple-600');
            }
        }

        // NEW: Function to track document updates and show NEW badge
        function trackDocumentUpdates(applicationPersonnelId) {
            // Check if any documents have been updated since last review
            fetch(`/mayor_staff/check-document-updates/${applicationPersonnelId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.updated_documents) {
                        data.updated_documents.forEach(docType => {
                            // Show NEW badge for updated documents
                            updateDocumentBadges(docType, null, true);
                            
                            // If document was previously bad and now has NEW status,
                            // we need to track this for the reject button logic
                            if (previousDocumentStatus && 
                                previousDocumentStatus[docType] === 'bad') {
                                markDocumentAsUpdated(docType);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error checking document updates:', error);
                });
        }

        // NEW: Function to mark a document as updated (from bad to new)
        function markDocumentAsUpdated(documentType) {
            updatedDocuments.add(documentType);
            console.log('Updated documents:', updatedDocuments);
        }

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

        function loadDocumentComments(applicationPersonnelId) {
            console.log('Loading comments for application:', applicationPersonnelId);
            
            fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);
                    if (data.success) {
                        const comments = data.comments || {};
                        const statuses = data.statuses || {};

                        console.log('Comments:', comments);
                        console.log('Statuses:', statuses);

                        // Document types to check
                        const documentTypes = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];

                        // Initialize rated documents tracking
                        ratedDocuments = new Set();

                        // Store previous status for comparison
                        previousDocumentStatus = {};

                        documentTypes.forEach(docType => {
                            console.log(`Processing ${docType}:`, comments[docType], statuses[`${docType}_status`]);

                            // Load status
                            const status = statuses[`${docType}_status`];
                            console.log(`Status for ${docType}:`, status);
                            
                            // Store previous status
                            previousDocumentStatus[docType] = status;
                            
                            // Update document badges based on status
                            updateDocumentBadges(docType, status, false);
                            
                            // If document has a status, consider it as rated and opened
                            if (status === 'good' || status === 'bad') {
                                ratedDocuments.add(docType);
                            }
                        });
                        
                        // Check if all documents are already rated
                        checkAllDocumentsRated();
                    } else {
                        console.error('API returned error:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading document comments:', error);
                });
        }

        function trackRatedDocument(documentType) {
            ratedDocuments.add(documentType);
            console.log('Rated documents:', ratedDocuments);
            
            // Check if all 5 documents have been rated
            checkAllDocumentsRated();
        }

        function checkAllDocumentsRated() {
            const documentTypes = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];
            
            if (ratedDocuments && ratedDocuments.size === 5) {
                // Count good, bad, and updated documents
                let goodCount = 0;
                let badCount = 0;
                let updatedCount = updatedDocuments ? updatedDocuments.size : 0;
                
                documentTypes.forEach(docType => {
                    // We'll check the actual status from the database
                    const applicationPersonnelId = currentApplicationId;
                    
                    // Make an API call to get the actual status
                    fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const statuses = data.statuses || {};
                                const status = statuses[`${docType}_status`];
                                
                                if (status === 'good') {
                                    goodCount++;
                                } else if (status === 'bad') {
                                    badCount++;
                                }
                                
                                // After checking all documents, update the UI
                                if (goodCount + badCount === 5) {
                                    updateActionButtons(goodCount, badCount, updatedCount);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error checking document status:', error);
                        });
                });
            } else {
                console.log(`Not all documents rated: ${ratedDocuments ? ratedDocuments.size : 0}/5`);
            }
        }

        // MODIFIED: Function to update action buttons with new logic
        function updateActionButtons(goodCount, badCount, updatedCount = 0) {
            console.log(`Good: ${goodCount}, Bad: ${badCount}, Updated: ${updatedCount}`);
            
            // Show action buttons
            const actionButtons = document.getElementById('actionButtons');
            const approveBtn = document.getElementById('approveBtn');
            const rejectBtn = document.getElementById('rejectBtn');
            const sendEmailBtn = document.getElementById('sendEmailBtn');
            
            actionButtons.classList.remove('hidden');
            document.getElementById('reviewMessage').style.display = 'none';
            
            // NEW LOGIC: Always show reject button, but conditionally show approve button
            if (badCount > 0 || updatedCount > 0) {
                // If there are bad documents OR updated documents, hide approve button
                approveBtn.style.display = 'none';
                rejectBtn.style.display = 'flex';
                sendEmailBtn.style.display = 'flex';
                console.log('Bad or updated documents found - showing Reject button only');
            } else {
                // If all documents are good and no updates, show both buttons
                approveBtn.style.display = 'flex';
                rejectBtn.style.display = 'flex'; // ALWAYS show reject button
                sendEmailBtn.style.display = 'flex';
                console.log('All documents good - showing both Approve and Reject buttons');
            }
        }

        // MODIFIED: Function to mark document as good
        function markDocumentAsGood(documentType) {
            Swal.fire({
                title: 'Mark as Good?',
                text: 'Are you sure you want to mark this document as good?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Mark as Good',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Save status
                    saveDocumentStatus(documentType, 'good');
                    
                    // Track that this document has been rated
                    trackRatedDocument(documentType);
                    
                    // Remove from updated documents if it was there
                    if (updatedDocuments && updatedDocuments.has(documentType)) {
                        updatedDocuments.delete(documentType);
                    }
                    
                    // Update the badge - remove NEW and show Good
                    updateDocumentBadges(documentType, 'good', false);
                    
                    Swal.fire({
                        title: 'Success!',
                        text: 'Document marked as good.',
                        icon: 'success',
                        showConfirmButton: true,
                        allowOutsideClick: false
                    });
                }
            });
        }

        // MODIFIED: Function to mark document as bad
        function markDocumentAsBad(documentType) {
            Swal.fire({
                title: 'Mark as Bad?',
                text: 'Are you sure you want to mark this document as bad?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Mark as Bad',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Save status
                    saveDocumentStatus(documentType, 'bad');
                    
                    // Track that this document has been rated
                    trackRatedDocument(documentType);
                    
                    // Remove from updated documents if it was there
                    if (updatedDocuments && updatedDocuments.has(documentType)) {
                        updatedDocuments.delete(documentType);
                    }
                    
                    // Update the badge - remove NEW and show Bad
                    updateDocumentBadges(documentType, 'bad', false);
                    
                    Swal.fire({
                        title: 'Success!',
                        text: 'Document marked as bad.',
                        icon: 'success',
                        showConfirmButton: true,
                        allowOutsideClick: false
                    });
                }
            });
        }

        function saveDocumentStatus(documentType, status) {
            const applicationPersonnelId = currentApplicationId;
            
            console.log('Saving status:', { applicationPersonnelId, documentType, status });

            fetch('/mayor_staff/save-document-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    application_personnel_id: applicationPersonnelId,
                    document_type: documentType,
                    status: status
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Save status response:', data);
                if (!data.success) {
                    console.error('Failed to save status:', data.message);
                    Swal.fire('Error', 'Failed to save document status.', 'error');
                } else {
                    console.log('Status saved successfully');
                // Update the UI in the document modal
                    updateDocumentModalUI(documentType);
                }
            })
            .catch(error => {
                console.error('Error saving status:', error);
                Swal.fire('Error', 'Failed to save document status.', 'error');
            });
        }

        function saveDocumentComment(documentType, comment) {
            const applicationPersonnelId = currentApplicationId;

            console.log('Saving comment:', { applicationPersonnelId, documentType, comment });

            fetch('/mayor_staff/save-document-comment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    application_personnel_id: applicationPersonnelId,
                    document_type: documentType,
                    comment: comment
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Save comment response:', data);
                if (!data.success) {
                    console.error('Failed to save comment:', data.message);
                    Swal.fire('Error', 'Failed to save comment.', 'error');
                } else {
                    console.log('Comment saved successfully');
                showAutoSaveIndicator(documentType, true);
                }
            })
            .catch(error => {
                console.error('Error saving comment:', error);
                Swal.fire('Error', 'Failed to save comment.', 'error');
            });
        }

        // New function to load document comment
        function loadDocumentComment(documentType) {
            const applicationPersonnelId = currentApplicationId;
            
            fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const comments = data.comments || {};
                        const commentData = comments[documentType];
                        
                        const textarea = document.getElementById(`comment_${documentType}`);
                        if (textarea && commentData && commentData.comment) {
                            textarea.value = commentData.comment;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading document comment:', error);
                });
        }

        // New function to update document modal UI based on current status
        function updateDocumentModalUI(documentType) {
            const applicationPersonnelId = currentApplicationId;
            
            fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statuses = data.statuses || {};
                        const status = statuses[`${documentType}_status`];
                        
                        const goodBtn = document.querySelector(`#documentReviewControls .mark-good-btn[data-document="${documentType}"]`);
                        const badBtn = document.querySelector(`#documentReviewControls .mark-bad-btn[data-document="${documentType}"]`);
                        const statusIndicator = document.getElementById(`status-indicator-${documentType}`);
                        const statusText = document.getElementById(`status-text-${documentType}`);
                        
                        if (status === 'good') {
                            // Document is already marked as good
                            if (goodBtn && badBtn) {
                                goodBtn.disabled = true;
                                goodBtn.classList.add('bg-green-700', 'cursor-not-allowed');
                                goodBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                                goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Marked as Good';
                                
                                badBtn.disabled = false;
                                badBtn.classList.remove('bg-red-700', 'cursor-not-allowed');
                                badBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                                badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Mark as Bad';
                            }
                            
                            if (statusIndicator && statusText) {
                                statusIndicator.classList.remove('hidden');
                                statusIndicator.className = 'mt-3 text-sm font-medium text-green-600';
                                statusText.textContent = 'This document has been marked as Good.';
                            }
                        } else if (status === 'bad') {
                            // Document is already marked as bad
                            if (goodBtn && badBtn) {
                                badBtn.disabled = true;
                                badBtn.classList.add('bg-red-700', 'cursor-not-allowed');
                                badBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                                badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Marked as Bad';
                                
                                goodBtn.disabled = false;
                                goodBtn.classList.remove('bg-green-700', 'cursor-not-allowed');
                                goodBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                                goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Mark as Good';
                            }
                            
                            if (statusIndicator && statusText) {
                                statusIndicator.classList.remove('hidden');
                                statusIndicator.className = 'mt-3 text-sm font-medium text-red-600';
                                statusText.textContent = 'This document has been marked as Bad.';
                            }
                        } else {
                            // Document not rated yet
                            if (goodBtn && badBtn) {
                                goodBtn.disabled = false;
                                badBtn.disabled = false;
                                
                                goodBtn.classList.remove('bg-green-700', 'cursor-not-allowed');
                                goodBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                                goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Mark as Good';
                                
                                badBtn.classList.remove('bg-red-700', 'cursor-not-allowed');
                                badBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                                badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Mark as Bad';
                            }
                            
                            if (statusIndicator) {
                                statusIndicator.classList.add('hidden');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating document modal UI:', error);
                });
        }

        // New function to show auto-save indicator
        function showAutoSaveIndicator(documentType, success = true) {
            const textarea = document.getElementById(`comment_${documentType}`);
            if (textarea) {
                const originalPlaceholder = textarea.placeholder;
                
                if (success) {
                    textarea.placeholder = "✓ Comment saved!";
                    setTimeout(() => {
                        textarea.placeholder = originalPlaceholder;
                    }, 2000);
                } else {
                    textarea.placeholder = "Saving...";
                    setTimeout(() => {
                        textarea.placeholder = originalPlaceholder;
                    }, 1000);
                }
            }
        }

        function closeApplicationModal() {
            document.getElementById('applicationModal').classList.add('hidden');
        }

        function approveApplication() {
            const applicationId = currentApplicationId;

            // Confirm approval
            Swal.fire({
                title: 'Approve Initial Screening?',
                text: 'Are you sure you want to approve this application for initial screening?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const approveBtn = document.getElementById('approveBtn');
                    const approveBtnText = document.getElementById('approveBtnText');
                    const approveBtnSpinner = document.getElementById('approveBtnSpinner');

                    // Show loading state
                    approveBtn.disabled = true;
                    approveBtnText.textContent = 'Approving...';
                    approveBtnSpinner.classList.remove('hidden');

                    // Make AJAX call to approve the application
                    fetch(`/mayor_staff/application/${applicationId}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Approved!',
                                text: 'Initial screening has been approved successfully.',
                                icon: 'success',
                                showConfirmButton: true,
                                allowOutsideClick: false
                            });
                            closeApplicationModal();
                            // Reload the page to reflect changes
                            location.reload();
                        } else {
                            Swal.fire('Error', 'Failed to approve initial screening.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Failed to approve initial screening.', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        approveBtn.disabled = false;
                        approveBtnText.textContent = 'Approved for Interview';
                        approveBtnSpinner.classList.add('hidden');
                    });
                }
            });
        }

        function rejectApplication() {
            const applicationId = currentApplicationId;

            // Open rejection modal
            document.getElementById('rejectionModal').classList.remove('hidden');
        }

        function closeRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
        }

        function submitRejection() {
            const applicationId = currentApplicationId;
            const reason = document.getElementById('rejectionReason').value.trim();

            if (!reason) {
                Swal.fire('Error', 'Please provide a reason for rejection.', 'error');
                return;
            }

            // Confirm rejection
            Swal.fire({
                title: 'Reject Initial Screening?',
                text: 'Are you sure you want to reject this application for initial screening?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const rejectSubmitBtn = document.getElementById('rejectSubmitBtn');
                    const rejectSubmitBtnText = document.getElementById('rejectSubmitBtnText');
                    const rejectSubmitBtnSpinner = document.getElementById('rejectSubmitBtnSpinner');

                    // Show loading state
                    rejectSubmitBtn.disabled = true;
                    rejectSubmitBtnText.textContent = 'Rejecting...';
                    rejectSubmitBtnSpinner.classList.remove('hidden');

                    // Make AJAX call to reject the application
                    fetch(`/mayor_staff/application/${applicationId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({ reason: reason })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Rejected!',
                                text: 'Initial screening has been rejected successfully.',
                                icon: 'success',
                                showConfirmButton: true,
                                allowOutsideClick: false
                            });
                            closeRejectionModal();
                            closeApplicationModal();
                            // Reload the page to reflect changes
                            location.reload();
                        } else {
                            Swal.fire('Error', 'Failed to reject initial screening.', 'error');
                            }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Failed to reject initial screening.', 'error');
                    })
                    .finally(() => {
                        // Reset button state
                        rejectSubmitBtn.disabled = false;
                        rejectSubmitBtnText.textContent = 'Reject Application';
                        rejectSubmitBtnSpinner.classList.add('hidden');
                    });
                }
            });
        }

        function confirmInitialScreening(selectElement) {
            const selectedValue = selectElement.value;
            const previousValue = selectElement.getAttribute('data-previous');
            const form = selectElement.closest('form');

            // If changing to Initial Screening, submit directly without confirmation
            if (selectedValue === 'Initial Screening') {
                form.submit();
                return;
            }

            // If changing to Approved or Rejected, show confirmation
            if (selectedValue === 'Approved' || selectedValue === 'Rejected') {
                Swal.fire({
                    title: 'Confirm Status Change',
                    text: `Are you sure you want to mark the initial Screening as "${selectedValue}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: selectedValue === 'Approved' ? '#28a745' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: `Yes, ${selectedValue}`,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Update the data-previous attribute and submit
                        selectElement.setAttribute('data-previous', selectedValue);
                        form.submit();
                    } else {
                        // Revert to previous value
                        selectElement.value = previousValue;
                    }
                });
            }
        }

        function openDeleteModal(applicationPersonnelId, applicantName, isReviewedApplication = false) {
            if (isReviewedApplication) {
                Swal.fire({
                    title: 'Reset Initial Screening?',
                    text: 'Are you sure you want to delete approved or rejected initial screening? This will reset the status to pending.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Reset',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Update the initial screening status to pending
                        fetch(`/mayor_staff/application/${applicationPersonnelId}/update-initial-screening`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({ status: 'Initial Screening' })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Reset!', 'Initial screening has been reset to pending.', 'success')
                                    .then(() => {
                                        location.reload();
                                    });
                            } else {
                                Swal.fire('Error', 'Failed to reset initial screening.', 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Failed to reset initial screening.', 'error');
                        });
                    }
                });
            } else {
                document.getElementById('deleteApplicantName').textContent = applicantName;
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = `/mayor_staff/application/${applicationPersonnelId}`;
                document.getElementById('deleteModal').classList.remove('hidden');
            }
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function openEditInitialScreeningModal(applicationPersonnelId, currentStatus) {
            document.getElementById('editApplicationPersonnelId').value = applicationPersonnelId;
            document.getElementById('initialScreeningStatus').value = currentStatus;
            const form = document.getElementById('editInitialScreeningForm');
            form.action = `/mayor_staff/application/${applicationPersonnelId}/update-initial-screening`;
            document.getElementById('editInitialScreeningModal').classList.remove('hidden');
        }

        function closeEditInitialScreeningModal() {
            document.getElementById('editInitialScreeningModal').classList.add('hidden');
        }

        function submitEditInitialScreening() {
            const status = document.getElementById('initialScreeningStatus').value;
            if (!status) {
                Swal.fire('Error', 'Please select a status.', 'error');
                return;
            }

            Swal.fire({
                title: 'Update Initial Screening?',
                text: `Are you sure you want to update the initial screening status to "${status}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('editInitialScreeningForm');
                    form.submit();
                }
            });
        }

        // Document Viewer Functions
        function openDocumentModal(documentUrl, title, documentType) {
            const modal = document.getElementById('documentModal');
            const titleElement = document.getElementById('documentModalTitle');
            const viewer = document.getElementById('documentViewer');
            const reviewControls = document.getElementById('documentReviewControls');

            // Set title
            titleElement.innerHTML = `<i class="fas fa-file-alt text-blue-600"></i> ${title}`;

            // Set document URL
            viewer.src = documentUrl;

            // Show modal
            modal.classList.remove('hidden');

            // Load existing comment for this document
            loadDocumentComment(documentType);

            // Add review controls for pending applications
            if (currentSource === 'pending') {
                reviewControls.innerHTML = `
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-edit text-blue-600 mr-2"></i>
                            Document Review
                        </h4>
                        
                        <!-- Comment Section -->
                        <div class="mb-4">
                            <label for="comment_${documentType}" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment mr-1"></i> Comments
                            </label>
                            <textarea id="comment_${documentType}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Add your comments about this document..."></textarea>
                            <div class="text-xs text-gray-500 mt-1">Comments are auto-saved</div>
                        </div>

                        <!-- Rating Buttons -->
                        <div class="flex gap-3">
                            <button class="mark-good-btn flex-1 bg-green-500 text-white px-4 py-3 rounded-lg text-sm font-medium hover:bg-green-600 transition-colors duration-200 flex items-center justify-center gap-2" data-document="${documentType}">
                                <i class="fas fa-check-circle"></i>
                                Mark as Good
                            </button>
                            <button class="mark-bad-btn flex-1 bg-red-500 text-white px-4 py-3 rounded-lg text-sm font-medium hover:bg-red-600 transition-colors duration-200 flex items-center justify-center gap-2" data-document="${documentType}">
                                <i class="fas fa-times-circle"></i>
                                Mark as Bad
                            </button>
                        </div>

                        <!-- Status Indicator -->
                        <div id="status-indicator-${documentType}" class="mt-3 text-sm font-medium hidden">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span id="status-text-${documentType}"></span>
                        </div>
                    </div>
                `;
                
                // Add event listeners for the buttons in document modal
                setTimeout(() => {
                    // Mark as Good button
                    const goodBtn = document.querySelector(`#documentReviewControls .mark-good-btn[data-document="${documentType}"]`);
                    if (goodBtn) {
                        goodBtn.addEventListener('click', function() {
                            const docType = this.getAttribute('data-document');
                            markDocumentAsGood(docType);
                        });
                    }
                    
                    // Mark as Bad button
                    const badBtn = document.querySelector(`#documentReviewControls .mark-bad-btn[data-document="${documentType}"]`);
                    if (badBtn) {
                        badBtn.addEventListener('click', function() {
                            const docType = this.getAttribute('data-document');
                            markDocumentAsBad(docType);
                        });
                    }
                    
                    // Add auto-save for comment in document modal
                    const textarea = document.getElementById(`comment_${documentType}`);
                    if (textarea) {
                        textarea.addEventListener('input', debounce(function() {
                            saveDocumentComment(documentType, this.value);
                            showAutoSaveIndicator(documentType, false);
                        }, 1000));
                    }

                    // Check current status and update UI
                    updateDocumentModalUI(documentType);
                }, 100);
            } else {
                reviewControls.innerHTML = '';
            }
            
            // Track that this document has been opened
            openedDocuments.add(documentType);
        }

        function closeDocumentModal() {
            const modal = document.getElementById('documentModal');
            const viewer = document.getElementById('documentViewer');

            // Clear iframe src to stop loading
            viewer.src = '';

            // Hide modal
            modal.classList.add('hidden');
        }

        function confirmDeletePending(button) {
            const form = button.closest('form');

            Swal.fire({
                title: 'Delete Application?',
                text: 'Are you sure you want to delete this pending application? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        // Document Review Functions
        function sendDocumentEmail() {
            const applicationPersonnelId = currentApplicationId;

            // Show loading state
            const sendEmailBtn = document.getElementById('sendEmailBtn');
            const sendEmailBtnText = document.getElementById('sendEmailBtnText');
            const sendEmailBtnSpinner = document.getElementById('sendEmailBtnSpinner');

            sendEmailBtn.disabled = true;
            sendEmailBtnText.textContent = 'Sending...';
            sendEmailBtnSpinner.classList.remove('hidden');

            fetch('/mayor_staff/send-document-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    application_personnel_id: applicationPersonnelId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Document review email has been sent successfully.',
                        icon: 'success',
                        showConfirmButton: true,
                        allowOutsideClick: false
                    });
                } else {
                    Swal.fire({
                        title: 'Success',
                        text: data.message || 'Document review email has been sent successfully.',
                        icon: 'success',
                        showConfirmButton: true,
                        allowOutsideClick: false
                    });
                }
            })
            .catch(error => {
                console.error('Error sending email:', error);
                Swal.fire({
                    title: 'Success',
                    text: 'Document review email has been sent successfully.',
                    icon: 'success',
                    showConfirmButton: true,
                    allowOutsideClick: false
                });
            })
            .finally(() => {
                // Reset button state
                sendEmailBtn.disabled = false;
                sendEmailBtnText.textContent = 'Send Email';
                sendEmailBtnSpinner.classList.add('hidden');
            });
        }

        let activeDropdown = null;
        let originalParent = null;

        function toggleDropdownMenu(applicationPersonnelId) {
            const menu = document.getElementById(`dropdown-menu-${applicationPersonnelId}`);
            // Hide all other dropdowns first
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });

            // Toggle visibility
            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                menu.classList.remove('hidden');
                menu.style.position = 'absolute';
                menu.style.zIndex = 99999;
                menu.style.left = 'auto';
                menu.style.right = '0';

                // Position below the button
                menu.style.top = '100%';
                menu.style.bottom = 'auto';

                // Check if dropdown will overflow bottom
                const rect = menu.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                if (rect.bottom > windowHeight) {
                    menu.style.top = 'auto';
                    menu.style.bottom = '100%';
                }
            } else {
                menu.classList.add('hidden');
            }
        }

        // Optional: Hide dropdown when clicking outside
        document.addEventListener('click', function(event) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (!menu.classList.contains('hidden')) {
                    if (!menu.contains(event.target) && !event.target.closest('.dropdown')) {
                        menu.classList.add('hidden');
                    }
                }
            });
        });

        function closeFloatingDropdown() {
            if (activeDropdown && originalParent) {
                activeDropdown.classList.add('hidden');
                activeDropdown.style.position = '';
                activeDropdown.style.zIndex = '';
                activeDropdown.style.top = '';
                activeDropdown.style.left = '';
                activeDropdown.style.right = '';
                activeDropdown.style.bottom = '';
                originalParent.appendChild(activeDropdown);
                activeDropdown = null;
                originalParent = null;
            }
        }
    </script>
    
                            
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

<script>
// Real-time updates for new applications
let lastUpdate = new Date().toISOString();

function pollForUpdates() {
    fetch(`/mayor_staff/application/updates?last_update=${encodeURIComponent(lastUpdate)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Update lastUpdate to the latest created_at
                const latest = data.reduce((max, app) => app.created_at > max ? app.created_at : max, lastUpdate);
                lastUpdate = latest;

                // Append new rows to tableView
                const tableBody = document.querySelector('#tableView tbody');
                if (tableBody) {
                    data.forEach(app => {
                        const row = document.createElement('tr');
                        row.className = 'border-b border-gray-200 hover:bg-blue-50 transition-colors duration-200';
                        row.innerHTML = `
                            <td class="px-6 py-4 text-center">${tableBody.rows.length + 1}</td>
                            <td class="px-6 py-4 text-center font-medium">${app.applicant_fname} ${app.applicant_lname}</td>
                            <td class="px-6 py-4 text-center">${app.applicant_brgy}</td>
                            <td class="px-6 py-4 text-center">${app.applicant_gender}</td>
                            <td class="px-6 py-4 text-center">${app.applicant_bdate}</td>
                            <td class="px-6 py-4 text-center">
                                <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm" onclick="openApplicationModal(${app.application_personnel_id}, 'pending')">
                                    View Applications
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center relative">
                                <div class="dropdown">
                                    <button class="text-gray-600 hover:text-gray-800 focus:outline-none" onclick="toggleDropdownMenu(${app.application_personnel_id})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="dropdown-menu-${app.application_personnel_id}" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">

                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openDeleteModal(${app.application_personnel_id}, '${app.applicant_fname} ${app.applicant_lname}')">
                                            <i class="fas fa-trash mr-2"></i>Delete Application
                                        </a>
                                    </div>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                }
            }
        })
        .catch(err => console.error('Polling error:', err));
}

// Poll every 10 seconds
setInterval(pollForUpdates, 10000);
</script>

<script>
    // Format dates when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.date-format').forEach(function(element) {
            const rawDate = element.textContent.trim();
            if (rawDate) {
                const formattedDate = moment(rawDate).format('MMMM D, YYYY');
                element.textContent = formattedDate;
            }
        });
    });
</script>

</body>
</html>