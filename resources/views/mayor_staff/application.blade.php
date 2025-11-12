<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/application.css') }}" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

</head>
<style>
/* Document Viewer Modal Styles */
.document-modal-content {
    max-height: 80vh;
    overflow-y: auto;
}
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding: 1rem;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.pagination-info {
    color: #6b7280;
    font-size: 0.875rem;
}

.pagination-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: white;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
    background-color: #f9fafb;
    border-color: #9ca3af;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-page-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0 1rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.pagination-page-input {
    width: 3.5rem;
    padding: 0.25rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    text-align: center;
}

.pagination-page-input:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
}

.document-viewer-container {
    height: calc(100vh - 300px); /* A4 height equivalent */
    min-height: 800px; /* Minimum A4 height */
    max-height: 900px; /* Maximum A4 height */
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    overflow: hidden;
    background-color: #f9fafb;
}

.document-viewer {
    width: 100%;
    height: 100%;
    border: none;
    background-color: white;
}

/* Review Controls - Maintain current button styles */
#documentReviewControls {
    margin-top: 1rem;
}

#documentReviewControls .mark-good-btn,
#documentReviewControls .mark-bad-btn {
    flex: 1;
    background-color: #10b981; /* Green for good */
    color: white;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
}

#documentReviewControls .mark-bad-btn {
    background-color: #ef4444; /* Red for bad */
}

#documentReviewControls .mark-good-btn:hover {
    background-color: #059669;
}

#documentReviewControls .mark-bad-btn:hover {
    background-color: #dc2626;
}

