// Global variables for renewal document rating
let currentRenewalId = null;
let currentDocumentType = null;
let ratedRenewalDocuments = new Set();
let renewalDocumentStatuses = {};
let selectedRenewalId = null;

// Track document updates and show new/updated badges
let documentUpdateTracker = {};

// Save document ratings to localStorage
function saveRenewalRatingsToStorage(renewalId, documentType, status, comment = '') {
    const storageKey = `renewal_ratings_${renewalId}`;
    let ratings = JSON.parse(localStorage.getItem(storageKey)) || {};
    
    ratings[documentType] = {
        status: status,
        comment: comment,
        timestamp: new Date().toISOString()
    };
    
    localStorage.setItem(storageKey, JSON.stringify(ratings));
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize document update tracker
function initializeDocumentUpdateTracker() {
    const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];
    documentTypes.forEach(docType => {
        documentUpdateTracker[docType] = {
            lastStatus: null,
            hasUpdate: false,
            isNew: true
        };
    });
}

// Open document viewer with rating controls
function openRenewalDocumentViewer(documentUrl, title, documentType, renewalId) {
    currentDocumentType = documentType;
    currentRenewalId = renewalId;
    
    document.getElementById('documentTitle').textContent = title;
    document.getElementById('documentViewer').src = documentUrl;
    document.getElementById('downloadDocument').href = documentUrl;
    document.getElementById('documentViewerModal').classList.remove('hidden');
    document.getElementById('documentLoading').style.display = 'flex';

    // Check if this document has been updated
    const isUpdated = documentUpdateTracker[documentType] && documentUpdateTracker[documentType].hasUpdate;
    
    // Add review controls
    const reviewControls = document.getElementById('documentReviewControls');
    reviewControls.innerHTML = `
        <div class="bg-gray-50 p-4 rounded-lg border">
            <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Document Review
                ${isUpdated ? '<span class="ml-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full">UPDATED</span>' : ''}
            </h4>
            
            <!-- Comment Section -->
            <div class="mb-4">
                <label for="comment_${documentType}" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-comment mr-1"></i> Comments
                </label>
                <textarea id="comment_${documentType}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Add your comments about this document..."></textarea>
                <div class="text-xs text-gray-500 mt-1">Comments are auto-saved</div>
            </div>

            <!-- Rating Buttons -->
            <div class="flex gap-3">
                <button class="mark-good-btn flex-1 bg-green-500 text-white px-4 py-3 rounded-lg text-sm font-medium hover:bg-green-600 transition-colors duration-200 flex items-center justify-center gap-2" data-document="${documentType}">
                    <i class="fas fa-check-circle"></i>
                    Mark as Good
                </button>
                <button class="mark-bad-btn flex-1 bg-red-500 text-white px-4 py-3 rounded-lg text-sm font-medium hover:bg-red-600 transition-colors duration-200 flex items-center justify-center gap-2" data-document="${documentType}">
                    <i class="fas fa-times-circle"></i>
                    Mark as Bad
                </button>
            </div>

            <!-- Status Indicator -->
            <div id="status-indicator-${documentType}" class="mt-3 text-sm font-medium hidden">
                <i class="fas fa-info-circle mr-1"></i>
                <span id="status-text-${documentType}"></span>
            </div>

            <!-- Update Notification -->
            ${isUpdated ? `
            <div class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-sync-alt text-orange-500 mr-2"></i>
                    <span class="text-orange-700 text-sm font-medium">This document has been updated since last review</span>
                </div>
            </div>
            ` : ''}
        </div>
    `;
    
    // Add event listeners
    setTimeout(() => {
        // Mark as Good button
        const goodBtn = document.querySelector(`#documentReviewControls .mark-good-btn[data-document="${documentType}"]`);
        if (goodBtn) {
            goodBtn.addEventListener('click', function() {
                const docType = this.getAttribute('data-document');
                markRenewalDocumentAsGood(docType);
            });
        }
        
        // Mark as Bad button
        const badBtn = document.querySelector(`#documentReviewControls .mark-bad-btn[data-document="${documentType}"]`);
        if (badBtn) {
            badBtn.addEventListener('click', function() {
                const docType = this.getAttribute('data-document');
                markRenewalDocumentAsBad(docType);
            });
        }
        
        // Auto-save for comments
        const textarea = document.getElementById(`comment_${documentType}`);
        if (textarea) {
            textarea.addEventListener('input', debounce(function() {
                saveRenewalDocumentComment(documentType, this.value);
                showRenewalAutoSaveIndicator(documentType, false);
            }, 1000));
        }

        // Load current status and update UI
        updateRenewalDocumentModalUI(documentType);
        
        // Load existing comment
        loadRenewalDocumentComment(documentType);
        
        // Load comments and show update button if document is bad
        loadDocumentComments(renewalId);
        
        // Reset update status when document is viewed
        if (documentUpdateTracker[documentType] && documentUpdateTracker[documentType].hasUpdate) {
            setTimeout(() => {
                resetDocumentUpdateStatus(documentType);
                if (renewalDocumentStatuses[documentType] === 'good') {
                    updateRenewalDocumentBadge(documentType, 'good');
                }
            }, 3000);
        }
    }, 100);

    // Hide loading when iframe loads
    document.getElementById('documentViewer').onload = function() {
        document.getElementById('documentLoading').style.display = 'none';
        document.getElementById('documentViewer').style.display = 'block';
    };
}

