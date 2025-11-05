// disburse.js

// Global pagination state for disbursement records
const disbursementPaginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Function to get full name for sorting from disbursement table
function getDisbursementFullNameForSorting(row) {
    const nameCell = row.cells[0]; // Name is in first column for disbursement records
    if (!nameCell) return '';
    return nameCell.textContent.trim().toLowerCase();
}

// Function to sort rows alphabetically by last name for disbursement records
function sortDisbursementRowsAlphabetically(rows) {
    return rows.sort((a, b) => {
        const nameA = getDisbursementFullNameForSorting(a);
        const nameB = getDisbursementFullNameForSorting(b);
        return nameA.localeCompare(nameB);
    });
}

// Initialize data from the disbursement records table
function initializeDisbursementData() {
    const tableBody = document.querySelector('#tab-content-records tbody');
    if (!tableBody) return;
    
    const tableRows = Array.from(tableBody.querySelectorAll('tr'));
    disbursementPaginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by last name
    disbursementPaginationState.allRows = sortDisbursementRowsAlphabetically(disbursementPaginationState.allRows);
    disbursementPaginationState.filteredRows = [...disbursementPaginationState.allRows];
}

// Initialize pagination for disbursement records
function initializeDisbursementPagination() {
    updateDisbursementPagination();
}

// Update pagination display for disbursement records
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
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> disbursement records
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

// Change page for disbursement records
function changeDisbursementPage(page) {
    const state = disbursementPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateDisbursementPagination();
}

// Go to specific page for disbursement records
function goToDisbursementPage(page) {
    const state = disbursementPaginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
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
        const sortedFilteredRows = sortDisbursementRowsAlphabetically(filteredRows);

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

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on the disbursement records tab
    const recordsContent = document.getElementById('tab-content-records');
    if (recordsContent && !recordsContent.classList.contains('hidden')) {
        initializeDisbursementData();
        initializeDisbursementPagination();
        initializeDisbursementFiltering();
    }
    
    // Also reinitialize when switching to records tab
    const recordsTab = document.getElementById('tab-records');
    if (recordsTab) {
        recordsTab.addEventListener('click', function() {
            setTimeout(() => {
                initializeDisbursementData();
                initializeDisbursementPagination();
                initializeDisbursementFiltering();
            }, 100);
        });
    }
});