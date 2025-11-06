// Enhanced filtering for disbursement page - Pure Client Side
class DisbursementFilter {
    constructor() {
        this.unsignedCurrentPage = 1;
        this.signedCurrentPage = 1;
        this.rowsPerPage = 10;
        this.unsignedData = [];
        this.signedData = [];
        
        this.init();
    }

    init() {
        this.preventFormSubmission();
        this.loadUnsignedData();
        this.loadSignedData();
        this.setupEventListeners();
    }

    preventFormSubmission() {
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.filterData();
                return false;
            });
        }
    }

    loadUnsignedData() {
        const rows = document.querySelectorAll('#unsignedTabContent tbody tr');
        this.unsignedData = Array.from(rows).map(row => {
            return {
                element: row,
                name: row.cells[0].textContent.trim(),
                barangay: row.cells[1].textContent.trim(),
                semester: row.cells[2].textContent.trim(),
                academicYear: row.cells[3].textContent.trim(),
                isVisible: true
            };
        });
        this.renderUnsignedPage();
    }

    loadSignedData() {
        const rows = document.querySelectorAll('#signedTabContent tbody tr');
        this.signedData = Array.from(rows).map(row => {
            return {
                element: row,
                name: row.cells[0].textContent.trim(),
                barangay: row.cells[1].textContent.trim(),
                semester: row.cells[2].textContent.trim(),
                academicYear: row.cells[3].textContent.trim(),
                isVisible: true
            };
        });
        this.renderSignedPage();
    }

    setupEventListeners() {
        // Remove any existing event listeners and add new ones
        this.removeExistingListeners();
        
        // Search input
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                this.filterData();
            });
        }

        // Filter select elements
        const filters = [
            'select[name="barangay"]',
            'select[name="academic_year"]', 
            'select[name="semester"]'
        ];

        filters.forEach(selector => {
            const element = document.querySelector(selector);
            if (element) {
                element.addEventListener('change', () => {
                    this.filterData();
                });
            }
        });

        // Clear filters button
        const clearButton = document.querySelector('button[onclick="clearFilters()"]');
        if (clearButton) {
            // Remove existing onclick and replace with new one
            clearButton.removeAttribute('onclick');
            clearButton.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }
    }

    removeExistingListeners() {
        // Remove any auto-submit behavior from the original code
        const filterInputs = document.querySelectorAll('#filterForm input, #filterForm select');
        filterInputs.forEach(input => {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
        });
    }

    clearAllFilters() {
        document.querySelector('input[name="search"]').value = '';
        document.querySelector('select[name="barangay"]').value = '';
        document.querySelector('select[name="academic_year"]').value = '';
        document.querySelector('select[name="semester"]').value = '';
        
        this.filterData();
    }

    filterData() {
        const searchTerm = document.querySelector('input[name="search"]').value.toLowerCase();
        const barangayFilter = document.querySelector('select[name="barangay"]').value;
        const academicYearFilter = document.querySelector('select[name="academic_year"]').value;
        const semesterFilter = document.querySelector('select[name="semester"]').value;

        console.log('Filtering with:', { searchTerm, barangayFilter, academicYearFilter, semesterFilter });

        // Filter unsigned data
        this.unsignedData.forEach(item => {
            const matchesSearch = !searchTerm || item.name.toLowerCase().includes(searchTerm);
            const matchesBarangay = !barangayFilter || item.barangay === barangayFilter;
            const matchesAcademicYear = !academicYearFilter || item.academicYear === academicYearFilter;
            const matchesSemester = !semesterFilter || item.semester === semesterFilter;
            
            item.isVisible = matchesSearch && matchesBarangay && matchesAcademicYear && matchesSemester;
        });

        // Filter signed data
        this.signedData.forEach(item => {
            const matchesSearch = !searchTerm || item.name.toLowerCase().includes(searchTerm);
            const matchesBarangay = !barangayFilter || item.barangay === barangayFilter;
            const matchesAcademicYear = !academicYearFilter || item.academicYear === academicYearFilter;
            const matchesSemester = !semesterFilter || item.semester === semesterFilter;
            
            item.isVisible = matchesSearch && matchesBarangay && matchesAcademicYear && matchesSemester;
        });

        this.unsignedCurrentPage = 1;
        this.signedCurrentPage = 1;
        this.renderUnsignedPage();
        this.renderSignedPage();
    }

    renderUnsignedPage() {
        const visibleData = this.unsignedData.filter(item => item.isVisible);
        const startIndex = (this.unsignedCurrentPage - 1) * this.rowsPerPage;
        const endIndex = startIndex + this.rowsPerPage;
        const pageData = visibleData.slice(startIndex, endIndex);

        console.log(`Unsigned: Showing ${pageData.length} of ${visibleData.length} items`);

        // Hide all rows
        this.unsignedData.forEach(item => {
            item.element.style.display = 'none';
        });

        // Show only current page rows
        pageData.forEach(item => {
            item.element.style.display = '';
        });

        this.renderUnsignedPagination(visibleData.length);
        
        // Update the count badge if it exists
        this.updateUnsignedCount(visibleData.length);
    }

    renderSignedPage() {
        const visibleData = this.signedData.filter(item => item.isVisible);
        const startIndex = (this.signedCurrentPage - 1) * this.rowsPerPage;
        const endIndex = startIndex + this.rowsPerPage;
        const pageData = visibleData.slice(startIndex, endIndex);

        console.log(`Signed: Showing ${pageData.length} of ${visibleData.length} items`);

        // Hide all rows
        this.signedData.forEach(item => {
            item.element.style.display = 'none';
        });

        // Show only current page rows
        pageData.forEach(item => {
            item.element.style.display = '';
        });

        this.renderSignedPagination(visibleData.length);
        
        // Update the count badge if it exists
        this.updateSignedCount(visibleData.length);
    }

    updateUnsignedCount(count) {
        const badge = document.querySelector('#unsignedTab .bg-red-500');
        if (badge) {
            badge.textContent = count;
        }
    }

    updateSignedCount(count) {
        const badge = document.querySelector('#signedTab .bg-green-500');
        if (badge) {
            badge.textContent = count;
        }
    }

    renderUnsignedPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / this.rowsPerPage);
        const container = document.getElementById('unsignedPagination') || this.createPaginationContainer('unsigned');
        
        if (totalItems === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 py-4">No records found</div>';
            return;
        }

        if (totalPages <= 1) {
            container.innerHTML = `
                <div class="text-center text-sm text-gray-600 mt-2">
                    Showing ${totalItems} of ${totalItems} entries
                </div>
            `;
            return;
        }

        let paginationHTML = `
            <div class="flex justify-center items-center space-x-2 mt-4">
                <button onclick="disbursementFilter.prevUnsignedPage()" 
                        ${this.unsignedCurrentPage === 1 ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.unsignedCurrentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    <i class="fas fa-chevron-left mr-1"></i> Previous
                </button>
        `;

        // Show page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, this.unsignedCurrentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        if (startPage > 1) {
            paginationHTML += `
                <button onclick="disbursementFilter.goToUnsignedPage(1)" 
                        class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-50">
                    1
                </button>
                ${startPage > 2 ? '<span class="px-2">...</span>' : ''}
            `;
        }

        for (let i = startPage; i <= endPage; i++) {
            if (i === this.unsignedCurrentPage) {
                paginationHTML += `
                    <button class="px-3 py-1 rounded border bg-red-600 text-white font-semibold">
                        ${i}
                    </button>
                `;
            } else {
                paginationHTML += `
                    <button onclick="disbursementFilter.goToUnsignedPage(${i})" 
                            class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-50">
                        ${i}
                    </button>
                `;
            }
        }

        if (endPage < totalPages) {
            paginationHTML += `
                ${endPage < totalPages - 1 ? '<span class="px-2">...</span>' : ''}
                <button onclick="disbursementFilter.goToUnsignedPage(${totalPages})" 
                        class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-50">
                    ${totalPages}
                </button>
            `;
        }

        paginationHTML += `
                <button onclick="disbursementFilter.nextUnsignedPage()" 
                        ${this.unsignedCurrentPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.unsignedCurrentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Next <i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
            <div class="text-center text-sm text-gray-600 mt-2">
                Showing ${Math.min((this.unsignedCurrentPage - 1) * this.rowsPerPage + 1, totalItems)} to ${Math.min(this.unsignedCurrentPage * this.rowsPerPage, totalItems)} of ${totalItems} entries
            </div>
        `;

        container.innerHTML = paginationHTML;
    }

    renderSignedPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / this.rowsPerPage);
        const container = document.getElementById('signedPagination') || this.createPaginationContainer('signed');
        
        if (totalItems === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 py-4">No records found</div>';
            return;
        }

        if (totalPages <= 1) {
            container.innerHTML = `
                <div class="text-center text-sm text-gray-600 mt-2">
                    Showing ${totalItems} of ${totalItems} entries
                </div>
            `;
            return;
        }

        let paginationHTML = `
            <div class="flex justify-center items-center space-x-2 mt-4">
                <button onclick="disbursementFilter.prevSignedPage()" 
                        ${this.signedCurrentPage === 1 ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.signedCurrentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    <i class="fas fa-chevron-left mr-1"></i> Previous
                </button>
        `;

        // Show page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, this.signedCurrentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        if (startPage > 1) {
            paginationHTML += `
                <button onclick="disbursementFilter.goToSignedPage(1)" 
                        class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-50">
                    1
                </button>
                ${startPage > 2 ? '<span class="px-2">...</span>' : ''}
            `;
        }

        for (let i = startPage; i <= endPage; i++) {
            if (i === this.signedCurrentPage) {
                paginationHTML += `
                    <button class="px-3 py-1 rounded border bg-green-600 text-white font-semibold">
                        ${i}
                    </button>
                `;
            } else {
                paginationHTML += `
                    <button onclick="disbursementFilter.goToSignedPage(${i})" 
                            class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-50">
                        ${i}
                    </button>
                `;
            }
        }

        if (endPage < totalPages) {
            paginationHTML += `
                ${endPage < totalPages - 1 ? '<span class="px-2">...</span>' : ''}
                <button onclick="disbursementFilter.goToSignedPage(${totalPages})" 
                        class="px-3 py-1 rounded border bg-white text-gray-700 hover:bg-gray-50">
                    ${totalPages}
                </button>
            `;
        }

        paginationHTML += `
                <button onclick="disbursementFilter.nextSignedPage()" 
                        ${this.signedCurrentPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.signedCurrentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Next <i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
            <div class="text-center text-sm text-gray-600 mt-2">
                Showing ${Math.min((this.signedCurrentPage - 1) * this.rowsPerPage + 1, totalItems)} to ${Math.min(this.signedCurrentPage * this.rowsPerPage, totalItems)} of ${totalItems} entries
            </div>
        `;

        container.innerHTML = paginationHTML;
    }

    createPaginationContainer(type) {
        const container = document.createElement('div');
        container.id = `${type}Pagination`;
        container.className = 'pagination-container mt-4';
        
        const tabContent = document.getElementById(`${type}TabContent`);
        if (tabContent) {
            const table = tabContent.querySelector('table');
            if (table) {
                table.parentNode.insertBefore(container, table.nextSibling);
            } else {
                tabContent.appendChild(container);
            }
        }
        
        return container;
    }

    // Navigation methods for unsigned tab
    prevUnsignedPage() {
        if (this.unsignedCurrentPage > 1) {
            this.unsignedCurrentPage--;
            this.renderUnsignedPage();
        }
    }

    nextUnsignedPage() {
        const totalPages = Math.ceil(this.unsignedData.filter(item => item.isVisible).length / this.rowsPerPage);
        if (this.unsignedCurrentPage < totalPages) {
            this.unsignedCurrentPage++;
            this.renderUnsignedPage();
        }
    }

    goToUnsignedPage(page) {
        this.unsignedCurrentPage = page;
        this.renderUnsignedPage();
    }

    // Navigation methods for signed tab
    prevSignedPage() {
        if (this.signedCurrentPage > 1) {
            this.signedCurrentPage--;
            this.renderSignedPage();
        }
    }

    nextSignedPage() {
        const totalPages = Math.ceil(this.signedData.filter(item => item.isVisible).length / this.rowsPerPage);
        if (this.signedCurrentPage < totalPages) {
            this.signedCurrentPage++;
            this.renderSignedPage();
        }
    }

    goToSignedPage(page) {
        this.signedCurrentPage = page;
        this.renderSignedPage();
    }
}

// Initialize disbursement filter when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.disbursementFilter = new DisbursementFilter();
    
    // Replace the global clearFilters function
    window.clearFilters = function() {
        if (window.disbursementFilter) {
            window.disbursementFilter.clearAllFilters();
        }
    };
});

// Fallback initialization
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.disbursementFilter) {
            window.disbursementFilter = new DisbursementFilter();
        }
    });
} else {
    window.disbursementFilter = new DisbursementFilter();
}