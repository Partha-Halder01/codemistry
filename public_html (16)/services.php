<?php require 'header.php'; ?>
<link rel="stylesheet" href="CSS/services-styles.css">
<script>document.title = "Our Services - CodeMistry";</script>

<!-- Floating Background Icons -->
<div class="floating-icons">
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 11.5a1 1 0 01-1.898-.659l4-11.5a1 1 0 011.265-.606zM11 6a1 1 0 10-2 0h2zm-4 0a1 1 0 10-2 0h2zm7.732 0a1 1 0 10-2 0h2z" clip-rule="evenodd"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-1.293 1.293a1 1 0 001.414 1.414L6 12.414V16a1 1 0 001 1h6a1 1 0 001-1v-3.586l1.293 1.293a1 1 0 001.414-1.414L14 11.586V8a6 6 0 00-6-6zM8 8a2 2 0 114 0H8z"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C3.732 5.943 7.522 3 10 3s6.268 2.943 9.542 7c-3.274 4.057-7.064 7-9.542 7S3.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path></svg></span>
    <span><svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path></svg></span>
</div>

<main>
    <section class="py-20 md:py-24 relative">
        <div class="container mx-auto px-6">
            <!-- Header -->
            <div class="text-center mb-16 md:mb-20">
                <h1 class="section-header text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6">Our Services</h1>
                <p class="text-gray-400 text-lg md:text-xl mt-8 max-w-3xl mx-auto leading-relaxed">
                    We build high-quality, affordable websites tailored to your needs. Explore our premium packages below.
                </p>
            </div>

            <!-- Services Grid -->
            <div id="services-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                <div class="loading-skeleton text-gray-400 col-span-full text-center py-12">
                    <svg class="animate-spin h-12 w-12 mx-auto mb-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-lg">Loading amazing services...</p>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    function htmlspecialchars(str) {
        if (typeof str !== 'string') return '';
        return str.replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;')
                  .replace(/'/g, '&#039;');
    }
    
    function generateStarRating(ratingStr) {
        const rating = Math.max(0, Math.min(5, parseFloat(ratingStr) || 5.0));
        let starsHtml = '';
        const gradientPrefix = `starGrad_${Math.random().toString(36).substring(2, 7)}_`;

        for (let i = 1; i <= 5; i++) {
            const starValue = rating - (i - 1);
            let fillPercentage = 0;

            if (starValue >= 1) {
                fillPercentage = 100;
            } else if (starValue > 0) {
                fillPercentage = Math.round(starValue * 100);
            } else {
                fillPercentage = 0;
            }

            const gradientId = `${gradientPrefix}${i}`;
            const gradientDef = `
                <linearGradient id="${gradientId}">
                    <stop offset="${fillPercentage}%" class="star-filled" />
                    <stop offset="${fillPercentage}%" class="star-empty" />
                </linearGradient>
            `;

            starsHtml += `
                <svg class="w-5 h-5 flex-shrink-0" fill="url(#${gradientId})" viewBox="0 0 20 20">
                    <defs>${gradientDef}</defs>
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            `;
        }
        return starsHtml;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const servicesGrid = document.getElementById('services-grid');

        fetch('api.php?action=get_services')
            .then(response => response.json())
            .then(data => {
                servicesGrid.innerHTML = '';
                if (data.success && data.services.length > 0) {
                    data.services.forEach(service => {
                        const imagePath = service.cover_image_path ? htmlspecialchars(service.cover_image_path) : 'https://via.placeholder.com/400x225.png?text=CodeMistry+Service';
                        const ratingValue = parseFloat(service.rating).toFixed(1);

                        const serviceCard = `
                            <a href="service-detail.php?id=${service.id}" class="group card-glow-effect rounded-2xl flex flex-col overflow-hidden">
                                <!-- Image -->
                                <div class="image-container aspect-[16/9] relative">
                                    <img src="${imagePath}" 
                                         alt="${htmlspecialchars(service.name)}" 
                                         class="card-image w-full h-full object-cover"
                                         loading="lazy">
                                </div>
                                
                                <!-- Content -->
                                <div class="p-6 flex flex-col flex-grow">
                                    <!-- Rating -->
                                    <div class="star-container mb-4">
                                        ${generateStarRating(service.rating)}
                                        <span class="text-sm text-yellow-400 font-bold ml-1">${ratingValue}</span>
                                    </div>
                                    
                                    <!-- Title -->
                                    <h3 class="text-2xl font-bold text-white mb-3 truncate" title="${htmlspecialchars(service.name)}">
                                        ${htmlspecialchars(service.name)}
                                    </h3>
                                    
                                    <!-- Description -->
                                    <p class="text-gray-400 text-sm leading-relaxed mb-6 line-clamp-2 h-10">
                                        ${htmlspecialchars(service.description)}
                                    </p>
                                    
                                    <!-- Footer -->
                                    <div class="mt-auto pt-4 border-t border-gray-700/50">
                                        <div class="flex items-center justify-between">
                                            <div class="price-badge">
                                                <span class="text-xs text-gray-400 block mb-1">Starting at</span>
                                                <span class="text-2xl font-bold text-blue-400">
                                                    ₹${parseInt(service.deposit_price).toLocaleString('en-IN')}
                                                </span>
                                            </div>
                                            <span class="view-details-link">
                                                View Details
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        `;
                        servicesGrid.innerHTML += serviceCard;
                    });

                    // Mouse tracking for cards
                    document.querySelectorAll('.card-glow-effect').forEach(card => {
                        card.addEventListener('mousemove', (e) => {
                            const rect = card.getBoundingClientRect();
                            const x = ((e.clientX - rect.left) / rect.width) * 100;
                            const y = ((e.clientY - rect.top) / rect.height) * 100;
                            card.style.setProperty('--mouse-x', `${x}%`);
                            card.style.setProperty('--mouse-y', `${y}%`);
                        });
                    });
                } else {
                    servicesGrid.innerHTML = `
                        <div class="col-span-full text-center py-20">
                            <svg class="w-24 h-24 mx-auto mb-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-gray-400 text-xl">No services are available at the moment.</p>
                            <p class="text-gray-500 mt-2">Check back soon for amazing offerings!</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching services:', error);
                servicesGrid.innerHTML = `
                    <div class="col-span-full text-center py-20">
                        <svg class="w-24 h-24 mx-auto mb-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-red-400 text-xl font-semibold">Failed to load services</p>
                        <p class="text-gray-500 mt-2">Please try again later or contact support.</p>
                    </div>
                `;
            });
    });
</script>
<?php require 'footer.php'; ?>
