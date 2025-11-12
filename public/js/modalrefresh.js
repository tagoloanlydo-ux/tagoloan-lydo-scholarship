// modalrefresh.js
document.addEventListener('DOMContentLoaded', function() {
    let modalRefreshInterval;
    let isModalOpen = false;
    let currentScholarId = null;
    let currentModalType = null; // 'review', 'view', or 'edit'

    // Function to start modal refresh
    function startModalRefresh() {
        if (modalRefreshInterval) {
            clearInterval(modalRefreshInterval);
        }

        modalRefreshInterval = setInterval(() => {
            if (isModalOpen && currentScholarId) {
                refreshModalContent();
            }
        }, 2000); // Refresh every 2 seconds
    }

    // Function to stop modal refresh
    function stopModalRefresh() {
        if (modalRefreshInterval) {
            clearInterval(modalRefreshInterval);
            modalRefreshInterval = null;
        }
    }

    // Function to refresh modal content based on current modal type
    function refreshModalContent() {
        if (!currentScholarId || !currentModalType) return;

        // Store current scroll position and any form data
        const scrollPosition = window.scrollY;
        const modalContent = document.getElementById(getModalContentId());

        if (!modalContent) return;

        // Create a temporary backup of important form inputs
        const formData = backupFormData();

        // Show subtle loading indicator without disrupting user
        showSubtleLoading();

        // Make AJAX request to refresh content
        fetch(`/lydo_staff/renewal/get-renewal-data/${currentScholarId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            updateModalContent(data);
            restoreFormData(formData);
            hideSubtleLoading();
            
            // Restore scroll position
            window.scrollTo(0, scrollPosition);
        })
        .catch(error => {
            console.log('Modal refresh failed:', error);
            hideSubtleLoading();
        });
    }

    // Helper function to get the appropriate modal content ID
    function getModalContentId() {
        switch(currentModalType) {
            case 'review': return 'applicationContent';
            case 'view': return 'viewRenewalContent';
            case 'edit': return 'editRenewalContent';
            default: return 'applicationContent';
        }
    }

    // Backup form data to prevent loss during refresh
    function backupFormData() {
        const formData = {};
        const modal = document.getElementById(getCurrentModalId());
        
        if (modal) {
            const inputs = modal.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.name || input.id) {
                    const key = input.name || input.id;
                    formData[key] = input.value;
                }
            });
        }
        
        return formData;
    }

    // Restore form data after refresh
    function restoreFormData(formData) {
        const modal = document.getElementById(getCurrentModalId());
        
        if (modal) {
            Object.keys(formData).forEach(key => {
                const input = modal.querySelector(`[name="${key}"], [id="${key}"]`);
                if (input && input.value !== formData[key]) {
                    input.value = formData[key];
                    
                    // Trigger change event for any dependent functionality
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                }
            });
        }
    }

    // Get current modal ID based on type
    function getCurrentModalId() {
        switch(currentModalType) {
            case 'review': return 'openRenewalModal';
            case 'view': return 'viewRenewalModal';
            case 'edit': return 'editRenewalModal';
            default: return 'openRenewalModal';
        }
    }

    // Show subtle loading indicator
    function showSubtleLoading() {
        const modal = document.getElementById(getCurrentModalId());
        if (modal) {
            const existingLoader = modal.querySelector('.subtle-refresh-loader');
            if (!existingLoader) {
                const loader = document.createElement('div');
                loader.className = 'subtle-refresh-loader absolute top-2 right-2';
                loader.innerHTML = '<i class="fas fa-sync-alt fa-spin text-blue-500 text-sm"></i>';
                loader.style.zIndex = '1000';
                modal.querySelector('.relative')?.appendChild(loader) || modal.appendChild(loader);
            }
        }
    }

    // Hide subtle loading indicator
    function hideSubtleLoading() {
        const modal = document.getElementById(getCurrentModalId());
        if (modal) {
            const loader = modal.querySelector('.subtle-refresh-loader');
            if (loader) {
                loader.remove();
            }
        }
    }

    // Update modal content with new data
    function updateModalContent(data) {
        // This function should be customized based on your modal structure
        // For now, it's a generic implementation
        const contentArea = document.getElementById(getModalContentId());
        if (contentArea && data.html) {
            contentArea.innerHTML = data.html;
        }
    }

    // Monitor modal open events
    function setupModalEventListeners() {
        // Listen for modal open events (you'll need to trigger these from your existing modal functions)
        document.addEventListener('modalOpened', function(e) {
            isModalOpen = true;
            currentScholarId = e.detail.scholarId;
            currentModalType = e.detail.modalType;
            startModalRefresh();
        });

        // Listen for modal close events
        document.addEventListener('modalClosed', function(e) {
            isModalOpen = false;
            currentScholarId = null;
            currentModalType = null;
            stopModalRefresh();
        });

        // Also listen for visibility changes to optimize performance
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden, pause refreshing
                stopModalRefresh();
            } else if (isModalOpen) {
                // Page is visible and modal is open, resume refreshing
                startModalRefresh();
            }
        });
    }

    // Override your existing modal functions to trigger events
    function patchModalFunctions() {
        // Store original functions
        const originalOpenRenewalModal = window.openRenewalModal;
        const originalCloseApplicationModal = window.closeApplicationModal;
        const originalOpenViewRenewalModal = window.openViewRenewalModal;
        const originalCloseViewRenewalModal = window.closeViewRenewalModal;
        const originalOpenEditRenewalModal = window.openEditRenewalModal;
        const originalCloseEditRenewalModal = window.closeEditRenewalModal;

        // Override openRenewalModal
        window.openRenewalModal = function(scholarId) {
            originalOpenRenewalModal(scholarId);
            document.dispatchEvent(new CustomEvent('modalOpened', {
                detail: { scholarId: scholarId, modalType: 'review' }
            }));
        };

        // Override closeApplicationModal
        window.closeApplicationModal = function() {
            originalCloseApplicationModal();
            document.dispatchEvent(new CustomEvent('modalClosed'));
        };

        // Override openViewRenewalModal
        window.openViewRenewalModal = function(scholarId) {
            originalOpenViewRenewalModal(scholarId);
            document.dispatchEvent(new CustomEvent('modalOpened', {
                detail: { scholarId: scholarId, modalType: 'view' }
            }));
        };

        // Override closeViewRenewalModal
        window.closeViewRenewalModal = function() {
            originalCloseViewRenewalModal();
            document.dispatchEvent(new CustomEvent('modalClosed'));
        };

        // Override openEditRenewalModal
        window.openEditRenewalModal = function(scholarId, status) {
            originalOpenEditRenewalModal(scholarId, status);
            document.dispatchEvent(new CustomEvent('modalOpened', {
                detail: { scholarId: scholarId, modalType: 'edit' }
            }));
        };

        // Override closeEditRenewalModal
        window.closeEditRenewalModal = function() {
            originalCloseEditRenewalModal();
            document.dispatchEvent(new CustomEvent('modalClosed'));
        };
    }

    // Initialize the modal refresh system
    function initModalRefresh() {
        setupModalEventListeners();
        patchModalFunctions();
        
        console.log('Modal refresh system initialized - will refresh every 2 seconds when modals are open');
    }

    // Start the system
    initModalRefresh();

    // Export functions for global access if needed
    window.ModalRefresh = {
        start: startModalRefresh,
        stop: stopModalRefresh,
        refreshNow: refreshModalContent
    };
});