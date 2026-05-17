import { useEffect, useRef, useCallback, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import {
    ArrowRight, Globe, Smartphone, Database, Brain, Layers, RefreshCcw,
    ChevronRight, ChevronLeft, Users, Code2, Cpu, Palette, Quote, Star,
    Briefcase, Trophy, Clock, TrendingUp, Monitor, Server, RefreshCw,
    BookOpen, Calendar, ChevronDown
} from 'lucide-react';
import Seo from '../components/Seo';
import { organizationLd, localBusinessLd, websiteLd, faqPageLd, SITE_INFO } from '../seo/structuredData';

/* ─── Scroll Reveal Hook ─── */
const useScrollReveal = () => {
    useEffect(() => {
        const observer = new IntersectionObserver(
            (entries) => entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('visible'); }),
            { threshold: 0.1 }
        );
        document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .stagger-children').forEach((el) => observer.observe(el));
        return () => observer.disconnect();
    }, []);
};

/* ─── Parallax Hook ─── rAF-batched, single read of viewport per frame */
const useParallax = () => {
    const elementsRef = useRef([]);
    const register = useCallback((el) => {
        if (el && !elementsRef.current.includes(el)) elementsRef.current.push(el);
    }, []);
    useEffect(() => {
        let ticking = false;
        const update = () => {
            const viewportH = window.innerHeight;
            const scrollY = window.scrollY;
            elementsRef.current.forEach((el) => {
                // Cache offsetTop once per element; refreshed on resize
                if (el._cachedTop == null) {
                    el._cachedTop = el.getBoundingClientRect().top + scrollY;
                }
                const top = el._cachedTop - scrollY;
                const speed = parseFloat(el.dataset.speed || '0.15');
                const yOffset = (top - viewportH / 2) * speed;
                el.style.transform = `translate3d(0, ${yOffset}px, 0)`;
            });
            ticking = false;
        };
        const onScroll = () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        };
        const onResize = () => {
            // Invalidate cached positions on layout-affecting resize
            elementsRef.current.forEach((el) => { el._cachedTop = null; });
            onScroll();
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onResize, { passive: true });
        update();
        return () => {
            window.removeEventListener('scroll', onScroll);
            window.removeEventListener('resize', onResize);
        };
    }, []);
    return register;
};

/* ─── Count Up Hook ─── */
const useCountUp = (end, duration = 2000) => {
    const [count, setCount] = useState(0);
    const ref = useRef(null);
    const counted = useRef(false);

    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting && !counted.current) {
                    counted.current = true;
                    const startTime = performance.now();
                    const animate = (now) => {
                        const progress = Math.min((now - startTime) / duration, 1);
                        const eased = 1 - Math.pow(1 - progress, 3);
                        setCount(eased * end);
                        if (progress < 1) requestAnimationFrame(animate);
                    };
                    requestAnimationFrame(animate);
                }
            },
            { threshold: 0.3 }
        );
        if (ref.current) observer.observe(ref.current);
        return () => observer.disconnect();
    }, [end, duration]);

    return { count, ref };
};

const CountUpCard = ({ value, suffix = '', label, Icon, color, transparent }) => {
    const numericValue = parseFloat(value);
    const { count, ref } = useCountUp(isNaN(numericValue) ? 0 : numericValue, 2200);
    const decimalPlaces = (value.toString().split('.')[1] || '').length;
    const hasDecimal = decimalPlaces > 0;
    const displaySuffix = value.replace(/[0-9.]/g, '') || suffix;
    const displayValue = hasDecimal ? count.toFixed(decimalPlaces) : Math.floor(count);

    if (transparent) {
        return (
            <div ref={ref} className="bg-white/[0.12] border border-white/15 rounded-2xl p-5 group hover:bg-white/[0.18] hover:-translate-y-1 transition-all duration-300">
                <div className={`w-10 h-10 rounded-xl bg-gradient-to-br ${color} flex items-center justify-center mb-3`}>
                    <Icon className="w-5 h-5 text-white" />
                </div>
                <div className="text-3xl font-display font-extrabold text-white leading-none">{displayValue}{displaySuffix}</div>
                <div className="text-[11px] text-white/70 font-semibold mt-1.5 uppercase tracking-wider">{label}</div>
            </div>
        );
    }

    return (
        <div ref={ref} className="bg-white/[0.12] border border-white/15 rounded-xl px-2 py-3 text-center">
            <Icon className="w-4 h-4 text-brand-400 mx-auto mb-1" />
            <div className="text-sm sm:text-lg font-display font-extrabold text-white">{displayValue}{displaySuffix}</div>
            <div className="text-[9px] sm:text-[10px] text-white/70 font-medium">{label}</div>
        </div>
    );
};

