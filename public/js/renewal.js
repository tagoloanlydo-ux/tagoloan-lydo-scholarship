// Global variables for renewal document rating
let currentRenewalId = null;
let currentDocumentType = null;
let ratedRenewalDocuments = new Set();
let renewalDocumentStatuses = {};
let selectedRenewalId = null;

// Track document updates and show new/updated badges
let documentUpdateTracker = {};

function saveRenewalRatingsToStorage(renewalId, documentType, status, reason = '') {
    const storageKey = `renewal_ratings_${renewalId}`;
    let ratings = JSON.parse(localStorage.getItem(storageKey)) || {};
    
    ratings[documentType] = {
        status: status,
        reason: reason,
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
            isNew: false
        };
    });
}

// Open document viewer with rating controls
function openRenewalDocumentViewer(documentUrl, title, documentType, renewalId) {
    currentDocumentType = documentType;
    currentRenewalId = renewalId;
    
    document.getElementById('documentTitle').textContent = title;
    document.getElementById('documentViewer').src = documentUrl;
    document.getElementById('documentLoading').style.display = 'flex';
    document.getElementById('documentViewerModal').classList.remove('hidden');

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
        
        // Load comments
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

// Enhanced update document badge based on status
function updateRenewalDocumentBadge(documentType, status) {
    const badge = document.getElementById(`badge-${documentType}`);
    const icon = document.getElementById(`icon-${documentType}`);
    
    // Normalize status to handle different casing and convert "New" to "updated"
    const normalized = (status || '').toString().toLowerCase();
    const displayStatus = normalized === 'new' ? 'updated' : normalized;

    // Reset all styles first
    badge.classList.remove('badge-new', 'badge-good', 'badge-bad', 'badge-updated', 'hidden');
    icon.classList.remove('text-red-600', 'text-green-600', 'text-gray-500', 'text-purple-600', 'text-orange-500');
    
    // Apply new status
    if (displayStatus === 'good') {
        badge.classList.add('badge-good');
        badge.textContent = '✓';
        icon.classList.add('text-green-600');
        badge.classList.remove('hidden');
    } else if (displayStatus === 'bad') {
        badge.classList.add('badge-bad');
        badge.textContent = '✗';
        icon.classList.add('text-red-600');
        badge.classList.remove('hidden');
    } else if (displayStatus === 'updated') {
        // Unified "Updated" badge for server-side 'New'/'Updated' and local 'updated' flag
        badge.classList.add('badge-updated');
        badge.textContent = 'Updated';
        icon.classList.add('text-orange-500');
        badge.classList.remove('hidden');
    } else {
        // No status, hide the badge
        badge.classList.add('hidden');
        icon.classList.add('text-purple-600');
    }
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
                    
                    // Normalize server/local status to a consistent form
                    // Convert "New" to "updated" for badge display
                    const normalizedStatus = status ? status.toString().trim() : null;
                    let badgeStatus = normalizedStatus;
                    if (normalizedStatus && ['new', 'updated'].includes(normalizedStatus.toLowerCase())) {
                        badgeStatus = 'updated';
                    }
                    
                    renewalDocumentStatuses[docType] = normalizedStatus;
                    
                    // Initialize tracking if not exists
                    if (!documentUpdateTracker[docType]) {
                        documentUpdateTracker[docType] = {
                            lastStatus: normalizedStatus,
                            hasUpdate: false,
                            isNew: false
                        };
                    } else {
                        // Check if this is an update from bad to good
                        if ((documentUpdateTracker[docType].lastStatus || '').toLowerCase() === 'bad' && (normalizedStatus || '').toLowerCase() === 'good') {
                            documentUpdateTracker[docType].hasUpdate = true;
                            documentUpdateTracker[docType].lastStatus = normalizedStatus;
                        }
                    }
                    
                    // Decide badge state: treat 'new' or 'updated' as 'updated'
                    let finalBadgeStatus = badgeStatus;
                    if (documentUpdateTracker[docType] && documentUpdateTracker[docType].hasUpdate) {
                        finalBadgeStatus = 'updated';
                    } else if (normalizedStatus && ['new', 'updated'].includes(normalizedStatus.toLowerCase())) {
                        finalBadgeStatus = 'updated';
                    }
                    
                    updateRenewalDocumentBadge(docType, finalBadgeStatus);
                    
                    // If document has a status, consider it as rated
                    if (normalizedStatus && ['good', 'bad'].includes(normalizedStatus.toLowerCase())) {
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
        input: 'textarea',
        inputLabel: 'Reason for marking as bad:',
        inputPlaceholder: 'Please explain why this document is bad...',
        inputAttributes: {
            'aria-label': 'Enter reason for marking document as bad'
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Mark as Bad',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) {
                return 'Please provide a reason for marking this document as bad'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const comment = result.value;
            
            // Save status as bad
            saveRenewalDocumentStatus(documentType, 'bad', comment);
            
            // IMPORTANT: Also save the comment to the database
            saveRenewalDocumentComment(documentType, comment);
            
            // Update tracking
            ratedRenewalDocuments.add(documentType);
            renewalDocumentStatuses[documentType] = 'bad';
            
            // Update badge
            updateRenewalDocumentBadge(documentType, 'bad');
            
            // Update modal UI
            updateRenewalDocumentModalUI(documentType);
            
            // Check if all documents are rated
            checkAllRenewalDocumentsRated();
            
            // Close document viewer
            closeDocumentViewerModal();
            
            // Show success
            Swal.fire({
                icon: 'success',
                title: 'Marked as Bad',
                text: 'Document marked as bad and comment saved.',
                timer: 1500
            });
        }
    });
}

