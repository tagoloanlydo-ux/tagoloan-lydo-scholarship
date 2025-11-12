// Pagination state for application page
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

// Initialize pagination when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializePagination();
    
    // Update the existing event listeners to use debounce
    const searchInputTable = document.getElementById('searchInputTable');
    const barangaySelectTable = document.getElementById('barangaySelectTable');
    const searchInputList = document.getElementById('searchInputList');
    const barangaySelectList = document.getElementById('barangaySelectList');
    
    if (searchInputTable) {
        searchInputTable.addEventListener('input', debounce(filterTable, 300));
    }
    if (barangaySelectTable) {
        barangaySelectTable.addEventListener('change', filterTable);
    }
    if (searchInputList) {
        searchInputList.addEventListener('input', debounce(filterList, 300));
    }
    if (barangaySelectList) {
        barangaySelectList.addEventListener('change', filterList);
    }
});

// Initialize pagination
function initializePagination() {
    // Get ALL table rows (not just visible ones) from Table View
    const tableRows = Array.from(document.querySelectorAll('#tableView tbody tr'));
    paginationState.table.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    paginationState.table.filteredRows = [...paginationState.table.allRows];
    
    // Get ALL list rows from List View
    const listRows = Array.from(document.querySelectorAll('#listView tbody tr'));
    paginationState.list.allRows = listRows.filter(row => !row.querySelector('td[colspan]'));
    paginationState.list.filteredRows = [...paginationState.list.allRows];
    
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

// Filter Table View
function filterTable() {
    const searchValue = document.getElementById('searchInputTable').value.toLowerCase().trim();
    const barangayValue = document.getElementById('barangaySelectTable').value.toLowerCase().trim();

    const filteredRows = paginationState.table.allRows.filter(row => {
        const nameCell = row.cells[1]; // Name column
        const barangayCell = row.cells[2]; // Barangay column

        if (!nameCell || !barangayCell) return false;

        const name = nameCell.textContent.toLowerCase();
        const barangay = barangayCell.textContent.toLowerCase();

        const matchesName = name.includes(searchValue);
        const matchesBarangay = barangayValue === '' || barangay.includes(barangayValue);

        return matchesName && matchesBarangay;
    });

    // Update filtered rows and reset to page 1
    paginationState.table.filteredRows = filteredRows;
    paginationState.table.currentPage = 1;
    updatePagination('table');
}

// Filter List View
function filterList() {
    const searchValue = document.getElementById('searchInputList').value.toLowerCase().trim();
    const barangayValue = document.getElementById('barangaySelectList').value.toLowerCase().trim();

    const filteredRows = paginationState.list.allRows.filter(row => {
        const nameCell = row.cells[1]; // Name column
        const barangayCell = row.cells[2]; // Barangay column

        if (!nameCell || !barangayCell) return false;

        const name = nameCell.textContent.toLowerCase();
        const barangay = barangayCell.textContent.toLowerCase();

        const matchesName = name.includes(searchValue);
        const matchesBarangay = barangayValue === '' || barangay.includes(barangayValue);

        return matchesName && matchesBarangay;
    });

    // Update filtered rows and reset to page 1
    paginationState.list.filteredRows = filteredRows;
    paginationState.list.currentPage = 1;
    updatePagination('list');
}

// Update the existing showTable and showList functions
function showTable() {
    document.getElementById('tableView').classList.remove('hidden');
    document.getElementById('listView').classList.add('hidden');
    document.getElementById('pendingTab').classList.add('active');
    document.getElementById('reviewedTab').classList.remove('active');
    
    // Reset to first page
    paginationState.table.currentPage = 1;
    updatePagination('table');
}

function showList() {
    document.getElementById('tableView').classList.add('hidden');
    document.getElementById('listView').classList.remove('hidden');
    document.getElementById('pendingTab').classList.remove('active');
    document.getElementById('reviewedTab').classList.add('active');
    
    // Reset to first page
    paginationState.list.currentPage = 1;
    updatePagination('list');
}

// Clear filters for Table View
function clearFiltersTable() {
    document.getElementById('searchInputTable').value = '';
    document.getElementById('barangaySelectTable').value = '';
    filterTable();
}

// Clear filters for List View
function clearFiltersList() {
    document.getElementById('searchInputList').value = '';
    document.getElementById('barangaySelectList').value = '';
    filterList();
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