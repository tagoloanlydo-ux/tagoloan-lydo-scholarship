// disburse.js - Filtering and Pagination for Disbursement Page

// Global variables for pagination
const disbursementPaginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Function to get full name for sorting
function getFullNameForSorting(row) {
    const nameCell = row.cells[0]; // Name is in first column (0-based index)
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
function initializeDisbursementData() {
    const tableRows = Array.from(document.querySelectorAll('#tab-content-records table tbody tr, #tab-content-signed table tbody tr'));
    disbursementPaginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by last name
    disbursementPaginationState.allRows = sortRowsAlphabetically(disbursementPaginationState.allRows);
    disbursementPaginationState.filteredRows = [...disbursementPaginationState.allRows];
}

// Initialize pagination for disbursement records
function initializeDisbursementPagination() {
    updateDisbursementPagination();
}

function updateDisbursementPagination() {
    const state = disbursementPaginationState;
    
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
    const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
    const startItem = state.filteredRows.length === 0 ? 0 : Math.min((state.currentPage - 1) * state.rowsPerPage + 1, state.filteredRows.length);
    const endItem = Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length);
    
    // Update pagination info
    const paginationInfo = document.getElementById('disbursementPaginationInfo');
    const currentPageInput = document.getElementById('disbursementCurrentPage');
    const totalPagesSpan = document.getElementById('disbursementTotalPages');
    const prevPageBtn = document.getElementById('disbursementPrevPage');
    const nextPageBtn = document.getElementById('disbursementNextPage');
    
    if (paginationInfo) {
        paginationInfo.textContent = `Showing page ${state.currentPage} of ${totalPages}`;
    }
    
    if (currentPageInput) {
        currentPageInput.value = state.currentPage;
        currentPageInput.max = totalPages;
    }
    
    if (totalPagesSpan) {
        totalPagesSpan.textContent = totalPages;
    }
    
    if (prevPageBtn) {
        prevPageBtn.disabled = state.currentPage === 1;
    }
    
    if (nextPageBtn) {
        nextPageBtn.disabled = state.currentPage === totalPages || totalPages === 0;
    }
}

// Change page for disbursement records
function changeDisbursementPage(page) {
    const state = disbursementPaginationState;
    const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateDisbursementPagination();
}

// Go to specific page for disbursement records
function goToDisbursementPage(page) {
    const state = disbursementPaginationState;
    const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateDisbursementPagination();
}

