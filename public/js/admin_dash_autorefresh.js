// admin_dash_autorefresh.js - SMOOTH VERSION
document.addEventListener('DOMContentLoaded', function() {
    const REFRESH_INTERVAL = 2000; // 2 seconds
    const REFRESH_URL = '/lydo_admin/dashboard-data';
    
    // Add subtle styles for smooth transitions
    const style = document.createElement('style');
    style.textContent = `
        .count-update {
            transition: all 0.3s ease;
        }
        
        .count-changing {
            color: #7e22ce;
            text-shadow: 0 0 8px rgba(126, 34, 206, 0.3);
        }
        
        .distribution-item {
            transition: all 0.4s ease;
        }
        
        .distribution-item-updating {
            opacity: 0.7;
            transform: translateX(-5px);
        }
        
        .distribution-item-new {
            animation: subtleSlideIn 0.5s ease-out;
        }
        
        @keyframes subtleSlideIn {
            from { 
                opacity: 0; 
                transform: translateY(5px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }
        
        .fade-update {
            animation: gentleFade 0.6s ease-in-out;
        }
        
        @keyframes gentleFade {
            0% { opacity: 1; }
            50% { opacity: 0.8; }
            100% { opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    let currentBarangayData = [];
    let currentSchoolData = [];

    async function fetchUpdatedData() {
        try {
            const response = await fetch(REFRESH_URL, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache'
                }
            });
            
            if (!response.ok) throw new Error(`Network response was not ok: ${response.status}`);
            
            const data = await response.json();
            updateDashboard(data);
            
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    }
    
    function updateDashboard(data) {
        // Update counts smoothly
        updateCountSmoothly('totalApplicants', data.totalApplicants);
        updateCountSmoothly('totalScholarsWholeYear', data.totalScholarsWholeYear);
        updateCountSmoothly('inactiveScholars', data.inactiveScholars);
        updateCountSmoothly('graduatedScholars', data.graduatedScholars);
        
        // Update distributions with minimal visual change
        updateDistributionSmoothly('barangayDistribution', data.barangayDistribution, 'applicant_brgy');
        updateDistributionSmoothly('schoolDistribution', data.schoolDistribution, 'applicant_school_name');
        
        // Update chart smoothly
        if (data.scholarStatsPerYear && window.scholarTrendsChart) {
            updateChartSmoothly(data.scholarStatsPerYear);
        }
    }
    
    function updateCountSmoothly(elementClass, newValue) {
        const elements = document.querySelectorAll('h3.text-4xl');
        if (elements.length >= 4) {
            const index = elementClass === 'totalApplicants' ? 0 : 
                         elementClass === 'totalScholarsWholeYear' ? 1 :
                         elementClass === 'inactiveScholars' ? 2 : 3;
            
            if (elements[index]) {
                const currentValue = parseInt(elements[index].textContent.replace(/,/g, ''));
                if (currentValue !== newValue) {
                    // Very subtle animation
                    elements[index].classList.add('count-changing');
                    elements[index].textContent = newValue;
                    
                    setTimeout(() => {
                        elements[index].classList.remove('count-changing');
                    }, 300);
                }
            }
        }
    }
    
    function updateDistributionSmoothly(type, newData, dataKey) {
        let container;
        let bgClass, countBgClass;
        
        if (type === 'barangayDistribution') {
            container = document.querySelector('.bg-white.rounded-xl.shadow-md.p-4:first-child .space-y-2');
            bgClass = 'bg-blue-50 border-blue-100';
            countBgClass = 'bg-blue-200 text-blue-800';
            currentData = currentBarangayData;
        } else {
            container = document.querySelector('.bg-white.rounded-xl.shadow-md.p-4:last-child .space-y-2');
            bgClass = 'bg-green-50 border-green-100';
            countBgClass = 'bg-green-200 text-green-800';
            currentData = currentSchoolData;
        }
        
        if (!container || !newData) return;
        
        // Check if data actually changed
        const newDataString = JSON.stringify(newData);
        const currentDataString = type === 'barangayDistribution' 
            ? JSON.stringify(currentBarangayData) 
            : JSON.stringify(currentSchoolData);
            
        if (newDataString === currentDataString) {
            return; // No changes, skip update
        }
        
        // Update stored data
        if (type === 'barangayDistribution') {
            currentBarangayData = [...newData];
        } else {
            currentSchoolData = [...newData];
        }
        
        // Get current items for comparison
        const currentItems = Array.from(container.children);
        
        // Update existing items or create new ones
        newData.forEach((item, index) => {
            const itemName = item[dataKey] || 'Unknown';
            const itemCount = item.count;
            
            // Try to find existing item
            let existingItem = currentItems.find(div => {
                const nameSpan = div.querySelector('span.text-sm.font-medium');
                return nameSpan && nameSpan.textContent === itemName;
            });
            
            if (existingItem) {
                // Update existing item count if changed
                const countSpan = existingItem.querySelector('span:last-child');
                const currentCount = parseInt(countSpan.textContent);
                
                if (currentCount !== itemCount) {
                    countSpan.textContent = itemCount;
                    existingItem.classList.add('fade-update');
                    setTimeout(() => existingItem.classList.remove('fade-update'), 600);
                }
                
                // Reorder if position changed
                if (existingItem.parentNode && index < currentItems.length && existingItem !== container.children[index]) {
                    container.insertBefore(existingItem, container.children[index]);
                }
            } else {
                // Create new item with subtle entrance
                const itemDiv = document.createElement('div');
                itemDiv.className = `flex justify-between items-center p-2 ${bgClass} rounded-lg border distribution-item distribution-item-new`;
                itemDiv.innerHTML = `
                    <span class="text-sm font-medium text-gray-700">${itemName}</span>
                    <span class="text-sm ${countBgClass} px-2 py-1 rounded-full font-medium">${itemCount}</span>
                `;
                
                if (index < container.children.length) {
                    container.insertBefore(itemDiv, container.children[index]);
                } else {
                    container.appendChild(itemDiv);
                }
                
                // Remove animation class after entrance
                setTimeout(() => {
                    itemDiv.classList.remove('distribution-item-new');
                }, 500);
            }
        });
        
        // Remove items that are no longer in the data
        currentItems.forEach(item => {
            const nameSpan = item.querySelector('span.text-sm.font-medium');
            if (nameSpan) {
                const itemName = nameSpan.textContent;
                const stillExists = newData.some(newItem => 
                    (newItem[dataKey] || 'Unknown') === itemName
                );
                
                if (!stillExists) {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-10px)';
                    setTimeout(() => {
                        if (item.parentNode) {
                            item.parentNode.removeChild(item);
                        }
                    }, 400);
                }
            }
        });
    }
    
    function updateChartSmoothly(newStats) {
        if (!window.scholarTrendsChart || !newStats) return;
        
        try {
            const academicYears = newStats.map(stat => stat.academic_year);
            const activeCounts = newStats.map(stat => stat.active);
            const inactiveCounts = newStats.map(stat => stat.inactive);
            const graduatedCounts = newStats.map(stat => stat.graduated);
            
            // Check if data actually changed
            const currentData = window.scholarTrendsChart.data;
            const dataChanged = 
                JSON.stringify(currentData.labels) !== JSON.stringify(academicYears) ||
                JSON.stringify(currentData.datasets[0].data) !== JSON.stringify(activeCounts) ||
                JSON.stringify(currentData.datasets[1].data) !== JSON.stringify(inactiveCounts) ||
                JSON.stringify(currentData.datasets[2].data) !== JSON.stringify(graduatedCounts);
            
            if (dataChanged) {
                // Update with very subtle animation
                window.scholarTrendsChart.data.labels = academicYears;
                window.scholarTrendsChart.data.datasets[0].data = activeCounts;
                window.scholarTrendsChart.data.datasets[1].data = inactiveCounts;
                window.scholarTrendsChart.data.datasets[2].data = graduatedCounts;
                
                window.scholarTrendsChart.update({
                    duration: 300, // Shorter duration
                    easing: 'easeOutQuart'
                });
            }
            
        } catch (error) {
            console.error('Error updating chart:', error);
        }
    }
    
    // Smart refresh - only refresh when tab is visible
    let refreshInterval;
    
    function startSmartRefresh() {
        // Stop existing interval
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        
        // Start new interval only if page is visible
        if (!document.hidden) {
            refreshInterval = setInterval(fetchUpdatedData, REFRESH_INTERVAL);
            console.log('🔄 Auto-refresh started (2s interval)');
        }
    }
    
    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
                console.log('⏸️ Auto-refresh paused (tab hidden)');
            }
        } else {
            startSmartRefresh();
            // Immediate refresh when coming back to tab
            setTimeout(fetchUpdatedData, 100);
        }
    });
    
    // Initialize
    function initialize() {
        // Store initial data
        const barangayContainer = document.querySelector('.bg-white.rounded-xl.shadow-md.p-4:first-child .space-y-2');
        const schoolContainer = document.querySelector('.bg-white.rounded-xl.shadow-md.p-4:last-child .space-y-2');
        
        if (barangayContainer) {
            currentBarangayData = Array.from(barangayContainer.children).map(div => {
                const name = div.querySelector('span.text-sm.font-medium').textContent;
                const count = parseInt(div.querySelector('span:last-child').textContent);
                return { applicant_brgy: name, count: count };
            });
        }
        
        if (schoolContainer) {
            currentSchoolData = Array.from(schoolContainer.children).map(div => {
                const name = div.querySelector('span.text-sm.font-medium').textContent;
                const count = parseInt(div.querySelector('span:last-child').textContent);
                return { applicant_school_name: name, count: count };
            });
        }
        
        // Start smart refresh
        startSmartRefresh();
        
        // Initial fetch
        setTimeout(fetchUpdatedData, 500);
    }
    
    // Wait a bit before initializing to ensure DOM is ready
    setTimeout(initialize, 100);
    
    // Debug functions (remove in production)
    window.dashboardRefresh = {
        forceRefresh: fetchUpdatedData,
        stopRefresh: () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                console.log('🛑 Auto-refresh stopped');
            }
        },
        startRefresh: startSmartRefresh
    };
});