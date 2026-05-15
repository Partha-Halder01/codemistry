<?php require 'header.php'; ?>
<link rel="stylesheet" href="CSS/service-detail-styles.css">

<style>
    
    /* Sticky sidebar */
    .sticky-card { position: -webkit-sticky; position: sticky; top: 100px; }

    /* Payment tabs */
    .tab-active { 
        background-color: #3b82f6; 
        color: white; 
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        transform: scale(1.02);
    }
    .tab-inactive { background-color: rgba(59, 130, 246, 0.15); color: #9ca3af; }
    .tab-inactive:hover { background-color: rgba(59, 130, 246, 0.3); color: #d1d5db; }

    /* FAQ accordion styling */
    .faq-item details > summary { list-style: none; cursor: pointer; }
    .faq-item details > summary::-webkit-details-marker { display: none; }
    .faq-item details[open] summary .arrow { transform: rotate(90deg); }
    .faq-item summary { outline: none; transition: background-color 0.2s ease; }
    .faq-item summary:hover { background-color: rgba(255, 255, 255, 0.05); }

    /* Star gradient stop colors */
    .star-filled { stop-color: #FACC15; }
    .star-empty { stop-color: #4B5563; }

    /* Content Section Box - What's Included */
    .content-section {
        background: linear-gradient(180deg, rgba(17, 24, 39, 0.3) 0%, rgba(17, 24, 39, 0) 100%);
        border: 1px solid rgba(55, 65, 81, 0.5);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    /* --- Simple Process List Styles --- */
    .process-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem; /* 20px */
        margin-top: 1.5rem; /* 24px */
    }

    .process-item {
        display: flex;
        align-items: flex-start; /* Align icon with the top of the text */
        gap: 0.75rem; /* 12px */
    }

    .process-number {
        width: 2.5rem; /* 40px */
        height: 2.5rem; /* 40px */
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem; /* 18px */
        font-weight: bold;
        color: white;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .process-item:hover .process-number {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
    }
    
    .process-text {
        /* This wrapper is to hold the title and description */
        padding-top: 0.25rem; /* Small padding to align text nicely */
    }

    .process-title {
        font-size: 1rem; /* 16px */
        font-weight: 700;
        color: white;
        margin-bottom: 0.25rem; /* 4px */
        line-height: 1.3;
    }

    .process-description {
        font-size: 0.875rem; /* 14px */
        color: #cbd5e1;
        line-height: 1.5;
    }
    /* No media queries needed, it's mobile-friendly by default */
    
    /* --- Animated Floating Icons --- */
    .floating-icons { position: fixed; inset: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1; pointer-events: none; }
    .floating-icons span { position: absolute; display: block; color: rgba(59, 130, 246, 0.12); animation: float 20s linear infinite; bottom: -150px; will-change: transform, opacity; }
    .floating-icons span svg { width: 100%; height: 100%; display: block; }
    .floating-icons span:nth-child(1) { left: 10%; animation-duration: 25s; animation-delay: -5s; width: 40px; height: 40px; }
    .floating-icons span:nth-child(2) { left: 80%; animation-duration: 30s; animation-delay: -18s; width: 60px; height: 60px;}
    .floating-icons span:nth-child(3) { left: 5%; animation-duration: 18s; animation-delay: -2s; width: 35px; height: 35px;}
    .floating-icons span:nth-child(4) { left: 90%; animation-duration: 22s; animation-delay: -15s; width: 50px; height: 50px;}
    @keyframes float { 0% { transform: translateY(0) rotate(0deg); opacity: 1; } 100% { transform: translateY(-150vh) rotate(720deg); opacity: 0; } }
    
    /* Enhanced CTA gradient with animation */
    .cta-gradient {
        background: linear-gradient(270deg, #0052d4, #4364f7, #6fb1fc);
        background-size: 200% 200%;
        animation: gradient-flow 12s ease infinite;
    }
    @keyframes gradient-flow { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }

    /* Horizontal Scrollbar for Other Services */
    .horizontal-scroller { -ms-overflow-style: none; scrollbar-width: none; }
    .horizontal-scroller::-webkit-scrollbar { display: none; }

    /* Price Card Enhancements */
    .price-card {
        background: linear-gradient(135deg, rgba(17, 24, 39, 0.95) 0%, rgba(31, 41, 55, 0.95) 100%);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .price-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .price-display {
        font-size: 2.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: shimmer 3s infinite;
    }

    @media (min-width: 640px) {
        .price-display {
            font-size: 3rem;
        }
    }

    @media (min-width: 1024px) {
        .price-display {
            font-size: 3.5rem;
        }
    }

    @keyframes shimmer {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }

    /* Enhanced Button Glow Animation */
    .btn-glow-yellow {
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(234, 179, 8, 0.3);
    }

    .btn-glow-yellow::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-glow-yellow:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-glow-yellow:hover {
        box-shadow: 0 6px 25px rgba(234, 179, 8, 0.5);
    }

    .btn-glow-green {
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }

    .btn-glow-green::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-glow-green:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-glow-green:hover {
        box-shadow: 0 6px 25px rgba(34, 197, 94, 0.5);
    }

    /* Fade In Animation */
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

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Feature Item Hover */
    .feature-item {
        transition: all 0.3s ease;
        padding: 0.75rem;
        border-radius: 0.5rem;
    }

    .feature-item:hover {
        background: rgba(59, 130, 246, 0.1);
        transform: translateX(5px);
    }

    /* Image Zoom Effect */
    .image-container {
        overflow: hidden;
        border-radius: 0.75rem;
    }

    .image-zoom {
        transition: transform 0.5s ease;
    }

    .image-container:hover .image-zoom {
        transform: scale(1.05);
    }

    /* Savings Badge */
    .savings-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        animation: badge-pulse 2s infinite;
    }

    @keyframes badge-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Process Section Title */
    .process-section-title {
        text-align: center;
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.4;
    }

    .process-section-title.deposit-title {
        background: linear-gradient(135deg, #eab308 0%, #f59e0b 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* WhatsApp Button Enhancement */
    .whatsapp-float {
        animation: float-button 3s ease-in-out infinite;
    }

    @keyframes float-button {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .whatsapp-float:hover {
        animation: none;
        transform: scale(1.1) rotate(5deg);
    }

    /* Enhanced Other Services Hover Effect */
    .service-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .service-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 25px 50px rgba(59, 130, 246, 0.3);
    }

    .service-card img {
        transition: transform 0.5s ease;
    }

    .service-card:hover img {
        transform: scale(1.1);
    }

    @media (min-width: 1024px) { 
        .content-section { padding: 2rem; margin-top: 2rem; }
    }
  </style>

<div class="floating-icons">
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 11.5a1 1 0 01-1.898-.659l4-11.5a1 1 0 011.265-.606zM11 6a1 1 0 10-2 0h2zm-4 0a1 1 0 10-2 0h2zm7.732 0a1 1 0 10-2 0h2z" clip-rule="evenodd"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-1.293 1.293a1 1 0 001.414 1.414L6 12.414V16a1 1 0 001 1h6a1 1 0 001-1v-3.586l1.293 1.293a1 1 0 001.414-1.414L14 11.586V8a6 6 0 00-6-6zM8 8a2 2 0 114 0H8z"></path></svg></span>
</div>

<main>
    <div id="main-content" class="opacity-0 transition-opacity duration-500 min-h-[60vh]">
        <div class="text-center py-40">
            <p class="text-gray-400 text-lg">Loading service details...</p>
        </div>
    </div>
    <div id="extra-sections-container"></div>
</main>

<a href="https://wa.me/918910710136" class="whatsapp-float fixed bottom-6 right-6 md:bottom-10 md:right-10 bg-[#25D366] w-14 h-14 md:w-16 md:h-16 rounded-full flex items-center justify-center text-white shadow-lg z-50 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-[#25D366]" target="_blank" aria-label="Chat on WhatsApp">
    <svg class="w-8 h-8 md:w-9 md:h-9" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.35 3.43 16.84L2 22L7.32 20.55C8.75 21.31 10.36 21.73 12.04 21.73C17.5 21.73 21.95 17.28 21.95 11.82C21.95 6.36 17.5 2 12.04 2M12.04 20.13C10.56 20.13 9.12 19.74 7.85 19L7.54 18.82L4.44 19.6L5.25 16.58L5.06 16.27C4.24 14.93 3.74 13.42 3.74 11.82C3.74 7.32 7.46 3.6 12.04 3.6C16.62 3.6 20.34 7.32 20.34 11.82C20.34 16.32 16.62 20.03 12.04 20.03V20.13M17.46 14.35C17.17 14.21 15.91 13.58 15.65 13.48C15.4 13.38 15.22 13.33 15.04 13.63C14.86 13.92 14.31 14.54 14.11 14.74C13.92 14.93 13.73 14.96 13.44 14.81C13.15 14.67 12.22 14.32 11.13 13.37C10.29 12.61 9.74 11.85 9.57 11.55C9.4 11.25 9.53 11.13 9.65 11C9.76 10.88 9.91 10.67 10.06 10.5C10.21 10.33 10.26 10.21 10.36 10.01C10.46 9.82 10.41 9.67 10.34 9.55C10.26 9.42 9.77 8.17 9.55 7.62C9.33 7.07 9.11 7.15 8.96 7.14H8.54C8.38 7.14 8.1 7.21 7.87 7.46C7.63 7.7 7.04 8.25 7.04 9.4C7.04 10.55 7.89 11.65 8.02 11.82C8.15 12 9.77 14.41 12.22 15.39C14.67 16.37 14.67 15.92 15.22 15.84C15.77 15.77 16.91 15.11 17.16 14.51C17.41 13.91 17.41 13.43 17.34 13.33C17.26 13.23 17.04 13.16 16.75 13.02C16.46 12.87 17.74 14.49 17.46 14.35Z"/></svg>
</a>

<script>
let currentServiceId = null;

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const serviceId = urlParams.get('id');
    currentServiceId = parseInt(serviceId, 10);
    const mainContent = document.getElementById('main-content');
    if (!currentServiceId) {
         displayError("Invalid or missing Service ID.", mainContent);
         return;
    }
    fetch(`api.php?action=get_service_detail&id=${currentServiceId}`)
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            if (data.success && data.service) {
                renderService(data.service, mainContent);
                renderExtraSections(); 
            } else {
                displayError(data.message || "Service not found.", mainContent);
            }
        }).catch(err => {
            console.error('Service detail fetch error:', err);
            displayError(`Failed to load service details. ${err.message}`, mainContent);
        });
});

function displayError(message, container) {
    container.innerHTML = `<div class="container mx-auto px-6 py-20 text-center"><h1 class="text-2xl font-bold text-red-400 mb-4">Error</h1><p class="text-red-300">${message}</p><a href="services.php" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">Back to Services</a></div>`;
    container.classList.remove('opacity-0');
}

function generateStarRating(ratingStr) {
    const rating = Math.max(0, Math.min(5, parseFloat(ratingStr) || 5.0));
    let starsHtml = '';
    const gradientPrefix = `starGrad_${Math.random().toString(36).substring(2, 7)}_`;
    for (let i = 1; i <= 5; i++) {
        const fillPercentage = Math.round(Math.max(0, Math.min(1, rating - (i - 1))) * 100);
        const gradientId = `${gradientPrefix}${i}`;
        const gradientDef = `<linearGradient id="${gradientId}"><stop offset="${fillPercentage}%" class="star-filled" /><stop offset="${fillPercentage}%" class="star-empty" /></linearGradient>`;
        starsHtml += `<svg class="w-5 h-5 flex-shrink-0" fill="url(#${gradientId})" viewBox="0 0 20 20"><defs>${gradientDef}</defs><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
    }
    return starsHtml;
}

function renderService(service, container) {
    document.title = `${service.name} - CodeMistry`;
    const imagePath = service.cover_image_path ? service.cover_image_path : 'https://via.placeholder.com/1280x720.png?text=Service+Image';
    const imageAlt = service.name || 'Service Image';
    const featuresHtml = (service.features || '').split(',').map(f => f.trim()).filter(f => f)
        .map(f => `<li class="feature-item flex items-start space-x-3"><svg class="w-6 h-6 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-gray-300">${htmlspecialchars(f)}</span></li>`).join('');
    const depositPrice = parseInt(service.deposit_price) || 0;
    const fullPrice = parseInt(service.full_price) || 0;
    const totalWithDeposit = (depositPrice > 0) ? Math.ceil(fullPrice * 1.08) : fullPrice;
    const savings = Math.max(0, totalWithDeposit - fullPrice);
    const savingsPercent = (totalWithDeposit > 0) ? Math.round((savings / totalWithDeposit) * 100) : 0;
    const ratingValue = parseFloat(service.rating).toFixed(1);
    let faqsHtml = '';
    try {
        const faqs = JSON.parse(service.faq || '[]');
        if (Array.isArray(faqs) && faqs.length > 0) {
            faqsHtml = `<div class="mt-12 lg:mt-16 lg:col-span-2 animate-fadeInUp" style="animation-delay: 0.6s;"><h2 class="text-3xl font-bold text-white mb-6 text-center lg:text-left">Frequently Asked Questions</h2><div class="space-y-4">`;
            faqs.forEach(faq => {
                if (faq && typeof faq.q === 'string' && typeof faq.a === 'string') {
                    faqsHtml += `<div class="faq-item bg-gray-800/50 border border-gray-700 rounded-lg overflow-hidden transition-all hover:shadow-lg hover:border-blue-500"><details><summary class="flex justify-between items-center p-4 lg:p-5 cursor-pointer"><h3 class="font-semibold text-white text-lg">${htmlspecialchars(faq.q)}</h3><span class="arrow transition-transform duration-300 transform shrink-0 ml-4 text-gray-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span></summary><div class="p-4 lg:p-5 pt-0 text-gray-300 prose prose-sm prose-invert max-w-none">${htmlspecialchars(faq.a).replace(/\n/g, '<br>')}</div></details></div>`;
                }
            });
            faqsHtml += `</div></div>`;
        }
    } catch(e) { console.error("Error parsing FAQ JSON:", e); }

    container.innerHTML = `
        <section class="py-12 md:py-16 lg:py-20">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12 xl:gap-16">
                    <div class="lg:col-span-2">
                        <div class="image-container mb-6 md:mb-8 aspect-[16/9] bg-gray-800 border border-gray-700 shadow-lg animate-fadeInUp">
                            <img src="${htmlspecialchars(imagePath)}" alt="${htmlspecialchars(imageAlt)}" class="image-zoom w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 animate-fadeInUp" style="animation-delay: 0.1s;">
                            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white leading-tight mb-2 sm:mb-0">${htmlspecialchars(service.name)}</h1>
                            <div class="flex items-center space-x-2 flex-shrink-0 mt-2 sm:mt-0"><div class="flex">${generateStarRating(service.rating)}</div><span class="text-yellow-400 font-semibold text-lg">${ratingValue}</span><span class="text-gray-400 text-sm">/ 5.0</span></div>
                        </div>
                        <p class="text-gray-300 mt-4 text-lg leading-relaxed mb-8 animate-fadeInUp" style="animation-delay: 0.2s;">${htmlspecialchars(service.description)}</p>
                        ${featuresHtml ? `<div class="content-section animate-fadeInUp" style="animation-delay: 0.3s;"><h2 class="text-2xl font-bold text-white mb-6">What's Included</h2><ul class="grid grid-cols-2 gap-x-4 gap-y-2 text-base">${featuresHtml}</ul></div>` : ''}
                    </div>
                    <div class="lg:col-span-1">
                        <div class="sticky-card space-y-6 animate-fadeInUp" style="animation-delay: 0.4s;">
                            <div class="price-card p-5 md:p-6 rounded-xl border border-gray-700 shadow-xl">
                                <div class="grid grid-cols-2 gap-1 bg-gray-900/60 p-1 rounded-lg mb-6">
                                    <button id="deposit-tab" class="py-2.5 px-3 rounded-md text-sm font-semibold transition-all duration-200 tab-active">Pay Deposit</button>
                                    <button id="full-payment-tab" class="py-2.5 px-3 rounded-md text-sm font-semibold transition-all duration-200 tab-inactive">Full Payment</button>
                                </div>
                                <div id="deposit-view" class="text-center">
                                    <p class="text-sm text-gray-400 mb-3">Pay deposit now, rest on completion</p>
                                    <div class="price-display font-extrabold mb-2">₹${depositPrice.toLocaleString('en-IN')}</div>
                                    <p class="text-xs text-gray-500 mb-1">Deposit Amount</p>
                                    <p class="text-xs text-gray-400 bg-gray-800/50 rounded-lg py-2 px-3 inline-block">Total Est: ~₹${totalWithDeposit.toLocaleString('en-IN')} (incl. ~8% fee)</p>
                                    <a href="checkout.php?service_id=${service.id}&plan=deposit" class="btn-glow-yellow mt-6 w-full block text-center bg-yellow-500 hover:bg-yellow-600 text-yellow-900 font-bold py-4 px-5 rounded-lg transition-all duration-300 text-lg relative z-10">Pay Deposit Now</a>
                                </div>
                                <div id="full-payment-view" class="hidden text-center">
                                    ${savings > 0 ? `<div class="savings-badge text-white font-bold text-sm py-2 px-4 rounded-full inline-block mb-3">Save ${savingsPercent}% - Best Value!</div>` : ''}
                                    <div class="price-display font-extrabold mb-2">₹${fullPrice.toLocaleString('en-IN')}</div>
                                    <p class="text-xs text-gray-500 mb-2">One-Time Payment</p>
                                    ${savings > 0 ? `<p class="text-sm text-gray-400"><span class="line-through">₹${totalWithDeposit.toLocaleString('en-IN')}</span> <span class="text-green-400 font-semibold ml-2">Save ₹${savings.toLocaleString('en-IN')}</span></p>` : ''}
                                    <a href="checkout.php?service_id=${service.id}&plan=full" class="btn-glow-green mt-6 w-full block text-center bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-5 rounded-lg transition-all duration-300 text-lg relative z-10">Pay Full Amount</a>
                                </div>
                            </div>
                            
                            <div id="process-section" class="bg-gray-800/30 border border-gray-700 rounded-xl p-4 md:p-5">
                                <div id="deposit-process">
                                    <h3 class="process-section-title deposit-title">Your Journey with Us</h3>
                                    <div class="process-list"> <div class="process-item"> <div class="process-number">1</div>
                                            <div class="process-text"> <h4 class="process-title">Consultation Call</h4>
                                                <p class="process-description">We discuss your project requirements and vision</p>
                                            </div>
                                        </div>
                                        
                                        <div class="process-item"> <div class="process-number">2</div>
                                            <div class="process-text"> <h4 class="process-title">Build & Review</h4>
                                                <p class="process-description">Development with your feedback and reviews</p>
                                            </div>
                                        </div>
                                        
                                        <div class="process-item"> <div class="process-number">3</div>
                                            <div class="process-text"> <h4 class="process-title">Payment & Launch</h4>
                                                <p class="process-description">Complete payment and launch your website</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="full-payment-process" class="hidden">
                                    <h3 class="process-section-title">Your Journey with Us</h3>
                                    <div class="process-list"> <div class="process-item"> <div class="process-number">1</div>
                                            <div class="process-text"> <h4 class="process-title">Consultation Call</h4>
                                                <p class="process-description">Complete project planning and discussion</p>
                                            </div>
                                        </div>
                                        
                                        <div class="process-item"> <div class="process-number">2</div>
                                            <div class="process-text"> <h4 class="process-title">Fast-Track Build</h4>
                                                <p class="process-description">Priority development with quick delivery</p>
                                            </div>
                                        </div>
                                        
                                        <div class="process-item"> <div class="process-number">3</div>
                                            <div class="process-text"> <h4 class="process-title">Go Live!</h4>
                                                <p class="process-description">Direct launch - no payment wait</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${faqsHtml}
                </div>
            </div>
        </section>
    `;
    container.classList.remove('opacity-0');
    initializePageScripts();
}

function renderExtraSections() {
    const container = document.getElementById('extra-sections-container');
    const callToActionHtml = `<section class="py-20 animate-fadeInUp" style="animation-delay: 0.7s;"><div class="container mx-auto px-6"><div class="cta-gradient rounded-2xl p-10 md:p-16 text-center shadow-2xl"><h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Ready to Start Your Project?</h2><p class="text-blue-100 mt-4 mb-8 text-lg max-w-2xl mx-auto">Let's discuss your vision! Book a free consultation with our experts and get started today.</p><a href="contact.php" class="bg-white hover:bg-gray-100 text-blue-700 font-bold py-4 px-10 rounded-full text-lg inline-block transition-all hover:scale-105 shadow-xl">Book Free Consultation</a></div></div></section>`;
    const otherServicesHtml = `<section class="py-20 border-t border-gray-800"><div class="container mx-auto px-6"><div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold text-white mb-3">Explore More Services</h2><p class="text-gray-400">Discover other solutions to grow your business</p></div><div id="other-services-list" class="flex overflow-x-auto space-x-6 pb-4 horizontal-scroller"><p class="text-gray-400 text-center w-full">Loading services...</p></div></div></section>`;
    container.innerHTML = callToActionHtml + otherServicesHtml;
    loadOtherServices();
}

