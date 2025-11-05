<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>LYDO Scholarship Announcements</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/scholar.css') }}" />
    <link rel="icon" type="image/png" href="{{ asset('/images/LYDO.png') }}">
      <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
            transition: opacity 0.3s ease;
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
    <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner">
      <img src="{{ asset('images/LYDO.png') }}" alt="Loading..." />
    </div>
  </div>
  <body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="banner-grad flex items-center px-6 text-white shadow-md">
         <img src="/images/LYDO.png" alt="LYDO Logo" class="h-10 mr-4"/>
      <div>
        <h1 class="text-3xl font-extrabold">LYDO SCHOLARSHIP</h1>
        <p class="text-sm tracking-widest">
          PARA SA KABATAAN, PARA SA KINABUKASAN.
        </p>
      </div>
    </header>

    <!-- MAIN ANNOUNCEMENT SECTION -->
    <main class="flex flex-1 flex-col md:flex-row items-center justify-center px-6 py-10 gap-12 flex-nowrap">
      <!-- LEFT SIDE -->
      <div class="flex flex-col items-center text-center md:text-left md:items-start max-w-lg min-w-0 md:min-w-[400px]">
        <h2 class="text-5xl font-extrabold mb-4 text-purple-700 leading-tight">
          Latest Announcements
        </h2>
        <p class="text-xl leading-relaxed text-gray-700 mb-4">
          Stay updated with the latest scholarship announcements, requirements, and important information.
        </p>
        <button onclick="window.location='{{ route('home') }}'" class="flex items-center gap-2 text-purple-600 hover:text-purple-800 font-semibold mt-4">
          <i class="fa-solid fa-arrow-left"></i> ← Back to portal
        </button>
      </div>

      <!-- RIGHT SIDE (ANNOUNCEMENT LIST) -->
      <div class="w-full max-w-2xl space-y-6 ">
        <div class="bg-white rounded-lg shadow-lg p-2 max-h-[400px] overflow-y-auto">
          <h3 class="text-2xl font-bold text-purple-700 mb-1 text-center">Recent Announcements</h3>

          @forelse($announcements as $announcement)
            <div class="bg-purple-50 border-l-4 border-purple-600 p-4 rounded-lg mb-4 hover:bg-purple-100 transition-colors">
              <div class="flex justify-between items-start mb-2">
                <h4 class="text-lg font-semibold text-purple-800">{{ $announcement->announce_title }}</h4>
                <span class="text-sm text-gray-500">{{ $announcement->created_at->format('M d, Y') }}</span>
              </div>

              <div class="announcement-content">
                <p class="text-gray-700 mb-2">
                  <span class="short-content">{{ Str::limit(strip_tags($announcement->announce_content), 150) }}</span>
                  @if(strlen(strip_tags($announcement->announce_content)) > 150)
                    <span class="full-content" style="display:none;">{!! nl2br(e($announcement->announce_content)) !!}</span>
                    <button onclick="toggleContent(this)" class="text-purple-600 hover:text-purple-800 text-sm font-medium ml-2">
                      Read More
                    </button>
                  @endif
                </p>
              </div>
            </div>
          @empty
            <div class="text-center py-8">
              <p class="text-gray-500 text-lg">No announcements available at the moment.</p>
              <p class="text-gray-400 text-sm mt-2">Please check back later for updates.</p>
            </div>
          @endforelse
        </div>


      </div>
    </main>

    <!-- FOOTER -->
    <footer class="text-center py-4 text-sm text-gray-500">
      © 2025 LYDO Scholarship. All rights reserved.
    </footer>

    <script>
      function toggleContent(button) {
        const contentDiv = button.parentElement;
        const short = contentDiv.querySelector('.short-content');
        const full = contentDiv.querySelector('.full-content');

        if (full.style.display === 'none') {
          full.style.display = 'inline';
          short.style.display = 'none';
          button.textContent = 'Read Less';
        } else {
          full.style.display = 'none';
          short.style.display = 'inline';
          button.textContent = 'Read More';
        }
      }

      // SweetAlert notifications
      @if (session('success'))
        Swal.fire({
          title: 'Success!',
          text: "{{ session('success') }}",
          icon: 'success',
          confirmButtonText: 'OK'
        });
      @endif

      @if (session('error'))
        Swal.fire({
          title: 'Error!',
          text: "{{ session('error') }}",
          icon: 'error',
          confirmButtonText: 'Try Again'
        });
      @endif
    </script>
    <script src="{{ asset('js/spinner.js') }}"></script>
  </body>
</html>
