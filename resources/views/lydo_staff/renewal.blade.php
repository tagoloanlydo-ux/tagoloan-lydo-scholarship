<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ADD CSRF META -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <audio id="notificationSound" src="{{ asset('notification/blade.wav') }}" preload="auto"></audio>
    <link rel="stylesheet" href="{{ asset('css/renewal.css') }}" />

    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <style>
  /* A4 Exact Proportion */
#documentViewer {
    width: 794px; /* A4 width in pixels (210mm) */
    height: 1122px; /* A4 height in pixels (297mm) */
    min-height: 1122px;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    margin: 0 auto; /* Center the document */
}

#documentViewerModal .bg-white {
    max-width: 900px; /* Allow some padding */
    max-height: 95vh;
}

/* Responsive A4 */
@media (max-width: 900px) {
    #documentViewer {
        width: 100%;
        height: calc(100vw * 1.414); /* Maintain A4 ratio (1:1.414) */
        min-height: auto;
        max-height: 80vh;
    }
}

@media (max-width: 768px) {
    #documentViewer {
        height: calc(95vw * 1.414);
        max-height: 75vh;
    }
}

@media (max-width: 480px) {
    #documentViewer {
        height: calc(90vw * 1.414);
        max-height: 70vh;
    }
}
  .animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.scale-0 {
    transform: scale(0);
    transition: transform 0.3s ease;
}

#notifCount {
    transition: all 0.3s ease;
}

#notifDropdown {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
 .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    justify-content: center;
    align-items: center;
    z-index: 9999;
    display: none;
    transition: opacity 0.3s ease;
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

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
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

/* Responsive spinner size */
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

/* Document status badges */
.document-status-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    border: 2px solid white;
}

.badge-new {
    background-color: #8b5cf6;
    color: white;
    width: 24px;
}

.badge-good {
    background-color: #10b981;
    color: white;
}

.badge-bad {
    background-color: #ef4444;
    color: white;
}

.badge-updated {
    background-color: #f59e0b;
    color: white;
}

.document-item-wrapper {
    position: relative;
}

/* Responsive document viewer */
@media (max-width: 768px) {
    #documentViewerModal .bg-white {
        margin: 1rem;
        max-height: calc(100vh - 2rem);
    }
    
    #documentViewer {
        min-height: 300px;
    }
    
    .document-status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

@media (max-width: 480px) {
    #documentViewerModal .bg-white {
        margin: 0.5rem;
        max-height: calc(100vh - 1rem);
        border-radius: 1rem;
    }
    
    #documentViewer {
        min-height: 250px;
    }
    
    #documentReviewControls .flex.gap-3 {
        flex-direction: column;
    }
    
    #documentReviewControls button {
        width: 100%;
    }
}
   
    </style>

</head>
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
                            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