/* ─── Data ─── */
const techLogos = [
    { name: 'React', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg' },
    { name: 'Next.js', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nextjs/nextjs-original.svg' },
    { name: 'Laravel', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg' },
    { name: 'Node.js', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg' },
    { name: 'Flutter', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/flutter/flutter-original.svg' },
    { name: 'Python', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg' },
    { name: 'AWS', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/amazonwebservices/amazonwebservices-plain-wordmark.svg' },
    { name: 'Docker', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg' },
    { name: 'MongoDB', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mongodb/mongodb-original.svg' },
    { name: 'PostgreSQL', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postgresql/postgresql-original.svg' },
    { name: 'TypeScript', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg' },
    { name: 'Tailwind', logo: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg' },
];

const testimonials = [
    { name: 'Rajesh Kumar', role: 'Founder, KumarTech Solutions', text: 'Codemistry transformed our outdated website into a blazing-fast modern platform. Our leads increased by 3x within the first month. Absolutely phenomenal work and great communication throughout the project.', avatar: 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=150&q=80', rating: 5 },
    { name: 'Anita Desai', role: 'CEO, Desai Fashion House', text: 'The custom CRM they built for us has completely streamlined our operations. What used to take hours now takes minutes. Their attention to detail and understanding of our workflow is remarkable.', avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&q=80', rating: 5 },
    { name: 'Suresh Menon', role: 'CTO, DataBridge Analytics', text: 'Working with Codemistry felt like having an in-house dev team. They understood our vision perfectly and delivered a product that exceeded all our expectations within the timeline.', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&q=80', rating: 5 },
    { name: 'Kavitha Nair', role: 'Director, GreenRoot Organics', text: 'Our e-commerce platform handles thousands of orders daily without a single hiccup. The AI chatbot they integrated has reduced our support tickets by 60%. A true game-changer for our business.', avatar: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&q=80', rating: 5 },
    { name: 'Amit Verma', role: 'Managing Director, BuildSmart Infra', text: 'From concept to launch in just 6 weeks! Codemistry delivered a polished mobile app that our field engineers love. Their ongoing support and quick response time has been exceptional.', avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&q=80', rating: 5 },
];

/* ─── FAQ Data (drives both UI + FAQPage JSON-LD rich snippet) ─── */
const HOME_FAQS = [
    {
        question: 'How much does a website cost in India?',
        answer: 'A basic business website starts from ₹8,000–₹25,000. A custom web application or e-commerce store typically ranges from ₹30,000–₹1,50,000 depending on features, design complexity, and integrations. Codemistry offers transparent INR pricing with no hidden charges.',
    },
    {
        question: 'How long does it take to build a website or app?',
        answer: 'A standard business website takes 1–2 weeks. A custom web application or e-commerce platform takes 3–8 weeks. Mobile apps (iOS/Android) generally take 6–12 weeks. We share weekly demos so you always know the progress.',
    },
    {
        question: 'Do you work with startups and small businesses in India?',
        answer: 'Yes. Most of our clients are Indian startups, SMBs, and solo founders. We offer flexible payment plans, GST-compliant invoicing, and milestone-based billing to make quality development accessible.',
    },
    {
        question: 'What technologies do you use for web and app development?',
        answer: 'We use React, Next.js, and Laravel for web; Flutter and React Native for mobile apps; Node.js and Python for backend services; and AWS/VPS for hosting. All code is clean, scalable, and fully owned by you.',
    },
    {
        question: 'Can you redesign or update my existing website?',
        answer: 'Absolutely. We handle website redesigns, CMS migrations, speed optimizations, and ongoing maintenance. We can modernize any legacy site to meet current SEO and performance standards.',
    },
    {
        question: 'Do you provide website maintenance after launch?',
        answer: 'Yes. We offer monthly maintenance plans starting from ₹2,000/month covering security updates, content changes, backups, uptime monitoring, and priority support.',
    },
];

/* ─── FAQ Accordion ─── */
const FaqAccordion = ({ faqs }) => {
    const [open, setOpen] = useState(null);
    return (
        <div className="divide-y divide-charcoal-100">
            {faqs.map((faq, i) => (
                <div key={i}>
                    <button
                        onClick={() => setOpen(open === i ? null : i)}
                        className="w-full flex items-center justify-between gap-4 py-5 text-left"
                        aria-expanded={open === i}
                    >
                        <span className="text-base sm:text-lg font-display font-semibold text-charcoal-900">{faq.question}</span>
                        <ChevronDown className={`w-5 h-5 text-brand-500 shrink-0 transition-transform duration-300 ${open === i ? 'rotate-180' : ''}`} />
                    </button>
                    {open === i && (
                        <p className="pb-5 text-charcoal-600 text-sm sm:text-base leading-relaxed">{faq.answer}</p>
                    )}
                </div>
            ))}
        </div>
    );
};

const Home = () => {
    useScrollReveal();
    const registerParallax = useParallax();
    const [activeSlide, setActiveSlide] = useState(0);
    const [isSliding, setIsSliding] = useState(true);
    const [featuredServices, setFeaturedServices] = useState([]);
    const [servicesLoading, setServicesLoading] = useState(true);
    const [latestPosts, setLatestPosts] = useState([]);
    const slideInterval = useRef(null);
    const touchStartX = useRef(0);

    const fetchFeaturedServices = async () => {
        try {
            const response = await api.get('/services?featured=true');
            setFeaturedServices(response.data);
        } catch (error) {
            if (import.meta.env.DEV) console.error('Error fetching featured services:', error);
        } finally {
            setServicesLoading(false);
        }
    };

    const getIconForService = (name) => {
        const lowerName = name.toLowerCase();
        if (lowerName.includes('app')) return { Icon: Smartphone, color: 'text-emerald-500', bg: 'bg-emerald-500' };
        if (lowerName.includes('crm') || lowerName.includes('data')) return { Icon: Database, color: 'text-purple-500', bg: 'bg-purple-500' };
        if (lowerName.includes('ai') || lowerName.includes('intelligence')) return { Icon: Cpu, color: 'text-cyan-500', bg: 'bg-cyan-500' };
        if (lowerName.includes('manage') || lowerName.includes('host')) return { Icon: Server, color: 'text-pink-500', bg: 'bg-pink-500' };
        if (lowerName.includes('update') || lowerName.includes('maintain')) return { Icon: RefreshCw, color: 'text-amber-500', bg: 'bg-amber-500' };
        return { Icon: Monitor, color: 'text-brand-500', bg: 'bg-brand-500' };
    };

    const startAutoSwipe = useCallback(() => {
        if (slideInterval.current) clearInterval(slideInterval.current);
        slideInterval.current = setInterval(() => {
            setActiveSlide((prev) => prev + 1);
        }, 12000);
    }, []);

    useEffect(() => {
        startAutoSwipe();
        fetchFeaturedServices();
        api.get('/blog-posts/latest')
            .then(r => setLatestPosts(r.data?.data || []))
            .catch(() => setLatestPosts([]));
        return () => clearInterval(slideInterval.current);
    }, [startAutoSwipe]);

    const goToSlide = (idx) => { setActiveSlide(idx); startAutoSwipe(); };
    const nextSlide = () => {
        setActiveSlide((prev) => prev + 1);
        startAutoSwipe();
    };
    const prevSlide = () => {
        if (activeSlide === 0) {
            setIsSliding(false);
            setActiveSlide(testimonials.length - 1);
            requestAnimationFrame(() => setIsSliding(true));
        } else {
            setActiveSlide((prev) => prev - 1);
        }
        startAutoSwipe();
    };

    useEffect(() => {
        if (activeSlide === testimonials.length) {
            const timer = setTimeout(() => {
                setIsSliding(false);
                setActiveSlide(0);
                requestAnimationFrame(() => setIsSliding(true));
            }, 720);
            return () => clearTimeout(timer);
        }
    }, [activeSlide]);

    const handleTouchStart = (e) => { touchStartX.current = e.touches[0].clientX; };
    const handleTouchEnd = (e) => {
        const diff = touchStartX.current - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) { diff > 0 ? nextSlide() : prevSlide(); }
    };

    return (
        <div className="bg-white overflow-x-hidden">
            <Seo
                title="Web & App Development Company in India — Affordable, On-Time"
                description="Codemistry is an India-based web & app development company. Affordable INR pricing, GST-compliant, AI integrations, e-commerce, and more — for businesses across India."
                canonical={SITE_INFO.url + '/'}
                keywords="web development company India, app development India, ecommerce India, AI integration India, custom software India, hire web developer India, website development cost India, Codemistry"
                jsonLd={[organizationLd(), localBusinessLd(), websiteLd(), faqPageLd(HOME_FAQS)]}
            />

            {/* ═══════════════════════════════════════════════ */}
            {/* 1. HERO BANNER                                 */}
            {/* ═══════════════════════════════════════════════ */}
            <section className="relative min-h-[100svh] flex flex-col justify-center overflow-hidden">
                {/* Background image with slow zoom */}
                <div className="absolute inset-0">
                    <picture>
                        <source media="(max-width: 768px)" srcSet="/hero-mobile.webp" />
                        <img
                            src="/hero.webp"
                            alt="Codemistry team collaborating on software development project"
                            fetchpriority="high"
                            loading="eager"
                            decoding="async"
                            width="1024"
                            height="683"
                            className="w-full h-full object-cover hero-bg-zoom"
                        />
                    </picture>
                </div>
                <div className="absolute inset-0 bg-gradient-to-r from-charcoal-950/95 via-charcoal-950/80 to-charcoal-950/50"></div>

                <div className="relative z-10 w-full max-w-7xl mx-auto px-5 sm:px-6 lg:px-10 py-20">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">

                        {/* Left — Text */}
                        <div className="lg:col-span-7">
                            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-white/20 text-white/80 text-xs sm:text-sm font-medium mb-6 sm:mb-8 animate-fade-in-up backdrop-blur-sm bg-white/5">
                                <span className="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                                Software Development Company
                            </div>

                            <h1 className="text-4xl sm:text-5xl md:text-6xl lg:text-[4.5rem] font-display font-extrabold text-white leading-[1.08] mb-5 sm:mb-7 animate-fade-in-up" style={{ animationDelay: '0.1s' }}>
                                Transform Your
                                <br />
                                Ideas Into
                                <br />
                                <span className="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-brand-300">Powerful Software.</span>
                            </h1>

                            <p className="text-base sm:text-lg text-white/65 max-w-lg mb-8 sm:mb-10 leading-relaxed animate-fade-in-up" style={{ animationDelay: '0.2s' }}>
                                We build websites, mobile apps, CRM systems &amp; AI solutions that help Indian businesses scale faster and serve customers better.
                            </p>

                            <div className="flex flex-col sm:flex-row gap-3 sm:gap-4 animate-fade-in-up" style={{ animationDelay: '0.3s' }}>
                                <Link to="/contact" className="btn-white group text-base sm:text-lg justify-center">
                                    Start a project <ArrowRight className="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" />
                                </Link>
                                <Link to="/services" className="inline-flex items-center justify-center px-7 py-3.5 text-base sm:text-lg font-semibold rounded-full text-white border-2 border-white/25 hover:bg-white/10 transition-all">
                                    Our services
                                </Link>
                            </div>
                        </div>

                        {/* Right — Infographic Cards (transparent) */}
                        <div className="lg:col-span-5 hidden lg:block animate-fade-in-up" style={{ animationDelay: '0.4s' }}>
                            <div className="grid grid-cols-2 gap-4">
                                {[
                                    { value: '25+', label: 'Developers', Icon: Users, color: 'from-brand-500 to-brand-600' },
                                    { value: '100%', label: 'Satisfaction', Icon: TrendingUp, color: 'from-emerald-500 to-emerald-600' },
                                    { value: '1+', label: 'Years Experience', Icon: Clock, color: 'from-cyan-500 to-cyan-600' },
                                    { value: '4.8', label: 'Average Rating', Icon: Trophy, color: 'from-amber-500 to-amber-600' },
                                ].map((s, i) => (
                                    <CountUpCard key={i} {...s} transparent />
                                ))}
                            </div>

                            {/* Infographic bar */}
                            <div className="mt-4 bg-white/[0.08] backdrop-blur-md border border-white/15 rounded-2xl p-4 flex items-center gap-4">
                                <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center shrink-0">
                                    <TrendingUp className="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <div className="text-white font-display font-bold text-sm">3x Average Growth</div>
                                    <div className="text-white/70 text-xs">Our clients see measurable results within 30 days</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Mobile infographic (transparent) */}
                    <div className="lg:hidden grid grid-cols-4 gap-2 mt-10 animate-fade-in-up" style={{ animationDelay: '0.4s' }}>
                        {[
                            { value: '25+', label: 'Developers', Icon: Users, color: 'from-brand-500 to-brand-600' },
                            { value: '100%', label: 'Satisfaction', Icon: TrendingUp, color: 'from-emerald-500 to-emerald-600' },
                            { value: '1+', label: 'Years', Icon: Clock, color: 'from-cyan-500 to-cyan-600' },
                            { value: '4.8', label: 'Rating', Icon: Trophy, color: 'from-amber-500 to-amber-600' },
                        ].map((s, i) => (
                            <CountUpCard key={i} {...s} />
                        ))}
                    </div>
                </div>

                <div className="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-500 via-brand-400 to-brand-600 z-20"></div>
            </section>

            {/* ═══════════════════════════════════════════════ */}
            {/* 2. TECH MARQUEE                                */}
            {/* ═══════════════════════════════════════════════ */}
            <section className="py-5 sm:py-7 border-b border-charcoal-100 overflow-hidden bg-white">
                <div className="marquee-track">
                    {[...techLogos, ...techLogos].map((tech, i) => (
                        <div key={i} className="mx-5 sm:mx-9 flex items-center gap-2.5 whitespace-nowrap select-none shrink-0">
                            <img src={tech.logo} alt={tech.name} loading="lazy" decoding="async" width="32" height="32" className="w-7 h-7 sm:w-8 sm:h-8" />
                            <span className="text-charcoal-500 font-display font-semibold text-xs sm:text-sm">{tech.name}</span>
                        </div>
                    ))}
                </div>
            </section>

            {/* ═══════════════════════════════════════════════ */}
            {/* 3. OUR EXPERTISE                               */}
            {/* ═══════════════════════════════════════════════ */}
            <section className="cv-auto py-16 sm:py-24 md:py-28 px-5 sm:px-6 lg:px-10">
                <div className="max-w-7xl mx-auto">
                    <div className="flex flex-col md:flex-row items-center md:items-end justify-between gap-4 sm:gap-6 mb-10 sm:mb-14 reveal text-center md:text-left">
                        <div>
                            <p className="text-brand-600 font-semibold text-xs sm:text-sm tracking-wide uppercase mb-2 sm:mb-3">What we do</p>
                            <h2 className="text-3xl sm:text-4xl md:text-5xl font-display font-extrabold">Our Expertise</h2>
                        </div>
                        <Link to="/services" className="hidden md:flex text-brand-600 font-semibold items-center gap-1 hover:gap-2 transition-all text-sm">
                            View all services <ChevronRight className="w-4 h-4" />
                        </Link>
                    </div>
                    <div className="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5 stagger-children">
                        {[
                            { icon: Globe, title: 'Web Development', desc: 'Modern, fast, SEO-ready websites.', iconBg: 'from-orange-500 to-amber-500', border: 'hover:border-orange-200', badge: 'bg-orange-50 text-orange-600' },
                            { icon: Smartphone, title: 'App Development', desc: 'iOS & Android apps that scale.', iconBg: 'from-emerald-500 to-teal-500', border: 'hover:border-emerald-200', badge: 'bg-emerald-50 text-emerald-600' },
                            { icon: Database, title: 'Custom CRM', desc: 'Tailored systems for your workflow.', iconBg: 'from-violet-500 to-purple-500', border: 'hover:border-violet-200', badge: 'bg-violet-50 text-violet-600' },
                            { icon: Brain, title: 'AI Integration', desc: 'Smart automation & chatbots.', iconBg: 'from-sky-500 to-cyan-500', border: 'hover:border-sky-200', badge: 'bg-sky-50 text-sky-600' },
                            { icon: Layers, title: 'Website Management', desc: 'Hosting, updates & maintenance.', iconBg: 'from-rose-500 to-pink-500', border: 'hover:border-rose-200', badge: 'bg-rose-50 text-rose-600' },
                            { icon: RefreshCcw, title: 'Website Updation', desc: 'Modernize your legacy site.', iconBg: 'from-amber-500 to-yellow-500', border: 'hover:border-amber-200', badge: 'bg-amber-50 text-amber-600' },
                        ].map((s, i) => {
                            const Icon = s.icon;
                            return (
                                <Link to="/contact" key={i} className={`group bg-white border border-charcoal-100 ${s.border} rounded-2xl p-5 sm:p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300`}>
                                    <div className={`w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br ${s.iconBg} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform`}>
                                        <Icon className="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                                    </div>
                                    <h3 className="text-sm sm:text-lg font-display font-bold text-charcoal-950 mb-1">{s.title}</h3>
                                    <p className="text-charcoal-600 text-xs sm:text-sm mb-3">{s.desc}</p>
                                    <span className={`inline-flex items-center gap-1 text-xs font-semibold ${s.badge.split(' ')[1]} group-hover:gap-2 transition-all`}>
                                        Learn more <ChevronRight className="w-3 h-3" />
                                    </span>
                                </Link>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* ═══════════════════════════════════════════════ */}
            {/* 4. HOW WE WORK — Timeline Journey               */}
            {/* ═══════════════════════════════════════════════ */}
            <section className="cv-auto py-16 sm:py-24 md:py-32 px-5 sm:px-6 lg:px-10 bg-charcoal-950 overflow-hidden relative">
                {/* Subtle ambient glow blobs */}
                <div className="absolute top-20 left-1/4 w-72 h-72 bg-brand-500/[0.04] rounded-full blur-3xl pointer-events-none"></div>
                <div className="absolute bottom-20 right-1/4 w-96 h-96 bg-brand-400/[0.03] rounded-full blur-3xl pointer-events-none"></div>

                <div className="max-w-6xl mx-auto relative z-10">
                    {/* Centered heading */}
                    <div className="text-center mb-14 sm:mb-20 reveal">
                        <p className="text-brand-400 font-semibold text-xs sm:text-sm tracking-[0.25em] uppercase mb-3 sm:mb-4">Our Process</p>
                        <h2 className="text-3xl sm:text-4xl md:text-5xl font-display font-extrabold text-white">
                            How We Work
                        </h2>
                        <p className="text-white/70 text-sm sm:text-base mt-4 max-w-lg mx-auto">A proven process that turns your vision into reality — step by step.</p>
                    </div>

                    {/* Timeline */}
                    <div className="relative">
                        {/* Vertical line with glow */}
                        <div className="absolute top-0 bottom-0 left-5 md:left-1/2 w-px bg-gradient-to-b from-transparent via-brand-500/20 to-transparent md:-translate-x-px"></div>
                        <div className="absolute top-0 bottom-0 left-5 md:left-1/2 w-px bg-gradient-to-b from-transparent via-brand-500/30 to-transparent md:-translate-x-px timeline-line-glow"></div>

                        {[
                            { step: 'Step 01', title: 'Discovery & Understanding', desc: 'We take the time to deeply understand your business, target audience, goals, and challenges before writing a single line of code. This phase sets the foundation for everything that follows.', img: 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=700&q=80' },
                            { step: 'Step 02', title: 'Strategy & Planning', desc: 'Wireframes, system architecture, database design, tech stack selection, and project timelines — everything is meticulously mapped out and shared with you for approval.', img: 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=700&q=80' },
                            { step: 'Step 03', title: 'Design & Development', desc: 'Our team builds your product with clean, scalable code and pixel-perfect UI. You receive weekly demos and progress updates, so there are never any surprises.', img: 'https://images.unsplash.com/photo-1571171637578-41bc2dd41cd2?w=700&q=80' },
                            { step: 'Step 04', title: 'Launch & Support', desc: 'Rigorous testing, seamless deployment, performance optimization, and dedicated ongoing support to ensure your product grows with your business.', img: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=700&q=80' },
                        ].map((item, idx) => {
                            const isEven = idx % 2 === 0;
                            return (
                                <div key={idx} className={`relative flex flex-col md:flex-row items-start md:items-center gap-6 md:gap-0 mb-16 sm:mb-20 md:mb-28 last:mb-0 ${isEven ? '' : 'md:flex-row-reverse'}`}>

                                    {/* Timeline dot — glowing */}
                                    <div className="absolute left-5 md:left-1/2 top-0 md:top-1/2 -translate-x-1/2 md:-translate-y-1/2 z-10">
                                        <div className="w-4 h-4 rounded-full bg-brand-500 ring-4 ring-charcoal-950 timeline-dot"></div>
                                    </div>

                                    {/* Image side — with hover glow */}
                                    <div className={`w-full md:w-[calc(50%-2rem)] pl-12 md:pl-0 ${isEven ? 'md:pr-12' : 'md:pl-12'} ${isEven ? 'reveal-left' : 'reveal-right'}`}>
                                        <div className="relative group rounded-2xl overflow-hidden timeline-img-glow transition-shadow duration-500 border border-white/[0.06]">
                                            <img
                                                src={item.img.replace('w=700&q=80', 'w=560&q=55&fm=webp&auto=format')}
                                                alt={item.title}
                                                loading="lazy"
                                                decoding="async"
                                                width="700"
                                                height="288"
                                                className="w-full h-52 sm:h-64 md:h-72 object-cover group-hover:scale-105 transition-transform duration-700"
                                            />
                                            {/* Subtle gradient overlay on image */}
                                            <div className="absolute inset-0 bg-gradient-to-t from-charcoal-950/50 via-transparent to-transparent"></div>
                                            {/* Step badge with glow border */}
                                            <div className="absolute top-4 left-4 bg-charcoal-950/80 backdrop-blur-md text-brand-400 font-display font-bold text-xs px-4 py-2 rounded-lg tracking-wider uppercase border border-brand-500/20">
                                                {item.step}
                                            </div>
                                            {/* Step number large watermark */}
                                            <div className="absolute bottom-3 right-4 text-white/[0.04] font-display font-extrabold text-6xl sm:text-7xl select-none pointer-events-none">
                                                {String(idx + 1).padStart(2, '0')}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Content side — with card glow */}
                                    <div className={`w-full md:w-[calc(50%-2rem)] pl-12 md:pl-0 ${isEven ? 'md:pl-12' : 'md:pr-12'} ${isEven ? 'reveal-right' : 'reveal-left'}`}>
                                        <p className="text-brand-300 font-semibold text-[10px] sm:text-xs tracking-[0.2em] uppercase mb-2 sm:mb-3 flex items-center gap-2">
                                            <span className="w-8 h-px bg-gradient-to-r from-brand-500/60 to-transparent"></span>
                                            Milestone
                                        </p>
                                        <h3 className="text-xl sm:text-2xl md:text-3xl font-display font-bold text-white mb-3 sm:mb-4">{item.title}</h3>
                                        <div className="bg-white/[0.04] border border-white/10 rounded-xl p-5 sm:p-6 timeline-card hover:border-brand-500/20 transition-colors duration-500">
                                            <p className="text-white/60 text-sm sm:text-base leading-relaxed">{item.desc}</p>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* ═══════════════════════════════════════════════ */}
            {/* 5. PARALLAX BREAK                              */}
            {/* ═══════════════════════════════════════════════ */}
            <section
                className="relative h-[45vh] sm:h-[50vh] md:h-[60vh]"
                style={{
                    backgroundImage: "url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1600&q=80')",
                    backgroundAttachment: 'fixed', backgroundSize: 'cover', backgroundPosition: 'center',
                }}
            >
                <div className="absolute inset-0 bg-charcoal-950/60"></div>
                <div className="relative z-10 flex items-center justify-center h-full text-center px-5">
                    <div className="reveal-scale">
                        <h2 className="text-2xl sm:text-3xl md:text-5xl font-display font-extrabold text-white mb-4 max-w-2xl mx-auto">Real People. Real Code. Real Results.</h2>
                        <p className="text-white/60 text-sm sm:text-lg max-w-lg mx-auto mb-8">Every project is built from scratch, tailored to your unique needs.</p>
                        <Link to="/contact" className="btn-white group">
                            Talk to us <ArrowRight className="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </Link>
                    </div>
                </div>
            </section>

            {/* ═══════════════════════════════════════════════ */}
            {/* 6. SERVICE DETAILS                             */}
            {/* ═══════════════════════════════════════════════ */}
            <section className="cv-auto py-16 sm:py-24 md:py-28 px-5 sm:px-6 lg:px-10">
                <div className="max-w-7xl mx-auto">
                    <div className="mb-14 sm:mb-20 reveal text-center md:text-left">
                        <p className="text-brand-600 font-semibold text-xs sm:text-sm tracking-wide uppercase mb-2 sm:mb-3">Services in detail</p>
                        <h2 className="text-3xl sm:text-4xl md:text-5xl font-display font-extrabold mb-4">What We Deliver</h2>
                        <p className="text-charcoal-500 text-base sm:text-lg max-w-xl">End-to-end solutions — from design to deployment, we handle everything.</p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {servicesLoading ? (
                            <div className="col-span-full flex justify-center py-20"><div className="w-10 h-10 border-4 border-brand-200 border-t-brand-600 rounded-full animate-spin"></div></div>
                        ) : featuredServices.length > 0 ? (
                            featuredServices.map((service, index) => {
                                const { Icon, color, bg } = getIconForService(service.name);
                                return (
                                    <Link
                                        key={service.id}
                                        to={`/services/${service.slug}`}
                                        className="group bg-white rounded-[2rem] p-6 md:p-8 flex flex-col h-full border border-charcoal-100/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgb(0,0,0,0.08)] hover:-translate-y-2 transition-all duration-300 relative overflow-hidden"
                                        style={{ animationDelay: `${index * 0.1}s` }}
                                    >
                                        {/* Decorative Top Accent */}
                                        <div className={`absolute top-0 left-0 right-0 h-1.5 ${bg} opacity-10 group-hover:opacity-100 transition-opacity duration-300`}></div>

                                        {service.cover_image_path ? (
                                            <div className="w-full aspect-video rounded-2xl md:rounded-[1.5rem] overflow-hidden mb-6 relative">
                                                <img
                                                    src={service.cover_image_path}
                                                    alt={service.name}
                                                    loading="lazy"
                                                    decoding="async"
                                                    width="600"
                                                    height="338"
                                                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out"
                                                />
                                                <div className="absolute inset-0 bg-gradient-to-t from-charcoal-950/40 via-transparent to-transparent"></div>
                                                <div className={`absolute bottom-3 left-3 w-10 h-10 rounded-xl bg-white/90 backdrop-blur-md flex items-center justify-center ${color} shadow-lg`}>
                                                    <Icon className="w-5 h-5" />
                                                </div>
                                            </div>
                                        ) : (
                                            <div className={`w-14 h-14 rounded-2xl ${bg} text-white flex items-center justify-center mb-6 shadow-lg shadow-${bg.replace('bg-', '')}/30 group-hover:scale-110 transition-transform duration-300`}>
                                                <Icon className="w-7 h-7" />
                                            </div>
                                        )}

                                        <h3 className="text-xl md:text-2xl font-display font-bold text-charcoal-950 mb-3 group-hover:text-brand-600 transition-colors">{service.name}</h3>
                                        <p className="text-charcoal-500 text-sm leading-relaxed mb-6 line-clamp-3 md:line-clamp-2 flex-grow font-medium">{service.description}</p>

                                        <div className="pt-4 mt-auto border-t border-charcoal-100 flex items-center justify-between">
                                            <span className="text-charcoal-400 text-xs font-semibold tracking-wider uppercase">View Details</span>
                                            <div className="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center group-hover:bg-brand-600 transition-colors duration-300">
                                                <ArrowRight className="w-5 h-5 text-brand-600 group-hover:text-white transition-colors duration-300 group-hover:-rotate-45" />
                                            </div>
                                        </div>
                                    </Link>
                                );
                            })
                        ) : (
                            <div className="col-span-full text-center py-20 bg-charcoal-50 rounded-2xl border border-dashed border-charcoal-200">
                                <Code2 className="w-10 h-10 text-charcoal-300 mx-auto mb-3" />
                                <h3 className="font-medium text-charcoal-900 mb-2">No featured services yet.</h3>
                                <p className="text-charcoal-500 text-sm mb-4">Check back later or view all our services.</p>
                                <Link to="/services" className="btn-primary">View All Services</Link>
                            </div>
                        )}
                    </div>
                </div>
            </section>

            {/* ═══════════════════════════════════════════════ */}
            {/* 7. CLIENT REVIEWS                              */}
            {/* ═══════════════════════════════════════════════ */}
            <section className="cv-auto py-16 sm:py-24 md:py-28 px-5 sm:px-6 lg:px-10 bg-white overflow-hidden">
                <div className="max-w-7xl mx-auto">
                    <div className="flex flex-col md:flex-row items-center md:items-end justify-between gap-4 mb-10 sm:mb-14 reveal text-center md:text-left">
                        <div>
                            <p className="text-brand-600 font-semibold text-xs sm:text-sm tracking-wide uppercase mb-2 sm:mb-3">Testimonials</p>
                            <h2 className="text-3xl sm:text-4xl md:text-5xl font-display font-extrabold text-charcoal-950">What Our Clients Say</h2>
                        </div>
                        <div className="flex gap-2">
                            <button onClick={prevSlide} aria-label="Previous testimonial" className="w-11 h-11 sm:w-12 sm:h-12 rounded-full border-2 border-charcoal-200 flex items-center justify-center text-charcoal-600 hover:text-charcoal-950 hover:border-charcoal-950 transition-all duration-300">
                                <ChevronLeft className="w-5 h-5" aria-hidden="true" />
                            </button>
                            <button onClick={nextSlide} aria-label="Next testimonial" className="w-11 h-11 sm:w-12 sm:h-12 rounded-full border-2 border-charcoal-200 flex items-center justify-center text-charcoal-600 hover:text-charcoal-950 hover:border-charcoal-950 transition-all duration-300">
                                <ChevronRight className="w-5 h-5" aria-hidden="true" />
                            </button>
                        </div>
                    </div>

                    {/* Swipeable carousel */}
                    <div
                        className="overflow-hidden rounded-2xl sm:rounded-3xl"
                        onTouchStart={handleTouchStart}
                        onTouchEnd={handleTouchEnd}
                    >
                        <div
                            className={`flex ${isSliding ? 'transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]' : ''}`}
                            style={{ transform: `translateX(-${activeSlide * 100}%)` }}
                        >
                            {[...testimonials, testimonials[0]].map((t, i) => (
                                <div key={i} className="w-full flex-shrink-0">
                                    <div className="bg-charcoal-50 rounded-2xl sm:rounded-3xl border border-charcoal-100 p-5 sm:p-8 md:p-10 mx-0.5">
                                        {/* Mobile: author on top, quote below. Desktop: side by side */}
                                        <div className="flex flex-col md:flex-row md:items-center gap-5 md:gap-10">
                                            {/* Author */}
                                            <div className="flex items-center gap-4 md:w-1/3 md:flex-col md:text-center md:border-l md:border-charcoal-200 md:pl-10 md:order-2">
                                                <div className="w-14 h-14 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-full bg-brand-600 text-white ring-4 ring-white shadow-lg flex items-center justify-center font-display font-extrabold text-xl sm:text-3xl">
                                                    {t.name?.charAt(0)?.toUpperCase() || 'U'}
                                                </div>
                                                <div>
                                                    <h3 className="text-charcoal-950 font-display font-bold text-sm sm:text-base">{t.name}</h3>
                                                    <p className="text-charcoal-600 text-xs sm:text-sm mt-0.5">{t.role}</p>
                                                    <div className="flex gap-0.5 mt-1.5 md:justify-center">
                                                        {[...Array(t.rating)].map((_, j) => (
                                                            <Star key={j} className="w-3.5 h-3.5 text-brand-400 fill-brand-400" />
                                                        ))}
                                                    </div>
                                                </div>
                                            </div>
                                            {/* Quote */}
                                            <div className="md:w-2/3 md:order-1">
                                                <Quote className="w-8 h-8 sm:w-10 sm:h-10 text-brand-500/20 mb-3" />
                                                <p className="text-charcoal-800 text-sm sm:text-lg md:text-xl leading-relaxed font-display">
                                                    "{t.text}"
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Dots — visible dot is tiny but clickable area is 44×44 for a11y */}
                    <div className="flex justify-center gap-1 mt-8 sm:mt-10" role="tablist" aria-label="Testimonial navigation">
                        {testimonials.map((t, i) => (
                            <button
                                key={i}
                                onClick={() => goToSlide(i)}
                                role="tab"
                                aria-selected={i === (activeSlide % testimonials.length)}
                                aria-label={`Go to testimonial by ${t.name}`}
                                className="w-11 h-11 flex items-center justify-center group"
                            >
                                <span aria-hidden="true" className={`rounded-full transition-all duration-300 ${i === (activeSlide % testimonials.length) ? 'w-8 h-2.5 bg-brand-500' : 'w-2.5 h-2.5 bg-charcoal-200 group-hover:bg-charcoal-300'}`} />
                            </button>
                        ))}
                    </div>
                </div>
            </section>

            {/* ═══════════════════════════════════════════════ */}
            {/* 7b. LATEST FROM THE BLOG                       */}
            {/* ═══════════════════════════════════════════════ */}
            {latestPosts.length > 0 && (
                <section className="px-4 sm:px-6 lg:px-10 py-16 sm:py-24 bg-charcoal-50/40">
                    <div className="max-w-7xl mx-auto">
                        <div className="flex items-end justify-between gap-6 mb-10 flex-wrap">
                            <div>
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-brand-100 text-brand-700 rounded-full text-xs font-bold uppercase tracking-wider">
                                    <BookOpen className="w-3.5 h-3.5" /> From the Blog
                                </span>
                                <h2 className="mt-3 text-3xl sm:text-4xl font-display font-bold text-charcoal-950">
                                    Insights for Indian Businesses
                                </h2>
                                <p className="mt-2 text-charcoal-500 max-w-2xl text-sm sm:text-base">
                                    Practical guides on web & app development cost in India, e-commerce setup, AI integration and more.
                                </p>
                            </div>
                            <Link
                                to="/blog"
                                className="inline-flex items-center gap-2 px-5 py-2.5 bg-charcoal-950 hover:bg-charcoal-800 text-white rounded-full text-sm font-semibold transition-colors"
                            >
                                View all articles <ArrowRight className="w-4 h-4" />
                            </Link>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {latestPosts.map((post) => (
                                <article key={post.id} className="bg-white rounded-2xl border border-charcoal-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all flex flex-col">
                                    <Link to={`/blog/${post.slug}`} className="block">
                                        <div className="aspect-[16/9] bg-gradient-to-br from-brand-100 to-brand-50 overflow-hidden">
                                            {post.cover_image_url ? (
                                                <img src={post.cover_image_url} alt={post.title} loading="lazy" decoding="async" width="640" height="360" className="w-full h-full object-cover" />
                                            ) : (
                                                <div className="w-full h-full flex items-center justify-center text-brand-700 text-3xl font-display font-bold opacity-40">CM</div>
                                            )}
                                        </div>
                                    </Link>
                                    <div className="p-5 flex flex-col flex-1">
                                        <div className="flex items-center gap-1.5 text-xs text-charcoal-500 mb-2">
                                            <Calendar className="w-3 h-3" />
                                            {post.published_at && new Date(post.published_at).toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric' })}
                                        </div>
                                        <h3 className="font-display font-bold text-charcoal-900 text-lg leading-tight">
                                            <Link to={`/blog/${post.slug}`} className="hover:text-brand-600 transition-colors">{post.title}</Link>
                                        </h3>
                                        {post.excerpt && <p className="text-charcoal-600 text-sm mt-2 line-clamp-3 flex-1">{post.excerpt}</p>}
                                        <Link to={`/blog/${post.slug}`} className="inline-flex items-center gap-1 mt-4 text-brand-600 text-sm font-semibold hover:gap-2 transition-all">
                                            Read article <ArrowRight className="w-4 h-4" />
                                        </Link>
                                    </div>
                                </article>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* ═══════════════════════════════════════════════ */}
            {/* 8. FAQ                                         */}
            {/* ═══════════════════════════════════════════════ */}
            <section className="cv-auto py-16 sm:py-24 px-5 sm:px-6 lg:px-10 bg-white">
                <div className="max-w-3xl mx-auto">
                    <div className="text-center mb-10 reveal">
                        <p className="text-brand-600 font-semibold text-xs sm:text-sm tracking-wide uppercase mb-2">FAQ</p>
                        <h2 className="text-3xl sm:text-4xl font-display font-extrabold text-charcoal-950">Common Questions</h2>
                        <p className="text-charcoal-500 mt-3 text-sm sm:text-base">Everything you need to know before starting your project with us.</p>
                    </div>
                    <div className="bg-white border border-charcoal-100 rounded-2xl px-5 sm:px-8 shadow-sm">
                        <FaqAccordion faqs={HOME_FAQS} />
                    </div>
                    <p className="text-center mt-6 text-sm text-charcoal-500">
                        More questions?{' '}
                        <Link to="/contact" className="text-brand-600 font-semibold hover:underline">Talk to our team →</Link>
                    </p>
                </div>
            </section>

            {/* ═══════════════════════════════════════════════ */}
            {/* 9. CTA                                         */}
            {/* ═══════════════════════════════════════════════ */}
            <section className="cv-auto px-4 sm:px-6 lg:px-10 py-16 sm:py-24">
                <div className="relative w-full max-w-7xl mx-auto rounded-[2.5rem] bg-charcoal-950 overflow-hidden shadow-2xl group min-h-[500px] flex flex-col lg:flex-row items-center justify-between px-6 sm:px-12 md:px-16 py-16 sm:py-24 border border-charcoal-800 gap-12 lg:gap-8">

                    {/* Animated Background Orbs */}
                    <div className="absolute inset-0 z-0 overflow-hidden opacity-60 pointer-events-none">
                        <div className="absolute top-[-20%] left-[-10%] w-[50%] h-[70%] bg-brand-600/40 rounded-full blur-[120px] mix-blend-screen animate-pulse" style={{ animationDuration: '8s' }}></div>
                        <div className="absolute bottom-[-20%] right-[-10%] w-[60%] h-[80%] bg-rose-600/30 rounded-full blur-[120px] mix-blend-screen animate-pulse" style={{ animationDuration: '10s' }}></div>
                        <div className="absolute top-[20%] right-[20%] w-[30%] h-[40%] bg-violet-600/30 rounded-full blur-[100px] mix-blend-screen animate-pulse" style={{ animationDuration: '7s' }}></div>
                    </div>

                    {/* Dotted Texture Overlay */}
                    <div className="absolute inset-0 z-0 opacity-[0.15] mix-blend-overlay pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] bg-[size:24px_24px]"></div>

                    {/* Left Side Content */}
                    <div className="relative z-10 flex-1 w-full text-center lg:text-left">
                        <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-white/10 bg-white/5 mb-8 shadow-inner">
                            <span className="relative flex h-2.5 w-2.5">
                                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                                <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-500"></span>
                            </span>
                            <span className="text-white/90 text-xs sm:text-sm font-medium tracking-wide">Available for new projects</span>
                        </div>

                        <h2 className="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-display font-extrabold tracking-tight text-white mb-6 leading-[1.15]">
                            Let's build <br className="hidden lg:block" />
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-brand-300 via-white to-orange-200">the future.</span>
                        </h2>

                        <p className="text-white/60 text-lg sm:text-xl max-w-xl mx-auto lg:mx-0 mb-10 leading-relaxed font-light">
                            Top-tier engineering meets exceptional design. Tell us about your project, and we'll get back with a strategic roadmap.
                        </p>

                        <div className="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            <Link to="/contact" className="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-charcoal-950 text-lg font-bold rounded-full bg-white hover:bg-brand-50 transition-all duration-300 hover:scale-105 hover:shadow-[0_0_30px_rgba(255,255,255,0.3)] group/btn">
                                Start Your Project
                                <div className="w-8 h-8 ml-3 rounded-full bg-charcoal-900 flex items-center justify-center group-hover/btn:bg-brand-500 transition-colors">
                                    <ArrowRight className="w-4 h-4 text-white group-hover/btn:translate-x-0.5 transition-transform" />
                                </div>
                            </Link>
                            <Link to="/services" className="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-white text-lg font-medium rounded-full bg-transparent hover:bg-white/5 border-2 border-transparent hover:border-white/10 transition-all duration-300">
                                Explore Services
                            </Link>
                        </div>
                    </div>

                    {/* Right Side Visuals (Abstract UI Elements floating in 3D space) */}
                    <div className="w-full lg:w-[450px] h-[300px] sm:h-[400px] relative hidden md:block perspective-1000 group-hover:scale-[1.02] transition-transform duration-700 z-10">
                        {/* Floating Element 1 (Top Right) */}
                        <div className="absolute top-4 right-4 w-64 p-5 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl transform rotate-12 hover:rotate-0 hover:scale-110 hover:z-20 transition-all duration-500 ease-out cursor-default delay-100">
                            <div className="flex gap-3 items-center mb-4">
                                <div className="w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-orange-500 flex items-center justify-center shadow-lg shadow-brand-500/30">
                                    <div className="w-4 h-4 text-white">✈</div>
                                </div>
                                <div>
                                    <div className="w-20 h-2 bg-white/40 rounded-full mb-2"></div>
                                    <div className="w-12 h-2 bg-white/20 rounded-full"></div>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <div className="w-full h-2 bg-white/10 rounded-full"></div>
                                <div className="w-4/5 h-2 bg-white/10 rounded-full"></div>
                            </div>
                        </div>

                        {/* Floating Element 2 (Bottom Left) */}
                        <div className="absolute bottom-4 left-4 w-72 p-6 rounded-2xl bg-charcoal-900/60 backdrop-blur-xl border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.5)] transform -rotate-6 hover:rotate-0 hover:scale-110 hover:z-20 transition-all duration-500 ease-out cursor-default">
                            <div className="flex justify-between items-center mb-5">
                                <div className="space-y-2 w-1/2">
                                    <div className="w-full h-3 bg-brand-500/80 rounded-full"></div>
                                    <div className="w-2/3 h-2 bg-white/30 rounded-full"></div>
                                </div>
                                <div className="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/50 text-xs font-mono">
                                    {'</>'}
                                </div>
                            </div>
                            <div className="w-full h-24 rounded-lg bg-gradient-to-r from-white/5 to-transparent border border-white/5 relative overflow-hidden">
                                <div className="absolute top-0 bottom-0 left-0 w-1/3 bg-gradient-to-r from-brand-500/20 to-transparent"></div>
                                <div className="absolute bottom-4 left-4 right-4 h-1 bg-white/10 rounded-full overflow-hidden">
                                    <div className="h-full bg-brand-400 w-2/3 rounded-full"></div>
                                </div>
                            </div>
                        </div>

                        {/* Center decoration ring */}
                        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 rounded-full border border-white/10 border-t-brand-500/50 animate-[spin_10s_linear_infinite]"></div>
                        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 rounded-full border border-white/5 border-b-rose-500/50 animate-[spin_7s_linear_infinite_reverse]"></div>
                    </div>
                </div>
            </section>
        </div>
    );
};

export default Home;
