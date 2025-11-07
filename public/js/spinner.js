// spinner.js - WITH LINK NAVIGATION HANDLING
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');

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
        if (link && link.href && !link.href.includes('#')) {
            showSpinner();
        }
    });

    // ✅ Show spinner immediately during ANY page load
    showSpinner();

    // ✅ Hide spinner when page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(hideSpinner, 500);
    });

    // ✅ Handle browser back/forward navigation
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            showSpinner();
            setTimeout(hideSpinner, 500);
        }
    });

    // ✅ Extra fallback safety after 5s max
    setTimeout(hideSpinner, 5000);
});

window.addEventListener('beforeunload', function() {
    sessionStorage.setItem('pageRefresh', 'true');
});