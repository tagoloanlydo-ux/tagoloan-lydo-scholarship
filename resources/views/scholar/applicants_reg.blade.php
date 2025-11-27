<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/application_reg.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
    <title>Personal Information Form</title>
    <style>
    .duplicate-message {
    font-size: 12px;
    margin-top: 5px;
    padding: 5px;
    border-radius: 4px;
    text-align: center;
}
    /* Simple left-aligned back button */
    .header-simple {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .back-btn-left {
        background: transparent;
        border: none;
        font-size: 28px;
        color: rgb(0, 0, 0);
        cursor: pointer;
        padding: 5px 15px 5px 0;
        margin: 0;
        line-height: 1;
        align-self: flex-start;
    }

    .header-content-left {
        flex: 1;
        text-align: center;
    }
    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f0f0f5 0%, #d9d9ff 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        overflow-x: hidden;
    }

    /* Full screen container */
    .full-screen-container {
        width: 100%;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 0;
        box-sizing: border-box;
    }

    /* Select2 customization */
    .select2-container--default .select2-selection--single {
      border: 1px solid #d1d5db !important;
      border-radius: 0.5rem !important;
      height: 42px !important;
      padding: 8px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 40px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 26px !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: #7c3aed !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
      border: 1px solid #d1d5db !important;
      border-radius: 0.375rem !important;
    }

    .select2-dropdown {
      border: 1px solid #d1d5db !important;
      border-radius: 0.5rem !important;
    }

    /* Error and valid states */
    .error { 
      border-color: #ef4444 !important; 
      background-color: #fef2f2 !important;
    }
    .valid { 
      border-color: #10b981 !important; 
      background-color: #f0fdf4 !important;
    }
    .error-message { 
      color: #ef4444; 
      font-size: 12px; 
      margin-top: 4px; 
      display: block; 
    }

    .tab-container {
        width: 100%;
        min-height: 100vh; /* change from fixed height to min-height so content doesn't force buttons to bottom */
        background: transparent;
        padding: 30px;
        border-radius: 0;
        box-shadow: none;
        position: relative;
        overflow: hidden;
        z-index: 1;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
    }

    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #000000 !important;
        border-radius: 8px !important;
        background-color: #fff !important;
        padding: 0 !important;
        font-size: 14px !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #000 !important;
        line-height: 40px !important;
        padding-left: 12px !important;
        padding-right: 20px !important;
        font-size: 14px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #000000 !important;
        opacity: 1 !important;
        font-size: 14px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #000 transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #7c3aed !important;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.25) !important;
    }

    .select2-container--default .select2-results__option {
        padding: 10px 12px !important;
        font-size: 14px !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #7c3aed !important;
    }

    .select2-container--default .select2-results__group {
        font-weight: 600 !important;
        color: #3b0066 !important;
        background-color: #f0f0f5 !important;
        padding: 8px 12px !important;
        font-size: 14px !important;
    }
    .login-container {
        width: 100%;
        max-width: 800px;
        background: transparent;
        padding: 30px 30px 10px;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(102, 51, 153, 0.2);
        position: relative;
        overflow: hidden;
        z-index: 1;
        box-sizing: border-box;
    }
    .credentials-container {
        width: 100%;
        max-width: 500px;
        background: transparent;
        padding: 30px 30px 50px;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(102, 51, 153, 0.2);
        position: relative;
        overflow: hidden;
        z-index: 1;
        box-sizing: border-box;
    }

    /* Headers */
    .login-container h1,
    .credentials-container h1,
    .tab-container h1 {
        color: #000000;
        font-size: 30px;
        margin-bottom: 6px;
        font-weight: 800;
        text-align: center;
    }
    .login-container p.subtitle,
    .credentials-container p.subtitle,
    .tab-container p.subtitle {
        color: #000000;
        margin-bottom: 20px;
        font-size: 15px;
        text-align: center;
    }

    /* ===== Form ===== */
    form {
        z-index: 3;
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .input-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 15px;
    }
    .input-group {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .input-group.wide {
        flex: 2;
    }
    .input-group.small {
        max-width: 120px;
        flex: 0 0 auto;
    }

    /* Labels */
    label {
        font-weight: 600;
        margin-bottom: 4px;
        color: #3b0066;
        font-size: 14px;
    }

    /* Error / Valid */
    input.error,
    select.error {
        border: 2px solid #dc2626; /* red-600 */
    }
    input.valid,
    select.valid {
        border: 2px solid #16a34a; /* green-600 */
    }
    .error-message {
        color: red;
        font-size: 12px;
        margin-top: 3px;
        display: block;
        white-space: nowrap;
    }

    /* Inputs + Selects */
    input,
    select {
        padding: 10px 12px;
        border: 1px solid #000000;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        height: 42px;
        background-color: #fff;
        width: 100%;
        box-sizing: border-box;
        transition: all 0.2s ease;
    }
    input:focus,
    select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.25);
    }

    /* Placeholder */
    input::placeholder {
        color: #000000;
        font-size: 14px;
        opacity: 1;
    }
    select:invalid {
        color: #9ca3af;
    }
    select option {
        color: #111;
    }

    /* File Inputs */
    input[type="file"] {
        border: 1px solid #999;
        border-radius: 8px;
        padding: 6px;
        height: 42px;
        font-size: 14px;
        color: #3b0066;
    }
    input[type="file"]::file-selector-button {
        background: #7c3aed;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        margin-right: 10px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
    }
    input[type="file"]::file-selector-button:hover {
        background: #5b21b6;
    }

    /* Submit Button */
    .login-btn {
        padding: 12px 20px;
        font-weight: 700;
        font-size: 14px;
        margin-top: 0;
        background: linear-gradient(90deg, #4b2b8d 0%, #230061 100%);
        color: white;
        border: none;
        border-radius: 14px;
        cursor: pointer;
        box-shadow: 0 10px 20px #5132a6cc;
        transition: background 0.3s ease;
        height: 42px;
    }
    .login-btn:hover {
        background: linear-gradient(90deg, #5b21b6 0%, #3b0066 100%);
    }

    /* Tab Navigation */
    .tab-nav {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #ddd;
    }

    .tab-button {
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        background: #f0f0f0;
        color: #666;
        border: none;
        border-radius: 8px 8px 0 0;
        cursor: pointer;
        transition: background 0.3s ease, color 0.3s ease;
        margin-right: 5px;
    }

    .tab-button.active {
        background: linear-gradient(90deg, #4b2b8d 0%, #230061 100%);
        color: white;
    }

    .tab-button:hover {
        background: #ddd;
    }

    .tab-button.active:hover {
        background: linear-gradient(90deg, #5b21b6 0%, #3b0066 100%);
    }

    .tab-content {
        display: none;
        flex: 0 1 auto; /* prevent tab content from stretching to full height */
        overflow-y: auto;
        margin-bottom: 8px; /* small gap above buttons */
    }

    .tab-content.active {
        display: flex;
        flex-direction: column;
    }

    .button-row {
        display: flex;
        justify-content: space-between;
        margin-top: 8px; /* reduce spacing from fields to buttons */
        gap: 100px;
    }

    /* Gamitin ito sa HTML: <button class="nav-btn">Next</button> */
    .nav-btn {
        padding: 12px 20px;
        font-weight: 700;
        font-size: 14px;
        background: linear-gradient(90deg, #4b2b8d 0%, #230061 100%);
        color: white;
        border: none;
        border-radius: 14px;
        cursor: pointer;
        box-shadow: 0 10px 20px #5132a6cc;
        transition: background 0.3s ease;
        height: 42px;
        /* WALANG MARGIN-TOP DITO */
    }
    .nav-btn:hover {
        background: linear-gradient(90deg, #5b21b6 0%, #3b0066 100%);
    }

    .nav-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* ===== Decorative Elements ===== */
    .decorative-wave-container {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 180px;
        z-index: 0;
        overflow: hidden;
    }
    svg {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 200%;
        height: 100%;
    }
    .wave1 {
        animation: waveMove 10s linear infinite;
    }
    .wave2 {
        animation: waveMove 14s linear infinite;
    }
    .wave3 {
        animation: waveMove 20s linear infinite;
    }
    .wave4 {
        animation: waveMove 25s linear infinite;
    }
    .wave5 {
        animation: waveMove 30s linear infinite;
    }
    @keyframes waveMove {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }

    /* Floating Circles */
    .floating-circles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    }
    .circle {
        position: absolute;
        width: 25px;
        height: 25px;
        background: rgba(109, 83, 211, 0.25);
        border-radius: 50%;
        animation: floatUp 12s infinite ease-in-out;
    }
    .circle.small {
        width: 15px;
        height: 15px;
        background: rgba(140, 112, 247, 0.3);
        animation-duration: 15s;
    }
    .square1 {
        width: 14px;
        height: 14px;
        right: 40px;
        top: 80%;
        border-radius: 4px;
        background: rgba(109, 83, 211, 0.3);
        box-shadow: 0 0 6px rgba(109, 83, 211, 0.5);
    }

    /* Page Header */
    .page-header {
        width: 100%;
        text-align: center;
        margin-bottom: 10px;
        margin-top: 0;
    }
    .page-header .logo {
        width: 80px;
        height: auto;
        display: block;
        margin: 0 auto 10px;
    }
    .page-header .title {
        font-size: 32px;
        font-weight: 900;
        color: #3b0066;
        margin: 0;
    }
    .footer-text {
        margin-top: 5px;
        text-align: center;
        font-size: 17px;
        font-weight: 500;
        color: #555;
    }

    .banner-grad {
            background: linear-gradient(90deg, #4c1d95 0%, #7e22ce 100%);
        height: 100px;
        position: relative;
    }
    /* Back button */
    .back-btn {
        background: transparent;
        border: none;
        margin-right: 20px;
        font-size: 28px;
        color: rgb(0, 0, 0);
        font-size: 50px;
        cursor: pointer;
    }

    /* ===== Responsive Design ===== */

    /* Large screens (desktops) */
    @media (min-width: 1200px) {
        .full-screen-container {
            padding: 0;
        }

        .tab-container {
            max-width: 100%;
        }
    }

    /* Medium screens (tablets and small desktops) */
    @media (max-width: 1024px) {
        .full-screen-container {
            padding: 0;
        }

        .tab-container {
            max-width: 100%;
        }

        .tab-container h1 {
            font-size: 28px;
        }

        .tab-container p.subtitle {
            font-size: 14px;
        }

        .input-row {
            gap: 15px;
        }
    }

    /* Small screens (tablets) */
    @media (max-width: 768px) {
        body {
            padding: 0;
        }

        .full-screen-container {
            padding: 0;
        }

        .tab-container {
            width: 100%;
            max-width: none;
            padding: 20px;
        }

        .tab-container h1 {
            font-size: 26px;
        }

        .tab-container p.subtitle {
            font-size: 14px;
        }

        .input-row {
            gap: 15px;
        }

        .tab-nav {
            flex-direction: column;
            gap: 8px;
        }

        .tab-button {
            margin-right: 0;
            border-radius: 8px;
            padding: 14px 20px;
        }

        .back-btn {
            font-size: 45px;
        }
    }

    /* Extra small screens (large phones) */
    @media (max-width: 640px) {
        body {
            padding: 0;
        }

        .full-screen-container {
            padding: 0;
        }

        .tab-container {
            padding: 15px;
            border-radius: 0;
        }

        .tab-container h1 {
            font-size: 24px;
        }

        .tab-container p.subtitle {
            font-size: 13px;
        }

        .input-row {
            gap: 12px;
        }

        .tab-nav {
            gap: 6px;
        }

        .tab-button {
            padding: 12px 18px;
            font-size: 13px;
        }

        .back-btn {
            font-size: 40px;
        }
    }

    /* Mobile phones */
    @media (max-width: 480px) {
        body {
            padding: 0;
        }

        .full-screen-container {
            padding: 0;
        }

        .tab-container {
            padding: 12px;
            border-radius: 0;
        }

        .tab-container h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .tab-container p.subtitle {
            font-size: 12px;
            margin-bottom: 18px;
        }

        .input-row {
            flex-direction: column;
            gap: 10px;
            margin-bottom: 12px;
        }

        .input-group {
            width: 100%;
        }

        .input-group[style*="width: 10px"] {
            width: 100% !important;
        }

        label {
            font-size: 13px;
        }

        input,
        select {
            padding: 9px 11px;
            font-size: 14px;
            height: 40px;
        }

        input[type="file"] {
            padding: 5px;
            height: 40px;
            font-size: 14px;
        }

        input[type="file"]::file-selector-button {
            padding: 5px 10px;
            font-size: 13px;
        }

        .login-btn {
            padding: 14px 18px;
            font-size: 16px;
            height: 44px;
        }

        .button-row {
            flex-direction: column;
            gap: 12px;
        }

        .nav-btn {
            width: 100%;
            padding: 14px 18px;
            font-size: 16px;
            height: 44px;
        }

        .tab-nav {
            flex-direction: column;
            gap: 5px;
        }

        .tab-button {
            margin-right: 0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
        }

        .back-btn {
            font-size: 38px;
            margin-right: 8px;
        }

        .error-message {
            font-size: 12px;
        }
    }

    /* Very small screens */
    @media (max-width: 360px) {
        body {
            padding: 0;
        }

        .full-screen-container {
            padding: 0;
        }

        .tab-container {
            padding: 10px;
            border-radius: 0;
        }

        .tab-container h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .tab-container p.subtitle {
            font-size: 11px;
            margin-bottom: 15px;
        }

        input,
        select {
            padding: 8px 10px;
            font-size: 13px;
            height: 38px;
        }

        input[type="file"] {
            padding: 4px;
            height: 38px;
            font-size: 13px;
        }

        input[type="file"]::file-selector-button {
            padding: 4px 8px;
            font-size: 12px;
        }

        .login-btn,
        .nav-btn {
            padding: 12px 16px;
            font-size: 15px;
            height: 42px;
        }

        .tab-button {
            padding: 10px 14px;
            font-size: 13px;
        }

        .back-btn {
            font-size: 35px;
            margin-right: 5px;
        }

        .error-message {
            font-size: 11px;
        }
    }

    </style>
    </head>
  <body>
    <div class="banner-grad flex flex-col md:flex-row items-center justify-center md:justify-between w-full h-25 px-4 md:px-6 text-white">
      <div class="flex flex-col md:flex-row items-center text-center md:text-left">
           <img src="/images/LYDO.png" alt="LYDO Logo" class="h-8 md:h-10 mb-2 md:mb-0 md:mr-4"/>
        <div>
          <h1 class="text-xl md:text-2xl font-bold">LYDO SCHOLARSHIP</h1>
          <p class="text-xs tracking-widest">
            PARA SA KABATAAN, PARA SA KINABUKASAN.
          </p>
        </div>
      </div>
    </div>
    
    <!-- Full-screen form container -->
    <div class="full-screen-container">
<div class="tab-container">
    <div class="header-simple">
        <button class="back-btn-left" onclick="history.back()">←</button>
        <div class="header-content-left">
            <h1>Applicant Registration</h1>
            <p class="subtitle">Fill out the required details below</p>
        </div>
            </div>
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Tab Navigation -->
        <div class="tab-nav">
          <button class="tab-button active" data-tab="personal">Personal Information</button>
          <button class="tab-button" data-tab="education">Educational Attainment</button>
          <button class="tab-button" data-tab="requirements">Application Requirements</button>
        </div>

        <form id="applicationForm" method="POST" action="{{ route('applicants.register') }}" enctype="multipart/form-data">
        @csrf

          <!-- Tab Content: Personal Information -->
          <div id="personal" class="tab-content active">
            <!-- Name Fields -->
            <div class="input-row">
              <div class="input-group">
                <label for="fname">First Name</label>
                <input type="text" id="fname" name="applicant_fname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="mname">Middle Name</label>
                <input type="text" id="mname" name="applicant_mname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="applicant_lname" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>
              <div class="input-group" style="width: 10px">
                <label for="suffix">Suffix</label>
                <input type="text" id="suffix" name="applicant_suffix" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                <small class="error-message"></small>
              </div>
            </div>

            <!-- Personal Details -->
            <div class="input-row">
              <div class="input-group">
                <label for="gender">Gender</label>
                <select id="gender" name="applicant_gender" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                  <option value=""></option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="bdate">Birth Date</label>
                <input type="date" id="bdate" name="applicant_bdate" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="civil_status">Civil Status</label>
                <select id="civil_status" name="applicant_civil_status" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                  <option value=""></option>
                  <option value="single">Single</option>
                  <option value="married">Married</option>
                  <option value="widowed">Widowed</option>
                  <option value="divorced">Divorced</option>
                </select>
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="brgy">Barangay</label>
                <select id="brgy" name="applicant_brgy" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                  <option value=""></option>
                  <option value="Sugbong cogon">Sugbong cogon</option>
                  <option value="Baluarte">Baluarte</option>
                  <option value="Casinglot">Casinglot</option>
                  <option value="Gracia">Gracia</option>
                  <option value="Mohon">Mohon</option>
                  <option value="Natumolan">Natumolan</option>
                  <option value="Poblacion">Poblacion</option>
                  <option value="Rosario">Rosario</option>
                  <option value="Santa Ana">Santa Ana</option>
                  <option value="Santa Cruz">Santa Cruz</option>
                </select>
                <small class="error-message"></small>
              </div>
            </div>

            <!-- Contact Details -->
            <div class="input-row">
              <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="applicant_email" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="contact">Contact Number</label>
                <input type="tel" id="contact" name="applicant_contact_number" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required maxlength="12" />
                <small class="error-message"></small>
              </div>
            </div>
          </div>

          <!-- Tab Content: Educational Attainment -->
          <div id="education" class="tab-content">
            <div class="input-row">
              <div class="input-group" style="width: 100%">
                <label for="school_name">School Name</label>
                <select id="school_name" name="applicant_school_name" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 select2" required>
                  <option value=""></option>
                  <optgroup label="Others">
                    <option value="Others">Others (Please specify below)</option>
                  </optgroup>
                  <optgroup label="State Universities">
                    <option value="USTP CDO">University of Science and Technology of Southern Philippines (USTP) – Cagayan de Oro</option>
                    <option value="USTP Claveria">University of Science and Technology of Southern Philippines (USTP) – Claveria</option>
                    <option value="USTP Villanueva">University of Science and Technology of Southern Philippines (USTP) – Villanueva</option>
                    <option value="MSU Naawan">Mindanao State University – Naawan (MSU-Naawan)</option>
                    <option value="MOSCAT">Misamis Oriental State College of Agriculture and Technology (MOSCAT), Claveria</option>
                  </optgroup>
                  <optgroup label="Community Colleges">
                    <option value="Opol Community College">Opol Community College</option>
                    <option value="Tagoloan Community College">Tagoloan Community College</option>
                    <option value="Bugo Community College">Bugo Community College</option>
                    <option value="Initao Community College">Initao Community College</option>
                    <option value="Magsaysay College">Magsaysay College, Misamis Oriental</option>
                  </optgroup>
                  <optgroup label="Private Colleges & Universities">
                    <option value="Liceo de Cagayan University">Liceo de Cagayan University, CDO</option>
                    <option value="PHINMA COC">PHINMA Cagayan de Oro College</option>
                    <option value="Capitol University">Capitol University, CDO</option>
                    <option value="Lourdes College">Lourdes College, CDO</option>
                    <option value="Blessed Mother College">Blessed Mother College, CDO</option>
                    <option value="Pilgrim Christian College">Pilgrim Christian College, CDO</option>
                    <option value="Gingoog Christian College">Gingoog Christian College</option>
                    <option value="Christ the King College">Christ the King College, Gingoog City</option>
                    <option value="St. Rita's College">St. Rita's College of Balingasag</option>
                    <option value="St. Peter's College">St. Peter's College of Balingasag</option>
                    <option value="Saint John Vianney Seminary">Saint John Vianney Theological Seminary, CDO</option>
                    <option value="Asian College of science and Technology">Asian College of Science and Technology, CDO</option>
                  </optgroup>
                </select>
                <input type="text" id="school_name_other" name="applicant_school_name_other" placeholder="Please specify your school" style="display: none; margin-top: 8px; padding: 10px; border: 1px solid black; border-radius: 8px; font-size: 14px; outline: none; width: 100%;"/>
                <small class="error-message"></small>
              </div>
            </div>

            <!-- Academic Details -->
            <div class="input-row">
              <div class="input-group">
                <label for="year_level">Year Level</label>
                <select id="year_level" name="applicant_year_level" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                  <option value="">Select Year Level</option>
                  <option value="1st Year">1st Year</option>
                  <option value="2nd Year">2nd Year</option>
                  <option value="3rd Year">3rd Year</option>
                  <option value="4th Year">4th Year</option>
                  <option value="5th Year">5th Year</option>
                </select>
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="applicant_course" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="Course" />
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="acad_year">Academic Year</label>
                <input type="text" id="acad_year" name="applicant_acad_year" class="pl-2 w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required placeholder="e.g., 2023-2024" readonly />
                <small class="error-message"></small>
              </div>
            </div>
          </div>

          <!-- Tab Content: Application Requirements -->
          <div id="requirements" class="tab-content">
            <div class="input-row">
              <div class="input-group">
                <label for="application_letter">Application Letter</label>
                <input type="file" id="application_letter" name="application_letter" accept="application/pdf" required class="input-file"/>
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="grade_slip">Grade Slip</label>
                <input type="file" id="grade_slip" name="grade_slip" accept="application/pdf" required class="input-file" />
                <small class="error-message"></small>
              </div>
            </div>

            <div class="input-row">
              <div class="input-group">
                <label for="certificate_of_registration">Certificate of Registration</label>
                <input type="file" id="certificate_of_registration" name="certificate_of_registration" accept="application/pdf" required class="input-file"/>
                <small class="error-message"></small>
              </div>
              <div class="input-group">
                <label for="barangay_indigency">Barangay Indigency</label>
                <input type="file" id="barangay_indigency" name="barangay_indigency" accept="application/pdf" required class="input-file"/>
                <small class="error-message"></small>
              </div>
            </div>

            <div class="input-row">
              <div class="input-group">
                <label for="student_id">Student ID</label>
                <input type="file" id="student_id" name="student_id" accept="application/pdf" required class="input-file"/>
                <small class="error-message"></small>
              </div>
            </div>
            
          </div>

          <!-- Navigation Buttons -->
          <div class="button-row">
            <button type="button" id="prevBtn" class="nav-btn prev-btn" style="display: none;">Previous</button>
            <button type="button" id="nextBtn" class="nav-btn next-btn">Next</button>
            <button type="submit" id="submitBtn" class="nav-btn submit-btn" style="display: none;">
              <span id="submitBtnText">Submit</span>
              <svg id="submitBtnSpinner" class="hidden animate-spin h-5 w-5 ml-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
            </button>
          </div>
        </form>
      </div>
    </div>

<script>
// Tab switching logic
const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const submitBtn = document.getElementById('submitBtn');
let currentTab = 0;
let select2Initialized = false;

// Enhanced duplicate applicant check function
function checkDuplicateApplicant() {
    const fname = document.getElementById('fname').value.trim();
    const lname = document.getElementById('lname').value.trim();
    const gender = document.getElementById('gender').value;
    const bdate = document.getElementById('bdate').value;
    const acadYear = document.getElementById('acad_year').value;

    // Only check if all required fields are filled
    if (!fname || !lname || !gender || !bdate || !acadYear) {
        // Clear duplicate message if fields are incomplete
        const personalTab = document.getElementById('personal');
        let duplicateMessage = personalTab.querySelector('.duplicate-message');
        if (duplicateMessage) {
            duplicateMessage.innerHTML = '';
        }
        return;
    }

    // Show checking state
    const personalTab = document.getElementById('personal');
    let duplicateMessage = personalTab.querySelector('.duplicate-message');
    if (!duplicateMessage) {
        duplicateMessage = document.createElement('div');
        duplicateMessage.className = 'duplicate-message';
        // Insert after the birthdate field or at the end of personal tab
        personalTab.appendChild(duplicateMessage);
    }

    duplicateMessage.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Checking for existing applications...';
    duplicateMessage.style.color = '#3b82f6';
    duplicateMessage.style.fontSize = '12px';
    duplicateMessage.style.marginTop = '5px';
    duplicateMessage.style.padding = '5px';
    duplicateMessage.style.borderRadius = '4px';
    duplicateMessage.style.textAlign = 'center';

    fetch('/check-duplicate-applicant', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            fname: fname,
            lname: lname,
            gender: gender,
            bdate: bdate,
            acad_year: acadYear
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            duplicateMessage.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>An applicant with the same name, gender, birth date, and academic year already exists.';
            duplicateMessage.style.color = '#ef4444';
            duplicateMessage.style.backgroundColor = '#fef2f2';
            duplicateMessage.style.border = '1px solid #fecaca';
            
            // Disable form submission
            nextBtn.disabled = true;
            submitBtn.disabled = true;
        } else {
            duplicateMessage.innerHTML = '<i class="fa-solid fa-circle-check mr-1"></i>No duplicate application found for this academic year.';
            duplicateMessage.style.color = '#10b981';
            duplicateMessage.style.backgroundColor = '#f0fdf4';
            duplicateMessage.style.border = '1px solid #bbf7d0';
            
            // Re-enable buttons if no other errors
            toggleButton();
        }
    })
    .catch(error => {
        console.error('Error checking duplicate applicant:', error);
        duplicateMessage.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>Error checking for duplicates. Please try again.';
        duplicateMessage.style.color = '#ef4444';
        duplicateMessage.style.backgroundColor = '#fef2f2';
        duplicateMessage.style.border = '1px solid #fecaca';
    });
}

// Add event listeners for duplicate applicant check
function initializeDuplicateChecking() {
    const fieldsToCheck = ['fname', 'lname', 'gender', 'bdate', 'acad_year'];
    
    fieldsToCheck.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', checkDuplicateApplicant);
            field.addEventListener('blur', checkDuplicateApplicant);
            // For academic year, also check on input since it's readonly but might be auto-filled
            if (fieldId === 'acad_year') {
                field.addEventListener('input', checkDuplicateApplicant);
            }
        }
    });
}

function showTab(index) {
  tabContents.forEach(content => content.classList.remove('active'));
  tabButtons.forEach(button => button.classList.remove('active'));
  tabContents[index].classList.add('active');
  tabButtons[index].classList.add('active');

  prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
  nextBtn.style.display = index === tabContents.length - 1 ? 'none' : 'inline-block';
  submitBtn.style.display = index === tabContents.length - 1 ? 'inline-block' : 'none';

  // Initialize Select2 when education tab is shown
  if (index === 1 && !select2Initialized) {
    $('#school_name').select2({
      placeholder: 'Search and select your school...',
      allowClear: true,
      minimumInputLength: 0, // show all options on initial dropdown click
      width: '100%',
      dropdownParent: $('#education'),
      // Always match "Others" so it's visible even when search term doesn't match any option
      matcher: function(params, data) {
        // If no search term (empty), keep default behaviour
        if ($.trim(params.term) === '') return data;

        // If this 'data' is a group, apply matching on its children and always keep 'Others'
        if (data.children && data.children.length) {
          var filteredChildren = [];
          data.children.forEach(function(child) {
            // Always include 'Others' option regardless of search term
            if (child.id === 'Others' || (child.text && child.text.toLowerCase().indexOf('others') !== -1)) {
              filteredChildren.push(child);
              return;
            }
            // Keep child if it matches search term
            if (child.text && child.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
              filteredChildren.push(child);
            }
          });

          if (filteredChildren.length > 0) {
            // return a new copy of the group with filtered children
            var copy = $.extend(true, {}, data);
            copy.children = filteredChildren;
            return copy;
          }
          return null;
        }

        // Single option fallback: always include Others
        if (data.id === 'Others' || (data.text && data.text.toLowerCase().indexOf('others') !== -1)) {
          return data;
        }

        // Default matching
        if (typeof data.text === 'undefined') return null;
        if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
          return data;
        }
        return null;
      },
      // Put 'Others' at the top of the dropdown results
      sorter: function(data) {
        return data.sort(function(a, b) {
          // If 'a' or 'b' is a group, their text might be the optgroup label; check children first
          if (a.id === 'Others' || (a.text && a.text.toLowerCase().indexOf('others') !== -1)) return -1;
          if (b.id === 'Others' || (b.text && b.text.toLowerCase().indexOf('others') !== -1)) return 1;
          return a.text.localeCompare(b.text);
        });
      },
      language: {
        noResults: function() {
          return 'No results found. If your school is not listed, select "Others".';
        }
      },
      escapeMarkup: function(markup) { return markup; }
    });

  // Handle "Others" option
  $('#school_name').on('change', function() {
    const schoolOtherInput = document.getElementById("school_name_other");
    if (this.value === "Others") {
      schoolOtherInput.style.display = "block";
      schoolOtherInput.setAttribute("required", "required");
    } else {
      schoolOtherInput.style.display = "none";
      schoolOtherInput.removeAttribute("required");
      schoolOtherInput.value = "";
    }
    validateInput(this);
    toggleButton();
  });

  select2Initialized = true;
}

  updateButtonStates();
}

