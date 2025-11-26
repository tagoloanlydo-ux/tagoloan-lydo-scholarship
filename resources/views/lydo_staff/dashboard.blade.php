<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <audio id="notificationSound" src="{{ asset('notification/blade.wav') }}" preload="auto"></audio>
    <script src="{{ asset('js/dashrefresh.js') }}"></script>
    @vite('resources/js/app.js')
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
  <style>
  .animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.scale-0 {
    transform: scale(0);
    transition: transform 0.3s ease;
}

#notifCount {
    transition: all 0.3s ease;
}

#notifDropdown {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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
    justify-content: center;
    align-items: center;
    z-index: 9999;
    display: none;
    transition: opacity 0.3s ease;
    animation: fadeIn 1s ease forwards;
}
/* Card update animations */
.card-updating {
    position: relative;
    overflow: hidden;
}

.card-updating::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

.card-updated {
    animation: pulseUpdate 1s ease-in-out;
}

@keyframes pulseUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Ensure smooth transitions for card numbers */
#applicantsCount, #pendingInitialCount, #approvedRenewalsCount, #pendingRenewalsCount {
    transition: all 0.3s ease;
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
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
                            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
</div>
@php
    $badgeCount = ($notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0) ? $notifications->where('initial_screening', 'Approved')->count() : 0;
@endphp

