// Global variables for applicants page
let currentApplicantId = null;
let currentApplicantName = null;
let currentDocumentUrls = {};

// Pagination and data variables
let currentPageMayor = 1;
let currentPageLydo = 1;
const itemsPerPage = 15;
let allMayorApplicants = [];
let allLydoApplicants = [];
let filteredMayorApplicants = [];
let filteredLydoApplicants = [];

// Document titles mapping
const documentTitles = {
    application_letter: 'Application Letter',
    cert_reg: 'Certificate of Registration',
    grade_slip: 'Grade Slip',
    brgy_indigency: 'Barangay Indigency',
    student_id: 'Student ID'
};

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Applicants page loaded - initializing...');
    initializeApplicantsData();
    initializeApplicantsModalEvents();
    initializeTabEvents();
    
    // Hide loading spinner when page is fully loaded
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        setTimeout(() => {
            loadingOverlay.classList.add('fade-out');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 1000);
        }, 2000);
    }
});

// Initialize applicants data
function initializeApplicantsData() {
    console.log('Initializing applicants data...');
    // Data will be loaded when tabs are clicked
}

// Initialize modal events for applicants
function initializeApplicantsModalEvents() {
    // Close modal when clicking X button
    const modalCloseBtn = document.querySelector('#applicationHistoryModal .modal-close');
    if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', closeApplicationModal);
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('applicationHistoryModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeApplicationModal();
            }
        });
    }
}

// Initialize tab events
function initializeTabEvents() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to current button and content
            button.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            // Load data for the active tab
            if (tabId === 'mayor-applicants') {
                loadMayorApplicants();
            } else if (tabId === 'lydo-reviewed') {
                loadLydoReviewedApplicants();
            }
        });
    });
}

