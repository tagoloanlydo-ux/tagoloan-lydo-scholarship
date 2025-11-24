document.addEventListener('DOMContentLoaded', function() {
    let refreshInterval;
    let isRefreshing = false;
    let currentView = 'table'; // 'table' or 'list'

    // Initialize auto-refresh
    function initAutoRefresh() {
        // Start refresh interval (5 seconds)
        refreshInterval = setInterval(refreshData, 5000);
        
        // Detect view changes to adjust refresh logic
        detectViewChanges();
        
        console.log('Auto-refresh initialized - refreshing every 5 seconds');
    }

    // Detect when user switches between table and list views
    function detectViewChanges() {
        const tableView = document.getElementById('tableView');
        const listView = document.getElementById('listView');
        
        // Use MutationObserver to detect visibility changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (!tableView.classList.contains('hidden')) {
                        currentView = 'table';
                    } else if (!listView.classList.contains('hidden')) {
                        currentView = 'list';
                    }
                }
            });
        });

        if (tableView) {
            observer.observe(tableView, { attributes: true });
        }
        if (listView) {
            observer.observe(listView, { attributes: true });
        }
    }

    // Main refresh function
    async function refreshData() {
        if (isRefreshing) return;
        
        isRefreshing = true;
        
        try {
            // Get current pagination state
            const currentPage = getCurrentPage();
            const searchTerm = getCurrentSearchTerm();
            const barangayFilter = getCurrentBarangayFilter();
            
            // Perform refresh based on current view
            if (currentView === 'table') {
                await refreshTableView(currentPage, searchTerm, barangayFilter);
            } else {
                await refreshListView(currentPage, searchTerm, barangayFilter);
            }
            
            // Update badge counts if they exist
            updateBadgeCounts();
            
        } catch (error) {
            console.error('Refresh error:', error);
        } finally {
            isRefreshing = false;
        }
    }

    // Refresh table view data
    async function refreshTableView(page = 1, searchTerm = '', barangayFilter = '') {
        // Create a subtle loading indicator (barely noticeable)
        showSubtleLoading('table');
        
        // Simulate API call - in real implementation, this would be an actual fetch
        await simulateDataFetch();
        
        // In a real implementation, you would fetch new data from your server
        // For now, we'll just update the content subtly
        updateTableContent();
        
        hideSubtleLoading('table');
    }

    // Refresh list view data
    async function refreshListView(page = 1, searchTerm = '', barangayFilter = '') {
        showSubtleLoading('list');
        
        await simulateDataFetch();
        updateListContent();
        
        hideSubtleLoading('list');
    }

    // Get current page number
    function getCurrentPage() {
        const activePagination = document.querySelector('.pagination-container .active');
        return activePagination ? parseInt(activePagination.textContent) : 1;
    }

    // Get current search term
    function getCurrentSearchTerm() {
        if (currentView === 'table') {
            return document.getElementById('nameSearch')?.value || '';
        } else {
            return document.getElementById('listNameSearch')?.value || '';
        }
    }

    // Get current barangay filter
    function getCurrentBarangayFilter() {
        if (currentView === 'table') {
            return document.getElementById('barangayFilter')?.value || '';
        } else {
            return document.getElementById('listBarangayFilter')?.value || '';
        }
    }

    // Show very subtle loading indication
    function showSubtleLoading(viewType) {
        const container = viewType === 'table' ? 
            document.getElementById('tableView') : 
            document.getElementById('listView');
        
        if (container) {
            container.style.opacity = '0.99'; // Almost imperceptible change
        }
    }

    // Hide subtle loading
    function hideSubtleLoading(viewType) {
        const container = viewType === 'table' ? 
            document.getElementById('tableView') : 
            document.getElementById('listView');
        
        if (container) {
            container.style.opacity = '1';
        }
    }

    // Simulate data fetch (replace with actual API call)
    function simulateDataFetch() {
        return new Promise(resolve => {
            setTimeout(resolve, 100); // Quick simulated delay
        });
    }

    // Update table content (this would be replaced with actual data update logic)
    function updateTableContent() {
        // Check if any new renewals have been added
        const currentRows = document.querySelectorAll('#tableView tbody tr');
        
        // In a real implementation, you would:
        // 1. Fetch new data from server
        // 2. Compare with current data
        // 3. Only update if there are changes
        // 4. Preserve pagination state
        
        // For now, we'll just do a very subtle update that doesn't disrupt user
        updateRowHighlights('table');
    }

    // Update list content
    function updateListContent() {
        updateRowHighlights('list');
    }

    // Update row highlights to indicate fresh data
    function updateRowHighlights(viewType) {
        const rows = document.querySelectorAll(
            viewType === 'table' ? 
            '#tableView tbody tr' : 
            '#listView tbody tr'
        );

        rows.forEach(row => {
            // Add a very subtle flash effect
            row.style.transition = 'background-color 0.3s ease';
            row.style.backgroundColor = 'rgba(124, 58, 237, 0.02)';
            
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 300);
        });
    }

    // Update notification badge counts
    function updateBadgeCounts() {
        const renewalsBadge = document.getElementById('pendingRenewalsBadge');
        const notifCount = document.getElementById('notifCount');
        
        // These would be updated with actual counts from your data
        // For now, we'll just maintain the current values
        console.log('Refreshed badge counts');
    }

    // Pause refresh when user is interacting with the page
    function pauseRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            console.log('Auto-refresh paused');
        }
    }

    // Resume refresh
    function resumeRefresh() {
        if (!refreshInterval) {
            refreshInterval = setInterval(refreshData, 5000);
            console.log('Auto-refresh resumed');
        }
    }

    // Detect user activity to pause/resume refresh
    function setupUserActivityDetection() {
        let activityTimer;
        
        function userActivity() {
            pauseRefresh();
            
            clearTimeout(activityTimer);
            activityTimer = setTimeout(() => {
                resumeRefresh();
            }, 30000); // Resume after 30 seconds of inactivity
        }
        
        // Add event listeners for user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, userActivity, { passive: true });
        });
    }

    // Stop auto-refresh
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
            console.log('Auto-refresh stopped');
        }
    }

    // Initialize when page loads
    initAutoRefresh();
    setupUserActivityDetection();

    // Export functions for global access if needed
    window.autoRefresh = {
        pause: pauseRefresh,
        resume: resumeRefresh,
        stop: stopAutoRefresh,
        refresh: refreshData
    };

    // Clean up when page is hidden (tab switch)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            pauseRefresh();
        } else {
            resumeRefresh();
        }
    });

    // Clean up before page unload
    window.addEventListener('beforeunload', function() {
        stopAutoRefresh();
    });
});