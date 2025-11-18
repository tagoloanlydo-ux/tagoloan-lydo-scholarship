// screening.js - All JavaScript functionality

// Global variables
let currentReviewId = null; // Store ID for PDF printing
let paginationState = {
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

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing screening page...');
    
    // Initialize pagination
    initializePagination();
    
    // Set current date
    setCurrentDate();
    
    // Add event listeners for search and filter
    document.getElementById('nameSearch').addEventListener('input', debounce(filterTable, 300));
    document.getElementById('barangayFilter').addEventListener('change', filterTable);
    document.getElementById('listNameSearch').addEventListener('input', debounce(filterList, 300));
    document.getElementById('listBarangayFilter').addEventListener('change', filterList);

    // Initialize remarks validation
    validateRemarks();
});

// Pagination functions
function initializePagination() {
    console.log('Initializing pagination...');
    
    // Get ALL table rows (not just visible ones)
    const tableRows = Array.from(document.querySelectorAll('#tableView tbody tr'));
    paginationState.table.allRows = tableRows.filter(row => {
        const hasColspan = row.querySelector('td[colspan]');
        const hasCells = row.cells.length > 1;
        return !hasColspan && hasCells;
    });
    paginationState.table.filteredRows = [...paginationState.table.allRows];
    
    console.log('Table rows found:', paginationState.table.allRows.length);
    
    // Get ALL list rows
    const listRows = Array.from(document.querySelectorAll('#listView tbody tr'));
    paginationState.list.allRows = listRows.filter(row => {
        const hasColspan = row.querySelector('td[colspan]');
        const hasCells = row.cells.length > 1;
        return !hasColspan && hasCells;
    });
    paginationState.list.filteredRows = [...paginationState.list.allRows];
    
    console.log('List rows found:', paginationState.list.allRows.length);
    
    updatePagination('table');
    updatePagination('list');
}

function updatePagination(viewType) {
    const state = paginationState[viewType];
    const containerId = viewType === 'table' ? 'tablePagination' : 'listPagination';
    const container = document.getElementById(containerId);
    
    if (!container) {
        console.error('Pagination container not found:', containerId);
        return;
    }
    
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
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage) || 1;
    
    const startItem = state.filteredRows.length === 0 ? 0 : Math.min(startIndex + 1, state.filteredRows.length);
    const endItem = Math.min(endIndex, state.filteredRows.length);
    
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

function changePage(viewType, page) {
    const state = paginationState[viewType];
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage) || 1;
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updatePagination(viewType);
}

function goToPage(viewType, page) {
    const state = paginationState[viewType];
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage) || 1;
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updatePagination(viewType);
}

// Tab switching functions
function showTable() {
    document.getElementById('tableView').classList.remove('hidden');
    document.getElementById('listView').classList.add('hidden');
    document.getElementById('tab-screening').classList.add('active');
    document.getElementById('tab-reviewed').classList.remove('active');
    
    // Reset to first page
    paginationState.table.currentPage = 1;
    updatePagination('table');
}

function showList() {
    document.getElementById('tableView').classList.add('hidden');
    document.getElementById('listView').classList.remove('hidden');
    document.getElementById('tab-screening').classList.remove('active');
    document.getElementById('tab-reviewed').classList.add('active');
    
    // Reset to first page
    paginationState.list.currentPage = 1;
    updatePagination('list');
}

// Modal Tab switching functionality
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.add('hidden'));

    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
        button.classList.remove('text-violet-600');
        button.classList.add('text-gray-500');
        button.classList.remove('border-b-2', 'border-violet-600');
    });

    // Show the selected tab content
    document.getElementById('tab-' + tabName + '-content').classList.remove('hidden');

    // Add active class to the selected tab button
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('active', 'text-violet-600', 'border-b-2', 'border-violet-600');
    activeTab.classList.remove('text-gray-500');

    // Update progress bar and step indicator
    updateProgress(tabName);

    // If showing remarks tab, update financial summary
    if (tabName === 'remarks') {
        updateRemarksTab();
    }
}

// Update progress bar and step indicator
function updateProgress(tabName) {
    const stepInfo = {
        'family': { step: 1, title: 'Family Details', width: '16%' },
        'family-members': { step: 2, title: 'Family Members', width: '32%' },
        'additional': { step: 3, title: 'Additional Info', width: '48%' },
        'social-service': { step: 4, title: 'Social Service', width: '64%' },
        'health': { step: 5, title: 'Health & Signatures', width: '80%' },
        'remarks': { step: 6, title: 'Final Remarks', width: '100%' }
    };

    const info = stepInfo[tabName];
    if (info) {
        document.getElementById('current-step').textContent = info.step;
        document.getElementById('step-title').textContent = info.title;
        document.getElementById('progress-bar').style.width = info.width;
    }
}