// Save document status to server
function saveRenewalDocumentStatus(documentType, status, reason = '') {
    fetch('/lydo_staff/save-renewal-document-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
            renewal_id: currentRenewalId,
            document_type: documentType,
            status: status,
            reason: reason
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

// add helper to get CSRF token from meta
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.getAttribute('content');
    // fallback: try to read existing form token if present
    const input = document.querySelector('input[name="_token"]');
    return input ? input.value : '';
}

// In saveRenewalDocumentComment function - UPDATE THIS:
function saveRenewalDocumentComment(documentType, comment) {
    console.log('Saving comment for:', documentType, 'Comment:', comment);
    
    // Save to server
    fetch('/lydo_staff/save-renewal-document-comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
            renewal_id: currentRenewalId,
            document_type: documentType,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Save comment response:', data);
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

// In loadDocumentComments function - ADD DEBUG LOGGING:
function loadDocumentComments(renewalId) {
    console.log('Loading comments for renewal:', renewalId);
    
    fetch(`/lydo_staff/get-document-comments/${renewalId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Comments response:', data);
            if (data.success) {
                // Store comments for later use
                window.documentComments = data.comments;
                window.documentStatuses = data.statuses;
                
                // Log what we received
                console.log('Received comments:', data.comments);
                console.log('Received statuses:', data.statuses);
            }
        })
        .catch(error => {
            console.error('Error loading document comments:', error);
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
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');

    actionButtons.style.display = 'flex';

    // NEW LOGIC: If there are bad documents, show send email button and reject only
    if (badCount > 0) {
        sendEmailBtn.style.display = 'flex';
        approveBtn.style.display = 'none';
        rejectBtn.style.display = 'flex';
        console.log('Bad documents found - showing Send Email and Reject buttons');
    } else {
        // If all documents are good, show approve and reject buttons
        sendEmailBtn.style.display = 'none';
        approveBtn.style.display = 'flex';
        rejectBtn.style.display = 'flex';
        console.log('All documents good - showing Approve and Reject buttons');
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
    } else if (status === 'New') {
        // Document is new
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

// Load document comments
function loadDocumentComments(renewalId) {
    fetch(`/lydo_staff/get-document-comments/${renewalId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store comments for later use
                window.documentComments = data.comments;
                window.documentStatuses = data.statuses;
            }
        })
        .catch(error => {
            console.error('Error loading document comments:', error);
        });
}

// Enhanced function to handle document status changes
function handleDocumentStatusChange(documentType, status) {
    // Hide approve button when there are bad documents
    if (status === 'bad') {
        const approveBtn = document.getElementById('approveBtn');
        if (approveBtn) {
            approveBtn.style.display = 'none';
        }
    }
    
    // Update action buttons based on all document statuses
    checkAllRenewalDocumentsRated();
}

function sendEmailForBadDocuments() {
    // Show loading
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const sendEmailText = document.getElementById('sendEmailText');
    const sendEmailSpinner = document.getElementById('sendEmailSpinner');

    sendEmailText.classList.add('hidden');
    sendEmailSpinner.classList.remove('hidden');
    sendEmailBtn.disabled = true;

    // Get bad documents
    const badDocuments = [];
    const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];

    documentTypes.forEach(docType => {
        if (renewalDocumentStatuses[docType] === 'bad') {
            badDocuments.push(docType);
        }
    });

    if (badDocuments.length === 0) {
        Swal.fire('Error', 'No documents marked as bad to send correction request.', 'error');
        sendEmailText.classList.remove('hidden');
        sendEmailSpinner.classList.add('hidden');
        sendEmailBtn.disabled = false;
        return;
    }

    console.log('Sending email for bad documents:', badDocuments);
    console.log('Renewal ID:', selectedRenewalId);

    // Send request
    fetch('/lydo_staff/send-email-for-bad-documents', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            renewal_id: selectedRenewalId,
            bad_documents: badDocuments
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Email response:', data);
        if (data.success) {
            Swal.fire({
                title: 'Correction Request Sent! 📧',
                text: 'Scholar has been notified about the required document corrections.',
                icon: 'success',
                confirmButtonColor: '#10b981',
                timer: 4000
            });
        } else {
            Swal.fire('Error', data.message || 'Failed to send correction request', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending correction email:', error);
        Swal.fire('Error', 'Failed to send correction request. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button
        sendEmailText.classList.remove('hidden');
        sendEmailSpinner.classList.add('hidden');
        sendEmailBtn.disabled = false;
    });
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
            'X-CSRF-TOKEN': getCsrfToken()
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
            'X-CSRF-TOKEN': getCsrfToken()
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