function updateButtonStates() {
  const currentTabContent = tabContents[currentTab];
  const hasErrorMessage = Array.from(
    currentTabContent.querySelectorAll(".error-message")
  ).some((msg) => msg.textContent.trim() !== "");

  const hasEmptyRequired = Array.from(
    currentTabContent.querySelectorAll("input[required], select[required]")
  ).some((input) => {
    if (input.type === "file") return input.files.length === 0;
    return !input.value.trim();
  });

  nextBtn.disabled = hasErrorMessage || hasEmptyRequired;
}

tabButtons.forEach((button, index) => {
  button.addEventListener('click', () => {
    // Validate current tab before allowing switch to another tab
    const currentTabContent = tabContents[currentTab];
    const hasErrorMessage = Array.from(
      currentTabContent.querySelectorAll(".error-message")
    ).some((msg) => msg.textContent.trim() !== "");

    const hasEmptyRequired = Array.from(
      currentTabContent.querySelectorAll("input[required], select[required]")
    ).some((input) => {
      if (input.type === "file") return input.files.length === 0;
      return !input.value.trim();
    });

    if (index !== currentTab && (hasErrorMessage || hasEmptyRequired)) {
      return; // Prevent switching to other tabs if current tab has errors
    }

    currentTab = index;
    showTab(currentTab);
  });
});

