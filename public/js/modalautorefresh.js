// Stealth auto-refresh functionality for document modal
class ModalAutoRefresh {
    constructor() {
        this.isAutoRefreshEnabled = false;
        this.modalRefreshInterval = null;
        this.modalRefreshTime = 2000; // 2 seconds
        this.isModalRefreshing = false;
        this.currentModalData = null;
        this.lastDocumentStatus = {};
    }

    // Initialize modal auto-refresh
    init() {
        console.log('Initializing stealth modal auto-refresh...');
        
        // Observe modal openings
        this.observeModalOpenings();
        
        // Start modal refresh when modal opens
        this.startModalRefresh();
    }

    // Observe when modals open
    observeModalOpenings() {
        // Watch for application modal
        const applicationModal = document.getElementById('applicationModal');
        if (applicationModal) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (!applicationModal.classList.contains('hidden')) {
                            // Modal opened - start refresh
                            this.onModalOpened();
                        } else {
                            // Modal closed - stop refresh
                            this.onModalClosed();
                        }
                    }
                });
            });

            observer.observe(applicationModal, { 
                attributes: true, 
                attributeFilter: ['class'] 
            });
        }

        // Also watch for document modal
        const documentModal = document.getElementById('documentModal');
        if (documentModal) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (!documentModal.classList.contains('hidden')) {
                            // Document modal opened
                            this.onDocumentModalOpened();
                        }
                    }
                });
            });

            observer.observe(documentModal, { 
                attributes: true, 
                attributeFilter: ['class'] 
            });
        }
    }

    // When application modal opens
    onModalOpened() {
        console.log('Application modal opened - starting stealth refresh');
        
        // Store current modal state
        this.storeCurrentModalState();
        
        // Start modal refresh
        this.startModalRefresh();
    }

    // When modal closes
    onModalClosed() {
        console.log('Modal closed - stopping stealth refresh');
        this.stopModalRefresh();
        this.currentModalData = null;
    }

    // When document modal opens
    onDocumentModalOpened() {
        // Pause modal refresh while document viewer is open
        this.stopModalRefresh();
        
        // Resume when document modal closes
        const documentModal = document.getElementById('documentModal');
        if (documentModal) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (documentModal.classList.contains('hidden')) {
                            // Document modal closed - resume application modal refresh
                            observer.disconnect();
                            this.startModalRefresh();
                        }
                    }
                });
            });

            observer.observe(documentModal, { 
                attributes: true, 
                attributeFilter: ['class'] 
            });
        }
    }

    // Store current modal state
    storeCurrentModalState() {
        if (!currentApplicationId) return;

        this.currentModalData = {
            applicationId: currentApplicationId,
            documentStatus: this.getCurrentDocumentStatus(),
            timestamp: Date.now()
        };

        // Store initial document status for comparison
        this.lastDocumentStatus = { ...this.currentModalData.documentStatus };
    }

    // Get current document status from badges
    getCurrentDocumentStatus() {
        const status = {};
        const documentTypes = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];
        
        documentTypes.forEach(docType => {
            const badge = document.getElementById(`badge-${docType}`);
            const icon = document.getElementById(`icon-${docType}`);
            
            if (badge && !badge.classList.contains('hidden')) {
                if (badge.classList.contains('badge-good')) {
                    status[docType] = 'good';
                } else if (badge.classList.contains('badge-bad')) {
                    status[docType] = 'bad';
                } else if (badge.classList.contains('badge-updated')) {
                    status[docType] = 'updated';
                } else if (badge.classList.contains('badge-new')) {
                    status[docType] = 'new';
                }
            } else if (icon) {
                // Check icon color as fallback
                if (icon.classList.contains('text-green-600')) {
                    status[docType] = 'good';
                } else if (icon.classList.contains('text-red-600')) {
                    status[docType] = 'bad';
                } else {
                    status[docType] = 'unreviewed';
                }
            }
        });

        return status;
    }

    // Start modal refresh
    startModalRefresh() {
        if (this.modalRefreshInterval) {
            clearInterval(this.modalRefreshInterval);
        }

        this.isAutoRefreshEnabled = true;
        
        this.modalRefreshInterval = setInterval(() => {
            if (!this.isModalRefreshing && this.currentModalData) {
                this.refreshModalContent();
            }
        }, this.modalRefreshTime);

        console.log('Modal stealth auto-refresh started');
    }

    // Stop modal refresh
    stopModalRefresh() {
        if (this.modalRefreshInterval) {
            clearInterval(this.modalRefreshInterval);
            this.modalRefreshInterval = null;
        }
        this.isAutoRefreshEnabled = false;
        console.log('Modal stealth auto-refresh stopped');
    }

    // Refresh modal content
    async refreshModalContent() {
        if (this.isModalRefreshing || !this.currentModalData) return;

        this.isModalRefreshing = true;
        
        try {
            const applicationId = this.currentModalData.applicationId;
            
            // Fetch updated document status
            const response = await fetch(`/mayor_staff/get-document-comments/${applicationId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                cache: 'no-cache'
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();
            
            if (data.success) {
                this.updateModalIfNeeded(data.statuses || {});
            }

        } catch (error) {
            console.error('Error refreshing modal:', error);
        } finally {
            this.isModalRefreshing = false;
        }
    }

    // Update modal if document status changed
    updateModalIfNeeded(newStatuses) {
        let hasChanges = false;
        const documentTypes = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];

        documentTypes.forEach(docType => {
            const statusKey = `${docType}_status`;
            const newStatus = newStatuses[statusKey];
            const currentStatus = this.lastDocumentStatus[docType];

            // Check if status changed
            if (newStatus && newStatus !== currentStatus) {
                hasChanges = true;
                this.updateDocumentBadge(docType, newStatus);
                this.lastDocumentStatus[docType] = newStatus;
            }
        });

        // Update action buttons if changes detected
        if (hasChanges) {
            this.updateActionButtons();
            console.log('Modal content updated silently');
        }
    }

    // Update document badge
    updateDocumentBadge(documentType, status) {
        const badge = document.getElementById(`badge-${documentType}`);
        const icon = document.getElementById(`icon-${documentType}`);
        
        if (!badge || !icon) return;

        // Remove all classes first
        badge.classList.remove('badge-new', 'badge-good', 'badge-bad', 'badge-updated', 'hidden');
        icon.classList.remove('text-red-600', 'text-green-600', 'text-gray-500', 'text-purple-600');
        
        // Apply new status
        switch (status) {
            case 'good':
                badge.classList.add('badge-good');
                badge.innerHTML = '✓';
                icon.classList.add('text-green-600');
                break;
            case 'bad':
                badge.classList.add('badge-bad');
                badge.innerHTML = '✗';
                icon.classList.add('text-red-600');
                break;
            case 'New':
                badge.classList.add('badge-updated');
                badge.innerHTML = 'Updated';
                icon.classList.add('text-purple-600');
                break;
            default:
                // For new documents or unreviewed
                if (!badge.classList.contains('hidden')) {
                    badge.classList.add('hidden');
                }
                icon.classList.add('text-purple-600');
        }

        // Ensure badge is visible if it has content
        if (status !== 'unreviewed' && status !== '') {
            badge.classList.remove('hidden');
        }
    }

    // Update action buttons based on document status
    updateActionButtons() {
        if (typeof checkAllDocumentsRated === 'function') {
            // Use the existing function to update buttons
            checkAllDocumentsRated();
        } else {
            // Fallback: manually check document status
            this.manualUpdateActionButtons();
        }
    }

    // Manual update of action buttons
    manualUpdateActionButtons() {
        const documentTypes = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];
        let goodCount = 0;
        let badCount = 0;

        documentTypes.forEach(docType => {
            const status = this.lastDocumentStatus[docType];
            if (status === 'good') {
                goodCount++;
            } else if (status === 'bad') {
                badCount++;
            }
        });

        this.updateButtonsBasedOnStatus(goodCount, badCount);
    }

    // Update buttons based on status counts
    updateButtonsBasedOnStatus(goodCount, badCount) {
        const actionButtons = document.getElementById('actionButtons');
        const approveBtn = document.getElementById('approveBtn');
        const rejectBtn = document.getElementById('rejectBtn');
        const sendEmailBtn = document.getElementById('sendEmailBtn');

        if (!actionButtons || !approveBtn || !rejectBtn) return;

        // Show action buttons
        actionButtons.classList.remove('hidden');

        if (goodCount === 5) {
            // All documents are good
            approveBtn.style.display = 'flex';
            rejectBtn.style.display = 'none';
            if (sendEmailBtn) sendEmailBtn.style.display = 'none';
        } else {
            // Some documents are bad
            approveBtn.style.display = 'none';
            rejectBtn.style.display = 'flex';
            if (sendEmailBtn) sendEmailBtn.style.display = 'flex';
        }
    }

    // Check if modal is currently open
    isModalOpen() {
        const applicationModal = document.getElementById('applicationModal');
        return applicationModal && !applicationModal.classList.contains('hidden');
    }

    // Force refresh modal (for external calls)
    forceRefresh() {
        if (this.isModalOpen() && this.currentModalData) {
            this.refreshModalContent();
        }
    }
}

// Initialize modal auto-refresh when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the page to load completely
    setTimeout(() => {
        window.modalAutoRefresh = new ModalAutoRefresh();
        window.modalAutoRefresh.init();
        
        // Override the openApplicationModal to work with our auto-refresh
        const originalOpenApplicationModal = window.openApplicationModal;
        if (originalOpenApplicationModal) {
            window.openApplicationModal = function(applicationPersonnelId, source = 'pending') {
                // Call original function
                originalOpenApplicationModal(applicationPersonnelId, source);
                
                // Start modal auto-refresh
                setTimeout(() => {
                    if (window.modalAutoRefresh) {
                        window.modalAutoRefresh.storeCurrentModalState();
                        window.modalAutoRefresh.startModalRefresh();
                    }
                }, 500);
            };
        }

        // Override closeApplicationModal to stop refresh
        const originalCloseApplicationModal = window.closeApplicationModal;
        if (originalCloseApplicationModal) {
            window.closeApplicationModal = function() {
                // Stop modal auto-refresh
                if (window.modalAutoRefresh) {
                    window.modalAutoRefresh.stopModalRefresh();
                }
                
                // Call original function
                originalCloseApplicationModal();
            };
        }

        // Override document rating functions to trigger immediate refresh
        const originalMarkDocumentAsGood = window.markDocumentAsGood;
        if (originalMarkDocumentAsGood) {
            window.markDocumentAsGood = function(documentType) {
                // Call original function
                originalMarkDocumentAsGood(documentType);
                
                // Force refresh after a delay
                setTimeout(() => {
                    if (window.modalAutoRefresh) {
                        window.modalAutoRefresh.forceRefresh();
                    }
                }, 1000);
            };
        }

        const originalMarkDocumentAsBad = window.markDocumentAsBad;
        if (originalMarkDocumentAsBad) {
            window.markDocumentAsBad = function(documentType) {
                // Call original function
                originalMarkDocumentAsBad(documentType);
                
                // Force refresh after a delay
                setTimeout(() => {
                    if (window.modalAutoRefresh) {
                        window.modalAutoRefresh.forceRefresh();
                    }
                }, 1000);
            };
        }

    }, 2000);
});

// Global function to manually refresh modal
function refreshModal() {
    if (window.modalAutoRefresh) {
        window.modalAutoRefresh.forceRefresh();
    }
}

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModalAutoRefresh;
}