<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/screening.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">

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
@php
    $badgeCount = ($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0) ? $notifications->where('initial_screening', 'Approved')->count() : 0;
@endphp
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
                        <ul class="max-h-60 overflow-y-auto"> @forelse($notifications as $notif) <li class="px-4 py-2 hover:bg-gray-50 text-sm border-b"> @if($notif->initial_screening == 'Approved') <p class="text-green-600 font-medium"> ✅ {{ $notif->name }} passed initial screening </p> @elseif($notif->status == 'Renewed') <p class="text-blue-600 font-medium"> 🔄 {{ $notif->name }} submitted renewal </p> @endif <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                </p>
                            </li> @empty <li class="px-4 py-3 text-gray-500 text-sm">No new notifications</li> @endforelse </ul>
                    </div>
                </div>
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
                            <a href="/lydo_staff/screening" class="flex items-center justify-between p-3 rounded-lg text-gray-700 bg-violet-600 text-white">
                                <div class="flex items-center">
                                    <i class="bx bxs-file-blank text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Screening</span>
                                </div>
                                @if($pendingScreening > 0) <span id="pendingScreeningBadge" class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                    {{ $pendingScreening }}
                                </span> @endif
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/renewal" class=" flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Renewals</span>
                                </div>
                                @if($pendingRenewals > 0) <span class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
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
                        <h5 class="text-3xl font-bold text-gray-800">Screening Applicants</h5>
                    </div>
                    <!-- 🔎 View Switch -->
                    <div class="flex justify-end items-center mb-6">
                        <!-- Tab Switch -->
            <div class="flex gap-2">
                <div class="tab active" id="tab-screening" onclick="showTable()">Assign Remarks</div>
                <div class="tab" id="tab-review" onclick="showList()">View Remarks</div>
            </div>
                    </div>
                    <!-- ✅ Table View (Applicants without remarks) -->
                    <div id="tableView" class="overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-blue-50 p-3  border border-blue-200">
                               Screening Table: Evaluate and assign remarks to applicants.
                            </h3>
                            <!-- Search & Filter for Table View -->
                            <div class="flex gap-2 mt-4">
                                <input type="text" id="searchInput_table" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                                <select id="barangaySelect_table" class="border rounded px-3 py-2">
                                    <option value="">All Barangays</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <table id="screeningTable" class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
                            <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Remarks</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody> @forelse($tableApplicants as $index => $app) <tr class="hover:bg-gray-50 border-b" data-id="{{ $app->applicant_id }}">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_course }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_school_name }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <form method="POST" action="{{ route('updateRemarks', $app->application_personnel_id) }}"> @csrf @method('PUT') <select name="remarks" onchange="confirmRemarksChange(this)" class="border border-gray-300 rounded-lg px-2 py-1 text-[15px] focus:ring-2 focus:ring-blue-400">
                                                <option value="">Select...</option>
                                                <option value="Poor" {{ $app->remarks == 'Poor' ? 'selected' : '' }}>Poor</option>
                                                <option value="Ultra Poor" {{ $app->remarks == 'Ultra Poor' ? 'selected' : '' }}>Ultra Poor</option>
                                                 <option value="Non Poor" {{ $app->remarks == 'Non Poor' ? 'selected' : '' }}>Non Poor</option>
                                                </select>
                                        </form>
                                    </td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm" data-id="{{ $app->applicant_id }}" data-fname="{{ $app->applicant_fname }}" data-mname="{{ $app->applicant_mname }}" data-lname="{{ $app->applicant_lname }}" data-suffix="{{ $app->applicant_suffix }}" data-gender="{{ $app->applicant_gender }}" data-bdate="{{ $app->applicant_bdate }}" data-civil="{{ $app->applicant_civil_status }}" data-brgy="{{ $app->applicant_brgy }}" data-email="{{ $app->applicant_email }}" data-contact="{{ $app->applicant_contact_number }}" data-school="{{ $app->applicant_school_name }}" data-year="{{ $app->applicant_year_level }}" data-course="{{ $app->applicant_course }}" data-acad="{{ $app->applicant_acad_year }}" onclick="openPersonalEditModal(this)"> Edit Info </button>
                                    </td>
                                </tr> @empty <tr>
                                    <td colspan="7" class="text-center py-4 border border-gray-200 text-gray-500">No applicants found.</td>
                                </tr> @endforelse </tbody>
                        </table>
                    <div class="mt-4">
                        {{ $tableApplicants->links() }}
                    </div>
                    </div>
                    <!-- Full Personal Info Modal -->
                    <div id="personalEditModal" class="fixed inset-0 hidden bg-black bg-opacity-40 flex items-center justify-center z-50">
                        <div class="bg-white w-[50rem] max-h-[90vh] overflow-y-auto p-6 rounded-lg shadow-lg">
                            <h2 class="text-lg font-semibold mb-4 text-gray-700">Edit Personal Information</h2>
                            <form id="personalEditForm" method="POST" action=""> @csrf @method('PUT') <input type="hidden" name="applicant_id" id="personal_edit_id">
                                <!-- Grid Form with Design -->
                                <div class="grid grid-cols-1 gap-6">
                                    <!-- SECTION: Personal Information -->
                                    <div class="bg-white shadow-md rounded-xl p-4 border">
                                        <h3 class="text-lg font-semibold text-blue-600 mb-3 border-b pb-2">Personal Information</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">First Name</label>
                                                <input type="text" name="applicant_fname" id="personal_edit_fname" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_fname')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Middle Name</label>
                                                <input type="text" name="applicant_mname" id="personal_edit_mname" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_mname')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Last Name</label>
                                                <input type="text" name="applicant_lname" id="personal_edit_lname" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_lname')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Suffix</label>
                                                <input type="text" name="applicant_suffix" id="personal_edit_suffix" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_suffix')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Gender</label>
                                                <select name="applicant_gender" id="personal_edit_gender" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                                @error('applicant_gender')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Civil Status</label>
                                                <select name="applicant_civil_status" id="personal_edit_civil_status" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                    <option value="widowed">Widowed</option>
                                                </select>
                                                @error('applicant_civil_status')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Birthdate</label>
                                                <input type="date" name="applicant_bdate" id="personal_edit_bdate" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_bdate')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Barangay</label>
                                                <select name="applicant_brgy" id="personal_edit_brgy" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                    <option value="">-- Select Barangay --</option>
                                                    <option value="Baluarte">Baluarte</option>
                                                    <option value="Casinglot">Casinglot</option>
                                                    <option value="Gracia">Gracia</option>
                                                    <option value="Mohon">Mohon</option>
                                                    <option value="Natumolan">Natumolan</option>
                                                    <option value="Poblacion">Poblacion</option>
                                                    <option value="Rosario">Rosario</option>
                                                    <option value="Santa Ana">Santa Ana</option>
                                                    <option value="Santa Cruz">Santa Cruz</option>
                                                    <option value="Sugbongcogon">Sugbongcogon</option>
                                                </select>
                                                @error('applicant_brgy')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <!-- SECTION: Contact Information -->
                                    <div class="bg-white shadow-md rounded-xl p-4 border">
                                        <h3 class="text-lg font-semibold text-blue-600 mb-3 border-b pb-2">Contact Information</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Email</label>
                                                <input type="email" name="applicant_email" id="personal_edit_email" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_email')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Contact Number</label>
                                                <input type="text" name="applicant_contact_number" id="personal_edit_contact" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_contact_number')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <!-- SECTION: Education -->
                                    <div class="bg-white shadow-md rounded-xl p-4 border">
                                        <h3 class="text-lg font-semibold text-blue-600 mb-3 border-b pb-2">Education</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">School</label>
                                                <input type="text" name="applicant_school_name" id="personal_edit_school" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_school_name')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Year Level</label>
                                                <input type="text" name="applicant_year_level" id="personal_edit_year" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_year_level')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Course</label>
                                                <input type="text" name="applicant_course" id="personal_edit_course" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_course')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-600">Academic Year</label>
                                                <input type="text" name="applicant_acad_year" id="personal_edit_acad" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400">
                                                @error('applicant_acad_year')
                                                <span class="error-message">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Actions -->
                                <div class="mt-6 flex justify-end space-x-2">
                                    <button type="button" onclick="closePersonalEditModal()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
                                    <button type="submit" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ✅ List View (Applicants with remarks) -->
                    <div id="listView" class="hidden overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-green-50 p-3 rounded-lg border border-green-200">
                                Review Table: View applicants with assigned remarks.
                            </h3>
                            <!-- Search & Filter for List View -->
                            <div class="flex gap-2 mt-4">
                                <input type="text" id="searchInput_list" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                                <select id="barangaySelect_list" class="border rounded px-3 py-2">
                                    <option value="">All Barangays</option>
                                    @foreach($barangays as $brgy)
                                        <option value="{{ $brgy }}">{{ $brgy }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Remarks</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody> @forelse($listApplicants as $index => $app)
                            <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_course }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_school_name }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="inline-block px-3 py-1 text-xs font-medium rounded-full
                            @if($app->remarks == 'Poor') bg-red-100 text-red-700 border border-red-300
                            @elseif($app->remarks == 'Non Poor') bg-yellow-100 text-yellow-700 border border-yellow-300
                            @elseif($app->remarks == 'Ultra Poor') bg-purple-100 text-purple-700 border border-purple-300
                            @elseif($app->remarks == 'Non Indigenous') bg-green-100 text-green-700 border border-green-300
                            @else bg-gray-100 text-gray-600 border
                            @endif">
                                            {{ $app->remarks ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                 <button
                                    title="Edit Remarks"
                                    class="px-3 py-1 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow"
                                    data-id="{{ $app->application_personnel_id }}"
                                    data-remarks="{{ $app->remarks }}"
                                    onclick="openEditRemarksModal(this)">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </button>


                                    </td>
                                </tr> @empty <tr>
                                    <td colspan="7" class="text-center py-4 border border-gray-200 text-gray-500">No applicants with remarks.</td>
                                </tr> @endforelse </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $listApplicants->links() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Remarks Modal -->
        <div id="editRemarksModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">
            
            <h2 class="text-xl font-semibold mb-4">Edit Remarks</h2>
            
            <form id="updateRemarksForm" method="POST" action="{{ route('updateApplicantsRemarks') }}">
            @csrf
            <input type="hidden" name="id" id="remarks_id">

            <label for="remarks_input" class="block text-sm font-medium text-gray-700">Remarks</label>
            <select name="remarks" id="remarks_input" class="mt-1 block w-full border rounded-lg p-2">
                <option value="">-- Select Remark --</option>
                <option value="Poor">Poor</option>
                <option value="Non-Poor">Non-Poor</option>
                <option value="Ultra Poor">Ultra Poor</option>
            </select>

            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" onclick="closeEditRemarksModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Update</button>
            </div>
            </form>
        </div>
        </div>
        <script>
            function openPersonalEditModal(button) {
                document.getElementById('personal_edit_id').value = button.dataset.id;
                document.getElementById('personal_edit_fname').value = button.dataset.fname;
                document.getElementById('personal_edit_mname').value = button.dataset.mname;
                document.getElementById('personal_edit_lname').value = button.dataset.lname;
                document.getElementById('personal_edit_suffix').value = button.dataset.suffix;
                // Handle gender selection case-insensitively
                let genderSelect = document.getElementById('personal_edit_gender');
                genderSelect.value = button.dataset.gender;
                if (genderSelect.selectedIndex === -1) {
                    for (let option of genderSelect.options) {
                        if (option.value.toLowerCase() === button.dataset.gender.toLowerCase()) {
                            option.selected = true;
                            break;
                        }
                    }
                }
                document.getElementById('personal_edit_bdate').value = button.dataset.bdate;
                // Handle civil status selection case-insensitively
                let civilSelect = document.getElementById('personal_edit_civil_status');
                civilSelect.value = button.dataset.civil;
                if (civilSelect.selectedIndex === -1) {
                    for (let option of civilSelect.options) {
                        if (option.value.toLowerCase() === button.dataset.civil.toLowerCase()) {
                            option.selected = true;
                            break;
                        }
                    }
                }
                document.getElementById('personal_edit_brgy').value = button.dataset.brgy;
                document.getElementById('personal_edit_email').value = button.dataset.email;
                document.getElementById('personal_edit_contact').value = button.dataset.contact;
                document.getElementById('personal_edit_school').value = button.dataset.school;
                document.getElementById('personal_edit_year').value = button.dataset.year;
                document.getElementById('personal_edit_course').value = button.dataset.course;
                document.getElementById('personal_edit_acad').value = button.dataset.acad;
                let form = document.getElementById('personalEditForm');
                form.action = "/lydo_staff/update-applicant/" + button.dataset.id;
                document.getElementById('personalEditModal').classList.remove('hidden');
            }

            function closePersonalEditModal() {
                document.getElementById('personalEditModal').classList.add('hidden');
            }
        </script>
        <script>
            function showTable() {
                document.getElementById("tableView").classList.remove("hidden");
                document.getElementById("listView").classList.add("hidden");

                // Update tab active states
                document.getElementById("tab-screening").classList.add("active");
                document.getElementById("tab-review").classList.remove("active");

                localStorage.setItem("viewMode", "table"); // save preference

                // Restore filter values from localStorage for table view
                const savedSearch = localStorage.getItem("searchValue_table") || "";
                const savedBarangay = localStorage.getItem("barangayValue_table") || "";
                document.getElementById("searchInput_table").value = savedSearch;
                document.getElementById("barangaySelect_table").value = savedBarangay;

                // Reapply filter to the new visible table
                filterTable();
            }

            function showList() {
                document.getElementById("listView").classList.remove("hidden");
                document.getElementById("tableView").classList.add("hidden");

                // Update tab active states
                document.getElementById("tab-review").classList.add("active");
                document.getElementById("tab-screening").classList.remove("active");

                localStorage.setItem("viewMode", "list"); // save preference

                // Restore filter values from localStorage for list view
                const savedSearch = localStorage.getItem("searchValue_list") || "";
                const savedBarangay = localStorage.getItem("barangayValue_list") || "";
                document.getElementById("searchInput_list").value = savedSearch;
                document.getElementById("barangaySelect_list").value = savedBarangay;

                // Reapply filter to the new visible table
                filterTable();
            }
            // ✅ Kapag nag-load ang page, i-apply yung last view
            document.addEventListener("DOMContentLoaded", function() {
                let viewMode = localStorage.getItem("viewMode") || "table"; // default table
                if (viewMode === "list") {
                    showList();
                } else {
                    showTable();
                }

                // Add event listeners for real-time filtering
                document.getElementById('searchInput_table').addEventListener('input', filterTable);
                document.getElementById('barangaySelect_table').addEventListener('change', filterTable);
                document.getElementById('searchInput_list').addEventListener('input', filterTable);
                document.getElementById('barangaySelect_list').addEventListener('change', filterTable);
            });
        </script>

        <script>
            function confirmRemarksChange(selectElement) {
                const selectedValue = selectElement.value;
                if (!selectedValue) return; // If no value selected, do nothing

                Swal.fire({
                    title: 'Confirm Remarks Change',
                    text: `Are you sure you want to set remarks to "${selectedValue}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        selectElement.form.submit();
                    } else {
                        // Reset the select to previous value if canceled
                        selectElement.value = selectElement.getAttribute('data-previous') || '';
                    }
                });
            }

            // Store previous value on focus
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('select[name="remarks"]').forEach(select => {
                    select.addEventListener('focus', function() {
                        this.setAttribute('data-previous', this.value);
                    });
                });

                // Add confirmation for modal form submit
                const modalForm = document.getElementById('updateRemarksForm');
                if (modalForm) {
                    modalForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const selectedRemarks = document.getElementById('remarks_input').value;
                        if (!selectedRemarks) {
                            Swal.fire('Error', 'Please select a remark before updating.', 'error');
                            return;
                        }

                        Swal.fire({
                            title: 'Confirm Remarks Update',
                            text: `Are you sure you want to update remarks to "${selectedRemarks}"?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, update it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                modalForm.submit();
                            }
                        });
                    });
                }

                // Add confirmation for personal edit form submit
                const personalEditForm = document.getElementById('personalEditForm');
                if (personalEditForm) {
                    personalEditForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        Swal.fire({
                            title: 'Confirm Update',
                            text: 'Are you sure you want to update the personal information?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, update it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                personalEditForm.submit();
                            }
                        });
                    });
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
 <script src="{{ asset('js/logout.js') }}"></script>
 <script>