prevBtn.addEventListener('click', () => {
  if (currentTab > 0) {
    currentTab--;
    showTab(currentTab);
  }
});

nextBtn.addEventListener('click', () => {
  // Validate current tab before proceeding
  const currentTabContent = tabContents[currentTab];
  const hasErrorMessage = Array.from(
    currentTabContent.querySelectorAll(".error-message")
  ).some((msg) => msg.textContent.trim() !== "");

  const hasEmptyRequired = Array.from(
    currentTabContent.querySelectorAll("input[required], select[required]")
  ).some((input) => {
    if (input.type === "file") return input.files.length === 0;
    return !input.value.trim();
  });

  if (hasErrorMessage || hasEmptyRequired) {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Please complete all required fields and fix any errors before proceeding to the next tab.",
    });
    return; // Prevent tab switch
  }

  if (currentTab < tabContents.length - 1) {
    currentTab++;
    showTab(currentTab);
  }
});

// Initialize first tab
showTab(currentTab);

const applicationForm = document.getElementById("applicationForm");
const submitBtnText = document.getElementById('submitBtnText');
const submitBtnSpinner = document.getElementById('submitBtnSpinner');

const rules = {
  name: /^[A-Za-z\s]+$/, // letters and spaces only
  contact: /^(09\d{9}|\+639\d{9})$/,
  gmail: /^[a-zA-Z0-9._%+-]+@gmail\.com$/  // Gmail format
};

