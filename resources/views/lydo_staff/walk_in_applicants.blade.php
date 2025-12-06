
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Walk-in Applicants Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    
    <style>
            .dashboard-grid {
            display: grid;
            grid-template-rows: auto 1fr;
            height: 100vh;
        }
        .select2-container--default .select2-selection--single {
            border: 1px solid #000000 !important;
            border-radius: 8px !important;
            height: 42px !important;
            padding: 8px !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
        }
        
        .error { 
            border-color: #ef4444 !important; 
            background-color: #fef2f2 !important;
        }
        .valid { 
            border-color: #10b981 !important; 
            background-color: #f0fdf4 !important;
        }
        .error-message { 
            color: #ef4444; 
            font-size: 12px; 
            margin-top: 4px; 
            display: block; 
        }
        
        /* File Input Styling */
        input[type="file"] {
            border: 1px solid #999;
            border-radius: 8px;
            padding: 6px;
            height: 42px;
            font-size: 14px;
            color: #3b0066;
        }
        
        input[type="file"]::file-selector-button {
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            margin-right: 10px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }
        
        input[type="file"]::file-selector-button:hover {
            background: #5b21b6;
        }
        
        /* Loading overlay */
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
        
        /* Tab styling */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .tab-button {
            padding: 12px 20px;
            font-weight: 600;
            font-size: 14px;
            background: #f0f0f0;
            color: #666;
            border: none;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            transition: background 0.3s ease, color 0.3s ease;
            margin-right: 5px;
        }
        
        .tab-button.active {
            background: linear-gradient(90deg, #4b2b8d 0%, #230061 100%);
            color: white;
        }
        
        .duplicate-message {
            font-size: 12px;
            margin-top: 5px;
            padding: 5px;
            border-radius: 4px;
            text-align: center;
        }
        
        /* Form styling */
        .input-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .input-group {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        
        .input-group.wide {
            flex: 2;
        }
        
        input, select {
            padding: 10px 12px;
            border: 1px solid #000000;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            height: 42px;
            background-color: #fff;
            width: 100%;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }
        
        input:focus, select:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.25);
        }
        
        .nav-btn {
            padding: 12px 20px;
            font-weight: 700;
            font-size: 14px;
            background: linear-gradient(90deg, #4b2b8d 0%, #230061 100%);
            color: white;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            box-shadow: 0 10px 20px #5132a6cc;
            transition: background 0.3s ease;
            height: 42px;
        }
        
        .nav-btn:hover {
            background: linear-gradient(90deg, #5b21b6 0%, #3b0066 100%);
        }
        
        .nav-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .button-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            gap: 100px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner">
            <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
        </div>
    </div>
    
    <div class="dashboard-grid">
        <!-- Header (Same as renewal) -->
        <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg">
                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Staff</span>
                </div>
                
                <!-- Notification Bell -->
                <div class="relative hidden">
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
                </div>
            </div>
        </header>
        
        <div class="flex flex-1 overflow-hidden"> 
            <!-- Sidebar (Same as renewal) -->
            <div class="w-20 md:w-80 bg-white shadow-md flex flex-col transition-all duration-300">
                <nav class="flex-1 p-2 md:p-4 space-y-1 overflow-y-auto">
                    <ul class="side-menu top space-y-4">
                        <li>
                            <a href="/lydo_staff/dashboard" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
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
                                @if($pendingScreening > 0)
                                    <span class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                        {{ $pendingScreening }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/renewal" class="flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Renewals</span>
                                </div>
                                @if($pendingRenewals > 0)
                                    <span id="pendingRenewalsBadge" class="ml-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                        {{ $pendingRenewals }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/disbursement" class="flex items-center justify-between p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bx-wallet text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Disbursement</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_staff/walk_in" class="flex items-center p-3 rounded-lg text-gray-700 bg-violet-600 text-white">
                                <i class="fas fa-walking text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Walk-in Applicants</span>
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
            
            <!-- Main Content Area -->
            <div class="flex-1 overflow-hidden p-4 md:p-2 text-[16px] content-scrollable">
                <div class="p-4 bg-gray-50 min-h-screen rounded-lg shadow">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">Walk-in Applicants Registration</h2>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-200">
                            📝 Register walk-in applicants (No database entry will be made)
                        </h3>
                    </div>
                    
                    <!-- Success Message Display -->
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                                <div>
                                    <h4 class="text-green-800 font-semibold">Success!</h4>
                                    <p class="text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Display Walk-in Data if Available -->
                        @if(session('walk_in_data'))
                            <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <h4 class="text-lg font-semibold text-gray-800 mb-3">Walk-in Applicant Information Recorded:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Name:</p>
                                        <p class="font-medium">{{ session('walk_in_data.applicant_fname') }} {{ session('walk_in_data.applicant_mname') }} {{ session('walk_in_data.applicant_lname') }} {{ session('walk_in_data.applicant_suffix') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Email:</p>
                                        <p class="font-medium">{{ session('walk_in_data.applicant_email') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Contact:</p>
                                        <p class="font-medium">{{ session('walk_in_data.applicant_contact_number') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">School:</p>
                                        <p class="font-medium">{{ session('walk_in_data.applicant_school_name') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Course:</p>
                                        <p class="font-medium">{{ session('walk_in_data.applicant_course') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Year Level:</p>
                                        <p class="font-medium">{{ session('walk_in_data.applicant_year_level') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Academic Year:</p>
                                        <p class="font-medium">{{ session('walk_in_data.applicant_acad_year') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Submitted At:</p>
                                        <p class="font-medium">{{ session('walk_in_data.submitted_at') }}</p>
                                    </div>
                                    @if(session('walk_in_data.documents_info'))
                                        <div class="col-span-2">
                                            <p class="text-sm text-gray-600">Documents:</p>
                                            <p class="font-medium">5 documents uploaded (PDF files stored in session)</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-600 mr-3 text-xl"></i>
                                <div>
                                    <h4 class="text-red-800 font-semibold">Validation Errors:</h4>
                                    <ul class="text-red-700 list-disc pl-5 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Walk-in Form (Based on applicants_reg) -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <form id="walkInForm" method="POST" action="{{ route('walk.in.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Tab Navigation -->
                            <div class="tab-nav mb-6">
                                <button type="button" class="tab-button active" data-tab="personal">Personal Information</button>
                                <button type="button" class="tab-button" data-tab="education">Educational Attainment</button>
                                <button type="button" class="tab-button" data-tab="requirements">Application Requirements</button>
                            </div>
                            
                            <!-- Tab Content: Personal Information -->
                            <div id="personal" class="tab-content active">
                                <!-- Name Fields -->
                                <div class="input-row">
                                    <div class="input-group">
                                        <label for="fname">First Name *</label>
                                        <input type="text" id="fname" name="applicant_fname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required value="{{ old('applicant_fname') }}" />
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="mname">Middle Name</label>
                                        <input type="text" id="mname" name="applicant_mname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" value="{{ old('applicant_mname') }}" />
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="lname">Last Name *</label>
                                        <input type="text" id="lname" name="applicant_lname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required value="{{ old('applicant_lname') }}" />
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group" style="width: 10px">
                                        <label for="suffix">Suffix</label>
                                        <input type="text" id="suffix" name="applicant_suffix" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" value="{{ old('applicant_suffix') }}" />
                                        <small class="error-message"></small>
                                    </div>
                                </div>
                                
                                <!-- Personal Details -->
                                <div class="input-row">
                                    <div class="input-group">
                                        <label for="gender">Gender *</label>
                                        <select id="gender" name="applicant_gender" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('applicant_gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('applicant_gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="bdate">Birth Date *</label>
                                        <input type="date" id="bdate" name="applicant_bdate" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required value="{{ old('applicant_bdate') }}" />
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="civil_status">Civil Status *</label>
                                        <select id="civil_status" name="applicant_civil_status" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                                            <option value="">Select Civil Status</option>
                                            <option value="single" {{ old('applicant_civil_status') == 'single' ? 'selected' : '' }}>Single</option>
                                            <option value="married" {{ old('applicant_civil_status') == 'married' ? 'selected' : '' }}>Married</option>
                                            <option value="widowed" {{ old('applicant_civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                            <option value="divorced" {{ old('applicant_civil_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        </select>
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="brgy">Barangay *</label>
                                        <select id="brgy" name="applicant_brgy" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                                            <option value="">Select Barangay</option>
                                            <option value="Sugbong cogon" {{ old('applicant_brgy') == 'Sugbong cogon' ? 'selected' : '' }}>Sugbong cogon</option>
                                            <option value="Baluarte" {{ old('applicant_brgy') == 'Baluarte' ? 'selected' : '' }}>Baluarte</option>
                                            <option value="Casinglot" {{ old('applicant_brgy') == 'Casinglot' ? 'selected' : '' }}>Casinglot</option>
                                            <option value="Gracia" {{ old('applicant_brgy') == 'Gracia' ? 'selected' : '' }}>Gracia</option>
                                            <option value="Mohon" {{ old('applicant_brgy') == 'Mohon' ? 'selected' : '' }}>Mohon</option>
                                            <option value="Natumolan" {{ old('applicant_brgy') == 'Natumolan' ? 'selected' : '' }}>Natumolan</option>
                                            <option value="Poblacion" {{ old('applicant_brgy') == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                                            <option value="Rosario" {{ old('applicant_brgy') == 'Rosario' ? 'selected' : '' }}>Rosario</option>
                                            <option value="Santa Ana" {{ old('applicant_brgy') == 'Santa Ana' ? 'selected' : '' }}>Santa Ana</option>
                                            <option value="Santa Cruz" {{ old('applicant_brgy') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                                        </select>
                                        <small class="error-message"></small>
                                    </div>
                                </div>
                                
                                <!-- Contact Details -->
                                <div class="input-row">
                                    <div class="input-group">
                                        <label for="email">Email *</label>
                                        <input type="email" id="email" name="applicant_email" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required value="{{ old('applicant_email') }}" />
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="contact">Contact Number *</label>
                                        <input type="tel" id="contact" name="applicant_contact_number" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required maxlength="12" value="{{ old('applicant_contact_number') }}" />
                                        <small class="error-message"></small>
                                    </div>
                                </div>
                                
                                <!-- Duplicate Check Message -->
                                <div id="duplicateMessage" class="duplicate-message"></div>
                            </div>
                            
                            <!-- Tab Content: Educational Attainment -->
                            <div id="education" class="tab-content">
                                <div class="input-row">
                                    <div class="input-group" style="width: 100%">
                                        <label for="school_name">School Name *</label>
                                        <select id="school_name" name="applicant_school_name" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 select2" required>
                                            <option value="">Select School</option>
                                            <optgroup label="Others">
                                                <option value="Others" {{ old('applicant_school_name') == 'Others' ? 'selected' : '' }}>Others (Please specify below)</option>
                                            </optgroup>
                                            <optgroup label="State Universities">
                                                <option value="USTP CDO" {{ old('applicant_school_name') == 'USTP CDO' ? 'selected' : '' }}>University of Science and Technology of Southern Philippines (USTP) – Cagayan de Oro</option>
                                                <option value="USTP Claveria" {{ old('applicant_school_name') == 'USTP Claveria' ? 'selected' : '' }}>University of Science and Technology of Southern Philippines (USTP) – Claveria</option>
                                                <option value="USTP Villanueva" {{ old('applicant_school_name') == 'USTP Villanueva' ? 'selected' : '' }}>University of Science and Technology of Southern Philippines (USTP) – Villanueva</option>
                                                <option value="MSU Naawan" {{ old('applicant_school_name') == 'MSU Naawan' ? 'selected' : '' }}>Mindanao State University – Naawan (MSU-Naawan)</option>
                                                <option value="MOSCAT" {{ old('applicant_school_name') == 'MOSCAT' ? 'selected' : '' }}>Misamis Oriental State College of Agriculture and Technology (MOSCAT), Claveria</option>
                                            </optgroup>
                                            <optgroup label="Community Colleges">
                                                <option value="Opol Community College" {{ old('applicant_school_name') == 'Opol Community College' ? 'selected' : '' }}>Opol Community College</option>
                                                <option value="Tagoloan Community College" {{ old('applicant_school_name') == 'Tagoloan Community College' ? 'selected' : '' }}>Tagoloan Community College</option>
                                                <option value="Bugo Community College" {{ old('applicant_school_name') == 'Bugo Community College' ? 'selected' : '' }}>Bugo Community College</option>
                                                <option value="Initao Community College" {{ old('applicant_school_name') == 'Initao Community College' ? 'selected' : '' }}>Initao Community College</option>
                                                <option value="Magsaysay College" {{ old('applicant_school_name') == 'Magsaysay College' ? 'selected' : '' }}>Magsaysay College, Misamis Oriental</option>
                                            </optgroup>
                                            <optgroup label="Private Colleges & Universities">
                                                <option value="Liceo de Cagayan University" {{ old('applicant_school_name') == 'Liceo de Cagayan University' ? 'selected' : '' }}>Liceo de Cagayan University, CDO</option>
                                                <option value="PHINMA COC" {{ old('applicant_school_name') == 'PHINMA COC' ? 'selected' : '' }}>PHINMA Cagayan de Oro College</option>
                                                <option value="Capitol University" {{ old('applicant_school_name') == 'Capitol University' ? 'selected' : '' }}>Capitol University, CDO</option>
                                                <option value="Lourdes College" {{ old('applicant_school_name') == 'Lourdes College' ? 'selected' : '' }}>Lourdes College, CDO</option>
                                                <option value="Blessed Mother College" {{ old('applicant_school_name') == 'Blessed Mother College' ? 'selected' : '' }}>Blessed Mother College, CDO</option>
                                                <option value="Pilgrim Christian College" {{ old('applicant_school_name') == 'Pilgrim Christian College' ? 'selected' : '' }}>Pilgrim Christian College, CDO</option>
                                                <option value="Gingoog Christian College" {{ old('applicant_school_name') == 'Gingoog Christian College' ? 'selected' : '' }}>Gingoog Christian College</option>
                                                <option value="Christ the King College" {{ old('applicant_school_name') == 'Christ the King College' ? 'selected' : '' }}>Christ the King College, Gingoog City</option>
                                                <option value="St. Rita's College" {{ old('applicant_school_name') == 'St. Rita\'s College' ? 'selected' : '' }}>St. Rita's College of Balingasag</option>
                                                <option value="St. Peter's College" {{ old('applicant_school_name') == 'St. Peter\'s College' ? 'selected' : '' }}>St. Peter's College of Balingasag</option>
                                                <option value="Saint John Vianney Seminary" {{ old('applicant_school_name') == 'Saint John Vianney Seminary' ? 'selected' : '' }}>Saint John Vianney Theological Seminary, CDO</option>
                                                <option value="Asian College of science and Technology" {{ old('applicant_school_name') == 'Asian College of science and Technology' ? 'selected' : '' }}>Asian College of Science and Technology, CDO</option>
                                            </optgroup>
                                        </select>
                                        <input type="text" id="school_name_other" name="applicant_school_name_other" placeholder="Please specify your school" style="display: none; margin-top: 8px; padding: 10px; border: 1px solid black; border-radius: 8px; font-size: 14px; outline: none; width: 100%;" value="{{ old('applicant_school_name_other') }}"/>
                                        <small class="error-message"></small>
                                    </div>
                                </div>
                                
                                <!-- Academic Details -->
                                <div class="input-row">
                                    <div class="input-group">
                                        <label for="year_level">Year Level *</label>
                                        <select id="year_level" name="applicant_year_level" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                                            <option value="">Select Year Level</option>
                                            <option value="1st Year" {{ old('applicant_year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                                            <option value="2nd Year" {{ old('applicant_year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                            <option value="3rd Year" {{ old('applicant_year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                            <option value="4th Year" {{ old('applicant_year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
                                            <option value="5th Year" {{ old('applicant_year_level') == '5th Year' ? 'selected' : '' }}>5th Year</option>
                                        </select>
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="course">Course *</label>
                                        <input type="text" id="course" name="applicant_course" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="Course" value="{{ old('applicant_course') }}" />
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="acad_year">Academic Year *</label>
                                        <input type="text" id="acad_year" name="applicant_acad_year" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="e.g., 2023-2024" readonly value="{{ old('applicant_acad_year', date('Y') . '-' . (date('Y')+1)) }}" />
                                        <small class="error-message"></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Content: Application Requirements -->
                            <div id="requirements" class="tab-content">
                                <div class="input-row">
                                    <div class="input-group">
                                        <label for="application_letter">Application Letter *</label>
                                        <input type="file" id="application_letter" name="application_letter" accept="application/pdf" required class="input-file"/>
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="grade_slip">Grade Slip *</label>
                                        <input type="file" id="grade_slip" name="grade_slip" accept="application/pdf" required class="input-file" />
                                        <small class="error-message"></small>
                                    </div>
                                </div>

                                <div class="input-row">
                                    <div class="input-group">
                                        <label for="certificate_of_registration">Certificate of Registration *</label>
                                        <input type="file" id="certificate_of_registration" name="certificate_of_registration" accept="application/pdf" required class="input-file"/>
                                        <small class="error-message"></small>
                                    </div>
                                    <div class="input-group">
                                        <label for="barangay_indigency">Barangay Indigency *</label>
                                        <input type="file" id="barangay_indigency" name="barangay_indigency" accept="application/pdf" required class="input-file"/>
                                        <small class="error-message"></small>
                                    </div>
                                </div>

                                <div class="input-row">
                                    <div class="input-group">
                                        <label for="student_id">Student ID *</label>
                                        <input type="file" id="student_id" name="student_id" accept="application/pdf" required class="input-file"/>
                                        <small class="error-message"></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Navigation Buttons -->
                            <div class="button-row mt-8">
                                <button type="button" id="prevBtn" class="nav-btn prev-btn" style="display: none;">Previous</button>
                                <button type="button" id="nextBtn" class="nav-btn next-btn">Next</button>
                                <button type="submit" id="submitBtn" class="nav-btn submit-btn" style="display: none;">
                                    <span id="submitBtnText">Submit Walk-in Information</span>
                                    <svg id="submitBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching logic
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        let currentTab = 0;
        let select2Initialized = false;
        
        // Show tab function
        function showTab(index) {
            tabContents.forEach(content => content.classList.remove('active'));
            tabButtons.forEach(button => button.classList.remove('active'));
            tabContents[index].classList.add('active');
            tabButtons[index].classList.add('active');
            
            prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
            nextBtn.style.display = index === tabContents.length - 1 ? 'none' : 'inline-block';
            submitBtn.style.display = index === tabContents.length - 1 ? 'inline-block' : 'none';
            
            // Initialize Select2 when education tab is shown
            if (index === 1 && !select2Initialized) {
                $('#school_name').select2({
                    placeholder: 'Search and select your school...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#education'),
                    language: {
                        noResults: function() {
                            return 'No results found. If your school is not listed, select "Others".';
                        }
                    }
                });
                
                // Handle "Others" option
                $('#school_name').on('change', function() {
                    const schoolOtherInput = document.getElementById("school_name_other");
                    if (this.value === "Others") {
                        schoolOtherInput.style.display = "block";
                        schoolOtherInput.setAttribute("required", "required");
                    } else {
                        schoolOtherInput.style.display = "none";
                        schoolOtherInput.removeAttribute("required");
                        schoolOtherInput.value = "";
                    }
                    validateInput(this);
                    toggleButton();
                });
                
                select2Initialized = true;
            }
            
            updateButtonStates();
        }
        
        // Update button states based on validation
        function updateButtonStates() {
            const currentTabContent = tabContents[currentTab];
            const hasErrorMessage = Array.from(
                currentTabContent.querySelectorAll(".error-message")
            ).some((msg) => msg.textContent.trim() !== "");
            
            const hasEmptyRequired = Array.from(
                currentTabContent.querySelectorAll("input[required], select[required]")
            ).some((input) => {
                if (input.type === "file") return input.files.length === 0;
                return !input.value.trim();
            });
            
            nextBtn.disabled = hasErrorMessage || hasEmptyRequired;
            if (submitBtn.style.display !== 'none') {
                submitBtn.disabled = hasErrorMessage || hasEmptyRequired;
            }
        }
        
        // Tab button event listeners
        tabButtons.forEach((button, index) => {
            button.addEventListener('click', () => {
                // Validate current tab before allowing switch
                const currentTabContent = tabContents[currentTab];
                const hasErrorMessage = Array.from(
                    currentTabContent.querySelectorAll(".error-message")
                ).some((msg) => msg.textContent.trim() !== "");
                
                const hasEmptyRequired = Array.from(
                    currentTabContent.querySelectorAll("input[required], select[required]")
                ).some((input) => {
                    if (input.type === "file") return input.files.length === 0;
                    return !input.value.trim();
                });
                
                if (index !== currentTab && (hasErrorMessage || hasEmptyRequired)) {
                    return; // Prevent switching to other tabs if current tab has errors
                }
                
                currentTab = index;
                showTab(currentTab);
            });
        });
        
        // Previous button
        prevBtn.addEventListener('click', () => {
            if (currentTab > 0) {
                currentTab--;
                showTab(currentTab);
            }
        });
        
        // Next button
        nextBtn.addEventListener('click', () => {
            // Validate current tab before proceeding
            const currentTabContent = tabContents[currentTab];
            const hasErrorMessage = Array.from(
                currentTabContent.querySelectorAll(".error-message")
            ).some((msg) => msg.textContent.trim() !== "");
            
            const hasEmptyRequired = Array.from(
                currentTabContent.querySelectorAll("input[required], select[required]")
            ).some((input) => {
                if (input.type === "file") return input.files.length === 0;
                return !input.value.trim();
            });
            
            if (hasErrorMessage || hasEmptyRequired) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Please complete all required fields and fix any errors before proceeding to the next tab.",
                });
                return; // Prevent tab switch
            }
            
            if (currentTab < tabContents.length - 1) {
                currentTab++;
                showTab(currentTab);
            }
        });
        
        // Initialize first tab
        showTab(currentTab);
        
        // Form validation
        const walkInForm = document.getElementById("walkInForm");
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnSpinner = document.getElementById('submitBtnSpinner');
        
        const rules = {
            name: /^[A-Za-z\s]+$/, // letters and spaces only
            contact: /^(09\d{9}|\+639\d{9})$/,
            gmail: /^[a-zA-Z0-9._%+-]+@gmail\.com$/  // Gmail format
        };
        
        let debounceTimers = {};
        
        function validateInput(input) {
            const id = input.id;
            const value = input.value.trim();
            const errorEl = getErrorEl(input);
            let errorMsg = "";
            let valid = true;
            
            // Required field validation
            if (input.hasAttribute("required") && !value) {
                errorMsg = "This field cannot be empty";
                valid = false;
            }
            
            // Name validation
            if (valid && ["fname", "mname", "lname"].includes(id)) {
                if (value && !rules.name.test(value)) {
                    errorMsg = "Only letters are allowed";
                    valid = false;
                }
            }
            
            // Contact validation
            if (valid && id === "contact") {
                if (value && !rules.contact.test(value)) {
                    errorMsg = "Format: 09XXXXXXXXX or +639XXXXXXXXX";
                    valid = false;
                }
            }
            
            // Email format validation
            if (valid && id === "email" && value) {
                if (!rules.gmail.test(value)) {
                    errorMsg = "Email must end with @gmail.com";
                    valid = false;
                }
            }
            
            // Birthdate validation
            if (valid && id === "bdate") {
                if (value) {
                    const date = new Date(value);
                    const today = new Date();
                    if (isNaN(date.getTime())) {
                        errorMsg = "Invalid date";
                        valid = false;
                    } else if (date > today) {
                        errorMsg = "Birth date cannot be in the future";
                        valid = false;
                    }
                }
            }
            
            // Select validation
            if (valid && input.tagName === 'SELECT' && !value) {
                errorMsg = "This field is required";
                valid = false;
            }
            
            // Update UI
            updateUI(input, valid, errorMsg);
            toggleButton();
            return valid;
        }
        
        // File validation
        function validateFile(input) {
            const file = input.files[0];
            let valid = true;
            let errorMsg = "";
            
            if (!file) {
                valid = false;
                errorMsg = "This file is required";
            } else {
                const isPdf = file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");
                if (!isPdf) {
                    valid = false;
                    errorMsg = "Only PDF files are allowed";
                } else if (file.size > 5 * 1024 * 1024) {
                    valid = false;
                    errorMsg = "File size must not exceed 5MB";
                }
            }
            
            updateUI(input, valid, errorMsg);
            toggleButton();
            return valid;
        }
        
        function getErrorEl(input) {
            return input.parentElement.querySelector(".error-message");
        }
        
        function updateUI(input, valid, errorMsg = "") {
            const errorEl = getErrorEl(input);
            if (!valid) {
                input.classList.add("error");
                input.classList.remove("valid");
                if (errorEl) errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>' + errorMsg;
            } else {
                input.classList.remove("error");
                input.classList.add("valid");
                if (errorEl) errorEl.innerHTML = "";
            }
        }
        
        function toggleButton() {
            updateButtonStates();
        }
        
        // Initialize event listeners for validation
        function initializeEventListeners() {
            const inputs = walkInForm.querySelectorAll("input, select");
            
            inputs.forEach(input => {
                if (input.type === "file") {
                    input.addEventListener("change", () => validateFile(input));
                } else {
                    input.addEventListener("input", function() {
                        validateInput(this);
                        toggleButton();
                    });
                    
                    input.addEventListener("blur", function() {
                        validateInput(this);
                        toggleButton();
                    });
                }
            });
            
            // Initialize duplicate checking
            initializeDuplicateChecking();
        }
        
        // Enhanced duplicate applicant check function
        function checkDuplicateApplicant() {
            const fname = document.getElementById('fname').value.trim();
            const lname = document.getElementById('lname').value.trim();
            const gender = document.getElementById('gender').value;
            const bdate = document.getElementById('bdate').value;
            const acadYear = document.getElementById('acad_year').value;
            
            // Only check if all required fields are filled
            if (!fname || !lname || !gender || !bdate || !acadYear) {
                const duplicateMessage = document.getElementById('duplicateMessage');
                if (duplicateMessage) {
                    duplicateMessage.innerHTML = '';
                }
                return;
            }
            
            // Show checking state
            const duplicateMessage = document.getElementById('duplicateMessage');
            if (!duplicateMessage) return;
            
            duplicateMessage.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Checking for existing applications...';
            duplicateMessage.style.color = '#3b82f6';
            duplicateMessage.style.backgroundColor = '#eff6ff';
            duplicateMessage.style.border = '1px solid #bfdbfe';
            
            fetch('/check-duplicate-applicant', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    fname: fname,
                    lname: lname,
                    gender: gender,
                    bdate: bdate,
                    acad_year: acadYear
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    duplicateMessage.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>An applicant with the same name, gender, birth date, and academic year already exists in the database.';
                    duplicateMessage.style.color = '#ef4444';
                    duplicateMessage.style.backgroundColor = '#fef2f2';
                    duplicateMessage.style.border = '1px solid #fecaca';
                    
                    // Disable form submission
                    nextBtn.disabled = true;
                    submitBtn.disabled = true;
                } else {
                    duplicateMessage.innerHTML = '<i class="fa-solid fa-circle-check mr-1"></i>No duplicate application found for this academic year.';
                    duplicateMessage.style.color = '#10b981';
                    duplicateMessage.style.backgroundColor = '#f0fdf4';
                    duplicateMessage.style.border = '1px solid #bbf7d0';
                    
                    // Re-enable buttons if no other errors
                    toggleButton();
                }
            })
            .catch(error => {
                console.error('Error checking duplicate applicant:', error);
                duplicateMessage.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>Error checking for duplicates. Please try again.';
                duplicateMessage.style.color = '#ef4444';
                duplicateMessage.style.backgroundColor = '#fef2f2';
                duplicateMessage.style.border = '1px solid #fecaca';
            });
        }
        
        // Add event listeners for duplicate applicant check
        function initializeDuplicateChecking() {
            const fieldsToCheck = ['fname', 'lname', 'gender', 'bdate', 'acad_year'];
            
            fieldsToCheck.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('change', checkDuplicateApplicant);
                    field.addEventListener('blur', checkDuplicateApplicant);
                }
            });
        }
        
        // Contact number validation - only numbers and max 12 digits
        const contactInput = document.getElementById('contact');
        if (contactInput) {
            contactInput.addEventListener('input', function(e) {
                // Remove any non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Limit to 12 digits
                if (this.value.length > 12) {
                    this.value = this.value.slice(0, 12);
                }
                
                // Validate the input
                validateInput(this);
            });
            
            contactInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const numericText = pastedText.replace(/[^0-9]/g, '');
                document.execCommand('insertText', false, numericText.slice(0, 12));
            });
        }
        
        // Auto-capitalize names
        function capitalizeWords(str) {
            return str.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }
        
        function handleInputCapitalization(event) {
            const input = event.target;
            const cursorPosition = input.selectionStart;
            
            if (input.value) {
                input.value = capitalizeWords(input.value);
                input.setSelectionRange(cursorPosition, cursorPosition);
            }
        }
        
        const textInputs = document.querySelectorAll('input[type="text"]');
        textInputs.forEach(input => {
            input.addEventListener('blur', handleInputCapitalization);
        });
        
        const courseInput = document.getElementById('course');
        if (courseInput) {
            courseInput.addEventListener('blur', handleInputCapitalization);
        }
        
        // Form submission handler
        walkInForm.addEventListener("submit", function (e) {
            e.preventDefault();
            
            // Final validation before submission
            let hasErrors = false;
            const requiredInputs = walkInForm.querySelectorAll("input[required], select[required]");
            
            // Validate all fields first
            requiredInputs.forEach(input => {
                if (input.type === "file") {
                    validateFile(input);
                } else {
                    validateInput(input);
                }
                
                if (input.classList.contains("error") || (input.type !== "file" && !input.value.trim()) || (input.type === "file" && input.files.length === 0)) {
                    hasErrors = true;
                }
            });
            
            if (hasErrors) {
                Swal.fire({
                    icon: "error",
                    title: "Validation Error",
                    text: "Please complete all required fields and fix any errors before submitting.",
                });
                return;
            }
            
            // Show confirmation dialog
            Swal.fire({
                title: "Submit Walk-in Information?",
                text: "This information will be recorded but NOT saved to the database. Continue?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#6d53d3",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable UI and show spinner
                    submitBtn.disabled = true;
                    submitBtnText.textContent = 'Submitting...';
                    submitBtnSpinner.classList.remove('hidden');
                    
                    // Show loading overlay
                    document.getElementById('loadingOverlay').style.display = 'flex';
                    
                    // Submit the form
                    walkInForm.submit();
                }
            });
        });
        
        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeEventListeners();
            toggleButton();
            
            // Set current academic year
            const currentYear = new Date().getFullYear();
            const acadYearInput = document.getElementById('acad_year');
            if (acadYearInput && !acadYearInput.value) {
                acadYearInput.value = `${currentYear}-${currentYear + 1}`;
            }
            
            // Handle school name "Others" option on page load
            const schoolSelect = document.getElementById('school_name');
            const schoolOtherInput = document.getElementById('school_name_other');
            if (schoolSelect && schoolSelect.value === 'Others') {
                schoolOtherInput.style.display = "block";
                schoolOtherInput.setAttribute("required", "required");
            }
            
            // Restore old input values for validation
            const inputs = walkInForm.querySelectorAll("input, select");
            inputs.forEach(input => {
                if (input.value) {
                    if (input.type === "file") {
                        // File inputs can't restore values
                    } else {
                        validateInput(input);
                    }
                }
            });
        });
    </script>
</body>
</html>
