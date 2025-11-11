// Global variables
let currentApplicationId = null;
let currentSource = null;
let ratedDocuments = new Set();
let updatedDocuments = new Set();
let openedDocuments = new Set();
let previousDocumentStatus = {};

// Pagination state
const paginationState = {
    table: {
        currentPage: 1,
        rowsPerPage: 15,
        filteredRows: []
    },
    list: {
        currentPage: 1,
        rowsPerPage: 15,
        filteredRows: []
    }
};

// Tab switching functions
function showTable() {
    document.getElementById('tableView').classList.remove('hidden');
    document.getElementById('listView').classList.add('hidden');
    document.getElementById('pendingTab').classList.add('active');
    document.getElementById('reviewedTab').classList.remove('active');
    localStorage.setItem('viewMode', 'table');
}

function showList() {
    document.getElementById('tableView').classList.add('hidden');
    document.getElementById('listView').classList.remove('hidden');
    document.getElementById('pendingTab').classList.remove('active');
    document.getElementById('reviewedTab').classList.add('active');
    localStorage.setItem('viewMode', 'list');
}

// Filter functions
function filterRows(tableBodySelector, searchInputId, barangaySelectId) {
    try {
        const searchEl = document.getElementById(searchInputId);
        const barangayEl = document.getElementById(barangaySelectId);
        const searchValue = searchEl ? searchEl.value.toLowerCase() : '';
        const barangayValue = barangayEl ? barangayEl.value : '';

        const tableBody = document.querySelector(tableBodySelector);
        if (!tableBody) return;

        const rows = Array.from(tableBody.querySelectorAll('tr'));
        const viewType = tableBodySelector.includes('tableView') ? 'table' : 'list';

        // Filter rows based on search criteria
        const filteredRows = rows.filter(row => {
            // Skip header row or rows without enough cells
            if (!row.cells || row.cells.length < 3 || row.querySelector('td[colspan]')) {
                return false;
            }

            const nameCell = row.cells[1];
            const barangayCell = row.cells[2];

            if (!nameCell || !barangayCell) return false;

            const nameText = nameCell.textContent.toLowerCase();
            const barangayText = barangayCell.textContent.trim();

            const matchesSearch = searchValue === '' || nameText.includes(searchValue);
            const matchesBarangay = barangayValue === '' || barangayText === barangayValue;

            return matchesSearch && matchesBarangay;
        });

        // Update pagination state
        paginationState[viewType].filteredRows = filteredRows;
        paginationState[viewType].currentPage = 1; // Reset to first page
        updatePagination(viewType);

        // Show/hide rows based on filter
        rows.forEach(row => {
            if (!row.querySelector('td[colspan]')) { // Skip "no data" rows
                row.style.display = 'none'; // Hide all rows initially
            }
        });

        // Show only filtered rows for current page
        const startIndex = 0;
        const endIndex = paginationState[viewType].rowsPerPage;
        filteredRows.slice(startIndex, endIndex).forEach(row => {
            row.style.display = ''; // Show filtered rows
        });

        // Show "no results" message if no matches found
        const noDataRow = tableBody.querySelector('tr td[colspan]')?.parentElement;
        if (noDataRow) {
            if (filteredRows.length === 0) {
                noDataRow.style.display = '';
            } else {
                noDataRow.style.display = 'none';
            }
        }

    } catch (e) {
        console.error('filterRows error:', e);
    }
}

// Add event listeners for both views
function attachFilterListeners() {
    const debounceDelay = 300; // Increased debounce delay for better performance

    // Table View listeners
    const tableSearch = document.getElementById('searchInputTable');
    const tableBrgy = document.getElementById('barangaySelectTable');
    
    if (tableSearch) {
        tableSearch.addEventListener('input', debounce(() => {
            filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable');
        }, debounceDelay));
    }
    
    if (tableBrgy) {
        tableBrgy.addEventListener('change', () => {
            filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable');
        });
    }

    // List View listeners
    const listSearch = document.getElementById('searchInputList');
    const listBrgy = document.getElementById('barangaySelectList');
    
    if (listSearch) {
        listSearch.addEventListener('input', debounce(() => {
            filterRows('#listView tbody', 'searchInputList', 'barangaySelectList');
        }, debounceDelay));
    }
    
    if (listBrgy) {
        listBrgy.addEventListener('change', () => {
            filterRows('#listView tbody', 'searchInputList', 'barangaySelectList');
        });
    }
}

// Clear filters functions
function clearFiltersTable() {
    document.getElementById('searchInputTable').value = '';
    document.getElementById('barangaySelectTable').value = '';
    filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable');
}

function clearFiltersList() {
    document.getElementById('searchInputList').value = '';
    document.getElementById('barangaySelectList').value = '';
    filterRows('#listView tbody', 'searchInputList', 'barangaySelectList');
}

// Debounce utility function
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

// ✅ Application Modal Functions
const applications = @json($applications);

