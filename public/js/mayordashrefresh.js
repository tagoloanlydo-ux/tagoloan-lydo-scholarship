// mayordashrefresh.js - Stealth auto-refresh for Mayor Staff Dashboard
class MayorDashboardRefresh {
    constructor() {
        this.isAutoRefreshEnabled = false;
        this.refreshInterval = null;
        this.refreshTime = 5000; // 5 seconds
        this.isRefreshing = false;
        this.lastDashboardHash = '';
        this.lastPendingHash = '';
        this.lastDecisionsHash = '';
    }

    // Initialize auto-refresh
    init() {
        console.log('Initializing mayor dashboard auto-refresh...');
        
        // Calculate initial hashes
        this.lastDashboardHash = this.calculateDashboardHash();
        this.lastPendingHash = this.calculatePendingHash();
        this.lastDecisionsHash = this.calculateDecisionsHash();
        
        // Start auto-refresh
        this.startAutoRefresh();
        
        // Add event listeners to pause on user interaction
        this.addPauseListeners();
    }

    // Start auto-refresh
    startAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }

        this.isAutoRefreshEnabled = true;
        
        this.refreshInterval = setInterval(() => {
            if (!this.isRefreshing) {
                this.refreshDashboard();
            }
        }, this.refreshTime);

        console.log('Mayor dashboard auto-refresh started (5 seconds interval)');
    }

    // Stop auto-refresh
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        this.isAutoRefreshEnabled = false;
        console.log('Mayor dashboard auto-refresh stopped');
    }

    // Toggle auto-refresh
    toggleAutoRefresh() {
        if (this.isAutoRefreshEnabled) {
            this.stopAutoRefresh();
        } else {
            this.startAutoRefresh();
        }
    }

    // Refresh dashboard components
    async refreshDashboard() {
        if (this.isRefreshing) return;

        this.isRefreshing = true;
        
        try {
            // Get current filter states
            const currentSearch = document.getElementById('searchInput')?.value || '';
            const currentStatusFilter = document.getElementById('statusFilter')?.value || '';
            const currentSchoolFilter = document.getElementById('schoolFilter')?.value || '';
            const currentAcademicYearFilter = document.getElementById('academicYearFilter')?.value || '';

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

            // Extract dashboard data
            const newDashboardData = this.extractDashboardData(doc);
            const newPendingData = this.extractPendingData(doc);
            const newDecisionsData = this.extractDecisionsData(doc);

            // Check if data actually changed using hash comparison
            const newDashboardHash = this.calculateDataHash(newDashboardData);
            const newPendingHash = this.calculateDataHash(newPendingData);
            const newDecisionsHash = this.calculateDataHash(newDecisionsData);

            let dashboardUpdated = false;
            let pendingUpdated = false;
            let decisionsUpdated = false;

            // Update components only if data changed
            if (newDashboardHash !== this.lastDashboardHash) {
                this.updateDashboardCards(newDashboardData);
                this.lastDashboardHash = newDashboardHash;
                dashboardUpdated = true;
            }

            if (newPendingHash !== this.lastPendingHash) {
                this.updatePendingApplications(newPendingData);
                this.lastPendingHash = newPendingHash;
                pendingUpdated = true;
            }

            if (newDecisionsHash !== this.lastDecisionsHash) {
                this.updateRecentDecisions(newDecisionsData, currentSearch, currentStatusFilter, currentSchoolFilter, currentAcademicYearFilter);
                this.lastDecisionsHash = newDecisionsHash;
                decisionsUpdated = true;
            }

            // Show subtle notification only if data changed
            if (dashboardUpdated || pendingUpdated || decisionsUpdated) {
                this.showSubtleNotification(dashboardUpdated, pendingUpdated, decisionsUpdated);
            }

        } catch (error) {
            console.error('Error refreshing dashboard:', error);
        } finally {
            this.isRefreshing = false;
        }
    }

    // Extract dashboard card data
    extractDashboardData(doc) {
        const cards = doc.querySelectorAll('.grid.grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-3.gap-6.mb-6 a');
        const cardData = [];
        
        cards.forEach(card => {
            const title = card.querySelector('.text-sm')?.textContent?.trim() || '';
            const value = card.querySelector('.text-3xl')?.textContent?.trim() || '';
            const description = card.querySelector('.text-sm:last-child')?.textContent?.trim() || '';
            cardData.push({ title, value, description });
        });

        return cardData;
    }

    // Extract pending applications data
    extractPendingData(doc) {
        const pendingContainer = doc.getElementById('pendingApplicationsList');
        if (!pendingContainer) return [];
        
        const pendingItems = pendingContainer.querySelectorAll('div[data-id]');
        const pendingData = [];
        
        pendingItems.forEach(item => {
            const id = item.getAttribute('data-id');
            const name = item.querySelector('h4')?.textContent?.trim() || '';
            const details = item.querySelector('.text-gray-600')?.textContent?.trim() || '';
            pendingData.push({ id, name, details });
        });

        return pendingData;
    }

    // Extract recent decisions data
    extractDecisionsData(doc) {
        const decisionsContainer = doc.getElementById('decisionsList');
        if (!decisionsContainer) return [];
        
        const decisionItems = decisionsContainer.querySelectorAll('.decision-item');
        const decisionsData = [];
        
        decisionItems.forEach(item => {
            const name = item.querySelector('.font-medium')?.textContent?.trim() || '';
            const details = item.querySelector('.text-sm')?.textContent?.trim() || '';
            const status = item.querySelector('span')?.textContent?.trim() || '';
            const statusClass = Array.from(item.querySelector('span')?.classList || [])
                .find(cls => cls.includes('bg-') && cls.includes('text-'));
            
            decisionsData.push({ 
                name, 
                details, 
                status,
                statusClass,
                dataName: item.getAttribute('data-name'),
                dataStatus: item.getAttribute('data-status'),
                dataSchool: item.getAttribute('data-school'),
                dataAcademicYear: item.getAttribute('data-academic-year')
            });
        });

        return decisionsData;
    }

    // Calculate hash for data
    calculateDataHash(data) {
        const dataString = JSON.stringify(data);
        return this.simpleHash(dataString);
    }

    // Simple hash function
    simpleHash(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return hash.toString();
    }

    // Calculate dashboard hash
    calculateDashboardHash() {
        const cards = document.querySelectorAll('.grid.grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-3.gap-6.mb-6 a');
        const cardData = [];
        
        cards.forEach(card => {
            const title = card.querySelector('.text-sm')?.textContent?.trim() || '';
            const value = card.querySelector('.text-3xl')?.textContent?.trim() || '';
            cardData.push({ title, value });
        });

        return this.calculateDataHash(cardData);
    }

    // Calculate pending applications hash
    calculatePendingHash() {
        const pendingContainer = document.getElementById('pendingApplicationsList');
        if (!pendingContainer) return '0';
        
        const pendingItems = pendingContainer.querySelectorAll('div[data-id]');
        const pendingData = [];
        
        pendingItems.forEach(item => {
            const id = item.getAttribute('data-id');
            const name = item.querySelector('h4')?.textContent?.trim() || '';
            pendingData.push({ id, name });
        });

        return this.calculateDataHash(pendingData);
    }

    // Calculate recent decisions hash
    calculateDecisionsHash() {
        const decisionsContainer = document.getElementById('decisionsList');
        if (!decisionsContainer) return '0';
        
        const decisionItems = decisionsContainer.querySelectorAll('.decision-item');
        const decisionsData = [];
        
        decisionItems.forEach(item => {
            const name = item.querySelector('.font-medium')?.textContent?.trim() || '';
            const status = item.querySelector('span')?.textContent?.trim() || '';
            decisionsData.push({ name, status });
        });

        return this.calculateDataHash(decisionsData);
    }

    // Update dashboard cards
    updateDashboardCards(newData) {
        console.log('Dashboard cards updated silently');
        
        const cards = document.querySelectorAll('.grid.grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-3.gap-6.mb-6 a');
        
        cards.forEach((card, index) => {
            if (newData[index]) {
                const valueElement = card.querySelector('.text-3xl');
                const descriptionElement = card.querySelector('.text-sm:last-child');
                
                if (valueElement && valueElement.textContent !== newData[index].value) {
                    valueElement.textContent = newData[index].value;
                }
                
                if (descriptionElement && descriptionElement.textContent !== newData[index].description) {
                    descriptionElement.textContent = newData[index].description;
                }
            }
        });
    }

    // Update pending applications
    updatePendingApplications(newData) {
        console.log('Pending applications updated silently');
        
        const pendingContainer = document.getElementById('pendingApplicationsList');
        if (!pendingContainer) return;
        
        // Clear existing content
        pendingContainer.innerHTML = '';
        
        // Add new pending applications
        newData.forEach(item => {
            const pendingItem = document.createElement('div');
            pendingItem.className = 'p-4 hover:bg-gray-50 transition text-sm';
            pendingItem.setAttribute('data-id', item.id);
            pendingItem.innerHTML = `
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="font-semibold text-base text-gray-800">${item.name}</h4>
                        <div class="text-gray-600 mt-1 text-sm">
                            ${item.details}
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full border bg-yellow-50 text-yellow-700 border-yellow-300">
                        Pending Review
                    </span>
                </div>
            `;
            pendingContainer.appendChild(pendingItem);
        });
        
        // Update pending count
        const pendingCount = document.getElementById('pendingCount');
        if (pendingCount) {
            pendingCount.textContent = `Showing ${newData.length} pending applications`;
        }
    }

    // Update recent decisions
    updateRecentDecisions(newData, currentSearch, currentStatusFilter, currentSchoolFilter, currentAcademicYearFilter) {
        console.log('Recent decisions updated silently');
        
        const decisionsContainer = document.getElementById('decisionsList');
        const noResults = document.getElementById('noResults');
        if (!decisionsContainer) return;
        
        // Clear existing content
        decisionsContainer.innerHTML = '';
        
        let visibleCount = 0;
        
        // Add new decisions
        newData.forEach(item => {
            const matchesSearch = !currentSearch || item.dataName.includes(currentSearch.toLowerCase());
            const matchesStatus = !currentStatusFilter || item.dataStatus === currentStatusFilter;
            const matchesSchool = !currentSchoolFilter || item.dataSchool === currentSchoolFilter;
            const matchesAcademicYear = !currentAcademicYearFilter || item.dataAcademicYear === currentAcademicYearFilter;
            
            if (matchesSearch && matchesStatus && matchesSchool && matchesAcademicYear) {
                const decisionItem = document.createElement('div');
                decisionItem.className = 'decision-item flex justify-between items-center border-b border-gray-200 pb-2';
                decisionItem.setAttribute('data-name', item.dataName);
                decisionItem.setAttribute('data-status', item.dataStatus);
                decisionItem.setAttribute('data-school', item.dataSchool);
                decisionItem.setAttribute('data-academic-year', item.dataAcademicYear);
                decisionItem.innerHTML = `
                    <div>
                        <p class="font-medium">${item.name}</p>
                        <p class="text-sm text-gray-500">${item.details}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium flex items-center gap-1 ${item.statusClass}">
                        ${item.status.includes('Approved') ? '<i class="fas fa-check text-xs"></i>' : 
                          item.status.includes('Rejected') ? '<i class="fas fa-times text-xs"></i>' : 
                          '<i class="fas fa-clock text-xs"></i>'}
                        ${item.status}
                    </span>
                `;
                decisionsContainer.appendChild(decisionItem);
                visibleCount++;
            }
        });
        
        // Show/hide no results message
        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
    }

    // Show subtle notification
    showSubtleNotification(dashboardUpdated, pendingUpdated, decisionsUpdated) {
        // Only show in console for debugging
        if (dashboardUpdated) {
            console.log('Dashboard statistics updated');
        }
        if (pendingUpdated) {
            console.log('New pending applications detected');
        }
        if (decisionsUpdated) {
            console.log('Recent decisions updated');
        }
    }

    // Add pause listeners for user interaction
    addPauseListeners() {
        // Pause when user is interacting with filters
        const filterElements = [
            'searchInput', 'statusFilter', 'schoolFilter', 'academicYearFilter', 
            'filterToggle', 'resetFilters'
        ];
        
        filterElements.forEach(elementId => {
            const element = document.getElementById(elementId);
            if (element) {
                element.addEventListener('focus', () => {
                    this.stopAutoRefresh();
                });
                
                element.addEventListener('blur', () => {
                    setTimeout(() => {
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 1000);
                });
            }
        });

        // Pause when mouse is over important sections
        const importantSections = [
            document.getElementById('decisionsList'),
            document.getElementById('pendingApplicationsList'),
            document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-3.gap-6.mb-6')
        ];
        
        importantSections.forEach(section => {
            if (section) {
                section.addEventListener('mouseenter', () => {
                    if (this.isAutoRefreshEnabled) {
                        this.stopAutoRefresh();
                        // Restart with longer interval when user leaves
                        setTimeout(() => {
                            if (!this.isAutoRefreshEnabled) {
                                this.startAutoRefresh();
                            }
                        }, 8000);
                    }
                });
            }
        });

        // Pause when page is not visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoRefresh();
            } else {
                if (!this.isAutoRefreshEnabled) {
                    setTimeout(() => this.startAutoRefresh(), 2000);
                }
            }
        });
    }
}

// Initialize auto-refresh when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait for the page to fully load
    setTimeout(() => {
        window.mayorDashboardRefresh = new MayorDashboardRefresh();
        window.mayorDashboardRefresh.init();
        
        // Add keyboard shortcut to toggle refresh (completely hidden feature)
        document.addEventListener('keydown', function(e) {
            // Ctrl+Alt+D to toggle dashboard refresh (completely hidden from user)
            if (e.ctrlKey && e.altKey && e.key === 'd') {
                e.preventDefault();
                window.mayorDashboardRefresh.toggleAutoRefresh();
                console.log('Mayor dashboard auto-refresh toggled');
            }
        });
    }, 3000);
});

// Manual refresh function (completely hidden)
function stealthDashboardRefresh() {
    if (window.mayorDashboardRefresh) {
        window.mayorDashboardRefresh.refreshDashboard();
    }
}

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MayorDashboardRefresh;
}
