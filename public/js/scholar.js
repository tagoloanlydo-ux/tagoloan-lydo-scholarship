// scholar.js - Complete JavaScript file for scholar page functionality

// Global pagination state
const paginationState = {
    currentPage: 1,
    rowsPerPage: 15,
    allRows: [],
    filteredRows: []
};

// Function to get full name for sorting
function getFullNameForSorting(row) {
    const nameCell = row.cells[1];
    if (!nameCell) return '';
    return nameCell.textContent.trim().toLowerCase();
}

// Function to sort rows alphabetically by last name
function sortRowsAlphabetically(rows) {
    return rows.sort((a, b) => {
        const nameA = getFullNameForSorting(a);
        const nameB = getFullNameForSorting(b);
        return nameA.localeCompare(nameB);
    });
}

// Initialize data from the table
function initializeScholarData() {
    const tableRows = Array.from(document.querySelectorAll('table tbody tr'));
    paginationState.allRows = tableRows.filter(row => !row.querySelector('td[colspan]'));
    
    // Sort rows alphabetically by last name
    paginationState.allRows = sortRowsAlphabetically(paginationState.allRows);
    paginationState.filteredRows = [...paginationState.allRows];
}

// Initialize pagination
function initializeScholarPagination() {
    updateScholarPagination();
}

