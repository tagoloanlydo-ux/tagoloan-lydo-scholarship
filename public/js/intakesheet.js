// Wizard state
let currentStep = 1;
const totalSteps = 4;
const progressBar = document.getElementById("progressBar");

// localStorage key
const STORAGE_KEY = 'familyIntakeFormData';

// Generate serial number
function generateSerialNumber() {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');
  const serial = `FIS-${year}${month}${day}-${hours}${minutes}${seconds}`;
  return serial;
}

// Save form data to localStorage
function saveFormData() {
  try {
    const data = collectData();
    data.currentStep = currentStep;
    
    // Check if data is too large
    const dataString = JSON.stringify(data);
    if (dataString.length > 5000000) { // 5MB limit
      console.warn('Data too large, clearing some data');
    }
    
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  } catch (e) {
    console.error('Error saving to localStorage:', e);
    // Fallback: save without signatures
    const data = collectData();
    data.currentStep = currentStep;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  }
}

// Load form data from localStorage
function loadFormData() {
  const saved = localStorage.getItem(STORAGE_KEY);
  if (saved) {
    const data = JSON.parse(saved);
    populateForm(data);
    currentStep = data.currentStep || 1;
    showStep(currentStep);
  }
}

// Populate form with loaded data
function populateForm(data) {
  // Head of family - only set if field is empty to preserve initial applicant values
  if (!document.getElementById('applicant_fname').value) {
    document.getElementById('applicant_fname').value = data.head.fname || '';
  }
  if (!document.getElementById('applicant_mname').value) {
    document.getElementById('applicant_mname').value = data.head.mname || '';
  }
  if (!document.getElementById('applicant_lname').value) {
    document.getElementById('applicant_lname').value = data.head.lname || '';
  }
  if (!document.getElementById('applicant_suffix').value) {
    document.getElementById('applicant_suffix').value = data.head.suffix || '';
  }
  document.getElementById('head_4ps').value = data.head._4ps || '';
  document.getElementById('head_ipno').value = data.head.ipno || '';
  document.getElementById('head_address').value = data.head.address || '';
  document.getElementById('head_zone').value = data.head.zone || '';
  if (!document.getElementById('applicant_brgy').value) {
    document.getElementById('applicant_brgy').value = data.head.barangay || '';
  }
  if (!document.getElementById('applicant_bdate').value) {
    document.getElementById('applicant_bdate').value = data.head.dob || '';
  }
  document.getElementById('head_pob').value = data.head.pob || '';
  document.getElementById('head_educ').value = data.head.educ || '';
  document.getElementById('head_occ').value = data.head.occ || '';
  document.getElementById('head_religion').value = data.head.religion || '';

  // Sex radio - only set if no radio is currently checked
  const currentSex = document.querySelector('input[name="applicant_gender"]:checked')?.value;
  if (!currentSex && data.head.sex && data.head.sex !== '') {
    document.querySelectorAll('input[name="applicant_gender"]').forEach(radio => radio.checked = false);
    document.querySelector(`input[name="applicant_gender"][value="${data.head.sex}"]`).checked = true;
  }

  // Location radio
  if (data.location) {
    document.querySelector(`input[name="location"][value="${data.location}"]`).checked = true;
  }

  // Family members
  const tbody = document.querySelector('#familyTable tbody');
  const initialRow = tbody.rows[0].cloneNode(true);
  tbody.innerHTML = '';
  if (data.family.length === 0) {
    tbody.appendChild(initialRow);
  } else {
    data.family.forEach((member) => {
      const row = initialRow.cloneNode(true);
      row.querySelector('.fm-name').value = member.name || '';
      row.querySelector('.fm-relation').value = member.relation || '';
      row.querySelector('.fm-birth').value = member.birth || '';
      row.querySelector('.fm-age').value = member.age || '';
      row.querySelector('.fm-sex').value = member.sex || '';
      row.querySelector('.fm-civil').value = member.civil || '';
      row.querySelector('.fm-educ').value = member.educ || '';
      row.querySelector('.fm-occ').value = member.occ || '';
      row.querySelector('.fm-income').value = member.income || '';
      row.querySelector('.fm-remarks').value = member.remarks || '';
      tbody.appendChild(row);
    });
  }

  // Household
  document.getElementById('house_total_income').value = data.house.total_income || '';
  document.getElementById('house_net_income').value = data.house.net_income || '';
  document.getElementById('other_income').value = data.house.other_income || '';
  document.getElementById('house_house').value = data.house.house || '';
  document.getElementById('house_lot').value = data.house.lot || '';
  document.getElementById('house_value').value = data.house.house_value || '';
  document.getElementById('lot_value').value = data.house.lot_value || '';
  document.getElementById('house_rent').value = data.house.house_rent || '';
  document.getElementById('lot_rent').value = data.house.lot_rent || '';
  document.getElementById('house_water').value = data.house.water || '';
  document.getElementById('house_electric').value = data.house.electric || '';
}