// Close document viewer modal
function closeDocumentViewerModal() {
    document.getElementById('documentViewerModal').classList.add('hidden');
    document.getElementById('documentViewer').src = '';
    document.getElementById('documentViewer').style.display = 'none';
    document.getElementById('documentLoading').style.display = 'flex';
    document.getElementById('documentReviewControls').innerHTML = '';
}

// Load document ratings from localStorage
function loadRenewalRatingsFromStorage(renewalId) {
    const storageKey = `renewal_ratings_${renewalId}`;
    return JSON.parse(localStorage.getItem(storageKey)) || {};
}

// Clear ratings for a specific renewal
function clearRenewalRatingsFromStorage(renewalId) {
    const storageKey = `renewal_ratings_${renewalId}`;
    localStorage.removeItem(storageKey);
}

// Open renewal modal with document rating functionality
function openRenewalModal(scholarId) {
    const contentDiv = document.getElementById('applicationContent');
    contentDiv.innerHTML = '';

    // Reset tracking
    window.documentsClicked = 0;
    ratedRenewalDocuments = new Set();
    renewalDocumentStatuses = {};
    document.getElementById('actionButtons').style.display = 'none';

    if (window.renewals[scholarId]) {
        selectedRenewalId = window.renewals[scholarId][0].renewal_id; // latest renewal
        currentRenewalId = selectedRenewalId;

        window.renewals[scholarId].forEach((r, index) => {
            const statusBadge = r.renewal_status === 'Approved'
                ? 'bg-green-100 text-green-700'
                : r.renewal_status === 'Rejected'
                ? 'bg-red-100 text-red-700'
                : 'bg-yellow-100 text-yellow-700';

            contentDiv.innerHTML += `
                <div class="border border-gray-200 rounded-xl shadow bg-white p-6 mb-6">
                    
                    <!-- Top Info -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div class="space-y-1 text-gray-700 text-sm">
                            <p><strong>Semester:</strong> ${r.renewal_semester}</p>
                            <p><strong>Academic Year:</strong> ${r.renewal_acad_year}</p>
                            <p><strong>Date Submitted:</strong> ${r.date_submitted}</p>
                        </div>
                        <span class="mt-3 md:mt-0 px-4 py-1 text-sm font-semibold rounded-full ${statusBadge}">
                            ${r.renewal_status}
                        </span>
                    </div>

                    <hr class="my-4">

                    <!-- Documents Section with Rating -->
                    <p class="text-sm text-gray-600 mb-3">Note: Please review and rate all three documents before the Approve/Reject buttons appear.</p>
                    <h4 class="text-gray-800 font-semibold mb-3">Submitted Documents</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="document-item-wrapper">
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_cert_of_reg}', 'Certificate of Registration', 'cert_of_reg', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-blue-50 transition w-full">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2" id="icon-cert_of_reg"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Certificate of Reg.</span>
                            </button>
                            <div class="document-status-badge hidden" id="badge-cert_of_reg"></div>
                        </div>
                        <div class="document-item-wrapper">
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_grade_slip}', 'Grade Slip', 'grade_slip', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-green-50 transition w-full">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2" id="icon-grade_slip"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Grade Slip</span>
                            </button>
                            <div class="document-status-badge hidden" id="badge-grade_slip"></div>
                        </div>
                        <div class="document-item-wrapper">
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_brgy_indigency}', 'Barangay Indigency', 'brgy_indigency', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-purple-50 transition w-full">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2" id="icon-brgy_indigency"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Barangay Indigency</span>
                            </button>
                            <div class="document-status-badge hidden" id="badge-brgy_indigency"></div>
                        </div>
                    </div>
                </div>
            `;
        });

        // Load existing document statuses
        loadRenewalDocumentStatuses(selectedRenewalId);
        
        // Load document comments
        loadDocumentComments(selectedRenewalId);
    } else {
        contentDiv.innerHTML = `<p class="text-gray-500">No renewal requirements found for this scholar.</p>`;
    }

    document.getElementById('openRenewalModal').classList.remove('hidden');
}

