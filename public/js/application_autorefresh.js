// application_autorefresh.js
// Auto-refresh functionality for Scholarship Management Application

class AutoRefreshManager {
    constructor() {
        this.refreshInterval = 30000; // 30 seconds
        this.isRefreshing = false;
        this.lastUpdateTime = Date.now();
        this.init();
    }

    init() {
        console.log('AutoRefreshManager initialized');
        
        // Start the auto-refresh interval
        this.startAutoRefresh();
        
        // Set up event listeners for user activity
        this.setupActivityListeners();
        
        // Initialize real-time updates for notifications
        this.setupNotificationUpdates();
    }

    startAutoRefresh() {
        setInterval(() => {
            if (!this.isRefreshing && this.shouldRefresh()) {
                this.refreshContent();
            }
        }, this.refreshInterval);
    }

    shouldRefresh() {
        // Don't refresh if user is actively interacting with the page
        const timeSinceLastActivity = Date.now() - this.lastUpdateTime;
        return timeSinceLastActivity > 5000; // 5 seconds of inactivity
    }

    setupActivityListeners() {
        // Update last activity time on user interaction
        const events = ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'];
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.lastUpdateTime = Date.now();
            });
        });
    }

    setupNotificationUpdates() {
        // Refresh notifications more frequently
        setInterval(() => {
            this.refreshNotifications();
        }, 15000); // 15 seconds for notifications
    }

    async refreshContent() {
        if (this.isRefreshing) return;

        this.isRefreshing = true;
        
        try {
            await Promise.all([
                this.refreshTableView(),
                this.refreshListView(),
                this.refreshNotifications()
            ]);
            
            console.log('Content refreshed successfully');
        } catch (error) {
            console.error('Error refreshing content:', error);
        } finally {
            this.isRefreshing = false;
        }
    }

    async refreshTableView() {
        const tableView = document.getElementById('tableView');
        if (!tableView || tableView.classList.contains('hidden')) return;

        try {
            // CHANGE THIS LINE - Use Laravel route
            const response = await fetch('/mayor_staff/refresh-table-view', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateTableView(data);
            }
        } catch (error) {
            console.error('Error refreshing table view:', error);
        }
    }

    async refreshListView() {
        const listView = document.getElementById('listView');
        if (!listView || listView.classList.contains('hidden')) return;

        try {
            // CHANGE THIS LINE - Use Laravel route
            const response = await fetch('/mayor_staff/refresh-list-view', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateListView(data);
            }
        } catch (error) {
            console.error('Error refreshing list view:', error);
        }
    }

    async refreshNotifications() {
        try {
            // CHANGE THIS LINE - Use Laravel route
            const response = await fetch('/mayor_staff/refresh-notifications', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateNotifications(data);
            }
        } catch (error) {
            console.error('Error refreshing notifications:', error);
        }
    }

    updateTableView(data) {
        if (!data || !data.html) return;

        // Store current scroll position
        const scrollPosition = window.scrollY;
        
        // Create temporary container to parse new HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = data.html;
        
        // Get the new table body
        const newTbody = tempDiv.querySelector('#tableView tbody');
        const currentTbody = document.querySelector('#tableView tbody');
        
        if (newTbody && currentTbody) {
            // Smooth transition for table update
            currentTbody.style.opacity = '0.7';
            currentTbody.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                currentTbody.innerHTML = newTbody.innerHTML;
                currentTbody.style.opacity = '1';
                
                // Update pagination if exists
                this.updatePagination('tablePagination', data.pagination);
                
                // Reformat dates
                this.reformatDates();
                
                // Restore scroll position
                window.scrollTo(0, scrollPosition);
            }, 300);
        }
    }

    updateListView(data) {
        if (!data || !data.html) return;

        const scrollPosition = window.scrollY;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = data.html;
        
        const newTbody = tempDiv.querySelector('#listView tbody');
        const currentTbody = document.querySelector('#listView tbody');
        
        if (newTbody && currentTbody) {
            currentTbody.style.opacity = '0.7';
            currentTbody.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                currentTbody.innerHTML = newTbody.innerHTML;
                currentTbody.style.opacity = '1';
                
                // Update pagination if exists
                this.updatePagination('listPagination', data.pagination);
                
                // Reformat dates
                this.reformatDates();
                
                // Update badge statuses
                this.updateDocumentBadges(data.updatedDocuments);
                
                window.scrollTo(0, scrollPosition);
            }, 300);
        }
    }

    updateNotifications(data) {
        if (!data || !data.html) return;

        const notifDropdown = document.getElementById('notifDropdown');
        const notifCount = document.getElementById('notifCount');
        const notifBell = document.getElementById('notifBell');
        
        if (data.html) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;
            
            const newNotifList = tempDiv.querySelector('ul');
            const currentNotifList = document.querySelector('#notifDropdown ul');
            
            if (newNotifList && currentNotifList) {
                currentNotifList.innerHTML = newNotifList.innerHTML;
            }
        }
        
        // Update notification count badge
        if (notifCount && data.count !== undefined) {
            if (data.count > 0) {
                notifCount.textContent = data.count;
                notifCount.classList.remove('hidden');
                
                // Add subtle pulse animation for new notifications
                if (data.newNotifications) {
                    this.animateNewNotification(notifBell);
                }
            } else {
                notifCount.classList.add('hidden');
            }
        }
    }

    updatePagination(paginationId, paginationData) {
        const paginationContainer = document.getElementById(paginationId);
        if (paginationContainer && paginationData) {
            paginationContainer.innerHTML = paginationData;
        }
    }

    updateDocumentBadges(updatedDocuments) {
        if (!updatedDocuments) return;

        updatedDocuments.forEach(docId => {
            const badge = document.getElementById(`updatedBadge-${docId}`);
            const reviewBtn = document.getElementById(`reviewBtn-${docId}`);
            
            if (badge) {
                badge.classList.remove('hidden');
                
                // Add pulse animation
                badge.style.animation = 'pulse 2s infinite';
                
                // Remove animation after 6 seconds
                setTimeout(() => {
                    badge.style.animation = '';
                }, 6000);
            }
            
            if (reviewBtn) {
                reviewBtn.classList.add('ring-2', 'ring-purple-500');
                
                // Remove highlight after 5 seconds
                setTimeout(() => {
                    reviewBtn.classList.remove('ring-2', 'ring-purple-500');
                }, 5000);
            }
        });
    }

    animateNewNotification(element) {
        if (!element) return;
        
        element.classList.add('animate-pulse');
        setTimeout(() => {
            element.classList.remove('animate-pulse');
        }, 3000);
    }

    reformatDates() {
        // Reformat all dates after content update
        document.querySelectorAll('.date-format').forEach(function(element) {
            const rawDate = element.textContent.trim();
            if (rawDate) {
                const formattedDate = moment(rawDate).format('MMMM D, YYYY');
                element.textContent = formattedDate;
            }
        });
    }

    // Manual refresh trigger
    triggerManualRefresh() {
        this.refreshContent();
    }

    // Update refresh interval
    setRefreshInterval(interval) {
        this.refreshInterval = interval;
    }
}

// Initialize auto-refresh when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.autoRefreshManager = new AutoRefreshManager();
    
    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .refreshing {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .content-update {
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);
});

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AutoRefreshManager;
}