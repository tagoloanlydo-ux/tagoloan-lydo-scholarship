<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/admin_scholar.css') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>

<style>
/* Pagination Styles */
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    padding: 1rem;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    max-width: fit-content;
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

.note-box {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.note-box h4 {
    color: #0369a1;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.note-box p {
    color: #0c4a6e;
    font-size: 0.875rem;
}

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

/* Pagination Container - Same as announcement page */
.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    padding: 1rem;
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    max-width: fit-content;
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
        width: 100%;
        max-width: 100%;
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

    <div class="dashboard-grid">
        <!-- Header -->
        <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} Lydo Admin</span>
            </div>
        </header>

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
                        <li class="relative">
                            <button onclick="toggleDropdown('scholarMenu')"
                                class="w-full flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white focus:outline-none">
                                <div class="flex items-center">
                                    <i class="bx bxs-graduation text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Scholar</span>
                                </div>
                                <i class="bx bx-chevron-down ml-2"></i>
                            </button>
                            <ul id="scholarMenu" class="ml-10 mt-2 space-y-2 hidden">
                                <li>
                                    <a href="/lydo_admin/scholar" 
                                    class="flex items-center p-2 rounded-lg text-black-700 bg-violet-600 text-white">
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

            <div class="flex-1 overflow-auto p-4 md:p-5 text-[16px]">
                <div class="note-box">
                    <p>This section displays all scholars currently enrolled in the scholarship program. You can view scholar information, track their academic progress, and manage their scholarship status. Use the filters to find specific scholars by name, barangay, academic year, or status.</p>
                    <p class="mt-2 text-amber-600 font-medium">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Note:</strong> Scholars are categorized by status: Active, Inactive, or Graduated. Use the status filter to view scholars based on their current standing in the program.
                    </p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                    <form method="GET" action="{{ route('LydoAdmin.scholar') }}" id="filterForm">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <input type="text" id="searchInput" name="search" placeholder="Search by name..."
                                    class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black"
                                    value="{{ request('search') }}">
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
                            <!-- Status Filter Dropdown -->
                            <div class="flex-1">
                                <select id="statusSelect" name="status" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active Scholars</option>
                                    <option value="inactive" {{ $statusFilter == 'inactive' ? 'selected' : '' }}>Inactive Scholars</option>
                                    <option value="graduated" {{ $statusFilter == 'graduated' ? 'selected' : '' }}>Graduated Scholars</option>
                                    <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All Scholars</option>
                                </select>
                            </div>
                            
                            <!-- Print to PDF Button -->
                            <div class="flex-1">
                                <button type="button" id="printPdfBtn" class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-file-pdf mr-2"></i>Print to PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Scholars Table -->
                <div class="bg-white shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Lydo Scholar List</h3>
                        <div class="flex space-x-2">
                            <button type="button" id="sendEmailBtn" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                <i class="fas fa-envelope mr-2"></i>Email
                            </button>
                            <button type="button" id="sendSmsBtn" class="px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                <i class="fas fa-comment-alt mr-2"></i>SMS
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                        <thead class="bg-violet-600 to-teal-600 text-white uppercase text-sm">
                            <tr>
                                <th class="px-4 py-3 border border-gray-200 text-center">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                <th class="px-4 py-3 border border-gray-200 text-center">Email</th>
                                <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                <th class="px-4 py-3 border border-gray-200 text-center">Academic Year</th>
                                <th class="px-4 py-3 border border-gray-200 text-center">Personal Info</th>
                                <th class="px-4 py-3 border border-gray-200 text-center">Document</th>
                                <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scholars as $scholar)
                                <tr class="scholar-row hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <input type="checkbox" name="selected_scholars" value="{{ $scholar->applicant_email }}" data-scholar-id="{{ $scholar->scholar_id }}" class="scholar-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $scholar->applicant_lname }}{{ $scholar->applicant_suffix ? ' ' . $scholar->applicant_suffix : '' }}, 
                                            {{ $scholar->applicant_fname }} 
                                            {{ $scholar->applicant_mname ? $scholar->applicant_mname . ' ' : '' }}
                                        </div>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <div class="text-sm text-gray-900">{{ $scholar->applicant_brgy }}</div>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <div class="text-sm text-gray-900">{{ $scholar->applicant_email }}</div>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <div class="text-sm text-gray-900">{{ $scholar->applicant_school_name }}</div>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <div class="text-sm text-gray-900">{{ $scholar->applicant_course }}</div>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <div class="text-sm text-gray-900">{{ $scholar->applicant_acad_year ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors" 
                                                onclick="openPersonalInfoModal({{ $scholar->scholar_id }})">
                                            View Info
                                        </button>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors" onclick="openDocumentModal({{ $scholar->scholar_id }})">
                                            Renewal History
                                        </button>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $scholar->scholar_status == 'active' ? 'bg-green-100 text-green-800' : 
                                            ($scholar->scholar_status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                            ($scholar->scholar_status == 'graduated' ? 'bg-violet-100 text-violet-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($scholar->scholar_status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 border border-gray-200 text-gray-500">No scholars found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination Container -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        <div class="flex justify-center">
                            <div class="pagination-container">
                                <div class="pagination-info" id="paginationInfo">
                                    Showing page 1 of 1
                                </div>
                                <div class="pagination-buttons">
                                    <button class="pagination-btn" id="prevPage" disabled>
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <div class="pagination-page-info">
                                        Page 
                                        <input type="number" class="pagination-page-input" id="currentPage" value="1" min="1">
                                        of <span id="totalPages">1</span>
                                    </div>
                                    <button class="pagination-btn" id="nextPage">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Modal -->
    <div id="documentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-1 mx-auto p-4 md:p-6 border w-full max-w-6xl shadow-2xl rounded-xl bg-white max-h-[98vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                    Scholar Documents
                    <span id="currentDocIndicator" class="ml-3 text-sm font-normal text-gray-600 hidden">
                        (Viewing: <span id="currentDocName" class="font-medium text-blue-600"></span>)
                    </span>
                </h3>
                <div class="flex items-center space-x-2">
                    <button id="fullscreenBtn" class="text-gray-400 hover:text-gray-600 transition-colors p-2" title="Toggle Fullscreen">
                        <i class="fas fa-expand text-lg"></i>
                    </button>
                    <button type="button" id="closeDocumentModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Scholar Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-blue-800 mb-2">Scholar Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm font-medium text-blue-700">Name:</span>
                            <span id="docScholarName" class="text-sm text-blue-900 ml-2">-</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-blue-700">Email:</span>
                            <span id="docScholarEmail" class="text-sm text-blue-900 ml-2">-</span>
                        </div>
                    </div>
                </div>

                <!-- Academic Period Selection -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Select Academic Period</h4>
                    <div class="flex flex-wrap gap-4" id="academicPeriodTabs">
                        <!-- Tabs will be dynamically generated here -->
                    </div>
                </div>

                <!-- Documents Section -->
                <div id="documentsSection">
                    <!-- Documents will be dynamically loaded here based on selected academic period -->
                </div>

                <!-- No Documents Message -->
                <div id="noDocumentsMessage" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center hidden">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                    <h4 class="text-lg font-semibold text-yellow-800 mb-2">No Documents Found</h4>
                    <p class="text-yellow-700">No documents submitted for this academic period.</p>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-200">
                    <button type="button" id="closeDocument" class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Email Modal -->
    <div id="sendEmailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-1 mx-auto p-4 md:p-6 border w-full max-w-4xl shadow-2xl rounded-xl bg-white max-h-[98vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-envelope text-blue-600 mr-3"></i>
                    Send Email to Scholars
                </h3>
                <button type="button" id="closeSendEmailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="sendEmailForm" method="POST">
                @csrf
                <div class="space-y-6">
                    <!-- Selected Scholars -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-blue-800 mb-2">Selected Scholars</h4>
                        <div id="selectedScholarsList" class="text-sm text-blue-700">
                            <!-- Selected scholars will be listed here -->
                        </div>
                        <input type="hidden" name="selected_emails" id="selectedEmailsInput">
                    </div>

                    <!-- Email Type -->
                    <div>
                        <label for="emailType" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select id="emailType" name="email_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="custom">Custom</option>
                            <option value="registration">Registration</option>
                        </select>
                    </div>

                    <!-- Email Subject -->
                    <div>
                        <label for="emailSubject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <input type="text" id="emailSubject" name="subject" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter email subject">
                    </div>

                    <!-- Email Message -->
                    <div>
                        <label for="emailMessage" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="emailMessage" name="message" rows="8" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter your message here..."></textarea>
                    </div>

                    <!-- Send Options -->
                    <div class="bg-gray-50 border hidden border-gray-200 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Send Options</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="send_type" value="bulk" checked class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Send to all selected scholars</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="send_type" value="individual" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Send individual emails to each scholar</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-200 mt-6 space-x-3">
                    <button type="button" id="cancelSendEmail" class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Send SMS Modal -->
    <div id="sendSmsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-1 mx-auto p-4 md:p-6 border w-full max-w-4xl shadow-2xl rounded-xl bg-white max-h-[98vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-comment-alt text-purple-600 mr-3"></i>
                    Send SMS to Scholars
                </h3>
                <button type="button" id="closeSendSmsModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="sendSmsForm" method="POST">
                @csrf
                <div class="space-y-6">
                    <!-- Selected Scholars -->
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-purple-800 mb-2">Selected Scholars</h4>
                        <div id="selectedSmsScholarsList" class="text-sm text-purple-700">
                            <!-- Selected scholars will be listed here -->
                        </div>
                        <input type="hidden" name="selected_emails" id="selectedSmsEmailsInput">
                    </div>

                    <!-- SMS Type Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMS Type</label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="sms_type" value="plain" checked 
                                       class="sms-type-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Plain Text</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="sms_type" value="schedule"
                                       class="sms-type-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Schedule</span>
                            </label>
                        </div>
                    </div>

                    <!-- Schedule Note (Hidden by Default) -->
                    <div id="scheduleNote" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 mb-1">Schedule Notification</h4>
                                <p class="text-sm text-blue-700">
                                    When you send a schedule SMS, the same schedule information will also be sent to scholars via email for their reference.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Message -->
                    <div id="smsMessageContainer">
                        <label for="smsMessage" class="block text-sm font-medium text-gray-700 mb-2">SMS Message</label>
                        <textarea id="smsMessage" name="message" rows="4"  maxlength="160"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="Enter your SMS message (max 160 characters)..."></textarea>
                        <div class="text-sm text-gray-500 mt-1">
                            <span id="smsCharCount">0</span>/160 characters
                        </div>
                    </div>

                    <!-- Schedule Fields (Hidden by Default) -->
                    <div id="scheduleFields" class="hidden mb-4 space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <label for="scheduleWhat" class="block text-sm font-medium text-gray-700 mb-2">What (Event/Activity)</label>
                            <input type="text" id="scheduleWhat" name="schedule_what"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., Scholarship Orientation, Interview">
                        </div>
                        
                        <div>
                            <label for="scheduleWhere" class="block text-sm font-medium text-gray-700 mb-2">Where (Location)</label>
                            <input type="text" id="scheduleWhere" name="schedule_where"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., LYDO Office, City Hall">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="scheduleDate" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                <input type="date" id="scheduleDate" name="schedule_date"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            </div>
                            
                            <div>
                                <label for="scheduleTime" class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                                <input type="time" id="scheduleTime" name="schedule_time"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <!-- SMS Options -->
                    <div class="bg-gray-50 border border-gray-200 hidden rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">SMS Options</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="sms_send_type" value="bulk" checked class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Send to all selected scholars</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="sms_send_type" value="individual" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Send individual SMS to each scholar</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-200 mt-6 space-x-3">
                    <button type="button" id="cancelSendSms" class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Send SMS
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Personal Information Modal -->
    <div id="personalInfoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-1 mx-auto p-4 md:p-6 border w-full max-w-4xl shadow-2xl rounded-xl bg-white max-h-[98vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-circle text-blue-600 mr-3"></i>
                    Scholar Personal Information
                </h3>
                <button type="button" id="closePersonalInfoModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Personal Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Personal Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <p id="infoFullName" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <p id="infoGender" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                            <p id="infoBirthDate" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                            <p id="infoCivilStatus" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                            <p id="infoBarangay" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <p id="infoContact" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Academic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                            <p id="infoSchool" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                            <p id="infoCourse" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                            <p id="infoYearLevel" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                            <p id="infoAcadYear" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                    </div>
                </div>

                <!-- Scholarship Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Scholarship Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Scholar ID</label>
                            <p id="infoScholarId" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <p id="infoScholarStatus" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date Activated</label>
                            <p id="infoDateActivated" class="text-sm text-gray-900 bg-gray-50 p-2 rounded">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                <button type="button" id="closePersonalInfo" class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
    // Global variables for pagination
    const paginationState = {
        currentPage: 1,
        rowsPerPage: 15,
        allRows: [],
        filteredRows: []
    };

    // Function to get full name for sorting
    function getFullNameForSorting(row) {
        const nameCell = row.cells[1];
        if (!nameCell) return '';
        
        const nameDiv = nameCell.querySelector('div');
        if (nameDiv) {
            return nameDiv.textContent.trim().toLowerCase();
        }
        
        return nameCell.textContent.trim().toLowerCase();
    }

    // Function to sort rows alphabetically by last name
    function sortRowsAlphabetically(rows) {
        return rows.sort((a, b) => {
            const nameA = getFullNameForSorting(a);
            const nameB = getFullNameForSorting(b);
            return nameA.localeCompare(nameB);
        });
    }

    // Initialize data from the table
    function initializeScholarData() {
        const tableRows = Array.from(document.querySelectorAll('table tbody tr'));
        paginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
        
        // Sort rows alphabetically by last name
        paginationState.allRows = sortRowsAlphabetically(paginationState.allRows);
        paginationState.filteredRows = [...paginationState.allRows];
    }

    // Initialize pagination
    function initializeScholarPagination() {
        updateScholarPagination();
    }

    function updateScholarPagination() {
        const state = paginationState;
        
        // Hide all rows first
        state.allRows.forEach(row => {
            row.style.display = 'none';
        });
        
        // Calculate pagination for filtered rows
        const startIndex = (state.currentPage - 1) * state.rowsPerPage;
        const endIndex = startIndex + state.rowsPerPage;
        const pageRows = state.filteredRows.slice(startIndex, endIndex);
        
        // Show only rows for current page
        pageRows.forEach(row => {
            row.style.display = '';
        });
        
        // Update pagination controls
        const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
        const startItem = state.filteredRows.length === 0 ? 0 : Math.min((state.currentPage - 1) * state.rowsPerPage + 1, state.filteredRows.length);
        const endItem = Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length);
        
        // Update pagination info
        const paginationInfo = document.getElementById('paginationInfo');
        const currentPageInput = document.getElementById('currentPage');
        const totalPagesSpan = document.getElementById('totalPages');
        const prevPageBtn = document.getElementById('prevPage');
        const nextPageBtn = document.getElementById('nextPage');
        
        if (paginationInfo) {
            paginationInfo.textContent = `Showing page ${state.currentPage} of ${totalPages}`;
        }
        
        if (currentPageInput) {
            currentPageInput.value = state.currentPage;
            currentPageInput.max = totalPages;
        }
        
        if (totalPagesSpan) {
            totalPagesSpan.textContent = totalPages;
        }
        
        if (prevPageBtn) {
            prevPageBtn.disabled = state.currentPage === 1;
        }
        
        if (nextPageBtn) {
            nextPageBtn.disabled = state.currentPage === totalPages || totalPages === 0;
        }
        
        // Reinitialize checkbox listeners and update button/select-all states
        initializeCheckboxSystem();
        updateButtonStates();
        updateSelectAllState();
    }

    // Change page
    function changeScholarPage(page) {
        const state = paginationState;
        const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
        
        if (page < 1) page = 1;
        if (page > totalPages) page = totalPages;
        
        state.currentPage = page;
        updateScholarPagination();
    }

    // Go to specific page
    function goToScholarPage(page) {
        const state = paginationState;
        const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
        
        page = parseInt(page);
        if (isNaN(page) || page < 1) page = 1;
        if (page > totalPages) page = totalPages;
        
        state.currentPage = page;
        updateScholarPagination();
    }

    // Initialize filtering functionality - FIXED VERSION
    function initializeScholarFiltering() {
        const searchInput = document.getElementById('searchInput');
        const barangaySelect = document.getElementById('barangaySelect');
        const academicYearSelect = document.getElementById('academicYearSelect');
        const statusSelect = document.getElementById('statusSelect');

        function filterScholarTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedBarangay = barangaySelect.value;
            const selectedAcademicYear = academicYearSelect.value;
            const selectedStatus = statusSelect.value.toLowerCase();

            const filteredRows = paginationState.allRows.filter(row => {
                const nameCell = row.cells[1];
                const barangayCell = row.cells[2];
                const academicYearCell = row.cells[6];
                const statusCell = row.cells[9]; // Status is in column 9 (0-based index)

                if (!nameCell || !barangayCell || !academicYearCell || !statusCell) return false;

                const name = nameCell.textContent.toLowerCase();
                const barangay = barangayCell.textContent.trim();
                const academicYear = academicYearCell.textContent.trim();
                const statusSpan = statusCell.querySelector('span');
                const status = statusSpan ? statusSpan.textContent.trim().toLowerCase() : '';

                const nameMatch = !searchTerm || name.includes(searchTerm);
                const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
                const academicYearMatch = !selectedAcademicYear || academicYear === selectedAcademicYear;
                const statusMatch = selectedStatus === 'all' || status === selectedStatus;

                return nameMatch && barangayMatch && academicYearMatch && statusMatch;
            });

            // Sort filtered results alphabetically
            const sortedFilteredRows = sortRowsAlphabetically(filteredRows);

            // Update filtered rows and reset to page 1
            paginationState.filteredRows = sortedFilteredRows;
            paginationState.currentPage = 1;
            updateScholarPagination();
            
            // Reset select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            
            // Update button states
            updateButtonStates();
        }

        // Add event listeners with debouncing
        if (searchInput) {
            searchInput.addEventListener('input', debounce(filterScholarTable, 300));
        }
        if (barangaySelect) {
            barangaySelect.addEventListener('change', filterScholarTable);
        }
        if (academicYearSelect) {
            academicYearSelect.addEventListener('change', filterScholarTable);
        }
        if (statusSelect) {
            statusSelect.addEventListener('change', filterScholarTable);
        }

        // Apply initial filters if any
        setTimeout(filterScholarTable, 100);
    }

    // Debounce function for search
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

    // Global variables for checkbox management
    let selectAllCheckbox = null;
    let scholarCheckboxes = [];
    let sendEmailBtn = null;
    let sendSmsBtn = null;

    // Initialize checkbox system
    function initializeCheckboxSystem() {
        selectAllCheckbox = document.getElementById('selectAll');
        scholarCheckboxes = document.querySelectorAll('.scholar-checkbox');
        sendEmailBtn = document.getElementById('sendEmailBtn');
        sendSmsBtn = document.getElementById('sendSmsBtn');

        // Initialize select all checkbox
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', handleSelectAllChange);
        }

        // Initialize individual checkboxes
        scholarCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', handleCheckboxChange);
        });

        // Initial button state update
        updateButtonStates();
        updateSelectAllState();
    }

    // Handle select all checkbox change
    function handleSelectAllChange() {
        const allScholarCheckboxes = document.querySelectorAll('.scholar-checkbox');
        allScholarCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateButtonStates();
    }

    // Handle individual checkbox change
    function handleCheckboxChange() {
        updateButtonStates();
        updateSelectAllState();
    }

    // Update button visibility and state
    function updateButtonStates() {
        const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');
        const hasSelection = selectedCheckboxes.length > 0;

        // Update Email Button
        if (sendEmailBtn) {
            sendEmailBtn.disabled = !hasSelection;
            if (hasSelection) {
                sendEmailBtn.classList.remove('hidden');
            } else {
                sendEmailBtn.classList.add('hidden');
            }
        }

        // Update SMS Button
        if (sendSmsBtn) {
            sendSmsBtn.disabled = !hasSelection;
            if (hasSelection) {
                sendSmsBtn.classList.remove('hidden');
            } else {
                sendSmsBtn.classList.add('hidden');
            }
        }
    }

    // Update select all checkbox state
    function updateSelectAllState() {
        if (!selectAllCheckbox) return;

        const allScholarCheckboxes = document.querySelectorAll('.scholar-checkbox');
        const allCheckedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');

        if (allScholarCheckboxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (allCheckedCheckboxes.length === allScholarCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (allCheckedCheckboxes.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    // Send Email Modal functionality
    function initializeEmailModal() {
        const sendEmailBtn = document.getElementById('sendEmailBtn');
        if (sendEmailBtn) {
            sendEmailBtn.addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    Swal.fire({
                        title: 'No Scholars Selected',
                        text: 'Please select at least one scholar to send an email.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const selectedScholars = [];
                const selectedEmails = [];

                selectedCheckboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const name = row.querySelector('td:nth-child(2) div').textContent.trim();
                    const email = checkbox.value;

                    selectedScholars.push(name);
                    selectedEmails.push(email);
                });

                // Populate modal
                const scholarsList = document.getElementById('selectedScholarsList');
                scholarsList.innerHTML = selectedScholars.map(name => `<div class="mb-1">• ${name}</div>`).join('');

                document.getElementById('selectedEmailsInput').value = selectedEmails.join(',');

                // Show modal
                document.getElementById('sendEmailModal').classList.remove('hidden');
            });
        }
    }

    // Send SMS Modal functionality
    function initializeSmsModal() {
        const sendSmsBtn = document.getElementById('sendSmsBtn');
        if (sendSmsBtn) {
            sendSmsBtn.addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    Swal.fire({
                        title: 'No Scholars Selected',
                        text: 'Please select at least one scholar to send an SMS.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const selectedScholars = [];
                const selectedEmails = [];

                selectedCheckboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const name = row.querySelector('td:nth-child(2) div').textContent.trim();
                    const email = checkbox.value;

                    selectedScholars.push(name);
                    selectedEmails.push(email);
                });

                // Populate modal
                const scholarsList = document.getElementById('selectedSmsScholarsList');
                scholarsList.innerHTML = selectedScholars.map(name => `<div class="mb-1">• ${name}</div>`).join('');

                document.getElementById('selectedSmsEmailsInput').value = selectedEmails.join(',');
                
                // Reset form
                document.getElementById('smsMessage').value = '';
                document.getElementById('smsCharCount').textContent = '0';
                
                // Show modal
                document.getElementById('sendSmsModal').classList.remove('hidden');
            });
        }
    }

    // SMS Character Count
    function initializeSmsCharacterCount() {
        const smsMessage = document.getElementById('smsMessage');
        if (smsMessage) {
            smsMessage.addEventListener('input', function() {
                const length = this.value.length;
                const charCount = document.getElementById('smsCharCount');
                
                if (charCount) {
                    charCount.textContent = length;
                    
                    if (length > 160) {
                        charCount.classList.add('text-red-600');
                    } else {
                        charCount.classList.remove('text-red-600');
                    }
                }
            });
        }
    }

    // Modal close functionality
    function initializeModalClose() {
        // Close Send Email Modal
        const closeSendEmailModal = document.getElementById('closeSendEmailModal');
        if (closeSendEmailModal) {
            closeSendEmailModal.addEventListener('click', function() {
                document.getElementById('sendEmailModal').classList.add('hidden');
            });
        }

        const cancelSendEmail = document.getElementById('cancelSendEmail');
        if (cancelSendEmail) {
            cancelSendEmail.addEventListener('click', function() {
                document.getElementById('sendEmailModal').classList.add('hidden');
            });
        }

        // Close Send SMS Modal
        const closeSendSmsModal = document.getElementById('closeSendSmsModal');
        if (closeSendSmsModal) {
            closeSendSmsModal.addEventListener('click', function() {
                document.getElementById('sendSmsModal').classList.add('hidden');
            });
        }

        const cancelSendSms = document.getElementById('cancelSendSms');
        if (cancelSendSms) {
            cancelSendSms.addEventListener('click', function() {
                document.getElementById('sendSmsModal').classList.add('hidden');
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            const smsModal = document.getElementById('sendSmsModal');
            const emailModal = document.getElementById('sendEmailModal');
            
            if (e.target === smsModal) {
                smsModal.classList.add('hidden');
            }
            if (e.target === emailModal) {
                emailModal.classList.add('hidden');
            }
        });
    }

    // Handle Send Email Form Submission
    function initializeEmailForm() {
        const sendEmailForm = document.getElementById('sendEmailForm');
        if (sendEmailForm) {
            sendEmailForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
                submitBtn.disabled = true;

                // Submit form via AJAX
                fetch('/lydo_admin/send-email-to-scholars', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Email Sent Successfully!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        document.getElementById('sendEmailModal').classList.add('hidden');
                        // Reset form
                        this.reset();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Failed to send email.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while sending the email.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }
    }

    // Handle Send SMS Form Submission
    function initializeSmsForm() {
        const sendSmsForm = document.getElementById('sendSmsForm');
        if (sendSmsForm) {
            sendSmsForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const message = document.getElementById('smsMessage').value.trim();
                const smsType = document.querySelector('input[name="sms_type"]:checked').value;
                
                // Validate schedule fields if schedule type is selected
                if (smsType === 'schedule') {
                    const scheduleWhat = document.getElementById('scheduleWhat').value.trim();
                    const scheduleWhere = document.getElementById('scheduleWhere').value.trim();
                    const scheduleDate = document.getElementById('scheduleDate').value;
                    const scheduleTime = document.getElementById('scheduleTime').value;
                    
                    if (!scheduleWhat || !scheduleWhere || !scheduleDate || !scheduleTime) {
                        Swal.fire({
                            title: 'Missing Schedule Information!',
                            text: 'Please fill in all schedule fields (What, Where, Date, and Time).',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }
                }

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
                submitBtn.disabled = true;

                // Submit form via AJAX
                fetch('/lydo_admin/send-sms-to-scholars', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let successMessage = data.message;
                        
                        // Add additional info for schedule type
                        if (smsType === 'schedule') {
                            successMessage += '\n\nEmail notifications have also been sent to all selected scholars.';
                        }
                        
                        Swal.fire({
                            title: 'SMS Sent Successfully!',
                            text: successMessage,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        
                        // Show detailed results if available
                        if (data.details && data.details.length > 0) {
                            const details = data.details.join('\n');
                            Swal.fire({
                                title: 'SMS Sending Details',
                                text: details,
                                icon: 'info',
                                confirmButtonText: 'OK',
                                width: '600px'
                            });
                        }
                        
                        document.getElementById('sendSmsModal').classList.add('hidden');
                        // Reset form
                        this.reset();
                        document.getElementById('smsCharCount').textContent = '0';
                        
                        // Reset schedule fields visibility
                        document.getElementById('scheduleFields').classList.add('hidden');
                        document.getElementById('smsMessageContainer').classList.remove('hidden');
                        
                        // Reset radio to plain type
                        document.querySelector('input[name="sms_type"][value="plain"]').checked = true;
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Failed to send SMS.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while sending the SMS.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }
    }

    function initializeSmsTypeToggle() {
        const smsTypeRadios = document.querySelectorAll('.sms-type-radio');
        const scheduleFields = document.getElementById('scheduleFields');
        const scheduleNote = document.getElementById('scheduleNote');
        const smsMessageContainer = document.getElementById('smsMessageContainer');
        
        smsTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'schedule') {
                    scheduleFields.classList.remove('hidden');
                    scheduleNote.classList.remove('hidden');
                    if (smsMessageContainer) smsMessageContainer.classList.add('hidden');
                } else {
                    scheduleFields.classList.add('hidden');
                    scheduleNote.classList.add('hidden');
                    if (smsMessageContainer) smsMessageContainer.classList.remove('hidden');
                }
            });
        });

        // Set initial visibility based on the currently checked radio
        const selected = document.querySelector('input[name="sms_type"]:checked');
        if (selected) {
            if (selected.value === 'schedule') {
                scheduleFields.classList.remove('hidden');
                scheduleNote.classList.remove('hidden');
                if (smsMessageContainer) smsMessageContainer.classList.add('hidden');
            } else {
                scheduleFields.classList.add('hidden');
                scheduleNote.classList.add('hidden');
                if (smsMessageContainer) smsMessageContainer.classList.remove('hidden');
            }
        }
    }

    // Personal Information Modal functionality
    function openPersonalInfoModal(scholarId) {
        const modal = document.getElementById('personalInfoModal');
        
        // Show loading state
        modal.classList.remove('hidden');
        document.getElementById('infoFullName').textContent = 'Loading...';
        
        // Fetch scholar personal information
        fetch(`/lydo_admin/get-scholar-personal-info/${scholarId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const scholar = data.scholar;
                    
                    // Personal Information
                    document.getElementById('infoFullName').textContent = 
                        `${scholar.applicant_lname}${scholar.applicant_suffix ? ' ' + scholar.applicant_suffix : ''}, ${scholar.applicant_fname} ${scholar.applicant_mname || ''}`;
                    document.getElementById('infoGender').textContent = scholar.applicant_gender || 'N/A';
                    document.getElementById('infoBirthDate').textContent = scholar.applicant_bdate ? new Date(scholar.applicant_bdate).toLocaleDateString() : 'N/A';
                    document.getElementById('infoCivilStatus').textContent = scholar.applicant_civil_status || 'N/A';
                    document.getElementById('infoBarangay').textContent = scholar.applicant_brgy || 'N/A';
                    document.getElementById('infoContact').textContent = scholar.applicant_contact_number || 'N/A';
                    
                    // Academic Information
                    document.getElementById('infoSchool').textContent = scholar.applicant_school_name || 'N/A';
                    document.getElementById('infoCourse').textContent = scholar.applicant_course || 'N/A';
                    document.getElementById('infoYearLevel').textContent = scholar.applicant_year_level || 'N/A';
                    document.getElementById('infoAcadYear').textContent = scholar.applicant_acad_year || 'N/A';
                    
                    // Scholarship Information
                    document.getElementById('infoScholarId').textContent = scholar.scholar_id || 'N/A';
                    document.getElementById('infoScholarStatus').textContent = scholar.scholar_status ? scholar.scholar_status.charAt(0).toUpperCase() + scholar.scholar_status.slice(1) : 'N/A';
                    document.getElementById('infoDateActivated').textContent = scholar.date_activated ? new Date(scholar.date_activated).toLocaleDateString() : 'N/A';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to load scholar information.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    modal.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error fetching scholar info:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to load scholar information.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                modal.classList.add('hidden');
            });
    }

    // Initialize personal info modal close functionality
    function initializePersonalInfoModalClose() {
        const closePersonalInfoModal = document.getElementById('closePersonalInfoModal');
        const closePersonalInfo = document.getElementById('closePersonalInfo');
        const modal = document.getElementById('personalInfoModal');

        if (closePersonalInfoModal) {
            closePersonalInfoModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        }

        if (closePersonalInfo) {
            closePersonalInfo.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }

    // Print to PDF functionality
    document.getElementById('printPdfBtn').addEventListener('click', function() {
        // Get current filter values
        const searchValue = document.getElementById('searchInput').value;
        const barangayValue = document.getElementById('barangaySelect').value;
        const academicYearValue = document.getElementById('academicYearSelect').value;
        const statusValue = document.getElementById('statusSelect').value;

        // Build query parameters
        const params = new URLSearchParams();

        if (searchValue) params.append('search', searchValue);
        if (barangayValue) params.append('barangay', barangayValue);
        if (academicYearValue) params.append('academic_year', academicYearValue);
        if (statusValue && statusValue !== 'all') params.append('status', statusValue);

        // Generate PDF URL
        const pdfUrl = `/lydo_admin/generate-scholars-pdf?${params.toString()}`;

        // Show loading state
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating PDF...';
        this.disabled = true;

        // Open PDF in new tab
        const newWindow = window.open(pdfUrl, '_blank');

        // Reset button after a delay
        setTimeout(() => {
            this.innerHTML = originalText;
            this.disabled = false;
        }, 3000);

        setTimeout(() => {
            if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                Swal.fire({
                    title: 'PDF Generated',
                    text: 'Your PDF is ready. If it didn\'t open automatically, check your browser\'s pop-up settings.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        }, 2000);
    });

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

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize main functionality
        initializeScholarData();
        initializeScholarPagination();
        initializeScholarFiltering();
        
        // Initialize checkbox and button system
        initializeCheckboxSystem();
        initializeEmailModal();
        initializeSmsModal();
        initializeSmsCharacterCount();
        initializeModalClose();
        initializeEmailForm();
        initializeSmsForm();
        initializeSmsTypeToggle();
        initializePersonalInfoModalClose();
        
        // Email type pre-fill functionality
        const emailTypeSelect = document.getElementById('emailType');
        const emailSubjectInput = document.getElementById('emailSubject');
        const emailMessageTextarea = document.getElementById('emailMessage');

        if (emailTypeSelect && emailSubjectInput && emailMessageTextarea) {
            emailTypeSelect.addEventListener('change', function() {
                if (this.value === 'registration') {
                    emailSubjectInput.value = 'Account Registration Required';
                    emailMessageTextarea.value = 'Dear Scholar,\n\nYou are required to complete your account registration to access the scholarship system.\n\nPlease visit the following link to Create your username and password:\n\n{{ route("scholar.scholar_reg") }}\n\nNote: For personalized access, you may receive an individual email with a secure link.\n\nBest regards,\nLYDO Scholarship Team';
                } else if (this.value === 'custom') {
                    // Clear fields for custom emails
                    emailSubjectInput.value = '';
                    emailMessageTextarea.value = '';
                }
            });
        }

        // Pagination event listeners
        document.getElementById('prevPage').addEventListener('click', function() {
            changeScholarPage(paginationState.currentPage - 1);
        });

        document.getElementById('nextPage').addEventListener('click', function() {
            changeScholarPage(paginationState.currentPage + 1);
        });

        document.getElementById('currentPage').addEventListener('change', function() {
            goToScholarPage(this.value);
        });

        console.log('Scholar page fully initialized');
    });
    </script>

    <script src="{{ asset('js/filter_paginate.js') }}"></script>
    <script src="{{ asset('js/scholar.js') }}"></script>
    <script src="{{ asset('js/spinner.js') }}"></script>
    <script src="{{ asset('js/scholar_logout.js') }}"></script>
    <script src="{{ asset('js/toggle_dropdown.js') }}"></script>
</body>
</html>