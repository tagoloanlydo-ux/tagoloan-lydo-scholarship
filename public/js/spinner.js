document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Function to show spinner
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
            }, 1000); // Match the fadeOut animation duration
        }
    }
    
    // Show spinner when page starts loading
    showSpinner();
    
    // Hide spinner when page is fully loaded
    window.addEventListener('load', function() {
        hideSpinner();
    });
    
    // Detect browser tab refresh/reload
    let pageReloading = false;
    
    window.addEventListener('beforeunload', function() {
        pageReloading = true;
        showSpinner();
    });
    
    window.addEventListener('unload', function() {
        if (pageReloading) {
            showSpinner();
        }
    });
    
    // Detect browser back/forward navigation
    window.addEventListener('pageshow', function(event) {
        // If page is loaded from cache, hide spinner immediately
        if (event.persisted) {
            hideSpinner();
        }
    });
    
    // Detect AJAX requests (optional)
    let activeRequests = 0;
    
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        activeRequests++;
        showSpinner();
        
        return originalFetch.apply(this, args)
            .then(response => {
                activeRequests--;
                if (activeRequests === 0) {
                    hideSpinner();
                }
                return response;
            })
            .catch(error => {
                activeRequests--;
                if (activeRequests === 0) {
                    hideSpinner();
                }
                throw error;
            });
    };
});