// Modal functions for applicants
function closeApplicationModal() {
    const modal = document.getElementById('applicationHistoryModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    currentApplicantId = null;
    currentApplicantName = null;
    
    // Close all document tabs when modal closes
    closeAllDocumentTabs();
}

function openApplicationModal() {
    const modal = document.getElementById('applicationHistoryModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

// Enhanced document handling functions for applicants
function setupApplicantDocumentButtons(data) {
    console.log('Setting up applicant document buttons with data:', data);
    
    // Store document URLs globally
    currentDocumentUrls = {
        application_letter: data.doc_application_letter || null,
        cert_reg: data.doc_cert_reg || null,
        grade_slip: data.doc_grade_slip || null,
        brgy_indigency: data.doc_brgy_indigency || null,
        student_id: data.doc_student_id || null
    };

    // Get document statuses from the data
    const documentStatuses = data.document_statuses || {};

    // Get the documents container
    const documentsContainer = document.getElementById('modal-documents-container');
    if (!documentsContainer) {
        console.error('Documents container not found');
        return;
    }

    // Clear existing content
    documentsContainer.innerHTML = '';

    // Define document order
    const documentOrder = ['application_letter', 'cert_reg', 'grade_slip', 'brgy_indigency', 'student_id'];

    // Collect available documents with statuses
    const availableDocuments = documentOrder.map(docType => {
        const hasDoc = currentDocumentUrls[docType] && currentDocumentUrls[docType] !== 'null';
        const status = documentStatuses[docType] || (hasDoc ? 'Good' : 'Missing');
        
        return {
            type: docType,
            url: currentDocumentUrls[docType],
            title: documentTitles[docType],
            status: status,
            available: hasDoc
        };
    });

    console.log('Available documents with statuses:', availableDocuments);

    if (availableDocuments.length === 0) {
        documentsContainer.innerHTML = '<p class="text-center text-gray-500 py-8">No documents available for viewing.</p>';
        return;
    }

    // Determine preview size (use A4-like height when there are exactly 5 documents)
    const availableCount = availableDocuments.filter(d => d.available).length;
    const A4_HEIGHT_PX = 1123;
    const defaultPreviewHeight = 420;
    const previewHeight = (availableCount === 5) ? A4_HEIGHT_PX : defaultPreviewHeight;
    const previewMinHeight = (availableCount === 5) ? A4_HEIGHT_PX : 320;

    // Create document status cards with inline preview (iframe) so they are opened immediately inside modal
    availableDocuments.forEach((doc, index) => {
        const statusColor = doc.status === 'Good' ? 'green' : 
                           doc.status === 'Bad' ? 'red' : 'gray';
        
        const statusIcon = doc.status === 'Good' ? 'fa-check-circle' : 
                          doc.status === 'Bad' ? 'fa-times-circle' : 'fa-question-circle';

        const documentDiv = document.createElement('div');
        documentDiv.className = 'document-status-card mb-6 bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden';

        // Build inner HTML: header + info + inline iframe preview (if available)
        documentDiv.innerHTML = `
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h4 class="text-lg font-semibold text-gray-800">${doc.title}</h4>
                <span class="status-badge status-${statusColor} px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas ${statusIcon} mr-1"></i>${doc.status}
                </span>
            </div>
            <div class="p-4">
                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                    <div class="document-info flex-1">
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Status:</strong> 
                            <span class="text-${statusColor}-600 font-medium">${doc.status}</span>
                        </p>
                        ${doc.available ? 
                            `<a href="${doc.url}" target="_blank" class="text-blue-500 hover:text-blue-700 text-sm font-medium inline-block mb-2">
                                <i class="fas fa-external-link-alt mr-1"></i> Open in new tab
                            </a>` : 
                            '<p class="text-red-500 text-sm">Document not available</p>'
                        }
                    </div>
                </div>

                ${doc.available ? `
                    <div class="document-preview mt-4 border border-gray-200 rounded overflow-hidden" style="min-height:${previewMinHeight}px;">
                        <iframe src="${doc.url}" width="100%" height="${previewHeight}" style="border:0;" loading="lazy"></iframe>
                    </div>
                ` : ''}
            </div>
        `;

        documentsContainer.appendChild(documentDiv);
    });

    console.log('Document status setup completed with inline previews');
}

// Function to automatically open all documents
function openAllDocuments() {
    // No-op: documents are now rendered inline inside the modal by setupApplicantDocumentButtons()
    console.log('openAllDocuments() skipped because inline previews are used');
}

// Function to close all document tabs
function closeAllDocumentTabs() {
    const tabsContainer = document.getElementById('documentTabsContainer');
    if (tabsContainer) {
        tabsContainer.remove();
    }
}

// Function to format date
function formatDate(dateString) {
    if (!dateString || dateString === '-') return '-';

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString; // Return original if invalid

    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    return date.toLocaleDateString('en-US', options);
}

// Populate modal with intake sheet data for applicants
function populateApplicantIntakeSheetModal(data, name, type = 'details') {
    console.log('=== START POPULATE APPLICANT MODAL DEBUG ===');
    console.log('Full data received:', data);
    console.log('Setting up document buttons...');
    setupApplicantDocumentButtons(data);
    
    // Populate head of family section
    document.getElementById('modal-applicant-name').textContent = name || '-';
    document.getElementById('modal-applicant-gender').textContent = data.applicant_gender || '-';
    document.getElementById('modal-remarks').textContent = data.remarks || '-';
    document.getElementById('modal-head-dob').textContent = data.head_dob || '-';
    document.getElementById('modal-head-pob').textContent = data.head_pob || '-';
    document.getElementById('modal-head-address').textContent = data.head_address || '-';
    document.getElementById('modal-head-zone').textContent = data.head_zone || '-';
    document.getElementById('modal-head-barangay').textContent = data.head_barangay || '-';
    document.getElementById('modal-head-religion').textContent = data.head_religion || '-';
    document.getElementById('modal-serial-number').textContent = data.serial_number || '-';
    document.getElementById('modal-head-4ps').textContent = data.head_4ps || '-';
    document.getElementById('modal-head-ipno').textContent = data.head_ipno || '-';
    document.getElementById('modal-head-educ').textContent = data.head_educ || '-';
    document.getElementById('modal-head-occ').textContent = data.head_occ || '-';

    // Populate household information
    document.getElementById('modal-house-total-income').textContent = data.house_total_income || '-';
    document.getElementById('modal-house-net-income').textContent = data.house_net_income || '-';
    document.getElementById('modal-other-income').textContent = data.other_income || '-';
    document.getElementById('modal-house-house').textContent = data.house_house || '-';
    document.getElementById('modal-house-lot').textContent = data.house_lot || '-';
    document.getElementById('modal-house-electric').textContent = data.house_electric || '-';
    document.getElementById('modal-house-water').textContent = data.house_water || '-';

    // DEBUG: Comprehensive family members analysis
    console.log('=== FAMILY MEMBERS ANALYSIS ===');
    console.log('Family Members Data:', data.family_members);
    console.log('Family Members Type:', typeof data.family_members);
    console.log('Family Members Count:', data.family_members ? data.family_members.length : 0);
    console.log('=== END POPULATE MODAL DEBUG ===');
    
    if (data.family_members && data.family_members.length > 0) {
        console.log('First Family Member Full Object:', data.family_members[0]);
        console.log('First Family Member Keys:', Object.keys(data.family_members[0]));
    }

    // Populate family members - COMPREHENSIVE FIELD CHECKING
    const familyMembersTbody = document.getElementById('modal-family-members');
    familyMembersTbody.innerHTML = '';
    
    if (data.family_members && data.family_members.length > 0) {
        console.log('Rendering family members table...');
        
        data.family_members.forEach((member, index) => {
            console.log(`Processing family member ${index + 1}:`, member);
            
            // Comprehensive field checking with fallbacks
            const name = member.NAME || member.name || 'NO NAME';
            const relation = member.RELATION || member.relationship || member.relation || 'NO RELATION';
            const birthdate = member.BIRTHDATE || member.birthdate || 'NO BIRTHDATE';
            const age = member.AGE || member.age || 'NO AGE';
            const sex = member.SEX || member.sex || member.gender || 'NO SEX';
            const civilStatus = member['CIVIL STATUS'] || member.civil_status || 'NO CIVIL STATUS';
            const education = member['EDUCATIONAL ATTAINMENT'] || member.education || 'NO EDUCATION';
            const occupation = member.OCCUPATION || member.occupation || 'NO OCCUPATION';
            const income = member.INCOME || member.monthly_income || member.income || 'NO INCOME';
            const remarks = member.REMARKS || member.remarks || 'NO REMARKS';
            
            console.log(`Member ${index + 1} final values:`, {
                name, relation, birthdate, age, sex, civilStatus, education, occupation, income, remarks
            });
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${name}</td>
                <td>${relation}</td>
                <td>${birthdate}</td>
                <td>${age}</td>
                <td>${sex}</td>
                <td>${civilStatus}</td>
                <td>${education}</td>
                <td>${occupation}</td>
                <td>${income}</td>
                <td>${remarks}</td>
            `;
            familyMembersTbody.appendChild(row);
        });
        console.log('Family members table rendered successfully');
    } else {
        console.log('No family members to display');
        familyMembersTbody.innerHTML = '<tr><td colspan="10" class="text-center py-4">No family members found</td></tr>';
    }

    // DEBUG: Service records analysis
    console.log('=== SERVICE RECORDS ANALYSIS ===');
    console.log('Service Records Data:', data.social_service_records);
    console.log('Service Records Count:', data.social_service_records ? data.social_service_records.length : 0);

    // Populate service records
    const serviceRecordsTbody = document.getElementById('modal-service-records');
    serviceRecordsTbody.innerHTML = '';
    
    if (data.social_service_records && data.social_service_records.length > 0) {
        console.log('Rendering service records table...');
        data.social_service_records.forEach((record, index) => {
            console.log(`Processing service record ${index + 1}:`, record);
            
            const date = record.DATE || record.date || '-';
            const problem = record['PROBLEM/NEED'] || record.problem || record.problem_need || '-';
            const action = record['ACTION/ASSISTANCE GIVEN'] || record.action || record.action_assistance || '-';
            const remarks = record.REMARKS || record.remarks || '-';
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${date}</td>
                <td>${problem}</td>
                <td>${action}</td>
                <td>${remarks}</td>
            `;
            serviceRecordsTbody.appendChild(row);
        });
        console.log('Service records table rendered successfully');
    } else {
        console.log('No service records to display');
        serviceRecordsTbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">No service records found</td></tr>';
    }

    // Setup document buttons
    setupApplicantDocumentButtons(data);

    // Populate signatures and photos
    document.getElementById('modal-worker-fullname').textContent = data.worker_name || '-';
    document.getElementById('modal-officer-fullname').textContent = data.officer_name || '-';
    document.getElementById('modal-date-entry').textContent = formatDate(data.date_entry);

    // AUTO-OPEN ALL DOCUMENTS
    setTimeout(() => {
        openAllDocuments();
    }, 1000); // 1 second delay after modal opens
}

// View applicant intake sheet (for LYDO Reviewed applicants)
function viewApplicantIntakeSheet(applicantId, applicantName, status) {
    showLoadingOverlay();
    console.log('Loading intake sheet for applicant:', applicantId);
    
    fetch(`/lydo_admin/get-application-personnel/${applicantId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Application personnel response:', data);
            if (data.success) {
                return fetch(`/lydo_admin/intake-sheet/${data.application_personnel_id}`);
            } else {
                throw new Error(data.message || 'Failed to get application personnel ID');
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            hideLoadingOverlay();
            console.log('Intake sheet response:', data);
            if (data.success) {
                currentApplicantId = applicantId;
                currentApplicantName = applicantName;
                populateApplicantIntakeSheetModal(data.intakeSheet, applicantName, 'intake');
                openApplicationModal();
            } else {
                Swal.fire('Error', data.message || 'Failed to load intake sheet', 'error');
            }
        })
        .catch(error => {
            hideLoadingOverlay();
            console.error('Error loading intake sheet:', error);
            Swal.fire('Error', 'Failed to load application details: ' + error.message, 'error');
        });
}

// View applicant documents only (for Mayor Staff applicants)
function viewApplicantDocuments(applicantId, applicantName, status) {
    showLoadingOverlay();
    console.log('Loading documents for applicant:', applicantId);
    
    fetch(`/lydo_admin/applicant-documents/${applicantId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            hideLoadingOverlay();
            console.log('Documents response:', data);
            if (data.success) {
                currentApplicantId = applicantId;
                currentApplicantName = applicantName;
                openApplicantDocumentsModal(applicantName, data.documents);
            } else {
                Swal.fire('Error', data.message || 'Failed to load documents', 'error');
            }
        })
        .catch(error => {
            hideLoadingOverlay();
            console.error('Error loading documents:', error);
            Swal.fire('Error', 'Failed to load documents: ' + error.message, 'error');
        });
}

// Modal for View Documents Only (5 DOCUMENTS)
function openApplicantDocumentsModal(applicantName, documents) {
    // Set modal title
    document.getElementById('modalTitle').textContent = `Documents - ${applicantName}`;
    
    // Hide intake sheet sections and show only documents
    document.querySelectorAll('.intake-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show documents section
    const documentsSection = document.querySelector('.bg-white.rounded-lg.shadow-lg.mb-6');
    if (documentsSection) {
        documentsSection.style.display = 'block';
    }
    
    // Setup document buttons with the documents data
    const documentData = {
        doc_application_letter: documents.doc_application_letter,
        doc_cert_reg: documents.doc_cert_reg,
        doc_grade_slip: documents.doc_grade_slip,
        doc_brgy_indigency: documents.doc_brgy_indigency,
        doc_student_id: documents.doc_student_id,
        document_statuses: {} // Empty statuses for documents-only view
    };
    
    setupApplicantDocumentButtons(documentData);
    openApplicationModal();
}

// Loading overlay functions
function showLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (!overlay) return;
    overlay.style.display = 'flex';
    overlay.classList.remove('fade-out');
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (!overlay) return;
    overlay.classList.add('fade-out');
    setTimeout(() => {
        overlay.style.display = 'none';
        overlay.classList.remove('fade-out');
    }, 300);
}

// Data loading functions
async function loadMayorApplicants() {
    showLoadingOverlay();
    try {
        const response = await fetch('/lydo_admin/get-mayor-applicants');
        const data = await response.json();
        allMayorApplicants = data.applicants || [];
        // Initialize selection state
        allMayorApplicants.forEach(applicant => {
            applicant.selected = false;
        });
        filteredMayorApplicants = [...allMayorApplicants];
        currentPageMayor = 1;
        renderMayorApplicantsTable();
        setupMayorPagination();
        setupMayorFilters();
        updateApprovedCount();
    } catch (error) {
        console.error('Error loading mayor applicants:', error);
    } finally {
        hideLoadingOverlay();
    }
}

async function loadLydoReviewedApplicants() {
    showLoadingOverlay();
    try {
        const response = await fetch('/lydo_admin/get-lydo-reviewed-applicants');
        const data = await response.json();
        allLydoApplicants = data.applicants || [];
        // Initialize selection state
        allLydoApplicants.forEach(applicant => {
            applicant.selected = false;
        });
        filteredLydoApplicants = [...allLydoApplicants];
        currentPageLydo = 1;
        renderLydoApplicantsTable();
        setupLydoPagination();
        setupLydoFilters();
    } catch (error) {
        console.error('Error loading LYDO reviewed applicants:', error);
    } finally {
        hideLoadingOverlay();
    }
}

// Render functions
function renderMayorApplicantsTable() {
    const tableBody = document.getElementById('mayorApplicantsTable');
    const startIndex = (currentPageMayor - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const currentItems = filteredMayorApplicants.slice(startIndex, endIndex);

    if (currentItems.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="px-4 py-2 text-center text-sm text-gray-500">
                    No applicants found.
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = currentItems.map(applicant => `
        <tr class="hover:bg-gray-50 border-b">
            <td class="px-4 border border-gray-200 py-2 text-center">
                <input type="checkbox" name="selected_applicants" value="${applicant.applicant_id}" 
                       ${applicant.selected ? 'checked' : ''}
                       class="applicant-checkbox-mayor rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm font-medium text-gray-900">
                    ${formatName(applicant)}
                </div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_brgy || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_email || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_contact_number || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_school_name || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_acad_year || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="flex gap-2 justify-center">
                    <button type="button" 
                            onclick="viewApplicantDocuments('${applicant.applicant_id}', '${escapeString(formatName(applicant))}', '${applicant.initial_screening}')"
                            class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow">
                        <i class="fas fa-file-alt mr-1"></i> View Documents
                    </button>
                </div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                    ${applicant.initial_screening === 'Approved' ? 'bg-green-100 text-green-800' : 
                      applicant.initial_screening === 'Rejected' ? 'bg-red-100 text-red-800' : 
                      'bg-yellow-100 text-yellow-800'}">
                    ${applicant.initial_screening === 'Approved' ? 'Approved by Mayor' : applicant.initial_screening || 'Pending'}
                </span>
            </td>
        </tr>
    `).join('');

    setupMayorCheckboxes();
    updateSelectAllCheckbox('mayor');
}

function renderLydoApplicantsTable() {
    const tableBody = document.getElementById('lydoApplicantsTable');
    const startIndex = (currentPageLydo - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const currentItems = filteredLydoApplicants.slice(startIndex, endIndex);

    if (currentItems.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="px-4 py-2 text-center text-sm text-gray-500">
                    No applicants found.
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = currentItems.map(applicant => `
        <tr class="hover:bg-gray-50 border-b">
            <td class="px-4 border border-gray-200 py-2 text-center">
                <input type="checkbox" name="selected_applicants" value="${applicant.applicant_id}" 
                       ${applicant.selected ? 'checked' : ''}
                       class="applicant-checkbox-lydo rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm font-medium text-gray-900">
                    ${formatName(applicant)}
                </div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_brgy || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_email || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_contact_number || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_school_name || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="text-sm text-gray-900">${applicant.applicant_acad_year || 'N/A'}</div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <div class="flex gap-2 justify-center">
                    <button type="button" 
                            onclick="viewApplicantIntakeSheet('${applicant.applicant_id}', '${escapeString(formatName(applicant))}', '${applicant.initial_screening}')"
                            class="px-3 py-1 text-sm bg-purple-500 hover:bg-purple-600 text-white rounded-lg shadow">
                        <i class="fas fa-clipboard-list mr-1"></i> View Application
                    </button>
                </div>
            </td>
            <td class="px-4 border border-gray-200 py-2 text-center">
                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                    ${applicant.remarks === 'Poor' ? 'bg-orange-100 text-orange-800' : 
                      applicant.remarks === 'Non-Poor' ? 'bg-green-100 text-green-800' : 
                      applicant.remarks === 'Ultra Poor' ? 'bg-red-100 text-red-800' : 
                      'bg-gray-100 text-gray-800'}">
                    ${applicant.remarks || 'Not Specified'}
                </span>
            </td>
        </tr>
    `).join('');

    setupLydoCheckboxes();
    updateSelectAllCheckbox('lydo');
}

// Helper functions
function formatName(applicant) {
    let name = '';
    if (applicant.applicant_lname) {
        name += applicant.applicant_lname.charAt(0).toUpperCase() + applicant.applicant_lname.slice(1).toLowerCase();
    }
    if (applicant.applicant_suffix) {
        name += ' ' + applicant.applicant_suffix;
    }
    if (applicant.applicant_fname) {
        name += (name ? ', ' : '') + applicant.applicant_fname.charAt(0).toUpperCase() + applicant.applicant_fname.slice(1).toLowerCase();
    }
    if (applicant.applicant_mname) {
        name += ' ' + applicant.applicant_mname.charAt(0).toUpperCase() + '.';
    }
    return name;
}

function escapeString(str) {
    return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

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

function updateApprovedCount() {
    const approvedCount = allMayorApplicants.filter(applicant => 
        applicant.initial_screening === 'Approved'
    ).length;
    
    const approvedTabButton = document.querySelector('[data-tab="mayor-applicants"]');
    if (approvedTabButton) {
        const existingBadge = approvedTabButton.querySelector('.approved-badge');
        if (existingBadge) {
            existingBadge.remove();
        }
        
        if (approvedCount > 0) {
            const badge = document.createElement('span');
            badge.className = 'approved-badge ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full';
            badge.textContent = approvedCount;
            approvedTabButton.appendChild(badge);
        }
    }
}

// Filter setup functions
function setupMayorFilters() {
    const searchInput = document.getElementById('searchInputMayor');
    const barangaySelect = document.getElementById('barangaySelectMayor');
    const academicYearSelect = document.getElementById('academicYearSelectMayor');
    const initialScreeningSelect = document.getElementById('initialScreeningSelectMayor');

    const applyFilters = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const barangayFilter = barangaySelect.value;
        const academicYearFilter = academicYearSelect.value;
        const screeningFilter = initialScreeningSelect.value;

        filteredMayorApplicants = allMayorApplicants.filter(applicant => {
            const matchesSearch = !searchTerm || 
                formatName(applicant).toLowerCase().includes(searchTerm) ||
                (applicant.applicant_email && applicant.applicant_email.toLowerCase().includes(searchTerm)) ||
                (applicant.applicant_school_name && applicant.applicant_school_name.toLowerCase().includes(searchTerm));
            
            const matchesBarangay = !barangayFilter || applicant.applicant_brgy === barangayFilter;
            const matchesAcademicYear = !academicYearFilter || applicant.applicant_acad_year === academicYearFilter;
            
            let matchesScreening = true;
            if (screeningFilter === 'Approved') {
                matchesScreening = applicant.initial_screening === 'Approved';
            } else if (screeningFilter === 'Rejected') {
                matchesScreening = applicant.initial_screening === 'Rejected';
            } else if (screeningFilter === 'Pending') {
                matchesScreening = !applicant.initial_screening || applicant.initial_screening === 'Pending';
            }

            return matchesSearch && matchesBarangay && matchesAcademicYear && matchesScreening;
        });

        currentPageMayor = 1;
        renderMayorApplicantsTable();
        setupMayorPagination();
    };

    searchInput.addEventListener('input', debounce(applyFilters, 300));
    barangaySelect.addEventListener('change', applyFilters);
    academicYearSelect.addEventListener('change', applyFilters);
    initialScreeningSelect.addEventListener('change', applyFilters);
}

function setupLydoFilters() {
    const searchInput = document.getElementById('searchInputLydo');
    const barangaySelect = document.getElementById('barangaySelectLydo');
    const academicYearSelect = document.getElementById('academicYearSelectLydo');
    const remarksSelect = document.getElementById('remarksSelectLydo');

    const applyFilters = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const barangayFilter = barangaySelect.value;
        const academicYearFilter = academicYearSelect.value;
        const remarksFilter = remarksSelect.value;

        filteredLydoApplicants = allLydoApplicants.filter(applicant => {
            const matchesSearch = !searchTerm || 
                formatName(applicant).toLowerCase().includes(searchTerm) ||
                (applicant.applicant_email && applicant.applicant_email.toLowerCase().includes(searchTerm)) ||
                (applicant.applicant_school_name && applicant.applicant_school_name.toLowerCase().includes(searchTerm));
            
            const matchesBarangay = !barangayFilter || applicant.applicant_brgy === barangayFilter;
            const matchesAcademicYear = !academicYearFilter || applicant.applicant_acad_year === academicYearFilter;
            const matchesRemarks = !remarksFilter || applicant.remarks === remarksFilter;

            return matchesSearch && matchesBarangay && matchesAcademicYear && matchesRemarks;
        });

        currentPageLydo = 1;
        renderLydoApplicantsTable();
        setupLydoPagination();
    };

    searchInput.addEventListener('input', debounce(applyFilters, 300));
    barangaySelect.addEventListener('change', applyFilters);
    academicYearSelect.addEventListener('change', applyFilters);
    remarksSelect.addEventListener('change', applyFilters);
}

// Pagination setup
function setupMayorPagination() {
    const totalPages = Math.ceil(filteredMayorApplicants.length / itemsPerPage);
    document.getElementById('totalPagesMayor').textContent = totalPages;
    document.getElementById('paginationInfoMayor').textContent = `Showing page ${currentPageMayor} of ${totalPages}`;
    
    document.getElementById('prevPageMayor').disabled = currentPageMayor === 1;
    document.getElementById('nextPageMayor').disabled = currentPageMayor === totalPages || totalPages === 0;

    document.getElementById('prevPageMayor').onclick = () => {
        if (currentPageMayor > 1) {
            currentPageMayor--;
            renderMayorApplicantsTable();
            setupMayorPagination();
        }
    };

    document.getElementById('nextPageMayor').onclick = () => {
        if (currentPageMayor < totalPages) {
            currentPageMayor++;
            renderMayorApplicantsTable();
            setupMayorPagination();
        }
    };

    document.getElementById('currentPageMayor').onchange = (e) => {
        const page = parseInt(e.target.value);
        if (page >= 1 && page <= totalPages) {
            currentPageMayor = page;
            renderMayorApplicantsTable();
            setupMayorPagination();
        } else {
            e.target.value = currentPageMayor;
        }
    };
}

function setupLydoPagination() {
    const totalPages = Math.ceil(filteredLydoApplicants.length / itemsPerPage);
    document.getElementById('totalPagesLydo').textContent = totalPages;
    document.getElementById('paginationInfoLydo').textContent = `Showing page ${currentPageLydo} of ${totalPages}`;
    
    document.getElementById('prevPageLydo').disabled = currentPageLydo === 1;
    document.getElementById('nextPageLydo').disabled = currentPageLydo === totalPages || totalPages === 0;

    document.getElementById('prevPageLydo').onclick = () => {
        if (currentPageLydo > 1) {
            currentPageLydo--;
            renderLydoApplicantsTable();
            setupLydoPagination();
        }
    };

    document.getElementById('nextPageLydo').onclick = () => {
        if (currentPageLydo < totalPages) {
            currentPageLydo++;
            renderLydoApplicantsTable();
            setupLydoPagination();
        }
    };

    document.getElementById('currentPageLydo').onchange = (e) => {
        const page = parseInt(e.target.value);
        if (page >= 1 && page <= totalPages) {
            currentPageLydo = page;
            renderLydoApplicantsTable();
            setupLydoPagination();
        } else {
            e.target.value = currentPageLydo;
        }
    };
}

// Checkbox setup functions
function setupMayorCheckboxes() {
    const selectAll = document.getElementById('selectAllMayor');
    const checkboxes = document.querySelectorAll('.applicant-checkbox-mayor');
    const copyBtn = document.getElementById('copyNamesBtnMayor');
    const emailBtn = document.getElementById('emailSelectedBtnMayor');
    const smsBtn = document.getElementById('smsSelectedBtnMayor');

    selectAll.addEventListener('change', (e) => {
        const isChecked = e.target.checked;
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        
        if (isChecked) {
            filteredMayorApplicants.forEach(applicant => {
                applicant.selected = true;
            });
        } else {
            filteredMayorApplicants.forEach(applicant => {
                applicant.selected = false;
            });
        }
        
        updateButtonVisibility('mayor');
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const applicantId = checkbox.value;
            const applicant = filteredMayorApplicants.find(app => app.applicant_id == applicantId);
            if (applicant) {
                applicant.selected = checkbox.checked;
            }
            updateButtonVisibility('mayor');
            updateSelectAllCheckbox('mayor');
        });
    });

    copyBtn.addEventListener('click', () => copySelectedNames('mayor'));
    emailBtn.addEventListener('click', () => openEmailModal('mayor'));
    smsBtn.addEventListener('click', () => openSmsModal('mayor'));
}

function setupLydoCheckboxes() {
    const selectAll = document.getElementById('selectAllLydo');
    const checkboxes = document.querySelectorAll('.applicant-checkbox-lydo');
    const copyBtn = document.getElementById('copyNamesBtnLydo');
    const emailBtn = document.getElementById('emailSelectedBtnLydo');
    const smsBtn = document.getElementById('smsSelectedBtnLydo');

    selectAll.addEventListener('change', (e) => {
        const isChecked = e.target.checked;
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        
        if (isChecked) {
            filteredLydoApplicants.forEach(applicant => {
                applicant.selected = true;
            });
        } else {
            filteredLydoApplicants.forEach(applicant => {
                applicant.selected = false;
            });
        }
        
        updateButtonVisibility('lydo');
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const applicantId = checkbox.value;
            const applicant = filteredLydoApplicants.find(app => app.applicant_id == applicantId);
            if (applicant) {
                applicant.selected = checkbox.checked;
            }
            updateButtonVisibility('lydo');
            updateSelectAllCheckbox('lydo');
        });
    });

    copyBtn.addEventListener('click', () => copySelectedNames('lydo'));
    emailBtn.addEventListener('click', () => openEmailModal('lydo'));
    smsBtn.addEventListener('click', () => openSmsModal('lydo'));
}

// Update Select All checkbox state
function updateSelectAllCheckbox(tab) {
    const selectAll = document.getElementById(`selectAll${tab.charAt(0).toUpperCase() + tab.slice(1)}`);
    const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
    
    if (filteredApplicants.length === 0) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
        return;
    }
    
    const selectedCount = filteredApplicants.filter(applicant => applicant.selected).length;
    
    if (selectedCount === 0) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    } else if (selectedCount === filteredApplicants.length) {
        selectAll.checked = true;
        selectAll.indeterminate = false;
    } else {
        selectAll.checked = false;
        selectAll.indeterminate = true;
    }
}

