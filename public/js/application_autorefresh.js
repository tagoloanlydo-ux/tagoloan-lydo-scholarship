// application_autorefresh.js
// Auto-refresh functionality for Scholarship Management Page (Always On) with Modal Support

document.addEventListener('DOMContentLoaded', function() {
    let autoRefreshEnabled = true;
    let refreshInterval;
    let isModalOpen = false;
    let currentOpenModalType = null;
    
    // Function to perform silent refresh
    function performSilentRefresh() {
        if (!autoRefreshEnabled || isModalOpen) return;
        
        console.log('Performing silent refresh...');
        
        // Create a subtle loading indicator (optional)
        showSubtleLoadingIndicator();
        
        // Use fetch to get updated content
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            },
            cache: 'no-cache'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            // Parse the new HTML
            const parser = new DOMParser();
            const newDocument = parser.parseFromString(html, 'text/html');
            
            // Update specific parts of the page without full reload
            updatePageContent(newDocument);
            
            // Hide loading indicator
            hideSubtleLoadingIndicator();
        })
        .catch(error => {
            console.error('Auto-refresh failed:', error);
            hideSubtleLoadingIndicator();
        });
    }

    // Function to refresh modal content specifically
    function refreshModalContent() {
        if (!isModalOpen || !currentOpenModalType) return;

        console.log('Refreshing modal content for:', currentOpenModalType);
        
        // Use fetch to get updated content
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            },
            cache: 'no-cache'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            // Parse the new HTML
            const parser = new DOMParser();
            const newDocument = parser.parseFromString(html, 'text/html');
            
            // Update modal content based on current open modal type
            updateModalContent(newDocument);
        })
        .catch(error => {
            console.error('Modal refresh failed:', error);
        });
    }
    
    // Function to show subtle loading indicator
    function showSubtleLoadingIndicator() {
        let indicator = document.getElementById('auto-refresh-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'auto-refresh-indicator';
            indicator.style.cssText = `
                position: fixed;
                top: 10px;
                right: 10px;
                width: 16px;
                height: 16px;
                border: 2px solid #f3f3f3;
                border-top: 2px solid #7e22ce;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                z-index: 9999;
                opacity: 0.5;
                display: none;
            `;
            
            // Add CSS for spin animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
            
            document.body.appendChild(indicator);
        }
        indicator.style.display = 'block';
    }
    
    // Function to hide loading indicator
    function hideSubtleLoadingIndicator() {
        const indicator = document.getElementById('auto-refresh-indicator');
        if (indicator) {
            indicator.style.opacity = '0';
            setTimeout(() => {
                indicator.style.display = 'none';
                indicator.style.opacity = '0.5';
            }, 300);
        }
    }
    
    // Function to update page content with new data
    function updatePageContent(newDocument) {
        // Update notifications
        updateNotifications(newDocument);
        
        // Update application tables
        updateApplicationTables(newDocument);
        
        // Update pagination if needed
        updatePagination(newDocument);
        
        // Update badge counts
        updateBadgeCounts(newDocument);
        
        // Update document status badges
        updateDocumentStatusBadges(newDocument);
    }

    // Function to update modal content
    function updateModalContent(newDocument) {
        switch(currentOpenModalType) {
            case 'application':
                updateApplicationModalContent(newDocument);
                break;
            case 'document':
                updateDocumentModalContent(newDocument);
                break;
            case 'delete':
                updateDeleteModalContent(newDocument);
                break;
        }
    }

    // Update application modal content
    function updateApplicationModalContent(newDocument) {
        const currentModal = document.getElementById('applicationModal');
        const currentContent = document.getElementById('applicationContent');
        
        if (!currentModal || !currentContent) return;

        const newModal = newDocument.getElementById('applicationModal');
        const newContent = newDocument.getElementById('applicationContent');
        
        if (newModal && newContent) {
            // Only update if content has changed
            if (currentContent.innerHTML !== newContent.innerHTML) {
                console.log('Updating application modal content...');
                
                // Store current application ID to maintain state
                const currentAppId = currentApplicationId;
                
                // Update content
                currentContent.innerHTML = newContent.innerHTML;
                
                // Restore current application ID
                if (currentAppId) {
                    currentApplicationId = currentAppId;
                }
                
                // Reattach event listeners for modal buttons
                reattachApplicationModalEvents();
            }
        }
    }

    // Update document modal content
    function updateDocumentModalContent(newDocument) {
        const currentModal = document.getElementById('documentModal');
        const currentViewer = document.getElementById('documentViewer');
        const currentControls = document.getElementById('documentReviewControls');
        
        if (!currentModal) return;

        // For document modal, we mainly need to ensure the document is still loaded
        // and update review controls if needed
        if (currentViewer && currentViewer.src) {
            // Re-verify the document URL is still valid
            const documentUrl = currentViewer.src;
            currentViewer.src = '';
            setTimeout(() => {
                currentViewer.src = documentUrl;
            }, 100);
        }

        // Update review controls if they exist
        if (currentControls) {
            const newControls = newDocument.getElementById('documentReviewControls');
            if (newControls && currentControls.innerHTML !== newControls.innerHTML) {
                currentControls.innerHTML = newControls.innerHTML;
                reattachDocumentModalEvents();
            }
        }
    }

    // Update delete modal content
    function updateDeleteModalContent(newDocument) {
        // Delete modal is simple, usually doesn't need dynamic updates
        // But we can update the applicant name if needed
        const currentApplicantName = document.getElementById('deleteApplicantName');
        const newApplicantName = newDocument.getElementById('deleteApplicantName');
        
        if (currentApplicantName && newApplicantName && 
            currentApplicantName.textContent !== newApplicantName.textContent) {
            currentApplicantName.textContent = newApplicantName.textContent;
        }
    }
    
    // Update notifications section
    function updateNotifications(newDocument) {
        const currentNotifications = document.querySelector('#notifDropdown ul');
        const newNotifications = newDocument.querySelector('#notifDropdown ul');
        
        if (currentNotifications && newNotifications) {
            // Only update if content has changed
            if (currentNotifications.innerHTML !== newNotifications.innerHTML) {
                currentNotifications.innerHTML = newNotifications.innerHTML;
                
                // Update notification count badge
                const currentCount = document.getElementById('notifCount');
                const newCount = newDocument.getElementById('notifCount');
                
                if (currentCount && newCount) {
                    currentCount.innerHTML = newCount.innerHTML;
                }
            }
        }
    }
    
    // Update application tables
    function updateApplicationTables(newDocument) {
        // Update pending applications table
        updateTableSection('tableView', newDocument);
        
        // Update reviewed applications table
        updateTableSection('listView', newDocument);
    }
    
    // Helper function to update a specific table section
    function updateTableSection(sectionId, newDocument) {
        const currentSection = document.getElementById(sectionId);
        const newSection = newDocument.getElementById(sectionId);
        
        if (currentSection && newSection) {
            // Get current view state
            const isHidden = currentSection.classList.contains('hidden');
            
            // Only update if content has changed
            if (currentSection.innerHTML !== newSection.innerHTML) {
                // Store scroll position
                const scrollPosition = window.scrollY;
                
                // Update content
                currentSection.innerHTML = newSection.innerHTML;
                
                // Restore view state
                if (isHidden) {
                    currentSection.classList.add('hidden');
                } else {
                    currentSection.classList.remove('hidden');
                }
                
                // Restore scroll position
                window.scrollTo(0, scrollPosition);
                
                // Reattach event listeners
                reattachTableEventListeners(sectionId);
            }
        }
    }
    
    // Reattach event listeners to table elements after update
    function reattachTableEventListeners(sectionId) {
        const section = document.getElementById(sectionId);
        if (!section) return;
        
        // Reattach click events for review buttons
        section.querySelectorAll('button[onclick*="openApplicationModal"]').forEach(button => {
            const originalOnClick = button.getAttribute('onclick');
            button.onclick = function() {
                eval(originalOnClick);
            };
        });
        
        // Reattach click events for delete buttons
        section.querySelectorAll('button[onclick*="confirmDeletePending"]').forEach(button => {
            const originalOnClick = button.getAttribute('onclick');
            button.onclick = function() {
                eval(originalOnClick);
            };
        });
        
        // Reattach click events for tab switching
        const pendingTab = document.getElementById('pendingTab');
        const reviewedTab = document.getElementById('reviewedTab');
        
        if (pendingTab) {
            pendingTab.onclick = function() { showTable(); };
        }
        if (reviewedTab) {
            reviewedTab.onclick = function() { showList(); };
        }
    }

    // Reattach application modal events
    function reattachApplicationModalEvents() {
        const approveBtn = document.getElementById('approveBtn');
        const rejectBtn = document.getElementById('rejectBtn');
        const sendEmailBtn = document.getElementById('sendEmailBtn');

        if (approveBtn) {
            approveBtn.onclick = approveApplication;
        }
        if (rejectBtn) {
            rejectBtn.onclick = rejectApplication;
        }
        if (sendEmailBtn) {
            sendEmailBtn.onclick = sendDocumentEmail;
        }

        // Reattach document item click events
        document.querySelectorAll('.document-item-wrapper a[onclick*="openDocumentModal"]').forEach(link => {
            const originalOnClick = link.getAttribute('onclick');
            link.onclick = function(event) {
                event.preventDefault();
                eval(originalOnClick);
            };
        });
    }

    // Reattach document modal events
    function reattachDocumentModalEvents() {
        const goodBtn = document.querySelector('#documentReviewControls .mark-good-btn');
        const badBtn = document.querySelector('#documentReviewControls .mark-bad-btn');

        if (goodBtn) {
            const documentType = goodBtn.getAttribute('data-document');
            goodBtn.onclick = function() {
                markDocumentAsGood(documentType);
            };
        }
        if (badBtn) {
            const documentType = badBtn.getAttribute('data-document');
            badBtn.onclick = function() {
                markDocumentAsBad(documentType);
            };
        }
    }
    
    // Update pagination
    function updatePagination(newDocument) {
        const tablePagination = document.getElementById('tablePagination');
        const listPagination = document.getElementById('listPagination');
        const newTablePagination = newDocument.getElementById('tablePagination');
        const newListPagination = newDocument.getElementById('listPagination');
        
        if (tablePagination && newTablePagination) {
            if (tablePagination.innerHTML !== newTablePagination.innerHTML) {
                tablePagination.innerHTML = newTablePagination.innerHTML;
            }
        }
        
        if (listPagination && newListPagination) {
            if (listPagination.innerHTML !== newListPagination.innerHTML) {
                listPagination.innerHTML = newListPagination.innerHTML;
            }
        }
    }
    
    // Update badge counts
    function updateBadgeCounts(newDocument) {
        // Update notification badge
        const currentBadge = document.getElementById('notifCount');
        const newBadge = newDocument.getElementById('notifCount');
        
        if (currentBadge && newBadge && currentBadge.textContent !== newBadge.textContent) {
            currentBadge.textContent = newBadge.textContent;
        }
    }
    
    // Update document status badges
    function updateDocumentStatusBadges(newDocument) {
        // This would update any document status badges that might have changed
    }
    
    // Start auto-refresh
    function startAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        
        refreshInterval = setInterval(performSilentRefresh, 3000); // 3 seconds
    }

    // Start modal refresh (faster interval for modals)
    function startModalRefresh() {
        // Refresh modal content every 2 seconds when modal is open
        return setInterval(refreshModalContent, 2000);
    }
    
    // Stop auto-refresh
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
        hideSubtleLoadingIndicator();
    }

    // Monitor modal openings and closings
    function observeModalChanges() {
        // Override the modal open/close functions to track state
        const originalOpenApplicationModal = window.openApplicationModal;
        const originalCloseApplicationModal = window.closeApplicationModal;
        const originalOpenDocumentModal = window.openDocumentModal;
        const originalCloseDocumentModal = window.closeDocumentModal;
        const originalOpenDeleteModal = window.openDeleteModal;
        const originalCloseDeleteModal = window.closeDeleteModal;

        // Application Modal
        window.openApplicationModal = function(...args) {
            isModalOpen = true;
            currentOpenModalType = 'application';
            stopAutoRefresh();
            startModalRefresh();
            if (originalOpenApplicationModal) {
                return originalOpenApplicationModal.apply(this, args);
            }
        };

        window.closeApplicationModal = function(...args) {
            isModalOpen = false;
            currentOpenModalType = null;
            startAutoRefresh();
            if (originalCloseApplicationModal) {
                return originalCloseApplicationModal.apply(this, args);
            }
        };

        // Document Modal
        window.openDocumentModal = function(...args) {
            isModalOpen = true;
            currentOpenModalType = 'document';
            stopAutoRefresh();
            startModalRefresh();
            if (originalOpenDocumentModal) {
                return originalOpenDocumentModal.apply(this, args);
            }
        };

        window.closeDocumentModal = function(...args) {
            isModalOpen = false;
            currentOpenModalType = null;
            startAutoRefresh();
            if (originalCloseDocumentModal) {
                return originalCloseDocumentModal.apply(this, args);
            }
        };

        // Delete Modal
        window.openDeleteModal = function(...args) {
            isModalOpen = true;
            currentOpenModalType = 'delete';
            stopAutoRefresh();
            if (originalOpenDeleteModal) {
                return originalOpenDeleteModal.apply(this, args);
            }
        };

        window.closeDeleteModal = function(...args) {
            isModalOpen = false;
            currentOpenModalType = null;
            startAutoRefresh();
            if (originalCloseDeleteModal) {
                return originalCloseDeleteModal.apply(this, args);
            }
        };

        // Also observe modal state via DOM changes as backup
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && 
                    (mutation.target.id === 'applicationModal' || 
                     mutation.target.id === 'documentModal' ||
                     mutation.target.id === 'deleteModal')) {
                    
                    const isHidden = mutation.target.classList.contains('hidden');
                    
                    if (!isHidden && !isModalOpen) {
                        // Modal opened
                        isModalOpen = true;
                        currentOpenModalType = mutation.target.id.replace('Modal', '');
                        stopAutoRefresh();
                        if (currentOpenModalType !== 'delete') {
                            startModalRefresh();
                        }
                    } else if (isHidden && isModalOpen) {
                        // Modal closed
                        isModalOpen = false;
                        currentOpenModalType = null;
                        startAutoRefresh();
                    }
                }
            });
        });

        // Observe modal elements
        const applicationModal = document.getElementById('applicationModal');
        const documentModal = document.getElementById('documentModal');
        const deleteModal = document.getElementById('deleteModal');

        if (applicationModal) {
            observer.observe(applicationModal, { attributes: true });
        }
        if (documentModal) {
            observer.observe(documentModal, { attributes: true });
        }
        if (deleteModal) {
            observer.observe(deleteModal, { attributes: true });
        }
    }
    
    // Pause auto-refresh when page is not visible
    function handleVisibilityChange() {
        if (document.hidden) {
            // Page is hidden, pause auto-refresh
            stopAutoRefresh();
        } else {
            // Page is visible again, restart auto-refresh
            startAutoRefresh();
        }
    }
    
    // Initialize auto-refresh
    function initAutoRefresh() {
        // Start auto-refresh
        startAutoRefresh();
        
        // Monitor modal states
        observeModalChanges();
        
        // Pause when page is not visible
        document.addEventListener('visibilitychange', handleVisibilityChange);
        
        // Stop auto-refresh before page unload
        window.addEventListener('beforeunload', stopAutoRefresh);
    }
    
    // Check if we're on the application page before initializing
    if (window.location.pathname.includes('/mayor_staff/application')) {
        // Wait a bit before initializing to avoid conflict with other scripts
        setTimeout(initAutoRefresh, 1000);
    }
});