function openEditRemarksModal(button) {
    let id = button.getAttribute("data-id");
    let remarks = button.getAttribute("data-remarks");

    document.getElementById("remarks_id").value = id;
    document.getElementById("remarks_input").value = remarks;

    document.getElementById("editRemarksModal").classList.remove("hidden");
}

function closeEditRemarksModal() {
    document.getElementById("editRemarksModal").classList.add("hidden");
}
</script>

<script>
// Real-time updates for new applicants
let lastUpdate = new Date().toISOString();

function pollForNewApplicants() {
    fetch(`/lydo_staff/latest-applicants?last_update=${encodeURIComponent(lastUpdate)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Update lastUpdate to the latest created_at
                const latest = data.reduce((max, app) => app.created_at > max ? app.created_at : max, lastUpdate);
                lastUpdate = latest;

                // Append new rows to tableView
                const tableBody = document.querySelector('#tableView tbody');
                if (tableBody) {
                    data.forEach(app => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50 border-b';
                        row.setAttribute('data-id', app.applicant_id);
                        row.innerHTML = `
                            <td class="px-4 border border-gray-200 py-2 text-center">${tableBody.rows.length + 1}</td>
                            <td class="px-4 border border-gray-200 py-2 text-center">${app.applicant_fname} ${app.applicant_lname}</td>
                            <td class="px-4 border border-gray-200 py-2 text-center">${app.applicant_brgy}</td>
                            <td class="px-4 border border-gray-200 py-2 text-center">${app.applicant_course}</td>
                            <td class="px-4 border border-gray-200 py-2 text-center">${app.applicant_school_name}</td>
                            <td class="px-4 border border-gray-200 py-2 text-center">
                                <form method="POST" action="/lydo_staff/update-remarks/${app.application_personnel_id}"> @csrf @method('PUT') <select name="remarks" onchange="confirmRemarksChange(this)" class="border border-gray-300 rounded-lg px-2 py-1 text-[15px] focus:ring-2 focus:ring-blue-400">
                                        <option value="">Select...</option>
                                        <option value="Poor">Poor</option>
                                        <option value="Ultra Poor">Ultra Poor</option>
                                        <option value="Non Poor">Non Poor</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm" data-id="${app.applicant_id}" data-fname="${app.applicant_fname}" data-mname="${app.applicant_mname}" data-lname="${app.applicant_lname}" data-suffix="${app.applicant_suffix}" data-gender="${app.applicant_gender}" data-bdate="${app.applicant_bdate}" data-civil="${app.applicant_civil_status}" data-brgy="${app.applicant_brgy}" data-email="${app.applicant_email}" data-contact="${app.applicant_contact_number}" data-school="${app.applicant_school_name}" data-year="${app.applicant_year_level}" data-course="${app.applicant_course}" data-acad="${app.applicant_acad_year}" onclick="openPersonalEditModal(this)"> Edit Info </button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                }
            }
        })
        .catch(err => console.error('Polling new applicants error:', err));
}

