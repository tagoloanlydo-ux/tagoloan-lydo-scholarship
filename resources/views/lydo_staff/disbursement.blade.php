<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Disbursement Records - Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <audio id="notificationSound" src="{{ asset('notification/blade.wav') }}" preload="auto"></audio>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>
  <style>
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
/* Pagination Styles */
.pagination-container {
    margin-top: 1px;
    margin-bottom: 10px;
    display: flex;
    justify-content: center;
}

.pagination-container button {
    transition: all 0.2s ease;
    margin: 0 0.25rem;
    border: 1px solid #d1d5db;
}

.pagination-container button:hover {
    background-color: #e5e7eb;
    border-color: #9ca3af;
}

.pagination-container .active {
    background-color: #7c3aed !important;
    color: white !important;
    border-color: #7c3aed !important;
}
/* Status badges for renewal */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-approved {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.status-rejected {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.status-pending {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
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

/* Pagination Styles */
.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 1rem 0;
}

.pagination li {
    margin: 0 0.25rem;
}

.pagination li a,
.pagination li span {
    display: block;
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination li a:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
}

.pagination li.active span {
    background-color: #7c3aed;
    border-color: #7c3aed;
    color: white;
}

.pagination li.disabled span {
    color: #9ca3af;
    cursor: not-allowed;
}

/* Tab Styles */
.tab-button {
    transition: all 0.3s ease;
}

.active-tab {
    border-color: #7c3aed !important;
    color: #7c3aed !important;
}

/* Custom Tab Styles */
.tab {
    cursor: pointer;
    padding: 14px 28px;
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    background: linear-gradient(145deg, #ffffff, #f8fafc);
    color: #64748b;
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
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: white;
    border-color: #7c3aed;
    box-shadow: 0 4px 20px rgba(124, 58, 237, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    transform: translateY(-2px) scale(1.02);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.tab:hover:not(.active) {
    background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
    border-color: #a78bfa;
    color: #7c3aed;
    transform: translateY(-1px) scale(1.01);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.tab:active {
    transform: translateY(0) scale(0.98);
    transition: all 0.1s ease;
}
   
</style>

<body class="bg-gray-50">
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
        <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
</div>
@php
    $badgeCount = ($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0) ? $notifications->where('initial_screening', 'Approved')->count() : 0;
@endphp
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
                            <a href="/lydo_staff/dashboard" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
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
                            <a href="/lydo_staff/renewal" class="flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Renewals</span>
                                </div>
                                @if($pendingRenewals > 0)
                                    <span class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                        {{ $pendingRenewals }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/disbursement" class="flex items-center p-3 rounded-lg text-gray-700 bg-violet-600 text-white">
                                <i class="bx bx-wallet text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Disbursement</span>
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
            <div class="flex-1 main-content-area p-4 md:p-2 text-[16px]">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <!-- Disbursement Tabs -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Disbursement Records</h2>

                        <!-- Tab Navigation -->
                        <div class="flex gap-2 mb-6">
                            <div onclick="showUnsignedTab()" class="tab active" id="tab-unsigned">
                                <i class="fas fa-table mr-1"></i> Pending Signature
                                @if($unsignedDisbursements->count() > 0)
                                    <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unsignedDisbursements->count() }}</span>
                                @endif
                            </div>
                            <div onclick="showSignedTab()" class="tab" id="tab-signed">
                                <i class="fas fa-list mr-1"></i> Signed
                                @if($signedDisbursements->count() > 0)
                                    <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $signedDisbursements->count() }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Pending Signature Tab -->
                        <div id="unsignedTabContent" class="tab-content">
                            <!-- Search and Filter Section for Unsigned Tab -->
                            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
                                <div class="flex gap-4 items-end">
                                    <div class="flex gap-4">
                                        <!-- Search by Name -->
                                        <div>
                                            <label for="unsignedNameSearch" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                                            <div class="relative">
                                                <input type="text" id="unsignedNameSearch" placeholder="Enter name..."
                                                    style="padding: 0.75rem 2.5rem; width: 20rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; transition: all 0.2s; background-color: white;"
                                                    onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.2)'; this.style.outline='none'"
                                                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                            </div>
                                        </div>

                                        <!-- Filter by Barangay -->
                                        <div>
                                            <label for="unsignedBarangayFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                                            <select id="unsignedBarangayFilter"
                                                style="padding: 0.75rem 2.5rem; width: 16rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; transition: all 0.2s; background-color: white; appearance: none; background-image: url('data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3e%3cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27m6 8 4 4 4-4%27/%3e%3c/svg%3e'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                                                onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.2)'; this.style.outline='none'"
                                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                                <option value="">All Barangays</option>
                                                @foreach($barangays as $barangay)
                                                    <option value="{{ $barangay }}">{{ $barangay }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($unsignedDisbursements->count() > 0)
                                <div class="overflow-hidden border border-gray-200 shadow-lg">
                                    <div class="overflow-y-auto">
                                        <table class="w-full table-fixed border-collapse text-[17px]">
                                            <thead class="bg-violet-600 text-white uppercase text-sm sticky top-0 z-10">
                                                <tr>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-center">Semester</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-canter">Academic Year</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-canter">Amount</th>
                                                   <th class="w-1/6 px-4 py-3 border border-gray-200 text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($unsignedDisbursements as $disburse)
                                                    <tr class="hover:bg-gray-50 border-b" data-id="{{ $disburse->disburse_id }}">
                                                        <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">{{ $disburse->full_name }}</td>
                                                        <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">{{ $disburse->applicant_brgy }}</td>
                                                        <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">{{ $disburse->disburse_semester }}</td>
                                                        <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">{{ $disburse->disburse_acad_year }}</td>
                                                        <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">₱{{ number_format($disburse->disburse_amount, 2) }}</td>

                                                        <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">
                                                            <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm" onclick="openSignatureModal({{ $disburse->disburse_id }})">
                                                                Sign Application
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="pagination-container" id="unsignedPagination"></div>
                                    </div>
                                </div>

                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500 text-lg">No unsigned disbursement records found.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Signed Tab -->
                    <div id="signedTabContent" class="tab-content" style="display: none;">
                        <!-- Search and Filter Section for Signed Tab -->
                        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
                            <div class="flex gap-4 items-end">
                                <div class="flex gap-4">
                                    <!-- Search by Name -->
                                    <div>
                                        <label for="signedNameSearch" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                                        <div class="relative">
                                            <input type="text" id="signedNameSearch" placeholder="Enter name..."
                                                style="padding: 0.75rem 2.5rem; width: 20rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; transition: all 0.2s; background-color: white;"
                                                onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.2)'; this.style.outline='none'"
                                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                        </div>
                                    </div>

                                    <!-- Filter by Barangay -->
                                    <div>
                                        <label for="signedBarangayFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                                        <select id="signedBarangayFilter"
                                            style="padding: 0.75rem 2.5rem; width: 16rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; transition: all 0.2s; background-color: white; appearance: none; background-image: url('data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3e%3cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27m6 8 4 4 4-4%27/%3e%3c/svg%3e'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                                            onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.2)'; this.style.outline='none'"
                                            onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                                            <option value="">All Barangays</option>
                                            @foreach($barangays as $barangay)
                                                <option value="{{ $barangay }}">{{ $barangay }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($signedDisbursements->count() > 0)
                            <div class="overflow-hidden border border-gray-200 shadow-lg">
                                <div class="overflow-y-auto">
                                    <table class="w-full table-fixed border-collapse text-[17px]">
                                        <thead class="bg-green-600 to-teal-600 text-white uppercase text-sm sticky top-0 z-10">
                                            <tr>
                                                <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Full Name</th>
                                                <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Barangay</th>
                                                <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Semester</th>
                                                <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Academic Year</th>
                                                <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Amount</th>
                                                <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Signature</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($signedDisbursements as $disburse)
                                                <tr class="hover:bg-gray-50 border-b">
                                                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">{{ $disburse->full_name }}</td>
                                                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">{{ $disburse->applicant_brgy }}</td>
                                                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">{{ $disburse->disburse_semester }}</td>
                                                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">{{ $disburse->disburse_acad_year }}</td>
                                                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">₱{{ number_format($disburse->disburse_amount, 2) }}</td>
                                                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">
                                                        @if($disburse->disburse_signature)
                                                            <img src="{{ $disburse->disburse_signature }}" 
                                                                alt="Signature" 
                                                                class="max-w-20 max-h-12 mx-auto border border-gray-300 rounded">
                                                        @else
                                                            <span class="text-gray-400 text-sm">No signature</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="pagination-container" id="signedPagination"></div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 text-lg">No signed disbursement records found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    // Tab switching functionality
    function showUnsignedTab() {
        document.getElementById('tab-unsigned').classList.add('active');
        document.getElementById('tab-signed').classList.remove('active');
        document.getElementById('unsignedTabContent').style.display = 'block';
        document.getElementById('signedTabContent').style.display = 'none';
        disbursementPagination.renderUnsignedPage();
    }

    function showSignedTab() {
        document.getElementById('tab-signed').classList.add('active');
        document.getElementById('tab-unsigned').classList.remove('active');
        document.getElementById('signedTabContent').style.display = 'block';
        document.getElementById('unsignedTabContent').style.display = 'none';
        disbursementPagination.renderSignedPage();
    }

    // Filter functions
    function clearUnsignedFilters() {
        document.getElementById('unsignedNameSearch').value = '';
        document.getElementById('unsignedBarangayFilter').value = '';
        disbursementPagination.filterUnsignedData();
    }

    function clearSignedFilters() {
        document.getElementById('signedNameSearch').value = '';
        document.getElementById('signedBarangayFilter').value = '';
        disbursementPagination.filterSignedData();
    }

    // Initialize filter event listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('unsignedNameSearch').addEventListener('input', function() {
            disbursementPagination.filterUnsignedData();
        });

        document.getElementById('unsignedBarangayFilter').addEventListener('change', function() {
            disbursementPagination.filterUnsignedData();
        });

        document.getElementById('signedNameSearch').addEventListener('input', function() {
            disbursementPagination.filterSignedData();
        });

        document.getElementById('signedBarangayFilter').addEventListener('change', function() {
            disbursementPagination.filterSignedData();
        });
    });
