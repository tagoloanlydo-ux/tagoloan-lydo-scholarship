<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>
<style>
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
    animation: fadeIn 1s ease forwards;
}
/* Center the pagination container */
.pagination-container {
    display: flex;
    justify-content: center; /* Center the content */
    align-items: center;
    margin: 1.5rem auto 0 auto; /* Center horizontally */
    padding: 1rem;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    max-width: fit-content; /* Only take as much width as needed */
}

/* For mobile responsiveness */
@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
        width: 100%;
        max-width: 100%;
    }
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .pagination-buttons {
        order: -1;
    }
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
/* Center the pagination container */
.pagination-container {
    display: flex;
    justify-content: center; /* Center the content */
    align-items: center;
    margin: 0 auto; /* Remove top margin, center horizontally */
    padding: 1rem;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    max-width: fit-content; /* Only take as much width as needed */
}

/* For mobile responsiveness */
@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
        width: 100%;
        max-width: 100%;
    }
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .pagination-buttons {
        order: -1;
    }
}
</style>
<body class="bg-gray-50">
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
        <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        imageUrl: 'https://cdn-icons-png.flaticon.com/512/190/190411.png',
        imageWidth: 80,
        imageHeight: 80,
        imageAlt: 'Success image',
        showConfirmButton: false,
        timer: 2500
    });
