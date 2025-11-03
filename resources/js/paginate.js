const itemsPerPage = 15;

const paginationState = {
    table: { currentPage: 1, totalPages: 1, perPage: itemsPerPage, rows: [] },
    list: { currentPage: 1, totalPages: 1, perPage: itemsPerPage, rows: [] }
};

const isDataRow = (row) => !row.querySelector('th');

function renderPaginationControls(view) {
    const state = paginationState[view];
    const container = document.getElementById(view === 'table' ? 'tablePagination' : 'listPagination');
    if (!container) return;

    const createBtn = (text, disabled = false, cls = '') => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `mx-1 px-3 py-1 rounded border ${cls} ${disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'}`;
        btn.textContent = text;
        btn.disabled = disabled;
        return btn;
    };

    container.innerHTML = '';
    
    // Previous button
    const prev = createBtn('Prev', state.currentPage === 1);
    prev.onclick = () => goToPage(view, state.currentPage - 1);
    container.appendChild(prev);

    // Page numbers
    const maxButtons = 7;
    let startPage = Math.max(1, state.currentPage - Math.floor(maxButtons / 2));
    let endPage = Math.min(state.totalPages, startPage + maxButtons - 1);
    if (endPage - startPage + 1 < maxButtons) {
        startPage = Math.max(1, endPage - maxButtons + 1);
    }

    for (let p = startPage; p <= endPage; p++) {
        const cls = p === state.currentPage ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-gray-700';
        const btn = createBtn(p, false, cls);
        btn.onclick = () => goToPage(view, p);
        container.appendChild(btn);
    }

    // Next button
    const next = createBtn('Next', state.currentPage === state.totalPages);
    next.onclick = () => goToPage(view, state.currentPage + 1);
    container.appendChild(next);
}

function goToPage(view, page) {
    const state = paginationState[view];
    if (!page || page < 1) page = 1;
    if (page > state.totalPages) page = state.totalPages;
    
    state.currentPage = page;
    
    // Hide all rows first
    const selector = view === 'table' ? '#tableView tbody tr' : '#listView tbody tr';
    const allRows = Array.from(document.querySelectorAll(selector)).filter(isDataRow);
    allRows.forEach(r => r.style.display = 'none');
    
    // Show only rows for current page
    const start = (state.currentPage - 1) * state.perPage;
    const end = start + state.perPage;
    state.rows.slice(start, end).forEach(r => r.style.display = '');
    
    // Re-render pagination controls
    renderPaginationControls(view);
}

function updatePagination(view) {
    const state = paginationState[view];
    const selector = view === 'table' ? '#tableView tbody tr' : '#listView tbody tr';
    const allRows = Array.from(document.querySelectorAll(selector)).filter(isDataRow);
    
    // Get only visible rows (not filtered out)
    state.rows = allRows.filter(r => r.style.display !== 'none');
    state.totalPages = Math.max(1, Math.ceil(state.rows.length / state.perPage));
    
    // Adjust current page if needed
    if (state.currentPage > state.totalPages) {
        state.currentPage = state.totalPages;
    }
    
    // Show rows for current page
    allRows.forEach(r => r.style.display = 'none');
    const start = (state.currentPage - 1) * state.perPage;
    const end = start + state.perPage;
    state.rows.slice(start, end).forEach(r => r.style.display = '');
    
    renderPaginationControls(view);
}

function filterTable(tableId, searchInputId) {
    const input = document.getElementById(searchInputId).value.toLowerCase();
    const barangaySelectId = tableId === '#tableView' ? 'barangaySelectTable' : 'barangaySelectList';
    const barangayValue = document.getElementById(barangaySelectId).value.toLowerCase();
    const table = document.querySelector(tableId);
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesSearch = text.includes(input);
        const matchesBarangay = barangayValue === '' || text.includes(barangayValue);
        row.style.display = (matchesSearch && matchesBarangay) ? '' : 'none';
    });

    // Update pagination after filtering
    const view = tableId === '#tableView' ? 'table' : 'list';
    updatePagination(view);
}

// Initialize pagination and filtering
document.addEventListener('DOMContentLoaded', () => {
    // Initialize pagination for both views
    updatePagination('table');
    updatePagination('list');

    // Attach filter listeners
    document.getElementById('searchInputTable').addEventListener('input', () => filterTable('#tableView', 'searchInputTable'));
    document.getElementById('searchInputList').addEventListener('input', () => filterTable('#listView', 'searchInputList'));

    // Attach barangay filter listeners
    document.getElementById('barangaySelectTable').addEventListener('change', () => filterTable('#tableView', 'searchInputTable'));
    document.getElementById('barangaySelectList').addEventListener('change', () => filterTable('#listView', 'searchInputList'));
});
