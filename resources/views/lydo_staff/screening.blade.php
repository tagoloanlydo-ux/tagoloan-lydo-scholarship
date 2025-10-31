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
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">

    <style>
        /* Enhanced Professional Color Palette */
        :root {
            --primary-color: #1e40af;
            --primary-dark: #1e3a8a;
            --primary-light: #3b82f6;
            --primary-lighter: #dbeafe;
            --secondary-color: #f8fafc;
            --accent-color: #059669;
            --accent-light: #d1fae5;
            --danger-color: #dc2626;
            --danger-light: #fee2e2;
            --warning-color: #d97706;
            --warning-light: #fef3c7;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --background-light: #f8fafc;
            --background-white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --radius-sm: 0.375rem;
            --radius: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
        }

        /* Enhanced Tab Styling */
        .tab {
            cursor: pointer;
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: white;
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .tab.active {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-color: var(--primary-color);
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .tab.tab-green.active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-color: #10b981;
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .tab:hover:not(.active) {
            background-color: var(--secondary-color);
            border-color: var(--primary-light);
            color: var(--primary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        .tab.tab-green:hover:not(.active) {
            background-color: #d1fae5;
            border-color: #10b981;
            color: #065f46;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        /* Enhanced Table Styling */
        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        #tableView table,
        #listView table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        #tableView table th,
        #tableView table td,
        #listView table th,
        #listView table td {
            border-bottom: 1px solid var(--border-color);
            padding: 16px 20px;
            text-align: center;
            transition: background-color 0.2s ease;
        }

        #tableView table th {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        #listView table th {
            background: #08A045;
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        #tableView table tbody tr:last-child td,
        #listView table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Enhanced Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(124, 58, 237, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }

        .btn-secondary {
            background: white;
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--secondary-color);
            border-color: var(--primary-light);
            color: var(--primary-color);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--accent-color), #059669);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* Professional view mode styling */
        .view-mode input[readonly],
        .view-mode select[disabled],
        .view-mode textarea[readonly] {
            background-color: transparent !important;
            border: none !important;
            color: var(--text-primary) !important;
            font-weight: 500 !important;
            padding: 0 !important;
            cursor: default !important;
        }

        .view-mode input[readonly]:focus,
        .view-mode select[disabled]:focus,
        .view-mode textarea[readonly]:focus {
            outline: none !important;
        }

        /* Enhanced Intake Sheet Layout Styling */
        .intake-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 1.5rem;
            position: relative;
        }

        .intake-header::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .intake-section {
            margin-bottom: 2rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            background: white;
            box-shadow: var(--shadow);
            transition: box-shadow 0.3s ease;
        }

        .intake-section:hover {
            box-shadow: var(--shadow-lg);
        }

        .intake-section-title {
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.75rem;
            color: var(--text-primary);
            font-size: 1.1rem;
        }

        .intake-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .intake-table th,
        .intake-table td {
            border-bottom: 1px solid var(--border-color);
            padding: 12px 16px;
            text-align: left;
        }

        .intake-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .intake-table tbody tr:hover {
            background-color: var(--background-light);
        }

        .intake-signature-area {
            border-top: 2px solid var(--primary-color);
            margin-top: 3rem;
            padding-top: 2rem;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 8px;
            padding: 2rem;
        }

        .intake-signature-line {
            border-top: 2px solid var(--text-primary);
            width: 250px;
            margin-top: 3rem;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }

        /* Enhanced Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            overflow-y: auto;
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }


        /* Enhanced Search and Filter Section */
        .search-filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .search-filter-section .flex {
            gap: 1rem;
            align-items: center;
        }

        .search-filter-section input,
        .search-filter-section select {
            padding: 10px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-filter-section input:focus,
        .search-filter-section select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        /* Enhanced Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .status-approved {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border: 1px solid #10b981;
        }

        .status-rejected {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        /* Clean printable box style */
        .print-box {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: white;
            box-shadow: var(--shadow);
            transition: box-shadow 0.3s ease;
        }

        .print-box:hover {
            box-shadow: var(--shadow-lg);
        }

        .thin-border {
            border: 1px solid var(--border-color);
        }

        /* Layout for review (full screen) */
        .review-columns {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        /* Enhanced Notification Styles */
        .notification-item {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .notification-item:hover {
            transform: translateX(4px);
            box-shadow: var(--shadow);
        }

        .notification-approval {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-left-color: var(--accent-color);
        }

        .notification-renewal {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-left-color: #3b82f6;
        }

        /* Print rules */
        @page {
            size: landscape;
            margin: 4mm;
        }

        @media print {
            body {
                background: white !important;
                color: #000;
                font-size: 10px;
            }
            .no-print {
                display: none !important;
            }
            .max-w-6xl {
                max-width: 100% !important;
                width: 100% !important;
            }
            #reviewArea {
                page-break-inside: avoid;
                padding: 0.125rem !important;
            }
            .review-columns {
                font-size: 9px;
                gap: 4px;
            }
            .thin-border {
                margin-bottom: 0.125rem;
                padding: 0.125rem;
            }
            table {
                font-size: 8px;
            }
            .text-sm {
                font-size: 8px !important;
            }
            .text-xs {
                font-size: 7px !important;
            }
            h2 {
                font-size: 12px !important;
            }
            h4 {
                font-size: 10px !important;
            }
        }

        /* Enhanced Responsive Design */
        @media (max-width: 768px) {
            .review-columns {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .modal-content {
                width: 98%;
                margin: 1% auto;
            }

            .modal-body {
                padding: 1rem;
            }

            .search-filter-section .flex {
                flex-direction: column;
                align-items: stretch;
            }

            .tab {
                padding: 10px 16px;
                font-size: 13px;
            }

            #tableView table th,
            #tableView table td,
            #listView table th,
            #listView table td {
                padding: 12px 8px;
                font-size: 12px;
            }
        }

        /* Fix for modal display */
        .modal-open {
            overflow: hidden;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Enhanced Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--secondary-color);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Enhanced Table Input Styling */
        .table-input-styling input,
        .table-input-styling select {
            border: 1px solid #e2e8f0;
            background: white;
            width: 100%;
            padding: 6px 8px;
            font-size: 14px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .table-input-styling input:focus,
        .table-input-styling select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
            background: #f8fafc;
        }

        .table-input-styling input[readonly] {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="dashboard-grid">
        <header class="bg-blue-600 shadow-sm p-4 flex justify-between items-center font-sans">
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
                         <button type="button" onclick="confirmLogout()" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
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
                    <!-- ✅ Applicants -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex gap-2">
                            <div onclick="showTable()" class="tab active" id="tab-screening">
                                <i class="fas fa-table mr-1"></i> Pending Remarks
                            </div>
                            <div onclick="showList()" class="tab tab-green" id="tab-reviewed">
                                <i class="fas fa-list mr-1"></i> Reviewed Applicants
                            </div>
                        </div>
                    </div>

                    <!-- Pending Remarks Tab -->
                    <div id="tableView" class="overflow-x-auto">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-violet-50 p-3 rounded-lg border border-violet-200">
                            📋 Pending Remarks: View applicants awaiting remarks assignment.
                            </h3>
                        </div>
                        <div class="flex gap-2 mb-4">
                            <input type="text" id="nameSearch" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                            <select id="barangayFilter" class="border rounded px-3 py-2">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-green-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Intake Sheet</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($tableApplicants as $index => $app)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_course }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_school_name }}</td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <button
                                            title="Assign Remarks"
                                            class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow"
                                            data-id="{{ $app->application_personnel_id }}"
                                            data-remarks=""
                                            data-name="{{ $app->applicant_fname }} {{ $app->applicant_lname }}"
                                            data-fname="{{ $app->applicant_fname }}"
                                            data-mname="{{ $app->applicant_mname }}"
                                            data-lname="{{ $app->applicant_lname }}"
                                            data-suffix="{{ $app->applicant_suffix }}"
                                            data-bdate="{{ $app->applicant_bdate }}"
                                            data-brgy="{{ $app->applicant_brgy }}"
                                            data-gender="{{ $app->applicant_gender }}"
                                            data-pob="{{ $app->applicant_pob }}"
                                            onclick="openEditRemarksModal(this)">
                                            <i class="fas fa-plus mr-1"></i> Intake Sheet
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 border border-gray-200 text-gray-500">No applicants pending remarks.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $tableApplicants->appends(request()->query())->links() }}
                        </div>
                    </div>

                    <!-- Reviewed Applicants Tab -->
                    <div id="listView" class="overflow-x-auto hidden">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 bg-white p-3 rounded-lg border border-gray-200">
                            ✅ Reviewed Applicants: View applicants with assigned remarks (Poor, Non Poor, Ultra Poor).
                            </h3>
                        </div>
                        <div class="flex gap-2 mb-4">
                            <input type="text" id="listNameSearch" placeholder="Search name..." class="border rounded px-3 py-2 w-64">
                            <select id="listBarangayFilter" class="border rounded px-3 py-2">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $brgy)
                                    <option value="{{ $brgy }}">{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-green-800 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-4 py-3 border border-gray-200 text-center">#</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Name</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Barangay</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Course</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">School</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Remarks</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Status</th>
                                    <th class="px-4 py-3 border border-gray-200 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listApplicants as $index => $app)
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_fname }} {{ $app->applicant_lname }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_brgy }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_course }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">{{ $app->applicant_school_name }}</td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->remarks == 'Poor') bg-red-100 text-red-800
                                            @elseif($app->remarks == 'Non Poor') bg-yellow-100 text-yellow-800
                                            @elseif($app->remarks == 'Ultra Poor') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->remarks }}
                                        </span>
                                    </td>
                                    <td class="px-4 border border-gray-200 py-2 text-center">
                                        <span class="px-2 py-1 text-sm rounded-lg
                                            @if($app->initial_screening == 'Reviewed') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $app->initial_screening }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border border-gray-200 text-center">
                                        <button
                                            title="View Intake Sheet"
                                            class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow"
                                            data-id="{{ $app->application_personnel_id }}"
                                            onclick="openReviewModal(this)">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 border border-gray-200 text-gray-500">No reviewed applicants.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $listApplicants->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Intake Sheet Modal -->