<body class="bg-gray-50">
    <div class="dashboard-grid">
          <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                
                <!-- Notification Bell with Improved System -->
                <div class="relative">
                    <button id="notifBell" class="relative focus:outline-none">
                        <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                        @if($badgeCount > 0)
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center transition-all duration-300">
                                {{ $badgeCount }}
                            </span>
                        @else
                            <span id="notifCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center hidden transition-all duration-300"></span>
                        @endif
                    </button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b font-semibold text-gray-700 flex justify-between items-center">
                            <span>Notifications</span>
                            <button id="markAllRead" class="text-xs text-blue-600 hover:text-blue-800 transition-colors">Mark all as read</button>
                        </div>
                        <ul id="notifList" class="max-h-60 overflow-y-auto">
                            @forelse($notifications as $notif)
                            <li class="px-4 py-2 hover:bg-gray-50 text-sm border-b transition-colors">
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
            <div class="w-20 md:w-80 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
                        <li>
                            <a href="/lydo_staff/dashboard" class=" flex items-center p-3 rounded-lg text-white bg-violet-600 hover:bg-violet-700">
                                <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/screening" class="flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bxs-file-blank text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Applicant Interview</span>
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
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm"> 
                        @csrf 
                        <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                            <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                            <span class="hidden md:block text-red-600">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
     
            <div class="flex-1 overflow-y-auto p-4 md:p-6">                    
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-6">

                <!-- Applicants -->
                <div class="bg-white rounded-xl shadow-md p-5 flex flex-col justify-between min-h-[180px]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Applicants ({{ $currentAcadYear }})</p>
                            <h3 id="applicantsCount" class="text-4xl font-extrabold text-indigo-600">{{ $applicantsCurrentYear }}</h3>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-2 inline-flex items-center justify-center">
                            <i class="fas {{ $percentage >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-indigo-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-sm bg-indigo-50 p-2 rounded-lg text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ number_format($percentage, 1) }}% vs last year ({{ $lastAcadYear }})
                    </div>
                </div>

                <!-- Pending Initial Screening -->
                <div class="bg-white rounded-xl shadow-md p-5 flex flex-col justify-between min-h-[180px]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Initial Screening</p>
                            <h3 id="pendingInitialCount" class="text-4xl font-extrabold text-yellow-600">{{ $pendingInitial }}</h3>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-2 inline-flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-sm bg-yellow-50 p-2 rounded-lg text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ number_format($pendingInitialPercentage, 1) }}% of applicants still pending
                    </div>
                </div>

                <!-- Approved Renewal -->
                <div class="bg-white rounded-xl shadow-md p-5 flex flex-col justify-between min-h-[180px] order-2 md:order-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Approved Renewal</p>
                            <h3 id="approvedRenewalsCount" class="text-4xl font-extrabold text-green-600">{{ $approvedRenewals }}</h3>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 inline-flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-sm bg-green-50 p-2 rounded-lg text-gray-600">
                        <i class="fas fa-check mr-1"></i>
                        {{ number_format($completionRate, 1) }}% of renewals approved
                    </div>
                </div>

                <!-- Pending Renewal -->
                <div class="bg-white rounded-xl shadow-md p-5 flex flex-col justify-between min-h-[180px] order-1 md:order-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Pending Renewal</p>
                            <h3 id="pendingRenewalsCount" class="text-4xl font-extrabold text-yellow-600">{{ $pendingRenewals }}</h3>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-2 inline-flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-sm bg-yellow-50 p-2 rounded-lg text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ number_format($pendingRenewalPercentage, 1) }}% of renewals pending
                    </div>
                </div>
            </div>

    <div class="p-6 text-lg">
                        <!-- Filter Buttons -->
    <div class="flex flex-wrap gap-3 mb-6 items-center">

        <a href="{{ route('LydoStaff.dashboard', ['filter' => 'all']) }}" class="px-5 py-2.5 text-sm font-semibold rounded-full border shadow-sm transition-all hover:shadow-md
           {{ $filter == 'all'
                ? 'bg-blue-600 text-white border-blue-600'
                : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-blue-100 hover:text-blue-600' }}">
                                <i class="fas fa-list mr-1"></i> All </a>
        <a href="{{ route('LydoStaff.dashboard', ['filter' => 'poor']) }}" class="px-5 py-2.5 text-sm font-semibold rounded-full border shadow-sm transition-all hover:shadow-md
           {{ $filter == 'poor'
                ? 'bg-red-600 text-white border-red-600'
                : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-red-100 hover:text-red-600' }}">
                                <i class="fas fa-exclamation-circle mr-1"></i> Poor </a>
        <a href="{{ route('LydoStaff.dashboard', ['filter' => 'non_poor']) }}" class="px-5 py-2.5 text-sm font-semibold rounded-full border shadow-sm transition-all hover:shadow-md
           {{ $filter == 'non_poor'
                ? 'bg-yellow-500 text-white border-yellow-500'
                : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-yellow-100 hover:text-yellow-600' }}">
                                <i class="fas fa-user-check mr-1"></i> Non Poor </a>
         <a href="{{ route('LydoStaff.dashboard', ['filter' => 'ultra_poor']) }}" class="px-5 py-2.5 text-sm font-semibold rounded-full border shadow-sm transition-all hover:shadow-md
           {{ $filter == 'ultra_poor'
                ? 'bg-purple-600 text-white border-purple-600'
                : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-purple-100 hover:text-purple-600' }}">
                                <i class="fas fa-star mr-1"></i> Ultra Poor </a>
                            <!-- Search Box -->
                            <div class="relative flex-1 max-w-xs ml-auto">
                                <input id="searchInput" type="text" placeholder="Search applicants..." value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" autocomplete="off">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                                <input type="hidden" id="currentFilter" value="{{ $filter }}">
                            </div>
                        </div>
                        
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <!-- Header -->
                    <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                        <h3 class="font-semibold text-lg flex items-center text-gray-700">
                            <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                            Applicants ({{ ucfirst(str_replace('_', ' ', $filter)) }})
                        </h3>
                        <div id="showingCount" class="text-gray-500 text-sm">
                            Showing {{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }}
                        </div>
                    </div>

                     <!-- Applicants List -->
                    <div id="applicantsList" class="overflow-y-auto divide-y divide-gray-200" style="max-height: 350px;">
                        @forelse($applications as $applicant)
                            <div class="p-4 hover:bg-gray-50 transition text-sm" data-id="{{ $applicant->applicant_id }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-base text-gray-800">{{ $applicant->name }}</h4>
                                        <div class="text-gray-600 mt-1 text-sm">
                                            <span>{{ $applicant->course }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $applicant->school }}</span>
                                        </div>
                                    </div>

                                    @php
                                        $remarkKey = strtolower(str_replace(' ', '_', $applicant->remarks));
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-medium rounded-full border
                                        {{ $remarkKey == 'poor' ? 'bg-red-50 text-red-700 border-red-300' : '' }}
                                        {{ $remarkKey == 'non_poor' ? 'bg-yellow-50 text-yellow-700 border-yellow-300' : '' }}
                                        {{ $remarkKey == 'ultra_poor' ? 'bg-purple-50 text-purple-700 border-purple-300' : '' }}">
                                        {{ $applicant->remarks ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-gray-500 text-center">0 applicants found.</div>
                        @endforelse
                    </div>

                    <!-- Footer -->
                    <div class="p-4 border-t text-sm bg-gray-50">
                        {{ $applications->links() }}
                    </div>
                </div>

                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const searchInput = document.getElementById('searchInput');
                        const currentFilter = document.getElementById('currentFilter').value;
                        const applicantsList = document.getElementById('applicantsList');
                        const headerCount = document.getElementById('showingCount');
                        const paginationFooter = document.querySelector('.p-4.border-t');

                        let debounceTimer;

                        searchInput.addEventListener('input', function () {
                            clearTimeout(debounceTimer);
                            debounceTimer = setTimeout(() => {
                                const query = this.value.trim();

                                fetch(`{{ route('LydoStaff.dashboard') }}?filter=${currentFilter}&search=${encodeURIComponent(query)}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    // Update header count
                                    headerCount.textContent = `Showing 1-${data.length} of ${data.length}`;

                                    // Update table body
                                    if (data.length === 0) {
                                        applicantsList.innerHTML = '<div class="p-4 text-gray-500 text-center">0 applicants found.</div>';
                                    } else {
                                        applicantsList.innerHTML = data.map(applicant => {
                                            const remarkKey = applicant.remarks ? applicant.remarks.toLowerCase().replace(' ', '_') : '';
                                            let badgeClass = '';
                                            if (remarkKey === 'poor') badgeClass = 'bg-red-50 text-red-700 border-red-300';
                                            else if (remarkKey === 'non_poor') badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-300';
                                            else if (remarkKey === 'ultra_poor') badgeClass = 'bg-purple-50 text-purple-700 border-purple-300';

                                            return `
                                                <div class="p-4 hover:bg-gray-50 transition text-sm">
                                                    <div class="flex items-start justify-between">
                                                        <div>
                                                            <h4 class="font-semibold text-base text-gray-800">${applicant.name}</h4>
                                                            <div class="text-gray-600 mt-1 text-sm">
                                                                <span>${applicant.course}</span>
                                                                <span class="mx-2">•</span>
                                                                <span>${applicant.school}</span>
                                                            </div>
                                                        </div>
                                                        <span class="px-3 py-1 text-xs font-medium rounded-full border ${badgeClass}">
                                                            ${applicant.remarks || 'N/A'}
                                                        </span>
                                                    </div>
                                                </div>
                                            `;
                                        }).join('');
                                    }

                                    // Hide pagination
                                    if (paginationFooter) paginationFooter.style.display = 'none';
                                })
                                .catch(() => {
                                    applicantsList.innerHTML = '<div class="p-4 text-red-500 text-center">Error fetching results.</div>';
                                });
                            }, 300); // Debounce 300ms
                        });
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
                <script src="{{ asset('js/logout.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifBell = document.getElementById('notifBell');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifCount = document.getElementById('notifCount');
    const markAllRead = document.getElementById('markAllRead');
    const notificationSound = document.getElementById('notificationSound');
    
    let notificationCheckInterval;
    let lastBadgeCount = {{ $badgeCount }};
    let isDropdownOpen = false;

    // Function to check for new notifications
    function checkNewNotifications() {
        fetch('{{ route("lydo_staff.notification_counts") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.badge_count > lastBadgeCount) {
                    // New notification arrived
                    playNotificationSound();
                    updateBadgeCount(data.badge_count);
                    lastBadgeCount = data.badge_count;
                    
                    // Show visual pulse effect
                    notifBell.classList.add('animate-pulse');
                    setTimeout(() => {
                        notifBell.classList.remove('animate-pulse');
                    }, 2000);
                } else if (data.badge_count !== lastBadgeCount) {
                    // Count changed (decreased)
                    updateBadgeCount(data.badge_count);
                    lastBadgeCount = data.badge_count;
                }
            })
            .catch(error => console.error('Error checking notifications:', error));
    }

    // Function to play notification sound
    function playNotificationSound() {
        if (notificationSound) {
            notificationSound.currentTime = 0;
            notificationSound.play().catch(e => console.log('Audio play failed:', e));
        }
    }

    // Function to update badge count
    function updateBadgeCount(count) {
        if (notifCount) {
            if (count > 0) {
                notifCount.textContent = count;
                notifCount.classList.remove('hidden');
                notifCount.classList.add('flex');
            } else {
                notifCount.classList.add('hidden');
                notifCount.classList.remove('flex');
            }
        }
    }

    // Toggle notification dropdown
    notifBell.addEventListener('click', function(e) {
        e.stopPropagation();
        isDropdownOpen = !notifDropdown.classList.contains('hidden');
        
        if (!isDropdownOpen) {
            // Opening dropdown - mark as read
            notifDropdown.classList.remove('hidden');
            markNotificationsAsRead();
            isDropdownOpen = true;
        } else {
            // Closing dropdown
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Mark all as read button
    if (markAllRead) {
        markAllRead.addEventListener('click', function(e) {
            e.stopPropagation();
            markNotificationsAsRead();
        });
    }

    // Function to mark notifications as read
    function markNotificationsAsRead() {
        fetch('{{ route("lydo_staff.mark_notifications_read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                updateBadgeCount(0);
                lastBadgeCount = 0;
                
                // Add visual feedback
                notifCount.classList.add('scale-0');
                setTimeout(() => {
                    notifCount.classList.remove('scale-0');
                }, 300);
            }
        })
        .catch(error => console.error('Error marking notifications as read:', error));
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!notifBell.contains(event.target) && !notifDropdown.contains(event.target)) {
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Close dropdown on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && isDropdownOpen) {
            notifDropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Start checking for new notifications every 5 seconds
    notificationCheckInterval = setInterval(checkNewNotifications, 2000);

    // Initial check
    checkNewNotifications();

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(notificationCheckInterval);
    });

    // Handle page visibility changes (stop checking when tab is not active)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(notificationCheckInterval);
        } else {
            notificationCheckInterval = setInterval(checkNewNotifications, 5000);
            // Check immediately when returning to tab
            checkNewNotifications();
        }
    });
});

// Real-time updates using Laravel Echo and Pusher (if you have it set up)
document.addEventListener('DOMContentLoaded', function() {
    // If you have Laravel Echo configured for real-time updates
    @if(config('broadcasting.default') !== 'log')
    window.Echo.channel('lydo-staff-updates')
        .listen('.applicant.updated', (e) => {
            if (e.type === 'pending_initial') {
                // Trigger immediate notification check
                checkNewNotifications();
            }
        })
        .listen('.renewal.updated', (e) => {
            if (e.type === 'pending_renewals') {
                // Trigger immediate notification check
                checkNewNotifications();
            }
        });
    @endif
});
</script>
<script src="{{ asset('js/spinner.js') }}"></script>
</body>
</html>
