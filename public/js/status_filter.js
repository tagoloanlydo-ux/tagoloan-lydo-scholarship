// Global pagination state for both tabs
const paginationState = {
    withoutRenewal: {
        currentPage: 1,
        rowsPerPage: 10,
        allRows: [],
        filteredRows: []
    },
    graduating: {
        currentPage: 1,
        rowsPerPage: 10,
        allRows: [],
        filteredRows: []
    }
};

// Initialize data for both tabs
function initializeStatusData() {
    // Without Renewal tab
    const withoutRenewalRows = Array.from(document.querySelectorAll('#withoutRenewalTableBody .scholar-row'));
    paginationState.withoutRenewal.allRows = withoutRenewalRows;
    paginationState.withoutRenewal.filteredRows = [...withoutRenewalRows];
    
    // Graduating tab
    const graduatingRows = Array.from(document.querySelectorAll('#graduatingTableBody .graduating-scholar-row'));
    paginationState.graduating.allRows = graduatingRows;
    paginationState.graduating.filteredRows = [...graduatingRows];
    
    console.log('Without Renewal rows:', withoutRenewalRows.length);
    console.log('Graduating rows:', graduatingRows.length);
}

// Initialize pagination for both tabs
function initializeStatusPagination() {
    updateStatusPagination('withoutRenewal');
    updateStatusPagination('graduating');
}

// Update pagination display for specific tab
function updateStatusPagination(tabType) {
    const state = paginationState[tabType];
    
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
    
    console.log(`${tabType}: Showing ${pageRows.length} rows on page ${state.currentPage}`);
    
    // Update button states for the active tab
    if (tabType === 'withoutRenewal') {
        updateButtons();
    } else if (tabType === 'graduating') {
        updateGraduatingButtons();
    }
}

// Change page for specific tab
function changeStatusPage(tabType, page) {
    const state = paginationState[tabType];
    const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateStatusPagination(tabType);
}

// Go to specific page for specific tab
function goToStatusPage(tabType, page) {
    const state = paginationState[tabType];
    const totalPages = Math.max(1, Math.ceil(state.filteredRows.length / state.rowsPerPage));
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateStatusPagination(tabType);
}

// Initialize filtering functionality for both tabs
function initializeStatusFiltering() {
    // Without Renewal tab filters
    const withoutRenewalSearch = document.getElementById('withoutRenewalSearch');
    const withoutRenewalBarangay = document.getElementById('withoutRenewalBarangay');
    
    // Graduating tab filters
    const graduatingSearch = document.getElementById('graduatingSearch');
    const graduatingBarangay = document.getElementById('graduatingBarangay');

    // Without Renewal tab event listeners
    if (withoutRenewalSearch) {
        withoutRenewalSearch.addEventListener('input', debounce(() => {
            filterWithoutRenewalTable(withoutRenewalSearch.value.toLowerCase(), withoutRenewalBarangay.value);
        }, 300));
    }
    
    if (withoutRenewalBarangay) {
        withoutRenewalBarangay.addEventListener('change', () => {
            filterWithoutRenewalTable(withoutRenewalSearch.value.toLowerCase(), withoutRenewalBarangay.value);
        });
    }

    // Graduating tab event listeners
    if (graduatingSearch) {
        graduatingSearch.addEventListener('input', debounce(() => {
            filterGraduatingTable(graduatingSearch.value.toLowerCase(), graduatingBarangay.value);
        }, 300));
    }
    
    if (graduatingBarangay) {
        graduatingBarangay.addEventListener('change', () => {
            filterGraduatingTable(graduatingSearch.value.toLowerCase(), graduatingBarangay.value);
        });
    }

    console.log('Filtering initialized for both tabs');
}

// Filter Without Renewal Applications table
function filterWithoutRenewalTable(searchTerm, selectedBarangay) {
    const state = paginationState.withoutRenewal;
    
    console.log('Filtering Without Renewal:', { searchTerm, selectedBarangay });
    
    const filteredRows = state.allRows.filter(row => {
        const nameCell = row.cells[1]; // Name column (index 1)
        const barangayCell = row.cells[2]; // Barangay column (index 2)

        if (!nameCell || !barangayCell) return false;

        const name = nameCell.textContent.toLowerCase();
        const barangay = barangayCell.textContent.trim();

        const nameMatch = name.includes(searchTerm);
        const barangayMatch = !selectedBarangay || barangay === selectedBarangay;

        return nameMatch && barangayMatch;
    });

    // Update filtered rows and reset to page 1
    state.filteredRows = filteredRows;
    state.currentPage = 1;
    updateStatusPagination('withoutRenewal');
    
    // Reset select all checkbox
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
    
    console.log('Without Renewal filtered results:', filteredRows.length);
}

