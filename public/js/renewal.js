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
    // IMPORTANT: Ensure renewalId is passed and set
    if (!renewalId) {
        console.error('renewalId is required but not provided');
        Swal.fire('Error', 'Renewal ID is missing. Please reload and try again.', 'error');
        return;
    }

    currentDocumentType = documentType;
    currentRenewalId = renewalId;  // Set this explicitly
    selectedRenewalId = renewalId; // Also update selectedRenewalId as backup
    
    console.log('Opening document viewer:', {
        documentType: documentType,
        renewalId: renewalId,
        currentRenewalId: currentRenewalId
    });
    
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



// Fallback function using only localStorage
function fallbackToLocalStorage(renewalId) {
    const localRatings = loadRenewalRatingsFromStorage(renewalId);
    const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];
    
    documentTypes.forEach(docType => {
        if (localRatings[docType]) {
            const status = localRatings[docType].status;
            renewalDocumentStatuses[docType] = status;
            if (['good', 'bad'].includes(status)) {
                ratedRenewalDocuments.add(docType);
            }
            updateRenewalDocumentBadge(docType, status);
        } else {
            updateRenewalDocumentBadge(docType, null);
        }
    });
    
    checkAllRenewalDocumentsRated();
}

// Open renewal modal with document rating functionality - SHOW ONLY PENDING RENEWALS
function openRenewalModal(scholarId) {
    const contentDiv = document.getElementById('applicationContent');
    contentDiv.innerHTML = '';

    // Reset tracking
    window.documentsClicked = 0;
    ratedRenewalDocuments = new Set();
    renewalDocumentStatuses = {};
    document.getElementById('actionButtons').style.display = 'none';

    if (window.renewals[scholarId]) {
        // FILTER: Get only PENDING renewals
        const pendingRenewals = window.renewals[scholarId].filter(r => r.renewal_status === 'Pending');
        
        if (pendingRenewals.length === 0) {
            contentDiv.innerHTML = `<p class="text-gray-500">No pending renewals found for this scholar.</p>`;
            document.getElementById('openRenewalModal').classList.remove('hidden');
            return;
        }

        selectedRenewalId = pendingRenewals[0].renewal_id; // latest pending renewal
        currentRenewalId = selectedRenewalId;

        // Force refresh from database
        refreshDocumentStatuses(selectedRenewalId);

        // DISPLAY ONLY PENDING RENEWALS - EACH WITH ITS OWN ACADEMIC YEAR AND SEMESTER
        pendingRenewals.forEach((r, index) => {
            const statusBadge = 'bg-yellow-100 text-yellow-700'; // Always yellow for pending

            contentDiv.innerHTML += `
                <div class="border border-gray-200 rounded-xl shadow bg-white p-6 mb-6">
                    
                    <!-- Top Info - CLEARLY SHOW ACADEMIC YEAR AND SEMESTER -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div class="space-y-1 text-gray-700 text-sm">
                            <p class="font-semibold text-lg text-blue-600">${r.renewal_acad_year} - ${r.renewal_semester}</p>
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
    
    if (!badge || !icon) {
        console.log(`Elements not found for: ${documentType}`);
        return;
    }

    // Normalize status
    const normalized = status ? status.toString().toLowerCase() : '';
    console.log(`Updating badge for ${documentType} to:`, normalized);

    // Reset all styles first
    badge.classList.remove('badge-new', 'badge-good', 'badge-bad', 'badge-updated', 'hidden');
    icon.classList.remove('text-red-600', 'text-green-600', 'text-gray-500', 'text-purple-600', 'text-orange-500');
    
    // Apply new status based on database value
    if (normalized === 'good') {
        badge.classList.add('badge-good');
        badge.textContent = '✓';
        icon.classList.add('text-green-600');
        badge.classList.remove('hidden');
        console.log(`✓ Set ${documentType} to GOOD`);
    } else if (normalized === 'bad') {
        badge.classList.add('badge-bad');
        badge.textContent = '✗';
        icon.classList.add('text-red-600');
        badge.classList.remove('hidden');
        console.log(`✗ Set ${documentType} to BAD`);
    } else if (normalized === 'new' || normalized === 'updated') {
        badge.classList.add('badge-updated');
        badge.textContent = 'Updated';
        badge.style.width = 'auto';
        badge.style.padding = '2px 6px';
        badge.style.borderRadius = '8px';
        badge.style.fontSize = '0.7rem';
        badge.style.top = '-8px';
        badge.style.right = '-8px';
        icon.classList.add('text-orange-500');
        badge.classList.remove('hidden');
        console.log(`↻ Set ${documentType} to UPDATED`);
    } else {
        // No status, hide the badge
        badge.classList.add('hidden');
        icon.classList.add('text-purple-600');
        console.log(`- Set ${documentType} to NO STATUS (hidden)`);
    }
}

function checkForNewDocumentStatus() {
    const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];
    const actionButtons = document.getElementById('actionButtons');
    
    let hasNewStatus = false;
    
    // Check each document type for "New" status
    documentTypes.forEach(docType => {
        const status = renewalDocumentStatuses[docType];
        if (status && status.toString().toLowerCase() === 'new') {
            hasNewStatus = true;
            console.log(`Found NEW status for: ${docType}`);
        }
    });
    
    // DON'T hide action buttons completely - just show appropriate ones
    if (hasNewStatus) {
        console.log('Documents have NEW status - showing appropriate buttons');
        // Let checkAllRenewalDocumentsRated handle which buttons to show
    } else {
        console.log('No NEW document status found');
    }
}

// Enhanced loadRenewalDocumentStatuses function with NEW status check
function loadRenewalDocumentStatuses(renewalId) {
    console.log('Loading document statuses for renewal:', renewalId);
    
    fetch(`/lydo_staff/get-renewal-document-statuses/${renewalId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statuses = data.statuses || {};
                
                console.log('DEBUG - Server statuses from database:', statuses);

                // Document type mappings to database columns
                const documentMappings = {
                    'cert_of_reg': 'cert_of_reg_status',
                    'grade_slip': 'grade_slip_status', 
                    'brgy_indigency': 'brgy_indigency_status'
                };

                // Clear existing ratings to start fresh from database
                ratedRenewalDocuments.clear();
                renewalDocumentStatuses = {};

                Object.keys(documentMappings).forEach(docType => {
                    const dbColumnName = documentMappings[docType];
                    let dbStatus = statuses[dbColumnName];
                    
                    console.log(`DEBUG - ${docType} (${dbColumnName}):`, dbStatus);

                    // Use database status as primary source
                    if (dbStatus) {
                        // Normalize the status
                        const normalizedStatus = dbStatus.toString().trim().toLowerCase();
                        
                        // Store in our tracking objects
                        renewalDocumentStatuses[docType] = normalizedStatus;
                        
                        // If document has a valid status, mark it as rated
                        // EXCLUDE "new" status from being considered as "rated"
                        if (['good', 'bad'].includes(normalizedStatus)) {
                            ratedRenewalDocuments.add(docType);
                        }
                        
                        // Update badge based on database value
                        let badgeStatus = normalizedStatus;
                        if (normalizedStatus === 'new') {
                            badgeStatus = 'updated'; // Show "Updated" for new documents
                        }
                        
                        console.log(`Setting badge for ${docType} to:`, badgeStatus);
                        updateRenewalDocumentBadge(docType, badgeStatus);
                    } else {
                        // No status in database, check localStorage as fallback
                        const localRatings = loadRenewalRatingsFromStorage(renewalId);
                        if (localRatings[docType]) {
                            const localStatus = localRatings[docType].status;
                            renewalDocumentStatuses[docType] = localStatus;
                            if (['good', 'bad'].includes(localStatus)) {
                                ratedRenewalDocuments.add(docType);
                            }
                            updateRenewalDocumentBadge(docType, localStatus);
                        } else {
                            // No status anywhere, show default
                            updateRenewalDocumentBadge(docType, null);
                        }
                    }
                });
                
                // Check if all documents are rated (this will handle NEW status properly)
                checkAllRenewalDocumentsRated();
                
                console.log('Final document statuses:', renewalDocumentStatuses);
                console.log('Rated documents:', Array.from(ratedRenewalDocuments));
            }
        })
        .catch(error => {
            console.error('Error loading renewal document statuses:', error);
            // Fallback to localStorage only if server fails
            fallbackToLocalStorage(renewalId);
        });
}

// Enhanced refreshDocumentStatuses function
function refreshDocumentStatuses(renewalId) {
    console.log('Force refreshing document statuses for:', renewalId);
    // Clear localStorage for this renewal to force fresh load from database
    clearRenewalRatingsFromStorage(renewalId);
    // Reload from database
    loadRenewalDocumentStatuses(renewalId);
}
function checkAllRenewalDocumentsRated() {
    const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];

    // Count documents by status
    let goodCount = 0;
    let badCount = 0;
    let newCount = 0;
    let unratedCount = 0;

    documentTypes.forEach(docType => {
        const status = renewalDocumentStatuses[docType] ? renewalDocumentStatuses[docType].toString().toLowerCase() : '';

        if (status === 'good') goodCount++;
        else if (status === 'bad') badCount++;
        else if (status === 'new') newCount++;
        else unratedCount++;
    });

    console.log(`Status counts - Good: ${goodCount}, Bad: ${badCount}, New: ${newCount}, Unrated: ${unratedCount}`);

    const actionButtons = document.getElementById('actionButtons');

    // SHOW BUTTONS if there are bad documents (even with new/unrated documents) OR if all documents are rated
    if (badCount > 0 || (newCount === 0 && goodCount + badCount === 3)) {
        actionButtons.style.display = 'flex';
        updateRenewalActionButtons(goodCount, badCount, newCount);
        console.log('Showing buttons - Bad documents found or all documents rated');
    } else {
        // HIDE BUTTONS if no bad documents and not all documents are rated
        actionButtons.style.display = 'none';
        console.log('Hiding buttons - No bad documents and not all rated');
    }
}