// Clear localStorage on submit
function clearFormData() {
  localStorage.removeItem(STORAGE_KEY);
}

// Show loading spinner
function showLoadingSpinner() {
  const overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.style.display = 'flex';
  }
}

// Hide loading spinner
function hideLoadingSpinner() {
  const overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.style.display = 'none';
  }
}

function showStep(step) {
  document
    .querySelectorAll(".step")
    .forEach((el, i) => el.classList.toggle("hidden", i + 1 !== step));
  document.getElementById("prevBtn").style.display =
    step === 1 ? "none" : "inline-block";
  document.getElementById("nextBtn").innerText =
    step === totalSteps ? "Submit" : "Next";
  progressBar.style.width = (step / totalSteps) * 100 + "%";
  
  // Adjust navigation alignment
  const navControls = document.getElementById("navControls");
  if (step === 1) {
    navControls.classList.remove('justify-between');
    navControls.classList.add('justify-end');
  } else {
    navControls.classList.remove('justify-end');
    navControls.classList.add('justify-between');
  }
  if (step === 2) {
    validateStep2();
  }
  if (step === 3) {
    validateStep3();
  }
  if (step === totalSteps) {
    populateReview();
    document.getElementById("reviewArea").classList.remove("hidden");
  } else {
    document.getElementById("reviewArea").classList.add("hidden");
  }
  saveFormData();
}

function nextPrev(delta) {
  if (delta === 1 && currentStep === totalSteps) {
    // Already at review; Next is disabled here (Review button shown). Use buttons in review.
    return;
  }
  currentStep += delta;
  if (currentStep < 1) currentStep = 1;
  if (currentStep > totalSteps) currentStep = totalSteps;
  showStep(currentStep);
}

function handleNext() {
  if (currentStep === totalSteps) {
    if (validateAllSteps()) {
      submitForm();
    } else {
      Swal.fire({
        title: 'Validation Error',
        text: 'Please fill all required fields before submitting.',
        icon: 'warning',
        confirmButtonText: 'OK'
      });
    }
  } else {
    // Perform validation for the current step
    if (currentStep === 1) {
      validateStep1();
    } else if (currentStep === 2) {
      validateStep2();
    } else if (currentStep === 3) {
      validateStep3();
    }
    // Check if the Next button is enabled after validation
    const nextBtn = document.getElementById('nextBtn');
    if (!nextBtn.disabled) {
      nextPrev(1);
    }
  }
}

// Calculate age from birthdate
function calculateAge(birthdate) {
  if (!birthdate) return "";
  const today = new Date();
  const birth = new Date(birthdate);
  
  // VALIDATION ADDED
  if (isNaN(birth.getTime())) {
    return "Invalid date";
  }
  if (birth > today) {
    return "Future date";
  }
  
  let age = today.getFullYear() - birth.getFullYear();
  const monthDiff = today.getMonth() - birth.getMonth();
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
    age--;
  }
  return age;
}

function attachBirthdateListener(birthInput) {
  birthInput.addEventListener('change', function() {
    updateAgeFromBirthdate(this);
  });
  
  // INPUT EVENT LISTENER ADDED
  birthInput.addEventListener('input', function() {
    updateAgeFromBirthdate(this);
  });
}

function updateAgeFromBirthdate(birthInput) {
  const ageInput = birthInput.closest('tr').querySelector('.fm-age');
  const age = calculateAge(birthInput.value);
  ageInput.value = age;
  
  // VISUAL FEEDBACK ADDED
  if (age === "Invalid date" || age === "Future date") {
    ageInput.style.color = 'red';
    ageInput.title = age;
  } else {
    ageInput.style.color = '';
    ageInput.title = '';
  }
}