function loadOtherServices() {
    fetch(`api.php?action=get_services`)
        .then(res => res.json())
        .then(data => {
            const listContainer = document.getElementById('other-services-list');
            if (data.success && data.services) {
                const otherServices = data.services.filter(s => s.id !== currentServiceId);
                if (otherServices.length > 0) {
                    listContainer.innerHTML = otherServices.map(service => {
                        const imagePath = service.cover_image_path ? htmlspecialchars(service.cover_image_path) : 'https://via.placeholder.com/400x225.png?text=CodeMistry';
                        return `
                        <a href="service-detail.php?id=${service.id}" class="service-card group flex-shrink-0 w-80 md:w-96 bg-gray-800/50 rounded-xl overflow-hidden border border-gray-700/80">
                           <div class="aspect-[16/9] bg-gray-700 overflow-hidden"><img src="${imagePath}" alt="${htmlspecialchars(service.name)}" class="w-full h-full object-cover"></div>
                           <div class="p-5">
                                <div class="flex items-center space-x-1 mb-2">
                                    ${generateStarRating(service.rating)}
                                    <span class="text-xs text-yellow-400 font-semibold pt-px">${parseFloat(service.rating).toFixed(1)}</span>
                                </div>
                                <h3 class="text-lg font-bold text-white truncate mb-3 group-hover:text-blue-400 transition-colors">${htmlspecialchars(service.name)}</h3>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400 text-sm">Starting at</span>
                                    <span class="text-xl font-bold text-blue-400">₹${parseInt(service.deposit_price).toLocaleString('en-IN')}</span>
                                </div>
                           </div>
                        </a>
                    `}).join('');
                } else {
                    listContainer.innerHTML = '<p class="text-gray-400 text-center w-full">No other services available.</p>';
                }
            } else {
                listContainer.innerHTML = '<p class="text-red-400 text-center w-full">Could not load services.</p>';
            }
        }).catch(err => {
            console.error("Error fetching other services:", err);
            document.getElementById('other-services-list').innerHTML = '<p class="text-red-400 text-center w-full">Could not load services.</p>';
        });
}