function updateRenewalActionButtons(goodCount, badCount, newCount = 0) {
    console.log(`Good: ${goodCount}, Bad: ${badCount}, New: ${newCount}`);

    const actionButtons = document.getElementById('actionButtons');
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');

    actionButtons.style.display = 'flex';

    const totalDocuments = 3;
    const allGood = goodCount === totalDocuments;
    const hasBadDocuments = badCount > 0;
    const hasNewDocuments = newCount > 0;
    
    // This function should only be called when there are NO NEW documents
    // and at least one document is rated (good or bad)
    
    if (allGood) {
        // ALL DOCUMENTS ARE GOOD - Show ONLY APPROVE button
        sendEmailBtn.style.display = 'none';
        approveBtn.style.display = 'flex';
        rejectBtn.style.display = 'none';
        console.log('All documents good - showing ONLY Approve button');
    } else if (hasBadDocuments) {
        // HAS BAD DOCUMENTS - Show Send Email and Reject buttons
        sendEmailBtn.style.display = 'flex';
        approveBtn.style.display = 'none';
        rejectBtn.style.display = 'flex';
        console.log('Bad documents found - showing Send Email and Reject buttons');
    } else {
        // MIXED STATUS (some good, some unrated) - Show both Approve and Reject buttons
        sendEmailBtn.style.display = 'none';
        approveBtn.style.display = 'flex';
        rejectBtn.style.display = 'flex';
        console.log('Mixed status - showing both Approve and Reject buttons');
    }
}

