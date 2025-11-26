// remarks-classification.js - Automatic remarks classification based on Per Capita Income

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing remarks classification system...');
    
    // Add event listener to automatically assign remarks when per capita income changes
    const perCapitaField = document.getElementById('per_capita_income');
    if (perCapitaField) {
        perCapitaField.addEventListener('input', autoAssignRemarks);
    }
    
    // Also recalculate when family members or incomes change
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'characterData') {
                setTimeout(autoAssignRemarks, 100);
            }
        });
    });
    
    // Observe family members table for changes
    const familyMembersTable = document.getElementById('family_members_tbody');
    if (familyMembersTable) {
        observer.observe(familyMembersTable, {
            childList: true,
            subtree: true,
            characterData: true
        });
    }
});

// Function to automatically assign remarks based on per capita income
function autoAssignRemarks() {
    const perCapitaIncome = parseFloat(document.getElementById('per_capita_income').value) || 0;
    const remarksSelect = document.getElementById('remarks');
    
    if (!remarksSelect) return;
    
    let suggestedRemark = '';
    let explanation = '';
    
    // Apply household classification criteria
    if (perCapitaIncome === 0) {
        // No income data yet
        return;
    } else if (perCapitaIncome < 1910) {
        suggestedRemark = 'Ultra Poor';
        explanation = 'Below ₱1,910 per capita income - cannot meet basic food needs';
    } else if (perCapitaIncome >= 1910 && perCapitaIncome <= 2759) {
        suggestedRemark = 'Poor';
        explanation = '₱1,910 – ₱2,759 per capita income - below poverty line; can meet food needs but not non-food essentials';
    } else if (perCapitaIncome > 2759) {
        suggestedRemark = 'Non Poor';
        explanation = 'Above ₱2,759 per capita income - can meet both food and basic non-food needs; not considered poor';
    }
    
    // Only auto-assign if no remark is currently selected or if we're giving a suggestion
    const currentRemark = remarksSelect.value;
    
    if (suggestedRemark && (!currentRemark || currentRemark === '')) {
        // Show suggestion to user
        showRemarkSuggestion(suggestedRemark, explanation, perCapitaIncome);
    } else if (suggestedRemark && currentRemark !== suggestedRemark) {
        // Show indicator that current selection doesn't match calculated classification
        showRemarkMismatchWarning(currentRemark, suggestedRemark, explanation, perCapitaIncome);
    } else {
        // Clear any warnings if they match
        clearRemarkWarnings();
    }
}

// Show suggestion for remark assignment
function showRemarkSuggestion(suggestedRemark, explanation, perCapitaIncome) {
    clearRemarkWarnings();
    
    const remarksContainer = document.getElementById('remarks').closest('div');
    if (!remarksContainer) return;
    
    const suggestionDiv = document.createElement('div');
    suggestionDiv.id = 'remark-suggestion';
    suggestionDiv.className = 'mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg';
    suggestionDiv.innerHTML = `
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-blue-500 mt-1 mr-2"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-800">
                    Suggested Remark: <strong>${suggestedRemark}</strong>
                </p>
                <p class="text-xs text-blue-600 mt-1">
                    ${explanation}
                </p>
                <p class="text-xs text-blue-500 mt-1">
                    Per Capita Income: ₱${perCapitaIncome.toFixed(2)}
                </p>
                <div class="mt-2 flex gap-2">
                    <button type="button" onclick="applySuggestedRemark('${suggestedRemark}')" 
                            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors">
                        <i class="fas fa-check mr-1"></i> Apply Suggestion
                    </button>
                    <button type="button" onclick="ignoreSuggestion()" 
                            class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-700 text-xs rounded transition-colors">
                        <i class="fas fa-times mr-1"></i> Ignore
                    </button>
                </div>
            </div>
        </div>
    `;
    
    remarksContainer.appendChild(suggestionDiv);
}

// Show warning when current selection doesn't match calculated classification
function showRemarkMismatchWarning(currentRemark, suggestedRemark, explanation, perCapitaIncome) {
    clearRemarkWarnings();
    
    const remarksContainer = document.getElementById('remarks').closest('div');
    if (!remarksContainer) return;
    
    const warningDiv = document.createElement('div');
    warningDiv.id = 'remark-mismatch-warning';
    warningDiv.className = 'mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg';
    warningDiv.innerHTML = `
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-2"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-yellow-800">
                    Classification Mismatch
                </p>
                <p class="text-xs text-yellow-700 mt-1">
                    Current selection: <strong>${currentRemark}</strong><br>
                    Calculated classification: <strong>${suggestedRemark}</strong>
                </p>
                <p class="text-xs text-yellow-600 mt-1">
                    ${explanation}
                </p>
                <p class="text-xs text-yellow-500 mt-1">
                    Per Capita Income: ₱${perCapitaIncome.toFixed(2)}
                </p>
                <div class="mt-2 flex gap-2">
                    <button type="button" onclick="applySuggestedRemark('${suggestedRemark}')" 
                            class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors">
                        <i class="fas fa-sync-alt mr-1"></i> Use Calculated
                    </button>
                    <button type="button" onclick="keepCurrentRemark()" 
                            class="px-3 py-1 bg-gray-300 hover:bg-gray-400 text-gray-700 text-xs rounded transition-colors">
                        <i class="fas fa-times mr-1"></i> Keep Current
                    </button>
                </div>
            </div>
        </div>
    `;
    
    remarksContainer.appendChild(warningDiv);
}

