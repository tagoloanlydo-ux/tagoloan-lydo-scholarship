<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/renewal.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body class="bg-gray-50">
 <div class="dashboard-grid">
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                   <!-- Navbar -->
                   <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                <div class="relative">
                    @php
                        $badgeCount = ($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0) ? $notifications->where('initial_screening', 'Approved')->count() : 0;
                    @endphp
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
                        <ul class="max-h-60 overflow-y-auto"> @forelse($notifications as $notif) <li class="px-4 py-2 hover:bg-gray-50 text-sm border-b"> @if($notif->initial_screening == 'Approved') <p class="text-green-600 font-medium"> ✅ {{ $notif->name }} passed initial screening </p> @elseif($notif->status == 'Renewed') <p class="text-blue-600 font-medium"> 🔄 {{ $notif->name }} submitted renewal </p> @endif <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                </p>
                            </li> @empty <li class="px-4 py-3 text-gray-500 text-sm">No new notifications</li> @endforelse </ul>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="flex flex-1 overflow-hidden"> 
            <div class="w-16 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
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
                                    <span class="ml-4 hidden md:block text-lg">Screening</span>
                                </div>
                                @if($pendingScreening > 0) <span class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                    {{ $pendingScreening }}
                                </span> @endif
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/renewal" class=" flex items-center justify-between p-3 rounded-lg text-gray-700 bg-violet-600 text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Renewals</span>
                                </div>
                                @if($pendingRenewals > 0) <span id="pendingRenewalsBadge" class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                    {{ $pendingRenewals }}
                                </span> @endif
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
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm"> @csrf <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
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

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex gap-2 mb-4">
                <input type="text" id="nameSearch" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                <select id="barangayFilter" class="border rounded px-3 py-2">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $brgy)
                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <div onclick="showTable()" class="tab active" id="tab-renewal">
                    <i class="fas fa-table mr-1"></i> Process Renewals
                </div>
                <div onclick="showList()" class="tab" id="tab-review">
                    <i class="fas fa-list mr-1"></i> View Status
                </div>
            </div>
        </div>

        <div id="tableView" class="overflow-x-auto">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-200">
                📋 Pending Renewal Applications: Review and process new renewal submissions from scholars
                </h3>
            </div>
            <table class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
        <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white uppercase text-sm">
                <tr>
                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                    <th class="px-4 py-3 border border-gray-200 text-center">Applications</th>
                    <th class="px-4 py-3 border border-gray-200 text-center">Email</th>
                </tr>
            </thead>
           <tbody>
                @php $count = 1; @endphp
                @forelse($tableApplicants as $app)
                    <tr class="hover:bg-gray-50 border-b" data-id="{{ $app->scholar_id }}">
                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $count++ }}</td>
                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                        <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                        <td class="px-4 border border-gray-200 py-2 text-center">
            <button onclick="openRenewalModal({{ $app->scholar_id }})"
                    class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow">
                Review Renewal Docs
            </button>


        </td>
        <td class="px-4 py-2 border border-gray-200 text-center">
        <button
            onclick="openEmailModal(
                '{{ $app->applicant_email }}',
                '{{ $app->applicant_fname }}',
                '{{ $app->renewal_cert_of_reg }}',
                '{{ $app->renewal_grade_slip }}',
                '{{ $app->renewal_brgy_indigency }}'
            )"
            class="px-3 py-1 text-sm bg-green-500 hover:bg-green-600 text-white rounded-lg shadow">
            <i class="fas fa-envelope"></i> Email
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
    </div>


    <div id="emailModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl transform transition-transform scale-95 opacity-0 animate-fadeIn">
        <div class="flex justify-between items-center px-6 py-4 rounded-t-2xl bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-md">
        <h2 class="text-lg font-semibold">Send Email</h2>
        <button onclick="closeEmailModal()" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
        </div>

    
        <div class="p-6 space-y-4">
        <input type="hidden" id="recipientEmail">
        
        <div>
            <label class="block text-sm font-medium text-gray-700">To</label>
            <input id="emailTo" type="text" readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Subject</label>
            <input id="emailSubject" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Message</label>
            <textarea id="emailMessage" rows="5" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"></textarea>
        </div>
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeEmailModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">Cancel</button>
        <button onclick="sendEmail()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Send</button>
        </div>

    </div>
    </div>

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
    </div>

    <div class="flex justify-between items-center gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">

    <button onclick="closeApplicationModal()" 
            class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
        Cancel
    </button>

    <div class="flex gap-3" id="actionButtons" style="display: none;">

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



    <div id="listView" class="hidden overflow-x-auto">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-700 bg-green-50 p-3 rounded-lg border border-green-200">
            Processed Renewal Applications: View applications with their current approval status
            </h3>
        </div>
        <table class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
        <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
            <tr>
                <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                <th class="px-4 py-3 border border-gray-200 text-center">Application</th>
                <th class="px-4 py-3 border border-gray-200 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @php $count = 1; @endphp
            @forelse($listView as $app)
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $count++ }}</td>
                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>   
                <td class="px-4 border border-gray-200 py-2 text-center">
                        @if($app->renewal_status === 'Approved')
                            <span class="status-badge status-approved">Approved</span>
                        @elseif($app->renewal_status === 'Rejected')
                            <span class="status-badge status-rejected">Rejected</span>
                        @else
                            <span class="status-badge">{{ $app->renewal_status }}</span>
                        @endif
                    </td>
    <td class="px-4 py-2 border border-gray-200 text-center">
        <button onclick="openViewRenewalModal({{ $app->scholar_id }})"
                class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow">
            Review Renewal Docs
        </button>
    </td>
    <td class="px-4 py-2 border border-gray-200 text-center">
        <button onclick="openEditRenewalModal({{ $app->scholar_id }}, '{{ $app->renewal_status }}')"
                class="px-3 py-1 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow">
            <i class="fas fa-edit mr-1"></i> Edit
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
        {{ $listView->links() }}
    </div>
    </div>
    </div>
    </div>

    <div id="editRenewalModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl animate-fadeIn">
        

        <div class="flex items-center justify-between px-6 py-4 border-b">
        <h2 class="text-xl font-semibold text-gray-800">
            <i class="fas fa-edit text-yellow-500 mr-2"></i> Edit Renewal Status
        </h2>
        <button onclick="closeEditRenewalModal()" 
                class="p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-gray-500 text-lg"></i>
        </button>
        </div>


        <div class="p-6 space-y-4">
        <input type="hidden" id="editScholarId">

        <label class="block text-gray-700 font-medium mb-2">Renewal Status</label>
        <select id="editRenewalStatus" class="w-full border rounded-lg px-3 py-2">
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>
        </div>


        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeEditRenewalModal()" 
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Cancel
        </button>
        <button onclick="saveEditRenewalStatus()" 
                class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
            Save
        </button>
        </div>
    </div>
    </div>



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

        </div>


        <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
        <button onclick="closeViewRenewalModal()" 
                class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
            Close
        </button>
        </div>
    </div>
    </div>

