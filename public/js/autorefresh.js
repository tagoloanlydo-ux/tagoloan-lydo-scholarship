// Stealth auto-refresh functionality for application tables
class TableAutoRefresh {
    constructor() {
        this.isAutoRefreshEnabled = false;
        this.refreshInterval = null;
        this.refreshTime = 2000; // 2 seconds
        this.isRefreshing = false;
        this.lastTableHash = '';
        this.lastListHash = '';
    }

    // Initialize auto-refresh
    init() {
        console.log('Initializing stealth auto-refresh...');
        
        // Calculate initial hashes
        this.lastTableHash = this.calculateTableHash('table');
        this.lastListHash = this.calculateTableHash('list');
        
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
                this.refreshTables();
            }
        }, this.refreshTime);

        console.log('Stealth auto-refresh started (2 seconds interval)');
    }

    // Stop auto-refresh
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        this.isAutoRefreshEnabled = false;
        console.log('Stealth auto-refresh stopped');
    }

    // Refresh both tables
    async refreshTables() {
        if (this.isRefreshing) return;

        this.isRefreshing = true;
        
        try {
            // Get current states
            const currentTableView = !document.getElementById('tableView').classList.contains('hidden');
            const currentTablePage = paginationState?.table?.currentPage || 1;
            const currentListPage = paginationState?.list?.currentPage || 1;
            const currentSearchTable = document.getElementById('searchInputTable')?.value || '';
            const currentSearchList = document.getElementById('searchInputList')?.value || '';
            const currentBarangayTable = document.getElementById('barangaySelectTable')?.value || '';
            const currentBarangayList = document.getElementById('barangaySelectList')?.value || '';

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
                this.updateTableIfChanged('table', newTableRows, currentTableView, currentTablePage, currentSearchTable, currentBarangayTable);
                this.lastTableHash = newTableHash;
                tableUpdated = true;
            }

            if (newListHash !== this.lastListHash) {
                this.updateTableIfChanged('list', newListRows, !currentTableView, currentListPage, currentSearchList, currentBarangayList);
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
        const rows = paginationState[viewType]?.allRows || [];
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
    updateTableIfChanged(viewType, newRows, isActiveView, currentPage, currentSearch, currentBarangay) {
        console.log(`${viewType} table updated silently`);

        // Replace table body
        const tbody = document.querySelector(`#${viewType === 'table' ? 'tableView' : 'listView'} tbody`);
        if (tbody) {
            tbody.innerHTML = newRows.map(row => row.html).join('');
        }

        // Reinitialize pagination
        this.reinitializePagination(viewType, newRows);

        // Reapply filters
        this.reapplyFilters(viewType, currentSearch, currentBarangay, currentPage);

        // Restore current page if it's the active view
        if (isActiveView) {
            setTimeout(() => {
                const state = paginationState[viewType];
                state.currentPage = Math.min(currentPage, Math.ceil(state.filteredRows.length / state.rowsPerPage));
                updatePagination(viewType);
            }, 50);
        }

        // Reattach event listeners
        this.reattachEventListeners();
    }

    // Reinitialize pagination
    reinitializePagination(viewType, newRows) {
        const rows = Array.from(document.querySelectorAll(`#${viewType === 'table' ? 'tableView' : 'listView'} tbody tr`));
        paginationState[viewType].allRows = rows.filter(row => !row.querySelector('td[colspan]'));
        paginationState[viewType].filteredRows = [...paginationState[viewType].allRows];
        paginationState[viewType].currentPage = 1;
    }

    // Reapply filters
    reapplyFilters(viewType, searchValue, barangayValue, currentPage) {
        const searchInput = document.getElementById(viewType === 'table' ? 'searchInputTable' : 'searchInputList');
        const barangaySelect = document.getElementById(viewType === 'table' ? 'barangaySelectTable' : 'barangaySelectList');

        if (searchInput) searchInput.value = searchValue;
        if (barangaySelect) barangaySelect.value = barangayValue;

        // Re-filter
        if (viewType === 'table') {
            filterTable();
        } else {
            filterList();
        }

        // Restore page
        const state = paginationState[viewType];
        state.currentPage = Math.min(currentPage, Math.ceil(state.filteredRows.length / state.rowsPerPage));
        updatePagination(viewType);
    }

    // Reattach event listeners
    reattachEventListeners() {
        // Reattach modal open listeners
        document.querySelectorAll('button[onclick*="openApplicationModal"]').forEach(button => {
            const onclick = button.getAttribute('onclick');
            if (onclick && onclick.includes('openApplicationModal')) {
                const matches = onclick.match(/openApplicationModal\((\d+),?\s*'?(\w+)?'?\)/);
                if (matches) {
                    const id = matches[1];
                    const source = matches[2] || 'pending';
                    button.onclick = () => openApplicationModal(parseInt(id), source);
                }
            }
        });

        // Reattach delete listeners
        document.querySelectorAll('button[onclick*="confirmDeletePending"]').forEach(button => {
            const onclick = button.getAttribute('onclick');
            if (onclick && onclick.includes('confirmDeletePending')) {
                const matches = onclick.match(/confirmDeletePending\((\d+),?\s*'([^']+)'\)/);
                if (matches) {
                    const id = matches[1];
                    const name = matches[2];
                    button.onclick = () => confirmDeletePending(parseInt(id), name);
                }
            }
        });

        // Reattach other delete buttons
        document.querySelectorAll('button[onclick*="openDeleteModal"]').forEach(button => {
            const onclick = button.getAttribute('onclick');
            if (onclick && onclick.includes('openDeleteModal')) {
                const matches = onclick.match(/openDeleteModal\((\d+),?\s*'([^']+)',?\s*(true|false)?\)/);
                if (matches) {
                    const id = matches[1];
                    const name = matches[2];
                    const isReviewed = matches[3] === 'true';
                    button.onclick = () => openDeleteModal(parseInt(id), name, isReviewed);
                }
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

        // Pause when user is typing in search
        const searchInputs = ['searchInputTable', 'searchInputList'];
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
    }
}

// Initialize auto-refresh when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait for pagination to initialize
    setTimeout(() => {
        if (typeof paginationState !== 'undefined') {
            window.tableAutoRefresh = new TableAutoRefresh();
            window.tableAutoRefresh.init();
            
            // Add keyboard shortcut to toggle refresh (completely hidden feature)
            document.addEventListener('keydown', function(e) {
                // Ctrl+Alt+R to toggle refresh (completely hidden from user)
                if (e.ctrlKey && e.altKey && e.key === 'r') {
                    e.preventDefault();
                    window.tableAutoRefresh.toggleAutoRefresh();
                    console.log('Stealth auto-refresh toggled');
                }
            });
        }
    }, 1500);
});

// Manual refresh function (completely hidden)
function stealthRefresh() {
    if (window.tableAutoRefresh) {
        window.tableAutoRefresh.refreshTables();
    }
}

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TableAutoRefresh;
}