function updateRenewalStatus(renewalId, status) {
    console.log('Updating renewal status:', { renewalId, status });
    
    if (!renewalId) {
        console.error('No renewal ID provided');
        Swal.fire('Error', 'Renewal ID not found. Please try again.', 'error');
        return;
    }

    // Show loading state
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    const approveText = document.getElementById('approveText');
    const rejectText = document.getElementById('rejectText');
    const approveSpinner = document.getElementById('approveSpinner');
    const rejectSpinner = document.getElementById('rejectSpinner');

    if (status === 'Approved') {
        approveText.textContent = 'Approving...';
        approveSpinner.classList.remove('hidden');
        approveBtn.disabled = true;
    } else {
        rejectText.textContent = 'Rejecting...';
        rejectSpinner.classList.remove('hidden');
        rejectBtn.disabled = true;
    }

    // Prepare request data
    const requestData = {
        renewal_status: status
    };

    // If rejecting, ask for reason
    if (status === 'Rejected') {
        Swal.fire({
            title: 'Reason for Rejection',
            input: 'textarea',
            inputLabel: 'Please provide the reason for rejection:',
            inputPlaceholder: 'Enter reason here...',
            inputAttributes: {
                'aria-label': 'Enter reason for rejection'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit Rejection',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please provide a reason for rejection';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                requestData.reason = result.value;
                submitStatusUpdate(renewalId, requestData, status);
            } else {
                // Reset buttons if cancelled
                resetButtons();
            }
        });
    } else {
        // For approval, submit directly
        submitStatusUpdate(renewalId, requestData, status);
    }
}

