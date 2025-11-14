// Add this JavaScript to your status.blade.php file
document.addEventListener('DOMContentLoaded', function() {
    // Initialize pagination for both tabs
    initializePagination('withoutRenewal');
    initializePagination('graduating');
});

function initializePagination(tabType) {
    const rowsPerPage = 10;
    let currentPage = 1;
    
    const tableBodyId = tabType === 'withoutRenewal' ? 'withoutRenewalTableBody' : 'graduatingTableBody';
    const paginationContainerId = tabType === 'withoutRenewal' ? 'paginationContainer' : 'graduatingPaginationContainer';
    const rowClass = tabType === 'withoutRenewal' ? '.scholar-row' : '.graduating-scholar-row';
    
    const tableBody = document.getElementById(tableBodyId);
    const paginationContainer = document.getElementById(paginationContainerId);
    
    if (!tableBody || !paginationContainer) return;
    
    const rows = Array.from(tableBody.querySelectorAll(rowClass));
    const totalRows = rows.length;
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    
    function displayPage(page) {
        currentPage = page;
        
        // Hide all rows
        rows.forEach(row => {
            row.style.display = 'none';
        });
        
        // Calculate start and end indices
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        
        // Show rows for current page
        for (let i = startIndex; i < endIndex && i < totalRows; i++) {
            if (rows[i]) {
                rows[i].style.display = '';
            }
        }
        
        // Update pagination controls
        renderPagination();
        
        // Update button states
        if (tabType === 'withoutRenewal') {
            updateButtons();
        } else {
            updateGraduatingButtons();
        }
    }
    
    function renderPagination() {
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let paginationHTML = '';
        
        // Previous button
        if (currentPage > 1) {
            paginationHTML += `
                <li class="pagination-item">
                    <a href="#" class="pagination-link pagination-arrow" data-page="${currentPage - 1}">
                        &laquo;
                    </a>
                </li>
            `;
        } else {
            paginationHTML += `
                <li class="pagination-item">
                    <span class="pagination-link pagination-arrow disabled">&laquo;</span>
                </li>
            `;
        }
        
        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                paginationHTML += `
                    <li class="pagination-item">
                        <span class="pagination-link active">${i}</span>
                    </li>
                `;
            } else {
                paginationHTML += `
                    <li class="pagination-item">
                        <a href="#" class="pagination-link" data-page="${i}">${i}</a>
                    </li>
                `;
            }
        }
        
        // Next button
        if (currentPage < totalPages) {
            paginationHTML += `
                <li class="pagination-item">
                    <a href="#" class="pagination-link pagination-arrow" data-page="${currentPage + 1}">
                        &raquo;
                    </a>
                </li>
            `;
        } else {
            paginationHTML += `
                <li class="pagination-item">
                    <span class="pagination-link pagination-arrow disabled">&raquo;</span>
                </li>
            `;
        }
        
        // Page info
        const startRow = (currentPage - 1) * rowsPerPage + 1;
        const endRow = Math.min(currentPage * rowsPerPage, totalRows);
        
        paginationHTML = `
            <div class="pagination-info">
                Showing ${startRow} to ${endRow} of ${totalRows} entries
            </div>
            <ul class="pagination">
                ${paginationHTML}
            </ul>
        `;
        
        paginationContainer.innerHTML = paginationHTML;
        
        // Add event listeners to pagination links
        paginationContainer.querySelectorAll('.pagination-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                displayPage(page);
            });
        });
    }
    
    // Initialize
    displayPage(1);
    
    // Update pagination when filtering changes
    if (tabType === 'withoutRenewal') {
        // Re-initialize pagination when filters change for without renewal tab
        const originalFilterFunction = window.filterWithoutRenewalTable;
        window.filterWithoutRenewalTable = function() {
            originalFilterFunction();
            setTimeout(() => {
                const visibleRows = Array.from(tableBody.querySelectorAll(`${rowClass}:not([style*="display: none"])`));
                updatePaginationForFilteredRows(visibleRows, tabType);
            }, 100);
        };
    } else {
        // Re-initialize pagination when filters change for graduating tab
        const originalFilterFunction = window.filterGraduatingTable;
        window.filterGraduatingTable = function() {
            originalFilterFunction();
            setTimeout(() => {
                const visibleRows = Array.from(tableBody.querySelectorAll(`${rowClass}:not([style*="display: none"])`));
                updatePaginationForFilteredRows(visibleRows, tabType);
            }, 100);
        };
    }
}

