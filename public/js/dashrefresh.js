document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard refresh script loaded');
    
    // Card elements to refresh
    const cardElements = {
        applicants: {
            count: document.getElementById('applicantsCount'),
            card: document.getElementById('applicantsCount')?.closest('.bg-white')
        },
        pendingInitial: {
            count: document.getElementById('pendingInitialCount'),
            card: document.getElementById('pendingInitialCount')?.closest('.bg-white')
        },
        approvedRenewals: {
            count: document.getElementById('approvedRenewalsCount'),
            card: document.getElementById('approvedRenewalsCount')?.closest('.bg-white')
        },
        pendingRenewals: {
            count: document.getElementById('pendingRenewalsCount'),
            card: document.getElementById('pendingRenewalsCount')?.closest('.bg-white')
        }
    };

    // Applicants list elements
    const applicantsList = document.getElementById('applicantsList');
    const showingCount = document.getElementById('showingCount');
    const currentFilter = document.getElementById('currentFilter')?.value || 'all';

    // Log elements found
    console.log('Card elements found:', cardElements);
    console.log('Applicants list found:', applicantsList);

    // Function to update card with animation
    function updateCard(cardKey, newValue) {
        const element = cardElements[cardKey];
        if (!element.count || !element.card) {
            console.log(`Card element not found for: ${cardKey}`);
            return;
        }

        const currentValue = parseInt(element.count.textContent.replace(/,/g, ''));
        console.log(`Updating ${cardKey}: ${currentValue} -> ${newValue}`);
        
        if (currentValue === newValue) {
            console.log(`No change for ${cardKey}, skipping update`);
            return;
        }

        // Add updating animation
        element.card.classList.add('card-updating');
        
        // Smooth number transition
        animateCount(element.count, currentValue, newValue, 800);
        
        // Remove updating animation and add updated pulse
        setTimeout(() => {
            element.card.classList.remove('card-updating');
            element.card.classList.add('card-updated');
            
            setTimeout(() => {
                element.card.classList.remove('card-updated');
            }, 1000);
        }, 800);
    }

    // Function to animate number counting
    function animateCount(element, start, end, duration) {
        const startTime = performance.now();
        const difference = end - start;
        
        function updateNumber(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth animation
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const currentValue = Math.floor(start + (difference * easeOutQuart));
            
            element.textContent = currentValue.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            } else {
                element.textContent = end.toLocaleString();
                console.log(`Animation completed: ${end.toLocaleString()}`);
            }
        }
        
        requestAnimationFrame(updateNumber);
    }

    // Function to update applicants list
    function updateApplicantsList(data) {
        if (!applicantsList) {
            console.log('Applicants list element not found');
            return;
        }

        console.log('Updating applicants list with data:', data);

        if (data.length === 0) {
            applicantsList.innerHTML = '<div class="p-4 text-gray-500 text-center">0 applicants found.</div>';
            if (showingCount) {
                showingCount.textContent = 'Showing 0-0 of 0';
            }
        } else {
            applicantsList.innerHTML = data.map(applicant => {
                const remarkKey = applicant.remarks ? applicant.remarks.toLowerCase().replace(' ', '_') : '';
                let badgeClass = '';
                if (remarkKey === 'poor') badgeClass = 'bg-red-50 text-red-700 border-red-300';
                else if (remarkKey === 'non_poor') badgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-300';
                else if (remarkKey === 'ultra_poor') badgeClass = 'bg-purple-50 text-purple-700 border-purple-300';

                return `
                    <div class="p-4 hover:bg-gray-50 transition text-sm" data-id="${applicant.applicant_id}">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-base text-gray-800">${applicant.name}</h4>
                                <div class="text-gray-600 mt-1 text-sm">
                                    <span>${applicant.course}</span>
                                    <span class="mx-2">•</span>
                                    <span>${applicant.school}</span>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full border ${badgeClass}">
                                ${applicant.remarks || 'N/A'}
                            </span>
                        </div>
                    </div>
                `;
            }).join('');

            if (showingCount) {
                showingCount.textContent = `Showing 1-${data.length} of ${data.length}`;
            }
        }

        // Hide pagination when using auto-refresh
        const paginationFooter = document.querySelector('.p-4.border-t');
        if (paginationFooter) {
            paginationFooter.style.display = 'none';
        }
    }

    // Function to fetch updated card data
    async function fetchCardData() {
        try {
            console.log('Fetching card data...');
            const response = await fetch('/lydo_staff/dashboard/card-data', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                cache: 'no-cache'
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }

            const data = await response.json();
            console.log('Card data received:', data);
            
            // Update each card with new data
            if (data.applicantsCurrentYear !== undefined) {
                updateCard('applicants', data.applicantsCurrentYear);
            }
            if (data.pendingInitial !== undefined) {
                updateCard('pendingInitial', data.pendingInitial);
            }
            if (data.approvedRenewals !== undefined) {
                updateCard('approvedRenewals', data.approvedRenewals);
            }
            if (data.pendingRenewals !== undefined) {
                updateCard('pendingRenewals', data.pendingRenewals);
            }

        } catch (error) {
            console.error('Card refresh failed:', error);
        }
    }

    // Function to fetch updated applicants list
    async function fetchApplicantsList() {
        try {
            console.log('Fetching applicants list...');
            const response = await fetch(`/lydo_staff/dashboard/applicants-data?filter=${currentFilter}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                cache: 'no-cache'
            });

            console.log('Applicants list response status:', response.status);

            if (!response.ok) {
                throw new Error(`Network response was not ok: ${response.status}`);
            }

            const data = await response.json();
            console.log('Applicants list data received:', data);
            
            // Update applicants list
            updateApplicantsList(data);

        } catch (error) {
            console.error('Applicants list refresh failed:', error);
        }
    }

    // Function to fetch all data
    async function fetchAllData() {
        await Promise.all([
            fetchCardData(),
            fetchApplicantsList()
        ]);
    }

    // Start auto-refresh (every 5 seconds)
    let refreshInterval = setInterval(fetchAllData, 5000);
    console.log('Auto-refresh started, interval:', refreshInterval);

    // Pause refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            console.log('Page hidden, pausing refresh');
            clearInterval(refreshInterval);
        } else {
            console.log('Page visible, resuming refresh');
            refreshInterval = setInterval(fetchAllData, 5000);
        }
    });

    // Also pause when window loses focus
    window.addEventListener('blur', function() {
        console.log('Window blur, pausing refresh');
        clearInterval(refreshInterval);
    });

    window.addEventListener('focus', function() {
        console.log('Window focus, resuming refresh');
        refreshInterval = setInterval(fetchAllData, 5000);
    });

    // Initial fetch after 1 second
    setTimeout(fetchAllData, 1000);
});