document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Function to show spinner - ONLY for page loads
    function showSpinner() {
        if (loadingOverlay) {
            loadingOverlay.classList.remove('fade-out');
            loadingOverlay.style.display = 'flex';
        }
    }

    // Function to hide spinner
    function hideSpinner() {
        if (loadingOverlay) {
            loadingOverlay.classList.add('fade-out');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 500); // Reduced timeout for better UX
        }
    }

    // Check if this is a refresh (page reload)
    const isRefresh = sessionStorage.getItem('pageRefresh') === 'true';

    // Clear the refresh flag
    sessionStorage.removeItem('pageRefresh');

    if (isRefresh) {
        // Show spinner only on refresh
        showSpinner();

        // Hide spinner when page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(hideSpinner, 500); // Small delay to ensure everything is loaded
        });

        // Fallback: hide spinner after 5 seconds max to prevent permanent loading
        setTimeout(hideSpinner, 5000);
    } else {
        // Hide spinner immediately for normal navigation
        hideSpinner();
    }
});

// Set refresh flag before page unload
window.addEventListener('beforeunload', function() {
    sessionStorage.setItem('pageRefresh', 'true');
});
