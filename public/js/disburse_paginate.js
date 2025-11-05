// disburse_paginate.js
// Pagination and filtering for disbursement tables

// Global pagination states for both tables
const disbursementPaginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

const signedDisbursementPaginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize disbursement table
    initializeDisbursementData();
    initializeDisbursementPagination();
    initializeDisbursementFiltering();
    
    // Initialize signed disbursement table
    initializeSignedDisbursementData();
    initializeSignedDisbursementPagination();
    initializeSignedDisbursementFiltering();
});

// =============================================
// DISBURSEMENT TABLE FUNCTIONS
// =============================================

// Initialize data from the disbursement table
function initializeDisbursementData() {
    const tableRows = Array.from(document.querySelectorAll('#disbursementTableBody tr'));
    disbursementPaginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    disbursementPaginationState.filteredRows = [...disbursementPaginationState.allRows];
}

// Initialize pagination for disbursement table
function initializeDisbursementPagination() {
    updateDisbursementPagination();
}

// Update pagination display for disbursement table
function updateDisbursementPagination() {
    const state = disbursementPaginationState;
    const container = document.getElementById('disbursementPaginationContainer');
    
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
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> disbursements
            </div>
            
            <div class="flex items-center space-x-1">
                <!-- First Page -->
                <button onclick="changeDisbursementPage(1)"
                    class="px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 ${
                        state.currentPage === 1
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>

                <!-- Previous Page -->
                <button onclick="changeDisbursementPage(${state.currentPage - 1})"
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
                           onchange="goToDisbursementPage(this.value)">
                    of ${totalPages}
                </div>

                <!-- Next Page -->
                <button onclick="changeDisbursementPage(${state.currentPage + 1})"
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === totalPages
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>

                <!-- Last Page -->
                <button onclick="changeDisbursementPage(${totalPages})"
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

// Change page for disbursement table
function changeDisbursementPage(page) {
    const state = disbursementPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateDisbursementPagination();
}

// Go to specific page for disbursement table
function goToDisbursementPage(page) {
    const state = disbursementPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateDisbursementPagination();
}

// Initialize filtering functionality for disbursement table
function initializeDisbursementFiltering() {
    const filterForm = document.getElementById('filterForm');
    
    if (!filterForm) return;
    
    const searchInput = filterForm.querySelector('input[name="search"]');
    const barangaySelect = filterForm.querySelector('select[name="barangay"]');
    const academicYearSelect = filterForm.querySelector('select[name="academic_year"]');
    const semesterSelect = filterForm.querySelector('select[name="semester"]');

    function filterDisbursementTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedBarangay = barangaySelect ? barangaySelect.value : '';
        const selectedAcademicYear = academicYearSelect ? academicYearSelect.value : '';
        const selectedSemester = semesterSelect ? semesterSelect.value : '';

        const filteredRows = disbursementPaginationState.allRows.filter(row => {
            const nameCell = row.cells[0];
            const barangayCell = row.cells[1];
            const semesterCell = row.cells[2];
            const academicYearCell = row.cells[3];

            if (!nameCell || !barangayCell || !semesterCell || !academicYearCell) return false;

            const name = nameCell.textContent.toLowerCase();
            const barangay = barangayCell.textContent.trim();
            const semester = semesterCell.textContent.trim();
            const academicYear = academicYearCell.textContent.trim();

            const nameMatch = !searchTerm || name.includes(searchTerm);
            const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
            const academicYearMatch = !selectedAcademicYear || academicYear === selectedAcademicYear;
            const semesterMatch = !selectedSemester || semester === selectedSemester;

            return nameMatch && barangayMatch && academicYearMatch && semesterMatch;
        });

        // Update filtered rows and reset to page 1
        disbursementPaginationState.filteredRows = filteredRows;
        disbursementPaginationState.currentPage = 1;
        updateDisbursementPagination();
    }

    // Add event listeners with debouncing
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterDisbursementTable, 300));
    }
    if (barangaySelect) {
        barangaySelect.addEventListener('change', filterDisbursementTable);
    }
    if (academicYearSelect) {
        academicYearSelect.addEventListener('change', filterDisbursementTable);
    }
    if (semesterSelect) {
        semesterSelect.addEventListener('change', filterDisbursementTable);
    }
}

