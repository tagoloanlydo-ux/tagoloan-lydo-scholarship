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
    <style>
        input::placeholder, select::placeholder {
            color: black !important;
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
       </style>
</head>

<body class="bg-gray-50 h-screen flex flex-col">
   <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner">
            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
        </div>
    </div>
    <!-- Header -->
    <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
         <div class="flex items-center space-x-4">
            <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Admin</span>        
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

                            <li class="text-blue-600 bg-blue-50">
                                <a href="/lydo_admin/applicants" 
                                class=" flex items-center justify-between p-3 rounded-lg text-white-700 bg-violet-600 text-white">
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
                            class=" flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
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
            <div class="flex-1 overflow-y-auto p-4 md:p-5 text-[16px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-black-800">List of Applicants</h2>
                </div>
                        <!-- Status Legend -->
                            <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-200">
                                <h4 class="text-sm font-semibold text-blue-800 mb-2">Application Status Guide:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
                                        <span class="text-gray-700"><strong>Pending:</strong> Waiting for Mayor Staff screening</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-green-400 rounded-full mr-2"></span>
                                        <span class="text-gray-700"><strong>Approved by Mayor:</strong> Ready for LYDO review</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-blue-400 rounded-full mr-2"></span>
                                        <span class="text-gray-700"><strong>Reviewed by LYDO:</strong> Final review completed</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 bg-red-400 rounded-full mr-2"></span>
                                        <span class="text-gray-700"><strong>Rejected:</strong> Application not approved</span>
                                    </div>
                                </div>
                            </div>
                <!-- Filter Section -->
                <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                    <form id="filterForm" method="GET" action="{{ url()->current() }}" 
                          class="grid grid-cols-1 md:grid-cols-5 gap-4">

                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
                                   placeholder="Enter name..."
                                   class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                        </div>

                        <!-- Barangay -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                            <select id="barangaySelect" name="barangay" 
                                    class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                                        {{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Year -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Academic Year</label>
                            <select id="academicYearSelect" name="academic_year" 
                                    class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                <option value="">All Academic Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Initial Screening Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                            <select id="initialScreeningSelect" name="initial_screening" 
                                    class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                <option value="all" {{ $initialScreeningStatus == 'all' ? 'selected' : '' }}>All Applicants</option>
                                <option value="Pending" {{ $initialScreeningStatus == 'Pending' ? 'selected' : '' }}>Pending For Initial Screening</option>
                                <option value="for_lydo_review" {{ $initialScreeningStatus == 'for_lydo_review' ? 'selected' : '' }}>Ready for LYDO Review (Approved by Mayor)</option>
                                <option value="Reviewed" {{ $initialScreeningStatus == 'Reviewed' ? 'selected' : '' }}>Reviewed by LYDO Staff</option>
                                <option value="Rejected" {{ $initialScreeningStatus == 'Rejected' ? 'selected' : '' }}>Rejected Applications</option>
                            </select>
                        </div>

                        <!-- Print Button -->
                        <div class="flex items-end">
                            <button type="button" id="printPdfBtn" 
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium">
                                <i class="fas fa-file-pdf"></i> Print PDF
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Applicants Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Applicants List</h3>
                    <div class="flex space-x-2">
                        <button id="copyNamesBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                            Copy Names
                        </button>
                        <button id="emailSelectedBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                            Email
                        </button>
                        <button id="smsSelectedBtn" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                            SMS
                        </button>
                    </div>
                    </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
                            <!-- In the table header section -->
                            <thead class="bg-violet-600 to-teal-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Full Name</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Email</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Academic Year</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Application History</th>
                                    <th class="px-4 py-3 border border-gray-200 align-middle text-center">Initial Screening</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($applicants as $applicant)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <input type="checkbox" name="selected_applicants" value="{{ $applicant->applicant_id }}" class="applicant-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </td>
                                    
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ ucfirst(strtolower($applicant->applicant_lname)) }}
                                                @if(!empty($applicant->applicant_suffix))
                                                    {{ ' ' . ucfirst(strtolower($applicant->applicant_suffix)) }}
                                                @endif
                                                , 
                                                {{ ucfirst(strtolower($applicant->applicant_fname)) }}
                                                @if(!empty($applicant->applicant_mname))
                                                    {{ ' ' . strtoupper(substr($applicant->applicant_mname, 0, 1)) . '.' }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm text-gray-900">{{ $applicant->applicant_brgy }}</div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm text-gray-900">{{ $applicant->applicant_email }}</div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm text-gray-900">{{ $applicant->applicant_school_name }}</div>
                                        </td>
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="text-sm text-gray-900">{{ $applicant->applicant_acad_year }}</div>
                                        </td>
                                        <!-- NEW COLUMN: Application History -->
                                        <td class="px-4 border border-gray-200 py-2 text-center">
                                            <div class="flex gap-2 justify-center">
                                                @if(in_array($applicant->initial_screening, ['Approved', 'Rejected']))
                                                    <!-- Show only documents for Approved/Rejected -->
                                                    <button type="button" 
                                                            onclick="viewApplicantDocuments('{{ $applicant->applicant_id }}', '{{ addslashes($applicant->applicant_fname) }} {{ addslashes($applicant->applicant_lname) }}', '{{ $applicant->initial_screening }}')"
                                                            class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow">
                                                        <i class="fas fa-file-alt mr-1"></i> View Documents
                                                    </button>
                                                @elseif($applicant->initial_screening === 'Reviewed')
                                                    <!-- Show intake sheet and documents for Reviewed -->
                                                    <button type="button" 
                                                            onclick="viewApplicantIntakeSheet('{{ $applicant->applicant_id }}', '{{ addslashes($applicant->applicant_fname) }} {{ addslashes($applicant->applicant_lname) }}', '{{ $applicant->initial_screening }}')"
                                                            class="px-3 py-1 text-sm bg-purple-500 hover:bg-purple-600 text-white rounded-lg shadow">
                                                        <i class="fas fa-clipboard-list mr-1"></i> View Application
                                                    </button>
                                                @else
                                                    <!-- Pending or other status -->
                                                    <span class="text-sm text-gray-500">No actions available</span>
                                                @endif
                                            </div>
                                        </td>
                                     <td class="px-4 border border-gray-200 py-2 text-center">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                                @if($applicant->initial_screening === 'Approved') bg-green-100 text-green-800
                                                @elseif($applicant->initial_screening === 'Rejected') bg-red-100 text-red-800
                                                @elseif($applicant->initial_screening === 'Reviewed') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                @if($applicant->initial_screening === 'Approved')
                                                    Approved by Mayor
                                                @else
                                                    {{ $applicant->initial_screening ?? 'Pending' }}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-2 text-center text-sm text-gray-500">
                                            No applicants found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>              
                         </table>
                    </div>
              <div class="px-6 py-4 bg-white border-t border-gray-200">
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

            <!-- Email Modal -->
            <div id="emailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Send Email to Selected Applicants</h3>
                            <button id="closeEmailModal" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <form id="emailForm">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Subject</label>
                                <input type="text" id="emailSubject" name="subject" required
                                       class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter email subject...">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Message</label>
                                <textarea id="emailMessage" name="message" rows="6" required
                                          class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter your email message..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipients Preview</label>
                                <div id="recipientsPreview" class="p-3 bg-gray-50 border border-gray-200 rounded-md max-h-32 overflow-y-auto text-sm text-gray-600">
                                    No recipients selected
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" id="cancelEmailBtn"
                                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" id="sendEmailBtn"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <span id="sendEmailText">Send Email</span>
                                    <span id="sendEmailLoading" class="hidden">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Sending...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Application History Modal -->
            <div id="applicationHistoryModal" class="hidden fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
                <div class="relative top-4 mx-auto p-6 w-11/12 max-w-6xl max-h-[95vh] overflow-hidden bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
                    <!-- Modern Header -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="w-8 h-8 rounded-lg">
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800" id="modalTitle">Application Details</h2>
                                <p class="text-sm text-gray-500">Comprehensive application information</p>
                            </div>
                        </div>
                        <button onclick="closeApplicationModal()" class="w-10 h-10 bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 rounded-full flex items-center justify-center transition-colors duration-200 shadow-sm">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body with Scroll -->
                    <div class="overflow-y-auto max-h-[calc(95vh-140px)] px-2">
                        <!-- Basic applicant info -->
                        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                    <i class="fas fa-user-graduate text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800">Applicant Information</h3>
                                    <p class="text-sm text-gray-600">Basic details of the scholarship applicant</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="applicantBasicInfo">
                                <!-- Basic info will be populated here -->
                            </div>
                        </div>

                        <!-- Intake Sheet Information (for Reviewed status) -->
                        <div id="intakeSheetInfo" class="hidden space-y-8">
                            <!-- Head of Family Section -->
                            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-6 rounded-xl border border-emerald-100">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-home text-white"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Head of Family</h3>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" id="headOfFamilyInfo">
                                    <!-- Head of family info will be populated here -->
                                </div>
                            </div>

                            <!-- Household Information Section -->
                            <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-6 rounded-xl border border-amber-100">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-house-user text-white"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Household Information</h3>
                                </div>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="householdInfo">
                                    <!-- Household info will be populated here -->
                                </div>
                            </div>

                            <!-- Family Members Section -->
                            <div class="bg-gradient-to-r from-rose-50 to-pink-50 p-6 rounded-xl border border-rose-100">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-rose-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Family Members</h3>
                                </div>
                                <div class="overflow-x-auto shadow-sm rounded-lg border border-gray-200">
                                    <table class="w-full border-collapse bg-white">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Name</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Relation</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Birthdate</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Age</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Sex</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Civil Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Education</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Occupation</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Income</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="familyMembersTable" class="divide-y divide-gray-200">
                                            <!-- Family members will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Service Records Section -->
                            <div class="bg-gradient-to-r from-cyan-50 to-blue-50 p-6 rounded-xl border border-cyan-100">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-clipboard-list text-white"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Social Service Records</h3>
                                </div>
                                <div class="overflow-x-auto shadow-sm rounded-lg border border-gray-200">
                                    <table class="w-full border-collapse bg-white">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Problem/Need</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Action/Assistance</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="serviceRecordsTable" class="divide-y divide-gray-200">
                                            <!-- Service records will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Section -->
                        <div class="bg-gradient-to-r from-purple-50 to-violet-50 p-6 rounded-xl border border-purple-100">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-800">Supporting Documents</h3>
                            </div>
                            <div id="documentsContainer" class="space-y-4">
                                <!-- Documents will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
                        <button onclick="closeApplicationModal()" class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 shadow-sm font-medium">
                            <i class="fas fa-times mr-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
  <!-- SMS Modal -->
<div id="smsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Send SMS to Selected Applicants</h3>
                <button id="closeSmsModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="smsForm">
                <!-- SMS Type Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMS Type</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="smsType" value="plain" checked 
                                   class="sms-type-radio text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Plain Text</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="smsType" value="schedule"
                                   class="sms-type-radio text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Schedule</span>
                        </label>
                    </div>
                </div>

                <!-- SMS Message -->
                <div id="smsMessageContainer" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMS Message</label>
                    <textarea id="smsMessage" name="message" rows="4"  maxlength="160"
                              class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter your SMS message (max 160 characters)..."></textarea>
                    <div class="text-sm text-gray-500 mt-1">
                        <span id="smsCharCount">0</span>/160 characters
                    </div>
                </div>

                <!-- Schedule Fields (Hidden by Default) -->
                <div id="scheduleFields" class="hidden mb-4 space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">What (Event/Activity)</label>
                        <input type="text" id="scheduleWhat" name="schedule_what"
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., Scholarship Orientation, Interview">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Where (Location)</label>
                        <input type="text" id="scheduleWhere" name="schedule_where"
                               class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., LYDO Office, City Hall">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" id="scheduleDate" name="schedule_date"
                                   class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                            <input type="time" id="scheduleTime" name="schedule_time"
                                   class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Recipients Preview -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recipients Preview</label>
                    <div id="smsRecipientsPreview" class="p-3 bg-gray-50 border border-gray-200 rounded-md max-h-32 overflow-y-auto text-sm text-gray-600">
                        No recipients selected
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelSmsBtn"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="sendSmsBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span id="sendSmsText">Send SMS</span>
                        <span id="sendSmsLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Sending...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ⚡ JS -->

<script>
    // Ensure the logo spinner only shows while the page / async work is loading.
    // The overlay is visible by default (so user sees it during initial page load)
    // and we hide it once the window 'load' event fires. Use helper functions
    // to show/hide during AJAX/fetch operations.
    window.addEventListener('load', function () {
        const overlay = document.getElementById('loadingOverlay');
        if (!overlay) return;
        overlay.classList.add('fade-out'); // uses existing CSS
        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.classList.remove('fade-out');
        }, 300);
    });

    // Show overlay (use before fetch/ajax)
    function showLoadingOverlay() {
        const overlay = document.getElementById('loadingOverlay');
        if (!overlay) return;
        overlay.style.display = 'flex';
        overlay.classList.remove('fade-out');
    }

    // Hide overlay (use in .finally() of fetch/ajax)
    function hideLoadingOverlay() {
        const overlay = document.getElementById('loadingOverlay');
        if (!overlay) return;
        overlay.classList.add('fade-out');
        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.classList.remove('fade-out');
        }, 300);
    }

    // Optional: example replacement pattern for existing fetch calls
    // Instead of: document.getElementById('loadingOverlay').style.display = 'flex';
    // Use: showLoadingOverlay();
    // And instead of: document.getElementById('loadingOverlay').style.display = 'none';
    // Use: hideLoadingOverlay();
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
    // SMS modal elements