let debounceTimers = {};

function validateInput(input) {
  const id = input.id;
  const value = input.value.trim();
  const errorEl = getErrorEl(input);
  let errorMsg = "";
  let valid = true;

  // Required field validation
  if (input.hasAttribute("required") && !value) {
    errorMsg = "This field cannot be empty";
    valid = false;
  }

  // Name validation (fname, mname, lname)
  if (valid && ["fname", "mname", "lname"].includes(id)) {
    if (value && !rules.name.test(value)) {
      errorMsg = "Only letters are allowed";
      valid = false;
    }
  }

  // Contact validation
  if (valid && id === "contact") {
    if (value && !rules.contact.test(value)) {
      errorMsg = "Format: 09XXXXXXXXX or +639XXXXXXXXX";
      valid = false;
    }
  }

  // Email format validation ONLY (don't check duplicates here)
  if (valid && id === "email" && value) {
    if (!rules.gmail.test(value)) {
      errorMsg = "Email must end with @gmail.com";
      valid = false;
    }
  }

  // Birthdate validation
  if (valid && id === "bdate") {
    if (value) {
      const date = new Date(value);
      const today = new Date();
      if (isNaN(date.getTime())) {
        errorMsg = "Invalid date";
        valid = false;
      } else if (date > today) {
        errorMsg = "Birth date cannot be in the future";
        valid = false;
      }
    }
  }

  // Select validation
  if (valid && input.tagName === 'SELECT' && !value) {
    errorMsg = "This field is required";
    valid = false;
  }

  // Update UI
  updateUI(input, valid, errorMsg);
  
  // Don't call checkDuplicate here - let the event listeners handle it
  toggleButton();

  return valid;
}

