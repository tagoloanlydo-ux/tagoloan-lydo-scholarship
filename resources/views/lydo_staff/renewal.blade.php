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
    <link rel="stylesheet" href="{{ asset('css/renewal.css') }}" />
    <style>
        :root {
            --primary-color: #7c3aed;
            --primary-dark: #6d28d9;
            --primary-light: #a78bfa;
            --text-secondary: #64748b;
        }

        /* Layout structure */
        body {
            height: 100vh;
            overflow: hidden;
        }
        
        .dashboard-grid {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Fixed header */
        header {
            position: sticky;
            top: 0;
            z-index: 40;
            flex-shrink: 0;
        }
        
        /* Main content area */
        .main-content {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        
        /* Fixed sidebar */
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            flex-shrink: 0;
        }
        
        /* Scrollable content area */
        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        
        /* Fixed tab buttons */
        .tab-container {
            position: sticky;
            top: 0;
            background: white;
            z-index: 30;
            padding: 1rem 0;
            margin-bottom: 1rem;
        }

        .tab {
            cursor: pointer;
            padding: 14px 28px;
            border-radius: 16px;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            color: var(--text-secondary);
            border: 2px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        .tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .tab:hover::before {
            left: 100%;
        }

        .tab.active {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 4px 20px rgba(30, 64, 175, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transform: translateY(-2px) scale(1.02);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .tab.tab-green.active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-color: #10b981;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transform: translateY(-2px) scale(1.02);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .tab:hover:not(.active) {
            background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
            border-color: var(--primary-light);
            color: var(--primary-color);
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .tab.tab-green:hover:not(.active) {
            background: linear-gradient(145deg, #d1fae5, #a7f3d0);
            border-color: #10b981;
            color: #065f46;
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .tab:active {
            transform: translateY(0) scale(0.98);
            transition: all 0.1s ease;
        }

        /* Document Status Badges */
        .document-item-wrapper {
            position: relative;
        }

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
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .badge-good {
            background-color: #10b981;
        }

        .badge-bad {
            background-color: #ef4444;
        }

        .badge-new {
            background-color: #8b5cf6;
            font-size: 8px;
            padding: 2px;
        }

        .badge-updated {
            background-color: #f59e0b;
            font-size: 8px;
            padding: 2px;
        }

        /* Status badges for list view */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="dashboard-grid">
        <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <!-- Navbar -->
                    <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                <div class="relative">
                    @php
                        $badgeCount = ($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0) ? $notifications->where('initial_screening', 'Approved')->count() : 0;
                    @endphp
                    <button id="notifBell" class="relative focus:outline-none">
                        <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                        @if($badgeCount > 0)
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $badgeCount }}
                            </span>
                        @endif
                    </button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b font-semibold text-gray-700">Notifications</div>
                        <ul class="max-h-60 overflow-y-auto">
                            @forelse($notifications as $notif)
                                <li class="px-4 py-2 hover:bg-gray-50 text-sm border-b">
                                    @if($notif->initial_screening == 'Approved')
                                        <p class="text-green-600 font-medium"> ✅ {{ $notif->name }} passed initial screening </p>
                                    @elseif($notif->status == 'Renewed')
                                        <p class="text-blue-600 font-medium"> 🔄 {{ $notif->name }} submitted renewal </p>
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
        
        <div class="main-content">
            <div class="sidebar w-20 md:w-80 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
                        <li>
                            <a href="/lydo_staff/dashboard"  class="flex items-center  p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/screening" class="flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bxs-file-blank text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Applicant Interview</span>
                                </div>
                                @if($pendingScreening > 0)
                                    <span class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                        {{ $pendingScreening }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/renewal" class=" flex items-center justify-between p-3 rounded-lg text-gray-700 bg-violet-600 text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Renewals</span>
                                </div>
                                @if($pendingRenewals > 0)
                                    <span id="pendingRenewalsBadge" class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                        {{ $pendingRenewals }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/disbursement" class=" flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-wallet text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Disbursement</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <ul class="side-menu space-y-1">
                        <li>
                            <a href="/lydo_staff/settings" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bxs-cog text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Settings</span>
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
            
            <div class="content-area">
                <div class="p-4 bg-gray-50 min-h-full rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">Scholar Renewal Review</h2>
                    </div>

                    <!-- Fixed Tab Container -->
                    <div class="tab-container">
                        <div class="flex flex-col md:flex-row items-center gap-4">
                            <div class="flex gap-2">
                                <div onclick="showTable()" class="tab active" id="tab-renewal">
                                    <i class="fas fa-table mr-1"></i> Process Renewals
                                </div>
                                <div onclick="showList()" class="tab" id="tab-review">
                                    <i class="fas fa-list mr-1"></i> View Status
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table View (Pending Renewals) -->
                    <div id="tableView" class="overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-200">
                            📋 Pending Renewal Applications: Review and process new renewal submissions from scholars
                            </h3>
                        </div>
                        
                        <!-- Search and Filter Section -->
                        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
                            <div class="flex gap-4 items-end">
                                <div class="flex gap-4">
                                    <!-- Search by Name -->
                                    <div>
                                        <label for="nameSearch" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                                        <div class="relative">
                                            <input type="text" id="nameSearch" placeholder="Enter applicant name..."
                                                style="padding: 0.75rem 2.5rem; width: 20rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; transition: all 0.2s; background-color: white;"
                                                onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.2)'; this.style.outline='none'"
                                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        
                                        </div>
                                    </div>

                                    <!-- Filter by Barangay -->
                                    <div>
                                        <label for="barangayFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                                        <select id="barangayFilter"
                                            style="padding: 0.75rem 2.5rem; width: 16rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; transition: all 0.2s; background-color: white; appearance: none; background-image: url('data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3e%3cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27m6 8 4 4 4-4%27/%3e%3c/svg%3e'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                                            onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.2)'; this.style.outline='none'"
                                            onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                            <option value="">All Barangays</option>
                                            @foreach($barangays as $brgy)
                                                <option value="{{ $brgy }}">{{ $brgy }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-violet-600 to-purple-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Year Level</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Academic Year</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Semester</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Applications</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 1; @endphp
                                @forelse($tableApplicants as $app)
                                    <tr class="hover:bg-gray-50 border-b" data-id="{{ $app->scholar_id }}">
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $count++ }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            {{ ucfirst(strtolower($app->applicant_lname)) }}, {{ ucfirst(strtolower($app->applicant_fname)) }} 
                                            @if($app->applicant_mname)
                                                {{ strtoupper(substr($app->applicant_mname, 0, 1)) }}.
                                            @endif
                                            @if($app->applicant_suffix)
                                                {{ ucfirst(strtolower($app->applicant_suffix)) }}
                                            @endif
                                        </td>                                        
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_year_level }}</td>
                                         <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->renewal_acad_year }}</td>
                                          <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->renewal_semester }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <button onclick="openRenewalModal({{ $app->scholar_id }})"
                                                    class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow">
                                                Review Renewal Docs
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">
                                            No renewals found for the current year.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                           <div class="mt-4">
                           <div class="pagination-container" id="tablePagination"></div>

                        </div>
                    </div>

                    <!-- List View (Processed Renewals) -->
                    <div id="listView" class="hidden overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-green-50 p-3 rounded-lg border border-green-200">
                            Processed Renewal Applications: View applications with their current approval status
                            </h3>
                        </div>
                        
                        <!-- Search and Filter Section -->
                        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
                            <div class="flex gap-4 items-end">
                                <div class="flex gap-4">
                                    <!-- Search by Name -->
                                    <div>
                                        <label for="listNameSearch" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                                        <div class="relative">
                                            <input type="text" id="listNameSearch" placeholder="Enter applicant name..."
                                                style="padding: 0.75rem 2.5rem; width: 20rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; transition: all 0.2s; background-color: white;"
                                                onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.2)'; this.style.outline='none'"
                                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                       
                                       
                                       
                                        </div>
                                    </div>

                                    <!-- Filter by Barangay -->
                                    <div>
                                        <label for="listBarangayFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                                        <select id="listBarangayFilter"
                                            style="padding: 0.75rem 2.5rem; width: 16rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; transition: all 0.2s; background-color: white; appearance: none; background-image: url('data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3e%3cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27m6 8 4 4 4-4%27/%3e%3c/svg%3e'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                                            onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.2)'; this.style.outline='none'"
                                            onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                            <option value="">All Barangays</option>
                                            @foreach($barangays as $brgy)
                                                <option value="{{ $brgy }}">{{ $brgy }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                             <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Year Level</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Academic Year</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Semester</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Application</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count = 1; @endphp
                                @forelse($listView as $app)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $count++ }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            {{ ucfirst(strtolower($app->applicant_lname)) }}, {{ ucfirst(strtolower($app->applicant_fname)) }} 
                                            @if($app->applicant_mname)
                                                {{ strtoupper(substr($app->applicant_mname, 0, 1)) }}.
                                            @endif
                                            @if($app->applicant_suffix)
                                                {{ ucfirst(strtolower($app->applicant_suffix)) }}
                                            @endif
                                        </td>                                        
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>   
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_year_level }}</td>
                                         <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->renewal_acad_year }}</td>
                                          <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->renewal_semester }}</td>
