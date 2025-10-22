function openEmailModal(applicationPersonnelId, applicantId, applicantName, applicantEmail) {
    console.log("openEmailModal called with applicationPersonnelId:", applicationPersonnelId);
    document.getElementById('emailApplicationPersonnelId').value = applicationPersonnelId;
    document.getElementById('recipientName').value = applicantName;
    document.getElementById('recipientEmail').value = applicantEmail || '';
    document.getElementById('emailSubject').value = '';
    document.getElementById('emailMessage').value = 'Please resubmit your application with the correct documents.';

    // Clear previous checkboxes
    const checkboxesDiv = document.getElementById('applicationCheckboxes');
    checkboxesDiv.innerHTML = '';

    console.log("Mapped applicantId:", applicantId);
    console.log("Applications for applicantId:", applications[applicantId]);

    window.currentApplicantId = applicantId;

    if (applicantId && applications[applicantId]) {
    checkboxesDiv.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="flex items-center">
            <input type="checkbox" name="application_issues[]" value="application_letter" id="app_letter" class="mr-2" />
            <label for="app_letter">Application Letter</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="application_issues[]" value="cert_of_reg" id="cert_reg" class="mr-2" />
            <label for="cert_reg">Certificate of Registration</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="application_issues[]" value="grade_slip" id="grade_slip" class="mr-2" />
            <label for="grade_slip">Grade Slip</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="application_issues[]" value="brgy_indigency" id="brgy_indigency" class="mr-2" />
            <label for="brgy_indigency">Barangay Indigency</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="application_issues[]" value="student_id" id="student_id" class="mr-2" />
            <label for="student_id">Student ID</label>
        </div>
        </div>
    `;

    // Add event listener to update message when checkboxes change
    setTimeout(() => {
        const checkboxes = checkboxesDiv.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateEmailMessage);
        });
    }, 100);
    }

    document.getElementById('emailModal').classList.remove('hidden');
}

function updateEmailMessage() {
    const checkboxes = document.querySelectorAll('input[name="application_issues[]"]:checked');
    let issues = [];
    let issueFields = [];
    checkboxes.forEach(checkbox => {
    issueFields.push(checkbox.value);
    switch(checkbox.value) {
        case 'application_letter':
        issues.push('Application Letter');
        break;
        case 'cert_of_reg':
        issues.push('Certificate of Registration');
        break;
        case 'grade_slip':
        issues.push('Grade Slip');
        break;
        case 'brgy_indigency':
        issues.push('Barangay Indigency');
        break;
        case 'student_id':
        issues.push('Student ID');
        break;
    }
    });

    let message = 'Please resubmit your application with the correct documents.';
    if (issues.length > 0) {
    message += '\n\nThe following documents have issues and need to be resubmitted:\n' + issues.map(issue => '- ' + issue).join('\n');
    // Append the update link
    const updateLink = window.location.origin + '/scholar/update-application/' + window.currentApplicantId + '?issues=' + issueFields.join(',');
    message += '\n\nUpdate your application here: ' + updateLink;
    }

    document.getElementById('emailMessage').value = message;
}

function closeEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
}

// Add loading state to email form submission
document.getElementById('emailForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission to handle with AJAX and SweetAlert
    const sendBtn = document.getElementById('sendEmailBtn');
    const sendBtnText = document.getElementById('sendBtnText');
    const sendBtnSpinner = document.getElementById('sendBtnSpinner');
    const form = this;

    // Show loading state immediately
    sendBtn.disabled = true;
    sendBtnText.textContent = 'Sending...';
    sendBtnSpinner.classList.remove('hidden');
    sendBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    sendBtn.classList.add('bg-blue-500', 'cursor-not-allowed');

    // Confirm with SweetAlert
    Swal.fire({
    title: 'Send Email Notification?',
text: "Do you want to send an email to the applicant",
icon: 'question',
showCancelButton: true,
confirmButtonText: 'Yes, Send',
cancelButtonText: 'No',
allowOutsideClick: false,
allowEscapeKey: false
    }).then((result) => {
    if (result.isConfirmed) {
        // Proceed with AJAX submission
        const formData = new FormData(form);
        fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
        })
        .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
        })
        .then(data => {
        if (data.success) {
            Swal.fire({
            icon: 'success',
            title: 'Email Sent!',
            text: data.message || 'Email has been sent successfully.',
            timer: 2000,
            showConfirmButton: false
            });
            closeEmailModal();
            // Optionally reload to update any UI
            // location.reload();
        } else {
            Swal.fire('Error', data.message || 'Failed to send email.', 'error');
        }
        })
        .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to send email. Please try again.', 'error');
        })
        .finally(() => {
        // Reset button state
        sendBtn.disabled = false;
        sendBtnText.textContent = 'Send';
        sendBtnSpinner.classList.add('hidden');
        sendBtn.classList.remove('bg-blue-500', 'cursor-not-allowed');
        sendBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        });
    } else {
        // User cancelled, reset button immediately
        sendBtn.disabled = false;
        sendBtnText.textContent = 'Send';
        sendBtnSpinner.classList.add('hidden');
        sendBtn.classList.remove('bg-blue-500', 'cursor-not-allowed');
        sendBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    }
    });
});

function confirmInitialScreening(selectElement) {
    const selectedValue = selectElement.value;
    const previousValue = selectElement.getAttribute('data-previous');
    const form = selectElement.closest('form');

    // If changing to Initial Screening, submit directly without confirmation
    if (selectedValue === 'Initial Screening') {
        form.submit();
        return;
    }

    // If changing to Approved or Rejected, show confirmation
    if (selectedValue === 'Approved' || selectedValue === 'Rejected') {
        Swal.fire({
            title: 'Confirm Status Change',
            text: `Are you sure you want to mark the initial Screening as "${selectedValue}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: selectedValue === 'Approved' ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${selectedValue}`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Update the data-previous attribute and submit
                selectElement.setAttribute('data-previous', selectedValue);
                form.submit();
            } else {
                // Revert to previous value
                selectElement.value = previousValue;
            }
        });
    }
}