// Add this function to handle sending correction request emails
function sendEmailForBadDocuments() {
    console.log('Sending email for bad documents...');
    
    if (!selectedRenewalId) {
        console.error('No renewal ID selected');
        Swal.fire('Error', 'No renewal application selected.', 'error');
        return;
    }

    // Get all bad documents
    const badDocuments = [];
    const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];
    
    documentTypes.forEach(docType => {
        const status = renewalDocumentStatuses[docType];
        if (status && status.toString().toLowerCase() === 'bad') {
            badDocuments.push(docType);
        }
    });

    if (badDocuments.length === 0) {
        Swal.fire('Info', 'No documents marked as bad to send correction request for.', 'info');
        return;
    }

    console.log('Bad documents found:', badDocuments);

    // Show loading state
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const sendEmailText = document.getElementById('sendEmailText');
    const sendEmailSpinner = document.getElementById('sendEmailSpinner');
    
    if (sendEmailText) sendEmailText.textContent = 'Sending...';
    if (sendEmailSpinner) sendEmailSpinner.classList.remove('hidden');
    if (sendEmailBtn) sendEmailBtn.disabled = true;

    // Send request to server
    fetch('/lydo_staff/send-email-for-bad-documents', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
            renewal_id: selectedRenewalId,
            bad_documents: badDocuments
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Email send response:', data);
        
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Correction request email sent successfully.',
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => {
                // Optionally close the modal or refresh
                closeApplicationModal();
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to send email');
        }
    })
    .catch(error => {
        console.error('Error sending correction email:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to send correction request: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    })
    .finally(() => {
        // Reset button state
        if (sendEmailText) sendEmailText.textContent = 'Send Correction Request';
        if (sendEmailSpinner) sendEmailSpinner.classList.add('hidden');
        if (sendEmailBtn) sendEmailBtn.disabled = false;
    });
}

function submitStatusUpdate(renewalId, requestData, status) {
    console.log('Submitting status update:', { renewalId, requestData, status });
    
    fetch(`/lydo_staff/update-renewal-status/${renewalId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: `Renewal ${status.toLowerCase()} successfully.`,
                icon: 'success',
                confirmButtonColor: '#10b981'
            }).then(() => {
                // Close modal and refresh page
                closeApplicationModal();
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error updating renewal status:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to update renewal status: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#ef4444'
        });
    })
    .finally(() => {
        // Reset buttons
        resetButtons();
    });
}

function resetButtons() {
    const approveText = document.getElementById('approveText');
    const rejectText = document.getElementById('rejectText');
    const approveSpinner = document.getElementById('approveSpinner');
    const rejectSpinner = document.getElementById('rejectSpinner');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    
    if (approveText) approveText.textContent = 'Approve';
    if (rejectText) rejectText.textContent = 'Reject';
    if (approveSpinner) approveSpinner.classList.add('hidden');
    if (rejectSpinner) rejectSpinner.classList.add('hidden');
    if (approveBtn) approveBtn.disabled = false;
    if (rejectBtn) rejectBtn.disabled = false;
}

// Enhanced CSRF token function
function getCsrfToken() {
    // Try meta tag first
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        const token = meta.getAttribute('content');
        if (token) return token;
    }
    
    // Try hidden input as fallback
    const input = document.querySelector('input[name="_token"]');
    if (input) return input.value;
    
    console.error('CSRF token not found');
    return '';
}


// Fixed markRenewalDocumentAsGood function - success message stays until OK is clicked
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
            // Check if this document was previously marked as bad or new
            const wasPreviouslyBad = documentUpdateTracker[documentType] && 
                                   documentUpdateTracker[documentType].lastStatus === 'bad';
            const wasNew = renewalDocumentStatuses[documentType] === 'new';
            
            // Update tracking
            if (documentUpdateTracker[documentType]) {
                documentUpdateTracker[documentType].lastStatus = 'good';
                if (wasPreviouslyBad || wasNew) {
                    documentUpdateTracker[documentType].hasUpdate = true;
                }
            }
            
            // Save status to server FIRST
            saveRenewalDocumentStatus(documentType, 'good').then(() => {
                // Save status to localStorage
                const comment = document.getElementById(`comment_${documentType}`)?.value || '';
                saveRenewalRatingsToStorage(currentRenewalId, documentType, 'good', comment);
                
                // Track that this document has been rated (remove from new status)
                ratedRenewalDocuments.add(documentType);
                renewalDocumentStatuses[documentType] = 'good';
                
                // Update the badge - show updated badge if it was previously bad or new
                if (wasPreviouslyBad || wasNew) {
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
                
                // Check if all documents are rated (including NEW status check)
                checkAllRenewalDocumentsRated();
                
                // Update modal UI
                updateRenewalDocumentModalUI(documentType);
                
                // Show success message that stays until OK is clicked
                Swal.fire({
                    title: 'Success!',
                    text: wasPreviouslyBad ? 'Document updated and marked as good!' : 
                          wasNew ? 'New document marked as good!' : 'Document marked as good.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    // This runs only when OK button is clicked
                    if (result.isConfirmed) {
                        // Close document viewer after user clicks OK
                        closeDocumentViewerModal();
                    }
                });
            }).catch(error => {
                console.error('Error saving document status:', error);
                Swal.fire('Error', 'Failed to save document status.', 'error');
            });
        }
    });
}

// Updated markRenewalDocumentAsBad function - success message stays until OK is clicked
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
            
            // Check if this document was previously marked as good or new
            const wasPreviouslyGood = documentUpdateTracker[documentType] && 
                                    documentUpdateTracker[documentType].lastStatus === 'good';
            const wasNew = renewalDocumentStatuses[documentType] === 'new';
            
            // Update tracking
            if (documentUpdateTracker[documentType]) {
                documentUpdateTracker[documentType].lastStatus = 'bad';
                if (wasPreviouslyGood || wasNew) {
                    documentUpdateTracker[documentType].hasUpdate = true;
                }
            }
            
            // Save status as bad
            saveRenewalDocumentStatus(documentType, 'bad', comment).then(() => {
                // IMPORTANT: Also save the comment to the database
                saveRenewalDocumentComment(documentType, comment);
                
                // Update tracking (remove from new status)
                ratedRenewalDocuments.add(documentType);
                renewalDocumentStatuses[documentType] = 'bad';
                
                // Update badge
                updateRenewalDocumentBadge(documentType, 'bad');
                
                // Update modal UI
                updateRenewalDocumentModalUI(documentType);
                
                // Check if all documents are rated (including NEW status check)
                checkAllRenewalDocumentsRated();
                
                // Show success message that stays until OK is clicked
                Swal.fire({
                    icon: 'success',
                    title: 'Marked as Bad',
                    text: wasPreviouslyGood ? 'Document updated and marked as bad!' : 
                          wasNew ? 'New document marked as bad!' : 'Document marked as bad and comment saved.',
                    confirmButtonText: 'OK',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    // This runs only when OK button is clicked
                    if (result.isConfirmed) {
                        // Close document viewer after user clicks OK
                        closeDocumentViewerModal();
                    }
                });
            }).catch(error => {
                console.error('Error saving document status:', error);
                Swal.fire('Error', 'Failed to save document status.', 'error');
            });
        }
    });
}

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

function clearFiltersTable() {
    document.getElementById('nameSearch').value = '';
    document.getElementById('barangayFilter').value = '';
    // Add filter logic here
}

function clearFiltersList() {
    document.getElementById('listNameSearch').value = '';
    document.getElementById('listBarangayFilter').value = '';
    // Add filter logic here
}

// Add this function if missing
function handleDocumentStatusChange(documentType, status) {
    console.log(`Document ${documentType} status changed to: ${status}`);
    // You can add any additional handling here if needed
}

// Updated saveRenewalDocumentStatus function to return Promise
function saveRenewalDocumentStatus(documentType, status, reason = '') {
    return new Promise((resolve, reject) => {
        // Validate that we have a renewal ID
        if (!currentRenewalId || currentRenewalId === null || currentRenewalId === undefined) {
            console.error('currentRenewalId is not set:', currentRenewalId);
            console.error('selectedRenewalId is:', selectedRenewalId);
            
            // Try to use selectedRenewalId as fallback
            if (selectedRenewalId && selectedRenewalId !== null) {
                currentRenewalId = selectedRenewalId;
                console.log('Using selectedRenewalId as fallback:', currentRenewalId);
            } else {
                Swal.fire('Error', 'Renewal ID not found. Please reload the page and try again.', 'error');
                reject(new Error('Renewal ID not found'));
                return;
            }
        }

        console.log('Saving status for renewal:', currentRenewalId, 'Document:', documentType, 'Status:', status);

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
            console.log('Save status response:', data);
            if (!data.success) {
                console.error('Failed to save status:', data.message);
                reject(new Error(data.message || 'Failed to save document status.'));
            } else {
                // Refresh statuses from database after saving
                refreshDocumentStatuses(currentRenewalId);
                resolve(data);
            }
        })
        .catch(error => {
            console.error('Error saving status:', error);
            reject(error);
        });
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
    // Validate renewal ID
    if (!currentRenewalId || currentRenewalId === null || currentRenewalId === undefined) {
        console.error('currentRenewalId is not set');
        if (selectedRenewalId && selectedRenewalId !== null) {
            currentRenewalId = selectedRenewalId;
        } else {
            console.warn('Cannot save comment - no renewal ID available');
            return;
        }
    }

    console.log('Saving comment for renewal:', currentRenewalId, 'Document:', documentType);
    
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


function updateRenewalActionButtons(goodCount, badCount, newCount = 0) {
    console.log(`Good: ${goodCount}, Bad: ${badCount}, New: ${newCount}`);

    const actionButtons = document.getElementById('actionButtons');
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const approveBtn = document.getElementById('approveBtn');
    const rejectBtn = document.getElementById('rejectBtn');

    actionButtons.style.display = 'flex';

    const totalDocuments = 3;
    const allGood = goodCount === totalDocuments;
    const hasBadDocuments = badCount > 0;
    const hasNewDocuments = newCount > 0;
    
    if (allGood) {
        // ALL DOCUMENTS ARE GOOD - Show ONLY APPROVE button
        sendEmailBtn.style.display = 'none';
        approveBtn.style.display = 'flex';
        rejectBtn.style.display = 'none';
        console.log('All documents good - showing ONLY Approve button');
    } else if (hasBadDocuments) {
        // HAS BAD DOCUMENTS - Show Send Email and Reject buttons
        sendEmailBtn.style.display = 'flex';
        approveBtn.style.display = 'none';
        rejectBtn.style.display = 'flex';
        console.log('Bad documents found - showing Send Email and Reject buttons');
    } else if (hasNewDocuments) {
        // HAS NEW DOCUMENTS - Show both buttons but Approve might be disabled
        sendEmailBtn.style.display = 'none';
        approveBtn.style.display = 'flex';
        rejectBtn.style.display = 'flex';
        console.log('New documents found - showing both buttons');
    } else {
        // MIXED or NOT ALL RATED - Show both Approve and Reject buttons
        sendEmailBtn.style.display = 'none';
        approveBtn.style.display = 'flex';
        rejectBtn.style.display = 'flex';
        console.log('Mixed status - showing both Approve and Reject buttons');
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
                // For "New" status, show "Updated" badge
                if (badgeStatus && badgeStatus.toLowerCase() === 'new') {
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
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            if (data.success) {
                const comments = data.comments || {};
                const statuses = data.statuses || {};

                console.log('Comments:', comments);
                console.log('Statuses:', statuses);

                // Document types to check
                const documentTypes = ['application_letter', 'cert_of_reg', 'grade_slip', 'brgy_indigency', 'student_id'];

                // Initialize rated documents tracking
                ratedDocuments = new Set();

                // Store previous status for comparison
                previousDocumentStatus = {};

                documentTypes.forEach(docType => {
                    console.log(`Processing ${docType}:`, comments[docType], statuses[`${docType}_status`]);

                    // Load status
                    const status = statuses[`${docType}_status`];
                    console.log(`Status for ${docType}:`, status);
                    
                    // Store previous status
                    previousDocumentStatus[docType] = status;
                    
                    // Update document badges based on status
                    updateDocumentBadges(docType, status, false);
                    
                    // If document has a status, consider it as rated and opened
                    if (status === 'good' || status === 'bad') {
                        ratedDocuments.add(docType);
                    }
                });
                
                // Check if all documents are already rated
                checkAllDocumentsRated();
            } else {
                console.error('API returned error:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading document comments:', error);
        });
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

function openViewRenewalModal(renewalId, renewalStatus) {
    const contentDiv = document.getElementById('viewRenewalContent');
    contentDiv.innerHTML = '';

    // Find the specific renewal by renewal_id
    let foundRenewal = null;
    
    // Search through all scholars to find the specific renewal
    Object.keys(window.renewals).forEach(scholarId => {
        const scholarRenewals = window.renewals[scholarId];
        const renewal = scholarRenewals.find(r => r.renewal_id == renewalId);
        if (renewal) {
            foundRenewal = renewal;
        }
    });

    if (foundRenewal) {
        const r = foundRenewal;
        const statusBadge = r.renewal_status === 'Approved'
            ? 'bg-green-100 text-green-700'
            : r.renewal_status === 'Rejected'
            ? 'bg-red-100 text-red-700'
            : 'bg-yellow-100 text-yellow-700';

        // Get document statuses from the database
        getRenewalDocumentStatusesForView(renewalId).then(statuses => {
            // Build the content HTML with document status badges
            let contentHTML = `
                <div class="border border-gray-200 rounded-xl shadow bg-white p-6">
                    <!-- Top Info - SHOW ONLY THIS SPECIFIC ACADEMIC YEAR AND SEMESTER -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div class="space-y-1 text-gray-700 text-sm">
                            <p class="font-semibold text-lg text-blue-600">${r.renewal_acad_year} - ${r.renewal_semester}</p>
                            <p><strong>Date Submitted:</strong> ${r.date_submitted}</p>
                            <p><strong>Status:</strong> ${r.renewal_status}</p>
                        </div>
                        <span class="mt-3 md:mt-0 px-4 py-1 text-sm font-semibold rounded-full ${statusBadge}">
                            ${r.renewal_status}
                        </span>
                    </div>`;

            // Add rejection reason section if status is Rejected
            if (r.renewal_status === 'Rejected' && r.rejection_reason) {
                contentHTML += `
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h5 class="font-semibold text-red-800 flex items-center gap-2 mb-2">
                            <i class="fas fa-exclamation-circle"></i>
                            Reason for Rejection
                        </h5>
                        <p class="text-red-700 text-sm">${r.rejection_reason}</p>
                    </div>`;
            }

            contentHTML += `
                    <hr class="my-4">

                    <h4 class="text-gray-800 font-semibold mb-3">Submitted Documents</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="document-item-wrapper">
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_cert_of_reg}', 'Certificate of Registration - ${r.renewal_acad_year} ${r.renewal_semester}', 'cert_of_reg', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-blue-50 transition w-full relative">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2" id="view-icon-cert_of_reg"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Certificate of Reg.</span>
                                <div class="document-status-badge hidden absolute -top-2 -right-2" id="view-badge-cert_of_reg"></div>
                            </button>
                        </div>
                        <div class="document-item-wrapper">
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_grade_slip}', 'Grade Slip - ${r.renewal_acad_year} ${r.renewal_semester}', 'grade_slip', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-green-50 transition w-full relative">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2" id="view-icon-grade_slip"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Grade Slip</span>
                                <div class="document-status-badge hidden absolute -top-2 -right-2" id="view-badge-grade_slip"></div>
                            </button>
                        </div>
                        <div class="document-item-wrapper">
                            <button onclick="openRenewalDocumentViewer('/storage/${r.renewal_brgy_indigency}', 'Barangay Indigency - ${r.renewal_acad_year} ${r.renewal_semester}', 'brgy_indigency', ${r.renewal_id})"
                                   class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 hover:bg-purple-50 transition w-full relative">
                                <i class="fas fa-file-alt text-violet-600 text-2xl mb-2" id="view-icon-brgy_indigency"></i>
                                <span class="text-sm font-medium text-gray-700 text-center">Barangay Indigency</span>
                                <div class="document-status-badge hidden absolute -top-2 -right-2" id="view-badge-brgy_indigency"></div>
                            </button>
                        </div>
                    </div>
                </div>`;

            contentDiv.innerHTML = contentHTML;

            // Update the badges with the retrieved statuses
            updateViewRenewalDocumentBadges(statuses);
        }).catch(error => {
            console.error('Error loading document statuses:', error);
            // Fallback without status badges
            contentDiv.innerHTML = `<p class="text-gray-500">Error loading document statuses.</p>`;
        });
    } else {
        contentDiv.innerHTML = `<p class="text-gray-500">Renewal details not found.</p>`;
    }

    document.getElementById('viewRenewalModal').classList.remove('hidden');
}

// Function to get document statuses for view modal
function getRenewalDocumentStatusesForView(renewalId) {
    return new Promise((resolve, reject) => {
        fetch(`/lydo_staff/get-renewal-document-statuses/${renewalId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resolve(data.statuses);
                } else {
                    reject(new Error(data.message || 'Failed to load document statuses'));
                }
            })
            .catch(error => reject(error));
    });
}