// Clear all remark warnings and suggestions
function clearRemarkWarnings() {
    const existingSuggestion = document.getElementById('remark-suggestion');
    const existingWarning = document.getElementById('remark-mismatch-warning');
    
    if (existingSuggestion) existingSuggestion.remove();
    if (existingWarning) existingWarning.remove();
}

// Apply the suggested remark
function applySuggestedRemark(remark) {
    const remarksSelect = document.getElementById('remarks');
    if (remarksSelect) {
        remarksSelect.value = remark;
        validateRemarks(); // Update the submit button state
        
        // Show confirmation
        Swal.fire({
            icon: 'success',
            title: 'Remark Applied',
            text: `Remark set to "${remark}" based on per capita income classification`,
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    clearRemarkWarnings();
}

// Ignore the suggestion
function ignoreSuggestion() {
    clearRemarkWarnings();
    
    // Optionally, store in sessionStorage to not show again for this session
    sessionStorage.setItem('ignoreRemarkSuggestions', 'true');
}

// Keep current remark selection
function keepCurrentRemark() {
    clearRemarkWarnings();
}

// Enhanced calculatePerCapitaIncome function with automatic remark assignment
function calculatePerCapitaIncome() {
    // Get total net income
    const netIncome = parseFloat(document.getElementById('house_net_income').value) || 0;
    
    // Count number of family members (including head of family)
    const familyRows = document.querySelectorAll('#family_members_tbody tr');
    const numberOfFamilyMembers = familyRows.length + 1; // +1 for head of family
    
    // Calculate per capita income
    const perCapitaIncome = numberOfFamilyMembers > 0 ? (netIncome / numberOfFamilyMembers) : 0;
    
    // Update the readonly fields
    const numMembersField = document.getElementById('number_of_family_members');
    const perCapitaField = document.getElementById('per_capita_income');
    
    if (numMembersField) numMembersField.value = numberOfFamilyMembers;
    if (perCapitaField) perCapitaField.value = perCapitaIncome.toFixed(2);
    
    // Update hidden fields for form submission (if they exist)
    const numMembersHidden = document.getElementById('number_of_family_members_hidden');
    const perCapitaHidden = document.getElementById('per_capita_income_hidden');
    
    if (numMembersHidden) numMembersHidden.value = numberOfFamilyMembers;
    if (perCapitaHidden) perCapitaHidden.value = perCapitaIncome.toFixed(2);
    
    // Trigger automatic remark assignment
    setTimeout(autoAssignRemarks, 100);
}

// Override the existing calculateIncomes function to include per capita calculation
const originalCalculateIncomes = window.calculateIncomes;
window.calculateIncomes = function() {
    if (originalCalculateIncomes) {
        originalCalculateIncomes();
    }
    
    // Calculate per capita income
    calculatePerCapitaIncome();
};

// Initialize when modal opens
function initializePerCapitaCalculation() {
    // Recalculate when modal opens
    setTimeout(function() {
        calculateIncomes();
        autoAssignRemarks();
    }, 100);
}

// Add classification guidelines display
function addClassificationGuidelines() {
    const remarksTab = document.getElementById('tab-remarks-content');
    if (!remarksTab) return;
    
    // Check if guidelines already exist
    if (document.getElementById('classification-guidelines')) return;
    
    const guidelinesHtml = `
        <div id="classification-guidelines" class="mb-6 p-4 bg-purple-50 rounded-xl border border-purple-200">
            <h4 class="font-semibold text-purple-800 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Classification Guidelines (Per Capita Income)
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="font-semibold text-red-700">Ultra Poor</div>
                    <div class="text-red-600 text-xs mt-1">Below ₱1,910</div>
                    <div class="text-red-500 text-xs mt-1">Cannot meet basic food needs</div>
                </div>
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="font-semibold text-yellow-700">Poor</div>
                    <div class="text-yellow-600 text-xs mt-1">₱1,910 – ₱2,759</div>
                    <div class="text-yellow-500 text-xs mt-1">Below poverty line; can meet food needs but not non-food essentials</div>
                </div>
                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="font-semibold text-green-700">Non Poor</div>
                    <div class="text-green-600 text-xs mt-1">Above ₱2,759</div>
                    <div class="text-green-500 text-xs mt-1">Can meet both food and basic non-food needs</div>
                </div>
            </div>
        </div>
    `;
    
    // Insert before the financial summary
    const financialSummary = remarksTab.querySelector('.bg-green-50');
    if (financialSummary) {
        financialSummary.insertAdjacentHTML('beforebegin', guidelinesHtml);
    }
}

// Enhanced showTab function to include guidelines
const originalShowTab = window.showTab;
window.showTab = function(tabName) {
    if (originalShowTab) {
        originalShowTab(tabName);
    }
    
    if (tabName === 'remarks') {
        setTimeout(addClassificationGuidelines, 100);
    }
};