</script>
@endif
            
                <div class="dashboard-grid">
                    <!-- Header -->
            <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
                        <div class="flex items-center">
                            <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                            <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} Lydo Admin</span>  
                    </header>
                    <!-- Main Content -->
                <div class="flex flex-1">
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

            <li>
                <a href="/lydo_admin/applicants" 
                class=" flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
                    <div class="flex items-center">
                        <i class="bx bxs-user text-center mx-auto md:mx-0 text-xl"></i>
                        <span class="ml-4 hidden md:block text-lg">Applicants</span>
                    </div>
                </a>
            </li>

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
                    class="flex items-center p-2 rounded-lg text-black-700 bg-violet-600 text-white">
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
                <a href="/lydo_admin/announcement"
                class=" flex items-center justify-between p-3 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
                    <div class="flex items-center">
                        <i class="bx bxs-megaphone text-center mx-auto md:mx-0 text-xl"></i>
                        <span class="ml-4 hidden md:block text-lg">Announcement</span>
                    </div>
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
                        <div class="flex-1 overflow-auto p-4 md:p-5 text-[16px]">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-800">Scholar Status Management</h2> <!-- Smaller text -->
                            </div>

                            <!-- Tabs Navigation -->
                            <div class="bg-white p-3 rounded-lg shadow-sm mb-4"> <!-- Reduced padding and margin -->
                                <div class="border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8">
                                        <button id="withoutRenewalTab" class="tab-button py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600" data-tab="withoutRenewal">
                                            Without Renewal Applications
                                        </button>
                                        <button id="graduatingTab" class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="graduating">
                                            Graduating Scholars
                                        </button>
                                    </nav>
                                </div>
                            </div>

                    <!-- Tab Content: Without Renewal Applications -->
                    <div id="withoutRenewalContent" class="tab-content">
                        @if($showRenewalSection)
                            <div class="note-box mb-4">
                                <h4>📋 Scholars Without Renewal Applications</h4>
                                <p class="text-sm text-black-600 mb-3">
                                    This section displays active scholars who haven't submitted their renewal applications for 
                                    <strong>{{ $renewalInfo['semester'] }}</strong>. 
                                    Renewal period: 
                                    <strong>{{ $renewalInfo['start_date']->format('M d, Y') }}</strong> to 
                                    <strong>{{ $renewalInfo['deadline']->format('M d, Y') }}</strong>.
                                </p>
                                
                                @if($renewalInfo['is_after_grace_period'])
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                        <h5 class="font-semibold text-red-800 mb-2">✅ Automatic Status Update Completed</h5>
                                        <p class="text-sm text-red-700">
                                            The grace period ended on <strong>{{ $renewalInfo['grace_period_end']->format('M d, Y') }}</strong>. 
                                            Scholars who haven't submitted renewal applications have been automatically set to inactive status.
                                        </p>
                                        @if(session('auto_update_info'))
                                            <p class="text-sm text-green-700 mt-2">
                                                <i class="fas fa-check-circle"></i> {{ session('auto_update_info') }}
                                            </p>
                                        @endif
                                    </div>
                                @elseif($renewalInfo['is_grace_period'])
                                    @php
                                        $daysRemaining = now()->diffInDays($renewalInfo['grace_period_end'], false);
                                    @endphp
                                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-3">
                                        <h5 class="font-semibold text-orange-800 mb-2">⏰ Grace Period Active</h5>
                                        <p class="text-sm text-orange-700">
                                            Renewal deadline has passed. Scholars have 
                                            <strong>{{ $daysRemaining }} {{ str_plural('day', $daysRemaining) }}</strong> 
                                            remaining in the grace period (until {{ $renewalInfo['grace_period_end']->format('M d, Y') }}) 
                                            before automatic status update to inactive.
                                        </p>
                                    </div>
                                @else
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-3">
                                        <h5 class="font-semibold text-amber-800 mb-2">⚠️ Required Actions:</h5>
                                        <ul class="text-sm text-amber-700 space-y-1">
                                            <li>• Send reminder emails to scholars about pending renewal applications</li>
                                            <li>• Update status to inactive for scholars who fail to renew</li>
                                            <li>• Copy names for reporting or follow-up purposes</li>
                                        </ul>
                                    </div>

                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <h5 class="font-semibold text-blue-800 mb-2">📝 Quick Steps:</h5>
                                        <ol class="text-sm text-blue-700 space-y-2">
                                            <li class="flex items-start">
                                                <span class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 flex-shrink-0">1</span>
                                                <span>Select scholars using checkboxes</span>
                                            </li>
                                            <li class="flex items-start">
                                                <span class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 flex-shrink-0">2</span>
                                                <span>Use "Email" to send renewal reminders</span>
                                            </li>
                                            <li class="flex items-start">
                                                <span class="bg-blue-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 flex-shrink-0">3</span>
                                                <span>Use "Update Status" to set as inactive if needed</span>
                                            </li>
                                        </ol>
                                    </div>
                                @endif
                            </div>
                            <!-- Without Renewal Tab - Filter Section -->
                            <div class="bg-white p-3 rounded-lg shadow-sm mb-4">
                                <div class="flex flex-col md:flex-row gap-3">
                                    <div class="flex-1">
                                        <input type="text" id="withoutRenewalSearch" placeholder="Search by name..." 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 placeholder-gray-500 text-sm">
                                    </div>
                                    <div class="flex-1">
                                        <select id="withoutRenewalBarangay" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                            <option value="">All Barangays</option>
                                            @foreach($barangays as $barangay)
                                                <option value="{{ $barangay }}">{{ $barangay }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                    <!-- Scholars without renewal applications -->
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-md font-semibold">
                                Active Scholars Without Renewal Applications
                                @if($scholarsWithoutRenewal->count() > 0)
                                    <span class="text-sm font-normal text-gray-500">({{ $scholarsWithoutRenewal->total() }} total)</span>
                                @endif
                            </h3>
                            <div class="flex space-x-2">
                                <button type="button" id="copyNamesBtn" class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden text-sm">
                                    Copy Names
                                </button>
                                <button type="button" id="sendEmailBtn" class="bg-violet-600 text-white px-3 py-1 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden text-sm">
                                    Email
                                </button>
                                <button type="button" id="updateStatusBtn" class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden text-sm">
                                    Update Status
                                </button>
                            </div>
                        </div>

                        @if($scholarsWithoutRenewal->count() > 0)
                        <form id="scholarForm" action="{{ route('LydoAdmin.updateScholarStatus') }}" method="POST">
                            @csrf

                            <div class="overflow-x-auto">
                                <table class="w-full table-auto border-collapse text-[15px] shadow-lg overflow-hidden border border-gray-200">
                                    <thead class="bg-violet-600 to-teal-600 text-white uppercase text-sm">
                                        <tr>
                                            <th class="px-3 py-2 border border-gray-200 text-center">
                                                <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                            </th>
                                            <th class="px-3 py-2 border align-middle border-gray-200 text-center">Full Name</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">Barangay</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">Email</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">School</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">Year Level</th>
                                        </tr>
                                    </thead>
                                    <tbody id="withoutRenewalTableBody">
                                        @foreach($scholarsWithoutRenewal as $scholar)
                                        <tr class="scholar-row hover:bg-gray-50 border-b">
                                            <td class="px-3 border border-gray-200 py-1 text-center">
                                                <input type="checkbox" name="selected_scholars[]" value="{{ $scholar->scholar_id }}" data-scholar-id="{{ $scholar->scholar_id }}" class="scholar-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            </td>
                                            <td class="px-3 border border-gray-200 py-1 text-center">
                                                {{ $scholar->full_name }}
                                            </td>
                                            <td class="px-3 border border-gray-200 py-1 text-center barangay-cell">{{ $scholar->applicant_brgy }}</td>
                                            <td class="px-3 border border-gray-200 py-1 text-center">{{ $scholar->applicant_email }}</td>
                                            <td class="px-3 border border-gray-200 py-1 text-center">{{ $scholar->applicant_school_name }}</td>
                                            <td class="px-3 border border-gray-200 py-1 text-center">{{ $scholar->applicant_year_level }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-4 py-3 bg-white border-t border-gray-200 ">
                                <div class="flex justify-center">
                                    <div class="pagination-container">
                                        <div class="pagination-info" id="paginationInfo">
                                            Showing page {{ $scholarsWithoutRenewal->currentPage() }} of {{ $scholarsWithoutRenewal->lastPage() }}
                                        </div>
                                        <div class="pagination-buttons">
                                            @if($scholarsWithoutRenewal->onFirstPage())
                                                <button class="pagination-btn" id="prevPage" disabled>
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                            @else
                                                <a href="{{ $scholarsWithoutRenewal->previousPageUrl() }}" class="pagination-btn">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            @endif
                                            
                                            <div class="pagination-page-info">
                                                Page 
                                                <input type="number" class="pagination-page-input" id="currentPage" value="{{ $scholarsWithoutRenewal->currentPage() }}" min="1" max="{{ $scholarsWithoutRenewal->lastPage() }}">
                                                of <span id="totalPages">{{ $scholarsWithoutRenewal->lastPage() }}</span>
                                            </div>
                                            
                                            @if($scholarsWithoutRenewal->hasMorePages())
                                                <a href="{{ $scholarsWithoutRenewal->nextPageUrl() }}" class="pagination-btn">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            @else
                                                <button class="pagination-btn" id="nextPage" disabled>
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @else
                        <div class="text-center py-6 text-gray-500">
                            @if($renewalInfo['is_after_grace_period'])
                                <p>All scholars without renewal applications have been automatically set to inactive status.</p>
                            @else
                                <p>No active scholars found without renewal applications for {{ $renewalInfo['semester'] }}.</p>
                            @endif
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8 bg-white rounded-lg shadow-sm">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 max-w-md mx-auto">
                            <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Active Renewal Period</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                @if($settings && $settings->renewal_start_date)
                                    The renewal period for {{ $settings->renewal_semester }} starts on 
                                    <strong>{{ \Carbon\Carbon::parse($settings->renewal_start_date)->format('M d, Y') }}</strong>.
                                @else
                                    No renewal dates are currently set in the system settings.
                                @endif
                            </p>
                            <a href="/lydo_admin/settings" class="inline-flex items-center px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors text-sm">
                                <i class="fas fa-cog mr-2"></i>
                                Configure Renewal Settings
                            </a>
                        </div>
                    </div>
                @endif
            </div>
                            <!-- Tab Content: Graduating Scholars -->
                            <div id="graduatingContent" class="tab-content hidden">
                                <div class="note-box mb-4">
                    <h4>🎓 Graduating Scholars Management</h4>
                    <p class="text-sm text-black-600 mb-3">
                        This section displays scholars who are in their final year or semester and approaching graduation.
                    </p>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                        <h5 class="font-semibold text-green-800 mb-2">✅ Graduation Process:</h5>
                        <ul class="text-sm text-green-700 space-y-1">
                            <li>• Verify completion of all academic requirements</li>
                            <li>• Mark scholars as graduated once they complete their programs</li>
                            <li>• Generate graduation certificates for completed scholars</li>
                            <li>• Send congratulatory emails or notifications</li>
                        </ul>
                    </div>

                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                        <h5 class="font-semibold text-purple-800 mb-2">📝 Quick Steps:</h5>
                        <ol class="text-sm text-purple-700 space-y-2">
                            <li class="flex items-start">
                                <span class="bg-purple-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 flex-shrink-0">1</span>
                                <span>Select graduating scholars using checkboxes</span>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-purple-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 flex-shrink-0">2</span>
                                <span>Use "Mark as Graduated" to update their status</span>
                            </li>
                            <li class="flex items-start">
                                <span class="bg-purple-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs mr-2 flex-shrink-0">3</span>
                                <span>Generate certificates and send congratulatory messages</span>
                            </li>
                        </ol>
                    </div>
                </div>


            <!-- Graduating Tab - Filter Section -->
            <div class="bg-white p-3 rounded-lg shadow-sm mb-4">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" id="graduatingSearch" placeholder="Search by name..." 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 placeholder-gray-500 text-sm">
                    </div>
                    <div class="flex-1">
                        <select id="graduatingBarangay" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay }}">{{ $barangay }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
                    <!-- Graduating Scholars -->
                    <div class="p-4 bg-white rounded-lg shadow-sm"> <!-- Reduced padding -->
                        <div class="flex justify-between items-center mb-3"> <!-- Reduced margin -->
                            <h3 class="text-md font-semibold">Graduating Scholars</h3> <!-- Smaller text -->
                            <div class="flex space-x-2">
                                <button type="button" id="graduatingCopyNamesBtn" class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden text-sm"> <!-- Smaller button -->
                                    Copy Names
                                </button>
                                <button type="button" id="graduatingSendEmailBtn" class="bg-green-600 text-white px-3 py-1 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden text-sm"> <!-- Smaller button -->
                                    Email
                                </button>
                                <button type="button" id="markAsGraduatedBtn" class="bg-purple-600 text-white px-3 py-1 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:bg-gray-400 disabled:cursor-not-allowed hidden text-sm"> <!-- Smaller button -->
                                    Mark as Graduated
                                </button>
                            </div>
                        </div>

                        @if(isset($graduatingScholars) && $graduatingScholars->count() > 0)
                        <form id="graduatingScholarForm" action="{{ route('LydoAdmin.markAsGraduated') }}" method="POST">
                            @csrf

                            <div class="overflow-x-auto">
                                <table class="w-full table-auto border-collapse text-[15px] shadow-lg overflow-hidden border border-gray-200"> <!-- Smaller text -->
                                    <thead class="bg-green-600 text-white uppercase text-sm">
                                        <tr>
                                            <th class="px-3 py-2 border border-gray-200 text-center"> <!-- Reduced padding -->
                                                <input type="checkbox" id="graduatingSelectAll" class="rounded border-gray-300">
                                            </th>
                                            <th class="px-3 py-2 border align-middle border-gray-200 text-center">Name</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">Barangay</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">Email</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">School</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">Year Level</th>
                                            <th class="px-3 py-2 border border-gray-200 align-middle text-center">Course</th>
                                        </tr>
                                    </thead>
                                    <tbody id="graduatingTableBody">
                                        @foreach($graduatingScholars as $scholar)
                                        <tr class="graduating-scholar-row hover:bg-gray-50 border-b">
                                            <td class="px-3 border border-gray-200 py-1 text-center"> <!-- Reduced padding -->
                                                <input type="checkbox" name="selected_graduating_scholars[]" value="{{ $scholar->scholar_id }}" data-scholar-id="{{ $scholar->scholar_id }}" class="graduating-scholar-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            </td>
                                            <td class="px-3 border border-gray-200 py-1 text-center"> <!-- Reduced padding -->
                                                {{ $scholar->full_name }}
                                                @if($scholar->applicant_suffix)
                                                    {{ $scholar->applicant_suffix }}
                                                @endif
                                            </td>
                                            <td class="px-3 border border-gray-200 py-1 text-center graduating-barangay-cell">{{ $scholar->applicant_brgy }}</td>
                                            <td class="px-3 border border-gray-200 py-1 text-center">{{ $scholar->applicant_email }}</td>
                                            <td class="px-3 border border-gray-200 py-1 text-center">{{ $scholar->applicant_school_name }}</td>
                                            <td class="px-3 border border-gray-200 py-1 text-center">
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                                    {{ $scholar->applicant_year_level }}
                                                </span>
                                            </td>
                                            <td class="px-3 border border-gray-200 py-1 text-center">{{ $scholar->applicant_course }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-4 py-3 bg-white border-t border-gray-200">
                                <div class="flex justify-center">
                                    <div class="pagination-container">
                                        <div class="pagination-info" id="graduatingPaginationInfo">
                                            Showing page {{ $graduatingScholars->currentPage() ?? 1 }} of {{ $graduatingScholars->lastPage() ?? 1 }}
                                        </div>
                                        <div class="pagination-buttons">
                                            @if(isset($graduatingScholars) && $graduatingScholars->onFirstPage())
                                                <button class="pagination-btn" id="graduatingPrevPage" disabled>
                                                    <i class="fas fa-chevron-left"></i>
                                                </button>
                                            @else
                                                <a href="{{ $graduatingScholars->previousPageUrl() ?? '#' }}" class="pagination-btn" id="graduatingPrevPage">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            @endif
                                            
                                            <div class="pagination-page-info">
                                                Page 
                                                <input type="number" class="pagination-page-input" id="graduatingCurrentPage" value="{{ $graduatingScholars->currentPage() ?? 1 }}" min="1" max="{{ $graduatingScholars->lastPage() ?? 1 }}">
                                                of <span id="graduatingTotalPages">{{ $graduatingScholars->lastPage() ?? 1 }}</span>
                                            </div>
                                            
                                            @if(isset($graduatingScholars) && $graduatingScholars->hasMorePages())
                                                <a href="{{ $graduatingScholars->nextPageUrl() ?? '#' }}" class="pagination-btn" id="graduatingNextPage">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            @else
                                                <button class="pagination-btn" id="graduatingNextPage" disabled>
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @else
                        <div class="text-center py-6 text-gray-500"> <!-- Reduced padding -->
                            <p>No graduating scholars found.</p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Email Modal -->
            <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-2xl rounded-xl bg-white"> <!-- Reduced top margin -->
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200"> <!-- Reduced margin -->
                        <h3 class="text-lg font-bold text-gray-900 flex items-center"> <!-- Smaller text -->
                            <i class="fas fa-envelope text-blue-600 mr-2"></i>
                            Send Email
                        </h3>
                        <button type="button" id="closeEmailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-lg"></i> <!-- Smaller icon -->
                        </button>
                    </div>

                    <form id="emailForm" method="POST" action="{{ route('LydoAdmin.sendEmail') }}" class="space-y-4"> <!-- Reduced spacing -->
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Recipients</label> <!-- Reduced margin -->
                            <input type="text" id="emailTo" name="email" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-medium text-sm"> <!-- Smaller text -->
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Subject</label> <!-- Reduced margin -->
                            <input type="text" name="subject" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" placeholder="Enter email subject"> <!-- Smaller text -->
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Message</label> <!-- Reduced margin -->
                            <textarea name="message" required rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical text-sm" placeholder="Enter your message"></textarea> <!-- Smaller text -->
                        </div>

                        <input type="hidden" id="scholarId" name="scholar_id" value="">
                        <input type="hidden" name="email_type" value="plain">

                        <!-- Loading Indicator -->
                        <div id="emailLoading" class="hidden flex items-center justify-center py-3"> <!-- Reduced padding -->
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div> <!-- Smaller spinner -->
                            <span class="ml-2 text-sm text-gray-600 font-medium">Sending email...</span> <!-- Smaller text -->
                        </div>

                        <div class="flex justify-end space-x-3 pt-3 border-t border-gray-200"> <!-- Reduced spacing -->
                            <button type="button" id="cancelEmail" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors text-sm"> <!-- Smaller button -->
                                Cancel
                            </button>
                            <button type="submit" id="sendEmailButton" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm"> <!-- Smaller button -->
                                <i class="fas fa-paper-plane mr-1"></i>Send Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>

 <script>
// Simple Tab Switching - Fixed Version
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing tabs');
    
    // Simple tab switching
    document.getElementById('withoutRenewalTab').addEventListener('click', function(e) {
        e.preventDefault();
        switchToTab('withoutRenewal');
    });

    document.getElementById('graduatingTab').addEventListener('click', function(e) {
        e.preventDefault();
        switchToTab('graduating');
    });

    function switchToTab(tabName) {
        console.log('Switching to tab:', tabName);
        
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(tab => {
            tab.classList.remove('border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        const targetContent = document.getElementById(tabName + 'Content');
        if (targetContent) {
            targetContent.classList.remove('hidden');
            console.log('Showing content for:', tabName);
        }
        
        // Activate selected tab
        const targetTab = document.getElementById(tabName + 'Tab');
        if (targetTab) {
            targetTab.classList.add('border-blue-500', 'text-blue-600');
            targetTab.classList.remove('border-transparent', 'text-gray-500');
        }
        
        // Show all rows for the active tab
        if (tabName === 'withoutRenewal') {
            showAllScholarRows();
        } else if (tabName === 'graduating') {
            showAllGraduatingRows();
        }
        
        // Update button states
        updateButtons();
        updateGraduatingButtons();
    }

    function showAllScholarRows() {
        const rows = document.querySelectorAll('#withoutRenewalTableBody .scholar-row');
        rows.forEach(row => {
            row.style.display = '';
        });
        console.log('Showing all scholar rows:', rows.length);
    }

    function showAllGraduatingRows() {
        const rows = document.querySelectorAll('#graduatingTableBody .graduating-scholar-row');
        rows.forEach(row => {
            row.style.display = '';
        });
        console.log('Showing all graduating rows:', rows.length);
    }

    // Your existing JavaScript code for regular scholars
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.scholar-checkbox');
    const copyNamesBtn = document.getElementById('copyNamesBtn');
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const updateStatusBtn = document.getElementById('updateStatusBtn');

    // Graduating scholars elements
    const graduatingSelectAll = document.getElementById('graduatingSelectAll');
    const graduatingCheckboxes = document.querySelectorAll('.graduating-scholar-checkbox');
    const graduatingCopyNamesBtn = document.getElementById('graduatingCopyNamesBtn');
    const graduatingSendEmailBtn = document.getElementById('graduatingSendEmailBtn');
    const markAsGraduatedBtn = document.getElementById('markAsGraduatedBtn');

    // Email modal elements
    const emailModal = document.getElementById('emailModal');
    const closeEmailModal = document.getElementById('closeEmailModal');
    const cancelEmail = document.getElementById('cancelEmail');
    const emailForm = document.getElementById('emailForm');
    const emailTo = document.getElementById('emailTo');
    const scholarId = document.getElementById('scholarId');

    // Select all checkbox functionality for regular scholars
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateButtons();
        });
    }

    // Select all checkbox functionality for graduating scholars
    if (graduatingSelectAll) {
        graduatingSelectAll.addEventListener('change', function() {
            const visibleCheckboxes = document.querySelectorAll('.graduating-scholar-checkbox');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateGraduatingButtons();
        });
    }

// Update button states for regular scholars (pagination-aware)
function updateButtons() {
    const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
    const visibleRows = document.querySelectorAll('.scholar-row:not([style*="display: none"])');
    const selectedCount = Array.from(visibleCheckboxes).filter(cb => cb.checked && cb.closest('tr').style.display !== 'none').length;
    
    if (copyNamesBtn) copyNamesBtn.disabled = selectedCount === 0;
    if (sendEmailBtn) sendEmailBtn.disabled = selectedCount === 0;
    if (updateStatusBtn) updateStatusBtn.disabled = selectedCount === 0;
    if (copyNamesBtn) copyNamesBtn.classList.toggle('hidden', selectedCount === 0);
    if (sendEmailBtn) sendEmailBtn.classList.toggle('hidden', selectedCount === 0);
    if (updateStatusBtn) updateStatusBtn.classList.toggle('hidden', selectedCount === 0);
}

// Update button states for graduating scholars (pagination-aware)
function updateGraduatingButtons() {
    const visibleCheckboxes = document.querySelectorAll('.graduating-scholar-checkbox');
    const visibleRows = document.querySelectorAll('.graduating-scholar-row:not([style*="display: none"])');
    const selectedCount = Array.from(visibleCheckboxes).filter(cb => cb.checked && cb.closest('tr').style.display !== 'none').length;
    
    if (graduatingCopyNamesBtn) graduatingCopyNamesBtn.disabled = selectedCount === 0;
    if (graduatingSendEmailBtn) graduatingSendEmailBtn.disabled = selectedCount === 0;
    if (markAsGraduatedBtn) markAsGraduatedBtn.disabled = selectedCount === 0;
    if (graduatingCopyNamesBtn) graduatingCopyNamesBtn.classList.toggle('hidden', selectedCount === 0);
    if (graduatingSendEmailBtn) graduatingSendEmailBtn.classList.toggle('hidden', selectedCount === 0);
    if (markAsGraduatedBtn) markAsGraduatedBtn.classList.toggle('hidden', selectedCount === 0);
}
    // Update button states for graduating scholars
    function updateGraduatingButtons() {
        const visibleCheckboxes = document.querySelectorAll('.graduating-scholar-checkbox');
        const selectedCount = Array.from(visibleCheckboxes).filter(cb => cb.checked).length;
        if (graduatingCopyNamesBtn) graduatingCopyNamesBtn.disabled = selectedCount === 0;
        if (graduatingSendEmailBtn) graduatingSendEmailBtn.disabled = selectedCount === 0;
        if (markAsGraduatedBtn) markAsGraduatedBtn.disabled = selectedCount === 0;
        if (graduatingCopyNamesBtn) graduatingCopyNamesBtn.classList.toggle('hidden', selectedCount === 0);
        if (graduatingSendEmailBtn) graduatingSendEmailBtn.classList.toggle('hidden', selectedCount === 0);
        if (markAsGraduatedBtn) markAsGraduatedBtn.classList.toggle('hidden', selectedCount === 0);
    }

    // Individual checkbox change for regular scholars
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('scholar-checkbox')) {
            updateButtons();

            // Update selectAll checkbox state
            const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
            const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(visibleCheckboxes).some(cb => cb.checked);

            if (selectAll) {
                selectAll.checked = allChecked;
                selectAll.indeterminate = someChecked && !allChecked;
            }
        }
    });

    // Individual checkbox change for graduating scholars
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('graduating-scholar-checkbox')) {
            updateGraduatingButtons();

            // Update selectAll checkbox state
            const visibleCheckboxes = document.querySelectorAll('.graduating-scholar-checkbox');
            const allChecked = Array.from(visibleCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(visibleCheckboxes).some(cb => cb.checked);

            if (graduatingSelectAll) {
                graduatingSelectAll.checked = allChecked;
                graduatingSelectAll.indeterminate = someChecked && !allChecked;
            }
        }
    });

    // Copy Names button functionality for regular scholars
    if (copyNamesBtn) {
        copyNamesBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
            const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'No Selection!',
                    text: 'Please select at least one scholar to copy names.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Group selected scholars by barangay
            const barangayGroups = {};
            selectedCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const name = row.querySelector('td:nth-child(2)').textContent.trim();
                const barangay = row.querySelector('td.barangay-cell')
                    ? row.querySelector('td.barangay-cell').textContent.trim()
                    : '';
                if (!barangayGroups[barangay]) {
                    barangayGroups[barangay] = [];
                }
                barangayGroups[barangay].push(name);
            });

            // Build the output string
            let output = '';
            Object.keys(barangayGroups).forEach(barangay => {
                output += `${barangay}\n`;
                barangayGroups[barangay].forEach((name, idx) => {
                    output += `${idx + 1}. ${name}\n`;
                });
                output += '\n';
            });

            navigator.clipboard.writeText(output.trim()).then(() => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Selected scholar names grouped by barangay copied to clipboard!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }).catch(err => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to copy names: ' + err,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // Copy Names button functionality for graduating scholars
    if (graduatingCopyNamesBtn) {
        graduatingCopyNamesBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.graduating-scholar-checkbox');
            const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'No Selection!',
                    text: 'Please select at least one graduating scholar to copy names.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Group selected scholars by barangay
            const barangayGroups = {};
            selectedCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const name = row.querySelector('td:nth-child(2)').textContent.trim();
                const barangay = row.querySelector('td.graduating-barangay-cell')
                    ? row.querySelector('td.graduating-barangay-cell').textContent.trim()
                    : '';
                if (!barangayGroups[barangay]) {
                    barangayGroups[barangay] = [];
                }
                barangayGroups[barangay].push(name);
            });

            // Build the output string
            let output = '';
            Object.keys(barangayGroups).forEach(barangay => {
                output += `${barangay}\n`;
                barangayGroups[barangay].forEach((name, idx) => {
                    output += `${idx + 1}. ${name}\n`;
                });
                output += '\n';
            });

            navigator.clipboard.writeText(output.trim()).then(() => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Selected graduating scholar names grouped by barangay copied to clipboard!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }).catch(err => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to copy names: ' + err,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // Send Email button functionality for regular scholars
    if (sendEmailBtn) {
        sendEmailBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
            const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'No Selection!',
                    text: 'Please select at least one scholar to send email.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Collect selected emails and scholar IDs
            let emails = [];
            let scholarIds = [];

            selectedCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const email = row.querySelector('td:nth-child(4)').textContent.trim();
                emails.push(email);
                scholarIds.push(checkbox.getAttribute('data-scholar-id'));
            });

            emailTo.value = emails.join(', ');
            scholarId.value = scholarIds.join(',');

            // Show modal
            emailModal.classList.remove('hidden');
        });
    }

    // Send Email button functionality for graduating scholars
    if (graduatingSendEmailBtn) {
        graduatingSendEmailBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.graduating-scholar-checkbox');
            const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'No Selection!',
                    text: 'Please select at least one graduating scholar to send email.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Collect selected emails and scholar IDs
            let emails = [];
            let scholarIds = [];

            selectedCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const email = row.querySelector('td:nth-child(4)').textContent.trim();
                emails.push(email);
                scholarIds.push(checkbox.getAttribute('data-scholar-id'));
            });

            emailTo.value = emails.join(', ');
            scholarId.value = scholarIds.join(',');

            // Show modal
            emailModal.classList.remove('hidden');
        });
    }

    // Close modal functionality
    if (closeEmailModal) {
        closeEmailModal.addEventListener('click', function() {
            emailModal.classList.add('hidden');
        });
    }

    if (cancelEmail) {
        cancelEmail.addEventListener('click', function() {
            emailModal.classList.add('hidden');
        });
    }

    // Close modal on outside click
    emailModal.addEventListener('click', function(e) {
        if (e.target === emailModal) {
            emailModal.classList.add('hidden');
        }
    });

    // Email form submission
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(emailForm);
            const submitButton = emailForm.querySelector('#sendEmailButton');
            const loadingIndicator = document.getElementById('emailLoading');

            // Show loading
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
            loadingIndicator.classList.remove('hidden');

            fetch(emailForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    emailModal.classList.add('hidden');
                    emailForm.reset();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Hide loading
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Email';
                loadingIndicator.classList.add('hidden');
            });
        });
    }

    // Update Status button functionality for regular scholars
    if (updateStatusBtn) {
        updateStatusBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.scholar-checkbox');
            const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'No Selection!',
                    text: 'Please select at least one scholar to update status.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Confirm bulk update
            Swal.fire({
                title: 'Confirm Update',
                text: `Are you sure you want to set inactive this account?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Update',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    const form = document.getElementById('scholarForm');
                    const submitButton = updateStatusBtn;

                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';

                    form.submit();
                }
            });
        });
    }

    // Mark as Graduated button functionality
    if (markAsGraduatedBtn) {
        markAsGraduatedBtn.addEventListener('click', function() {
            const visibleCheckboxes = document.querySelectorAll('.graduating-scholar-checkbox');
            const selectedCheckboxes = Array.from(visibleCheckboxes).filter(cb => cb.checked);

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'No Selection!',
                    text: 'Please select at least one scholar to mark as graduated.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Confirm graduation
            Swal.fire({
                title: 'Confirm Graduation',
                text: `Are you sure you want to mark ${selectedCheckboxes.length} scholar(s) as graduated?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Mark as Graduated',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    const form = document.getElementById('graduatingScholarForm');
                    const submitButton = markAsGraduatedBtn;

                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                    form.submit();
                }
            });
        });
    }

    // Initialize button states
    updateButtons();
    updateGraduatingButtons();

    // Initialize filtering functionality
    initializeFiltering();
});