function getErrorEl(input) {
  return input.parentElement.querySelector(".error-message");
}

function updateUI(input, valid, errorMsg = "") {
  const errorEl = getErrorEl(input);
  if (!valid) {
    input.classList.add("error");
    input.classList.remove("valid");
    if (errorEl) errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-1"></i>' + errorMsg;
  } else {
    input.classList.remove("error");
    input.classList.add("valid");
    if (errorEl) errorEl.innerHTML = "";
  }
}

function validateFile(input) {
  const file = input.files[0];
  let valid = true;
  let errorMsg = "";

  if (!file) {
    valid = false;
    errorMsg = "This file is required";
  } else {
    const isPdf = file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");
    if (!isPdf) {
      valid = false;
      errorMsg = "Only PDF files are allowed";
    } else if (file.size > 5 * 1024 * 1024) {
      valid = false;
      errorMsg = "File size must not exceed 5MB";
    }
  }

  updateUI(input, valid, errorMsg);
  toggleButton();
  return valid;
}

// Email duplicate check with debouncing - IMPROVED VERSION
function checkDuplicate(input) {
  const id = input.id;
  const value = input.value.trim();

  // Find the correct error message element
  const errorEl = getErrorEl(input);
  
  // Clear previous timer for this specific field
  if (debounceTimers[id]) {
    clearTimeout(debounceTimers[id]);
  }
  
  // Immediately clear error if empty
  if (!value) {
    input.classList.remove("error");
    input.classList.remove("valid");
    errorEl.innerHTML = "";
    toggleButton();
    return;
  }

  // Only check duplicates for email
  if (id !== 'email') {
    return;
  }

  // First validate format
  const formatValid = validateInput(input);
  if (!formatValid) {
    toggleButton();
    return;
  }

  // Show checking state
  errorEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Checking email availability...';
  
  // Debounce duplicate check
  debounceTimers[id] = setTimeout(() => {
    fetch('/check-applicant-email', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ email: value })
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      if (data.exists) {
        // AUTOMATIC ERROR DISPLAY - Duplicate found
        input.classList.add("error");
        input.classList.remove("valid");
        errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1"></i>This email is already registered. Please use a different email.`;
      } else {
        // No duplicate - mark as valid
        input.classList.remove("error");
        input.classList.add("valid");
        errorEl.innerHTML = "";
      }
      toggleButton();
    })
    .catch(error => {
      console.error('Error checking duplicate:', error);
      errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1"></i>Error checking email. Please try again.`;
      toggleButton();
    });
  }, 800); // Slightly longer debounce for duplicate checks
}

