<?php 
require 'config.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - CodeMistry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/checkout-styles.css">

</head>
<body class="animated-bg text-gray-200 relative overflow-x-hidden">
    <!-- Floating Background Icons -->
    <div class="floating-icon">
        <svg class="w-32 h-32 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
    </div>
    <div class="floating-icon">
        <svg class="w-40 h-40 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"></path></svg>
    </div>
    <div class="floating-icon">
        <svg class="w-36 h-36 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
    </div>

    <header class="bg-gray-900/80 header-blur sticky top-0 z-40 border-b border-gray-800/50 shadow-xl">
        <nav class="container mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                Code<span class="text-blue-500">Mistry</span>
            </a>
            <div class="flex items-center gap-2 text-gray-300 pulse-animation">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span class="text-xs sm:text-sm font-semibold">Secure Checkout</span>
            </div>
        </nav>
    </header>

    <main id="main-content" class="container mx-auto px-4 py-8 md:py-16 opacity-0 transition-opacity duration-500 relative z-10">
        <div class="text-center py-40">
            <div class="inline-block">
                <svg class="animate-spin h-12 w-12 text-blue-500 mx-auto" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-400 mt-4 text-lg">Loading checkout...</p>
            </div>
        </div>
    </main>

    <div id="status-message-area" class="fixed bottom-6 right-6 z-50 space-y-2"></div>

    <script>
        let serviceDetails = {};
        let selectedPlan = 'deposit';
        let originalAmount = 0;
        let finalAmount = 0;
        let discountPercent = 0;
        let appliedCoupon = null;
        let payButton; 
        let termsCheckbox;

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const serviceId = urlParams.get('service_id');
            selectedPlan = urlParams.get('plan') || 'deposit';

            if (!serviceId) {
                displayError("Service ID is missing. Please go back and select a service.");
                return;
            }

            fetch(`api.php?action=get_service_detail&id=${serviceId}`)
                .then(res => res.ok ? res.json() : Promise.reject(res))
                .then(data => {
                    if (data.success && data.service) {
                        serviceDetails = data.service;
                        originalAmount = selectedPlan === 'full' ? parseInt(serviceDetails.full_price) : parseInt(serviceDetails.deposit_price);
                        finalAmount = originalAmount;
                        renderCheckoutPage();
                    } else {
                        displayError(data.message || "Could not load service details. Please try again.");
                    }
                })
                .catch(err => {
                     console.error("Fetch Error:", err);
                     displayError("An error occurred while loading service details.");
                });
        });
        
        function renderCheckoutPage() {
            const main = document.getElementById('main-content');
            main.innerHTML = `
                <div class="max-w-7xl mx-auto">
                    <!-- Progress Steps - Mobile Optimized -->
                    <div class="mb-8 sm:mb-12 animate-fadeIn">
                        <div class="flex items-center justify-between px-2 sm:justify-center sm:gap-4 md:gap-8">
                            <div class="flex items-center gap-1 sm:gap-2">
                                <div class="progress-step-circle w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-lg text-sm">1</div>
                                <span class="progress-step-text text-xs sm:text-sm md:text-base font-semibold text-white">Information</span>
                            </div>
                            <div class="progress-line w-6 sm:w-12 md:w-24 h-1 bg-gray-700 rounded"></div>
                            <div class="flex items-center gap-1 sm:gap-2">
                                <div class="progress-step-circle w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 font-bold text-sm">2</div>
                                <span class="progress-step-text text-xs sm:text-sm md:text-base font-semibold text-gray-400">Payment</span>
                            </div>
                            <div class="progress-line w-6 sm:w-12 md:w-24 h-1 bg-gray-700 rounded"></div>
                            <div class="flex items-center gap-1 sm:gap-2">
                                <div class="progress-step-circle w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 font-bold text-sm">3</div>
                                <span class="progress-step-text text-xs sm:text-sm md:text-base font-semibold text-gray-400">Complete</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                        <!-- Left Column - Form -->
                        <div class="lg:order-1 space-y-8 animate-fadeIn" style="animation-delay: 0.1s;">
                            <div>
                                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-2 bg-gradient-to-r from-white to-blue-400 bg-clip-text text-transparent">Contact Information</h1>
                                <p class="text-gray-400 text-sm">We'll use this to create your account and send receipts.</p>
                            </div>
                            
                            <form id="checkout-form" class="space-y-5">
                                <div class="relative">
                                    <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Full Name <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" id="name" required placeholder="Enter your full name" class="w-full px-4 py-3.5 rounded-xl form-input text-base">
                                </div>
                                
                                <div class="relative">
                                    <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Email Address <span class="text-red-400">*</span>
                                    </label>
                                    <input type="email" id="email" required placeholder="you@example.com" class="w-full px-4 py-3.5 rounded-xl form-input text-base">
                                </div>
                                
                                <div class="relative">
                                    <label for="phone" class="block text-sm font-semibold text-gray-300 mb-2">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        Phone Number <span class="text-red-400">*</span>
                                    </label>
                                    <input type="tel" id="phone" pattern="[0-9]{10,15}" title="Enter a valid phone number (10-15 digits)" required placeholder="+91 98765 43210" class="w-full px-4 py-3.5 rounded-xl form-input text-base">
                                </div>
                            </form>
                            
                            <!-- Coupon Section - Mobile Optimized -->
                            <div class="bg-gradient-to-br from-purple-900/20 to-blue-900/20 p-4 sm:p-6 rounded-2xl border border-purple-500/30">
                                <h2 class="text-lg sm:text-xl font-bold text-white mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    Have a Coupon?
                                </h2>
                                <div class="flex gap-2 sm:gap-3">
                                    <input type="text" id="coupon-code" placeholder="DISCOUNT50" class="flex-1 px-3 sm:px-4 py-3 rounded-xl form-input uppercase tracking-wider font-mono text-sm sm:text-base">
                                    <button id="apply-coupon-btn" class="coupon-apply-btn btn-secondary btn-glow text-white font-bold px-3 sm:px-8 py-3 rounded-xl whitespace-nowrap transition-all relative z-10 text-sm sm:text-base">Apply</button>
                                </div>
                                <div id="coupon-status" class="text-sm mt-3 min-h-[20px]"></div>
                            </div>
                            
                            <!-- Terms & Complete Purchase -->
                            <div class="space-y-5">
                                <div class="flex items-start bg-gray-800/50 p-4 sm:p-5 rounded-xl border border-gray-700">
                                    <div class="flex items-center h-5">
                                        <input id="terms-conditions" type="checkbox" class="w-5 h-5 rounded-lg bg-gray-700 border-2 border-gray-600 text-blue-600 focus:ring-2 focus:ring-blue-500 custom-checkbox transition-all">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms-conditions" class="text-gray-300">
                                            I have read and agree to the <a href="terms.php" target="_blank" class="text-blue-400 hover:text-blue-300 underline font-semibold">Terms & Conditions</a> <span class="text-red-400">*</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <button id="pay-button" class="btn-primary btn-glow w-full text-white font-extrabold py-4 sm:py-5 rounded-xl text-base sm:text-lg disabled:bg-gray-500 disabled:cursor-not-allowed transition-all relative z-10 shadow-2xl" disabled>
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Complete Purchase
                                </button>
                                
                                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 text-xs text-gray-400">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        <span>SSL Encrypted</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        <span>Secure Payment</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Order Summary -->
                        <div class="lg:order-2 animate-fadeIn" style="animation-delay: 0.2s;">
                            <div class="lg:sticky lg:top-24">
                                <h1 class="text-xl sm:text-2xl font-extrabold text-white mb-6">Order Summary</h1>
                                <div class="order-card p-6 md:p-8 rounded-2xl space-y-6 shadow-2xl">
                                    <!-- Service Info -->
                                    <div class="flex items-start gap-4 sm:gap-5 pb-6 border-b border-gray-700/50">
                                        <div class="service-image flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-gray-700 shadow-lg">
                                            <img src="${serviceDetails.cover_image_path}" alt="${serviceDetails.name}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-white text-base sm:text-lg mb-2 break-words leading-tight">${serviceDetails.name}</h3>
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1 bg-blue-600/20 text-blue-400 text-xs font-semibold px-3 py-1 rounded-full border border-blue-500/30">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                    <span class="capitalize">${selectedPlan} Plan</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Price Breakdown -->
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center text-gray-300">
                                            <p class="font-medium">Subtotal</p>
                                            <p class="font-bold text-lg" id="summary-price">₹${originalAmount.toLocaleString('en-IN')}</p>
                                        </div>
                                        
                                        <div id="discount-row" class="hidden justify-between items-center">
                                            <p class="font-medium text-gray-300">Discount (<span id="discount-code" class="font-mono text-sm text-green-400"></span>)</p>
                                            <p class="text-green-400 font-bold text-lg" id="discount-amount"></p>
                                        </div>
                                        
                                        <div class="pt-5 border-t-2 border-gray-700/50">
                                            <div class="flex justify-between items-center">
                                                <p class="text-lg sm:text-xl font-extrabold text-white">Total Due Now</p>
                                                <p class="text-2xl sm:text-3xl font-extrabold price-gradient" id="final-price">₹${finalAmount.toLocaleString('en-IN')}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Trust Badges -->
                                    <div class="pt-6 border-t border-gray-700/50 space-y-3">
                                        <div class="flex items-center gap-3 text-sm text-gray-400">
                                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>100% Secure & Encrypted Payment</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-gray-400">
                                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Instant Order Confirmation</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm text-gray-400">
                                            <svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                            <span>Dedicated Support Team</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            
            requestAnimationFrame(() => {
                 main.classList.remove('opacity-0');
            });
           
            initializeCheckoutActions();
        }

        function displayError(message) {
             const main = document.getElementById('main-content');
             main.innerHTML = `
                <div class="text-center py-40">
                    <div class="inline-block p-8 bg-red-900/20 border-2 border-red-500/50 rounded-2xl">
                        <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h1 class="text-2xl text-red-400 font-bold mb-4">${message}</h1>
                        <a href="services.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all">Go Back to Services</a>
                    </div>
                </div>`;
             requestAnimationFrame(() => {
                 main.classList.remove('opacity-0');
             });
        }

        function updatePriceSummary() {
            const discountAmount = Math.round(originalAmount * (discountPercent / 100));
            finalAmount = Math.max(0, originalAmount - discountAmount); 
            
            document.getElementById('summary-price').textContent = `₹${originalAmount.toLocaleString('en-IN')}`;
            const discountRow = document.getElementById('discount-row');
            if(discountPercent > 0 && discountRow) {
                discountRow.classList.remove('hidden');
                discountRow.classList.add('flex');
                document.getElementById('discount-code').textContent = appliedCoupon;
                document.getElementById('discount-amount').textContent = `- ₹${discountAmount.toLocaleString('en-IN')}`;
            } else if (discountRow) {
                 discountRow.classList.add('hidden');
                 discountRow.classList.remove('flex');
            }
            document.getElementById('final-price').textContent = `₹${finalAmount.toLocaleString('en-IN')}`;
            checkFormValidity(); 
        }
        
        function checkFormValidity() {
            if (payButton && termsCheckbox) {
                const form = document.getElementById('checkout-form');
                payButton.disabled = !termsCheckbox.checked || (finalAmount > 0 && !form.checkValidity());
            }
        }

        function initializeCheckoutActions() {
            payButton = document.getElementById('pay-button'); 
            termsCheckbox = document.getElementById('terms-conditions');
            const form = document.getElementById('checkout-form');
            
            form.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', checkFormValidity);
            });
            termsCheckbox.addEventListener('change', checkFormValidity);

            checkFormValidity();

            const couponBtn = document.getElementById('apply-coupon-btn');
            const couponInput = document.getElementById('coupon-code');
            const couponStatus = document.getElementById('coupon-status');

            couponBtn.addEventListener('click', () => {
                const code = couponInput.value.trim();
                if (!code) {
                     couponStatus.innerHTML = '<p class="text-yellow-400"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>Please enter a code.</p>';
                     return;
                };
                
                couponBtn.disabled = true;
                couponBtn.textContent = 'Applying...';
                couponStatus.innerHTML = '<p class="text-yellow-400">Applying coupon...</p>';
                fetch('api.php?action=apply_coupon', {
                    method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ code: code })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        discountPercent = data.discount_percentage;
                        appliedCoupon = code.toUpperCase();
                        couponStatus.innerHTML = `<p class="text-green-400 font-semibold"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Success! ${discountPercent}% discount applied.</p>`;
                    } else {
                        discountPercent = 0;
                        appliedCoupon = null;
                        couponStatus.innerHTML = `<p class="text-red-400"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>${data.message}</p>`;
                    }
                    updatePriceSummary();
                })
                .catch(err => {
                    console.error("Coupon Apply Error:", err);
                    discountPercent = 0;
                    appliedCoupon = null;
                    couponStatus.innerHTML = '<p class="text-red-400"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Could not apply coupon. Try again.</p>';
                    updatePriceSummary();
                })
                .finally(() => {
                    couponBtn.disabled = false;
                    couponBtn.textContent = 'Apply';
                });
            });

            payButton.addEventListener('click', () => {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                 if (!termsCheckbox.checked) {
                    showStatusMessage("You must agree to the Terms & Conditions.", "error");
                    return;
                }
                
                payButton.innerHTML = '<svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
                payButton.disabled = true;

                const orderData = {
                    name: document.getElementById('name').value.trim(),
                    phone: document.getElementById('phone').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    service_id: parseInt(serviceDetails.id),
                    plan: selectedPlan,
                    amount: Math.round(finalAmount * 100),
                    coupon_code: appliedCoupon,
                    discount_percentage: discountPercent,
                    razorpay_payment_id: null
                };
                console.log("Order Data prepared:", orderData);

                const handleOrderCreation = (orderPayload) => {
                     showStatusMessage("Payment successful! Creating your order...", "info");
                     console.log("Sending to create_order:", orderPayload);
                     fetch('api.php?action=create_order', { 
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json' }, 
                        body: JSON.stringify(orderPayload) 
                    })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(({ status, body }) => {
                        console.log("create_order response:", status, body);
                        if (body.success) {
                            showStatusMessage("Order created successfully! Redirecting...", "success");
                            window.location.href = body.redirect || 'dashboard.html?new=true'; 
                        } else {
                            showStatusMessage(`Order Creation Failed: ${body.message || 'Unknown error'}. Please contact support with Payment ID: ${orderPayload.razorpay_payment_id || 'N/A'}`, 'error', 10000);
                            payButton.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Order Failed - Contact Support'; 
                        }
                    })
                    .catch(err => {
                        console.error("Network error during order creation:", err);
                        showStatusMessage(`Network Error after payment: Order might not be created. Please contact support with Payment ID: ${orderPayload.razorpay_payment_id || 'N/A'}`, 'error', 10000);
                        payButton.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Order Failed - Contact Support';
                    });
                };

                if (finalAmount <= 0) {
                    orderData.razorpay_payment_id = 'FREE_ORDER';
                    console.log("Processing free order...");
                    handleOrderCreation(orderData);
                } else {
                    console.log("Initiating Razorpay payment...");
                    const options = {
                        key: "<?php echo RAZORPAY_KEY_ID; ?>",
                        amount: Math.round(finalAmount * 100),
                        currency: "INR",
                        name: "CodeMistry",
                        description: `Order for ${serviceDetails.name} (${selectedPlan} plan)`,
                        image: "https://i.imgur.com/g237s54.png",
                        handler: function (response) {
                             console.log("Razorpay success response:", response);
                            orderData.razorpay_payment_id = response.razorpay_payment_id;
                            handleOrderCreation(orderData); 
                        },
                        prefill: { 
                            name: orderData.name, 
                            email: orderData.email, 
                            contact: orderData.phone 
                        },
                        theme: { color: "#3b82f6" },
                        modal: { 
                            ondismiss: function() { 
                                console.log("Razorpay modal dismissed.");
                                showStatusMessage("Payment cancelled.", "info", 3000);
                                payButton.innerHTML = '<svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>Complete Purchase';
                                payButton.disabled = false;
                            } 
                        }
                    };
                    
                    try {
                        const rzp = new Razorpay(options);
                         rzp.on('payment.failed', function (response){
                                console.error("Razorpay payment failed:", response);
                                let errorMsg = `Payment Failed`;
                                if (response.error) {
                                     errorMsg += `: ${response.error.description || response.error.reason || 'Unknown Razorpay error'}.`;
                                     if(response.error.metadata) {
                                         orderData.razorpay_payment_id = response.error.metadata.payment_id || null;
                                     }
                                }
                                showStatusMessage(errorMsg + " Please try again or contact support.", 'error', 8000);
                                payButton.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Payment Failed - Retry'; 
                                payButton.disabled = false;
                         });
                        rzp.open();
                    } catch (error) {
                         console.error("Error opening Razorpay checkout:", error);
                         showStatusMessage("Could not initialize payment gateway. Check console for details.", "error");
                         payButton.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Error - Retry Payment'; 
                         payButton.disabled = false; 
                    }
                }
            });
        }

        let statusTimeout;
        function showStatusMessage(message, type = 'info', duration = 5000) {
            const area = document.getElementById('status-message-area');
            if (!area) return;
            
            const messageDiv = document.createElement('div');
            messageDiv.textContent = message;
            messageDiv.className = 'px-6 py-4 rounded-xl shadow-2xl text-sm font-semibold text-white transform transition-all duration-300 '; 
            messageDiv.style.opacity = '0';
            messageDiv.style.transform = 'translateX(100%)';

            if (type === 'success') { 
                messageDiv.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-500');
                messageDiv.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${message}`;
            } 
            else if (type === 'error') { 
                messageDiv.classList.add('bg-gradient-to-r', 'from-red-600', 'to-red-500');
                messageDiv.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>${message}`;
            } 
            else { 
                messageDiv.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-500');
                messageDiv.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${message}`;
            }
            
            area.appendChild(messageDiv);
            
            requestAnimationFrame(() => { 
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateX(0)';
            });

            if (statusTimeout) clearTimeout(statusTimeout);

            statusTimeout = setTimeout(() => {
                messageDiv.style.opacity = '0';
                messageDiv.style.transform = 'translateX(100%)';
                setTimeout(() => { if (messageDiv.parentNode) messageDiv.remove(); }, 300);
            }, duration);
        }
    </script>
</body>
</html>