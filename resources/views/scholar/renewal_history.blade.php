<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renewal History - Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>

<body class="bg-gray-50">
    <div class="dashboard-grid">
        <header class="bg-gradient-to-r from-[#4c1d95] to-[#7e22ce] shadow-sm p-4 flex justify-between items-center font-sans">
            <div class="flex items-center">
                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg">
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
                            <a href="{{ route('scholar.dashboard') }}" class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-violet-600 hover:text-white">
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
                            <a href="{{ route('scholar.renewal_history') }}" class="flex items-center p-3 rounded-lg text-white bg-violet-600 hover:bg-violet-700">
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
                <!-- Page Header -->
                <div class="max-w-6xl mx-auto">
                    <div class="bg-white p-8 rounded-2xl shadow-lg mb-8">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-violet-700 mb-4">Renewal History</h2>
                            <p class="text-gray-600 text-lg">View all your submitted renewal applications across different academic years and semesters.</p>
                        </div>
                    </div>

                    <!-- Renewal History Table -->
                    <div class="bg-white p-6 rounded-2xl shadow">
                        <h3 class="text-xl font-semibold text-violet-700 mb-4">Your Renewal Applications</h3>
                        
                        @if($renewals->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead>
                                        <tr class="bg-gray-50 border-b">
                                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Academic Year</th>
                                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Semester</th>
                                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Year Level</th>
                                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Date Submitted</th>
                                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                            <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($renewals as $renewal)
                                            <tr class="border-b hover:bg-gray-50 transition duration-200">
                                                <td class="py-4 px-4 text-sm text-gray-800">{{ $renewal->renewal_acad_year }}</td>
                                                <td class="py-4 px-4 text-sm text-gray-800">{{ $renewal->renewal_semester }}</td>
                                                <td class="py-4 px-4 text-sm text-gray-800">{{ $renewal->applicant_year_level ?? 'N/A' }}</td>
                                                <td class="py-4 px-4 text-sm text-gray-800">{{ $renewal->date_submitted->format('M d, Y') }}</td>
                                                <td class="py-4 px-4">
                                                    @if($renewal->renewal_status == 'Approved')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                            <i class="fa-solid fa-check-circle mr-1"></i>
                                                            Approved
                                                        </span>
                                                    @elseif($renewal->renewal_status == 'Rejected')
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                            <i class="fa-solid fa-times-circle mr-1"></i>
                                                            Rejected
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                            <i class="fa-solid fa-clock mr-1"></i>
                                                            Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-4">
                                                    <button onclick="viewRenewalDetails({{ $renewal->renewal_id }})" 
                                                            class="text-violet-600 hover:text-violet-800 transition duration-200">
                                                        <i class="fa-solid fa-eye mr-1"></i>
                                                        View Details
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $renewals->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fa-solid fa-file-alt text-6xl text-gray-300 mb-4"></i>
                                <h4 class="text-xl font-semibold text-gray-600 mb-2">No Renewal History Found</h4>
                                <p class="text-gray-500 mb-6">You haven't submitted any renewal applications yet.</p>
                                <a href="{{ route('scholar.renewal_app') }}" class="inline-flex items-center px-6 py-3 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition duration-300">
                                    <i class="fa-solid fa-plus-circle mr-2"></i>
                                    Submit Your First Renewal
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Renewal Details Modal -->
    <div id="renewalDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-violet-700">Renewal Application Details</h3>
                        <button onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 transition duration-200">
                            <i class="fa-solid fa-times text-2xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-8 space-y-6" id="renewalDetailsContent">
                    <!-- Details will be loaded here via JavaScript -->
                </div>
                
                <div class="p-6 border-t flex justify-end">
                    <button onclick="closeDetailsModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // View renewal details
        function viewRenewalDetails(renewalId) {
            // Show loading state
            document.getElementById('renewalDetailsContent').innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-violet-600"></div>
                </div>
            `;
            
            document.getElementById('renewalDetailsModal').classList.remove('hidden');
            
            // Fetch renewal details via AJAX
            fetch(`/scholar/renewal/${renewalId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('renewalDetailsContent').innerHTML = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Academic Information</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Academic Year</label>
                                            <p class="text-gray-800">${data.renewal.renewal_acad_year}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Semester</label>
                                            <p class="text-gray-800">${data.renewal.renewal_semester}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Year Level</label>
                                            <p class="text-gray-800">${data.renewal.applicant_year_level || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Application Details</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Date Submitted</label>
                                            <p class="text-gray-800">${new Date(data.renewal.date_submitted).toLocaleDateString('en-US', { 
                                                year: 'numeric', 
                                                month: 'long', 
                                                day: 'numeric' 
                                            })}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Status</label>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                                                data.renewal.renewal_status === 'Approved' ? 'bg-green-100 text-green-800' :
                                                data.renewal.renewal_status === 'Rejected' ? 'bg-red-100 text-red-800' :
                                                'bg-yellow-100 text-yellow-800'
                                            }">
                                                <i class="fa-solid ${
                                                    data.renewal.renewal_status === 'Approved' ? 'fa-check-circle' :
                                                    data.renewal.renewal_status === 'Rejected' ? 'fa-times-circle' :
                                                    'fa-clock'
                                                } mr-1"></i>
                                                ${data.renewal.renewal_status}
                                            </span>
                                        </div>
                                        ${data.renewal.remarks ? `
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600">Remarks</label>
                                            <p class="text-gray-800">${data.renewal.remarks}</p>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">Submitted Documents</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-gray-700">Certificate of Registration</span>
                                            <span class="text-xs px-2 py-1 rounded ${
                                                data.renewal.cert_of_reg_status === 'good' ? 'bg-green-100 text-green-800' :
                                                data.renewal.cert_of_reg_status === 'bad' ? 'bg-red-100 text-red-800' :
                                                'bg-gray-100 text-gray-800'
                                            }">
                                                ${data.renewal.cert_of_reg_status || 'Not reviewed'}
                                            </span>
                                        </div>
                                        ${data.renewal.renewal_cert_of_reg ? `
                                        <a href="/storage/${data.renewal.renewal_cert_of_reg}" target="_blank" 
                                           class="text-violet-600 hover:text-violet-800 text-sm flex items-center">
                                            <i class="fa-solid fa-file-pdf mr-1"></i> View Document
                                        </a>
                                        ` : '<p class="text-gray-500 text-sm">No document</p>'}
                                    </div>
                                    
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-gray-700">Grade Slip</span>
                                            <span class="text-xs px-2 py-1 rounded ${
                                                data.renewal.grade_slip_status === 'good' ? 'bg-green-100 text-green-800' :
                                                data.renewal.grade_slip_status === 'bad' ? 'bg-red-100 text-red-800' :
                                                'bg-gray-100 text-gray-800'
                                            }">
                                                ${data.renewal.grade_slip_status || 'Not reviewed'}
                                            </span>
                                        </div>
                                        ${data.renewal.renewal_grade_slip ? `
                                        <a href="/storage/${data.renewal.renewal_grade_slip}" target="_blank" 
                                           class="text-violet-600 hover:text-violet-800 text-sm flex items-center">
                                            <i class="fa-solid fa-file-pdf mr-1"></i> View Document
                                        </a>
                                        ` : '<p class="text-gray-500 text-sm">No document</p>'}
                                    </div>
                                    
                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-gray-700">Barangay Indigency</span>
                                            <span class="text-xs px-2 py-1 rounded ${
                                                data.renewal.brgy_indigency_status === 'good' ? 'bg-green-100 text-green-800' :
                                                data.renewal.brgy_indigency_status === 'bad' ? 'bg-red-100 text-red-800' :
                                                'bg-gray-100 text-gray-800'
                                            }">
                                                ${data.renewal.brgy_indigency_status || 'Not reviewed'}
                                            </span>
                                        </div>
                                        ${data.renewal.renewal_brgy_indigency ? `
                                        <a href="/storage/${data.renewal.renewal_brgy_indigency}" target="_blank" 
                                           class="text-violet-600 hover:text-violet-800 text-sm flex items-center">
                                            <i class="fa-solid fa-file-pdf mr-1"></i> View Document
                                        </a>
                                        ` : '<p class="text-gray-500 text-sm">No document</p>'}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('renewalDetailsContent').innerHTML = `
                            <div class="text-center py-8">
                                <i class="fa-solid fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                                <h4 class="text-xl font-semibold text-gray-700 mb-2">Error Loading Details</h4>
                                <p class="text-gray-600">Unable to load renewal details. Please try again.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('renewalDetailsContent').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fa-solid fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                            <h4 class="text-xl font-semibold text-gray-700 mb-2">Error Loading Details</h4>
                            <p class="text-gray-600">Unable to load renewal details. Please try again.</p>
                        </div>
                    `;
                });
        }

        // Close details modal
        function closeDetailsModal() {
            document.getElementById('renewalDetailsModal').classList.add('hidden');
        }

        // Close modal on outside click and escape key
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('renewalDetailsModal');
            
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeDetailsModal();
                }
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeDetailsModal();
                }
            });
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
    </script>
</body>
</html>