function toggleButton() {
  updateButtonStates();
}

// Enhanced event listeners for real-time validation - IMPROVED VERSION
function initializeEventListeners() {
  const inputs = applicationForm.querySelectorAll("input, select");
  
  inputs.forEach(input => {
    // Real-time validation on input for email
    if (input.id === 'email') {
      input.addEventListener("input", function() {
        validateInput(this);
        checkDuplicate(this);
      });
    } else {
      input.addEventListener("input", function() {
        validateInput(this);
        toggleButton();
      });
    }

    // Validate on blur as well
    input.addEventListener("blur", function() {
      if (this.id === 'email') {
        validateInput(this);
        checkDuplicate(this);
      } else {
        validateInput(this);
        toggleButton();
      }
    });
  });

  // Add event listeners for duplicate applicant check
  const fieldsToCheck = ['fname', 'lname', 'gender', 'bdate', 'acad_year'];
  
  fieldsToCheck.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('change', checkDuplicateApplicant);
      field.addEventListener('blur', checkDuplicateApplicant);
    }
  });
}

// Attach events to all inputs
applicationForm.querySelectorAll("input, select").forEach((input) => {
  if (input.type === "file") {
    input.addEventListener("change", () => validateFile(input));
  } else {
    input.addEventListener("blur", () => validateInput(input));
    if (input.tagName === "SELECT") {
      input.addEventListener("change", () => validateInput(input));
    } else {
      input.addEventListener("input", () => validateInput(input));
    }
  }
});

