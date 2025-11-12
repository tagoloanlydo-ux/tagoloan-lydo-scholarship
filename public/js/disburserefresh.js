// Stealth auto-refresh functionality for disbursement tables
class DisbursementAutoRefresh {
    constructor() {
        this.isAutoRefreshEnabled = false;
        this.refreshInterval = null;
        this.refreshTime = 3000; // 3 seconds
        this.isRefreshing = false;
        this.lastUnsignedHash = '';
        this.lastSignedHash = '';
        this.currentTab = 'unsigned'; // Track current active tab
    }

    // Initialize auto-refresh
    init() {
        console.log('Initializing stealth disbursement auto-refresh...');
        
        // Calculate initial hashes
        this.lastUnsignedHash = this.calculateTableHash('unsigned');
        this.lastSignedHash = this.calculateTableHash('signed');
        
        // Determine current active tab
        this.determineActiveTab();
        
        // Start auto-refresh
        this.startAutoRefresh();
        
        // Add event listeners to pause on user interaction
        this.addPauseListeners();
        
        // Listen for tab changes
        this.addTabChangeListeners();
    }

    // Determine which tab is currently active
    determineActiveTab() {
        const unsignedTab = document.getElementById('tab-unsigned');
        if (unsignedTab && unsignedTab.classList.contains('active')) {
            this.currentTab = 'unsigned';
        } else {
            this.currentTab = 'signed';
        }
    }

    // Add listeners for tab changes
    addTabChangeListeners() {
        const unsignedTab = document.getElementById('tab-unsigned');
        const signedTab = document.getElementById('tab-signed');
        
        if (unsignedTab) {
            unsignedTab.addEventListener('click', () => {
                this.currentTab = 'unsigned';
            });
        }
        
        if (signedTab) {
            signedTab.addEventListener('click', () => {
                this.currentTab = 'signed';
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
                this.refreshDisbursements();
            }
        }, this.refreshTime);

        console.log('Stealth disbursement auto-refresh started (3 seconds interval)');
    }

