<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading Spinner</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
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
            opacity: 1;
            animation: fadeIn 1s ease forwards;
        }

        .loading-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }

        .spinner {
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: 0 auto 2rem;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner img {
            width: 80%;
            height: 100%;
            border-radius: 50%;
        }

        .text-line {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 1s ease forwards 0.5s both;
            color: white;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
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

        /* Responsive design for spinner */
        @media (max-width: 768px) {
            .text-line {
                font-size: 1.8rem;
            }
            .spinner {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 480px) {
            .text-line {
                font-size: 1.5rem;
            }
            .spinner {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-container">
            <div class="spinner">
                <img src="LYDO.png" alt="Loading Logo">
            </div>
            <div class="text-line">Loading...</div>
        </div>
    </div>

    <script>
        // Simulate loading and hide spinner after a delay
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const overlay = document.getElementById('loadingOverlay');
                overlay.classList.add('fade-out');
                setTimeout(function() {
                    overlay.style.display = 'none';
                }, 1000); // Match the fadeOut animation duration
                // In a real scenario, replace with actual content loading logic
            }, 3000); // Adjust delay as needed
        });
    </script>
</body>
</html>
