<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Family Intake Sheet - Wizard & Review (Landscape Print)</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SignaturePad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>



    <style>
      /* Floating Label Input Style */
      .form-group {
        position: relative;
        margin-bottom: 1rem;
      }
      .form-input {
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        width: 100%;
        padding: 1rem 0.75rem 0.25rem 0.75rem;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.18s ease;
        background: white;
      }
      .form-input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.08);
      }
      .form-label {
        position: absolute;
        top: 1rem;
        left: 0.75rem;
        color: #6b7280;
        font-size: 0.95rem;
        pointer-events: none;
        transition: all 0.18s ease;
      }
      .form-input:focus + .form-label,
      .form-input:not(:placeholder-shown) + .form-label {
        top: 0.3rem;
        left: 0.65rem;
        font-size: 0.72rem;
        color: #7c3aed;
        background: white;
        padding: 0 6px;
      }

      /* Clean printable box style */
      .print-box {
        border: 1px solid #d1d5db;
        border-radius: 4px;
        background: #fff;
      }
      .thin-border {
        border: 1px solid #e5e7eb;
      }

      /* Layout for review (full screen) */
      .review-columns {
        display: grid;
        grid-template-columns: 1fr; /* full width for responsive design */
        gap: 16px;
      }

      /* Print rules */
      @page {
        size: landscape;
        margin: 4mm;
      }
      @media print {
        body {
          background: white !important;
          color: #000;
          font-size: 10px;
        }
        .no-print {
          display: none !important;
        }
        /* Ensure review area spans the full printable page */
        .max-w-6xl {
          max-width: 100% !important;
          width: 100% !important;
        }
        #reviewArea {
          page-break-inside: avoid;
          padding: 0.125rem !important;
        }
        .review-columns {
          font-size: 9px;
          gap: 4px;
        }
        .thin-border {
          margin-bottom: 0.125rem;
          padding: 0.125rem;
        }
        table {
          font-size: 8px;
        }
        .text-sm {
          font-size: 8px !important;
        }
        .text-xs {
          font-size: 7px !important;
        }
        h2 {
          font-size: 12px !important;
        }
        h4 {
          font-size: 10px !important;
        }
      }

      /* Responsive review columns */
      @media (max-width: 768px) {
        .review-columns {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body class="bg-gray-100 min-h-screen">
    <div class="w-full bg-white p-6">
      <!-- HEADER -->
      <div
        class="flex items-center justify-between border-b pb-4 mb-4 no-print"
      >
        <div class="flex items-center space-x-4">
          <!-- placeholder logo: replace 'logo.png' in same folder -->
          <img
            src="logo.png"
            alt="Municipal Logo"
            class="w-16 h-16 object-contain"
          />
          <div>
            <p class="text-sm font-semibold text-gray-800">
              Republic of the Philippines
            </p>
            <p class="text-sm text-gray-700">Province of Misamis Oriental</p>
            <p class="text-sm text-gray-700">Municipality of Tagoloan</p>
            <p class="text-sm font-semibold text-gray-800">
              Municipal Social Welfare and Development Office
            </p>
          </div>
        </div>
        <div class="text-right">
          <p class="text-xs italic text-gray-600">
            Serial No.: <span id="printSerial">AUTO_GENERATED</span>
          </p>
          <h1 class="text-2xl font-bold text-gray-900">FAMILY INTAKE SHEET</h1>
        </div>
      </div>

      <!-- Progress -->
      <div class="w-full bg-gray-200 h-2 mb-4 rounded-full">
        <div
          id="progressBar"
          class="h-2 bg-purple-600 rounded-full transition-all duration-300"
          style="width: 12%"
        ></div>
      </div>

      <form id="intakeForm" class="space-y-6">
        <!-- STEP 1 -->
        <section class="step" id="step-1">
          <h3 class="text-lg font-semibold mb-3">
            Step 1 — Head of the Family
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="form-group">
              <input id="applicant_fname" placeholder=" " class="form-input" />
              <label class="form-label">First Name</label>
            </div>
            <div class="form-group">
              <input id="applicant_mname" placeholder=" " class="form-input" /><label
                class="form-label"
                >Middle Name</label
              >
            </div>
            <div class="form-group">
              <input id="applicant_lname" placeholder=" " class="form-input" /><label
                class="form-label"
                >Last Name</label
              >
            </div>

            <div class="form-group">
              <input
                id="applicant_suffix"
                placeholder=" "
                class="form-input"
              /><label class="form-label">Suffix</label>
            </div>
            <!-- 4Ps, IP No., Sex in one row -->
            <div class="md:col-span-4 grid grid-cols-3 gap-4">
              <div class="form-group">
                <input id="head_4ps" placeholder=" " class="form-input" /><label
                  class="form-label"
                  >4Ps Beneficiary (Yes/No)</label
                >
              </div>
              <div class="form-group">
                <input id="head_ipno" placeholder=" " class="form-input" /><label
                  class="form-label"
                  >IP No.</label
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sex</label>
                <div class="flex items-center gap-6">
                  <label class="flex items-center gap-2"><input type="radio" name="applicant_gender" value="Male" checked /> Male</label>
                  <label class="flex items-center gap-2"><input type="radio" name="applicant_gender" value="Female" /> Female</label>
                </div>
              </div>
            </div>

            <div class="form-group md:col-span-2">
              <input
                id="head_address"
                placeholder=" "
                class="form-input"
              /><label class="form-label">Address</label>
            </div>
            <div class="form-group">
              <input id="head_zone" placeholder=" " class="form-input" /><label
                class="form-label"
                >Zone</label
              >
            </div>
            <div class="form-group">
              <input
                id="head_barangay"
                placeholder=" "
                class="form-input"
              /><label class="form-label">Barangay</label>
            </div>

            <div class="form-group">
              <input
                id="head_dob"
                type="date"
                placeholder=" "
                class="form-input"
              /><label class="form-label">Date of Birth</label>
            </div>
            <div class="form-group">
              <input id="head_pob" placeholder=" " class="form-input" /><label
                class="form-label"
                >Place of Birth</label
              >
            </div>
            <div class="form-group">
              <select id="head_educ" class="form-input">
                <option value="" disabled selected>Select Educational Attainment</option>
                <option value="College Level (1st Year)">College Level (1st Year)</option>
                <option value="College Level (2nd Year)">College Level (2nd Year)</option>
                <option value="College Level (3rd Year)">College Level (3rd Year)</option>
                <option value="College Level (4th Year)">College Level (4th Year)</option>
              </select>
              <label class="form-label">Educational Attainment</label>
            </div>
            <div class="form-group">
              <input id="head_occ" placeholder=" " class="form-input" /><label
                class="form-label"
                >Occupation</label
              >
            </div>
            <div class="form-group md:col-span-2">
              <input
                id="head_religion"
                placeholder=" "
                class="form-input"
              /><label class="form-label">Religion</label>
            </div>

            <!-- Serial and location -->
            <div class="md:col-span-2 flex items-center gap-6">
              <label class="flex items-center gap-2"
                ><input
                  type="radio"
                  name="location"
                  value="Within Tagoloan"
                  checked
                />
                Within Tagoloan</label
              >
              <label class="flex items-center gap-2"
                ><input type="radio" name="location" value="Outside Tagoloan" />
                Outside Tagoloan</label
              >
            </div>
          </div>
        </section>

        <!-- STEP 2: Family Members -->
        <section class="step hidden" id="step-2">
          <h3 class="text-lg font-semibold mb-3">Step 2 — Family Members</h3>
          <div class="overflow-x-auto">
            <table id="familyTable" class="min-w-full text-sm thin-border">
              <thead class="bg-gray-100">
                <tr>
                  <th class="border px-2 py-1">Name</th>
                  <th class="border px-2 py-1">Relation</th>
                  <th class="border px-2 py-1">Birthdate</th>
                  <th class="border px-2 py-1">Age</th>
                  <th class="border px-2 py-1">Sex</th>
                  <th class="border px-2 py-1">Civil Status</th>
                  <th class="border px-2 py-1">Educational Attainment</th>
                  <th class="border px-2 py-1">Occupation</th>
                  <th class="border px-2 py-1">Income</th>
                  <th class="border px-2 py-1">Remarks</th>
                  <th class="border px-2 py-1">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="border px-2 py-1">
                    <input class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-name" />
                  </td>
                  <td class="border px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-relation">
                      <option value="">Select</option>
                      <option value="Spouse">Spouse</option>
                      <option value="Son">Son</option>
                      <option value="Daughter">Daughter</option>
                      <option value="Father">Father</option>
                      <option value="Mother">Mother</option>
                      <option value="Brother">Brother</option>
                      <option value="Sister">Sister</option>
                      <option value="Grandchild">Grandchild</option>
                      <option value="Other">Other</option>
                    </select>
                  </td>
                  <td class="border px-2 py-1">
                    <input type="date" class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-birth" />
                  </td>
                  <td class="border px-2 py-1">
                    <input type="number" class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-age" />
                  </td>
                  <td class="border px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-sex">
                      <option value="">Select</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                  </td>
                  <td class="border px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-civil">
                      <option value="">Select</option>
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widowed">Widowed</option>
                      <option value="Divorced">Divorced</option>
                      <option value="Separated">Separated</option>
                    </select>
                  </td>
                  <td class="border px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-educ">
                      <option value="">Select</option>
                      <option value="None">None</option>
                      <option value="Elementary">Elementary</option>
                      <option value="High School">High School</option>
                      <option value="College">College</option>
                      <option value="Vocational">Vocational</option>
                      <option value="Graduate">Graduate</option>
                    </select>
                  </td>
                  <td class="border px-2 py-1">
                    <input class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-occ" />
                  </td>
                  <td class="border px-2 py-1">
                    <div class="flex items-center">
                      <span class="mr-1 text-gray-600">₱</span>
                      <input type="number" class="flex-1 border border-gray-300 rounded px-2 py-1 text-sm fm-income" />
                    </div>
                  </td>
                  <td class="border px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-2 py-1 text-sm fm-remarks">
                      <option value="">Select</option>
                      <option value="CIC">CIC</option>
                      <option value="OSY">OSY</option>
                      <option value="SP">SP</option>
                      <option value="PWD">PWD</option>
                      <option value="SC">SC</option>
                      <option value="None">None</option>
                      <option value="Lactating Mother">Lactating Mother</option>
                      <option value="Pregnant Mother">Pregnant Mother</option>
                    </select>
                  </td>
                  <td class="border px-2 py-1">
                    <button
                      type="button"
                      onclick="deleteRow(this)"
                      class="bg-red-500 text-white px-2 py-1 rounded"
                    >
                      🗑
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
                      <button
              type="button"
              onclick="addRow()"
              class="bg-purple-600 text-white px-4 py-2 rounded"
            >
              + Add Member
            </button>
          <div class="mt-3">
            <h4 class="font-semibold mb-2">Remarks Categories:</h4>
            <ul class="list-disc list-inside text-sm mb-3">
              <li>A. Out of School Youth (OSY)</li>
              <li>B. Solo Parent (SP)</li>
              <li>C. Person with Disability (PWD)</li>
              <li>D. Senior Citizen (SC)</li>
              <li>E. Lactating Mother</li>
              <li>F. Pregnant Mother</li>
            </ul>
          </div>
        </section>

        <!-- STEP 3: Household Info -->
        <section class="step hidden" id="step-3">
          <div class="flex justify-between items-start mb-3">
            <h3 class="text-lg font-semibold">Step 3 — Household Information</h3>
          </div>

          <div class="space-y-4" id="householdForm">
            <!-- First line: Incomes -->
            <div class="grid grid-cols-2 gap-4">
              <div class="form-group">
                <input
                  id="other_income"
                  placeholder=""
                  type="number"
                  class="form-input"
                /><label class="form-label">Other Source of Income</label>
              </div>
              <div class="form-group">
                <input
                  id="house_total_income"
                  placeholder=""
                  type="number"
                  class="form-input"
                  readonly
                /><label class="form-label">Total Family Income</label>
              </div>
            </div>

            <!-- Second line: House, Lot, Electricity, Water -->
            <div class="grid grid-cols-4 gap-4">
              <div>
                <div class="form-group">
                  <select id="house_house" class="form-input">
                    <option value="" disabled selected>Select</option>
                    <option value="Owned">Owned</option>
                    <option value="Rented">Rented</option>
                  </select>
                  <label class="form-label">House (Owned/Rented)</label>
                </div>
                <div class="form-group hidden" id="house_value_group">
                  <input
                    id="house_value"
                    placeholder=""
                    type="number"
                    class="form-input"
                  /><label class="form-label">House Value</label>
                </div>
                <div class="form-group hidden" id="house_rent_group">
                  <input
                    id="house_rent"
                    placeholder=""
                    type="number"
                    class="form-input"
                  /><label class="form-label">House Rent Amount</label>
                </div>
              </div>
              <div>
                <div class="form-group">
                  <select id="house_lot" class="form-input">
                    <option value="" disabled selected>Select</option>
                    <option value="Owned">Owned</option>
                    <option value="Rented">Rented</option>
                  </select>
                  <label class="form-label">Lot (Owned/Rented)</label>
                </div>
                <div class="form-group hidden" id="lot_value_group">
                  <input
                    id="lot_value"
                    placeholder=""
                    type="number"
                    class="form-input"
                  /><label class="form-label">Lot Value</label>
                </div>
                <div class="form-group hidden" id="lot_rent_group">
                  <input
                    id="lot_rent"
                    placeholder=""
                    type="number"
                    class="form-input"
                  /><label class="form-label">Lot Rent Amount</label>
                </div>
              </div>
              <div class="form-group">
                <input
                  id="house_electric"
                  placeholder=""
                  type="number"
                  class="form-input"
                /><label class="form-label">Electricity</label>
              </div>
              <div class="form-group">
                <input
                  id="house_water"
                  placeholder=""
                  type="number"
                  class="form-input"
                /><label class="form-label">Water</label>
              </div>
            </div>

            <!-- Third line: Total Family Net Income -->
            <div class="grid grid-cols-1 gap-4">
              <div class="form-group">
                <input
                  id="house_net_income"
                  placeholder=""
                  type="number"
                  class="form-input"
                  readonly
                /><label class="form-label">Total Family Net Income</label>
              </div>
            </div>
          </div>

          <!-- Signatures Section -->
          <div class="mt-6 space-y-4">
            <h4 class="text-lg font-semibold">Signatures</h4>
            <div class="print-box p-4">
              <p class="font-semibold mb-2">Family Head Signature:</p>
              <canvas id="signatureClient" class="border border-gray-300 w-full h-32"></canvas>
              <button type="button" onclick="clearSignature('client')" class="mt-2 bg-gray-500 text-white px-3 py-1 rounded text-sm">Clear</button>
            </div>
          </div>

        </section>

        <!-- STEP 4: Review -->
        <section class="step hidden" id="step-4">
          <h3 class="text-lg font-semibold mb-3">Step 4 — Review and Submit</h3>
          <div id="reviewArea" class="w-full">
            <div class="review-columns">
              <div class="space-y-4">
                <h2 class="text-xl font-bold">Family Intake Sheet Review</h2>
                <div class="print-box p-4">
                  <p><strong>Serial No.:</strong> <span id="rv_serial"></span></p>
                  <p><strong>Name:</strong> <span id="rv_head_name"></span></p>
                  <div id="rv_head_table"></div>
                </div>
                <div class="print-box p-4">
                  <h4 class="font-semibold">Family Members</h4>
                  <table id="rv_family_table" class="min-w-full text-sm thin-border">
                    <thead class="bg-gray-100">
                      <tr>
                        <th class="border px-2 py-1">Name</th>
                        <th class="border px-2 py-1">Relation</th>
                        <th class="border px-2 py-1">Birthdate</th>
                        <th class="border px-2 py-1">Age</th>
                        <th class="border px-2 py-1">Sex</th>
                        <th class="border px-2 py-1">Civil Status</th>
                        <th class="border px-2 py-1">Educational Attainment</th>
                        <th class="border px-2 py-1">Occupation</th>
                        <th class="border px-2 py-1">Income</th>
                        <th class="border px-2 py-1">Remarks</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
                <div id="rv_household_table" class="print-box p-4">
                  <h4 class="font-semibold">Household Information</h4>
                  <table class="min-w-full text-sm">
                    <tr>
                        <td><strong>Other Source of Income:</strong> ₱<span id="rv_other_income"></span></td>
                      <td><strong>Total Family Income:</strong> ₱<span id="rv_total_income"></span></td>
                      <td><strong>Total Family Net Income:</strong> ₱<span id="rv_net_income"></span></td>

                    </tr>
    <tr>
      <td><strong>House (Owned/Rented):</strong> <span id="rv_house"></span><br><span id="rv_house_rent_display"></span></td>
      <td><strong>Lot (Owned/Rented):</strong> <span id="rv_lot"></span><br><span id="rv_lot_rent_display"></span></td>
      <td><strong>Water:</strong> ₱<span id="rv_water"></span></td>
      <td><strong>Electricity Source:</strong> ₱<span id="rv_electric"></span></td>
    </tr>
    <tr id="rv_value_row" style="display: none;">
      <td><strong>House Value:</strong> ₱<span id="rv_house_value"></span></td>
      <td><strong>Lot Value:</strong> ₱<span id="rv_lot_value"></span></td>
      <td colspan="2"></td>
    </tr>
                  </table>
                </div>
                <div class="print-box p-4">
                  <h4 class="font-semibold">Signatures</h4>
                  <div>
                    <p><strong>Family Head:</strong></p>
                    <div id="rv_sig_client"></div>
                  </div>
                </div>
                <div class="mt-6 flex justify-center gap-4 no-print">
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Navigation controls -->
        <div class="flex justify-between border-t pt-4 no-print">
          <button
            type="button"
            id="prevBtn"
            onclick="nextPrev(-1)"
            class="bg-gray-300 text-gray-800 px-5 py-2 rounded hover:bg-gray-400"
          >
            Back
          </button>
          
          <button
            type="button"
            id="nextBtn"
            onclick="nextPrev(1)"
            class="bg-purple-600 text-white px-5 py-2 rounded hover:bg-purple-700"
          >
            Next
          </button>
        </div>
      </form>
    </div>

    <script>
      // Wizard state
      let currentStep = 1;
      const totalSteps = 4;
      const progressBar = document.getElementById("progressBar");

      // SignaturePad instances
      const signaturePads = {};

      // localStorage key
      const STORAGE_KEY = 'familyIntakeFormData';

      // Save form data to localStorage
      function saveFormData() {
        const data = collectData();
        data.currentStep = currentStep;
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
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
        // Head of family
        document.getElementById('applicant_fname').value = data.head.fname || '';
        document.getElementById('applicant_mname').value = data.head.mname || '';
        document.getElementById('applicant_lname').value = data.head.lname || '';
        document.getElementById('applicant_suffix').value = data.head.suffix || '';
        document.getElementById('head_4ps').value = data.head._4ps || '';
        document.getElementById('head_ipno').value = data.head.ipno || '';
        document.getElementById('head_address').value = data.head.address || '';
        document.getElementById('head_zone').value = data.head.zone || '';
        document.getElementById('head_barangay').value = data.head.barangay || '';
        document.getElementById('head_dob').value = data.head.dob || '';
        document.getElementById('head_pob').value = data.head.pob || '';
        document.getElementById('head_educ').value = data.head.educ || '';
        document.getElementById('head_occ').value = data.head.occ || '';
        document.getElementById('head_religion').value = data.head.religion || '';

        // Sex radio
        if (data.head.sex) {
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
        if (data.signatures.client && signaturePads.client) {
          signaturePads.client.fromDataURL(data.signatures.client);
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
        if (step === 3) {
          // Resize signature canvas when Step 3 is shown
          const c = document.getElementById("signatureClient");
          if (c) resizeCanvasForSignature(c);
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

      // Calculate age from birthdate
      function calculateAge(birthdate) {
        if (!birthdate) return "";
        const today = new Date();
        const birth = new Date(birthdate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
          age--;
        }
        return age;
      }

      // Attach listener to birthdate input
      function attachBirthdateListener(birthInput) {
        birthInput.addEventListener('change', function() {
          const ageInput = this.closest('tr').querySelector('.fm-age');
          ageInput.value = calculateAge(this.value);
        });
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
        // Make age readonly
        row.querySelector('.fm-age').readOnly = true;
        // Attach listener to new birthdate
        attachBirthdateListener(row.querySelector('.fm-birth'));
        // Attach listener to new income input
        row.querySelector('.fm-income').addEventListener('input', calculateTotalFamilyIncome);
        table.appendChild(row);
      }
      function deleteRow(btn) {
        const table = document
          .getElementById("familyTable")
          .querySelector("tbody");
        if (table.rows.length > 1) {
          btn.closest("tr").remove();
          calculateTotalFamilyIncome();
        } else alert("At least one family member row is required.");
      }

      // Signature setup
      function resizeCanvasForSignature(canvas) {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
      }
      function setupSignatures() {
        const c = document.getElementById("signatureClient");
        if (c) {
          resizeCanvasForSignature(c);
          signaturePads.client = new SignaturePad(c);
        }
      }
      function clearSignature(who) {
        signaturePads[who]?.clear();
      }

      // Collect data
      function collectData() {
        const head = {
          fname: getVal("applicant_fname"),
          mname: getVal("applicant_mname"),
          lname: getVal("applicant_lname"),
          suffix: getVal("applicant_suffix"),
          _4ps: getVal("head_4ps"),
          ipno: getVal("head_ipno"),
          sex: document.querySelector('input[name="applicant_gender"]:checked')?.value || "",
          address: getVal("head_address"),
          zone: getVal("head_zone"),
          barangay: getVal("head_barangay"),
          dob: getVal("head_dob"),
          pob: getVal("head_pob"),
          educ: getVal("head_educ"),
          occ: getVal("head_occ"),
          religion: getVal("head_religion"),
          serial: getVal("serial_number"),
        };
        const location =
          document.querySelector('input[name="location"]:checked')?.value || "";

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
          client:
            signaturePads.client && !signaturePads.client.isEmpty()
              ? signaturePads.client.toDataURL()
              : null,
        };

        return {
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

      // Populate review area
      function populateReview() {
        const d = collectData();
        document.getElementById("rv_serial").innerText =
          d.head.serial || "AUTO_GENERATED";
        document.getElementById("rv_head_name").innerText = [
          d.head.fname,
          d.head.mname,
          d.head.lname,
          d.head.suffix,
        ]
          .filter(Boolean)
          .join(" ");
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
              <td><strong>Date of Birth:</strong> ${d.head.dob || "-"}</td>
              <td><strong>Place of Birth:</strong> ${d.head.pob || "-"}</td>
            </tr>
            <tr>
              <td><strong>Educational Attainment:</strong> ${d.head.educ || "-"}</td>
              <td><strong>Occupation:</strong> ${d.head.occ || "-"}</td>
              <td><strong>Religion:</strong> ${d.head.religion || "-"}</td>
            </tr>
          </table>
        `;
        document.getElementById("rv_total_income").innerText =
          d.house.total_income || "-";
        document.getElementById("rv_net_income").innerText =
          d.house.net_income || "-";
        document.getElementById("rv_other_income").innerText =
          d.house.other_income || "-";
        document.getElementById("rv_house").innerText = d.house.house || "-";
        document.getElementById("rv_lot").innerText = d.house.lot || "-";
        document.getElementById("rv_water").innerText = d.house.water || "-";
        document.getElementById("rv_electric").innerText =
          d.house.electric || "-";
        // Display rent amounts under ownership if rented
        document.getElementById("rv_house_rent_display").innerHTML =
          d.house.house === 'Rented' ? `<strong>House Rent:</strong> ₱${d.house.house_rent || '-'}` : '';
        document.getElementById("rv_lot_rent_display").innerHTML =
          d.house.lot === 'Rented' ? `<strong>Lot Rent:</strong> ₱${d.house.lot_rent || '-'}` : '';

        // family table build
        const tbody = document.querySelector("#rv_family_table tbody");
        tbody.innerHTML = "";
        d.family.forEach((f) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
          <td class="border px-2 py-1 text-left">${escapeHtml(f.name)}</td>
          <td class="border px-2 py-1 text-left">${escapeHtml(f.relation)}</td>
          <td class="border px-2 py-1 text-left">${formatDate(f.birth)}</td>
          <td class="border px-2 py-1 text-left">${escapeHtml(f.age)}</td>
          <td class="border px-2 py-1 text-left">${escapeHtml(f.sex)}</td>
          <td class="border px-2 py-1 text-left">${escapeHtml(f.civil)}</td>
          <td class="border px-2 py-1 text-left">${escapeHtml(f.educ)}</td>
          <td class="border px-2 py-1 text-left">${escapeHtml(f.occ)}</td>
          <td class="border px-2 py-1 text-left">₱${escapeHtml(f.income)}</td>
          <td class="border px-2 py-1 text-left">${escapeHtml(f.remarks)}</td>
        `;
          tbody.appendChild(tr);
        });

        // signatures
        const rvSigClient = document.getElementById("rv_sig_client");
        rvSigClient.innerHTML = "";
        if (d.signatures.client) {
          const img = document.createElement("img");
          img.src = d.signatures.client;
          img.style.maxWidth = "100%";
          img.style.height = "80px";
          rvSigClient.appendChild(img);
        } else
          rvSigClient.innerHTML =
            '<p class="text-xs text-gray-500">No signature</p>';
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
      function submitForm() {
        const data = collectData();
        // For demonstration, alert the data. In a real application, send to server.
        alert("Form submitted successfully!\n\nData:\n" + JSON.stringify(data, null, 2));
        // Example: fetch('/submit', { method: 'POST', body: JSON.stringify(data), headers: {'Content-Type': 'application/json'} });
      }



      // Setup on load
      window.addEventListener("load", () => {
        setupSignatures();
        loadFormData(); // Load saved data
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

        // Attach save listener to signature pad
        if (signaturePads.client) {
          signaturePads.client.addEventListener('endStroke', saveFormData);
        }
      });

      // make canvases responsive
      window.addEventListener("resize", () => {
        ["signatureClient"].forEach(
          (id) => {
            const c = document.getElementById(id);
            if (c) resizeCanvasForSignature(c);
          }
        );
      });
    </script>
  </body>
</html>
