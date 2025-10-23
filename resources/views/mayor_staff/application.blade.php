<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/mayor_app.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>
<body class="bg-gray-50">

    <div class="dashboard-grid">
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

                <!-- Main Content -->
                <div class="flex flex-1 overflow-hidden">
                    <!-- Sidebar -->
    <div class="w-16 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
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
            <div class="flex-1 main-content-area p-4 md:p-5 text-[16px]">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-3xl font-bold text-gray-800">Review Applicants Application</h5>
                </div>
                    <!-- 🔎 View Switch -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <!-- Tab Switch -->
            <div class="flex gap-2">
                <div class="tab active" onclick="showTable()">Pending Review</div>
                 <div class="tab" onclick="showList()">Reviewed Applications</div>
                </div>
             </div>

            <!-- ✅ Table View (Applicants without remarks) -->
            <div id="tableView">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-200">
                        The list below shows applicants who have submitted applications
                    </h3>
                </div>

                <!-- Search and Filter Section -->
                <div class="mb-6 flex flex-col md:flex-row gap-4 items-center justify-between bg-white p-4 rounded-lg shadow-sm border">
                    <div class="flex flex-col md:flex-row gap-4 w-full">
                        <!-- Search by Name -->
                        <div class="flex-1">
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                            <input type="text" id="searchInput" placeholder="Enter applicant name..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Filter by Barangay -->
                        <div class="flex-1">
                            <label for="barangaySelect" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                            <select id="barangaySelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Barangays</option>
                                @php
                                    $items = is_object($tableApplicants) && method_exists($tableApplicants, 'items') ? $tableApplicants->items() : $tableApplicants;
                                    $uniqueBarangays = collect($items)->pluck('applicant_brgy')->unique()->sort();
                                @endphp
                                @foreach($uniqueBarangays as $barangay)
                                    <option value="{{ $barangay }}">{{ $barangay }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <div class="flex-shrink-0">
                        <button onclick="clearFilters()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </button>
                    </div>
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
                        <th class="px-6 py-4 align-middle text-center"></th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($tableApplicants as $index => $app)
                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-center">{{ ($tableApplicants->currentPage() - 1) * $tableApplicants->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                            <td class="px-6 py-4 text-center">{{ $app->applicant_brgy }}</td>
                            <td class="px-6 py-4 text-center">{{ $app->applicant_gender }}</td>
                            <td class="px-6 py-4 text-center">{{ $app->applicant_bdate }}</td>
                            <td class="px-6 py-4 text-center">
                                <button
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm"
                                    onclick="openApplicationModal({{ $app->application_personnel_id }}, 'pending')">
                                    View Applications
                                </button>
                            </td>

                            <td class="px-6 py-4 text-center relative">
                                <div class="dropdown">
                                    <button class="text-gray-600 hover:text-gray-800 focus:outline-none" onclick="toggleDropdownMenu({{ $app->application_personnel_id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="dropdown-menu-{{ $app->application_personnel_id }}" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openEmailModal({{ $app->application_personnel_id }}, {{ $app->applicant_id }}, '{{ $app->applicant_fname }} {{ $app->applicant_lname }}', '{{ $app->applicant_email }}')">
                                            <i class="fas fa-envelope mr-2"></i>Send Email
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openDeleteModal({{ $app->application_personnel_id }}, '{{ $app->applicant_fname }} {{ $app->applicant_lname }}')">
                                            <i class="fas fa-trash mr-2"></i>Delete Application
                                        </a>
                                    </div>
                                </div>
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
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-700 bg-green-50 p-3 rounded-lg border border-green-200">
            The list below shows applicants who have approved and rejected screening
            </h3>
        </div>

        <!-- Search and Filter Section for Reviewed Applications -->
        <div class="mb-6 flex flex-col md:flex-row gap-4 items-center justify-between bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex flex-col md:flex-row gap-4 w-full">
                <!-- Search by Name -->
                <div class="flex-1">
                    <label for="searchInputList" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                    <input type="text" id="searchInputList" placeholder="Enter applicant name..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Filter by Barangay -->
                <div class="flex-1">
                    <label for="barangaySelectList" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                    <select id="barangaySelectList"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">All Barangays</option>
                        @php
                            $itemsList = is_object($listApplicants) && method_exists($listApplicants, 'items') ? $listApplicants->items() : $listApplicants;
                            $uniqueBarangaysList = collect($itemsList)->pluck('applicant_brgy')->unique()->sort();
                        @endphp
                        @foreach($uniqueBarangaysList as $barangay)
                            <option value="{{ $barangay }}">{{ $barangay }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Clear Filters Button -->
            <div class="flex-shrink-0">
                <button onclick="clearFiltersList()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </button>
            </div>
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
                <th class="px-6 py-4 align-middle text-center"></th>
            
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
                    <td class="px-6 py-4 text-center">{{ $app->applicant_bdate }}</td>
                    <td class="px-6 py-4 text-center">{{ $app->initial_screening }}</td>
                    <td class="px-6 py-4 text-center">
                        <button type="button"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm"
                            onclick="openApplicationModal({{ $app->application_personnel_id }}, 'reviewed')">
                            View Requirements
                        </button>
                    </td>
                    <td class="px-6 py-4 text-center relative">
                        <div class="dropdown">
                            <button class="text-gray-600 hover:text-gray-800 focus:outline-none" onclick="toggleDropdownMenu({{ $app->application_personnel_id }})">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="dropdown-menu-{{ $app->application_personnel_id }}" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openEditInitialScreeningModal({{ $app->application_personnel_id }}, '{{ $app->initial_screening_status ?? "" }}')">
                                    <i class="fas fa-edit mr-2"></i>Edit Initial Screening
                                </a>
                            </div>
                        </div>
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




    <script>
        function showTable() {
            document.getElementById("tableView").classList.remove("hidden");
            document.getElementById("listView").classList.add("hidden");
            document.querySelector('.tab.active').classList.remove('active');
            document.querySelectorAll('.tab')[0].classList.add('active');
            localStorage.setItem("viewMode", "table"); // save preference
        }

        function showList() {
            document.getElementById("listView").classList.remove("hidden");
            document.getElementById("tableView").classList.add("hidden");
            document.querySelector('.tab.active').classList.remove('active');
            document.querySelectorAll('.tab')[1].classList.add('active');
            localStorage.setItem("viewMode", "list"); // save preference
        }

        // ✅ Kapag nag-load ang page, i-apply yung last view
        document.addEventListener("DOMContentLoaded", function() {
            let viewMode = localStorage.getItem("viewMode") || "table"; // default table
            if(viewMode === "list") {
                showList();
            } else {
                showTable();
            }
        });

        // ✅ Application Modal Functions
        const applications = @json($applications);

    function openApplicationModal(applicationPersonnelId, source = 'pending') {
        const contentDiv = document.getElementById('applicationContent');
        contentDiv.innerHTML = '';

        // Store the current application ID globally for approve/reject functions
        window.currentApplicationId = applicationPersonnelId;

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
                            Click one of the documents to view the application
                        </p>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <a href="${foundApp.application_letter}" target="_blank"
        class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-blue-50 transition-all duration-200 hover:shadow-md">
            <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-gray-700 text-center">Application Letter</span>
        </a>
        <a href="${foundApp.cert_of_reg}" target="_blank"
        class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-indigo-50 transition-all duration-200 hover:shadow-md">
            <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-gray-700 text-center">Certificate of Reg.</span>
        </a>
        <a href="${foundApp.grade_slip}" target="_blank"
        class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-green-50 transition-all duration-200 hover:shadow-md">
            <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-gray-700 text-center">Grade Slip</span>
        </a>
        <a href="${foundApp.brgy_indigency}" target="_blank"
        class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-purple-50 transition-all duration-200 hover:shadow-md">
            <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-gray-700 text-center">Barangay Indigency</span>
        </a>
        <a href="${foundApp.student_id}" target="_blank"
        class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-orange-50 transition-all duration-200 hover:shadow-md">
            <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
            <span class="text-sm font-medium text-gray-700 text-center">Student ID</span>
        </a>
    </div>

                </div>
            `;
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
    </div>

                <div id="reviewMessage" class="text-gray-600 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>Please review all 5 documents before making a decision.
                </div>
            `;

            // Track document clicks
            let clickedDocuments = new Set();

            // Add click listeners to document links
            setTimeout(() => {
                const documentLinks = contentDiv.querySelectorAll('a[target="_blank"]');
                documentLinks.forEach((link, index) => {
                    link.addEventListener('click', function() {
                        clickedDocuments.add(index);
                        // Add visual feedback
                        this.classList.add('bg-green-100', 'border-green-300');
                        this.querySelector('i').classList.remove('text-purple-600');
                        this.querySelector('i').classList.add('text-green-600');

                        // Check if all 5 documents are clicked
                        if (clickedDocuments.size === 5) {
                            document.getElementById('actionButtons').classList.remove('hidden');
                            document.getElementById('reviewMessage').style.display = 'none';
                        }
                    });
                });
            }, 100);
        } else {
            footerDiv.innerHTML = '';
        }

        document.getElementById('applicationModal').classList.remove('hidden');
    }

    </script>
    <script>
        function closeApplicationModal() {
            document.getElementById('applicationModal').classList.add('hidden');
        }

        function approveApplication() {
            const applicationId = window.currentApplicationId;

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
                            Swal.fire('Approved!', 'Initial screening has been approved successfully.', 'success');
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
            const applicationId = window.currentApplicationId;

            // Open rejection modal
            document.getElementById('rejectionModal').classList.remove('hidden');
        }

        function closeRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
        }

        function submitRejection() {
            const applicationId = window.currentApplicationId;
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
                            Swal.fire('Rejected!', 'Initial screening has been rejected successfully.', 'success');
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
    </script>

    <!-- Email Modal -->
    <div id="emailModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-3xl h-[600px] overflow-y-auto  rounded-2xl shadow-2xl animate-fadeIn p-6">
        <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-envelope text-blue-600"></i>
            Send Email to Applicant
        </h2>
        <button onclick="closeEmailModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
        </div>
        <form id="emailForm" method="POST" action="/mayor_staff/send-email">
        @csrf
        <input type="hidden" name="application_personnel_id" id="emailApplicationPersonnelId" />
        <div class="mb-4">
            <label for="recipientName" class="block text-gray-700 font-medium mb-1">Recipient</label>
            <input type="text" id="recipientName" class="w-full border rounded px-3 py-2 bg-gray-100" readonly />
        </div>
        <div class="mb-4">
            <label for="recipientEmail" class="block text-gray-700 font-medium mb-1">Email Address</label>
            <input type="email" name="recipient_email" id="recipientEmail" class="w-full border rounded px-3 py-2" required />
        </div>
        <div class="mb-4">
            <label for="emailSubject" class="block text-gray-700 font-medium mb-1">Subject</label>
            <select name="subject" id="emailSubject" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Subject</option>
            <option value="Blurred Application">Blurred Application</option>
            <option value="Can't Access Application">Can't Access Application</option>
            <option value="Both">Both</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Application Issues (check which ones are blurred or can't access)</label>
            <div id="applicationCheckboxes">
            <!-- Checkboxes will be populated by JS -->
            </div>
        </div>
        <div class="mb-4">
            <label for="emailMessage" class="block text-gray-700 font-medium mb-1">Message</label>
            <textarea name="message" id="emailMessage" rows="5" class="w-full border rounded px-3 py-2" required></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeEmailModal()" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">Cancel</button>
            <button type="submit" id="sendEmailBtn" class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition flex items-center gap-2">
            <span id="sendBtnText">Send</span>
            <div id="sendBtnSpinner" class="hidden">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            </button>
        </div>
        </form>
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

    <script>
    function openEmailModal(applicationPersonnelId, applicantId, applicantName, applicantEmail) {
        console.log("openEmailModal called with applicationPersonnelId:", applicationPersonnelId);
        document.getElementById('emailApplicationPersonnelId').value = applicationPersonnelId;
        document.getElementById('recipientName').value = applicantName;
        document.getElementById('recipientEmail').value = applicantEmail || '';
        document.getElementById('emailSubject').value = '';
        document.getElementById('emailMessage').value = 'Please resubmit your application with the correct documents.';

        // Clear previous checkboxes
        const checkboxesDiv = document.getElementById('applicationCheckboxes');
        checkboxesDiv.innerHTML = '';

        console.log("Mapped applicantId:", applicantId);
        console.log("Applications for applicantId:", applications[applicantId]);

        window.currentApplicantId = applicantId;

        if (applicantId && applications[applicantId]) {
        checkboxesDiv.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="flex items-center">
                <input type="checkbox" name="application_issues[]" value="application_letter" id="app_letter" class="mr-2" />
                <label for="app_letter">Application Letter</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="application_issues[]" value="cert_of_reg" id="cert_reg" class="mr-2" />
                <label for="cert_reg">Certificate of Registration</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="application_issues[]" value="grade_slip" id="grade_slip" class="mr-2" />
                <label for="grade_slip">Grade Slip</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="application_issues[]" value="brgy_indigency" id="brgy_indigency" class="mr-2" />
                <label for="brgy_indigency">Barangay Indigency</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="application_issues[]" value="student_id" id="student_id" class="mr-2" />
                <label for="student_id">Student ID</label>
            </div>
            </div>
        `;

        // Add event listener to update message when checkboxes change
        setTimeout(() => {
            const checkboxes = checkboxesDiv.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateEmailMessage);
            });
        }, 100);
        }

        document.getElementById('emailModal').classList.remove('hidden');
    }

    function updateEmailMessage() {
        const checkboxes = document.querySelectorAll('input[name="application_issues[]"]:checked');
        let issues = [];
        let issueFields = [];
        checkboxes.forEach(checkbox => {
        issueFields.push(checkbox.value);
        switch(checkbox.value) {
            case 'application_letter':
            issues.push('Application Letter');
            break;
            case 'cert_of_reg':
            issues.push('Certificate of Registration');
            break;
            case 'grade_slip':
            issues.push('Grade Slip');
            break;
            case 'brgy_indigency':
            issues.push('Barangay Indigency');
            break;
            case 'student_id':
            issues.push('Student ID');
            break;
        }
        });

        let message = 'Please resubmit your application with the correct documents.';
        if (issues.length > 0) {
        message += '\n\nThe following documents have issues and need to be resubmitted:\n' + issues.map(issue => '- ' + issue).join('\n');
        // Append the update link
        const updateLink = window.location.origin + '/scholar/update-application/' + window.currentApplicantId + '?issues=' + issueFields.join(',');
        message += '\n\nUpdate your application here: ' + updateLink;
        }

        document.getElementById('emailMessage').value = message;
    }

    function closeEmailModal() {
        document.getElementById('emailModal').classList.add('hidden');
    }

    function openEditInitialScreeningModal(applicationPersonnelId, currentStatus) {
        document.getElementById('editApplicationPersonnelId').value = applicationPersonnelId;
        document.getElementById('initialScreeningStatus').value = currentStatus;
        document.getElementById('editInitialScreeningModal').classList.remove('hidden');
    }

    function closeEditInitialScreeningModal() {
        document.getElementById('editInitialScreeningModal').classList.add('hidden');
    }

    function submitEditInitialScreening() {
        const form = document.getElementById('editInitialScreeningForm');
        const formData = new FormData(form);

        // Confirm update
        Swal.fire({
            title: 'Update Initial Screening?',
            text: 'Are you sure you want to update the initial screening status?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const applicationPersonnelId = document.getElementById('editApplicationPersonnelId').value;
                fetch(`/mayor_staff/application/${applicationPersonnelId}/edit-initial-screening`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PATCH'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Updated!', 'Initial screening status has been updated successfully.', 'success');
                        closeEditInitialScreeningModal();
                        location.reload();
                    } else {
                        Swal.fire('Error', 'Failed to update initial screening status.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Failed to update initial screening status.', 'error');
                });
            }
        });
    }

    // Add loading state to email form submission
    document.getElementById('emailForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission to handle with AJAX and SweetAlert
        const sendBtn = document.getElementById('sendEmailBtn');
        const sendBtnText = document.getElementById('sendBtnText');
        const sendBtnSpinner = document.getElementById('sendBtnSpinner');
        const form = this;

        // Show loading state immediately
        sendBtn.disabled = true;
        sendBtnText.textContent = 'Sending...';
        sendBtnSpinner.classList.remove('hidden');
        sendBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        sendBtn.classList.add('bg-blue-500', 'cursor-not-allowed');

        // Confirm with SweetAlert
        Swal.fire({
        title: 'Send Email Notification?',
    text: "Do you want to send an email to the applicant",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Send',
    cancelButtonText: 'No',
    allowOutsideClick: false,
    allowEscapeKey: false
        }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with AJAX submission
            const formData = new FormData(form);
            fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
            })
            .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
            })
            .then(data => {
            if (data.success) {
                Swal.fire({
                icon: 'success',
                title: 'Email Sent!',
                text: data.message || 'Email has been sent successfully.',
                timer: 2000,
                showConfirmButton: false
                });
                closeEmailModal();
                // Optionally reload to update any UI
                // location.reload();
            } else {
                Swal.fire('Error', data.message || 'Failed to send email.', 'error');
            }
            })
            .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to send email. Please try again.', 'error');
            })
            .finally(() => {
            // Reset button state
            sendBtn.disabled = false;
            sendBtnText.textContent = 'Send';
            sendBtnSpinner.classList.add('hidden');
            sendBtn.classList.remove('bg-blue-500', 'cursor-not-allowed');
            sendBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            });
        } else {
            // User cancelled, reset button immediately
            sendBtn.disabled = false;
            sendBtnText.textContent = 'Send';
            sendBtnSpinner.classList.add('hidden');
            sendBtn.classList.remove('bg-blue-500', 'cursor-not-allowed');
            sendBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
        });
    });
    </script>

    <script>
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

        function openDeleteModal(applicationPersonnelId, applicantName) {
            document.getElementById('deleteApplicantName').textContent = applicantName;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/mayor_staff/application/${applicationPersonnelId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
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
    <script>


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
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openEmailModal(${app.application_personnel_id}, ${app.applicant_id}, '${app.applicant_fname} ${app.applicant_lname}', '${app.applicant_email}')">
                                            <i class="fas fa-envelope mr-2"></i>Send Email
                                        </a>
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

// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');

    // Function to filter table rows
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedBarangay = barangaySelect.value.toLowerCase();
        const tableBody = document.querySelector('#tableView tbody');
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const nameCell = row.cells[1]; // Name column
            const barangayCell = row.cells[2]; // Barangay column

            if (nameCell && barangayCell) {
                const name = nameCell.textContent.toLowerCase();
                const barangay = barangayCell.textContent.toLowerCase();

                const nameMatch = name.includes(searchTerm);
                const barangayMatch = selectedBarangay === '' || barangay === selectedBarangay;

                if (nameMatch && barangayMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    // Add event listeners
    searchInput.addEventListener('input', filterTable);
    barangaySelect.addEventListener('change', filterTable);
});

// Clear Filters Function for Pending Review
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('barangaySelect').value = '';

    // Show all rows
    const tableBody = document.querySelector('#tableView tbody');
    const rows = tableBody.querySelectorAll('tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}

// Search and Filter Functionality for Reviewed Applications
document.addEventListener('DOMContentLoaded', function() {
    const searchInputList = document.getElementById('searchInputList');
    const barangaySelectList = document.getElementById('barangaySelectList');

    // Function to filter list rows
    function filterList() {
        const searchTerm = searchInputList.value.toLowerCase();
        const selectedBarangay = barangaySelectList.value.toLowerCase();
        const tableBody = document.querySelector('#listView tbody');
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const nameCell = row.cells[1]; // Name column
            const barangayCell = row.cells[2]; // Barangay column

            if (nameCell && barangayCell) {
                const name = nameCell.textContent.toLowerCase();
                const barangay = barangayCell.textContent.toLowerCase();

                const nameMatch = name.includes(searchTerm);
                const barangayMatch = selectedBarangay === '' || barangay === selectedBarangay;

                if (nameMatch && barangayMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    // Add event listeners
    if (searchInputList) searchInputList.addEventListener('input', filterList);
    if (barangaySelectList) barangaySelectList.addEventListener('change', filterList);
});

// Clear Filters Function for Reviewed Applications
function clearFiltersList() {
    document.getElementById('searchInputList').value = '';
    document.getElementById('barangaySelectList').value = '';

    // Show all rows
    const tableBody = document.querySelector('#listView tbody');
    const rows = tableBody.querySelectorAll('tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}
</script>

</body>
</html>
