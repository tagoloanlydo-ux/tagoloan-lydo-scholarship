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
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₱)</label>
                <input type="number" name="amount" step="0.01" min="0" value="2500" placeholder="2,500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required>
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

            <!-- Scholar Table Container -->
            <div class="relative">
                <div class="border border-gray-300 rounded-lg bg-white shadow-sm" style="height: 280px; overflow-y: auto;">
                    <table class="w-full">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="w-8 px-4 py-2">
                                    <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                </th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Barangay</th>
                            </tr>
                        </thead>
                        <tbody id="scholarTableBody" class="divide-y divide-gray-200">
                            @foreach($scholars as $scholar)
                                <tr class="hover:bg-gray-50 scholar-row" data-scholar-id="{{ $scholar->scholar_id }}">
                                    <td class="px-4 py-2">
                                        <input type="checkbox" name="scholar_ids[]" value="{{ $scholar->scholar_id }}" class="scholar-checkbox rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $scholar->full_name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $scholar->applicant_brgy }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-700 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span><strong>Instructions:</strong> Only scholars without existing disbursements for the selected semester and academic year are shown. Use the search box to filter the list.</span>
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
            class="grid grid-cols-1 md:grid-cols-4 gap-4">

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

        </form>
    </div>

    <!-- Disbursement Records Table (Unsigned) -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-violet-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-signature text-violet-600"></i>
                        Disbursement Records (Pending Signature)
                    </h3>
                    <p class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Records waiting for signature approval
                    </p>
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-yellow-700">{{ $disbursements->count() }} pending</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                    <tr>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Name
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Barangay
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Semester
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Academic Year
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Amount
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($disbursements as $index => $disburse)
                    <tr class="transition-all duration-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-sm {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30' }}">
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ $disburse->full_name }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">
                            {{ $disburse->applicant_brgy }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 font-medium">
                            {{ $disburse->disburse_semester }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 font-medium">
                            {{ $disburse->disburse_acad_year }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-green-600">
                            ₱{{ number_format($disburse->disburse_amount, 2) }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($disburse->disburse_date)->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-signature text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Pending Disbursements</h3>
                                <p class="text-gray-500 max-w-md">All disbursements have been signed or no records exist yet. Create new disbursements to see them here.</p>
                                <div class="mt-4 flex items-center gap-2 text-sm text-gray-400">
                                    <i class="fas fa-check-circle"></i>
                                    All caught up!
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
<!-- Add this right after the closing </table> tag in the disbursement records section -->
<div class="px-6 py-4 bg-white border-t border-gray-200">
    <div class="pagination-container" id="disbursementPaginationContainer"></div>
</div>

    </div>
</div>

<div id="tab-content-signed" class="tab-content hidden">
    <!-- Search and Filter Section for Signed Disbursements -->
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

    <!-- Signed Disbursements Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-violet-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-signature text-violet-600"></i>
                        Signed Disbursements
                    </h3>
                    <p class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Completed disbursements with signatures
                    </p>
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-green-700">{{ $signedDisbursements->count() }} signed</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white">
                    <tr>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Name
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Barangay
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Semester
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Academic Year
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Amount
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Date
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold uppercase tracking-wider border-b border-violet-500">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody id="signedDisbursementTableBody" class="bg-white divide-y divide-gray-100">
                    @forelse($signedDisbursements as $index => $disburse)
                    <tr class="transition-all duration-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-sm {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/30' }}">
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ $disburse->full_name }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">
                            {{ $disburse->applicant_brgy }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 font-medium">
                            {{ $disburse->disburse_semester }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 font-medium">
                            {{ $disburse->disburse_acad_year }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-green-600">
                            ₱{{ number_format($disburse->disburse_amount, 2) }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($disburse->disburse_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-green-800">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Signed
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-signature text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Signed Disbursements</h3>
                                <p class="text-gray-500 max-w-md">No disbursements have been signed yet. Signed records will appear here once they are processed.</p>
                                <div class="mt-4 flex items-center gap-2 text-sm text-gray-400">
                                    <i class="fas fa-check-circle"></i>
                                    Waiting for signatures
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
<!-- Add this right after the closing </table> tag in the signed disbursements section -->
<div class="px-6 py-4 bg-white border-t border-gray-200">
    <div class="pagination-container" id="signedDisbursementPaginationContainer"></div>
</div>
    </div>
</div>
</div>
</div>
</div>
</div>

<script>
    // Tab Switching Functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Tab elements
        const createTab = document.getElementById('tab-create');
        const recordsTab = document.getElementById('tab-records');
        const signedTab = document.getElementById('tab-signed');
        
        const createContent = document.getElementById('tab-content-create');
        const recordsContent = document.getElementById('tab-content-records');
        const signedContent = document.getElementById('tab-content-signed');

        // Function to switch tabs
        function switchTab(tabName) {
            // Hide all content
            createContent.classList.add('hidden');
            recordsContent.classList.add('hidden');
            signedContent.classList.add('hidden');
            
            // Remove active styles from all tabs
            createTab.classList.remove('border-violet-500', 'text-violet-500');
            createTab.classList.add('border-transparent', 'text-gray-500');
            recordsTab.classList.remove('border-violet-500', 'text-violet-500');
            recordsTab.classList.add('border-transparent', 'text-gray-500');
            signedTab.classList.remove('border-violet-500', 'text-violet-500');
            signedTab.classList.add('border-transparent', 'text-gray-500');

            // Show selected content and style active tab
            if (tabName === 'create') {
                createContent.classList.remove('hidden');
                createTab.classList.add('border-violet-500', 'text-violet-500');
                createTab.classList.remove('border-transparent', 'text-gray-500');
            } else if (tabName === 'records') {
                recordsContent.classList.remove('hidden');
                recordsTab.classList.add('border-violet-500', 'text-violet-500');
                recordsTab.classList.remove('border-transparent', 'text-gray-500');
            } else if (tabName === 'signed') {
                signedContent.classList.remove('hidden');
                signedTab.classList.add('border-violet-500', 'text-violet-500');
                signedTab.classList.remove('border-transparent', 'text-gray-500');
            }
        }

        // Add click event listeners to tabs
        createTab.addEventListener('click', () => switchTab('create'));
        recordsTab.addEventListener('click', () => switchTab('records'));
        signedTab.addEventListener('click', () => switchTab('signed'));

        // Check URL parameters for active tab
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab) {
            switchTab(activeTab);
        }

        // Scholar Selection Functionality
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const scholarCheckboxes = document.querySelectorAll('.scholar-checkbox');
        const selectAllButton = document.getElementById('selectAllScholars');
        const clearAllButton = document.getElementById('clearAllScholars');
        const selectedCount = document.getElementById('selectedCount');
        const submitBtn = document.getElementById('submitBtn');
        const barangayFilter = document.getElementById('barangayFilter');
        const scholarSearch = document.getElementById('scholarSearch');

        // Update selected count
        function updateSelectedCount() {
            const selected = document.querySelectorAll('.scholar-checkbox:checked').length;
            selectedCount.textContent = `${selected} selected`;
            submitBtn.disabled = selected === 0;
        }

        // Select all scholars
        function selectAllScholars() {
            scholarCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            selectAllCheckbox.checked = true;
            updateSelectedCount();
        }

        // Clear all scholars
        function clearAllScholars() {
            scholarCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectAllCheckbox.checked = false;
            updateSelectedCount();
        }

        // Filter scholars by barangay
        function filterScholarsByBarangay() {
            const selectedBarangay = barangayFilter.value.toLowerCase();
            const rows = document.querySelectorAll('.scholar-row');
            
            rows.forEach(row => {
                const barangay = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                if (!selectedBarangay || barangay.includes(selectedBarangay)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Search scholars
        function searchScholars() {
            const searchTerm = scholarSearch.value.toLowerCase();
            const rows = document.querySelectorAll('.scholar-row');
            
            rows.forEach(row => {
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                if (name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Event listeners
        selectAllCheckbox.addEventListener('change', function() {
            if (this.checked) {
                selectAllScholars();
            } else {
                clearAllScholars();
            }
        });

        scholarCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        selectAllButton.addEventListener('click', selectAllScholars);
        clearAllButton.addEventListener('click', clearAllScholars);
        barangayFilter.addEventListener('change', filterScholarsByBarangay);
        scholarSearch.addEventListener('input', searchScholars);

        // Initial count update
        updateSelectedCount();

        // Date validation
        const disbursementDateInput = document.getElementById('disbursement_date');
        const today = new Date().toISOString().split('T')[0];
        disbursementDateInput.setAttribute('min', today);

        disbursementDateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const currentDate = new Date();
            
            if (selectedDate < currentDate) {
                this.classList.add('error');
                this.nextElementSibling.textContent = 'Disbursement date cannot be in the past.';
            } else {
                this.classList.remove('error');
                this.nextElementSibling.textContent = '';
            }
        });

    });
</script>
<script src="{{ asset('js/disburse.js') }}"></script>
<script src="{{ asset('js/signed_disburse.js') }}"></script>
</body>
</html>