<div id="editRemarksModal" class="fixed inset-0 hidden bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto py-8">
    <div class="bg-white w-full max-w-7xl rounded-2xl shadow-2xl p-8 max-h-[90vh] overflow-y-auto relative">
        <!-- Close button -->
        <button type="button" onclick="closeEditRemarksModal()" class="absolute top-6 right-6 text-gray-500 hover:text-gray-700 z-10 transition-colors duration-200 p-1 rounded-full hover:bg-gray-100">
            <i class="fas fa-times text-2xl"></i>
        </button>

        <!-- Header with logo -->
        <div class="flex items-center text-2xl font-bold mb-6 text-gray-800">
            <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Logo" class="h-10 w-auto mr-3">
            Family Intake Sheet
        </div>

        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200 mb-8 overflow-x-auto">
            <button type="button" id="tab-family" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-semibold text-violet-700 border-b-2 border-violet-700 bg-violet-50 rounded-t-lg transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-home mr-2"></i>
                    Family Details
                </span>
            </button>
            <button type="button" id="tab-family-members" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Family Members
                </span>
            </button>
            <button type="button" id="tab-additional" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Additional Info
                </span>
            </button>
            <button type="button" id="tab-social-service" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Social Service
                </span>
            </button>
            <button type="button" id="tab-health" class="tab-button flex-shrink-0 px-5 py-3 text-sm font-medium text-gray-600 hover:text-violet-600 border-b-2 border-transparent hover:border-violet-400 transition-all duration-200">
                <span class="flex items-center">
                    <i class="fas fa-file-signature mr-2"></i>
                    Health & Signatures
                </span>
            </button>
        </div>

        <!-- Progress indicator -->
        <div class="mb-6">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span>Step <span id="current-step">1</span> of 5</span>
                <span id="step-title">Family Details</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-violet-600 h-2 rounded-full transition-all duration-500" style="width: 20%"></div>
            </div>
        </div>

        <form id="updateRemarksForm" method="POST">
            @csrf
            <input type="hidden" name="id" id="remarks_id">
            <input type="hidden" id="modal_mode" value="edit">

            <!-- Family Details Tab -->
            <div id="tab-family-content" class="tab-content">
                <!-- Head of Family Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-violet-600"></i>
                        Head of Family
                    </h3>
                    
                    <!-- Row 1: 4Ps and IP No. -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">4Ps</label>
                            <select name="head_4ps" id="head_4ps" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">IP No.</label>
                            <input type="text" name="head_ipno" id="head_ipno" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="Optional">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Serial Number</label>
                            <input type="text" name="serial_number" id="serial_number" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                        </div>
                    </div>
                    
                    <!-- Row 2: Applicant Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="applicant_fname" id="applicant_fname" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Middle Name</label>
                            <input type="text" name="applicant_mname" id="applicant_mname" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="applicant_lname" id="applicant_lname" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Suffix</label>
                            <input type="text" name="applicant_suffix" id="applicant_suffix" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                    </div>
                    
                    <!-- Row 3: Address, Zone, Barangay -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="head_address" id="head_address" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Zone</label>
                            <input type="text" name="head_zone" id="head_zone" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Barangay <span class="text-red-500">*</span></label>
                            <input type="text" name="head_barangay" id="head_barangay" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                        </div>
                    </div>
                    
                    <!-- Row 4: Date of Birth, Place of Birth, Gender -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth <span class="text-red-500">*</span></label>
                            <input type="date" name="head_dob" id="head_dob" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Place of Birth</label>
                            <input type="text" name="head_pob" id="head_pob" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
                            <select name="applicant_gender" id="applicant_gender" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Row 5: Education, Occupation, Religion -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Education</label>
                            <select name="head_educ" id="head_educ" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Select Education</option>
                                <option value="Elementary">Elementary</option>
                                <option value="High School">High School</option>
                                <option value="Vocational">Vocational</option>
                                <option value="College">College</option>
                                <option value="Post Graduate">Post Graduate</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Occupation</label>
                            <select name="head_occ" id="head_occ" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Select Occupation</option>
                                <option value="Farmer">Farmer</option>
                                <option value="Teacher">Teacher</option>
                                <option value="Driver">Driver</option>
                                <option value="Business Owner">Business Owner</option>
                                <option value="Employee">Employee</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Student">Student</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Religion</label>
                            <select name="head_religion" id="head_religion" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Select Religion</option>
                                <option value="Catholic">Catholic</option>
                                <option value="Protestant">Protestant</option>
                                <option value="Islam">Islam</option>
                                <option value="Buddhist">Buddhist</option>
                                <option value="Atheist">Atheist</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Row 6: Serial Number -->
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('family-members')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Family Members Tab -->
            <div id="tab-family-members-content" class="tab-content hidden">
                <!-- Family Members Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-users mr-2 text-violet-600"></i>
                        Family Members
                    </h3>
                    <p class="text-sm text-gray-600 mb-4 bg-blue-50 p-3 rounded-lg">Please fill up all required fields in the family members table. Remarks should be selected based on the categories listed below.</p>
                    
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table id="family_members_table" class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Name</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Relation</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Birthdate</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Age</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Sex</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Civil Status</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Education</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Occupation</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Income</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Remarks</th>
                                    <th class="border px-3 py-3 font-semibold text-gray-700 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="family_members_tbody" class="table-input-styling">
                                <!-- Rows will be added dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <button type="button" onclick="addFamilyMemberRow()" class="mt-4 bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Family Member
                    </button>
                    
                    <textarea name="family_members" id="family_members" class="hidden"></textarea>
                    
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-tags mr-2 text-violet-600"></i>
                            Remarks Categories:
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Out of School Youth (OSY)</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Solo Parent (SP)</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Person with Disability (PWD)</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Senior Citizen (SC)</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Lactating Mother</span>
                            </div>
                            <div class="text-sm border border-gray-300 rounded-lg p-3 bg-white hover:bg-violet-50 hover:border-violet-300 transition-all duration-200">
                                <span class="font-medium text-gray-800">Pregnant Mother</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('family')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <button type="button" onclick="showTab('additional')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Additional Info Tab -->
            <div id="tab-additional-content" class="tab-content hidden">
                <!-- Household Info Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-home mr-2 text-violet-600"></i>
                        Household Information
                    </h3>
                    
                    <!-- Income Section -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Income Calculation
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Other Income</label>
                                <input type="number" step="0.01" name="other_income" id="other_income" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="0.00">
                                <p class="text-xs text-gray-500 mt-2">Additional income not from family members</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Total Income</label>
                                <input type="number" step="0.01" name="house_total_income" id="house_total_income" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                                <p class="text-xs text-gray-500 mt-2">Family Members Income + Other Income</p>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses Section -->
                    <div class="mb-6 p-4 bg-red-50 rounded-xl border border-red-200">
                        <h4 class="font-semibold text-red-800 mb-3 flex items-center">
                            <i class="fas fa-receipt mr-2"></i>
                            Expenses
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">House</label>
                                <select name="house_house" id="house_house" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                                    <option value="">Select</option>
                                    <option value="Owned">Owned</option>
                                    <option value="Rent">Rent</option>
                                </select>
                                <div id="house_rent_group" style="display: none;" class="mt-3">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">House Rent</label>
                                    <input type="number" step="0.01" name="house_house_rent" id="house_house_rent" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Lot</label>
                                <select name="house_lot" id="house_lot" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200">
                                    <option value="">Select</option>
                                    <option value="Owned">Owned</option>
                                    <option value="Rent">Rent</option>
                                </select>
                                <div id="lot_rent_group" style="display: none;" class="mt-3">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lot Rent</label>
                                    <input type="number" step="0.01" name="house_lot_rent" id="house_lot_rent" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Water</label>
                                <input type="number" step="0.01" name="house_water" id="house_water" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Electric</label>
                                <input type="number" step="0.01" name="house_electric" id="house_electric" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Net Income Section -->
                    <div class="mb-6 p-4 bg-green-50 rounded-xl border border-green-200">
                        <h4 class="font-semibold text-green-800 mb-3 flex items-center">
                            <i class="fas fa-calculator mr-2"></i>
                            Net Income Calculation
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Net Income</label>
                                <input type="number" step="0.01" name="house_net_income" id="house_net_income" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                                <p class="text-xs text-gray-500 mt-2">Total Income - Total Expenses</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Remarks <span class="text-red-500">*</span></label>
                                <select name="remarks" id="remarks" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200" required>
                                    <option value="">Select Remarks</option>
                                    <option value="Poor">Poor</option>
                                    <option value="Non Poor">Non Poor</option>
                                    <option value="Ultra Poor">Ultra Poor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('family-members')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <button type="button" id="additional-next-btn" onclick="showTab('social-service')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center" disabled>
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Social Service Records Tab -->
            <div id="tab-social-service-content" class="tab-content hidden">
                <!-- Social Service Records Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-clipboard-list mr-2 text-violet-600"></i>
                        Social Service Records
                    </h3>
                    
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table id="rv_service_records_table" class="w-full border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-3 py-3 text-left font-semibold text-gray-700">Date</th>
                                    <th class="border border-gray-300 px-3 py-3 text-left font-semibold text-gray-700">Problem/Need</th>
                                    <th class="border border-gray-300 px-3 py-3 text-left font-semibold text-gray-700">Action/Assistance Given</th>
                                    <th class="border border-gray-300 px-3 py-3 text-left font-semibold text-gray-700">Remarks</th>
                                    <th class="border border-gray-300 px-3 py-3 text-center font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody id="rv_service_records_tbody">
                                <!-- Rows will be added dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <button type="button" onclick="addRvServiceRecordRow()" class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Record
                    </button>
                               