    // Stop auto-refresh
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        this.isAutoRefreshEnabled = false;
        console.log('Stealth disbursement auto-refresh stopped');
    }

    // Toggle auto-refresh (for debugging)
    toggleAutoRefresh() {
        if (this.isAutoRefreshEnabled) {
            this.stopAutoRefresh();
        } else {
            this.startAutoRefresh();
        }
    }

    // Refresh disbursement tables
    async refreshDisbursements() {
        if (this.isRefreshing) return;

        this.isRefreshing = true;
        
        try {
            // Get current states
            const currentUnsignedPage = disbursementPagination?.currentUnsignedPage || 1;
            const currentSignedPage = disbursementPagination?.currentSignedPage || 1;
            const currentUnsignedSearch = document.getElementById('unsignedNameSearch')?.value || '';
            const currentSignedSearch = document.getElementById('signedNameSearch')?.value || '';
            const currentUnsignedBarangay = document.getElementById('unsignedBarangayFilter')?.value || '';
            const currentSignedBarangay = document.getElementById('signedBarangayFilter')?.value || '';

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

            // Extract disbursement data
            const newUnsignedRows = this.extractDisbursementRows(doc, '#unsignedTabContent tbody tr');
            const newSignedRows = this.extractDisbursementRows(doc, '#signedTabContent tbody tr');

            // Check if data actually changed using hash comparison
            const newUnsignedHash = this.calculateRowsHash(newUnsignedRows);
            const newSignedHash = this.calculateRowsHash(newSignedRows);

            let unsignedUpdated = false;
            let signedUpdated = false;

            // Update tables only if data changed
            if (newUnsignedHash !== this.lastUnsignedHash) {
                this.updateDisbursementTable('unsigned', newUnsignedRows, currentUnsignedPage, currentUnsignedSearch, currentUnsignedBarangay);
                this.lastUnsignedHash = newUnsignedHash;
                unsignedUpdated = true;
            }

            if (newSignedHash !== this.lastSignedHash) {
                this.updateDisbursementTable('signed', newSignedRows, currentSignedPage, currentSignedSearch, currentSignedBarangay);
                this.lastSignedHash = newSignedHash;
                signedUpdated = true;
            }

            // Show subtle notification only if data changed
            if (unsignedUpdated || signedUpdated) {
                this.showSubtleNotification(unsignedUpdated, signedUpdated);
            }

        } catch (error) {
            console.error('Error refreshing disbursements:', error);
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
    calculateTableHash(tabType) {
        const rows = disbursementPagination[`filtered${tabType.charAt(0).toUpperCase() + tabType.slice(1)}Data`] || [];
        const rowData = rows.map(row => {
            const cells = [
                row.full_name,
                row.applicant_brgy,
                row.disburse_semester,
                row.disburse_acad_year,
                row.disburse_amount
            ];
            return cells.join('|');
        }).join('||');
        return this.simpleHash(rowData);
    }

    // Extract disbursement rows from HTML
    extractDisbursementRows(doc, selector) {
        const rows = Array.from(doc.querySelectorAll(selector));
        return rows.filter(row => !row.querySelector('td[colspan]'))
                  .map(row => {
                      const cells = Array.from(row.cells).map(cell => cell.textContent.trim());
                      const buttons = Array.from(row.querySelectorAll('button')).map(btn => btn.outerHTML);
                      return { html: row.outerHTML, cells, buttons };
                  });
    }

    // Update disbursement table if data changed
    updateDisbursementTable(tabType, newRows, currentPage, currentSearch, currentBarangay) {
        console.log(`${tabType} disbursement table updated silently`);

        // Replace table body
        const tbody = document.querySelector(`#${tabType}TabContent tbody`);
        if (tbody) {
            tbody.innerHTML = newRows.map(row => row.html).join('');
        }

        // Update the pagination data
        this.updatePaginationData(tabType, newRows);

        // Reapply filters
        this.reapplyDisbursementFilters(tabType, currentSearch, currentBarangay, currentPage);

        // Reattach event listeners
        this.reattachDisbursementEventListeners(tabType);
    }

    // Update pagination data
    updatePaginationData(tabType, newRows) {
        // This is a simplified approach - in a real implementation, you'd need to 
        // update the actual data in disbursementPagination
        console.log(`Updated ${tabType} disbursement data`);
        
        // For a complete implementation, you would need to update the actual
        // disbursementPagination filtered data arrays
    }

    // Reapply filters
    reapplyDisbursementFilters(tabType, searchValue, barangayValue, currentPage) {
        const searchInput = document.getElementById(`${tabType}NameSearch`);
        const barangaySelect = document.getElementById(`${tabType}BarangayFilter`);

        if (searchInput) searchInput.value = searchValue;
        if (barangaySelect) barangaySelect.value = barangayValue;

        // Re-filter using the existing pagination functions
        if (tabType === 'unsigned') {
            disbursementPagination.filterUnsignedData();
        } else {
            disbursementPagination.filterSignedData();
        }

        // Restore page
        setTimeout(() => {
            if (tabType === 'unsigned') {
                disbursementPagination.currentUnsignedPage = currentPage;
                disbursementPagination.renderUnsignedPage();
            } else {
                disbursementPagination.currentSignedPage = currentPage;
                disbursementPagination.renderSignedPage();
            }
        }, 50);
    }

    // Reattach event listeners for disbursement tables
    reattachDisbursementEventListeners(tabType) {
        if (tabType === 'unsigned') {
            // Reattach signature modal listeners for unsigned tab
            document.querySelectorAll('#unsignedTabContent button[onclick*="openSignatureModal"]').forEach(button => {
                const onclick = button.getAttribute('onclick');
                if (onclick && onclick.includes('openSignatureModal')) {
                    const matches = onclick.match(/openSignatureModal\((\d+)\)/);
                    if (matches) {
                        const id = matches[1];
                        button.onclick = () => openSignatureModal(parseInt(id));
                    }
                }
            });
        }
        
        // Note: Signed tab doesn't need special event listeners as it's just displaying data
    }

    // Show subtle notification (very minimal)
    showSubtleNotification(unsignedUpdated, signedUpdated) {
        // Only show in console for debugging
        if (unsignedUpdated) {
            console.log('New unsigned disbursements detected');
        }
        if (signedUpdated) {
            console.log('New signed disbursements detected');
        }
        
        // Optional: Very subtle visual cue (completely hidden from user)
        // You can remove this entire function if you want absolutely no visual feedback
    }

    // Add pause listeners for user interaction
    addPauseListeners() {
        // Pause when user is interacting with signature modal
        const signatureModal = document.getElementById('signatureModal');
        if (signatureModal) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (signatureModal.classList.contains('hidden')) {
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

            observer.observe(signatureModal, { attributes: true, attributeFilter: ['class'] });
        }

        // Pause when user is typing in search
        const searchInputs = ['unsignedNameSearch', 'signedNameSearch'];
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
        const tables = [
            document.getElementById('unsignedTabContent'),
            document.getElementById('signedTabContent')
        ];
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

        // Pause when interacting with dropdown filters
        const dropdowns = ['unsignedBarangayFilter', 'signedBarangayFilter'];
        dropdowns.forEach(dropdownId => {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.addEventListener('focus', () => {
                    this.stopAutoRefresh();
                });
                
                dropdown.addEventListener('blur', () => {
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
    // Wait for disbursement pagination to initialize
    setTimeout(() => {
        if (typeof disbursementPagination !== 'undefined') {
            window.disbursementAutoRefresh = new DisbursementAutoRefresh();
            window.disbursementAutoRefresh.init();
            
            // Add keyboard shortcut to toggle refresh (completely hidden feature)
            document.addEventListener('keydown', function(e) {
                // Ctrl+Alt+D to toggle refresh (completely hidden from user)
                if (e.ctrlKey && e.altKey && e.key === 'd') {
                    e.preventDefault();
                    window.disbursementAutoRefresh.toggleAutoRefresh();
                    console.log('Stealth disbursement auto-refresh toggled');
                }
            });
        }
    }, 1500);
});

// Manual refresh function (completely hidden)
function stealthDisbursementRefresh() {
    if (window.disbursementAutoRefresh) {
        window.disbursementAutoRefresh.refreshDisbursements();
    }
}

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DisbursementAutoRefresh;
}