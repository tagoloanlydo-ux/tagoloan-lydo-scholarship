// renewalrefresh.js
// Stealth auto-refresh functionality for renewal tables and modals

class RenewalAutoRefresh {
    constructor() {
        this.isAutoRefreshEnabled = false;
        this.refreshInterval = null;
        this.refreshTime = 2000; // 2 seconds
        this.isRefreshing = false;
        this.lastTableHash = '';
        this.lastListHash = '';
        this.currentActiveTab = 'table'; // 'table' or 'list'
        this.currentOpenModal = null;
        this.currentScholarId = null;
    }

    // Initialize auto-refresh
    init() {
        console.log('Initializing stealth auto-refresh for renewal...');
        
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
        this.addModalListeners();
        
        // Monitor for new renewals
        this.monitorNewRenewals();
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
        const tabRenewal = document.getElementById('tab-renewal');
        const tabReview = document.getElementById('tab-review');
        
        if (tabRenewal) {
            tabRenewal.addEventListener('click', () => {
                setTimeout(() => {
                    this.currentActiveTab = 'table';
                    this.refreshTables(); // Refresh immediately on tab switch
                }, 100);
            });
        }
        
        if (tabReview) {
            tabReview.addEventListener('click', () => {
                setTimeout(() => {
                    this.currentActiveTab = 'list';
                    this.refreshTables(); // Refresh immediately on tab switch
                }, 100);
            });
        }
    }

