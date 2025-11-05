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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>
  <style>
  /* Pagination Styles */
.pagination-container {
    margin-top: 1rem;
}

.pagination-container button {
    transition: all 0.2s ease;
}

.pagination-container button:disabled {
    cursor: not-allowed;
    opacity: 0.6;
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
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                <div class="relative">
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

        <div class="flex flex-1 overflow-hidden">
            <div class="w-16 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
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
                                    <span class="ml-4 hidden md:block text-lg">Screening</span>
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
            <div class="flex-1 main-content-area p-4 md:p-5 text-[16px]">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">Disbursement Records</h2>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <form id="filterForm" method="GET" action="{{ route('LydoStaff.disbursement') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                           placeholder="Enter name..."
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                    @if(request('search'))
                                        <button type="button" onclick="clearFilters()" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Barangay Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                                <select name="barangay" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">All Barangays</option>
                                    @foreach($barangays as $barangay)
                                        <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                            {{ $barangay }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Year Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Academic Year</label>
                                <select name="academic_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">All Academic Years</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Semester Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Semester</label>
                                <select name="semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                                    <option value="">All Semesters</option>
                                    @foreach($semesters as $semester)
                                        <option value="{{ $semester }}" {{ request('semester') == $semester ? 'selected' : '' }}>
                                            {{ $semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Clear Filters Button -->
                            <div class="flex items-end">
                                <button type="button" onclick="clearFilters()" class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                                    Clear Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Disbursement Tabs -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Disbursement Records</h2>

                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="-mb-px flex space-x-8">
                                <button id="unsignedTab" class="tab-button active-tab whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-violet-500 text-violet-600">
                                    Pending Signature
                                    @if($unsignedDisbursements->count() > 0)
                                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unsignedDisbursements->count() }}</span>
                                    @endif
                                </button>
                                <button id="signedTab" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="signed">
                                    Signed
                                    @if($signedDisbursements->count() > 0)
                                        <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $signedDisbursements->count() }}</span>
                                    @endif
                                </button>
                            </nav>
                        </div>

                        <!-- Pending Signature Tab -->
                        <div id="unsignedTabContent" class="tab-content">
                            @if($unsignedDisbursements->count() > 0)
                                <div class="overflow-hidden border border-gray-200 shadow-lg">
                                    <div class="overflow-y-auto">
                                        <table class="w-full table-fixed border-collapse text-[17px]">
                                            <thead class="bg-gradient-to-r from-red-600 to-orange-600 text-white uppercase text-sm sticky top-0 z-10">
                                                <tr>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Full Name</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Barangay</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Semester</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Academic Year</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Amount</th>
                                                   <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Action</th>
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
                                        <div class="pagination-container" id="tablePagination"></div>
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
                            @if($signedDisbursements->count() > 0)
                                <div class="overflow-hidden border border-gray-200 shadow-lg">
                                    <div class="overflow-y-auto">
                                        <table class="w-full table-fixed border-collapse text-[17px]">
                                            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm sticky top-0 z-10">
                                                <tr>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Full Name</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Barangay</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Semester</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Academic Year</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Amount</th>
                                                    <th class="w-1/6 px-4 py-3 border border-gray-200 text-left">Action</th>
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
                                                            <button class="view-sig-btn bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm" data-signature="{{ $disburse->disburse_signature }}">
                                                                View Signature
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="pagination-container" id="listPagination"></div>
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
    </div>

    <script>
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('active-tab');
                    btn.classList.remove('border-violet-500', 'text-violet-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                // Hide all tab contents
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });

                // Add active class to clicked button
                button.classList.add('active-tab');
                button.classList.remove('border-transparent', 'text-gray-500');
                button.classList.add('border-violet-500', 'text-violet-600');

                // Show corresponding tab content
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId + 'TabContent').style.display = 'block';
            });
        });

        // Auto-filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterInputs = document.querySelectorAll('#filterForm input, #filterForm select');
            
            filterInputs.forEach(input => {
                input.addEventListener('input', function() {
                    document.getElementById('filterForm').submit();
                });
                
                input.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            });
        });

        // Clear filters function
        function clearFilters() {
            const form = document.getElementById('filterForm');
            const inputs = form.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                if (input.type === 'text' || input.type === 'search') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });
            
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('notificationsViewed') === 'true') {
                let notifCount = document.getElementById("notifCount");
                if (notifCount) {
                    notifCount.style.display = 'none';
                }
            }
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
    <div id="signatureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sign Application</h3>
                <div class="border-2 border-gray-300 rounded-lg p-4">
                    <canvas id="signatureCanvas" width="491" height="404" class="border border-gray-300 w-full"></canvas>
                </div>
                <div class="flex justify-between mt-4">
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
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            const signatureInput = document.createElement('input');
            signatureInput.type = 'hidden';
            signatureInput.name = 'signature';
            signatureInput.value = signatureData;
            form.appendChild(signatureInput);

            document.body.appendChild(form);
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

    // Close modal
    document.getElementById('closeViewSignature').addEventListener('click', function () {
        document.getElementById('viewSignatureModal').classList.add('hidden');
    });
