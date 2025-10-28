<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Family Intake Sheet - Review (Landscape Print)</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
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

      <!-- STEP 4: Review -->
      <section class="step" id="step-4">
        <h3 class="text-lg font-semibold mb-3">Review and Submit</h3>
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

                <!-- SOCIAL SERVICE RECORD -->
                <div class="thin-border p-2 mb-3 mt-4">
                  <h4 class="font-semibold mb-2">SOCIAL SERVICE RECORD</h4>
                  <table id="rv_ss_table" class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                      <tr>
                        <th class="border px-2 py-1 w-20 text-center">DATE</th>
                        <th class="border px-2 py-1 text-center">
                          PROBLEM PRESENTED<br /><span
                            class="text-xs text-gray-500"
                            >(to be filled by support staff)</span
                          >
                        </th>
                        <th class="border px-2 py-1 text-center">
                          ASSISTANCE PROVIDED<br /><span
                            class="text-xs text-gray-500"
                            >(to be filled by program implementer)</span
                          >
                        </th>
                        <th class="border px-2 py-1 text-center">REMARKS</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="border h-8"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                      </tr>
                      <tr>
                        <td class="border h-8"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                      </tr>
                      <tr>
                        <td class="border h-8"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                      </tr>
                      <tr>
                        <td class="border h-8"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                      </tr>
                      <tr>
                        <td class="border h-8"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                        <td class="border"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- HEALTH CONDITION & CODES -->
                <div class="thin-border p-2">
                  <h4 class="font-semibold mb-2">
                    HEALTH CONDITION &amp; CODES
                  </h4>
                  <p><strong>Health Condition:</strong></p>
                  <p class="ml-2">
                    A. DEAD • B. INJURED • C. MISSING • D. With Illness
                  </p>
                </div>
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
                <button
                  type="button"
                  onclick="window.print()"
                  class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700"
                >
                  Print
                </button>
                <button
                  type="button"
                  onclick="submitForm()"
                  class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700"
                >
                  Submit Form
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <script>
      // localStorage key
      const STORAGE_KEY = 'familyIntakeFormData';

      // Load form data from localStorage
      function loadFormData() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
          const data = JSON.parse(saved);
          populateReview(data);
        } else {
          alert("No form data found. Please complete the form first.");
        }
      }

      // Populate review with loaded data
      function populateReview(d) {
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
        const saved = localStorage.getItem(STORAGE_KEY);
        if (!saved) {
          alert("No form data found. Please complete the form first.");
          return;
        }
        
        const data = JSON.parse(saved);
        // For demonstration, alert the data. In a real application, send to server.
        alert("Form submitted successfully!\n\nData:\n" + JSON.stringify(data, null, 2));
        // Example: fetch('/submit', { method: 'POST', body: JSON.stringify(data), headers: {'Content-Type': 'application/json'} });
      }

      // Setup on load
      window.addEventListener("load", () => {
        loadFormData();
      });
    </script>
  </body>
</html>