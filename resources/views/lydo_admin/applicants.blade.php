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
            <div class="flex-1 overflow-y-auto p-4 md:p-5 text-[16px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-black-800">List of Applicants</h2>
                </div>

<!-- Filter Section -->
<div class="bg-white p-4 rounded-lg shadow-sm mb-6">
    <div class="flex flex-col md:flex-row gap-4" id="filterForm">
        <div class="flex-1">
            <input type="text" id="searchInput" placeholder="Search by name..." 
                   class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
        </div>
        <div class="flex-1">
            <select id="barangaySelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                <option value="">All Barangays</option>
                @foreach($barangays as $barangay)
                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>
                        {{ $barangay }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <select id="academicYearSelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                <option value="">All Academic Years</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
<div class="flex-1">
    <select id="initialScreeningSelect" class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
        <option value="all" {{ $initialScreeningStatus == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="Pending" {{ $initialScreeningStatus == 'Pending' ? 'selected' : '' }}>Pending For Initial Screening</option>
        <option value="Approved" {{ $initialScreeningStatus == 'Approved' ? 'selected' : '' }}>Approved From Mayor Staff</option>
        <option value="Rejected" {{ $initialScreeningStatus == 'Rejected' ? 'selected' : '' }}>Rejected From Mayor Staff</option>
        <option value="Reviewed" {{ $initialScreeningStatus == 'Reviewed' ? 'selected' : '' }}>Reviewed From Lydo Staff</option>
    </select>
</div>
    </div>
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
        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Initial Screening</th>
        <!-- NEW COLUMN: Application History -->
        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Application History</th>
    </tr>
</thead>

<tbody>
    @forelse($applicants as $applicant)
        <tr class="hover:bg-gray-50 border-b">
            <td class="px-4 border border-gray-200 py-2 text-center">
                <input type="checkbox" name="selected_applicants" value="{{ $applicant->applicant_id }}" class="applicant-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
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
            <td class="px-4 border border-gray-200 py-2 text-center">
                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                    @if($applicant->initial_screening === 'Approved') bg-green-100 text-green-800
                    @elseif($applicant->initial_screening === 'Rejected') bg-red-100 text-red-800
                    @elseif($applicant->initial_screening === 'Reviewed') bg-blue-100 text-blue-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ $applicant->initial_screening ?? 'Pending' }}
                </span>
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
        </tr>
    @empty
        <tr>
            <td colspan="9" class="px-4 py-2 text-center text-sm text-gray-500">
                No applicants found.
            </td>
        </tr>
    @endforelse
</tbody>               </table>
                    </div>

                 
                    <!-- Pagination -->
<!-- Pagination -->
<div class="px-6 py-4 bg-white border-t border-gray-200">
    <div class="pagination-container" id="paginationContainer"></div>
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
            <script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize pagination and filtering
    initializeApplicantData();
    initializeApplicantPagination();
    initializeApplicantFiltering();

    // Rest of your existing JavaScript code for checkboxes, buttons, etc.
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.applicant-checkbox');
    const copyNamesBtn = document.getElementById('copyNamesBtn');
    const emailSelectedBtn = document.getElementById('emailSelectedBtn');


                    // Email modal elements
                    const emailModal = document.getElementById('emailModal');
                    const closeEmailModal = document.getElementById('closeEmailModal');
                    const cancelEmailBtn = document.getElementById('cancelEmailBtn');
                    const emailForm = document.getElementById('emailForm');
                    const emailSubject = document.getElementById('emailSubject');
                    const emailMessage = document.getElementById('emailMessage');
                    const recipientsPreview = document.getElementById('recipientsPreview');
                    const sendEmailBtn = document.getElementById('sendEmailBtn');
                    const sendEmailText = document.getElementById('sendEmailText');
                    const sendEmailLoading = document.getElementById('sendEmailLoading');

                    // Select all checkbox functionality
                    selectAll.addEventListener('change', function() {
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateButtons();
                        updateRecipientsPreview();
                    });

                    // Update button states
                    function updateButtons() {
                        const selectedCount = document.querySelectorAll('.applicant-checkbox:checked').length;
                        const hasSelection = selectedCount > 0;

                        copyNamesBtn.disabled = !hasSelection;
                        emailSelectedBtn.disabled = !hasSelection;
                        copyNamesBtn.classList.toggle('hidden', !hasSelection);
                        emailSelectedBtn.classList.toggle('hidden', !hasSelection);
                    }

                    // Individual checkbox change
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            updateButtons();
                            updateRecipientsPreview();

                            // Update selectAll checkbox state
                            const allChecked = [...checkboxes].every(cb => cb.checked);
                            const someChecked = [...checkboxes].some(cb => cb.checked);

                            selectAll.checked = allChecked;
                            selectAll.indeterminate = someChecked && !allChecked;
                        });
                    });

                    // Update recipients preview
                    function updateRecipientsPreview() {
                        const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');

                        if (selectedCheckboxes.length === 0) {
                            recipientsPreview.innerHTML = 'No recipients selected';
                            return;
                        }

                        const recipients = Array.from(selectedCheckboxes).map(checkbox => {
                            const row = checkbox.closest('tr');
                            const name = row.querySelector('td:nth-child(2)').textContent.trim();
                            const email = row.querySelector('td:nth-child(4)').textContent.trim();
                            return `${name} (${email})`;
                        });

                        recipientsPreview.innerHTML = recipients.join('<br>');
                    }

                    // Copy Names button functionality
  copyNamesBtn.addEventListener('click', function() {
    const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        Swal.fire({
            title: 'No Selection!',
            text: 'Please select at least one applicant to copy names.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Group selected applicants by barangay
    const barangayGroups = {};
    selectedCheckboxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const name = row.querySelector('td:nth-child(2)').textContent.trim();
        const barangay = row.querySelector('td:nth-child(3)').textContent.trim();
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
            text: 'Selected applicant names grouped by barangay copied to clipboard!',
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

                    // Email Selected button functionality
                    emailSelectedBtn.addEventListener('click', function() {
                        const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');

                        if (selectedCheckboxes.length === 0) {
                            Swal.fire({
                                title: 'No Selection!',
                                text: 'Please select at least one applicant to send email.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        updateRecipientsPreview();
                        emailModal.classList.remove('hidden');
                        emailSubject.focus();
                    });

                    // Close email modal
                    function closeEmailModalHandler() {
                        emailModal.classList.add('hidden');
                        emailForm.reset();
                        sendEmailText.classList.remove('hidden');
                        sendEmailLoading.classList.add('hidden');
                        sendEmailBtn.disabled = false;
                    }

                    closeEmailModal.addEventListener('click', closeEmailModalHandler);
                    cancelEmailBtn.addEventListener('click', closeEmailModalHandler);

                    // Close modal when clicking outside
                    emailModal.addEventListener('click', function(e) {
                        if (e.target === emailModal) {
                            closeEmailModalHandler();
                        }
                    });

                    // Send email form submission
                    emailForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const selectedCheckboxes = document.querySelectorAll('.applicant-checkbox:checked');
                        const subject = emailSubject.value.trim();
                        const message = emailMessage.value.trim();

                        if (!subject || !message) {
                            Swal.fire({
                                title: 'Missing Information!',
                                text: 'Please fill in both subject and message fields.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        if (selectedCheckboxes.length === 0) {
                            Swal.fire({
                                title: 'No Recipients!',
                                text: 'No applicants selected to send email to.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        // Collect recipient data
                        const recipients = Array.from(selectedCheckboxes).map(checkbox => {
                            const row = checkbox.closest('tr');
                            return {
                                id: checkbox.value,
                                name: row.querySelector('td:nth-child(2)').textContent.trim(),
                                email: row.querySelector('td:nth-child(4)').textContent.trim()
                            };
                        });

                        // Show loading state
                        sendEmailText.classList.add('hidden');
                        sendEmailLoading.classList.remove('hidden');
                        sendEmailBtn.disabled = true;

                        // Send email via AJAX
                        fetch('/lydo_admin/send-email-to-applicants', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                recipients: recipients,
                                subject: subject,
                                message: message
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: `Email sent successfully to ${recipients.length} applicant(s)!`,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                closeEmailModalHandler();
                            } else {
                                throw new Error(data.message || 'Failed to send email');
                            }
                        })
                        .catch(error => {
                            console.error('Email sending error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to send email: ' + error.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        })
                        .finally(() => {
                            // Reset loading state
                            sendEmailText.classList.remove('hidden');
                            sendEmailLoading.classList.add('hidden');
                            sendEmailBtn.disabled = false;
                        });
                    });

                    // Auto-submit form when filters change
                    const searchInput = document.getElementById('searchInput');
                    const barangaySelect = document.getElementById('barangaySelect');
                    const academicYearSelect = document.getElementById('academicYearSelect');
                    const filterForm = document.getElementById('filterForm');

                    // Function to submit form with debounce for search input
                    let searchTimeout;
                    function submitForm() {
                        filterForm.submit();
                    }

                    function debounceSubmit() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(submitForm, 500); // 500ms delay
                    }

                    // Event listeners for filter changes - filters apply automatically
                    searchInput.addEventListener('input', debounceSubmit);
                    barangaySelect.addEventListener('change', submitForm);
                    academicYearSelect.addEventListener('change', submitForm);

                    // Initialize button states
                    updateButtons();
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
            </script>
        </div>
    </div>
    
<!-- ⚡ JS -->
<script>
    document.getElementById("notifBell").addEventListener("click", function () {
        let dropdown = document.getElementById("notifDropdown");
        dropdown.classList.toggle("hidden");

        // remove badge when opened
        let notifCount = document.getElementById("notifCount");
        if (notifCount) {
            notifCount.remove();
        }
    });
</script>
<script>
// Global pagination state
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
    
    // Get the text content and split by comma for "Last Name, First Name" format
    const nameText = nameCell.textContent.trim().toLowerCase();
    const nameParts = nameText.split(',');
    
    if (nameParts.length >= 2) {
        // Return "Last Name First Name" for proper alphabetical sorting
        return (nameParts[0] + nameParts[1]).toLowerCase();
    }
    
    return nameText.toLowerCase();
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
function initializeApplicantData() {
    const tableRows = Array.from(document.querySelectorAll('table tbody tr'));
    paginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by last name
    paginationState.allRows = sortRowsAlphabetically(paginationState.allRows);
    paginationState.filteredRows = [...paginationState.allRows];
}

// Initialize pagination
function initializeApplicantPagination() {
    updateApplicantPagination();
}

// Update pagination display
function updateApplicantPagination() {
    const state = paginationState;
    const container = document.getElementById('paginationContainer');
    
    if (!container) return;
    
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
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    const startItem = state.filteredRows.length === 0 ? 0 : Math.min((state.currentPage - 1) * state.rowsPerPage + 1, state.filteredRows.length);
    const endItem = Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length);
    
    container.innerHTML = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> applicants
            </div>
            
            <div class="flex items-center space-x-1">
                <!-- First Page -->
                <button onclick="changeApplicantPage(1)" 
                    class="px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 ${
                        state.currentPage === 1 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>
                
                <!-- Previous Page -->
                <button onclick="changeApplicantPage(${state.currentPage - 1})" 
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === 1 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-left"></i>
                </button>
                
                <!-- Page Info -->
                <div class="flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 bg-white">
                    Page 
                    <input type="number" 
                           class="mx-2 w-12 text-center border border-gray-300 rounded px-1 py-1 text-sm" 
                           value="${state.currentPage}" 
                           min="1" 
                           max="${totalPages}" 
                           onchange="goToApplicantPage(this.value)">
                    of ${totalPages}
                </div>
                
                <!-- Next Page -->
                <button onclick="changeApplicantPage(${state.currentPage + 1})" 
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === totalPages 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>
                
                <!-- Last Page -->
                <button onclick="changeApplicantPage(${totalPages})" 
                    class="px-3 py-2 text-sm font-medium rounded-r-md border border-gray-300 ${
                        state.currentPage === totalPages 
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
        </div>
    `;
}

// Change page
function changeApplicantPage(page) {
    const state = paginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateApplicantPagination();
}

// Go to specific page
function goToApplicantPage(page) {
    const state = paginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateApplicantPagination();
}

// Initialize filtering functionality
function initializeApplicantFiltering() {
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');
    const academicYearSelect = document.getElementById('academicYearSelect');

    function filterApplicantTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedBarangay = barangaySelect.value;
        const selectedAcademicYear = academicYearSelect.value;

        const filteredRows = paginationState.allRows.filter(row => {
            const nameCell = row.cells[1];
            const barangayCell = row.cells[2];
            const academicYearCell = row.cells[5];

            if (!nameCell || !barangayCell || !academicYearCell) return false;

            const name = nameCell.textContent.toLowerCase();
            const barangay = barangayCell.textContent.trim();
            const academicYear = academicYearCell.textContent.trim();

            const nameMatch = name.includes(searchTerm);
            const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
            const academicYearMatch = !selectedAcademicYear || academicYear === selectedAcademicYear;

            return nameMatch && barangayMatch && academicYearMatch;
        });

        // Sort filtered results alphabetically
        const sortedFilteredRows = sortRowsAlphabetically(filteredRows);

        // Update filtered rows and reset to page 1
        paginationState.filteredRows = sortedFilteredRows;
        paginationState.currentPage = 1;
        updateApplicantPagination();
        
        // Reset select all checkbox
        document.getElementById('selectAll').checked = false;
        document.getElementById('selectAll').indeterminate = false;
    }

    // Add event listeners with debouncing
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterApplicantTable, 300));
    }
    if (barangaySelect) {
        barangaySelect.addEventListener('change', filterApplicantTable);
    }
    if (academicYearSelect) {
        academicYearSelect.addEventListener('change', filterApplicantTable);
    }
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


// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApplicantData();
    initializeApplicantPagination();
    initializeApplicantFiltering();
});

// Add this to your JavaScript
const initialScreeningSelect = document.getElementById('initialScreeningSelect');
if (initialScreeningSelect) {
    initialScreeningSelect.addEventListener('change', function() {
        // This will trigger a page reload with the new filter
        window.location.href = updateUrlParameter(window.location.href, 'initial_screening', this.value);
    });
}

// Helper function to update URL parameters
function updateUrlParameter(url, param, value) {
    const urlObj = new URL(url);
    urlObj.searchParams.set(param, value);
    return urlObj.toString();
}
</script>
<script>
// Application History Functions
function viewApplicantDocuments(applicantId, applicantName, status) {
    console.log('Viewing documents for:', { applicantId, applicantName, status });
    
    // Show loading
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    fetch(`/lydo_admin/applicant-documents/${applicantId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Documents API Response:', data);
            
            if (data.success) {
                showApplicationModal(data.documents, null, applicantName, status, 'documents');
            } else {
                throw new Error(data.message || 'Failed to load documents');
            }
        })
        .catch(error => {
            console.error('Error fetching documents:', error);
            Swal.fire('Error', 'Failed to load applicant documents.', 'error');
        })
        .finally(() => {
            // Hide loading
            document.getElementById('loadingOverlay').style.display = 'none';
        });
}

function viewApplicantIntakeSheet(applicantId, applicantName, status) {
    console.log('Viewing intake sheet for:', { applicantId, applicantName, status });
    
    // Show loading
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    // First, get the application_personnel_id for this applicant
    fetch(`/lydo_admin/get-application-personnel/${applicantId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.application_personnel_id) {
                // Now fetch the intake sheet
                return fetch(`/lydo_admin/intake-sheet/${data.application_personnel_id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(intakeData => {
                        if (intakeData.success && intakeData.intakeSheet) {
                            showApplicationModal(intakeData.intakeSheet.documents || intakeData.intakeSheet, intakeData.intakeSheet, applicantName, status, 'intake');
                        } else {
                            throw new Error(intakeData.message || 'Failed to load intake sheet');
                        }
                    });
            } else {
                throw new Error('No application personnel record found');
            }
        })
        .catch(error => {
            console.error('Error fetching intake sheet:', error);
            Swal.fire('Error', 'Failed to load applicant intake sheet.', 'error');
        })
        .finally(() => {
            // Hide loading
            document.getElementById('loadingOverlay').style.display = 'none';
        });
}

function showApplicationModal(documentsData, intakeSheetData, applicantName, status, type) {
    const modal = document.getElementById('applicationHistoryModal');
    const modalContent = document.getElementById('modalContent');
    const modalTitle = document.getElementById('modalTitle');
    const applicantBasicInfo = document.getElementById('applicantBasicInfo');
    const intakeSheetInfo = document.getElementById('intakeSheetInfo');
    const documentsContainer = document.getElementById('documentsContainer');

    // Set modal title
    modalTitle.textContent = `Application Details - ${applicantName}`;

    // Populate basic applicant info
    let basicInfoHtml = `
        <p><strong>Name:</strong> ${applicantName}</p>
        <p><strong>Status:</strong> <span class="px-2 py-1 rounded text-sm ${getStatusColor(status)}">${status}</span></p>
    `;

    if (intakeSheetData) {
        basicInfoHtml += `
            <p><strong>Gender:</strong> ${intakeSheetData.applicant_gender || '-'}</p>
            <p><strong>Remarks:</strong> ${intakeSheetData.remarks || '-'}</p>
            <p><strong>Barangay:</strong> ${intakeSheetData.head_barangay || '-'}</p>
        `;
    }

    applicantBasicInfo.innerHTML = basicInfoHtml;

    // Show/hide intake sheet info based on type
    if (type === 'intake' && intakeSheetData) {
        intakeSheetInfo.classList.remove('hidden');
        populateIntakeSheetInfo(intakeSheetData);
    } else {
        intakeSheetInfo.classList.add('hidden');
    }

    // Populate documents
    populateDocuments(documentsData || intakeSheetData);

    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function populateIntakeSheetInfo(intakeSheetData) {
    // Populate Head of Family Information
    const headOfFamilyInfo = document.getElementById('headOfFamilyInfo');
    headOfFamilyInfo.innerHTML = `
        <div>
            <p><strong>Name:</strong> ${intakeSheetData.applicant_name || '-'}</p>
            <p><strong>Sex:</strong> ${intakeSheetData.applicant_gender || '-'}</p>
            <p><strong>Remarks:</strong> ${intakeSheetData.remarks || '-'}</p>
            <p><strong>Date of Birth:</strong> ${intakeSheetData.head_dob || '-'}</p>
            <p><strong>Place of Birth:</strong> ${intakeSheetData.head_pob || '-'}</p>
        </div>
        <div>
            <p><strong>Address:</strong> ${intakeSheetData.head_address || '-'}</p>
            <p><strong>Zone:</strong> ${intakeSheetData.head_zone || '-'}</p>
            <p><strong>Barangay:</strong> ${intakeSheetData.head_barangay || '-'}</p>
            <p><strong>Religion:</strong> ${intakeSheetData.head_religion || '-'}</p>
        </div>
        <div>
            <p><strong>Serial No.:</strong> ${intakeSheetData.serial_number || '-'}</p>
            <p><strong>4Ps:</strong> ${intakeSheetData.head_4ps || '-'}</p>
            <p><strong>IP No.:</strong> ${intakeSheetData.head_ipno || '-'}</p>
            <p><strong>Education:</strong> ${intakeSheetData.head_educ || '-'}</p>
            <p><strong>Occupation:</strong> ${intakeSheetData.head_occ || '-'}</p>
        </div>
    `;
    
    // Populate Household Information
    const householdInfo = document.getElementById('householdInfo');
    householdInfo.innerHTML = `
        <div>
            <p><strong>Total Family Income:</strong> ${formatCurrency(intakeSheetData.house_total_income)}</p>
            <p><strong>Total Family Net Income:</strong> ${formatCurrency(intakeSheetData.house_net_income)}</p>
            <p><strong>Other Source of Income:</strong> ${formatCurrency(intakeSheetData.other_income)}</p>
        </div>
        <div>
            <p><strong>House (Owned/Rented):</strong> ${intakeSheetData.house_house || '-'}</p>
            <p><strong>Lot (Owned/Rented):</strong> ${intakeSheetData.house_lot || '-'}</p>
            <p><strong>Electricity Source:</strong> ${intakeSheetData.house_electric || '-'}</p>
            <p><strong>Water:</strong> ${intakeSheetData.house_water || '-'}</p>
        </div>
    `;
    
    // Populate Family Members Table
    const familyMembersTable = document.getElementById('familyMembersTable');
    if (intakeSheetData.family_members && intakeSheetData.family_members.length > 0) {
        let familyMembersHtml = '';
        intakeSheetData.family_members.forEach(member => {
            familyMembersHtml += `
                <tr>
                    <td>${member.NAME || member.name || '-'}</td>
                    <td>${member.RELATION || member.relationship || '-'}</td>
                    <td>${member.BIRTHDATE || member.birthdate || '-'}</td>
                    <td>${member.AGE || member.age || '-'}</td>
                    <td>${member.SEX || member.sex || '-'}</td>
                    <td>${member['CIVIL STATUS'] || member.civil_status || '-'}</td>
                    <td>${member['EDUCATIONAL ATTAINMENT'] || member.education || '-'}</td>
                    <td>${member.OCCUPATION || member.occupation || '-'}</td>
                    <td>${formatCurrency(member.INCOME || member.income)}</td>
                    <td>${member.REMARKS || member.remarks || '-'}</td>
                </tr>
            `;
        });
        familyMembersTable.innerHTML = familyMembersHtml;
    } else {
        familyMembersTable.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-gray-500">No family members found</td></tr>';
    }
    
    // Populate Service Records Table
    const serviceRecordsTable = document.getElementById('serviceRecordsTable');
    if (intakeSheetData.social_service_records && intakeSheetData.social_service_records.length > 0) {
        let serviceRecordsHtml = '';
        intakeSheetData.social_service_records.forEach(record => {
            serviceRecordsHtml += `
                <tr>
                    <td>${record.DATE || record.date || '-'}</td>
                    <td>${record['PROBLEM/NEED'] || record.problem || '-'}</td>
                    <td>${record['ACTION/ASSISTANCE GIVEN'] || record.action || '-'}</td>
                    <td>${record.REMARKS || record.remarks || '-'}</td>
                </tr>
            `;
        });
        serviceRecordsTable.innerHTML = serviceRecordsHtml;
    } else {
        serviceRecordsTable.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500">No service records found</td></tr>';
    }
}

function populateDocuments(data) {
    const documentsContainer = document.getElementById('documentsContainer');
    const documentTitles = {
        'doc_application_letter': 'Application Letter',
        'doc_cert_reg': 'Certificate of Registration',
        'doc_grade_slip': 'Grade Slip',
        'doc_brgy_indigency': 'Barangay Indigency',
        'doc_student_id': 'Student ID'
    };

    let documentsHtml = '';
    let availableDocuments = 0;

    Object.keys(documentTitles).forEach(docType => {
        const docUrl = data[docType];
        if (docUrl && docUrl !== 'null') {
            availableDocuments++;
            documentsHtml += `
                <div class="document-section mb-6 bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-800">${documentTitles[docType]}</h4>
                    </div>
                    <div class="p-4">
                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                            <iframe
                                src="${docUrl}"
                                width="100%"
                                height="500"
                                style="border: none;"
                                title="${documentTitles[docType]}"
                                onerror="this.closest('.document-section').innerHTML='<div class=\\'p-4 text-center text-red-500\\'>Failed to load document</div>'">
                                <p class="p-4 text-center text-gray-500">Your browser does not support iframes.
                                    <a href="${docUrl}" target="_blank" class="text-blue-500 hover:text-blue-700 underline">Click here to view the document</a>
                                </p>
                            </iframe>
                        </div>
                        <div class="mt-2 flex justify-between items-center">
                            <span class="text-sm text-gray-500">Document ${availableDocuments}</span>
                            <a href="${docUrl}" target="_blank" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                <i class="fas fa-external-link-alt mr-1"></i> Open in new tab
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }
    });

    if (availableDocuments === 0) {
        documentsHtml = '<p class="text-center text-gray-500 py-8">No documents available for viewing.</p>';
    }

    documentsContainer.innerHTML = documentsHtml;
}

function closeApplicationModal() {
    const modal = document.getElementById('applicationHistoryModal');
    const modalContent = document.getElementById('modalContent');

    // Add closing animation
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    // Hide modal after animation
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Helper functions
function getStatusColor(status) {
    switch(status) {
        case 'Approved': return 'bg-green-100 text-green-800';
        case 'Rejected': return 'bg-red-100 text-red-800';
        case 'Reviewed': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function formatCurrency(amount) {
    if (!amount || isNaN(amount)) return '-';
    return '₱' + parseFloat(amount).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Add CSS for intake sections
const style = document.createElement('style');
style.textContent = `
    .intake-section {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .intake-section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .intake-table {
        width: 100%;
        border-collapse: collapse;
    }
    .intake-table th,
    .intake-table td {
        border: 1px solid #e5e7eb;
        padding: 0.75rem;
        text-align: left;
    }
    .intake-table th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #374151;
    }
    .document-section {
        margin-bottom: 1.5rem;
    }
`;
document.head.appendChild(style);
</script>
<script src="{{ asset('js/spinner.js') }}"></script>

</body>

</html>
