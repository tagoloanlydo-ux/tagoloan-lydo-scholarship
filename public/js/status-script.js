// Global variables
let currentApplicationId = null;
let currentApplicationName = null;
let currentView = 'table';
let currentDocumentUrls = {};

// Pagination state
const paginationState = {
    table: {
        currentPage: 1,
        rowsPerPage: 15,
        allRows: [],
        filteredRows: []
    },
    list: {
        currentPage: 1,
        rowsPerPage: 15,
        allRows: [],
        filteredRows: []
    }
};

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing...');
    initializeData();
    initializeModalEvents();
    initializePagination();
    initializeFiltering();
    initializeNotificationDropdown();
    initializeSidebarDropdown();
    
    // Hide loading spinner when page is fully loaded, with minimum display time
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        setTimeout(() => {
            loadingOverlay.classList.add('fade-out');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 1000);
        }, 2000);
    }
});

// Initialize modal events
function initializeModalEvents() {
    // View intake sheet buttons
    document.querySelectorAll('.view-intake-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            console.log('Opening modal for:', id, name);
            openIntakeSheetModal(id, name, 'intake');
        });
    });

    // Approve button
    const approveBtn = document.getElementById('approveBtn');
    if (approveBtn) {
        approveBtn.addEventListener('click', function() {
            if (currentApplicationId && currentApplicationName) {
                approveApplication(currentApplicationId, currentApplicationName);
            }
        });
    }

    // Reject button
    const rejectBtn = document.getElementById('rejectBtn');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            if (currentApplicationId && currentApplicationName) {
                rejectApplication(currentApplicationId, currentApplicationName);
            }
        });
    }

    // Close modal when clicking X button
    const modalCloseBtn = document.querySelector('.modal-close');
    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', closeIntakeSheetModal);
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('intakeSheetModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeIntakeSheetModal();
            }
        });
    }
}

// Initialize data from the tables
function initializeData() {
    // Get ALL table rows (not just visible ones)
    const tableRows = Array.from(document.querySelectorAll('#tableView tbody tr'));
    paginationState.table.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    paginationState.table.filteredRows = [...paginationState.table.allRows];
    
    // Get ALL list rows
    const listRows = Array.from(document.querySelectorAll('#listView tbody tr'));
    paginationState.list.allRows = listRows.filter(row => !row.querySelector('td[colspan]'));
    paginationState.list.filteredRows = [...paginationState.list.allRows];
}

// Initialize pagination
function initializePagination() {
    updatePagination('table');
    updatePagination('list');
}