// Calculate total family income and net income
function calculateTotalFamilyIncome() {
  let familyTotal = 0;
  document.querySelectorAll('.fm-income').forEach(input => {
    const value = parseFloat(input.value) || 0;
    familyTotal += value;
  });
  const otherIncome = parseFloat(document.getElementById('other_income').value) || 0;
  const totalIncome = familyTotal + otherIncome;
  document.getElementById('house_total_income').value = totalIncome;
  // Calculate expenses
  const houseRent = parseFloat(document.getElementById('house_rent').value) || 0;
  const lotRent = parseFloat(document.getElementById('lot_rent').value) || 0;
  const electricity = parseFloat(document.getElementById('house_electric').value) || 0;
  const water = parseFloat(document.getElementById('house_water').value) || 0;
  const totalExpenses = houseRent + lotRent + electricity + water;
  const netIncome = totalIncome - totalExpenses;
  document.getElementById('house_net_income').value = netIncome;
}

// Toggle rent amount inputs
function toggleRentInputs() {
  const houseSelect = document.getElementById('house_house');
  const lotSelect = document.getElementById('house_lot');
  const houseRentGroup = document.getElementById('house_rent_group');
  const lotRentGroup = document.getElementById('lot_rent_group');

  if (houseSelect.value === 'Rented') {
    houseRentGroup.classList.remove('hidden');
  } else {
    houseRentGroup.classList.add('hidden');
    document.getElementById('house_rent').value = '';
  }

  if (lotSelect.value === 'Rented') {
    lotRentGroup.classList.remove('hidden');
  } else {
    lotRentGroup.classList.add('hidden');
    document.getElementById('lot_rent').value = '';
  }
}

// Family rows
function addRow() {
  const table = document
    .getElementById("familyTable")
    .querySelector("tbody");
  const row = table.rows[0].cloneNode(true);
  row.querySelectorAll("input").forEach((i) => (i.value = ""));
  row.querySelectorAll("select").forEach((s) => (s.selectedIndex = 0));
  // Make age readonly WITH PLACEHOLDER
  const ageInput = row.querySelector('.fm-age');
  ageInput.readOnly = true;
  ageInput.placeholder = "Auto-calculated"; // PLACEHOLDER ADDED
  // Attach listener to new birthdate
  attachBirthdateListener(row.querySelector('.fm-birth'));

  // In window load - WITH PLACEHOLDER
  document.querySelectorAll('.fm-birth').forEach(attachBirthdateListener);
  document.querySelectorAll('.fm-age').forEach(ageInput => {
    ageInput.readOnly = true;
    ageInput.placeholder = "Auto-calculated"; // PLACEHOLDER ADDED
  });
  table.appendChild(row);
  validateStep2();
}

function deleteRow(btn) {
  const table = document
    .getElementById("familyTable")
    .querySelector("tbody");
  if (table.rows.length > 1) {
    btn.closest("tr").remove();
    calculateTotalFamilyIncome();
    validateStep2();
  } else alert("At least one family member row is required.");
}

// Collect data - FIXED VERSION with proper rent field collection
function collectData() {
  // Get the values directly from the form inputs
  const fname = document.getElementById('applicant_fname').value;
  const mname = document.getElementById('applicant_mname').value;
  const lname = document.getElementById('applicant_lname').value;
  const suffix = document.getElementById('applicant_suffix').value;
  
  // Head data
  const head = {
    _4ps: getVal("head_4ps"),
    ipno: getVal("head_ipno"),
    address: getVal("head_address"),
    zone: getVal("head_zone"),
    barangay: getVal("applicant_brgy"),
    dob: getVal("applicant_bdate"),
    pob: getVal("head_pob"),
    educ: getVal("head_educ"),
    occ: getVal("head_occ"),
    religion: getVal("head_religion"),
    sex: document.querySelector('input[name="applicant_gender"]:checked')?.value || "",
    serial: getVal("serial_number"),
    fname: fname,
    mname: mname,
    lname: lname,
    suffix: suffix
  };

  const location = document.querySelector('input[name="location"]:checked')?.value || "";

  const family = [];
  document.querySelectorAll("#familyTable tbody tr").forEach((tr) => {
    const name = tr.querySelector(".fm-name")?.value || "";
    const relation = tr.querySelector(".fm-relation")?.value || "";
    const birth = tr.querySelector(".fm-birth")?.value || "";
    const age = tr.querySelector(".fm-age")?.value || "";
    const sex = tr.querySelector(".fm-sex")?.value || "";
    const civil = tr.querySelector(".fm-civil")?.value || "";
    const educ = tr.querySelector(".fm-educ")?.value || "";
    const occ = tr.querySelector(".fm-occ")?.value || "";
    const income = tr.querySelector(".fm-income")?.value || "";
    const remarks = tr.querySelector(".fm-remarks")?.value || "";
    
    if (name.trim() !== '') {
      family.push({
        name: name,
        relation: relation,
        birth: birth,
        age: age,
        sex: sex,
        civil: civil,
        educ: educ,
        occ: occ,
        income: income,
        remarks: remarks,
      });
    }
  });

  // FIXED: Properly collect rent values with proper field names
  const house = {
    total_income: getVal("house_total_income"),
    net_income: getVal("house_net_income"),
    other_income: getVal("other_income"),
    house: getVal("house_house"),
    lot: getVal("house_lot"),
    house_value: getVal("house_value"),
    lot_value: getVal("lot_value"),
    house_rent: getVal("house_rent"),  // Make sure this matches your database field
    lot_rent: getVal("lot_rent"),      // Make sure this matches your database field
    water: getVal("house_water"),
    electric: getVal("house_electric"),
  };

  return {
    head: head,
    location: location,
    family: family,
    house: house,
    application_personnel_id: document.querySelector('input[name="application_personnel_id"]').value,
    token: document.querySelector('input[name="token"]').value
  };
}
function getVal(id) {
  const el = document.getElementById(id);
  return el ? el.value : "";
}

