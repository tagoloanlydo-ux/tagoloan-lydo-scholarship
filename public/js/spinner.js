// spinner.js - WITH LINK NAVIGATION HANDLING - FIXED VERSION
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    
    // Check if we're returning from a page refresh
    const isPageRefresh = sessionStorage.getItem('pageRefresh') === 'true';

    // SHOW spinner
    function showSpinner() {
        if (loadingOverlay) {
            loadingOverlay.classList.remove('fade-out');
            loadingOverlay.style.display = 'flex';
        }
    }

    // HIDE spinner
    function hideSpinner() {
        if (loadingOverlay) {
            loadingOverlay.classList.add('fade-out');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 500);
        }
    }

    // ✅ Show spinner on ALL internal link clicks
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && !link.href.includes('#') && 
            link.target !== '_blank' && !link.hasAttribute('download')) {
            
            // Check if it's an internal link (same domain)
            const isInternalLink = link.hostname === window.location.hostname;
            if (isInternalLink) {
                showSpinner();
            }
        }
    });

    // ✅ Don't show spinner immediately if it's a page refresh
    if (!isPageRefresh) {
        showSpinner();
    }

    // ✅ Hide spinner when page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(hideSpinner, 300);
        sessionStorage.removeItem('pageRefresh');
    });

    // ✅ Handle browser back/forward navigation
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            showSpinner();
            setTimeout(hideSpinner, 300);
        }
    });

    // ✅ Handle page hide (for navigation)
    window.addEventListener('pagehide', function() {
        sessionStorage.setItem('pageNavigating', 'true');
    });

    // ✅ Check if we're arriving via navigation
    if (sessionStorage.getItem('pageNavigating') === 'true') {
        showSpinner();
        sessionStorage.removeItem('pageNavigating');
    }

    // ✅ Extra fallback safety after 3s max
    setTimeout(hideSpinner, 3000);
});

// Set flag before page unload
window.addEventListener('beforeunload', function() {
    sessionStorage.setItem('pageNavigating', 'true');
});