<div class="mt-4 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-500 px-4 py-2">
        <h4 class="font-bold text-white flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Remarks Key
        </h4>
    </div>
    <div class="p-4">
        <div class="grid grid-cols-2 gap-3">
            <div class="flex items-center p-2 hover:bg-amber-50 rounded transition-colors">
                <span class="w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center mr-2">A</span>
                <span class="text-sm font-medium">DEAD</span>
            </div>
            <div class="flex items-center p-2 hover:bg-amber-50 rounded transition-colors">
                <span class="w-6 h-6 bg-orange-500 text-white text-xs font-bold rounded-full flex items-center justify-center mr-2">B</span>
                <span class="text-sm font-medium">INJURED</span>
            </div>
            <div class="flex items-center p-2 hover:bg-amber-50 rounded transition-colors">
                <span class="w-6 h-6 bg-blue-500 text-white text-xs font-bold rounded-full flex items-center justify-center mr-2">C</span>
                <span class="text-sm font-medium">MISSING</span>
            </div>
            <div class="flex items-center p-2 hover:bg-amber-50 rounded transition-colors">
                <span class="w-6 h-6 bg-green-500 text-white text-xs font-bold rounded-full flex items-center justify-center mr-2">D</span>
                <span class="text-sm font-medium">With Illness</span>
            </div>
        </div>
    </div>
