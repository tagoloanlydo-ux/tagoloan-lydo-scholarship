// Stealth auto-refresh functionality for dashboard cards
class DashboardAutoRefresh {
    constructor() {
        this.isAutoRefreshEnabled = false;
        this.refreshInterval = null;
        this.refreshTime = 5000; // 5 seconds for dashboard cards
        this.isRefreshing = false;
        this.lastCardValues = {};
        this.initialized = false;
    }

    // Initialize auto-refresh
    init() {
        console.log('Initializing dashboard cards stealth auto-refresh...');
        
        // Store initial card values
        this.storeCurrentCardValues();
        
        // Start auto-refresh
        this.startAutoRefresh();
        
        // Add event listeners to pause on user interaction
        this.addPauseListeners();
        
        this.initialized = true;
    }

    // Store current card values for comparison
    storeCurrentCardValues() {
        this.lastCardValues = {
            applicants: document.getElementById('applicantsCount')?.textContent || '0',
            pendingInitial: document.getElementById('pendingInitialCount')?.textContent || '0',
            approvedRenewals: document.getElementById('approvedRenewalsCount')?.textContent || '0',
            pendingRenewals: document.getElementById('pendingRenewalsCount')?.textContent || '0'
        };
    }

    // Start auto-refresh
    startAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }

        this.isAutoRefreshEnabled = true;
        
        this.refreshInterval = setInterval(() => {
            if (!this.isRefreshing) {
                this.refreshCards();
            }
        }, this.refreshTime);

        console.log('Dashboard cards stealth auto-refresh started (5 seconds interval)');
    }

    // Stop auto-refresh
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        this.isAutoRefreshEnabled = false;
        console.log('Dashboard cards stealth auto-refresh stopped');
    }

    // Toggle auto-refresh
    toggleAutoRefresh() {
        if (this.isAutoRefreshEnabled) {
            this.stopAutoRefresh();
        } else {
            this.startAutoRefresh();
        }
    }

    // Refresh dashboard cards
    async refreshCards() {
        if (this.isRefreshing) return;

        this.isRefreshing = true;
        
        try {
            // Fetch updated data silently
            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                },
                cache: 'no-cache'
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Extract card values from the fetched HTML
            const newCardValues = this.extractCardValues(doc);

            // Update cards only if values changed
            this.updateCardsIfChanged(newCardValues);

        } catch (error) {
            console.error('Error refreshing dashboard cards:', error);
        } finally {
            this.isRefreshing = false;
        }
    }

    // Extract card values from HTML
    extractCardValues(doc) {
        return {
            applicants: doc.getElementById('applicantsCount')?.textContent || '0',
            pendingInitial: doc.getElementById('pendingInitialCount')?.textContent || '0',
            approvedRenewals: doc.getElementById('approvedRenewalsCount')?.textContent || '0',
            pendingRenewals: doc.getElementById('pendingRenewalsCount')?.textContent || '0'
        };
    }

    // Update cards if values changed
    updateCardsIfChanged(newCardValues) {
        let anyCardUpdated = false;

        // Check each card and update if changed
        Object.keys(newCardValues).forEach(cardKey => {
            if (newCardValues[cardKey] !== this.lastCardValues[cardKey]) {
                this.updateCard(cardKey, newCardValues[cardKey]);
                this.lastCardValues[cardKey] = newCardValues[cardKey];
                anyCardUpdated = true;
            }
        });

        // Show subtle notification only if data changed
        if (anyCardUpdated) {
            this.showSubtleNotification();
        }
    }

    // Update individual card with smooth animation
    updateCard(cardKey, newValue) {
        const cardElement = document.getElementById(this.getCardElementId(cardKey));
        if (!cardElement) return;

        console.log(`Dashboard card ${cardKey} updated: ${this.lastCardValues[cardKey]} → ${newValue}`);

        // Add subtle animation
        cardElement.classList.add('card-updating');
        
        // Update value with slight delay for visual effect
        setTimeout(() => {
            cardElement.textContent = newValue;
            cardElement.classList.remove('card-updating');
            cardElement.classList.add('card-updated');
            
            // Remove updated class after animation
            setTimeout(() => {
                cardElement.classList.remove('card-updated');
            }, 1000);
        }, 300);
    }

    // Get card element ID based on card key
    getCardElementId(cardKey) {
        const cardIdMap = {
            applicants: 'applicantsCount',
            pendingInitial: 'pendingInitialCount',
            approvedRenewals: 'approvedRenewalsCount',
            pendingRenewals: 'pendingRenewalsCount'
        };
        
        return cardIdMap[cardKey] || '';
    }

    // Show subtle notification (very minimal)
    showSubtleNotification() {
        // Only show in console for debugging
        console.log('Dashboard cards updated with new data');
        
        // Optional: Very subtle visual cue (completely hidden from user)
        // You can remove this if you want absolutely no visual feedback
    }

    // Add pause listeners for user interaction
    addPauseListeners() {
        // Pause when user is interacting with modals
        const modals = ['applicationModal', 'deleteModal', 'rejectionModal', 'editInitialScreeningModal', 'documentModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                // Only pause if modal is visible
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            if (modal.classList.contains('hidden')) {
                                // Modal closed, resume refresh
                                if (!this.isAutoRefreshEnabled) {
                                    setTimeout(() => this.startAutoRefresh(), 1000);
                                }
                            } else {
                                // Modal opened, pause refresh
                                this.stopAutoRefresh();
                            }
                        }
                    });
                });

                observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
            }
        });

        // Pause when mouse is over cards (user might be reading)
        const cards = document.querySelectorAll('.bg-white.rounded-xl.shadow-md');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                // Slow down refresh when user is viewing cards
                if (this.isAutoRefreshEnabled) {
                    this.stopAutoRefresh();
                    // Restart with longer interval when user leaves
                    setTimeout(() => {
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 10000); // 10 seconds after user leaves
                }
            });
        });

        // Pause when page is not visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Page is hidden, pause refresh
                this.stopAutoRefresh();
            } else {
                // Page is visible, resume refresh
                if (!this.isAutoRefreshEnabled) {
                    setTimeout(() => this.startAutoRefresh(), 2000);
                }
            }
        });
    }
}

// Initialize auto-refresh when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the page to fully load
    setTimeout(() => {
        window.dashboardAutoRefresh = new DashboardAutoRefresh();
        window.dashboardAutoRefresh.init();
        
        // Add keyboard shortcut to toggle refresh (completely hidden feature)
        document.addEventListener('keydown', function(e) {
            // Ctrl+Alt+D to toggle dashboard refresh (completely hidden from user)
            if (e.ctrlKey && e.altKey && e.key === 'd') {
                e.preventDefault();
                if (window.dashboardAutoRefresh) {
                    window.dashboardAutoRefresh.toggleAutoRefresh();
                    console.log('Dashboard stealth auto-refresh toggled');
                }
            }
        });
    }, 2000);
});

// Manual refresh function (completely hidden)
function stealthDashboardRefresh() {
    if (window.dashboardAutoRefresh) {
        window.dashboardAutoRefresh.refreshCards();
    }
}

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardAutoRefresh;
}