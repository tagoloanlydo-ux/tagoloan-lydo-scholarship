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
});
