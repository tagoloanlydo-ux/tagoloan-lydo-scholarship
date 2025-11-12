// statusrefresh.js - Stealth auto-refresh for scholarship status tables
class StatusAutoRefresh {
    constructor() {
        this.isAutoRefreshEnabled = false;
        this.refreshInterval = null;
        this.refreshTime = 3000; // 3 seconds
        this.isRefreshing = false;
        this.lastTableHash = '';
        this.lastListHash = '';
        this.currentView = 'table'; // 'table' or 'list'
    }

    // Initialize auto-refresh
    init() {
        console.log('Initializing status auto-refresh...');
        
        // Calculate initial hashes
        this.lastTableHash = this.calculateTableHash('table');
        this.lastListHash = this.calculateTableHash('list');
        
        // Start auto-refresh
        this.startAutoRefresh();
        
        // Add event listeners to pause on user interaction
        this.addPauseListeners();
        
        // Track current view
        this.trackCurrentView();
    }

    // Track which view is currently active
    trackCurrentView() {
        const tableView = document.getElementById('tableView');
        const listView = document.getElementById('listView');
        
        if (tableView && !tableView.classList.contains('hidden')) {
            this.currentView = 'table';
        } else if (listView && !listView.classList.contains('hidden')) {
            this.currentView = 'list';
        }
        
        // Observe view changes
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (tableView && !tableView.classList.contains('hidden')) {
                        this.currentView = 'table';
                    } else if (listView && !listView.classList.contains('hidden')) {
                        this.currentView = 'list';
                    }
                }
            });
        });
        
        if (tableView) observer.observe(tableView, { attributes: true });
        if (listView) observer.observe(listView, { attributes: true });
    }

    // Start auto-refresh
    startAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }

        this.isAutoRefreshEnabled = true;
        
        this.refreshInterval = setInterval(() => {
            if (!this.isRefreshing) {
                this.refreshTables();
            }
        }, this.refreshTime);

        console.log('Status auto-refresh started (3 seconds interval)');
    }

    // Stop auto-refresh
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        this.isAutoRefreshEnabled = false;
        console.log('Status auto-refresh stopped');
    }

    // Toggle auto-refresh
    toggleAutoRefresh() {
        if (this.isAutoRefreshEnabled) {
            this.stopAutoRefresh();
        } else {
            this.startAutoRefresh();
        }
    }

    // Refresh both tables
    async refreshTables() {
        if (this.isRefreshing) return;

        this.isRefreshing = true;
        
        try {
            // Get current states
            const currentTableView = !document.getElementById('tableView').classList.contains('hidden');
            const currentSearchTable = document.getElementById('searchInputTable')?.value || '';
            const currentSearchList = document.getElementById('listNameSearch')?.value || '';
            const currentBarangayTable = document.getElementById('barangaySelectTable')?.value || '';
            const currentBarangayList = document.getElementById('listBarangayFilter')?.value || '';
            const currentStatusFilter = document.getElementById('listStatusFilter')?.value || '';

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

            // Extract table data
            const newTableRows = this.extractTableRows(doc, '#tableView tbody tr');
            const newListRows = this.extractTableRows(doc, '#listView tbody tr');

            // Check if data actually changed using hash comparison
            const newTableHash = this.calculateRowsHash(newTableRows);
            const newListHash = this.calculateRowsHash(newListRows);

            let tableUpdated = false;
            let listUpdated = false;

            // Update tables only if data changed
            if (newTableHash !== this.lastTableHash) {
                this.updateTableIfChanged('table', newTableRows, currentTableView, currentSearchTable, currentBarangayTable);
                this.lastTableHash = newTableHash;
                tableUpdated = true;
            }

            if (newListHash !== this.lastListHash) {
                this.updateTableIfChanged('list', newListRows, !currentTableView, currentSearchList, currentBarangayList, currentStatusFilter);
                this.lastListHash = newListHash;
                listUpdated = true;
            }

            // Show subtle notification only if data changed
            if (tableUpdated || listUpdated) {
                this.showSubtleNotification(tableUpdated, listUpdated);
            }

        } catch (error) {
            console.error('Error refreshing tables:', error);
        } finally {
            this.isRefreshing = false;
        }
    }

    // Calculate hash for rows to detect changes
    calculateRowsHash(rows) {
        const rowData = rows.map(row => row.cells.join('|')).join('||');
        return this.simpleHash(rowData);
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

    // Calculate table hash
    calculateTableHash(viewType) {
        const tbody = document.querySelector(`#${viewType === 'table' ? 'tableView' : 'listView'} tbody`);
        if (!tbody) return '0';
        
        const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
        const rowData = rows.map(row => Array.from(row.cells).map(cell => cell.textContent.trim()).join('|')).join('||');
        return this.simpleHash(rowData);
    }

    // Extract table rows from HTML
    extractTableRows(doc, selector) {
        const rows = Array.from(doc.querySelectorAll(selector));
        return rows.filter(row => !row.querySelector('td[colspan]'))
                  .map(row => {
                      const cells = Array.from(row.cells).map(cell => cell.textContent.trim());
                      const buttons = Array.from(row.querySelectorAll('button')).map(btn => btn.outerHTML);
                      return { html: row.outerHTML, cells, buttons };
                  });
    }

    // Update table if data changed
    updateTableIfChanged(viewType, newRows, isActiveView, currentSearch, currentBarangay, currentStatus = '') {
        console.log(`${viewType} table updated silently`);

        // Replace table body
        const tbody = document.querySelector(`#${viewType === 'table' ? 'tableView' : 'listView'} tbody`);
        if (tbody) {
            tbody.innerHTML = newRows.map(row => row.html).join('');
        }

        // Reapply filters
        this.reapplyFilters(viewType, currentSearch, currentBarangay, currentStatus);

        // Reattach event listeners
        this.reattachEventListeners();
    }

    // Reapply filters
    reapplyFilters(viewType, searchValue, barangayValue, statusValue = '') {
        let searchInput, barangaySelect, statusSelect;

        if (viewType === 'table') {
            searchInput = document.getElementById('searchInputTable');
            barangaySelect = document.getElementById('barangaySelectTable');
        } else {
            searchInput = document.getElementById('listNameSearch');
            barangaySelect = document.getElementById('listBarangayFilter');
            statusSelect = document.getElementById('listStatusFilter');
        }

        if (searchInput) searchInput.value = searchValue;
        if (barangaySelect) barangaySelect.value = barangayValue;
        if (statusSelect) statusSelect.value = statusValue;

        // Trigger filter events if needed
        if (searchInput && searchValue) {
            const event = new Event('input', { bubbles: true });
            searchInput.dispatchEvent(event);
        }

        if (barangaySelect && barangayValue) {
            const event = new Event('change', { bubbles: true });
            barangaySelect.dispatchEvent(event);
        }

        if (statusSelect && statusValue) {
            const event = new Event('change', { bubbles: true });
            statusSelect.dispatchEvent(event);
        }
    }

    // Reattach event listeners
    reattachEventListeners() {
        // Reattach intake sheet modal listeners
        document.querySelectorAll('.view-intake-btn').forEach(button => {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            if (id && name) {
                button.onclick = () => {
                    // This would need to be connected to your existing modal opening function
                    console.log(`Opening intake sheet for ${name} (ID: ${id})`);
                    // You'll need to integrate this with your existing modal system
                };
            }
        });
    }

    // Show subtle notification (very minimal)
    showSubtleNotification(tableUpdated, listUpdated) {
        // Only show in console for debugging
        if (tableUpdated) {
            console.log('New pending applications detected');
        }
        if (listUpdated) {
            console.log('New reviewed applications detected');
        }
        
        // Optional: Very subtle visual cue (completely hidden from user)
        // You can remove this entire function if you want absolutely no visual feedback
    }

    // Add pause listeners for user interaction
    addPauseListeners() {
        // Pause when user is interacting with modals
        const modals = ['intakeSheetModal'];
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

        // Pause when user is typing in search
        const searchInputs = ['searchInputTable', 'listNameSearch'];
        searchInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                let typingTimer;
                
                input.addEventListener('focus', () => {
                    this.stopAutoRefresh();
                });
                
                input.addEventListener('input', () => {
                    // Restart timer on each input
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        // Resume after user stops typing for 1 second
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 1000);
                });
                
                input.addEventListener('blur', () => {
                    clearTimeout(typingTimer);
                    setTimeout(() => {
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 500);
                });
            }
        });

        // Pause when mouse is over tables (user might be reading)
        const tables = [document.getElementById('tableView'), document.getElementById('listView')];
        tables.forEach(table => {
            if (table) {
                table.addEventListener('mouseenter', () => {
                    // Don't stop completely, just slow down
                    if (this.isAutoRefreshEnabled) {
                        this.stopAutoRefresh();
                        // Restart with longer interval when user leaves
                        setTimeout(() => {
                            if (!this.isAutoRefreshEnabled) {
                                this.startAutoRefresh();
                            }
                        }, 5000);
                    }
                });
            }
        });

        // Pause when interacting with filter dropdowns
        const filterSelects = ['barangaySelectTable', 'listBarangayFilter', 'listStatusFilter'];
        filterSelects.forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                select.addEventListener('focus', () => {
                    this.stopAutoRefresh();
                });
                
                select.addEventListener('blur', () => {
                    setTimeout(() => {
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 500);
                });
            }
        });
    }
}

// Initialize auto-refresh when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the page to fully load
    setTimeout(() => {
        window.statusAutoRefresh = new StatusAutoRefresh();
        window.statusAutoRefresh.init();
        
        // Add keyboard shortcut to toggle refresh (completely hidden feature)
        document.addEventListener('keydown', function(e) {
            // Ctrl+Alt+R to toggle refresh (completely hidden from user)
            if (e.ctrlKey && e.altKey && e.key === 'r') {
                e.preventDefault();
                window.statusAutoRefresh.toggleAutoRefresh();
                console.log('Status auto-refresh toggled');
            }
        });
    }, 2000);
});

// Manual refresh function (completely hidden)
function stealthStatusRefresh() {
    if (window.statusAutoRefresh) {
        window.statusAutoRefresh.refreshTables();
    }
}

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StatusAutoRefresh;
}