</script>

@if($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0)
<script>
    if (localStorage.getItem('notificationsViewed') !== 'true') {
        const audio = new Audio('/notification/blade.wav');
        audio.play().catch(e => console.log('Audio play failed', e));
    }
</script>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('notificationsViewed') === 'true') {
            let notifCount = document.getElementById("notifCount");
            if (notifCount) {
                notifCount.style.display = 'none';
            }
        }
    });
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

    <!-- Signature Modal -->
    <div id="signatureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-full overflow-y-auto">
            <div class="p-5">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sign Application</h3>
                <div class="border-2 border-gray-300 rounded-lg p-4 mb-4">
                    <canvas id="signatureCanvas" width="400" height="300" class="border border-gray-300 w-full"></canvas>
                </div>
                <div class="flex justify-between">
                    <button id="clearSignature" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Clear</button>
                    <div>
                        <button id="cancelSignature" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mr-2">Cancel</button>
                        <button id="saveSignature" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Save Signature</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Signature Modal -->
    <div id="viewSignatureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">View Signature</h3>
                <div class="border-2 border-gray-300 rounded-lg p-4 text-center">
                    <img id="signatureImage" src="" alt="Signature" class="max-w-full h-auto">
                </div>
                <div class="flex justify-end mt-4">
                    <button id="closeViewSignature" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let signaturePad;
        let currentDisburseId;

        function openSignatureModal(disburseId) {
            currentDisburseId = disburseId;
            document.getElementById('signatureModal').classList.remove('hidden');

            const canvas = document.getElementById('signatureCanvas');
            signaturePad = new SignaturePad(canvas);
        }

        document.getElementById('clearSignature').addEventListener('click', function() {
            signaturePad.clear();
        });

        document.getElementById('cancelSignature').addEventListener('click', function() {
            signaturePad.clear();
            document.getElementById('signatureModal').classList.add('hidden');
        });

        document.getElementById('saveSignature').addEventListener('click', function() {
            if (signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Signature',
                    text: 'Please provide a signature before saving.',
                });
                return;
            }

            const signatureData = signaturePad.toDataURL();

            // Create a form to submit the signature
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/lydo_staff/sign-disbursement/' + currentDisburseId;

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input'); // ✅ Fixed variable name
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput); // ✅ Fixed variable name
            }

            const signatureInput = document.createElement('input');
            signatureInput.type = 'hidden';
            signatureInput.name = 'signature';
            signatureInput.value = signatureData;
            form.appendChild(signatureInput);

            document.body.appendChild(form);
            
            // Show loading state
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }
            
            form.submit();
        });
    </script>
    <script>
    // Handle "View Signature" button clicks
    document.querySelectorAll('.view-sig-btn').forEach(button => {
        button.addEventListener('click', function () {
            const signatureUrl = this.getAttribute('data-signature');
            const imgElement = document.getElementById('signatureImage');

            if (signatureUrl) {
                imgElement.src = signatureUrl;
            } else {
                imgElement.src = '';
            }

            document.getElementById('viewSignatureModal').classList.remove('hidden');
        });
    });

    // Close the view signature modal
    document.getElementById('closeViewSignature').addEventListener('click', function () {
        document.getElementById('viewSignatureModal').classList.add('hidden');
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifBell = document.getElementById('notifBell');
        const notifDropdown = document.getElementById('notifDropdown');

        notifBell.addEventListener('click', function() {
            notifDropdown.classList.toggle('hidden');
            localStorage.setItem('notificationsViewed', 'true');
            let notifCount = document.getElementById("notifCount");
            if (notifCount) {
                notifCount.style.display = 'none';
            }
        });

        document.addEventListener('click', function(event) {
            if (!notifBell.contains(event.target) && !notifDropdown.contains(event.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    });
</script>
<script>
    document.getElementById('logoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'flex';

        setTimeout(() => {
            loadingOverlay.classList.add('fade-out');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 1000);
        }, 1000);
    });
</script>
<script>
// Pagination and Filtering for Disbursement
const disbursementPagination = {
    itemsPerPage: 10,
    currentUnsignedPage: 1,
    currentSignedPage: 1,
    filteredUnsignedData: @json($unsignedDisbursements),
    filteredSignedData: @json($signedDisbursements),

    init() {
        this.renderUnsignedPage();
        this.renderSignedPage();
    },

    // Unsigned Tab Functions
    filterUnsignedData() {
        const nameSearch = document.getElementById('unsignedNameSearch').value.toLowerCase();
        const barangayFilter = document.getElementById('unsignedBarangayFilter').value;

        this.filteredUnsignedData = @json($unsignedDisbursements).filter(item => {
            const matchesName = item.full_name.toLowerCase().includes(nameSearch);
            const matchesBarangay = !barangayFilter || item.applicant_brgy === barangayFilter;
            return matchesName && matchesBarangay;
        });

        this.currentUnsignedPage = 1;
        this.renderUnsignedPage();
    },

    renderUnsignedPage() {
        const startIndex = (this.currentUnsignedPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const pageData = this.filteredUnsignedData.slice(startIndex, endIndex);
        
        this.renderUnsignedTable(pageData);
        this.renderUnsignedPagination();
    },

    renderUnsignedTable(data) {
        const tbody = document.querySelector('#unsignedTabContent tbody');
        if (!tbody) return;

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">
                        No unsigned disbursement records found.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = data.map((disburse, index) => `
            <tr class="hover:bg-gray-50 border-b" data-id="${disburse.disburse_id}">
                <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">${disburse.full_name}</td>
                <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">${disburse.applicant_brgy}</td>
                <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">${disburse.disburse_semester}</td>
                <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">${disburse.disburse_acad_year}</td>
                <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">₱${parseFloat(disburse.disburse_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">
                    <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm" onclick="openSignatureModal(${disburse.disburse_id})">
                        Sign Application
                    </button>
                </td>
            </tr>
        `).join('');
    },

    renderUnsignedPagination() {
        const totalPages = Math.ceil(this.filteredUnsignedData.length / this.itemsPerPage);
        const container = document.getElementById('unsignedPagination');
        
        if (!container || totalPages <= 1) {
            if (container) container.innerHTML = '';
            return;
        }

        container.innerHTML = this.createPaginationHTML(this.currentUnsignedPage, totalPages, 'unsigned');
    },

    // Signed Tab Functions
    filterSignedData() {
        const nameSearch = document.getElementById('signedNameSearch').value.toLowerCase();
        const barangayFilter = document.getElementById('signedBarangayFilter').value;

        this.filteredSignedData = @json($signedDisbursements).filter(item => {
            const matchesName = item.full_name.toLowerCase().includes(nameSearch);
            const matchesBarangay = !barangayFilter || item.applicant_brgy === barangayFilter;
            return matchesName && matchesBarangay;
        });

        this.currentSignedPage = 1;
        this.renderSignedPage();
    },

    renderSignedPage() {
        const startIndex = (this.currentSignedPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        const pageData = this.filteredSignedData.slice(startIndex, endIndex);
        
        this.renderSignedTable(pageData);
        this.renderSignedPagination();
    },

        renderSignedTable(data) {
            const tbody = document.querySelector('#signedTabContent tbody');
            if (!tbody) return;

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">
                            No signed disbursement records found.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map((disburse, index) => `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">${disburse.full_name}</td>
                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">${disburse.applicant_brgy}</td>
                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">${disburse.disburse_semester}</td>
                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">${disburse.disburse_acad_year}</td>
                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">₱${parseFloat(disburse.disburse_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="w-1/6 px-4 border border-gray-200 py-2 text-center">
                        ${disburse.disburse_signature ? 
                            `<img src="${disburse.disburse_signature}" alt="Signature" class="max-w-20 max-h-12 mx-auto border border-gray-300 rounded">` : 
                            `<span class="text-gray-400 text-sm">No signature</span>`
                        }
                    </td>
                </tr>
            `).join('');
        },

    renderSignedPagination() {
        const totalPages = Math.ceil(this.filteredSignedData.length / this.itemsPerPage);
        const container = document.getElementById('signedPagination');
        
        if (!container || totalPages <= 1) {
            if (container) container.innerHTML = '';
            return;
        }

        container.innerHTML = this.createPaginationHTML(this.currentSignedPage, totalPages, 'signed');
    },

    // Common Pagination Functions
 // Common Pagination Functions
createPaginationHTML(currentPage, totalPages, type) {
    let html = '<div class="flex justify-center items-center space-x-2 mt-4">';
    
    // Previous button
    if (currentPage > 1) {
        html += `<button onclick="disbursementPagination.goToPage(${currentPage - 1}, '${type}')" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">Previous</button>`;
    }
    
    // Page numbers - limit to show only 5 pages at a time
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    // Adjust start page if we're near the end
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    // Show first page and ellipsis if needed
    if (startPage > 1) {
        html += `<button onclick="disbursementPagination.goToPage(1, '${type}')" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">1</button>`;
        if (startPage > 2) {
            html += `<span class="px-2 text-gray-500">...</span>`;
        }
    }
    
    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            html += `<span class="px-3 py-1 bg-violet-600 text-white rounded font-semibold">${i}</span>`;
        } else {
            html += `<button onclick="disbursementPagination.goToPage(${i}, '${type}')" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">${i}</button>`;
        }
    }
    
    // Show last page and ellipsis if needed
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<span class="px-2 text-gray-500">...</span>`;
        }
        html += `<button onclick="disbursementPagination.goToPage(${totalPages}, '${type}')" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">${totalPages}</button>`;
    }
    
    // Next button
    if (currentPage < totalPages) {
        html += `<button onclick="disbursementPagination.goToPage(${currentPage + 1}, '${type}')" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">Next</button>`;
    }
    
    // Add page info
    html += `<span class="ml-4 text-sm text-gray-600">Page ${currentPage} of ${totalPages}</span>`;
    
    html += '</div>';
    return html;
},

    goToPage(page, type) {
        if (type === 'unsigned') {
            this.currentUnsignedPage = page;
            this.renderUnsignedPage();
        } else {
            this.currentSignedPage = page;
            this.renderSignedPage();
        }
    },

    attachSignatureViewListeners() {
        document.querySelectorAll('.view-sig-btn').forEach(button => {
            button.addEventListener('click', function () {
                const signatureUrl = this.getAttribute('data-signature');
                const imgElement = document.getElementById('signatureImage');

                if (signatureUrl) {
                    imgElement.src = signatureUrl;
                } else {
                    imgElement.src = '';
                }

                document.getElementById('viewSignatureModal').classList.remove('hidden');
            });
        });
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    disbursementPagination.init();
    
    // Add event listeners for filters
    document.getElementById('unsignedNameSearch').addEventListener('input', function() {
        disbursementPagination.filterUnsignedData();
    });

    document.getElementById('unsignedBarangayFilter').addEventListener('change', function() {
        disbursementPagination.filterUnsignedData();
    });

    document.getElementById('signedNameSearch').addEventListener('input', function() {
        disbursementPagination.filterSignedData();
    });

    document.getElementById('signedBarangayFilter').addEventListener('change', function() {
        disbursementPagination.filterSignedData();
    });
});

// Clear filter functions
function clearUnsignedFilters() {
    document.getElementById('unsignedNameSearch').value = '';
    document.getElementById('unsignedBarangayFilter').value = '';
    disbursementPagination.filterUnsignedData();
}

function clearSignedFilters() {
    document.getElementById('signedNameSearch').value = '';
    document.getElementById('signedBarangayFilter').value = '';
    disbursementPagination.filterSignedData();
}

// Tab switching functionality
function showUnsignedTab() {
    document.getElementById('tab-unsigned').classList.add('active');
    document.getElementById('tab-signed').classList.remove('active');
    document.getElementById('unsignedTabContent').style.display = 'block';
    document.getElementById('signedTabContent').style.display = 'none';
    disbursementPagination.renderUnsignedPage();
}

function showSignedTab() {
    document.getElementById('tab-signed').classList.add('active');
    document.getElementById('tab-unsigned').classList.remove('active');
    document.getElementById('signedTabContent').style.display = 'block';
    document.getElementById('unsignedTabContent').style.display = 'none';
    disbursementPagination.renderSignedPage();
}
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
  <script src="{{ asset('js/disburserefresh.js') }}"></script>
  <script src="{{ asset('js/spinner.js') }}"></script>

</body>
</html>