// Attach file validation to all file inputs
["application_letter", "grade_slip", "certificate_of_registration", "barangay_indigency", "student_id"]
  .forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("change", function () {
        validateFile(this);
      });
    }
  });

// Academic year auto-fill
const currentYear = new Date().getFullYear();
const acadYearInput = document.getElementById('acad_year');
if (acadYearInput) {
  acadYearInput.value = `${currentYear}-${currentYear + 1}`;
}

// Form submission handler
applicationForm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Final validation before submission
    let hasErrors = false;
    const requiredInputs = applicationForm.querySelectorAll("input[required], select[required]");

    // Validate all fields first
    requiredInputs.forEach(input => {
      if (input.type === "file") {
        validateFile(input);
      } else {
        validateInput(input);
      }

      if (input.id === 'email') {
        checkDuplicate(input);
      }

      if (input.classList.contains("error") || (input.type !== "file" && !input.value.trim()) || (input.type === "file" && input.files.length === 0)) {
        hasErrors = true;
      }
    });

    if (hasErrors) {
      Swal.fire({
        icon: "error",
        title: "Validation Error",
        text: "Please complete all required fields and fix any errors before submitting.",
      });
      return;
    }

    // Show confirmation dialog
    Swal.fire({
      title: "Are you sure?",
      text: "Do you want to submit your application?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#6d53d3",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, submit it!"
    }).then((result) => {
      if (result.isConfirmed) {
        // Disable UI and show spinner
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Submitting...';
        submitBtnSpinner.classList.remove('hidden');

        // Send AJAX request with form data (including files)
        const formData = new FormData(applicationForm);

        fetch(applicationForm.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          },
          credentials: 'same-origin'
        })
        .then(response => {
          // If server returns JSON (AJAX), parse it. If it returned a redirect (non-AJAX), the page will still handle it, but for AJAX we expect JSON.
          if (response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
              return response.json();
            }
            // If it returns html (redirect), treat as success
            return { success: true };
          } else {
            return response.json().catch(() => { throw new Error('Server error') });
          }
        })
        .then(data => {
          // Handle success or error response
          if (data.success) {
            // Success message
            Swal.fire({
              icon: 'success',
              title: 'Your application has been forwarded to the Mayors Office Staff.',
              text: 'Please wait for an email from the Mayor Staff where you will receive an intake sheet that you need to fill out. Note: Check your email regularly including spam folder.',
              confirmButtonColor: '#6d53d3'
            }).then(() => {
              // Redirect or perform any other action
              window.location.href = '/'; // Redirect to home or desired page
            });
          } else {
            // Error message (server-side validation errors)
            let errorMessage = 'Please fix the following errors:<ul>';
            for (const [key, value] of Object.entries(data.errors)) {
              errorMessage += `<li>${value.join(', ')}</li>`;
            }
            errorMessage += '</ul>';

            Swal.fire({
              icon: 'error',
              title: 'Submission Error',
              html: errorMessage,
              confirmButtonColor: '#6d53d3'
            });
          }
        })
        .catch(error => {
          console.error('Error submitting form:', error);
          Swal.fire({
            icon: 'error',
            title: 'Submission Error',
            text: 'An error occurred while submitting your application. Please try again later.',
            confirmButtonColor: '#6d53d3'
          });
        })
        .finally(() => {
          // Re-enable the submit button and hide spinner after completion
          submitBtn.disabled = false;
          submitBtnText.textContent = 'Submit';
          submitBtnSpinner.classList.add('hidden');
        });
      }
    });
});