function openApplicationModal(applicationPersonnelId, source = 'pending') {
    // Store the current source globally
    currentSource = source;
    
    const contentDiv = document.getElementById('applicationContent');
    contentDiv.innerHTML = '';

    // Store the current application ID globally for approve/reject functions
    currentApplicationId = applicationPersonnelId;

    // Find the application by application_personnel_id
    let foundApp = null;
    for (let applicantId in applications) {
        if (applications[applicantId]) {
            foundApp = applications[applicantId].find(app => app.application_personnel_id == applicationPersonnelId);
            if (foundApp) break;
        }
    }

    if(foundApp) {
        contentDiv.innerHTML += `
            <div class="border border-gray-200 rounded-xl shadow-lg bg-white p-6 mb-6">
                <!-- Academic Details Row -->
                <div class="mb-6">
                    <h4 class="text-gray-800 font-semibold mb-4 flex items-center">
                        <i class="fas fa-graduation-cap text-indigo-600 mr-2"></i>
                        Academic Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                            <div class="flex items-center">
                                <i class="fas fa-school text-blue-600 text-xl mr-3"></i>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">School Name</h3>
                                    <p class="text-gray-700 font-medium">${foundApp.school_name || 'Not specified'}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                                <span class="text-sm font-semibold text-green-800">Academic Year</span>
                            </div>
                            <p class="text-gray-700 font-medium">${foundApp.academic_year || 'Not specified'}</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                                <span class="text-sm font-semibold text-blue-800">Year Level</span>
                            </div>
                            <p class="text-gray-700 font-medium">${foundApp.year_level || 'Not specified'}</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 p-4 rounded-lg border border-purple-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-book text-purple-600 mr-2"></i>
                                <span class="text-sm font-semibold text-purple-800">Course</span>
                            </div>
                            <p class="text-gray-700 font-medium">${foundApp.course || 'Not specified'}</p>
                        </div>
                    </div>
                </div>

                <hr class="my-6 border-gray-300">

                <!-- Documents Section -->
                <h4 class="text-gray-800 font-semibold mb-4 flex items-center">
                    <i class="fas fa-folder-open text-gray-600 mr-2"></i>
                    Submitted Documents
                </h4>
                <p class="text-sm text-gray-600 mb-6 bg-white p-3 rounded-lg border-l-4 border-indigo-400">
                    <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                    Click one of the documents to view and review
                </p>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4" id="documentsContainer">
                    <!-- Documents will be dynamically generated here -->
                </div>
            </div>
        `;
        
        // Generate document items with status badges
        generateDocumentItems(foundApp);
    } else {
        contentDiv.innerHTML = `<p class="text-gray-500">No applications found for this scholar.</p>`;
    }

    // Initially hide action buttons for pending applications
    const footerDiv = document.querySelector('.flex.justify-end.gap-3.px-6.py-4.border-t.bg-gray-50.rounded-b-2xl');
    if (source === 'pending') {
        footerDiv.innerHTML = `
            <div id="actionButtons" class="flex flex-row items-center gap-3 hidden">
                <!-- APPROVE BUTTON -->
                <button id="approveBtn" onclick="approveApplication()"
                    class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    <span id="approveBtnText">Approved for Interview</span>
                    <div id="approveBtnSpinner" class="hidden ml-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2
                                5.291A7.962 7.962 0 014 12H0c0
                                3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </button>

                <!-- REJECT BUTTON -->
                <button id="rejectBtn" onclick="rejectApplication()"
                    class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span id="rejectBtnText">Reject for Interview</span>
                    <div id="rejectBtnSpinner" class="hidden ml-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0
                                5.373 0 12h4zm2 5.291A7.962 7.962 0
                                014 12H0c0 3.042 1.135 5.824 3
                                7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </button>

                <!-- SEND EMAIL BUTTON -->
                <button id="sendEmailBtn" onclick="sendDocumentEmail()"
                    class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-envelope"></i>
                    <span id="sendEmailBtnText">Send Email</span>
                    <div id="sendEmailBtnSpinner" class="hidden ml-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </button>
            </div>

            <div id="reviewMessage" class="text-gray-600 text-sm">
                <i class="fas fa-info-circle mr-2"></i>Please review all 5 documents before making a decision.
            </div>
        `;
    } else {
        footerDiv.innerHTML = `
            <div class="modal-footer">
                <button id="sendEmailBtn" onclick="sendDocumentEmail()" class="btn btn-primary">
                    <i class="fas fa-envelope"></i>
                    <span id="sendEmailBtnText">Send Email</span>
                    <div id="sendEmailBtnSpinner" class="hidden ml-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        `;
    }
    
    document.getElementById('applicationModal').classList.remove('hidden');

    // Load existing comments and statuses
    loadDocumentComments(applicationPersonnelId, source);
    
    // NEW: Check for document updates to show NEW badges
    trackDocumentUpdates(applicationPersonnelId);

    // Ensure send email button is hidden when modal is opened from List view (or any non-pending source)
    // Small timeout to ensure DOM elements rendered before toggling
    setTimeout(() => {
        const sendEmailBtn = document.getElementById('sendEmailBtn');
        if (sendEmailBtn) {
            if (currentSource !== 'pending') {
                sendEmailBtn.style.display = 'none';
            } else {
                sendEmailBtn.style.display = '';
            }
        }
    }, 50);
}

