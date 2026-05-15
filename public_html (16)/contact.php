<?php require 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raise a Ticket - CodeMistry</title>
    <link rel="stylesheet" href="CSS/contact-styles.css">
</head>
<body class="animated-bg">
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <main class="container mx-auto px-4 md:px-6 py-16 md:py-20 relative z-10">
        <!-- Header Section -->
        <div class="text-center mb-12 animate-fadeIn">
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-2xl icon-bounce">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold title-gradient mb-4">Get In Touch</h1>
            <p class="text-lg md:text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Have a question or a project in mind? Raise a ticket, and we'll get back to you within 24 hours.
            </p>
        </div>

        <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Contact Info Cards -->
            <div class="lg:col-span-1 space-y-4 animate-fadeIn" style="animation-delay: 0.1s;">
                <div class="contact-card">
                    <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Email Us</h3>
                    <p class="text-gray-400 text-sm mb-2">Send us an email anytime</p>
                    <a href="mailto:support@codemistry.com" class="text-blue-400 hover:text-blue-300 text-sm font-semibold">support@codemistry.com</a>
                </div>

                <div class="contact-card">
                    <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Call Us</h3>
                    <p class="text-gray-400 text-sm mb-2">Mon-Fri from 9am to 6pm</p>
                    <a href="tel:+918910710136" class="text-green-400 hover:text-green-300 text-sm font-semibold">+91 89107 10136</a>
                </div>

                <div class="contact-card">
                    <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Response Time</h3>
                    <p class="text-gray-400 text-sm mb-2">We typically respond within</p>
                    <span class="text-purple-400 text-sm font-semibold">24 Hours</span>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2 animate-fadeIn" style="animation-delay: 0.2s;">
                <form id="contact-form" class="glass-card rounded-2xl p-6 md:p-8 space-y-6">
                    <div>
                        <label for="name" class="form-label">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Your Name <span class="text-red-400">*</span></span>
                        </label>
                        <input type="text" id="name" name="name" required placeholder="John Doe"
                               class="modern-input w-full rounded-xl px-4 py-3.5 text-base">
                    </div>

                    <div>
                        <label for="phone" class="form-label">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>Phone Number <span class="text-red-400">*</span></span>
                        </label>
                        <input type="tel" id="phone" name="phone" required placeholder="+91 98765 43210"
                               class="modern-input w-full rounded-xl px-4 py-3.5 text-base">
                    </div>

                    <div>
                        <label for="email" class="form-label">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>Your Email (Optional)</span>
                        </label>
                        <input type="email" id="email" name="email" placeholder="you@example.com"
                               class="modern-input w-full rounded-xl px-4 py-3.5 text-base">
                        <p class="text-xs text-gray-500 mt-2">You'll receive a confirmation email</p>
                    </div>

                    <div>
                        <label for="message" class="form-label">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span>Your Query <span class="text-red-400">*</span></span>
                        </label>
                        <textarea id="message" name="message" rows="5" required placeholder="What can we help you with?"
                                  class="modern-input w-full rounded-xl px-4 py-3.5 text-base resize-none"></textarea>
                    </div>

                    <button id="submit-button" type="submit" 
                            class="btn-primary btn-glow w-full text-white font-bold py-4 px-6 rounded-xl text-base relative z-10 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Submit Ticket</span>
                    </button>
                </form>

                <div id="form-status-message" class="hidden mt-6 px-6 py-4 rounded-xl text-center font-semibold"></div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contactForm = document.getElementById('contact-form');
            const statusMessage = document.getElementById('form-status-message');
            const submitButton = document.getElementById('submit-button');
            const emailInput = document.getElementById('email');

            contactForm.addEventListener('submit', (e) => {
                e.preventDefault();
                submitButton.disabled = true;
                submitButton.innerHTML = '<svg class="spinner w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Submitting...</span>';
                statusMessage.classList.add('hidden');

                const formData = new FormData(contactForm);
                const hasEmail = emailInput.value.trim() !== '';

                fetch('api_temp.php', {
                    method: 'POST',
                    body: formData
                })
                .then(async response => {
                    const text = await response.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('The server response was not in the correct format. Please try again.');
                    }
                    
                    if (!response.ok) {
                        throw new Error(data.message || `Server error: ${response.status}`);
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        statusMessage.classList.remove('hidden', 'bg-red-900/50', 'border-red-700', 'text-red-300');
                        statusMessage.classList.add('bg-green-900/50', 'border-2', 'border-green-500', 'text-green-300');
                        
                        let successMsg = "✅ Thank you! Your ticket has been submitted successfully.";
                        if (hasEmail) {
                            successMsg += " Our team will contact you within 24 hours.";
                        }
                        statusMessage.innerHTML = `
                            <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ${successMsg}
                        `;
                        
                        contactForm.reset();
                    } else {
                        throw new Error(data.message || 'An unknown error occurred.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusMessage.classList.remove('hidden', 'bg-green-900/50', 'border-green-500', 'text-green-300');
                    statusMessage.classList.add('bg-red-900/50', 'border-2', 'border-red-500', 'text-red-300');
                    statusMessage.innerHTML = `
                        <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ${error.message || 'An error occurred while submitting your ticket. Please try again.'}
                    `;
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg><span>Submit Ticket</span>';
                });
            });
        });
    </script>
</body>
</html>
<?php require 'footer.php'; ?>
