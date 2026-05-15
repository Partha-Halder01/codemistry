import React from 'react';
import { Helmet } from 'react-helmet-async';
import { SITE_INFO } from '../seo/structuredData';

const DEFAULT_DESCRIPTION =
    'Codemistry is an India-based web & app development company building affordable, high-performance websites, mobile apps, e-commerce stores and AI integrations for businesses across India.';

const Seo = ({
    title,
    description = DEFAULT_DESCRIPTION,
    canonical,
    ogImage,
    keywords,
    type = 'website',
    jsonLd,
    noIndex = false,
}) => {
    const fullTitle = title
        ? `${title} | ${SITE_INFO.name}`
        : `${SITE_INFO.name} — Web & App Development Company in India`;

    const url = canonical || (typeof window !== 'undefined' ? window.location.href : SITE_INFO.url);
    const image = ogImage || `${SITE_INFO.url}/og-default.png`;

    const blocks = Array.isArray(jsonLd) ? jsonLd : (jsonLd ? [jsonLd] : []);

    return (
        <Helmet>
            <html lang="en-IN" />
            <title>{fullTitle}</title>
            <meta name="description" content={description} />
            {keywords && <meta name="keywords" content={keywords} />}
            <meta name="robots" content={noIndex ? 'noindex,nofollow' : 'index,follow'} />
            <link rel="canonical" href={url} />
            <link rel="alternate" hrefLang="en-in" href={url} />
            <link rel="alternate" hrefLang="x-default" href={url} />

            {/* India-specific geo meta */}
            <meta name="geo.region" content="IN-WB" />
            <meta name="geo.placename" content="Uttar Dinajpur, West Bengal, India" />
            <meta name="geo.position" content="26.1322;88.0110" />
            <meta name="ICBM" content="26.1322, 88.0110" />

            {/* Open Graph */}
            <meta property="og:type" content={type} />
            <meta property="og:title" content={fullTitle} />
            <meta property="og:description" content={description} />
            <meta property="og:url" content={url} />
            <meta property="og:image" content={image} />
            <meta property="og:site_name" content={SITE_INFO.name} />
            <meta property="og:locale" content="en_IN" />

            {/* Twitter */}
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" content={fullTitle} />
            <meta name="twitter:description" content={description} />
            <meta name="twitter:image" content={image} />

            {blocks.map((block, idx) => (
                <script key={idx} type="application/ld+json">
                    {JSON.stringify(block)}
                </script>
            ))}
        </Helmet>
    );
};

export default Seo;