// Load existing document statuses
function loadRenewalDocumentStatuses(renewalId) {
    // First check localStorage for existing ratings
    const localRatings = loadRenewalRatingsFromStorage(renewalId);
    
    // Then check server for statuses
    fetch(`/lydo_staff/get-renewal-document-statuses/${renewalId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statuses = data.statuses || {};
                const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];

                documentTypes.forEach(docType => {
                    let statusData = statuses[docType];
                    let status = statusData ? statusData.status : null;
                    let comment = statusData ? statusData.comment : '';
                    
                    // Check localStorage first (most recent)
                    if (localRatings[docType]) {
                        status = localRatings[docType].status;
                        comment = localRatings[docType].comment;
                    }
                    
                    renewalDocumentStatuses[docType] = status;
                    
                    // Initialize tracking if not exists
                    if (!documentUpdateTracker[docType]) {
                        documentUpdateTracker[docType] = {
                            lastStatus: status,
                            hasUpdate: false,
                            isNew: false
                        };
                    } else {
                        // Check if this is an update from bad to good
                        if (documentUpdateTracker[docType].lastStatus === 'bad' && status === 'good') {
                            documentUpdateTracker[docType].hasUpdate = true;
                            documentUpdateTracker[docType].lastStatus = 'good';
                        }
                    }
                    
                    // Update document badges - show updated badge if applicable
                    let badgeStatus = status;
                    if (documentUpdateTracker[docType] && documentUpdateTracker[docType].hasUpdate) {
                        badgeStatus = 'updated';
                    } else if (!status && (!documentUpdateTracker[docType] || documentUpdateTracker[docType].isNew)) {
                        badgeStatus = 'new';
                    }
                    
                    updateRenewalDocumentBadge(docType, badgeStatus);
                    
                    // If document has a status, consider it as rated
                    if (status === 'good' || status === 'bad') {
                        ratedRenewalDocuments.add(docType);
                    }
                });
                
                // Check if all documents are rated
                checkAllRenewalDocumentsRated();
            }
        })
        .catch(error => {
            console.error('Error loading renewal document statuses:', error);
        });
}

// Enhanced update document badge based on status
function updateRenewalDocumentBadge(documentType, status) {
    const badge = document.getElementById(`badge-${documentType}`);
    const icon = document.getElementById(`icon-${documentType}`);
    
    // Reset all styles first
    badge.classList.remove('badge-new', 'badge-good', 'badge-bad', 'badge-updated', 'hidden');
    icon.classList.remove('text-red-600', 'text-green-600', 'text-gray-500', 'text-purple-600', 'text-orange-500');
    
    // Apply new status
    if (status === 'good') {
        badge.classList.add('badge-good');
        badge.innerHTML = '✓';
        icon.classList.add('text-green-600');
        badge.classList.remove('hidden');
    } else if (status === 'bad') {
        badge.classList.add('badge-bad');
        badge.innerHTML = '✗';
        icon.classList.add('text-red-600');
        badge.classList.remove('hidden');
    } else if (status === 'updated') {
        badge.classList.add('badge-updated');
        badge.innerHTML = '🔄';
        icon.classList.add('text-orange-500');
        badge.classList.remove('hidden');
    } else if (status === 'new') {
        badge.classList.add('badge-new');
        badge.innerHTML = 'NEW';
        icon.classList.add('text-purple-600');
        badge.classList.remove('hidden');
    } else {
        // No status, hide the badge
        badge.classList.add('hidden');
        icon.classList.add('text-purple-600');
    }
}

// Enhanced mark document as good with update tracking
function markRenewalDocumentAsGood(documentType) {
    Swal.fire({
        title: 'Mark as Good?',
        text: 'Are you sure you want to mark this document as good?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Mark as Good',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Check if this document was previously marked as bad
            const wasPreviouslyBad = documentUpdateTracker[documentType] && 
                                   documentUpdateTracker[documentType].lastStatus === 'bad';
            
            // Update tracking
            if (documentUpdateTracker[documentType]) {
                documentUpdateTracker[documentType].lastStatus = 'good';
                if (wasPreviouslyBad) {
                    documentUpdateTracker[documentType].hasUpdate = true;
                    documentUpdateTracker[documentType].isNew = false;
                }
            }
            
            // Save status to server
            saveRenewalDocumentStatus(documentType, 'good');
            
            // Save status to localStorage
            const comment = document.getElementById(`comment_${documentType}`)?.value || '';
            saveRenewalRatingsToStorage(currentRenewalId, documentType, 'good', comment);
            
            // Track that this document has been rated
            ratedRenewalDocuments.add(documentType);
            renewalDocumentStatuses[documentType] = 'good';
            
            // Update the badge - show updated badge if it was previously bad
            if (wasPreviouslyBad) {
                updateRenewalDocumentBadge(documentType, 'updated');
                // Auto-clear the updated status after 5 seconds
                setTimeout(() => {
                    if (renewalDocumentStatuses[documentType] === 'good') {
                        updateRenewalDocumentBadge(documentType, 'good');
                    }
                }, 5000);
            } else {
                updateRenewalDocumentBadge(documentType, 'good');
            }
            
            // Handle the status change
            handleDocumentStatusChange(documentType, 'good');
            
            // Check if all documents are rated
            checkAllRenewalDocumentsRated();
            
            // Update modal UI
            updateRenewalDocumentModalUI(documentType);
            
            Swal.fire({
                title: 'Success!',
                text: wasPreviouslyBad ? 'Document updated and marked as good!' : 'Document marked as good.',
                icon: 'success',
                showConfirmButton: true,
                allowOutsideClick: false
            });
        }
    });
}

// Enhanced mark document as bad with update tracking
function markRenewalDocumentAsBad(documentType) {
    Swal.fire({
        title: 'Mark as Bad?',
        text: 'Are you sure you want to mark this document as bad?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Mark as Bad',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Track the bad rating for update detection
            if (documentUpdateTracker[documentType]) {
                documentUpdateTracker[documentType].lastStatus = 'bad';
                documentUpdateTracker[documentType].hasUpdate = false;
            }
            
            // Save status to server
            saveRenewalDocumentStatus(documentType, 'bad');
            
            // Save status to localStorage
            const comment = document.getElementById(`comment_${documentType}`)?.value || '';
            saveRenewalRatingsToStorage(currentRenewalId, documentType, 'bad', comment);
            
            // Track that this document has been rated
            ratedRenewalDocuments.add(documentType);
            renewalDocumentStatuses[documentType] = 'bad';
            
            // Update the badge
            updateRenewalDocumentBadge(documentType, 'bad');
            
            // Handle the status change (show update button, etc.)
            handleDocumentStatusChange(documentType, 'bad');
            
            // Check if all documents are rated
            checkAllRenewalDocumentsRated();
            
            // Update modal UI
            updateRenewalDocumentModalUI(documentType);
            
            Swal.fire({
                title: 'Success!',
                text: 'Document marked as bad.',
                icon: 'success',
                showConfirmButton: true,
                allowOutsideClick: false
            });
        }
    });
}

// Save document status to server
function saveRenewalDocumentStatus(documentType, status) {
    fetch('/lydo_staff/save-renewal-document-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            renewal_id: currentRenewalId,
            document_type: documentType,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to save status:', data.message);
            Swal.fire('Error', 'Failed to save document status.', 'error');
        }
    })
    .catch(error => {
        console.error('Error saving status:', error);
        Swal.fire('Error', 'Failed to save document status.', 'error');
    });
}

// Save document comment
function saveRenewalDocumentComment(documentType, comment) {
    // Save to server
    fetch('/lydo_staff/save-renewal-document-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            renewal_id: currentRenewalId,
            document_type: documentType,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to save comment:', data.message);
            Swal.fire('Error', 'Failed to save comment.', 'error');
        } else {
            // Also save to localStorage
            const currentStatus = renewalDocumentStatuses[documentType] || '';
            saveRenewalRatingsToStorage(currentRenewalId, documentType, currentStatus, comment);
            showRenewalAutoSaveIndicator(documentType, true);
        }
    })
    .catch(error => {
        console.error('Error saving comment:', error);
        Swal.fire('Error', 'Failed to save comment.', 'error');
    });
}

// Load existing document comment
function loadRenewalDocumentComment(documentType) {
    // First check localStorage
    const localRatings = loadRenewalRatingsFromStorage(currentRenewalId);
    if (localRatings[documentType] && localRatings[documentType].comment) {
        const textarea = document.getElementById(`comment_${documentType}`);
        if (textarea) {
            textarea.value = localRatings[documentType].comment;
            return; // Use localStorage value and don't fetch from server
        }
    }
    
    // Fallback to server if no localStorage data
    fetch(`/lydo_staff/get-renewal-document-statuses/${currentRenewalId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statuses = data.statuses || {};
                const statusData = statuses[documentType];
                
                const textarea = document.getElementById(`comment_${documentType}`);
                if (textarea && statusData && statusData.comment) {
                    textarea.value = statusData.comment;
                }
            }
        })
        .catch(error => {
            console.error('Error loading renewal document comment:', error);
        });
}

