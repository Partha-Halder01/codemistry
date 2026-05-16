import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import {
    Monitor, Smartphone, Database, Cpu, Server, RefreshCw,
    ArrowRight, ChevronRight, Star, CheckCircle2, Tag
} from 'lucide-react';
import Seo from '../components/Seo';
import { breadcrumbLd, itemListLd, organizationLd, SITE_INFO } from '../seo/structuredData';

const Services = () => {
    const [services, setServices] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get('/services')
            .then(r => setServices(r.data))
            .catch(console.error)
            .finally(() => setLoading(false));
    }, []);

    const getIconForService = (name) => {
        const n = name.toLowerCase();
        if (n.includes('app') && !n.includes('web')) return { Icon: Smartphone, gradient: 'from-emerald-400 to-teal-600' };
        if (n.includes('crm') || n.includes('data')) return { Icon: Database, gradient: 'from-violet-400 to-purple-600' };
        if (n.includes('ai') || n.includes('intelligence')) return { Icon: Cpu, gradient: 'from-cyan-400 to-sky-600' };
        if (n.includes('manage') || n.includes('host')) return { Icon: Server, gradient: 'from-pink-400 to-rose-600' };
        if (n.includes('updat') || n.includes('maintain')) return { Icon: RefreshCw, gradient: 'from-amber-400 to-orange-600' };
        return { Icon: Monitor, gradient: 'from-brand-400 to-brand-700' };
    };

    const getPriceRange = (service) => {
        if (service.pricings && service.pricings.length > 0) {
            const startPrices = service.pricings.map(p => Number(p.price));
            const endPrices = service.pricings.map(p => Number(p.end_price || p.price));
            const minPrice = Math.min(...startPrices);
            const maxPrice = Math.max(...endPrices);
            if (minPrice === maxPrice) {
                return `₹${Number(minPrice).toLocaleString('en-IN')}`;
            }
            return `₹${Number(minPrice).toLocaleString('en-IN')} - ₹${Number(maxPrice).toLocaleString('en-IN')}`;
        }
        if (service.full_price) return `₹${Number(service.full_price).toLocaleString('en-IN')}`;
        return null;
    };

    return (
        <div className="min-h-screen bg-white">
            <Seo
                title="Web & App Development Services in India — Transparent INR Pricing"
                description="Hire Codemistry for web development, mobile apps, e-commerce, AI integration and custom CRM — affordable INR pricing, GST invoice, on-time delivery for Indian businesses."
                canonical={SITE_INFO.url + '/services'}
                keywords="web development services India, hire web developer India, app development company India, ecommerce website development India, AI integration service India, custom software India, website cost India"
                jsonLd={[
                    organizationLd(),
                    breadcrumbLd([
                        { name: 'Home', url: SITE_INFO.url + '/' },
                        { name: 'Services', url: SITE_INFO.url + '/services' },
                    ]),
                    itemListLd((services || []).map(s => ({ name: s.name, url: `${SITE_INFO.url}/services/${s.slug}` }))),
                ]}
            />

            {/* ── Compact Header ── */}
            <section className="pt-24 pb-8 px-5 bg-white">
                <div className="max-w-4xl mx-auto text-center">
                    <span className="inline-block px-3 py-1 bg-brand-100 text-brand-700 rounded-full text-xs font-bold tracking-wider uppercase mb-4">Web &amp; App Development India</span>
                    <h1 className="text-4xl md:text-5xl font-display font-extrabold text-charcoal-950 leading-tight mb-4">
                        Development Services for Indian Businesses
                    </h1>
                    <p className="text-charcoal-500 text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
                        Affordable websites, mobile apps, e-commerce stores and AI solutions — transparent INR pricing, GST invoice, and on-time delivery guaranteed.
                    </p>
                    <div className="mt-5 flex flex-wrap justify-center gap-3 text-xs text-charcoal-500">
                        {['✓ Transparent INR Pricing', '✓ GST Invoice Provided', '✓ On-Time Delivery', '✓ Free Consultation'].map(b => (
                            <span key={b} className="px-3 py-1.5 bg-charcoal-50 border border-charcoal-100 rounded-full font-medium">{b}</span>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── Services Grid ── */}
            <section id="services-grid" className="px-5 sm:px-6 lg:px-10 pb-20 pt-8">
                <div className="max-w-7xl mx-auto">
                    {loading ? (
                        <div className="flex justify-center py-32">
                            <div className="w-12 h-12 border-4 border-brand-200 border-t-brand-500 rounded-full animate-spin" />
                        </div>
                    ) : services.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
                            {services.map((service, index) => {
                                const { Icon, gradient } = getIconForService(service.name);
                                const priceRange = getPriceRange(service);
                                return (
                                    <Link
                                        key={service.id}
                                        to={`/services/${service.slug}`}
                                        className="group relative bg-white border border-charcoal-100 rounded-3xl overflow-hidden hover:shadow-2xl hover:shadow-charcoal-900/8 hover:-translate-y-2 transition-all duration-300 flex flex-col"
                                        style={{ animationDelay: `${index * 0.08}s` }}
                                    >
                                        {/* Image / Gradient Header */}
                                        <div className="relative h-52 overflow-hidden flex-shrink-0">
                                            {service.cover_image_path ? (
                                                <>
                                                    <img
                                                        src={service.cover_image_path}
                                                        alt={service.name}
                                                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                                    />
                                                    <div className="absolute inset-0 bg-gradient-to-t from-charcoal-950/70 via-charcoal-950/20 to-transparent" />
                                                </>
                                            ) : (
                                                <div className={`w-full h-full bg-gradient-to-br ${gradient} flex items-center justify-center relative overflow-hidden`}>
                                                    <div className="absolute -top-8 -right-8 w-36 h-36 bg-white/10 rounded-full" />
                                                    <div className="absolute -bottom-8 -left-8 w-28 h-28 bg-white/10 rounded-full" />
                                                    <div className="w-20 h-20 rounded-3xl bg-white/20 backdrop-blur-sm flex items-center justify-center z-10">
                                                        <Icon className="w-10 h-10 text-white" />
                                                    </div>
                                                </div>
                                            )}

                                            {/* Price badge — top right */}
                                            {priceRange && (
                                                <div className="absolute top-3 right-3 z-20 bg-white/95 backdrop-blur-sm text-charcoal-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                                                    <Tag className="w-3 h-3 text-brand-500" />
                                                    {priceRange}
                                                </div>
                                            )}

                                            
                                        </div>

                                        {/* Card Body */}
                                        <div className="p-6 flex flex-col flex-1">
                                            <h3 className="text-xl font-display font-bold text-charcoal-950 mb-2 group-hover:text-brand-600 transition-colors">
                                                {service.name}
                                            </h3>
                                            <p className="text-charcoal-500 text-sm leading-relaxed mb-5 line-clamp-2 flex-1">
                                                {service.description}
                                            </p>

                                            {service.pricings && service.pricings.length > 0 && (
                                                <div className="flex items-center gap-2 mb-5">
                                                    <CheckCircle2 className="w-3.5 h-3.5 text-brand-500 shrink-0" />
                                                    <span className="text-xs text-charcoal-500 font-medium">
                                                        {service.pricings.length} pricing plan{service.pricings.length > 1 ? 's' : ''} available
                                                    </span>
                                                </div>
                                            )}

                                            <div className="flex items-center justify-between pt-4 border-t border-charcoal-100">
                                                <span className="text-brand-600 font-semibold text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                                                    View details <ChevronRight className="w-4 h-4" />
                                                </span>
                                                {getPriceRange(service) && (
                                                    <span className="text-xs text-charcoal-400 font-medium">{getPriceRange(service)}</span>
                                                )}
                                            </div>
                                        </div>

                                        {/* Bottom accent bar on hover */}
                                        <div className={`h-1 bg-gradient-to-r ${gradient} opacity-0 group-hover:opacity-100 transition-opacity duration-300`} />
                                    </Link>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="py-32 text-center bg-charcoal-50 rounded-3xl border border-dashed border-charcoal-200">
                            <Monitor className="w-14 h-14 mx-auto mb-4 text-charcoal-300" />
                            <p className="text-xl font-semibold text-charcoal-600 mb-2">No services available yet.</p>
                            <p className="text-charcoal-400 text-sm mb-6">Check back later or contact us directly.</p>
                            <Link to="/contact" className="inline-flex items-center gap-2 bg-brand-500 text-white font-semibold px-6 py-3 rounded-full hover:bg-brand-600 transition-colors">
                                Contact Us <ArrowRight className="w-4 h-4" />
                            </Link>
                        </div>
                    )}
                </div>
            </section>

            {/* ── Bottom CTA ── */}
            <section className="bg-charcoal-950 mx-5 sm:mx-6 lg:mx-10 mb-16 rounded-3xl px-8 py-14 text-center relative overflow-hidden">
                <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_rgba(34,197,94,0.12)_0%,_transparent_70%)]" />
                <div className="relative z-10">
                    <div className="flex items-center justify-center gap-1.5 mb-4">
                        {[...Array(5)].map((_, i) => <Star key={i} className="w-4 h-4 fill-amber-400 text-amber-400" />)}
                        <span className="text-charcoal-400 text-sm ml-2">Trusted by 50+ businesses</span>
                    </div>
                    <h2 className="text-3xl sm:text-4xl font-display font-extrabold text-white mb-4">
                        Ready to build something amazing?
                    </h2>
                    <p className="text-charcoal-400 max-w-lg mx-auto mb-8 text-sm sm:text-base">
                        Let's turn your idea into a powerful digital product. Get a free consultation today.
                    </p>
                    <Link to="/contact" className="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-9 py-4 rounded-full transition-all hover:-translate-y-0.5 shadow-xl shadow-brand-500/30 text-base">
                        Get Free Consultation <ArrowRight className="w-5 h-5" />
                    </Link>
                </div>
            </section>
        </div>
    );
};

export default Services;