</script>
                <script>
                    document.getElementById("notifBell").addEventListener("click", function() {
                        document.getElementById("notifDropdown").classList.toggle("hidden");
                        let notifCount = document.getElementById("notifCount");
                        if (notifCount) {
                            notifCount.remove();
                        }
                    });
                    // Pagination functionality for renewal page
class RenewalPagination {
    constructor() {
        this.tableCurrentPage = 1;
        this.listCurrentPage = 1;
        this.rowsPerPage = 10;
        this.tableData = [];
        this.listData = [];
        
        this.init();
    }

    init() {
        // Get data from the table
        this.loadTableData();
        this.loadListData();
        this.setupEventListeners();
    }

    loadTableData() {
        const rows = document.querySelectorAll('#tableView tbody tr');
        this.tableData = Array.from(rows).map(row => {
            return {
                element: row,
                name: row.cells[1].textContent.trim(),
                barangay: row.cells[2].textContent.trim(),
                isVisible: true
            };
        });
        this.renderTablePage();
    }

    loadListData() {
        const rows = document.querySelectorAll('#listView tbody tr');
        this.listData = Array.from(rows).map(row => {
            return {
                element: row,
                name: row.cells[1].textContent.trim(),
                barangay: row.cells[2].textContent.trim(),
                isVisible: true
            };
        });
        this.renderListPage();
    }

    setupEventListeners() {
        // Search functionality for table view
        const nameSearch = document.getElementById('nameSearch');
        const barangayFilter = document.getElementById('barangayFilter');

        if (nameSearch) {
            nameSearch.addEventListener('input', () => this.filterTableData());
        }

        if (barangayFilter) {
            barangayFilter.addEventListener('change', () => this.filterTableData());
        }

        // Search functionality for list view
        const listNameSearch = document.getElementById('listNameSearch');
        const listBarangayFilter = document.getElementById('listBarangayFilter');

        if (listNameSearch) {
            listNameSearch.addEventListener('input', () => this.filterListData());
        }

        if (listBarangayFilter) {
            listBarangayFilter.addEventListener('change', () => this.filterListData());
        }
    }

    filterTableData() {
        const nameSearch = document.getElementById('nameSearch').value.toLowerCase();
        const barangayFilter = document.getElementById('barangayFilter').value;

        this.tableData.forEach(item => {
            const matchesName = item.name.toLowerCase().includes(nameSearch);
            const matchesBarangay = !barangayFilter || item.barangay === barangayFilter;
            
            item.isVisible = matchesName && matchesBarangay;
        });

        this.tableCurrentPage = 1;
        this.renderTablePage();
    }

    filterListData() {
        const nameSearch = document.getElementById('listNameSearch').value.toLowerCase();
        const barangayFilter = document.getElementById('listBarangayFilter').value;

        this.listData.forEach(item => {
            const matchesName = item.name.toLowerCase().includes(nameSearch);
            const matchesBarangay = !barangayFilter || item.barangay === barangangayFilter;
            
            item.isVisible = matchesName && matchesBarangay;
        });

        this.listCurrentPage = 1;
        this.renderListPage();
    }

    renderTablePage() {
        const visibleData = this.tableData.filter(item => item.isVisible);
        const startIndex = (this.tableCurrentPage - 1) * this.rowsPerPage;
        const endIndex = startIndex + this.rowsPerPage;
        const pageData = visibleData.slice(startIndex, endIndex);

        // Hide all rows
        this.tableData.forEach(item => {
            item.element.style.display = 'none';
        });

        // Show only current page rows
        pageData.forEach(item => {
            item.element.style.display = '';
        });

        this.renderTablePagination(visibleData.length);
    }

