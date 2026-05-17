import { Outlet, Link, useLocation } from 'react-router-dom';
import { useState, useEffect } from 'react';
import { Menu, X, Mail, Phone as PhoneIcon, MapPin, ArrowRight, ArrowUpRight, Instagram, Linkedin, Twitter, MessageCircle, ChevronDown, Home, Briefcase, Info, Phone, BookOpen } from 'lucide-react';
import Chatbot from './Chatbot';
import MobileBottomBar from './MobileBottomBar';

const Layout = () => {
    const [scrolled, setScrolled] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);
    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [openFooterSection, setOpenFooterSection] = useState(null);

    const toggleFooterSection = (section) => {
        setOpenFooterSection(openFooterSection === section ? null : section);
    };

    const location = useLocation();
    const isHome = location.pathname === '/';
    const isAbout = location.pathname === '/about';
    const isAiSupport = location.pathname === '/ai-support';
    const isTransparentPage = isHome || isAbout;

    useEffect(() => {
        try { setIsAuthenticated(!!localStorage.getItem('auth_token')); } catch { /* private mode — ignore */ }
        const onScroll = () => setScrolled(window.scrollY > 60);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, [location]);

    useEffect(() => setMobileOpen(false), [location]);

    const isTransparent = isTransparentPage && !scrolled;
    const whatsappNumber = '918910710136';
    const whatsappMessage = `Hi Codemistry, I visited your website. I am on this page: ${location.pathname}. Can we talk?`;
    const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(whatsappMessage)}`;

    const navLinks = [
        { to: '/', label: 'Home', icon: Home },
        { to: '/services', label: 'Services', icon: Briefcase },
        { to: '/blog', label: 'Blog', icon: BookOpen },
        { to: '/about', label: 'About', icon: Info },
        { to: '/contact', label: 'Contact', icon: Phone },
    ];

    return (
        <div className="min-h-screen flex flex-col bg-white">
            {/* Skip to main content — keyboard / screen-reader navigation */}
            <a href="#main-content" className="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[100] focus:px-4 focus:py-2 focus:bg-brand-600 focus:text-white focus:rounded-lg focus:text-sm focus:font-semibold">
                Skip to main content
            </a>

            {/* ──── HEADER ──── */}
            <header role="banner" className={`fixed w-full z-50 transition-all duration-500 ${isTransparent ? 'bg-transparent' : 'bg-white/90 backdrop-blur-xl shadow-md shadow-charcoal-950/[0.03] border-b border-charcoal-50'}`}>
                <div className="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10">
                    <div className="flex justify-between items-center h-14 md:h-16">
                        {/* Logo */}
                        <Link to="/" className="flex items-center gap-2.5 group">
                            <img src="/logo.png" alt="Codemistry Logo" className="h-[36px] md:h-[48px] w-auto object-contain transition-transform duration-300 group-hover:scale-105" />
                        </Link>

                        {/* Desktop Nav — Pill Style */}
                        <nav aria-label="Main navigation" className="hidden md:flex items-center">
                            <div className={`flex items-center gap-1 px-1.5 py-1.5 rounded-full transition-all duration-300 ${isTransparent ? 'bg-white/[0.08] backdrop-blur-sm' : 'bg-charcoal-50'}`}>
                                {navLinks.map((link) => (
                                    <Link key={link.to} to={link.to}
                                        aria-current={location.pathname === link.to ? 'page' : undefined}
                                        className={`text-sm font-medium px-4 py-1.5 rounded-full transition-all duration-200 ${isTransparent
                                            ? (location.pathname === link.to ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white hover:bg-white/10')
                                            : (location.pathname === link.to ? 'bg-white text-charcoal-950 shadow-sm' : 'text-charcoal-500 hover:text-charcoal-950')
                                            }`}
                                    >{link.label}</Link>
                                ))}
                            </div>
                        </nav>

                        {/* Desktop CTA - Removed as requested */}

                        {/* Mobile Menu Button */}
                        <button onClick={() => setMobileOpen(true)} aria-label="Open navigation menu" aria-expanded={mobileOpen} aria-controls="mobile-drawer" className={`md:hidden p-2 rounded-xl transition-all ${isTransparent ? 'text-white hover:bg-white/10' : 'text-charcoal-800 hover:bg-charcoal-50'}`}>
                            <Menu className="w-6 h-6" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </header>

            {/* ──── MOBILE SIDE DRAWER ──── */}
            {/* Overlay */}
            <div
                className={`fixed inset-0 z-[60] bg-charcoal-950/50 backdrop-blur-sm transition-opacity duration-300 md:hidden ${mobileOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'}`}
                onClick={() => setMobileOpen(false)}
            />
            {/* Drawer */}
            <div id="mobile-drawer" role="dialog" aria-modal="true" aria-label="Navigation menu" className={`fixed top-0 right-0 z-[70] h-full w-[280px] bg-white shadow-2xl transition-transform duration-400 ease-[cubic-bezier(0.16,1,0.3,1)] md:hidden flex flex-col ${mobileOpen ? 'translate-x-0' : 'translate-x-full'}`}>
                {/* Drawer Header */}
                <div className="flex items-center justify-between px-5 h-16 border-b border-charcoal-100">
                    <Link to="/" onClick={() => setMobileOpen(false)} className="flex items-center gap-2">
                        <img src="/logo.png" alt="Codemistry Logo" className="h-10 w-auto object-contain" />
                    </Link>
                    <button onClick={() => setMobileOpen(false)} aria-label="Close navigation menu" className="p-2 rounded-xl text-charcoal-400 hover:text-charcoal-950 hover:bg-charcoal-50 transition-all">
                        <X className="w-5 h-5" aria-hidden="true" />
                    </button>
                </div>

                {/* Drawer Nav Links */}
                <nav aria-label="Mobile navigation" className="flex-1 overflow-y-auto px-4 py-8 space-y-2">
                    {navLinks.map((link) => {
                        const Icon = link.icon;
                        const isActive = location.pathname === link.to;
                        return (
                            <Link
                                key={link.to}
                                to={link.to}
                                onClick={() => setMobileOpen(false)}
                                aria-current={isActive ? 'page' : undefined}
                                className={`flex items-center gap-4 text-base font-medium py-3.5 px-4 rounded-2xl transition-all ${isActive ? 'text-charcoal-950 bg-brand-50 shadow-sm border border-brand-100' : 'text-charcoal-500 hover:text-charcoal-950 hover:bg-charcoal-50 border border-transparent'}`}
                            >
                                <div className={`p-2 rounded-xl flex items-center justify-center transition-colors ${isActive ? 'bg-white text-brand-600 shadow-sm' : 'bg-charcoal-50 text-charcoal-400 group-hover:bg-white group-hover:text-charcoal-900 group-hover:shadow-sm'}`}>
                                    <Icon className="w-5 h-5" />
                                </div>
                                {link.label}
                            </Link>
                        );
                    })}
                </nav>

                {/* Drawer Footer - Removed as requested */}
            </div>

            {/* ──── CONTENT ──── */}
            <main id="main-content" className="flex-grow">
                <Outlet />
            </main>

            {/* Mobile Bottom Navigation */}
            <MobileBottomBar />

            {/* Chatbot Integration */}
            <Chatbot />

            {/* Floating WhatsApp Button */}
            {!isAiSupport && (
                <a
                    href={whatsappUrl}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="fixed bottom-24 md:bottom-6 right-6 z-50 bg-[#25D366] text-white p-4 rounded-full shadow-[0_0_30px_rgba(37,211,102,0.5)] hover:shadow-[0_0_50px_rgba(37,211,102,0.8)] hover:scale-110 hover:bg-[#20ba5a] transition-all duration-300 group overflow-hidden animate-whatsapp animate-float"
                    aria-label="Contact on WhatsApp"
                >
                    <div className="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    <div className="relative z-10 group-hover:animate-shake">
                        <svg className="w-6 h-6 fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                    </div>
                </a>
            )}

            {/* ──── FOOTER ──── */}
            {!isAiSupport && (
                <footer className="bg-charcoal-950 text-white relative overflow-hidden">
                    {/* Subtle ambient glow */}
                    <div className="absolute top-0 left-1/3 w-96 h-96 bg-brand-500/[0.03] rounded-full blur-3xl pointer-events-none"></div>


                    {/* Main Footer */}
                    <div className="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10 py-14 sm:py-16">
                        <div className="grid grid-cols-2 md:grid-cols-12 gap-10 md:gap-8">
                            {/* Brand Column */}
                            <div className="col-span-2 md:col-span-4">
                                <Link to="/" className="flex items-center gap-2.5 mb-5 group">
                                    <div className="bg-white/10 rounded-xl p-3 inline-block"><img src="/logo.png" alt="Codemistry Logo" className="h-12 w-auto object-contain brightness-0 invert" /></div>
                                </Link>
                                <p className="text-charcoal-400 text-sm leading-relaxed max-w-xs mb-6">
                                    We craft world-class websites, apps, CRMs, and AI-powered solutions for ambitious Indian businesses.
                                </p>
                                {/* Social icons */}
                                <div className="flex gap-2.5">
                                    {[
                                        { icon: Instagram, href: 'https://www.instagram.com/codemistry01?igsh=MWFkeTB2OXg5d3BwaQ==', label: 'Codemistry on Instagram' },
                                        { icon: Linkedin, href: '#', label: 'Codemistry on LinkedIn' },
                                        { icon: Twitter, href: '#', label: 'Codemistry on Twitter' },
                                        { icon: Mail, href: 'mailto:codemistry359@gmail.com', label: 'Email Codemistry' },
                                    ].map((s, i) => (
                                        <a key={i} href={s.href} aria-label={s.label} target={s.href.startsWith('http') ? '_blank' : undefined} rel={s.href.startsWith('http') ? 'noopener noreferrer' : undefined} className="w-11 h-11 rounded-xl bg-white/[0.06] hover:bg-white/[0.12] border border-white/[0.06] flex items-center justify-center transition-all duration-300 group">
                                            <s.icon className="w-4 h-4 text-charcoal-400 group-hover:text-white transition-colors" aria-hidden="true" />
                                        </a>
                                    ))}
                                </div>
                            </div>

                            {/* Company */}
                            <div className="col-span-4 md:col-span-2 border-b border-white/10 md:border-0 pb-4 md:pb-0">
                                <button
                                    onClick={() => toggleFooterSection('company')}
                                    className="w-full flex items-center justify-between md:cursor-default"
                                >
                                    <h3 className="text-xs font-semibold uppercase tracking-[0.15em] text-white/70 md:mb-5">Company</h3>
                                    <ChevronDown className={`w-4 h-4 text-white/40 md:hidden transition-transform duration-300 ${openFooterSection === 'company' ? 'rotate-180' : ''}`} />
                                </button>
                                <div className={`grid transition-all duration-300 md:grid-rows-[1fr] md:mt-0 ${openFooterSection === 'company' ? 'grid-rows-[1fr] mt-4 opacity-100' : 'grid-rows-[0fr] opacity-0 md:opacity-100'}`}>
                                    <ul className="space-y-3.5 overflow-hidden">
                                        {[
                                            { to: '/about', label: 'About Us' },
                                            { to: '/services', label: 'Services' },
                                            { to: '/blog', label: 'Blog' },
                                            { to: '/contact', label: 'Contact' },
                                            { to: '/login', label: 'Admin Login' },
                                        ].map((link) => (
                                            <li key={link.to}>
                                                <Link to={link.to} className="text-sm text-charcoal-400 hover:text-white transition-colors flex items-center gap-1 group">
                                                    {link.label}
                                                    <ArrowUpRight className="w-3 h-3 opacity-0 -translate-y-px group-hover:opacity-100 transition-all" />
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>

                            {/* Services */}
                            <div className="col-span-4 md:col-span-3 border-b border-white/10 md:border-0 py-4 md:py-0">
                                <button
                                    onClick={() => toggleFooterSection('services')}
                                    className="w-full flex items-center justify-between md:cursor-default"
                                >
                                    <h3 className="text-xs font-semibold uppercase tracking-[0.15em] text-white/70 md:mb-5">Services</h3>
                                    <ChevronDown className={`w-4 h-4 text-white/40 md:hidden transition-transform duration-300 ${openFooterSection === 'services' ? 'rotate-180' : ''}`} />
                                </button>
                                <div className={`grid transition-all duration-300 md:grid-rows-[1fr] md:mt-0 ${openFooterSection === 'services' ? 'grid-rows-[1fr] mt-4 opacity-100' : 'grid-rows-[0fr] opacity-0 md:opacity-100'}`}>
                                    <ul className="space-y-3.5 overflow-hidden">
                                        {['Web Development', 'App Development', 'Custom CRM', 'AI Integration', 'Website Management'].map((s) => (
                                            <li key={s}>
                                                <Link to="/services" className="text-sm text-charcoal-400 hover:text-white transition-colors flex items-center gap-1 group">
                                                    {s}
                                                    <ArrowUpRight className="w-3 h-3 opacity-0 -translate-y-px group-hover:opacity-100 transition-all" />
                                                </Link>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>

                            {/* Contact info */}
                            <div className="col-span-4 md:col-span-3 pt-4 md:pt-0">
                                <button
                                    onClick={() => toggleFooterSection('contact')}
                                    className="w-full flex items-center justify-between md:cursor-default"
                                >
                                    <h3 className="text-xs font-semibold uppercase tracking-[0.15em] text-white/70 md:mb-5">Contact</h3>
                                    <ChevronDown className={`w-4 h-4 text-white/40 md:hidden transition-transform duration-300 ${openFooterSection === 'contact' ? 'rotate-180' : ''}`} />
                                </button>
                                <div className={`grid transition-all duration-300 md:grid-rows-[1fr] md:mt-0 ${openFooterSection === 'contact' ? 'grid-rows-[1fr] mt-4 opacity-100' : 'grid-rows-[0fr] opacity-0 md:opacity-100'}`}>
                                    <ul className="space-y-4 overflow-hidden">
                                        <li className="flex items-start gap-3">
                                            <Mail className="w-4 h-4 text-charcoal-500 mt-0.5 shrink-0" />
                                            <a href="mailto:codemistry359@gmail.com" className="text-sm text-charcoal-400 hover:text-white transition-colors">codemistry359@gmail.com</a>
                                        </li>
                                        <li className="flex items-start gap-3">
                                            <Phone className="w-4 h-4 text-charcoal-500 mt-0.5 shrink-0" />
                                            <a href="tel:+918910710136" className="text-sm text-charcoal-400 hover:text-white transition-colors">+91 89107 10136</a>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Bottom Bar */}
                    <div className="border-t border-white/[0.06]">
                        <div className="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10 py-5 flex flex-col sm:flex-row justify-between items-center gap-3">
                            <p className="text-charcoal-500 text-xs">© 2026 Codemistry. All rights reserved.</p>
                            <div className="flex items-center gap-4 text-charcoal-500 text-xs">
                                <Link to="/privacy" className="hover:text-white transition-colors">Privacy Policy</Link>
                                <span>•</span>
                                <Link to="/terms" className="hover:text-white transition-colors">Terms of Service</Link>
                                <span>•</span>
                                <span>Built with ♥ in India</span>
                            </div>
                        </div>
                    </div>
                </footer>
            )}
        </div>
    );
};

export default Layout;




