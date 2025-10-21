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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
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
           class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
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
          <a href="/lydo_admin/report" class=" flex items-center p-3 rounded-lg text-black-600 bg-violet-600 text-white">
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
            <div class="flex-1 overflow-y-auto p-4 md:p-2 text-[14px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Reports Dashboard</h2>
                </div>

                <!-- Tab Navigation -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="flex border-b">
                        <button class="tab-btn px-6 py-3 border-b-2 border-blue-500 text-blue-600 font-medium" data-tab="summary">Summary</button>
                        <button class="tab-btn px-6 py-3 text-gray-600 hover:text-blue-600" data-tab="scholars">List of Scholars</button>
                        <button class="tab-btn px-6 py-3 text-gray-600 hover:text-blue-600" data-tab="applicants">Applicants by Remarks</button>
                        <button class="tab-btn px-6 py-3 text-gray-600 hover:text-blue-600" data-tab="school">School Demographic</button>
                        <button class="tab-btn px-6 py-3 text-gray-600 hover:text-blue-600" data-tab="barangay">Barangay Demographic</button>
                        <button class="tab-btn px-6 py-3 text-gray-600 hover:text-blue-600" data-tab="renewal">Renewal</button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Summary Tab -->
                    <div id="summary-tab" class="tab-pane active">
                        <!-- 4 Column Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="bg-white rounded-xl shadow-sm p-5 border border-blue-300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Total Applicants</p>
                                        <p class="text-2xl font-bold mt-2 text-gray-800">{{ $totalApplicants }}</p>
                                        <p class="text-sm mt-1 text-gray-600">Total registered applicants</p>
                                    </div>
                                    <span class="bg-blue-50 text-blue-600 p-3 rounded-xl text-xl border border-blue-200 shadow-sm">
                                        <i class="fas fa-users"></i>
                                    </span>
                                </div>
                            </div>

        

                            <div class="bg-white rounded-xl shadow-sm p-5 border border-red-300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Rejected Applicants</p>
                                        <p class="text-2xl font-bold mt-2 text-gray-800">{{ $rejectedApplicantsCount }}</p>
                                        <p class="text-sm mt-1 text-gray-600">Rejected applications</p>
                                    </div>
                                    <span class="bg-red-50 text-red-600 p-3 rounded-xl text-xl border border-red-200 shadow-sm">
                                        <i class="fas fa-times-circle"></i>
                                    </span>
                                </div>
                            </div>

  <div class="bg-white rounded-xl shadow-sm p-5 border border-green-300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Active Scholars</p>
                                        <p class="text-2xl font-bold mt-2 text-gray-800">{{ $activeScholarsCount }}</p>
                                        <p class="text-sm mt-1 text-gray-600">Currently active scholars</p>
                                    </div>
                                    <span class="bg-green-50 text-green-600 p-3 rounded-xl text-xl border border-green-200 shadow-sm">
                                        <i class="fas fa-users"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-xl shadow-sm p-5 border border-red-300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Inactive Scholars</p>
                                        <p class="text-2xl font-bold mt-2 text-gray-800">{{ $inactiveScholarsCount }}</p>
                                        <p class="text-sm mt-1 text-gray-600">Currently inactive</p>
                                    </div>
                                    <span class="bg-red-50 text-red-600 p-3 rounded-xl text-xl border border-red-200 shadow-sm">
                                        <i class="fas fa-user-times"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Two Column Line Graphs -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <!-- Approval Rate Trend Line Graph -->
                            <div class="bg-white p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold mb-4">Approval Rate Trend</h3>
                                <canvas id="approvalRateChart" width="400" height="200"></canvas>
                            </div>

                            <!-- Active vs Inactive Scholars Line Graph -->
                            <div class="bg-white p-6 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold mb-4">Active vs Inactive Scholars</h3>
                                <canvas id="activeInactiveChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- List of Scholars Tab -->
                    <div id="scholars-tab" class="tab-pane hidden">
                        <div class="bg-white  shadow-sm p-6">
                            <h3 class="text-lg font-semibold mb-4">List of Scholars</h3>
                            <!-- Filter Section -->
                            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                                <form id="filterForm" method="GET" action="{{ route('LydoAdmin.report') }}"
                                    class="flex flex-col md:flex-row gap-4">
                                    <div class="flex-1">
                                        <input type="text" id="searchInput" name="search" placeholder="Search by name..."
                                               value="{{ request('search') }}"
                                               class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    </div>
                                    <div class="flex-1">
                                        <select id="barangaySelect" name="barangay" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                            <option value="">All Barangays</option>
                                            @foreach($barangays as $barangay)
                                                <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                                    {{ $barangay }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <select id="academicYearSelect" name="academic_year" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                            <option value="">All Academic Years</option>
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
<div class="flex-1">
    <select id="scholarStatusSelect" name="status" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
        <option value="">All Statuses</option>
        @foreach($statuses as $status)
            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                {{ ucfirst($status) }}
            </option>
        @endforeach
    </select>
</div>
<div class="flex-1">
    <button type="button" id="printPdfBtn"
        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium flex items-center justify-center" title="Generate and print scholars report">
        <i class="fas fa-print mr-2"></i> Print PDF
    </button>
</div>
                                </div>
                            </div>
                            <div class="overflow-x-auto bg-white  shadow-sm border border-gray-200">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-green-600 text-white">
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Name
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                School
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Barangay
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Year Level
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($scholars as $scholar)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $scholar->applicant_fname }} {{ $scholar->applicant_lname }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 font-medium">{{ $scholar->applicant_school_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">{{ $scholar->applicant_brgy }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">{{ $scholar->applicant_year_level }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($scholar->scholar_status === 'Approved')
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold border border-gray-300 bg-gray-50 text-gray-800">
                                                        {{ $scholar->scholar_status }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold border border-gray-300 bg-gray-50 text-gray-800">
                                                        {{ $scholar->scholar_status }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Applicants by Remarks Tab -->
                    <div id="applicants-tab" class="tab-pane hidden">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold mb-4">Applicants by Remarks</h3>
                            <!-- Filter Section -->
                            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="applicantsFilterForm">
                                    <div class="flex-1">
                                        <input type="text" id="applicantsSearchInput" placeholder="Search by name..."
                                               value="{{ request('search') }}"
                                               class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    </div>
                                    <div class="flex-1">
                                        <select id="applicantsBarangaySelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                            <option value="">All Barangays</option>
                                            @foreach($barangays as $barangay)
                                                <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                                    {{ $barangay }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <select id="applicantsAcademicYearSelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                            <option value="">All Academic Years</option>
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                            <div class="flex-1">
                                <select id="applicantsRemarksSelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    <option value="">All Remarks</option>
                                    <option value="Ultra Poor" {{ request('remarks') == 'Ultra Poor' ? 'selected' : '' }}>Ultra Poor</option>
                                    <option value="Poor" {{ request('remarks') == 'Poor' ? 'selected' : '' }}>Poor</option>
                                    <option value="Non Poor" {{ request('remarks') == 'Non Poor' ? 'selected' : '' }}>Non Poor</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <button type="button" id="printApplicantsPdfBtn"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium flex items-center justify-center" title="Generate and print applicants report">
                                    <i class="fas fa-print mr-2"></i> Print PDF
                                </button>
                            </div>
                                </div>
                            </div>
                            <div class="overflow-x-auto bg-white shadow-sm border border-gray-200">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-green-600 text-white">
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Applicant Details
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Email
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Contact
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Barangay
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b border-gray-300">
                                                Remarks
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($applicantsWithRemarks as $applicant)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $applicant->applicant_fname }} {{ $applicant->applicant_lname }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">{{ $applicant->applicant_email }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">{{ $applicant->applicant_contact_number }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">{{ $applicant->applicant_brgy }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($applicant->remarks === 'Ultra Poor')
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold border border-gray-300 bg-gray-50 text-gray-800">
                                                        {{ $applicant->remarks }}
                                                    </span>
                                                @elseif($applicant->remarks === 'Poor')
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold border border-gray-300 bg-gray-50 text-gray-800">
                                                        {{ $applicant->remarks }}
                                                    </span>
                                                @elseif($applicant->remarks === 'Non Poor')
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold border border-gray-300 bg-gray-50 text-gray-800">
                                                        {{ $applicant->remarks }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold border border-gray-300 bg-gray-50 text-gray-800">
                                                        {{ $applicant->remarks }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $applicantsWithRemarks->links() }}
                        </div>
                    </div>

                    <!-- School Demographic Tab -->
                    <div id="school-tab" class="tab-pane hidden">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold mb-4">School Demographic</h3>
                            <div class="overflow-x-auto bg-white  shadow-sm border border-gray-200">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-green-600 text-white">
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Educational Institution
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Scholar Count
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Distribution
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($schoolDemographics as $index => $school)
                                        <tr class="hover:bg-emerald-50 transition-colors duration-200 group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-emerald-400 to-teal-400 flex items-center justify-center text-white font-semibold">
                                                            {{ $index + 1 }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-gray-900 group-hover:text-emerald-600">
                                                            {{ $school->applicant_school_name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">Educational Institution</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <span class="text-2xl font-bold text-emerald-600 mr-2">{{ $school->total }}</span>
                                                    <span class="text-sm text-gray-500">scholars</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                                        <div class="bg-gradient-to-r from-emerald-400 to-teal-400 h-2 rounded-full"
                                                             style="width: {{ min(100, ($school->total / max($schoolDemographics->max('total'), 1)) * 100) }}%">
                                                        </div>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ number_format(($school->total / max($schoolDemographics->sum('total'), 1)) * 100, 1) }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Barangay Demographic Tab -->
                    <div id="barangay-tab" class="tab-pane hidden">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold mb-4">Barangay Demographic</h3>
                            <div class="overflow-x-auto bg-white shadow-sm border border-gray-200">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-green-600 text-white">
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Barangay Location
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Applicant Count
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Distribution
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($barangayDemographics as $index => $barangay)
                                        <tr class="hover:bg-orange-50 transition-colors duration-200 group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-orange-400 to-amber-400 flex items-center justify-center text-white font-semibold">
                                                            {{ $index + 1 }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-gray-900 group-hover:text-orange-600">
                                                            {{ $barangay->applicant_brgy }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">Local Community</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <span class="text-2xl font-bold text-orange-600 mr-2">{{ $barangay->total }}</span>
                                                    <span class="text-sm text-gray-500">applicants</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                                        <div class="bg-gradient-to-r from-orange-400 to-amber-400 h-2 rounded-full"
                                                             style="width: {{ min(100, ($barangay->total / max($barangayDemographics->max('total'), 1)) * 100) }}%">
                                                        </div>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ number_format(($barangay->total / max($barangayDemographics->sum('total'), 1)) * 100, 1) }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Renewal Tab -->
                    <div id="renewal-tab" class="tab-pane hidden">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold mb-4">Approved Renewal Applications</h3>
                            <!-- Filter Section -->
                            <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                                <div class="flex flex-col md:flex-row gap-4" id="renewalFilterForm">
                                    <div class="flex-1">
                                        <input type="text" id="renewalSearchInput" placeholder="Search by name..."
                                               value="{{ request('search') }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 placeholder-gray-500">
                                    </div>
                                    <div class="flex-1">
                                        <select id="renewalBarangaySelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                                            <option value="">All Barangays</option>
                                            @foreach($barangays as $barangay)
                                                <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                                    {{ $barangay }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <select id="renewalAcademicYearSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                                            <option value="">All Academic Years</option>
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <select id="renewalStatusSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                                            <option value="">All Statuses</option>
                                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <button type="button" id="printRenewalPdfBtn"
                                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium flex items-center justify-center" title="Generate and print renewal report">
                                            <i class="fas fa-print mr-2"></i> Print PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-x-auto bg-white  shadow-sm border border-gray-200">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-green-600 text-white">
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Scholar Details
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                School
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Barangay
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Academic Year
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">
                                                Date Submitted
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($approvedRenewals as $renewal)
                                        <tr class="hover:bg-cyan-50 transition-colors duration-200 group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-cyan-400 to-blue-400 flex items-center justify-center text-white font-semibold">
                                                            {{ strtoupper(substr($renewal->applicant_fname, 0, 1)) }}{{ strtoupper(substr($renewal->applicant_lname, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-gray-900 group-hover:text-cyan-600">
                                                            {{ $renewal->applicant_fname }} {{ $renewal->applicant_mname ?? '' }} {{ $renewal->applicant_lname }} {{ $renewal->applicant_suffix ?? '' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">{{ $renewal->applicant_course }} - {{ $renewal->applicant_year_level }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 font-medium">{{ $renewal->applicant_school_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $renewal->applicant_brgy }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $renewal->renewal_acad_year }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    {{ $renewal->renewal_status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                                    <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($renewal->date_submitted)->format('M d, Y') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                                    <p class="text-lg font-medium">No approved renewal applications found</p>
                                                    <p class="text-sm">Approved renewal applications will appear here once they are processed.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove active class from all buttons and panes
                    tabBtns.forEach(b => b.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600'));
                    tabBtns.forEach(b => b.classList.add('text-gray-600'));
                    tabPanes.forEach(pane => pane.classList.add('hidden'));

                    // Add active class to clicked button
                    btn.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
                    btn.classList.remove('text-gray-600');

                    // Show corresponding pane
                    const tabId = btn.getAttribute('data-tab');
                    document.getElementById(`${tabId}-tab`).classList.remove('hidden');
                });
            });

            // Initialize charts
            const approvalRateCtx = document.getElementById('approvalRateChart').getContext('2d');
            new Chart(approvalRateCtx, {
                type: 'line',
                data: {
                    labels: @json($years),
                    datasets: [
                        {
                            label: 'Approval Rate (%)',
                            data: @json($approvalRateTrend),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Approval Rate (%)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            const activeInactiveCtx = document.getElementById('activeInactiveChart').getContext('2d');
            new Chart(activeInactiveCtx, {
                type: 'line',
                data: {
                    labels: @json($years),
                    datasets: [
                        {
                            label: 'Active Scholars',
                            data: @json($activeScholarsTrend),
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Inactive Scholars',
                            data: @json($inactiveScholarsTrend),
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        });

  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');
    const academicYearSelect = document.getElementById('academicYearSelect');
    const scholarStatusSelect = document.getElementById('scholarStatusSelect'); // Add this line
    const printPdfBtn = document.getElementById('printPdfBtn');
    const scholarsTableBody = document.querySelector('#scholars-tab tbody');

    // ...existing code...

    function filterScholars() {
        const search = searchInput.value;
        const barangay = barangaySelect.value;
        const academicYear = academicYearSelect.value;
        const status = scholarStatusSelect.value; // Add this line

        // Show loading state
        showLoading();

        // Prepare data for AJAX request
        const formData = new FormData();
        formData.append('search', search);
        formData.append('barangay', barangay);
        formData.append('academic_year', academicYear);
        formData.append('status', status); // Add this line
        formData.append('_token', '{{ csrf_token() }}');

        // ...existing fetch code...
    }

    // Print PDF functionality
    if (printPdfBtn) {
        printPdfBtn.addEventListener('click', function() {
            const filterForm = document.getElementById('filterForm');
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);

            // Open PDF in new window/tab
            window.open(`{{ route("LydoAdmin.report.pdf.scholars") }}?${params.toString()}`, '_blank');
        });
    }

    // ...existing event listeners...

    if (scholarStatusSelect) {
        scholarStatusSelect.addEventListener('change', function() {
            filterScholars();
        });
    }
});

        // AJAX filtering functionality for Applicants
        document.addEventListener('DOMContentLoaded', function() {
            const applicantsSearchInput = document.getElementById('applicantsSearchInput');
            const applicantsBarangaySelect = document.getElementById('applicantsBarangaySelect');
            const applicantsAcademicYearSelect = document.getElementById('applicantsAcademicYearSelect');
            const applicantsRemarksSelect = document.getElementById('applicantsRemarksSelect');
            const applicantsTableBody = document.querySelector('#applicants-tab tbody');

            // Function to show loading state for applicants
            function showApplicantsLoading() {
                applicantsTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="p-4 text-center">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <span class="ml-2">Loading...</span>
                            </div>
                        </td>
                    </tr>
                `;
            }

            // Function to update applicants table with AJAX results
            function updateApplicantsTable(results) {
                if (results.length === 0) {
                    applicantsTableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">
                                No applicants found matching your criteria
                            </td>
                        </tr>
                    `;
                    return;
                }

                applicantsTableBody.innerHTML = results.map(applicant => `
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">
                                ${applicant.applicant_fname} ${applicant.applicant_lname}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">${applicant.applicant_email}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">${applicant.applicant_contact_number}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">${applicant.applicant_brgy}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${getRemarksBadge(applicant.remarks)}
                        </td>
                    </tr>
                `).join('');
            }

            // Function to get remarks badge HTML
            function getRemarksBadge(remarks) {
                if (remarks === 'Ultra Poor') {
                    return `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-800 border border-gray-300">
                        ${remarks}
                    </span>`;
                } else if (remarks === 'Poor') {
                    return `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-800 border border-gray-300">
                        ${remarks}
                    </span>`;
                } else if (remarks === 'Non Poor') {
                    return `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-800 border border-gray-300">
                        ${remarks}
                    </span>`;
                } else {
                    return `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-800 border border-gray-300">
                        ${remarks}
                    </span>`;
                }
            }

            // Function to perform AJAX request for applicants
            function filterApplicants() {
                const search = applicantsSearchInput.value;
                const barangay = applicantsBarangaySelect.value;
                const academicYear = applicantsAcademicYearSelect.value;
                const remarks = applicantsRemarksSelect.value;

                // Show loading state
                showApplicantsLoading();

                // Prepare data for AJAX request
                const formData = new FormData();
                formData.append('search', search);
                formData.append('barangay', barangay);
                formData.append('academic_year', academicYear);
                formData.append('remarks', remarks);
                formData.append('tab', 'applicants');
                formData.append('_token', '{{ csrf_token() }}');

                // Make AJAX request
                fetch('{{ route("LydoAdmin.report.post") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateApplicantsTable(data.applicants);
                    } else {
                        throw new Error('Server returned error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    applicantsTableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="p-4 text-center text-red-500">
                                Error loading data. Please try again.
                            </td>
                        </tr>
                    `;
                });
            }

            // Real-time search functionality for applicants
            let applicantsSearchTimeout;
            if (applicantsSearchInput) {
                applicantsSearchInput.addEventListener('input', function() {
                    // Clear previous timeout
                    clearTimeout(applicantsSearchTimeout);

                    // Set new timeout to trigger search after user stops typing (300ms delay)
                    applicantsSearchTimeout = setTimeout(function() {
                        filterApplicants();
                    }, 300);
                });
            }

            // Real-time dropdown filtering for applicants
if (applicantsBarangaySelect) {
    applicantsBarangaySelect.addEventListener('change', function() {
        filterApplicants();
    });
}

if (applicantsAcademicYearSelect) {
    applicantsAcademicYearSelect.addEventListener('change', function() {
        filterApplicants();
    });
}

if (applicantsRemarksSelect) {
    applicantsRemarksSelect.addEventListener('change', function() {
        filterApplicants();
    });
}

            // Print PDF functionality for applicants
            const printApplicantsPdfBtn = document.getElementById('printApplicantsPdfBtn');
            if (printApplicantsPdfBtn) {
                printApplicantsPdfBtn.addEventListener('click', function() {
                    const search = applicantsSearchInput.value;
                    const barangay = applicantsBarangaySelect.value;
                    const academicYear = applicantsAcademicYearSelect.value;
                    const remarks = applicantsRemarksSelect.value;

                    const params = new URLSearchParams({
                        search: search,
                        barangay: barangay,
                        academic_year: academicYear,
                        remarks: remarks
                    });

                    // Open PDF in new window/tab
                    window.open(`{{ route("LydoAdmin.report.pdf.applicants") }}?${params.toString()}`, '_blank');
                });
            }
        });

        // AJAX filtering functionality for Renewal
        document.addEventListener('DOMContentLoaded', function() {
            const renewalSearchInput = document.getElementById('renewalSearchInput');
            const renewalBarangaySelect = document.getElementById('renewalBarangaySelect');
            const renewalAcademicYearSelect = document.getElementById('renewalAcademicYearSelect');
            const renewalStatusSelect = document.getElementById('renewalStatusSelect');
            const renewalTableBody = document.querySelector('#renewal-tab tbody');

            // Function to show loading state for renewal
            function showRenewalLoading() {
                renewalTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="p-4 text-center">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-600"></div>
                                <span class="ml-2">Loading...</span>
                            </div>
                        </td>
                    </tr>
                `;
            }

            // Function to update renewal table with AJAX results
            function updateRenewalTable(results) {
                if (results.length === 0) {
                    renewalTableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-lg font-medium">No renewal applications found matching your criteria</p>
                                    <p class="text-sm">Try adjusting your filters to see more results.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                renewalTableBody.innerHTML = results.map(renewal => `
                    <tr class="hover:bg-cyan-50 transition-colors duration-200 group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-cyan-400 to-blue-400 flex items-center justify-center text-white font-semibold">
                                        ${renewal.applicant_fname.charAt(0).toUpperCase()}${renewal.applicant_lname.charAt(0).toUpperCase()}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900 group-hover:text-cyan-600">
                                        ${renewal.applicant_fname} ${renewal.applicant_mname || ''} ${renewal.applicant_lname} ${renewal.applicant_suffix || ''}
                                    </div>
                                    <div class="text-xs text-gray-500">${renewal.applicant_course} - ${renewal.applicant_year_level}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">${renewal.applicant_school_name}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                ${renewal.applicant_brgy}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ${renewal.renewal_acad_year}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="fas fa-check-circle mr-1"></i>
                                ${renewal.renewal_status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                <span class="text-sm text-gray-900">${new Date(renewal.date_submitted).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }

            // Function to perform AJAX request for renewal
            function filterRenewals() {
                const search = renewalSearchInput.value;
                const barangay = renewalBarangaySelect.value;
                const academicYear = renewalAcademicYearSelect.value;
                const status = renewalStatusSelect.value;

                // Show loading state
                showRenewalLoading();

                // Prepare data for AJAX request
                const formData = new FormData();
                formData.append('search', search);
                formData.append('barangay', barangay);
                formData.append('academic_year', academicYear);
                formData.append('status', status);
                formData.append('tab', 'renewal');
                formData.append('_token', '{{ csrf_token() }}');

                // Make AJAX request
                fetch('{{ route("LydoAdmin.report.post") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateRenewalTable(data.renewals);
                    } else {
                        throw new Error('Server returned error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    renewalTableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="p-4 text-center text-red-500">
                                Error loading data. Please try again.
                            </td>
                        </tr>
                    `;
                });
            }

            // Real-time search functionality for renewal
            let renewalSearchTimeout;
            if (renewalSearchInput) {
                renewalSearchInput.addEventListener('input', function() {
                    // Clear previous timeout
                    clearTimeout(renewalSearchTimeout);

                    // Set new timeout to trigger search after user stops typing (300ms delay)
                    renewalSearchTimeout = setTimeout(function() {
                        filterRenewals();
                    }, 300);
                });
            }

            // Real-time dropdown filtering for renewal
            if (renewalBarangaySelect) {
                renewalBarangaySelect.addEventListener('change', function() {
                    filterRenewals();
                });
            }

            if (renewalAcademicYearSelect) {
                renewalAcademicYearSelect.addEventListener('change', function() {
                    filterRenewals();
                });
            }

            if (renewalStatusSelect) {
                renewalStatusSelect.addEventListener('change', function() {
                    filterRenewals();
                });
            }

            // Print PDF functionality for renewal
            const printRenewalPdfBtn = document.getElementById('printRenewalPdfBtn');
            if (printRenewalPdfBtn) {
                printRenewalPdfBtn.addEventListener('click', function() {
                    const search = renewalSearchInput.value;
                    const barangay = renewalBarangaySelect.value;
                    const academicYear = renewalAcademicYearSelect.value;
                    const status = renewalStatusSelect.value;

                    const params = new URLSearchParams({
                        search: search,
                        barangay: barangay,
                        academic_year: academicYear,
                        status: status
                    });

                    // Open PDF in new window/tab
                    window.open(`{{ route("LydoAdmin.report.pdf.renewal") }}?${params.toString()}`, '_blank');
                });
            }
        });


    </script>
</body>

</html>
