<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <style>
        .error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 1px #ef4444 !important;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="dashboard-grid">
        <!-- Header -->
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Admin</span>
<div class="relative">
    <!-- 🔔 Bell Icon -->
    <button id="notifBell" class="relative focus:outline-none">
        <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
        @if($notifications->count() > 0)
            <span id="notifCount"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center">
                {{ $notifications->count() }}
            </span>
        @endif
    </button>


    <!-- 🔽 Dropdown -->
    <div id="notifDropdown"
         class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-white-200 z-50">
        <div class="p-3 border-b font-semibold text-white-700">Notifications</div>
<ul class="max-h-60 overflow-y-auto">
    @forelse($notifications as $notif)
        <li class="px-4 py-2 hover:bg-white-50 text-base border-b">
            {{-- Application --}}
            @if($notif->type === 'application')
                <p class="font-medium
                    {{ $notif->status === 'Approved' ? 'text-green-600' : 'text-red-600' }}">
                    📌 Application of {{ $notif->name }} was {{ $notif->status }}
                </p>
            @elseif($notif->type === 'renewal')
                <p class="font-medium
                    {{ $notif->status === 'Approved' ? 'text-green-600' : 'text-red-600' }}">
                    🔄 Renewal of {{ $notif->name }} was {{ $notif->status }}
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

<!-- ⚡ JS -->
<script>
    document.getElementById("notifBell").addEventListener("click", function () {
        let dropdown = document.getElementById("notifDropdown");
        dropdown.classList.toggle("hidden");

        // remove badge when opened
        let notifCount = document.getElementById("notifCount");
        if (notifCount) {
            notifCount.remove();
        }
    });
</script>


            </div>

        </header>
        <!-- Main Content -->
      <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <div class="w-16 md:w-72 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
        <li>
          <a href="/lydo_admin/dashboard" class="idebar-item flex items-center p-3 rounded-lg text-black-600 hover:bg-violet-600 hover:text-white">
            <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Dashboard</span>
          </a>
        </li>
<!-- Staff Dropdown -->
<li class="relative">
    <button onclick="toggleDropdown('staffMenu')"
        class="w-full flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white focus:outline-none">
        <div class="flex items-center">
            <i class="bx bxs-user-detail text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Staff</span>
        </div>
<i class="bx bx-chevron-down ml-2"></i>
</button>

<!-- Dropdown Menu -->
<ul id="staffMenu" class="ml-10 mt-2 space-y-2 hidden">
    <li>
        <a href="/lydo_admin/lydo" 
           class="flex items-center p-2 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-user mr-2"></i> LYDO Staff
        </a>
    </li>
    <li>
        <a href="/lydo_admin/mayor" 
           class="flex items-center p-2 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-building-house mr-2"></i> Mayor Staff
        </a>
    </li>
</ul>


<script>
    function toggleDropdown(id) {
        const menu = document.getElementById(id);
        menu.classList.toggle("hidden");
    }
</script>



<!-- Scholar Dropdown -->
<li class="relative">
    <button onclick="toggleDropdown('scholarMenu')"
        class="w-full flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white focus:outline-none">
        <div class="flex items-center">
            <i class="bx bxs-graduation text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Scholar</span>
        </div>
        <i class="bx bx-chevron-down ml-2"></i>
    </button>

    <!-- Dropdown Menu -->
<ul id="scholarMenu" class="ml-10 mt-2 space-y-2 hidden">
    <li>
        <a href="/lydo_admin/scholar" 
           class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-list-ul mr-2"></i> List of Scholars
        </a>
    </li>
    <li>
        <a href="/lydo_admin/status" 
           class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
           <i class="bx bx-check-circle mr-2"></i> Status
        </a>
    </li>
    <li>
        <a href="/lydo_admin/disbursement" 
           class="flex items-center p-2 rounded-lg text-black-700 bg-violet-600 text-white">
           <i class="bx bx-wallet mr-2"></i> Disbursement
        </a>
    </li>
</ul>

