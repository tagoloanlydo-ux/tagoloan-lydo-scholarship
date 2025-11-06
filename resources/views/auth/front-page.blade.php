<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LYDO Scholarship Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a1e78;
            --secondary-color: #6a4fd4;
            --accent-color: #7a58f7;
            --text-color: #ffffff;
            --text-secondary: #d1d5db;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, var(--accent-color) 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Enhanced transition overlay */
        .transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 50%, var(--accent-color) 100%);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        
        .transition-overlay.fade-out {
            opacity: 0;
            transform: scale(1.1);
            pointer-events: none;
        }
        
        /* Enhanced animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .fade-in-scale {
            animation: fadeInScale 0.8s ease-out forwards;
        }
        
        .fade-delay-1 {
            animation-delay: 0.2s;
        }
        
        .fade-delay-2 {
            animation-delay: 0.4s;
        }
        
        .fade-delay-3 {
            animation-delay: 0.6s;
        }
        
        .fade-delay-4 {
            animation-delay: 0.8s;
        }
        
        .fade-delay-5 {
            animation-delay: 1s;
        }
        
        /* Button enhancements */
        .action-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .action-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        
        .action-btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }
        
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        /* Logo animation */
        .logo {
            animation: fadeInScale 1s ease-out, pulse 3s infinite 2s;
        }
        
        /* Responsive text sizing */
        .responsive-text {
            font-size: clamp(1.5rem, 4vw, 3rem);
        }
        
        .responsive-subtext {
            font-size: clamp(0.875rem, 2.5vw, 1.125rem);
        }
        
        /* Background animation */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .bg-bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite linear;
        }
        
        @keyframes float {
            0% {
                transform: translateY(100vh) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.5;
            }
            90% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-100px) translateX(100px) rotate(360deg);
                opacity: 0;
            }
        }
        
        /* Mobile-first responsive adjustments */
        @media (max-width: 640px) {
            .button-container {
                width: 90%;
                max-width: 20rem;
            }
            
            .action-btn {
                padding: 0.75rem 1rem;
            }
        }
        
        @media (min-width: 1024px) {
            .button-container {
                max-width: 28rem;
            }
        }
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
<body class="min-h-screen w-full flex flex-col items-center justify-between text-white">
    <!-- Background Animation -->
    <div class="bg-animation" id="bgAnimation"></div>

    <!-- Transition Overlay -->
    <div id="overlay" class="transition-overlay flex items-center justify-center px-4 text-center">
        <div class="text-center fade-in-scale">
            <img src="/images/LYDO.png" alt="LYDO Logo" class="h-16 md:h-24 mx-auto mb-4 logo" />
            <h1 class="responsive-text font-bold text-white">
                LYDO Scholarship Management
            </h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col items-center justify-center flex-grow w-full px-4 py-8">
        <!-- Hero Section -->
        <div class="flex flex-col items-center text-center max-w-4xl w-full">
            <img src="/images/LYDO.png" alt="LYDO Logo" class="h-16 md:h-24 mb-4 fade-in-scale" />
            
            <h1 class="responsive-text font-bold fade-in-up fade-delay-1 opacity-0 mb-2">
                LYDO Scholarship Portal
            </h1>

            <p class="mt-3 responsive-subtext text-gray-200 max-w-md md:max-w-2xl fade-in-up fade-delay-2 opacity-0 leading-relaxed">
                Welcome scholars! 🎓 Your journey to success starts here. Together,
                let's empower education and build brighter futures.
            </p>
        </div>

        <!-- Buttons -->
        <div class="button-container flex flex-col gap-4 w-full mt-8 md:mt-12 fade-in-up fade-delay-3 opacity-0 px-4">
            <button
                class="action-btn flex items-center justify-center gap-3 bg-blue-700 hover:bg-blue-600 py-4 rounded-xl shadow-lg text-white font-semibold transition-all text-base md:text-lg"
                onclick="window.location='{{ route('scholar.login') }}'">
                <i class="fa-solid fa-right-to-bracket"></i> Log In Scholar
            </button>

            <button
                class="action-btn flex items-center justify-center gap-3 bg-green-700 hover:bg-green-600 py-4 rounded-xl shadow-lg text-white font-semibold transition-all text-base md:text-lg"
                onclick="window.location='{{ route('login') }}'">
                <i class="fa-solid fa-right-to-bracket"></i> Log In Lydo Personnel
            </button>
            
            <!-- Application Button with Status Logic -->
            @php
                $settings = \App\Models\Settings::first();
                $currentDate = now()->toDateString();
                $applicationDisabled = false;
                $applicationMessage = '';

                if ($settings && $settings->application_start_date && $settings->application_deadline) {
                    $startDate = $settings->application_start_date->toDateString();
                    $deadline = $settings->application_deadline->toDateString();

                    if ($currentDate < $startDate) {
                        $applicationDisabled = true;
                        $applicationMessage = 'Application period starts on ' . $settings->application_start_date->format('M d, Y');
                    } elseif ($currentDate > $deadline) {
                        $applicationDisabled = true;
                        $applicationMessage = 'Application period has ended on ' . $settings->application_deadline->format('M d, Y');
                    }
                } elseif ($settings && (!$settings->application_start_date || !$settings->application_deadline)) {
                    $applicationDisabled = true;
                    $applicationMessage = 'Application period not yet set by administrator';
                }
            @endphp

            @if($applicationDisabled)
                <div class="flex flex-col">
                    <button disabled class="flex items-center justify-center gap-3 bg-gray-600 py-4 w-full rounded-xl shadow text-white font-semibold transition text-base md:text-lg" title="{{ $applicationMessage }}">
                        <i class="fa-solid fa-file-circle-question"></i> Apply as Scholar
                    </button>
                    <p class="text-sm text-gray-300 mt-2 text-center">{{ $applicationMessage }}</p>
                </div>
            @else
                <a href="{{ route('applicants.registration') }}" class="w-full">
                    <button class="action-btn flex w-full items-center justify-center gap-3 bg-red-700 hover:bg-red-600 py-4 rounded-xl shadow-lg text-white font-semibold transition-all text-base md:text-lg">
                        <i class="fa-solid fa-file-pen"></i> Apply as Scholar
                    </button>
                </a>
            @endif

            <a href="{{ route('scholar.announcements') }}" class="w-full">
                <button class="action-btn flex items-center w-full justify-center gap-3 bg-yellow-700 hover:bg-yellow-600 py-4 rounded-xl shadow-lg text-white font-semibold transition-all text-base md:text-lg">
                    <i class="fa-solid fa-bullhorn"></i> View Announcements
                </button>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="mb-6 md:mb-8 text-sm md:text-base text-gray-200 text-center fade-in-up fade-delay-4 opacity-0 px-4">
        <p>© 2025 LYDO Scholar</p>
        <p class="text-gray-300 mt-1">Empowering education through opportunity</p>
    </div>

    <!-- JavaScript -->
    <script>
        // Create background bubbles
        function createBubbles() {
            const bgAnimation = document.getElementById('bgAnimation');
            const bubbleCount = Math.min(Math.floor(window.innerWidth / 50), 30); // Adjust based on screen size
            
            for (let i = 0; i < bubbleCount; i++) {
                const bubble = document.createElement('div');
                bubble.classList.add('bg-bubble');
                
                // Random size between 20px and 100px
                const size = Math.random() * 80 + 20;
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                
                // Random position
                bubble.style.left = `${Math.random() * 100}%`;
                
                // Random animation delay and duration
                const delay = Math.random() * 15;
                const duration = Math.random() * 10 + 15;
                bubble.style.animationDelay = `${delay}s`;
                bubble.style.animationDuration = `${duration}s`;
                
                bgAnimation.appendChild(bubble);
            }
        }
        
        // Initialize page
        window.addEventListener("load", () => {
            // Create background animation
            createBubbles();
            
            // Handle overlay transition
            const overlay = document.getElementById("overlay");
            setTimeout(() => {
                overlay.classList.add("fade-out");
                setTimeout(() => {
                    overlay.style.display = "none";
                }, 800);
            }, 1500);
            
            // Add focus styles for accessibility
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('focus', function() {
                    this.classList.add('ring-2', 'ring-white', 'ring-opacity-50');
                });
                
                button.addEventListener('blur', function() {
                    this.classList.remove('ring-2', 'ring-white', 'ring-opacity-50');
                });
            });
        });
        
        // Adjust bubbles on resize
        window.addEventListener('resize', function() {
            const bgAnimation = document.getElementById('bgAnimation');
            bgAnimation.innerHTML = '';
            createBubbles();
        });
    </script>
    <script src="{{ asset('js/spinner.js') }}"></script>

</body>
</html>