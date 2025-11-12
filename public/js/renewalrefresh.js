// renewalrefresh.js - Auto refresh for renewal tables and modal content
document.addEventListener('DOMContentLoaded', function() {
    let refreshInterval;
    let currentView = 'tableView'; // Default view
    let currentModalScholarId = null;
    let isModalOpen = false;

    // Initialize auto-refresh
    initializeAutoRefresh();

    // Function to initialize auto-refresh
    function initializeAutoRefresh() {
        // Clear any existing interval
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }

        // Set new interval for auto-refresh every 2 seconds
        refreshInterval = setInterval(() => {
            refreshContent();
        }, 2000);
    }

    // Main refresh function
    async function refreshContent() {
        try {
            // Refresh the current table view
            await refreshCurrentView();
            
            // If modal is open, refresh modal content using existing functions
            if (isModalOpen && currentModalScholarId) {
                await refreshModalContent(currentModalScholarId);
            }
        } catch (error) {
            console.error('Error during auto-refresh:', error);
        }
    }

    // Refresh current view (table or list)
    async function refreshCurrentView() {
        try {
            // Get current URL with parameters
            const currentUrl = window.location.href;
            const response = await fetch(currentUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html, */*'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const text = await response.text();
            const parser = new DOMParser();
            const newDocument = parser.parseFromString(text, 'text/html');
            
            if (currentView === 'tableView') {
                await updateTableView(newDocument);
            } else {
                await updateListView(newDocument);
            }

        } catch (error) {
            console.error('Error refreshing view:', error);
        }
    }

    // Update table view
    async function updateTableView(newDocument) {
        const newTableView = newDocument.getElementById('tableView');
        const currentTableView = document.getElementById('tableView');
        
        if (!newTableView || !currentTableView) return;

        // Get current scroll position
        const scrollPosition = window.scrollY;
        
        // Extract only the table body content to avoid disrupting filters
        const newTbody = newTableView.querySelector('tbody');
        const currentTbody = currentTableView.querySelector('tbody');
        
        if (newTbody && currentTbody) {
            // Replace table body content
            currentTbody.innerHTML = newTbody.innerHTML;
            
            // Update pagination if exists
            const newPagination = newTableView.querySelector('.pagination-container');
            const currentPagination = currentTableView.querySelector('.pagination-container');
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }
            
            // Re-attach event listeners
            reattachTableViewEventListeners();
        }
        
        // Restore scroll position
        window.scrollTo(0, scrollPosition);
    }

    // Update list view
    async function updateListView(newDocument) {
        const newListView = newDocument.getElementById('listView');
        const currentListView = document.getElementById('listView');
        
        if (!newListView || !currentListView) return;

        // Get current scroll position
        const scrollPosition = window.scrollY;
        
        // Extract only the table body content
        const newTbody = newListView.querySelector('tbody');
        const currentTbody = currentListView.querySelector('tbody');
        
        if (newTbody && currentTbody) {
            // Replace table body content
            currentTbody.innerHTML = newTbody.innerHTML;
            
            // Update pagination if exists
            const newPagination = newListView.querySelector('.pagination-container');
            const currentPagination = currentListView.querySelector('.pagination-container');
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }
            
            // Re-attach event listeners
            reattachListViewEventListeners();
        }
        
        // Restore scroll position
        window.scrollTo(0, scrollPosition);
    }

    // Refresh modal content by re-opening it
    async function refreshModalContent(scholarId) {
        if (!scholarId) return;

        try {
            // Use the existing modal function to refresh content
            if (window.openRenewalModal) {
                // Store current modal state
                const modal = document.getElementById('openRenewalModal');
                if (modal && !modal.classList.contains('hidden')) {
                    // The modal will automatically fetch fresh data when opened
                    // We don't need to do anything as the table refresh will handle it
                }
            }
        } catch (error) {
            console.error('Error refreshing modal:', error);
        }
    }

    // Re-attach event listeners for table view buttons
    function reattachTableViewEventListeners() {
        // Review buttons in table view
        const reviewButtons = document.querySelectorAll('#tableView button[onclick*="openRenewalModal"]');
        reviewButtons.forEach(button => {
            // Remove existing onclick and add fresh event listener
            const originalOnclick = button.getAttribute('onclick');
            if (originalOnclick && originalOnclick.includes('openRenewalModal')) {
                button.removeAttribute('onclick');
                button.addEventListener('click', function() {
                    const scholarId = this.closest('tr').getAttribute('data-id');
                    if (scholarId) {
                        openRenewalModal(parseInt(scholarId));
                    }
                });
            }
        });
    }

    // Re-attach event listeners for list view buttons
    function reattachListViewEventListeners() {
        // Review buttons in list view
        const reviewButtons = document.querySelectorAll('#listView button[onclick*="openViewRenewalModal"]');
        reviewButtons.forEach(button => {
            const originalOnclick = button.getAttribute('onclick');
            if (originalOnclick && originalOnclick.includes('openViewRenewalModal')) {
                button.removeAttribute('onclick');
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const scholarId = row.getAttribute('data-id') || 
                                    row.querySelector('td:nth-child(1)')?.textContent?.trim();
                    if (scholarId) {
                        openViewRenewalModal(parseInt(scholarId));
                    }
                });
            }
        });

        // Edit buttons in list view
        const editButtons = document.querySelectorAll('#listView button[onclick*="openEditRenewalModal"]');
        editButtons.forEach(button => {
            const originalOnclick = button.getAttribute('onclick');
            if (originalOnclick && originalOnclick.includes('openEditRenewalModal')) {
                button.removeAttribute('onclick');
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const scholarId = row.getAttribute('data-id') || 
                                    row.querySelector('td:nth-child(1)')?.textContent?.trim();
                    const statusBadge = row.querySelector('.status-badge');
                    const status = statusBadge ? statusBadge.textContent.trim() : 'Pending';
                    
                    if (scholarId) {
                        openEditRenewalModal(parseInt(scholarId), status);
                    }
                });
            }
        });
    }

    // Track modal state by overriding existing functions
    function trackModalState() {
        // Track openRenewalModal
        const originalOpenRenewalModal = window.openRenewalModal;
        if (originalOpenRenewalModal) {
            window.openRenewalModal = function(scholarId) {
                isModalOpen = true;
                currentModalScholarId = scholarId;
                originalOpenRenewalModal(scholarId);
            };
        }

        // Track openViewRenewalModal
        const originalOpenViewRenewalModal = window.openViewRenewalModal;
        if (originalOpenViewRenewalModal) {
            window.openViewRenewalModal = function(scholarId) {
                isModalOpen = true;
                currentModalScholarId = scholarId;
                originalOpenViewRenewalModal(scholarId);
            };
        }

        // Track modal close events
        const originalCloseApplicationModal = window.closeApplicationModal;
        if (originalCloseApplicationModal) {
            window.closeApplicationModal = function() {
                isModalOpen = false;
                currentModalScholarId = null;
                originalCloseApplicationModal();
            };
        }

        const originalCloseViewRenewalModal = window.closeViewRenewalModal;
        if (originalCloseViewRenewalModal) {
            window.closeViewRenewalModal = function() {
                isModalOpen = false;
                currentModalScholarId = null;
                originalCloseViewRenewalModal();
            };
        }

        const originalCloseEditRenewalModal = window.closeEditRenewalModal;
        if (originalCloseEditRenewalModal) {
            window.closeEditRenewalModal = function() {
                isModalOpen = false;
                currentModalScholarId = null;
                originalCloseEditRenewalModal();
            };
        }
    }

    // Track view changes
    function trackViewChanges() {
        const originalShowTable = window.showTable;
        if (originalShowTable) {
            window.showTable = function() {
                currentView = 'tableView';
                originalShowTable();
            };
        }

        const originalShowList = window.showList;
        if (originalShowList) {
            window.showList = function() {
                currentView = 'listView';
                originalShowList();
            };
        }
    }

    // Initialize everything
    function initialize() {
        trackModalState();
        trackViewChanges();
        reattachTableViewEventListeners();
        reattachListViewEventListeners();
        
        // Add data-id attributes to table rows for easier access
        document.querySelectorAll('#tableView tbody tr').forEach((row, index) => {
            const scholarId = row.querySelector('td:nth-child(4) button')?.getAttribute('onclick')?.match(/\d+/);
            if (scholarId) {
                row.setAttribute('data-id', scholarId[0]);
            }
        });

        document.querySelectorAll('#listView tbody tr').forEach((row, index) => {
            const scholarId = row.querySelector('td:nth-child(5) button')?.getAttribute('onclick')?.match(/\d+/);
            if (scholarId) {
                row.setAttribute('data-id', scholarId[0]);
            }
        });
    }

    // Start initialization
    initialize();

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });

    console.log('Auto-refresh enabled - refreshing every 2 seconds');
});