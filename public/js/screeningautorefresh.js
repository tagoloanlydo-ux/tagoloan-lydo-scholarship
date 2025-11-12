// screeningautorefresh.js
// Stealth auto-refresh functionality for screening tables

class ScreeningAutoRefresh {
    constructor() {
        this.isAutoRefreshEnabled = false;
        this.refreshInterval = null;
        this.refreshTime = 3000; // 3 seconds
        this.isRefreshing = false;
        this.lastTableHash = '';
        this.lastListHash = '';
        this.currentActiveTab = 'table'; // 'table' or 'list'
    }

    // Initialize auto-refresh
    init() {
        console.log('Initializing stealth auto-refresh for screening...');
        
        // Detect current active tab
        this.detectActiveTab();
        
        // Calculate initial hashes
        this.lastTableHash = this.calculateTableHash('tableView');
        this.lastListHash = this.calculateTableHash('listView');
        
        // Start auto-refresh
        this.startAutoRefresh();
        
        // Add event listeners
        this.addTabSwitchListeners();
        this.addPauseListeners();
        
        // Monitor for new applicants
        this.monitorNewApplicants();
    }

    // Detect which tab is currently active
    detectActiveTab() {
        const tableView = document.getElementById('tableView');
        const listView = document.getElementById('listView');
        
        if (tableView && !tableView.classList.contains('hidden')) {
            this.currentActiveTab = 'table';
        } else if (listView && !listView.classList.contains('hidden')) {
            this.currentActiveTab = 'list';
        }
    }

    // Add listeners for tab switching
    addTabSwitchListeners() {
        const tabScreening = document.getElementById('tab-screening');
        const tabReviewed = document.getElementById('tab-reviewed');
        
        if (tabScreening) {
            tabScreening.addEventListener('click', () => {
                setTimeout(() => {
                    this.currentActiveTab = 'table';
                    this.refreshTables(); // Refresh immediately on tab switch
                }, 100);
            });
        }
        
        if (tabReviewed) {
            tabReviewed.addEventListener('click', () => {
                setTimeout(() => {
                    this.currentActiveTab = 'list';
                    this.refreshTables(); // Refresh immediately on tab switch
                }, 100);
            });
        }
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

        console.log('Screening auto-refresh started (3 seconds interval)');
    }

    // Stop auto-refresh
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        this.isAutoRefreshEnabled = false;
        console.log('Screening auto-refresh stopped');
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
            // Get current filter values
            const nameSearch = document.getElementById('nameSearch')?.value || '';
            const listNameSearch = document.getElementById('listNameSearch')?.value || '';
            const barangayFilter = document.getElementById('barangayFilter')?.value || '';
            const listBarangayFilter = document.getElementById('listBarangayFilter')?.value || '';

            // Fetch updated data
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
                this.updateTableSection('tableView', newTableRows, nameSearch, barangayFilter);
                this.lastTableHash = newTableHash;
                tableUpdated = true;
                
