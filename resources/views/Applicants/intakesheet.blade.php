<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Intake Sheet - Wizard & Review (Landscape Print)</title>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SignaturePad -->
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    /* Add this to your CSS in intakesheet.blade.php */
button:disabled {
  cursor: not-allowed;
  opacity: 0.5 !important;
  background-color: #9ca3af !important;
}

#nextBtn:disabled {
  background-color: #9ca3af !important;
  cursor: not-allowed;
}
      /* Floating Label Input Style */
      .form-group {
        position: relative;
        margin-bottom: 1rem;
      }
      .form-input {
        border: 1px solid black;
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
          margin: 0;
          padding: 0;
        }
        .no-print {
          display: none !important;
        }
        /* Ensure review area spans the full printable page */
        .max-w-6xl {
          max-width: 100% !important;
          width: 100% !important;
          margin: 0 !important;
          padding: 0 !important;
        }
        
        /* Print header styling */
        .print-header {
          display: flex !important;
          justify-content: space-between;
          align-items: flex-start;
          width: 100%;
          margin-bottom: 10px;
          border-bottom: 1px solid #000;
          padding-bottom: 5px;
        }
        .print-header-left {
          text-align: left;
          font-size: 9px;
          line-height: 1.2;
        }
        .print-header-right {
          text-align: right;
          font-size: 9px;
        }
        .print-title {
          font-size: 14px !important;
          font-weight: bold;
          text-align: center;
          margin: 5px 0;
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

      /* Step 1 and Step 3 field borders */
      #step-1 .form-input, #householdForm .form-input {
        border: 1px solid grey;
      }

      /* Signature canvas styling */
      #signatureCanvas {
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        cursor: crosshair;
      }

      @media print {
        #signatureCanvas {
          border: 1px solid #000;
        }
      }
      
      /* Print header - hidden by default */
      .print-header {
        display: none;
      }

      /* Loading Spinner Styles */
      .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        animation: fadeIn 1s ease forwards;
      }

      .spinner {
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
      }

      .spinner img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
      }

      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }

      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
      }

      .fade-out {
        animation: fadeOut 1s ease forwards;
      }

      @keyframes fadeOut {
        to {
          opacity: 0;
          visibility: hidden;
        }
      }

      /* Responsive spinner size */
      @media (max-width: 768px) {
        .spinner {
          width: 80px;
          height: 80px;
        }
      }

      @media (max-width: 480px) {
        .spinner {
          width: 60px;
          height: 60px;
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
            src="{{ asset('images/LYDO.png') }}"
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
          <input type="hidden" id="serial_number" />
          <h1 class="text-2xl font-bold text-gray-900">FAMILY INTAKE SHEET</h1>
        </div>
      </div>

      <!-- PRINT HEADER (Only shows when printing) -->
      <div class="print-header">
        <div class="print-header-left">
          <div>Republic of the Philippines</div>
          <div>Province of Misamis Oriental</div>
          <div>Municipality of Tagoloan</div>
          <div>Municipal Social Welfare and Development Office</div>
        </div>
        <div class="print-header-right">
          Serial No.: <span id="printSerialNumber"></span>
        </div>
      </div>
      <div class="print-title">FAMILY INTAKE SHEET</div>

      <!-- Progress -->
      <div class="w-full bg-gray-200 h-2 mb-4 rounded-full no-print">
        <div
          id="progressBar"
          class="h-2 bg-purple-600 rounded-full transition-all duration-300"
          style="width: 12%"
        ></div>
      </div>

    <form id="intakeForm" class="space-y-6" method="POST" action="{{ route('submit.intake.sheet') }}">
    @csrf
      <input type="hidden" name="application_personnel_id" value="{{ $application_personnel_id ?? request()->route('application_personnel_id') }}">    <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="token" value="{{ $token }}">

        <section class="step" id="step-1">
          <h3 class="text-lg font-semibold mb-3">
            Step 1 — Head of the Family
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="form-group">
              <input id="applicant_fname" placeholder=" " class="form-input" value="{{ $applicant->applicant_fname ?? '' }}" />
              <label class="form-label">First Name</label>
            </div>
            <div class="form-group">
              <input id="applicant_mname" placeholder=" " class="form-input" value="{{ $applicant->applicant_mname ?? '' }}" /><label
                class="form-label"
                >Middle Name</label
              >
            </div>
            <div class="form-group">
              <input id="applicant_lname" placeholder=" " class="form-input" value="{{ $applicant->applicant_lname ?? '' }}" /><label
                class="form-label"
                >Last Name</label
              >
            </div>

            <div class="form-group">
              <input
                id="applicant_suffix"
                placeholder=" "
                class="form-input"
                value="{{ $applicant->applicant_suffix ?? '' }}"
              /><label class="form-label">Suffix (Optional)</label>
            </div>
            <!-- 4Ps, IP No., Sex in one row -->
            <div class="md:col-span-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div class="form-group">
                <select id="head_4ps" class="form-input">
                  <option value="" disabled selected>Select</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
                <label class="form-label">4Ps Beneficiary <span style="color: red;">*</span></label>
              </div>
              <div class="form-group">
                <input id="head_ipno" placeholder=" " class="form-input" /><label
                  class="form-label"
                  >IP No. (Optional)</label
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sex</label>
                <div class="flex items-center gap-6" data-initial-sex="{{ $applicant->applicant_gender ?? '' }}">
                  <label class="flex items-center gap-2"><input type="radio" name="applicant_gender" value="Male" @if(($applicant->applicant_gender ?? '') == 'Male') checked @endif /> Male</label>
                  <label class="flex items-center gap-2"><input type="radio" name="applicant_gender" value="Female" @if(($applicant->applicant_gender ?? '') == 'Female') checked @endif /> Female</label>
                </div>
              </div>
            </div>

            <div class="md:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="form-group">
                <input
                  id="head_address"
                  placeholder=" "
                  class="form-input"
                /><label class="form-label">Address <span style="color: red;">*</span></label>
              </div>
              <div class="form-group">
                <input id="head_zone" placeholder=" " class="form-input" /><label
                  class="form-label"
                  >Zone <span style="color: red;">*</span></label
                >
              </div>
            </div>
            <div class="form-group">
              <input
                id="applicant_brgy"
                placeholder=" "
                class="form-input"
                value="{{ $applicant->applicant_brgy ?? '' }}"
              /><label class="form-label">Barangay</label>
            </div>

            <div class="form-group">
              <input
                id="applicant_bdate"
                type="date"
                placeholder=" "
                class="form-input"
                value="{{ $applicant->applicant_bdate ?? '' }}"
              /><label class="form-label">Date of Birth</label>
            </div>
            <div class="form-group">
              <input id="head_pob" placeholder=" " class="form-input" /><label
                class="form-label"
                >Place of Birth <span style="color: red;">*</span></label
              >
            </div>
            <div class="form-group">
              <select id="head_educ" class="form-input">
                <option value="" disabled selected>Select Educational Attainment</option>
                <option value="Elementary">Elementary</option>
                <option value="High School">High School</option>
                <option value="Vocational">Vocational</option>
                <option value="College">College</option>
                <option value="Post Graduate">Post Graduate</option>
              </select>
              <label class="form-label">Educational Attainment <span style="color: red;">*</span></label>
            </div>
            <div class="form-group">
              <select id="head_occ" class="form-input">
                <option value="" disabled selected>Select Occupation</option>
                <option value="Farmer">Farmer</option>
                <option value="Teacher">Teacher</option>
                <option value="Driver">Driver</option>
                <option value="Business Owner">Business Owner</option>
                <option value="Employee">Employee</option>
                <option value="Unemployed">Unemployed</option>
                <option value="Student">Student</option>
                <option value="Other">Other</option>
              </select>
              <label class="form-label">Occupation <span style="color: red;">*</span></label>
            </div>
            <div class="form-group md:col-span-2">
              <select id="head_religion" class="form-input">
                <option value="" disabled selected>Select Religion</option>
                <option value="Catholic">Catholic</option>
                <option value="Protestant">Protestant</option>
                <option value="Islam">Islam</option>
                <option value="Buddhist">Buddhist</option>
                <option value="Atheist">Atheist</option>
                <option value="Other">Other</option>
              </select>
              <label class="form-label">Religion <span style="color: red;">*</span></label>
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
          <p class="text-sm text-gray-600 mb-3">Please fill up all required fields in the family members table. Remarks should be selected based on the categories listed below.</p>
          <div class="overflow-x-auto">
            <table id="familyTable" class="min-w-full text-xs md:text-sm thin-border">
              <thead class="bg-gray-100">
                <tr>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Name</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Relation</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Birthdate</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Age</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Sex</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Civil Status</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Educational Attainment</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Occupation</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Monthly Income</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Remarks</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="border px-1 md:px-2 py-1">
                    <input class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-name" />
                  </td>
                  <td class="border px-1 md:px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-relation">
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
                  <td class="border px-1 md:px-2 py-1">
                    <input type="date" class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-birth" />
                  </td>
                  <td class="border px-1 md:px-2 py-1">
                    <input type="number" class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-age" />
                  </td>
                  <td class="border px-1 md:px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-sex">
                      <option value="">Select</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                  </td>
                  <td class="border px-1 md:px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-civil">
                      <option value="">Select</option>
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widowed">Widowed</option>
                      <option value="Divorced">Divorced</option>
                      <option value="Separated">Separated</option>
                    </select>
                  </td>
                  <td class="border px-1 md:px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-educ">
                      <option value="">Select</option>
                      <option value="None">None</option>
                      <option value="Elementary">Elementary</option>
                      <option value="High School">High School</option>
                      <option value="College">College</option>
                      <option value="Vocational">Vocational</option>
                      <option value="Graduate">Graduate</option>
                    </select>
                  </td>
                  <td class="border px-1 md:px-2 py-1">
                    <input class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-occ" />
                  </td>
                  <td class="border px-1 md:px-2 py-1">
                    <div class="flex items-center">
                      <span class="mr-1 text-gray-600 text-xs md:text-sm">₱</span>
                      <input type="number" class="flex-1 border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-income" />
                    </div>
                  </td>
                  <td class="border px-1 md:px-2 py-1">
                    <select class="w-full border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm fm-remarks">
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
                  <td class="border px-1 md:px-2 py-1">
                    <button
                      type="button"
                      onclick="deleteRow(this)"
                      class="bg-red-500 text-white px-1 md:px-2 py-1 rounded text-xs md:text-sm"
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
          <div class="mt-4">
            <h4 class="font-semibold mb-3 text-gray-800">Remarks Categories:</h4>
            <div class="grid grid-cols-2 gap-4">
              <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Out of School Youth (OSY)</div>
              <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Solo Parent (SP)</div>
              <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Person with Disability (PWD)</div>
              <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Senior Citizen (SC)</div>
              <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Lactating Mother</div>
              <div class="text-sm text-black border border-gray-300 rounded p-2 hover:text-violet-600">Pregnant Mother</div>
            </div>
          </div>
        </section>

        <!-- STEP 3: Household Info -->
        <section class="step hidden" id="step-3">
          <div class="flex justify-between items-start mb-3">
            <h3 class="text-lg font-semibold">Step 3 — Household Information</h3>
          </div>

          <div class="space-y-4" id="householdForm">
            <!-- First line: Incomes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                /><label class="form-label">Total Family Income (Monthly)</label>
              </div>
            </div>

            <!-- Second line: House, Lot, Electricity, Water -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <div class="form-group">
                  <select id="house_house" class="form-input">
                    <option value="" disabled selected>Select</option>
                    <option value="Owned">Owned</option>
                    <option value="Rented">Rented</option>
                  </select>
                  <label class="form-label">House (Owned/Rented) <span style="color: red;">*</span></label>
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
  /><label class="form-label">House Rent Monthly Amount <span style="color: red;">*</span></label>
</div>
              </div>
              <div>
                <div class="form-group">
                  <select id="house_lot" class="form-input">
                    <option value="" disabled selected>Select</option>
                    <option value="Owned">Owned</option>
                    <option value="Rented">Rented</option>
                  </select>
                  <label class="form-label">Lot (Owned/Rented) <span style="color: red;">*</span></label>
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
  /><label class="form-label">Lot Rent Monthly Amount <span style="color: red;">*</span></label>
</div>
              </div>
              <div class="form-group">
                <input
                  id="house_electric"
                  placeholder=""
                  type="number"
                  class="form-input"
                /><label class="form-label">Electricity Monthly Billing <span style="color: red;">*</span></label>
              </div>
              <div class="form-group">
                <input
                  id="house_water"
                  placeholder=""
                  type="number"
                  class="form-input"
                /><label class="form-label">Water Monthly Billing <span style="color: red;">*</span></label>
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
                /><label class="form-label">Total Family Net Income (Monthly)</label>
              </div>
            </div>
          </div>
        </section>

 <!-- STEP 4: Review -->
<section class="step hidden" id="step-4">
  <h3 class="text-lg font-semibold mb-3 no-print">Step 4 — Review and Submit</h3>
  <div id="reviewArea" class="w-full">
    <div class="review-columns">
      <div class="space-y-4">
        <h2 class="text-xl font-bold no-print">Family Intake Sheet Review</h2>
        <div class="print-box p-4">
          <p class="text-sm md:text-base"><strong>Serial No.:</strong> <span id="rv_serial"></span></p>
          <p class="text-sm md:text-base"><strong>Name:</strong> <span id="rv_head_name"></span></p>
          <div id="rv_head_table"></div>
        </div>
        <div class="print-box p-4">
          <h4 class="font-semibold text-sm md:text-base">Family Members</h4>
          <div class="overflow-x-auto">
            <table id="rv_family_table" class="min-w-full text-xs md:text-sm thin-border">
              <thead class="bg-gray-100">
                <tr>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Name</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Relation</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Birthdate</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Age</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Sex</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Civil Status</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Educational Attainment</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Occupation</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Monthly Income</th>
                  <th class="border px-1 md:px-2 py-1 text-xs md:text-sm">Remarks</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div id="rv_household_table" class="print-box p-4">
          <h4 class="font-semibold text-sm md:text-base">Household Information</h4>
          <table class="min-w-full text-xs md:text-sm">
            <tr>
                <td class="px-1 md:px-2 py-1"><strong>Other Source of Income:</strong> ₱<span id="rv_other_income"></span></td>
              <td class="px-1 md:px-2 py-1"><strong>Total Family Income (Monthly):</strong> ₱<span id="rv_total_income"></span></td>
              <td class="px-1 md:px-2 py-1"><strong>Total Family Net Income (Monthly):</strong> ₱<span id="rv_net_income"></span></td>
            </tr>
            <tr>
              <td class="px-1 md:px-2 py-1"><strong>House (Owned/Rented):</strong> <span id="rv_house"></span><br><span id="rv_house_rent_display"></span></td>
              <td class="px-1 md:px-2 py-1"><strong>Lot (Owned/Rented):</strong> <span id="rv_lot"></span><br><span id="rv_lot_rent_display"></span></td>
              <td class="px-1 md:px-2 py-1"><strong>Water Monthly Billing:</strong> ₱<span id="rv_water"></span></td>
              <td class="px-1 md:px-2 py-1"><strong>Electricity Monthly Billing:</strong> ₱<span id="rv_electric"></span></td>
            </tr>
            <tr id="rv_value_row" style="display: none;">
              <td class="px-1 md:px-2 py-1"><strong>House Value:</strong> ₱<span id="rv_house_value"></span></td>
              <td class="px-1 md:px-2 py-1"><strong>Lot Value:</strong> ₱<span id="rv_lot_value"></span></td>
              <td colspan="2" class="px-1 md:px-2 py-1"></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
        <!-- Navigation controls -->
        <div id="navControls" class="flex justify-between border-t pt-4 no-print">
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
            onclick="handleNext()"
            class="bg-purple-600 text-white px-5 py-2 rounded hover:bg-purple-700"
          >
            Next
          </button>
        </div>
      </form>
    </div>

    <!-- Loading Spinner Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
      <div class="spinner">
        <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
      </div>
    </div>

    <script src="{{ asset('js/intakesheet.js') }}"></script>
  </body>
</html>
