// filter_paginate.js - Complete JavaScript for client-side filtering and pagination

// Global pagination state
const paginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Function to get full name for sorting
function getFullNameForSorting(row) {
    const nameCell = row.cells[1];
    if (!nameCell) return '';
    return nameCell.textContent.trim().toLowerCase();
}

// Function to sort rows alphabetically by last name
function sortRowsAlphabetically(rows) {
    return rows.sort((a, b) => {
        const nameA = getFullNameForSorting(a);
        const nameB = getFullNameForSorting(b);
        return nameA.localeCompare(nameB);
    });
}

// Initialize data from the table
function initializeScholarData() {
    const tableRows = Array.from(document.querySelectorAll('table tbody tr'));
    paginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by last name
    paginationState.allRows = sortRowsAlphabetically(paginationState.allRows);
    paginationState.filteredRows = [...paginationState.allRows];
}

// Initialize pagination
function initializeScholarPagination() {
    updateScholarPagination();
}

// Update pagination display
function updateScholarPagination() {
    const state = paginationState;
    const container = document.getElementById('paginationContainer');
    
    if (!container) return;
    
    // Hide all rows first
    state.allRows.forEach(row => {
        row.style.display = 'none';
    });
    
    // Calculate pagination for filtered rows
    const startIndex = (state.currentPage - 1) * state.rowsPerPage;
    const endIndex = startIndex + state.rowsPerPage;
    const pageRows = state.filteredRows.slice(startIndex, endIndex);
    
    // Show only rows for current page
    pageRows.forEach(row => {
        row.style.display = '';
    });
    
    // Update pagination controls
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    const startItem = state.filteredRows.length === 0 ? 0 : Math.min((state.currentPage - 1) * state.rowsPerPage + 1, state.filteredRows.length);
    const endItem = Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length);
    
    container.innerHTML = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> scholars
            </div>
            
            <div class="flex items-center space-x-1">
                <!-- First Page -->
                <button onclick="changeScholarPage(1)"
                    class="px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 ${
                        state.currentPage === 1
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>

                <!-- Previous Page -->
                <button onclick="changeScholarPage(${state.currentPage - 1})"
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === 1
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-left"></i>
                </button>

                <!-- Page Info -->
                <div class="flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 bg-white">
                    Page
                    <input type="number"
                           class="mx-2 w-12 text-center border border-gray-300 rounded px-1 py-1 text-sm"
                           value="${state.currentPage}"
                           min="1"
                           max="${totalPages}"
                           onchange="goToScholarPage(this.value)">
                    of ${totalPages}
                </div>

                <!-- Next Page -->
                <button onclick="changeScholarPage(${state.currentPage + 1})"
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === totalPages
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>

                <!-- Last Page -->
                <button onclick="changeScholarPage(${totalPages})"
                    class="px-3 py-2 text-sm font-medium rounded-r-md border border-gray-300 ${
                        state.currentPage === totalPages
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
        </div>
    `;
}

// Change page
function changeScholarPage(page) {
    const state = paginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateScholarPagination();
    updateCheckboxStates();
}

// Go to specific page
function goToScholarPage(page) {
    const state = paginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateScholarPagination();
    updateCheckboxStates();
}

// Initialize filtering functionality
function initializeScholarFiltering() {
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');
    const academicYearSelect = document.getElementById('academicYearSelect');
    const statusSelect = document.getElementById('statusSelect');

    // New helper: fetch filtered rows from server and replace table body.
    async function fetchAndReplaceScholars() {
        try {
            const params = new URLSearchParams();
            if (searchInput && searchInput.value) params.set('search', searchInput.value);
            if (barangaySelect && barangaySelect.value) params.set('barangay', barangaySelect.value);
            if (academicYearSelect && academicYearSelect.value) params.set('academic_year', academicYearSelect.value);
            if (statusSelect && statusSelect.value) params.set('status', statusSelect.value);

            const resp = await fetch(`/lydo_admin/scholar?${params.toString()}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!resp.ok) throw new Error('Failed to load scholars from server');

            const html = await resp.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.querySelector('table tbody');

            if (newTbody) {
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = newTbody.innerHTML;

                // Re-initialize client-side state after replacing rows
                initializeScholarData();
                paginationState.currentPage = 1;
                updateScholarPagination();
                initializeCheckboxSelection();
                updateCheckboxStates();
                updateSendButton();

                // Re-attach other handlers that rely on DOM (documents modal buttons, etc.)
                initializeEmailFunctionality();
                initializeAnnouncementFunctionality();
                initializeDocumentsModal();
            }
        } catch (err) {
            console.error('Error fetching scholars:', err);
        }
    }

   function filterScholarTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedBarangay = barangaySelect.value;
    const selectedAcademicYear = academicYearSelect.value;
    const selectedStatus = statusSelect.value;

    const filteredRows = paginationState.allRows.filter(row => {
        const nameCell = row.cells[1];
        const barangayCell = row.cells[2];
        const academicYearCell = row.cells[6];
        const statusCell = row.cells[7];

        if (!nameCell || !barangayCell || !academicYearCell || !statusCell) return false;

        const name = nameCell.textContent.toLowerCase();
        const barangay = barangayCell.textContent.trim();
        const academicYear = academicYearCell.textContent.trim();
        
        // Get status from the badge text
        const statusBadge = statusCell.querySelector('span');
        const status = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';

        const nameMatch = name.includes(searchTerm);
        const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
        const academicYearMatch = !selectedAcademicYear || academicYear === selectedAcademicYear;
        
        // Status matching logic - UPDATED TO INCLUDE GRADUATED
        let statusMatch = true;
        if (selectedStatus === 'active') {
            statusMatch = status === 'active';
        } else if (selectedStatus === 'inactive') {
            statusMatch = status === 'inactive';
        } else if (selectedStatus === 'graduated') {
            statusMatch = status === 'graduated';
        }
        // If 'all' is selected, statusMatch remains true

        return nameMatch && barangayMatch && academicYearMatch && statusMatch;
    });

    // Sort filtered results alphabetically
    const sortedFilteredRows = sortRowsAlphabetically(filteredRows);

    // Update filtered rows and reset to page 1
    paginationState.filteredRows = sortedFilteredRows;
    paginationState.currentPage = 1;
    updateScholarPagination();
    updateCheckboxStates();
    
    // Update URL without reloading (optional)
    updateURLParams({
        search: searchTerm,
        barangay: selectedBarangay,
        academic_year: selectedAcademicYear,
        status: selectedStatus
    });
}

    // Add event listeners with debouncing
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterScholarTable, 300));
    }
    if (barangaySelect) {
        barangaySelect.addEventListener('change', filterScholarTable);
    }
    if (academicYearSelect) {
        academicYearSelect.addEventListener('change', filterScholarTable);
    }
