// Wizard state
let currentStep = 1;
const totalSteps = 4;
const progressBar = document.getElementById("progressBar");

// SignaturePad instances
const signaturePads = {};

// Signature variables
let signaturePad = null;

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
    
    // Optimize signature data for storage
    if (data.signatures && data.signatures.client) {
      data.signatures.client = optimizeSignatureData(data.signatures.client);
    }
    
    data.currentStep = currentStep;
    
    // Check if data is too large
    const dataString = JSON.stringify(data);
    if (dataString.length > 5000000) { // 5MB limit
      console.warn('Data too large, clearing signatures');
      data.signatures.client = null; // Remove signature if too large
    }
    
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  } catch (e) {
    console.error('Error saving to localStorage:', e);
    // Fallback: save without signatures
    const data = collectData();
    data.signatures.client = null;
    data.currentStep = currentStep;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
  }
}

function optimizeSignatureData(dataURL) {
  // Basic optimization - you can enhance this later
  // For now, just ensure it's not empty and return as-is
  if (!dataURL || dataURL === 'data:,') {
    return null;
  }
  return dataURL;
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

  // Signatures
  if (data.signatures.client && signaturePad) {
    signaturePad.fromDataURL(data.signatures.client);
    document.getElementById('signature_client').value = data.signatures.client;
  }
}

// Clear localStorage on submit
function clearFormData() {
  localStorage.removeItem(STORAGE_KEY);
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
  // Show Print button only on review step
  if (step === totalSteps) {
    document.getElementById("printBtn").classList.remove("hidden");
  } else {
    document.getElementById("printBtn").classList.add("hidden");
  }
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
    // Resize signature canvas when Step 3 is shown
    const c = document.getElementById("signatureCanvas");
    if (c) resizeCanvasForSignature(c);
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
    submitForm();
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

function resizeCanvasForSignature(canvas) {
  const ratio = Math.max(window.devicePixelRatio || 1, 1);
  canvas.width = canvas.offsetWidth * ratio;
  canvas.height = canvas.offsetHeight * ratio;
  canvas.getContext("2d").scale(ratio, ratio);
  
  // Re-initialize SignaturePad after resize if it exists
  if (signaturePad) {
    signaturePad.clear();
  }
}

// Initialize signature pad - REPLACE THE EXISTING FUNCTION
function initializeSignaturePad() {
  const canvas = document.getElementById('signatureCanvas');
  if (!canvas) return;
  
  // Resize canvas
  resizeCanvasForSignature(canvas);
  
  // Initialize SignaturePad
  signaturePad = new SignaturePad(canvas, {
    backgroundColor: 'rgb(255, 255, 255)',
    penColor: 'rgb(0, 0, 0)',
    minWidth: 1,
    maxWidth: 3
  });
  
  // Load saved signature if exists
  loadSavedSignature();
}

// Clear signature - REPLACE THE EXISTING FUNCTION
function clearSignature() {
  if (signaturePad) {
    signaturePad.clear();
    document.getElementById('signature_client').value = '';
    saveFormData(); // Update localStorage
  }
}


// Save signature - REPLACE THE EXISTING FUNCTION
// Save signature - UPDATED VERSION
function saveSignature() {
  if (!signaturePad) return;

  if (signaturePad.isEmpty()) {
    Swal.fire({
      title: 'No Signature',
      text: 'Please provide a signature before saving.',
      icon: 'warning',
      confirmButtonText: 'OK'
    });
    return;
  }

  const signatureData = signaturePad.toDataURL();
  document.getElementById('signature_client').value = signatureData;
  
  // Generate a filename
  const timestamp = new Date().getTime();
  const filename = `signature_${timestamp}.png`;
  document.getElementById('signature_filename').value = filename;
  
  saveFormData(); // Save to localStorage
  
  Swal.fire({
    title: 'Signature Saved',
    text: 'Family head signature has been saved successfully.',
    icon: 'success',
    timer: 1500,
    showConfirmButton: false
  });
}

// Replace the entire submitForm function with this:
async function submitForm() {
  // Validate signature
  const signatureData = document.getElementById('signature_client').value;
  const signatureFilename = document.getElementById('signature_filename').value;
  
  if (!signatureData) {
    Swal.fire({
      title: 'Signature Required',
      text: 'Please provide and save the family head signature before submitting.',
      icon: 'warning',
      confirmButtonText: 'OK'
    });
    return;
  }

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

  try {
    // Get the form element
    const form = document.getElementById('intakeForm');
    
    // Create a FormData object from the form
    const formData = new FormData(form);
    
    // Add the collected data as JSON
    const data = collectData();
    formData.append('form_data', JSON.stringify(data));
    
    // Add signature data
    formData.append('signature_data', signatureData);
    formData.append('signature_filename', signatureFilename);

    const response = await fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: formData
    });

    const result = await response.json();
    
    if (response.ok) {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: result.message || 'Family intake sheet submitted successfully!',
        confirmButtonText: 'OK'
      }).then(() => {
        clearFormData();
        // Redirect to a success page or home
        window.location.href = '/submission-success';
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Submission Failed',
        text: result.message || 'Error submitting form. Please try again.',
        confirmButtonText: 'OK'
      });
    }
  } catch (error) {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'An unexpected error occurred. Please try again.',
      confirmButtonText: 'OK'
    });
  }
}

// Load saved signature from localStorage - REPLACE THE EXISTING FUNCTION
function loadSavedSignature() {
  const saved = localStorage.getItem(STORAGE_KEY);
  if (saved) {
    const data = JSON.parse(saved);
    if (data.signatures && data.signatures.client && signaturePad) {
      signaturePad.fromDataURL(data.signatures.client);
      document.getElementById('signature_client').value = data.signatures.client;
    }
  }
}

