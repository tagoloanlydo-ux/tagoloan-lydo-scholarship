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
        
        /* Tab Styles */
        .tab-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }
        
        .tab-header {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
        }
        
        .tab-button {
            padding: 1rem 1.5rem;
            border: none;
            background: transparent;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 2px solid transparent;
        }
        
        .tab-button.active {
            color: #7c3aed;
            border-bottom-color: #7c3aed;
            background: white;
        }
        
        .tab-button:hover:not(.active) {
            background: #f1f5f9;
            color: #4b5563;
        }
        
        .tab-content {
            display: none;
            padding: 1.5rem;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .note-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .note-box h4 {
            color: #0369a1;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .note-box p {
            color: #0c4a6e;
            font-size: 0.875rem;
        }

        /* Center the pagination container */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            padding: 1rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            max-width: fit-content;
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

        /* A4 Portrait Modal Styles */
        .modal-content {
            position: relative;
            background: white;
            margin: 1% auto;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            width: 210mm; /* A4 width */
            min-height: 297mm; /* A4 height */
            max-height: 95vh;
            overflow-y: auto;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal-content.scaled {
            transform: scale(1);
        }

        .modal-header {
            background: linear-gradient(135deg, #4c1d95 0%, #7e22ce 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px 8px 0 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        /* Review Columns for A4 */
        .review-columns {
            padding: 2rem;
            max-height: calc(297mm - 120px);
            overflow-y: auto;
        }

        /* Improved Section Styles */
        .intake-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            page-break-inside: avoid;
        }

        .intake-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #7c3aed;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 1rem;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-radius: 8px 8px 0 0;
        }

        /* Improved Table Styles */
        .intake-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.875rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .intake-table th {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            font-weight: 600;
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        .intake-table td {
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            background: white;
        }

        .intake-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .intake-table tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Document Section Improvements */
        .bg-white.rounded-lg.shadow-lg.mb-6 {
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        .bg-purple-600 {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%) !important;
        }

        /* Document Cards Improvements */
        .document-status-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .document-status-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .document-status-card .bg-gray-50 {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .document-preview {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-top: 1rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Status Badges */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .status-green {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-red {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .status-gray {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        /* Button Improvements */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }

        /* Responsive Design for A4 Modal */
        @media (max-width: 1200px) {
            .modal-content {
                width: 90%;
                min-height: auto;
                max-height: 90vh;
                margin: 2% auto;
            }
        }

        @media (max-width: 992px) {
            .modal-content {
                width: 95%;
                margin: 2% auto;
            }
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 98%;
                margin: 1% auto;
                transform: none;
            }
            
            .review-columns {
                padding: 1rem;
            }
            
            .intake-section {
                padding: 1rem;
            }
            
            .intake-table {
                font-size: 0.75rem;
            }
            
            .intake-table th,
            .intake-table td {
                padding: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                width: 100%;
                margin: 0;
                border-radius: 0;
                max-height: 100vh;
            }
            
            .review-columns {
                padding: 0.5rem;
            }
            
            .intake-section {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }
            
            .intake-table {
                font-size: 0.7rem;
            }
        }

        /* Print Styles for A4 */
        @media print {
            .modal-content {
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                transform: none !important;
            }
            
            .modal-header {
                position: static !important;
            }
            
            .btn {
                display: none !important;
            }
            
            .review-columns {
                max-height: none !important;
                overflow: visible !important;
            }
        }

        /* Smooth scrolling */
        .review-columns {
            scroll-behavior: smooth;
        }

        /* Loading state for iframes */
        .document-preview iframe {
            background: #f8fafc;
            transition: opacity 0.3s ease;
        }

        .document-preview iframe[src] {
            background: white;
        }

        /* Additional width adjustments for different screen sizes */
        @media (min-width: 1400px) {
            .modal-content {
                width: 210mm;
                margin: 1% auto;
            }
        }

        @media (min-width: 1201px) and (max-width: 1399px) {
            .modal-content {
                width: 85%;
                margin: 1.5% auto;
            }
        }

        /* Document Preview Styles */
        .document-preview-container {
            position: relative;
            background: #f8fafc;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .document-preview-container iframe {
            transition: opacity 0.3s ease;
            background: white;
            min-height: 300px;
        }

        .document-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6b7280;
            font-size: 0.875rem;
            z-index: 1;
        }

        .document-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
        }

        /* Document Grid Layout */
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .document-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .document-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .document-card-header {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .document-card-body {
            padding: 1.5rem;
        }

        .document-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
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
            <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg">
            <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Admin</span>        
        </div>
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
                        <button onclick="toggleDropdown('staffMenu')" class="w-full flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white focus:outline-none">
                            <div class="flex items-center">
                                <i class="bx bxs-user-detail text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Staff</span>
                            </div>
                            <i class="bx bx-chevron-down ml-2"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <ul id="staffMenu" class="ml-10 mt-2 space-y-2 hidden">
                            <li>
                                <a href="/lydo_admin/lydo" class="flex items-center p-2 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
                                    <i class="bx bx-user mr-2"></i> LYDO Staff
                                </a>
                            </li>
                            <li>
                                <a href="/lydo_admin/mayor" class="flex items-center p-2 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
                                    <i class="bx bx-building-house mr-2"></i> Mayor Staff
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="text-blue-600 bg-blue-50">
                        <a href="/lydo_admin/applicants" class="flex items-center justify-between p-3 rounded-lg text-white-700 bg-violet-600 text-white">
                            <div class="flex items-center">
                                <i class="bx bxs-user text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Applicants</span>
                            </div>
                        </a>
                    </li>

                    <!-- Scholar Dropdown -->
                    <li class="relative">
                        <button onclick="toggleDropdown('scholarMenu')" class="w-full flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white focus:outline-none">
                            <div class="flex items-center">
                                <i class="bx bxs-graduation text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Scholar</span>
                            </div>
                            <i class="bx bx-chevron-down ml-2"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <ul id="scholarMenu" class="ml-10 mt-2 space-y-2 hidden">
                            <li>
                                <a href="/lydo_admin/scholar" class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
                                    <i class="bx bx-list-ul mr-2"></i> List of Scholars
                                </a>
                            </li>
                            <li>
                                <a href="/lydo_admin/status" class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
                                    <i class="bx bx-check-circle mr-2"></i> Status
                                </a>
                            </li>
                            <li>
                                <a href="/lydo_admin/disbursement" class="flex items-center p-2 rounded-lg text-black-700 hover:bg-violet-600 hover:text-white">
                                    <i class="bx bx-wallet mr-2"></i> Disbursement
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="/lydo_admin/announcement" class="flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
                            <div class="flex items-center">
                                <i class="bx bxs-megaphone text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Announcement</span>
                            </div>
                        </a>
                    </li>
                </ul>

                <ul class="side-menu space-y-1">
                    <li>
                        <a href="/lydo_admin/settings" class="flex items-center p-3 rounded-lg text-black-600 hover:bg-violet-600 hover:text-white">
                            <i class="bx bxs-cog text-center mx-auto md:mx-0 text-xl"></i>
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

        <!-- Main Content Area -->
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

            <!-- Tab Container -->
            <div class="tab-container">
                <div class="tab-header">
                    <button class="tab-button active" data-tab="mayor-applicants">Mayor Staff Applicants</button>
                    <button class="tab-button" data-tab="lydo-reviewed">LYDO Reviewed Applicants</button>
                </div>

                <!-- Mayor Staff Applicants Tab -->
                <div class="tab-content active" id="mayor-applicants">
                    <div class="note-box">
                        <p>This tab displays applicants who have been processed by Mayor Staff. You can view applications that are either <strong>Approved</strong> or <strong>Rejected</strong> by the Mayor's office. Use this section to review decisions made by Mayor Staff and communicate with applicants about their status.</p>
                        <p class="mt-2 text-amber-600 font-medium">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Note:</strong> Approved applicants will no longer appear in this list once they have been reviewed by LYDO Staff. They will be moved to the "LYDO Reviewed Applicants" tab.
                        </p>
                    </div>

                    <!-- Filter Section for Mayor Staff Applicants -->
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <form id="filterFormMayor" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                                <input type="text" id="searchInputMayor" name="search" 
                                       placeholder="Enter name..."
                                       class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                            </div>

                            <!-- Barangay -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                                <select id="barangaySelectMayor" name="barangay" 
                                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    <option value="">All Barangays</option>
                                    @foreach($barangays as $barangay)
                                        <option value="{{ $barangay }}">{{ $barangay }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Year -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Academic Year</label>
                                <select id="academicYearSelectMayor" name="academic_year" 
                                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    <option value="">All Academic Years</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Initial Screening Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                                <select id="initialScreeningSelectMayor" name="initial_screening" 
                                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    <option value="all">All Applicants</option>
                                    <option value="Approved">Approved by Mayor</option>
                                    <option value="Rejected">Rejected Applications</option>
                                </select>
                            </div>

                            <!-- Print Button -->
                            <div class="flex items-end">
                                <button type="button" id="printPdfBtnMayor" 
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium">
                                    <i class="fas fa-file-pdf"></i> Print PDF
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Applicants Table for Mayor Staff -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Mayor Staff Applicants List</h3>
                            <div class="flex space-x-2">
                                <button id="copyNamesBtnMayor" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                    Copy Names
                                </button>
                                <button id="emailSelectedBtnMayor" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                    Email
                                </button>
                                <button id="smsSelectedBtnMayor" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                    SMS
                                </button>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                                <thead class="bg-violet-600 to-teal-600 text-white uppercase text-sm">
                                    <tr>
                                        <th class="px-4 py-3 border border-gray-200 text-left">
                                            <input type="checkbox" id="selectAllMayor" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Full Name</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Barangay</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Email</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Phone Number</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">School</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Academic Year</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Application History</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Initial Screening</th>
                                    </tr>
                                </thead>
                                <tbody id="mayorApplicantsTable">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination for Mayor Staff Applicants -->
                        <div class="px-6 py-4 bg-white border-t border-gray-200">
                            <div class="flex justify-center">
                                <div class="pagination-container">
                                    <div class="pagination-info" id="paginationInfoMayor">
                                        Showing page 1 of 1
                                    </div>
                                    <div class="pagination-buttons">
                                        <button class="pagination-btn" id="prevPageMayor" disabled>
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <div class="pagination-page-info">
                                            Page 
                                            <input type="number" class="pagination-page-input" id="currentPageMayor" value="1" min="1">
                                            of <span id="totalPagesMayor">1</span>
                                        </div>
                                        <button class="pagination-btn" id="nextPageMayor">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LYDO Reviewed Applicants Tab -->
                <div class="tab-content" id="lydo-reviewed">
                    <div class="note-box">
                        <p>This tab displays applicants who have been <strong>reviewed by LYDO Staff</strong>. These applications have undergone final evaluation and have been categorized based on economic status (Poor, Non-Poor, Ultra Poor). Use this section to manage finalized applications and communicate with reviewed applicants.</p>
                    </div>

                    <!-- Filter Section for LYDO Reviewed Applicants -->
                    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                        <form id="filterFormLydo" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                                <input type="text" id="searchInputLydo" name="search" 
                                       placeholder="Enter name..."
                                       class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                            </div>

                            <!-- Barangay -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Barangay</label>
                                <select id="barangaySelectLydo" name="barangay" 
                                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    <option value="">All Barangays</option>
                                    @foreach($barangays as $barangay)
                                        <option value="{{ $barangay }}">{{ $barangay }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Year -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Academic Year</label>
                                <select id="academicYearSelectLydo" name="academic_year" 
                                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    <option value="">All Academic Years</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Remarks Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Remarks</label>
                                <select id="remarksSelectLydo" name="remarks" 
                                        class="w-full px-4 py-2 border border-black rounded-lg focus:ring-2 focus:ring-black-500 placeholder-black">
                                    <option value="">All Remarks</option>
                                    <option value="Poor">Poor</option>
                                    <option value="Non Poor">Non-Poor</option>
                                    <option value="Ultra Poor">Ultra Poor</option>
                                </select>
                            </div>

                            <!-- Print Button -->
                            <div class="flex items-end">
                                <button type="button" id="printPdfBtnLydo" 
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-sm font-medium">
                                    <i class="fas fa-file-pdf"></i> Print PDF
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Applicants Table for LYDO Reviewed -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">LYDO Reviewed Applicants List</h3>
                            <div class="flex space-x-2">
                                <button id="copyNamesBtnLydo" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                    Copy Names
                                </button>
                                <button id="emailSelectedBtnLydo" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                    Email
                                </button>
                                <button id="smsSelectedBtnLydo" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed hidden">
                                    SMS
                                </button>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border-collapse text-[17px] shadow-lg border border-gray-200">
                                <thead class="bg-violet-600 to-teal-600 text-white uppercase text-sm">
                                    <tr>
                                        <th class="px-4 py-3 border border-gray-200 text-left">
                                            <input type="checkbox" id="selectAllLydo" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Full Name</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Barangay</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Email</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Phone Number</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">School</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Academic Year</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Application History</th>
                                        <th class="px-4 py-3 border border-gray-200 align-middle text-center">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="lydoApplicantsTable">
                                    <!-- Data will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination for LYDO Reviewed Applicants -->
                        <div class="px-6 py-4 bg-white border-t border-gray-200">
                            <div class="flex justify-center">
                                <div class="pagination-container">
                                    <div class="pagination-info" id="paginationInfoLydo">
                                        Showing page 1 of 1
                                    </div>
                                    <div class="pagination-buttons">
                                        <button class="pagination-btn" id="prevPageLydo" disabled>
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <div class="pagination-page-info">
                                            Page 
                                            <input type="number" class="pagination-page-input" id="currentPageLydo" value="1" min="1">
                                            of <span id="totalPagesLydo">1</span>
                                        </div>
                                        <button class="pagination-btn" id="nextPageLydo">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
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
                    <button id="closeEmailModal" class="text-gray-400 hover:text-gray-600" onclick="closeEmailModal()">
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

            <!-- NOTE: Schedule Type Notification -->
            <div id="scheduleNote" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                    <div>
                        <p class="text-sm text-blue-800 font-medium">
                            <strong>Note:</strong> When selecting "Schedule" type, emails will also be sent automatically 
                            to all selected applicants along with the SMS notifications.
                        </p>
                    </div>
                </div>
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

                <!-- SMS Message (Hidden for Schedule Type) -->
                <div id="smsMessageContainer" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMS Message</label>
                    <textarea id="smsMessage" name="message" rows="4" maxlength="160"
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
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-6 rounded-xl border border-amber-100 w-full mt-6" id="applicantBasicInfo">
                        <!-- Basic info will be populated here -->
                    </div>
                </div>

                <!-- Intake Sheet Information (for Reviewed status) -->
                <div id="intakeSheetInfo" class="hidden flex flex-col space-y-8 w-full">
                    <!-- Head of Family Section -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-6 rounded-xl border border-emerald-100 w-full">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-home text-white"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">Head of Family</h3>
                        </div>
                    <div id="headOfFamilyInfo" style="width: 100%;">
                        <!-- Head of family info will be populated here -->
                    </div>
                </div>

                <!-- Household Information Section -->
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-6 rounded-xl border border-amber-100 w-full mt-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-house-user text-white"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800">Household Information</h3>
                    </div>
                    <div id="householdInfo" style="width: 100%;">
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

            <!-- ADDED: Footer with Print Button -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 bg-white sticky bottom-0 z-10">
                <div class="text-sm text-gray-500">
                    <span id="applicantStatusInfo">LYDO Reviewed Application</span>
                </div>
                <div class="flex space-x-3">
                    <button onclick="printApplicationHistory()" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm font-medium flex items-center">
                        <i class="fas fa-print mr-2"></i> Print Application
                    </button>
                    <button onclick="closeApplicationModal()" 
                            class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-200 shadow-sm font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to current button and content
                    button.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                    
                    // Load data for the active tab
                    if (tabId === 'mayor-applicants') {
                        loadMayorApplicants();
                    } else if (tabId === 'lydo-reviewed') {
                        loadLydoReviewedApplicants();
                    }
                });
            });

            // Initialize first tab
            loadMayorApplicants();

            // Initialize dropdown functionality
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
            document.querySelectorAll("ul[id]").forEach(menu => {
                const state = localStorage.getItem(menu.id);
                if (state === "open") {
                    menu.classList.remove("hidden");
                }
            });

            // Make toggleDropdown global
            window.toggleDropdown = toggleDropdown;

            // Initialize modal close event listeners
            initializeModalEvents();
        });

        // Global variables for pagination and data
        let currentPageMayor = 1;
        let currentPageLydo = 1;
        const itemsPerPage = 15;
        let allMayorApplicants = [];
        let allLydoApplicants = [];
        let filteredMayorApplicants = [];
        let filteredLydoApplicants = [];

        // Load Mayor Staff Applicants
        async function loadMayorApplicants() {
            showLoadingOverlay();
            try {
                const response = await fetch('/lydo_admin/get-mayor-applicants');
                const data = await response.json();
                allMayorApplicants = data.applicants || [];
                // Initialize selection state
                allMayorApplicants.forEach(applicant => {
                    applicant.selected = false;
                });
                filteredMayorApplicants = [...allMayorApplicants];
                currentPageMayor = 1;
                renderMayorApplicantsTable();
                setupMayorPagination();
                setupMayorFilters();
                updateApprovedCount();
            } catch (error) {
                console.error('Error loading mayor applicants:', error);
            } finally {
                hideLoadingOverlay();
            }
        }

        // Load LYDO Reviewed Applicants
        async function loadLydoReviewedApplicants() {
            showLoadingOverlay();
            try {
                const response = await fetch('/lydo_admin/get-lydo-reviewed-applicants');
                const data = await response.json();
                allLydoApplicants = data.applicants || [];
                // Initialize selection state
                allLydoApplicants.forEach(applicant => {
                    applicant.selected = false;
                });
                filteredLydoApplicants = [...allLydoApplicants];
                currentPageLydo = 1;
                renderLydoApplicantsTable();
                setupLydoPagination();
                setupLydoFilters();
            } catch (error) {
                console.error('Error loading LYDO reviewed applicants:', error);
            } finally {
                hideLoadingOverlay();
            }
        }

        // Render Mayor Applicants Table
        function renderMayorApplicantsTable() {
            const tableBody = document.getElementById('mayorApplicantsTable');
            const startIndex = (currentPageMayor - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const currentItems = filteredMayorApplicants.slice(startIndex, endIndex);

            if (currentItems.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-4 py-2 text-center text-sm text-gray-500">
                            No applicants found.
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = currentItems.map(applicant => `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <input type="checkbox" name="selected_applicants" value="${applicant.applicant_id}" 
                               ${applicant.selected ? 'checked' : ''}
                               class="applicant-checkbox-mayor rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm font-medium text-gray-900">
                            ${formatName(applicant)}
                        </div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_brgy || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_email || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_contact_number || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_school_name || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_acad_year || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="flex gap-2 justify-center">
                            <button type="button" 
                                    onclick="viewApplicantDocuments('${applicant.applicant_id}', '${escapeString(formatName(applicant))}', '${applicant.initial_screening}')"
                                    class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow">
                                <i class="fas fa-file-alt mr-1"></i> View Documents
                            </button>
                        </div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            ${applicant.initial_screening === 'Approved' ? 'bg-green-100 text-green-800' : 
                              applicant.initial_screening === 'Rejected' ? 'bg-red-100 text-red-800' : 
                              'bg-yellow-100 text-yellow-800'}">
                            ${applicant.initial_screening === 'Approved' ? 'Approved by Mayor' : applicant.initial_screening || 'Pending'}
                        </span>
                    </td>
                </tr>
            `).join('');

            setupMayorCheckboxes();
            updateSelectAllCheckbox('mayor');
        }

        // Render LYDO Reviewed Applicants Table
        function renderLydoApplicantsTable() {
            const tableBody = document.getElementById('lydoApplicantsTable');
            const startIndex = (currentPageLydo - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const currentItems = filteredLydoApplicants.slice(startIndex, endIndex);

            if (currentItems.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-4 py-2 text-center text-sm text-gray-500">
                            No applicants found.
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = currentItems.map(applicant => `
                <tr class="hover:bg-gray-50 border-b">
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <input type="checkbox" name="selected_applicants" value="${applicant.applicant_id}" 
                               ${applicant.selected ? 'checked' : ''}
                               class="applicant-checkbox-lydo rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm font-medium text-gray-900">
                            ${formatName(applicant)}
                        </div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_brgy || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_email || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_contact_number || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_school_name || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="text-sm text-gray-900">${applicant.applicant_acad_year || 'N/A'}</div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <div class="flex gap-2 justify-center">
                            <button type="button" 
                                    onclick="viewApplicantIntakeSheet('${applicant.applicant_id}', '${escapeString(formatName(applicant))}', '${applicant.initial_screening}')"
                                    class="px-3 py-1 text-sm bg-purple-500 hover:bg-purple-600 text-white rounded-lg shadow">
                                <i class="fas fa-clipboard-list mr-1"></i> View Application
                            </button>
                        </div>
                    </td>
                    <td class="px-4 border border-gray-200 py-2 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            ${applicant.remarks === 'Poor' ? 'bg-orange-100 text-orange-800' : 
                              applicant.remarks === 'Non-Poor' ? 'bg-green-100 text-green-800' : 
                              applicant.remarks === 'Ultra Poor' ? 'bg-red-100 text-red-800' : 
                              'bg-gray-100 text-gray-800'}">
                            ${applicant.remarks || 'Not Specified'}
                        </span>
                    </td>
                </tr>
            `).join('');

            setupLydoCheckboxes();
            updateSelectAllCheckbox('lydo');
        }

        // Filter setup for Mayor Applicants
        function setupMayorFilters() {
            const searchInput = document.getElementById('searchInputMayor');
            const barangaySelect = document.getElementById('barangaySelectMayor');
            const academicYearSelect = document.getElementById('academicYearSelectMayor');
            const initialScreeningSelect = document.getElementById('initialScreeningSelectMayor');

            const applyFilters = () => {
                const searchTerm = searchInput.value.toLowerCase();
                const barangayFilter = barangaySelect.value;
                const academicYearFilter = academicYearSelect.value;
                const screeningFilter = initialScreeningSelect.value;

                filteredMayorApplicants = allMayorApplicants.filter(applicant => {
                    const matchesSearch = !searchTerm || 
                        formatName(applicant).toLowerCase().includes(searchTerm) ||
                        (applicant.applicant_email && applicant.applicant_email.toLowerCase().includes(searchTerm)) ||
                        (applicant.applicant_school_name && applicant.applicant_school_name.toLowerCase().includes(searchTerm));
                    
                    const matchesBarangay = !barangayFilter || applicant.applicant_brgy === barangayFilter;
                    const matchesAcademicYear = !academicYearFilter || applicant.applicant_acad_year === academicYearFilter;
                    
                    let matchesScreening = true;
                    if (screeningFilter === 'Approved') {
                        matchesScreening = applicant.initial_screening === 'Approved';
                    } else if (screeningFilter === 'Rejected') {
                        matchesScreening = applicant.initial_screening === 'Rejected';
                    } else if (screeningFilter === 'Pending') {
                        matchesScreening = !applicant.initial_screening || applicant.initial_screening === 'Pending';
                    }

                    return matchesSearch && matchesBarangay && matchesAcademicYear && matchesScreening;
                });

                currentPageMayor = 1;
                renderMayorApplicantsTable();
                setupMayorPagination();
            };

            searchInput.addEventListener('input', debounce(applyFilters, 300));
            barangaySelect.addEventListener('change', applyFilters);
            academicYearSelect.addEventListener('change', applyFilters);
            initialScreeningSelect.addEventListener('change', applyFilters);
        }

        // Filter setup for LYDO Reviewed Applicants
        function setupLydoFilters() {
            const searchInput = document.getElementById('searchInputLydo');
            const barangaySelect = document.getElementById('barangaySelectLydo');
            const academicYearSelect = document.getElementById('academicYearSelectLydo');
            const remarksSelect = document.getElementById('remarksSelectLydo');

            const applyFilters = () => {
                const searchTerm = searchInput.value.toLowerCase();
                const barangayFilter = barangaySelect.value;
                const academicYearFilter = academicYearSelect.value;
                const remarksFilter = remarksSelect.value;

                filteredLydoApplicants = allLydoApplicants.filter(applicant => {
                    const matchesSearch = !searchTerm || 
                        formatName(applicant).toLowerCase().includes(searchTerm) ||
                        (applicant.applicant_email && applicant.applicant_email.toLowerCase().includes(searchTerm)) ||
                        (applicant.applicant_school_name && applicant.applicant_school_name.toLowerCase().includes(searchTerm));
                    
                    const matchesBarangay = !barangayFilter || applicant.applicant_brgy === barangayFilter;
                    const matchesAcademicYear = !academicYearFilter || applicant.applicant_acad_year === academicYearFilter;
                    const matchesRemarks = !remarksFilter || applicant.remarks === remarksFilter;

                    return matchesSearch && matchesBarangay && matchesAcademicYear && matchesRemarks;
                });

                currentPageLydo = 1;
                renderLydoApplicantsTable();
                setupLydoPagination();
            };

            searchInput.addEventListener('input', debounce(applyFilters, 300));
            barangaySelect.addEventListener('change', applyFilters);
            academicYearSelect.addEventListener('change', applyFilters);
            remarksSelect.addEventListener('change', applyFilters);
        }

        // Pagination setup for Mayor Applicants
        function setupMayorPagination() {
            const totalPages = Math.ceil(filteredMayorApplicants.length / itemsPerPage);
            document.getElementById('totalPagesMayor').textContent = totalPages;
            document.getElementById('paginationInfoMayor').textContent = `Showing page ${currentPageMayor} of ${totalPages}`;
            
            document.getElementById('prevPageMayor').disabled = currentPageMayor === 1;
            document.getElementById('nextPageMayor').disabled = currentPageMayor === totalPages || totalPages === 0;

            document.getElementById('prevPageMayor').onclick = () => {
                if (currentPageMayor > 1) {
                    currentPageMayor--;
                    renderMayorApplicantsTable();
                    setupMayorPagination();
                }
            };

            document.getElementById('nextPageMayor').onclick = () => {
                if (currentPageMayor < totalPages) {
                    currentPageMayor++;
                    renderMayorApplicantsTable();
                    setupMayorPagination();
                }
            };

            document.getElementById('currentPageMayor').onchange = (e) => {
                const page = parseInt(e.target.value);
                if (page >= 1 && page <= totalPages) {
                    currentPageMayor = page;
                    renderMayorApplicantsTable();
                    setupMayorPagination();
                } else {
                    e.target.value = currentPageMayor;
                }
            };
        }

        // Pagination setup for LYDO Reviewed Applicants
        function setupLydoPagination() {
            const totalPages = Math.ceil(filteredLydoApplicants.length / itemsPerPage);
            document.getElementById('totalPagesLydo').textContent = totalPages;
            document.getElementById('paginationInfoLydo').textContent = `Showing page ${currentPageLydo} of ${totalPages}`;
            
            document.getElementById('prevPageLydo').disabled = currentPageLydo === 1;
            document.getElementById('nextPageLydo').disabled = currentPageLydo === totalPages || totalPages === 0;

            document.getElementById('prevPageLydo').onclick = () => {
                if (currentPageLydo > 1) {
                    currentPageLydo--;
                    renderLydoApplicantsTable();
                    setupLydoPagination();
                }
            };

            document.getElementById('nextPageLydo').onclick = () => {
                if (currentPageLydo < totalPages) {
                    currentPageLydo++;
                    renderLydoApplicantsTable();
                    setupLydoPagination();
                }
            };

            document.getElementById('currentPageLydo').onchange = (e) => {
                const page = parseInt(e.target.value);
                if (page >= 1 && page <= totalPages) {
                    currentPageLydo = page;
                    renderLydoApplicantsTable();
                    setupLydoPagination();
                } else {
                    e.target.value = currentPageLydo;
                }
            };
        }

        // Checkbox setup functions
        function setupMayorCheckboxes() {
            const selectAll = document.getElementById('selectAllMayor');
            const checkboxes = document.querySelectorAll('.applicant-checkbox-mayor');
            const copyBtn = document.getElementById('copyNamesBtnMayor');
            const emailBtn = document.getElementById('emailSelectedBtnMayor');
            const smsBtn = document.getElementById('smsSelectedBtnMayor');

            selectAll.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                
                if (isChecked) {
                    filteredMayorApplicants.forEach(applicant => {
                        applicant.selected = true;
                    });
                } else {
                    filteredMayorApplicants.forEach(applicant => {
                        applicant.selected = false;
                    });
                }
                
                updateButtonVisibility('mayor');
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const applicantId = checkbox.value;
                    const applicant = filteredMayorApplicants.find(app => app.applicant_id == applicantId);
                    if (applicant) {
                        applicant.selected = checkbox.checked;
                    }
                    updateButtonVisibility('mayor');
                    updateSelectAllCheckbox('mayor');
                });
            });

            copyBtn.addEventListener('click', () => copySelectedNames('mayor'));
            emailBtn.addEventListener('click', () => openEmailModal('mayor'));
            smsBtn.addEventListener('click', () => openSmsModal('mayor'));
        }

        function setupLydoCheckboxes() {
            const selectAll = document.getElementById('selectAllLydo');
            const checkboxes = document.querySelectorAll('.applicant-checkbox-lydo');
            const copyBtn = document.getElementById('copyNamesBtnLydo');
            const emailBtn = document.getElementById('emailSelectedBtnLydo');
            const smsBtn = document.getElementById('smsSelectedBtnLydo');

            selectAll.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                
                if (isChecked) {
                    filteredLydoApplicants.forEach(applicant => {
                        applicant.selected = true;
                    });
                } else {
                    filteredLydoApplicants.forEach(applicant => {
                        applicant.selected = false;
                    });
                }
                
                updateButtonVisibility('lydo');
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const applicantId = checkbox.value;
                    const applicant = filteredLydoApplicants.find(app => app.applicant_id == applicantId);
                    if (applicant) {
                        applicant.selected = checkbox.checked;
                    }
                    updateButtonVisibility('lydo');
                    updateSelectAllCheckbox('lydo');
                });
            });

            copyBtn.addEventListener('click', () => copySelectedNames('lydo'));
            emailBtn.addEventListener('click', () => openEmailModal('lydo'));
            smsBtn.addEventListener('click', () => openSmsModal('lydo'));
        }

        // Update Select All checkbox state
        function updateSelectAllCheckbox(tab) {
            const selectAll = document.getElementById(`selectAll${tab.charAt(0).toUpperCase() + tab.slice(1)}`);
            const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
            
            if (filteredApplicants.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
                return;
            }
            
            const selectedCount = filteredApplicants.filter(applicant => applicant.selected).length;
            
            if (selectedCount === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (selectedCount === filteredApplicants.length) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            }
        }

        // Button visibility function
        function updateButtonVisibility(tab) {
            const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
            const hasSelection = filteredApplicants.some(applicant => applicant.selected);
            
            const copyBtn = document.getElementById(`copyNamesBtn${tab.charAt(0).toUpperCase() + tab.slice(1)}`);
            const emailBtn = document.getElementById(`emailSelectedBtn${tab.charAt(0).toUpperCase() + tab.slice(1)}`);
            const smsBtn = document.getElementById(`smsSelectedBtn${tab.charAt(0).toUpperCase() + tab.slice(1)}`);

            [copyBtn, emailBtn, smsBtn].forEach(btn => {
                if (btn) {
                    btn.classList.toggle('hidden', !hasSelection);
                    btn.disabled = !hasSelection;
                }
            });
        }

        // Copy names function
        function copySelectedNames(tab) {
            const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
            const selectedApplicants = filteredApplicants.filter(applicant => applicant.selected);
            
            if (selectedApplicants.length === 0) {
                Swal.fire('Error', 'No applicants selected', 'error');
                return;
            }

            const names = selectedApplicants.map(applicant => formatName(applicant));

            if (names.length > 0) {
                navigator.clipboard.writeText(names.join(', '))
                    .then(() => {
                        Swal.fire('Success', `${names.length} names copied to clipboard!`, 'success');
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Failed to copy names', 'error');
                    });
            }
        }

        // Email modal
        function openEmailModal(tab) {
            const modal = document.getElementById('emailModal');
            const preview = document.getElementById('recipientsPreview');

            const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
            const selectedApplicants = filteredApplicants.filter(applicant => applicant.selected);
            
            if (selectedApplicants.length === 0) {
                preview.textContent = 'No recipients selected';
            } else {
                const items = selectedApplicants.map(applicant => {
                    const name = formatName(applicant);
                    const email = applicant.applicant_email || 'N/A';
                    return `<div class="mb-1"><strong>${escapeHtml(name)}</strong> — ${escapeHtml(email)}</div>`;
                }).join('');
                preview.innerHTML = `<div class="mb-2 text-sm font-semibold">Selected: ${selectedApplicants.length} applicants</div>${items}`;
            }

            modal.classList.remove('hidden');
        }

        // SMS modal
        function openSmsModal(tab) {
            const modal = document.getElementById('smsModal');
            const preview = document.getElementById('smsRecipientsPreview');

            const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
            const selectedApplicants = filteredApplicants.filter(applicant => applicant.selected);
            
            if (selectedApplicants.length === 0) {
                preview.textContent = 'No recipients selected';
            } else {
                const items = selectedApplicants.map(applicant => {
                    const name = formatName(applicant);
                    const phone = applicant.applicant_contact_number || 'N/A';
                    return `<div class="mb-1"><strong>${escapeHtml(name)}</strong> — ${escapeHtml(phone)}</div>`;
                }).join('');
                preview.innerHTML = `<div class="mb-2 text-sm font-semibold">Selected: ${selectedApplicants.length} applicants</div>${items}`;
            }

            modal.classList.remove('hidden');
        }

        // Helper functions
        function formatName(applicant) {
            let name = '';
            if (applicant.applicant_lname) {
                name += applicant.applicant_lname.charAt(0).toUpperCase() + applicant.applicant_lname.slice(1).toLowerCase();
            }
            if (applicant.applicant_suffix) {
                name += ' ' + applicant.applicant_suffix;
            }
            if (applicant.applicant_fname) {
                name += (name ? ', ' : '') + applicant.applicant_fname.charAt(0).toUpperCase() + applicant.applicant_fname.slice(1).toLowerCase();
            }
            if (applicant.applicant_mname) {
                name += ' ' + applicant.applicant_mname.charAt(0).toUpperCase() + '.';

                            }
            return name;
        }

        function escapeString(str) {
            return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

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

        function updateApprovedCount() {
            const approvedCount = allMayorApplicants.filter(applicant => 
                applicant.initial_screening === 'Approved'
            ).length;
            
            const approvedTabButton = document.querySelector('[data-tab="mayor-applicants"]');
            if (approvedTabButton) {
                const existingBadge = approvedTabButton.querySelector('.approved-badge');
                if (existingBadge) {
                    existingBadge.remove();
                }
                

            }
        }

        // Print Application History function
        function printApplicationHistory() {
            // Get the currently viewed applicant from the modal
            const modalTitle = document.getElementById('modalTitle').textContent;
            let applicantName = '';
            let applicantId = null;

            // Extract applicant name from modal title
            if (modalTitle.includes('Application Details - ')) {
                applicantName = modalTitle.replace('Application Details - ', '');
            } else if (modalTitle.includes('Documents - ')) {
                applicantName = modalTitle.replace('Documents - ', '');
            }

            // Find the applicant ID from the current data
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            
            if (activeTab === 'lydo-reviewed') {
                // Search for the applicant in the LYDO reviewed list
                const applicant = allLydoApplicants.find(app => 
                    formatName(app) === applicantName
                );
                
                if (applicant) {
                    applicantId = applicant.applicant_id;
                }
            } else if (activeTab === 'mayor-applicants') {
                // Search for the applicant in the Mayor applicants list
                const applicant = allMayorApplicants.find(app => 
                    formatName(app) === applicantName
                );
                
                if (applicant) {
                    applicantId = applicant.applicant_id;
                }
            }

            if (!applicantId) {
                Swal.fire('Error', 'Could not find applicant data. Please try reopening the application details.', 'error');
                return;
            }

            showLoadingOverlay();
            
            console.log('Printing application for:', applicantName, 'ID:', applicantId);

            // Get the application personnel ID first
            fetch(`/lydo_admin/get-application-personnel/${applicantId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Open the print view in a new window
                        const printUrl = `/lydo_admin/print-application-history/${data.application_personnel_id}`;
                        console.log('Opening print URL:', printUrl);
                        window.open(printUrl, '_blank');
                        hideLoadingOverlay();
                    } else {
                        throw new Error(data.message || 'Failed to get application data');
                    }
                })
                .catch(error => {
                    hideLoadingOverlay();
                    console.error('Error:', error);
                    Swal.fire('Error', 'Failed to load application for printing: ' + error.message, 'error');
                });
        }

        // FIXED: Modal functions for View Documents and View Application
        function viewApplicantDocuments(applicantId, applicantName, status) {
            showLoadingOverlay();
            console.log('Loading documents for applicant:', applicantId);
            
            fetch(`/lydo_admin/applicant-documents/${applicantId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoadingOverlay();
                    console.log('Documents response:', data);
                    if (data.success) {
                        openDocumentsModal(applicantName, data.documents);
                    } else {
                        Swal.fire('Error', data.message || 'Failed to load documents', 'error');
                    }
                })
                .catch(error => {
                    hideLoadingOverlay();
                    console.error('Error loading documents:', error);
                    Swal.fire('Error', 'Failed to load documents: ' + error.message, 'error');
                });
        }

        function viewApplicantIntakeSheet(applicantId, applicantName, status) {
            showLoadingOverlay();
            console.log('Loading intake sheet for applicant:', applicantId);
            
            fetch(`/lydo_admin/get-application-personnel/${applicantId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Application personnel response:', data);
                    if (data.success) {
                        return fetch(`/lydo_admin/intake-sheet/${data.application_personnel_id}`);
                    } else {
                        throw new Error(data.message || 'Failed to get application personnel ID');
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoadingOverlay();
                    console.log('Intake sheet response:', data);
                    if (data.success) {
                        openIntakeSheetModal(applicantName, data.intakeSheet);
                    } else {
                        Swal.fire('Error', data.message || 'Failed to load intake sheet', 'error');
                    }
                })
                .catch(error => {
                    hideLoadingOverlay();
                    console.error('Error loading intake sheet:', error);
                    Swal.fire('Error', 'Failed to load application details: ' + error.message, 'error');
                });
        }

function openIntakeSheetModal(applicantName, data) {
    const modal = document.getElementById('applicationHistoryModal');
    const modalTitle = document.getElementById('modalTitle');
    const applicantBasicInfo = document.getElementById('applicantBasicInfo');
    const intakeSheetInfo = document.getElementById('intakeSheetInfo');
    const documentsContainer = document.getElementById('documentsContainer');

    // Set modal content - INTAKE SHEET + DOCUMENTS
    modalTitle.textContent = `Application Details - ${applicantName}`;
    intakeSheetInfo.classList.remove('hidden'); // Show intake sheet section

    // SAFE ACCESS: Check if intakeSheet exists
    const intakeSheet = data.intakeSheet || data || {};
    const applicantGender = intakeSheet.applicant_gender || 'N/A';
    const remarks = intakeSheet.remarks || 'Not Specified';

    // Set basic info - use safe data access
    applicantBasicInfo.innerHTML = `
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <h4 class="font-semibold text-gray-800 mb-2">Applicant: ${applicantName}</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Gender:</span>
                    <span class="ml-2">${applicantGender}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Remarks:</span>
                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold 
                        ${remarks === 'Poor' ? 'bg-orange-100 text-orange-800' : 
                          remarks === 'Non-Poor' ? 'bg-green-100 text-green-800' : 
                          remarks === 'Ultra Poor' ? 'bg-red-100 text-red-800' : 
                          'bg-gray-100 text-gray-800'}">
                        ${remarks}
                    </span>
                </div>
            </div>
        </div>
    `;

    // Pass the ENTIRE data object to populateIntakeSheetData with safe access
    populateIntakeSheetData(data);

    // SET DOCUMENTS SECTION USING THE SAME STYLE AS OPEN DOCUMENTS MODAL
    let documentsHTML = `
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Supporting Documents</h3>
            <div class="documents-grid">
    `;
    
    // Define document types with icons and labels - SAME AS IN OPEN DOCUMENTS MODAL
    const documentTypes = [
        {
            key: 'doc_application_letter',
            label: 'Application Letter',
            icon: 'fas fa-file-alt'
        },
        {
            key: 'doc_cert_reg', 
            label: 'Certificate of Registration',
            icon: 'fas fa-certificate'
        },
        {
            key: 'doc_grade_slip',
            label: 'Grade Slip',
            icon: 'fas fa-chart-line'
        },
        {
            key: 'doc_brgy_indigency',
            label: 'Barangay Indigency',
            icon: 'fas fa-home'
        },
        {
            key: 'doc_student_id',
            label: 'Student ID',
            icon: 'fas fa-id-card'
        }
    ];

    // Create document cards for each type - SAME FORMAT AS OPEN DOCUMENTS MODAL
    documentTypes.forEach(docType => {
        const documentUrl = intakeSheet[docType.key]; // Use the safe intakeSheet object
        
        documentsHTML += `
            <div class="document-card">
                <div class="document-card-header">
                    <i class="${docType.icon} text-white"></i>
                    <h4 class="font-semibold">${docType.label}</h4>
                </div>
                <div class="document-card-body">
                    ${documentUrl ? `
                        <div class="document-preview-container">
                            <div class="document-loading" id="loading-${docType.key}">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading document...
                            </div>
                            <iframe 
                                src="${documentUrl}" 
                                class="w-full h-64"  <!-- SAME HEIGHT AS DOCUMENTS MODAL -->
                                frameborder="0"
                                loading="lazy"
                                onload="document.getElementById('loading-${docType.key}').style.display = 'none'"
                                onerror="document.getElementById('loading-${docType.key}').innerHTML = '<i class=\\'fas fa-exclamation-triangle mr-2\\'></i>Failed to load document'">
                            </iframe>
                        </div>
                        <div class="document-actions">
                            <a href="${documentUrl}" 
                               target="_blank" 
                               class="btn-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-external-link-alt mr-1"></i> Open
                            </a>
                            <a href="${documentUrl}" 
                               download 
                               class="btn-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    ` : `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-file-exclamation text-3xl mb-3 text-gray-400"></i>
                            <p class="font-medium">No ${docType.label} available</p>
                            <p class="text-sm mt-1">This document has not been uploaded by the applicant.</p>
                        </div>
                    `}
                </div>
            </div>
        `;
    });
    
    documentsHTML += '</div></div>';
    documentsContainer.innerHTML = documentsHTML;

    // Update status info
    document.getElementById('applicantStatusInfo').textContent = 'LYDO Reviewed Application - Complete Intake Sheet';

    // SHOW PRINT BUTTON FOR REVIEW APPLICATION
    const printButton = modal.querySelector('button[onclick="printApplicationHistory()"]');
    if (printButton) {
        printButton.style.display = 'flex';
    }

    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        const modalContent = document.getElementById('modalContent');
        if (modalContent) {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scaled');
        }
    }, 50);
}
function openDocumentsModal(applicantName, documents) {
    const modal = document.getElementById('applicationHistoryModal');
    const modalTitle = document.getElementById('modalTitle');
    const applicantBasicInfo = document.getElementById('applicantBasicInfo');
    const documentsContainer = document.getElementById('documentsContainer');
    const intakeSheetInfo = document.getElementById('intakeSheetInfo');

    // Set modal content - DOCUMENTS ONLY
    modalTitle.textContent = `Documents - ${applicantName}`;
    intakeSheetInfo.classList.add('hidden'); // Hide intake sheet section

    // Set basic info
    applicantBasicInfo.innerHTML = `
        <div class="bg-white p-4 rounded-lg border border-gray-200 w-full">
            <h4 class="font-semibold text-gray-800 mb-2">Applicant: ${applicantName}</h4>
            <p class="text-sm text-gray-600">View supporting documents submitted by the applicant.</p>
        </div>
    `;

    // Set documents - ALL 5 DOCUMENTS WITH PREVIEW
    let documentsHTML = '<div class="documents-grid">';
    
    // Define document types with icons and labels
    const documentTypes = [
        {
            key: 'doc_application_letter',
            label: 'Application Letter',
            icon: 'fas fa-file-alt'
        },
        {
            key: 'doc_cert_reg', 
            label: 'Certificate of Registration',
            icon: 'fas fa-certificate'
        },
        {
            key: 'doc_grade_slip',
            label: 'Grade Slip',
            icon: 'fas fa-chart-line'
        },
        {
            key: 'doc_brgy_indigency',
            label: 'Barangay Indigency',
            icon: 'fas fa-home'
        },
        {
            key: 'doc_student_id',
            label: 'Student ID',
            icon: 'fas fa-id-card'
        }
    ];

    // Create document cards for each type
    documentTypes.forEach(docType => {
        const documentUrl = documents[docType.key];
        
        documentsHTML += `
            <div class="document-card">
                <div class="document-card-header">
                    <i class="${docType.icon} text-white"></i>
                    <h4 class="font-semibold">${docType.label}</h4>
                </div>
                <div class="document-card-body">
                    ${documentUrl ? `
                        <div class="document-preview-container">
                            <div class="document-loading" id="loading-${docType.key}">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading document...
                            </div>
                            <iframe 
                                src="${documentUrl}" 
                                class="w-full h-64"
                                frameborder="0"
                                loading="lazy"
                                onload="document.getElementById('loading-${docType.key}').style.display = 'none'"
                                onerror="document.getElementById('loading-${docType.key}').innerHTML = '<i class=\\'fas fa-exclamation-triangle mr-2\\'></i>Failed to load document'">
                            </iframe>
                        </div>
                        <div class="document-actions">
                            <a href="${documentUrl}" 
                               target="_blank" 
                               class="btn-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-external-link-alt mr-1"></i> Open
                            </a>
                            <a href="${documentUrl}" 
                               download 
                               class="btn-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    ` : `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-file-exclamation text-3xl mb-3 text-gray-400"></i>
                            <p class="font-medium">No ${docType.label} available</p>
                            <p class="text-sm mt-1">This document has not been uploaded by the applicant.</p>
                        </div>
                    `}
                </div>
            </div>
        `;
    });
    
    documentsHTML += '</div>';
    
    documentsContainer.innerHTML = documentsHTML;

    // Update status info - HIDE PRINT BUTTON FOR DOCUMENTS
    document.getElementById('applicantStatusInfo').textContent = 'Application Documents';
    
    // HIDE PRINT BUTTON FOR DOCUMENTS VIEW
    const printButton = modal.querySelector('button[onclick="printApplicationHistory()"]');
    if (printButton) {
        printButton.style.display = 'none';
    }

    // Show modal with animation
    modal.classList.remove('hidden');
    setTimeout(() => {
        const modalContent = document.getElementById('modalContent');
        if (modalContent) {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scaled');
        }
    }, 50);
}
// CORRECTED: Helper function to populate intake sheet data
function populateIntakeSheetData(data) {
    console.log('=== POPULATING INTAKE SHEET DATA ===');
    console.log('Full data object:', data);
    
    // SAFE ACCESS: Extract the main data with fallbacks
    const intakeData = data.intakeSheet || data || {};
    const familyMembers = data.family_members || [];
    const serviceRecords = data.rv_service_records || [];

// Head of Family Information - use intakeData with safe access
const headOfFamilyInfo = document.getElementById('headOfFamilyInfo');
if (headOfFamilyInfo) {
    headOfFamilyInfo.innerHTML = `
        <div class="bg-white p-4 rounded-lg border border-gray-200 w-full">
            <h4 class="font-semibold text-gray-800 mb-3">Head of Family Information</h4>
            <div class="grid grid-cols-2 gap-4 w-full">
                <div class="space-y-3 w-full">
                    <div class="flex items-center w-full">
                        <span class="font-medium text-gray-700 w-24">4PS:</span>
                        <span class="flex-1">${intakeData.head_4ps || 'N/A'}</span>
                    </div>
                    <div class="flex items-center w-full">
                        <span class="font-medium text-gray-700 w-24">IP No:</span>
                        <span class="flex-1">${intakeData.head_ipno || 'N/A'}</span>
                    </div>
                    <div class="w-full">
                        <div class="flex items-center w-full mb-1">
                            <span class="font-medium text-gray-700 w-24">Address:</span>
                        </div>
                        <div class="flex gap-4 w-full">
                            <div class="flex-1">
                                <span class="font-medium text-gray-700 text-sm block">Zone:</span>
                                <span>${intakeData.head_zone || 'N/A'}</span>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium text-gray-700 text-sm block">Barangay:</span>
                                <span>${intakeData.head_barangay || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-3 w-full">
                    <div class="flex items-center w-full">
                        <span class="font-medium text-gray-700 w-28">Place of Birth:</span>
                        <span class="flex-1">${intakeData.head_pob || 'N/A'}</span>
                    </div>
                    <div class="flex items-center w-full">
                        <span class="font-medium text-gray-700 w-28">Date of Birth:</span>
                        <span class="flex-1">${formatBirthdate(intakeData.head_dob)}</span> <!-- FORMATTED DATE -->
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100 w-full">
                <div class="w-full">
                    <span class="font-medium text-gray-700 block">Education:</span>
                    <span>${intakeData.head_educ || 'N/A'}</span>
                </div>
                <div class="w-full">
                    <span class="font-medium text-gray-700 block">Occupation:</span>
                    <span>${intakeData.head_occ || 'N/A'}</span>
                </div>
                <div class="w-full">
                    <span class="font-medium text-gray-700 block">Religion:</span>
                    <span>${intakeData.head_religion || 'N/A'}</span>
                </div>
            </div>
        </div>
    `;
}
    // Household Information - use intakeData
    const householdInfo = document.getElementById('householdInfo');
    if (householdInfo) {
        householdInfo.innerHTML = `
            <div class="bg-white p-4 rounded-lg border border-gray-200 w-full">
                <h4 class="font-semibold text-gray-800 mb-3">Household Information</h4>
                <div class="grid grid-cols-2 gap-4 w-full">
                    <div class="space-y-3 w-full">
                        <div class="flex items-center w-full">
                            <span class="font-medium text-gray-700 w-32">Serial Number:</span>
                            <span class="flex-1">${intakeData.serial_number || 'N/A'}</span>
                        </div>
                        <div class="flex items-center w-full">
                            <span class="font-medium text-gray-700 w-32">Total Income:</span>
                            <span class="flex-1">${intakeData.house_total_income || 'N/A'}</span>
                        </div>
                        <div class="flex items-center w-full">
                            <span class="font-medium text-gray-700 w-32">Net Income:</span>
                            <span class="flex-1">${intakeData.house_net_income || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="space-y-3 w-full">
                        <div class="flex items-center w-full">
                            <span class="font-medium text-gray-700 w-32">Other Income:</span>
                            <span class="flex-1">${intakeData.other_income || 'N/A'}</span>
                        </div>
                        <div class="flex gap-4 w-full">
                            <div class="flex-1 w-full">
                                <span class="font-medium text-gray-700 text-sm block">House:</span>
                                <span>${intakeData.house_house || 'N/A'}</span>
                            </div>
                            <div class="flex-1 w-full">
                                <span class="font-medium text-gray-700 text-sm block">Lot:</span>
                                <span>${intakeData.house_lot || 'N/A'}</span>
                            </div>
                        </div>
                        <div class="flex gap-4 w-full">
                            <div class="flex-1 w-full">
                                <span class="font-medium text-gray-700 text-sm block">Electric:</span>
                                <span>${intakeData.house_electric || 'N/A'}</span>
                            </div>
                            <div class="flex-1 w-full">
                                <span class="font-medium text-gray-700 text-sm block">Water:</span>
                                <span>${intakeData.house_water || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

// Family Members Table - use the family_members from root
const familyMembersTable = document.getElementById('familyMembersTable');
if (familyMembersTable) {
    console.log('=== FAMILY MEMBERS DATA ANALYSIS ===');
    console.log('Family Members Data:', familyMembers);
    console.log('Family Members Count:', familyMembers.length);
    
    let familyHTML = '';
    
    if (familyMembers.length > 0) {
        console.log('First Family Member Full Object:', familyMembers[0]);
        console.log('First Family Member Keys:', Object.keys(familyMembers[0]));
        
        familyMembers.forEach((member, index) => {
            console.log(`Processing family member ${index}:`, member);
            
            // Use the exact field names from your debug output
            const name = member.name || '-';
            const relation = member.relationship || '-';
            const birthdate = formatBirthdate(member.birthdate); // FORMATTED DATE
            const age = member.age || '-';
            const sex = member.sex || '-';
            const civilStatus = member.civil_status || '-';
            const education = member.education || '-';
            const occupation = member.occupation || '-';
            const income = member.monthly_income || '-';
            const remarks = member.remarks || '-';
            
            console.log(`Member ${index} mapped values:`, {
                name, relation, birthdate, age, sex, civilStatus, education, occupation, income, remarks
            });
            
            familyHTML += `
                <tr>
                    <td class="px-4 py-2 border-b">${name}</td>
                    <td class="px-4 py-2 border-b">${relation}</td>
                    <td class="px-4 py-2 border-b">${birthdate}</td>
                    <td class="px-4 py-2 border-b">${age}</td>
                    <td class="px-4 py-2 border-b">${sex}</td>
                    <td class="px-4 py-2 border-b">${civilStatus}</td>
                    <td class="px-4 py-2 border-b">${education}</td>
                    <td class="px-4 py-2 border-b">${occupation}</td>
                    <td class="px-4 py-2 border-b">${income}</td>
                    <td class="px-4 py-2 border-b">${remarks}</td>
                </tr>
            `;
        });
    } else {
        familyHTML = `
            <tr>
                <td colspan="10" class="px-4 py-4 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-users text-2xl text-gray-300 mb-2"></i>
                        <span>No family members data available</span>
                    </div>
                </td>
            </tr>
        `;
    }
    
    familyMembersTable.innerHTML = familyHTML;
}

 // Service Records Table - use rv_service_records from root
const serviceRecordsTable = document.getElementById('serviceRecordsTable');
if (serviceRecordsTable) {
    console.log('=== SERVICE RECORDS DATA ANALYSIS ===');
    console.log('Service Records Data:', serviceRecords);
    console.log('Service Records Count:', serviceRecords.length);
    
    let serviceHTML = '';
    
    if (serviceRecords.length > 0) {
        console.log('First Service Record Full Object:', serviceRecords[0]);
        console.log('First Service Record Keys:', Object.keys(serviceRecords[0]));
        
        serviceRecords.forEach((record, index) => {
            console.log(`Processing service record ${index}:`, record);
            
            // Use the exact field names from your debug output
            const date = record.date || '-';
            const problem = record.problem || '-';
            const action = record.action || '-';
            const remarks = record.remarks || '-';
            
            console.log(`Record ${index} mapped values:`, {
                date, problem, action, remarks
            });
            
            serviceHTML += `
                <tr>
                    <td class="px-4 py-2 border-b">${date}</td>
                    <td class="px-4 py-2 border-b">${problem}</td>
                    <td class="px-4 py-2 border-b">${action}</td>
                    <td class="px-4 py-2 border-b">${remarks}</td>
                </tr>
            `;
        });
    } else {
        serviceHTML = `
            <tr>
                <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-clipboard-list text-2xl text-gray-300 mb-2"></i>
                        <span>No service records available</span>
                    </div>
                </td>
            </tr>
        `;
    }
    
    serviceRecordsTable.innerHTML = serviceHTML;
}
}
        function closeApplicationModal() {
            const modal = document.getElementById('applicationHistoryModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Loading overlay functions
        window.addEventListener('load', function () {
            const overlay = document.getElementById('loadingOverlay');
            if (!overlay) return;
            overlay.classList.add('fade-out');
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('fade-out');
            }, 300);
        });

        function showLoadingOverlay() {
            const overlay = document.getElementById('loadingOverlay');
            if (!overlay) return;
            overlay.style.display = 'flex';
            overlay.classList.remove('fade-out');
        }

        function hideLoadingOverlay() {
            const overlay = document.getElementById('loadingOverlay');
            if (!overlay) return;
            overlay.classList.add('fade-out');
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.classList.remove('fade-out');
            }, 300);
        }

        // Modal close functions
        function closeEmailModal() {
            document.getElementById('emailModal').classList.add('hidden');
        }

        function closeSmsModal() {
            document.getElementById('smsModal').classList.add('hidden');
        }

        // Initialize modal events
        function initializeModalEvents() {
            // Email modal close
            const closeEmailModalBtn = document.getElementById('closeEmailModal');
            const cancelEmailBtn = document.getElementById('cancelEmailBtn');
            
            if (closeEmailModalBtn) {
                closeEmailModalBtn.addEventListener('click', closeEmailModal);
            }
            if (cancelEmailBtn) {
                cancelEmailBtn.addEventListener('click', closeEmailModal);
            }

            // SMS modal close
            const closeSmsModalBtn = document.getElementById('closeSmsModal');
            const cancelSmsBtn = document.getElementById('cancelSmsBtn');
            
            if (closeSmsModalBtn) {
                closeSmsModalBtn.addEventListener('click', closeSmsModal);
            }
            if (cancelSmsBtn) {
                cancelSmsBtn.addEventListener('click', closeSmsModal);
            }

            // Close modals when clicking outside
            const emailModal = document.getElementById('emailModal');
            const smsModal = document.getElementById('smsModal');
            const applicationHistoryModal = document.getElementById('applicationHistoryModal');

            if (emailModal) {
                emailModal.addEventListener('click', function(e) {
                    if (e.target === emailModal) {
                        closeEmailModal();
                    }
                });
            }

            if (smsModal) {
                smsModal.addEventListener('click', function(e) {
                    if (e.target === smsModal) {
                        closeSmsModal();
                    }
                });
            }

            if (applicationHistoryModal) {
                applicationHistoryModal.addEventListener('click', function(e) {
                    if (e.target === applicationHistoryModal) {
                        closeApplicationModal();
                    }
                });
            }

            // SMS Form Submission
            document.getElementById('smsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const sendSmsBtn = document.getElementById('sendSmsBtn');
                const sendSmsText = document.getElementById('sendSmsText');
                const sendSmsLoading = document.getElementById('sendSmsLoading');
                
                const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
                const filteredApplicants = activeTab === 'mayor-applicants' ? filteredMayorApplicants : filteredLydoApplicants;
                const selectedApplicants = filteredApplicants.filter(applicant => applicant.selected);
                
                if (selectedApplicants.length === 0) {
                    Swal.fire('Error', 'Please select at least one applicant', 'error');
                    return;
                }

                const selectedEmails = selectedApplicants.map(applicant => applicant.applicant_email).join(',');

                const formData = new FormData(this);
                formData.append('selected_emails', selectedEmails);
                formData.append('sms_type', document.querySelector('input[name="smsType"]:checked').value);

                sendSmsText.classList.add('hidden');
                sendSmsLoading.classList.remove('hidden');
                sendSmsBtn.disabled = true;

                fetch('/lydo_admin/send-sms-to-applicants', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                        document.getElementById('smsModal').classList.add('hidden');
                        document.getElementById('smsForm').reset();
                        document.getElementById('scheduleFields').classList.add('hidden');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Failed to send SMS', 'error');
                })
                .finally(() => {
                    sendSmsText.classList.remove('hidden');
                    sendSmsLoading.classList.add('hidden');
                    sendSmsBtn.disabled = false;
                });
            });

            // SMS Type Toggle
            document.querySelectorAll('.sms-type-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const scheduleFields = document.getElementById('scheduleFields');
                    const scheduleNote = document.getElementById('scheduleNote');
                    const smsMessageContainer = document.getElementById('smsMessageContainer');
                    
                    if (this.value === 'schedule') {
                        scheduleFields.classList.remove('hidden');
                        scheduleNote.classList.remove('hidden');
                        smsMessageContainer.classList.add('hidden');
                        document.getElementById('scheduleWhat').required = true;
                        document.getElementById('smsMessage').required = false;
                    } else {
                        scheduleFields.classList.add('hidden');
                        scheduleNote.classList.add('hidden');
                        smsMessageContainer.classList.remove('hidden');
                        document.getElementById('scheduleWhat').required = false;
                        document.getElementById('smsMessage').required = true;
                    }
                });
            });

            // Character count for SMS
            document.getElementById('smsMessage').addEventListener('input', function() {
                const charCount = this.value.length;
                document.getElementById('smsCharCount').textContent = charCount;
                
                if (charCount > 160) {
                    document.getElementById('smsCharCount').classList.add('text-red-500');
                } else {
                    document.getElementById('smsCharCount').classList.remove('text-red-500');
                }
            });

            // Print PDF functionality
            document.getElementById('printPdfBtnMayor').addEventListener('click', function() {
                printMayorApplicantsPdf();
            });

            document.getElementById('printPdfBtnLydo').addEventListener('click', function() {
                printLydoReviewedApplicantsPdf();
            });
        }

        // Print PDF functions
        function printMayorApplicantsPdf() {
            showLoadingOverlay();
            
            const search = document.getElementById('searchInputMayor').value;
            const barangay = document.getElementById('barangaySelectMayor').value;
            const academicYear = document.getElementById('academicYearSelectMayor').value;
            const initialScreening = document.getElementById('initialScreeningSelectMayor').value;

            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (barangay) params.append('barangay', barangay);
            if (academicYear) params.append('academic_year', academicYear);
            
            if (initialScreening && initialScreening !== 'all') {
                params.append('initial_screening', initialScreening);
            } else {
                params.append('initial_screening', 'all');
            }

            const url = `/lydo_admin/generate-mayor-applicants-pdf?${params.toString()}`;
            window.open(url, '_blank');
            
            setTimeout(() => {
                hideLoadingOverlay();
            }, 2000);
        }

        function printLydoReviewedApplicantsPdf() {
            showLoadingOverlay();
            
            const search = document.getElementById('searchInputLydo').value;
            const barangay = document.getElementById('barangaySelectLydo').value;
            const academicYear = document.getElementById('academicYearSelectLydo').value;
            const remarks = document.getElementById('remarksSelectLydo').value;

            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (barangay) params.append('barangay', barangay);
            if (academicYear) params.append('academic_year', academicYear);
            
            if (remarks) {
                params.append('remarks', remarks);
            }

            const url = `/lydo_admin/generate-lydo-applicants-pdf?${params.toString()}`;
            window.open(url, '_blank');
            
            setTimeout(() => {
                hideLoadingOverlay();
            }, 2000);
        }

        // Make functions global
        window.printApplicationHistory = printApplicationHistory;
        window.viewApplicantDocuments = viewApplicantDocuments;
        window.viewApplicantIntakeSheet = viewApplicantIntakeSheet;
        window.closeApplicationModal = closeApplicationModal;
        window.closeEmailModal = closeEmailModal;
        window.closeSmsModal = closeSmsModal;
        window.toggleDropdown = toggleDropdown;

        // Add this helper function to format dates
function formatBirthdate(dateString) {
    if (!dateString || dateString === '-' || dateString === 'N/A') {
        return 'N/A';
    }
    
    try {
        // Handle different date formats
        const date = new Date(dateString);
        
        // Check if date is valid
        if (isNaN(date.getTime())) {
            return dateString; // Return original if invalid
        }
        
        // Format as "Month Day, Year" (e.g., "October 23, 2003")
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    } catch (error) {
        console.error('Error formatting date:', error);
        return dateString; // Return original if error
    }
}
    </script>
</body>
</html>