                // Update pending count badge if needed
                this.updatePendingCount(newTableRows.length);
            }

            if (newListHash !== this.lastListHash) {
                this.updateTableSection('listView', newListRows, listNameSearch, listBarangayFilter);
                this.lastListHash = newListHash;
                listUpdated = true;
            }

            // Show very subtle notification only in console
            if (tableUpdated || listUpdated) {
                this.showStealthNotification(tableUpdated, listUpdated);
            }

        } catch (error) {
            console.error('Error refreshing screening tables:', error);
        } finally {
            this.isRefreshing = false;
        }
    }

    // Update pending count badge
    updatePendingCount(newCount) {
        const badge = document.getElementById('pendingScreeningBadge');
        if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            if (newCount !== currentCount) {
                badge.textContent = newCount;
                
                // Very subtle animation
                badge.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    badge.style.transform = 'scale(1)';
                }, 300);
            }
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
    calculateTableHash(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return '0';
        
        const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => 
            !row.querySelector('td[colspan]') && row.cells.length > 1
        );
        
        const rowData = rows.map(row => 
            Array.from(row.cells).map(cell => cell.textContent.trim()).join('|')
        ).join('||');
        
        return this.simpleHash(rowData);
    }

    // Extract table rows from HTML
    extractTableRows(doc, selector) {
        const rows = Array.from(doc.querySelectorAll(selector));
        return rows.filter(row => !row.querySelector('td[colspan]') && row.cells.length > 1)
                  .map(row => {
                      const cells = Array.from(row.cells).map(cell => cell.textContent.trim());
                      const buttons = Array.from(row.querySelectorAll('button')).map(btn => ({
                          html: btn.outerHTML,
                          onclick: btn.getAttribute('onclick'),
                          dataset: btn.dataset
                      }));
                      return { html: row.outerHTML, cells, buttons };
                  });
    }

    // Update table section
    updateTableSection(tableId, newRows, currentSearch, currentBarangayFilter) {
        console.log(`${tableId} updated silently`);
        
        // Get current scroll position to restore later
        const container = document.querySelector('.content-scrollable');
        const scrollTop = container ? container.scrollTop : 0;

        // Replace table body
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (tbody && newRows.length > 0) {
            tbody.innerHTML = newRows.map(row => row.html).join('');
            
            // Reattach event listeners to buttons
            this.reattachEventListeners(tableId);
            
            // Reapply filters
            this.reapplyFilters(tableId, currentSearch, currentBarangayFilter);
        } else if (tbody) {
            // No rows case
            const colCount = document.querySelector(`#${tableId} thead th`).parentElement.cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-4 border border-gray-200 text-gray-500">No applicants found.</td></tr>`;
        }

        // Restore scroll position
        if (container) {
            container.scrollTop = scrollTop;
        }
    }

    // Reattach event listeners to buttons
    reattachEventListeners(tableId) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (!tbody) return;

        // Reattach intake sheet button listeners
        tbody.querySelectorAll('button[data-id]').forEach(button => {
            const dataId = button.getAttribute('data-id');
            const dataRemarks = button.getAttribute('data-remarks');
            const dataName = button.getAttribute('data-name');
            const dataFname = button.getAttribute('data-fname');
            const dataMname = button.getAttribute('data-mname');
            const dataLname = button.getAttribute('data-lname');
            const dataSuffix = button.getAttribute('data-suffix');
            const dataBdate = button.getAttribute('data-bdate');
            const dataBrgy = button.getAttribute('data-brgy');
            const dataGender = button.getAttribute('data-gender');

            if (button.textContent.includes('Intake Sheet')) {
                button.onclick = () => openEditRemarksModal({
                    getAttribute: (attr) => {
                        const attributes = {
                            'data-id': dataId,
                            'data-remarks': dataRemarks,
                            'data-name': dataName,
                            'data-fname': dataFname,
                            'data-mname': dataMname,
                            'data-lname': dataLname,
                            'data-suffix': dataSuffix,
                            'data-bdate': dataBdate,
                            'data-brgy': dataBrgy,
                            'data-gender': dataGender
                        };
                        return attributes[attr];
                    }
                });
            }
        });

        // Reattach review button listeners for list view
        tbody.querySelectorAll('button[onclick*="openReviewModal"]').forEach(button => {
            const onclick = button.getAttribute('onclick');
            if (onclick && onclick.includes('openReviewModal')) {
                const matches = onclick.match(/openReviewModal\(this\)/);
                if (matches) {
                    button.onclick = () => openReviewModal(button);
                }
            }
        });
    }

    // Reapply filters after refresh
    reapplyFilters(tableId, searchValue, barangayValue) {
        let searchInput, barangaySelect;
        
        if (tableId === 'tableView') {
            searchInput = document.getElementById('nameSearch');
            barangaySelect = document.getElementById('barangayFilter');
        } else {
            searchInput = document.getElementById('listNameSearch');
            barangaySelect = document.getElementById('listBarangayFilter');
        }

        if (searchInput) searchInput.value = searchValue;
        if (barangaySelect) barangaySelect.value = barangayValue;

        // Trigger filter if values exist
        if (searchValue || barangayValue) {
            this.applyTableFilter(tableId, searchValue, barangayValue);
        }
    }

    // Apply table filter
    applyTableFilter(tableId, searchValue, barangayValue) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => 
            !row.querySelector('td[colspan]')
        );

        rows.forEach(row => {
            let showRow = true;
            const cells = Array.from(row.cells);
            const nameCell = cells[1]; // Name is in second column
            const barangayCell = cells[2]; // Barangay is in third column

            // Apply search filter
            if (searchValue && nameCell) {
                const nameText = nameCell.textContent.toLowerCase();
                if (!nameText.includes(searchValue.toLowerCase())) {
                    showRow = false;
                }
            }

            // Apply barangay filter
            if (barangayValue && barangayCell) {
                const barangayText = barangayCell.textContent.trim();
                if (barangayText !== barangayValue) {
                    showRow = false;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });

        // Show no results message if all rows are hidden
        const visibleRows = rows.filter(row => row.style.display !== 'none');
        const noResultsRow = tbody.querySelector('tr td[colspan]');
        
        if (visibleRows.length === 0 && !noResultsRow) {
            const colCount = document.querySelector(`#${tableId} thead th`).parentElement.cells.length;
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-4 border border-gray-200 text-gray-500">No applicants match your filters.</td></tr>`;
        } else if (visibleRows.length > 0 && noResultsRow) {
            // Remove no results message if there are visible rows
            noResultsRow.closest('tr').remove();
        }
    }

    // Show stealth notification (completely hidden from user)
    showStealthNotification(tableUpdated, listUpdated) {
        // Only log to console for debugging
        if (tableUpdated) {
            console.log('New pending applicants detected');
        }
        if (listUpdated) {
            console.log('Reviewed applicants list updated');
        }
    }

    // Monitor for new applicants (additional check)
    monitorNewApplicants() {
        // This can be extended to show very subtle indicators
        // Currently kept completely hidden
    }

    // Add pause listeners for user interaction
    addPauseListeners() {
        // Pause when modals are open
        const modals = ['editRemarksModal', 'reviewModal', 'signatureModal'];
        
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            if (modal.classList.contains('hidden')) {
                                // Modal closed, resume refresh after delay
                                setTimeout(() => {
                                    if (!this.isAutoRefreshEnabled) {
                                        this.startAutoRefresh();
                                    }
                                }, 2000);
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
        const searchInputs = ['nameSearch', 'listNameSearch', 'barangayFilter', 'listBarangayFilter'];
        
        searchInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                let typingTimer;
                
                input.addEventListener('focus', () => {
                    this.stopAutoRefresh();
                });
                
                input.addEventListener('input', () => {
                    // Keep refresh stopped while typing
                    clearTimeout(typingTimer);
                    this.stopAutoRefresh();
                    
                    typingTimer = setTimeout(() => {
                        // Resume after user stops typing for 2 seconds
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 2000);
                });
                
                input.addEventListener('blur', () => {
                    clearTimeout(typingTimer);
                    setTimeout(() => {
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 1000);
                });
            }
        });

        // Slow down refresh when user is interacting with tables
        const tables = [document.getElementById('tableView'), document.getElementById('listView')];
        
        tables.forEach(table => {
            if (table) {
                table.addEventListener('mouseenter', () => {
                    // Slow down to 10 seconds when user is viewing table
                    if (this.isAutoRefreshEnabled) {
                        this.stopAutoRefresh();
                        setTimeout(() => {
                            if (!this.isAutoRefreshEnabled) {
                                this.refreshTime = 10000;
                                this.startAutoRefresh();
                            }
                        }, 5000);
                    }
                });
                
                table.addEventListener('mouseleave', () => {
                    // Return to normal speed
                    setTimeout(() => {
                        this.refreshTime = 3000;
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 3000);
                });
            }
        });
    }
}

// Initialize auto-refresh when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the page to fully load
    setTimeout(() => {
        window.screeningAutoRefresh = new ScreeningAutoRefresh();
        window.screeningAutoRefresh.init();
        
        // Add completely hidden keyboard shortcut (Ctrl+Alt+S)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.altKey && e.key === 's') {
                e.preventDefault();
                window.screeningAutoRefresh.toggleAutoRefresh();
                console.log('Screening auto-refresh toggled');
            }
        });
    }, 2000);
});

// Manual refresh function (completely hidden)
function stealthScreeningRefresh() {
    if (window.screeningAutoRefresh) {
        window.screeningAutoRefresh.refreshTables();
    }
}

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ScreeningAutoRefresh;
}