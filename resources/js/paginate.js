// Pagination script for mayor staff application tables
class TablePaginator {
    constructor(tableBodyId, paginationContainerId, rowsPerPage = 10) {
        this.tableBodyId = tableBodyId;
        this.paginationContainerId = paginationContainerId;
        this.rowsPerPage = rowsPerPage;
        this.currentPage = 1;
        this.allRows = [];
        this.filteredRows = [];

        this.init();
    }

    init() {
        this.updateRows();
        this.renderPagination();
        this.showPage(1);
    }

    updateRows() {
        const tableBody = document.querySelector(`#${this.tableBodyId}`);
        if (tableBody) {
            this.allRows = Array.from(tableBody.querySelectorAll('tr'));
            this.filteredRows = [...this.allRows];
        }
    }

    filterRows(searchValue = '', barangayValue = '') {
        this.filteredRows = this.allRows.filter(row => {
            const nameCell = row.cells[1]; // Name column
            const barangayCell = row.cells[2]; // Barangay column

            if (nameCell && barangayCell) {
                const nameText = nameCell.textContent.toLowerCase();
                const barangayText = barangayCell.textContent.trim();

                const matchesSearch = searchValue === '' || nameText.includes(searchValue);
                const matchesBarangay = barangayValue === '' || barangayText === barangayValue;

                return matchesSearch && matchesBarangay;
            }
            return true;
        });

        this.currentPage = 1;
        this.renderPagination();
        this.showPage(1);
    }

    showPage(page) {
        this.currentPage = page;
        const startIndex = (page - 1) * this.rowsPerPage;
        const endIndex = startIndex + this.rowsPerPage;

        // Hide all rows first
        this.allRows.forEach(row => {
            row.style.display = 'none';
        });

        // Show only filtered rows for current page
        this.filteredRows.slice(startIndex, endIndex).forEach(row => {
            row.style.display = '';
        });

        this.updatePaginationButtons();
    }

