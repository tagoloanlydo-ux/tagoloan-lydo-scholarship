<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
</head>

<body class="bg-gray-50">
    <div class="dashboard-grid">
        <!-- Header -->
        <header class="bg-violet-600 shadow-sm p-4 flex justify-between items-center font-sans">
                            <div class="flex items-center">
                                <img src="{{ asset('images/LYDO.png') }}" alt="Logo" class="h-10 w-auto rounded-lg ">
                                <h1 class="text-lg font-bold text-white ml-4">Lydo Scholarship</h1>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-white font-semibold">{{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }} | Lydo Admin</span>
                            <div class="relative">
                                <!-- 🔔 Bell Icon -->
                                <button id="notifBell" class="relative focus:outline-none">
                                    <i class="fas fa-bell text-white text-2xl cursor-pointer"></i>
                                    @if($notifications->count() > 0)
                                        <span id="notifCount"
                                            class="absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center">
                                            {{ $notifications->count() }}
                                        </span>
                                    @endif
                                </button>
                    <!-- 🔽 Dropdown -->
                            <div id="notifDropdown"
                        class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-white-200 z-50">
                        <div class="p-3 border-b font-semibold text-white-700">Notifications</div>
                        <ul class="max-h-60 overflow-y-auto">
                            @forelse($notifications as $notif)
                                <li class="px-4 py-2 hover:bg-white-50 text-base border-b">
                                    {{-- Application --}}
                                    @if($notif->type === 'application')
                                        <p class="font-medium 
                                            {{ $notif->status === 'Approved' ? 'text-green-600' : 'text-red-600' }}">
                                            📌 Application of {{ $notif->name }} was {{ $notif->status }}
                                        </p>
                                    @elseif($notif->type === 'renewal')
                                        <p class="font-medium 
                                            {{ $notif->status === 'Approved' ? 'text-green-600' : 'text-red-600' }}">
                                            🔄 Renewal of {{ $notif->name }} was {{ $notif->status }}
                                        </p>
                                    @endif

                                    {{-- Time ago --}}
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
                        <li >
                            <a href="/lydo_admin/applicants" 
                            class=" flex items-center justify-between p-3 rounded-lg text-white-700 hover:bg-violet-600 hover:text-white">
                                <div class="flex items-center">
                                    <i class="bx bxs-user text-center mx-auto md:mx-0 text-xl"></i>
                                    <span class="ml-4 hidden md:block text-lg">Applicants</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="/lydo_admin/announcement"
                            class="flex items-center justify-between p-3 rounded-lg text-white-700 bg-violet-600 text-white">
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
                    <form method="POST" action="{{ route('logout') }}" id="logoutForm"> 
                        @csrf 
                        <button type="submit" class="flex items-center p-2 text-red-600 text-lg hover:bg-violet-600 hover:text-white rounded-lg w-full text-left">
                            <i class="fas fa-sign-out-alt mx-auto md:mx-0 mr-2 text-red-600"></i>
                            <span class="hidden md:block text-red-600">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            <div class="flex-1 overflow-hidden p-4 md:p-5 text-[16px]">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Announcements</h2>
                    <button onclick="openModal()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Create Announcement
                    </button>
                </div>

                <!-- Existing Announcements Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Existing Announcements</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse text-[17px] shadow-lg  border border-gray-200">
                            <thead class="bg-gradient-to-r from-green-600 to-teal-600 text-white uppercase text-sm">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider border border-gray-200">Title</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider border border-gray-200">Content</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider border border-gray-200">Type</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider border border-gray-200">Date Posted</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider border border-gray-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($announcements as $announcement)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="font-medium text-gray-900">{{ $announcement->announce_title }}</span>
                                        </td>
                                        <td class="px-6 py-4 border border-gray-200 max-w-xs">
                                            <button onclick="openContentModal('{{ addslashes($announcement->announce_title) }}', `{{ str_replace(['`', '$'], ['\`', '\$'], $announcement->announce_content) }}`)"
                                                    class="content-preview-btn text-sm text-blue-600 hover:text-blue-800 underline break-words">
                                                {{ Str::limit($announcement->announce_content, 100) }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            <span class="capitalize">{{ $announcement->announce_type }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            {{ \Carbon\Carbon::parse($announcement->date_posted)->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap border border-gray-200">
                                            @if(isset($announcement->announce_id) && !empty($announcement->announce_id))
                                                <!-- Delete Icon Button -->
                                                <form method="POST" action="{{ route('LydoAdmin.deleteAnnouncement', $announcement->announce_id) }}" class="inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                        class="text-red-600 hover:text-red-800 text-xl mr-2 delete-btn"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <!-- Edit Icon Button -->
                                                <button type="button"
                                                    class="text-yellow-500 hover:text-yellow-700 text-xl edit-btn"
                                                    title="Edit"
                                                    onclick="openEditModal({{ $announcement->announce_id }}, '{{ addslashes($announcement->announce_title) }}', '{{ addslashes($announcement->announce_type) }}', `{{ str_replace(['`', '$'], ['\`', '\$'], $announcement->announce_content) }}`)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @else
                                                <button class="text-gray-400 text-xl cursor-not-allowed" disabled title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No announcements found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Announcement Modal -->
    <div id="announcementModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Create New Announcement</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('LydoAdmin.storeAnnouncement') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="announce_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="announce_title" name="announce_title" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter announcement title">
                        </div>
                        <div>
                            <label for="announce_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="announce_type" name="announce_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Type</option>
                                <option value="applicants">For Applicants</option>
                                <option value="scholars">For Scholars</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="announce_content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea id="announce_content" name="announce_content" rows="6" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Enter announcement content"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Create Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Content View Modal -->
    <div id="contentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="contentModalTitle" aria-describedby="contentModalContent">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] flex flex-col" role="document" tabindex="-1">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 id="contentModalTitle" class="text-xl font-semibold text-gray-800 pr-4"></h3>
                <button onclick="closeContentModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex-1 p-6 overflow-y-auto">
                <div id="contentModalContent" class="prose prose-gray max-w-none">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"></p>
                </div>
            </div>
            <div class="flex justify-end p-6 border-t border-gray-200">
                <button onclick="closeContentModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" aria-label="Close modal">
                    Close
                </button>
            </div>
        </div>
    </div>
<!-- Edit Announcement Modal -->
    <div id="editAnnouncementModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Edit Announcement</h3>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editAnnouncementForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_announce_id" name="announce_id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="edit_announce_title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" id="edit_announce_title" name="announce_title" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter announcement title">
                        </div>
                        <div>
                            <label for="edit_announce_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="edit_announce_type" name="announce_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Type</option>
                                <option value="applicants">For Applicants</option>
                                <option value="scholars">For Scholars</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="edit_announce_content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea id="edit_announce_content" name="announce_content" rows="6" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter announcement content"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                            Update Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    function openEditModal(id, title, type, content) {
        document.getElementById('edit_announce_id').value = id;
        document.getElementById('edit_announce_title').value = title;
        document.getElementById('edit_announce_type').value = type;
        document.getElementById('edit_announce_content').value = content;
        document.getElementById('editAnnouncementForm').action = '/lydo_admin/announcement/' + id;
        document.getElementById('editAnnouncementModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editAnnouncementModal').classList.add('hidden');
    }
    // Close modal when clicking outside
    document.getElementById('editAnnouncementModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('editAnnouncementModal').classList.contains('hidden')) {
            closeEditModal();
        }
    });
</script>

    <script>
        const contentModal = document.getElementById('contentModal');
        const modalContainer = contentModal.querySelector('[role="document"]');

        function openContentModal(title, content) {
            document.getElementById('contentModalTitle').textContent = title;
            const contentElement = document.getElementById('contentModalContent');
            contentElement.innerHTML = `<p class="text-gray-700 leading-relaxed whitespace-pre-wrap">${content}</p>`;
            contentModal.classList.remove('hidden');
            modalContainer.focus();
        }

        function closeContentModal() {
            contentModal.classList.add('hidden');
        }

        // Close content modal when clicking outside modal container
        contentModal.addEventListener('click', function(e) {
            if (e.target === contentModal) {
                closeContentModal();
            }
        });

        // Close content modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !contentModal.classList.contains('hidden')) {
                closeContentModal();
            }
        });

        // Trap focus inside modal when open
        modalContainer.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                const focusableElements = modalContainer.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];

                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });
    </script>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif
    <script>
    // SweetAlert for Delete
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Are you sure you want to delete?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // SweetAlert for Update (Edit Modal)
    document.getElementById('editAnnouncementForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure you want to update?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });
</script>
<script>
    document.getElementById("notifBell").addEventListener("click", function () {
        let dropdown = document.getElementById("notifDropdown");
        dropdown.classList.toggle("hidden");

        // remove badge when opened
        let notifCount = document.getElementById("notifCount");
        if (notifCount) {
            notifCount.remove();
        }
    });
</script>

<script>
    function toggleDropdown(id) {
        const menu = document.getElementById(id);
        menu.classList.toggle("hidden");
    }
</script>
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
 <script src="{{ asset('js/logout.js') }}"></script>
 
    <script>
        function openModal() {
            document.getElementById('announcementModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('announcementModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('announcementModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>

</body>

</html>
