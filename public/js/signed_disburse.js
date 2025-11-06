// signed_disburse.js

// Global pagination state for signed disbursement records
const signedDisbursementPaginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Function to get full name for sorting from signed disbursement table
function getSignedDisbursementFullNameForSorting(row) {
    const nameCell = row.cells[0]; // Name is in first column for signed disbursement records
    if (!nameCell) return '';
    return nameCell.textContent.trim().toLowerCase();
}

// Function to sort rows alphabetically by last name for signed disbursement records
function sortSignedDisbursementRowsAlphabetically(rows) {
    return rows.sort((a, b) => {
        const nameA = getSignedDisbursementFullNameForSorting(a);
        const nameB = getSignedDisbursementFullNameForSorting(b);
        return nameA.localeCompare(nameB);
    });
}

// Initialize data from the signed disbursement records table
function initializeSignedDisbursementData() {
    const tableBody = document.querySelector('#tab-content-signed tbody');
    if (!tableBody) return;
    
    const tableRows = Array.from(tableBody.querySelectorAll('tr'));
    signedDisbursementPaginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by last name
    signedDisbursementPaginationState.allRows = sortSignedDisbursementRowsAlphabetically(signedDisbursementPaginationState.allRows);
    signedDisbursementPaginationState.filteredRows = [...signedDisbursementPaginationState.allRows];
}

// Initialize pagination for signed disbursement records
function initializeSignedDisbursementPagination() {
    updateSignedDisbursementPagination();
}

// Update pagination display for signed disbursement records
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
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> signed disbursement records
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

// Change page for signed disbursement records
function changeSignedDisbursementPage(page) {
    const state = signedDisbursementPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateSignedDisbursementPagination();
}

// Go to specific page for signed disbursement records
function goToSignedDisbursementPage(page) {
    const state = signedDisbursementPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
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
        const sortedFilteredRows = sortSignedDisbursementRowsAlphabetically(filteredRows);

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
}

// Initialize print PDF functionality for signed disbursements
function initializeSignedDisbursementPrintPdf() {
    const printPdfBtn = document.getElementById('signedPrintPdfBtn');
    if (!printPdfBtn) return;

    printPdfBtn.addEventListener('click', function() {
        // Get current filter values
        const searchInput = document.querySelector('#signedFilterForm input[name="search"]');
        const barangaySelect = document.querySelector('#signedFilterForm select[name="barangay"]');
        const academicYearSelect = document.querySelector('#signedFilterForm select[name="academic_year"]');
        const semesterSelect = document.querySelector('#signedFilterForm select[name="semester"]');

        const search = searchInput ? searchInput.value : '';
        const barangay = barangaySelect ? barangaySelect.value : '';
        const academic_year = academicYearSelect ? academicYearSelect.value : '';
        const semester = semesterSelect ? semesterSelect.value : '';

        // Build URL with parameters
        let url = '/lydo_admin/disbursement-pdf';
        const params = new URLSearchParams();

        if (search) params.append('search', search);
        if (barangay) params.append('barangay', barangay);
        if (academic_year) params.append('academic_year', academic_year);
        if (semester) params.append('semester', semester);

        if (params.toString()) {
            url += '?' + params.toString();
        }

        // Open PDF in new tab/window
        window.open(url, '_blank');
    });
}

// Debounce function for search
function signedDebounce(func, wait) {
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

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on the signed disbursement tab
    const signedContent = document.getElementById('tab-content-signed');
    if (signedContent && !signedContent.classList.contains('hidden')) {
        initializeSignedDisbursementData();
        initializeSignedDisbursementPagination();
        initializeSignedDisbursementFiltering();
        initializeSignedDisbursementPrintPdf();
    }

    // Also reinitialize when switching to signed tab
    const signedTab = document.getElementById('tab-signed');
    if (signedTab) {
        signedTab.addEventListener('click', function() {
            setTimeout(() => {
                initializeSignedDisbursementData();
                initializeSignedDisbursementPagination();
                initializeSignedDisbursementFiltering();
                initializeSignedDisbursementPrintPdf();
            }, 100);
        });
    }
});