// Check if all documents are rated
function checkAllRenewalDocumentsRated() {
    const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];
    
    if (ratedRenewalDocuments.size === 3) {
        // Count good and bad documents
        let goodCount = 0;
        let badCount = 0;
        
        documentTypes.forEach(docType => {
            if (renewalDocumentStatuses[docType] === 'good') {
                goodCount++;
            } else if (renewalDocumentStatuses[docType] === 'bad') {
                badCount++;
            }
        });
        
        // Update action buttons based on document ratings
        updateRenewalActionButtons(goodCount, badCount);
    } else {
        console.log(`Not all documents rated: ${ratedRenewalDocuments.size}/3`);
    }
}

// Update action buttons based on document ratings
function updateRenewalActionButtons(goodCount, badCount) {
    console.log(`Good: ${goodCount}, Bad: ${badCount}`);
    
    // Show action buttons
    const actionButtons = document.getElementById('actionButtons');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    
    actionButtons.style.display = 'flex';
    
    // NEW LOGIC: If there are bad documents, hide approve button and show reject only
    if (badCount > 0) {
        approveBtn.style.display = 'none';
        rejectBtn.style.display = 'flex';
        console.log('Bad documents found - showing Reject button only');
    } else {
        // If all documents are good, show both buttons
        approveBtn.style.display = 'flex';
        rejectBtn.style.display = 'flex';
        console.log('All documents good - showing both Approve and Reject buttons');
    }
}