if (statusSelect) {
    // When status changes, ask server for rows with that status, then apply client filters.
    statusSelect.addEventListener('change', function() {
        // Fetch server-side filtered rows (so graduated rows exist in DOM) then run client filter
        fetchAndReplaceScholars().then(() => {
            // after server rows are in DOM, apply client side filters (search/barangay/academicyear)
            filterScholarTable();
        });
    });
}
}

// Update URL parameters without reloading
function updateURLParams(params) {
    const url = new URL(window.location);
    
    Object.keys(params).forEach(key => {
        if (params[key]) {
            url.searchParams.set(key, params[key]);
        } else {
            url.searchParams.delete(key);
        }
    });
    
    window.history.pushState({}, '', url);
}

// Debounce function for search
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

// Checkbox selection functionality
function initializeCheckboxSelection() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.scholar-checkbox');

    // Select All checkbox functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            if (this.checked) {
                // Check all currently visible checkboxes
                const visibleRows = paginationState.filteredRows;
                visibleRows.forEach(row => {
                    const checkbox = row.querySelector('.scholar-checkbox');
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            } else {
                // Uncheck all checkboxes
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
            updateSendButton();
        });
    }

    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSendButton();
            updateSelectAllState();
        });
    });
}

// Update select all checkbox state
function updateSelectAllState() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.scholar-checkbox');
    
    if (!selectAll) return;
    
    const visibleCheckboxes = Array.from(checkboxes).filter(cb => 
        cb.closest('tr').style.display !== 'none'
    );
    const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);
    const someChecked = visibleCheckboxes.some(cb => cb.checked);
    
    selectAll.checked = allChecked;
    selectAll.indeterminate = someChecked && !allChecked;
}

// Update checkbox states after filtering/pagination
function updateCheckboxStates() {
    updateSelectAllState();
    updateSendButton();
}

