import { useState, useEffect, useRef, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../api';
import Seo from '../components/Seo';
import { breadcrumbLd, organizationLd, serviceLd, SITE_INFO } from '../seo/structuredData';
import {
    CheckCircle2, ChevronDown, ArrowLeft, ArrowRight,
    Star, Phone, Shield, Clock, Zap, MessageCircle,
    ChevronRight, Users, Package
} from 'lucide-react';

const DEFAULT_PROCESS_STEPS = [
    { title: 'Consultation Call', description: 'We discuss your requirements, goals, budget, and timeline in detail to set the right foundation.' },
    { title: 'Build & Review', description: 'Our team builds your solution with regular demos and feedback loops so there are never any surprises.' },
    { title: 'Payment & Launch', description: 'Final handover, deployment, and guidance — your product goes live and we\'re here for ongoing support.' },
];

/* ─── Parallax Hook ─── */
const useParallax = () => {
    const elementsRef = useRef([]);
    const register = useCallback((el) => {
        if (el && !elementsRef.current.includes(el)) elementsRef.current.push(el);
    }, []);
    useEffect(() => {
        const handleScroll = () => {
            elementsRef.current.forEach((el) => {
                const rect = el.getBoundingClientRect();
                const speed = parseFloat(el.dataset.speed || '0.15');
                const yOffset = (rect.top - window.innerHeight / 2) * speed;
                el.style.transform = `translateY(${yOffset}px) scale(1.15)`;
            });
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);
    return register;
};

const ServiceDetails = () => {
    const params = useParams();
    const slug = params.slug || params.id;
    const [service, setService] = useState(null);
    const [loading, setLoading] = useState(true);
    const [openFaq, setOpenFaq] = useState(0);
    const [relatedServices, setRelatedServices] = useState([]);
    const [selectedPlanId, setSelectedPlanId] = useState(null);
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    const dropdownRef = useRef(null);
    const registerParallax = useParallax();

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setIsDropdownOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    useEffect(() => {
        window.scrollTo(0, 0);
        api.get(`/services/${slug}`)
            .then(r => {
                setService(r.data);
                if (r.data?.pricings?.length > 0) {
                    const popular = r.data.pricings.find(p => p.is_popular);
                    setSelectedPlanId(popular ? popular.id : r.data.pricings[0].id);
                }
            })
            .catch(console.error)
            .finally(() => setLoading(false));

        api.get('/services')
            .then(r => setRelatedServices(r.data.filter(s => s.slug !== slug).slice(0, 3)))
            .catch(() => { });
    }, [slug]);

    const formatPrice = (p) =>
        new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(p || 0);

    if (loading) {
        return (
            <div className="min-h-screen bg-white flex items-center justify-center">
                <div className="w-12 h-12 border-4 border-brand-200 border-t-brand-500 rounded-full animate-spin" />
            </div>
        );
    }

    if (!service) {
        return (
            <div className="min-h-screen bg-white pt-32 text-center px-5">
                <h1 className="text-2xl font-bold text-charcoal-900 mb-4">Service not found</h1>
                <Link to="/services" className="text-brand-600 font-semibold hover:underline inline-flex items-center gap-2">
                    <ArrowLeft className="w-4 h-4" /> Back to Services
                </Link>
            </div>
        );
    }

    const featureList = service.features
        ? (typeof service.features === 'string' ? service.features.split('\n').filter(Boolean) : service.features)
        : [];

    const depositPrice = service.deposit_price || (service.full_price ? Math.round(service.full_price * 0.3) : 0);
    const fullPrice = service.full_price || 0;

    const activePlan = service.pricings?.find(p => String(p.id) === String(selectedPlanId)) || service.pricings?.[0];

    const displayDepositPrice = activePlan ? Math.round(activePlan.price * 0.3) : depositPrice;
    const displayFullPrice = activePlan ? activePlan.price : fullPrice;
    const whatsappNumber = '918967739189';
    const activePlanPriceText = activePlan
        ? (activePlan.end_price
            ? `${formatPrice(activePlan.price)} - ${formatPrice(activePlan.end_price)}`
            : formatPrice(activePlan.price))
        : formatPrice(displayFullPrice);
    const whatsappServiceUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(
        `Hi Codemistry, I am interested in your ${service.name} service${activePlan ? ` (${activePlan.plan_name} plan)` : ''}. Please share details.`
    )}`;

    const canonical = `${SITE_INFO.url}/services/${service.slug}`;

    return (
        <div className="bg-white min-h-screen pb-0">
            <Seo
                title={`${service.name} in India — Pricing, Process & Features`}
                description={(service.description || '').slice(0, 160) || `Hire Codemistry for ${service.name} in India. Affordable INR pricing, transparent process, and ongoing support.`}
                canonical={canonical}
                keywords={`${service.name} India, ${service.name} cost India, ${service.name} company India, Codemistry`}
                jsonLd={[
                    organizationLd(),
                    breadcrumbLd([
                        { name: 'Home', url: SITE_INFO.url + '/' },
                        { name: 'Services', url: SITE_INFO.url + '/services' },
                        { name: service.name, url: canonical },
                    ]),
                    serviceLd(service, canonical),
                ]}
            />

            {/* ═══ HERO — Two Column layout ═══ */}
            <section className="pt-20 pb-4 bg-white">
                <div className="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10">


                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-y-4 lg:gap-10 pb-6 items-start">

                        {/* Main Grid: On mobile flex-col, on desktop grid */}

                        {/* 1. Cover Image (Top left on desktop, Top on mobile) */}
                        {service.cover_image_path && (
                            <div className="lg:col-span-7 order-1 lg:order-1 rounded-3xl overflow-hidden mb-4 lg:mb-0 border border-charcoal-100 shadow-sm h-fit">
                                <img src={service.cover_image_path.startsWith('http') ? service.cover_image_path : `${api.defaults.baseURL.replace('/api', '')}/storage/${service.cover_image_path}`} alt={service.name} className="w-full h-64 md:h-80 object-cover" />
                            </div>
                        )}

                        {/* 2. Title & Text (Bottom left on desktop, Bottom on mobile) */}
                        <div className="lg:col-span-7 lg:row-start-2 order-3 lg:order-3 flex flex-col pt-2 md:pt-6">
                            <h1 className="text-3xl md:text-5xl font-display font-extrabold text-charcoal-950 leading-tight mb-6">
                                {service.name}
                            </h1>

                            <p className="text-charcoal-600 text-base leading-relaxed mb-8">
                                {service.description}
                            </p>

                            {/* What's Included */}
                            {((activePlan?.features && activePlan.features.length > 0) ? activePlan.features : featureList)?.length > 0 && (
                                <div className="bg-white rounded-[1.5rem] p-6 lg:p-8 border border-charcoal-100 shadow-xl shadow-charcoal-950/[0.03]">
                                    <h3 className="text-charcoal-950 font-extrabold text-sm uppercase tracking-wide mb-6 flex items-center gap-2">
                                        <div className="w-8 h-8 rounded-full bg-brand-50 flex items-center justify-center">
                                            <CheckCircle2 className="w-4 h-4 text-brand-500" />
                                        </div>
                                        What's Included ({activePlan ? activePlan.plan_name : 'General'})
                                    </h3>
                                    <div className="flex flex-col gap-4">
                                        {((activePlan?.features && activePlan.features.length > 0) ? activePlan.features : featureList).map((feat, i) => (
                                            <div key={i} className="flex items-center gap-4 group">
                                                <div className="w-7 h-7 rounded-full bg-brand-50 border border-brand-100 flex items-center justify-center shrink-0 group-hover:bg-brand-500 group-hover:border-brand-500 transition-colors shadow-sm">
                                                    <CheckCircle2 className="w-4 h-4 text-brand-500 group-hover:text-white transition-colors" />
                                                </div>
                                                <span className="text-charcoal-700 text-[15px] font-semibold leading-snug">{feat}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* 3. Sticky Pricing Card (Right on desktop, Middle on mobile) */}
                        <div className="lg:col-span-5 lg:row-span-2 order-2 lg:order-2 lg:sticky lg:top-28 mb-4 lg:mb-0">
                            <div className="bg-white rounded-3xl shadow-2xl overflow-hidden">
                                <div className="p-7">
                                    {service.pricings && service.pricings.length > 1 ? (
                                        /* Multiple Plans */
                                        <>
                                            <div className="mb-5 relative" ref={dropdownRef}>
                                                <span className="text-charcoal-400 text-[11px] uppercase tracking-widest font-extrabold block mb-2.5 ps-1">Select Plan</span>
                                                <div
                                                    onClick={() => setIsDropdownOpen(!isDropdownOpen)}
                                                    className={`w-full bg-white border ${isDropdownOpen ? 'border-brand-500 ring-4 ring-brand-500/10' : 'border-charcoal-200 hover:border-brand-300'} text-charcoal-900 text-[15px] font-bold rounded-2xl px-5 py-3.5 pr-12 transition-all cursor-pointer shadow-sm relative flex items-center select-none`}
                                                >
                                                    <span className="truncate">{activePlan ? activePlan.plan_name : 'Select Plan'}</span>
                                                    <div className="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center text-charcoal-400 transition-colors pointer-events-none">
                                                        <ChevronDown className={`w-5 h-5 transition-transform duration-300 ${isDropdownOpen ? 'rotate-180 text-brand-500' : ''}`} />
                                                    </div>
                                                </div>

                                                {/* Custom Dropdown Options */}
                                                <div className={`absolute top-full left-0 right-0 mt-2 bg-white border border-charcoal-200 rounded-2xl shadow-[0_15px_40px_-5px_rgba(0,0,0,0.15)] overflow-hidden transition-all duration-300 z-[60] origin-top flex flex-col ${isDropdownOpen ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-0 pointer-events-none'}`}>
                                                    {service.pricings.map((p) => {
                                                        const isSelected = String(selectedPlanId) === String(p.id);
                                                        return (
                                                            <div
                                                                key={p.id}
                                                                onClick={() => {
                                                                    setSelectedPlanId(p.id);
                                                                    setIsDropdownOpen(false);
                                                                }}
                                                                className={`px-5 py-4 cursor-pointer font-semibold text-sm transition-colors border-b last:border-b-0 border-charcoal-100 flex items-center justify-between ${isSelected
                                                                    ? 'bg-brand-50 text-brand-800'
                                                                    : 'text-charcoal-700 hover:bg-brand-50 hover:text-brand-700'
                                                                    }`}
                                                            >
                                                                <span>{p.plan_name}</span>
                                                                <span className={`text-[11px] font-bold px-2 py-0.5 rounded-full ${isSelected ? 'bg-brand-200/50 text-brand-700' : 'bg-charcoal-100 text-charcoal-500'}`}>
                                                                    {p.end_price ? `${formatPrice(p.price)} - ${formatPrice(p.end_price)}` : formatPrice(p.price)}
                                                                </span>
                                                            </div>
                                                        );
                                                    })}
                                                </div>
                                            </div>

                                            <div className="flex flex-col gap-1 mb-4 mt-4">
                                                <span className="text-charcoal-400 text-xs uppercase tracking-wide font-semibold mb-1">
                                                    Price Range
                                                </span>
                                                <span className="text-3xl lg:text-4xl font-display font-extrabold text-charcoal-950 tracking-tight">
                                                    {(() => {
                                                        const activePlan = service.pricings.find(p => String(p.id) === String(selectedPlanId)) || service.pricings[0];
                                                        if (!activePlan) return formatPrice(service.full_price || 0);

                                                        const startPrice = activePlan.price;
                                                        const endPrice = activePlan.end_price;

                                                        return endPrice
                                                            ? `${formatPrice(startPrice)} - ${formatPrice(endPrice)}`
                                                            : formatPrice(startPrice);
                                                    })()}
                                                </span>
                                            </div>

                                            <div className="bg-brand-50 rounded-xl p-4 mt-4 mb-5 border border-brand-100">
                                                <p className="text-brand-800 text-sm font-medium mb-2">
                                                    <strong>25%</strong> need to pay advance and rest of the amount after website live.
                                                </p>
                                                <p className="text-green-700 text-sm font-medium flex items-center gap-1.5">
                                                    <CheckCircle2 className="w-4 h-4" /> 5% discount upon full payment.
                                                </p>
                                            </div>

                                            <p className="text-xs text-charcoal-400 mb-5">{service.pricings.length} pricing plans available — view all below</p>
                                        </>
                                    ) : displayFullPrice > 0 ? (
                                        <>
                                            <p className="text-charcoal-400 text-xs uppercase tracking-wide font-semibold mb-1">
                                                Price
                                            </p>
                                            <div className="flex flex-col gap-1 mb-2">
                                                <div className="flex items-baseline gap-2 flex-wrap">
                                                    <span className="text-3xl md:text-3xl lg:text-4xl font-display font-extrabold text-charcoal-950">
                                                        {formatPrice(displayFullPrice)}
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="bg-brand-50 rounded-xl p-4 mt-4 mb-5 border border-brand-100">
                                                <p className="text-brand-800 text-sm font-medium mb-2">
                                                    <strong>25%</strong> need to pay advance and rest of the amount after website live.
                                                </p>
                                                <p className="text-green-700 text-sm font-medium flex items-center gap-1.5">
                                                    <CheckCircle2 className="w-4 h-4" /> 5% discount upon full payment.
                                                </p>
                                            </div>
                                        </>
                                    ) : (
                                        <div className="mb-4">
                                            <p className="text-charcoal-400 text-xs uppercase tracking-wide font-semibold mb-1">Pricing</p>
                                            <p className="text-2xl font-display font-bold text-charcoal-950">Custom Quote</p>
                                            <p className="text-xs text-charcoal-400 mt-1">Contact us for a tailored estimate</p>
                                        </div>
                                    )}

                                    <a
                                        href={whatsappServiceUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="flex items-center justify-center gap-2 w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-brand-500/20 text-base"
                                    >
                                        <Phone className="w-4 h-4" /> Contact Us Now
                                    </a>

                                    {/* Trust indicators */}
                                    <div className="mt-5 pt-5 border-t border-charcoal-100 grid grid-cols-3 gap-3 text-center">
                                        {[
                                            { icon: Shield, label: 'Quality Assured' },
                                            { icon: Clock, label: 'On-time Delivery' },
                                            { icon: Users, label: '25+ Developers' },
                                        ].map(({ icon: Icon, label }) => (
                                            <div key={label} className="flex flex-col items-center gap-1">
                                                <div className="w-8 h-8 rounded-full bg-brand-50 flex items-center justify-center">
                                                    <Icon className="w-4 h-4 text-brand-500" />
                                                </div>
                                                <span className="text-[10px] text-charcoal-500 font-medium leading-tight">{label}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* ═══ HOW WE WORK TIMELINE ═══ */}
            <section className="py-24 relative px-5 sm:px-6 lg:px-10">
                <div className="max-w-7xl mx-auto">
                    <div className="text-center mb-20">
                        <p className="text-brand-600 font-bold tracking-wider uppercase text-xs mb-3">How We Work</p>
                        <h2 className="text-3xl md:text-5xl font-display font-extrabold text-charcoal-950">A proven, transparent process.</h2>
                    </div>

                    <div className="relative">
                        {/* Desktop Horizontal Line */}
                        <div className="hidden md:block absolute top-[24px] left-[10%] right-[10%] h-px bg-brand-500/20"></div>

                        <div className="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-6">
                            {(service.process_steps?.length > 0 ? service.process_steps : [
                                { title: 'Discovery', description: 'We dive deep into your requirements, target audience, and business goals to form a solid strategy.' },
                                { title: 'UI/UX Design', description: 'Our design team creates beautiful, intuitive wireframes and high-fidelity mockups for your approval.' },
                                { title: 'Development', description: 'We write clean, secure, and scalable code following agile methodologies and regular sprints.' },
                                { title: 'Launching', description: 'Rigorous quality assurance testing followed by a smooth deployment and post-launch support.' }
                            ]).map((step, idx) => (
                                <div key={idx} className="relative pl-12 md:pl-0">
                                    {/* Mobile vertical line */}
                                    <div className="md:hidden absolute left-5 top-12 bottom-[-3rem] w-px bg-brand-500/30"></div>

                                    <div className="md:text-center">
                                        <div className="w-12 h-12 rounded-full border-4 border-charcoal-50 bg-brand-50 shadow-lg flex items-center justify-center text-brand-600 font-bold font-display text-xl mb-4 relative z-10 md:mx-auto absolute left-[-6px] top-0 md:static">
                                            {idx + 1}
                                        </div>
                                        <h3 className="text-xl font-bold text-charcoal-950 mb-2">{step.title}</h3>
                                        <p className="text-charcoal-600 leading-relaxed text-sm md:text-base">{step.description}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* ═══ PRICING PLANS (Multiple) ═══ */}
            {service.pricings && service.pricings.length > 0 && (
                <section className="py-20 px-0 sm:px-6 lg:px-10 overflow-hidden">
                    <div className="max-w-7xl mx-auto">
                        <div className="text-center mb-12 px-5 sm:px-0">
                            <p className="text-brand-600 font-bold text-xs tracking-widest uppercase mb-3">Pricing Plans</p>
                            <h2 className="text-3xl md:text-5xl font-display font-extrabold text-charcoal-950">Choose Your Plan</h2>
                        </div>

                        <div className={`flex overflow-x-auto pb-10 pt-4 px-5 sm:px-0 sm:grid sm:overflow-visible sm:pb-0 gap-5 sm:gap-7 snap-x snap-mandatory hide-scrollbar max-w-6xl mx-auto ${service.pricings.length === 1 ? 'sm:grid-cols-1 sm:max-w-md' : service.pricings.length === 2 ? 'sm:grid-cols-1 md:grid-cols-2 sm:max-w-2xl' : 'sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3'}`}>
                            {service.pricings.map((plan) => {
                                const isPopular = plan.is_popular;
                                return (
                                    <div key={plan.id} className={`snap-center w-[85vw] max-w-sm sm:w-auto sm:max-w-none shrink-0 relative flex flex-col rounded-3xl p-8 transition-all duration-300 ${isPopular ? 'bg-charcoal-950 border-2 border-brand-500 shadow-2xl shadow-brand-500/20' : 'bg-white border border-charcoal-200 hover:border-brand-300 hover:shadow-lg'}`}>
                                        {isPopular && (
                                            <div className="absolute -top-4 left-1/2 -translate-x-1/2 bg-brand-500 text-white font-bold text-xs uppercase tracking-widest px-5 py-1.5 rounded-full flex items-center gap-1.5 shadow-lg whitespace-nowrap">
                                                <Star className="w-3 h-3 fill-white" /> Most Popular
                                            </div>
                                        )}

                                        <div className={`inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full mb-4 self-start ${isPopular ? 'bg-brand-500/20 text-brand-300' : 'bg-charcoal-100 text-charcoal-500'}`}>
                                            <Package className="w-3 h-3" /> {plan.plan_name}
                                        </div>

                                        <div className="mb-2">
                                            <div className="flex items-baseline gap-2 flex-wrap mb-1">
                                                <span className={`text-4xl font-display font-extrabold ${isPopular ? 'text-white' : 'text-charcoal-950'}`}>
                                                    {plan.end_price ? `${formatPrice(plan.price)} - ${formatPrice(plan.end_price)}` : formatPrice(plan.price)}
                                                </span>
                                            </div>

                                            <div className={`rounded-xl p-3 my-3 text-sm font-medium ${isPopular ? 'bg-white/10 border-white/20 text-white/90' : 'bg-brand-50 border-brand-100 text-brand-800'}`}>
                                                <p className="mb-1.5"><strong>25%</strong> need to pay advance and rest of the amount after website live.</p>
                                                <p className={`flex items-center gap-1.5 text-xs ${isPopular ? 'text-green-400' : 'text-green-700'}`}>
                                                    <CheckCircle2 className="w-3.5 h-3.5" /> 5% discount upon full payment.
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex-1 space-y-3 mb-8 mt-2">
                                            {plan.features?.map((f, i) => (
                                                <div key={i} className="flex items-start gap-3">
                                                    <div className={`w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5 ${isPopular ? 'bg-brand-500' : 'bg-brand-100'}`}>
                                                        <CheckCircle2 className={`w-3 h-3 ${isPopular ? 'text-white' : 'text-brand-600'}`} />
                                                    </div>
                                                    <span className={`text-sm leading-snug ${isPopular ? 'text-charcoal-300' : 'text-charcoal-600'}`}>{f}</span>
                                                </div>
                                            ))}
                                            {(!plan.features || plan.features.length === 0) && (
                                                <p className={`text-sm ${isPopular ? 'text-charcoal-400' : 'text-charcoal-400'}`}>Contact for full feature details.</p>
                                            )}
                                        </div>

                                        <a
                                            href={`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(
                                                `Hi Codemistry, I want to get started with ${service.name} - ${plan.plan_name} plan${plan.end_price ? ` (${formatPrice(plan.price)} - ${formatPrice(plan.end_price)})` : ` (${formatPrice(plan.price)})`}. Please guide me.`
                                            )}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className={`text-center w-full py-3.5 rounded-2xl font-bold text-sm transition-all ${isPopular ? 'bg-brand-500 text-white hover:bg-brand-400' : 'bg-charcoal-950 text-white hover:bg-charcoal-800'}`}
                                        >
                                            Get Started →
                                        </a>
                                    </div>
                                );
                            })}
                        </div>

                        <div className="mt-12 max-w-2xl mx-auto bg-white border border-charcoal-100 rounded-2xl p-5 flex items-center gap-4">
                            <div className="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center shrink-0">
                                <Shield className="w-6 h-6 text-amber-600" />
                            </div>
                            <div className="text-left">
                                <p className="text-charcoal-900 font-bold text-sm mb-0.5 whitespace-nowrap">Ownership & Hosting Policy</p>
                                <p className="text-charcoal-500 text-[13px] leading-relaxed">
                                    <strong>Domain and Hosting costs are not included</strong> in the service prices above. We help you setup everything in <strong>your own accounts</strong> so you maintain 100% ownership.
                                </p>
                            </div>
                        </div>

                    </div>
                </section>
            )}

            {/* ═══ FAQs ═══ */}
            {service.faq && service.faq.length > 0 && (
                <section className="py-20 px-5 sm:px-6 lg:px-10">
                    <div className="max-w-3xl mx-auto">
                        <div className="text-center mb-12">
                            <p className="text-brand-600 font-semibold text-xs tracking-widest uppercase mb-3">FAQ</p>
                            <h2 className="text-3xl font-display font-bold text-charcoal-950">Frequently Asked Questions</h2>
                        </div>
                        <div className="space-y-3">
                            {service.faq.map((faq, idx) => {
                                const isOpen = openFaq === idx;
                                return (
                                    <div key={idx} className={`border rounded-2xl overflow-hidden transition-all duration-200 ${isOpen ? 'border-brand-200 bg-white shadow-sm' : 'border-charcoal-200 bg-white hover:border-brand-200'}`}>
                                        <button onClick={() => setOpenFaq(isOpen ? null : idx)} className="w-full text-left px-6 py-5 flex items-center justify-between gap-4">
                                            <span className="font-semibold text-charcoal-900 text-base">{faq.q}</span>
                                            <div className={`shrink-0 w-8 h-8 rounded-full flex items-center justify-center transition-colors ${isOpen ? 'bg-brand-500 text-white' : 'bg-charcoal-100 text-charcoal-400'}`}>
                                                <ChevronDown className={`w-4 h-4 transition-transform duration-300 ${isOpen ? 'rotate-180' : ''}`} />
                                            </div>
                                        </button>
                                        <div className={`px-6 overflow-hidden transition-all duration-300 ${isOpen ? 'max-h-96 pb-6 opacity-100' : 'max-h-0 opacity-0'}`}>
                                            <p className="text-charcoal-600 leading-relaxed text-sm">{faq.a}</p>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </section>
            )}

            {/* ═══ CTA ═══ */}
            <section className="relative py-14 md:py-20 px-5 sm:px-6 lg:px-10 overflow-hidden bg-charcoal-950 border-t border-white/5">
                {/* Parallax Background */}
                <div
                    ref={registerParallax}
                    data-speed="0.08"
                    className="absolute inset-0 z-0 will-change-transform scale-115"
                    style={{
                        backgroundImage: `url(${service.cta_image_path
                            ? (service.cta_image_path.startsWith('http') ? service.cta_image_path : `${api.defaults.baseURL.replace('/api', '')}/storage/${service.cta_image_path}`)
                            : 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1600&q=80'})`,
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        opacity: 0.4
                    }}
                />

                {/* Overlay for readability */}
                <div className="absolute inset-0 z-[1] bg-charcoal-950/70" />

                {/* Content */}
                <div className="max-w-4xl mx-auto text-center relative z-10 py-6 md:py-8">
                    <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-white/10 bg-white/5 mb-8 backdrop-blur-md">
                        <span className="w-2 h-2 rounded-full bg-brand-500 animate-pulse" />
                        <span className="text-white/80 text-xs sm:text-sm font-medium tracking-wide uppercase">Let's Talk Project</span>
                    </div>

                    <h2 className="text-4xl md:text-6xl font-display font-extrabold text-white mb-6 leading-tight drop-shadow-2xl">
                        Ready to build something <br />
                        <span className="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-brand-300">extraordinary?</span>
                    </h2>

                    <p className="text-white/70 mb-12 text-lg md:text-xl max-w-2xl mx-auto font-medium leading-relaxed">
                        Let's discuss your {service.name} project and build something amazing together.
                    </p>

                    <div className="flex items-center justify-center">
                        <a
                            href={`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(`Hi Codemistry, I want to discuss my ${service.name} project. Please contact me.`)}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="group relative inline-flex items-center gap-3 bg-white text-charcoal-950 font-bold px-10 py-5 rounded-full hover:bg-brand-50 transition-all shadow-[0_0_40px_rgba(255,255,255,0.2)] hover:shadow-[0_0_50px_rgba(255,255,255,0.3)] hover:-translate-y-1"
                        >
                            <Phone className="w-5 h-5 text-brand-600" />
                            Contact Us Now
                            <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </a>
                    </div>
                </div>

                {/* Decorative Elements */}
                <div className="absolute -bottom-20 -left-20 w-64 h-64 bg-brand-500/10 rounded-full blur-[100px] pointer-events-none" />
                <div className="absolute -top-20 -right-20 w-64 h-64 bg-brand-600/10 rounded-full blur-[100px] pointer-events-none" />
            </section>
        </div>
    );
};

export default ServiceDetails;