const smsModal = document.getElementById('smsModal');
const closeSmsModal = document.getElementById('closeSmsModal');
const cancelSmsBtn = document.getElementById('cancelSmsBtn');
const smsForm = document.getElementById('smsForm');
const smsMessage = document.getElementById('smsMessage');
const smsRecipientsPreview = document.getElementById('smsRecipientsPreview');
const sendSmsBtn = document.getElementById('sendSmsBtn');
const sendSmsText = document.getElementById('sendSmsText');
const sendSmsLoading = document.getElementById('sendSmsLoading');
const smsCharCount = document.getElementById('smsCharCount');
const smsSelectedBtn = document.getElementById('smsSelectedBtn');

// SMS character count
smsMessage.addEventListener('input', function() {
    const length = this.value.length;
    smsCharCount.textContent = length;
    
    if (length > 160) {
        smsCharCount.classList.add('text-red-600');
        sendSmsBtn.disabled = true;
    } else {
        smsCharCount.classList.remove('text-red-600');
        sendSmsBtn.disabled = length === 0;
    }
});

// SMS Selected button functionality
smsSelectedBtn.addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        Swal.fire({
            title: 'No Selection!',
            text: 'Please select at least one applicant to send SMS.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    updateSmsRecipientsPreview();
    smsModal.classList.remove('hidden');
    smsMessage.focus();
});