#documentReviewControls .mark-good-btn:disabled,
#documentReviewControls .mark-bad-btn:disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .document-viewer-container {
        height: calc(100vh - 250px);
        min-height: 600px;
        max-height: 700px;
    }
    
    #documentReviewControls .flex {
        flex-direction: column;
       }
}
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.badge-updated {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #8b5cf6;
    color: white;
    border-radius: 10px;
    padding: 2px 30px;
    font-size: 10px;
    font-weight: bold;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
</style>
<body class="bg-gray-50">

    <!-- Loading Spinner Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-container">
            <div class="spinner">
                <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
            </div>
            <div class="text-line">Loading...</div>
        </div>
    </div>

    <!-- Header -->
<header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
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
    <div class="sidebar-fixed w-72 bg-white shadow-md flex flex-col transition-all duration-300">
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

                    <!-- Dropdown Menul -->
                    <ul id="scholarMenu" class="ml-10 mt-2 space-y-2 hidden">
                        <li>
                            <a href="/mayor_staff/application" class="flex items-center p-2 rounded-lg text-white bg-violet-600">
                            <i class="bx bx-search-alt mr-2 text-white-700"></i> Review Applications
                            </a>
                        </li>
                        <li>
                            <a href="/mayor_staff/status" class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                            <i class="bx bx-check-circle mr-2 text-white-700"></i> Scholarship Approval
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
                <div class="p-10 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-3xl font-bold text-gray-800">Review Applicants Application</h5>
                </div>

                    <div class="flex justify-start items-center mb-6 gap-4">
                                    <!-- Tab Switch -->
                        <div class="flex gap-2">
                            <div id="pendingTab" class="tab active" onclick="showTable()">Pending Review</div>
                            <div id="reviewedTab" class="tab" onclick="showList()">Reviewed Applications</div>
                            </div>
                        </div>
            <!-- ✅ Table View (Applicants without remarks) -->
            <div id="tableView">
<!-- Search and Filter Section for Table View -->
<div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
    <div class="flex gap-4 items-end">
        <!-- Left side container -->
        <div class="flex gap-4">
            <!-- Search by Name -->
            <div>
                <label for="searchInputTable" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                <div class="relative">
<input type="text" id="searchInputTable" placeholder="Enter applicant name..."
    class="w-80 px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 bg-white outline-none">
<button onclick="clearFiltersTable()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                </div>
            </div>

            <!-- Filter by Barangay -->
            <div>
                <label for="barangaySelectTable" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                <select id="barangaySelectTable" onchange="filterRows('table')"
                    class="w-64 px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 bg-white appearance-none outline-none"
                    style="background-image: url('data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3e%3cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27m6 8 4 4 4-4%27/%3e%3c/svg%3e'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $brgy)
                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                    @endforeach
                </select>
            </div>
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
                                <button type="button"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm"
                                    onclick="openApplicationModal({{ $app->application_personnel_id }}, 'pending')">
                                    Review Applications
                                </button>
                            </td>

<td class="px-6 py-4 text-center">
    <!-- I-remove ang inline form at palitan ng button na may confirmDelete function -->
    <button type="button" 
            onclick="confirmDeletePending({{ $app->application_personnel_id }}, '{{ $app->applicant_fname }} {{ $app->applicant_lname }}')" 
            class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 text-sm font-medium transition-colors duration-200 shadow-sm">
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
            <div class="mt-4">
            <!-- Pagination for Table View -->
<div class="pagination-container" id="tablePagination"></div>
            </div>
        </div>

            <!-- ✅ List View (Approved and Rejected applications) -->
    <div id="listView" class="hidden overflow-x-auto">
<!-- Search and Filter Section for List View -->
<div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
    <div class="flex gap-4 items-end">
        <!-- Left side container -->
        <div class="flex gap-4">
            <!-- Search by Name -->
            <div>
                <label for="searchInputList" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                <div class="relative">
                 <input type="text" id="searchInputList" placeholder="Enter applicant name..."
    class="w-80 px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 bg-white outline-none">
<button onclick="clearFiltersList()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                </div>
            </div>

            <!-- Filter by Barangay -->
            <div>
                <label for="barangaySelectList" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                <select id="barangaySelectList" onchange="filterRows('list')"
                    class="w-64 px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 bg-white appearance-none outline-none"
                    style="background-image: url('data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3e%3cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27m6 8 4 4 4-4%27/%3e%3c/svg%3e'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $brgy)
                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                    @endforeach
                </select>
            </div>
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
    <div class="relative inline-block">
        <button type="button"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm relative"
            onclick="openApplicationModal({{ $app->application_personnel_id }}, 'reviewed')"
            id="reviewBtn-{{ $app->application_personnel_id }}">
            Review Requirements
        </button>
        <!-- Updated Badge - will be shown/hidden via JavaScript -->
        <span id="updatedBadge-{{ $app->application_personnel_id }}" 
              class="badge-updated hidden">Updated</span>
    </div>
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
                            <div class="mt-4">
                            <!-- Pagination for List View -->
<div class="pagination-container" id="listPagination"></div>
    </div>
        </div>
        </div>
        </div>

        <!-- FIXED: Application Modal with proper z-index -->
        <div id="applicationModal" class="modal-overlay hidden">
            <div class="modal-content">
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

        <!-- FIXED: Delete Confirmation Modal -->
        <div id="deleteModal" class="modal-overlay hidden">
            <div class="modal-content max-w-md">
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
<!-- In your delete modal -->
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

<!-- FIXED: Rejection Modal -->
<div id="rejectionModal" class="modal-overlay hidden">
    <div class="modal-content max-w-2xl">
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

        <!-- FIXED: Edit Initial Screening Modal -->
        <div id="editInitialScreeningModal" class="modal-overlay hidden">
            <div class="modal-content max-w-2xl">
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

        <!-- FIXED: Document Viewer Modal -->
        <div id="documentModal" class="modal-overlay hidden">
            <div class="modal-content max-w-6xl">
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

            // Tab switching functions
            function showTable() {
                document.getElementById('tableView').classList.remove('hidden');
                document.getElementById('listView').classList.add('hidden');
                document.getElementById('pendingTab').classList.add('active');
                document.getElementById('reviewedTab').classList.remove('active');
                localStorage.setItem('viewMode', 'table');
            }

            function showList() {
                document.getElementById('tableView').classList.add('hidden');
                document.getElementById('listView').classList.remove('hidden');
                document.getElementById('pendingTab').classList.remove('active');
                document.getElementById('reviewedTab').classList.add('active');
                localStorage.setItem('viewMode', 'list');
            }

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
        <div class="modal-footer">
            <button id="sendEmailBtn" onclick="sendDocumentEmail()" class="btn btn-primary">
                <i class="fas fa-envelope"></i>
                <span id="sendEmailBtnText">Send Email</span>
                <div id="sendEmailBtnSpinner" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>
        </div>
    `;
}
                document.getElementById('applicationModal').classList.remove('hidden');

                // Load existing comments and statuses
                loadDocumentComments(applicationPersonnelId, source);
                
                // NEW: Check for document updates to show NEW badges
                trackDocumentUpdates(applicationPersonnelId);

                // Ensure send email button is hidden when modal is opened from List view (or any non-pending source)
                // Small timeout to ensure DOM elements rendered before toggling
                setTimeout(() => {
                    const sendEmailBtn = document.getElementById('sendEmailBtn');
                    if (sendEmailBtn) {
                        if (currentSource !== 'pending') {
                            sendEmailBtn.style.display = 'none';
                        } else {
                            sendEmailBtn.style.display = '';
                        }
                    }
                }, 50);
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
    } else if (status === 'New') {
        badge.classList.add('badge-updated');
        badge.innerHTML = 'Updated';
        icon.classList.add('text-purple-600');
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
        badge.innerHTML = 'Updated';
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
            
            // Also check for documents with 'New' status
            if (data.success && data.statuses) {
                Object.entries(data.statuses).forEach(([key, status]) => {
                    if (key.endsWith('_status') && status === 'New') {
                        const docType = key.replace('_status', '');
                        updateDocumentBadges(docType, 'New', false);
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
                    // Make a single API call to get all statuses
                    const applicationPersonnelId = currentApplicationId;

                    fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const statuses = data.statuses || {};
                                let goodCount = 0;
                                let badCount = 0;
                                let updatedCount = updatedDocuments ? updatedDocuments.size : 0;

                                // Count good and bad documents from the response
                                documentTypes.forEach(docType => {
                                    const status = statuses[`${docType}_status`];
                                    if (status === 'good') {
                                        goodCount++;
                                    } else if (status === 'bad') {
                                        badCount++;
                                    }
                                });

                                console.log(`Final counts - Good: ${goodCount}, Bad: ${badCount}, Updated: ${updatedCount}`);

                                // Update the action buttons based on the counts
                                updateActionButtons(goodCount, badCount, updatedCount);
                            }
                        })
                        .catch(error => {
                            console.error('Error checking document status:', error);
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

                // Show buttons based on document status
                if (goodCount === 5) {
                    // All documents are good - show only approve button
                    approveBtn.style.display = 'flex';
                    rejectBtn.style.display = 'none';
                    sendEmailBtn.style.display = 'none';
                    console.log('All documents good - showing only Approve button');
                } else {
                    // There are bad documents - show reject and send email buttons
                    approveBtn.style.display = 'none';
                    rejectBtn.style.display = 'flex';
                    sendEmailBtn.style.display = 'flex';
                    console.log('Not all documents good - showing Reject and Send Email buttons');
                }
            }

// MODIFIED: Function to mark document as good (WITHOUT confirmation, auto-close document viewer)
function markDocumentAsGood(documentType) {
    // Show loading state immediately
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we save your feedback',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Save status without reason for good documents
    saveDocumentStatus(documentType, 'good', '')
    .then(() => {
        // Track that this document has been rated
        trackRatedDocument(documentType);

        // Remove from updated documents if it was there
        if (updatedDocuments && updatedDocuments.has(documentType)) {
            updatedDocuments.delete(documentType);
        }

        // Update the badge - remove NEW and show Good
        updateDocumentBadges(documentType, 'good', false);

        // Show success message and close document viewer when OK is clicked
        Swal.fire({
            title: 'Success!',
            text: 'Document marked as good.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'swal2-confirm-btn'
            }
        }).then((result) => {
            // Close the document viewer modal when OK is clicked
            if (result.isConfirmed) {
                closeDocumentModal();
                updateDocumentModalUI(documentType);
            }
        });

    })
    .catch(error => {
        console.error('Error saving status:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to save document status. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

// MODIFIED: Function to mark document as good (WITHOUT confirmation, close document viewer only on OK click)
function markDocumentAsGood(documentType) {
    // Show loading state immediately
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we save your feedback',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Save status without reason for good documents
    saveDocumentStatus(documentType, 'good', '')
    .then(() => {
        // Track that this document has been rated
        trackRatedDocument(documentType);

        // Remove from updated documents if it was there
        if (updatedDocuments && updatedDocuments.has(documentType)) {
            updatedDocuments.delete(documentType);
        }

        // Update the badge - remove NEW and show Good
        updateDocumentBadges(documentType, 'good', false);

        // Show success message and close document viewer ONLY when OK is clicked
        Swal.fire({
            title: 'Success!',
            text: 'Document marked as good.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'swal2-confirm-btn'
            }
        }).then((result) => {
            // ONLY close the document viewer modal when OK is clicked
            if (result.isConfirmed) {
                closeDocumentModal();
            }
            // Update UI regardless of OK click
            updateDocumentModalUI(documentType);
        });

    })
    .catch(error => {
        console.error('Error saving status:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to save document status. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

// MODIFIED: Function to mark document as bad with reason input (WITH confirmation and reason, close document viewer on OK click)
function markDocumentAsBad(documentType) {
    Swal.fire({
        title: 'Mark as Bad?',
        text: 'Please provide the reason why this document is marked as bad:',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Reason for marking as bad',
        inputPlaceholder: 'Enter the reason why this document needs to be updated...',
        inputAttributes: {
            'aria-label': 'Enter the reason why this document needs to be updated',
            'rows': 3
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Mark as Bad',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'Please provide a reason for marking this document as bad';
            }
            if (value.length < 10) {
                return 'Please provide a more detailed reason (at least 10 characters)';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const reason = result.value;
            
            // Show loading state
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save your feedback',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Save status with reason for bad documents
            saveDocumentStatus(documentType, 'bad', reason)
            .then(() => {
                // Track that this document has been rated
                trackRatedDocument(documentType);
                
                // Remove from updated documents if it was there
                if (updatedDocuments && updatedDocuments.has(documentType)) {
                    updatedDocuments.delete(documentType);
                }
                
                // Update the badge - remove NEW and show Bad
                updateDocumentBadges(documentType, 'bad', false);
                
                // Show success message and close document viewer when OK is clicked
                Swal.fire({
                    title: 'Success!',
                    text: 'Document marked as bad with reason saved.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    // Close the document viewer modal when OK is clicked
                    if (result.isConfirmed) {
                        closeDocumentModal();
                    }
                    // Update UI regardless of OK click
                    updateDocumentModalUI(documentType);
                });
            })
            .catch(error => {
                console.error('Error saving status:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to save document status. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}
function saveDocumentStatus(documentType, status, reason = '') {
    const applicationPersonnelId = currentApplicationId;

    console.log('Saving status:', { applicationPersonnelId, documentType, status, reason });

    return fetch('/mayor_staff/save-document-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            application_personnel_id: applicationPersonnelId,
            document_type: documentType,
            status: status,
            reason: reason
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Save status response:', data);
        if (!data.success) {
            throw new Error(data.message || 'Failed to save status');
        }
        console.log('Status saved successfully');
        // Update the UI in the document modal
        updateDocumentModalUI(documentType);
        return data;
    })
    .catch(error => {
        console.error('Error in saveDocumentStatus:', error);
        throw error;
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
                } else if (status === 'New') {
                    // Document has been updated (from bad to New)
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
                    
                    if (statusIndicator && statusText) {
                        statusIndicator.classList.remove('hidden');
                        statusIndicator.className = 'mt-3 text-sm font-medium text-purple-600';
                        statusText.textContent = 'This document has been updated and needs review.';
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
                    }).then(() => {
                        closeApplicationModal();
                        
                        // ✅ REMOVE FROM TABLE WITHOUT RELOAD
                        removeApplicationFromTable(applicationId);
                    });
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

function removeApplicationFromTable(applicationId) {
    // Hanapin at tanggalin ang row sa pending table
    const rows = document.querySelectorAll('#tableView tbody tr');
    let foundRow = null;
    
    rows.forEach(row => {
        // Skip header row or rows without enough cells
        if (!row.cells || row.cells.length < 7) return;
        
        const viewButton = row.querySelector('button[onclick*="openApplicationModal"]');
        if (viewButton && viewButton.getAttribute('onclick').includes(applicationId.toString())) {
            foundRow = row;
        }
    });
    
    if (foundRow) {
        // Animate removal
        foundRow.style.transition = 'all 0.3s ease';
        foundRow.style.opacity = '0';
        foundRow.style.transform = 'translateX(-100%)';
        
        setTimeout(() => {
            foundRow.remove();
            
            // Update ang row numbers at pagination
            updateRowNumbers();
            
            // Show success message if no more rows
            if (document.querySelectorAll('#tableView tbody tr').length === 0) {
                showNoApplicationsMessage();
            }
        }, 300);
    } else {
        console.warn('Application row not found for ID:', applicationId);
        // Fallback: reload the page
        location.reload();
    }
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('#tableView tbody tr');
    let count = 1;
    
    rows.forEach(row => {
        // Skip if it's a "no data" row
        if (row.querySelector('td[colspan]')) return;
        
        const firstCell = row.cells[0];
        if (firstCell) {
            firstCell.textContent = count++;
        }
    });
}

function showNoApplicationsMessage() {
    const tableBody = document.querySelector('#tableView tbody');
    if (tableBody && tableBody.querySelectorAll('tr').length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500 bg-gray-50">
                    No pending applications found.
                </td>
            </tr>
        `;
    }
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
                    }).then(() => {
                        // DAGDAG: Isara ang rejection modal at application modal
                        closeRejectionModal();
                        closeApplicationModal();
                        
                        // Remove from table without reload
                        removeApplicationFromTable(applicationId);
                    });
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
function closeRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
    // Clear the rejection reason when closing
    document.getElementById('rejectionReason').value = '';
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
                    }).then(() => {
                        // Close both modals
                        closeRejectionModal();
                        closeApplicationModal();
                        
                        // Remove from table without reload
                        removeApplicationFromTable(applicationId);
                    });
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
function closeApplicationModal() {
    document.getElementById('applicationModal').classList.add('hidden');
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

    // Add review controls for pending applications
    if (currentSource === 'pending') {
        reviewControls.innerHTML = `
            <div class="bg-gray-50 p-4 rounded-lg border">
                <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Document Review
                </h4>

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

function confirmDeletePending(applicationPersonnelId, applicantName) {
    Swal.fire({
        title: 'Delete Application?',
        text: `Are you sure you want to delete the application for ${applicantName}? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a form and submit it (traditional way)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/mayor_staff/application/${applicationPersonnelId}`;
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('input[name="_token"]').value;
            form.appendChild(csrfToken);
            
            // Add method spoofing for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
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
                text: 'Document review email with reasons has been sent successfully.',
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

function rejectApplication() {
    // Show the rejection modal instead of directly submitting
    document.getElementById('rejectionModal').classList.remove('hidden');
}
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
        // Format dates when the page loads
// Format dates when the page loads
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.date-format').forEach(function(element) {
        const rawDate = element.textContent.trim();
        if (rawDate) {
            const formattedDate = moment(rawDate).format('MMMM D YYYY'); // Removed the comma
            element.textContent = formattedDate;
        }
    });
});
    </script>
    <script>
    
    </script>
<script src="{{ asset('js/app_spinner.js') }}"></script>
<script src="{{ asset('js/application_paginate.js') }}"></script>
<!-- Add this with your other script includes -->
<script src="{{ asset('js/autorefresh.js') }}"></script>
<script src="{{ asset('js/modalautorefresh.js') }}"></script>
    </body>
    </html>