// Update document modal UI based on current status
function updateRenewalDocumentModalUI(documentType) {
    const status = renewalDocumentStatuses[documentType];
    
    const goodBtn = document.querySelector(`#documentReviewControls .mark-good-btn[data-document="${documentType}"]`);
    const badBtn = document.querySelector(`#documentReviewControls .mark-bad-btn[data-document="${documentType}"]`);
    const statusIndicator = document.getElementById(`status-indicator-${documentType}`);
    const statusText = document.getElementById(`status-text-${documentType}`);
    
    if (status === 'good') {
        // Document is already marked as good
        if (goodBtn && badBtn) {
            goodBtn.disabled = true;
            goodBtn.classList.add('bg-green-700', 'cursor-not-allowed');
            goodBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
            goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Marked as Good';
            
            badBtn.disabled = false;
            badBtn.classList.remove('bg-red-700', 'cursor-not-allowed');
            badBtn.classList.add('bg-red-500', 'hover:bg-red-600');
            badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Mark as Bad';
        }
        
        if (statusIndicator && statusText) {
            statusIndicator.classList.remove('hidden');
            statusIndicator.className = 'mt-3 text-sm font-medium text-green-600';
            statusText.textContent = 'This document has been marked as Good.';
        }
    } else if (status === 'bad') {
        // Document is already marked as bad
        if (goodBtn && badBtn) {
            badBtn.disabled = true;
            badBtn.classList.add('bg-red-700', 'cursor-not-allowed');
            badBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
            badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Marked as Bad';
            
            goodBtn.disabled = false;
            goodBtn.classList.remove('bg-green-700', 'cursor-not-allowed');
            goodBtn.classList.add('bg-green-500', 'hover:bg-green-600');
            goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Mark as Good';
        }
        
        if (statusIndicator && statusText) {
            statusIndicator.classList.remove('hidden');
            statusIndicator.className = 'mt-3 text-sm font-medium text-red-600';
            statusText.textContent = 'This document has been marked as Bad.';
        }
    } else {
        // Document not rated yet
        if (goodBtn && badBtn) {
            goodBtn.disabled = false;
            badBtn.disabled = false;
            
            goodBtn.classList.remove('bg-green-700', 'cursor-not-allowed');
            goodBtn.classList.add('bg-green-500', 'hover:bg-green-600');
            goodBtn.innerHTML = '<i class="fas fa-check-circle"></i> Mark as Good';
            
            badBtn.classList.remove('bg-red-700', 'cursor-not-allowed');
            badBtn.classList.add('bg-red-500', 'hover:bg-red-600');
            badBtn.innerHTML = '<i class="fas fa-times-circle"></i> Mark as Bad';
        }
        
        if (statusIndicator) {
            statusIndicator.classList.add('hidden');
        }
    }
}