// Initialize filtering functionality for disbursement records
function initializeDisbursementFiltering() {
    const searchInput = document.querySelector('#filterForm input[name="search"]');
    const barangaySelect = document.querySelector('#filterForm select[name="barangay"]');
    const academicYearSelect = document.querySelector('#filterForm select[name="academic_year"]');
    const semesterSelect = document.querySelector('#filterForm select[name="semester"]');

    function filterDisbursementTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedBarangay = barangaySelect ? barangaySelect.value : '';
        const selectedAcademicYear = academicYearSelect ? academicYearSelect.value : '';
        const selectedSemester = semesterSelect ? semesterSelect.value : '';

        const filteredRows = disbursementPaginationState.allRows.filter(row => {
            const nameCell = row.cells[0]; // Name column
            const barangayCell = row.cells[1]; // Barangay column
            const semesterCell = row.cells[2]; // Semester column
            const academicYearCell = row.cells[3]; // Academic Year column

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

        // Sort filtered results alphabetically
        const sortedFilteredRows = sortRowsAlphabetically(filteredRows);

        // Update filtered rows and reset to page 1
        disbursementPaginationState.filteredRows = sortedFilteredRows;
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

    // Apply initial filters if any
    setTimeout(filterDisbursementTable, 100);
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

// Global variables for signed disbursement pagination
const signedDisbursementPaginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Initialize data from the signed disbursement table
function initializeSignedDisbursementData() {
    const tableRows = Array.from(document.querySelectorAll('#tab-content-signed table tbody tr'));
    signedDisbursementPaginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by last name
    signedDisbursementPaginationState.allRows = sortRowsAlphabetically(signedDisbursementPaginationState.allRows);
    signedDisbursementPaginationState.filteredRows = [...signedDisbursementPaginationState.allRows];
}

// Initialize pagination for signed disbursement records
function initializeSignedDisbursementPagination() {
    updateSignedDisbursementPagination();
}

function updateSignedDisbursementPagination() {
    const state = signedDisbursementPaginationState;
    
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
    const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
    const startItem = state.filteredRows.length === 0 ? 0 : Math.min((state.currentPage - 1) * state.rowsPerPage + 1, state.filteredRows.length);
    const endItem = Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length);
    
    // Update pagination info
    const paginationInfo = document.getElementById('signedDisbursementPaginationInfo');
    const currentPageInput = document.getElementById('signedDisbursementCurrentPage');
    const totalPagesSpan = document.getElementById('signedDisbursementTotalPages');
    const prevPageBtn = document.getElementById('signedDisbursementPrevPage');
    const nextPageBtn = document.getElementById('signedDisbursementNextPage');
    
    if (paginationInfo) {
        paginationInfo.textContent = `Showing page ${state.currentPage} of ${totalPages}`;
    }
    
    if (currentPageInput) {
        currentPageInput.value = state.currentPage;
        currentPageInput.max = totalPages;
    }
    
    if (totalPagesSpan) {
        totalPagesSpan.textContent = totalPages;
    }
    
    if (prevPageBtn) {
        prevPageBtn.disabled = state.currentPage === 1;
    }
    
    if (nextPageBtn) {
        nextPageBtn.disabled = state.currentPage === totalPages || totalPages === 0;
    }
}

// Change page for signed disbursement records
function changeSignedDisbursementPage(page) {
    const state = signedDisbursementPaginationState;
    const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateSignedDisbursementPagination();
}

// Go to specific page for signed disbursement records
function goToSignedDisbursementPage(page) {
    const state = signedDisbursementPaginationState;
    const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateSignedDisbursementPagination();
}

// Initialize filtering functionality for signed disbursement records
function initializeSignedDisbursementFiltering() {
    const searchInput = document.querySelector('#signedFilterForm input[name="search"]');
    const barangaySelect = document.querySelector('#signedFilterForm select[name="barangay"]');
    const academicYearSelect = document.querySelector('#signedFilterForm select[name="academic_year"]');
    const semesterSelect = document.querySelector('#signedFilterForm select[name="semester"]');

    function filterSignedDisbursementTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedBarangay = barangaySelect ? barangaySelect.value : '';
        const selectedAcademicYear = academicYearSelect ? academicYearSelect.value : '';
        const selectedSemester = semesterSelect ? semesterSelect.value : '';

        const filteredRows = signedDisbursementPaginationState.allRows.filter(row => {
            const nameCell = row.cells[0]; // Name column
            const barangayCell = row.cells[1]; // Barangay column
            const semesterCell = row.cells[2]; // Semester column
            const academicYearCell = row.cells[3]; // Academic Year column

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

        // Sort filtered results alphabetically
        const sortedFilteredRows = sortRowsAlphabetically(filteredRows);

        // Update filtered rows and reset to page 1
        signedDisbursementPaginationState.filteredRows = sortedFilteredRows;
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

    // Apply initial filters if any
    setTimeout(filterSignedDisbursementTable, 100);
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize disbursement records functionality
    initializeDisbursementData();
    initializeDisbursementPagination();
    initializeDisbursementFiltering();
    
    // Initialize signed disbursement functionality
    initializeSignedDisbursementData();
    initializeSignedDisbursementPagination();
    initializeSignedDisbursementFiltering();
    
    // Pagination event listeners for disbursement records
    const disbursementPrevPage = document.getElementById('disbursementPrevPage');
    const disbursementNextPage = document.getElementById('disbursementNextPage');
    const disbursementCurrentPage = document.getElementById('disbursementCurrentPage');
    
    if (disbursementPrevPage) {
        disbursementPrevPage.addEventListener('click', function() {
            changeDisbursementPage(disbursementPaginationState.currentPage - 1);
        });
    }
    
    if (disbursementNextPage) {
        disbursementNextPage.addEventListener('click', function() {
            changeDisbursementPage(disbursementPaginationState.currentPage + 1);
        });
    }
    
    if (disbursementCurrentPage) {
        disbursementCurrentPage.addEventListener('change', function() {
            goToDisbursementPage(this.value);
        });
    }
    
    // Pagination event listeners for signed disbursement records
    const signedDisbursementPrevPage = document.getElementById('signedDisbursementPrevPage');
    const signedDisbursementNextPage = document.getElementById('signedDisbursementNextPage');
    const signedDisbursementCurrentPage = document.getElementById('signedDisbursementCurrentPage');
    
    if (signedDisbursementPrevPage) {
        signedDisbursementPrevPage.addEventListener('click', function() {
            changeSignedDisbursementPage(signedDisbursementPaginationState.currentPage - 1);
        });
    }
    
    if (signedDisbursementNextPage) {
        signedDisbursementNextPage.addEventListener('click', function() {
            changeSignedDisbursementPage(signedDisbursementPaginationState.currentPage + 1);
        });
    }
    
    if (signedDisbursementCurrentPage) {
        signedDisbursementCurrentPage.addEventListener('change', function() {
            goToSignedDisbursementPage(this.value);
        });
    }
    
    console.log('Disbursement filtering and pagination initialized');
});