</div>
         
                    <textarea name="rv_service_records" id="rv_service_records" class="hidden"></textarea>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('additional')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <button type="button" onclick="showTab('health')" class="px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        Next
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Health & Signatures Tab -->
            <div id="tab-health-content" class="tab-content hidden">
                <!-- Health & Signatures Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200 flex items-center">
                        <i class="fas fa-file-signature mr-2 text-violet-600"></i>
                        Health & Signatures
                    </h3>
                    
                    <!-- Worker Name and Officer Name in one line -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Worker Name</label>
                            <input type="text" name="worker_name" id="worker_name" value="{{ session('lydopers') ? (session('lydopers')->lydopers_fname . ' ' . session('lydopers')->lydopers_lname) : '' }}" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Officer Name</label>
                            <input type="text" name="officer_name" id="officer_name" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                    </div>
                    
                    <!-- Date Entry and Signature Client -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date Entry</label>
                            <input type="date" name="date_entry" id="date_entry" class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Signature Client</label>
                            <input type="text" name="signature_client" id="signature_client" class="w-full border border-gray-300 rounded-xl p-3 bg-gray-100 text-gray-600" readonly>
                        </div>
                    </div>
                    
                    <!-- Signature Worker and Signature Officer with modals -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Signature Worker</label>
                            <button type="button" onclick="openSignatureModal('worker')" class="w-full border border-gray-300 rounded-xl p-3 bg-white hover:bg-gray-50 text-left transition-all duration-200 flex items-center justify-between">
                                <span id="worker-signature-text">Click to Sign</span>
                                <i class="fas fa-pen text-violet-600"></i>
                            </button>
                            <input type="hidden" name="signature_worker" id="signature_worker">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Signature Officer</label>
                            <button type="button" onclick="openSignatureModal('officer')" class="w-full border border-gray-300 rounded-xl p-3 bg-white hover:bg-gray-50 text-left transition-all duration-200 flex items-center justify-between">
                                <span id="officer-signature-text">Click to Sign</span>
                                <i class="fas fa-pen text-violet-600"></i>
                            </button>
                            <input type="hidden" name="signature_officer" id="signature_officer">
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="showTab('social-service')" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Previous
                    </button>
                    <div class="flex gap-3">
                        <button type="button" onclick="saveAsDraft()" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-xl font-medium transition-all duration-200">
                            Save as Draft
                        </button>
                        <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Submit Form
                        </button>
                    </div>
                </div>
            </div>
       

        <!-- Review Modal for Reviewed Applicants -->
        <div id="reviewModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="text-xl font-bold">Review Family Intake Sheet</h2>
                    <button class="modal-close" onclick="closeReviewModal()">&times;</button>
                </div>

                <div id="modalReviewContent">
                    <!-- Content will be populated here -->
                </div>

                <div class="modal-actions">
                    <button
                        type="button"
                        onclick="printReviewModal()"
                        class="bg-blue-500 text-white px-5 py-2 rounded hover:bg-blue-600 mr-2"
                    >
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button
                        type="button"
                        onclick="closeReviewModal()"
                        class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Signature Modal -->
        <div id="signatureModal" class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800" id="signatureModalTitle">Signature</h3>
                    <button type="button" onclick="closeSignatureModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50">
                        <canvas id="signatureCanvas" width="400" height="200" class="border border-gray-300 rounded bg-white"></canvas>
                    </div>
                </div>

                <div class="flex justify-between gap-3">
                    <button type="button" onclick="clearSignature()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        <i class="fas fa-eraser mr-2"></i>Clear
                    </button>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeSignatureModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="button" onclick="saveSignature()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Save Signature
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Add this function to set current date
            function setCurrentDate() {
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('date_entry').value = today;
            }

            // Main Tab switching functionality
            function showTable() {
                document.getElementById('tableView').classList.remove('hidden');
                document.getElementById('listView').classList.add('hidden');
                document.getElementById('tab-screening').classList.add('active');
                document.getElementById('tab-reviewed').classList.remove('active');
                filterTable();
            }

            function showList() {
                document.getElementById('tableView').classList.add('hidden');
                document.getElementById('listView').classList.remove('hidden');
                document.getElementById('tab-screening').classList.remove('active');
                document.getElementById('tab-reviewed').classList.add('active');
                filterList();
            }

            // Modal Tab switching functionality
            function showTab(tabName) {
                // Hide all tab contents
                const tabContents = document.querySelectorAll('.tab-content');
                tabContents.forEach(content => content.classList.add('hidden'));

                // Remove active class from all tab buttons
                const tabButtons = document.querySelectorAll('.tab-button');
                tabButtons.forEach(button => {
                    button.classList.remove('active');
                    button.classList.remove('text-violet-600');
                    button.classList.add('text-gray-500');
                    button.classList.remove('border-b-2', 'border-violet-600');
                });

                // Show the selected tab content
                document.getElementById('tab-' + tabName + '-content').classList.remove('hidden');

                // Add active class to the selected tab button
                const activeTab = document.getElementById('tab-' + tabName);
                activeTab.classList.add('active', 'text-violet-600', 'border-b-2', 'border-violet-600');
                activeTab.classList.remove('text-gray-500');

                // Update progress bar and step indicator
                updateProgress(tabName);
            }

            // Update progress bar and step indicator
            function updateProgress(tabName) {
                const stepInfo = {
                    'family': { step: 1, title: 'Family Details', width: '20%' },
                    'family-members': { step: 2, title: 'Family Members', width: '40%' },
                    'additional': { step: 3, title: 'Additional Info', width: '60%' },
                    'social-service': { step: 4, title: 'Social Service', width: '80%' },
                    'health': { step: 5, title: 'Health & Signatures', width: '100%' }
                };

                const info = stepInfo[tabName];
                if (info) {
                    document.getElementById('current-step').textContent = info.step;
                    document.getElementById('step-title').textContent = info.title;
                    document.getElementById('progress-bar').style.width = info.width;
                }
            }

            // Open Edit Remarks Modal
            function openEditRemarksModal(button) {
                const id = button.getAttribute("data-id");
                const name = button.getAttribute("data-name");
                const fname = button.getAttribute("data-fname");
                const mname = button.getAttribute("data-mname");
                const lname = button.getAttribute("data-lname");
                const suffix = button.getAttribute("data-suffix");
                const bdate = button.getAttribute("data-bdate");
                const brgy = button.getAttribute("data-brgy");
                const gender = button.getAttribute("data-gender");
                const pob = button.getAttribute("data-pob");

                // Set the values in the modal form
                document.getElementById('remarks_id').value = id;
                document.getElementById('applicant_fname').value = fname || '';
                document.getElementById('applicant_mname').value = mname || '';
                document.getElementById('applicant_lname').value = lname || '';
                document.getElementById('applicant_suffix').value = suffix || '';
                document.getElementById('head_dob').value = bdate || '';
                document.getElementById('head_barangay').value = brgy || '';

                // Generate serial number and location
                document.getElementById('serial_number').value = 'SN-' + Date.now();


                // Set current date for Date Entry
                setCurrentDate();

                // Clear previous family members and service records
                document.getElementById('family_members_tbody').innerHTML = '';
                document.getElementById('rv_service_records_tbody').innerHTML = '';

                // Fetch existing intake sheet data and populate form
                fetch(`/lydo_staff/intake-sheet/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            populateEditModal(data);
                        }
                    })
                    .catch(err => console.error('Error fetching intake sheet data:', err))
                    .finally(() => {
                        // Show the modal
                        document.getElementById('editRemarksModal').classList.remove('hidden');
                        document.body.classList.add('modal-open');

                        // Reset to first tab
                        showTab('family');
                    });
            }

            // Close Edit Remarks Modal
            function closeEditRemarksModal() {
                document.getElementById('editRemarksModal').classList.add('hidden');
                document.body.classList.remove('modal-open');
            }

            // Open Review Modal
            function openReviewModal(button) {
                const id = button.getAttribute("data-id");
                if (!id) {
                    console.error('No ID provided');
                    return;
                }
                
                // Show loading state
                document.getElementById('modalReviewContent').innerHTML = '<div class="p-4 text-center">Loading...</div>';
                
                // Fetch intake sheet data with error handling
                fetch(`/lydo_staff/intake-sheet/${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Fetched data:', data); // Debug log
                        if (data) {
                            populateReviewModal(data);
                            document.getElementById('reviewModal').style.display = 'block';
                            document.body.classList.add('modal-open');
                        } else {
                            throw new Error('No data received');
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching intake sheet data:', err);
                        document.getElementById('modalReviewContent').innerHTML = `
                            <div class="p-4 text-center text-red-600">
                                Error loading data: ${err.message}
                            </div>
                        `;
                    });
            }

            // Close Review Modal
            function closeReviewModal() {
                document.getElementById('reviewModal').style.display = 'none';
                document.body.classList.remove('modal-open');
            }

            // Update the populateReviewModal function to handle null/undefined values
            function populateReviewModal(d) {
                if (!d) {
                    console.error('No data received');
                    return;
                }

                const modalContent = document.getElementById('modalReviewContent');
                
                // Add console logging to debug
                console.log('Received data:', d);
                
                // Check if required data exists before populating
                const fullName = [
                    d.applicant_fname || '',
                    d.applicant_mname || '',
                    d.applicant_lname || '',
                    d.applicant_suffix || ''
                ].filter(Boolean).join(' ');

                modalContent.innerHTML = `
                    <div class="review-columns">
                        <div class="space-y-4">
                            <div class="print-box p-4">
                                <p><strong>Serial No.:</strong> ${d.serial_number || "N/A"}</p>
                                <p><strong>Name:</strong> ${fullName || "N/A"}</p>
                                <table class="min-w-full text-sm">
                                    <tr>
                                        <td><strong>Sex:</strong> ${d.applicant_gender || "N/A"}</td>
                                        <td><strong>4Ps:</strong> ${d.head_4ps || "N/A"}</td>
                                        <td><strong>IP No.:</strong> ${d.head_ipno || "N/A"}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="print-box p-4">
                                <h4 class="font-semibold">Family Members</h4>
                                <table class="min-w-full text-sm border border-gray-300">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border px-2 py-1">Name</th>
                                            <th class="border px-2 py-1">Relation</th>
                                            <th class="border px-2 py-1">Birthdate</th>
                                            <th class="border px-2 py-1">Age</th>
                                            <th class="border px-2 py-1">Sex</th>
                                            <th class="border px-2 py-1">Civil Status</th>
                                            <th class="border px-2 py-1">Educational Attainment</th>
                                            <th class="border px-2 py-1">Occupation</th>
                                            <th class="border px-2 py-1">Income</th>
                                            <th class="border px-2 py-1">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${(() => {
                                            let familyMembers = [];
                                            if (d.family_members) {
                                                if (typeof d.family_members === 'string') {
                                                    try {
                                                        familyMembers = JSON.parse(d.family_members);
                                                    } catch (e) {
                                                        familyMembers = [];
                                                    }
                                                } else if (Array.isArray(d.family_members)) {
                                                    familyMembers = d.family_members;
                                                }
                                            }
                                            return familyMembers.length > 0 ? familyMembers.map(f => `
                                                <tr>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.name || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.relationship || f.relation || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${formatDate(f.birthdate || f.birth)}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.age || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.sex || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.civil_status || f.civil || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.education || f.educ || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.occupation || f.occ || '')}</td>
                                                    <td class="border px-2 py-1 text-left">₱${escapeHtml(f.monthly_income || f.income || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(f.remarks || '')}</td>
                                                </tr>
                                            `).join('') : '<tr><td colspan="10" class="border px-2 py-1 text-center text-gray-500">No family members found</td></tr>';
                                        })()}
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="print-box p-4">
                                <h4 class="font-semibold">Household Information</h4>
                                <table class="min-w-full text-sm">
                                    <tr>
                                        <td><strong>Other Source of Income:</strong> ₱${d.other_income || "-"}</td>
                                        <td><strong>Total Family Income:</strong> ₱${d.house_total_income || "-"}</td>
                                        <td><strong>Total Family Net Income:</strong> ₱${d.house_net_income || "-"}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>House (Owned/Rented):</strong> ${d.house_house || "-"} ${d.house_house_rent ? `(Rent: ₱${d.house_house_rent})` : ''}</td>
                                        <td><strong>Lot (Owned/Rented):</strong> ${d.house_lot || "-"} ${d.house_lot_rent ? `(Rent: ₱${d.house_lot_rent})` : ''}</td>
                                        <td><strong>Water:</strong> ₱${d.house_water || "-"}</td>
                                        <td><strong>Electricity Source:</strong> ₱${d.house_electric || "-"}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="print-box p-4">
                                <h4 class="font-semibold">Social Service Records</h4>
                                <table class="min-w-full text-sm thin-border">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border px-2 py-1">Date</th>
                                            <th class="border px-2 py-1">Problem/Need</th>
                                            <th class="border px-2 py-1">Action/Assistance Given</th>
                                            <th class="border px-2 py-1">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${(() => {
                                            let serviceRecords = d.rv_service_records;
                                            if (typeof serviceRecords === 'string') {
                                                try {
                                                    serviceRecords = JSON.parse(serviceRecords);
                                                } catch (e) {
                                                    serviceRecords = [];
                                                }
                                            }
                                            return Array.isArray(serviceRecords) && serviceRecords.length > 0 ? serviceRecords.map(r => `
                                                <tr>
                                                    <td class="border px-2 py-1 text-left">${formatDate(r.date)}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(r.problem || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(r.action || '')}</td>
                                                    <td class="border px-2 py-1 text-left">${escapeHtml(r.remarks || '')}</td>
                                                </tr>
                                            `).join('') : '<tr><td colspan="4" class="border px-2 py-1 text-center text-gray-500">No social service records found</td></tr>';
                                        })()}
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="print-box p-4">
                                <h4 class="font-semibold">Signatures</h4>
                                <div>
                                    <p><strong>Family Head:</strong></p>
                                    <div>
                                        ${d.signature_client ? 
                                            `<img src="${d.signature_client}" style="max-width: 100%; height: 80px;" />` : 
                                            '<p class="text-xs text-gray-500">No signature</p>'
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Utility functions
            function formatDate(dateString) {
                if (!dateString) return "-";
                const date = new Date(dateString);
                if (isNaN(date)) return dateString;
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            }

            function escapeHtml(s) {
                if (!s) return "";
                return s.replace(
                    /[&<>"']/g,
                    (m) =>
                        ({
                            "&": "&amp;",
                            "<": "&lt;",
                            ">": "&gt;",
                            '"': "&quot;",
                            "'": "&#39;",
                        }[m])
                );
            }

            // Family Members Functions
            function addFamilyMemberRow() {
                const tbody = document.getElementById('family_members_tbody');

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="border px-2 py-1">
                        <input type="text" name="family_member_name[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="Full Name">
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_relation[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                      <option value="">Select</option>
                      <option value="Spouse">Spouse</option>
                      <option value="Son">Son</option>
                      <option value="Daughter">Daughter</option>
                      <option value="Father">Father</option>
                      <option value="Mother">Mother</option>
                      <option value="Brother">Brother</option>
                      <option value="Sister">Sister</option>
                      <option value="Grandchild">Grandchild</option>
                      <option value="Other">Other</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <input type="date" name="family_member_birthdate[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" onchange="calculateAge(this)">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="number" name="family_member_age[]" class="w-full border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-600" placeholder="Age" readonly>
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_sex[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_civil_status[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                      <option value="">Select</option>
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widowed">Widowed</option>
                      <option value="Divorced">Divorced</option>
                      <option value="Separated">Separated</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_education[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                      <option value="">Select</option>
                      <option value="None">None</option>
                      <option value="Elementary">Elementary</option>
                      <option value="High School">High School</option>
                      <option value="College">College</option>
                      <option value="Vocational">Vocational</option>
                      <option value="Graduate">Graduate</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" name="family_member_occupation[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="Occupation">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="number" step="0.01" name="family_member_income[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="0.00">
                    </td>
                    <td class="border px-2 py-1">
                        <select name="family_member_remarks[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                                 <option value="">Select</option>
                      <option value="CIC">CIC</option>
                      <option value="OSY">OSY</option>
                      <option value="SP">SP</option>
                      <option value="PWD">PWD</option>
                      <option value="SC">SC</option>
                      <option value="None">None</option>
                      <option value="Lactating Mother">Lactating Mother</option>
                      <option value="Pregnant Mother">Pregnant Mother</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors duration-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            }

            // Function to calculate age from birthdate
            function calculateAge(birthdateInput) {
                const birthdate = new Date(birthdateInput.value);
                const today = new Date();

                if (isNaN(birthdate)) {
                    return;
                }

                let age = today.getFullYear() - birthdate.getFullYear();
                const monthDiff = today.getMonth() - birthdate.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
                    age--;
                }

                // Find the age input in the same row
                const row = birthdateInput.closest('tr');
                const ageInput = row.querySelector('input[name="family_member_age[]"]');
                if (ageInput) {
                    ageInput.value = age;
                }
            }

            // Service Records Functions
            function addRvServiceRecordRow() {
                const tbody = document.getElementById('rv_service_records_tbody');

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="border px-2 py-1">
                        <input type="date" name="service_record_date[]" class="w-full border-none focus:ring-0" value="${new Date().toISOString().split('T')[0]}">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" name="service_record_problem[]" class="w-full border-none focus:ring-0" placeholder="Problem/Need">
                    </td>
                    <td class="border px-2 py-1">
                        <input type="text" name="service_record_action[]" class="w-full border-none focus:ring-0" placeholder="Action/Assistance">
                    </td>
                    <td class="border px-2 py-1">
                        <select name="service_record_remarks[]" class="w-full border-none focus:ring-0">
                            <option value="">Select Remarks</option>
                            <option value="A. DEAD">A. DEAD</option>
                            <option value="B. INJURED">B. INJURED</option>
                            <option value="C. MISSING">C. MISSING</option>
                            <option value="D. With Illness">D. With Illness</option>
                        </select>
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            }

            // Filtering functions
            function filterTable() {
                const nameSearchValue = document.getElementById('nameSearch').value.toLowerCase().trim();
                const barangayFilterValue = document.getElementById('barangayFilter').value.toLowerCase().trim();

                const tableViewRows = document.querySelectorAll('#tableView tbody tr');
                tableViewRows.forEach(row => {
                    const nameCell = row.cells[1].textContent.toLowerCase();
                    const barangayCell = row.cells[2].textContent.toLowerCase();

                    const matchesName = nameCell.includes(nameSearchValue);
                    const matchesBarangay = barangayFilterValue === '' || barangayCell.includes(barangayFilterValue);

                    if (matchesName && matchesBarangay) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            function filterList() {
                const nameSearchValue = document.getElementById('listNameSearch').value.toLowerCase().trim();
                const barangayFilterValue = document.getElementById('listBarangayFilter').value.toLowerCase().trim();

                const listViewRows = document.querySelectorAll('#listView tbody tr');
                listViewRows.forEach(row => {
                    const nameCell = row.cells[1].textContent.toLowerCase();
                    const barangayCell = row.cells[2].textContent.toLowerCase();

                    const matchesName = nameCell.includes(nameSearchValue);
                    const matchesBarangay = barangayFilterValue === '' || barangayCell.includes(barangayFilterValue);

                    if (matchesName && matchesBarangay) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Update the calculateIncomes function with proper calculation
            function calculateIncomes() {
                // Calculate total family income from family members
                let totalFamilyIncome = 0;
                const incomeInputs = document.querySelectorAll('input[name="family_member_income[]"]');
                incomeInputs.forEach(input => {
                    totalFamilyIncome += parseFloat(input.value) || 0;
                });

                // Add other income
                const otherIncome = parseFloat(document.getElementById('other_income').value) || 0;
                const houseTotalIncome = totalFamilyIncome + otherIncome;
                
                // Set total income
                document.getElementById('house_total_income').value = houseTotalIncome.toFixed(2);

                // Calculate expenses (house rent, lot rent, water, electric)
                const houseRent = parseFloat(document.getElementById('house_house_rent').value) || 0;
                const lotRent = parseFloat(document.getElementById('house_lot_rent').value) || 0;
                const houseWater = parseFloat(document.getElementById('house_water').value) || 0;
                const houseElectric = parseFloat(document.getElementById('house_electric').value) || 0;
                
                // Total expenses (house rent + lot rent + water + electric)
                const totalExpenses = houseRent + lotRent + houseWater + houseElectric;
                
                // Calculate net income (total income minus total expenses)
                const netIncome = houseTotalIncome - totalExpenses;
                
                document.getElementById('house_net_income').value = netIncome.toFixed(2);
            }

            // Form submission handling
            document.addEventListener('DOMContentLoaded', function() {
                // Set current date when page loads
                setCurrentDate();

                // Add event listeners for house and lot toggles
                const houseSelect = document.getElementById('house_house');
                const lotSelect = document.getElementById('house_lot');
                const houseRentGroup = document.getElementById('house_rent_group');
                const lotRentGroup = document.getElementById('lot_rent_group');

                function toggleHouseFields() {
                    const value = houseSelect.value;
                    if (value === 'Rent') {
                        houseRentGroup.style.display = 'block';
                    } else {
                        houseRentGroup.style.display = 'none';
                        document.getElementById('house_house_rent').value = '';
                        calculateIncomes(); // Recalculate when field is hidden
                    }
                }

                function toggleLotFields() {
                    const value = lotSelect.value;
                    if (value === 'Rent') {
                        lotRentGroup.style.display = 'block';
                    } else {
                        lotRentGroup.style.display = 'none';
                        document.getElementById('house_lot_rent').value = '';
                        calculateIncomes(); // Recalculate when field changes
                    }
                }

                if (houseSelect) {
                    houseSelect.addEventListener('change', toggleHouseFields);
                    // Initialize on page load
                    toggleHouseFields();
                }
                if (lotSelect) {
                    lotSelect.addEventListener('change', toggleLotFields);
                    // Initialize on page load
                    toggleLotFields();
                }

                // Add event listeners for all income and expense fields
                document.addEventListener('input', function(e) {
                    if (e.target.name === 'family_member_income[]' ||
                        e.target.id === 'other_income' ||
                        e.target.id === 'house_house_rent' ||
                        e.target.id === 'house_lot_rent' ||
                        e.target.id === 'house_water' ||
                        e.target.id === 'house_electric') {
                        calculateIncomes();
                    }
                });

                // Enable/disable next button based on remarks selection
                const remarksSelect = document.getElementById('remarks');
                const additionalNextBtn = document.getElementById('additional-next-btn');

                function checkRemarksSelection() {
                    if (remarksSelect && remarksSelect.value) {
                        additionalNextBtn.disabled = false;
                        additionalNextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        additionalNextBtn.classList.add('hover:bg-blue-700');
                    } else {
                        additionalNextBtn.disabled = true;
                        additionalNextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        additionalNextBtn.classList.remove('hover:bg-blue-700');
                    }
                }

                if (remarksSelect) {
                    remarksSelect.addEventListener('change', checkRemarksSelection);
                    // Initial check
                    checkRemarksSelection();
                }

                // Add confirmation for modal form submit
                const modalForm = document.getElementById('updateRemarksForm');
                if (modalForm) {
                    modalForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const selectedRemarks = document.getElementById('remarks').value;
                        if (!selectedRemarks) {
                            Swal.fire('Error', 'Please select a remark before updating.', 'error');
                            return;
                        }

                        // Serialize family members data
                        let familyMembers = [];
                        const familyRows = document.querySelectorAll('#family_members_tbody tr');
                        familyRows.forEach(row => {
                            const cells = row.cells;
                            familyMembers.push({
                                name: cells[0].querySelector('input')?.value || '',
                                relationship: cells[1].querySelector('select')?.value || '',
                                birthdate: cells[2].querySelector('input')?.value || '',
                                age: cells[3].querySelector('input')?.value || '',
                                sex: cells[4].querySelector('select')?.value || '',
                                civil_status: cells[5].querySelector('select')?.value || '',
                                education: cells[6].querySelector('select')?.value || '',
                                occupation: cells[7].querySelector('input')?.value || '',
                                monthly_income: cells[8].querySelector('input')?.value || '',
                                remarks: cells[9].querySelector('select')?.value || '',
                            });
                        });

                        // Convert to JSON string
                        document.getElementById('family_members').value = JSON.stringify(familyMembers);

                        // Serialize service records data
                        let serviceRecords = [];
                        const serviceRows = document.querySelectorAll('#rv_service_records_tbody tr');
                        serviceRows.forEach(row => {
                            const cells = row.cells;
                            serviceRecords.push({
                                date: cells[0].querySelector('input')?.value || '',
                                problem: cells[1].querySelector('input')?.value || '',
                                action: cells[2].querySelector('input')?.value || '',
                                remarks: cells[3].querySelector('input')?.value || '',
                            });
                        });

                        // Convert to JSON string
                        document.getElementById('rv_service_records').value = JSON.stringify(serviceRecords);

                        const id = document.getElementById('remarks_id').value;
                        modalForm.action = "/lydo_staff/update-intake-sheet/" + id;

                        // Show loading state
                        Swal.fire({
                            title: 'Saving Intake Sheet',
                            text: 'Please wait...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form via AJAX to handle errors better
                        fetch(modalForm.action, {
                            method: 'POST',
                            body: new FormData(modalForm),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(data => {
                            Swal.close();

                            // Check if response contains success message
                            if (data.includes('success')) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Intake sheet updated successfully!',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    closeEditRemarksModal();
                                    location.reload(); // Reload to reflect changes
                                });
                            } else {
                                throw new Error('Unexpected response');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update intake sheet: ' + error.message,
                                confirmButtonText: 'OK'
                            });
                        });
                    });
                }

                // Add event listeners for real-time filtering
                document.getElementById('nameSearch').addEventListener('input', filterTable);
                document.getElementById('barangayFilter').addEventListener('change', filterTable);
                document.getElementById('listNameSearch').addEventListener('input', filterList);
                document.getElementById('listBarangayFilter').addEventListener('change', filterList);

                // Close modals when clicking outside
                window.addEventListener('click', function(event) {
                    const editModal = document.getElementById('editRemarksModal');
                    const reviewModal = document.getElementById('reviewModal');
                    
                    if (event.target === editModal) {
                        closeEditRemarksModal();
                    }
                    if (event.target === reviewModal) {
                        closeReviewModal();
                    }
                });

                // Initial calculation
                calculateIncomes();
            });

            // Populate Edit Modal with existing data
            function populateEditModal(data) {
                // Populate head of family details
                document.getElementById('head_4ps').value = data.head_4ps || '';
                document.getElementById('head_ipno').value = data.head_ipno || '';
                document.getElementById('head_address').value = data.head_address || '';
                document.getElementById('head_zone').value = data.head_zone || '';
                document.getElementById('head_educ').value = data.head_educ || '';
                document.getElementById('head_occ').value = data.head_occ || '';
                document.getElementById('head_religion').value = data.head_religion || '';

                // Only populate Place of Birth and Gender if intake sheet has saved values
                if (data.head_pob) {
                    document.getElementById('head_pob').value = data.head_pob;
                }
                if (data.applicant_gender) {
                    document.getElementById('applicant_gender').value = data.applicant_gender;
                }

                // Populate household information
                document.getElementById('other_income').value = data.other_income || '';
                document.getElementById('house_total_income').value = data.house_total_income || '';
                document.getElementById('house_net_income').value = data.house_net_income || '';
                document.getElementById('house_house').value = data.house_house || '';
                document.getElementById('house_house_rent').value = data.house_house_rent || '';
                document.getElementById('house_lot').value = data.house_lot || '';
                document.getElementById('house_lot_rent').value = data.house_lot_rent || '';
                document.getElementById('house_water').value = data.house_water || '';
                document.getElementById('house_electric').value = data.house_electric || '';

                // Handle conditional fields for house and lot
                const houseSelect = document.getElementById('house_house');
                const lotSelect = document.getElementById('house_lot');

                if (data.house_house === 'Rent') {
                    document.getElementById('house_rent_group').style.display = 'block';
                    document.getElementById('house_house_rent').value = data.house_house_rent || '';
                }

                if (data.house_lot === 'Rent') {
                    document.getElementById('lot_rent_group').style.display = 'block';
                    document.getElementById('house_lot_rent').value = data.house_lot_rent || '';
                }

                // Trigger change events to update UI
                if (houseSelect) {
                    houseSelect.dispatchEvent(new Event('change'));
                }
                if (lotSelect) {
                    lotSelect.dispatchEvent(new Event('change'));
                }

                // Populate remarks
                document.getElementById('remarks').value = data.remarks || '';

                // Populate health & signatures
                document.getElementById('worker_name').value = data.worker_name || '';
                document.getElementById('officer_name').value = data.officer_name || '';

                // Populate family members
                if (data.family_members) {
                    let familyMembers = data.family_members;
                    if (typeof familyMembers === 'string') {
                        try {
                            familyMembers = JSON.parse(familyMembers);
                        } catch (e) {
                            familyMembers = [];
                        }
                    }
                    if (Array.isArray(familyMembers)) {
                        familyMembers.forEach(member => {
                            addFamilyMemberRow();
                            const rows = document.querySelectorAll('#family_members_tbody tr');
                            const lastRow = rows[rows.length - 1];
                            lastRow.cells[0].querySelector('input').value = member.name || '';
                            lastRow.cells[1].querySelector('select').value = member.relationship || member.relation || '';
                            lastRow.cells[2].querySelector('input').value = member.birthdate || member.birth || '';
                            lastRow.cells[3].querySelector('input').value = member.age || '';
                            lastRow.cells[4].querySelector('select').value = member.sex || '';
                            lastRow.cells[5].querySelector('select').value = member.civil_status || member.civil || '';
                            lastRow.cells[6].querySelector('select').value = member.education || member.educ || '';
                            lastRow.cells[7].querySelector('input').value = member.occupation || member.occ || '';
                            lastRow.cells[8].querySelector('input').value = member.monthly_income || member.income || '';
                            lastRow.cells[9].querySelector('select').value = member.remarks || '';
                        });
                    }
                }

                // Populate service records
                if (data.rv_service_records) {
                    let serviceRecords = data.rv_service_records;
                    if (typeof serviceRecords === 'string') {
                        try {
                            serviceRecords = JSON.parse(serviceRecords);
                        } catch (e) {
                            serviceRecords = [];
                        }
                    }
                    if (Array.isArray(serviceRecords)) {
                        serviceRecords.forEach(record => {
                            addRvServiceRecordRow();
                            const rows = document.querySelectorAll('#rv_service_records_tbody tr');
                            const lastRow = rows[rows.length - 1];
                            // Only set the date if it's not already set to current date
                            if (!lastRow.cells[0].querySelector('input').value) {
                                lastRow.cells[0].querySelector('input').value = record.date || '';
                            }
                            lastRow.cells[1].querySelector('input').value = record.problem || '';
                            lastRow.cells[2].querySelector('input').value = record.action || '';
                            lastRow.cells[3].querySelector('input').value = record.remarks || '';
                        });
                    }
                }

                // Calculate incomes after populating data
                setTimeout(calculateIncomes, 100);
            }

            // Signature modal variables
            let signaturePad = null;
            let currentSignatureType = '';

            // Signature modal functions
            function openSignatureModal(type) {
                currentSignatureType = type;
                document.getElementById('signatureModalTitle').textContent = type.charAt(0).toUpperCase() + type.slice(1) + ' Signature';
                document.getElementById('signatureModal').classList.remove('hidden');
                document.body.classList.add('modal-open');

                // Initialize signature pad
                const canvas = document.getElementById('signatureCanvas');
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 1,
                    maxWidth: 3
                });

                // Resize canvas for proper rendering
                resizeCanvas(canvas);
            }

            function closeSignatureModal() {
                document.getElementById('signatureModal').classList.add('hidden');
                document.body.classList.remove('modal-open');
                if (signaturePad) {
                    signaturePad.clear();
                }
            }

            function clearSignature() {
                if (signaturePad) {
                    signaturePad.clear();
                }
            }

            function saveSignature() {
                if (!signaturePad) return;

                if (signaturePad.isEmpty()) {
                    Swal.fire({
                        title: 'No Signature',
                        text: 'Please provide a signature before saving.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const signatureData = signaturePad.toDataURL();
                document.getElementById('signature_' + currentSignatureType).value = signatureData;

                // Update button text to show signature is saved
                const button = document.querySelector(`button[onclick="openSignatureModal('${currentSignatureType}')"]`);
                if (button) {
                    button.textContent = 'Signature Saved ✓';
                    button.classList.remove('bg-gray-100', 'hover:bg-gray-200');
                    button.classList.add('bg-green-100', 'hover:bg-green-200', 'text-green-800');
                }

                closeSignatureModal();

                Swal.fire({
                    title: 'Signature Saved',
                    text: 'The signature has been saved successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }

            function resizeCanvas(canvas) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                if (signaturePad) {
                    signaturePad.clear();
                }
            }

            // Logout function
            function confirmLogout() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You will be logged out of the system.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, logout!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logoutForm').submit();
                    }
                });
            }
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
    </div>
</body>
</html>