    renderListPage() {
        const visibleData = this.listData.filter(item => item.isVisible);
        const startIndex = (this.listCurrentPage - 1) * this.rowsPerPage;
        const endIndex = startIndex + this.rowsPerPage;
        const pageData = visibleData.slice(startIndex, endIndex);

        // Hide all rows
        this.listData.forEach(item => {
            item.element.style.display = 'none';
        });

        // Show only current page rows
        pageData.forEach(item => {
            item.element.style.display = '';
        });

        this.renderListPagination(visibleData.length);
    }

    renderTablePagination(totalItems) {
        const totalPages = Math.ceil(totalItems / this.rowsPerPage);
        const container = document.getElementById('tablePagination');
        
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <div class="flex justify-center items-center space-x-2 mt-4">
                <button onclick="renewalPagination.prevTablePage()" 
                        ${this.tableCurrentPage === 1 ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.tableCurrentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Previous
                </button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === this.tableCurrentPage) {
                paginationHTML += `
                    <button class="px-3 py-1 rounded border bg-violet-600 text-white">
                        ${i}
                    </button>
                `;
            } else {
                paginationHTML += `
                    <button onclick="renewalPagination.goToTablePage(${i})" 
                            class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-50">
                        ${i}
                    </button>
                `;
            }
        }

        paginationHTML += `
                <button onclick="renewalPagination.nextTablePage()" 
                        ${this.tableCurrentPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.tableCurrentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Next
                </button>
            </div>
            <div class="text-center text-sm text-gray-600 mt-2">
                Showing ${Math.min((this.tableCurrentPage - 1) * this.rowsPerPage + 1, totalItems)} to ${Math.min(this.tableCurrentPage * this.rowsPerPage, totalItems)} of ${totalItems} entries
            </div>
        `;

        container.innerHTML = paginationHTML;
    }

    renderListPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / this.rowsPerPage);
        const container = document.getElementById('listPagination');
        
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <div class="flex justify-center items-center space-x-2 mt-4">
                <button onclick="renewalPagination.prevListPage()" 
                        ${this.listCurrentPage === 1 ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.listCurrentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Previous
                </button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === this.listCurrentPage) {
                paginationHTML += `
                    <button class="px-3 py-1 rounded border bg-green-600 text-white">
                        ${i}
                    </button>
                `;
            } else {
                paginationHTML += `
                    <button onclick="renewalPagination.goToListPage(${i})" 
                            class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-50">
                        ${i}
                    </button>
                `;
            }
        }

        paginationHTML += `
                <button onclick="renewalPagination.nextListPage()" 
                        ${this.listCurrentPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.listCurrentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Next
                </button>
            </div>
            <div class="text-center text-sm text-gray-600 mt-2">
                Showing ${Math.min((this.listCurrentPage - 1) * this.rowsPerPage + 1, totalItems)} to ${Math.min(this.listCurrentPage * this.rowsPerPage, totalItems)} of ${totalItems} entries
            </div>
        `;

        container.innerHTML = paginationHTML;
    }

    prevTablePage() {
        if (this.tableCurrentPage > 1) {
            this.tableCurrentPage--;
            this.renderTablePage();
        }
    }

    nextTablePage() {
        const totalPages = Math.ceil(this.tableData.filter(item => item.isVisible).length / this.rowsPerPage);
        if (this.tableCurrentPage < totalPages) {
            this.tableCurrentPage++;
            this.renderTablePage();
        }
    }

    goToTablePage(page) {
        this.tableCurrentPage = page;
        this.renderTablePage();
    }

    prevListPage() {
        if (this.listCurrentPage > 1) {
            this.listCurrentPage--;
            this.renderListPage();
        }
    }

    nextListPage() {
        const totalPages = Math.ceil(this.listData.filter(item => item.isVisible).length / this.rowsPerPage);
        if (this.listCurrentPage < totalPages) {
            this.listCurrentPage++;
            this.renderListPage();
        }
    }

    goToListPage(page) {
        this.listCurrentPage = page;
        this.renderListPage();
    }
}

// Initialize pagination
const renewalPagination = new RenewalPagination();

// Clear filters functions
function clearFiltersTable() {
    document.getElementById('nameSearch').value = '';
    document.getElementById('barangayFilter').value = '';
    renewalPagination.filterTableData();
}

function clearFiltersList() {
    document.getElementById('listNameSearch').value = '';
    document.getElementById('listBarangayFilter').value = '';
    renewalPagination.filterListData();
}
                </script>
    <script src="{{ asset('js/logout.js') }}"></script>
    <script src="{{ asset('js/spinner.js') }}"></script>
    <script src="{{ asset('js/disbursement_filter.js') }}"></script>


</body>

</html>