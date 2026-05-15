<?php
require 'header.php';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Login - CodeMistry</title>
    <link rel="stylesheet" href="CSS/login-styles.css">
</head>
<body class="animated-bg">
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <main class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4 py-12 relative z-10">
        <div class="w-full max-w-md glass-card p-8 md:p-10 rounded-3xl animate-fadeIn">
            <!-- Header Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-2xl icon-bounce">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-4xl font-extrabold text-center mb-3 title-gradient">Client Portal</h1>
            <p class="text-center text-gray-400 mb-8">Welcome back! Login to access your dashboard</p>
            
            <!-- Email Form -->
            <form id="email-form" class="space-y-5">
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required 
                           class="w-full modern-input px-4 py-3.5 rounded-xl text-base">
                </div>
                <button type="submit" class="btn-primary btn-glow w-full text-white font-bold py-4 px-6 rounded-xl text-base relative z-10 transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Send OTP
                </button>
            </form>

            <!-- OTP Form (Hidden Initially) -->
            <form id="otp-form" class="hidden space-y-5">
                <div class="bg-blue-900/20 border border-blue-500/30 rounded-xl p-4 mb-4">
                    <p class="text-center text-blue-300 text-sm">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Enter the 6-digit code sent to your email
                    </p>
                </div>
                <input type="hidden" id="otp-email" name="email">
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Verification Code
                    </label>
                    <input type="text" id="otp" name="otp" placeholder="000000" required maxlength="6"
                           class="w-full modern-input px-4 py-3.5 rounded-xl text-center tracking-[0.5em] text-2xl font-bold">
                </div>
                <button type="submit" class="btn-success btn-glow w-full text-white font-bold py-4 px-6 rounded-xl text-base relative z-10 transition-all">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Verify & Login
                </button>
            </form>

            <div id="login-status" class="mt-6 text-center min-h-[24px]"></div>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-gray-900/50 text-gray-400">New to CodeMistry?</span>
                </div>
            </div>

            <p class="text-center">
                <a href="signup.php" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Create New Account
                </a>
            </p>
        </div>
    </main>
    
    <script>
        const emailForm = document.getElementById('email-form');
        const otpForm = document.getElementById('otp-form');
        const statusDiv = document.getElementById('login-status');

        emailForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = emailForm.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<svg class="spinner w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Sending...';
            submitBtn.disabled = true;
            statusDiv.innerHTML = '<p class="text-yellow-400 text-sm">📧 Sending OTP to your email...</p>';
            
            const formData = new FormData(emailForm);

            fetch('api.php?action=send_email_otp', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.innerHTML = '<p class="text-green-400 font-semibold text-sm">✅ ' + data.message + '</p>';
                    emailForm.classList.add('hidden');
                    otpForm.classList.remove('hidden');
                    document.getElementById('otp-email').value = formData.get('email');
                } else {
                    statusDiv.innerHTML = '<p class="text-red-400 font-semibold text-sm">❌ ' + data.message + '</p>';
                    submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>Send OTP';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                statusDiv.innerHTML = '<p class="text-red-400 font-semibold text-sm">❌ An error occurred. Please try again.</p>';
                submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>Send OTP';
                submitBtn.disabled = false;
            });
        });

        otpForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = otpForm.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<svg class="spinner w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Verifying...';
            submitBtn.disabled = true;
            statusDiv.innerHTML = '<p class="text-yellow-400 text-sm">🔍 Verifying your code...</p>';
            
            const formData = new FormData(otpForm);

            fetch('api.php?action=verify_email_otp', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.innerHTML = '<p class="text-green-400 font-semibold text-sm">✅ Success! Redirecting to dashboard...</p>';
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    statusDiv.innerHTML = '<p class="text-red-400 font-semibold text-sm">❌ ' + data.message + '</p>';
                    submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Verify & Login';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                statusDiv.innerHTML = '<p class="text-red-400 font-semibold text-sm">❌ An error occurred while verifying.</p>';
                submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Verify & Login';
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
