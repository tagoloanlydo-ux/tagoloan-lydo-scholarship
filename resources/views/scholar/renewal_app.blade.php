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
<body class="bg-gray-50">
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
                            <a href="{{ route('scholar.dashboard') }}" class=" flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bxs-dashboard text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scholar.renewal_app') }}" class="flex items-center justify-between p-3 rounded-lg text-white bg-violet-600 hover:bg-violet-700">
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
                    <button type="submit" class="flex items-center p-2 text-red-600 hover:bg-violet-600 hover:text-white rounded-lg w-full text-left transition duration-200">
                        <i class="fas fa-sign-out-alt mx-auto md:mx-0 md:mr-2 text-red-600 hover:text-white"></i>
                        <span class="hidden md:block text-red-600 hover:text-white">Logout</span>
                    </button>
                </form>
            </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 md:p-6">
                <!-- Greeting and Application Section -->
                <div class="max-w-6xl mx-auto">
                    <div class="bg-white p-8 rounded-2xl shadow-lg mb-8">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-violet-700 mb-4">Welcome to Scholarship Renewal</h2>
                            <p class="text-gray-600 text-lg">We're excited to help you continue your scholarship journey. Please submit your renewal application with the required documents.</p>
                        </div>

                    <div class="text-center">
           @php
    $now = now();
    $isWithinRenewalPeriod = true;
    $deadlineMessage = '';

    if ($settings && $settings->renewal_deadline && $now->isAfter($settings->renewal_deadline)) {
        $isWithinRenewalPeriod = false;
        $deadlineMessage = 'Renewal submission deadline has passed.';
    } elseif ($settings && $settings->renewal_start_date && $now->isBefore($settings->renewal_start_date)) {
        $isWithinRenewalPeriod = false;
        $deadlineMessage = 'Renewal submission has not started yet.';
    }

    // Check if scholar has approved renewal for current academic year
    $hasApprovedRenewal = $approvedRenewalExists ?? false;
    
    // UPDATED LOGIC: Can submit renewal only if:
    // 1. Within renewal period AND
    // 2. No approved renewal exists AND  
    // 3. Scholar is eligible to renew (not in their starting academic year)
    $canSubmitRenewal = $isWithinRenewalPeriod && !$hasApprovedRenewal && ($canRenewForNextYear ?? false);
    
    // Check if there are bad documents
    $hasBadDocuments = count(array_filter($badDocuments ?? [])) > 0;
@endphp

@if(!$canRenewForNextYear && !$hasApprovedRenewal)
    <div class="mb-4">
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-4">
            <i class="fa-solid fa-info-circle mr-2"></i>
            Renewal is not available for your starting academic year.
            @php
                $applicantStartYear = session('scholar')->applicant->applicant_acad_year ?? 'N/A';
                $currentYear = now()->year;
                $currentMonth = now()->month;
                $currentAcademicYear = $currentMonth >= 6 
                    ? $currentYear . '-' . ($currentYear + 1) 
                    : ($currentYear - 1) . '-' . $currentYear;
            @endphp
            <br><small>Your starting academic year: {{ $applicantStartYear }}</small>
            <br><small>Current academic year: {{ $currentAcademicYear }}</small>
        </div>
    </div>
