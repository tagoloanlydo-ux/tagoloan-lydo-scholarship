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
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm"> @csrf <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                            <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                            <span class="hidden md:block text-red-600">Logout</span>
                        </button>
                    </form>
                    </div>
                </div>
            <div class="flex-1 overflow-auto p-4 md:p-5 text-[16px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">List of Scholars</h2>
                </div>
                 <!-- Filter Section -->
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
                                <button type="button" id="printPdfBtn" class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-file-pdf mr-2"></i>Print to PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Scholars Table -->
                <div class="bg-white  shadow-sm overflow-hidden">
                        <!-- Add this after the table description and before the table -->
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-black-800">Scholars List</h3>
                                <p class="text-sm text-black-600 mt-1">This table contains the list of active scholars currently enrolled in the scholarship program.</p>
                            </div>
                            <button type="button" id="sendEmailBtn" class="w-24 px-2 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center disabled:bg-gray-400 disabled:cursor-not-allowed"  disabled>
                                <i class="fas fa-envelope mr-1"></i>Send
                            </button>                 
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg  overflow-hidden border border-gray-200">
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
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Document</th>
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
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $scholar->scholar_status == 'active' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($scholar->scholar_status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <button class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors" onclick="openDocumentModal({{ $scholar->scholar_id }})">
                                                Renewal History
                                            </button>
                                        </td>
                                   </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 border border-gray-200 text-gray-500">No scholars found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    <div class="px-3 py-2 bg-white border-t border-gray-200">
    <div class="flex justify-center">
        <div class="pagination-container">
            <div class="pagination-info" id="paginationInfo">
                Showing page 1 of 10
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
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
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
        <script>
                // Notification bell functionality
            document.getElementById("notifBell").addEventListener("click", function () {
                let dropdown = document.getElementById("notifDropdown");
                dropdown.classList.toggle("hidden");

                    // remove badge when opened
                let notifCount = document.getElementById("notifCount");
                 if (notifCount) {
                     notifCount.remove();
                    }
            });


            // Document Modal functionality
            function openDocumentModal(scholarId) {
                const modal = document.getElementById('documentModal');
                const scholarName = document.getElementById('docScholarName');
                const scholarEmail = document.getElementById('docScholarEmail');
                const academicPeriodTabs = document.getElementById('academicPeriodTabs');
                const documentsSection = document.getElementById('documentsSection');
                const noDocumentsMessage = document.getElementById('noDocumentsMessage');

                // Reset modal content
                scholarName.textContent = '-';
                scholarEmail.textContent = '-';
                academicPeriodTabs.innerHTML = '';
                documentsSection.innerHTML = '';
                noDocumentsMessage.classList.add('hidden');

                // Get scholar info from table
                const scholarRow = document.querySelector(`.scholar-checkbox[data-scholar-id="${scholarId}"]`).closest('tr');
                const scholarNameFromTable = scholarRow.querySelector('td:nth-child(2) div').textContent.trim();
                const scholarEmailFromTable = scholarRow.querySelector('td:nth-child(4) div').textContent.trim();

                scholarName.textContent = scholarNameFromTable;
                scholarEmail.textContent = scholarEmailFromTable;

                // Attach close event listeners
                attachModalCloseListeners();

                // Show modal
                modal.classList.remove('hidden');

                // Fetch scholar documents grouped by academic period
                fetch(`/lydo_admin/get-scholar-documents/${scholarId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.documents.length > 0) {
                            // Group documents by academic year and semester
                            const groupedDocuments = groupDocumentsByAcademicPeriod(data.documents);

                            // Create tabs for each academic period
                            createAcademicPeriodTabs(groupedDocuments, scholarId);

                            // Load first tab by default
                            if (Object.keys(groupedDocuments).length > 0) {
                                const firstPeriod = Object.keys(groupedDocuments)[0];
                                loadDocumentsForPeriod(groupedDocuments[firstPeriod], firstPeriod);
                            }
                        } else {
                            noDocumentsMessage.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching documents:', error);
                    });
            }

            // Function to attach modal close listeners
            function attachModalCloseListeners() {
                // Close document modal
                document.getElementById('closeDocumentModal').addEventListener('click', function() {
                    document.getElementById('documentModal').classList.add('hidden');
                });

                document.getElementById('closeDocument').addEventListener('click', function() {
                    document.getElementById('documentModal').classList.add('hidden');
                });

                // Close modal when clicking outside
                window.addEventListener('click', function(e) {
                    const modal = document.getElementById('documentModal');
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            }

            // Group documents by academic year and semester
            function groupDocumentsByAcademicPeriod(documents) {
                const grouped = {};
                
                documents.forEach(doc => {
                    const key = `${doc.renewal_acad_year}-${doc.renewal_semester}`;
                    if (!grouped[key]) {
                        grouped[key] = {
                            academicYear: doc.renewal_acad_year,
                            semester: doc.renewal_semester,
                            dateSubmitted: doc.date_submitted,
                            documents: []
                        };
                    }
                    grouped[key].documents.push(doc);
                });
                
                return grouped;
            }

            // Create tabs for academic periods
            function createAcademicPeriodTabs(groupedDocuments, scholarId) {
                const tabsContainer = document.getElementById('academicPeriodTabs');
                
                Object.keys(groupedDocuments).forEach((periodKey, index) => {
                    const period = groupedDocuments[periodKey];
                    const tab = document.createElement('button');
                    tab.className = `px-4 py-2 rounded-lg border transition-colors ${
                        index === 0 
                        ? 'bg-blue-600 text-white border-blue-600' 
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                    }`;
                    tab.textContent = `${period.semester} - ${period.academicYear}`;
                    tab.setAttribute('data-period', periodKey);
                    
                    tab.addEventListener('click', function() {
                        // Update active tab
                        document.querySelectorAll('#academicPeriodTabs button').forEach(btn => {
                            btn.className = 'px-4 py-2 rounded-lg border bg-white text-gray-700 border-gray-300 hover:bg-gray-50 transition-colors';
                        });
                        this.className = 'px-4 py-2 rounded-lg border bg-blue-600 text-white border-blue-600 transition-colors';
                        
                        // Load documents for selected period
                        loadDocumentsForPeriod(groupedDocuments[periodKey], periodKey);
                    });
                    
                    tabsContainer.appendChild(tab);
                });
            }

            // Load documents for specific academic period
            function loadDocumentsForPeriod(periodData, periodKey) {
                const documentsSection = document.getElementById('documentsSection');
                const noDocumentsMessage = document.getElementById('noDocumentsMessage');
                
                documentsSection.innerHTML = '';
                
                if (periodData.documents.length === 0) {
                    noDocumentsMessage.classList.remove('hidden');
                    return;
                }
                
                noDocumentsMessage.classList.add('hidden');
                
                // Create period info
                const periodInfo = document.createElement('div');
                periodInfo.className = 'bg-green-50 border border-green-200 rounded-lg p-4 mb-4';
                periodInfo.innerHTML = `
                    <h4 class="text-lg font-semibold text-green-800 mb-2">Academic Period: ${periodData.semester} - ${periodData.academicYear}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div><span class="font-medium text-green-700">Date Submitted:</span> ${periodData.dateSubmitted || 'N/A'}</div>
                    </div>
                `;
                documentsSection.appendChild(periodInfo);
                
                // Create documents grid
                const documentsGrid = document.createElement('div');
                documentsGrid.className = 'grid grid-cols-1 md:grid-cols-3 gap-4';
                documentsGrid.id = 'documentsGrid';
                
                const documentTypes = [
                    { key: 'renewal_cert_of_reg', name: 'Certificate of Registration', color: 'blue' },
                    { key: 'renewal_grade_slip', name: 'Grade Slip', color: 'green' },
                    { key: 'renewal_brgy_indigency', name: 'Barangay Indigency', color: 'purple' }
                ];
                
                documentTypes.forEach((docType, index) => {
                    const docContainer = document.createElement('div');
                    docContainer.className = 'border-2 border-gray-300 rounded-lg p-4 bg-white transition-all duration-300 hover:border-blue-300 document-container';
                    docContainer.setAttribute('data-doc-id', index + 1);
                    
                    const latestDocument = periodData.documents[0]; // Get the latest submission for this period
                    
                    const colorClasses = {
                        blue: 'bg-blue-100 text-blue-800',
                        green: 'bg-green-100 text-green-800',
                        purple: 'bg-purple-100 text-purple-800'
                    };
                    
                    let previewContent = '<span class="text-gray-500 text-sm">No document available</span>';
                    let downloadButton = '';
                    
                    if (latestDocument[docType.key]) {
                        const fileUrl = latestDocument[docType.key];
                        previewContent = `<iframe src="${fileUrl}" class="w-full h-full border-0" style="min-height: 200px;"></iframe>`;
                        downloadButton = `
                            <a href="${fileUrl}" target="_blank" class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors flex items-center justify-center mt-3">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        `;
                    }
                    
                // Gawing almost full height agad
            docContainer.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h5 class="font-semibold text-gray-700 flex items-center">
                        <span class="${colorClasses[docType.color]} text-xs font-bold px-2 py-1 rounded mr-2">${index + 1}</span>
                        ${docType.name}
                    </h5>
                    <button class="text-blue-600 hover:text-blue-800 text-sm transition-colors expand-btn" title="Expand Document">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
                <div class="mb-3 h-96 border border-gray-200 rounded flex items-center justify-center bg-gray-50 overflow-hidden transition-all duration-300 document-preview">
                    ${previewContent}
                </div>
                ${downloadButton}
            `; 
                    documentsGrid.appendChild(docContainer);
                });
                
                documentsSection.appendChild(documentsGrid);
                
                // Re-attach expand functionality
                attachExpandFunctionality();
            }

            // Attach expand functionality to document previews
            function attachExpandFunctionality() {
                let currentlyExpandedDoc = null;
                
                document.querySelectorAll('.expand-btn').forEach((button, index) => {
                    const preview = button.closest('.document-container').querySelector('.document-preview');
                    const icon = button.querySelector('i');
                    const container = preview.closest('.border-2');
                    const documentsGrid = document.getElementById('documentsGrid');
                    const modal = document.getElementById('documentModal');
                    
                    // Set initial collapsed state
                    preview.classList.remove('expanded');
                    preview.style.maxHeight = '200px';
                    icon.className = 'fas fa-expand';
                    button.title = 'Expand Document';
                    
                    button.addEventListener('click', function() {
                        const isExpanded = preview.classList.contains('expanded');
                        
                        if (isExpanded) {
                            // Collapse current document
                            preview.classList.remove('expanded');
                            preview.style.maxHeight = '200px';
                            container.classList.remove('md:col-span-3', 'col-span-1');
                            documentsGrid.classList.remove('grid-cols-1');
                            documentsGrid.classList.add('md:grid-cols-3');
                            icon.className = 'fas fa-expand';
                            button.title = 'Expand Document';
                            currentlyExpandedDoc = null;
                        } else {
                            // If another document is expanded, collapse it first
                            if (currentlyExpandedDoc !== null && currentlyExpandedDoc !== index) {
                                const prevButton = document.querySelectorAll('.expand-btn')[currentlyExpandedDoc];
                                const prevPreview = prevButton.closest('.document-container').querySelector('.document-preview');
                                const prevContainer = prevPreview.closest('.border-2');
                                const prevIcon = prevButton.querySelector('i');
                                
                                prevPreview.classList.remove('expanded');
                                prevPreview.style.maxHeight = '200px';
                                prevContainer.classList.remove('md:col-span-3', 'col-span-1');
                                prevIcon.className = 'fas fa-expand';
                                prevButton.title = 'Expand Document';
                            }
                            
                            // Expand current document
                            preview.classList.add('expanded');
                            preview.style.maxHeight = 'none';
                            container.classList.add('md:col-span-3', 'col-span-1');
                            documentsGrid.classList.remove('md:grid-cols-3');
                            documentsGrid.classList.add('grid-cols-1');
                            icon.className = 'fas fa-compress';
                            button.title = 'Collapse Document';
                            currentlyExpandedDoc = index;
                        }
                    });
                });
            }
            // Add this to your existing JavaScript
            document.addEventListener('DOMContentLoaded', function() {
                // Get filter elements
                const searchInput = document.getElementById('searchInput');
                const barangaySelect = document.getElementById('barangaySelect');
                const academicYearSelect = document.getElementById('academicYearSelect');
                const statusSelect = document.getElementById('statusSelect');
                
                // Add event listeners for real-time filtering
                searchInput.addEventListener('input', debounce(applyFilters, 500));
                barangaySelect.addEventListener('change', applyFilters);
                academicYearSelect.addEventListener('change', applyFilters);
                statusSelect.addEventListener('change', applyFilters);
                
            function applyFilters() {
                const searchValue = searchInput.value.toLowerCase();
                const barangayValue = barangaySelect.value;
                const academicYearValue = academicYearSelect.value;
                const statusValue = statusSelect.value;

                const rows = document.querySelectorAll('.scholar-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.querySelector('td:nth-child(2) div').textContent.toLowerCase();
                    const barangay = row.querySelector('td:nth-child(3) div').textContent;
                    const academicYear = row.querySelector('td:nth-child(7) div').textContent;
                    const statusElement = row.querySelector('td:nth-child(8) span');

                    // Get status and normalize to lowercase for comparison
                    let statusText = '';
                    if (statusElement) {
                        statusText = statusElement.textContent.trim().toLowerCase();
                    }

                    // Check if row matches all filters
                    const matchesSearch = !searchValue || name.includes(searchValue);
                    const matchesBarangay = !barangayValue || barangay === barangayValue;
                    const matchesAcademicYear = !academicYearValue || academicYear === academicYearValue;

                    // Fixed status matching logic
                    let matchesStatus = true;
                    if (statusValue !== 'all') {
                        matchesStatus = statusText === statusValue.toLowerCase();
                    }

                    if (matchesSearch && matchesBarangay && matchesAcademicYear && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show no results message if needed
                showNoResultsMessage(visibleCount === 0);

                // Update select all checkbox
                updateSelectAllCheckbox();
                updateSendButton();
            }

                function showNoResultsMessage(show) {
                    let noResultsRow = document.querySelector('.no-results-row');
                    
                    if (show && !noResultsRow) {
                        noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-row';
                        noResultsRow.innerHTML = `
                            <td colspan="9" class="text-center py-4 border border-gray-200 text-gray-500">
                                No scholars found matching your filters.
                            </td>
                        `;
                        document.querySelector('tbody').appendChild(noResultsRow);
                    } else if (!show && noResultsRow) {
                        noResultsRow.remove();
                    }
                }
                
                    function updateSelectAllCheckbox() {
                        const selectAll = document.getElementById('selectAll');
                        const visibleCheckboxes = document.querySelectorAll('.scholar-row:not([style*="display: none"]) .scholar-checkbox');
                        const checkedVisibleCheckboxes = document.querySelectorAll('.scholar-row:not([style*="display: none"]) .scholar-checkbox:checked');
                        
                        if (visibleCheckboxes.length === 0) {
                            selectAll.checked = false;
                            selectAll.indeterminate = false;
                        } else if (checkedVisibleCheckboxes.length === visibleCheckboxes.length) {
                            selectAll.checked = true;
                            selectAll.indeterminate = false;
                        } else if (checkedVisibleCheckboxes.length > 0) {
                            selectAll.checked = false;
                            selectAll.indeterminate = true;
                        } else {
                            selectAll.checked = false;
                            selectAll.indeterminate = false;
                        }
                        
                        // Update email button visibility
                        updateEmailButton();
                    }
                
                // Debounce function to limit how often the filter runs during typing
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
                
                // Initialize filters on page load
                applyFilters();
            });
                        </script>

            <script>
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


            </script>
            
            <script>
            // Send Email Modal functionality
            document.getElementById('sendEmailBtn').addEventListener('click', function() {
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

            // Close Send Email Modal
            document.getElementById('closeSendEmailModal').addEventListener('click', function() {
                document.getElementById('sendEmailModal').classList.add('hidden');
            });

            document.getElementById('cancelSendEmail').addEventListener('click', function() {
                document.getElementById('sendEmailModal').classList.add('hidden');
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                const modal = document.getElementById('sendEmailModal');
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });

            // Handle Send Email Form Submission
            document.getElementById('sendEmailForm').addEventListener('submit', function(e) {
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

            // Update Send Email Button State
            function updateSendButton() {
                const sendEmailBtn = document.getElementById('sendEmailBtn');
                const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');

                if (selectedCheckboxes.length > 0) {
                    sendEmailBtn.disabled = false;
                    sendEmailBtn.classList.remove('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                } else {
                    sendEmailBtn.disabled = true;
                    sendEmailBtn.classList.add('disabled:bg-gray-400', 'disabled:cursor-not-allowed');
                }
            }

            // Update Email Button (alias for updateSendButton for consistency)
            function updateEmailButton() {
                updateSendButton();
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateSendButton();

                // Email type pre-fill functionality
                const emailTypeSelect = document.getElementById('emailType');
                const emailSubjectInput = document.getElementById('emailSubject');
                const emailMessageTextarea = document.getElementById('emailMessage');

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
            });
            </script>

            <script src="{{ asset('js/filter_paginate.js') }}"></script>
            <script src="{{ asset('js/scholar.js') }}"></script>
            <script src="{{ asset('js/spinner.js') }}"></script>
            <script src="{{ asset('js/scholar_logout.js') }}"></script>
            <script src="{{ asset('js/toggle_dropdown.js') }}"></script>
        </div>
    </div>
</body>

</html>
