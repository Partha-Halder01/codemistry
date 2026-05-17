import React, { useEffect, useState, useRef } from 'react';
import { Shield, Target, Rocket, Zap, Users, Clock, CheckCircle, ArrowRight, Star } from 'lucide-react';
import { Link } from 'react-router-dom';
import Seo from '../components/Seo';
import { breadcrumbLd, organizationLd, SITE_INFO } from '../seo/structuredData';

/* ─── Hooks ─── */
const useScrollReveal = () => {
    useEffect(() => {
        const observer = new IntersectionObserver(
            (entries) => entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('visible'); }),
            { threshold: 0.1 }
        );
        document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up, .stagger-children').forEach((el) => observer.observe(el));
        return () => observer.disconnect();
    }, []);
};

const useCountUp = (end, duration = 2000) => {
    const [count, setCount] = useState(0);
    const ref = useRef(null);
    const counted = useRef(false);

    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting && !counted.current) {
                    counted.current = true;
                    let startTime;
                    const animate = (now) => {
                        if (!startTime) startTime = now;
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

/* ─── Components ─── */
const CountUpCard = ({ value, suffix = '', label, Icon, color }) => {
    const numericValue = parseFloat(value);
    const { count, ref } = useCountUp(isNaN(numericValue) ? 0 : numericValue, 2200);
    const decimalPlaces = (value.toString().split('.')[1] || '').length;
    const hasDecimal = decimalPlaces > 0;
    const displaySuffix = value.replace(/[0-9.]/g, '') || suffix;
    const displayValue = hasDecimal ? count.toFixed(decimalPlaces) : Math.floor(count);

    return (
        <div ref={ref} className="bg-white/[0.08] backdrop-blur-sm border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300 shadow-xl shadow-black/40 hover:bg-white/[0.12]">
            <div className={`absolute top-0 right-0 w-32 h-32 bg-gradient-to-br ${color} opacity-20 rounded-full blur-3xl group-hover:opacity-30 transition-opacity`}></div>
            <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${color} flex items-center justify-center mb-4 relative z-10`}>
                <Icon className="w-6 h-6 text-white" />
            </div>
            <div className="text-4xl md:text-5xl font-display font-extrabold text-white mb-2 relative z-10">{displayValue}{displaySuffix}</div>
            <div className="text-sm text-white/60 font-medium uppercase tracking-wider relative z-10">{label}</div>
        </div>
    );
};

const ProcessStep = ({ number, title, desc, delay }) => (
    <div className="relative pl-12 md:pl-0 animate-fade-in-up" style={{ animationDelay: delay }}>
        {/* Mobile vertical line */}
        <div className="md:hidden absolute left-5 top-12 bottom-[-3rem] w-px bg-brand-500/30"></div>

        <div className="md:text-center">
            <div className="w-12 h-12 rounded-full border-4 border-white bg-brand-50 shadow-lg flex items-center justify-center text-brand-600 font-bold font-display text-xl mb-4 relative z-10 md:mx-auto absolute left-[-6px] top-0 md:static">
                {number}
            </div>
            <h3 className="text-xl font-bold text-charcoal-950 mb-2">{title}</h3>
            <p className="text-charcoal-600 leading-relaxed text-sm md:text-base">{desc}</p>
        </div>
    </div>
);

const About = () => {
    useScrollReveal();

    return (
        <div className="bg-white overflow-x-hidden">
            <Seo
                title="About Codemistry — An India-Based Web & App Development Team"
                description="Learn about Codemistry — an India-based team building affordable web, mobile and AI products for businesses across India. Quality assured, on-time delivery."
                canonical={SITE_INFO.url + '/about'}
                keywords="web development company India, AI integration agency India, WhatsApp chatbot development India, Gemini AI integration India, progressive web app India, best web developers India, IT company West Bengal India"
                jsonLd={[
                    organizationLd(),
                    breadcrumbLd([
                        { name: 'Home', url: SITE_INFO.url + '/' },
                        { name: 'About', url: SITE_INFO.url + '/about' },
                    ]),
                ]}
            />
            {/* 1. HERO SECTION */}
            <section className="relative pt-44 pb-32 lg:pb-40 overflow-hidden">
                {/* Background Image */}
                <div
                    className="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center bg-no-repeat"
                ></div>

                {/* Dark Overlay for Text Readability */}
                <div className="absolute inset-0 bg-charcoal-950/80"></div>

                <div className="relative z-10 max-w-7xl mx-auto px-5 sm:px-6 lg:px-10 text-center">
                    <div className="inline-flex items-center gap-2 px-5 py-2 rounded-full border border-white/10 bg-white/5 text-white/80 text-xs sm:text-sm font-medium mb-8 animate-fade-in-up backdrop-blur-md">
                        <span className="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                        Founded 2021
                    </div>
                    <h1 className="text-4xl md:text-5xl lg:text-7xl font-display font-extrabold text-white mb-6 leading-[1.1] animate-fade-in-up" style={{ animationDelay: '0.1s' }}>
                        We build digital products <br className="hidden md:block" />
                        <span className="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-purple-400">people actually love.</span>
                    </h1>
                    <p className="text-lg md:text-xl text-white/60 max-w-2xl mx-auto animate-fade-in-up leading-relaxed" style={{ animationDelay: '0.2s' }}>
                        Codemistry was founded on a simple principle: software should not only work flawlessly, but it should also look stunning and be a joy to use.
                    </p>
                </div>

                <div className="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-charcoal-50 to-transparent"></div>
            </section>

            {/* 2. OUR STORY / SPLIT LAYOUT */}
            <section className="py-20 lg:py-32 relative bg-charcoal-50 overflow-hidden">
                <div className="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10 relative z-10">
                    <div className="flex flex-col lg:flex-row gap-16 lg:gap-24 items-start">
                        {/* Left: Sticky Text */}
                        <div className="w-full lg:w-1/2 lg:sticky lg:top-32 animate-fade-in-up">
                            <h2 className="text-brand-600 font-bold tracking-wider uppercase text-sm mb-3">Our Mission</h2>
                            <h3 className="text-3xl md:text-4xl lg:text-5xl font-display font-extrabold text-charcoal-950 mb-6 leading-tight">
                                Redefining the standard of <span className="text-brand-500">digital experiences.</span>
                            </h3>
                            <p className="text-charcoal-600 text-lg leading-relaxed mb-6">
                                We're not just a development agency. We're your technical partners. We believe that great software is a combination of robust engineering and breathtaking design.
                            </p>
                            <p className="text-charcoal-600 text-lg leading-relaxed mb-8">
                                Every project we undertake is built with scalability, security, and user experience at its core, ensuring your business is ready for the future.
                            </p>
                            <Link to="/contact" className="inline-flex items-center text-brand-600 font-bold hover:text-brand-700 transition-colors group text-lg">
                                Work with our team <ArrowRight className="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" />
                            </Link>
                        </div>

                        {/* Right: Icon Cards Grid */}
                        <div className="w-full lg:w-1/2 grid grid-cols-1 sm:grid-cols-2 gap-6 stagger-children">
                            {[
                                { icon: Rocket, title: 'Fast Execution', desc: 'We deliver high-quality software rapidly without compromising on standards.', color: 'text-brand-500', bg: 'bg-brand-50' },
                                { icon: Target, title: 'Goal Oriented', desc: 'Every feature we build is directly tied to your business objectives.', color: 'text-purple-500', bg: 'bg-purple-50' },
                                { icon: Shield, title: 'Enterprise Security', desc: 'Bank-grade security practices embedded into our development lifecycle.', color: 'text-emerald-500', bg: 'bg-emerald-50' },
                                { icon: Zap, title: 'High Performance', desc: 'Blazing fast load times and optimized database queries by default.', color: 'text-amber-500', bg: 'bg-amber-50' },
                            ].map((feature, idx) => (
                                <div key={idx} className="bg-white rounded-2xl p-8 border border-charcoal-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                                    <div className={`w-12 h-12 rounded-xl ${feature.bg} flex items-center justify-center mb-6`}>
                                        <feature.icon className={`w-6 h-6 ${feature.color}`} />
                                    </div>
                                    <h4 className="text-xl font-bold text-charcoal-950 mb-3">{feature.title}</h4>
                                    <p className="text-charcoal-600 leading-relaxed text-sm">{feature.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* 3. BY THE NUMBERS (Dark Infographic Grid with Parallax) */}
            <section className="py-24 relative overflow-hidden bg-charcoal-950">
                {/* Parallax Background */}
                <div
                    className="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop')] bg-fixed bg-cover bg-center opacity-40 mix-blend-luminosity"
                ></div>
                <div className="absolute inset-0 bg-charcoal-950/80"></div>

                <div className="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10 relative z-10">
                    <div className="text-center mb-16 animate-fade-in-up">
                        <h2 className="text-brand-500 font-bold tracking-wider uppercase text-sm mb-3">By The Numbers</h2>
                        <h3 className="text-3xl md:text-5xl font-display font-extrabold text-white">Proven Track Record</h3>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <CountUpCard value="1+" label="Years Experience" Icon={Clock} color="from-brand-500 to-brand-600" />
                        <CountUpCard value="100%" label="Satisfaction Rate" Icon={CheckCircle} color="from-emerald-500 to-emerald-600" />
                        <CountUpCard value="4.8" label="Average Rating" Icon={Star} color="from-amber-500 to-amber-600" />
                        <CountUpCard value="25+" label="Developers" Icon={Users} color="from-purple-500 to-purple-600" />
                    </div>
                </div>
            </section>

            {/* 4. OUR PROCESS TIMELINE */}
            <section className="py-24 bg-white relative">
                <div className="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10">
                    <div className="text-center mb-20 animate-fade-in-up">
                        <h2 className="text-brand-600 font-bold tracking-wider uppercase text-sm mb-3">How We Work</h2>
                        <h3 className="text-3xl md:text-5xl font-display font-extrabold text-charcoal-950">A proven, transparent process.</h3>
                    </div>

                    <div className="relative">
                        {/* Desktop Horizontal Line */}
                        <div className="hidden md:block absolute top-[24px] left-[10%] right-[10%] h-px bg-brand-500/20"></div>

                        <div className="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-6">
                            <ProcessStep
                                number="1"
                                title="Discovery"
                                desc="We dive deep into your requirements, target audience, and business goals to form a solid strategy."
                                delay="0s"
                            />
                            <ProcessStep
                                number="2"
                                title="UI/UX Design"
                                desc="Our design team creates beautiful, intuitive wireframes and high-fidelity mockups for your approval."
                                delay="0.1s"
                            />
                            <ProcessStep
                                number="3"
                                title="Development"
                                desc="We write clean, secure, and scalable code following agile methodologies and regular sprints."
                                delay="0.2s"
                            />
                            <ProcessStep
                                number="4"
                                title="Launching"
                                desc="Rigorous quality assurance testing followed by a smooth deployment and post-launch support."
                                delay="0.3s"
                            />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
};

export default About;