// Function to update document badges in view modal
function updateViewRenewalDocumentBadges(statuses) {
    const documentTypes = ['cert_of_reg', 'grade_slip', 'brgy_indigency'];
    
    documentTypes.forEach(docType => {
        const badge = document.getElementById(`view-badge-${docType}`);
        const icon = document.getElementById(`view-icon-${docType}`);
        
        if (!badge || !icon) return;

        // Get status from the database response
        const dbStatus = statuses[`${docType}_status`];
        const normalized = dbStatus ? dbStatus.toString().toLowerCase() : '';

        // Reset all styles first
        badge.classList.remove('badge-new', 'badge-good', 'badge-bad', 'badge-updated', 'hidden');
        icon.classList.remove('text-red-600', 'text-green-600', 'text-gray-500', 'text-purple-600', 'text-orange-500');
        
        // Apply new status based on database value
        if (normalized === 'good') {
            badge.classList.add('badge-good');
            badge.textContent = '✓';
            icon.classList.add('text-green-600');
            badge.classList.remove('hidden');
        } else if (normalized === 'bad') {
            badge.classList.add('badge-bad');
            badge.textContent = '✗';
            icon.classList.add('text-red-600');
            badge.classList.remove('hidden');
        } else if (normalized === 'new' || normalized === 'updated') {
            badge.classList.add('badge-updated');
            badge.textContent = 'Updated';
            badge.style.width = 'auto';
            badge.style.padding = '2px 6px';
            badge.style.borderRadius = '8px';
            badge.style.fontSize = '0.7rem';
            icon.classList.add('text-orange-500');
            badge.classList.remove('hidden');
        } else {
            // No status, hide the badge
            badge.classList.add('hidden');
            icon.classList.add('text-purple-600');
        }
    });
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