// Collect data
function collectData() {
  // Get the values directly from the form inputs
  const fname = document.getElementById('applicant_fname').value;
  const mname = document.getElementById('applicant_mname').value;
  const lname = document.getElementById('applicant_lname').value;
  const suffix = document.getElementById('applicant_suffix').value;
  
  const applicant = {
    fname: fname,
    mname: mname,
    lname: lname,
    suffix: suffix,
    gender: document.querySelector('input[name="applicant_gender"]:checked')?.value || "",
  };

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
    // Include names in head as well for backward compatibility
    fname: fname,
    mname: mname,
    lname: lname,
    suffix: suffix
  };

  const location = document.querySelector('input[name="location"]:checked')?.value || "";

  const family = [];
  document.querySelectorAll("#familyTable tbody tr").forEach((tr) => {
    family.push({
      name: tr.querySelector(".fm-name")?.value || "",
      relation: tr.querySelector(".fm-relation")?.value || "",
      birth: tr.querySelector(".fm-birth")?.value || "",
      age: tr.querySelector(".fm-age")?.value || "",
      sex: tr.querySelector(".fm-sex")?.value || "",
      civil: tr.querySelector(".fm-civil")?.value || "",
      educ: tr.querySelector(".fm-educ")?.value || "",
      occ: tr.querySelector(".fm-occ")?.value || "",
      income: tr.querySelector(".fm-income")?.value || "",
      remarks: tr.querySelector(".fm-remarks")?.value || "",
    });
  });

  const house = {
    total_income: getVal("house_total_income"),
    net_income: getVal("house_net_income"),
    other_income: getVal("other_income"),
    house: getVal("house_house"),
    lot: getVal("house_lot"),
    house_value: getVal("house_value"),
    lot_value: getVal("lot_value"),
    house_rent: getVal("house_rent"),
    lot_rent: getVal("lot_rent"),
    water: getVal("house_water"),
    electric: getVal("house_electric"),
  };

  const signatures = {
    client: document.getElementById('signature_client').value || null
  };

  return {
    applicant,
    head,
    location,
    family,
    house,
    signatures,
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
    document.getElementById("printSerialNumber").textContent = serial; // ADD THIS LINE

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
        <td><strong>Sex:</strong> ${d.head.sex || d.applicant.gender || "-"}</td>
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

  // Signatures
// In populateReview() function, update the signature display section:
const rvSigClient = document.getElementById("rv_sig_client");
rvSigClient.innerHTML = "";

// If we have a signature in localStorage for preview, show it
const signatureData = document.getElementById('signature_client').value;
if (signatureData) {
    const img = document.createElement("img");
    img.src = signatureData;
    img.style.maxWidth = "100%";
    img.style.height = "80px";
    img.style.border = "1px solid #ccc";
    rvSigClient.appendChild(img);
} else {
    rvSigClient.innerHTML = '<p class="text-xs text-gray-500">Signature will be saved to file</p>';
}
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

// Submit form function
async function submitForm() {
  // Validate signature
  const signatureData = document.getElementById('signature_client').value;
  if (!signatureData) {
    Swal.fire({
      title: 'Signature Required',
      text: 'Please provide and save the family head signature before submitting.',
      icon: 'warning',
      confirmButtonText: 'OK'
    });
    return;
  }

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

  const data = collectData();
  // Add application_personnel_id from URL
  data.application_personnel_id = window.location.pathname.split('/').pop();
  // Add token from URL query parameter
  const urlParams = new URLSearchParams(window.location.search);
  data.token = urlParams.get('token');
  try {
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
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: result.message || 'Family intake sheet submitted successfully!',
        confirmButtonText: 'OK'
      }).then(() => {
        clearFormData();
        // Optionally redirect
        window.location.href = '/'; // or wherever appropriate
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Submission Failed',
        text: result.message || 'Error submitting form. Please try again.',
        confirmButtonText: 'OK'
      });
    }
  } catch (error) {
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'An unexpected error occurred. Please try again.',
      confirmButtonText: 'OK'
    });
  }
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

// Setup on load
window.addEventListener("load", () => {
  initializeSignaturePad();
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

window.addEventListener("resize", () => {
  const canvas = document.getElementById('signatureCanvas');
  if (canvas) resizeCanvasForSignature(canvas);
});

// Handle print functionality
function handlePrint() {
  // Update the print serial number
  const serialNumber = document.getElementById('serial_number').value || generateSerialNumber();
  document.getElementById('printSerialNumber').textContent = serialNumber;
  
  // Show only the review area for printing
  document.querySelectorAll('.step').forEach(step => {
    step.classList.add('hidden');
  });
  document.getElementById('step-4').classList.remove('hidden');
  
  // Wait a moment for DOM to update, then print
  setTimeout(() => {
    window.print();
    
    // Restore the view after printing
    setTimeout(() => {
      showStep(currentStep);
    }, 100);
  }, 100);
}
// Handle print functionality
// Update the handlePrint function in intakesheet.js
async function handlePrint() {
    const data = collectData();
    const serial = document.getElementById('serial_number').value || generateSerialNumber();
    
    // Add the serial number to the data
    data.head.serial = serial;
    
    try {
        // Get the application_personnel_id from the URL
        const applicationPersonnelId = window.location.pathname.split('/').pop();
        
        // Use the correct route - remove the dash
        const printWindow = window.open(`/print-intake-sheet/${applicationPersonnelId}?data=${encodeURIComponent(JSON.stringify(data))}`, '_blank');
        
    } catch (error) {
        console.error('Error opening print window:', error);
        Swal.fire({
            icon: 'error',
            title: 'Print Error',
            text: 'Unable to open print view. Please try again.',
            confirmButtonText: 'OK'
        });
    }
}