// Update send button state
function updateSendButton() {
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const generateAnnouncementBtn = document.getElementById('generateAnnouncementBtn');
    
    const selectedCount = document.querySelectorAll('.scholar-checkbox:checked').length;
    
    if (sendEmailBtn) {
        sendEmailBtn.disabled = selectedCount === 0;
    }
    if (generateAnnouncementBtn) {
        generateAnnouncementBtn.disabled = selectedCount === 0;
    }

    // Show or hide buttons based on selection
    if (selectedCount > 0) {
        if (sendEmailBtn) sendEmailBtn.classList.remove('hidden');
        if (generateAnnouncementBtn) generateAnnouncementBtn.classList.remove('hidden');
    } else {
        if (sendEmailBtn) sendEmailBtn.classList.add('hidden');
        if (generateAnnouncementBtn) generateAnnouncementBtn.classList.add('hidden');
    }
}

// Email functionality
function initializeEmailFunctionality() {
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const emailModal = document.getElementById('emailModal');
    const cancelEmail = document.getElementById('cancelEmail');
    const closeEmailModal = document.getElementById('closeEmailModal');
    const emailForm = document.getElementById('emailForm');
    const emailLoading = document.getElementById('emailLoading');
    const sendEmailButton = document.getElementById('sendEmailButton');

    // Open email modal
    if (sendEmailBtn) {
        sendEmailBtn.addEventListener('click', function() {
            const selectedEmails = Array.from(document.querySelectorAll('.scholar-checkbox:checked'))
                .map(checkbox => checkbox.value)
                .join(', ');

            const selectedScholarIds = Array.from(document.querySelectorAll('.scholar-checkbox:checked'))
                .map(checkbox => checkbox.getAttribute('data-scholar-id'))
                .join(', ');

            document.getElementById('emailTo').value = selectedEmails;
            document.getElementById('scholarId').value = selectedScholarIds;

            if (emailModal) {
                emailModal.classList.remove('hidden');
            }
        });
    }

    // Close email modal
    if (cancelEmail) {
        cancelEmail.addEventListener('click', function() {
            if (emailModal) {
                emailModal.classList.add('hidden');
            }
        });
    }

    // Close email modal with close button
    if (closeEmailModal) {
        closeEmailModal.addEventListener('click', function() {
            if (emailModal) {
                emailModal.classList.add('hidden');
            }
        });
    }

    // Handle email form submission
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading indicator
            if (emailLoading) {
                emailLoading.classList.remove('hidden');
            }
            if (sendEmailButton) {
                sendEmailButton.disabled = true;
            }
            
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading indicator
                if (emailLoading) {
                    emailLoading.classList.add('hidden');
                }
                if (sendEmailButton) {
                    sendEmailButton.disabled = false;
                }
                
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Email sent successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    if (emailModal) {
                        emailModal.classList.add('hidden');
                    }
                    emailForm.reset();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to send email: ' + data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                // Hide loading indicator
                if (emailLoading) {
                    emailLoading.classList.add('hidden');
                }
                if (sendEmailButton) {
                    sendEmailButton.disabled = false;
                }
                Swal.fire({
                    title: 'Error!',
                    text: 'Error sending email: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const emailModal = document.getElementById('emailModal');
        const announcementModal = document.getElementById('announcementModal');
        
        if (e.target === emailModal) {
            emailModal.classList.add('hidden');
        }
        if (e.target === announcementModal) {
            announcementModal.classList.add('hidden');
        }
    });
}