<script>
function openViewRenewalModal(scholarId) {
    const contentDiv = document.getElementById('viewRenewalContent');
    contentDiv.innerHTML = '';

    if (renewals[scholarId]) {
        renewals[scholarId].forEach(r => {
            const statusBadge = r.renewal_status === 'Approved'
                ? 'bg-green-100 text-green-700'
                : r.renewal_status === 'Rejected'
                ? 'bg-red-100 text-red-700'
                : 'bg-yellow-100 text-yellow-700';

            contentDiv.innerHTML += `
                <div class="border border-gray-200 rounded-xl shadow bg-white p-6 mb-6">
                    
                    <!-- Top Info -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div class="space-y-1 text-gray-700 text-sm">
                            <p><strong>Semester:</strong> ${r.renewal_semester}</p>
                            <p><strong>Academic Year:</strong> ${r.renewal_acad_year}</p>
                            <p><strong>Date Submitted:</strong> ${r.date_submitted}</p>
                        </div>
                        <span class="mt-3 md:mt-0 px-4 py-1 text-sm font-semibold rounded-full ${statusBadge}">
                            ${r.renewal_status}
                        </span>
                    </div>

                    <hr class="my-4">

                    <!-- Documents Section -->
                    <p class="text-sm text-gray-600 mb-3">Note: Please click on all three documents to review them before the Approve/Reject buttons appear.</p>
                    <h4 class="text-gray-800 font-semibold mb-3">Submitted Documents</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="/storage/${r.renewal_cert_of_reg}" target="_blank"
                           class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-blue-50 transition">
                            <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700 text-center">Certificate of Reg.</span>
                        </a>
                        <a href="/storage/${r.renewal_grade_slip}" target="_blank"
                           class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-green-50 transition">
                            <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700 text-center">Grade Slip</span>
                        </a>
                        <a href="/storage/${r.renewal_brgy_indigency}" target="_blank"
                           class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-purple-50 transition">
                            <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700 text-center">Barangay Indigency</span>
                        </a>
                    </div>
                </div>
            `;
        });
    } else {
        contentDiv.innerHTML = `<p class="text-gray-500">No renewal requirements found for this scholar.</p>`;
    }

    document.getElementById('viewRenewalModal').classList.remove('hidden');
}