function updatePaginationForFilteredRows(visibleRows, tabType) {
    const rowsPerPage = 10;
    let currentPage = 1;
    
    const paginationContainerId = tabType === 'withoutRenewal' ? 'paginationContainer' : 'graduatingPaginationContainer';
    const paginationContainer = document.getElementById(paginationContainerId);
    
    if (!paginationContainer) return;
    
    const totalRows = visibleRows.length;
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    
    function displayFilteredPage(page) {
        currentPage = page;
        
        // Hide all rows first
        const allRows = document.querySelectorAll(tabType === 'withoutRenewal' ? '.scholar-row' : '.graduating-scholar-row');
        allRows.forEach(row => {
            row.style.display = 'none';
        });
        
        // Calculate start and end indices
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        
        // Show visible rows for current page
        for (let i = startIndex; i < endIndex && i < totalRows; i++) {
            if (visibleRows[i]) {
                visibleRows[i].style.display = '';
            }
        }
        
        renderFilteredPagination();
        
        // Update button states
        if (tabType === 'withoutRenewal') {
            updateButtons();
        } else {
            updateGraduatingButtons();
        }
    }
    
    function renderFilteredPagination() {
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let paginationHTML = '';
        
        // Previous button
        if (currentPage > 1) {
            paginationHTML += `
                <li class="pagination-item">
                    <a href="#" class="pagination-link pagination-arrow" data-page="${currentPage - 1}">
                        &laquo;
                    </a>
                </li>
            `;
        } else {
            paginationHTML += `
                <li class="pagination-item">
                    <span class="pagination-link pagination-arrow disabled">&laquo;</span>
                </li>
            `;
        }
        
        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                paginationHTML += `
                    <li class="pagination-item">
                        <span class="pagination-link active">${i}</span>
                    </li>
                `;
            } else {
                paginationHTML += `
                    <li class="pagination-item">
                        <a href="#" class="pagination-link" data-page="${i}">${i}</a>
                    </li>
                `;
            }
        }
        
        // Next button
        if (currentPage < totalPages) {
            paginationHTML += `
                <li class="pagination-item">
                    <a href="#" class="pagination-link pagination-arrow" data-page="${currentPage + 1}">
                        &raquo;
                    </a>
                </li>
            `;
        } else {
            paginationHTML += `
                <li class="pagination-item">
                    <span class="pagination-link pagination-arrow disabled">&raquo;</span>
                </li>
            `;
        }
        
        // Page info
        const startRow = (currentPage - 1) * rowsPerPage + 1;
        const endRow = Math.min(currentPage * rowsPerPage, totalRows);
        
        paginationHTML = `
            <div class="pagination-info">
                Showing ${startRow} to ${endRow} of ${totalRows} entries
            </div>
            <ul class="pagination">
                ${paginationHTML}
            </ul>
        `;
        
        paginationContainer.innerHTML = paginationHTML;
        
        // Add event listeners to pagination links
        paginationContainer.querySelectorAll('.pagination-link[data-page]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                displayFilteredPage(page);
            });
        });
    }
    
    // Initialize filtered pagination
    displayFilteredPage(1);
}

// Update your existing tab switching function to reset pagination
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
        console.log('Showing content for:', tabName);
    }
    
    // Activate selected tab
    const targetTab = document.getElementById(tabName + 'Tab');
    if (targetTab) {
        targetTab.classList.add('border-blue-500', 'text-blue-600');
        targetTab.classList.remove('border-transparent', 'text-gray-500');
    }
    
    // Re-initialize pagination for the active tab
    setTimeout(() => {
        initializePagination(tabName);
    }, 100);
    
    // Update button states
    if (tabName === 'withoutRenewal') {
        updateButtons();
    } else if (tabName === 'graduating') {
        updateGraduatingButtons();
    }
}