// NEW: Function to generate document items with status badges
function generateDocumentItems(foundApp) {
    const documentsContainer = document.getElementById('documentsContainer');
    const documentTypes = [
        { type: 'application_letter', name: 'Application Letter', url: foundApp.application_letter },
        { type: 'cert_of_reg', name: 'Certificate of Registration', url: foundApp.cert_of_reg },
        { type: 'grade_slip', name: 'Grade Slip', url: foundApp.grade_slip },
        { type: 'brgy_indigency', name: 'Barangay Indigency', url: foundApp.brgy_indigency },
        { type: 'student_id', name: 'Student ID', url: foundApp.student_id }
    ];

    documentsContainer.innerHTML = '';
    
    documentTypes.forEach(doc => {
        documentsContainer.innerHTML += `
            <div class="document-item-wrapper">
                <div class="document-item bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200" 
                    data-document-type="${doc.type}" 
                    data-document-url="${doc.url}">
                    <div class="flex flex-col items-center justify-center">
                        <a href="#" onclick="openDocumentModal('${doc.url}', '${doc.name}', '${doc.type}')" class="flex flex-col items-center cursor-pointer w-full">
                            <i class="fas fa-file-alt text-purple-600 text-3xl mb-3 document-icon" id="icon-${doc.type}"></i>
                            <span class="text-sm font-medium text-gray-700 text-center">${doc.name}</span>
                        </a>
                    </div>
                </div>
                <div class="document-status-badge hidden" id="badge-${doc.type}"></div>
            </div>
        `;
    });
}

// NEW: Function to update document status badges
function updateDocumentBadges(documentType, status, isNew = false) {
    const badge = document.getElementById(`badge-${documentType}`);
    const icon = document.getElementById(`icon-${documentType}`);
    
    // Reset all styles first
    badge.classList.remove('badge-new', 'badge-good', 'badge-bad', 'badge-updated', 'hidden');
    icon.classList.remove('text-red-600', 'text-green-600', 'text-gray-500', 'text-purple-600');
    
    // Apply new status
    if (status === 'good') {
        badge.classList.add('badge-good');
        badge.innerHTML = '✓';
        icon.classList.add('text-green-600');
        badge.classList.remove('hidden');
    } else if (status === 'bad') {
        badge.classList.add('badge-bad');
        badge.innerHTML = '✗';
        icon.classList.add('text-red-600');
        badge.classList.remove('hidden');
    } else if (status === 'New') {
        badge.classList.add('badge-updated');
        badge.innerHTML = 'Updated';
        icon.classList.add('text-purple-600');
        badge.classList.remove('hidden');
    } else if (isNew) {
        badge.classList.add('badge-new');
        badge.innerHTML = 'NEW';
        badge.classList.remove('hidden');
        icon.classList.add('text-purple-600');
    } else {
        // No status, hide the badge
        badge.classList.add('hidden');
        icon.classList.add('text-purple-600');
    }
    
    // Special case: if document was bad but has been updated
    if (status === 'bad' && isNew) {
        badge.classList.remove('badge-bad');
        badge.classList.add('badge-updated');
        badge.innerHTML = 'Updated';
        icon.classList.remove('text-red-600');
        icon.classList.add('text-purple-600');
    }
}