// Update remarks tab with financial data
function updateRemarksTab() {
    // Copy values from additional info tab to remarks tab
    const totalIncome = document.getElementById('house_total_income').value;
    const netIncome = document.getElementById('house_net_income').value;
    
    document.getElementById('house_total_income_final').value = totalIncome;
    document.getElementById('house_net_income_final').value = netIncome;
    
    // Calculate total expenses
    const houseRent = parseFloat(document.getElementById('house_rent').value) || 0;
    const lotRent = parseFloat(document.getElementById('lot_rent').value) || 0;
    const houseWater = parseFloat(document.getElementById('house_water').value) || 0;
    const houseElectric = parseFloat(document.getElementById('house_electric').value) || 0;
    const totalExpenses = houseRent + lotRent + houseWater + houseElectric;
    
    document.getElementById('total_expenses_final').value = totalExpenses.toFixed(2);
    
    // Validate remarks
    validateRemarks();
}

// Validate remarks selection
function validateRemarks() {
    const remarksSelect = document.getElementById('remarks');
    const submitBtn = document.getElementById('submitFormBtn');
    
    if (remarksSelect && submitBtn) {
        if (remarksSelect.value === '') {
            submitBtn.disabled = true;
            submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        }
    }
}

// Set current date
function setCurrentDate() {
    const today = new Date().toISOString().split('T')[0];
    const dateEntry = document.getElementById('date_entry');
    if (dateEntry) {
        dateEntry.value = today;
    }
}

// Edit Remarks Modal functions
function openEditRemarksModal(button) {
    console.log('Opening modal for button:', button);
    
    try {
        const id = button.getAttribute("data-id");
        const name = button.getAttribute("data-name");
        const fname = button.getAttribute("data-fname");
        const mname = button.getAttribute("data-mname");
        const lname = button.getAttribute("data-lname");
        const suffix = button.getAttribute("data-suffix");
        const bdate = button.getAttribute("data-bdate");
        const brgy = button.getAttribute("data-brgy");
        const gender = button.getAttribute("data-gender");

        console.log('Applicant data:', { id, name, fname, brgy });
        const form = document.getElementById('updateRemarksForm');
        form.action = `/lydo_staff/update-intake-sheet/${id}`;

        // Set basic values in the modal form
        document.getElementById('remarks_id').value = id || '';
        document.getElementById('applicant_full_name').textContent = name || 'Applicant';
        document.getElementById('applicant_fname').value = fname || '';
        document.getElementById('applicant_mname').value = mname || '';
        document.getElementById('applicant_lname').value = lname || '';
        document.getElementById('applicant_suffix').value = suffix || '';
        document.getElementById('head_dob').value = bdate || '';
        document.getElementById('head_barangay').value = brgy || '';
        
        if (gender) {
            document.getElementById('applicant_gender').value = gender;
        }

        // Generate serial number
        document.getElementById('serial_number').value = 'SN-' + Date.now();

        // Clear previous data
        document.getElementById('family_members_tbody').innerHTML = '';
        document.getElementById('rv_service_records_tbody').innerHTML = '';

        // Show the modal FIRST
        const modal = document.getElementById('editRemarksModal');
        if (!modal) {
            throw new Error('Modal element not found');
        }

        modal.classList.remove('hidden');
        document.body.classList.add('modal-open');

        // Reset to first tab
        showTab('family');

        // Show loading message
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait while we load the intake sheet',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Try to load existing data
        fetch(`/lydo_staff/intake-sheet/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('No existing data found');
                }
                return response.json();
            })
            .then(data => {
                Swal.close();
                if (data && Object.keys(data).length > 0) {
                    populateEditModal(data);
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Loaded',
                        text: 'Existing intake sheet data loaded successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.log('No existing data found, starting fresh');
                // Continue with empty form
            });

    } catch (error) {
        console.error('Error in openEditRemarksModal:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to open modal: ' + error.message,
            confirmButtonText: 'OK'
        });
    }
}

function closeEditRemarksModal() {
    document.getElementById('editRemarksModal').classList.add('hidden');
    document.body.classList.remove('modal-open');
}

// Review Modal functions
function openReviewModal(button) {
    const id = button.getAttribute("data-id");
    currentReviewId = id; // STORE THE ID FOR PDF PRINTING
    
    if (!id) {
        console.error('No ID provided');
        Swal.fire('Error', 'No applicant ID provided', 'error');
        return;
    }
    
    // Show loading state
    document.getElementById('modalReviewContent').innerHTML = '<div class="p-4 text-center">Loading intake sheet data...</div>';
    
    // Show the modal first
    const reviewModal = document.getElementById('reviewModal');
    if (reviewModal) {
        reviewModal.style.display = 'block';
        document.body.classList.add('modal-open');
    }
    
    // Fetch intake sheet data with error handling
    fetch(`/lydo_staff/intake-sheet/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Fetched intake sheet data:', data);
            if (data && Object.keys(data).length > 0) {
                populateReviewModal(data);
            } else {
                throw new Error('No data received from server');
            }
        })
        .catch(err => {
            console.error('Error fetching intake sheet data:', err);
            document.getElementById('modalReviewContent').innerHTML = `
                <div class="p-4 text-center text-red-600">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Error loading data: ${err.message}
                    <br><br>
                    <button onclick="closeReviewModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Close
                    </button>
                </div>
            `;
        });
}