@endif
                        @if(!$isWithinRenewalPeriod)
                            <div class="mb-4">
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                                    <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                                    {{ $deadlineMessage }}
                                    @if($settings && $settings->renewal_start_date)
                                        <br><small>Renewal period starts: {{ $settings->renewal_start_date->format('M d, Y') }}</small>
                                    @endif
                                    @if($settings && $settings->renewal_deadline)
                                        <br><small>Deadline: {{ $settings->renewal_deadline->format('M d, Y') }}</small>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($hasApprovedRenewal)
                            <div class="mb-4">
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                                    <i class="fa-solid fa-check-circle mr-2"></i>
                                    Congratulations! Your renewal application for this academic year has been approved.
                                </div>
                            </div>
                            <button disabled class="bg-gray-400 cursor-not-allowed text-white font-bold py-4 px-8 rounded-xl shadow-lg">
                                <i class="fa-solid fa-check-circle mr-2"></i>
                                Renewal Approved
                            </button>
                        @elseif($renewal && $renewal->renewal_status == 'Approved')
                            <div class="mb-4">
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                                    <i class="fa-solid fa-check-circle mr-2"></i>
                                    Congratulations! Your renewal application has been approved.
                                </div>
                            </div>
                            @if($canSubmitRenewal)
                                <button onclick="openModal()" id="renewalButton" class="bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 px-8 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg">
                                    <i class="fa-solid fa-plus-circle mr-2"></i>
                                    Apply for Renewal
                                </button>
                            @else
                                <button disabled class="bg-gray-400 cursor-not-allowed text-white font-bold py-4 px-8 rounded-xl shadow-lg">
                                    <i class="fa-solid fa-plus-circle mr-2"></i>
                                    Apply for Renewal
                                </button>
                            @endif
                        @elseif($renewal)
                            <!-- Show red button if there are bad documents -->
                            @if($hasBadDocuments)
                                <div class="mb-4">
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                                        <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                                        Some documents need to be updated. Please click the button below to fix them.
                                    </div>
                                </div>
                                <button onclick="openModal()" id="renewalButton" class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg">
                                    <i class="fa-solid fa-exclamation-circle mr-2"></i>
                                    Update Required Documents
                                </button>
                            @else
                                @if($canSubmitRenewal)
                                    <button onclick="openModal()" id="renewalButton" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-4 px-8 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg">
                                        <i class="fa-solid fa-edit mr-2"></i>
                                        Your Application Is Pending
                                    </button>
                                @else
                                    <button disabled class="bg-gray-400 cursor-not-allowed text-white font-bold py-4 px-8 rounded-xl shadow-lg">
                                        <i class="fa-solid fa-edit mr-2"></i>
                                        Your Application Is Pending
                                    </button>
                                @endif
                            @endif
                        @else
                            @if($canSubmitRenewal)
                                <button onclick="openModal()" id="renewalButton" class="bg-violet-600 hover:bg-violet-700 text-white font-bold py-4 px-8 rounded-xl transition duration-300 transform hover:scale-105 shadow-lg">
                                    <i class="fa-solid fa-plus-circle mr-2"></i>
                                    Apply for Renewal
                                </button>
                            @else
                                <button disabled class="bg-gray-400 cursor-not-allowed text-white font-bold py-4 px-8 rounded-xl shadow-lg">
                                    <i class="fa-solid fa-plus-circle mr-2"></i>
                                    Apply for Renewal
                                </button>
                            @endif
                        @endif
                    </div>
                    </div>

                    <!-- Requirements Section -->
                    <div class="bg-white p-6 rounded-2xl shadow">
                        <h3 class="text-xl font-semibold text-violet-700 mb-4">Renewal Requirements</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-center">
                                <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                                Certificate of Registration
                            </li>
                            <li class="flex items-center">
                                <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                                Grade Slip
                            </li>
                            <li class="flex items-center">
                                <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                                Barangay Indigency
                            </li>
                            <li class="flex items-center">
                                <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                                Current Semester and Academic Year
                            </li>
                            <li class="flex items-center">
                                <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                                Updated Year Level
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for Renewal Application Form -->
<div id="renewalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-violet-700">Renewal Application Form</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition duration-200">
                        <i class="fa-solid fa-times text-2xl"></i>
                    </button>
                </div>
                
                <!-- Bad Documents Warning -->
                @php
                    $hasBadDocuments = count(array_filter($badDocuments ?? [])) > 0;
                @endphp
                
                @if($renewal && $hasBadDocuments)
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fa-solid fa-exclamation-triangle text-red-500 mr-2"></i>
                        <span class="text-red-700 font-semibold">The following documents need to be updated:</span>
                    </div>
                    <div class="mt-2 space-y-1">
                        @if($badDocuments['renewal_cert_of_reg'])
                        <div class="flex items-center text-red-600">
                            <i class="fa-solid fa-file-pdf mr-2"></i>
                            <span>Certificate of Registration - Needs Update</span>
                        </div>
                        @endif
                        @if($badDocuments['renewal_grade_slip'])
                        <div class="flex items-center text-red-600">
                            <i class="fa-solid fa-file-pdf mr-2"></i>
                            <span>Grade Slip - Needs Update</span>
                        </div>
                        @endif
                        @if($badDocuments['renewal_brgy_indigency'])
                        <div class="flex items-center text-red-600">
                            <i class="fa-solid fa-file-pdf mr-2"></i>
                            <span>Barangay Indigency - Needs Update</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <form method="POST" action="{{ route('scholar.submit_renewal') }}" enctype="multipart/form-data" class="p-8 space-y-8" id="renewalForm">
                @csrf

                <!-- Hidden renewal_id for updates -->
                @if($renewal)
                    <input type="hidden" name="renewal_id" value="{{ $renewal->renewal_id }}">
                @endif

                <!-- First Row: Semester, Academic Year, Year Level -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Semester -->
                    <div>
                        <label for="renewal_semester" class="block text-sm font-semibold text-gray-800 mb-2">Semester</label>
                        <input type="text" name="renewal_semester" id="renewal_semester" required readonly
                            value="{{ $settings->renewal_semester ?? '1st Semester' }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed shadow-sm">
                    </div>

                    <!-- Academic Year -->
                    <div>
                        <label for="renewal_acad_year" class="block text-sm font-semibold text-gray-800 mb-2">Academic Year</label>
                        <input type="text" name="renewal_acad_year" id="renewal_acad_year" required readonly
                               value="{{ $renewal ? $renewal->renewal_acad_year : '' }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed shadow-sm">
                    </div>

                    <!-- Year Level -->
                    <div>
                        <label for="applicant_year_level" class="block text-sm font-semibold text-gray-800 mb-2">Year Level</label>
                        <input type="text" name="applicant_year_level" id="applicant_year_level" required readonly
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed shadow-sm">
                    </div>
                </div>

                <!-- Second Row: Certificate of Registration, Grade Slip, Barangay Indigency -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Certificate of Registration -->
                    <div id="cert_of_reg_container">
                        <label for="renewal_cert_of_reg" class="block text-sm font-semibold text-gray-800 mb-2">
                            Certificate of Registration
                            @if($renewal && $badDocuments['renewal_cert_of_reg'] ?? false)
                                <span class="text-red-600 font-semibold">(Required - Document needs update)</span>
                            @elseif($renewal)
                                <span class="text-sm text-gray-500">(Optional - upload new file to replace existing)</span>
                            @endif
                        </label>
                        <input type="file" name="renewal_cert_of_reg" id="renewal_cert_of_reg" accept=".pdf" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 shadow-sm {{ ($renewal && ($badDocuments['renewal_cert_of_reg'] ?? false)) ? 'border-red-300 bg-red-50' : '' }}"
                               {{ (!$renewal || ($badDocuments['renewal_cert_of_reg'] ?? false)) ? 'required' : '' }}>
                        <div id="cert_of_reg_error" class="text-red-500 text-sm mt-1 hidden">File size must not exceed 5MB.</div>
                    </div>

                    <!-- Grade Slip -->
                    <div id="grade_slip_container">
                        <label for="renewal_grade_slip" class="block text-sm font-semibold text-gray-800 mb-2">
                            Grade Slip
                            @if($renewal && $badDocuments['renewal_grade_slip'] ?? false)
                                <span class="text-red-600 font-semibold">(Required - Document needs update)</span>
                            @elseif($renewal)
                                <span class="text-sm text-gray-500">(Optional - upload new file to replace existing)</span>
                            @endif
                        </label>
                        <input type="file" name="renewal_grade_slip" id="renewal_grade_slip" accept=".pdf" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 shadow-sm {{ ($renewal && ($badDocuments['renewal_grade_slip'] ?? false)) ? 'border-red-300 bg-red-50' : '' }}"
                               {{ (!$renewal || ($badDocuments['renewal_grade_slip'] ?? false)) ? 'required' : '' }}>
                        <div id="grade_slip_error" class="text-red-500 text-sm mt-1 hidden">File size must not exceed 5MB.</div>
                    </div>

                    <!-- Barangay Indigency -->
                    <div id="brgy_indigency_container">
                        <label for="renewal_brgy_indigency" class="block text-sm font-semibold text-gray-800 mb-2">
                            Barangay Indigency
                            @if($renewal && $badDocuments['renewal_brgy_indigency'] ?? false)
                                <span class="text-red-600 font-semibold">(Required - Document needs update)</span>
                            @elseif($renewal)
                                <span class="text-sm text-gray-500">(Optional - upload new file to replace existing)</span>
                            @endif
                        </label>
                        <input type="file" name="renewal_brgy_indigency" id="renewal_brgy_indigency" accept=".pdf" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 shadow-sm {{ ($renewal && ($badDocuments['renewal_brgy_indigency'] ?? false)) ? 'border-red-300 bg-red-50' : '' }}"
                               {{ (!$renewal || ($badDocuments['renewal_brgy_indigency'] ?? false)) ? 'required' : '' }}>
                        <div id="brgy_indigency_error" class="text-red-500 text-sm mt-1 hidden">File size must not exceed 5MB.</div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal()" class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                        Cancel
                    </button>
                    <button type="submit" id="submitButton" class="px-8 py-3 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition font-semibold">
                        @if($renewal && $hasBadDocuments)
                            Update Required Documents
                        @elseif($renewal)
                            Update Application
                        @else
                            Submit Application
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Global variable to track bad documents
    let badDocuments = @json($badDocuments ?? []);
    let hasBadDocuments = Object.values(badDocuments).some(status => status);
    const renewalExists = {{ $renewal ? 'true' : 'false' }};