function closeViewRenewalModal() {
    document.getElementById('viewRenewalModal').classList.add('hidden');
}
</script>


<script>
    const renewals = @json($renewals); // ✅ important
function openRenewalModal(scholarId) {
    const contentDiv = document.getElementById('applicationContent');
    contentDiv.innerHTML = '';

    window.documentsClicked = 0;
    document.getElementById('actionButtons').style.display = 'none';

    if (renewals[scholarId]) {
        selectedRenewalId = renewals[scholarId][0].renewal_id; // latest renewal

        renewals[scholarId].forEach((r, index) => {
            const statusBadge = r.renewal_status === 'Approved'
                ? 'bg-green-100 text-green-700'
                : r.renewal_status === 'Rejected'
                ? 'bg-red-100 text-red-700'
                : 'bg-yellow-100 text-yellow-700';

            contentDiv.innerHTML += `
                <div class="border border-gray-200 rounded-xl shadow bg-white p-6 mb-6">
                    
                    <!-- Top Info -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div class="space-y-1 text-gray-700 text-sm">
                            <p><strong>Semester:</strong> ${r.renewal_semester}</p>
                            <p><strong>Academic Year:</strong> ${r.renewal_acad_year}</p>
                            <p><strong>Date Submitted:</strong> ${r.date_submitted}</p>
                        </div>
                        <span class="mt-3 md:mt-0 px-4 py-1 text-sm font-semibold rounded-full ${statusBadge}">
                            ${r.renewal_status}
                        </span>
                    </div>

                    <hr class="my-4">

                    <!-- Documents Section -->
                    <p class="text-sm text-gray-600 mb-3">Note: Please click on all three documents to review them before the Approve/Reject buttons appear.</p>
                    <h4 class="text-gray-800 font-semibold mb-3">Submitted Documents</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="/storage/${r.renewal_cert_of_reg}" target="_blank" ${index === 0 ? 'onclick="this.querySelector(\'i\').classList.remove(\'text-violet-600\'); this.querySelector(\'i\').classList.add(\'text-green-600\'); window.documentsClicked++; if(window.documentsClicked >= 3) { document.getElementById(\'actionButtons\').style.display = \'flex\'; }"' : ''}
                           class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-blue-50 transition">
                            <i class="fas fa-file-alt text-violet-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700 text-center">Certificate of Reg.</span>
                        </a>
                        <a href="/storage/${r.renewal_grade_slip}" target="_blank" ${index === 0 ? 'onclick="this.querySelector(\'i\').classList.remove(\'text-violet-600\'); this.querySelector(\'i\').classList.add(\'text-green-600\'); window.documentsClicked++; if(window.documentsClicked >= 3) { document.getElementById(\'actionButtons\').style.display = \'flex\'; }"' : ''}
                           class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-green-50 transition">
                            <i class="fas fa-file-alt text-violet-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700 text-center">Grade Slip</span>
                        </a>
                        <a href="/storage/${r.renewal_brgy_indigency}" target="_blank" ${index === 0 ? 'onclick="this.querySelector(\'i\').classList.remove(\'text-violet-600\'); this.querySelector(\'i\').classList.add(\'text-green-600\'); window.documentsClicked++; if(window.documentsClicked >= 3) { document.getElementById(\'actionButtons\').style.display = \'flex\'; }"' : ''}
                           class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-purple-50 transition">
                            <i class="fas fa-file-alt text-violet-600 text-2xl mb-2"></i>
                            <span class="text-sm font-medium text-gray-700 text-center">Barangay Indigency</span>
                        </a>
                    </div>
                </div>
            `;
        });
    } else {
        contentDiv.innerHTML = `<p class="text-gray-500">No renewal requirements found for this scholar.</p>`;
    }

    document.getElementById('openRenewalModal').classList.remove('hidden');
}

