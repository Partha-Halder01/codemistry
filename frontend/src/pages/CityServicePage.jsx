import { useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import Seo from '../components/Seo';
import { cityPages } from '../data/cityPages';
import { serviceTypes } from '../data/servicePages';
import {
    faqPageLd, localBusinessLd, breadcrumbLd, organizationLd, SITE_INFO,
} from '../seo/structuredData';
import { ArrowRight, CheckCircle2, Phone, MapPin } from 'lucide-react';

const CityServicePage = ({ serviceSlug }) => {
    const { citySlug } = useParams();

    const cityData = cityPages.find((c) => c.slug === citySlug);
    const serviceData = serviceTypes.find((s) => s.slug === serviceSlug);

    useEffect(() => {
        const observer = new IntersectionObserver(
            (entries) => entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('visible'); }),
            { threshold: 0.1 }
        );
        document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
        return () => observer.disconnect();
    }, []);

    if (!cityData || !serviceData) {
        return (
            <div className="min-h-screen flex flex-col items-center justify-center bg-white px-5 text-center">
                <h1 className="text-3xl font-display font-extrabold text-charcoal-950 mb-4">Page Not Found</h1>
                <p className="text-charcoal-500 mb-8">This city-service combination is not available yet.</p>
                <Link to="/services" className="px-6 py-3 bg-brand-500 text-white rounded-full font-semibold hover:bg-brand-600 transition-all">
                    View All Services
                </Link>
            </div>
        );
    }

    const canonical = `${SITE_INFO.url}/services/${serviceSlug}-${citySlug}`;
    const faqs = serviceData.faqs(cityData.city, cityData.priceNote);
    const keywords = serviceData.keywords(cityData.city).join(', ');

    const cityLocalBusinessLd = {
        ...localBusinessLd(),
        serviceArea: {
            '@type': 'City',
            name: cityData.city,
            containedInPlace: { '@type': 'State', name: cityData.state },
        },
    };

    const cityServiceLd = {
        '@context': 'https://schema.org',
        '@type': 'Service',
        name: `${serviceData.name} in ${cityData.city}`,
        description: serviceData.descTemplate(cityData.city),
        provider: { '@type': 'Organization', name: SITE_INFO.name, url: SITE_INFO.url },
        areaServed: {
            '@type': 'City',
            name: cityData.city,
            containedInPlace: { '@type': 'State', name: cityData.state },
        },
        url: canonical,
    };

    return (
        <div className="bg-white min-h-screen overflow-x-hidden">
            <Seo
                title={serviceData.h1Template(cityData.city)}
                description={serviceData.descTemplate(cityData.city)}
                canonical={canonical}
                keywords={keywords}
                jsonLd={[
                    organizationLd(),
                    cityLocalBusinessLd,
                    cityServiceLd,
                    breadcrumbLd([
                        { name: 'Home', url: SITE_INFO.url + '/' },
                        { name: 'Services', url: SITE_INFO.url + '/services' },
                        { name: `${serviceData.name} in ${cityData.city}`, url: canonical },
                    ]),
                    faqPageLd(faqs),
                ]}
            />

            {/* ── HERO ── */}
            <section className="pt-24 pb-16 px-5 sm:px-6 lg:px-10 bg-charcoal-950 relative overflow-hidden">
                <div className="absolute inset-0 bg-gradient-to-br from-charcoal-950 via-charcoal-900 to-charcoal-950 opacity-90" />
                <div className="absolute top-0 right-0 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl" />
                <div className="relative z-10 max-w-5xl mx-auto">
                    <div className="flex items-center gap-2 text-brand-400 text-xs font-semibold uppercase tracking-widest mb-4">
                        <MapPin className="w-3.5 h-3.5" />
                        <span>{cityData.city}, {cityData.state}</span>
                    </div>
                    <h1 className="text-4xl sm:text-5xl lg:text-6xl font-display font-extrabold text-white leading-tight mb-5">
                        {serviceData.name} in {cityData.city}
                    </h1>
                    <p className="text-white/60 text-base sm:text-lg max-w-2xl mb-8 leading-relaxed">
                        {serviceData.intro(cityData.city, cityData)}
                    </p>
                    <div className="flex flex-wrap gap-3">
                        <Link
                            to="/contact"
                            className="inline-flex items-center gap-2 px-7 py-3.5 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-full transition-all shadow-lg shadow-brand-500/30"
                        >
                            Get a free quote <ArrowRight className="w-4 h-4" />
                        </Link>
                        <a
                            href="tel:+918967739189"
                            className="inline-flex items-center gap-2 px-7 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition-all border border-white/20"
                        >
                            <Phone className="w-4 h-4" /> Call us now
                        </a>
                    </div>
                </div>
            </section>

            {/* ── WHY CODEMISTRY IN {CITY} ── */}
            <section className="py-16 px-5 sm:px-6 lg:px-10">
                <div className="max-w-5xl mx-auto reveal opacity-0 translate-y-4 transition-all duration-700">
                    <h2 className="text-2xl sm:text-3xl font-display font-extrabold text-charcoal-950 mb-3">
                        Why Choose Codemistry for {serviceData.shortName} in {cityData.city}?
                    </h2>
                    <p className="text-charcoal-500 text-sm mb-8">Trusted by businesses across India — from startups to established companies.</p>
                    <ul className="grid sm:grid-cols-2 gap-4">
                        {serviceData.benefits.map((benefit, i) => (
                            <li key={i} className="flex items-start gap-3 bg-charcoal-50 rounded-2xl p-4">
                                <CheckCircle2 className="w-5 h-5 text-brand-500 mt-0.5 shrink-0" />
                                <span className="text-charcoal-700 text-sm leading-relaxed">{benefit}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            </section>

            {/* ── LOCAL MARKET CONTEXT ── */}
            <section className="py-16 px-5 sm:px-6 lg:px-10 bg-charcoal-50">
                <div className="max-w-5xl mx-auto reveal opacity-0 translate-y-4 transition-all duration-700">
                    <h2 className="text-2xl sm:text-3xl font-display font-extrabold text-charcoal-950 mb-4">
                        {serviceData.name} for {cityData.city} Businesses
                    </h2>
                    <p className="text-charcoal-600 text-base leading-relaxed mb-4">
                        {cityData.description}
                    </p>
                    <p className="text-charcoal-600 text-base leading-relaxed mb-6">
                        {cityData.priceNote}
                    </p>
                    <div className="flex flex-wrap gap-3 text-sm">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 text-brand-700 rounded-full font-medium">
                            Population {cityData.population}
                        </span>
                        <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 text-brand-700 rounded-full font-medium">
                            GST-compliant invoicing
                        </span>
                        <span className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 text-brand-700 rounded-full font-medium">
                            INR pricing, no hidden fees
                        </span>
                    </div>
                </div>
            </section>

            {/* ── FAQ ── */}
            <section className="py-16 px-5 sm:px-6 lg:px-10">
                <div className="max-w-3xl mx-auto reveal opacity-0 translate-y-4 transition-all duration-700">
                    <h2 className="text-2xl sm:text-3xl font-display font-extrabold text-charcoal-950 mb-8">
                        Frequently Asked Questions
                    </h2>
                    <div className="space-y-3">
                        {faqs.map((faq, i) => (
                            <details key={i} className="group border border-charcoal-200 rounded-2xl p-5 open:border-brand-200 open:bg-brand-50/30 transition-all">
                                <summary className="font-semibold text-charcoal-900 cursor-pointer list-none flex justify-between items-center gap-4 text-sm sm:text-base">
                                    <span>{faq.q}</span>
                                    <span className="text-brand-500 text-lg shrink-0 group-open:rotate-45 transition-transform">+</span>
                                </summary>
                                <p className="text-charcoal-600 text-sm mt-3 leading-relaxed">{faq.a}</p>
                            </details>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── CTA ── */}
            <section className="py-16 px-5 sm:px-6 lg:px-10 bg-brand-600">
                <div className="max-w-3xl mx-auto text-center">
                    <h2 className="text-2xl sm:text-3xl font-display font-extrabold text-white mb-3">
                        Ready to start your {serviceData.shortName} project in {cityData.city}?
                    </h2>
                    <p className="text-white/70 text-sm mb-8">
                        We respond within a few business hours. No commitment required.
                    </p>
                    <div className="flex flex-wrap justify-center gap-3">
                        <Link
                            to="/contact"
                            className="inline-flex items-center gap-2 px-8 py-4 bg-white text-brand-600 font-bold rounded-full hover:bg-brand-50 transition-all"
                        >
                            <Phone className="w-4 h-4" /> Contact Codemistry
                        </Link>
                        <Link
                            to="/services"
                            className="inline-flex items-center gap-2 px-8 py-4 bg-white/10 text-white font-semibold rounded-full hover:bg-white/20 transition-all border border-white/20"
                        >
                            All Services <ArrowRight className="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </section>
        </div>
    );
};

export default CityServicePage;