// Button visibility function
function updateButtonVisibility(tab) {
    const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
    const hasSelection = filteredApplicants.some(applicant => applicant.selected);
    
    const copyBtn = document.getElementById(`copyNamesBtn${tab.charAt(0).toUpperCase() + tab.slice(1)}`);
    const emailBtn = document.getElementById(`emailSelectedBtn${tab.charAt(0).toUpperCase() + tab.slice(1)}`);
    const smsBtn = document.getElementById(`smsSelectedBtn${tab.charAt(0).toUpperCase() + tab.slice(1)}`);

    [copyBtn, emailBtn, smsBtn].forEach(btn => {
        if (btn) {
            btn.classList.toggle('hidden', !hasSelection);
            btn.disabled = !hasSelection;
        }
    });
}

// Copy names function
function copySelectedNames(tab) {
    const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
    const selectedApplicants = filteredApplicants.filter(applicant => applicant.selected);
    
    if (selectedApplicants.length === 0) {
        Swal.fire('Error', 'No applicants selected', 'error');
        return;
    }

    const names = selectedApplicants.map(applicant => formatName(applicant));

    if (names.length > 0) {
        navigator.clipboard.writeText(names.join(', '))
            .then(() => {
                Swal.fire('Success', `${names.length} names copied to clipboard!`, 'success');
            })
            .catch(() => {
                Swal.fire('Error', 'Failed to copy names', 'error');
            });
    }
}

