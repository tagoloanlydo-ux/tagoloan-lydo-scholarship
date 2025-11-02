<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LYDO Scholarship </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="/img/LYDO.png">
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <audio id="notificationSound" src="{{ asset('notification/blade.wav') }}" preload="auto"></audio>
    <style>
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
            opacity: 1;
            animation: fadeIn 1s ease forwards;
        }

        .loading-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }

        .spinner {
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: 0 auto 2rem;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner img {
            width: 80%;
            height: 100%;
            border-radius: 50%;
        }

        .text-line {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 1s ease forwards 0.5s both;
            color: white;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
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

        /* Responsive design for spinner */
        @media (max-width: 768px) {
            .text-line {
                font-size: 1.8rem;
            }
            .spinner {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 480px) {
            .text-line {
                font-size: 1.5rem;
            }
            .spinner {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>

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
    <div class="dashboard-grid">
        <!-- Header -->
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
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
                            <a href="/mayor_staff/dashboard" class="w-ful flex items-center p-3 rounded-lg text-white bg-violet-600 hover:bg-violet-700">
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
                                        <a href="/mayor_staff/application"
                                        class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                        <i class="bx bx-search-alt mr-2 text-white-700"></i> Review Applications
                                        </a>
                                </li>
                                <li>
                                        <a href="/mayor_staff/status"
                                        class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                        <i class="bx bx-check-circle mr-2 text-white-700"></i> Update Status
                                        </a>
                                </li>
                            </ul>
                        </li>
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
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm"> @csrf <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                            <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                            <span class="hidden md:block text-red-600">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-4 md:p-5 text-[14px] text-black-600">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Card 1: Applicants -->
                    <a href="/mayor_staff/application" class="bg-white rounded-xl shadow-md p-5 flex flex-col min-h-[180px] hover:shadow-lg transition-shadow cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-violet-100 rounded-full p-3 flex items-center justify-center">
                                    <i class="fas fa-users text-violet-600 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-violet-600 font-medium">Total Applicants</p>
                                    <p class="text-xs text-gray-500">{{ $currentAcadYear }}</p>
                                </div>
                            </div>
                            <div class="{{ $percentage >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                <i class="fas {{ $percentage >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-violet-600 mb-3">{{ $applicantsCurrentYear }}</h3>
                        <div class="mt-auto">
                            <div class="text-sm {{ $percentage >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }} p-2 rounded-lg">
                                <i class="fas {{ $percentage >= 0 ? 'fa-chart-line' : 'fa-chart-line-down' }} mr-1"></i>
                                {{ number_format(abs($percentage), 1) }}% {{ $percentage >= 0 ? 'increase' : 'decrease' }} vs {{ $lastAcadYear }}
                            </div>
                        </div>
                    </a>

                    <!-- Card 2: Pending Initial Screening -->
                    <a href="/mayor_staff/application" class="bg-orange-50 rounded-xl shadow-md p-5 flex flex-col min-h-[180px] hover:shadow-lg transition-shadow cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-orange-100 rounded-full p-3 flex items-center justify-center">
                                    <i class="fas fa-clock text-orange-500 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-orange-600 font-medium">Pending Screening</p>
                                    <p class="text-xs text-gray-500">Initial Review</p>
                                </div>
                            </div>
                            <div class="text-orange-500">
                                <i class="fas fa-hourglass-half text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-orange-600 mb-3">{{ $pendingInitial }}</h3>
                        <div class="mt-auto">
                            <div class="text-sm bg-orange-100 text-orange-700 p-2 rounded-lg">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $pendingInitial }} applications awaiting review
                            </div>
                        </div>
                    </a>

                    <!-- Card 3: Pending Applications -->
                    <a href="/mayor_staff/status" class="bg-blue-50 rounded-xl shadow-md p-5 flex flex-col min-h-[180px] hover:shadow-lg transition-shadow cursor-pointer">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 rounded-full p-3 flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-500 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-600 font-medium">Pending Status</p>
                                    <p class="text-xs text-gray-500">Final Decision</p>
                                </div>
                            </div>
                            <div class="@if($pendingStatus > 0) text-blue-500 @else text-green-500 @endif">
                                <i class="fas @if($pendingStatus > 0) fa-hourglass-half @else fa-check-circle @endif text-xl"></i>
                            </div>
                        </div>
                        <h3 class="text-3xl font-extrabold text-blue-600 mb-3">{{ $pendingStatus }}</h3>
                        <div class="mt-auto">
                            <div class="text-sm @if($pendingStatus > 0) bg-blue-100 text-blue-700 @else bg-green-50 text-green-700 @endif p-2 rounded-lg">
                                <i class="fas @if($pendingStatus > 0) fa-clock @else fa-check @endif mr-1"></i>
                                @if($pendingStatus > 0)
                                    {{ $pendingStatus }} applications pending final decision
                                @else
                                    All applications processed
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
<br>
<!-- Three Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Recent Decisions Section -->
                <div class="bg-white rounded-xl shadow-md p-5 lg:col-span-2 flex flex-col h-60">
                        <div class="flex justify-between items-center mb-4 sticky top-0 bg-white z-10 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-violet-700">Recent Decisions</h3>
                            <button id="filterToggle" class="text-violet-700 hover:text-violet-800 text-sm font-medium">
                                <i class="fas fa-filter mr-1"></i>
                                Filters
                            </button>
                        </div>

                        <!-- Filter Section -->
                        <div id="filterSection" class="mb-4 p-3 bg-gray-50 rounded-lg hidden">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <!-- Search Input -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input type="text" id="searchInput" placeholder="Search by name..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                                        <option value="">All Status</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                </div>

                                <!-- School Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                                    <select id="schoolFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                                        <option value="">All Schools</option>
                                        @foreach($recentDecisions->unique('applicant_school_name') as $decision)
                                            <option value="{{ $decision->applicant_school_name }}">{{ $decision->applicant_school_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Academic Year Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                                    <select id="academicYearFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500">
                                        <option value="">All Years</option>
                                        @foreach($availableAcademicYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Reset Filters Button -->
                            <div class="mt-3 flex justify-end">
                                <button id="resetFilters" class="px-4 py-2 bg-violet-600 text-white rounded-md hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 text-sm font-medium">
                                    <i class="fas fa-undo mr-1"></i>
                                    Reset Filters
                                </button>
                            </div>
                        </div>

                        <div id="decisionsList" class="space-y-3 overflow-y-auto flex-1">
                            @forelse($recentDecisions as $decision)
                                @php
                                    if ($decision->status == 'Approved') {
                                        $label = 'Approved as Scholar';
                                    } elseif ($decision->status == 'Rejected') {
                                        $label = 'Rejected as Scholar';
                                    } elseif ($decision->initial_screening == 'Approved') {
                                        $label = 'Approved for Interview';
                                    } elseif ($decision->initial_screening == 'Rejected') {
                                        $label = 'Rejected for Interview';
                                    } else {
                                        $label = 'Pending';
                                    }
                                @endphp
                                <div class="decision-item flex justify-between items-center border-b border-gray-200 pb-2"
                                    data-name="{{ strtolower($decision->applicant_fname . ' ' . $decision->applicant_lname) }}"
                                    data-status="{{ $decision->status }}"
                                    data-school="{{ $decision->applicant_school_name }}"
                                    data-academic-year="{{ $decision->applicant_acad_year ?? '' }}">
                                    <div>
                                        <p class="font-medium">{{ $decision->applicant_fname }} {{ $decision->applicant_lname }}</p>
                                        <p class="text-sm text-gray-500">{{ $decision->applicant_school_name }} - {{ $decision->applicant_course }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1
                                        @if($decision->status == 'Approved') bg-green-100 text-green-800
                                        @elseif($decision->status == 'Rejected') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        @if($decision->status == 'Approved')
                                            <i class="fas fa-check text-xs"></i>
                                        @elseif($decision->status == 'Rejected')
                                            <i class="fas fa-times text-xs"></i>
                                        @else
                                            <i class="fas fa-clock text-xs"></i>
                                        @endif
                                        {{ $label }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-4">
                                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                                    <p>No recent decisions available</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- No Results Message -->
                        <div id="noResults" class="text-center text-gray-500 py-4 hidden">
                            <i class="fas fa-search text-2xl mb-2"></i>
                            <p>No decisions match your filters</p>
                        </div>
                    </div>

                    <!-- Remarks Distribution Section -->
                    <div class="bg-white rounded-xl shadow-md p-5">
                        <h3 class="text-lg font-bold text-violet-700 mb-4 text-center">Application Status Distribution</h3>
                        <div class="flex items-center justify-center">
                            <div class="w-40 h-40">
                                <canvas id="remarksChart"></canvas>
                            </div>
                            <div id="legendContainer" class="flex-1 text-sm ml-6">
                                <!-- Legend will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Pending Applications -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
                    <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                        <h3 class="font-semibold text-lg flex items-center text-gray-700">
                            <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                            New Pending Applications
                        </h3>
                        <div id="pendingCount" class="text-gray-500 text-sm">
                            Showing latest pending applications
                        </div>
                    </div>

                    <div id="pendingApplicationsList" class="overflow-y-auto divide-y divide-gray-200" style="max-height: 350px;">
                        @php
                            $pendingApps = DB::table("tbl_applicant")
                                ->join("tbl_application", "tbl_applicant.applicant_id", "=", "tbl_application.applicant_id")
                                ->join("tbl_application_personnel", "tbl_application.application_id", "=", "tbl_application_personnel.application_id")
                                ->select(
                                    "tbl_applicant.applicant_id",
                                    DB::raw("CONCAT(tbl_applicant.applicant_fname, ' ', COALESCE(tbl_applicant.applicant_mname, ''), ' ', tbl_applicant.applicant_lname, IFNULL(CONCAT(' ', tbl_applicant.applicant_suffix), '')) as name"),
                                    "tbl_applicant.applicant_course as course",
                                    "tbl_applicant.applicant_school_name as school",
                                    "tbl_applicant.created_at",
                                    "tbl_application_personnel.remarks",
                                )
                                ->where("tbl_applicant.applicant_acad_year", $currentAcadYear)
                                ->where("tbl_application_personnel.initial_screening", "=", "Pending")
                                ->orderBy("tbl_applicant.applicant_id", "desc")
                                ->limit(10)
                                ->get();
                        @endphp
                        @forelse($pendingApps as $app)
                            <div class="p-4 hover:bg-gray-50 transition text-sm" data-id="{{ $app->applicant_id }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-base text-gray-800">{{ $app->name }}</h4>
                                        <div class="text-gray-600 mt-1 text-sm">
                                            <span>{{ $app->course }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $app->school }}</span>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full border bg-yellow-50 text-yellow-700 border-yellow-300">
                                        Pending Review
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-gray-500 text-center">No pending applications.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Applicant Trend Chart -->
                <div class="bg-white rounded-xl shadow-md p-8 mb-6">
                    <h3 class="text-lg font-bold text-violet-700 mb-6">Applicant Trend (Last 5 Years)</h3>
                    <div class="w-full h-80">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
<script>
    // Filter Toggle
    document.getElementById('filterToggle').addEventListener('click', function() {
        const filterSection = document.getElementById('filterSection');
        filterSection.classList.toggle('hidden');
    });

    // Filter and Search Functionality
    function filterDecisions() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const schoolFilter = document.getElementById('schoolFilter').value;
        const academicYearFilter = document.getElementById('academicYearFilter').value;

        const decisionItems = document.querySelectorAll('.decision-item');
        const noResults = document.getElementById('noResults');
        let visibleCount = 0;

        decisionItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const status = item.getAttribute('data-status');
            const school = item.getAttribute('data-school');
            const academicYear = item.getAttribute('data-academic-year');

            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesSchool = !schoolFilter || school === schoolFilter;
            const matchesAcademicYear = !academicYearFilter || academicYear === academicYearFilter;

            if (matchesSearch && matchesStatus && matchesSchool && matchesAcademicYear) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    // Reset Filters Functionality
    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('schoolFilter').value = '';
        document.getElementById('academicYearFilter').value = '';
        filterDecisions();
    }

    // Add event listeners to filters
    document.getElementById('searchInput').addEventListener('input', filterDecisions);
    document.getElementById('statusFilter').addEventListener('change', filterDecisions);
    document.getElementById('schoolFilter').addEventListener('change', filterDecisions);
    document.getElementById('academicYearFilter').addEventListener('change', filterDecisions);
    document.getElementById('resetFilters').addEventListener('click', resetFilters);

    // Charts
    document.addEventListener('DOMContentLoaded', function() {
        // Remarks Distribution Chart with Custom Legend
        const ctx = document.getElementById('remarksChart').getContext('2d');
        const remarksData = @json($remarksDistribution);

        const labels = remarksData.map(item => item.remarks);
        const data = remarksData.map(item => item.count);
        const backgroundColors = [
            '#FF6384', // Poor - Red
            '#36A2EB', // Non Poor - Blue
            '#FFCE56', // Ultra Poor - Yellow
            '#4BC0C0'  // Non Indigenous - Teal
        ];

        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Hide default legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Custom Legend
        const legendContainer = document.getElementById('legendContainer');
        labels.forEach((label, index) => {
            const legendItem = document.createElement('div');
            legendItem.className = 'flex items-center mb-1';
            legendItem.innerHTML = `
                <div class="w-3 h-3 rounded-full mr-2" style="background-color: ${backgroundColors[index]}"></div>
                <span class="text-gray-700">${label}: ${data[index]}</span>
            `;
            legendContainer.appendChild(legendItem);
        });

        // Applicant Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendData = @json($applicantTrend);

        const trendLabels = trendData.map(item => item.year);
        const trendValues = trendData.map(item => item.count);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Number of Applicants',
                    data: trendValues,
                    borderColor: '#7C3AED',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Applicants: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });
    });
</script>

<script>
    // Welcome modal using SweetAlert2 with user name and role, auto-dismiss after 4 seconds
    console.log('Welcome script running');
    console.log('sessionStorage welcomeShown:', sessionStorage.getItem('welcomeShown'));
    if (!sessionStorage.getItem('welcomeShown')) {
        console.log('Showing welcome modal');
        Swal.fire({
            title: '👋 Welcome back, {{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} (Mayor Staff)!',
            icon: 'success',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: false,
            width: '600px',
            didOpen: (modal) => {
                modal.addEventListener('mouseenter', Swal.stopTimer)
                modal.addEventListener('mouseleave', Swal.resumeTimer)
            },
            position: 'center',
            background: '#f3e8ff',
            color: '#5b21b6'
        });
        sessionStorage.setItem('welcomeShown', 'true');
    } else {
        console.log('Welcome modal already shown, skipping');
    }
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
<script>
    document.getElementById("notifBell").addEventListener("click", function () {
        let dropdown = document.getElementById("notifDropdown");
        dropdown.classList.toggle("hidden");

        // remove badge when opened
        let notifCount = document.getElementById("notifCount");
        if (notifCount) {
            notifCount.remove();
            // Mark notifications as viewed
            fetch('/mayor_staff/mark-notifications-viewed', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
        }
    });
</script>
<script>
    // Play sound if there are new notifications
    const hasNewNotifications = {{ $showBadge && $notifications->count() > 0 ? 'true' : 'false' }};
    if (hasNewNotifications) {
        document.getElementById('notificationSound').play();
    }
</script>

 <script src="{{ asset('js/logout.js') }}"></script>

<script>
    // Hide loading spinner when page is fully loaded
    window.addEventListener('load', function() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.classList.add('fade-out');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 1000); // Match the fade-out animation duration
        }
    });
</script>
</div>
</body>