</script>
<script>
function updateRenewalStatus(renewalId, status) {
    // Show loading spinner and disable buttons
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    const approveText = document.getElementById('approveText');
    const rejectText = document.getElementById('rejectText');
    const approveSpinner = document.getElementById('approveSpinner');
    const rejectSpinner = document.getElementById('rejectSpinner');

    if (status === 'Approved') {
        approveBtn.disabled = true;
        approveText.textContent = 'Processing...';
        approveSpinner.classList.remove('hidden');
    } else {
        rejectBtn.disabled = true;
        rejectText.textContent = 'Processing...';
        rejectSpinner.classList.remove('hidden');
    }

    if (status === 'Rejected') {
        Swal.fire({
            title: 'Reject Renewal',
            text: 'Please provide a reason for rejection:',
            input: 'textarea',
            inputPlaceholder: 'Enter the reason for rejection...',
            inputValidator: (value) => {
                if (!value) {
                    return 'Reason is required!';
                }
            },
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#6b7280",
            confirmButtonText: "Reject"
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = result.value;
                fetch(`/lydo_staff/renewal/${renewalId}/update-status`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        renewal_status: status,
                        reason: reason
                    })
                })
                .then(res => res.json())
                .then(data => {
                    // Hide loading spinner and re-enable buttons
                    rejectBtn.disabled = false;
                    rejectText.textContent = 'Reject';
                    rejectSpinner.classList.add('hidden');

                    if (data.success) {
                        Swal.fire({
                            title: "Success!",
                            text: `Renewal has been ${status}.`,
                            icon: "success",
                            confirmButtonColor: "#3b82f6"
                        }).then(() => {
                            // Close the modal first
                            closeApplicationModal();

                            // Find the scholar ID from the renewal data and update the row
                            let scholarIdToUpdate = null;
                            for (const [scholarId, renewals] of Object.entries(window.renewals)) {
                                if (renewals.some(r => r.renewal_id === renewalId)) {
                                    scholarIdToUpdate = scholarId;
                                    break;
                                }
                            }

                            if (scholarIdToUpdate) {
                                updateRenewalRowStatus(scholarIdToUpdate, status);
                            } else {
                                // Fallback to reload if we can't find the scholar ID
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire("Error", "Failed to update renewal status.", "error");
                    }
                })
                .catch(err => {
                    // Hide loading spinner and re-enable buttons on error
                    rejectBtn.disabled = false;
                    rejectText.textContent = 'Reject';
                    rejectSpinner.classList.add('hidden');

                    console.error(err);
                    Swal.fire("Error", "Something went wrong.", "error");
                });
            } else {
                // User cancelled, hide loading spinner and re-enable buttons
                rejectBtn.disabled = false;
                rejectText.textContent = 'Reject';
                rejectSpinner.classList.add('hidden');
            }
        });
    } else {
        Swal.fire({
            title: `Are you sure?`,
            text: `Do you want to mark this renewal as ${status}?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: status === 'Approved' ? "#16a34a" : "#dc2626",
            cancelButtonColor: "#6b7280",
            confirmButtonText: status === 'Approved' ? "Yes, Approve" : "Yes, Reject"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/lydo_staff/renewal/${renewalId}/update-status`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        renewal_status: status
                    })
                })
                .then(res => res.json())
                .then(data => {
                    // Hide loading spinner and re-enable buttons
                    approveBtn.disabled = false;
                    approveText.textContent = 'Approve';
                    approveSpinner.classList.add('hidden');

                    if (data.success) {
                        Swal.fire({
                            title: "Success!",
                            text: `Renewal has been ${status}.`,
                            icon: "success",
                            confirmButtonColor: "#3b82f6"
                        }).then(() => {
                            // Close the modal first
                            closeApplicationModal();

                            // Find the scholar ID from the renewal data and update the row
                            let scholarIdToUpdate = null;
                            for (const [scholarId, renewals] of Object.entries(window.renewals)) {
                                if (renewals.some(r => r.renewal_id === renewalId)) {
                                    scholarIdToUpdate = scholarId;
                                    break;
                                }
                            }

                            if (scholarIdToUpdate) {
                                updateRenewalRowStatus(scholarIdToUpdate, status);
                            } else {
                                // Fallback to reload if we can't find the scholar ID
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire("Error", "Failed to update renewal status.", "error");
                    }
                })
                .catch(err => {
                    // Hide loading spinner and re-enable buttons on error
                    approveBtn.disabled = false;
                    approveText.textContent = 'Approve';
                    approveSpinner.classList.add('hidden');

                    console.error(err);
                    Swal.fire("Error", "Something went wrong.", "error");
                });
            } else {
                // User cancelled, hide loading spinner and re-enable buttons
                approveBtn.disabled = false;
                approveText.textContent = 'Approve';
                approveSpinner.classList.add('hidden');
            }
        });
    }
}
</script>



