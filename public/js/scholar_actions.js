// scholar_actions.js - Handle copy names and email functionality for scholars

// Copy Names functionality
function initializeCopyNamesFunctionality() {
    const copyNamesBtn = document.getElementById('copyNamesBtn');

    if (copyNamesBtn) {
        copyNamesBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.scholar-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'No Selection!',
                    text: 'Please select at least one scholar to copy names.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Group selected scholars by barangay
            const barangayGroups = {};
            selectedCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                const name = row.querySelector('td:nth-child(2) div').textContent.trim();
                const barangay = row.querySelector('td:nth-child(3) div').textContent.trim();
                if (!barangayGroups[barangay]) {
                    barangayGroups[barangay] = [];
                }
                barangayGroups[barangay].push(name);
            });

            // Sort barangays alphabetically
            const sortedBarangays = Object.keys(barangayGroups).sort();

            // Build the output string
            let output = '';
            sortedBarangays.forEach(barangay => {
                output += `${barangay}\n`;
                // Sort names alphabetically within each barangay
                barangayGroups[barangay].sort().forEach((name, idx) => {
                    output += `${idx + 1}. ${name}\n`;
                });
                output += '\n';
            });

            navigator.clipboard.writeText(output.trim()).then(() => {
                Swal.fire({
                    title: 'Success!',
                    text: 'Selected scholar names grouped by barangay copied to clipboard!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }).catch(err => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to copy names: ' + err,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
}

// Email functionality
function initializeEmailFunctionality() {
    const emailBtn = document.getElementById('emailBtn');
    const emailModal = document.getElementById('emailModal');
    const cancelEmail = document.getElementById('cancelEmail');
    const closeEmailModal = document.getElementById('closeEmailModal');
    const emailForm = document.getElementById('emailForm');
    const emailLoading = document.getElementById('emailLoading');
    const sendEmailButton = document.getElementById('sendEmailButton');

    // Open email modal
    if (emailBtn) {
        emailBtn.addEventListener('click', function() {
            const selectedEmails = Array.from(document.querySelectorAll('.scholar-checkbox:checked'))
                .map(checkbox => checkbox.value)
                .join(', ');

            const selectedScholarIds = Array.from(document.querySelectorAll('.scholar-checkbox:checked'))
                .map(checkbox => checkbox.getAttribute('data-scholar-id'))
                .join(', ');

            document.getElementById('emailTo').value = selectedEmails;
            document.getElementById('scholarId').value = selectedScholarIds;

            if (emailModal) {
                emailModal.classList.remove('hidden');
            }
        });
    }

    // Close email modal
    if (cancelEmail) {
        cancelEmail.addEventListener('click', function() {
            if (emailModal) {
                emailModal.classList.add('hidden');
            }
        });
    }

    // Close email modal with close button
    if (closeEmailModal) {
        closeEmailModal.addEventListener('click', function() {
            if (emailModal) {
                emailModal.classList.add('hidden');
            }
        });
    }

    // Handle email form submission
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading indicator
            if (emailLoading) {
                emailLoading.classList.remove('hidden');
            }
            if (sendEmailButton) {
                sendEmailButton.disabled = true;
            }

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading indicator
                if (emailLoading) {
                    emailLoading.classList.add('hidden');
                }
                if (sendEmailButton) {
                    sendEmailButton.disabled = false;
                }

                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Email sent successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    if (emailModal) {
                        emailModal.classList.add('hidden');
                    }
                    emailForm.reset();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to send email: ' + data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                // Hide loading indicator
                if (emailLoading) {
                    emailLoading.classList.add('hidden');
                }
                if (sendEmailButton) {
                    sendEmailButton.disabled = false;
                }
                Swal.fire({
                    title: 'Error!',
                    text: 'Error sending email: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const emailModal = document.getElementById('emailModal');
        if (e.target === emailModal) {
            emailModal.classList.add('hidden');
        }
    });
}

// Update button states based on selection
function updateActionButtons() {
    const copyNamesBtn = document.getElementById('copyNamesBtn');
    const emailBtn = document.getElementById('emailBtn');

    const selectedCount = document.querySelectorAll('.scholar-checkbox:checked').length;

    if (copyNamesBtn) {
        copyNamesBtn.disabled = selectedCount === 0;
    }
    if (emailBtn) {
        emailBtn.disabled = selectedCount === 0;
    }
}

// Initialize all functionalities when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCopyNamesFunctionality();
    initializeEmailFunctionality();

    // Update buttons on checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('scholar-checkbox')) {
            updateActionButtons();
        }
    });

    // Initial button state
    updateActionButtons();
});