</div>
<body class="bg-gray-50">
    <div class="dashboard-grid">
        <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                
                <!-- Notification Bell with Improved System -->
                <div class="relative">
                    <button id="notifBell" class="relative focus:outline-none">
                        <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                        @if($badgeCount > 0)
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center transition-all duration-300">
                                {{ $badgeCount }}
                            </span>
                        @else
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center hidden transition-all duration-300"></span>
                        @endif
                    </button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b font-semibold text-gray-700 flex justify-between items-center">
                            <span>Notifications</span>
                            <button id="markAllRead" class="text-xs text-blue-600 hover:text-blue-800 transition-colors">Mark all as read</button>
                        </div>
                        <ul id="notifList" class="max-h-60 overflow-y-auto">
                            @forelse($notifications as $notif)
                            <li class="px-4 py-2 hover:bg-gray-50 text-sm border-b transition-colors">
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
        
        <div class="flex flex-1 overflow-hidden"> 
            <div class="w-20 md:w-80 bg-white shadow-md flex flex-col transition-all duration-300">
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
                  <li>
                            <a href="/lydo_staff/walk_in" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="fas fa-walking text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Walk-in Applicants</span>
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
            
            <div class="flex-1 overflow-hidden p-4 md:p-2 text-[16px] content-scrollable">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">Scholar Renewal Review</h2>
                    </div>

                    <div class="flex flex-col md:flex-row items-center mb-6 gap-4">
                        <div class="flex gap-2">
                            <div onclick="showTable()" class="tab active" id="tab-renewal">
                                <i class="fas fa-table mr-1"></i> Process Renewals
                            </div>
                            <div onclick="showList()" class="tab" id="tab-review">
                                <i class="fas fa-list mr-1"></i> View Status
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
    
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifBell = document.getElementById('notifBell');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifCount = document.getElementById('notifCount');
    const markAllRead = document.getElementById('markAllRead');
    const notificationSound = document.getElementById('notificationSound');
    
    let notificationCheckInterval;
    let lastBadgeCount = {{ $badgeCount }};
    let isDropdownOpen = false;

    // Function to check for new notifications
    function checkNewNotifications() {
        fetch('{{ route("lydo_staff.notification_counts") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.badge_count > lastBadgeCount) {
                    // New notification arrived
                    playNotificationSound();
                    updateBadgeCount(data.badge_count);
                    lastBadgeCount = data.badge_count;
                    
                    // Show visual pulse effect
                    notifBell.classList.add('animate-pulse');
                    setTimeout(() => {
                        notifBell.classList.remove('animate-pulse');
                    }, 2000);
                } else if (data.badge_count !== lastBadgeCount) {
                    // Count changed (decreased)
                    updateBadgeCount(data.badge_count);
                    lastBadgeCount = data.badge_count;
                }
            })
            .catch(error => console.error('Error checking notifications:', error));
    }

    // Function to play notification sound
    function playNotificationSound() {
        if (notificationSound) {
            notificationSound.currentTime = 0;
            notificationSound.play().catch(e => console.log('Audio play failed:', e));
        }
    }

    // Function to update badge count
    function updateBadgeCount(count) {
        if (notifCount) {
            if (count > 0) {
                notifCount.textContent = count;
                notifCount.classList.remove('hidden');
                notifCount.classList.add('flex');
            } else {
                notifCount.classList.add('hidden');
                notifCount.classList.remove('flex');
            }
        }
    }

    // Toggle notification dropdown
    notifBell.addEventListener('click', function(e) {
        e.stopPropagation();
        isDropdownOpen = !notifDropdown.classList.contains('hidden');
        
        if (!isDropdownOpen) {
            // Opening dropdown - mark as read
            notifDropdown.classList.remove('hidden');
            markNotificationsAsRead();
            isDropdownOpen = true;
        } else {
            // Closing dropdown
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Mark all as read button
    if (markAllRead) {
        markAllRead.addEventListener('click', function(e) {
            e.stopPropagation();
            markNotificationsAsRead();
        });
    }

    // Function to mark notifications as read
    function markNotificationsAsRead() {
        fetch('{{ route("lydo_staff.mark_notifications_read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateBadgeCount(0);
                lastBadgeCount = 0;
                
                // Add visual feedback
                notifCount.classList.add('scale-0');
                setTimeout(() => {
                    notifCount.classList.remove('scale-0');
                }, 300);
            }
        })
        .catch(error => console.error('Error marking notifications as read:', error));
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!notifBell.contains(event.target) && !notifDropdown.contains(event.target)) {
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Close dropdown on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && isDropdownOpen) {
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Start checking for new notifications every 5 seconds
    notificationCheckInterval = setInterval(checkNewNotifications, 2000);

    // Initial check
    checkNewNotifications();

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(notificationCheckInterval);
    });

    // Handle page visibility changes (stop checking when tab is not active)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(notificationCheckInterval);
        } else {
            notificationCheckInterval = setInterval(checkNewNotifications, 5000);
            // Check immediately when returning to tab
            checkNewNotifications();
        }
    });
});

// Real-time updates using Laravel Echo and Pusher (if you have it set up)
document.addEventListener('DOMContentLoaded', function() {
    // If you have Laravel Echo configured for real-time updates
    @if(config('broadcasting.default') !== 'log')
    window.Echo.channel('lydo-staff-updates')
        .listen('.applicant.updated', (e) => {
            if (e.type === 'pending_initial') {
                // Trigger immediate notification check
                checkNewNotifications();
            }
        })
        .listen('.renewal.updated', (e) => {
            if (e.type === 'pending_renewals') {
                // Trigger immediate notification check
                checkNewNotifications();
            }
        });
    @endif
});
</script>
    <script src="{{ asset('js/renewal.js') }}"></script>
    <script src="{{ asset('js/spinner.js') }}"></script>
    <script src="{{ asset('js/logout.js') }}"></script>
    <script src="{{ asset('js/renewal_paginate.js') }}"></script>
    <script src="{{ asset('js/renewal_refresh.js') }}"></script>
</body>

</html>