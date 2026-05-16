// JSON-LD structured data factories for Indian-market SEO.

const SITE = {
    name: 'Codemistry',
    url: 'https://codemistry.in',
    logo: 'https://codemistry.in/logo.png',
    phone: '+91 89677 39189',
    email: 'jamiaalfurqan01@gmail.com',
    sameAs: [
        // Add real social URLs as they go live
    ],
    address: {
        '@type': 'PostalAddress',
        streetAddress: 'East Fatepur, P.O Magnavita, P.S Karandighi',
        addressLocality: 'Uttar Dinajpur',
        addressRegion: 'WB',
        postalCode: '733201',
        addressCountry: 'IN',
    },
};

export const SITE_INFO = SITE;

export const organizationLd = () => ({
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: SITE.name,
    url: SITE.url,
    logo: SITE.logo,
    contactPoint: [{
        '@type': 'ContactPoint',
        telephone: SITE.phone,
        contactType: 'customer support',
        areaServed: 'IN',
        availableLanguage: ['English', 'Hindi', 'Bengali'],
    }],
    sameAs: SITE.sameAs,
});

export const localBusinessLd = () => ({
    '@context': 'https://schema.org',
    '@type': 'LocalBusiness',
    '@id': SITE.url + '#business',
    name: SITE.name,
    image: SITE.logo,
    url: SITE.url,
    telephone: SITE.phone,
    email: SITE.email,
    priceRange: '₹₹',
    address: SITE.address,
    areaServed: { '@type': 'Country', name: 'India' },
});

export const breadcrumbLd = (items) => ({
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((it, idx) => ({
        '@type': 'ListItem',
        position: idx + 1,
        name: it.name,
        item: it.url,
    })),
});

export const articleLd = (post, canonical) => ({
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: post.title,
    description: post.meta_description || post.excerpt || '',
    image: post.cover_image_url ? [post.cover_image_url] : undefined,
    datePublished: post.published_at,
    dateModified: post.updated_at || post.published_at,
    author: { '@type': 'Person', name: post.author_name || 'Codemistry Team' },
    publisher: {
        '@type': 'Organization',
        name: SITE.name,
        logo: { '@type': 'ImageObject', url: SITE.logo },
    },
    mainEntityOfPage: { '@type': 'WebPage', '@id': canonical },
    keywords: post.meta_keywords || (Array.isArray(post.tags) ? post.tags.join(', ') : ''),
});

export const serviceLd = (service, canonical) => ({
    '@context': 'https://schema.org',
    '@type': 'Service',
    name: service.name,
    description: service.description,
    provider: { '@type': 'Organization', name: SITE.name, url: SITE.url },
    areaServed: { '@type': 'Country', name: 'India' },
    url: canonical,
    offers: service.full_price ? {
        '@type': 'Offer',
        priceCurrency: 'INR',
        price: service.full_price,
        availability: 'https://schema.org/InStock',
    } : undefined,
});

export const itemListLd = (items) => ({
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    itemListElement: items.map((it, idx) => ({
        '@type': 'ListItem',
        position: idx + 1,
        name: it.name,
        url: it.url,
    })),
});

export const websiteLd = () => ({
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: SITE.name,
    url: SITE.url,
    potentialAction: {
        '@type': 'SearchAction',
        target: { '@type': 'EntryPoint', urlTemplate: `${SITE.url}/blog?search={search_term_string}` },
        'query-input': 'required name=search_term_string',
    },
});

export const faqPageLd = (faqs) => ({
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map(({ question, answer }) => ({
        '@type': 'Question',
        name: question,
        acceptedAnswer: { '@type': 'Answer', text: answer },
    })),
});
