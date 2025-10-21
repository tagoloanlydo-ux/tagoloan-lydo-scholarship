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
                            <a href="{{ route('scholar.renewal_app') }}" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
                                <i class="bx bx-refresh text-center mx-auto md:mx-0 text-xl"></i>
                                <span class="ml-4 hidden md:block text-lg">Renewal</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('scholar.settings') }}" class="flex items-center justify-between p-3 rounded-lg text-white bg-violet-600 hover:bg-violet-700">
                                <div class="flex items-center">
                                    <i class="bx bxs-cog text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Settings</span>
                                </div>
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
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fa-solid fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    @csrf
                    <!-- Tab Navigation -->
                    <div class="flex border-b border-gray-200 mb-6">
                        <button id="personal-tab" class="tab-button active px-6 py-3 text-violet-600 border-b-2 border-violet-600 font-medium">Personal Information</button>
                        <button id="password-tab" class="tab-button px-6 py-3 text-gray-500 hover:text-violet-600 font-medium">Change Password</button>
                    </div>

                    <!-- Personal Information Tab -->
                    <div id="personal-content" class="tab-content">
                        <h2 class="text-2xl font-bold text-violet-700 mb-6 flex items-center">
                            <i class="fas fa-user-circle mr-3"></i>Personal Information
                        </h2>
                        <!-- Row 1: First Name, Middle Name, Last Name, Suffix -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3 top-4 text-gray-400"></i>
                                    <input type="text" name="applicant_fname" value="{{ old('applicant_fname', $scholar->applicant->applicant_fname) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3 top-4 text-gray-400"></i>
                                    <input type="text" name="applicant_mname" value="{{ old('applicant_mname', $scholar->applicant->applicant_mname) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3 top-4 text-gray-400"></i>
                                    <input type="text" name="applicant_lname" value="{{ old('applicant_lname', $scholar->applicant->applicant_lname) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Suffix</label>
                                <div class="relative">
                                    <i class="fas fa-tag absolute left-3 top-4 text-gray-400"></i>
                                    <input type="text" name="applicant_suffix" value="{{ old('applicant_suffix', $scholar->applicant->applicant_suffix) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Gender, Birth Date, Civil Status -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <div class="relative">
                                    <i class="fas fa-venus-mars absolute left-3 top-4 text-gray-400"></i>
                                    <select name="applicant_gender" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                        <option value="male" {{ old('applicant_gender', $scholar->applicant->applicant_gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('applicant_gender', $scholar->applicant->applicant_gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('applicant_gender', $scholar->applicant->applicant_gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Birth Date</label>
                                <div class="relative">
                                    <i class="fas fa-calendar-alt absolute left-3 top-4 text-gray-400"></i>
                                    <input type="date" name="applicant_bdate" value="{{ old('applicant_bdate', $scholar->applicant->applicant_bdate) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Civil Status</label>
                                <div class="relative">
                                    <i class="fas fa-heart absolute left-3 top-4 text-gray-400"></i>
                                    <select name="applicant_civil_status" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                        <option value="single" {{ old('applicant_civil_status', $scholar->applicant->applicant_civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('applicant_civil_status', $scholar->applicant->applicant_civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="widowed" {{ old('applicant_civil_status', $scholar->applicant->applicant_civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="divorced" {{ old('applicant_civil_status', $scholar->applicant->applicant_civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Barangay, Email, Contact Number -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Barangay</label>
                                <div class="relative">
                                    <i class="fas fa-map-marker-alt absolute left-3 top-4 text-gray-400"></i>
                                    <input type="text" name="applicant_brgy" value="{{ old('applicant_brgy', $scholar->applicant->applicant_brgy) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-3 top-4 text-gray-400"></i>
                                    <input type="email" name="applicant_email" value="{{ old('applicant_email', $scholar->applicant->applicant_email) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                                <div class="relative">
                                    <i class="fas fa-phone absolute left-3 top-4 text-gray-400"></i>
                                    <input type="text" name="applicant_contact_number" value="{{ old('applicant_contact_number', $scholar->applicant->applicant_contact_number) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4: School Name, Year Level, Academic Year -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">School Name</label>
                                <div class="relative">
                                    <i class="fas fa-school absolute left-3 top-4 text-gray-400"></i>
                                    <input type="text" name="applicant_school_name" value="{{ old('applicant_school_name', $scholar->applicant->applicant_school_name) }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Year Level</label>
                                <div class="relative">
                                    <i class="fas fa-graduation-cap absolute left-3 top-3 text-gray-400"></i>
                                    <input type="text" value="{{ $scholar->applicant->applicant_year_level }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm bg-gray-100" readonly>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                                <div class="relative">
                                    <i class="fas fa-calendar absolute left-3 top-4 text-gray-400"></i>
                                    <input type="text" value="{{ $scholar->applicant->applicant_acad_year }}" class="pl-10 pr-4 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm bg-gray-100" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div id="password-content" class="tab-content hidden">
                        <h2 class="text-2xl font-bold text-violet-700 mb-6 flex items-center">
                            <i class="fas fa-lock mr-3"></i>Change Password
                        </h2>
                        <!-- Row 1: Old Password -->
                        <div class="mb-6">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-3 top-4 text-gray-400"></i>
                                    <input type="password" name="current_password" id="current_password" class="pl-10 pr-12 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                    <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-4 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="current-password-toggle-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: New Password and Confirm New Password -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <div class="relative">
                                    <i class="fas fa-key absolute left-3 top-4 text-gray-400"></i>
                                    <input type="password" name="password" id="password" class="pl-10 pr-12 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                    <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-4 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="password-toggle-icon"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <div class="relative">
                                    <i class="fas fa-key absolute left-3 top-4 text-gray-400"></i>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="pl-10 pr-12 py-3 h-12 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" required>
                                    <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-4 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="password-confirmation-toggle-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-violet-600 to-purple-600 text-white px-8 py-4 rounded-xl hover:from-violet-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-medium">
                            <i class="fas fa-save mr-2"></i>Update Settings
                        </button>
                    </div>
                </div>

                <script>
                    // Tab functionality
                    document.getElementById('personal-tab').addEventListener('click', function() {
                        document.getElementById('personal-content').classList.remove('hidden');
                        document.getElementById('password-content').classList.add('hidden');
                        document.getElementById('personal-tab').classList.add('text-violet-600', 'border-b-2', 'border-violet-600');
                        document.getElementById('password-tab').classList.remove('text-violet-600', 'border-b-2', 'border-violet-600');
                    });

                    document.getElementById('password-tab').addEventListener('click', function() {
                        document.getElementById('password-content').classList.remove('hidden');
                        document.getElementById('personal-content').classList.add('hidden');
                        document.getElementById('password-tab').classList.add('text-violet-600', 'border-b-2', 'border-violet-600');
                        document.getElementById('personal-tab').classList.remove('text-violet-600', 'border-b-2', 'border-violet-600');
                    });

                    // Password toggle functionality
                    function togglePassword(fieldId) {
                        const passwordField = document.getElementById(fieldId);
                        const toggleIcon = document.getElementById(fieldId + '-toggle-icon');

                        if (passwordField.type === 'password') {
                            passwordField.type = 'text';
                            toggleIcon.classList.remove('fa-eye');
                            toggleIcon.classList.add('fa-eye-slash');
                        } else {
                            passwordField.type = 'password';
                            toggleIcon.classList.remove('fa-eye-slash');
                            toggleIcon.classList.add('fa-eye');
                        }
                    }
                </script>
            </div>
        </div>
    </div>
</body>

</html>
