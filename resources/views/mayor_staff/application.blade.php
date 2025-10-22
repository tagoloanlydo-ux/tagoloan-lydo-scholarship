<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/mayor_app.css') }}" />i
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
     <link rel="icon" type="image/x-icon" href="/img/LYDO.png">
     <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>
<body class="bg-gray-50">

    <div class="dashboard-grid">
            <!-- Header -->
                <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
                    <div class="flex items-center">
                        <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                            <h1 class="text-2xl font-bold text-white ml-4">Lydo Scholarship</h1>
                    </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Mayor Staff</span>
                                    
                                    <div class="relative">
                                        <!-- 🔔 Bell Icon -->
                                        <button id="notifBell" class="relative focus:outline-none">
                                            <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                                            @if($showBadge && $notifications->count() > 0)
                                                <span id="notifCount"
                                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center">
                                                    {{ $notifications->count() }}
                                                </span>
                                            @endif
                                        </button>

                                        <!-- 🔽 Dropdown -->
                                        <div id="notifDropdown"
                                            class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                            <div class="p-3 border-b font-semibold text-violet-600">Notifications</div>
                                            <ul class="max-h-60 overflow-y-auto">
                                                @forelse($notifications as $notif)
                                                    <li class="px-4 py-2 hover:bg-gray-50 text-base border-b">
                                                        {{-- New Application --}}
                                                        @if($notif->type === 'application')
                                                            <p class="text-blue-600 font-medium">
                                                                📝 {{ $notif->name }} submitted a new application
                                                            </p>
                                                        {{-- New Remark --}}
                                                        @elseif($notif->type === 'remark')
                                                            <p class="text-purple-600 font-medium">
                                                                💬 New remark for {{ $notif->name }}:
                                                                <b>{{ $notif->remarks }}</b>
                                                            </p>
                                                        @endif

                                                        {{-- Time ago --}}
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

                <!-- Main Content -->
                <div class="flex flex-1 overflow-hidden">
                    <!-- Sidebar -->
    <div class="w-16 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
        <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
            <ul class="side-menu top space-y-4">
                <li>
                    <a href="/mayor_staff/dashboard" class="w-ful flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                        <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
                        <span class="ml-4 hidden md:block text-lg">Dashboard</span>
                    </a>
                </li>
                <li class="relative">
                    <button onclick="toggleDropdown('scholarMenu')"
                        class="w-full flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white focus:outline-none">
                        <div class="flex items-center">
                            <i class="bx bxs-graduation text-center mx-auto md:mx-0 text-xl text-white-700"></i>
                            <span class="ml-4 hidden md:block text-lg">Applicants</span>
                        </div>
                        <i class="bx bx-chevron-down ml-2"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <ul id="scholarMenu" class="ml-10 mt-2 space-y-2 hidden">
                        <li>
                            <a href="/mayor_staff/application" class="flex items-center p-2 rounded-lg text-white bg-violet-600">
                            <i class="bx bx-search-alt mr-2 text-white-700"></i> Review Applications
                            </a>
                        </li>
                        <li>
                            <a href="/mayor_staff/status" class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                            <i class="bx bx-check-circle mr-2 text-white-700"></i> Update Status
                            </a>
                        </li>
                    </ul>
                </li>
             </ul>
                    <ul class="side-menu space-y-1">
                        <li>
                            <a href="/mayor_staff/settings" class="w-full flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bxs-cog text-center mx-auto md:mx-0 text-xl text-white-700"></i>
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
            <div class="flex-1 main-content-area p-4 md:p-5 text-[16px]">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-3xl font-bold text-gray-800">Review Applicants Application</h5>
                </div>
            <!-- 🔎 View Switch -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <!-- Tab Switch (moved to right side) -->
            <div class="flex gap-2 md:ml-auto">
                <div class="tab active" onclick="showTable()">Pending Review</div>
                 <div class="tab" onclick="showList()">Reviewed Applications</div>
                </div>
             </div>

            <!-- ✅ Table View (Applicants without remarks) -->
            <div id="tableView">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-200">
                        The list below shows applicants who have submitted applications
                    </h3>
                </div>
                <!-- Search & Filter for Pending Review -->
                <div class="flex gap-2 mb-4">
                    {{-- Search --}}
                    <input type="text" id="search_pending"
                        placeholder="Search name..."
                        class="border rounded px-3 py-2 w-64">

                    {{-- Barangay dropdown --}}
                    <select id="barangay_pending" class="border rounded px-3 py-2">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $brgy)
                            <option value="{{ $brgy }}">
                                {{ $brgy }}
                            </option>
                        @endforeach
                    </select>
                </div>
            <table id="pendingTable" class="w-full table-auto border-collapse text-[17px] shadow-lg rounded-lg overflow-visible border border-gray-200">
                <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white uppercase text-sm">
                    <tr>
                        <th class="px-6 py-4 align-middle text-center">#</th>
                        <th class="px-6 py-4 align-middle text-center">Name</th>
                        <th class="px-6 py-4 align-middle text-center">Barangay</th>
                        <th class="px-6 py-4 align-middle text-center">Gender</th>
                        <th class="px-6 py-4 align-middle text-center">Birthday</th>
                        <th class="px-6 py-4 align-middle text-center">Applications</th>
                        <th class="px-6 py-4 align-middle text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($tableApplicants as $index => $applicant)
                    <tr>
                        <td class="px-6 py-4 text-center">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-center">{{ $applicant->applicant_fname }} {{ $applicant->applicant_lname }}</td>
                        <td class="px-6 py-4 text-center">{{ $applicant->applicant_brgy }}</td>
                        <td class="px-6 py-4 text-center">{{ $applicant->applicant_gender }}</td>
                        <td class="px-6 py-4 text-center">{{ $applicant->applicant_bdate }}</td>
                        <td class="px-6 py-4 text-center">
                            <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm" onclick="openApplicationModal({{ $applicant->application_personnel_id }}, 'pending')">View Applications</button>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="dropdown">
                                <button class="text-gray-600 hover:text-gray-800 focus:outline-none" onclick="toggleDropdownMenu({{ $applicant->application_personnel_id }})">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="dropdown-menu-{{ $applicant->application_personnel_id }}" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openEmailModal({{ $applicant->application_personnel_id }}, {{ $applicant->application_personnel_id }}, '{{ $applicant->applicant_fname }} {{ $applicant->applicant_lname }}', '{{ $applicant->applicant_email }}')">
                                        <i class="fas fa-envelope mr-2"></i>Send Email
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openDeleteModal({{ $applicant->application_personnel_id }}, '{{ $applicant->applicant_fname }} {{ $applicant->applicant_lname }}')">
                                        <i class="fas fa-trash mr-2"></i>Delete Application
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

            <!-- ✅ List View (Approved and Rejected applications) -->
    <div id="listView" class="hidden overflow-x-auto">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-700 bg-green-50 p-3 rounded-lg border border-green-200">
            The list below shows applicants who have approved and rejected screening
            </h3>
        </div>
        <!-- Search & Filter for Reviewed Applications -->
        <div class="flex gap-2 mb-4">
            {{-- Search --}}
            <input type="text" id="search_reviewed"
                placeholder="Search name..."
                class="border rounded px-3 py-2 w-64">

            {{-- Barangay dropdown --}}
            <select id="barangay_reviewed" class="border rounded px-3 py-2">
                <option value="">All Barangays</option>
                @foreach($barangays as $brgy)
                    <option value="{{ $brgy }}">
                        {{ $brgy }}
                    </option>
                @endforeach
            </select>
        </div>
            <table id="reviewedTable" class="w-full table-auto border-collapse text-[17px] shadow-lg rounded-lg overflow-visible border border-gray-200">
        <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
            <tr>
            <th class="px-6 py-4 align-middle text-center">#</th>
                <th class="px-6 py-4 align-middle text-center">Name</th>
                <th class="px-6 py-4 align-middle text-center">Barangay</th>
                <th class="px-6 py-4 align-middle text-center">Gender</th>
                <th class="px-6 py-4 align-middle text-center">Birthday</th>
                <th class="px-6 py-4 align-middle text-center">Initial Screening</th>
                <th class="px-6 py-4 align-middle text-center">Application</th>
                <th class="px-6 py-4 align-middle text-center"></th>

            </tr>
        </thead>
        <tbody class="bg-white">
            @foreach($listApplicants as $index => $applicant)
            <tr>
                <td class="px-6 py-4 text-center">{{ $loop->iteration }}</td>
                <td class="px-6 py-4 text-center">{{ $applicant->applicant_fname }} {{ $applicant->applicant_lname }}</td>
                <td class="px-6 py-4 text-center">{{ $applicant->applicant_brgy }}</td>
                <td class="px-6 py-4 text-center">{{ $applicant->applicant_gender }}</td>
                <td class="px-6 py-4 text-center">{{ $applicant->applicant_bdate }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $applicant->initial_screening === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $applicant->initial_screening }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm font-medium transition-colors duration-200 shadow-sm" onclick="openApplicationModal({{ $applicant->application_personnel_id }}, 'reviewed')">View Requirements</button>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="dropdown">
                        <button class="text-gray-600 hover:text-gray-800 focus:outline-none" onclick="toggleDropdownMenu({{ $applicant->application_personnel_id }})">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="dropdown-menu-{{ $applicant->application_personnel_id }}" class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="openEditInitialScreeningModal({{ $applicant->application_personnel_id }}, '{{ $applicant->initial_screening }}')">
                                <i class="fas fa-edit mr-2"></i>Edit Initial Screening
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>
    </div>


     <div id="applicationModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
         <div class="bg-white w-full max-w-6xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl animate-fadeIn">
        
        <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-folder-open text-blue-600"></i>
                    Application Requirements</h2>
            <button onclick="closeApplicationModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-times text-gray-500 text-lg"></i>
            </button>
        </div>
        <!-- Body -->
        <div id="applicationContent" class="p-6 space-y-4">
        <!-- Dynamic Content via JS -->
        </div>
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">

        </div>
    </div>
    </div>

    <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl animate-fadeIn">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
                Confirm Delete
            </h2>
            <button onclick="closeDeleteModal()"
                    class="p-2 rounded-full hover:bg-gray-100 transition">
                <i class="fas fa-times text-gray-500 text-lg"></i>
            </button>
            </div>

        <!-- Body -->
        <div id="deleteModalContent" class="p-6 space-y-4">
        <p class="text-gray-700">Are you sure you want to delete the application for <strong id="deleteApplicantName"></strong>?</p>
        <p class="text-sm text-gray-500">This action cannot be undone.</p>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeDeleteModal()"
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Cancel
        </button>
        <form id="deleteForm" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
            <i class="fas fa-trash mr-2"></i> Delete
            </button>
        </form>
        </div>
    </div>
    </div>

    <!-- Email Modal -->
    <div id="emailModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-3xl h-[600px] overflow-y-auto  rounded-2xl shadow-2xl animate-fadeIn p-6">
        <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-envelope text-blue-600"></i>
            Send Email to Applicant
        </h2>
        <button onclick="closeEmailModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
        </div>
        <form id="emailForm" method="POST" action="/mayor_staff/send-email">
        @csrf
        <input type="hidden" name="application_personnel_id" id="emailApplicationPersonnelId" />
        <div class="mb-4">
            <label for="recipientName" class="block text-gray-700 font-medium mb-1">Recipient</label>
            <input type="text" id="recipientName" class="w-full border rounded px-3 py-2 bg-gray-100" readonly />
        </div>
        <div class="mb-4">
            <label for="recipientEmail" class="block text-gray-700 font-medium mb-1">Email Address</label>
            <input type="email" name="recipient_email" id="recipientEmail" class="w-full border rounded px-3 py-2" required />
        </div>
        <div class="mb-4">
            <label for="emailSubject" class="block text-gray-700 font-medium mb-1">Subject</label>
            <select name="subject" id="emailSubject" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Subject</option>
            <option value="Blurred Application">Blurred Application</option>
            <option value="Can't Access Application">Can't Access Application</option>
            <option value="Both">Both</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Application Issues (check which ones are blurred or can't access)</label>
            <div id="applicationCheckboxes">
            <!-- Checkboxes will be populated by JS -->
            </div>
        </div>
        <div class="mb-4">
            <label for="emailMessage" class="block text-gray-700 font-medium mb-1">Message</label>
            <textarea name="message" id="emailMessage" rows="5" class="w-full border rounded px-3 py-2" required></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeEmailModal()" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">Cancel</button>
            <button type="submit" id="sendEmailBtn" class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition flex items-center gap-2">
            <span id="sendBtnText">Send</span>
            <div id="sendBtnSpinner" class="hidden">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            </button>
        </div>
        </form>
    </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl animate-fadeIn">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-times-circle text-red-600"></i>
            Reject Initial Screening
        </h2>
        <button onclick="closeRejectionModal()"
                class="p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
        <p class="text-gray-700">Please provide the reason for rejecting this application:</p>
        <form id="rejectionForm">
            <div class="mb-4">
            <label for="rejectionReason" class="block text-gray-700 font-medium mb-2">Reason for Rejection</label>
            <textarea id="rejectionReason" name="reason" rows="4" class="w-full border rounded px-3 py-2" placeholder="Enter the reason for rejection..." required></textarea>
            </div>
        </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeRejectionModal()"
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Cancel
        </button>
        <button id="rejectSubmitBtn" onclick="submitRejection()" class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition flex items-center gap-2">
            <i class="fas fa-times"></i>
            <span id="rejectSubmitBtnText">Reject Application</span>
            <div id="rejectSubmitBtnSpinner" class="hidden ml-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            </div>
        </button>
        </div>
    </div>
    </div>

    <!-- Edit Initial Screening Modal -->
    <div id="editInitialScreeningModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl animate-fadeIn">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-edit text-blue-600"></i>
            Edit Initial Screening
        </h2>
        <button onclick="closeEditInitialScreeningModal()"
                class="p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
        <p class="text-gray-700">Update the initial screening status for this application:</p>
        <form id="editInitialScreeningForm" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="application_personnel_id" id="editApplicationPersonnelId" />
        <div class="mb-4">
        <label for="initialScreeningStatus" class="block text-gray-700 font-medium mb-2">Initial Screening Status</label>
        <select id="initialScreeningStatus" name="initial_screening_status" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Status</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>
        </div>
        </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeEditInitialScreeningModal()"
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Cancel
        </button>
        <button onclick="submitEditInitialScreening()" class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
            <i class="fas fa-save mr-2"></i> Update
        </button>
        </div>
    </div>
    </div>

    <script src="{{ asset('js/logout.js') }}"></script>
    <script>const applications = @json($applications);</script>
    <script src="{{ asset('js/application.js') }}"></script>
    <script src="{{ asset('js/mayor_staff_application.js') }}"></script>


</body>
</html>
