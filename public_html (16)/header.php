<?php
session_start();
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeMistry</title> 
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/header-styles.css">
</head>
<body class="bg-gray-900 text-gray-200">
    <div class="background-wrapper">
    
    <!-- Glass Header with Animated Logo -->
    <header class="glass-header sticky top-0 z-50">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-18">
                
                <!-- Animated Logo -->
                <a href="index.php" class="logo-container flex items-center space-x-2 group">
                    <div class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        <span class="logo-code">Code</span><span class="logo-mistry">Mistry</span>
                    </div>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="index.php" 
                       class="nav-link <?php echo ($current_page == 'index.php') ? 'text-blue-400 active' : 'text-gray-300'; ?> hover:text-blue-400 px-3 lg:px-4 py-2 text-sm lg:text-base font-medium">
                        Home
                    </a>
                    <a href="services.php" 
                       class="nav-link <?php echo ($current_page == 'services.php' || $current_page == 'service-detail.php') ? 'text-blue-400 active' : 'text-gray-300'; ?> hover:text-blue-400 px-3 lg:px-4 py-2 text-sm lg:text-base font-medium">
                        Services
                    </a>
                    <a href="contact.php" 
                       class="nav-link <?php echo ($current_page == 'contact.php') ? 'text-blue-400 active' : 'text-gray-300'; ?> hover:text-blue-400 px-3 lg:px-4 py-2 text-sm lg:text-base font-medium">
                        Contact
                    </a>
                    
                    <!-- CTA Button -->
                    <div class="ml-4 lg:ml-6">
                        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                            <a href="dashboard.php" 
                               class="cta-button text-white font-semibold py-2 px-4 lg:px-6 rounded-lg text-sm lg:text-base inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                My Account
                            </a>
                        <?php else: ?>
                            <a href="login.php" 
                               class="cta-button text-white font-semibold py-2 px-4 lg:px-6 rounded-lg text-sm lg:text-base inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" 
                        class="md:hidden p-2 rounded-lg text-gray-300 hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                        aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path class="hamburger-line" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu Overlay -->
    <div id="menu-overlay" 
         class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 opacity-0 invisible transition-all duration-300">
    </div>

    <!-- Modern Mobile Menu -->
    <div id="mobile-menu" 
         class="fixed top-0 right-0 h-full w-80 max-w-[85vw] shadow-2xl z-50 transform translate-x-full md:hidden overflow-y-auto">
        
        <!-- Menu Header with Animated Logo -->
        <div class="flex items-center justify-between p-5 border-b border-white/10">
            <div class="logo-container text-xl font-extrabold">
                <span class="logo-code">Code</span><span class="logo-mistry">Mistry</span>
            </div>
            <button id="close-menu-button" 
                    class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-all"
                    aria-label="Close menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Menu Content -->
        <div class="flex flex-col p-5 space-y-2">
            <a href="index.php" 
               class="mobile-nav-link <?php echo ($current_page == 'index.php') ? 'bg-white/10 text-blue-400 active' : 'text-gray-300 hover:bg-white/5'; ?> text-base font-medium py-3.5 px-4 rounded-lg transition-all flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Home
            </a>
            
            <a href="services.php" 
               class="mobile-nav-link <?php echo ($current_page == 'services.php' || $current_page == 'service-detail.php') ? 'bg-white/10 text-blue-400 active' : 'text-gray-300 hover:bg-white/5'; ?> text-base font-medium py-3.5 px-4 rounded-lg transition-all flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Services
            </a>
            
            <a href="contact.php" 
               class="mobile-nav-link <?php echo ($current_page == 'contact.php') ? 'bg-white/10 text-blue-400 active' : 'text-gray-300 hover:bg-white/5'; ?> text-base font-medium py-3.5 px-4 rounded-lg transition-all flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Contact
            </a>
            
            <!-- Mobile CTA -->
            <div class="pt-4 mt-4 border-t border-white/10">
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                    <a href="dashboard.php" 
                       class="cta-button w-full text-center text-white font-semibold py-3.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        My Account
                    </a>
                <?php else: ?>
                    <a href="login.php" 
                       class="cta-button w-full text-center text-white font-semibold py-3.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Login / Sign Up
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle with smooth animations
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuOverlay = document.getElementById('menu-overlay');

        function openMenu() {
            mobileMenu.classList.remove('translate-x-full');
            menuOverlay.classList.remove('opacity-0', 'invisible');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            mobileMenu.classList.add('translate-x-full');
            menuOverlay.classList.add('opacity-0', 'invisible');
            document.body.style.overflow = '';
        }

        mobileMenuButton?.addEventListener('click', openMenu);
        closeMenuButton?.addEventListener('click', closeMenu);
        menuOverlay?.addEventListener('click', closeMenu);

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('translate-x-full')) {
                closeMenu();
            }
        });
    </script>
