<?php
require 'header.php';
require 'config.php';
?>
<link rel="stylesheet" href="CSS/dashboard-styles.css">
<style>
    body {
        background-color: #0c0a18;
    }

    /* Custom scrollbar for order list and chat */
    .dashboard-scrollbar::-webkit-scrollbar { width: 6px; }
    .dashboard-scrollbar::-webkit-scrollbar-track { background: #1f2937; border-radius: 3px; }
    .dashboard-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 3px; }
    .dashboard-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }

    /* Smooth scroll behavior for chat */
    #chat-box {
        scroll-behavior: smooth;
        will-change: scroll-position;
    }

    /* Modern chat bubble styles with optimized animations */
    .chat-bubble-user { 
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        animation: slideInRight 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .chat-bubble-admin { 
        background-color: #374151;
        color: #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        animation: slideInLeft 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Optimized typing indicator */
    .typing-indicator {
        display: inline-flex;
        gap: 4px;
        padding: 12px 16px;
        background-color: #374151;
        border-radius: 18px;
        animation: slideInLeft 0.3s ease-out;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        background-color: #9ca3af;
        border-radius: 50%;
        animation: typingDot 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(1) { animation-delay: 0s; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typingDot {
        0%, 60%, 100% { 
            transform: scale(0.8);
            opacity: 0.5;
        }
        30% { 
            transform: scale(1.2);
            opacity: 1;
        }
    }

    /* Loading skeleton for chat */
    .chat-skeleton {
        background: linear-gradient(90deg, #1f2937 25%, #374151 50%, #1f2937 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 12px;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Active order card with modern styling */
    .order-card.active {
        border-color: #3b82f6;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%);
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        transform: scale(1.02);
    }
    .order-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    /* Disabled button style */
    button:disabled,
    button[disabled] {
        background-color: #4b5563;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Styles for My Account Section - Mobile Optimized */
    .account-details-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    @media (min-width: 640px) {
        .account-details-grid {
            grid-template-columns: auto 1fr;
            gap: 0.75rem 1rem;
            align-items: center;
        }
    }
    
    .account-label { 
        font-weight: 600;
        color: #9ca3af;
        font-size: 0.875rem;
    }
    
    @media (min-width: 640px) {
        .account-label {
            text-align: right;
        }
    }
    
    .account-value { 
        color: #e5e7eb;
        word-break: break-all;
        font-size: 0.875rem;
    }
    
    .account-input {
        width: 100%;
        background-color: #374151;
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #4b5563;
        outline: none;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    .account-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    }

    /* Mobile-friendly button improvements */
    .mobile-btn {
        min-height: 44px;
        touch-action: manipulation;
    }

    /* Improved mobile spacing */
    @media (max-width: 768px) {
        .order-card {
            margin-bottom: 0.5rem;
        }
        
        #chat-box {
            max-height: 400px;
        }
        
        .chat-bubble-user,
        .chat-bubble-admin {
            max-width: 85%;
        }
    }

    /* Modern glassmorphism effect */
    .glass-card {
        background: rgba(31, 41, 55, 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(75, 85, 99, 0.5);
    }

    /* Smooth animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-in {
        animation: slideIn 0.4s ease-out;
    }

    /* Mobile navigation improvements */
    @media (max-width: 1024px) {
        .mobile-stack {
            display: flex;
            flex-direction: column;
        }
        
        .mobile-order {
            order: 1;
        }
        
        .mobile-chat {
            order: 2;
        }
        
        .mobile-account {
            order: 3;
        }
    }

    /* Better touch targets for mobile */
    @media (max-width: 768px) {
        button, a.btn {
            padding: 0.875rem 1.25rem;
            font-size: 1rem;
        }
    }

    /* Message delivery status indicators */
    .message-status {
        font-size: 0.75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .message-status.sent { color: #9ca3af; }
    .message-status.delivered { color: #3b82f6; }
    .message-status.read { color: #10b981; }

    /* Auto-scroll indicator */
    .scroll-to-bottom-btn {
        position: absolute;
        bottom: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
        opacity: 0;
        pointer-events: none;
    }

    .scroll-to-bottom-btn.visible {
        opacity: 1;
        pointer-events: all;
    }

    .scroll-to-bottom-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.5);
    }

    /* Optimized message container */
    .message-container {
        contain: content;
        transform: translateZ(0);
    }
</style>

<main id="dashboard-content" class="container mx-auto px-3 sm:px-4 md:px-6 lg:px-8 py-4 md:py-6 opacity-0 transition-opacity duration-500">
    <div id="dashboard-loading" class="text-center py-20">
         <svg class="animate-spin h-12 w-12 text-blue-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
         </svg>
        <p class="text-gray-400 text-lg">Loading your dashboard...</p>
    </div>
</main>

<div id="status-message-area" class="fixed bottom-4 right-4 left-4 sm:left-auto z-50 w-auto sm:w-full sm:max-w-sm"></div>

<script>
    let currentUser = null;
    let currentOrders = [];
    let activeOrderId = null;
    let activeOrderUid = null;
    let chatPollInterval = null;
    let lastMessageCount = 0;
    let lastMessageTimestamp = null;
    let isUserAtBottom = true;
    let isChatLoading = false;
    let messageCache = new Map();
    
    // OPTIMIZATION: Adaptive polling intervals based on activity
    const POLLING_INTERVALS = {
        ACTIVE: 5000,      // 5 seconds when chat is active
        MODERATE: 15000,   // 15 seconds when moderately active
        IDLE: 30000,       // 30 seconds when idle
        BACKGROUND: 60000  // 60 seconds when tab is in background
    };
    
    let currentPollingInterval = POLLING_INTERVALS.ACTIVE;
    let lastActivityTime = Date.now();
    let isTabVisible = true;

    // --- Core Functions ---

    function loadDashboardData() {
        fetch('api.php?action=get_dashboard_data')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Dashboard data received:", data);
                if (data.success) {
                    currentUser = data.user;
                    currentOrders = data.orders;
                    renderDashboard();

                    const firstActiveOrder = currentOrders.find(o => o.status !== 'Completed' && o.status !== 'Cancelled');
                    if (firstActiveOrder) {
                        selectOrder(firstActiveOrder.id, firstActiveOrder.order_uid);
                    } else if (currentOrders.length > 0) {
                        selectOrder(currentOrders[0].id, currentOrders[0].order_uid);
                    } else {
                        document.getElementById('chat-section')?.classList.add('hidden');
                        document.getElementById('chat-section')?.classList.remove('flex');
                        document.getElementById('chat-message').disabled = true;
                        document.getElementById('chat-submit-button').disabled = true;
                    }
                } else {
                    console.error("API error:", data.message);
                    renderErrorState(`Error: ${data.message || 'Could not load dashboard.'}. Redirecting to login...`);
                    setTimeout(() => window.location.href = 'login.php', 3000);
                }
            })
            .catch(error => {
                console.error("Fetch error loading dashboard:", error);
                renderErrorState('Failed to load dashboard data. Please refresh the page or check your connection.');
            });
    }

    function renderDashboard() {
        const dashboardContent = document.getElementById('dashboard-content');
        if (!currentUser) {
            renderErrorState('User data not loaded.');
            return;
        }

        // --- Orders List HTML ---
        let ordersHtml = `<div class="text-center py-8 px-4">
            <svg class="w-16 h-16 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-400 mb-2">You have no orders yet.</p>
            <a href="services.php" class="inline-block text-blue-400 font-semibold hover:text-blue-300 transition-colors">View services →</a>
        </div>`;

        if (currentOrders.length > 0) {
             ordersHtml = currentOrders.map(order => {
                const orderDate = new Date(order.order_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
                const { badgeClass, badgeText } = getStatusBadge(order.status);
                const isAwaitingPayment = order.status === 'Awaiting Final Payment';
                const remainingBalancePaise = (order.total_service_price || 0) - (order.amount_paid || 0);
                const remainingBalanceRupees = Math.max(0, remainingBalancePaise / 100);
                const paymentAmount = Math.max(0, remainingBalancePaise);
                const orderIdStr = String(order.id);
                const orderUidStr = String(order.order_uid);

                return `
                <div id="order-card-${orderIdStr}"
                     class="order-card glass-card p-4 rounded-xl cursor-pointer space-y-3 animate-slide-in"
                     onclick="selectOrder('${orderIdStr}', '${orderUidStr}')">

                    <div class="flex justify-between items-start gap-3">
                        <h3 class="font-bold text-white text-base sm:text-lg leading-tight flex-1" title="${htmlspecialchars(order.service_name)}">${htmlspecialchars(order.service_name)}</h3>
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full ${badgeClass} whitespace-nowrap shrink-0">
                            ${badgeText}
                        </span>
                    </div>

                    <div class="text-sm text-gray-400 space-y-1">
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            <span class="font-mono text-gray-300">${orderUidStr}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-gray-300">${orderDate}</span>
                        </p>
                    </div>
                    
                    ${isAwaitingPayment && remainingBalanceRupees > 0 ? `
                        <div class="pt-2 border-t border-gray-700">
                            <p class="text-sm text-yellow-400 mb-2 font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Remaining: ₹${remainingBalanceRupees.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </p>
                            <button id="pay-btn-${orderUidStr}"
                                    onclick="event.stopPropagation(); initiatePayment('${orderUidStr}', ${paymentAmount}, this);"
                                    class="mobile-btn w-full text-center bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white text-sm font-bold py-3 px-4 rounded-lg transition-all duration-200 shadow-lg">
                                Pay Remaining Balance
                            </button>
                        </div>
                    ` : ''}
                </div>
                `;
             }).join('');
        }

        // --- Main Dashboard HTML ---
        dashboardContent.innerHTML = `
            <div class="mb-6 md:mb-8 animate-slide-in">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white">Welcome, <span id="user-welcome-name">${htmlspecialchars(currentUser.name)}</span>!</h1>
                <p class="text-gray-400 mt-1 text-base sm:text-lg">Manage your projects and communicate with us.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 mobile-stack">

                <!-- Orders Section - Mobile Order 1 -->
                <div class="lg:col-span-1 flex flex-col gap-4 sm:gap-6 mobile-order">
                    <div class="flex flex-col min-h-[300px] lg:min-h-[400px]">
                        <div class="flex items-center justify-between mb-4 px-1">
                            <h2 class="text-xl sm:text-2xl font-bold text-white">Your Orders</h2>
                            <span class="text-sm text-gray-400 bg-gray-800/60 px-3 py-1 rounded-full">${currentOrders.length}</span>
                        </div>
                        <div class="flex-1 glass-card rounded-xl p-3 sm:p-4 space-y-3 overflow-y-auto dashboard-scrollbar">
                            ${ordersHtml}
                        </div>
                    </div>

                    <!-- My Account Section - Mobile Order 3 -->
                    <div class="glass-card rounded-xl p-4 sm:p-5 mobile-account animate-slide-in">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl sm:text-2xl font-bold text-white">My Account</h2>
                            <button id="edit-account-btn" onclick="toggleAccountEdit(true)" class="text-sm text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit
                            </button>
                        </div>

                        <div id="account-view">
                            <div class="account-details-grid text-sm">
                                <span class="account-label">Name:</span>
                                <span id="account-name-view" class="account-value break-words">${htmlspecialchars(currentUser.name)}</span>
                                <span class="account-label">Email:</span>
                                <span id="account-email-view" class="account-value break-words">${htmlspecialchars(currentUser.email)}</span>
                                <span class="account-label">Phone:</span>
                                <span id="account-phone-view" class="account-value break-words">${htmlspecialchars(currentUser.phone)}</span>
                            </div>
                        </div>

                        <div id="account-edit" class="hidden">
                            <form id="account-edit-form" class="space-y-4 text-sm">
                                <div class="account-details-grid">
                                    <label for="account-name-edit" class="account-label">Name:</label>
                                    <input type="text" id="account-name-edit" name="name" value="${htmlspecialchars(currentUser.name)}" required class="account-input">

                                    <span class="account-label">Email:</span>
                                    <span class="account-value text-gray-400">${htmlspecialchars(currentUser.email)} <small>(Cannot be changed)</small></span>

                                    <label for="account-phone-edit" class="account-label">Phone:</label>
                                    <input type="tel" id="account-phone-edit" name="phone" value="${htmlspecialchars(currentUser.phone)}" required pattern="[0-9+]{10,15}" title="Enter 10-15 digits, optionally with +" class="account-input">
                                </div>
                                <div class="flex flex-col sm:flex-row gap-3 mt-4">
                                    <button type="submit" class="mobile-btn flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg text-sm transition-colors">Save Changes</button>
                                    <button type="button" onclick="toggleAccountEdit(false)" class="mobile-btn flex-1 bg-gray-600 hover:bg-gray-500 text-white font-semibold py-3 px-4 rounded-lg text-sm transition-colors">Cancel</button>
                                </div>
                            </form>
                        </div>
                         <div id="account-status" class="text-sm mt-3 min-h-[20px]"></div>
                    </div>
                </div>

                <!-- Chat Section - Mobile Order 2 -->
                <div class="lg:col-span-2 flex flex-col mobile-chat">
                    <div id="chat-section" class="glass-card rounded-xl flex flex-col h-full ${currentOrders.length === 0 ? 'hidden' : 'flex'} animate-slide-in" style="min-height: 500px;">
                        <div class="p-3 sm:p-4 border-b border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <h2 id="chat-header" class="text-lg sm:text-xl font-bold text-white truncate flex-1">Select an order to chat</h2>
                            <a id="whatsapp-link" href="https://wa.me/918910710136" target="_blank" class="mobile-btn w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-2.5 sm:py-2 px-4 rounded-lg text-sm transition-all duration-200 shadow-lg">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.35 3.43 16.84L2 22L7.32 20.55C8.75 21.31 10.36 21.73 12.04 21.73C17.5 21.73 21.95 17.28 21.95 11.82C21.95 6.36 17.5 2 12.04 2M12.04 20.13C10.56 20.13 9.12 19.74 7.85 19L7.54 18.82L4.44 19.6L5.25 16.58L5.06 16.27C4.24 14.93 3.74 13.42 3.74 11.82C3.74 7.32 7.46 3.6 12.04 3.6C16.62 3.6 20.34 7.32 20.34 11.82C20.34 16.32 16.62 20.03 12.04 20.03V20.13M17.46 14.35C17.17 14.21 15.91 13.58 15.65 13.48C15.4 13.38 15.22 13.33 15.04 13.63C14.86 13.92 14.31 14.54 14.11 14.74C13.92 14.93 13.73 14.96 13.44 14.81C13.15 14.67 12.22 14.32 11.13 13.37C10.29 12.61 9.74 11.85 9.57 11.55C9.4 11.25 9.53 11.13 9.65 11C9.76 10.88 9.91 10.67 10.06 10.5C10.21 10.33 10.26 10.21 10.36 10.01C10.46 9.82 10.41 9.67 10.34 9.55C10.26 9.42 9.77 8.17 9.55 7.62C9.33 7.07 9.11 7.15 8.96 7.14H8.54C8.38 7.14 8.1 7.21 7.87 7.46C7.63 7.7 7.04 8.25 7.04 9.4C7.04 10.55 7.89 11.65 8.02 11.82C8.15 12 9.77 14.41 12.22 15.39C14.67 16.37 14.67 15.92 15.22 15.84C15.77 15.77 16.91 15.11 17.16 14.51C17.41 13.91 17.41 13.43 17.34 13.33C17.26 13.23 17.04 13.16 16.75 13.02C16.46 12.87 17.74 14.49 17.46 14.35Z"/></svg>
                                <span>WhatsApp Chat</span>
                            </a>
                        </div>
                        <div id="chat-container" class="relative flex-1">
                            <div id="chat-box" class="h-full p-3 sm:p-4 md:p-6 space-y-3 sm:space-y-4 overflow-y-auto dashboard-scrollbar">
                                <p class="text-gray-400 text-center">Select an order to view chat history.</p>
                            </div>
                            <button id="scroll-to-bottom" class="scroll-to-bottom-btn" onclick="scrollToBottomSmooth()">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </button>
                        </div>
                        <div class="p-3 sm:p-4 border-t border-gray-700 bg-gray-800/50 rounded-b-xl">
                            <form id="chat-form" class="flex gap-2 sm:gap-3">
                                <input type="hidden" id="chat-order-id" name="order_id" value="">
                                <input type="text" id="chat-message" name="message" placeholder="Type your message..." required
                                       class="flex-1 bg-gray-700 text-white p-3 sm:p-3.5 rounded-lg border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base" 
                                       disabled
                                       autocomplete="off">
                                <button type="submit" id="chat-submit-button"
                                        class="mobile-btn bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 sm:px-5 rounded-lg transition-all duration-200 shadow-lg shrink-0" disabled>
                                    <span class="hidden sm:inline">Send</span>
                                    <svg class="w-5 h-5 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        dashboardContent.classList.remove('opacity-0');
        document.getElementById('dashboard-loading')?.remove();

        initializeChatForm();
        initializeAccountEditForm();
        initializeVisibilityHandling();
        initializeChatScrollHandling();
    }

    function renderErrorState(message) {
         const dashboardContent = document.getElementById('dashboard-content');
         dashboardContent.innerHTML = `
             <div class="text-center py-20 glass-card rounded-xl p-8 border-red-700 animate-slide-in">
                <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-red-400 mb-4">Oops!</h2>
                <p class="text-red-300 font-semibold mb-6">${message}</p>
                <a href="login.php" class="inline-block mobile-btn bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                    Go to Login
                </a>
             </div>`;
         dashboardContent.classList.remove('opacity-0');
         document.getElementById('dashboard-loading')?.remove();
    }

    function selectOrder(orderId, orderUid) {
        orderId = String(orderId);
        orderUid = String(orderUid);

        if (activeOrderId === orderId) return;

        activeOrderId = orderId;
        activeOrderUid = orderUid;
        lastMessageCount = 0;
        lastMessageTimestamp = null;

        console.log(`Selected Order - ID: ${activeOrderId}, UID: ${activeOrderUid}`);

        document.querySelectorAll('.order-card').forEach(card => card.classList.remove('active'));
        const selectedCard = document.getElementById(`order-card-${orderId}`);
        if (selectedCard) {
            selectedCard.classList.add('active');
            if (window.innerWidth < 1024) {
                document.getElementById('chat-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        const selectedOrder = currentOrders.find(o => String(o.id) === orderId);
        if (!selectedOrder) {
            console.error(`Order data not found in currentOrders for ID: ${orderId}`);
            document.getElementById('chat-header').textContent = `Error loading order details`;
            document.getElementById('chat-message').disabled = true;
            document.getElementById('chat-submit-button').disabled = true;
            document.getElementById('chat-box').innerHTML = '<p class="text-red-400 text-center">Could not find order data.</p>';
            return;
        }

        document.getElementById('chat-header').textContent = `Chat: ${htmlspecialchars(selectedOrder.service_name)} (#${orderUid})`;
        document.getElementById('chat-order-id').value = orderId;
        document.getElementById('chat-message').disabled = false;
        document.getElementById('chat-submit-button').disabled = false;

        const whatsappLink = document.getElementById('whatsapp-link');
        if(whatsappLink) {
             whatsappLink.href = `https://wa.me/918910710136?text=Hi, I have a question about my project (Order ID: ${orderUid}).`;
        }

        // Reset polling to active interval
        currentPollingInterval = POLLING_INTERVALS.ACTIVE;
        updateActivityTime();

        loadChatMessages(orderId, true);
        setupAdaptivePolling(orderId);
    }

    // OPTIMIZATION: Adaptive polling based on activity
    function setupAdaptivePolling(orderId) {
        if (chatPollInterval) clearInterval(chatPollInterval);
        
        chatPollInterval = setInterval(() => {
            if (activeOrderId !== orderId) {
                clearInterval(chatPollInterval);
                return;
            }
            
            // Adjust polling interval based on activity
            const timeSinceActivity = Date.now() - lastActivityTime;
            
            if (!isTabVisible) {
                currentPollingInterval = POLLING_INTERVALS.BACKGROUND;
            } else if (timeSinceActivity < 60000) { // Active in last 1 min
                currentPollingInterval = POLLING_INTERVALS.ACTIVE;
            } else if (timeSinceActivity < 300000) { // Active in last 5 min
                currentPollingInterval = POLLING_INTERVALS.MODERATE;
            } else {
                currentPollingInterval = POLLING_INTERVALS.IDLE;
            }
            
            // Clear and reset with new interval
            clearInterval(chatPollInterval);
            chatPollInterval = setInterval(() => {
                if (activeOrderId === orderId) loadChatMessages(orderId, false);
            }, currentPollingInterval);
            
            loadChatMessages(orderId, false);
        }, currentPollingInterval);
    }

    function updateActivityTime() {
        lastActivityTime = Date.now();
    }

    // OPTIMIZATION: Efficient chat loading with caching and incremental updates
    function loadChatMessages(orderId, isInitialLoad = false) {
        const chatBox = document.getElementById('chat-box');
        if (!orderId || isChatLoading) return;

        if (isInitialLoad) {
            chatBox.innerHTML = `
                <div class="space-y-3">
                    <div class="chat-skeleton h-16 w-3/4"></div>
                    <div class="chat-skeleton h-16 w-2/3 ml-auto"></div>
                    <div class="chat-skeleton h-16 w-3/4"></div>
                </div>`;
        }

        isChatLoading = true;
        const wasAtBottom = isUserAtBottom;

        // Use timestamp-based polling for efficiency
        const queryParams = lastMessageTimestamp 
            ? `?action=get_chat_messages&order_id=${orderId}&since=${lastMessageTimestamp}`
            : `?action=get_chat_messages&order_id=${orderId}`;

        fetch(`api.php${queryParams}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const messages = data.messages;
                    
                    // OPTIMIZATION: Only update if new messages arrived
                    if (messages.length === lastMessageCount && !isInitialLoad) {
                        isChatLoading = false;
                        return;
                    }

                    lastMessageCount = messages.length;
                    
                    if (messages.length > 0) {
                        lastMessageTimestamp = new Date(messages[messages.length - 1].created_at).getTime();
                        updateActivityTime(); // New message = activity
                    }

                    renderMessages(messages, isInitialLoad, wasAtBottom);
                } else {
                    if (isInitialLoad) {
                       chatBox.innerHTML = `<p class="text-red-400 text-center">Failed to load chat: ${data.message || 'Unknown error'}</p>`;
                    }
                }
                isChatLoading = false;
            })
            .catch(err => {
                 if (isInitialLoad) {
                    chatBox.innerHTML = '<p class="text-red-400 text-center">Error loading chat history. Check connection.</p>';
                 }
                 console.error("Network error fetching chat messages:", err);
                 isChatLoading = false;
            });
    }

    // OPTIMIZATION: Efficient message rendering with virtual scrolling consideration
    function renderMessages(messages, isInitialLoad, shouldScroll) {
        const chatBox = document.getElementById('chat-box');
        
        const messagesHtml = messages.map((msg, index) => {
            const isUser = msg.sender === 'user';
            const time = new Date(msg.created_at).toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });
            const isLastMessage = index === messages.length - 1;
            
            return `
                <div class="message-container flex ${isUser ? 'justify-end' : 'justify-start'} w-full" data-message-id="${msg.id}">
                    <div class="max-w-[85%] sm:max-w-xs md:max-w-md lg:max-w-lg">
                        <div class="${isUser ? 'chat-bubble-user' : 'chat-bubble-admin'} rounded-2xl p-3 sm:p-3.5 shadow-md ${isLastMessage ? 'last:pb-16' : ''}">
                            <p class="text-sm sm:text-base break-words leading-relaxed">${htmlspecialchars(msg.message).replace(/\n/g, '<br>')}</p>
                        </div>
                        <p class="text-xs ${isUser ? 'text-right' : 'text-left'} text-gray-500 mt-1 px-2">${time}</p>
                    </div>
                </div>`;
        }).join('');

        if (messages.length === 0) {
            chatBox.innerHTML = `<div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <p class="text-gray-400">No messages yet. Start the conversation!</p>
            </div>`;
        } else {
            chatBox.innerHTML = messagesHtml;
        }

        // OPTIMIZATION: Smart scrolling based on user position
        if (isInitialLoad || shouldScroll) {
            requestAnimationFrame(() => {
                chatBox.scrollTop = chatBox.scrollHeight;
                isUserAtBottom = true;
                updateScrollButton();
            });
        }
    }

    // OPTIMIZATION: Scroll handling with debouncing
    let scrollDebounceTimer;
    function initializeChatScrollHandling() {
        const chatBox = document.getElementById('chat-box');
        if (!chatBox) return;

        chatBox.addEventListener('scroll', () => {
            clearTimeout(scrollDebounceTimer);
            scrollDebounceTimer = setTimeout(() => {
                const threshold = 150;
                isUserAtBottom = (chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight) < threshold;
                updateScrollButton();
            }, 100);
        });
    }

    function updateScrollButton() {
        const scrollBtn = document.getElementById('scroll-to-bottom');
        if (scrollBtn) {
            if (isUserAtBottom) {
                scrollBtn.classList.remove('visible');
            } else {
                scrollBtn.classList.add('visible');
            }
        }
    }

    function scrollToBottomSmooth() {
        const chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
            isUserAtBottom = true;
            updateScrollButton();
        }
    }

    // OPTIMIZATION: Tab visibility handling
    function initializeVisibilityHandling() {
        document.addEventListener('visibilitychange', () => {
            isTabVisible = !document.hidden;
            if (isTabVisible && activeOrderId) {
                // Refresh immediately when tab becomes visible
                loadChatMessages(activeOrderId, false);
                updateActivityTime();
            }
        });
    }

    function initializeChatForm() {
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('chat-message');
        const submitButton = document.getElementById('chat-submit-button');

        // Track typing activity
        messageInput.addEventListener('input', () => {
            updateActivityTime();
        });

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const currentActiveOrderId = document.getElementById('chat-order-id').value;
            if (!currentActiveOrderId || messageInput.value.trim() === '') return;

            const formData = new FormData(this);
            const messageToSend = messageInput.value;

            messageInput.disabled = true;
            submitButton.disabled = true;
            updateActivityTime();

            formData.set('order_id', currentActiveOrderId);

            // Optimistic UI update
            appendSentMessage(messageToSend);
            messageInput.value = '';

            fetch('api.php?action=send_chat_message', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Reload messages to get server confirmation
                    setTimeout(() => loadChatMessages(currentActiveOrderId, false), 300);
                } else {
                    showStatusMessage('Failed to send message: ' + (data.message || 'Unknown error'), 'error');
                    // Reload to sync state
                    loadChatMessages(currentActiveOrderId, false);
                }
            })
            .catch(err => {
                showStatusMessage('Network error sending message.', 'error');
                loadChatMessages(currentActiveOrderId, false);
            })
            .finally(() => {
                if (document.getElementById('chat-order-id').value === currentActiveOrderId) {
                    messageInput.disabled = false;
                    submitButton.disabled = false;
                    messageInput.focus();
                }
            });
        });
    }

    function appendSentMessage(message) {
         const chatBox = document.getElementById('chat-box');
         const time = new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });
         const messageDiv = document.createElement('div');
         messageDiv.className = 'message-container flex justify-end w-full';
         messageDiv.innerHTML = `
            <div class="max-w-[85%] sm:max-w-xs md:max-w-md lg:max-w-lg opacity-80">
                <div class="chat-bubble-user rounded-2xl p-3 sm:p-3.5 shadow-md">
                    <p class="text-sm sm:text-base break-words leading-relaxed">${htmlspecialchars(message).replace(/\n/g, '<br>')}</p>
                </div>
                <p class="text-xs text-right text-gray-500 mt-1 px-2">${time} <span class="text-yellow-400">●</span></p>
            </div>`;

         const placeholder = chatBox.querySelector('p.text-center');
         if (placeholder) placeholder.remove();

         chatBox.appendChild(messageDiv);
         requestAnimationFrame(() => {
             chatBox.scrollTop = chatBox.scrollHeight;
             isUserAtBottom = true;
             updateScrollButton();
         });
    }

    // --- Payment Functions (same as before) ---

    function initiatePayment(orderUid, amountPaise, buttonElement) {
         console.log(`Initiating payment for Order UID: ${orderUid}, Amount: ${amountPaise}`);
         if (!currentUser) {
             showStatusMessage('User data not available. Cannot initiate payment.', 'error');
             return;
         }
         if (buttonElement) {
             buttonElement.textContent = 'Processing...';
             buttonElement.disabled = true;
         }

         const options = {
             key: "<?php echo RAZORPAY_KEY_ID; ?>",
             amount: amountPaise,
             currency: "INR",
             name: "CodeMistry",
             description: `Final Payment for Order ${orderUid}`,
             image: "https://i.imgur.com/g237s54.png",
             handler: function (response) {
                  console.log("Razorpay success response:", response);
                  if (buttonElement) buttonElement.textContent = 'Verifying...';
                  verifyPayment(response.razorpay_payment_id, orderUid, buttonElement, response.razorpay_signature, response.razorpay_order_id);
             },
             prefill: {
                 name: currentUser.name,
                 email: currentUser.email,
                 contact: currentUser.phone
             },
             theme: { color: "#3b82f6" },
             modal: {
                 ondismiss: function() {
                      console.log("Payment modal dismissed for order:", orderUid);
                      if (buttonElement) {
                         buttonElement.textContent = 'Pay Remaining Balance';
                         buttonElement.disabled = false;
                      }
                      showStatusMessage("Payment cancelled.", "info", 3000);
                 }
             }
         };

         try {
            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function (response){
                    console.error("Razorpay payment failed:", response);
                     showStatusMessage(`Payment Failed: ${response.error?.description || 'Unknown Razorpay error'}.`, 'error');
                     if (buttonElement) {
                         buttonElement.textContent = 'Payment Failed - Retry';
                         buttonElement.disabled = false;
                     }
            });
            rzp.open();
         } catch (error) {
             console.error("Error opening Razorpay checkout:", error);
             showStatusMessage("Could not initialize payment gateway. Please try again later.", "error");
              if (buttonElement) {
                 buttonElement.textContent = 'Error - Retry';
                 buttonElement.disabled = false;
             }
         }
    }

    function verifyPayment(paymentId, orderUid, buttonElement, signature = null, razorpayOrderId = null) {
        showStatusMessage("Verifying payment...", "info");
        const formData = new FormData();
        formData.append('razorpay_payment_id', paymentId);
        formData.append('order_uid', orderUid);
        if (signature) formData.append('razorpay_signature', signature);
        if (razorpayOrderId) formData.append('razorpay_order_id', razorpayOrderId);

        fetch('api.php?action=verify_remaining_payment', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
             console.log("Verification response:", status, body);
            if (body.success) {
                showStatusMessage("Payment Successful! Refreshing dashboard...", "success", 3000);
                setTimeout(loadDashboardData, 1500);
            } else {
                 const errorMessage = `Verification Failed: ${body.message || 'Please contact support.'}`;
                 showStatusMessage(errorMessage, 'error', 8000);
                 if (buttonElement) {
                     buttonElement.textContent = 'Verification Failed - Retry';
                     buttonElement.disabled = false;
                 }
            }
        })
        .catch(error => {
            console.error("Network error during verification:", error);
            showStatusMessage('Network error during payment verification. Please contact support.', 'error', 8000);
             if (buttonElement) {
                 buttonElement.textContent = 'Verification Error - Retry';
                 buttonElement.disabled = false;
             }
        });
    }

    // --- Utility Functions ---

    function getStatusBadge(status) {
        switch (status) {
            case 'Paid':
            case 'Processing':
                return { badgeClass: 'bg-blue-600/20 text-blue-300 border border-blue-500/30', badgeText: 'Processing' };
            case 'Awaiting Final Payment':
                return { badgeClass: 'bg-yellow-600/20 text-yellow-300 border border-yellow-500/30', badgeText: 'Awaiting Payment' };
            case 'Completed':
                return { badgeClass: 'bg-green-600/20 text-green-300 border border-green-500/30', badgeText: 'Completed' };
            case 'Cancelled':
                return { badgeClass: 'bg-red-600/20 text-red-300 border border-red-500/30', badgeText: 'Cancelled' };
            default:
                return { badgeClass: 'bg-gray-600/20 text-gray-300 border border-gray-500/30', badgeText: status };
        }
    }

    function showStatusMessage(message, type = 'info', duration = 5000) {
        const area = document.getElementById('status-message-area');
        if (!area) return;

        const messageDiv = document.createElement('div');
        messageDiv.textContent = message;

        let bgColor, textColor, borderColor, iconSvg;
        if (type === 'success') {
            bgColor = 'bg-green-600/90'; textColor = 'text-white'; borderColor = 'border-green-400';
            iconSvg = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        } else if (type === 'error') {
            bgColor = 'bg-red-600/90'; textColor = 'text-white'; borderColor = 'border-red-400';
            iconSvg = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        } else {
            bgColor = 'bg-blue-600/90'; textColor = 'text-white'; borderColor = 'border-blue-400';
            iconSvg = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        }

        messageDiv.className = `p-3 sm:p-4 rounded-lg shadow-xl text-sm sm:text-base mb-2 border ${bgColor} ${textColor} ${borderColor} transition-all duration-300 transform translate-y-2 opacity-0 backdrop-blur-sm`;
        messageDiv.innerHTML = iconSvg + message;

        area.appendChild(messageDiv);

        requestAnimationFrame(() => {
            messageDiv.classList.remove('translate-y-2', 'opacity-0');
        });

        setTimeout(() => {
            messageDiv.classList.add('opacity-0');
            setTimeout(() => messageDiv.remove(), 300);
        }, duration);
    }

    function htmlspecialchars(str) {
        if (typeof str !== 'string') return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return str.replace(/[&<>\"']/g, m => map[m]);
    }

    // --- Account Edit Functions ---

    function toggleAccountEdit(editing) {
        const viewDiv = document.getElementById('account-view');
        const editDiv = document.getElementById('account-edit');
        const editButton = document.getElementById('edit-account-btn');
        const statusDiv = document.getElementById('account-status');

        if (editing) {
            document.getElementById('account-name-edit').value = document.getElementById('account-name-view').textContent;
            document.getElementById('account-phone-edit').value = document.getElementById('account-phone-view').textContent;

            viewDiv.classList.add('hidden');
            editDiv.classList.remove('hidden');
            editButton.classList.add('hidden');
            statusDiv.textContent = '';
        } else {
            viewDiv.classList.remove('hidden');
            editDiv.classList.add('hidden');
            editButton.classList.remove('hidden');
        }
    }

    function initializeAccountEditForm() {
        const form = document.getElementById('account-edit-form');
        const statusDiv = document.getElementById('account-status');

        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            statusDiv.innerHTML = '<p class="text-yellow-400">Saving...</p>';
            const submitButton = form.querySelector('button[type="submit"]');
            const cancelButton = form.querySelector('button[type="button"]');
            submitButton.disabled = true;
            cancelButton.disabled = true;

            const formData = new FormData(form);

            fetch('api.php?action=update_user_details', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    statusDiv.innerHTML = '<p class="text-green-400 font-semibold">✅ Details updated!</p>';
                    currentUser.name = formData.get('name');
                    currentUser.phone = formData.get('phone');
                    document.getElementById('account-name-view').textContent = htmlspecialchars(currentUser.name);
                    document.getElementById('account-phone-view').textContent = htmlspecialchars(currentUser.phone);
                    document.getElementById('user-welcome-name').textContent = htmlspecialchars(currentUser.name);

                    setTimeout(() => {
                        toggleAccountEdit(false);
                        statusDiv.textContent = '';
                    }, 1500);

                } else {
                    statusDiv.innerHTML = `<p class="text-red-400 font-semibold">❌ ${data.message || 'Update failed.'}</p>`;
                }
            })
            .catch(err => {
                console.error("Account update error:", err);
                statusDiv.innerHTML = '<p class="text-red-400 font-semibold">❌ Network error. Please try again.</p>';
            })
            .finally(() => {
                submitButton.disabled = false;
                cancelButton.disabled = false;
            });
        });
    }

    // --- Initial Load ---
    document.addEventListener('DOMContentLoaded', loadDashboardData);

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (chatPollInterval) clearInterval(chatPollInterval);
    });

</script>

<?php
require 'footer.php';
?>