// Update pagination display
function updateScholarPagination() {
    const state = paginationState;
    const container = document.getElementById('paginationContainer');
    
    if (!container) return;
    
    // Hide all rows first
    state.allRows.forEach(row => {
        row.style.display = 'none';
    });
    
    // Calculate pagination for filtered rows
    const startIndex = (state.currentPage - 1) * state.rowsPerPage;
    const endIndex = startIndex + state.rowsPerPage;
    const pageRows = state.filteredRows.slice(startIndex, endIndex);
    
    // Show only rows for current page
    pageRows.forEach(row => {
        row.style.display = '';
    });
    
    // Update pagination controls
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    const startItem = state.filteredRows.length === 0 ? 0 : Math.min((state.currentPage - 1) * state.rowsPerPage + 1, state.filteredRows.length);
    const endItem = Math.min(state.currentPage * state.rowsPerPage, state.filteredRows.length);
    
    container.innerHTML = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Showing <span class="font-semibold">${startItem}-${endItem}</span> of <span class="font-semibold">${state.filteredRows.length}</span> scholars
            </div>
            
            <div class="flex items-center space-x-1">
                <!-- First Page -->
                <button onclick="changeScholarPage(1)"
                    class="px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300 ${
                        state.currentPage === 1
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>

                <!-- Previous Page -->
                <button onclick="changeScholarPage(${state.currentPage - 1})"
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === 1
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-left"></i>
                </button>

                <!-- Page Info -->
                <div class="flex items-center px-4 py-2 text-sm text-gray-700 border border-gray-300 bg-white">
                    Page
                    <input type="number"
                           class="mx-2 w-12 text-center border border-gray-300 rounded px-1 py-1 text-sm"
                           value="${state.currentPage}"
                           min="1"
                           max="${totalPages}"
                           onchange="goToScholarPage(this.value)">
                    of ${totalPages}
                </div>

                <!-- Next Page -->
                <button onclick="changeScholarPage(${state.currentPage + 1})"
                    class="px-3 py-2 text-sm font-medium border border-gray-300 ${
                        state.currentPage === totalPages
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>

                <!-- Last Page -->
                <button onclick="changeScholarPage(${totalPages})"
                    class="px-3 py-2 text-sm font-medium rounded-r-md border border-gray-300 ${
                        state.currentPage === totalPages
                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            : 'bg-white text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                    }"
                    ${state.currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
        </div>
    `;
}

// Change page
function changeScholarPage(page) {
    const state = paginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateScholarPagination();
}

// Go to specific page
function goToScholarPage(page) {
    const state = paginationState;
    const totalPages = Math.ceil(state.filteredRows.length / state.rowsPerPage);
    
    page = parseInt(page);
    if (isNaN(page) || page < 1) page = 1;
    if (page > totalPages) page = totalPages;
    
    state.currentPage = page;
    updateScholarPagination();
}

// Initialize filtering functionality
function initializeScholarFiltering() {
    const searchInput = document.getElementById('searchInput');
    const barangaySelect = document.getElementById('barangaySelect');
    const academicYearSelect = document.getElementById('academicYearSelect');
    const statusSelect = document.getElementById('statusSelect');

    function filterScholarTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedBarangay = barangaySelect.value;
        const selectedAcademicYear = academicYearSelect.value;
        const selectedStatus = statusSelect.value;

        const filteredRows = paginationState.allRows.filter(row => {
            const nameCell = row.cells[1];
            const barangayCell = row.cells[2];
            const academicYearCell = row.cells[6];
            const statusCell = row.cells[7];

            if (!nameCell || !barangayCell || !academicYearCell || !statusCell) return false;

            const name = nameCell.textContent.toLowerCase();
            const barangay = barangayCell.textContent.trim();
            const academicYear = academicYearCell.textContent.trim();
            const status = statusCell.textContent.trim().toLowerCase();

            const nameMatch = name.includes(searchTerm);
            const barangayMatch = !selectedBarangay || barangay === selectedBarangay;
            const academicYearMatch = !selectedAcademicYear || academicYear === selectedAcademicYear;
            const statusMatch = selectedStatus === 'all' || status === selectedStatus;

            return nameMatch && barangayMatch && academicYearMatch && statusMatch;
        });

        // Sort filtered results alphabetically
        const sortedFilteredRows = sortRowsAlphabetically(filteredRows);

        // Update filtered rows and reset to page 1
        paginationState.filteredRows = sortedFilteredRows;
        paginationState.currentPage = 1;
        updateScholarPagination();
        
        // Reset select all checkbox
        document.getElementById('selectAll').checked = false;
        document.getElementById('selectAll').indeterminate = false;
    }

    // Add event listeners with debouncing
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterScholarTable, 300));
    }
    if (barangaySelect) {
        barangaySelect.addEventListener('change', filterScholarTable);
    }
    if (academicYearSelect) {
        academicYearSelect.addEventListener('change', filterScholarTable);
    }
    if (statusSelect) {
        statusSelect.addEventListener('change', filterScholarTable);
    }
}

// Debounce function for search
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

// Documents modal functionality
function initializeDocumentsModal() {
    const documentsModal = document.getElementById('documentsModal');
    const closeDocumentsModal = document.getElementById('closeDocumentsModal');
    const closeDocuments = document.getElementById('closeDocuments');
    const viewDocumentsBtns = document.querySelectorAll('.view-documents-btn');

    // Function to load documents for a scholar
    async function loadScholarDocuments(scholarId) {
        const loadingElement = document.getElementById('documentsLoading');
        const contentElement = document.getElementById('documentsContent');
        const noDocumentsElement = document.getElementById('noDocumentsMessage');

        // Show loading, hide other elements
        loadingElement.classList.remove('hidden');
        contentElement.classList.add('hidden');
        noDocumentsElement.classList.add('hidden');

        try {
            const response = await fetch(`/lydo_admin/get-scholar-documents/${scholarId}`);
            const data = await response.json();

            // Hide loading
            loadingElement.classList.add('hidden');

            if (data.success && data.documents) {
                const documents = data.documents;
                
                // Clear previous content
                document.getElementById('certOfRegContainer').innerHTML = '';
                document.getElementById('gradeSlipContainer').innerHTML = '';
                document.getElementById('brgyIndigencyContainer').innerHTML = '';
                document.getElementById('renewalInfo').innerHTML = '';

                if (documents.length > 0) {
                    // Show documents content
                    contentElement.classList.remove('hidden');

                    documents.forEach(doc => {
                        // Certificate of Registration
                        if (doc.renewal_cert_of_reg) {
                            const certOfRegDiv = createDocumentElement(doc.renewal_cert_of_reg, 'Certificate of Registration');
                            document.getElementById('certOfRegContainer').appendChild(certOfRegDiv);
                        }

                        // Grade Slip
                        if (doc.renewal_grade_slip) {
                            const gradeSlipDiv = createDocumentElement(doc.renewal_grade_slip, 'Grade Slip');
                            document.getElementById('gradeSlipContainer').appendChild(gradeSlipDiv);
                        }

                        // Barangay Indigency
                        if (doc.renewal_brgy_indigency) {
                            const brgyIndigencyDiv = createDocumentElement(doc.renewal_brgy_indigency, 'Barangay Indigency');
                            document.getElementById('brgyIndigencyContainer').appendChild(brgyIndigencyDiv);
                        }

                        // Renewal Information
                        const renewalInfo = `
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <strong>Semester:</strong> ${doc.renewal_semester}
                                </div>
                                <div>
                                    <strong>Academic Year:</strong> ${doc.renewal_acad_year}
                                </div>
                                <div>
                                    <strong>Date Submitted:</strong> ${new Date(doc.date_submitted).toLocaleDateString()}
                                </div>
                                <div>
                                    <strong>Status:</strong> 
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${
                                        doc.renewal_status === 'Approved' ? 'bg-green-100 text-green-800' : 
                                        doc.renewal_status === 'Rejected' ? 'bg-red-100 text-red-800' : 
                                        'bg-yellow-100 text-yellow-800'
                                    }">
                                        ${doc.renewal_status}
                                    </span>
                                </div>
                            </div>
                        `;
                        document.getElementById('renewalInfo').innerHTML = renewalInfo;
                    });
                } else {
                    // Show no documents message
                    noDocumentsElement.classList.remove('hidden');
                }
            } else {
                // Show no documents message
                noDocumentsElement.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading documents:', error);
            loadingElement.classList.add('hidden');
            noDocumentsElement.classList.remove('hidden');
        }
    }

    // Helper function to create document element with proper URLs
    function createDocumentElement(filePath, documentType) {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg border';
        
        const fileName = filePath ? filePath.split('/').pop() : 'No file';
        const isImage = filePath && /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);
        const isPDF = filePath && /\.pdf$/i.test(fileName);
        
        div.innerHTML = `
            <div class="flex-1 min-w-0">
                <span class="text-sm font-medium text-gray-700 block">${documentType}</span>
                <span class="text-xs text-gray-500 truncate block">${fileName}</span>
            </div>
            <div class="flex space-x-2 ml-3">
                ${filePath ? `
                    <button type="button" 
                            onclick="viewDocument('${filePath}', '${documentType}')"
                            class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors flex items-center gap-1">
                        <i class="fas ${isPDF ? 'fa-file-pdf' : (isImage ? 'fa-image' : 'fa-download')}"></i>
                        ${isPDF ? 'View PDF' : (isImage ? 'View' : 'Download')}
                    </button>
                ` : `
                    <span class="px-3 py-1 bg-gray-300 text-gray-600 text-xs rounded cursor-not-allowed">
                        No File
                    </span>
                `}
            </div>
        `;
        
        return div;
    }

    // Event listeners for view documents buttons
    viewDocumentsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const scholarId = this.getAttribute('data-scholar-id');
            loadScholarDocuments(scholarId);
            documentsModal.classList.remove('hidden');
        });
    });

    // Close modal events
    closeDocumentsModal.addEventListener('click', function() {
        documentsModal.classList.add('hidden');
    });

    closeDocuments.addEventListener('click', function() {
        documentsModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === documentsModal) {
            documentsModal.classList.add('hidden');
        }
    });
}

// Document Viewer Modal Functions (Same as renewal)
function viewDocument(filePath, documentTitle) {
    if (!filePath) {
        showError('No document available');
        return;
    }

    const viewer = document.getElementById('documentViewer');
    const loading = document.getElementById('documentLoading');
    const downloadLink = document.getElementById('downloadDocument');
    const title = document.getElementById('documentTitle');
    
    // Set document title
    title.textContent = documentTitle;
    
    // Show loading, hide viewer
    loading.style.display = 'flex';
    viewer.style.display = 'none';
    
    // Set download link
    downloadLink.href = filePath;
    downloadLink.download = filePath.split('/').pop();
    
    // Show modal
    document.getElementById('documentViewerModal').classList.remove('hidden');
    
    // Check if it's a PDF
    if (filePath.toLowerCase().endsWith('.pdf')) {
        // For PDF files, use the iframe
        viewer.src = filePath;
        viewer.onload = function() {
            loading.style.display = 'none';
            viewer.style.display = 'block';
        };
        viewer.onerror = function() {
            loading.style.display = 'none';
            // If PDF fails to load in iframe, try to open in new tab
            window.open(filePath, '_blank');
            closeDocumentViewerModal();
        };
    } else {
        // For non-PDF files (images), open in new tab
        loading.style.display = 'none';
        window.open(filePath, '_blank');
        closeDocumentViewerModal();
    }
}
// Enhanced document viewing functionality
function initializeEnhancedDocumentsModal() {
    const viewDocumentsBtns = document.querySelectorAll('.view-documents-btn');
    const quickDocBtns = document.querySelectorAll('.quick-doc-btn');

    // Enhanced view documents button
    viewDocumentsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const scholarId = this.getAttribute('data-scholar-id');
            loadScholarDocumentsWithNavigation(scholarId);
            document.getElementById('documentViewerModal').classList.remove('hidden');
        });
    });

    // Quick document access buttons
    quickDocBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const scholarId = this.getAttribute('data-scholar-id');
            const docType = this.getAttribute('data-doc-type');
            loadAndOpenSpecificDocument(scholarId, docType);
        });
    });
}

// Load documents with navigation sidebar
async function loadScholarDocumentsWithNavigation(scholarId) {
    const navigation = document.getElementById('documentNavigation');
    const renewalInfo = document.getElementById('renewalInfoContent');
    
    // Show loading
    navigation.innerHTML = '<div class="text-center text-gray-500">Loading documents...</div>';
    renewalInfo.innerHTML = '<div class="text-center text-gray-500">Loading information...</div>';
    
    try {
        const response = await fetch(`/lydo_admin/get-scholar-documents/${scholarId}`);
        const data = await response.json();

        if (data.success && data.documents && data.documents.length > 0) {
            // Build document navigation
            let navHTML = '';
            let renewalHTML = '';
            
            data.documents.forEach((doc, index) => {
                const docTypes = [
                    { key: 'renewal_cert_of_reg', name: 'Certificate of Registration', icon: 'fa-file-certificate', color: 'green' },
                    { key: 'renewal_grade_slip', name: 'Grade Slip', icon: 'fa-chart-line', color: 'yellow' },
                    { key: 'renewal_brgy_indigency', name: 'Barangay Indigency', icon: 'fa-home', color: 'purple' }
                ];
                
                // Document navigation for this renewal
                navHTML += `
                    <div class="mb-4">
                        <div class="font-semibold text-gray-700 mb-2 flex items-center justify-between">
                            <span>Renewal ${index + 1}</span>
                            <span class="px-2 py-1 text-xs rounded-full ${getStatusBadgeClass(doc.renewal_status)}">
                                ${doc.renewal_status}
                            </span>
                        </div>
                        <div class="space-y-2 ml-2">
                `;
                
                docTypes.forEach(docType => {
                    const hasDocument = doc[docType.key];
                    navHTML += `
                        <button onclick="loadDocumentFromNavigation('${doc[docType.key]}', '${docType.name} - Renewal ${index + 1}', '${docType.key}')"
                                class="w-full text-left p-2 rounded-lg border transition-all flex items-center gap-2 ${hasDocument ? 'bg-white hover:bg-gray-50 border-gray-200' : 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'}">
                            <i class="fas ${docType.icon} text-${docType.color}-500"></i>
                            <span class="flex-1 text-sm">${docType.name}</span>
                            ${hasDocument ? '<i class="fas fa-eye text-blue-500"></i>' : '<i class="fas fa-times text-red-500"></i>'}
                        </button>
                    `;
                });
                
                navHTML += `</div></div>`;
                
                // Renewal information
                renewalHTML += `
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <h5 class="font-semibold text-sm mb-2">Renewal ${index + 1}</h5>
                        <div class="text-xs space-y-1">
                            <div><strong>Semester:</strong> ${doc.renewal_semester}</div>
                            <div><strong>Academic Year:</strong> ${doc.renewal_acad_year}</div>
                            <div><strong>Submitted:</strong> ${new Date(doc.date_submitted).toLocaleDateString()}</div>
                            <div><strong>Status:</strong> <span class="${getStatusTextClass(doc.renewal_status)}">${doc.renewal_status}</span></div>
                        </div>
                    </div>
                `;
            });
            
            navigation.innerHTML = navHTML;
            renewalInfo.innerHTML = renewalHTML;
            
            // Load first available document by default
            const firstDoc = data.documents[0];
            const docTypes = ['renewal_cert_of_reg', 'renewal_grade_slip', 'renewal_brgy_indigency'];
            const firstAvailableDocType = docTypes.find(type => firstDoc[type]);
            
            if (firstAvailableDocType) {
                const docName = getDocumentTypeName(firstAvailableDocType);
                loadDocumentFromNavigation(firstDoc[firstAvailableDocType], `${docName} - Renewal 1`, firstAvailableDocType);
            }
            
        } else {
            navigation.innerHTML = '<div class="text-center text-gray-500 text-sm">No documents found</div>';
            renewalInfo.innerHTML = '<div class="text-center text-gray-500 text-sm">No renewal information</div>';
        }
    } catch (error) {
        console.error('Error loading documents:', error);
        navigation.innerHTML = '<div class="text-center text-red-500 text-sm">Error loading documents</div>';
        renewalInfo.innerHTML = '<div class="text-center text-red-500 text-sm">Error loading information</div>';
    }
}

// Load and open specific document directly
async function loadAndOpenSpecificDocument(scholarId, docType) {
    try {
        const response = await fetch(`/lydo_admin/get-scholar-documents/${scholarId}`);
        const data = await response.json();

        if (data.success && data.documents && data.documents.length > 0) {
            const latestRenewal = data.documents[0]; // Get the latest renewal
            const documentUrl = latestRenewal[`renewal_${docType}`];
            const documentName = getDocumentTypeName(`renewal_${docType}`);
            
            if (documentUrl) {
                // Open the modal and load the document
                loadScholarDocumentsWithNavigation(scholarId);
                document.getElementById('documentViewerModal').classList.remove('hidden');
                
                // Wait a bit for the navigation to load, then select the document
                setTimeout(() => {
                    loadDocumentFromNavigation(documentUrl, documentName, `renewal_${docType}`);
                }, 500);
            } else {
                showError('Document not available');
            }
        } else {
            showError('No documents found for this scholar');
        }
    } catch (error) {
        console.error('Error loading specific document:', error);
        showError('Error loading document');
    }
}

// Load document in the viewer
function loadDocumentFromNavigation(documentUrl, title, documentType) {
    if (!documentUrl) {
        document.getElementById('noDocumentMessage').classList.remove('hidden');
        document.getElementById('documentViewer').style.display = 'none';
        document.getElementById('documentLoading').style.display = 'none';
        document.getElementById('downloadDocument').style.display = 'none';
        return;
    }

    const viewer = document.getElementById('documentViewer');
    const loading = document.getElementById('documentLoading');
    const downloadLink = document.getElementById('downloadDocument');
    const noDocumentMessage = document.getElementById('noDocumentMessage');
    const documentTitle = document.getElementById('documentTitle');
    
    // Set document title
    documentTitle.textContent = title;
    
    // Show loading, hide viewer and no document message
    loading.style.display = 'flex';
    viewer.style.display = 'none';
    noDocumentMessage.classList.add('hidden');
    downloadLink.style.display = 'flex';
    
    // Set download link
    downloadLink.href = documentUrl;
    downloadLink.download = documentUrl.split('/').pop();
    
    // Check if it's a PDF
    if (documentUrl.toLowerCase().endsWith('.pdf')) {
        // For PDF files, use the iframe
        viewer.src = documentUrl;
        viewer.onload = function() {
            loading.style.display = 'none';
            viewer.style.display = 'block';
        };
        viewer.onerror = function() {
            loading.style.display = 'none';
            noDocumentMessage.classList.remove('hidden');
        };
    } else {
        // For non-PDF files (images), open in new tab
        loading.style.display = 'none';
        window.open(documentUrl, '_blank');
    }
}

// Helper functions
function getDocumentTypeName(docTypeKey) {
    const names = {
        'renewal_cert_of_reg': 'Certificate of Registration',
        'renewal_grade_slip': 'Grade Slip',
        'renewal_brgy_indigency': 'Barangay Indigency'
    };
    return names[docTypeKey] || docTypeKey;
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'Approved': return 'bg-green-100 text-green-800';
        case 'Rejected': return 'bg-red-100 text-red-800';
        default: return 'bg-yellow-100 text-yellow-800';
    }
}

function getStatusTextClass(status) {
    switch(status) {
        case 'Approved': return 'text-green-600';
        case 'Rejected': return 'text-red-600';
        default: return 'text-yellow-600';
    }
}

// Update the DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', function() {
    initializeScholarData();
    initializeScholarPagination();
    initializeScholarFiltering();
    initializeEnhancedDocumentsModal(); // Replace initializeDocumentsModal with this
    
    // ... rest of your existing code
});
function closeDocumentViewerModal() {
    document.getElementById('documentViewerModal').classList.add('hidden');
    const viewer = document.getElementById('documentViewer');
    viewer.src = '';
    viewer.style.display = 'none';
}

// Utility functions
function showError(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}

function showSuccess(message) {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK'
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeScholarData();
    initializeScholarPagination();
    initializeScholarFiltering();
    initializeDocumentsModal();
    
    // Add document viewer modal to the page if it doesn't exist
    if (!document.getElementById('documentViewerModal')) {
        const documentViewerModal = document.createElement('div');
        documentViewerModal.id = 'documentViewerModal';
        documentViewerModal.className = 'fixed inset-0 hidden bg-black bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50';
        documentViewerModal.innerHTML = `
            <div class="bg-white w-full max-w-7xl max-h-8xl rounded-2xl shadow-2xl animate-fadeIn">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-alt text-blue-600"></i>
                        <span id="documentTitle">Document Viewer</span>
                    </h2>
                    <button onclick="closeDocumentViewerModal()"
                            class="p-2 rounded-full hover:bg-gray-100 transition">
                        <i class="fas fa-times text-gray-500 text-lg"></i>
                    </button>
                </div>

                <div class="p-6">
                    <div class="overflow-auto max-h-[70vh]">
                        <iframe id="documentViewer" src="" class="w-full h-[55vh] border rounded-lg" style="display: none;"></iframe>
                        <div id="documentLoading" class="flex items-center justify-center h-[55vh] text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p>Loading document...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 rounded-b-2xl">
                    <a id="downloadDocument" href="" target="_blank" download class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Download
                    </a>
                    <button onclick="closeDocumentViewerModal()"
                            class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                        Close
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(documentViewerModal);
    }
});