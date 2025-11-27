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
   
    </style>
<body class="bg-gray-50">
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
                            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
</div>
    <div class="dashboard-grid">
<header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-white font-semibold">{{ session('scholar')->applicant->applicant_fname }} {{ session('scholar')->applicant->applicant_lname }} | Scholar</span>
                </div>
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden">
            <div class="w-16 md:w-64 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
                        <li>
                            <a href="{{ route('scholar.dashboard') }}" class=" flex items-center p-3 rounded-lg text-white bg-violet-600 hover:bg-violet-700">
                                <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Dashboard</span>
                            </a>
                        </li>
                        @php
                            $settings = \App\Models\Settings::first();
                            $currentDate = now()->toDateString();
                            $renewalDisabled = false;
                            $renewalMessage = '';

                            if ($settings && $settings->renewal_start_date && $settings->renewal_deadline) {
                              $renewalStartDate = $settings->renewal_start_date->toDateString();
                              $renewalDeadline = $settings->renewal_deadline->toDateString();

                              if ($currentDate < $renewalStartDate) {
                                $renewalDisabled = true;
                                $renewalMessage = 'Renewal period starts on ' . $settings->renewal_start_date->format('M d, Y');
                              } elseif ($currentDate > $renewalDeadline) {
                                $renewalDisabled = true;
                                $renewalMessage = 'Renewal period has ended on ' . $settings->renewal_deadline->format('M d, Y');
                              }
                            } elseif ($settings && (!$settings->renewal_start_date || !$settings->renewal_deadline)) {
                              $renewalDisabled = true;
                              $renewalMessage = 'Renewal period not yet set by administrator';
                            }
                        @endphp

                        <li>
                            <a href="{{ route('scholar.renewal_app') }}" class="flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Renewal</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scholar.renewal_history') }}" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bx-history text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Renewal History</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scholar.settings') }}" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
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

            <div class="flex-1 overflow-y-auto p-4 md:p-6">
                <!-- Announcements Section -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Announcements</h2>
                    <div class="relative pl-8 border-l-4 border-violet-300 space-y-8">
                        @forelse($announcements as $index => $announcement)
                            @php
                                $icons = ['fa-bell', 'fa-calendar-day', 'fa-wallet', 'fa-bullhorn', 'fa-info-circle'];
                                $icon = $icons[$index % count($icons)];
                            @endphp
                            <div class="relative">
                                <span class="absolute -left-5 top-1 flex items-center justify-center w-10 h-10 bg-violet-600 text-white rounded-full shadow-md">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </span>
                                <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
                                    <div class="announcement-header cursor-pointer" onclick="toggleContent(this)">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-lg font-semibold text-violet-700">{{ $announcement->announce_title }}</h3>
                                            <i class="fa-solid fa-chevron-down text-violet-600 transition-transform duration-300"></i>
                                        </div>
                                        <p class="text-gray-600 mt-2 text-sm">{{ Str::limit($announcement->announce_content, 100) }}</p>
                                        <span class="text-xs text-gray-400 mt-2 block">📅 {{ $announcement->date_posted->format('M d, Y') }}</span>
                                    </div>
                                    <div class="announcement-content hidden mt-4">
                                        <p class="text-gray-600 whitespace-pre-line">{{ $announcement->announce_content }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white p-6 rounded-2xl shadow">
                                <p class="text-gray-600">No announcements available at the moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleContent(header) {
            const content = header.nextElementSibling;
            const icon = header.querySelector('i.fa-chevron-down');
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>

    <script>
        // Welcome modal using SweetAlert2 with user name and role, auto-dismiss after 4 seconds
        console.log('Welcome script running');
        console.log('sessionStorage welcomeShown:', sessionStorage.getItem('welcomeShown'));
        if (!sessionStorage.getItem('welcomeShown')) {
            console.log('Showing welcome modal');
            Swal.fire({
                title: '👋 Welcome back, {{ session('scholar')->applicant->applicant_fname }} {{ session('scholar')->applicant->applicant_lname }} (Scholar)!',
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
            sessionStorage.setItem('welcomeShown', 'true');
        } else {
            console.log('Welcome modal already shown, skipping');
        }
    </script>
<script src="{{ asset('js/spinner.js') }}"></script>

</body>
</html>
