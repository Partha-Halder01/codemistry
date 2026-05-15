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
    <title>Sign Up - CodeMistry</title>
    <link rel="stylesheet" href="CSS/singup-styles.css">
</head>
<body class="animated-bg">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <main class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4 py-12 relative z-10">
        <div class="w-full max-w-md glass-card p-8 md:p-10 rounded-3xl animate-fadeIn">
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-2xl icon-bounce">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-4xl font-extrabold text-center mb-3 title-gradient">Create Account</h1>
            <p class="text-center text-gray-400 mb-8">Join CodeMistry and start your journey</p>
            
            <form id="signup-form" class="space-y-5">
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Full Name
                    </label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required 
                           class="w-full modern-input px-4 py-3.5 rounded-xl text-base">
                </div>

                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" placeholder="+91 98765 43210" required 
                           class="w-full modern-input px-4 py-3.5 rounded-xl text-base">
                </div>

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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Create Account
                </button>
            </form>

            <form id="otp-form" class="hidden space-y-5">
                <div class="bg-green-900/20 border border-green-500/30 rounded-xl p-4 mb-4">
                    <p class="text-center text-green-300 text-sm">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        We've sent a verification code to your email
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Verify & Continue
                </button>
            </form>

            <div id="status-message" class="mt-6 text-center min-h-[24px]"></div>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-gray-900/50 text-gray-400">Already a member?</span>
                </div>
            </div>

            <p class="text-center">
                <a href="login.php" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Login to Your Account
                </a>
            </p>
        </div>
    </main>

    <script>
        const signupForm = document.getElementById('signup-form');
        const otpForm = document.getElementById('otp-form');
        const statusDiv = document.getElementById('status-message');

        signupForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = signupForm.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<svg class="spinner w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating...';
            submitBtn.disabled = true;
            statusDiv.innerHTML = '<p class="text-yellow-400 text-sm">⏳ Creating your account...</p>';
            
            const formData = new FormData(signupForm);

            fetch('api.php?action=signup', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusDiv.innerHTML = '<p class="text-green-400 font-semibold text-sm">✅ ' + data.message + '</p>';
                    signupForm.classList.add('hidden');
                    otpForm.classList.remove('hidden');
                    document.getElementById('otp-email').value = formData.get('email');
                } else {
                    statusDiv.innerHTML = '<p class="text-red-400 font-semibold text-sm">❌ ' + data.message + '</p>';
                    submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Create Account';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                statusDiv.innerHTML = '<p class="text-red-400 font-semibold text-sm">❌ An error occurred. Please try again.</p>';
                submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Create Account';
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
                    statusDiv.innerHTML = '<p class="text-green-400 font-semibold text-sm">✅ Success! Redirecting to your dashboard...</p>';
                    setTimeout(() => {
                        window.location.href = 'dashboard.php?new=true';
                    }, 1000);
                } else {
                    statusDiv.innerHTML = '<p class="text-red-400 font-semibold text-sm">❌ ' + data.message + '</p>';
                    submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Verify & Continue';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                statusDiv.innerHTML = '<p class="text-red-400 font-semibold text-sm">❌ An error occurred while verifying.</p>';
                submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Verify & Continue';
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