function closeReviewModal() {
    const reviewModal = document.getElementById('reviewModal');
    if (reviewModal) {
        reviewModal.style.display = 'none';
    }
    document.body.classList.remove('modal-open');
}

// Populate Review Modal
function populateReviewModal(d) {
    if (!d) {
        console.error('No data received');
        return;
    }

    const modalContent = document.getElementById('modalReviewContent');

    // Add console logging to debug
    console.log('Received data:', d);

    // Check if required data exists before populating
    const fullName = [
        d.applicant_fname || '',
        d.applicant_mname || '',
        d.applicant_lname || '',
        d.applicant_suffix || ''
    ].filter(Boolean).join(' ');

    modalContent.innerHTML = `
        <div class="review-columns">
            <div class="space-y-4">
                <!-- Head of Family Section -->
                <div class="print-box p-4">
                    <h4 class="font-semibold mb-3">Head of Family</h4>
                    <table class="min-w-full text-sm">
                        <tr>
                            <td><strong>Serial No.:</strong> ${d.serial_number || "N/A"}</td>
                            <td><strong>Name:</strong> ${fullName || "N/A"}</td>
                            <td><strong>Sex:</strong> ${d.applicant_gender || "N/A"}</td>
                        </tr>
                        <tr>
                            <td><strong>4Ps:</strong> ${d.head_4ps || "N/A"}</td>
                            <td><strong>IP No.:</strong> ${d.head_ipno || "N/A"}</td>
                            <td><strong>Address:</strong> ${d.head_address || "N/A"}</td>
                        </tr>
                        <tr>
                            <td><strong>Zone:</strong> ${d.head_zone || "N/A"}</td>
                            <td><strong>Barangay:</strong> ${d.head_barangay || "N/A"}</td>
                            <td><strong>Date of Birth:</strong> ${formatDate(d.head_dob)}</td>
                        </tr>
                        <tr>
                            <td><strong>Place of Birth:</strong> ${d.head_pob || "N/A"}</td>
                            <td><strong>Education:</strong> ${d.head_educ || "N/A"}</td>
                            <td><strong>Occupation:</strong> ${d.head_occ || "N/A"}</td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>Religion:</strong> ${d.head_religion || "N/A"}</td>
                        </tr>
                    </table>
                </div>

                <!-- Family Members Section -->
                <div class="print-box p-4">
                    <h4 class="font-semibold mb-3">Family Members</h4>
                    <table class="min-w-full text-sm border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">Name</th>
                                <th class="border px-2 py-1">Relation</th>
                                <th class="border px-2 py-1">Birthdate</th>
                                <th class="border px-2 py-1">Age</th>
                                <th class="border px-2 py-1">Sex</th>
                                <th class="border px-2 py-1">Civil Status</th>
                                <th class="border px-2 py-1">Educational Attainment</th>
                                <th class="border px-2 py-1">Occupation</th>
                                <th class="border px-2 py-1">Income</th>
                                <th class="border px-2 py-1">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(() => {
                                let familyMembers = [];
                                if (d.family_members) {
                                    if (typeof d.family_members === 'string') {
                                        try {
                                            familyMembers = JSON.parse(d.family_members);
                                        } catch (e) {
                                            familyMembers = [];
                                        }
                                    } else if (Array.isArray(d.family_members)) {
                                        familyMembers = d.family_members;
                                    }
                                }
                                return familyMembers.length > 0 ? familyMembers.map(f => `
                                    <tr>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(f.name || '')}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(f.relationship || f.relation || '')}</td>
                                        <td class="border px-2 py-1 text-left">${formatDate(f.birthdate || f.birth)}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(f.age || '')}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(f.sex || '')}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(f.civil_status || f.civil || '')}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(f.education || f.educ || '')}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(f.occupation || f.occ || '')}</td>
                                        <td class="border px-2 py-1 text-left">₱${escapeHtml(f.monthly_income || f.income || '')}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(f.remarks || '')}</td>
                                    </tr>
                                `).join('') : '<tr><td colspan="10" class="border px-2 py-1 text-center text-gray-500">No family members found</td></tr>';
                            })()}
                        </tbody>
                    </table>
                </div>

                <!-- Household Information Section -->
                <div class="print-box p-4">
                    <h4 class="font-semibold mb-3">Household Information</h4>
                    <table class="min-w-full text-sm">
                        <tr>
                            <td><strong>Other Source of Income:</strong> ₱${d.other_income || "-"}</td>
                            <td><strong>Total Family Income:</strong> ₱${d.house_total_income || "-"}</td>
                            <td><strong>Total Family Net Income:</strong> ₱${d.house_net_income || "-"}</td>
                        </tr>
                        <tr>
                            <td><strong>House (Owned/Rented):</strong> ${d.house_house || "-"} ${d.house_rent ? `(Rent: ₱${d.house_rent})` : ''}</td>
                            <td><strong>Lot (Owned/Rented):</strong> ${d.house_lot || "-"} ${d.lot_rent ? `(Rent: ₱${d.lot_rent})` : ''}</td>
                            <td><strong>Water:</strong> ₱${d.house_water || "-"}</td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>Electricity Source:</strong> ₱${d.house_electric || "-"}</td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>Remarks:</strong> ${d.remarks || "N/A"}</td>
                        </tr>
                    </table>
                </div>

                <!-- Social Service Records Section -->
                <div class="print-box p-4">
                    <h4 class="font-semibold mb-3">Social Service Records</h4>
                    <table class="min-w-full text-sm thin-border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-2 py-1">Date</th>
                                <th class="border px-2 py-1">Problem/Need</th>
                                <th class="border px-2 py-1">Action/Assistance Given</th>
                                <th class="border px-2 py-1">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(() => {
                                let serviceRecords = d.rv_service_records;
                                if (typeof serviceRecords === 'string') {
                                    try {
                                        serviceRecords = JSON.parse(serviceRecords);
                                    } catch (e) {
                                        serviceRecords = [];
                                    }
                                }
                                return Array.isArray(serviceRecords) && serviceRecords.length > 0 ? serviceRecords.map(r => `
                                    <tr>
                                        <td class="border px-2 py-1 text-left">${formatDate(r.date)}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(r.problem || '')}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(r.action || '')}</td>
                                        <td class="border px-2 py-1 text-left">${escapeHtml(r.remarks || '')}</td>
                                    </tr>
                                `).join('') : '<tr><td colspan="4" class="border px-2 py-1 text-center text-gray-500">No social service records found</td></tr>';
                            })()}
                        </tbody>
                    </table>
                </div>

                <!-- Worker Information Section -->
                <div class="print-box p-4">
                    <h4 class="font-semibold mb-3">Worker Information</h4>
                    <table class="min-w-full text-sm">
                        <tr>
                            <td><strong>Worker Name:</strong> ${d.worker_name || "N/A"}</td>
                            <td><strong>Officer Name:</strong> ${d.officer_name || "N/A"}</td>
                            <td><strong>Date Entry:</strong> ${formatDate(d.date_entry)}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    `;
}