function initializePageScripts() {
    const depositTab = document.getElementById('deposit-tab');
    const fullPaymentTab = document.getElementById('full-payment-tab');
    const depositView = document.getElementById('deposit-view');
    const fullPaymentView = document.getElementById('full-payment-view');
    const depositProcess = document.getElementById('deposit-process');
    const fullPaymentProcess = document.getElementById('full-payment-process');
    if (!depositTab || !fullPaymentTab || !depositView || !fullPaymentView || !depositProcess || !fullPaymentProcess) {
        console.error("One or more elements for tab switching not found."); return;
    }
    function switchTabs(selected) {
        if (selected === 'deposit') {
            depositTab.classList.replace('tab-inactive', 'tab-active');
            fullPaymentTab.classList.replace('tab-active', 'tab-inactive');
            depositView.classList.remove('hidden');
            fullPaymentView.classList.add('hidden');
            depositProcess.classList.remove('hidden');
            fullPaymentProcess.classList.add('hidden');
        } else {
            fullPaymentTab.classList.replace('tab-inactive', 'tab-active');
            depositTab.classList.replace('tab-active', 'tab-inactive');
            fullPaymentView.classList.remove('hidden');
            depositView.classList.add('hidden');
            fullPaymentProcess.classList.remove('hidden');
            depositProcess.classList.add('hidden');
        }
    }
    depositTab.addEventListener('click', (e) => { e.preventDefault(); switchTabs('deposit'); });
    fullPaymentTab.addEventListener('click', (e) => { e.preventDefault(); switchTabs('full'); });
}

function htmlspecialchars(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}
</script>

<?php require 'footer.php'; ?>