    // Add modal listeners
    addModalListeners() {
        const modals = ['openRenewalModal', 'viewRenewalModal', 'editRenewalModal', 'documentViewerModal'];
        
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                // Track when modals open/close
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            if (modal.classList.contains('hidden')) {
                                // Modal closed
                                if (this.currentOpenModal === modalId) {
                                    this.currentOpenModal = null;
                                    this.currentScholarId = null;
                                }
                            } else {
                                // Modal opened
                                this.currentOpenModal = modalId;
                                
                                // Extract scholar ID from modal content if possible
                                if (modalId === 'openRenewalModal' || modalId === 'viewRenewalModal') {
                                    this.extractScholarIdFromModal();
                                }
                            }
                        }
                    });
                });

                observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
            }
        });
    }

    // Extract scholar ID from modal content
    extractScholarIdFromModal() {
        // Try to get scholar ID from various sources
        const content = document.getElementById('applicationContent') || document.getElementById('viewRenewalContent');
        if (content) {
            const buttons = content.querySelectorAll('button[onclick*="rateDocument"]');
            if (buttons.length > 0) {
                const onclick = buttons[0].getAttribute('onclick');
                const match = onclick.match(/rateDocument\((\d+),/);
                if (match) {
                    this.currentScholarId = parseInt(match[1]);
                }
            }
        }
        
        // Also check global variable
        if (typeof selectedRenewalId !== 'undefined' && selectedRenewalId) {
            this.currentScholarId = selectedRenewalId;
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

        console.log('Renewal auto-refresh started (2 seconds interval)');
    }

    // Stop auto-refresh
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        this.isAutoRefreshEnabled = false;
        console.log('Renewal auto-refresh stopped');
    }

    // Toggle auto-refresh
    toggleAutoRefresh() {
        if (this.isAutoRefreshEnabled) {
            this.stopAutoRefresh();
        } else {
            this.startAutoRefresh();
        }
    }

    // Refresh both tables and modal content if open
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

            // Refresh modal content if open and data changed
            if ((tableUpdated || listUpdated) && this.currentOpenModal && this.currentScholarId) {
                this.refreshModalContent();
            }

            // Show very subtle notification only in console
            if (tableUpdated || listUpdated) {
                this.showStealthNotification(tableUpdated, listUpdated);
            }

        } catch (error) {
            console.error('Error refreshing renewal tables:', error);
        } finally {
            this.isRefreshing = false;
        }
    }

    // Refresh modal content
    async refreshModalContent() {
        if (!this.currentScholarId) return;

        try {
            let endpoint, contentElement, modalType;
            
            switch (this.currentOpenModal) {
                case 'openRenewalModal':
                    endpoint = `/lydo_staff/renewal/${this.currentScholarId}/modal`;
                    contentElement = 'applicationContent';
                    modalType = 'review';
                    break;
                case 'viewRenewalModal':
                    endpoint = `/lydo_staff/renewal/${this.currentScholarId}/view`;
                    contentElement = 'viewRenewalContent';
                    modalType = 'view';
                    break;
                default:
                    return;
            }

            const response = await fetch(endpoint, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            });

            if (response.ok) {
                const html = await response.text();
                const element = document.getElementById(contentElement);
                if (element) {
                    // Preserve scroll position
                    const scrollPos = element.scrollTop;
                    
                    element.innerHTML = html;
                    
                    // Reattach event listeners
                    this.reattachModalEventListeners(modalType);
                    
                    // Restore scroll position
                    element.scrollTop = scrollPos;
                    
                    console.log(`Renewal modal content refreshed for scholar ${this.currentScholarId}`);
                }
            }
        } catch (error) {
            console.error('Error refreshing modal content:', error);
        }
    }

    // Update pending count badge
    updatePendingCount(newCount) {
        const badge = document.getElementById('pendingRenewalsBadge');
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
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-4 border border-gray-200 text-gray-500">No renewals found for the current year.</td></tr>`;
        }

        // Restore scroll position
        if (container) {
            container.scrollTop = scrollTop;
        }
    }

    // Reattach event listeners to table buttons
    reattachEventListeners(tableId) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (!tbody) return;

        // Reattach review renewal button listeners for table view
        tbody.querySelectorAll('button[onclick*="openRenewalModal"]').forEach(button => {
            const onclick = button.getAttribute('onclick');
            if (onclick && onclick.includes('openRenewalModal')) {
                const matches = onclick.match(/openRenewalModal\((\d+)\)/);
                if (matches) {
                    const scholarId = parseInt(matches[1]);
                    button.onclick = () => openRenewalModal(scholarId);
                }
            }
        });

        // Reattach view renewal button listeners for list view
        tbody.querySelectorAll('button[onclick*="openViewRenewalModal"]').forEach(button => {
            const onclick = button.getAttribute('onclick');
            if (onclick && onclick.includes('openViewRenewalModal')) {
                const matches = onclick.match(/openViewRenewalModal\((\d+)\)/);
                if (matches) {
                    const scholarId = parseInt(matches[1]);
                    button.onclick = () => openViewRenewalModal(scholarId);
                }
            }
        });

        // Reattach edit renewal button listeners for list view
        tbody.querySelectorAll('button[onclick*="openEditRenewalModal"]').forEach(button => {
            const onclick = button.getAttribute('onclick');
            if (onclick && onclick.includes('openEditRenewalModal')) {
                const matches = onclick.match(/openEditRenewalModal\((\d+),?\s*'([^']*)'?\)/);
                if (matches) {
                    const scholarId = parseInt(matches[1]);
                    const status = matches[2] || 'Pending';
                    button.onclick = () => openEditRenewalModal(scholarId, status);
                }
            }
        });
    }

    // Reattach modal event listeners
    reattachModalEventListeners(modalType) {
        if (modalType === 'review') {
            // Reattach document rating buttons
            document.querySelectorAll('button[onclick*="rateDocument"]').forEach(button => {
                const onclick = button.getAttribute('onclick');
                if (onclick && onclick.includes('rateDocument')) {
                    const matches = onclick.match(/rateDocument\((\d+),\s*'([^']+)',\s*'([^']+)'\)/);
                    if (matches) {
                        const scholarId = parseInt(matches[1]);
                        const documentType = matches[2];
                        const rating = matches[3];
                        button.onclick = () => rateDocument(scholarId, documentType, rating);
                    }
                }
            });

            // Reattach document viewer buttons
            document.querySelectorAll('button[onclick*="openDocumentViewer"]').forEach(button => {
                const onclick = button.getAttribute('onclick');
                if (onclick && onclick.includes('openDocumentViewer')) {
                    const matches = onclick.match(/openDocumentViewer\((\d+),\s*'([^']+)',\s*'([^']+)'\)/);
                    if (matches) {
                        const scholarId = parseInt(matches[1]);
                        const documentType = matches[2];
                        const documentPath = matches[3];
                        button.onclick = () => openDocumentViewer(scholarId, documentType, documentPath);
                    }
                }
            });

            // Reattach action buttons if they exist
            const approveBtn = document.getElementById('approveBtn');
            const rejectBtn = document.getElementById('rejectBtn');
            const sendEmailBtn = document.getElementById('sendEmailBtn');

            if (approveBtn) {
                approveBtn.onclick = () => updateRenewalStatus(selectedRenewalId, 'Approved');
            }
            if (rejectBtn) {
                rejectBtn.onclick = () => updateRenewalStatus(selectedRenewalId, 'Rejected');
            }
            if (sendEmailBtn) {
                sendEmailBtn.onclick = () => sendEmailForBadDocuments();
            }
        }
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
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-4 border border-gray-200 text-gray-500">No renewals match your filters.</td></tr>`;
        } else if (visibleRows.length > 0 && noResultsRow) {
            // Remove no results message if there are visible rows
            noResultsRow.closest('tr').remove();
        }
    }

    // Show stealth notification (completely hidden from user)
    showStealthNotification(tableUpdated, listUpdated) {
        // Only log to console for debugging
        if (tableUpdated) {
            console.log('New pending renewals detected');
        }
        if (listUpdated) {
            console.log('Processed renewals list updated');
        }
    }

    // Monitor for new renewals (additional check)
    monitorNewRenewals() {
        // This can be extended to show very subtle indicators
        // Currently kept completely hidden
    }

    // Add pause listeners for user interaction
    addPauseListeners() {
        // Pause when modals are open
        const modals = ['openRenewalModal', 'viewRenewalModal', 'editRenewalModal', 'documentViewerModal'];
        
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
                                }, 1000);
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
                        // Resume after user stops typing for 1.5 seconds
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 1500);
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

        // Slow down refresh when user is interacting with tables
        const tables = [document.getElementById('tableView'), document.getElementById('listView')];
        
        tables.forEach(table => {
            if (table) {
                table.addEventListener('mouseenter', () => {
                    // Slow down to 5 seconds when user is viewing table
                    if (this.isAutoRefreshEnabled) {
                        this.stopAutoRefresh();
                        setTimeout(() => {
                            if (!this.isAutoRefreshEnabled) {
                                this.refreshTime = 5000;
                                this.startAutoRefresh();
                            }
                        }, 3000);
                    }
                });
                
                table.addEventListener('mouseleave', () => {
                    // Return to normal speed
                    setTimeout(() => {
                        this.refreshTime = 2000;
                        if (!this.isAutoRefreshEnabled) {
                            this.startAutoRefresh();
                        }
                    }, 2000);
                });
            }
        });
    }
}

// Initialize auto-refresh when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the page to fully load
    setTimeout(() => {
        window.renewalAutoRefresh = new RenewalAutoRefresh();
        window.renewalAutoRefresh.init();
        
        // Add completely hidden keyboard shortcut (Ctrl+Alt+R)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.altKey && e.key === 'r') {
                e.preventDefault();
                window.renewalAutoRefresh.toggleAutoRefresh();
                console.log('Renewal auto-refresh toggled');
            }
        });
    }, 2000);
});

// Manual refresh function (completely hidden)
function stealthRenewalRefresh() {
    if (window.renewalAutoRefresh) {
        window.renewalAutoRefresh.refreshTables();
    }
}

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RenewalAutoRefresh;
}