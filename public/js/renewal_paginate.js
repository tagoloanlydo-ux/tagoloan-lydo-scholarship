// Pagination state for renewal page
const renewalPaginationState = {
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
    initializeRenewalPagination();
    
    // Update the existing event listeners to use debounce
    const nameSearch = document.getElementById('nameSearch');
    const barangayFilter = document.getElementById('barangayFilter');
    const listNameSearch = document.getElementById('listNameSearch');
    const listBarangayFilter = document.getElementById('listBarangayFilter');
    
    if (nameSearch) {
        nameSearch.addEventListener('input', debounceRenewal(filterRenewalTable, 300));
    }
    if (barangayFilter) {
        barangayFilter.addEventListener('change', filterRenewalTable);
    }
    if (listNameSearch) {
        listNameSearch.addEventListener('input', debounceRenewal(filterRenewalList, 300));
    }
    if (listBarangayFilter) {
        listBarangayFilter.addEventListener('change', filterRenewalList);
    }
});

// Initialize pagination for renewal
function initializeRenewalPagination() {
    // Get ALL table rows (not just visible ones) from Table View
    const tableRows = Array.from(document.querySelectorAll('#tableView tbody tr'));
    renewalPaginationState.table.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    renewalPaginationState.table.filteredRows = [...renewalPaginationState.table.allRows];
    
    // Get ALL list rows from List View
    const listRows = Array.from(document.querySelectorAll('#listView tbody tr'));
    renewalPaginationState.list.allRows = listRows.filter(row => !row.querySelector('td[colspan]'));
    renewalPaginationState.list.filteredRows = [...renewalPaginationState.list.allRows];
    
    updateRenewalPagination('table');
    updateRenewalPagination('list');
}

// Update pagination display for renewal
function updateRenewalPagination(viewType) {
    const state = renewalPaginationState[viewType];
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
            <button class="pagination-btn" onclick="changeRenewalPage('${viewType}', 1)" ${state.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-angle-double-left"></i>
            </button>
            <button class="pagination-btn" onclick="changeRenewalPage('${viewType}', ${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-angle-left"></i>
            </button>
            <div class="pagination-page-info">
                Page <input type="number" class="pagination-page-input" value="${state.currentPage}" min="1" max="${totalPages}" onchange="goToRenewalPage('${viewType}', this.value)"> of ${totalPages}
            </div>
            <button class="pagination-btn" onclick="changeRenewalPage('${viewType}', ${state.currentPage + 1})" ${state.currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-angle-right"></i>
            </button>
            <button class="pagination-btn" onclick="changeRenewalPage('${viewType}', ${totalPages})" ${state.currentPage === totalPages ? 'disabled' : ''}>
                <i class="fas fa-angle-double-right"></i>
            </button>
        </div>
    `;
}

// Change page for renewal
function changeRenewalPage(viewType, page) {
    const state = renewalPaginationState[viewType];
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateRenewalPagination(viewType);
}

// Go to specific page for renewal
function goToRenewalPage(viewType, page) {
    const state = renewalPaginationState[viewType];
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateRenewalPagination(viewType);
}

// Filter Table View for renewal
function filterRenewalTable() {
    const searchValue = document.getElementById('nameSearch').value.toLowerCase().trim();
    const barangayValue = document.getElementById('barangayFilter').value.toLowerCase().trim();

    const filteredRows = renewalPaginationState.table.allRows.filter(row => {
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
    renewalPaginationState.table.filteredRows = filteredRows;
    renewalPaginationState.table.currentPage = 1;
    updateRenewalPagination('table');
}

// Filter List View for renewal
function filterRenewalList() {
    const searchValue = document.getElementById('listNameSearch').value.toLowerCase().trim();
    const barangayValue = document.getElementById('listBarangayFilter').value.toLowerCase().trim();

    const filteredRows = renewalPaginationState.list.allRows.filter(row => {
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
    renewalPaginationState.list.filteredRows = filteredRows;
    renewalPaginationState.list.currentPage = 1;
    updateRenewalPagination('list');
}

// Update the existing showTable and showList functions for renewal
function showTable() {
    document.getElementById('tableView').classList.remove('hidden');
    document.getElementById('listView').classList.add('hidden');
    document.getElementById('tab-renewal').classList.add('active');
    document.getElementById('tab-review').classList.remove('active');
    
    // Reset to first page
    renewalPaginationState.table.currentPage = 1;
    updateRenewalPagination('table');
}

function showList() {
    document.getElementById('tableView').classList.add('hidden');
    document.getElementById('listView').classList.remove('hidden');
    document.getElementById('tab-renewal').classList.remove('active');
    document.getElementById('tab-review').classList.add('active');
    
    // Reset to first page
    renewalPaginationState.list.currentPage = 1;
    updateRenewalPagination('list');
}

// Clear filters for Table View
function clearRenewalFiltersTable() {
    document.getElementById('nameSearch').value = '';
    document.getElementById('barangayFilter').value = '';
    filterRenewalTable();
}

// Clear filters for List View
function clearRenewalFiltersList() {
    document.getElementById('listNameSearch').value = '';
    document.getElementById('listBarangayFilter').value = '';
    filterRenewalList();
}

// Debounce function for search in renewal
function debounceRenewal(func, wait) {
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

// Add event listeners for clear buttons (you need to add these buttons in your HTML)
document.addEventListener('DOMContentLoaded', function() {
    // Add clear buttons if they don't exist in your HTML
    addClearButtons();
});

function addClearButtons() {
    // Add clear button for table view search
    const nameSearch = document.getElementById('nameSearch');
    if (nameSearch && !nameSearch.parentNode.querySelector('.clear-search')) {
        const clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'clear-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600';
        clearBtn.innerHTML = '✕';
        clearBtn.onclick = clearRenewalFiltersTable;
        nameSearch.parentNode.appendChild(clearBtn);
    }

    // Add clear button for list view search
    const listNameSearch = document.getElementById('listNameSearch');
    if (listNameSearch && !listNameSearch.parentNode.querySelector('.clear-search')) {
        const clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'clear-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600';
        clearBtn.innerHTML = '✕';
        clearBtn.onclick = clearRenewalFiltersList;
        listNameSearch.parentNode.appendChild(clearBtn);
    }
}