<td class="px-4 py-2 border border-gray-200 text-center">
    <button onclick="openViewRenewalModal({{ $app->renewal_id }}, '{{ $app->renewal_status }}')"
        class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow">
        Review Renewal Docs
    </button>
</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            @if($app->renewal_status === 'Approved')
                                                <span class="status-badge status-approved">Approved</span>
                                            @elseif($app->renewal_status === 'Rejected')
                                                <span class="status-badge status-rejected">Rejected</span>
                                            @else
                                                <span class="status-badge status-pending">{{ $app->renewal_status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">
                                            No renewals found for the current year.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                        <div class="pagination-container" id="listPagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Renewal Modal with Document Rating -->
    <div id="openRenewalModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl animate-fadeIn">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-folder-open text-blue-600"></i>
                    Renewal Requirements
                </h2>
                <button onclick="closeApplicationModal()" 
                        class="p-2 rounded-full hover:bg-gray-100 transition">
                    <i class="fas fa-times text-gray-500 text-lg"></i>
                </button>
            </div>
            
            <div id="applicationContent" class="p-6 space-y-6">
                <!-- Dynamic content will be loaded here -->
            </div>

            <div class="flex justify-between items-center gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
                <button onclick="closeApplicationModal()" 
                        class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </button>

                <div class="flex gap-3" id="actionButtons" style="display: none;">
                    <button onclick="sendEmailForBadDocuments()"
                            class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition flex items-center gap-2"
                            id="sendEmailBtn" style="display: none;">
                        <i class="fas fa-envelope"></i>
                        <span id="sendEmailText">Send Email</span>
                        <div id="sendEmailSpinner" class="hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>

                    <button onclick="updateRenewalStatus(selectedRenewalId, 'Approved')"
                            class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition flex items-center gap-2"
                            id="approveBtn">
                        <span id="approveText">Approve</span>
                        <div id="approveSpinner" class="hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>

                    <button onclick="updateRenewalStatus(selectedRenewalId, 'Rejected')"
                            class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition flex items-center gap-2"
                            id="rejectBtn">
                        <span id="rejectText">Reject</span>
                        <div id="rejectSpinner" class="hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>



<!-- View Renewal Modal -->
<div id="viewRenewalModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl animate-fadeIn">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-folder-open text-blue-600"></i>
                Renewal Details
            </h2>
            <button onclick="closeViewRenewalModal()"
                    class="p-2 rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-times text-gray-500 text-lg"></i>
            </button>
        </div>

        <div id="viewRenewalContent" class="p-6 space-y-6">
            <!-- Dynamic content will be loaded here -->
        </div>

        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
            <button onclick="closeViewRenewalModal()"
                    class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
<div id="documentViewerModal" class="fixed inset-0 hidden bg-black bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-7xl max-h-8xl rounded-2xl shadow-2xl animate-fadeIn">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-alt text-blue-600"></i>
                <span id="documentTitle">Document Viewer</span>
            </h2>
            <button onclick="closeDocumentViewerModal()"
                    class="p-2 rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-times text-gray-500 text-lg"></i>
            </button>
        </div>

        <div class="p-6">
            <!-- Make the viewer area scrollable so the review controls are reachable -->
            <div class="overflow-auto max-h-[70vh]">
                <iframe id="documentViewer" src="" class="w-full h-[55vh] border rounded-lg" style="display: none;"></iframe>
                <div id="documentLoading" class="flex items-center justify-center h-[55vh] text-gray-500">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                        <p>Loading document...</p>
                    </div>
                </div>

                <!-- Document Review Controls (will be visible after scrolling) -->
                <div id="documentReviewControls" class="mt-4 pb-6"></div>

                <!-- ADD THIS SECTION: Document Comments Display -->
                <div id="documentCommentsSection" class="mt-4 hidden">
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-comment text-blue-600 mr-2"></i>
                            Document Review Comments
                        </h4>
                        <div id="currentDocumentComments" class="space-y-3">
                            <!-- Comments will be loaded here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
            <button onclick="closeDocumentViewerModal()"
                    class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                Close
            </button>
        </div>
    </div>
</div>
     
    <script>
        // Global variables na kailangan
        const renewals = @json($renewals);
        
        // I-import ang modal functions
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure renewals is available globally
            window.renewals = renewals;
        });
    </script>
    <script src="{{ asset('js/renewal.js') }}"></script>
    <script src="{{ asset('js/spinner.js') }}"></script>
    <script src="{{ asset('js/logout.js') }}"></script>
    <script src="{{ asset('js/renewal_paginate.js') }}"></script>
</body>

</html>