function openDeleteModal(applicationPersonnelId, applicantName) {
    document.getElementById('deleteApplicantName').textContent = applicantName;
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/mayor_staff/application/${applicationPersonnelId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

let activeDropdown = null;
let originalParent = null;

function toggleDropdownMenu(applicationPersonnelId) {
    const menu = document.getElementById(`dropdown-menu-${applicationPersonnelId}`);
    // Hide all other dropdowns first
    document.querySelectorAll('.dropdown-menu').forEach(m => {
        if (m !== menu) m.classList.add('hidden');
    });

    // Toggle visibility
    const isHidden = menu.classList.contains('hidden');
    if (isHidden) {
        menu.classList.remove('hidden');
        menu.style.position = 'absolute';
        menu.style.zIndex = 99999;
        menu.style.left = 'auto';
        menu.style.right = '0';

        // Position below the button
        menu.style.top = '100%';
        menu.style.bottom = 'auto';

        // Check if dropdown will overflow bottom
        const rect = menu.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        if (rect.bottom > windowHeight) {
            menu.style.top = 'auto';
            menu.style.bottom = '100%';
        }
    } else {
        menu.classList.add('hidden');
    }
}

// Optional: Hide dropdown when clicking outside
document.addEventListener('click', function(event) {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (!menu.classList.contains('hidden')) {
            if (!menu.contains(event.target) && !event.target.closest('.dropdown')) {
                menu.classList.add('hidden');
            }
        }
    });
});

function closeFloatingDropdown() {
    if (activeDropdown && originalParent) {
        activeDropdown.classList.add('hidden');
        activeDropdown.style.position = '';
        activeDropdown.style.zIndex = '';
        activeDropdown.style.top = '';
        activeDropdown.style.left = '';
        activeDropdown.style.right = '';
        activeDropdown.style.bottom = '';
        originalParent.appendChild(activeDropdown);
        activeDropdown = null;
        originalParent = null;
    }
}

// Small debounce helper to avoid firing searches on every keystroke
function debounce(fn, delay) {
    let t;
    return function(...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), delay);
    };
}

// Helpers to persist and restore input/select values
function saveState(key, value) {
    try {
        localStorage.setItem(key, value);
    } catch (e) {
        // ignore if storage is not available
        console.warn('Could not save to localStorage', e);
    }
}

function loadState(key) {
    try {
        return localStorage.getItem(key);
    } catch (e) {
        return null;
    }
}