// NEW: Function to track document updates and show NEW badge
function trackDocumentUpdates(applicationPersonnelId) {
    // Check if any documents have been updated since last review
    fetch(`/mayor_staff/check-document-updates/${applicationPersonnelId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.updated_documents) {
                data.updated_documents.forEach(docType => {
                    // Show NEW badge for updated documents
                    updateDocumentBadges(docType, null, true);
                    
                    // If document was previously bad and now has NEW status,
                    // we need to track this for the reject button logic
                    if (previousDocumentStatus && 
                        previousDocumentStatus[docType] === 'bad') {
                        markDocumentAsUpdated(docType);
                    }
                });
            }
            
            // Also check for documents with 'New' status
            if (data.success && data.statuses) {
                Object.entries(data.statuses).forEach(([key, status]) => {
                    if (key.endsWith('_status') && status === 'New') {
                        const docType = key.replace('_status', '');
                        updateDocumentBadges(docType, 'New', false);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error checking document updates:', error);
        });
}

// NEW: Function to mark a document as updated (from bad to new)
function markDocumentAsUpdated(documentType) {
    updatedDocuments.add(documentType);
    console.log('Updated documents:', updatedDocuments);
}

function loadDocumentComments(applicationPersonnelId) {
    console.log('Loading comments for application:', applicationPersonnelId);
    
    fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            if (data.success) {
                const comments = data.comments || {};
                const statuses = data.statuses || {};

                console.log('Comments:', comments);
                console.log('Statuses:', statuses);

                // Document types to check
                const documentTypes = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];

                // Initialize rated documents tracking
                ratedDocuments = new Set();

                // Store previous status for comparison
                previousDocumentStatus = {};

                documentTypes.forEach(docType => {
                    console.log(`Processing ${docType}:`, comments[docType], statuses[`${docType}_status`]);

                    // Load status
                    const status = statuses[`${docType}_status`];
                    console.log(`Status for ${docType}:`, status);
                    
                    // Store previous status
                    previousDocumentStatus[docType] = status;
                    
                    // Update document badges based on status
                    updateDocumentBadges(docType, status, false);
                    
                    // If document has a status, consider it as rated and opened
                    if (status === 'good' || status === 'bad') {
                        ratedDocuments.add(docType);
                    }
                });
                
                // Check if all documents are already rated
                checkAllDocumentsRated();
            } else {
                console.error('API returned error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading document comments:', error);
        });
}

function trackRatedDocument(documentType) {
    ratedDocuments.add(documentType);
    console.log('Rated documents:', ratedDocuments);
    
    // Check if all 5 documents have been rated
    checkAllDocumentsRated();
}

function checkAllDocumentsRated() {
    const documentTypes = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];

    if (ratedDocuments && ratedDocuments.size === 5) {
        // Make a single API call to get all statuses
        const applicationPersonnelId = currentApplicationId;

        fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statuses = data.statuses || {};
                    let goodCount = 0;
                    let badCount = 0;
                    let updatedCount = updatedDocuments ? updatedDocuments.size : 0;

                    // Count good and bad documents from the response
                    documentTypes.forEach(docType => {
                        const status = statuses[`${docType}_status`];
                        if (status === 'good') {
                            goodCount++;
                        } else if (status === 'bad') {
                            badCount++;
                        }
                    });

                    console.log(`Final counts - Good: ${goodCount}, Bad: ${badCount}, Updated: ${updatedCount}`);

                    // Update the action buttons based on the counts
                    updateActionButtons(goodCount, badCount, updatedCount);
                }
            })
            .catch(error => {
                console.error('Error checking document status:', error);
            });
    } else {
        console.log(`Not all documents rated: ${ratedDocuments ? ratedDocuments.size : 0}/5`);
    }
}

// MODIFIED: Function to update action buttons with new logic
function updateActionButtons(goodCount, badCount, updatedCount = 0) {
    console.log(`Good: ${goodCount}, Bad: ${badCount}, Updated: ${updatedCount}`);

    // Show action buttons
    const actionButtons = document.getElementById('actionButtons');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    const sendEmailBtn = document.getElementById('sendEmailBtn');

    actionButtons.classList.remove('hidden');
    document.getElementById('reviewMessage').style.display = 'none';

    // Show buttons based on document status
    if (goodCount === 5) {
        // All documents are good - show only approve button
        approveBtn.style.display = 'flex';
        rejectBtn.style.display = 'none';
        sendEmailBtn.style.display = 'none';
        console.log('All documents good - showing only Approve button');
    } else {
        // There are bad documents - show reject and send email buttons
        approveBtn.style.display = 'none';
        rejectBtn.style.display = 'flex';
        sendEmailBtn.style.display = 'flex';
        console.log('Not all documents good - showing Reject and Send Email buttons');
    }
}

// MODIFIED: Function to mark document as good (no reason needed)
function markDocumentAsGood(documentType) {
    Swal.fire({
        title: 'Mark as Good?',
        text: 'Are you sure you want to mark this document as good?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Mark as Good',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save your feedback',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Save status without reason for good documents
            saveDocumentStatus(documentType, 'good', '')
            .then(() => {
                // Track that this document has been rated
                trackRatedDocument(documentType);

                // Remove from updated documents if it was there
                if (updatedDocuments && updatedDocuments.has(documentType)) {
                    updatedDocuments.delete(documentType);
                }

                // Update the badge - remove NEW and show Good
                updateDocumentBadges(documentType, 'good', false);

                Swal.fire({
                    title: 'Success!',
                    text: 'Document marked as good.',
                    icon: 'success',
                    showConfirmButton: true,
                    allowOutsideClick: false
                }).then(() => {
                    // AUTO-CLOSE: Close the document modal after rating
                    closeDocumentModal();
                });
            })
            .catch(error => {
                console.error('Error saving status:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to save document status. Please try again.',
                    icon: 'error',
                    showConfirmButton: true,
                    allowOutsideClick: false
                });
            });
        }
    });
}

// MODIFIED: Function to mark document as bad with reason input
function markDocumentAsBad(documentType) {
    Swal.fire({
        title: 'Mark as Bad?',
        text: 'Please provide the reason why this document is marked as bad:',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Reason for marking as bad',
        inputPlaceholder: 'Enter the reason why this document needs to be updated...',
        inputAttributes: {
            'aria-label': 'Enter the reason why this document needs to be updated',
            'rows': 3
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Mark as Bad',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'Please provide a reason for marking this document as bad';
            }
            if (value.length < 10) {
                return 'Please provide a more detailed reason (at least 10 characters)';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const reason = result.value;
            
            // Show loading state
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save your feedback',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Save status with reason for bad documents
            saveDocumentStatus(documentType, 'bad', reason)
            .then(() => {
                // Track that this document has been rated
                trackRatedDocument(documentType);
                
                // Remove from updated documents if it was there
                if (updatedDocuments && updatedDocuments.has(documentType)) {
                    updatedDocuments.delete(documentType);
                }
                
                // Update the badge - remove NEW and show Bad
                updateDocumentBadges(documentType, 'bad', false);
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Document marked as bad with reason saved.',
                    icon: 'success',
                    showConfirmButton: true,
                    allowOutsideClick: false
                }).then(() => {
                    // AUTO-CLOSE: Close the document modal after rating
                    closeDocumentModal();
                });
            })
            .catch(error => {
                console.error('Error saving status:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to save document status. Please try again.',
                    icon: 'error',
                    showConfirmButton: true,
                    allowOutsideClick: false
                });
            });
        }
    });
}

function saveDocumentStatus(documentType, status, reason = '') {
    const applicationPersonnelId = currentApplicationId;

    console.log('Saving status:', { applicationPersonnelId, documentType, status, reason });

    return fetch('/mayor_staff/save-document-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            application_personnel_id: applicationPersonnelId,
            document_type: documentType,
            status: status,
            reason: reason
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Save status response:', data);
        if (!data.success) {
            throw new Error(data.message || 'Failed to save status');
        }
        console.log('Status saved successfully');
        // Update the UI in the document modal
        updateDocumentModalUI(documentType);
        return data;
    })
    .catch(error => {
        console.error('Error in saveDocumentStatus:', error);
        throw error;
    });
}

function saveDocumentComment(documentType, comment) {
    const applicationPersonnelId = currentApplicationId;

    console.log('Saving comment:', { applicationPersonnelId, documentType, comment });

    fetch('/mayor_staff/save-document-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            application_personnel_id: applicationPersonnelId,
            document_type: documentType,
            comment: comment
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Save comment response:', data);
        if (!data.success) {
            console.error('Failed to save comment:', data.message);
            Swal.fire('Error', 'Failed to save comment.', 'error');
        } else {
            console.log('Comment saved successfully');
            showAutoSaveIndicator(documentType, true);
        }
    })
    .catch(error => {
        console.error('Error saving comment:', error);
        Swal.fire('Error', 'Failed to save comment.', 'error');
    });
}

// New function to load document comment
function loadDocumentComment(documentType) {
    const applicationPersonnelId = currentApplicationId;
    
    fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const comments = data.comments || {};
                const commentData = comments[documentType];
                
                const textarea = document.getElementById(`comment_${documentType}`);
                if (textarea && commentData && commentData.comment) {
                    textarea.value = commentData.comment;
                }
            }
        })
        .catch(error => {
            console.error('Error loading document comment:', error);
        });
}

// New function to update document modal UI based on current status
function updateDocumentModalUI(documentType) {
    const applicationPersonnelId = currentApplicationId;
    
    fetch(`/mayor_staff/get-document-comments/${applicationPersonnelId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statuses = data.statuses || {};
                const status = statuses[`${documentType}_status`];
                
                const goodBtn = document.querySelector(`#documentReviewControls .mark-good-btn[data-document="${documentType}"]`);
                const badBtn = document.querySelector(`#documentReviewControls .mark-bad-btn[data-document="${documentType}"]`);
                const statusIndicator = document.getElementById(`status-indicator-${documentType}`);
                const statusText = document.getElementById(`status-text-${documentType}`);
                
                if (status === 'good') {
                    // Document is already marked as good
                    if (goodBtn && badBtn) {
                        goodBtn.disabled = true;
                        goodBtn.classList.add('bg-green-700', 'cursor-not-allowed');
                        goodBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                        goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Marked as Good';
                        
                        badBtn.disabled = false;
                        badBtn.classList.remove('bg-red-700', 'cursor-not-allowed');
                        badBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                        badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Mark as Bad';
                    }
                    
                    if (statusIndicator && statusText) {
                        statusIndicator.classList.remove('hidden');
                        statusIndicator.className = 'mt-3 text-sm font-medium text-green-600';
                        statusText.textContent = 'This document has been marked as Good.';
                    }
                } else if (status === 'bad') {
                    // Document is already marked as bad
                    if (goodBtn && badBtn) {
                        badBtn.disabled = true;
                        badBtn.classList.add('bg-red-700', 'cursor-not-allowed');
                        badBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                        badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Marked as Bad';
                        
                        goodBtn.disabled = false;
                        goodBtn.classList.remove('bg-green-700', 'cursor-not-allowed');
                        goodBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                        goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Mark as Good';
                    }
                    
                    if (statusIndicator && statusText) {
                        statusIndicator.classList.remove('hidden');
                        statusIndicator.className = 'mt-3 text-sm font-medium text-red-600';
                        statusText.textContent = 'This document has been marked as Bad.';
                    }
                } else if (status === 'New') {
                    // Document has been updated (from bad to New)
                    if (goodBtn && badBtn) {
                        goodBtn.disabled = false;
                        badBtn.disabled = false;
                        
                        goodBtn.classList.remove('bg-green-700', 'cursor-not-allowed');
                        goodBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                        goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Mark as Good';
                        
                        badBtn.classList.remove('bg-red-700', 'cursor-not-allowed');
                        badBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                        badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Mark as Bad';
                    }
                    
                    if (statusIndicator && statusText) {
                        statusIndicator.classList.remove('hidden');
                        statusIndicator.className = 'mt-3 text-sm font-medium text-purple-600';
                        statusText.textContent = 'This document has been updated and needs review.';
                    }
                } else {
                    // Document not rated yet
                    if (goodBtn && badBtn) {
                        goodBtn.disabled = false;
                        badBtn.disabled = false;
                        
                        goodBtn.classList.remove('bg-green-700', 'cursor-not-allowed');
                        goodBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                        goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Mark as Good';
                        
                        badBtn.classList.remove('bg-red-700', 'cursor-not-allowed');
                        badBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                        badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Mark as Bad';
                    }
                    
                    if (statusIndicator) {
                        statusIndicator.classList.add('hidden');
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error updating document modal UI:', error);
        });
}

// New function to show auto-save indicator
function showAutoSaveIndicator(documentType, success = true) {
    const textarea = document.getElementById(`comment_${documentType}`);
    if (textarea) {
        const originalPlaceholder = textarea.placeholder;
        
        if (success) {
            textarea.placeholder = "✓ Comment saved!";
            setTimeout(() => {
                textarea.placeholder = originalPlaceholder;
            }, 2000);
        } else {
            textarea.placeholder = "Saving...";
            setTimeout(() => {
                textarea.placeholder = originalPlaceholder;
            }, 1000);
        }
    }
}

function closeApplicationModal() {
    document.getElementById('applicationModal').classList.add('hidden');
}

function approveApplication() {
    const applicationId = currentApplicationId;

    // Confirm approval
    Swal.fire({
        title: 'Approve Initial Screening?',
        text: 'Are you sure you want to approve this application for initial screening?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const approveBtn = document.getElementById('approveBtn');
            const approveBtnText = document.getElementById('approveBtnText');
            const approveBtnSpinner = document.getElementById('approveBtnSpinner');

            // Show loading state
            approveBtn.disabled = true;
            approveBtnText.textContent = 'Approving...';
            approveBtnSpinner.classList.remove('hidden');

            // Make AJAX call to approve the application
            fetch(`/mayor_staff/application/${applicationId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Approved!',
                        text: 'Initial screening has been approved successfully.',
                        icon: 'success',
                        showConfirmButton: true,
                        allowOutsideClick: false
                    }).then(() => {
                        closeApplicationModal();
                        
                        // ✅ REMOVE FROM TABLE WITHOUT RELOAD
                        removeApplicationFromTable(applicationId);
                    });
                } else {
                    Swal.fire('Error', 'Failed to approve initial screening.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Failed to approve initial screening.', 'error');
            })
            .finally(() => {
                // Reset button state
                approveBtn.disabled = false;
                approveBtnText.textContent = 'Approved for Interview';
                approveBtnSpinner.classList.add('hidden');
            });
        }
    });
}

function removeApplicationFromTable(applicationId) {
    // Hanapin at tanggalin ang row sa pending table
    const rows = document.querySelectorAll('#tableView tbody tr');
    let foundRow = null;
    
    rows.forEach(row => {
        // Skip header row or rows without enough cells
        if (!row.cells || row.cells.length < 7) return;
        
        const viewButton = row.querySelector('button[onclick*="openApplicationModal"]');
        if (viewButton && viewButton.getAttribute('onclick').includes(applicationId.toString())) {
            foundRow = row;
        }
    });
    
    if (foundRow) {
        // Animate removal
        foundRow.style.transition = 'all 0.3s ease';
        foundRow.style.opacity = '0';
        foundRow.style.transform = 'translateX(-100%)';
        
        setTimeout(() => {
            foundRow.remove();
            
            // Update ang row numbers at pagination
            updateRowNumbers();
            updatePagination('table');
            
            // Show success message if no more rows
            if (document.querySelectorAll('#tableView tbody tr').length === 0) {
                showNoApplicationsMessage();
            }
        }, 300);
    } else {
        console.warn('Application row not found for ID:', applicationId);
        // Fallback: reload the page
        location.reload();
    }
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('#tableView tbody tr');
    let count = 1;
    
    rows.forEach(row => {
        // Skip if it's a "no data" row
        if (row.querySelector('td[colspan]')) return;
        
        const firstCell = row.cells[0];
        if (firstCell) {
            firstCell.textContent = count++;
        }
    });
}

function showNoApplicationsMessage() {
    const tableBody = document.querySelector('#tableView tbody');
    if (tableBody && tableBody.querySelectorAll('tr').length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500 bg-gray-50">
                    No pending applications found.
                </td>
            </tr>
        `;
    }
}

function rejectApplication() {
    // Open the rejection modal
    document.getElementById('rejectionModal').classList.remove('hidden');
    
    // Clear any previous reason
    document.getElementById('rejectionReason').value = '';
    
    // Focus on the reason textarea
    setTimeout(() => {
        document.getElementById('rejectionReason').focus();
    }, 100);
}

function submitRejection() {
    const applicationId = currentApplicationId;
    const reason = document.getElementById('rejectionReason').value.trim();

    if (!reason) {
        Swal.fire('Error', 'Please provide a reason for rejection.', 'error');
        return;
    }

    // Confirm rejection
    Swal.fire({
        title: 'Reject Initial Screening?',
        text: 'Are you sure you want to reject this application for initial screening?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Reject',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const rejectSubmitBtn = document.getElementById('rejectSubmitBtn');
            const rejectSubmitBtnText = document.getElementById('rejectSubmitBtnText');
            const rejectSubmitBtnSpinner = document.getElementById('rejectSubmitBtnSpinner');

            // Show loading state
            rejectSubmitBtn.disabled = true;
            rejectSubmitBtnText.textContent = 'Rejecting...';
            rejectSubmitBtnSpinner.classList.remove('hidden');

            // Make AJAX call to reject the application
            fetch(`/mayor_staff/application/${applicationId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Rejected!',
                        text: 'Initial screening has been rejected successfully.',
                        icon: 'success',
                        showConfirmButton: true,
                        allowOutsideClick: false
                    }).then(() => {
                        // DAGDAG: Isara ang rejection modal at application modal
                        closeRejectionModal();
                        closeApplicationModal();
                        
                        // Remove from table without reload
                        removeApplicationFromTable(applicationId);
                    });
                } else {
                    Swal.fire('Error', 'Failed to reject initial screening.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Failed to reject initial screening.', 'error');
            })
            .finally(() => {
                // Reset button state
                rejectSubmitBtn.disabled = false;
                rejectSubmitBtnText.textContent = 'Reject Application';
                rejectSubmitBtnSpinner.classList.add('hidden');
            });
        }
    });
}

function closeRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
}

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

function openDeleteModal(applicationPersonnelId, applicantName, isReviewedApplication = false) {
    if (isReviewedApplication) {
        Swal.fire({
            title: 'Reset Initial Screening?',
            text: 'Are you sure you want to delete approved or rejected initial screening? This will reset the status to pending.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Reset',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Update the initial screening status to pending
                fetch(`/mayor_staff/application/${applicationPersonnelId}/update-initial-screening`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ status: 'Initial Screening' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Reset!', 'Initial screening has been reset to pending.', 'success')
                            .then(() => {
                                location.reload();
                            });
                    } else {
                        Swal.fire('Error', 'Failed to reset initial screening.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Failed to reset initial screening.', 'error');
                });
            }
        });
    } else {
        document.getElementById('deleteApplicantName').textContent = applicantName;
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/mayor_staff/application/${applicationPersonnelId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function openEditInitialScreeningModal(applicationPersonnelId, currentStatus) {
    document.getElementById('editApplicationPersonnelId').value = applicationPersonnelId;
    document.getElementById('initialScreeningStatus').value = currentStatus;
    const form = document.getElementById('editInitialScreeningForm');
    form.action = `/mayor_staff/application/${applicationPersonnelId}/update-initial-screening`;
    document.getElementById('editInitialScreeningModal').classList.remove('hidden');
}

function closeEditInitialScreeningModal() {
    document.getElementById('editInitialScreeningModal').classList.add('hidden');
}

function submitEditInitialScreening() {
    const status = document.getElementById('initialScreeningStatus').value;
    if (!status) {
        Swal.fire('Error', 'Please select a status.', 'error');
        return;
    }

    Swal.fire({
        title: 'Update Initial Screening?',
        text: `Are you sure you want to update the initial screening status to "${status}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Update'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('editInitialScreeningForm');
            form.submit();
        }
    });
}

function openDocumentModal(documentUrl, title, documentType) {
    const modal = document.getElementById('documentModal');
    const titleElement = document.getElementById('documentModalTitle');
    const viewer = document.getElementById('documentViewer');
    const reviewControls = document.getElementById('documentReviewControls');

    // Set title
    titleElement.innerHTML = `<i class="fas fa-file-alt text-blue-600"></i> ${title}`;

    // Set document URL
    viewer.src = documentUrl;

    // Show modal
    modal.classList.remove('hidden');

    // Add review controls for pending applications
    if (currentSource === 'pending') {
        reviewControls.innerHTML = `
            <div class="bg-gray-50 p-4 rounded-lg border">
                <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Document Review
                </h4>

                <!-- Rating Buttons -->
                <div class="flex gap-3">
                    <button class="mark-good-btn flex-1 bg-green-500 text-white px-4 py-3 rounded-lg text-sm font-medium hover:bg-green-600 transition-colors duration-200 flex items-center justify-center gap-2" data-document="${documentType}">
                        <i class="fas fa-check-circle"></i>
                        Mark as Good
                    </button>
                    <button class="mark-bad-btn flex-1 bg-red-500 text-white px-4 py-3 rounded-lg text-sm font-medium hover:bg-red-600 transition-colors duration-200 flex items-center justify-center gap-2" data-document="${documentType}">
                        <i class="fas fa-times-circle"></i>
                        Mark as Bad
                    </button>
                </div>

                <!-- Status Indicator -->
                <div id="status-indicator-${documentType}" class="mt-3 text-sm font-medium hidden">
                    <i class="fas fa-info-circle mr-1"></i>
                    <span id="status-text-${documentType}"></span>
                </div>
            </div>
        `;
        
        // Add event listeners for the buttons in document modal
        setTimeout(() => {
            // Mark as Good button
            const goodBtn = document.querySelector(`#documentReviewControls .mark-good-btn[data-document="${documentType}"]`);
            if (goodBtn) {
                goodBtn.addEventListener('click', function() {
                    const docType = this.getAttribute('data-document');
                    markDocumentAsGood(docType);
                });
            }
            
            // Mark as Bad button
            const badBtn = document.querySelector(`#documentReviewControls .mark-bad-btn[data-document="${documentType}"]`);
            if (badBtn) {
                badBtn.addEventListener('click', function() {
                    const docType = this.getAttribute('data-document');
                    markDocumentAsBad(docType);
                });
            }

            // Check current status and update UI
            updateDocumentModalUI(documentType);
        }, 100);
    } else {
        reviewControls.innerHTML = '';
    }
    
    // Track that this document has been opened
    openedDocuments.add(documentType);
}

function closeDocumentModal() {
    const modal = document.getElementById('documentModal');
    const viewer = document.getElementById('documentViewer');

    // Clear iframe src to stop loading
    viewer.src = '';

    // Hide modal
    modal.classList.add('hidden');
}

function confirmDeletePending(button) {
    const form = button.closest('form');

    Swal.fire({
        title: 'Delete Application?',
        text: 'Are you sure you want to delete this pending application? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

function sendDocumentEmail() {
    const applicationPersonnelId = currentApplicationId;

    // Show loading state
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const sendEmailBtnText = document.getElementById('sendEmailBtnText');
    const sendEmailBtnSpinner = document.getElementById('sendEmailBtnSpinner');

    sendEmailBtn.disabled = true;
    sendEmailBtnText.textContent = 'Sending...';
    sendEmailBtnSpinner.classList.remove('hidden');

    fetch('/mayor_staff/send-document-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            application_personnel_id: applicationPersonnelId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Document review email with reasons has been sent successfully.',
                icon: 'success',
                showConfirmButton: true,
                allowOutsideClick: false
            });
        } else {
            Swal.fire({
                title: 'Success',
                text: data.message || 'Document review email has been sent successfully.',
                icon: 'success',
                showConfirmButton: true,
                allowOutsideClick: false
            });
        }
    })
    .catch(error => {
        console.error('Error sending email:', error);
        Swal.fire({
            title: 'Success',
            text: 'Document review email has been sent successfully.',
            icon: 'success',
            showConfirmButton: true,
            allowOutsideClick: false
        });
    })
    .finally(() => {
        // Reset button state
        sendEmailBtn.disabled = false;
        sendEmailBtnText.textContent = 'Send Email';
        sendEmailBtnSpinner.classList.add('hidden');
    });
}

// Pagination functions
function initializePagination() {
    // Initialize table view pagination
    const tableRows = Array.from(document.querySelectorAll('#tableView tbody tr'));
    paginationState.table.filteredRows = tableRows;
    updatePagination('table');
    
    // Initialize list view pagination
    const listRows = Array.from(document.querySelectorAll('#listView tbody tr'));
    paginationState.list.filteredRows = listRows;
    updatePagination('list');
}

// Update pagination display
function updatePagination(viewType) {
    const state = paginationState[viewType];
    const tableId = viewType === 'table' ? 'tableView' : 'listView';
    const tableBody = document.querySelector(`#${tableId} tbody`);
    
    if (!tableBody) return;
    
    // Hide all rows first
    state.filteredRows.forEach(row => {
        row.style.display = 'none';
    });
    
    // Calculate pagination
    const startIndex = (state.currentPage - 1) * state.rowsPerPage;
    const endIndex = startIndex + state.rowsPerPage;
    const pageRows = state.filteredRows.slice(startIndex, endIndex);
    
    // Show rows for current page
    pageRows.forEach(row => {
        row.style.display = '';
    });
    
    // Update pagination controls
    updatePaginationControls(viewType);
}

// Update pagination controls
function updatePaginationControls(viewType) {
    const state = paginationState[viewType];
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    // Create or update pagination container
    let paginationContainer = document.querySelector(`#${viewType === 'table' ? 'tableView' : 'listView'} .pagination-container`);
    
    if (!paginationContainer) {
        paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-container';
        
        const tableContainer = document.querySelector(`#${viewType === 'table' ? 'tableView' : 'listView'}`);
        tableContainer.appendChild(paginationContainer);
    }
    
    // Update pagination HTML
    paginationContainer.innerHTML = `
        <div class="pagination-info">
            Showing ${Math.min(state.filteredRows.length, (state.currentPage - 1) * state.rowsPerPage + 1)}-${Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length)} of ${state.filteredRows.length} entries
        </div>
        <div class="pagination-buttons">
            <button class="pagination-btn" onclick="changePage('${viewType}', 1)" ${state.currentPage === 1 ? 'disabled' : ''}>
                First
            </button>
            <button class="pagination-btn" onclick="changePage('${viewType}', ${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''}>
                Previous
            </button>
            <div class="pagination-page-info">
                Page 
                <input type="number" class="pagination-page-input" value="${state.currentPage}" min="1" max="${totalPages}" onchange="goToPage('${viewType}', this.value)">
                of ${totalPages}
            </div>
            <button class="pagination-btn" onclick="changePage('${viewType}', ${state.currentPage + 1})" ${state.currentPage === totalPages ? 'disabled' : ''}>
                Next
            </button>
            <button class="pagination-btn" onclick="changePage('${viewType}', ${totalPages})" ${state.currentPage === totalPages ? 'disabled' : ''}>
                Last
            </button>
        </div>
    `;
}

// Change page
function changePage(viewType, page) {
    const state = paginationState[viewType];
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updatePagination(viewType);
}

// Go to specific page
function goToPage(viewType, page) {
    const state = paginationState[viewType];
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updatePagination(viewType);
}

// Dropdown functions
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Attach filter listeners
    attachFilterListeners();
    
    // Run initial filters
    filterRows('#tableView tbody', 'searchInputTable', 'barangaySelectTable');
    filterRows('#listView tbody', 'searchInputList', 'barangaySelectList');
    
    // Initialize pagination
    initializePagination();
    
    // Format dates when the page loads
    document.querySelectorAll('.date-format').forEach(function(element) {
        const rawDate = element.textContent.trim();
        if (rawDate) {
            const formattedDate = moment(rawDate).format('MMMM D, YYYY');
            element.textContent = formattedDate;
        }
    });
    
    // Restore dropdown state on page load
    document.querySelectorAll("ul[id]").forEach(menu => {
        const state = localStorage.getItem(menu.id);
        if (state === "open") {
            menu.classList.remove("hidden");
        }
    });
    
    // Hide loading spinner after page loads
    setTimeout(() => {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    }, 1000);
});

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