// Update SMS recipients preview
function updateSmsRecipientsPreview() {
    const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        smsRecipientsPreview.innerHTML = 'No recipients selected';
        return;
    }

    const recipients = Array.from(selectedCheckboxes).map(checkbox => {
        const row = checkbox.closest('tr');
        const name = row.querySelector('td:nth-child(2)').textContent.trim();
        const contact = row.querySelector('td:nth-child(4)').textContent.trim();
        return `${name} (${contact})`;
    });

    smsRecipientsPreview.innerHTML = recipients.join('<br>');
}

// Close SMS modal
function closeSmsModalHandler() {
    smsModal.classList.add('hidden');
    smsForm.reset();
    smsCharCount.textContent = '0';
    sendSmsText.classList.remove('hidden');
    sendSmsLoading.classList.add('hidden');
    sendSmsBtn.disabled = false;
}

closeSmsModal.addEventListener('click', closeSmsModalHandler);
cancelSmsBtn.addEventListener('click', closeSmsModalHandler);

// Close modal when clicking outside
smsModal.addEventListener('click', function(e) {
    if (e.target === smsModal) {
        closeSmsModalHandler();
    }
});

// Send SMS form submission
smsForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');
    const message = smsMessage.value.trim();

    if (!message) {
        Swal.fire({
            title: 'Missing Message!',
            text: 'Please enter an SMS message.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    if (message.length > 160) {
        Swal.fire({
            title: 'Message Too Long!',
            text: 'SMS message cannot exceed 160 characters.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    if (selectedCheckboxes.length === 0) {
        Swal.fire({
            title: 'No Recipients!',
            text: 'No applicants selected to send SMS to.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Collect recipient emails (we'll use these to identify scholars)
    const selectedEmails = Array.from(selectedCheckboxes).map(checkbox => {
        const row = checkbox.closest('tr');
        return row.querySelector('td:nth-child(4)').textContent.trim();
    }).join(',');

    // Show loading state
    sendSmsText.classList.add('hidden');
    sendSmsLoading.classList.remove('hidden');
    sendSmsBtn.disabled = true;

    // Send SMS via AJAX
    fetch('/lydo_admin/send-sms-to-scholars', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            selected_emails: selectedEmails,
            message: message
        })
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
            
            closeSmsModalHandler();
        } else {
            throw new Error(data.message || 'Failed to send SMS');
        }
    })
    .catch(error => {
        console.error('SMS sending error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to send SMS: ' + error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        // Reset loading state
        sendSmsText.classList.remove('hidden');
        sendSmsLoading.classList.add('hidden');
        sendSmsBtn.disabled = false;
    });
});

// Update the updateButtons function to include SMS button
function updateButtons() {
    const selectedCount = document.querySelectorAll('.applicant-checkbox:checked').length;
    const hasSelection = selectedCount > 0;

    copyNamesBtn.disabled = !hasSelection;
    emailSelectedBtn.disabled = !hasSelection;
    smsSelectedBtn.disabled = !hasSelection;
    
    copyNamesBtn.classList.toggle('hidden', !hasSelection);
    emailSelectedBtn.classList.toggle('hidden', !hasSelection);
    smsSelectedBtn.classList.toggle('hidden', !hasSelection);
}
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const copyNamesBtn = document.getElementById('copyNamesBtn');
    const emailSelectedBtn = document.getElementById('emailSelectedBtn');
    const smsSelectedBtn = document.getElementById('smsSelectedBtn');
    const selectAll = document.getElementById('selectAll');
    const tbody = document.querySelector('table tbody');

    // return only checkboxes from visible rows
    function getVisibleCheckboxes() {
        return Array.from(document.querySelectorAll('.applicant-checkbox'))
            .filter(ch => {
                const tr = ch.closest('tr');
                // row may be hidden via display:none or removed
                return tr && tr.offsetParent !== null;
            });
    }

    // update buttons visibility & disabled state
    function refreshButtons() {
        const visible = getVisibleCheckboxes();
        const checked = visible.filter(c => c.checked).length;
        const hasSelection = checked > 0;

        [copyNamesBtn, emailSelectedBtn, smsSelectedBtn].forEach(btn => {
            if (!btn) return;
            btn.classList.toggle('hidden', !hasSelection);
            btn.disabled = !hasSelection;
        });

        // keep selectAll in sync with visible checkboxes
        if (selectAll) {
            selectAll.checked = visible.length > 0 && visible.every(c => c.checked);
            selectAll.indeterminate = visible.some(c => c.checked) && !selectAll.checked;
        }
    }

    // attach change listener to each applicant checkbox
    function attachCheckboxListeners() {
        document.querySelectorAll('.applicant-checkbox').forEach(ch => {
            // avoid duplicate listeners by marking
            if (ch._listenerAttached) return;
            ch.addEventListener('change', () => {
                refreshButtons();
            });
            ch._listenerAttached = true;
        });
    }

    // selectAll behavior: toggle only visible checkboxes
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            const visible = getVisibleCheckboxes();
            visible.forEach(ch => { ch.checked = selectAll.checked; });
            refreshButtons();
        });
    }

    // observe tbody for row visibility / DOM changes (filtering hides rows via style)
    if (tbody) {
        const mo = new MutationObserver(() => {
            attachCheckboxListeners();
            refreshButtons();
        });
        mo.observe(tbody, { childList: true, subtree: true, attributes: true, attributeFilter: ['style', 'class'] });
    }

    // initial setup
    attachCheckboxListeners();
    refreshButtons();

    // expose global helper if other scripts call updateButtons()
    window.updateButtons = refreshButtons;
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');
    const academicYearSelect = document.getElementById('academicYearSelect');
    const initialScreeningSelect = document.getElementById('initialScreeningSelect');
    const table = document.querySelector('table tbody');
    const tableHeadCols = document.querySelectorAll('table thead th').length || 8;

    // prevent full form submit / refresh
    if (filterForm) filterForm.addEventListener('submit', e => e.preventDefault());

    // helper to normalize text
    const norm = s => (s||'').toString().trim().toLowerCase();

    // Update URL query params without reloading
    function updateUrlParams() {
        const params = new URLSearchParams(window.location.search);
        if (searchInput) {
            if (searchInput.value) params.set('search', searchInput.value);
            else params.delete('search');
        }
        if (barangaySelect) {
            if (barangaySelect.value) params.set('barangay', barangaySelect.value);
            else params.delete('barangay');
        }
        if (academicYearSelect) {
            if (academicYearSelect.value) params.set('academic_year', academicYearSelect.value);
            else params.delete('academic_year');
        }
        if (initialScreeningSelect) {
            if (initialScreeningSelect.value && initialScreeningSelect.value !== 'all') params.set('initial_screening', initialScreeningSelect.value);
            else params.delete('initial_screening');
        }
        const newUrl = `${location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        history.replaceState(null, '', newUrl);
    }

    // Create / remove "no results" row
    let noResultsRow = null;
    function showNoResults(show) {
        if (show) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.innerHTML = `<td colspan="${tableHeadCols}" class="px-4 py-2 text-center text-sm text-gray-500">No applicants found.</td>`;
                table.appendChild(noResultsRow);
            }
        } else {
            if (noResultsRow && noResultsRow.parentNode) noResultsRow.parentNode.removeChild(noResultsRow);
            noResultsRow = null;
        }
    }

    // Core client-side filtering
    function applyFilters() {
        if (!table) return;
        const q = norm(searchInput && searchInput.value);
        const barangay = norm(barangaySelect && barangaySelect.value);
        const acad = norm(academicYearSelect && academicYearSelect.value);
        const screening = (initialScreeningSelect && initialScreeningSelect.value) ? initialScreeningSelect.value.toLowerCase() : '';

        let visible = 0;
        const rows = Array.from(table.querySelectorAll('tr')).filter(r => !r.isSameNode(noResultsRow));
        rows.forEach(row => {
            // cells: 2=name, 3=barangay, 4=email, 5=school, 6=acad year, last=screening badge
            const nameCell = row.querySelector('td:nth-child(2)')?.textContent || '';
            const barangayCell = row.querySelector('td:nth-child(3)')?.textContent || '';
            const acadCell = row.querySelector('td:nth-child(6)')?.textContent || '';
            const screeningCell = row.querySelector('td:last-child')?.textContent || '';

            const matchesSearch = q === '' || [nameCell, barangayCell, row.textContent].join(' ').toLowerCase().includes(q);
            const matchesBarangay = barangay === '' || norm(barangayCell).includes(barangay);
            const matchesAcad = acad === '' || norm(acadCell).includes(acad);
            const matchesScreening = (!screening || screening === 'all') || norm(screeningCell).includes(screening);

            if (matchesSearch && matchesBarangay && matchesAcad && matchesScreening) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        showNoResults(visible === 0);
        updateUrlParams();
    }

    // Debounce helper
    function debounce(fn, wait = 250) {
        let t;
        return function(...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    // Attach listeners
    if (searchInput) searchInput.addEventListener('input', debounce(applyFilters, 250));
    if (barangaySelect) barangaySelect.addEventListener('change', applyFilters);
    if (academicYearSelect) academicYearSelect.addEventListener('change', applyFilters);
    if (initialScreeningSelect) initialScreeningSelect.addEventListener('change', applyFilters);

    // Apply initial filter using existing query params (keeps server-rendered selection)
    applyFilters();
});
</script>



</body>

</html>