// Email modal
function openEmailModal(tab) {
    const modal = document.getElementById('emailModal');
    const preview = document.getElementById('recipientsPreview');

    const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
    const selectedApplicants = filteredApplicants.filter(applicant => applicant.selected);
    
    if (selectedApplicants.length === 0) {
        preview.textContent = 'No recipients selected';
    } else {
        const items = selectedApplicants.map(applicant => {
            const name = formatName(applicant);
            const email = applicant.applicant_email || 'N/A';
            return `<div class="mb-1"><strong>${escapeHtml(name)}</strong> — ${escapeHtml(email)}</div>`;
        }).join('');
        preview.innerHTML = `<div class="mb-2 text-sm font-semibold">Selected: ${selectedApplicants.length} applicants</div>${items}`;
    }

    modal.classList.remove('hidden');
}

// SMS modal
function openSmsModal(tab) {
    const modal = document.getElementById('smsModal');
    const preview = document.getElementById('smsRecipientsPreview');

    const filteredApplicants = tab === 'mayor' ? filteredMayorApplicants : filteredLydoApplicants;
    const selectedApplicants = filteredApplicants.filter(applicant => applicant.selected);
    
    if (selectedApplicants.length === 0) {
        preview.textContent = 'No recipients selected';
    } else {
        const items = selectedApplicants.map(applicant => {
            const name = formatName(applicant);
            const phone = applicant.applicant_contact_number || 'N/A';
            return `<div class="mb-1"><strong>${escapeHtml(name)}</strong> — ${escapeHtml(phone)}</div>`;
        }).join('');
        preview.innerHTML = `<div class="mb-2 text-sm font-semibold">Selected: ${selectedApplicants.length} applicants</div>${items}`;
    }

    modal.classList.remove('hidden');
}