// Show auto-save indicator
function showRenewalAutoSaveIndicator(documentType, success = true) {
    const textarea = document.getElementById(`comment_${documentType}`);
    if (textarea) {
        const originalPlaceholder = textarea.placeholder;
        
        if (success) {
            textarea.placeholder = "✓ Comment saved!";
            setTimeout(() => {
                textarea.placeholder = originalPlaceholder;
            }, 2000);
        } else {
            textarea.placeholder = "Saving...";
            setTimeout(() => {
                textarea.placeholder = originalPlaceholder;
            }, 1000);
        }
    }
}

// Enhanced close function to preserve state
function closeApplicationModal() {
    // Store the current state before closing
    const currentState = {
        renewalDocumentStatuses: {...renewalDocumentStatuses},
        ratedRenewalDocuments: Array.from(ratedRenewalDocuments),
        currentRenewalId: currentRenewalId,
        selectedRenewalId: selectedRenewalId,
        documentUpdateTracker: {...documentUpdateTracker}
    };
    
    // Store in sessionStorage for temporary persistence
    sessionStorage.setItem('renewalModalState', JSON.stringify(currentState));
    
    // Close the modal
    document.getElementById('openRenewalModal').classList.add('hidden');
    document.getElementById('actionButtons').style.display = 'none';
}

// Function to restore modal state when reopened
function restoreModalState() {
    const savedState = sessionStorage.getItem('renewalModalState');
    if (savedState) {
        const state = JSON.parse(savedState);
        
        // Restore the state
        renewalDocumentStatuses = state.renewalDocumentStatuses || {};
        ratedRenewalDocuments = new Set(state.ratedRenewalDocuments || []);
        currentRenewalId = state.currentRenewalId;
        selectedRenewalId = state.selectedRenewalId;
        documentUpdateTracker = state.documentUpdateTracker || {};
        
        // Update UI based on restored state
        const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];
        documentTypes.forEach(docType => {
            if (renewalDocumentStatuses[docType]) {
                let badgeStatus = renewalDocumentStatuses[docType];
                if (documentUpdateTracker[docType] && documentUpdateTracker[docType].hasUpdate) {
                    badgeStatus = 'updated';
                }
                updateRenewalDocumentBadge(docType, badgeStatus);
            }
        });
        
        // Check if all documents are rated
        checkAllRenewalDocumentsRated();
    }
}

// Function to reset update status for a document
function resetDocumentUpdateStatus(documentType) {
    if (documentUpdateTracker[documentType]) {
        documentUpdateTracker[documentType].hasUpdate = false;
        documentUpdateTracker[documentType].isNew = false;
    }
}

// Enhanced function to ensure ratings persist through page refreshes
function initializePersistentRatings() {
    // Load from localStorage on page load
    if (selectedRenewalId) {
        const localRatings = loadRenewalRatingsFromStorage(selectedRenewalId);
        Object.keys(localRatings).forEach(docType => {
            renewalDocumentStatuses[docType] = localRatings[docType].status;
            if (localRatings[docType].status === 'good' || localRatings[docType].status === 'bad') {
                ratedRenewalDocuments.add(docType);
            }
        });
        checkAllRenewalDocumentsRated();
    }
}

// NEW FUNCTIONS FOR BAD DOCUMENT HANDLING

// Function to show update request button for bad documents
function showUpdateRequestButton(documentType, renewalId) {
    const hasBadDocument = renewalDocumentStatuses[documentType] === 'bad';
    
    if (hasBadDocument) {
        // Add update request button to the document viewer
        const reviewControls = document.getElementById('documentReviewControls');
        const existingUpdateBtn = document.getElementById(`updateRequestBtn-${documentType}`);
        
        if (!existingUpdateBtn) {
            const updateBtn = document.createElement('button');
            updateBtn.id = `updateRequestBtn-${documentType}`;
            updateBtn.className = 'mt-3 w-full bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors duration-200 flex items-center justify-center gap-2';
            updateBtn.innerHTML = `
                <i class="fas fa-sync-alt"></i>
                Request Document Update from Applicant
            `;
            updateBtn.onclick = () => openUpdateRequestModal(documentType, renewalId);
            
            reviewControls.appendChild(updateBtn);
        }
    }
}

