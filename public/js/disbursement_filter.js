// Enhanced filtering for disbursement page
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
        this.loadUnsignedData();
        this.loadSignedData();
        this.setupEventListeners();
        this.setupAutoFilter();
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
        // Search input clear button
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', () => this.filterData());
        }

        // Filter select elements
        const barangayFilter = document.querySelector('select[name="barangay"]');
        const academicYearFilter = document.querySelector('select[name="academic_year"]');
        const semesterFilter = document.querySelector('select[name="semester"]');

        if (barangayFilter) {
            barangayFilter.addEventListener('change', () => this.filterData());
        }
        if (academicYearFilter) {
            academicYearFilter.addEventListener('change', () => this.filterData());
        }
        if (semesterFilter) {
            semesterFilter.addEventListener('change', () => this.filterData());
        }
    }

    setupAutoFilter() {
        // Real-time filtering without form submission
        const filterInputs = document.querySelectorAll('#filterForm input, #filterForm select');
        
        filterInputs.forEach(input => {
            input.addEventListener('input', () => {
                this.filterData();
            });
            
            input.addEventListener('change', () => {
                this.filterData();
            });
        });
    }

    filterData() {
        const searchTerm = document.querySelector('input[name="search"]').value.toLowerCase();
        const barangayFilter = document.querySelector('select[name="barangay"]').value;
        const academicYearFilter = document.querySelector('select[name="academic_year"]').value;
        const semesterFilter = document.querySelector('select[name="semester"]').value;

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

        // Hide all rows
        this.unsignedData.forEach(item => {
            item.element.style.display = 'none';
        });

        // Show only current page rows
        pageData.forEach(item => {
            item.element.style.display = '';
        });

        this.renderUnsignedPagination(visibleData.length);
    }

    renderSignedPage() {
        const visibleData = this.signedData.filter(item => item.isVisible);
        const startIndex = (this.signedCurrentPage - 1) * this.rowsPerPage;
        const endIndex = startIndex + this.rowsPerPage;
        const pageData = visibleData.slice(startIndex, endIndex);

        // Hide all rows
        this.signedData.forEach(item => {
            item.element.style.display = 'none';
        });

        // Show only current page rows
        pageData.forEach(item => {
            item.element.style.display = '';
        });

        this.renderSignedPagination(visibleData.length);
    }

    renderUnsignedPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / this.rowsPerPage);
        const container = document.getElementById('unsignedPagination') || this.createPaginationContainer('unsigned');
        
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <div class="flex justify-center items-center space-x-2 mt-4">
                <button onclick="disbursementFilter.prevUnsignedPage()" 
                        ${this.unsignedCurrentPage === 1 ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.unsignedCurrentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Previous
                </button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === this.unsignedCurrentPage) {
                paginationHTML += `
                    <button class="px-3 py-1 rounded border bg-red-600 text-white">
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

        paginationHTML += `
                <button onclick="disbursementFilter.nextUnsignedPage()" 
                        ${this.unsignedCurrentPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.unsignedCurrentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Next
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
        
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = `
            <div class="flex justify-center items-center space-x-2 mt-4">
                <button onclick="disbursementFilter.prevSignedPage()" 
                        ${this.signedCurrentPage === 1 ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.signedCurrentPage === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Previous
                </button>
        `;

        for (let i = 1; i <= totalPages; i++) {
            if (i === this.signedCurrentPage) {
                paginationHTML += `
                    <button class="px-3 py-1 rounded border bg-green-600 text-white">
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

        paginationHTML += `
                <button onclick="disbursementFilter.nextSignedPage()" 
                        ${this.signedCurrentPage === totalPages ? 'disabled' : ''}
                        class="px-3 py-1 rounded border ${this.signedCurrentPage === totalPages ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    Next
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
            tabContent.appendChild(container);
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

// Initialize disbursement filter
const disbursementFilter = new DisbursementFilter();

// Modified clearFilters function for disbursement
function clearFilters() {
    document.querySelector('input[name="search"]').value = '';
    document.querySelector('select[name="barangay"]').value = '';
    document.querySelector('select[name="academic_year"]').value = '';
    document.querySelector('select[name="semester"]').value = '';
    
    disbursementFilter.filterData();
}

// Remove the old auto-filter functionality and replace with:
document.addEventListener('DOMContentLoaded', function() {
    // Remove the old form submission behavior
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            disbursementFilter.filterData();
        });
    }
});