document.getElementById("notifBell").addEventListener("click", function () {
     let dropdown = document.getElementById("notifDropdown");
    dropdown.classList.toggle("hidden");
    // remove badge when opened
    let notifCount = document.getElementById("notifCount");
     if (notifCount) {
    notifCount.remove();
    // Mark notifications as viewed on the server
    fetch('/mayor_staff/mark-notifications-viewed', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Notifications marked as viewed');
        }
    }).catch(error => {
        console.error('Error marking notifications as viewed:', error);
    });
    }
    });

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

// Tab switching functions
function showTable() {
    // Show pending table, hide reviewed
    document.getElementById('tableView').classList.remove('hidden');
    document.getElementById('listView').classList.add('hidden');

    // Update tab active states
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector('.tab:nth-child(1)').classList.add('active');

    // Persist active tab
    saveState('active_tab', 'pending');
}

function showList() {
    // Show reviewed table, hide pending
    document.getElementById('listView').classList.remove('hidden');
    document.getElementById('tableView').classList.add('hidden');

    // Update tab active states
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector('.tab:nth-child(2)').classList.add('active');

    // Persist active tab
    saveState('active_tab', 'reviewed');
}

// DataTables JS
$(document).ready(function() {
    // Initialize DataTable for pendingTable
    const pendingTable = $('#pendingTable').DataTable({
        "paging": true,
        "searching": false,
        "ordering": true,
        "info": true,
        "lengthChange": false,
        "responsive": true,
        "pageLength": 10
    });

    // Initialize DataTable for reviewedTable
    const reviewedTable = $('#reviewedTable').DataTable({
        "paging": true,
        "searching": false,
        "ordering": true,
        "info": true,
        "lengthChange": false,
        "responsive": true,
        "pageLength": 10
    });

    // Restore saved search/barangay state (if any) and apply to tables
    const pendingSearchSaved = loadState('pending_search') || '';
    const pendingBarangaySaved = loadState('pending_barangay') || '';
    const reviewedSearchSaved = loadState('reviewed_search') || '';
    const reviewedBarangaySaved = loadState('reviewed_barangay') || '';

    // Apply saved values to inputs if elements exist
    const $searchPending = $('#search_pending');
    const $barangayPending = $('#barangay_pending');
    const $searchReviewed = $('#search_reviewed');
    const $barangayReviewed = $('#barangay_reviewed');

    if ($searchPending.length) {
        $searchPending.val(pendingSearchSaved);
        pendingTable.column(1).search(pendingSearchSaved).draw();
    }
    if ($barangayPending.length) {
        $barangayPending.val(pendingBarangaySaved);
        if (pendingBarangaySaved === '') {
            pendingTable.column(2).search('').draw();
        } else {
            pendingTable.column(2).search('^' + pendingBarangaySaved + '$', true, false).draw();
        }
    }

    if ($searchReviewed.length) {
        $searchReviewed.val(reviewedSearchSaved);
        reviewedTable.column(1).search(reviewedSearchSaved).draw();
    }
    if ($barangayReviewed.length) {
        $barangayReviewed.val(reviewedBarangaySaved);
        if (reviewedBarangaySaved === '') {
            reviewedTable.column(2).search('').draw();
        } else {
            reviewedTable.column(2).search('^' + reviewedBarangaySaved + '$', true, false).draw();
        }
    }

    // Debounced handlers for inputs to avoid excessive redraws
    if ($searchPending.length) {
        $searchPending.on('input', debounce(function() {
            const val = this.value;
            pendingTable.column(1).search(val).draw();
            saveState('pending_search', val);
        }, 300));
    }

    if ($barangayPending.length) {
        $barangayPending.on('change', function() {
            const value = this.value;
            if (value === "") {
                pendingTable.column(2).search('').draw();
            } else {
                pendingTable.column(2).search('^' + value + '$', true, false).draw();
            }
            saveState('pending_barangay', value);
        });
    }

    if ($searchReviewed.length) {
        $searchReviewed.on('input', debounce(function() {
            const val = this.value;
            reviewedTable.column(1).search(val).draw();
            saveState('reviewed_search', val);
        }, 300));
    }

    if ($barangayReviewed.length) {
        $barangayReviewed.on('change', function() {
            const value = this.value;
            if (value === "") {
                reviewedTable.column(2).search('').draw();
            } else {
                reviewedTable.column(2).search('^' + value + '$', true, false).draw();
            }
            saveState('reviewed_barangay', value);
        });
    }

    // Restore active tab on page load
    const activeTab = loadState('active_tab') || 'pending';
    if (activeTab === 'reviewed') {
        showList();
    } else {
        showTable();
    }
});
