const itemsPerPage = 15;

function paginateTable(tableId, page) {
    const table = document.querySelector(tableId);
    const rows = table.querySelectorAll('tbody tr');
    const totalPages = Math.ceil(rows.length / itemsPerPage);

    // Hide all rows
    rows.forEach(row => row.style.display = 'none');

    // Calculate start and end index for the current page
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    // Show rows for the current page
    for (let i = start; i < end && i < rows.length; i++) {
        rows[i].style.display = '';
    }

    // Update pagination controls
    updatePaginationControls(totalPages, page, tableId);
}

function updatePaginationControls(totalPages, currentPage, tableId) {
    const paginationContainer = document.getElementById('paginationControls');
    paginationContainer.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.innerText = i;
        button.className = (i === currentPage) ? 'active' : '';
        button.onclick = () => paginateTable(tableId, i);
        paginationContainer.appendChild(button);
    }
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

    // Reset pagination after filtering
    paginateTable(tableId, 1);
}

// Initialize pagination and filtering
document.addEventListener('DOMContentLoaded', () => {
    // Initialize pagination for both views
    paginateTable('#tableView', 1);
    paginateTable('#listView', 1);

    // Attach filter listeners
    document.getElementById('searchInputTable').addEventListener('input', () => filterTable('#tableView', 'searchInputTable'));
    document.getElementById('searchInputList').addEventListener('input', () => filterTable('#listView', 'searchInputList'));

    // Attach barangay filter listeners
    document.getElementById('barangaySelectTable').addEventListener('change', () => filterTable('#tableView', 'searchInputTable'));
    document.getElementById('barangaySelectList').addEventListener('change', () => filterTable('#listView', 'searchInputList'));
});