function openModal() {
    // Check if scholar has approved renewal for current academic year
    const hasApprovedRenewal = {{ $hasApprovedRenewal ? 'true' : 'false' }};
    const canSubmitRenewal = {{ $canSubmitRenewal ? 'true' : 'false' }};
    const canRenewForNextYear = {{ $canRenewForNextYear ? 'true' : 'false' }};
    
    if (hasApprovedRenewal) {
        Swal.fire({
            title: 'Renewal Already Approved',
            text: 'You already have an approved renewal application for this academic year.',
            icon: 'info',
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (!canRenewForNextYear) {
        Swal.fire({
            title: 'Renewal Not Available',
            text: 'Renewal is not available for your starting academic year. You can submit renewal starting next academic year.',
            icon: 'info',
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'OK'
        });
        return;
    }

    if (!canSubmitRenewal) {
        Swal.fire({
            title: 'Renewal Not Available',
            text: '{{ $deadlineMessage }}',
            icon: 'warning',
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'OK'
        });
        return;
    }

    // If all checks pass, open the modal
    document.getElementById('renewalModal').classList.remove('hidden');
    setCurrentAcademicYearAndYearLevel();
    updateSubmitButton();
    updateDocumentFieldsVisibility();
}

    function closeModal() {
        document.getElementById('renewalModal').classList.add('hidden');
        resetFormValidation();
    }

    function updateDocumentFieldsVisibility() {
        // Always show the containers so users can open the file dialog and optionally upload files
        const certContainer = document.getElementById('cert_of_reg_container');
        const gradeContainer = document.getElementById('grade_slip_container');
        const brgyContainer = document.getElementById('brgy_indigency_container');

        ['cert_of_reg_container', 'grade_slip_container', 'brgy_indigency_container'].forEach(id => {
            const container = document.getElementById(id);
            if (container) {
                container.classList.remove('hidden');
                container.classList.remove('pointer-events-none'); // ensure clickable
            }
        });

        // Update required attributes and visual states depending on badDocuments flags
        const certInput = document.getElementById('renewal_cert_of_reg');
        const gradeInput = document.getElementById('renewal_grade_slip');
        const brgyInput = document.getElementById('renewal_brgy_indigency');

        if (certInput) {
            if (renewalExists) {
                certInput.required = !!badDocuments.renewal_cert_of_reg;
            } else {
                certInput.required = true;
            }
            certInput.classList.toggle('border-red-300', !!badDocuments.renewal_cert_of_reg);
            certInput.classList.toggle('bg-red-50', !!badDocuments.renewal_cert_of_reg);
            certInput.style.pointerEvents = 'auto';
            certInput.style.zIndex = 10;
        }

        if (gradeInput) {
            if (renewalExists) {
                gradeInput.required = !!badDocuments.renewal_grade_slip;
            } else {
                gradeInput.required = true;
            }
            gradeInput.classList.toggle('border-red-300', !!badDocuments.renewal_grade_slip);
            gradeInput.classList.toggle('bg-red-50', !!badDocuments.renewal_grade_slip);
            gradeInput.style.pointerEvents = 'auto';
            gradeInput.style.zIndex = 10;
        }

        if (brgyInput) {
            if (renewalExists) {
                brgyInput.required = !!badDocuments.renewal_brgy_indigency;
            } else {
                brgyInput.required = true;
            }
            brgyInput.classList.toggle('border-red-300', !!badDocuments.renewal_brgy_indigency);
            brgyInput.classList.toggle('bg-red-50', !!badDocuments.renewal_brgy_indigency);
            brgyInput.style.pointerEvents = 'auto';
            brgyInput.style.zIndex = 10;
        }

        // Ensure the container click delegates to the file input so any click opens the picker
        ensureFileContainersClickable();

        // Update submit button and validation state
        updateSubmitButton();
    }

    // Delegates container clicks to file inputs and ensures pointer-events enabled
    function ensureFileContainersClickable() {
        const mappings = [
            { containerId: 'cert_of_reg_container', inputId: 'renewal_cert_of_reg' },
            { containerId: 'grade_slip_container', inputId: 'renewal_grade_slip' },
            { containerId: 'brgy_indigency_container', inputId: 'renewal_brgy_indigency' }
        ];

        mappings.forEach(({ containerId, inputId }) => {
            const container = document.getElementById(containerId);
            const input = document.getElementById(inputId);
            if (!container || !input) return;

            // Make sure the container is interactive
            container.classList.remove('pointer-events-none');
            container.style.pointerEvents = 'auto';
            container.style.cursor = 'pointer';

            // Ensure input is interactive and above any overlay
            input.style.pointerEvents = 'auto';
            input.style.zIndex = 10;

            // Attach one click listener on the container to open file picker
            if (!input.dataset.clickAttached) {
                container.addEventListener('click', function (e) {
                    // If clicking the input directly, do nothing (native behavior)
                    if (e.target === input) return;
                    // Open the file picker
                    input.click();
                });
                input.dataset.clickAttached = '1';
            }
        });
    }

    // ...existing code...
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('renewalModal');
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Ensure file containers are clickable on load
        ensureFileContainersClickable();
    });

    // Logout confirmation
    document.getElementById('logoutForm')?.addEventListener('submit', function(e) {
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

    // Show success message if exists
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#7c3aed',
            confirmButtonText: 'OK'
        });
    @endif
</script>
<script>
// Simple deadline checker - add this to your existing script
function checkRenewalDeadline() {
    const renewalButton = document.querySelector('#renewalButton, button[onclick*="openModal"]');
    if (!renewalButton || renewalButton.disabled) return;
    
    const now = new Date();
    const renewalDeadline = new Date('{{ $settings->renewal_deadline ?? null }}');
    const renewalStart = new Date('{{ $settings->renewal_start_date ?? null }}');
    
    // Check if we're outside the renewal period
    if ((renewalDeadline && now > renewalDeadline) || (renewalStart && now < renewalStart)) {
        renewalButton.disabled = true;
        renewalButton.classList.remove('bg-violet-600', 'bg-yellow-600', 'bg-red-600', 'hover:bg-violet-700', 'hover:bg-yellow-700', 'hover:bg-red-700');
        renewalButton.classList.add('bg-gray-400', 'cursor-not-allowed');
        renewalButton.innerHTML = '<i class="fa-solid fa-plus-circle mr-2"></i>Apply for Renewal';
    }
}

// Check every 2 seconds
setInterval(checkRenewalDeadline, 2000);
</script>
</body>
</html>