// Filter Graduating Scholars table
function filterGraduatingTable(searchTerm, selectedBarangay) {
    const state = paginationState.graduating;
    
    console.log('Filtering Graduating:', { searchTerm, selectedBarangay });
    
    const filteredRows = state.allRows.filter(row => {
        const nameCell = row.cells[1]; // Name column (index 1)
        const barangayCell = row.cells[2]; // Barangay column (index 2)

        if (!nameCell || !barangayCell) return false;

        const name = nameCell.textContent.toLowerCase();
        const barangay = barangayCell.textContent.trim();

        const nameMatch = name.includes(searchTerm);
        const barangayMatch = !selectedBarangay || barangay === selectedBarangay;

        return nameMatch && barangayMatch;
    });

    // Update filtered rows and reset to page 1
    state.filteredRows = filteredRows;
    state.currentPage = 1;
    updateStatusPagination('graduating');
    
    // Reset select all checkbox
    const graduatingSelectAll = document.getElementById('graduatingSelectAll');
    if (graduatingSelectAll) {
        graduatingSelectAll.checked = false;
        graduatingSelectAll.indeterminate = false;
    }
    
    console.log('Graduating filtered results:', filteredRows.length);
}

// Update button states for regular scholars (pagination-aware)
function updateButtons() {
    const state = paginationState.withoutRenewal;
    const currentPageRows = state.filteredRows.slice(
        (state.currentPage - 1) * state.rowsPerPage,
        state.currentPage * state.rowsPerPage
    );
    
    const visibleCheckboxes = currentPageRows.map(row => 
        row.querySelector('.scholar-checkbox')
    ).filter(checkbox => checkbox !== null);
    
    const selectedCount = visibleCheckboxes.filter(cb => cb.checked).length;
    
    const copyNamesBtn = document.getElementById('copyNamesBtn');
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const updateStatusBtn = document.getElementById('updateStatusBtn');
    
    if (copyNamesBtn) copyNamesBtn.disabled = selectedCount === 0;
    if (sendEmailBtn) sendEmailBtn.disabled = selectedCount === 0;
    if (updateStatusBtn) updateStatusBtn.disabled = selectedCount === 0;
    if (copyNamesBtn) copyNamesBtn.classList.toggle('hidden', selectedCount === 0);
    if (sendEmailBtn) sendEmailBtn.classList.toggle('hidden', selectedCount === 0);
    if (updateStatusBtn) updateStatusBtn.classList.toggle('hidden', selectedCount === 0);
}

// Update button states for graduating scholars (pagination-aware)
function updateGraduatingButtons() {
    const state = paginationState.graduating;
    const currentPageRows = state.filteredRows.slice(
        (state.currentPage - 1) * state.rowsPerPage,
        state.currentPage * state.rowsPerPage
    );
    
    const visibleCheckboxes = currentPageRows.map(row => 
        row.querySelector('.graduating-scholar-checkbox')
    ).filter(checkbox => checkbox !== null);
    
    const selectedCount = visibleCheckboxes.filter(cb => cb.checked).length;
    
    const graduatingCopyNamesBtn = document.getElementById('graduatingCopyNamesBtn');
    const graduatingSendEmailBtn = document.getElementById('graduatingSendEmailBtn');
    const markAsGraduatedBtn = document.getElementById('markAsGraduatedBtn');
    
    if (graduatingCopyNamesBtn) graduatingCopyNamesBtn.disabled = selectedCount === 0;
    if (graduatingSendEmailBtn) graduatingSendEmailBtn.disabled = selectedCount === 0;
    if (markAsGraduatedBtn) markAsGraduatedBtn.disabled = selectedCount === 0;
    if (graduatingCopyNamesBtn) graduatingCopyNamesBtn.classList.toggle('hidden', selectedCount === 0);
    if (graduatingSendEmailBtn) graduatingSendEmailBtn.classList.toggle('hidden', selectedCount === 0);
    if (markAsGraduatedBtn) markAsGraduatedBtn.classList.toggle('hidden', selectedCount === 0);
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

// Tab switching function
function switchToTab(tabName) {
    console.log('Switching to tab:', tabName);
    
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(tab => {
        tab.classList.remove('border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    const targetContent = document.getElementById(tabName + 'Content');
    if (targetContent) {
        targetContent.classList.remove('hidden');
    }
    
    // Activate selected tab
    const targetTab = document.getElementById(tabName + 'Tab');
    if (targetTab) {
        targetTab.classList.add('border-blue-500', 'text-blue-600');
        targetTab.classList.remove('border-transparent', 'text-gray-500');
    }
    
    // Update button states for the active tab
    if (tabName === 'withoutRenewal') {
        updateButtons();
    } else if (tabName === 'graduating') {
        updateGraduatingButtons();
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing status page...');
    
    initializeStatusData();
    initializeStatusPagination();
    initializeStatusFiltering();
    
    console.log('Status page filtering and pagination initialized successfully');
});