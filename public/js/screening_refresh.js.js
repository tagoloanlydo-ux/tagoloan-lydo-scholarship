document.addEventListener('DOMContentLoaded', function() {
    let refreshInterval;
    let isTableViewActive = true;
    let currentTableViewPage = 1;
    let currentListViewPage = 1;
    
    // Initialize auto-refresh
    initializeAutoRefresh();
    
    // Track which view is active
    function trackActiveView() {
        const tableView = document.getElementById('tableView');
        const listView = document.getElementById('listView');
        
        if (tableView && !tableView.classList.contains('hidden')) {
            isTableViewActive = true;
        } else if (listView && !listView.classList.contains('hidden')) {
            isTableViewActive = false;
        }
    }
    
    // Store current pagination state
    function storePaginationState() {
        // Store table view pagination
        const tablePagination = document.querySelector('#tablePagination .active');
        if (tablePagination) {
            currentTableViewPage = parseInt(tablePagination.textContent) || 1;
        }
        
        // Store list view pagination
        const listPagination = document.querySelector('#listPagination .active');
        if (listPagination) {
            currentListViewPage = parseInt(listPagination.textContent) || 1;
        }
    }
    
    // Restore pagination state after refresh
    function restorePaginationState() {
        // Restore table view pagination
        if (currentTableViewPage > 1) {
            const tablePageLinks = document.querySelectorAll('#tablePagination a');
            tablePageLinks.forEach(link => {
                if (parseInt(link.textContent) === currentTableViewPage) {
                    link.click();
                }
            });
        }
        
        // Restore list view pagination
        if (currentListViewPage > 1) {
            const listPageLinks = document.querySelectorAll('#listPagination a');
            listPageLinks.forEach(link => {
                if (parseInt(link.textContent) === currentListViewPage) {
                    link.click();
                }
            });
        }
    }
    
    // Perform the refresh
    function performRefresh() {
        // Store current state before refresh
        storePaginationState();
        trackActiveView();
        
        // Create a subtle loading indicator (optional)
        showSubtleLoading();
        
        // Use AJAX to refresh the content
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.text())
        .then(html => {
            // Create a temporary DOM parser to extract the table content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Update table view content if it exists
            const newTableView = doc.getElementById('tableView');
            if (newTableView) {
                const currentTableView = document.getElementById('tableView');
                if (currentTableView) {
                    currentTableView.innerHTML = newTableView.innerHTML;
                }
            }
            
            // Update list view content if it exists
            const newListView = doc.getElementById('listView');
            if (newListView) {
                const currentListView = document.getElementById('listView');
                if (currentListView) {
                    currentListView.innerHTML = newListView.innerHTML;
                }
            }
            
            // Reattach event listeners to the new elements
            reattachEventListeners();
            
            // Restore pagination state
            restorePaginationState();
            
            // Hide loading indicator
            hideSubtleLoading();
        })
        .catch(error => {
            console.error('Refresh error:', error);
            hideSubtleLoading();
        });
    }
    
    // Show a subtle loading indicator
    function showSubtleLoading() {
        // Remove any existing loading indicator
        hideSubtleLoading();
        
        // Create a small, unobtrusive loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.id = 'subtle-refresh-indicator';
        loadingIndicator.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #7e22ce;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 1000;
            display: none;
        `;
        
        // Add CSS for the spin animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
        
        document.body.appendChild(loadingIndicator);
        
        // Show the indicator briefly
        setTimeout(() => {
            loadingIndicator.style.display = 'block';
        }, 100);
    }
    
    // Hide the loading indicator
    function hideSubtleLoading() {
        const indicator = document.getElementById('subtle-refresh-indicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }
    
    // Reattach event listeners after DOM updates
    function reattachEventListeners() {
        // Reattach tab switching functionality
        const tabScreening = document.getElementById('tab-screening');
        const tabReviewed = document.getElementById('tab-reviewed');
        
        if (tabScreening) {
            tabScreening.onclick = showTable;
        }
        
        if (tabReviewed) {
            tabReviewed.onclick = showList;
        }
        
        // Reattach modal openers
        const editButtons = document.querySelectorAll('button[onclick*="openEditRemarksModal"]');
        editButtons.forEach(button => {
            button.onclick = function() {
                openEditRemarksModal(this);
            };
        });
        
        const reviewButtons = document.querySelectorAll('button[onclick*="openReviewModal"]');
        reviewButtons.forEach(button => {
            button.onclick = function() {
                openReviewModal(this);
            };
        });
        
        // Reattach search and filter functionality
        const nameSearch = document.getElementById('nameSearch');
        const barangayFilter = document.getElementById('barangayFilter');
        const listNameSearch = document.getElementById('listNameSearch');
        const listBarangayFilter = document.getElementById('listBarangayFilter');
        
        if (nameSearch) {
            nameSearch.oninput = filterTableView;
        }
        
        if (barangayFilter) {
            barangayFilter.onchange = filterTableView;
        }
        
        if (listNameSearch) {
            listNameSearch.oninput = filterListView;
        }
        
        if (listBarangayFilter) {
            listBarangayFilter.onchange = filterListView;
        }
    }
    
    // Initialize auto-refresh functionality
    function initializeAutoRefresh() {
        // Start the refresh interval (5000ms = 5 seconds)
        refreshInterval = setInterval(performRefresh, 5000);
        
        // Pause auto-refresh when user is interacting with modals or forms
        document.addEventListener('focus', pauseAutoRefresh, true);
        document.addEventListener('mouseenter', pauseAutoRefresh, true);
        
        // Resume auto-refresh when user stops interacting
        document.addEventListener('blur', resumeAutoRefresh, true);
        document.addEventListener('mouseleave', resumeAutoRefresh, true);
    }
    
    // Pause auto-refresh
    function pauseAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    }
    
    // Resume auto-refresh
    function resumeAutoRefresh() {
        if (!refreshInterval) {
            refreshInterval = setInterval(performRefresh, 5000);
        }
    }
    
    // Simple filtering functions (you might need to adjust these based on your actual implementation)
    function filterTableView() {
        // Your existing table view filtering logic
        console.log('Filtering table view...');
    }
    
    function filterListView() {
        // Your existing list view filtering logic
        console.log('Filtering list view...');
    }
    
    // Override the existing showTable and showList functions to track active view
    const originalShowTable = window.showTable;
    const originalShowList = window.showList;
    
    window.showTable = function() {
        isTableViewActive = true;
        if (originalShowTable) originalShowTable();
    };
    
    window.showList = function() {
        isTableViewActive = false;
        if (originalShowList) originalShowList();
    };
});