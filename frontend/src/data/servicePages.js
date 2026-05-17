export const serviceTypes = [
    {
        name: 'Website Development',
        slug: 'web-development',
        shortName: 'Web Development',
        keywords: (city) => [
            `website development in ${city}`,
            `web development company ${city}`,
            `website design ${city}`,
            `web development cost ${city}`,
            `affordable website developers ${city}`,
            `PWA development ${city}`,
            `website redesign ${city}`,
            `startup website development ${city}`,
        ],
        faqs: (city, priceNote) => [
            {
                q: `How much does website development cost in ${city}?`,
                a: priceNote,
            },
            {
                q: `How long does it take to build a website in ${city}?`,
                a: 'A standard business website takes 2–4 weeks. E-commerce stores or custom platforms with payment integrations typically take 4–8 weeks depending on scope and feature complexity.',
            },
            {
                q: `Do you provide website maintenance in ${city} after launch?`,
                a: 'Yes. We offer ongoing maintenance, hosting support, security updates, and content changes. Our default post-launch support period is 3 months, with affordable monthly retainer options thereafter.',
            },
            {
                q: `Can you redesign an existing website for my ${city}-based business?`,
                a: 'Absolutely. We specialise in full website redesigns that improve speed, Core Web Vitals scores, SEO rankings, and conversion rates — without losing your existing traffic or domain authority.',
            },
            {
                q: `Do you build e-commerce websites with Razorpay and UPI in ${city}?`,
                a: 'Yes. Every e-commerce website we build includes Razorpay integration, UPI Intent for mobile checkout, COD support, GST-compliant invoicing, and optional Shiprocket shipping integration.',
            },
        ],
        benefits: [
            'INR-priced transparent quotes — no hidden charges',
            'GST-compliant invoicing for your accounts team',
            'Mobile-first design optimised for Indian users on 4G/5G',
            'Razorpay & UPI payment integration included as standard',
            '3 months free support and bug fixes after launch',
            'SEO-ready structure from day one (meta tags, sitemap, schema)',
        ],
        h1Template: (city) => `Website Development in ${city} | Codemistry`,
        descTemplate: (city) => `Professional website development in ${city} by Codemistry. Affordable INR pricing, Razorpay & UPI integration, mobile-first design, and 3 months free post-launch support.`,
        intro: (city, cityObj) =>
            `Codemistry provides professional website development services in ${city}, ${cityObj.state} — building fast, SEO-optimised, conversion-focused websites for businesses of all sizes. ${cityObj.localContext}`,
    },
    {
        name: 'AI Integration',
        slug: 'ai-integration',
        shortName: 'AI Integration',
        keywords: (city) => [
            `AI integration services ${city}`,
            `AI chatbot ${city}`,
            `AI development company ${city}`,
            `machine learning integration ${city}`,
            `AI automation for business ${city} India`,
            `Gemini AI integration ${city}`,
            `AI agent development ${city}`,
            `WhatsApp chatbot development ${city}`,
        ],
        faqs: (city) => [
            {
                q: `What AI integrations can you build for my ${city}-based business?`,
                a: `We build multilingual chatbots (English, Hindi, Bengali, Tamil), document automation, lead scoring systems, internal knowledge assistants, and custom AI workflows tailored to your ${city} business operations.`,
            },
            {
                q: `How much does AI integration cost in ${city}?`,
                a: 'AI chatbot projects start from ₹40,000. More complex document automation or lead-scoring integrations range from ₹80,000 to ₹3,00,000 depending on scope and API usage requirements.',
            },
            {
                q: `Which AI models do you use for ${city} businesses?`,
                a: 'We use Gemini, GPT-4o, and Claude depending on your requirements. We select the model that gives you the best accuracy-to-cost ratio — not the most expensive one by default.',
            },
            {
                q: `Can you integrate AI into our existing website or CRM in ${city}?`,
                a: 'Yes. We integrate AI into existing websites, mobile apps, CRMs, and internal tools via REST APIs — no full rebuild required. Most integrations go live within 2–4 weeks.',
            },
            {
                q: `Will the AI chatbot support Hindi and regional languages for ${city} customers?`,
                a: 'Yes. We build multilingual chatbots grounded on your product catalogue and FAQs, supporting English, Hindi, Bengali, Tamil, and other regional languages for India-wide customer coverage.',
            },
        ],
        benefits: [
            'Multilingual support: English, Hindi, Bengali, Tamil out of the box',
            'Integrates with your existing website, app, or CRM',
            'Cost-controlled architecture — no runaway API bills',
            'Grounded responses from your own product/FAQ data',
            'Transparent INR pricing with no vendor lock-in',
            'Ongoing model updates and fine-tuning support included',
        ],
        h1Template: (city) => `AI Integration Services in ${city} | Codemistry`,
        descTemplate: (city) => `Expert AI integration services in ${city} by Codemistry. Multilingual chatbots, document automation, and intelligent workflows — with transparent INR pricing and full post-launch support.`,
        intro: (city, cityObj) =>
            `Codemistry delivers AI integration services in ${city}, ${cityObj.state} — helping businesses build smarter workflows, customer-facing chatbots, and document automation powered by Gemini, GPT-4o, and Claude. ${cityObj.localContext}`,
    },
];