</li>

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
<li>
    <a href="/lydo_admin/applicants" 
     class=" flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
         <div class="flex items-center">
            <i class="bx bxs-user text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Applicants</span>
        </div>
    </a>
</li>
<li>
    <a href="/lydo_admin/announcement"
       class=" flex items-center justify-between p-3 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
        <div class="flex items-center">
            <i class="bx bxs-megaphone text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Announcement</span>
        </div>
    </a>
</li>

        <li>
          <a href="/lydo_admin/report" class=" flex items-center p-3 rounded-lg text-black-600 hover:bg-violet-600 hover:text-white">
            <i class="bx bxs-report text-center mx-auto md:mx-0 text-xl"></i>
            <span class="ml-4 hidden md:block text-lg">Reports</span>
          </a>
        </li>
      </ul>

      <ul class="side-menu space-y-1">
        <li>
          <a href="/lydo_admin/settings" class=" flex items-center p-3 rounded-lg text-black-600 hover:bg-violet-600 hover:text-white">
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

<script>
    document.getElementById('logoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });
</script>
</div>
            </div>
            <div class="flex-1 overflow-y-auto p-4 md:p-5 text-[16px]">
                                <div class="flex justify-between items-center mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">Disbursement Management</h2>
                    </div>

<div class="p-5">
    <!-- Tab Navigation -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button id="tab-create" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 border-violet-500 text-violet-500 font-medium text-sm" data-tab="create">
                    Create Disbursement
                </button>
                <button id="tab-records" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 border-transparent text-gray-500 font-medium text-sm" data-tab="records">
                    Disbursement Records
                </button>
                <button id="tab-signed" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 border-transparent text-gray-500 font-medium text-sm" data-tab="signed">
                    Signed Disbursements
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tab-content-create" class="tab-content">
    <!-- Create Disbursement Form -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Create New Disbursement</h3>
        <form method="POST" action="{{ route('LydoAdmin.createDisbursement') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <!-- Barangay Filter for Scholars -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Scholars by Barangay</label>
                <select id="barangayFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay }}">{{ $barangay }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Select a barangay to filter scholars</p>
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" name="amount" step="0.01" min="0" placeholder="Enter amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required>
            </div>

            <!-- Disbursement Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Disbursement Date</label>
                <input type="date" name="disbursement_date" id="disbursement_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required>
                <small class="error-message text-red-500 text-sm"></small>
            </div>

            <!-- Semester -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select name="semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required>
                    <option value="">Select Semester</option>
                    <option value="1st Semester">1st Semester</option>
                    <option value="2nd Semester">2nd Semester</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>

            <!-- Academic Year -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                @php
                    $currentYear = date('Y');
                    $nextYear = $currentYear + 1;
                @endphp
                <input type="text" name="academic_year" value="{{ $currentYear }}-{{ $nextYear }}" placeholder="e.g., 2024-2025" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required>
            </div>

            <!-- Scholar Selection -->
            <div class="col-span-full md:col-span-2 lg:col-span-3">
                <label class="block text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-users text-violet-600"></i>
                    Select Scholar(s)
                    <span id="selectedCount" class="text-xs bg-violet-100 text-violet-700 px-2 py-1 rounded-full font-medium">0 selected</span>
                </label>

                <!-- Search and Action Bar -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                        <!-- Search Input -->
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <input type="text" id="scholarSearch" placeholder="Search scholars..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 text-sm">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button type="button" id="selectAllScholars" class="flex items-center gap-2 px-4 py-2 text-sm bg-violet-600 text-white rounded-lg hover:bg-violet-700 focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium">
                                <i class="fas fa-check-square"></i>
                                Select All
                            </button>
                            <button type="button" id="clearAllScholars" class="flex items-center gap-2 px-4 py-2 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium">
                                <i class="fas fa-times-circle"></i>
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Scholar Select Container -->
                <div class="relative">
                    <select name="scholar_ids[]" multiple id="scholarSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 shadow-sm hover:shadow-md transition-all duration-200 bg-white" style="height: 280px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #a78bfa #f3f4f6;" required>
                        @foreach($scholars as $scholar)
                            <option value="{{ $scholar->scholar_id }}" class="py-2 px-3 hover:bg-violet-50">{{ $scholar->full_name }}</option>
                        @endforeach
                    </select>

                    <!-- Custom scrollbar styling -->
                    <style>
                        #scholarSelect::-webkit-scrollbar {
                            width: 6px;
                        }
                        #scholarSelect::-webkit-scrollbar-track {
                            background: #f3f4f6;
                            border-radius: 3px;
                        }
                        #scholarSelect::-webkit-scrollbar-thumb {
                            background: #a78bfa;
                            border-radius: 3px;
                        }
                        #scholarSelect::-webkit-scrollbar-thumb:hover {
                            background: #8b5cf6;
                        }
                        #scholarSelect option {
                            padding: 8px 12px;
                            border-bottom: 1px solid #f3f4f6;
                        }
                        #scholarSelect option:hover {
                            background-color: #faf5ff !important;
                        }
                        #scholarSelect option:checked {
                            background-color: #ddd6fe !important;
                            font-weight: 500;
                        }
                    </style>
                </div>

                <!-- Help Text -->
                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-blue-700 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span><strong>Instructions:</strong> Hold Ctrl (or Cmd on Mac) to select multiple scholars. Use the search box above to filter the list.</span>
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
<div class="col-span-full flex justify-center mt-4">
    <button type="submit" id="submitBtn" 
        class="bg-violet-600 text-white px-6 py-2 rounded-lg hover:bg-violet-700 focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
        Create Disbursement
    </button>