// Family Members Functions
function addFamilyMemberRow() {
    const tbody = document.getElementById('family_members_tbody');

    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="border px-2 py-1">
            <input type="text" name="family_member_name[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="Full Name" oninput="calculateIncomes()">
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_relation[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
          <option value="">Select</option>
          <option value="Spouse">Spouse</option>
          <option value="Son">Son</option>
          <option value="Daughter">Daughter</option>
          <option value="Father">Father</option>
          <option value="Mother">Mother</option>
          <option value="Brother">Brother</option>
          <option value="Sister">Sister</option>
          <option value="Grandchild">Grandchild</option>
          <option value="Other">Other</option>
            </select>
        </td>
        <td class="border px-2 py-1">
            <input type="date" name="family_member_birthdate[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" onchange="calculateAge(this)">
        </td>
        <td class="border px-2 py-1">
            <input type="number" name="family_member_age[]" class="w-full border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-600" placeholder="Age" readonly>
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_sex[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                <option value="">Select Sex</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_civil_status[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
          <option value="">Select</option>
          <option value="Single">Single</option>
          <option value="Married">Married</option>
          <option value="Widowed">Widowed</option>
          <option value="Divorced">Divorced</option>
          <option value="Separated">Separated</option>
            </select>
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_education[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
          <option value="">Select</option>
          <option value="None">None</option>
          <option value="Elementary">Elementary</option>
          <option value="High School">High School</option>
          <option value="College">College</option>
          <option value="Vocational">Vocational</option>
          <option value="Graduate">Graduate</option>
            </select>
        </td>
        <td class="border px-2 py-1">
            <input type="text" name="family_member_occupation[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="Occupation">
        </td>
        <td class="border px-2 py-1">
            <input type="number" step="0.01" name="family_member_income[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200" placeholder="0.00" oninput="calculateIncomes()">
        </td>
        <td class="border px-2 py-1">
            <select name="family_member_remarks[]" class="w-full border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                     <option value="">Select</option>
          <option value="CIC">CIC</option>
          <option value="OSY">OSY</option>
          <option value="SP">SP</option>
          <option value="PWD">PWD</option>
          <option value="SC">SC</option>
          <option value="None">None</option>
          <option value="Lactating Mother">Lactating Mother</option>
          <option value="Pregnant Mother">Pregnant Mother</option>
            </select>
        </td>
        <td class="border px-2 py-1 text-center">
            <button type="button" onclick="this.parentElement.parentElement.remove(); calculateIncomes();" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors duration-200">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
}

// Function to calculate age from birthdate
function calculateAge(birthdateInput) {
    const birthdate = new Date(birthdateInput.value);
    const today = new Date();

    if (isNaN(birthdate)) {
        return;
    }

    let age = today.getFullYear() - birthdate.getFullYear();
    const monthDiff = today.getMonth() - birthdate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
        age--;
    }

    // Find the age input in the same row
    const row = birthdateInput.closest('tr');
    const ageInput = row.querySelector('input[name="family_member_age[]"]');
    if (ageInput) {
        ageInput.value = age;
    }
}

// Service Records Functions
function addRvServiceRecordRow() {
    const tbody = document.getElementById('rv_service_records_tbody');

    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="border px-2 py-1">
            <input type="date" name="service_record_date[]" class="w-full border-none focus:ring-0" value="${new Date().toISOString().split('T')[0]}">
        </td>
        <td class="border px-2 py-1">
            <input type="text" name="service_record_problem[]" class="w-full border-none focus:ring-0" placeholder="Problem/Need">
        </td>
        <td class="border px-2 py-1">
            <input type="text" name="service_record_action[]" class="w-full border-none focus:ring-0" placeholder="Action/Assistance">
        </td>
        <td class="border px-2 py-1">
            <select name="service_record_remarks[]" class="w-full border-none focus:ring-0">
                <option value="">Select Remarks</option>
                <option value="Poor">Poor</option>
                <option value="Ultra Poor">Ultra Poor</option>
                <option value="Non Poor">Non Poor</option>  
            </select>
        </td>
        <td class="border px-2 py-1 text-center">
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
}

// Filtering functions
function filterTable() {
    const nameSearchValue = document.getElementById('nameSearch').value.toLowerCase().trim();
    const barangayFilterValue = document.getElementById('barangayFilter').value.toLowerCase().trim();

    const filteredRows = paginationState.table.allRows.filter(row => {
        const nameCell = row.cells[1];
        const barangayCell = row.cells[2];

        if (!nameCell || !barangayCell) return false;

        const name = nameCell.textContent.toLowerCase();
        const barangay = barangayCell.textContent.toLowerCase();

        const matchesName = name.includes(nameSearchValue);
        const matchesBarangay = barangayFilterValue === '' || barangay.includes(barangayFilterValue);

        return matchesName && matchesBarangay;
    });

    // Update filtered rows and reset to page 1
    paginationState.table.filteredRows = filteredRows;
    paginationState.table.currentPage = 1;
    updatePagination('table');
}

function filterList() {
    const nameSearchValue = document.getElementById('listNameSearch').value.toLowerCase().trim();
    const barangayFilterValue = document.getElementById('listBarangayFilter').value.toLowerCase().trim();

    const filteredRows = paginationState.list.allRows.filter(row => {
        const nameCell = row.cells[1];
        const barangayCell = row.cells[2];

        if (!nameCell || !barangayCell) return false;

        const name = nameCell.textContent.toLowerCase();
        const barangay = barangayCell.textContent.toLowerCase();

        const matchesName = name.includes(nameSearchValue);
        const matchesBarangay = barangayFilterValue === '' || barangay.includes(barangayFilterValue);

        return matchesName && matchesBarangay;
    });

    // Update filtered rows and reset to page 1
    paginationState.list.filteredRows = filteredRows;
    paginationState.list.currentPage = 1;
    updatePagination('list');
}

// Income Calculation
function calculateIncomes() {
    console.log('Calculating incomes...');
    
    // 1. Calculate Total Family Income from Family Members
    let totalFamilyIncome = 0;
    const incomeInputs = document.querySelectorAll('input[name="family_member_income[]"]');
    incomeInputs.forEach(input => {
        const incomeValue = parseFloat(input.value) || 0;
        totalFamilyIncome += incomeValue;
    });
    console.log('Total Family Income from members:', totalFamilyIncome);

    // 2. Get Other Income and add to Total Income
    const otherIncome = parseFloat(document.getElementById('other_income').value) || 0;
    console.log('Other Income:', otherIncome);
    
    // 3. Calculate Total Income (Family Members Income + Other Income)
    const houseTotalIncome = totalFamilyIncome + otherIncome;
    console.log('Total Income (Family + Other):', houseTotalIncome);
    
    // Set total income
    document.getElementById('house_total_income').value = houseTotalIncome.toFixed(2);

    // 4. Calculate Total Expenses
    const houseRent = parseFloat(document.getElementById('house_rent').value) || 0;
    const lotRent = parseFloat(document.getElementById('lot_rent').value) || 0;
    const houseWater = parseFloat(document.getElementById('house_water').value) || 0;
    const houseElectric = parseFloat(document.getElementById('house_electric').value) || 0;
    
    // Total expenses (house rent + lot rent + water + electric)
    const totalExpenses = houseRent + lotRent + houseWater + houseElectric;
    console.log('Total Expenses:', totalExpenses);
    
    // 5. Calculate Net Income (Total Income - Total Expenses)
    const netIncome = houseTotalIncome - totalExpenses;
    console.log('Net Income:', netIncome);
    
    document.getElementById('house_net_income').value = netIncome.toFixed(2);

    // 6. Enable the Next button (removed disabled attribute)
    const additionalNextBtn = document.getElementById('additional-next-btn');
    if (additionalNextBtn) {
        additionalNextBtn.disabled = false;
    }
}

// Toggle house rent field visibility
function toggleHouseRent() {
    const houseSelect = document.getElementById('house_house');
    const houseRentGroup = document.getElementById('house_rent_group');
    
    if (houseSelect.value === 'Rent') {
        houseRentGroup.style.display = 'block';
    } else {
        houseRentGroup.style.display = 'none';
        document.getElementById('house_rent').value = '';
    }
    calculateIncomes();
}

// Toggle lot rent field visibility
function toggleLotRent() {
    const lotSelect = document.getElementById('house_lot');
    const lotRentGroup = document.getElementById('lot_rent_group');
    
    if (lotSelect.value === 'Rent') {
        lotRentGroup.style.display = 'block';
    } else {
        lotRentGroup.style.display = 'none';
        document.getElementById('lot_rent').value = '';
    }
    calculateIncomes();
}

// Form submission handling
function confirmSubmitForm() {
    // Validate remarks first
    const remarksSelect = document.getElementById('remarks');
    if (!remarksSelect || remarksSelect.value === '') {
        Swal.fire({
            icon: 'error',
            title: 'Remarks Required',
            text: 'Please select a remark before submitting the form.',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to submit this intake sheet? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, submit it!'
    }).then((result) => {
        if (result.isConfirmed) {
            submitFormData();
        }
    });
}

function submitFormData() {
    // Serialize family members data
    let familyMembers = [];
    const familyRows = document.querySelectorAll('#family_members_tbody tr');
    familyRows.forEach(row => {
        const cells = row.cells;
        familyMembers.push({
            name: cells[0].querySelector('input')?.value || '',
            relationship: cells[1].querySelector('select')?.value || '',
            birthdate: cells[2].querySelector('input')?.value || '',
            age: cells[3].querySelector('input')?.value || '',
            sex: cells[4].querySelector('select')?.value || '',
            civil_status: cells[5].querySelector('select')?.value || '',
            education: cells[6].querySelector('select')?.value || '',
            occupation: cells[7].querySelector('input')?.value || '',
            monthly_income: cells[8].querySelector('input')?.value || '',
            remarks: cells[9].querySelector('select')?.value || '',
        });
    });

    // Convert to JSON string
    document.getElementById('family_members').value = JSON.stringify(familyMembers);

    // Serialize service records data
    let serviceRecords = [];
    const serviceRows = document.querySelectorAll('#rv_service_records_tbody tr');
    serviceRows.forEach(row => {
        const cells = row.cells;
        serviceRecords.push({
            date: cells[0].querySelector('input')?.value || '',
            problem: cells[1].querySelector('input')?.value || '',
            action: cells[2].querySelector('input')?.value || '',
            remarks: cells[3].querySelector('select')?.value || '',
        });
    });

    // Convert to JSON string
    document.getElementById('rv_service_records').value = JSON.stringify(serviceRecords);

    const id = document.getElementById('remarks_id').value;
    const formData = new FormData(document.getElementById('updateRemarksForm'));

    // Show loading state
    Swal.fire({
        title: 'Submitting Intake Sheet',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit form via AJAX with better error handling
    fetch(`/lydo_staff/update-intake-sheet/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(async response => {
        // First, try to parse as JSON
        const text = await response.text();
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Try to parse as JSON
        try {
            return JSON.parse(text);
        } catch (e) {
            // If not JSON, check if it contains success indicators
            if (text.toLowerCase().includes('success') || text.includes('Intake sheet submitted successfully')) {
                return { success: true, message: 'Intake sheet submitted successfully' };
            } else {
                throw new Error('Unexpected response format from server');
            }
        }
    })
    .then(data => {
        Swal.close();

        // Check if response contains success message
        if (data.success || data.message?.toLowerCase().includes('success')) {
            Swal.fire({
                icon: 'success',
                title: 'Intake Sheet Submitted!',
                text: data.message || 'The intake sheet has been successfully submitted.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Close modal and reload page to stay on screening
                closeEditRemarksModal();
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Submission failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Submission Failed',
            text: 'Failed to submit intake sheet: ' + error.message,
            confirmButtonText: 'OK'
        });
    });
}

// Populate Edit Modal with existing data
function populateEditModal(data) {
    console.log('Populating modal with data:', data);
    
    if (!data) {
        console.warn('No data provided to populateEditModal');
        return;
    }

    // Safe population function
    function safeSetValue(elementId, value, defaultValue = '') {
        const element = document.getElementById(elementId);
        if (element && value !== undefined && value !== null) {
            element.value = value;
        } else if (element) {
            element.value = defaultValue;
        }
    }

    function safeSetSelect(elementId, value, defaultValue = '') {
        const element = document.getElementById(elementId);
        if (element && value !== undefined && value !== null) {
            // Handle different value formats (Rented vs Rent)
            if (value === 'Rented') {
                element.value = 'Rent';
            } else {
                element.value = value;
            }
            // Trigger change event for select elements
            element.dispatchEvent(new Event('change'));
        } else if (element) {
            element.value = defaultValue;
        }
    }

    // Populate head of family details
    safeSetSelect('head_4ps', data.head_4ps, '');
    safeSetValue('head_ipno', data.head_ipno, '');
    safeSetValue('head_address', data.head_address, '');
    safeSetValue('head_zone', data.head_zone, '');
    safeSetSelect('head_educ', data.head_educ, '');
    safeSetSelect('head_occ', data.head_occ, '');
    safeSetSelect('head_religion', data.head_religion, '');

    // Only populate if data exists
    if (data.head_pob) {
        safeSetValue('head_pob', data.head_pob);
    }
    if (data.applicant_gender) {
        safeSetSelect('applicant_gender', data.applicant_gender);
    }

    // Populate household information
    safeSetValue('other_income', data.other_income, '0');
    safeSetValue('house_total_income', data.house_total_income, '0');
    safeSetValue('house_net_income', data.house_net_income, '0');
    
    // Handle house and lot with proper value mapping
    safeSetSelect('house_house', data.house_house, '');
    safeSetValue('house_rent', data.house_rent, '0');
    safeSetSelect('house_lot', data.house_lot, '');
    safeSetValue('lot_rent', data.lot_rent, '0');
    safeSetValue('house_water', data.house_water, '0');
    safeSetValue('house_electric', data.house_electric, '0');

    // Handle conditional fields for house and lot
    const houseSelect = document.getElementById('house_house');
    const lotSelect = document.getElementById('house_lot');

    // Show rent fields if data exists and value is 'Rent' or 'Rented'
    if (data.house_house === 'Rent' || data.house_house === 'Rented') {
        const houseRentGroup = document.getElementById('house_rent_group');
        if (houseRentGroup) {
            houseRentGroup.style.display = 'block';
        }
        // Ensure rent value is set
        if (data.house_rent) {
            document.getElementById('house_rent').value = data.house_rent;
        }
    }

    if (data.house_lot === 'Rent' || data.house_lot === 'Rented') {
        const lotRentGroup = document.getElementById('lot_rent_group');
        if (lotRentGroup) {
            lotRentGroup.style.display = 'block';
        }
        // Ensure rent value is set
        if (data.lot_rent) {
            document.getElementById('lot_rent').value = data.lot_rent;
        }
    }

    // Populate remarks
    safeSetSelect('remarks', data.remarks, '');
    validateRemarks();

    // Populate health & signatures
    if (data.worker_name && String(data.worker_name).trim() !== '') {
        safeSetValue('worker_name', data.worker_name);
    }
    if (data.officer_name && String(data.officer_name).trim() !== '') {
        safeSetValue('officer_name', data.officer_name);
    }

    // Populate family members
    if (data.family_members) {
        try {
            let familyMembers = data.family_members;
            if (typeof familyMembers === 'string') {
                familyMembers = JSON.parse(familyMembers);
            }
            
            if (Array.isArray(familyMembers) && familyMembers.length > 0) {
                familyMembers.forEach(member => {
                    addFamilyMemberRow();
                    const rows = document.querySelectorAll('#family_members_tbody tr');
                    const lastRow = rows[rows.length - 1];
                    
                    if (lastRow && lastRow.cells) {
                        const cells = lastRow.cells;
                        safeSetValueInCell(cells[0], member.name || member.Name || '');
                        safeSetSelectInCell(cells[1], member.relationship || member.relation || '');
                        safeSetValueInCell(cells[2], member.birthdate || member.birth || '');
                        safeSetValueInCell(cells[3], member.age || '');
                        safeSetSelectInCell(cells[4], member.sex || '');
                        safeSetSelectInCell(cells[5], member.civil_status || member.civil || '');
                        safeSetSelectInCell(cells[6], member.education || member.educ || '');
                        safeSetValueInCell(cells[7], member.occupation || member.occ || '');
                        safeSetValueInCell(cells[8], member.monthly_income || member.income || '0');
                        safeSetSelectInCell(cells[9], member.remarks || '');
                    }
                });
            }
        } catch (e) {
            console.error('Error parsing family members:', e);
        }
    }

    // Populate service records
    if (data.rv_service_records) {
        try {
            let serviceRecords = data.rv_service_records;
            if (typeof serviceRecords === 'string') {
                serviceRecords = JSON.parse(serviceRecords);
            }
            
            if (Array.isArray(serviceRecords) && serviceRecords.length > 0) {
                serviceRecords.forEach(record => {
                    addRvServiceRecordRow();
                    const rows = document.querySelectorAll('#rv_service_records_tbody tr');
                    const lastRow = rows[rows.length - 1];
                    
                    if (lastRow && lastRow.cells) {
                        const cells = lastRow.cells;
                        // Only set date if not empty
                        if (record.date) {
                            safeSetValueInCell(cells[0], record.date);
                        }
                        safeSetValueInCell(cells[1], record.problem || '');
                        safeSetValueInCell(cells[2], record.action || '');
                        safeSetSelectInCell(cells[3], record.remarks || '');
                    }
                });
            }
        } catch (e) {
            console.error('Error parsing service records:', e);
        }
    }

    // Calculate incomes after populating data
    setTimeout(calculateIncomes, 500);
    
    console.log('Modal populated successfully');
}

// Helper functions for cell population
function safeSetValueInCell(cell, value) {
    const input = cell.querySelector('input');
    if (input) {
        input.value = value || '';
    }
}

function safeSetSelectInCell(cell, value) {
    const select = cell.querySelector('select');
    if (select) {
        select.value = value || '';
    }
}

// Logout function
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of the system.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, logout!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form
            document.getElementById('logoutForm').submit();
        }
    });
}

// Save as Draft function
function saveAsDraft() {
    // Serialize family members data
    let familyMembers = [];
    const familyRows = document.querySelectorAll('#family_members_tbody tr');
    familyRows.forEach(row => {
        const cells = row.cells;
        familyMembers.push({
            name: cells[0].querySelector('input')?.value || '',
            relationship: cells[1].querySelector('select')?.value || '',
            birthdate: cells[2].querySelector('input')?.value || '',
            age: cells[3].querySelector('input')?.value || '',
            sex: cells[4].querySelector('select')?.value || '',
            civil_status: cells[5].querySelector('select')?.value || '',
            education: cells[6].querySelector('select')?.value || '',
            occupation: cells[7].querySelector('input')?.value || '',
            monthly_income: cells[8].querySelector('input')?.value || '',
            remarks: cells[9].querySelector('select')?.value || '',
        });
    });

    // Convert to JSON string
    document.getElementById('family_members').value = JSON.stringify(familyMembers);

    // Serialize service records data
    let serviceRecords = [];
    const serviceRows = document.querySelectorAll('#rv_service_records_tbody tr');
    serviceRows.forEach(row => {
        const cells = row.cells;
        serviceRecords.push({
            date: cells[0].querySelector('input')?.value || '',
            problem: cells[1].querySelector('input')?.value || '',
            action: cells[2].querySelector('input')?.value || '',
            remarks: cells[3].querySelector('select')?.value || '',
        });
    });

    // Convert to JSON string
    document.getElementById('rv_service_records').value = JSON.stringify(serviceRecords);

    const id = document.getElementById('remarks_id').value;
    const formData = new FormData(document.getElementById('updateRemarksForm'));
    formData.append('is_draft', '1'); // Add draft flag

    // Show loading state
    Swal.fire({
        title: 'Saving Draft',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit form via AJAX
    fetch("/lydo_staff/update-intake-sheet/" + id, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        Swal.close();

        // Check if response contains success message
        if (data.includes('success')) {
            Swal.fire({
                icon: 'success',
                title: 'Draft Saved!',
                text: 'Intake sheet draft saved successfully!',
                confirmButtonText: 'OK'
            }).then(() => {
                // Don't close modal, allow continue editing
            });
        } else {
            throw new Error('Unexpected response');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to save draft: ' + error.message,
            confirmButtonText: 'OK'
        });
    });
}

// PDF Printing Function
function printScreeningPdf() {
    if (!currentReviewId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No applicant ID found for printing',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Open PDF in new tab
    const pdfUrl = `/lydo_staff/pdf/intake-sheet-print/${currentReviewId}`;
    window.open(pdfUrl, '_blank');
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    if (isNaN(date)) return dateString;
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function escapeHtml(s) {
    if (!s) return "";
    return s.replace(
        /[&<>"']/g,
        (m) =>
            ({
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#39;",
            }[m])
    );
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