// Populate review area - FIXED VERSION
function populateReview() {
  const d = collectData();
  let serial = d.head.serial;
  if (!serial) {
    serial = generateSerialNumber();
    document.getElementById('serial_number').value = serial;
    d.head.serial = serial;
    saveFormData();
  }
  
  document.getElementById("rv_serial").innerText = serial;
  document.getElementById("printSerial").innerText = serial;
  document.getElementById("printSerialNumber").textContent = serial;

  // FIX: Get full name directly from form inputs to ensure it shows correctly
  const fullName = [
    document.getElementById('applicant_fname').value,
    document.getElementById('applicant_mname').value,
    document.getElementById('applicant_lname').value,
    document.getElementById('applicant_suffix').value
  ].filter(Boolean).join(" ");
  
  document.getElementById("rv_head_name").innerText = fullName;
  
  document.getElementById("rv_head_table").innerHTML = `
    <table class="min-w-full text-sm">
      <tr>
        <td><strong>Sex:</strong> ${d.head.sex || "-"}</td>
        <td><strong>4Ps:</strong> ${d.head._4ps || "-"}</td>
        <td><strong>IP No.:</strong> ${d.head.ipno || "-"}</td>
      </tr>
      <tr>
        <td><strong>Address:</strong> ${d.head.address || "-"}</td>
        <td><strong>Zone:</strong> ${d.head.zone || "-"}</td>
        <td><strong>Barangay:</strong> ${d.head.barangay || "-"}</td>
        <td><strong>Location:</strong> ${d.location || "-"}</td>
      </tr>
      <tr>
        <td><strong>Date of Birth:</strong> ${formatDate(d.head.dob) || "-"}</td>
        <td><strong>Place of Birth:</strong> ${d.head.pob || "-"}</td>
      </tr>
      <tr>
        <td><strong>Educational Attainment:</strong> ${d.head.educ || "-"}</td>
        <td><strong>Occupation:</strong> ${d.head.occ || "-"}</td>
        <td><strong>Religion:</strong> ${d.head.religion || "-"}</td>
      </tr>
    </table>
  `;
  
  document.getElementById("rv_total_income").innerText = d.house.total_income || "-";
  document.getElementById("rv_net_income").innerText = d.house.net_income || "-";
  document.getElementById("rv_other_income").innerText = d.house.other_income || "-";
  document.getElementById("rv_house").innerText = d.house.house || "-";
  document.getElementById("rv_lot").innerText = d.house.lot || "-";
  document.getElementById("rv_water").innerText = d.house.water || "-";
  document.getElementById("rv_electric").innerText = d.house.electric || "-";
  
  document.getElementById("rv_house_rent_display").innerHTML =
    d.house.house === 'Rented' ? `<strong>House Monthly Rent:</strong> ₱${d.house.house_rent || '-'}` : '';
  document.getElementById("rv_lot_rent_display").innerHTML =
    d.house.lot === 'Rented' ? `<strong>Lot Monthly Rent:</strong> ₱${d.house.lot_rent || '-'}` : '';

  // Family table build
  const tbody = document.querySelector("#rv_family_table tbody");
  tbody.innerHTML = "";
  d.family.forEach((f) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${escapeHtml(f.name)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${escapeHtml(f.relation)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${formatDate(f.birth)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${escapeHtml(f.age)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${escapeHtml(f.sex)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${escapeHtml(f.civil)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${escapeHtml(f.educ)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${escapeHtml(f.occ)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">₱${escapeHtml(f.income)}</td>
    <td class="border px-1 md:px-2 py-1 text-center text-xs md:text-sm">${escapeHtml(f.remarks)}</td>
  `;
    tbody.appendChild(tr);
  });
}

function formatDate(dateString) {
  if (!dateString) return "-";
  const date = new Date(dateString);
  if (isNaN(date)) return dateString;
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('en-US', options);
}

function escapeHtml(s) {
  if (!s) return "";
  return s.replace(
    /[&<>"']/g,
    (m) =>
      ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
      }[m])
  );
}

// Validate Step 1 fields and disable Next button if required fields are empty
function validateStep1() {
  const requiredFields = [
    'head_4ps',
    'head_address',
    'head_zone',
    'head_pob',
    'head_educ',
    'head_occ',
    'head_religion'
  ];
  const nextBtn = document.getElementById('nextBtn');
  let allFilled = true;
  requiredFields.forEach(id => {
    const el = document.getElementById(id);
    if (!el || !el.value.trim()) {
      allFilled = false;
    }
  });
  nextBtn.disabled = !allFilled;
  nextBtn.style.opacity = allFilled ? '1' : '0.5';
}



// Validate Step 2: Disable Next if any row has data but empty name
function validateStep2() {
  const nextBtn = document.getElementById('nextBtn');
  let canProceed = true;
  document.querySelectorAll('#familyTable tbody tr').forEach(tr => {
    const name = tr.querySelector('.fm-name')?.value.trim() || '';
    const relation = tr.querySelector('.fm-relation')?.value || '';
    const birth = tr.querySelector('.fm-birth')?.value || '';
    const age = tr.querySelector('.fm-age')?.value || '';
    const sex = tr.querySelector('.fm-sex')?.value || '';
    const civil = tr.querySelector('.fm-civil')?.value || '';
    const educ = tr.querySelector('.fm-educ')?.value || '';
    const occ = tr.querySelector('.fm-occ')?.value || '';
    const income = tr.querySelector('.fm-income')?.value || '';
    const remarks = tr.querySelector('.fm-remarks')?.value || '';
    const hasData = name || relation || birth || age || sex || civil || educ || occ || income || remarks;
    if (hasData && !name) {
      canProceed = false;
    }
  });
  nextBtn.disabled = !canProceed;
  nextBtn.style.opacity = canProceed ? '1' : '0.5';
}

// Validate Step 3: Disable Next if house ownership is not selected or rent amounts are missing when rented
function validateStep3() {
  const nextBtn = document.getElementById('nextBtn');
  const houseSelect = document.getElementById('house_house');
  const lotSelect = document.getElementById('house_lot');
  let isFilled = houseSelect && houseSelect.value.trim() !== '' && lotSelect && lotSelect.value.trim() !== '';

  // Check if rent amounts are provided when rented
  if (houseSelect.value === 'Rented') {
    const houseRent = document.getElementById('house_rent').value.trim();
    if (!houseRent) isFilled = false;
  }
  if (lotSelect.value === 'Rented') {
    const lotRent = document.getElementById('lot_rent').value.trim();
    if (!lotRent) isFilled = false;
  }

  nextBtn.disabled = !isFilled;
  nextBtn.style.opacity = isFilled ? '1' : '0.5';
}

// Validate all steps before submission
function validateAllSteps() {
  const head = collectData().head;
  
  // Check required head fields
  const requiredHeadFields = ['_4ps', 'address', 'zone', 'pob', 'educ', 'occ', 'religion'];
  const missingFields = requiredHeadFields.filter(field => !head[field] || head[field].trim() === '');
  
  if (missingFields.length > 0) {
    console.log('Missing head fields:', missingFields);
    return false;
  }
  
  // Check if at least one family member exists
  const family = collectData().family;
  if (family.length === 0) {
    console.log('No family members added');
    return false;
  }
  
  return true;
}

async function submitForm() {
  // Show confirmation dialog
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'Are you sure you want to submit the family intake sheet?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, submit it!',
    cancelButtonText: 'Cancel'
  });

  if (!result.isConfirmed) {
    return; // User cancelled
  }

  // Show loading spinner
  showLoadingSpinner();

  try {
    const data = collectData();

    console.log('Submitting data:', data);

    const response = await fetch('/submit-intake-sheet', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(data)
    });

    const result = await response.json();

    if (response.ok) {
      hideLoadingSpinner();
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        html: `
          <div style="text-align: left;">
            <p>${result.message || 'Family intake sheet submitted successfully!'}</p>
            <br>
            <p><strong>Important Next Steps:</strong></p>
            <p>✓ Please wait for the LYDO staff to inform you about the date and time of your face-to-face interview</p>
            <p>✓ You will receive notification through SMS and email</p>
            <p>✓ You can also check the announcement section for updates</p>
          </div>
        `,
        confirmButtonText: 'OK',
        width: '600px'
      }).then(() => {
        clearFormData();
        // Redirect to home page
        window.location.href = '/';
      });
    } else {
      hideLoadingSpinner();
      Swal.fire({
        icon: 'error',
        title: 'Submission Failed',
        text: result.message || 'Error submitting form. Please try again.',
        confirmButtonText: 'OK'
      });
    }
  } catch (error) {
    hideLoadingSpinner();
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'An unexpected error occurred. Please try again.',
      confirmButtonText: 'OK'
    });
  }
}

// Setup on load
window.addEventListener("load", () => {
  loadFormData();
  showStep(currentStep);
  
  // Attach listeners to existing birthdate inputs and make age readonly
  document.querySelectorAll('.fm-birth').forEach(attachBirthdateListener);
  document.querySelectorAll('.fm-age').forEach(ageInput => ageInput.readOnly = true);

  // Attach save listeners to all inputs
  document.querySelectorAll('input, select, textarea').forEach(el => {
    el.addEventListener('input', saveFormData);
    el.addEventListener('change', saveFormData);
  });

  // Attach listener to other_income for net income calculation
  document.getElementById('other_income').addEventListener('input', calculateTotalFamilyIncome);

  // Attach listeners to all existing family member income inputs
  document.querySelectorAll('.fm-income').forEach(input => input.addEventListener('input', calculateTotalFamilyIncome));

  // Attach listeners to expense inputs for net income calculation
  document.getElementById('house_rent').addEventListener('input', calculateTotalFamilyIncome);
  document.getElementById('lot_rent').addEventListener('input', calculateTotalFamilyIncome);
  document.getElementById('house_electric').addEventListener('input', calculateTotalFamilyIncome);
  document.getElementById('house_water').addEventListener('input', calculateTotalFamilyIncome);

  // Attach listeners to house and lot selects for toggling rent inputs
  document.getElementById('house_house').addEventListener('change', toggleRentInputs);
  document.getElementById('house_lot').addEventListener('change', toggleRentInputs);

  // Initial toggle based on loaded data
  toggleRentInputs();

  // Attach validation listeners to Step 1 required fields
  const step1Fields = [
    'head_4ps',
    'head_address',
    'head_zone',
    'head_pob',
    'head_educ',
    'head_occ',
    'head_religion'
  ];
  step1Fields.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', validateStep1);
      el.addEventListener('change', validateStep1);
    }
  });

  // Attach validation listeners to Step 2 family member fields
  function attachStep2Listeners() {
    document.querySelectorAll('#familyTable .fm-name, #familyTable .fm-relation, #familyTable .fm-birth, #familyTable .fm-age, #familyTable .fm-sex, #familyTable .fm-civil, #familyTable .fm-educ, #familyTable .fm-occ, #familyTable .fm-remarks, #familyTable .fm-income').forEach(el => {
      el.addEventListener('input', validateStep2);
      el.addEventListener('change', validateStep2);
    });
  }
  attachStep2Listeners();

  // Attach validation listeners to Step 3 fields
  document.getElementById('house_house').addEventListener('change', validateStep3);
  document.getElementById('house_lot').addEventListener('change', validateStep3);
  document.getElementById('house_rent').addEventListener('input', validateStep3);
  document.getElementById('house_rent').addEventListener('change', validateStep3);
  document.getElementById('lot_rent').addEventListener('input', validateStep3);
  document.getElementById('lot_rent').addEventListener('change', validateStep3);

  // Initial validation
  validateStep1();
});

