// Smooth Auto Refresh for Application Tables
class ApplicationRefresher {
    constructor() {
        this.isRefreshing = false;
        this.lastRefreshTime = Date.now();
        this.refreshInterval = 3000; // 3 seconds
        this.currentView = localStorage.getItem('viewMode') || 'table';
        this.init();
    }

    init() {
        console.log('Application Refresher initialized');
        this.startAutoRefresh();
        this.setupEventListeners();
    }

    startAutoRefresh() {
        setInterval(() => {
            this.refreshData();
        }, this.refreshInterval);
    }

    setupEventListeners() {
        // Listen for tab changes to update current view
        document.addEventListener('click', (e) => {
            if (e.target.id === 'pendingTab' || e.target.id === 'reviewedTab') {
                setTimeout(() => {
                    this.currentView = localStorage.getItem('viewMode') || 'table';
                }, 100);
            }
        });

        // Prevent refresh when modals are open
        document.addEventListener('showModal', () => {
            this.pauseRefresh();
        });

        document.addEventListener('hideModal', () => {
            this.resumeRefresh();
        });
    }

    async refreshData() {
        if (this.isRefreshing) return;

        try {
            this.isRefreshing = true;
            
            // Get current application IDs to maintain selection state
            const currentApplications = this.getCurrentApplicationIds();
            
            // Refresh based on current view
            if (this.currentView === 'table') {
                await this.refreshTableView();
            } else {
                await this.refreshListView();
            }

            // Restore any opened application states
            this.restoreApplicationStates(currentApplications);
            
            this.lastRefreshTime = Date.now();
            
        } catch (error) {
            console.error('Refresh error:', error);
        } finally {
            this.isRefreshing = false;
        }
    }

    getCurrentApplicationIds() {
        const applications = [];
        
        // Get IDs from both table and list views
        const tableRows = document.querySelectorAll('#tableView tbody tr');
        const listRows = document.querySelectorAll('#listView tbody tr');
        
        tableRows.forEach(row => {
            const button = row.querySelector('button[onclick*="openApplicationModal"]');
            if (button) {
                const match = button.getAttribute('onclick').match(/openApplicationModal\((\d+),/);
                if (match) {
                    applications.push({
                        id: parseInt(match[1]),
                        type: 'pending',
                        element: row
                    });
                }
            }
        });
        
        listRows.forEach(row => {
            const button = row.querySelector('button[onclick*="openApplicationModal"]');
            if (button) {
                const match = button.getAttribute('onclick').match(/openApplicationModal\((\d+),/);
                if (match) {
                    applications.push({
                        id: parseInt(match[1]),
                        type: 'reviewed',
                        element: row
                    });
                }
            }
        });
        
        return applications;
    }

    async refreshTableView() {
        const response = await fetch('/mayor_staff/get-table-data', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) return;

        const data = await response.json();
        
        if (data.success && data.html) {
            this.smoothUpdateTable('tableView', data.html);
            this.updateDateFormats();
        }
    }

    async refreshListView() {
        const response = await fetch('/mayor_staff/get-list-data', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) return;

        const data = await response.json();
        
        if (data.success && data.html) {
            this.smoothUpdateTable('listView', data.html);
            this.updateDateFormats();
        }
    }

    smoothUpdateTable(containerId, newHtml) {
        const container = document.getElementById(containerId);
        if (!container) return;

        // Create temporary container to parse new HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newHtml;

        // Get the table from new HTML
        const newTable = tempDiv.querySelector('table');
        const currentTable = container.querySelector('table');

        if (!newTable || !currentTable) return;

        // Get current scroll position
        const currentScroll = window.scrollY;

        // Smoothly update table body
        const currentTbody = currentTable.querySelector('tbody');
        const newTbody = newTable.querySelector('tbody');

        if (currentTbody && newTbody) {
            this.fadeOutTbody(currentTbody, () => {
                currentTbody.innerHTML = newTbody.innerHTML;
                this.fadeInTbody(currentTbody);
                
                // Restore scroll position
                window.scrollTo(0, currentScroll);
                
                // Update pagination if exists
                this.updatePagination(containerId, tempDiv);
            });
        }
    }

    fadeOutTbody(tbody, callback) {
        tbody.style.transition = 'opacity 0.3s ease';
        tbody.style.opacity = '0';
        
        setTimeout(() => {
            callback();
        }, 300);
    }

    fadeInTbody(tbody) {
        tbody.style.opacity = '0';
        setTimeout(() => {
            tbody.style.transition = 'opacity 0.3s ease';
            tbody.style.opacity = '1';
        }, 50);
    }

    updatePagination(containerId, tempDiv) {
        const currentPagination = document.querySelector(`#${containerId} .pagination-container`);
        const newPagination = tempDiv.querySelector('.pagination-container');
        
        if (currentPagination && newPagination) {
            currentPagination.innerHTML = newPagination.innerHTML;
        }
    }

    updateDateFormats() {
        // Re-format dates after refresh
        document.querySelectorAll('.date-format').forEach(function(element) {
            const rawDate = element.textContent.trim();
            if (rawDate) {
                // Handle different date formats including "October 23, 2003"
                let formattedDate;
                
                if (rawDate.includes(',')) {
                    // Date is already in readable format, keep it as is
                    formattedDate = rawDate;
                } else {
                    // Format from database date
                    formattedDate = moment(rawDate).format('MMMM D, YYYY');
                }
                
                element.textContent = formattedDate;
            }
        });
    }

    restoreApplicationStates(previousApplications) {
        // This would restore any visual states like hover effects, etc.
        // For now, we'll just ensure the view mode is maintained
        const currentView = localStorage.getItem('viewMode') || 'table';
        
        if (currentView === 'table') {
            document.getElementById('pendingTab')?.classList.add('active');
            document.getElementById('reviewedTab')?.classList.remove('active');
        } else {
            document.getElementById('pendingTab')?.classList.remove('active');
            document.getElementById('reviewedTab')?.classList.add('active');
        }
    }

    pauseRefresh() {
        this.isRefreshing = true;
    }

    resumeRefresh() {
        this.isRefreshing = false;
    }
}

// Custom events for modal handling
const originalOpenApplicationModal = window.openApplicationModal;
window.openApplicationModal = function(...args) {
    document.dispatchEvent(new CustomEvent('showModal'));
    return originalOpenApplicationModal.apply(this, args);
};

const originalCloseApplicationModal = window.closeApplicationModal;
window.closeApplicationModal = function(...args) {
    document.dispatchEvent(new CustomEvent('hideModal'));
    return originalCloseApplicationModal.apply(this, args);
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new ApplicationRefresher();
    
    // Also format dates on initial load
    document.querySelectorAll('.date-format').forEach(function(element) {
        const rawDate = element.textContent.trim();
        if (rawDate) {
            let formattedDate;
            
            if (rawDate.includes(',')) {
                formattedDate = rawDate;
            } else {
                formattedDate = moment(rawDate).format('MMMM D, YYYY');
            }
            
            element.textContent = formattedDate;
        }
    });
});

// Export for testing
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ApplicationRefresher;
}