<script>
    function showTable() {
        document.getElementById("tableView").classList.remove("hidden");
        document.getElementById("listView").classList.add("hidden");
        
        // Update tab active states
        document.getElementById("tab-renewal").classList.add("active");
        document.getElementById("tab-review").classList.remove("active");
        
        localStorage.setItem("viewMode", "table"); // save preference
    }

    function showList() {
        document.getElementById("listView").classList.remove("hidden");
        document.getElementById("tableView").classList.add("hidden");
        
        // Update tab active states
        document.getElementById("tab-review").classList.add("active");
        document.getElementById("tab-renewal").classList.remove("active");
        
        localStorage.setItem("viewMode", "list"); // save preference
    }


    document.addEventListener("DOMContentLoaded", function() {
        let viewMode = localStorage.getItem("viewMode") || "table"; // default table
        if(viewMode === "list") {
            showList();
        } else {
            showTable();
        }
    });
</script>
<script>
function closeApplicationModal() {
    document.getElementById('openRenewalModal').classList.add('hidden');
    document.getElementById('actionButtons').style.display = 'none';
}
</script>

<script>
let editingScholarId = null;

function openEditRenewalModal(scholarId, currentStatus) {
    editingScholarId = scholarId;
    document.getElementById("editScholarId").value = scholarId;


    document.getElementById("editRenewalStatus").value = currentStatus;


    document.getElementById("editRenewalModal").classList.remove("hidden");
}

function closeEditRenewalModal() {
    document.getElementById("editRenewalModal").classList.add("hidden");
}


