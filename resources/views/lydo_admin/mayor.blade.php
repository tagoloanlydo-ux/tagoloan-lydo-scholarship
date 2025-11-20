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
        
        /* Modal improvements */
        .modal-enter {
            animation: modalEnter 0.3s ease-out forwards;
        }
        
        .modal-exit {
            animation: modalExit 0.2s ease-in forwards;
        }
        
        @keyframes modalEnter {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        @keyframes modalExit {
            from {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
            to {
                opacity: 0;
                transform: scale(0.9) translateY(-10px);
            }
        }
        
        .info-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .contact-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner">
            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Header -->
        <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Admin</span>            
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
                                       class="flex items-center p-2 rounded-lg text-white-700 bg-violet-600 text-white">
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
                        </li>

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
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm"> 
                        @csrf 
                        <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                            <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                            <span class="hidden md:block text-red-600">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="flex-1 overflow-hidden p-4 md:p-5 text-[17px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Lydo Staff Management</h2>
                </div>

                <div class="p-5">
                    <!-- Tabs -->
                    <div class="flex border-b border-black-700 mb-4">
                        <button onclick="openTab('inactiveLydo')"
                            id="tab-inactiveLydo"
                            class="px-4 py-2 text-blue-600 border-b-2 border-blue-600 focus:outline-none">
                            Inactive LYDO Staff
                        </button>
                        <button onclick="openTab('activeLydo')"
                            id="tab-activeLydo"
                            class="px-4 py-2 text-black-600 hover:text-blue-600 focus:outline-none">
                            Active LYDO Staff
                        </button>
                    </div>

                    <div id="inactiveLydo" class="tab-content hidden">
                        <h2 class="text-lg font-semibold mb-2">Inactive Lydo Staff</h2>
                                <p class="text-sm text-black-600 mb-4">
                                This slideshow displays all LYDO staff members who are currently inactive. 
                                These staff members cannot access the Scholarship Management Account. 
                                Click the 'Activate' button to grant account access. 
                                You can also click any column to view the staff member's personal information.
                                </p>                        
                            <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-violet-600 to-teal-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">ID</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Role</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Created At</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Update Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inactiveStaff as $staff)
                                    <tr class="staff-row hover:bg-gray-50 border-b"
                                        data-id="{{ $staff->lydopers_id }}"
                                        data-fname="{{ $staff->lydopers_fname }}"
                                        data-mname="{{ $staff->lydopers_mname }}"
                                        data-lname="{{ $staff->lydopers_lname }}"
                                        data-suffix="{{ $staff->lydopers_suffix }}"
                                        data-address="{{ $staff->lydopers_address }}"
                                        data-bdate="{{ $staff->lydopers_bdate }}"
                                        data-email="{{ $staff->lydopers_email }}"
                                        data-contact="{{ $staff->lydopers_contact_number }}"
                                        data-username="{{ $staff->lydopers_username }}"
                                        data-role="{{ $staff->lydopers_role }}"
                                        data-status="{{ $staff->lydopers_status }}"
                                        data-created="{{ $staff->created_at }}">
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $staff->lydopers_id }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            {{ ucfirst($staff->lydopers_lname) }}, 
                                            {{ ucfirst($staff->lydopers_fname) }} 
                                            {{ $staff->lydopers_mname ? strtoupper(substr($staff->lydopers_mname, 0, 1)) . '.' : '' }} 
                                            {{ $staff->lydopers_suffix ?? '' }}
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ ucfirst($staff->lydopers_role) }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center text-red-600 font-semibold">
                                            {{ ucfirst($staff->lydopers_status) }}
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center text-gray-600">
                                            {{ \Carbon\Carbon::parse($staff->created_at)->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <button onclick="confirmToggle({{ $staff->lydopers_id }}, 'active')"
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                               Set Active
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">No Inactive Staff Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $inactiveStaff->appends(['active_page' => request('active_page')])->links() }}
                        </div>
                    </div>
                    
                    <div id="activeLydo" class="tab-content mt-6">
                        <h2 class="text-lg font-semibold mb-2">Active Lydo Staff</h2>
                        <p class="text-sm text-black-600 mb-4">This table lists all LYDO staff members who are currently active. These staff members are allowed to use their Scholarship Management Account. Please click the 'Inactivate' button to disable their account access.</p>
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">ID</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Role</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Created At</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Update Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeStaff as $staff)
                                    <tr class="staff-row hover:bg-gray-50 border-b"
                                        data-id="{{ $staff->lydopers_id }}"
                                        data-fname="{{ $staff->lydopers_fname }}"
                                        data-mname="{{ $staff->lydopers_mname }}"
                                        data-lname="{{ $staff->lydopers_lname }}"
                                        data-suffix="{{ $staff->lydopers_suffix }}"
                                        data-address="{{ $staff->lydopers_address }}"
                                        data-bdate="{{ $staff->lydopers_bdate }}"
                                        data-email="{{ $staff->lydopers_email }}"
                                        data-contact="{{ $staff->lydopers_contact_number }}"
                                        data-username="{{ $staff->lydopers_username }}"
                                        data-role="{{ $staff->lydopers_role }}"
                                        data-status="{{ $staff->lydopers_status }}"
                                        data-created="{{ $staff->created_at }}">
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $staff->lydopers_id }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            {{ ucfirst($staff->lydopers_lname) }}, 
                                            {{ ucfirst($staff->lydopers_fname) }} 
                                            {{ $staff->lydopers_mname ? strtoupper(substr($staff->lydopers_mname, 0, 1)) . '.' : '' }} 
                                            {{ $staff->lydopers_suffix ?? '' }}
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">{{ ucfirst($staff->lydopers_role) }}</td>
                                        <td class="px-4 border border-gray-200 py-2 text-center text-green-600 font-semibold">
                                            {{ ucfirst($staff->lydopers_status) }}
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center text-gray-600">
                                            {{ \Carbon\Carbon::parse($staff->created_at)->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <button onclick="confirmToggle({{ $staff->lydopers_id }}, 'inactive')"
                                               class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                               Set Inactive
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">No Active Staff Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $activeStaff->appends(['inactive_page' => request('inactive_page')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Modal Structure -->
    <div id="staffModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white shadow-2xl w-full h-full max-w-6xl max-h-screen overflow-hidden rounded-none md:rounded-xl">
            <div class="flex flex-col h-full">
                <!-- Modal Header -->
                <div class="flex justify-between items-center bg-gradient-to-r from-violet-600 to-purple-700 text-white px-8 py-6 shadow-lg">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <i class="fas fa-user-circle text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">Staff Profile Details</h3>
                            <p class="text-violet-100 text-sm">Complete information overview</p>
                        </div>
                    </div>
                    <button id="closeModal" class="text-white hover:text-gray-200 transition-colors bg-white/10 hover:bg-white/20 p-2 rounded-full">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 overflow-y-auto p-8 bg-gray-50">
                    <div class="max-w-6xl mx-auto">
                        <!-- Profile Header -->
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-6 mb-8 p-6 bg-white rounded-xl shadow-sm border border-gray-100">
                            <div class="flex-shrink-0">
                                <div class="w-24 h-24 bg-gradient-to-r from-violet-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                                    <span id="modal-initials">JD</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h2 id="modal-fullname" class="text-2xl font-bold text-gray-800 mb-2">John Doe</h2>
                                <div class="flex flex-wrap gap-4">
                                    <div class="flex items-center">
                                        <span class="text-gray-600 mr-2">Staff ID:</span>
                                        <span id="modal-id" class="font-mono font-bold text-violet-700">#12345</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-600 mr-2">Role:</span>
                                        <span id="modal-role" class="font-medium capitalize">Admin</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-600 mr-2">Status:</span>
                                        <span id="modal-status-badge" class="status-badge status-active">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left Column: Personal Information -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Personal Information Card -->
                                <div class="info-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-l-blue-500">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h4 class="text-lg font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-user text-blue-500 mr-3"></i>
                                            Personal Information
                                        </h4>
                                    </div>
                                    <div class="p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Full Name</label>
                                                    <p id="modal-fullname-card" class="text-gray-900 font-medium text-base bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Birthdate</label>
                                                    <p id="modal-bdate" class="text-gray-800 text-base bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"></p>
                                                </div>
                                            </div>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Address</label>
                                                    <p id="modal-address" class="text-gray-800 text-base bg-gray-50 px-4 py-3 rounded-lg border border-gray-200 min-h-[60px] flex items-center"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Member Since</label>
                                                    <p id="modal-created" class="text-gray-800 text-base bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Information Card -->
                                <div class="info-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-l-green-500">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h4 class="text-lg font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-cogs text-green-500 mr-3"></i>
                                            Account Information
                                        </h4>
                                    </div>
                                    <div class="p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-600 mb-2">Username</label>
                                                <p id="modal-username" class="text-gray-800 text-base bg-gray-50 px-4 py-3 rounded-lg border border-gray-200 font-medium"></p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-600 mb-2">Account Status</label>
                                                <p id="modal-status" class="text-base bg-gray-50 px-4 py-3 rounded-lg border border-gray-200 capitalize font-medium"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Contact Information -->
                            <div class="space-y-6">
                                <!-- Contact Information Card -->
                                <div class="info-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden border-l-purple-500">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h4 class="text-lg font-bold text-gray-800 flex items-center">
                                            <i class="fas fa-address-book text-purple-500 mr-3"></i>
                                            Contact Information
                                        </h4>
                                    </div>
                                    <div class="p-6 space-y-6">
                                        <div class="flex items-start space-x-4">
                                            <div class="contact-icon bg-purple-100 text-purple-600">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-600 mb-1">Email Address</label>
                                                <p id="modal-email" class="text-gray-800 text-base break-words"></p>
                                            </div>
                                        </div>

                                        <div class="flex items-start space-x-4">
                                            <div class="contact-icon bg-green-100 text-green-600">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-sm font-semibold text-gray-600 mb-1">Contact Number</label>
                                                <p id="modal-contact" class="text-gray-800 text-base font-medium"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Last updated: <span id="modal-updated">Just now</span>
                    </div>
                    <div class="flex space-x-3">
                        <button id="printModalBtn" class="hidden bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg transition-colors flex items-center space-x-2">
                            <i class="fas fa-print"></i>
                            <span>Print</span>
                        </button>
                        <button id="closeModalBtn" class="bg-violet-600 hover:bg-violet-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        // Tab functionality
        function openTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));

            // Remove active styles from all buttons
            document.querySelectorAll('[id^="tab-"]').forEach(btn => {
                btn.classList.remove('text-blue-600', 'border-blue-600');
                btn.classList.add('text-gray-600');
            });

            // Show selected tab content
            document.getElementById(tabId).classList.remove('hidden');

            // Highlight active tab button
            document.getElementById('tab-' + tabId).classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            document.getElementById('tab-' + tabId).classList.remove('text-gray-600');
        }

        // Set default tab on page load
        document.addEventListener("DOMContentLoaded", () => {
            openTab("inactiveLydo"); 
        });

        // Modal functionality
        const modal = document.getElementById('staffModal');
        const closeModal = document.getElementById('closeModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const printModalBtn = document.getElementById('printModalBtn');
        
        // Function to open modal with staff data
        function openStaffModal(staffData) {
            // Populate modal with data
            document.getElementById('modal-id').textContent = staffData.lydopers_id;
            
            const fullName = `${staffData.lydopers_fname} ${staffData.lydopers_mname || ''} ${staffData.lydopers_lname} ${staffData.lydopers_suffix || ''}`.trim();
            document.getElementById('modal-fullname').textContent = fullName;
            document.getElementById('modal-fullname-card').textContent = fullName;
            
            // Set initials for avatar
            const initials = (staffData.lydopers_fname?.charAt(0) || '') + (staffData.lydopers_lname?.charAt(0) || '');
            document.getElementById('modal-initials').textContent = initials || '?';
            
            document.getElementById('modal-bdate').textContent = staffData.lydopers_bdate ? 
                new Date(staffData.lydopers_bdate).toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                }) : 'Not provided';
                
            document.getElementById('modal-address').textContent = staffData.lydopers_address || 'Not provided';
            document.getElementById('modal-email').textContent = staffData.lydopers_email || 'Not provided';
            document.getElementById('modal-contact').textContent = staffData.lydopers_contact_number || 'Not provided';
            document.getElementById('modal-username').textContent = staffData.lydopers_username || 'Not provided';
            
            const role = staffData.lydopers_role ? 
                staffData.lydopers_role.charAt(0).toUpperCase() + staffData.lydopers_role.slice(1) : 'Not provided';
            document.getElementById('modal-role').textContent = role;
            
            const status = staffData.lydopers_status ? 
                staffData.lydopers_status.charAt(0).toUpperCase() + staffData.lydopers_status.slice(1) : 'Not provided';
            document.getElementById('modal-status').textContent = status;
            
            // Set status badge
            const statusBadge = document.getElementById('modal-status-badge');
            statusBadge.textContent = status;
            statusBadge.className = 'status-badge ' + (staffData.lydopers_status === 'active' ? 'status-active' : 'status-inactive');
            
            document.getElementById('modal-created').textContent = staffData.created_at ? 
                new Date(staffData.created_at).toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                }) : 'Not provided';
            
            // Set last updated time
            document.getElementById('modal-updated').textContent = 'Just now';
            
            // Show modal with animation
            modal.classList.remove('hidden');
            modal.classList.add('modal-enter');
            document.body.style.overflow = 'hidden';
        }
        
        // Function to close modal
        function closeStaffModal() {
            modal.classList.add('modal-exit');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('modal-exit', 'modal-enter');
                document.body.style.overflow = 'auto';
            }, 200);
        }
        
        // Print modal content
        printModalBtn.addEventListener('click', () => {
            const modalContent = modal.querySelector('.bg-white').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Staff Profile - ${document.getElementById('modal-fullname').textContent}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .print-header { border-bottom: 2px solid #4c1d95; padding-bottom: 10px; margin-bottom: 20px; }
                        .section { margin-bottom: 20px; }
                        .section-title { background: #f3f4f6; padding: 8px 12px; font-weight: bold; border-left: 4px solid #4c1d95; }
                        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 10px; }
                        .info-item { margin-bottom: 10px; }
                        .label { font-weight: bold; color: #6b7280; }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h1>Staff Profile Details</h1>
                        <p>Generated on ${new Date().toLocaleDateString()}</p>
                    </div>
                    ${modalContent}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        });
        
        // Event listeners for closing modal
        closeModal.addEventListener('click', closeStaffModal);
        closeModalBtn.addEventListener('click', closeStaffModal);
        
        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeStaffModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeStaffModal();
            }
        });
        
        // Make specific table cells clickable and open modal
        document.addEventListener('DOMContentLoaded', function() {
            // Active staff table rows
            const activeRows = document.querySelectorAll('#activeLydo tbody tr.staff-row');
            activeRows.forEach(row => {
                const clickableCells = row.querySelectorAll('td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5)');
                clickableCells.forEach(cell => {
                    cell.classList.add('cursor-pointer', 'hover:bg-gray-50');
                    cell.addEventListener('click', function() {
                        const staffData = {
                            lydopers_id: row.dataset.id,
                            lydopers_fname: row.dataset.fname,
                            lydopers_mname: row.dataset.mname,
                            lydopers_lname: row.dataset.lname,
                            lydopers_suffix: row.dataset.suffix,
                            lydopers_address: row.dataset.address,
                            lydopers_bdate: row.dataset.bdate,
                            lydopers_email: row.dataset.email,
                            lydopers_contact_number: row.dataset.contact,
                            lydopers_username: row.dataset.username,
                            lydopers_role: row.dataset.role,
                            lydopers_status: row.dataset.status,
                            created_at: row.dataset.created
                        };
                        openStaffModal(staffData);
                    });
                });
            });

            // Inactive staff table rows
            const inactiveRows = document.querySelectorAll('#inactiveLydo tbody tr.staff-row');
            inactiveRows.forEach(row => {
                const clickableCells = row.querySelectorAll('td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5)');
                clickableCells.forEach(cell => {
                    cell.classList.add('cursor-pointer', 'hover:bg-gray-50');
                    cell.addEventListener('click', function() {
                        const staffData = {
                            lydopers_id: row.dataset.id,
                            lydopers_fname: row.dataset.fname,
                            lydopers_mname: row.dataset.mname,
                            lydopers_lname: row.dataset.lname,
                            lydopers_suffix: row.dataset.suffix,
                            lydopers_address: row.dataset.address,
                            lydopers_bdate: row.dataset.bdate,
                            lydopers_email: row.dataset.email,
                            lydopers_contact_number: row.dataset.contact,
                            lydopers_username: row.dataset.username,
                            lydopers_role: row.dataset.role,
                            lydopers_status: row.dataset.status,
                            created_at: row.dataset.created
                        };
                        openStaffModal(staffData);
                    });
                });
            });
        });

        // Confirm toggle status
        function confirmToggle(id, action) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to set this staff to ${action}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, set it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/lydo_admin/lydo/toggle/${id}`;
                }
            });
        }

        // Success alert
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
        });
        @endif

        // Logout confirmation
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
    
    <script src="{{ asset('js/spinner.js') }}"></script>
</body>

</html>