// Initialize filtering functionality
function initializeFiltering() {
    // Add event listeners for without renewal tab
    const withoutRenewalSearch = document.getElementById('withoutRenewalSearch');
    const withoutRenewalBarangay = document.getElementById('withoutRenewalBarangay');
    
    if (withoutRenewalSearch) {
        withoutRenewalSearch.addEventListener('input', debounce(filterWithoutRenewalTable, 300));
    }
    if (withoutRenewalBarangay) {
        withoutRenewalBarangay.addEventListener('change', filterWithoutRenewalTable);
    }
    
    // Add event listeners for graduating tab
    const graduatingSearch = document.getElementById('graduatingSearch');
    const graduatingBarangay = document.getElementById('graduatingBarangay');
    
    if (graduatingSearch) {
        graduatingSearch.addEventListener('input', debounce(filterGraduatingTable, 300));
    }
    if (graduatingBarangay) {
        graduatingBarangay.addEventListener('change', filterGraduatingTable);
    }
}

// Filter Without Renewal Applications table
function filterWithoutRenewalTable() {
    const searchTerm = document.getElementById('withoutRenewalSearch').value.toLowerCase();
    const selectedBarangay = document.getElementById('withoutRenewalBarangay').value;

    const rows = document.querySelectorAll('#withoutRenewalTableBody .scholar-row');

    rows.forEach(row => {
        const nameCell = row.cells[1]; // Name column
        const barangayCell = row.querySelector('td.barangay-cell'); // Barangay column

        if (!nameCell || !barangayCell) return;

        const name = nameCell.textContent.toLowerCase().trim();
        const barangay = barangayCell.textContent.trim();

        const nameMatch = name.includes(searchTerm);
        const barangayMatch = !selectedBarangay || barangay === selectedBarangay;

        if (nameMatch && barangayMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Update button states after filtering
    updateButtons();
}

// Filter Graduating Scholars table
function filterGraduatingTable() {
    const searchTerm = document.getElementById('graduatingSearch').value.toLowerCase();
    const selectedBarangay = document.getElementById('graduatingBarangay').value;

    const rows = document.querySelectorAll('#graduatingTableBody .graduating-scholar-row');

    rows.forEach(row => {
        const nameCell = row.cells[1]; // Name column
        const barangayCell = row.querySelector('td.graduating-barangay-cell'); // Barangay column

        if (!nameCell || !barangayCell) return;

        const name = nameCell.textContent.toLowerCase().trim();
        const barangay = barangayCell.textContent.trim();

        const nameMatch = name.includes(searchTerm);
        const barangayMatch = !selectedBarangay || barangay === selectedBarangay;

        if (nameMatch && barangayMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Update button states after filtering
    updateGraduatingButtons();
}

// Debounce function for search input
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

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
 <script src="{{ asset('js/spinner.js') }}"></script>
  <script>
// Handle page navigation for without renewal table
document.addEventListener('DOMContentLoaded', function() {
    const currentPageInput = document.getElementById('currentPage');
    if (currentPageInput) {
        currentPageInput.addEventListener('change', function() {
            const page = parseInt(this.value);
            const totalPages = parseInt(document.getElementById('totalPages').textContent);
            const baseUrl = window.location.href.split('?')[0];
            
            if (page >= 1 && page <= totalPages) {
                window.location.href = `${baseUrl}?page=${page}`;
            } else {
                this.value = {{ $scholarsWithoutRenewal->currentPage() }};
            }
        });
    }
    
    // Handle page navigation for graduating table
    const graduatingCurrentPageInput = document.getElementById('graduatingCurrentPage');
    if (graduatingCurrentPageInput) {
        graduatingCurrentPageInput.addEventListener('change', function() {
            const page = parseInt(this.value);
            const totalPages = parseInt(document.getElementById('graduatingTotalPages').textContent);
            const baseUrl = window.location.href.split('?')[0];
            
            if (page >= 1 && page <= totalPages) {
                window.location.href = `${baseUrl}?graduating_page=${page}`;
            } else {
                this.value = {{ $graduatingScholars->currentPage() ?? 1 }};
            }
        });
    }
});
</script>
</body>

</html>