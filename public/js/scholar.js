// scholar.js - Complete JavaScript file for scholar page functionality

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
}

// Initialize filtering functionality
function initializeScholarFiltering() {
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');
    const academicYearSelect = document.getElementById('academicYearSelect');
    const statusSelect = document.getElementById('statusSelect');

function filterScholarTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedBarangay = barangaySelect.value;
    const selectedAcademicYear = academicYearSelect.value;
    const selectedStatus = statusSelect.value.toLowerCase(); // Ensure this is lowercase

    const filteredRows = paginationState.allRows.filter(row => {
        const nameCell = row.cells[1];
        const barangayCell = row.cells[2];
        const academicYearCell = row.cells[6];
        const statusCell = row.cells[7];

        if (!nameCell || !barangayCell || !academicYearCell || !statusCell) return false;

        const name = nameCell.textContent.toLowerCase();
        const barangay = barangayCell.textContent.trim();
        const academicYear = academicYearCell.textContent.trim();
        const status = statusCell.textContent.trim().toLowerCase(); // Ensure this matches the format

        const nameMatch = name.includes(searchTerm);
        const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
        const academicYearMatch = !selectedAcademicYear || academicYear === selectedAcademicYear;
        const statusMatch = selectedStatus === 'all' || status === selectedStatus; // Ensure case matches

        // Debugging logs
        console.log(`Name: ${name}, Barangay: ${barangay}, Academic Year: ${academicYear}, Status: ${status}, Selected Status: ${selectedStatus}`);

        return nameMatch && barangayMatch && academicYearMatch && statusMatch;
    });

    // Sort filtered results alphabetically
    const sortedFilteredRows = sortRowsAlphabetically(filteredRows);

    // Update filtered rows and reset to page 1
    paginationState.filteredRows = sortedFilteredRows;
    paginationState.currentPage = 1;
    updateScholarPagination();
    
    // Reset select all checkbox
    document.getElementById('selectAll').checked = false;
    document.getElementById('selectAll').indeterminate = false;
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
        statusSelect.addEventListener('change', filterScholarTable);
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

// Utility functions
function showError(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}

function showSuccess(message) {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK'
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeScholarData();
    initializeScholarPagination();
    initializeScholarFiltering();
});