// Poll every 10 seconds
setInterval(pollForNewApplicants, 10000);

function filterTable() {
    // Determine which table is visible
    const isTableView = !document.getElementById('tableView').classList.contains('hidden');
    const visibleTable = isTableView ? 'tableView' : 'listView';
    const searchInput = document.getElementById(isTableView ? 'searchInput_table' : 'searchInput_list');
    const barangaySelect = document.getElementById(isTableView ? 'barangaySelect_table' : 'barangaySelect_list');

    const searchValue = searchInput.value.toLowerCase();
    const barangayValue = barangaySelect.value.toLowerCase();

    // Save filter values to localStorage with view-specific keys
    const viewSuffix = isTableView ? '_table' : '_list';
    localStorage.setItem("searchValue" + viewSuffix, searchInput.value);
    localStorage.setItem("barangayValue" + viewSuffix, barangaySelect.value);

    const tableBody = document.querySelector(`#${visibleTable} tbody`);
    const rows = tableBody.querySelectorAll('tr');

    rows.forEach(row => {
        const nameCell = row.cells[1]; // Name column
        const barangayCell = row.cells[2]; // Barangay column

        if (nameCell && barangayCell) {
            const nameText = nameCell.textContent.toLowerCase();
            const barangayText = barangayCell.textContent.toLowerCase();

            const matchesSearch = nameText.includes(searchValue);
            const matchesBarangay = barangayValue === '' || barangayText === barangayValue;

            if (matchesSearch && matchesBarangay) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}


</body>

</html>