function saveEditRenewalStatus() {
    const newStatus = document.getElementById("editRenewalStatus").value;

    fetch(`/lydo_staff/renewal/update/${editingScholarId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ renewal_status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Renewal status updated successfully.',
                timer: 2000,
                showConfirmButton: false
            });
            closeEditRenewalModal();
            
            updateRenewalRowStatus(editingScholarId, newStatus);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Something went wrong. Please try again.'
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Server error. Try again later.'
        });
    });
}

function updateRenewalRowStatus(scholarId, newStatus) {
    // Find the specific row in the list view table
    const row = document.querySelector(`#listView tbody tr button[onclick*="openEditRenewalModal(${scholarId}"]`).closest('tr');
    
    if (row) {
        // Update the status cell
        const statusCell = row.querySelector('td:nth-child(5)'); // 5th column is status
        
        // Remove existing status classes
        statusCell.querySelector('.status-badge').classList.remove(
            'status-approved', 
            'status-rejected',
            'status-pending'
        );
        
        // Update the status text and add appropriate class
        const statusBadge = statusCell.querySelector('.status-badge');
        statusBadge.textContent = newStatus;
        
        if (newStatus === 'Approved') {
            statusBadge.classList.add('status-approved');
        } else if (newStatus === 'Rejected') {
            statusBadge.classList.add('status-rejected');
        } else {
            statusBadge.classList.add('status-pending');
        }
        
        // Also update the edit button to reflect the new status
        const editButton = row.querySelector('button[onclick*="openEditRenewalModal(' + scholarId + '"]');
        const currentOnClick = editButton.getAttribute('onclick');
        const updatedOnClick = currentOnClick.replace(
            /openEditRenewalModal\((\d+), '.*?'\)/,
            `openEditRenewalModal($1, '${newStatus}')`
        );
        editButton.setAttribute('onclick', updatedOnClick);
    }
}
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
                <script>
                    document.getElementById("notifBell").addEventListener("click", function() {
                        document.getElementById("notifDropdown").classList.toggle("hidden");
                        localStorage.setItem('notificationsViewed', 'true');
                        let notifCount = document.getElementById("notifCount");
                        if (notifCount) {
                            notifCount.innerText = '0';
                        }
                    });
                </script>
                 <script src="{{ asset('js/logout.js') }}"></script>
                 <script>
function openEmailModal(email, name = '', corStatus = '', gradeStatus = '', indigencyStatus = '') {
    document.getElementById("recipientEmail").value = email;
    document.getElementById("emailTo").value = email;

    document.getElementById("emailSubject").value = "Scholarship Renewal Document Notice";

    let issues = [];

    if (corStatus.toLowerCase() === 'blurred' || corStatus.toLowerCase() === 'missing') {
        issues.push("Certificate of Registration is " + corStatus);
    }
    if (gradeStatus.toLowerCase() === 'blurred' || gradeStatus.toLowerCase() === 'missing') {
        issues.push("Grade Slip is " + gradeStatus);
    }
    if (indigencyStatus.toLowerCase() === 'blurred' || indigencyStatus.toLowerCase() === 'missing') {
        issues.push("Barangay Indigency is " + indigencyStatus);
    }

    let defaultMessage = `Good day ${name},

We have reviewed your renewal documents and found the following issue(s):

- ${issues.length ? issues.join("\n- ") : "No issues detected."}

Kindly resubmit the correct document(s) at the earliest convenience.

Thank you,
Scholarship Office`;

    document.getElementById("emailMessage").value = defaultMessage;

    document.getElementById("emailModal").classList.remove("hidden");
}


</script>


<script>
function closeEmailModal() {
    document.getElementById("emailModal").classList.add("hidden");
}
</script>

<script>
// Client-side filtering for tableView
document.addEventListener('DOMContentLoaded', function() {
    const nameSearch = document.getElementById('nameSearch');
    const barangayFilter = document.getElementById('barangayFilter');

    function filterTable() {
        const searchValue = nameSearch.value.toLowerCase();
        const barangayValue = barangayFilter.value;

        const rows = document.querySelectorAll('#tableView tbody tr');

        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)'); // Name column
            const barangayCell = row.querySelector('td:nth-child(3)'); // Barangay column

            if (nameCell && barangayCell) {
                const nameText = nameCell.textContent.toLowerCase();
                const barangayText = barangayCell.textContent;

                const nameMatch = nameText.includes(searchValue);
                const barangayMatch = barangayValue === '' || barangayText === barangayValue;

                if (nameMatch && barangayMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    nameSearch.addEventListener('input', filterTable);
    barangayFilter.addEventListener('change', filterTable);
});
</script>
</body>
</html>