// Update pagination display
function updatePagination(viewType) {
    const state = paginationState[viewType];
    const containerId = viewType === 'table' ? 'tablePagination' : 'listPagination';
    const container = document.getElementById(containerId);
    
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
        <div class="pagination-info">
            Showing ${startItem} to ${endItem} of ${state.filteredRows.length} entries
        </div>
        <div class="pagination-buttons">
            <button class="pagination-btn" onclick="changePage('${viewType}', 1)" ${state.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-angle-double-left"></i>
            </button>
            <button class="pagination-btn" onclick="changePage('${viewType}', ${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-angle-left"></i>
            </button>
            <div class="pagination-page-info">
                Page <input type="number" class="pagination-page-input" value="${state.currentPage}" min="1" max="${totalPages}" onchange="goToPage('${viewType}', this.value)"> of ${totalPages}
            </div>
            <button class="pagination-btn" onclick="changePage('${viewType}', ${state.currentPage + 1})" ${state.currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-angle-right"></i>
            </button>
            <button class="pagination-btn" onclick="changePage('${viewType}', ${totalPages})" ${state.currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-angle-double-right"></i>
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

// Initialize filtering functionality
function initializeFiltering() {
    populateBarangayFilters();
    
    const tableNameSearch = document.getElementById('searchInputTable');
    const tableBarangayFilter = document.getElementById('barangaySelectTable');
    
    const listNameSearch = document.getElementById('listNameSearch');
    const listBarangayFilter = document.getElementById('listBarangayFilter');
    const listStatusFilter = document.getElementById('listStatusFilter');

    // Table View Filtering
    function filterTableView() {
        const searchTerm = tableNameSearch.value.toLowerCase();
        const selectedBarangay = tableBarangayFilter.value;

        const filteredRows = paginationState.table.allRows.filter(row => {
            const nameCell = row.cells[1];
            const barangayCell = row.cells[2];

            if (!nameCell || !barangayCell) return false;

            const name = nameCell.textContent.toLowerCase();
            const barangay = barangayCell.textContent.trim();

            const nameMatch = name.includes(searchTerm);
            const barangayMatch = !selectedBarangay || barangay === selectedBarangay;

            return nameMatch && barangayMatch;
        });

        // Update filtered rows and reset to page 1
        paginationState.table.filteredRows = filteredRows;
        paginationState.table.currentPage = 1;
        updatePagination('table');
    }

    // List View Filtering
    function filterListView() {
        const searchTerm = listNameSearch.value.toLowerCase();
        const selectedBarangay = listBarangayFilter.value;
        const selectedStatus = listStatusFilter.value;

        const filteredRows = paginationState.list.allRows.filter(row => {
            const nameCell = row.cells[1];
            const barangayCell = row.cells[2];
            const statusCell = row.cells[4];

            if (!nameCell || !barangayCell || !statusCell) return false;

            const name = nameCell.textContent.toLowerCase();
            const barangay = barangayCell.textContent.trim();
            const status = statusCell.textContent.trim();

            const nameMatch = name.includes(searchTerm);
            const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
            const statusMatch = !selectedStatus || status.toLowerCase() === selectedStatus.toLowerCase();

            return nameMatch && barangayMatch && statusMatch;
        });

        // Update filtered rows and reset to page 1
        paginationState.list.filteredRows = filteredRows;
        paginationState.list.currentPage = 1;
        updatePagination('list');
    }

    // Add event listeners with debouncing
    if (tableNameSearch) {
        tableNameSearch.addEventListener('input', debounce(filterTableView, 300));
    }
    if (tableBarangayFilter) {
        tableBarangayFilter.addEventListener('change', filterTableView);
    }
    
    if (listNameSearch) {
        listNameSearch.addEventListener('input', debounce(filterListView, 300));
    }
    if (listBarangayFilter) {
        listBarangayFilter.addEventListener('change', filterListView);
    }
    if (listStatusFilter) {
        listStatusFilter.addEventListener('change', filterListView);
    }
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

// Populate barangay filters
function populateBarangayFilters() {
    const barangays = getUniqueBarangays();
    
    const tableBarangayFilter = document.getElementById('barangaySelectTable');
    const listBarangayFilter = document.getElementById('listBarangayFilter');

    const populateDropdown = (dropdown) => {
        if (!dropdown) return;
        dropdown.innerHTML = '<option value="">All Barangays</option>';
        barangays.forEach(barangay => {
            const option = document.createElement('option');
            option.value = barangay;
            option.textContent = barangay;
            dropdown.appendChild(option);
        });
    };

    populateDropdown(tableBarangayFilter);
    populateDropdown(listBarangayFilter);
}

// Get unique barangays
function getUniqueBarangays() {
    const barangays = new Set();
    
    document.querySelectorAll('#tableView tbody tr, #listView tbody tr').forEach(row => {
        const barangayCell = row.cells[2];
        if (barangayCell) {
            const barangay = barangayCell.textContent.trim();
            if (barangay) barangays.add(barangay);
        }
    });
    
    return Array.from(barangays).sort();
}

// Tab switching functions
function showTable() {
    document.getElementById('loadingOverlay').style.display = 'flex';
    document.getElementById('loadingOverlay').classList.remove('fade-out');

    setTimeout(() => {
        document.getElementById('tableView').classList.remove('hidden');
        document.getElementById('listView').classList.add('hidden');
        document.getElementById('tab-pending').classList.add('active');
        document.getElementById('tab-approved-rejected').classList.remove('active');
        currentView = 'table';
        
        // Reset to first page
        paginationState.table.currentPage = 1;
        updatePagination('table');
        
        document.getElementById('loadingOverlay').classList.add('fade-out');
        setTimeout(() => {
            document.getElementById('loadingOverlay').style.display = 'none';
        }, 1000);
    }, 300);
}

function showList() {
    document.getElementById('loadingOverlay').style.display = 'flex';
    document.getElementById('loadingOverlay').classList.remove('fade-out');

    setTimeout(() => {
        document.getElementById('tableView').classList.add('hidden');
        document.getElementById('listView').classList.remove('hidden');
        document.getElementById('tab-pending').classList.remove('active');
        document.getElementById('tab-approved-rejected').classList.add('active');
        currentView = 'list';
        
        // Reset to first page
        paginationState.list.currentPage = 1;
        updatePagination('list');
        
        document.getElementById('loadingOverlay').classList.add('fade-out');
        setTimeout(() => {
            document.getElementById('loadingOverlay').style.display = 'none';
        }, 1000);
    }, 300);
}

// Modal functions
function closeIntakeSheetModal() {
    document.getElementById('intakeSheetModal').style.display = 'none';
    currentApplicationId = null;
    currentApplicationName = null;
}

function openIntakeSheetModal(id, name, type = 'intake') {
    console.log('openIntakeSheetModal called with:', { id, name, type });
    
    if (!id) {
        console.error('No ID provided for modal');
        Swal.fire('Error', 'No application ID provided.', 'error');
        return;
    }

    currentApplicationId = id;
    currentApplicationName = name;

    // Show loading
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'flex';
    }

    // Fetch intake sheet data
    fetch(`/mayor_staff/intake-sheet/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            
            if (data.success && data.intakeSheet) {
                populateIntakeSheetModal(data.intakeSheet, type);
                // Display the modal
                const modal = document.getElementById('intakeSheetModal');
                if (modal) {
                    modal.style.display = 'block';
                    console.log('Modal displayed successfully');
                } else {
                    console.error('Modal element not found');
                }
            } else {
                throw new Error(data.message || 'Failed to load intake sheet data');
            }
            
            // Hide loading
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching intake sheet:', error);
            Swal.fire('Error', 'Failed to load intake sheet data.', 'error');
            
            // Hide loading
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        });
}

// Document handling functions
function setupDocumentButtons(data) {
    // Store document URLs globally
    currentDocumentUrls = {
        application_letter: data.doc_application_letter || null,
        cert_reg: data.doc_cert_reg || null,
        grade_slip: data.doc_grade_slip || null,
        brgy_indigency: data.doc_brgy_indigency || null,
        student_id: data.doc_student_id || null
    };

    // Setup each document button
    setupDocumentButton('application_letter', data.doc_application_letter, 'modal-doc-application-letter-status', 'modal-doc-application-letter-btn');
    setupDocumentButton('cert_reg', data.doc_cert_reg, 'modal-doc-cert-reg-status', 'modal-doc-cert-reg-btn');
    setupDocumentButton('grade_slip', data.doc_grade_slip, 'modal-doc-grade-slip-status', 'modal-doc-grade-slip-btn');
    setupDocumentButton('brgy_indigency', data.doc_brgy_indigency, 'modal-doc-brgy-indigency-status', 'modal-doc-brgy-indigency-btn');
    setupDocumentButton('student_id', data.doc_student_id, 'modal-doc-student-id-status', 'modal-doc-student-id-btn');
}

function setupDocumentButton(docType, docUrl, statusElementId, buttonElementId) {
    const statusElement = document.getElementById(statusElementId);
    const buttonElement = document.getElementById(buttonElementId);
    
    if (docUrl) {
        statusElement.textContent = 'Available';
        statusElement.className = 'text-green-600 font-semibold';
        buttonElement.classList.remove('hidden');
    } else {
        statusElement.textContent = 'Not Available';
        statusElement.className = 'text-red-600';
        buttonElement.classList.add('hidden');
    }
}

function openDocument(docType) {
    const docUrl = currentDocumentUrls[docType];

    if (docUrl) {
        // Open document in new tab
        window.open(docUrl, '_blank');
    } else {
        Swal.fire('Document Not Available', 'This document is not available for viewing.', 'warning');
    }
}

// Function to format date
function formatDate(dateString) {
    if (!dateString || dateString === '-') return '-';

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString; // Return original if invalid

    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    return date.toLocaleDateString('en-US', options);
}

// Populate modal with data - COMPREHENSIVE DEBUGGING VERSION
function populateIntakeSheetModal(data, type = 'intake') {
    console.log('=== START POPULATE MODAL DEBUG ===');
    console.log('Full data received:', data);
    
    // Populate head of family section
    document.getElementById('modal-applicant-name').textContent = data.applicant_name || '-';
    document.getElementById('modal-applicant-gender').textContent = data.applicant_gender || '-';
    document.getElementById('modal-remarks').textContent = data.remarks || '-';
    document.getElementById('modal-head-dob').textContent = data.head_dob || '-';
    document.getElementById('modal-head-pob').textContent = data.head_pob || '-';
    document.getElementById('modal-head-address').textContent = data.head_address || '-';
    document.getElementById('modal-head-zone').textContent = data.head_zone || '-';
    document.getElementById('modal-head-barangay').textContent = data.head_barangay || '-';
    document.getElementById('modal-head-religion').textContent = data.head_religion || '-';
    document.getElementById('modal-serial-number').textContent = data.serial_number || '-';
    document.getElementById('modal-head-4ps').textContent = data.head_4ps || '-';
    document.getElementById('modal-head-ipno').textContent = data.head_ipno || '-';
    document.getElementById('modal-head-educ').textContent = data.head_educ || '-';
    document.getElementById('modal-head-occ').textContent = data.head_occ || '-';

    // Populate household information
    document.getElementById('modal-house-total-income').textContent = data.house_total_income || '-';
    document.getElementById('modal-house-net-income').textContent = data.house_net_income || '-';
    document.getElementById('modal-other-income').textContent = data.other_income || '-';
    document.getElementById('modal-house-house').textContent = data.house_house || '-';
    document.getElementById('modal-house-lot').textContent = data.house_lot || '-';
    document.getElementById('modal-house-electric').textContent = data.house_electric || '-';
    document.getElementById('modal-house-water').textContent = data.house_water || '-';

    // DEBUG: Comprehensive family members analysis
    console.log('=== FAMILY MEMBERS ANALYSIS ===');
    console.log('Family Members Data:', data.family_members);
    console.log('Family Members Type:', typeof data.family_members);
    console.log('Family Members Count:', data.family_members ? data.family_members.length : 0);
    
    if (data.family_members && data.family_members.length > 0) {
        console.log('First Family Member Full Object:', data.family_members[0]);
        console.log('First Family Member Keys:', Object.keys(data.family_members[0]));
        
        // Check each field in first member
        const firstMember = data.family_members[0];
        console.log('Field Values in First Member:');
        console.log('- NAME:', firstMember.NAME, '(exists:', firstMember.NAME !== undefined, ')');
        console.log('- RELATION:', firstMember.RELATION, '(exists:', firstMember.RELATION !== undefined, ')');
        console.log('- BIRTHDATE:', firstMember.BIRTHDATE, '(exists:', firstMember.BIRTHDATE !== undefined, ')');
        console.log('- AGE:', firstMember.AGE, '(exists:', firstMember.AGE !== undefined, ')');
        console.log('- SEX:', firstMember.SEX, '(exists:', firstMember.SEX !== undefined, ')');
        console.log('- CIVIL STATUS:', firstMember['CIVIL STATUS'], '(exists:', firstMember['CIVIL STATUS'] !== undefined, ')');
        console.log('- EDUCATIONAL ATTAINMENT:', firstMember['EDUCATIONAL ATTAINMENT'], '(exists:', firstMember['EDUCATIONAL ATTAINMENT'] !== undefined, ')');
        console.log('- OCCUPATION:', firstMember.OCCUPATION, '(exists:', firstMember.OCCUPATION !== undefined, ')');
        console.log('- INCOME:', firstMember.INCOME, '(exists:', firstMember.INCOME !== undefined, ')');
        console.log('- REMARKS:', firstMember.REMARKS, '(exists:', firstMember.REMARKS !== undefined, ')');
        
        // Also check for lowercase fields
        console.log('Lowercase field checks:');
        console.log('- name:', firstMember.name, '(exists:', firstMember.name !== undefined, ')');
        console.log('- relationship:', firstMember.relationship, '(exists:', firstMember.relationship !== undefined, ')');
        console.log('- birthdate:', firstMember.birthdate, '(exists:', firstMember.birthdate !== undefined, ')');
    }

    // Populate family members - COMPREHENSIVE FIELD CHECKING
    const familyMembersTbody = document.getElementById('modal-family-members');
    familyMembersTbody.innerHTML = '';
    
    if (data.family_members && data.family_members.length > 0) {
        console.log('Rendering family members table...');
        
        data.family_members.forEach((member, index) => {
            console.log(`Processing family member ${index + 1}:`, member);
            
            // Comprehensive field checking with fallbacks
            const name = member.NAME || member.name || 'NO NAME';
            const relation = member.RELATION || member.relationship || member.relation || 'NO RELATION';
            const birthdate = member.BIRTHDATE || member.birthdate || 'NO BIRTHDATE';
            const age = member.AGE || member.age || 'NO AGE';
            const sex = member.SEX || member.sex || member.gender || 'NO SEX';
            const civilStatus = member['CIVIL STATUS'] || member.civil_status || 'NO CIVIL STATUS';
            const education = member['EDUCATIONAL ATTAINMENT'] || member.education || 'NO EDUCATION';
            const occupation = member.OCCUPATION || member.occupation || 'NO OCCUPATION';
            const income = member.INCOME || member.monthly_income || member.income || 'NO INCOME';
            const remarks = member.REMARKS || member.remarks || 'NO REMARKS';
            
            console.log(`Member ${index + 1} final values:`, {
                name, relation, birthdate, age, sex, civilStatus, education, occupation, income, remarks
            });
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${name}</td>
                <td>${relation}</td>
                <td>${birthdate}</td>
                <td>${age}</td>
                <td>${sex}</td>
                <td>${civilStatus}</td>
                <td>${education}</td>
                <td>${occupation}</td>
                <td>${income}</td>
                <td>${remarks}</td>
            `;
            familyMembersTbody.appendChild(row);
        });
        console.log('Family members table rendered successfully');
    } else {
        console.log('No family members to display');
        familyMembersTbody.innerHTML = '<tr><td colspan="10" class="text-center py-4">No family members found</td></tr>';
    }

    // DEBUG: Service records analysis
    console.log('=== SERVICE RECORDS ANALYSIS ===');
    console.log('Service Records Data:', data.social_service_records);
    console.log('Service Records Count:', data.social_service_records ? data.social_service_records.length : 0);

    // Populate service records
    const serviceRecordsTbody = document.getElementById('modal-service-records');
    serviceRecordsTbody.innerHTML = '';
    
    if (data.social_service_records && data.social_service_records.length > 0) {
        console.log('Rendering service records table...');
        data.social_service_records.forEach((record, index) => {
            console.log(`Processing service record ${index + 1}:`, record);
            
            const date = record.DATE || record.date || '-';
            const problem = record['PROBLEM/NEED'] || record.problem || record.problem_need || '-';
            const action = record['ACTION/ASSISTANCE GIVEN'] || record.action || record.action_assistance || '-';
            const remarks = record.REMARKS || record.remarks || '-';
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${date}</td>
                <td>${problem}</td>
                <td>${action}</td>
                <td>${remarks}</td>
            `;
            serviceRecordsTbody.appendChild(row);
        });
        console.log('Service records table rendered successfully');
    } else {
        console.log('No service records to display');
        serviceRecordsTbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">No service records found</td></tr>';
    }

    // Setup document buttons
    setupDocumentButtons(data);

    // Populate signatures and photos
    document.getElementById('modal-worker-fullname').textContent = data.worker_name || '-';
    document.getElementById('modal-officer-fullname').textContent = data.officer_name || '-';
    document.getElementById('modal-date-entry').textContent = formatDate(data.date_entry);

    // Handle signatures with better error handling
    const workerSignatureDiv = document.getElementById('modal-worker-signature');
    const officerSignatureDiv = document.getElementById('modal-officer-signature');
    const clientSignatureDiv = document.getElementById('modal-client-signature-large');

    // Worker signature
    if (data.signature_worker_data) {
        workerSignatureDiv.innerHTML = `<img src="${data.signature_worker_data}" alt="Worker Signature" style="max-width:180px;height:60px;object-fit:contain; border: 1px solid #ccc;">`;
    } else {
        workerSignatureDiv.innerHTML = '<p class="text-xs text-gray-500">No signature available</p>';
    }

    // Officer signature  
    if (data.signature_officer_data) {
        officerSignatureDiv.innerHTML = `<img src="${data.signature_officer_data}" alt="Officer Signature" style="max-width:180px;height:60px;object-fit:contain; border: 1px solid #ccc;">`;
    } else {
        officerSignatureDiv.innerHTML = '<p class="text-xs text-gray-500">No signature available</p>';
    }

    // Client signature
    if (data.signature_client_data) {
        clientSignatureDiv.innerHTML = `<img src="${data.signature_client_data}" alt="Client Signature" style="max-width:300px;height:80px;object-fit:contain; border: 1px solid #ccc;">`;
    } else {
        clientSignatureDiv.innerHTML = '<p class="text-xs text-gray-500">No signature available</p>';
    }

    console.log('=== END POPULATE MODAL DEBUG ===');
}

// Application approval/rejection functions
function approveApplication(id, name) {
    Swal.fire({
        title: 'Approve Application?',
        text: `Are you sure you want to approve ${name}'s application?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('loadingOverlay').classList.remove('fade-out');

            // Send approval request
            fetch(`/mayor_staff/status/${id}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: 'Approved'
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);

                if (data.success === true) {
                    Swal.fire('Approved!', `Application for ${name} has been approved.`, 'success')
                        .then(() => {
                            closeIntakeSheetModal();
                            // Refresh the data instead of full page reload
                            refreshTableData();
                        });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to approve application.', 'error');
                }
            })
            .catch(error => {
                console.error('Error approving application:', error);
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);
                Swal.fire('Error!', 'Failed to approve application. Please try again.', 'error');
            });
        }
    });
}

function rejectApplication(id, name) {
    Swal.fire({
        title: 'Reject Application?',
        text: `Please provide a reason for rejecting ${name}'s application:`,
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Enter rejection reason...',
        inputValidator: (value) => {
            if (!value || value.trim().length === 0) {
                return 'Rejection reason is required!';
            }
            if (value.length > 1000) {
                return 'Rejection reason must be less than 1000 characters!';
            }
            return null;
        },
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Reject Application',
        cancelButtonText: 'Cancel',
        inputAttributes: {
            maxlength: 1000,
            style: 'resize: vertical; min-height: 80px;'
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const rejectionReason = result.value.trim();

            // Show loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            document.getElementById('loadingOverlay').classList.remove('fade-out');

            // Send rejection request with reason
            fetch(`/mayor_staff/status/${id}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    status: 'Rejected',
                    reason: rejectionReason
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);

                if (data.success === true) {
                    Swal.fire('Rejected!', `Application for ${name} has been rejected.`, 'success')
                        .then(() => {
                            closeIntakeSheetModal();
                            refreshTableData();
                        });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to reject application.', 'error');
                }
            })
            .catch(error => {
                console.error('Error rejecting application:', error);
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 1000);
                Swal.fire('Error!', 'Failed to reject application. Please try again.', 'error');
            });
        }
    });
}

// Data refresh functions
function refreshTableData() {
    // Show loading
    document.getElementById('loadingOverlay').style.display = 'flex';
    document.getElementById('loadingOverlay').classList.remove('fade-out');

    // Fetch updated data
    fetch('/mayor_staff/status?ajax=1', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update table view
        updateTableView(data.tableApplicants);
        
        // Update list view
        updateListView(data.listApplications);
        
        // Hide loading
        document.getElementById('loadingOverlay').classList.add('fade-out');
        setTimeout(() => {
            document.getElementById('loadingOverlay').style.display = 'none';
        }, 1000);
    })
    .catch(error => {
        console.error('Error refreshing data:', error);
        // Fallback: reload the page
        location.reload();
    });
}

function updateTableView(applicants) {
    const tbody = document.querySelector('#tableView tbody');
    if (!tbody) return;

    if (applicants.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 border border-gray-200 text-gray-500">0 results</td></tr>';
        return;
    }

    let html = '';
    applicants.forEach((app, index) => {
        html += `
            <tr class="hover:bg-gray-50 border-b" data-name="${app.fname} ${app.mname} ${app.lname} ${app.suffix}" data-barangay="${app.barangay}" data-remarks="${app.remarks}">
                <td class="px-4 border border-gray-200 py-2 text-center">${index + 1}</td>
                <td class="px-4 border border-gray-200 py-2 text-center">
                    ${app.fname} ${app.mname} ${app.lname} ${app.suffix}
                </td>
                <td class="px-4 border border-gray-200 py-2 text-center">${app.barangay}</td>
                <td class="px-4 border border-gray-200 py-2 text-center">
                    ${app.school || 'N/A'}
                </td>
                <td class="px-4 border border-gray-200 py-2 text-center">
                    <span class="px-2 py-1 text-sm rounded-lg
                        ${app.remarks == 'Ultra Poor' ? 'bg-red-100 text-red-800' :
                          app.remarks == 'Poor' ? 'bg-yellow-100 text-yellow-800' :
                          'bg-gray-100 text-gray-800'}">
                        ${app.remarks}
                    </span>
                </td>
                <td class="px-4 py-2 border border-gray-200 text-center">
                    <div class="flex gap-2 justify-center">
                        <button
                            title="View Intake Sheet"
                            class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow view-intake-btn"
                            data-id="${app.application_personnel_id}"
                            data-name="${app.fname} ${app.mname} ${app.lname} ${app.suffix}">
                            <i class="fas fa-eye mr-1"></i> View
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    
    // Re-attach event listeners to new buttons
    initializeModalEvents();
    
    // Update pagination
    initializeData();
    updatePagination('table');
}

function updateListView(applications) {
    const tbody = document.querySelector('#listView tbody');
    if (!tbody) return;

    if (applications.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 border border-gray-200 text-gray-500">0 results</td></tr>';
        return;
    }

    let html = '';
    applications.forEach((app, index) => {
        html += `
            <tr class="hover:bg-gray-50 border-b" data-name="${app.fname} ${app.mname} ${app.lname} ${app.suffix}" data-barangay="${app.barangay}" data-status="${app.status}">
                <td class="px-4 border border-gray-200 py-2 text-center">${index + 1}</td>
                <td class="px-4 border border-gray-200 py-2 text-center">
                    ${app.fname} ${app.mname} ${app.lname} ${app.suffix}
                </td>
                <td class="px-4 border border-gray-200 py-2 text-center">${app.barangay}</td>
                <td class="px-4 border border-gray-200 py-2 text-center">
                    ${app.school || 'N/A'}
                </td>
                <td class="px-4 border border-gray-200 py-2 text-center">
                    <span class="px-2 py-1 text-sm rounded-lg
                        ${app.status == 'Approved' ? 'bg-green-100 text-green-800' :
                          app.status == 'Rejected' ? 'bg-red-100 text-red-800' :
                          'bg-gray-100 text-gray-800'}">
                        ${app.status}
                    </span>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    
    // Update pagination
    initializeData();
    updatePagination('list');
}

// Print function
function printCurrentTable() {
    const printWindow = window.open('', '_blank');
    const currentView = document.getElementById('tableView').classList.contains('hidden') ? 'list' : 'table';
    const tableElement = currentView === 'table' ? document.getElementById('tableView') : document.getElementById('listView');
    const tableTitle = currentView === 'table' ? 'Pending Status Applications' : 'Approved/Rejected Applications';

    // Get current date and time
    const now = new Date();
    const dateTime = now.toLocaleString();

    // Create print content
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${tableTitle} - LYDO Scholarship</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    color: #333;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #7c3aed;
                    padding-bottom: 20px;
                }
                .header img {
                    max-width: 80px;
                    margin-bottom: 10px;
                }
                .header h1 {
                    color: #7c3aed;
                    margin: 10px 0;
                    font-size: 24px;
                }
                .header p {
                    margin: 5px 0;
                    color: #666;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                    font-size: 12px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                    vertical-align: top;
                }
                th {
                    background-color: ${currentView === 'table' ? '#7c3aed' : '#10b981'};
                    color: white;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                tr:hover {
                    background-color: #f5f5f5;
                }
                .status-badge {
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 11px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .status-pending {
                    background-color: #fef3c7;
                    color: #92400e;
                }
                .status-approved {
                    background-color: #d1fae5;
                    color: #065f46;
                }
                .status-rejected {
                    background-color: #fee2e2;
                    color: #991b1b;
                }
                .remarks-badge {
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 11px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .remarks-ultra-poor {
                    background-color: #fee2e2;
                    color: #991b1b;
                }
                .remarks-poor {
                    background-color: #fef3c7;
                    color: #92400e;
                }
                .remarks-other {
                    background-color: #f3f4f6;
                    color: #374151;
                }
                .print-info {
                    text-align: center;
                    margin-top: 30px;
                    font-size: 10px;
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Logo">
                <h1>LYDO Scholarship Management System</h1>
                <h2>${tableTitle}</h2>
                <p>Generated on: ${dateTime}</p>
                <p>Tagoloan, Misamis Oriental</p>
            </div>

            ${tableElement.innerHTML}

            <div class="print-info">
                <p>This document was generated from the LYDO Scholarship Management System</p>
                <p>Printed by: {{ session('lydopers')->lydopers_fname }} {{ session('lydopers')->lydopers_lname }}</p>
            </div>
        </body>
        </html>
    `;

    printWindow.document.write(printContent);
    printWindow.document.close();

    // Wait for content to load then print
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}

// Logout confirmation function
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure you want to log out?',
        text: 'You will be redirected to the login page.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, Log Out',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit();
        }
    });
}

// Notification dropdown
function initializeNotificationDropdown() {
    const notifBell = document.getElementById('notifBell');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifBell && notifDropdown) {
        notifBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            notifDropdown.classList.add('hidden');
        });

        // Prevent dropdown from closing when clicking inside it
        notifDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Sidebar dropdown functions
function toggleDropdown(id) {
    const el = document.getElementById(id);
    if (!el) return;

    const btn = document.querySelector(`button[onclick="toggleDropdown('${id}')"]`);
    const chevron = btn ? btn.querySelector('i.bx') : null;

    const willOpen = el.classList.contains('hidden'); // if hidden now, will open
    // Toggle visibility
    el.classList.toggle('hidden');

    // Optional: rotate chevron for visual cue
    if (chevron) {
        chevron.classList.toggle('rotate-180', willOpen);
    }

    // Persist state in localStorage so it stays open across pages
    try {
        localStorage.setItem(`sidebarDropdown_${id}`, willOpen ? 'open' : 'closed');
    } catch (e) {
        console.warn('Could not persist dropdown state:', e);
    }
}

function initializeSidebarDropdown() {
    // Restore saved dropdown states on page load
    try {
        Object.keys(localStorage).forEach(key => {
            if (!key.startsWith('sidebarDropdown_')) return;
            const id = key.replace('sidebarDropdown_', '');
            const state = localStorage.getItem(key);
            const el = document.getElementById(id);
            if (!el) return;

            const btn = document.querySelector(`button[onclick="toggleDropdown('${id}')"]`);
            const chevron = btn ? btn.querySelector('i.bx') : null;

            if (state === 'open') {
                el.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                el.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        });
    } catch (e) {
        console.warn('initializeSidebarDropdown error:', e);
    }
}