</div>
        </form>
    </div>
</div>

<div id="tab-content-records" class="tab-content hidden">
    <!-- Search and Filter Section -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form id="filterForm" method="GET" action="{{ route('LydoAdmin.disbursement') }}"
            class="grid grid-cols-1 md:grid-cols-5 gap-4">

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Enter name..."
                    class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
            </div>

            <!-- Barangay -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                <select name="barangay"
                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                            {{ $barangay }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Academic Year -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Academic Year</label>
                <select name="academic_year"
                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Semester -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Semester</label>
                <select name="semester"
                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                    <option value="">All Semesters</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester }}" {{ request('semester') == $semester ? 'selected' : '' }}>
                            {{ $semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Print Button -->
            <div class="flex items-end">
                <button type="button" id="printPdfBtn"
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium">
                    <i class="fas fa-print"></i> Print PDF
                </button>
            </div>

        </form>
            
    </div>

    <!-- Disbursement Table -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Disbursement List</h2>
        
        @if($disbursements->count() > 0)
            <div class="overflow-hidden border border-gray-200  shadow-lg">
                <table class="w-full  table-fixed border-collapse text-[17px]">
                    <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                        <tr>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Full Name</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Barangay</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Semester</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Academic Year</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Amount</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Disburse Date</th>
                        </tr>
                    </thead>
                </table>
                <div class="h-auto overflow-y-auto">
                    <table class="w-full table-fixed border-collapse text-[17px]">
                        <tbody id="disbursementTableBody">
                            @foreach($disbursements as $disburse)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ $disburse->full_name }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ $disburse->applicant_brgy }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ $disburse->disburse_semester }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ $disburse->disburse_acad_year }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">₱{{ number_format($disburse->disburse_amount, 2) }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ \Carbon\Carbon::parse($disburse->disburse_date)->format('F d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $disbursements->appends(request()->except('page'))->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No disbursement records found matching your criteria.</p>
            </div>
        @endif
    </div>
</div>

