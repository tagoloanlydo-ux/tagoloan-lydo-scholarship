// Auto-refresh notification count without page reload
class NotificationRefresher {
    constructor() {
        this.lastCount = 0;
        this.isRefreshing = false;
        this.refreshInterval = 30000; // 30 seconds
        this.init();
    }

    init() {
        // Initial count
        this.updateNotificationCount();
        
        // Start auto-refresh
        this.startAutoRefresh();
        
        // Listen for visibility change to refresh when tab becomes visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.updateNotificationCount();
            }
        });
    }

    startAutoRefresh() {
        setInterval(() => {
            if (!document.hidden && !this.isRefreshing) {
                this.updateNotificationCount();
            }
        }, this.refreshInterval);
    }

    async updateNotificationCount() {
        if (this.isRefreshing) return;
        
        this.isRefreshing = true;
        
        try {
            const response = await fetch('/mayor_staff/notification-count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
            });

            if (response.ok) {
                const data = await response.json();
                this.handleNotificationUpdate(data);
            }
        } catch (error) {
            console.error('Error fetching notification count:', error);
        } finally {
            this.isRefreshing = false;
        }
    }

    handleNotificationUpdate(data) {
        const currentCount = data.count || 0;
        const notifCountElement = document.getElementById('notifCount');
        const notifBell = document.getElementById('notifBell');

        // Update badge count
        if (currentCount > 0) {
            if (notifCountElement) {
                notifCountElement.textContent = currentCount;
            } else {
                this.createBadge(notifBell, currentCount);
            }
        } else {
            if (notifCountElement) {
                notifCountElement.remove();
            }
        }

        // Play sound if new notifications arrived
        if (currentCount > this.lastCount && this.lastCount > 0) {
            this.playNotificationSound();
        }

        this.lastCount = currentCount;
    }

    createBadge(bellElement, count) {
        const badge = document.createElement('span');
        badge.id = 'notifCount';
        badge.className = 'absolute -top-1 -right-1 bg-red-500 text-white text-sm rounded-full h-5 w-5 flex items-center justify-center';
        badge.textContent = count;
        bellElement.appendChild(badge);
    }

    playNotificationSound() {
        const sound = document.getElementById('notificationSound');
        if (sound) {
            sound.currentTime = 0;
            sound.play().catch(e => console.log('Audio play failed:', e));
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new NotificationRefresher();
});