// =============================================
// SIGNED DISBURSEMENT TABLE FUNCTIONS
// =============================================

// Initialize data from the signed disbursement table
function initializeSignedDisbursementData() {
    const tableRows = Array.from(document.querySelectorAll('#signedDisbursementTableBody tr'));
    signedDisbursementPaginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    signedDisbursementPaginationState.filteredRows = [...signedDisbursementPaginationState.allRows];
}

// Initialize pagination for signed disbursement table
function initializeSignedDisbursementPagination() {
    updateSignedDisbursementPagination();
}

// Update pagination display for signed disbursement table
function updateSignedDisbursementPagination() {
    const state = signedDisbursementPaginationState;
    const container = document.getElementById('signedDisbursementPaginationContainer');
    
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
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> signed disbursements
            </div>
            
            <div class="flex items-center space-x-1">
                <!-- First Page -->
                <button onclick="changeSignedDisbursementPage(1)"
                    class="px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 ${
                        state.currentPage === 1
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>

                <!-- Previous Page -->
                <button onclick="changeSignedDisbursementPage(${state.currentPage - 1})"
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
                           onchange="goToSignedDisbursementPage(this.value)">
                    of ${totalPages}
                </div>

                <!-- Next Page -->
                <button onclick="changeSignedDisbursementPage(${state.currentPage + 1})"
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === totalPages
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>

                <!-- Last Page -->
                <button onclick="changeSignedDisbursementPage(${totalPages})"
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

// Change page for signed disbursement table
function changeSignedDisbursementPage(page) {
    const state = signedDisbursementPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateSignedDisbursementPagination();
}

// Go to specific page for signed disbursement table
function goToSignedDisbursementPage(page) {
    const state = signedDisbursementPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateSignedDisbursementPagination();
}

// Initialize filtering functionality for signed disbursement table
function initializeSignedDisbursementFiltering() {
    const filterForm = document.getElementById('signedFilterForm');
    
    if (!filterForm) return;
    
    const searchInput = filterForm.querySelector('input[name="search"]');
    const barangaySelect = filterForm.querySelector('select[name="barangay"]');
    const academicYearSelect = filterForm.querySelector('select[name="academic_year"]');
    const semesterSelect = filterForm.querySelector('select[name="semester"]');

    function filterSignedDisbursementTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedBarangay = barangaySelect ? barangaySelect.value : '';
        const selectedAcademicYear = academicYearSelect ? academicYearSelect.value : '';
        const selectedSemester = semesterSelect ? semesterSelect.value : '';

        const filteredRows = signedDisbursementPaginationState.allRows.filter(row => {
            const nameCell = row.cells[0];
            const barangayCell = row.cells[1];
            const semesterCell = row.cells[2];
            const academicYearCell = row.cells[3];

            if (!nameCell || !barangayCell || !semesterCell || !academicYearCell) return false;

            const name = nameCell.textContent.toLowerCase();
            const barangay = barangayCell.textContent.trim();
            const semester = semesterCell.textContent.trim();
            const academicYear = academicYearCell.textContent.trim();

            const nameMatch = !searchTerm || name.includes(searchTerm);
            const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
            const academicYearMatch = !selectedAcademicYear || academicYear === selectedAcademicYear;
            const semesterMatch = !selectedSemester || semester === selectedSemester;

            return nameMatch && barangayMatch && academicYearMatch && semesterMatch;
        });

        // Update filtered rows and reset to page 1
        signedDisbursementPaginationState.filteredRows = filteredRows;
        signedDisbursementPaginationState.currentPage = 1;
        updateSignedDisbursementPagination();
    }

    // Add event listeners with debouncing
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterSignedDisbursementTable, 300));
    }
    if (barangaySelect) {
        barangaySelect.addEventListener('change', filterSignedDisbursementTable);
    }
    if (academicYearSelect) {
        academicYearSelect.addEventListener('change', filterSignedDisbursementTable);
    }
    if (semesterSelect) {
        semesterSelect.addEventListener('change', filterSignedDisbursementTable);
    }
}

// =============================================
// UTILITY FUNCTIONS
// =============================================

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