<div id="tab-content-signed" class="tab-content hidden">
    <!-- Search and Filter Section -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <form id="signedFilterForm" method="GET" action="{{ route('LydoAdmin.disbursement') }}"
            class="grid grid-cols-1 md:grid-cols-5 gap-4">

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Enter name..."
                    class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
            </div>

            <!-- Barangay -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                <select name="barangay"
                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                            {{ $barangay }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Academic Year -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Academic Year</label>
                <select name="academic_year"
                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Semester -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Semester</label>
                <select name="semester"
                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                    <option value="">All Semesters</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester }}" {{ request('semester') == $semester ? 'selected' : '' }}>
                            {{ $semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Print Button -->
            <div class="flex items-end">
                <button type="button" id="signedPrintPdfBtn"
                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium">
                    <i class="fas fa-print"></i> Print PDF
                </button>
            </div>

        </form>

    </div>

    <!-- Signed Disbursement Table -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Signed Disbursement List</h2>

        @if($signedDisbursements->count() > 0)
            <div class="overflow-hidden border border-gray-200 shadow-lg">
                <table class="w-full table-fixed border-collapse text-[17px]">
                    <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                        <tr>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Full Name</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Barangay</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Semester</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Academic Year</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Amount</th>
                            <th class="w-2/12 px-4 py-3 border border-gray-200 text-center">Disburse Date</th>
                        </tr>
                    </thead>
                </table>
                <div class="h-auto overflow-y-auto">
                    <table class="w-full table-fixed border-collapse text-[17px]">
                        <tbody id="signedDisbursementTableBody">
                            @foreach($signedDisbursements as $disburse)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ $disburse->full_name }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ $disburse->applicant_brgy }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ $disburse->disburse_semester }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ $disburse->disburse_acad_year }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">₱{{ number_format($disburse->disburse_amount, 2) }}</td>
                                    <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">{{ \Carbon\Carbon::parse($disburse->disburse_date)->format('F d, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $signedDisbursements->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 text-lg">No signed disbursement records found.</p>
            </div>
        @endif
    </div>
</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.getElementById("filterForm");
    const tableBody = document.getElementById("disbursementTableBody");

    // debounce function para hindi sobrang dami ng request habang nagta-type
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // function para mag-load ng filtered records
    function loadDisbursements() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);

        fetch(`/lydo_admin/disbursement?${params.toString()}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(res => res.json())
        .then(data => {
            tableBody.innerHTML = "";

            if (data.length > 0) {
                data.forEach(row => {
                    tableBody.innerHTML += `
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.full_name}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.applicant_brgy}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.disburse_semester}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.disburse_acad_year}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">₱${parseFloat(row.disburse_amount).toFixed(2)}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.disburse_date}</td>
                        </tr>
                    `;
                });
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">
                            No disbursement records found.
                        </td>
                    </tr>
                `;
            }
        })
        .catch(err => {
            console.error("Error fetching disbursements:", err);
        });
    }

    // auto filter habang nagta-type o nagbabago ang select
    filterForm.querySelectorAll("input, select").forEach(input => {
        if (input.type === "text") {
            input.addEventListener("input", debounce(loadDisbursements, 400));
        } else {
            input.addEventListener("change", loadDisbursements);
        }
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const signedFilterForm = document.getElementById("signedFilterForm");
    const signedTableBody = document.getElementById("signedDisbursementTableBody");

    // debounce function para hindi sobrang dami ng request habang nagta-type
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // function para mag-load ng filtered signed records
    function loadSignedDisbursements() {
        const formData = new FormData(signedFilterForm);
        const params = new URLSearchParams(formData);
        params.append('type', 'signed');

        fetch(`/lydo_admin/disbursement?${params.toString()}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(res => res.json())
        .then(data => {
            signedTableBody.innerHTML = "";

            if (data.length > 0) {
                data.forEach(row => {
                    signedTableBody.innerHTML += `
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.full_name}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.applicant_brgy}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.disburse_semester}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.disburse_acad_year}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">₱${parseFloat(row.disburse_amount).toFixed(2)}</td>
                            <td class="w-2/12 px-4 border border-gray-200 py-2 text-center">${row.disburse_date}</td>
                        </tr>
                    `;
                });
            } else {
                signedTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">
                            No signed disbursement records found.
                        </td>
                    </tr>
                `;
            }
        })
        .catch(err => {
            console.error("Error fetching signed disbursements:", err);
        });
    }

    // auto filter habang nagta-type o nagbabago ang select
    signedFilterForm.querySelectorAll("input, select").forEach(input => {
        if (input.type === "text") {
            input.addEventListener("input", debounce(loadSignedDisbursements, 400));
        } else {
            input.addEventListener("change", loadSignedDisbursements);
        }
    });
});
</script>