// Open modal to request document update
function openUpdateRequestModal(documentType, renewalId) {
    const documentNames = {
        'cert_of_reg': 'Certificate of Registration',
        'grade_slip': 'Grade Slip',
        'brgy_indigency': 'Barangay Indigency'
    };

    Swal.fire({
        title: 'Request Document Update',
        html: `
            <div class="text-left">
                <p class="mb-4 text-gray-600">You are requesting an update for: <strong>${documentNames[documentType]}</strong></p>
                <textarea id="updateComment" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="4" placeholder="Enter comments for the applicant explaining what needs to be updated..."></textarea>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Send Update Request',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const comment = document.getElementById('updateComment').value;
            if (!comment) {
                Swal.showValidationMessage('Please enter comments for the applicant');
                return false;
            }
            return { comment: comment };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            sendUpdateRequest(documentType, renewalId, result.value.comment);
        }
    });
}

// Send update request to server
function sendUpdateRequest(documentType, renewalId, comment) {
    fetch(`/lydo_staff/request-document-update/${renewalId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            document_type: documentType,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Update request sent to applicant',
                icon: 'success',
                confirmButtonColor: '#10b981'
            });
            
            // Update the document status to show it's been actioned
            renewalDocumentStatuses[documentType] = 'bad';
            updateRenewalDocumentBadge(documentType, 'bad');
            
            // Hide approve button if there are bad documents
            checkAllRenewalDocumentsRated();
        } else {
            Swal.fire('Error', data.message || 'Failed to send update request', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending update request:', error);
        Swal.fire('Error', 'Failed to send update request', 'error');
    });
}

// Load document comments
function loadDocumentComments(renewalId) {
    fetch(`/lydo_staff/get-document-comments/${renewalId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store comments for later use
                window.documentComments = data.comments;
                window.documentStatuses = data.statuses;
                
                // Show update buttons for bad documents
                Object.keys(data.statuses).forEach(docType => {
                    if (data.statuses[docType] === 'bad') {
                        showUpdateRequestButton(docType, renewalId);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading document comments:', error);
        });
}

// Enhanced function to handle document status changes
function handleDocumentStatusChange(documentType, status) {
    if (status === 'bad') {
        // Show the update request button
        showUpdateRequestButton(documentType, currentRenewalId);
        
        // Hide approve button when there are bad documents
        const approveBtn = document.getElementById('approveBtn');
        if (approveBtn) {
            approveBtn.style.display = 'none';
        }
    }
    
    // Update action buttons based on all document statuses
    checkAllRenewalDocumentsRated();
}

// Initialize everything when the page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeDocumentUpdateTracker();
    restoreModalState();
    initializePersistentRatings();
    
    // Override the openRenewalModal to ensure ratings persist
    const originalOpenRenewalModal = window.openRenewalModal;
    window.openRenewalModal = function(scholarId) {
        restoreModalState();
        originalOpenRenewalModal(scholarId);
        
        // Load document comments for this renewal
        if (selectedRenewalId) {
            loadDocumentComments(selectedRenewalId);
        }
    };
});

// Tab switching functions
function showTable() {
    document.getElementById('tableView').classList.remove('hidden');
    document.getElementById('listView').classList.add('hidden');
    document.getElementById('tab-renewal').classList.add('active');
    document.getElementById('tab-review').classList.remove('active');
}

function showList() {
    document.getElementById('tableView').classList.add('hidden');
    document.getElementById('listView').classList.remove('hidden');
    document.getElementById('tab-renewal').classList.remove('active');
    document.getElementById('tab-review').classList.add('active');
}

// Filter functions
function clearFiltersTable() {
    document.getElementById('nameSearch').value = '';
    document.getElementById('barangayFilter').value = '';
    // Add code to reload table data without filters
}

function clearFiltersList() {
    document.getElementById('listNameSearch').value = '';
    document.getElementById('listBarangayFilter').value = '';
    // Add code to reload list data without filters
}

// Update renewal status function
function updateRenewalStatus(renewalId, status) {
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    const approveText = document.getElementById('approveText');
    const rejectText = document.getElementById('rejectText');
    const approveSpinner = document.getElementById('approveSpinner');
    const rejectSpinner = document.getElementById('rejectSpinner');

    // Show loading state
    if (status === 'Approved') {
        approveText.classList.add('hidden');
        approveSpinner.classList.remove('hidden');
        approveBtn.disabled = true;
    } else {
        rejectText.classList.add('hidden');
        rejectSpinner.classList.remove('hidden');
        rejectBtn.disabled = true;
    }

    let reason = null;
    if (status === 'Rejected') {
        Swal.fire({
            title: 'Reason for Rejection',
            input: 'textarea',
            inputLabel: 'Please provide a reason for rejection:',
            inputPlaceholder: 'Enter reason here...',
            inputAttributes: {
                'aria-label': 'Enter reason for rejection'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit Rejection',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please provide a reason for rejection!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                reason = result.value;
                submitStatusUpdate(renewalId, status, reason);
            } else {
                // Reset button states if cancelled
                resetButtonStates();
            }
        });
    } else {
        submitStatusUpdate(renewalId, status, reason);
    }
}

function submitStatusUpdate(renewalId, status, reason) {
    fetch(`/lydo_staff/update-renewal-status/${renewalId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            renewal_status: status,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: `Renewal ${status.toLowerCase()} successfully.`,
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => {
                closeApplicationModal();
                location.reload(); // Reload to update the lists
            });
        } else {
            Swal.fire('Error', 'Failed to update renewal status', 'error');
            resetButtonStates();
        }
    })
    .catch(error => {
        console.error('Error updating renewal status:', error);
        Swal.fire('Error', 'Failed to update renewal status', 'error');
        resetButtonStates();
    });
}

