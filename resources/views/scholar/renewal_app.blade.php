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
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
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
                            $canSubmitRenewal = $isWithinRenewalPeriod && !$hasApprovedRenewal;
                        @endphp

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
                </div>

                <form method="POST" action="{{ route('scholar.submit_renewal') }}" enctype="multipart/form-data" class="p-8 space-y-8">
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
                        <div>
                            <label for="renewal_cert_of_reg" class="block text-sm font-semibold text-gray-800 mb-2">
                                Certificate of Registration
                                @if($renewal)
                                    <span class="text-sm text-gray-500">(Optional - upload new file to replace existing)</span>
                                @endif
                            </label>
                            <input type="file" name="renewal_cert_of_reg" id="renewal_cert_of_reg" accept=".pdf" {{ $renewal ? '' : 'required' }}
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 shadow-sm">
                            <div id="cert_of_reg_error" class="text-red-500 text-sm mt-1 hidden">File size must not exceed 5MB.</div>
                        </div>

                        <!-- Grade Slip -->
                        <div>
                            <label for="renewal_grade_slip" class="block text-sm font-semibold text-gray-800 mb-2">
                                Grade Slip
                                @if($renewal)
                                    <span class="text-sm text-gray-500">(Optional - upload new file to replace existing)</span>
                                @endif
                            </label>
                            <input type="file" name="renewal_grade_slip" id="renewal_grade_slip" accept=".pdf" {{ $renewal ? '' : 'required' }}
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 shadow-sm">
                            <div id="grade_slip_error" class="text-red-500 text-sm mt-1 hidden">File size must not exceed 5MB.</div>
                        </div>

                        <!-- Barangay Indigency -->
                        <div>
                            <label for="renewal_brgy_indigency" class="block text-sm font-semibold text-gray-800 mb-2">
                                Barangay Indigency
                                @if($renewal)
                                    <span class="text-sm text-gray-500">(Optional - upload new file to replace existing)</span>
                                @endif
                            </label>
                            <input type="file" name="renewal_brgy_indigency" id="renewal_brgy_indigency" accept=".pdf" {{ $renewal ? '' : 'required' }}
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 shadow-sm">
                            <div id="brgy_indigency_error" class="text-red-500 text-sm mt-1 hidden">File size must not exceed 5MB.</div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeModal()" class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                            Cancel
                        </button>
                        <button type="submit" id="submitButton" class="px-8 py-3 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition font-semibold">
                            @if($renewal)
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
        // SweetAlert confirmation before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent immediate submission

            const isUpdate = {{ $renewal ? 'true' : 'false' }};
            const confirmTitle = isUpdate ? 'Update Renewal Application?' : 'Submit Renewal Application?';
            const confirmText = isUpdate
                ? 'Are you sure you want to update your renewal application? This will replace any existing files you upload.'
                : 'Are you sure you want to submit your renewal application? Please ensure all information is correct.';

            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#6b7280',
                confirmButtonText: isUpdate ? 'Yes, Update' : 'Yes, Submit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    const submitButton = document.querySelector('button[type="submit"]');
                    const originalText = submitButton.textContent;
                    submitButton.textContent = 'Processing...';
                    submitButton.disabled = true;

                    // Submit the form
                    this.submit();
                }
            });
        });

        // After successful submission, change button to yellow with "Pending"
        document.addEventListener('DOMContentLoaded', function() {
            const renewalExists = {{ $renewal ? 'true' : 'false' }};
            const renewalStatus = '{{ $renewal ? $renewal->renewal_status : '' }}';

            if (renewalExists) {
                const btn = document.getElementById('renewalButton');

                // If status is approved, show "Apply for Renewal" with blue color
                if (renewalStatus === 'Approved') {
                    btn.innerHTML = '<i class="fa-solid fa-plus-circle mr-2"></i>Apply for Renewal';
                    btn.classList.remove('bg-violet-600', 'hover:bg-violet-700', 'bg-yellow-600', 'hover:bg-yellow-700');
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                } else {
                    // Otherwise, show "Your Application Is Pending" with yellow color
                    btn.textContent = 'Your Application Is Pending';
                    btn.classList.remove('bg-violet-600', 'hover:bg-violet-700');
                    btn.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                }
            }

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
        });
    </script>

    <script>
function openModal() {
    // Check if scholar has approved renewal for current academic year
    const hasApprovedRenewal = {{ $hasApprovedRenewal ? 'true' : 'false' }};
    const canSubmitRenewal = {{ $canSubmitRenewal ? 'true' : 'false' }};
    
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
    
    document.getElementById('renewalModal').classList.remove('hidden');
    setCurrentAcademicYearAndYearLevel();
}

function closeModal() {
    document.getElementById('renewalModal').classList.add('hidden');
    
    // Reset form validation states
    resetFormValidation();
}