<script>
    // Tab switching functionality with persistence
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        // Load active tab from localStorage or default to 'create'
        const activeTab = localStorage.getItem('activeDisbursementTab') || 'create';
        switchTab(activeTab);

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tab = this.getAttribute('data-tab');
                switchTab(tab);
                localStorage.setItem('activeDisbursementTab', tab);
            });
        });

        function switchTab(tab) {
            // Remove active classes from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('border-violet-500', 'text-violet-500');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Add active class to clicked button
            document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-' + tab).classList.add('border-violet-500', 'text-violet-500');

            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Show selected tab content
            document.getElementById('tab-content-' + tab).classList.remove('hidden');
        }
    });

    // Barangay filter functionality
    document.getElementById('barangayFilter').addEventListener('change', function() {
        const barangay = this.value;
        const scholarSelect = document.getElementById('scholarSelect');

        // Clear current options
        scholarSelect.innerHTML = '<option value="">Loading...</option>';

        // Fetch scholars for selected barangay
        fetch(`/lydo_admin/get-scholars-by-barangay?barangay=${encodeURIComponent(barangay)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    scholarSelect.innerHTML = '';
                    data.scholars.forEach(scholar => {
                        const option = document.createElement('option');
                        option.value = scholar.scholar_id;
                        option.textContent = scholar.full_name;
                        scholarSelect.appendChild(option);
                    });
                } else {
                    scholarSelect.innerHTML = '<option value="">Error loading scholars</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                scholarSelect.innerHTML = '<option value="">Error loading scholars</option>';
            });
    });

    // Select All functionality
    document.getElementById('selectAllScholars').addEventListener('click', function() {
        const scholarSelect = document.getElementById('scholarSelect');
        const options = scholarSelect.options;

        // First select all scholars
        for (let i = 0; i < options.length; i++) {
            options[i].selected = true;
        }

        // Then filter out scholars who already have disbursements for the current academic year and semester
        filterScholarsWithDisbursements();
    });

    // Clear All functionality
    document.getElementById('clearAllScholars').addEventListener('click', function() {
        const scholarSelect = document.getElementById('scholarSelect');
        const options = scholarSelect.options;

        for (let i = 0; i < options.length; i++) {
            options[i].selected = false;
        }
    });

    // Disbursement date validation
function validateDisbursementDate() {
    const dateInput = document.getElementById('disbursement_date');
    const errorElement = dateInput.nextElementSibling;
    const submitBtn = document.getElementById('submitBtn');
    const value = dateInput.value;

    if (value) {
        const date = new Date(value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Extract the year part
        const year = value.split('-')[0];

        if (isNaN(date.getTime())) {
            errorElement.textContent = "Invalid date";
            dateInput.classList.add('error');
            submitBtn.disabled = true;
        } 
        else if (!/^\d{4}$/.test(year)) {
            errorElement.textContent = "Year must be 4 digits (e.g., 2025)";
            dateInput.classList.add('error');
            submitBtn.disabled = true;
        }
        else if (date <= today) {
            errorElement.textContent = "Disbursement date must be in the future";
            dateInput.classList.add('error');
            submitBtn.disabled = true;
        } 
        else {
            errorElement.textContent = "";
            dateInput.classList.remove('error');
            toggleButton();
        }
    } else {
        errorElement.textContent = "";
        dateInput.classList.remove('error');
        toggleButton();
    }
}


    function toggleButton() {
        const submitBtn = document.getElementById('submitBtn');
        const errorElements = document.querySelectorAll('.error-message');

        let hasErrors = false;
        errorElements.forEach(element => {
            if (element.textContent.trim() !== '') {
                hasErrors = true;
            }
        });

        submitBtn.disabled = hasErrors;
    }

    // Add event listeners for disbursement date validation
    document.getElementById('disbursement_date').addEventListener('blur', validateDisbursementDate);
    document.getElementById('disbursement_date').addEventListener('input', validateDisbursementDate);

    // Function to filter out scholars with existing disbursements
    function filterScholarsWithDisbursements() {
        const academicYearInput = document.querySelector('input[name="academic_year"]');
        const semesterSelect = document.querySelector('select[name="semester"]');
        const scholarSelect = document.getElementById('scholarSelect');

        const academicYear = academicYearInput.value.trim();
        const semester = semesterSelect.value;

        if (!academicYear || !semester) {
            return; // Don't filter if either field is empty
        }

        // Fetch scholars who already have disbursements for this academic year and semester
        fetch(`/lydo_admin/get-scholars-with-disbursement?academic_year=${encodeURIComponent(academicYear)}&semester=${encodeURIComponent(semester)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const scholarsWithDisbursements = data.scholar_ids;
                    const options = scholarSelect.options;

                    // Unselect scholars who already have disbursements
                    for (let i = 0; i < options.length; i++) {
                        const option = options[i];
                        if (scholarsWithDisbursements.includes(option.value)) {
                            option.selected = false;
                        }
                        // Note: Scholars without disbursements remain selected if they were selected before
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching scholars with disbursements:', error);
            });
    }

    // Add event listeners for academic year and semester changes
    document.querySelector('input[name="academic_year"]').addEventListener('input', filterScholarsWithDisbursements);
    document.querySelector('select[name="semester"]').addEventListener('change', filterScholarsWithDisbursements);

    // Scholar search functionality
    document.getElementById('scholarSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const scholarSelect = document.getElementById('scholarSelect');
        const options = scholarSelect.options;

        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const text = option.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }
    });

    // Update selected count
    function updateSelectedCount() {
        const scholarSelect = document.getElementById('scholarSelect');
        const selectedOptions = Array.from(scholarSelect.selectedOptions);
        const count = selectedOptions.length;
        const selectedCountElement = document.getElementById('selectedCount');

        selectedCountElement.textContent = count + ' selected';

        // Change badge color based on count
        if (count === 0) {
            selectedCountElement.className = 'text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium';
        } else if (count < 5) {
            selectedCountElement.className = 'text-xs bg-violet-100 text-violet-700 px-2 py-1 rounded-full font-medium';
        } else {
            selectedCountElement.className = 'text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium';
        }
    }

    // Add event listener for scholar selection changes
    document.getElementById('scholarSelect').addEventListener('change', updateSelectedCount);

    // Update count on page load and after any selection changes
    updateSelectedCount();

    // Update count after select all/clear all actions
    document.getElementById('selectAllScholars').addEventListener('click', function() {
        setTimeout(updateSelectedCount, 10);
    });

    document.getElementById('clearAllScholars').addEventListener('click', function() {
        setTimeout(updateSelectedCount, 10);
    });
</script>

<script>
    // SweetAlert confirmation for form submission
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn.disabled) {
            return; // Don't show confirmation if form is invalid
        }
        Swal.fire({
            title: 'Are you sure you want to create disbursement?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                submitBtn.closest('form').submit();
            }
        });
    });

    // Print PDF functionality
    document.getElementById('printPdfBtn').addEventListener('click', function() {
        const filterForm = document.getElementById('filterForm');
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);

        // Open PDF in new window/tab
        window.open(`/lydo_admin/disbursement-pdf?${params.toString()}`, '_blank');
    });

    // Print PDF functionality for signed disbursements
    document.getElementById('signedPrintPdfBtn').addEventListener('click', function() {
        const signedFilterForm = document.getElementById('signedFilterForm');
        const formData = new FormData(signedFilterForm);
        const params = new URLSearchParams(formData);
        params.append('type', 'signed');

        // Open PDF in new window/tab
        window.open(`/lydo_admin/disbursement-pdf?${params.toString()}`, '_blank');
    });

    // SweetAlert for session messages
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>

</body>

</html>