    renderPagination() {
        const container = document.getElementById(this.paginationContainerId);
        if (!container) return;

        const totalPages = Math.ceil(this.filteredRows.length / this.rowsPerPage);

        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6 rounded-b-lg">
                <div class="flex justify-between flex-1 sm:hidden">
                    <button id="${this.paginationContainerId}-prev-mobile" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <button id="${this.paginationContainerId}-next-mobile" class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">${Math.min((this.currentPage - 1) * this.rowsPerPage + 1, this.filteredRows.length)}</span>
                            to <span class="font-medium">${Math.min(this.currentPage * this.rowsPerPage, this.filteredRows.length)}</span>
                            of <span class="font-medium">${this.filteredRows.length}</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        `;

        // Previous button
        paginationHTML += `
            <button id="${this.paginationContainerId}-prev" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(totalPages, this.currentPage + 2);

        if (startPage > 1) {
            paginationHTML += `
                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" onclick="window.tablePaginators['${this.tableBodyId}'].showPage(1)">1</button>
            `;
            if (startPage > 2) {
                paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === this.currentPage;
            paginationHTML += `
                <button class="relative inline-flex items-center px-4 py-2 border ${isActive ? 'border-indigo-500 bg-indigo-50 text-indigo-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'} text-sm font-medium" onclick="window.tablePaginators['${this.tableBodyId}'].showPage(${i})">${i}</button>
            `;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>`;
            }
            paginationHTML += `
                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" onclick="window.tablePaginators['${this.tableBodyId}'].showPage(${totalPages})">${totalPages}</button>
            `;
        }

        // Next button
        paginationHTML += `
            <button id="${this.paginationContainerId}-next" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

        paginationHTML += `
                        </nav>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = paginationHTML;
        this.updatePaginationButtons();
        this.attachEventListeners();
    }

    updatePaginationButtons() {
        const totalPages = Math.ceil(this.filteredRows.length / this.rowsPerPage);
        const prevBtn = document.getElementById(`${this.paginationContainerId}-prev`);
        const nextBtn = document.getElementById(`${this.paginationContainerId}-next`);
        const prevMobileBtn = document.getElementById(`${this.paginationContainerId}-prev-mobile`);
        const nextMobileBtn = document.getElementById(`${this.paginationContainerId}-next-mobile`);

        if (prevBtn) {
            prevBtn.disabled = this.currentPage === 1;
        }
        if (nextBtn) {
            nextBtn.disabled = this.currentPage === totalPages;
        }
        if (prevMobileBtn) {
            prevMobileBtn.disabled = this.currentPage === 1;
        }
        if (nextMobileBtn) {
            nextMobileBtn.disabled = this.currentPage === totalPages;
        }
    }

    attachEventListeners() {
        const prevBtn = document.getElementById(`${this.paginationContainerId}-prev`);
        const nextBtn = document.getElementById(`${this.paginationContainerId}-next`);
        const prevMobileBtn = document.getElementById(`${this.paginationContainerId}-prev-mobile`);
        const nextMobileBtn = document.getElementById(`${this.paginationContainerId}-next-mobile`);

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.showPage(this.currentPage - 1);
                }
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const totalPages = Math.ceil(this.filteredRows.length / this.rowsPerPage);
                if (this.currentPage < totalPages) {
                    this.showPage(this.currentPage + 1);
                }
            });
        }
        if (prevMobileBtn) {
            prevMobileBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.showPage(this.currentPage - 1);
                }
            });
        }
        if (nextMobileBtn) {
            nextMobileBtn.addEventListener('click', () => {
                const totalPages = Math.ceil(this.filteredRows.length / this.rowsPerPage);
                if (this.currentPage < totalPages) {
                    this.showPage(this.currentPage + 1);
                }
            });
        }
    }
}

// Global paginator instances
window.tablePaginators = {};

// Initialize pagination when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize pagination for table view (pending applications)
    if (document.getElementById('tablePagination')) {
        window.tablePaginators['tableView tbody'] = new TablePaginator('tableView tbody', 'tablePagination', 10);
    }

    // Initialize pagination for list view (reviewed applications)
    if (document.getElementById('listPagination')) {
        window.tablePaginators['listView tbody'] = new TablePaginator('listView tbody', 'listPagination', 10);
    }

    // Attach filter listeners for table view
    const tableSearch = document.getElementById('searchInputTable');
    const tableBrgy = document.getElementById('barangaySelectTable');
    if (tableSearch && window.tablePaginators['tableView tbody']) {
        tableSearch.addEventListener('input', debounce(() => {
            const searchValue = tableSearch.value.toLowerCase();
            const barangayValue = tableBrgy ? tableBrgy.value : '';
            window.tablePaginators['tableView tbody'].filterRows(searchValue, barangayValue);
        }, 150));
    }
    if (tableBrgy && window.tablePaginators['tableView tbody']) {
        tableBrgy.addEventListener('change', () => {
            const searchValue = tableSearch ? tableSearch.value.toLowerCase() : '';
            const barangayValue = tableBrgy.value;
            window.tablePaginators['tableView tbody'].filterRows(searchValue, barangayValue);
        });
    }

    // Attach filter listeners for list view
    const listSearch = document.getElementById('searchInputList');
    const listBrgy = document.getElementById('barangaySelectList');
    if (listSearch && window.tablePaginators['listView tbody']) {
        listSearch.addEventListener('input', debounce(() => {
            const searchValue = listSearch.value.toLowerCase();
            const barangayValue = listBrgy ? listBrgy.value : '';
            window.tablePaginators['listView tbody'].filterRows(searchValue, barangayValue);
        }, 150));
    }
    if (listBrgy && window.tablePaginators['listView tbody']) {
        listBrgy.addEventListener('change', () => {
            const searchValue = listSearch ? listSearch.value.toLowerCase() : '';
            const barangayValue = listBrgy.value;
            window.tablePaginators['listView tbody'].filterRows(searchValue, barangayValue);
        });
    }
});

// Debounce utility function
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

// Function to refresh pagination when table content changes
function refreshPagination(tableBodyId) {
    if (window.tablePaginators[tableBodyId]) {
        window.tablePaginators[tableBodyId].updateRows();
        window.tablePaginators[tableBodyId].renderPagination();
        window.tablePaginators[tableBodyId].showPage(1);
    }
}