// Reset form validation when closing modal
function resetFormValidation() {
    const errorDivs = document.querySelectorAll('[id$="_error"]');
    errorDivs.forEach(div => {
        div.classList.add('hidden');
    });
    
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.disabled = false;
    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
}
        // Set current academic year
        function setCurrentAcademicYear() {
            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth() + 1; // JavaScript months are 0-indexed

            let academicYear;
            if (currentMonth >= 6) { // June onwards
                academicYear = currentYear + '-' + (currentYear + 1);
            } else { // Before June
                academicYear = (currentYear - 1) + '-' + currentYear;
            }

            document.getElementById('renewal_acad_year').value = academicYear;
        }

        // File validation
        function validateFileSize(fileInput, errorDivId) {
            const file = fileInput.files[0];
            const errorDiv = document.getElementById(errorDivId);
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes

            if (file && file.size > maxSize) {
                errorDiv.classList.remove('hidden');
                return false;
            } else {
                errorDiv.classList.add('hidden');
                return true;
            }
        }

        // Check all validations and enable/disable submit button
        function checkFormValidation() {
            const isUpdate = {{ $renewal ? 'true' : 'false' }};
            const certOfRegValid = validateFileSize(document.getElementById('renewal_cert_of_reg'), 'cert_of_reg_error');
            const gradeSlipValid = validateFileSize(document.getElementById('renewal_grade_slip'), 'grade_slip_error');
            const brgyIndigencyValid = validateFileSize(document.getElementById('renewal_brgy_indigency'), 'brgy_indigency_error');

            const submitButton = document.querySelector('button[type="submit"]');

            let allValid;
            if (isUpdate) {
                // For updates, files are optional, so only validate if files are selected
                const certOfRegFile = document.getElementById('renewal_cert_of_reg').files[0];
                const gradeSlipFile = document.getElementById('renewal_grade_slip').files[0];
                const brgyIndigencyFile = document.getElementById('renewal_brgy_indigency').files[0];

                allValid = (!certOfRegFile || certOfRegValid) && (!gradeSlipFile || gradeSlipValid) && (!brgyIndigencyFile || brgyIndigencyValid);
            } else {
                // For new submissions, all files are required
                allValid = certOfRegValid && gradeSlipValid && brgyIndigencyValid;
            }

            if (allValid) {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Add event listeners to file inputs
        document.getElementById('renewal_cert_of_reg').addEventListener('change', function() {
            validateFileSize(this, 'cert_of_reg_error');
            checkFormValidation();
        });

        document.getElementById('renewal_grade_slip').addEventListener('change', function() {
            validateFileSize(this, 'grade_slip_error');
            checkFormValidation();
        });

        document.getElementById('renewal_brgy_indigency').addEventListener('change', function() {
            validateFileSize(this, 'brgy_indigency_error');
            checkFormValidation();
        });

        // Initial validation check
// Close modal when clicking outside or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('renewalModal');
    
    // Close when clicking outside modal content
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Close when pressing ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});

        // Handle form submission success and reset button text
        document.querySelector('form').addEventListener('submit', function(e) {
            // Store original form submission handler
            const originalSubmit = this.onsubmit;

            // Override onsubmit to reset button after successful submission
            this.onsubmit = function(event) {
                if (originalSubmit) {
                    originalSubmit.call(this, event);
                }

                // If form is successfully submitted, reset button text
                setTimeout(function() {
                    const renewalButton = document.getElementById('renewalButton');
                    if (renewalButton) {
                        renewalButton.innerHTML = '<i class="fa-solid fa-plus-circle mr-2"></i>Apply for Renewal';
                        renewalButton.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');
                        renewalButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    }
                }, 1000);
            };
        });
// Improved version with better year level calculation
function setCurrentAcademicYearAndYearLevel() {
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth() + 1;

    let academicYear;
    if (currentMonth >= 6) { // June onwards
        academicYear = currentYear + '-' + (currentYear + 1);
    } else { // Before June
        academicYear = (currentYear - 1) + '-' + currentYear;
    }

    document.getElementById('renewal_acad_year').value = academicYear;
    
    // Calculate year level
    calculateYearLevel(academicYear);
}

function calculateYearLevel(currentAcademicYear) {
    const startingAcademicYear = "{{ session('scholar')->applicant->applicant_acad_year }}";
    const startingYearLevel = "{{ session('scholar')->applicant->applicant_year_level }}";
    
    // Parse academic years
    const [startYear1, startYear2] = startingAcademicYear.split('-').map(Number);
    const [currentYear1, currentYear2] = currentAcademicYear.split('-').map(Number);
    
    // Calculate total years of study
    let yearsOfStudy;
    
    if (currentMonth >= 6) {
        // If current month is June or later, count from startYear1
        yearsOfStudy = currentYear1 - startYear1 + 1;
    } else {
        // If current month is before June, count from startYear1 but adjust
        yearsOfStudy = currentYear1 - startYear1;
    }
    
    // Map to year levels
    const yearLevelMap = {
        1: '1st Year',
        2: '2nd Year', 
        3: '3rd Year',
        4: '4th Year',
        5: '5th Year'
    };
    
    const yearLevel = yearLevelMap[yearsOfStudy] || '5th Year';
    document.getElementById('applicant_year_level').value = yearLevel;
}

// Calculate year level based on starting academic year
function calculateYearLevel(currentAcademicYear) {
    // Get the scholar's starting academic year from the database
    const startingAcademicYear = "{{ session('scholar')->applicant->applicant_acad_year ?? '2024-2025' }}";
    const startingYearLevel = "{{ session('scholar')->applicant->applicant_year_level ?? '1st Year' }}";
    
    // Extract years from academic year strings
    const startYear = parseInt(startingAcademicYear.split('-')[0]);
    const currentYear = parseInt(currentAcademicYear.split('-')[0]);
    
    // Calculate year difference
    const yearDifference = currentYear - startYear;
    
    // Map year levels
    const yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
    
    // Find starting index
    const startIndex = yearLevels.indexOf(startingYearLevel);
    
    if (startIndex !== -1) {
        const newIndex = startIndex + yearDifference;
        if (newIndex < yearLevels.length) {
            document.getElementById('applicant_year_level').value = yearLevels[newIndex];
        } else {
            // If beyond 5th year, show maximum
            document.getElementById('applicant_year_level').value = '5th Year';
        }
    } else {
        // Fallback calculation
        document.getElementById('applicant_year_level').value = yearLevels[yearDifference] || '2nd Year';
    }
}
    </script>
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
</body>

</html>