// Make functions global
window.viewApplicantDocuments = viewApplicantDocuments;
window.viewApplicantIntakeSheet = viewApplicantIntakeSheet;
window.closeApplicationModal = closeApplicationModal;
window.loadMayorApplicants = loadMayorApplicants;
window.loadLydoReviewedApplicants = loadLydoReviewedApplicants;

// Enhanced viewApplicantIntakeSheet function
function viewApplicantIntakeSheet(applicantId, applicantName, status) {
    showLoadingOverlay();
    console.log('Loading intake sheet for applicant:', applicantId);
    
    fetch(`/lydo_admin/get-application-personnel/${applicantId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Application personnel response:', data);
            if (data.success && data.application_personnel_id) {
                return fetch(`/lydo_admin/intake-sheet/${data.application_personnel_id}`);
            } else {
                throw new Error(data.message || 'Failed to get application personnel ID');
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideLoadingOverlay();
            console.log('Intake sheet response:', data);
            if (data.success) {
                currentApplicantId = applicantId;
                currentApplicantName = applicantName;
                populateApplicantIntakeSheetModal(data.intakeSheet, applicantName, 'intake');
                openApplicationModal();
            } else {
                Swal.fire('Error', data.message || 'Failed to load intake sheet', 'error');
            }
        })
        .catch(error => {
            hideLoadingOverlay();
            console.error('Error loading intake sheet:', error);
            Swal.fire('Error', 'Failed to load application details: ' + error.message, 'error');
        });
}

// Enhanced viewApplicantDocuments function
function viewApplicantDocuments(applicantId, applicantName, status) {
    showLoadingOverlay();
    console.log('Loading documents for applicant:', applicantId);
    
    fetch(`/lydo_admin/applicant-documents/${applicantId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideLoadingOverlay();
            console.log('Documents response:', data);
            if (data.success) {
                currentApplicantId = applicantId;
                currentApplicantName = applicantName;
                openApplicantDocumentsModal(applicantName, data.documents);
            } else {
                Swal.fire('Error', data.message || 'Failed to load documents', 'error');
            }
        })
        .catch(error => {
            hideLoadingOverlay();
            console.error('Error loading documents:', error);
            Swal.fire('Error', 'Failed to load documents: ' + error.message, 'error');
        });
}
function openApplicationModal() {
    const modal = document.getElementById('applicationHistoryModal');
    if (modal) {
        modal.classList.remove('hidden');
        // Ensure modal is visible
        modal.style.display = 'block';
    } else {
        console.error('Modal element not found');
    }
}

function closeApplicationModal() {
    const modal = document.getElementById('applicationHistoryModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
    currentApplicantId = null;
    currentApplicantName = null;
    closeAllDocumentTabs();
}