// Announcement functionality
function initializeAnnouncementFunctionality() {
    const generateAnnouncementBtn = document.getElementById('generateAnnouncementBtn');
    const announcementModal = document.getElementById('announcementModal');
    const announcementContent = document.getElementById('announcementContent');
    const closeAnnouncement = document.getElementById('closeAnnouncement');
    const copyAnnouncement = document.getElementById('copyAnnouncement');
    const closeAnnouncementModal = document.getElementById('closeAnnouncementModal');

    // Generate Announcement button functionality
    if (generateAnnouncementBtn) {
        generateAnnouncementBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'No Selection!',
                    text: 'Please select at least one scholar to copy names.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Group selected scholars by barangay
            const barangayGroups = {};
            selectedCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const name = row.querySelector('td:nth-child(2) div').textContent.trim();
                const barangay = row.querySelector('td:nth-child(3) div').textContent.trim();
                if (!barangayGroups[barangay]) {
                    barangayGroups[barangay] = [];
                }
                barangayGroups[barangay].push(name);
            });

            // Sort barangays alphabetically
            const sortedBarangays = Object.keys(barangayGroups).sort();

            // Build the output string
            let output = '';
            sortedBarangays.forEach(barangay => {
                output += `${barangay}\n`;
                // Sort names alphabetically within each barangay
                barangayGroups[barangay].sort().forEach((name, idx) => {
                    output += `${idx + 1}. ${name}\n`;
                });
                output += '\n';
            });

            navigator.clipboard.writeText(output.trim()).then(() => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Selected scholar names grouped by barangay copied to clipboard!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }).catch(err => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to copy names: ' + err,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // Copy announcement to clipboard
    if (copyAnnouncement) {
        copyAnnouncement.addEventListener('click', function() {
            if (announcementContent) {
                announcementContent.select();
                document.execCommand('copy');
                Swal.fire({
                    title: 'Success!',
                    text: 'Announcement copied to clipboard!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        });
    }

    // Close announcement modal
    if (closeAnnouncement) {
        closeAnnouncement.addEventListener('click', function() {
            if (announcementModal) {
                announcementModal.classList.add('hidden');
            }
        });
    }

    // Close announcement modal with close button
    if (closeAnnouncementModal) {
        closeAnnouncementModal.addEventListener('click', function() {
            if (announcementModal) {
                announcementModal.classList.add('hidden');
            }
        });
    }
}

// Documents modal functionality
function initializeDocumentsModal() {
    const documentsModal = document.getElementById('documentsModal');
    const closeDocumentsModal = document.getElementById('closeDocumentsModal');
    const closeDocuments = document.getElementById('closeDocuments');
    const viewDocumentsBtns = document.querySelectorAll('.view-documents-btn');

    // Function to load documents for a scholar
    async function loadScholarDocuments(scholarId) {
        const loadingElement = document.getElementById('documentsLoading');
        const contentElement = document.getElementById('documentsContent');
        const noDocumentsElement = document.getElementById('noDocumentsMessage');

        // Show loading, hide other elements
        if (loadingElement) loadingElement.classList.remove('hidden');
        if (contentElement) contentElement.classList.add('hidden');
        if (noDocumentsElement) noDocumentsElement.classList.add('hidden');

        try {
            const response = await fetch(`/lydo_admin/get-scholar-documents/${scholarId}`);
            const data = await response.json();

            // Hide loading
            if (loadingElement) loadingElement.classList.add('hidden');

            if (data.success && data.documents) {
                const documents = data.documents;
                
                // Clear previous content
                const certOfRegContainer = document.getElementById('certOfRegContainer');
                const gradeSlipContainer = document.getElementById('gradeSlipContainer');
                const brgyIndigencyContainer = document.getElementById('brgyIndigencyContainer');
                const renewalInfo = document.getElementById('renewalInfo');

                if (certOfRegContainer) certOfRegContainer.innerHTML = '';
                if (gradeSlipContainer) gradeSlipContainer.innerHTML = '';
                if (brgyIndigencyContainer) brgyIndigencyContainer.innerHTML = '';
                if (renewalInfo) renewalInfo.innerHTML = '';

                if (documents.length > 0 && contentElement) {
                    // Show documents content
                    contentElement.classList.remove('hidden');

                    documents.forEach(doc => {
                        // Certificate of Registration
                        if (doc.renewal_cert_of_reg && certOfRegContainer) {
                            const certOfRegDiv = createDocumentElement(doc.renewal_cert_of_reg, 'Certificate of Registration');
                            certOfRegContainer.appendChild(certOfRegDiv);
                        }

                        // Grade Slip
                        if (doc.renewal_grade_slip && gradeSlipContainer) {
                            const gradeSlipDiv = createDocumentElement(doc.renewal_grade_slip, 'Grade Slip');
                            gradeSlipContainer.appendChild(gradeSlipDiv);
                        }

                        // Barangay Indigency
                        if (doc.renewal_brgy_indigency && brgyIndigencyContainer) {
                            const brgyIndigencyDiv = createDocumentElement(doc.renewal_brgy_indigency, 'Barangay Indigency');
                            brgyIndigencyContainer.appendChild(brgyIndigencyDiv);
                        }

                        // Renewal Information
                        if (renewalInfo) {
                            const renewalInfoHTML = `
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <strong>Semester:</strong> ${doc.renewal_semester}
                                    </div>
                                    <div>
                                        <strong>Academic Year:</strong> ${doc.renewal_acad_year}
                                    </div>
                                    <div>
                                        <strong>Date Submitted:</strong> ${new Date(doc.date_submitted).toLocaleDateString()}
                                    </div>
                                    <div>
                                        <strong>Status:</strong> 
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${
                                            doc.renewal_status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                            doc.renewal_status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                            'bg-yellow-100 text-yellow-800'
                                        }">
                                            ${doc.renewal_status}
                                        </span>
                                    </div>
                                </div>
                            `;
                            renewalInfo.innerHTML = renewalInfoHTML;
                        }
                    });
                } else if (noDocumentsElement) {
                    // Show no documents message
                    noDocumentsElement.classList.remove('hidden');
                }
            } else if (noDocumentsElement) {
                // Show no documents message
                noDocumentsElement.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading documents:', error);
            if (loadingElement) loadingElement.classList.add('hidden');
            if (noDocumentsElement) noDocumentsElement.classList.remove('hidden');
        }
    }

    // Helper function to create document element
    function createDocumentElement(filePath, documentType) {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
        
        const fileName = filePath.split('/').pop();
        const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);
        
        div.innerHTML = `
            <span class="text-sm text-gray-700 truncate flex-1">${documentType}</span>
            <div class="flex space-x-2">
                <button type="button" 
                        class="view-document-btn px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"
                        data-file="${filePath}"
                        data-type="${isImage ? 'image' : 'file'}">
                    ${isImage ? 'View' : 'Download'}
                </button>
            </div>
        `;
        
        return div;
    }

    // Event listeners for view documents buttons
    viewDocumentsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const scholarId = this.getAttribute('data-scholar-id');
            loadScholarDocuments(scholarId);
            if (documentsModal) {
                documentsModal.classList.remove('hidden');
            }
        });
    });

    // Close modal events
    if (closeDocumentsModal) {
        closeDocumentsModal.addEventListener('click', function() {
            if (documentsModal) {
                documentsModal.classList.add('hidden');
            }
        });
    }

    if (closeDocuments) {
        closeDocuments.addEventListener('click', function() {
            if (documentsModal) {
                documentsModal.classList.add('hidden');
            }
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const documentsModal = document.getElementById('documentsModal');
        if (e.target === documentsModal) {
            documentsModal.classList.add('hidden');
        }
    });

    // Event delegation for document view/download buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-document-btn')) {
            const filePath = e.target.getAttribute('data-file');
            const fileType = e.target.getAttribute('data-type');
            
            if (fileType === 'image') {
                // Open image in new tab
                window.open(filePath, '_blank');
            } else {
                // Download file
                const link = document.createElement('a');
                link.href = filePath;
                link.download = filePath.split('/').pop();
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    });
}

// Initialize filter values from URL parameters
function initializeFiltersFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');
    const academicYearSelect = document.getElementById('academicYearSelect');
    const statusSelect = document.getElementById('statusSelect');
    
    if (searchInput && urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
    
    if (barangaySelect && urlParams.has('barangay')) {
        barangaySelect.value = urlParams.get('barangay');
    }
    
    if (academicYearSelect && urlParams.has('academic_year')) {
        academicYearSelect.value = urlParams.get('academic_year');
    }
    
    if (statusSelect && urlParams.has('status')) {
        statusSelect.value = urlParams.get('status');
    }
}

// Main initialization function
function initializeScholarPage() {
    initializeFiltersFromURL();
    initializeScholarData();
    initializeScholarPagination();
    initializeScholarFiltering();
    initializeCheckboxSelection();
    initializeEmailFunctionality();
    initializeAnnouncementFunctionality();
    initializeDocumentsModal();
    
    // Apply initial filtering based on URL parameters
    if (document.getElementById('statusSelect') || 
        document.getElementById('searchInput') || 
        document.getElementById('barangaySelect') || 
        document.getElementById('academicYearSelect')) {
        
        // Trigger filter to apply URL parameters
        const filterFunction = initializeScholarFiltering.toString().match(/function[^{]+\{([\s\S]*)\}$/)[1];
        if (filterFunction) {
            // This will trigger the filtering based on URL parameters
            setTimeout(() => {
                const event = new Event('change');
                if (document.getElementById('statusSelect')) {
                    document.getElementById('statusSelect').dispatchEvent(event);
                }
            }, 100);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeScholarPage();
});

// Handle browser back/forward buttons
window.addEventListener('popstate', function() {
    initializeFiltersFromURL();
    initializeScholarFiltering();
});