// Success message
@if(session('success'))
Swal.fire({
  icon: 'success',
  title: 'You successfully submitted the Application',
  text: 'Please wait for an email from the Mayor Staff where you will receive an intake sheet that you need to fill out.',
  confirmButtonColor: '#6d53d3'
});
@endif

// Prevent form submission on Enter key press
applicationForm.addEventListener("keydown", function (e) {
  if (e.key === "Enter") {
    e.preventDefault();
  }
});

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
  initializeEventListeners();
  initializeDuplicateChecking();
  toggleButton(); // Set initial button state

  // Add this script to automatically capitalize first letters
  function capitalizeWords(str) {
      return str.replace(/\b\w/g, function(char) {
          return char.toUpperCase();
      });
  }

  // Function to handle input capitalization
  function handleInputCapitalization(event) {
      const input = event.target;
      const cursorPosition = input.selectionStart;
      
      // Only process if there's a value
      if (input.value) {
          // Capitalize the input value
          input.value = capitalizeWords(input.value);
          
          // Restore cursor position
          input.setSelectionRange(cursorPosition, cursorPosition);
      }
  }

  // Apply to all text inputs
  const textInputs = document.querySelectorAll('input[type="text"]');
  
  textInputs.forEach(input => {
      // Capitalize on blur (when user leaves the field)
      input.addEventListener('blur', handleInputCapitalization);
  });

  // Also apply to the course input field specifically
  const courseInput = document.getElementById('course');
  if (courseInput) {
      courseInput.addEventListener('blur', handleInputCapitalization);
  }
});

// Contact number validation - only numbers and max 12 digits
document.addEventListener('DOMContentLoaded', function() {
    const contactInput = document.getElementById('contact');
    
    if (contactInput) {
        // Prevent non-numeric input
        contactInput.addEventListener('input', function(e) {
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 12 digits
            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }
            
            // Validate the input
            validateInput(this);
        });
        
        // Prevent paste of non-numeric content
        contactInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericText = pastedText.replace(/[^0-9]/g, '');
            document.execCommand('insertText', false, numericText.slice(0, 12));
        });
    }
});
</script>
<style>
/* Full screen container */
.full-screen-container {
  width: 100%;
  min-height: calc(100vh - 100px);
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 20px;
  box-sizing: border-box;
}

/* Select2 customization */
.select2-container--default .select2-selection--single {
  border: 1px solid #d1d5db !important;
  border-radius: 0.5rem !important;
  height: 42px !important;
  padding: 8px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 40px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 26px !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #7c3aed !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
  border: 1px solid #d1d5db !important;
  border-radius: 0.375rem !important;
}

.select2-dropdown {
  border: 1px solid #d1d5db !important;
  border-radius: 0.5rem !important;
}

/* Error and valid states */
.error { 
  border-color: #ef4444 !important; 
}
.valid { 
  border-color: #10b981 !important; 
}
.error-message { 
  color: #ef4444; 
  font-size: 12px; 
  margin-top: 4px; 
  display: block;
}
</style>

</body>
</html>