function resetButtonStates() {
    const approveText = document.getElementById('approveText');
    const rejectText = document.getElementById('rejectText');
    const approveSpinner = document.getElementById('approveSpinner');
    const rejectSpinner = document.getElementById('rejectSpinner');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');

    approveText.classList.remove('hidden');
    rejectText.classList.remove('hidden');
    approveSpinner.classList.add('hidden');
    rejectSpinner.classList.add('hidden');
    approveBtn.disabled = false;
    rejectBtn.disabled = false;
}

// Edit renewal modal functions
function openEditRenewalModal(scholarId, currentStatus) {
    document.getElementById('editScholarId').value = scholarId;
    document.getElementById('editRenewalStatus').value = currentStatus;
    document.getElementById('editRenewalModal').classList.remove('hidden');
}

function closeEditRenewalModal() {
    document.getElementById('editRenewalModal').classList.add('hidden');
}

function saveEditRenewalStatus() {
    const scholarId = document.getElementById('editScholarId').value;
    const status = document.getElementById('editRenewalStatus').value;

    fetch(`/lydo_staff/update-renewal-status/${scholarId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            renewal_status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Renewal status updated successfully.',
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => {
                closeEditRenewalModal();
                location.reload();
            });
        } else {
            Swal.fire('Error', 'Failed to update renewal status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating renewal status:', error);
        Swal.fire('Error', 'Failed to update renewal status', 'error');
    });
}

// View renewal modal functions
function openViewRenewalModal(scholarId) {
    const contentDiv = document.getElementById('viewRenewalContent');
    contentDiv.innerHTML = '';

    if (window.renewals[scholarId]) {
        window.renewals[scholarId].forEach((r, index) => {
            const statusBadge = r.renewal_status === 'Approved'
                ? 'bg-green-100 text-green-700'
                : r.renewal_status === 'Rejected'
                ? 'bg-red-100 text-red-700'
                : 'bg-yellow-100 text-yellow-700';

            contentDiv.innerHTML += `
                <div class="border border-gray-200 rounded-xl shadow bg-white p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div class="space-y-1 text-gray-700 text-sm">
                            <p><strong>Semester:</strong> ${r.renewal_semester}</p>
                            <p><strong>Academic Year:</strong> ${r.renewal_acad_year}</p>
                            <p><strong>Date Submitted:</strong> ${r.date_submitted}</p>
                        </div>
                        <span class="mt-3 md:mt-0 px-4 py-1 text-sm font-semibold rounded-full ${statusBadge}">
                            ${r.renewal_status}
                        </span>
                    </div>

                    <hr class="my-4">

                    <h4 class="text-gray-800 font-semibold mb-3">Submitted Documents</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_cert_of_reg}', 'Certificate of Registration', 'cert_of_reg', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-blue-50 transition w-full">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Certificate of Reg.</span>
                            </button>
                        </div>
                        <div>
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_grade_slip}', 'Grade Slip', 'grade_slip', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-green-50 transition w-full">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Grade Slip</span>
                            </button>
                        </div>
                        <div>
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_brgy_indigency}', 'Barangay Indigency', 'brgy_indigency', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-purple-50 transition w-full">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Barangay Indigency</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        contentDiv.innerHTML = `<p class="text-gray-500">No renewal requirements found for this scholar.</p>`;
    }

    document.getElementById('viewRenewalModal').classList.remove('hidden');
}

function closeViewRenewalModal() {
    document.getElementById('viewRenewalModal').classList.add('hidden');
}

// Notification dropdown
document.addEventListener('DOMContentLoaded', function() {
    const notifBell = document.getElementById('notifBell');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifBell && notifDropdown) {
        notifBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            notifDropdown.classList.add('hidden');
        });
    }
});