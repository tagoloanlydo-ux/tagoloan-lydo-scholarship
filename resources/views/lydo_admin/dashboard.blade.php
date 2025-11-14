<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        </header>
        <!-- Main Content -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            <div class="w-16 md:w-72 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
        <li>
          <a href="/lydo_admin/dashboard" class="w-ful flex items-center p-3 rounded-lg text-white bg-violet-600 hover:bg-violet-700">
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
            <div class="flex-1 overflow-hidden p-2 md:p-5 text-[14px] overflow-y-auto">
                <!-- Updated Cards Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Current Academic Year Applicants -->
                    <div class="bg-white rounded-xl shadow-md p-5 flex flex-col justify-between min-h-[180px]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Current Academic Year Applicants</p>
                                <h3 class="text-4xl font-extrabold text-indigo-600">{{ $totalApplicants }}</h3>
                            </div>
                            <div class="bg-indigo-100 rounded-full p-2 inline-flex items-center justify-center">
                                <i class="fas fa-users text-indigo-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-sm bg-indigo-50 p-2 rounded-lg text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Applicants for {{ $currentAcademicYear }}
                        </div>
                    </div>

                    <!-- Total Scholars -->
                    <div class="bg-white rounded-xl shadow-md p-5 flex flex-col justify-between min-h-[180px]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Scholars</p>
                                <h3 class="text-4xl font-extrabold text-green-600">{{ $totalScholarsWholeYear }}</h3>
                            </div>
                            <div class="bg-green-100 rounded-full p-2 inline-flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-green-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-sm bg-green-50 p-2 rounded-lg text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Active scholars across all years
                        </div>
                    </div>

                    <!-- Inactive Scholars -->
                    <div class="bg-white rounded-xl shadow-md p-5 flex flex-col justify-between min-h-[180px]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Inactive Scholars</p>
                                <h3 class="text-4xl font-extrabold text-red-600">{{ $inactiveScholars }}</h3>
                            </div>
                            <div class="bg-red-100 rounded-full p-2 inline-flex items-center justify-center">
                                <i class="fas fa-user-times text-red-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-sm bg-red-50 p-2 rounded-lg text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Scholars who are currently inactive
                        </div>
                    </div>

                    <!-- ADDED: Graduated Scholars Card -->
                    <div class="bg-white rounded-xl shadow-md p-5 flex flex-col justify-between min-h-[180px]">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Graduated Scholars</p>
                                <h3 class="text-4xl font-extrabold text-amber-600">{{ $graduatedScholars }}</h3>
                            </div>
                            <div class="bg-amber-100 rounded-full p-2 inline-flex items-center justify-center">
                                <i class="fas fa-user-graduate text-amber-600 text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-sm bg-amber-50 p-2 rounded-lg text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Scholars who have successfully graduated
                        </div>
                    </div>
                </div>

                <!-- Distribution Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <!-- Barangay Distribution -->
                    <div class="bg-white rounded-xl shadow-md p-4">
                        <h3 class="text-lg font-semibold text-violet-700 mb-3">Top Barangays</h3>
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @forelse($barangayDistribution as $barangay)
                                <div class="flex justify-between items-center p-2 bg-blue-50 rounded-lg border border-blue-100">
                                    <span class="text-sm font-medium text-gray-700">{{ $barangay->applicant_brgy ?: 'Unknown' }}</span>
                                    <span class="text-sm bg-blue-200 text-blue-800 px-2 py-1 rounded-full font-medium">
                                        {{ $barangay->count }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm text-center py-4">No barangay data available</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- School Distribution -->
                    <div class="bg-white rounded-xl shadow-md p-4">
                        <h3 class="text-lg font-semibold text-violet-700 mb-3">Top Schools</h3>
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @forelse($schoolDistribution as $school)
                                <div class="flex justify-between items-center p-2 bg-green-50 rounded-lg border border-green-100">
                                    <span class="text-sm font-medium text-gray-700">{{ $school->applicant_school_name ?: 'Unknown' }}</span>
                                    <span class="text-sm bg-green-200 text-green-800 px-2 py-1 rounded-full font-medium">
                                        {{ $school->count }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm text-center py-4">No school data available</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- ADDED: Scholar Trends Line Chart Section -->
                <div class="mt-6">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-semibold text-violet-700 mb-4">Scholar Trends Per Academic Year</h3>
                        <div class="h-80"> <!-- Fixed height for the chart -->
                            <canvas id="scholarTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

<script>
    // Scholar Trends Line Chart
    document.addEventListener('DOMContentLoaded', function() {
        const scholarStats = @json($scholarStatsPerYear);
        
        if (scholarStats.length > 0) {
            const academicYears = scholarStats.map(stat => stat.academic_year);
            const activeCounts = scholarStats.map(stat => stat.active);
            const inactiveCounts = scholarStats.map(stat => stat.inactive);
            const graduatedCounts = scholarStats.map(stat => stat.graduated);

            const ctx = document.getElementById('scholarTrendsChart').getContext('2d');
            const scholarTrendsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: academicYears,
                    datasets: [
                        {
                            label: 'Active Scholars',
                            data: activeCounts,
                            borderColor: '#10b981', // green-500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Inactive Scholars',
                            data: inactiveCounts,
                            borderColor: '#ef4444', // red-500
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Graduated Scholars',
                            data: graduatedCounts,
                            borderColor: '#f59e0b', // amber-500
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.4,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Academic Year',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Scholars',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        } else {
            // If no data available, show message
            document.getElementById('scholarTrendsChart').parentElement.innerHTML = 
                '<p class="text-gray-500 text-center py-8">No scholar trend data available</p>';
        }
    });
</script>

<script>
    // Welcome modal using SweetAlert2 with user name and role, auto-dismiss after 4 seconds
    @if(session('show_welcome'))
        console.log('Showing welcome modal after login');
        Swal.fire({
            title: '👋 Welcome back, {{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} (Lydo Admin)!',
            icon: 'success',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: false,
            width: '600px',
            didOpen: (modal) => {
                modal.addEventListener('mouseenter', Swal.stopTimer)
                modal.addEventListener('mouseleave', Swal.resumeTimer)
            },
            position: 'center',
            background: '#f3e8ff',
            color: '#5b21b6'
        });
        // Clear the session flag after showing
        @php
            session()->forget('show_welcome');
        @endphp
    @endif
</script>
<!-- Palitan ito -->
<script src="{{ asset('js/admin_dash_autorefresh.js') }}"></script>

<script>
    // Make chart globally accessible
    let scholarTrendsChart = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Your existing chart initialization code
        const scholarStats = @json($scholarStatsPerYear);
        
        if (scholarStats.length > 0) {
            const academicYears = scholarStats.map(stat => stat.academic_year);
            const activeCounts = scholarStats.map(stat => stat.active);
            const inactiveCounts = scholarStats.map(stat => stat.inactive);
            const graduatedCounts = scholarStats.map(stat => stat.graduated);

            const ctx = document.getElementById('scholarTrendsChart').getContext('2d');
            scholarTrendsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: academicYears,
                    datasets: [
                        {
                            label: 'Active Scholars',
                            data: activeCounts,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Inactive Scholars',
                            data: inactiveCounts,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'Graduated Scholars',
                            data: graduatedCounts,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.4,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
            
            // Make it globally accessible
            window.scholarTrendsChart = scholarTrendsChart;
        }
    });
</script>

<!-- Include the auto-refresh script -->
<script src="{{ asset('js/admin_dash_autorefresh.js') }}"></script>
<script src="{{ asset('js/spinner.js') }}"></script>

</div>
</body>

</html>