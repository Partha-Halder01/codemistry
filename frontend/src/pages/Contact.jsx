import { Mail, Phone, MapPin, Send } from 'lucide-react';
import { useState } from 'react';
import api from '../api';
import Seo from '../components/Seo';
import { breadcrumbLd, localBusinessLd, organizationLd, SITE_INFO } from '../seo/structuredData';

const Contact = () => {
    const [formData, setFormData] = useState({ name: '', email: '', phone: '', message: '' });
    const [status, setStatus] = useState({ type: '', message: '' });
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleChange = (e) => setFormData({ ...formData, [e.target.name]: e.target.value });

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setStatus({ type: '', message: '' });
        try {
            await api.post('/tickets', formData);
            setStatus({ type: 'success', message: 'Message sent! We\'ll get back to you within 24 hours.' });
            setFormData({ name: '', email: '', phone: '', message: '' });
        } catch (error) {
            setStatus({ type: 'error', message: error.response?.data?.message || 'Something went wrong.' });
        } finally { setIsSubmitting(false); }
    };

    const inputClass = "w-full bg-charcoal-50 border border-charcoal-200 rounded-lg px-4 py-3 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/40 focus:border-brand-500 transition-all placeholder:text-charcoal-300";

    return (
        <div className="min-h-screen bg-white pt-32 pb-24 px-4">
            <Seo
                title="Contact Codemistry — Web & App Development in India"
                description="Talk to Codemistry about your web, app, e-commerce or AI project. India-based team, transparent INR pricing, and 24-hour response."
                canonical={SITE_INFO.url + '/contact'}
                jsonLd={[
                    organizationLd(),
                    localBusinessLd(),
                    breadcrumbLd([
                        { name: 'Home', url: SITE_INFO.url + '/' },
                        { name: 'Contact', url: SITE_INFO.url + '/contact' },
                    ]),
                ]}
            />
            <div className="max-w-7xl mx-auto">
                <div className="mb-16 text-center animate-fade-in-up">
                    <h1 className="text-4xl md:text-5xl font-display font-extrabold text-charcoal-950 mb-4">Let's talk about your project</h1>
                    <p className="text-charcoal-500 text-lg max-w-xl mx-auto">Have an idea? We'd love to hear about it. Reach out and we'll respond within 24 hours.</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    {/* Contact Info */}
                    <div className="space-y-6">
                        <div className="bg-charcoal-50 rounded-2xl p-8">
                            <h3 className="text-lg font-display font-bold text-charcoal-950 mb-6">Contact Information</h3>
                            <div className="space-y-5">
                                {[
                                    { icon: Mail, label: 'Email Us', value: 'codemistry359@gmail.com', href: 'mailto:codemistry359@gmail.com' },
                                    { icon: Phone, label: 'Call Us', value: '8910710136', href: 'tel:8910710136' },
                                ].map((item, i) => {
                                    const Icon = item.icon;
                                    return (
                                        <div key={i} className="flex items-start gap-4">
                                            <div className="w-10 h-10 rounded-lg bg-brand-50 flex items-center justify-center shrink-0">
                                                <Icon className="w-5 h-5 text-brand-600" />
                                            </div>
                                            <div>
                                                <h4 className="text-charcoal-950 font-medium text-sm mb-0.5">{item.label}</h4>
                                                {item.href ? (
                                                    <a href={item.href} className="text-brand-600 hover:text-brand-700 text-sm">{item.value}</a>
                                                ) : (
                                                    <p className="text-charcoal-500 text-sm whitespace-pre-line">{item.value}</p>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        <div className="bg-charcoal-50 rounded-2xl p-8">
                            <h3 className="text-sm font-bold text-charcoal-950 mb-3">Office Hours</h3>
                            <p className="text-charcoal-500 text-sm flex justify-between border-b border-charcoal-200 pb-2 mb-2">
                                <span>Monday – Saturday:</span> <span>24 Hours</span>
                            </p>
                            <p className="text-charcoal-500 text-sm flex justify-between">
                                <span>Sunday:</span> <span>Closed</span>
                            </p>
                        </div>
                    </div>

                    {/* Form */}
                    <div className="bg-white border border-charcoal-100 rounded-2xl p-8">
                        <h3 className="text-xl font-display font-bold text-charcoal-950 mb-6">Send a Message</h3>

                        {status.message && (
                            <div className={`p-3 rounded-lg mb-5 text-sm ${status.type === 'success' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100'}`}>
                                {status.message}
                            </div>
                        )}

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label htmlFor="name" className="block text-xs font-medium text-charcoal-600 mb-1">Full Name *</label>
                                    <input type="text" id="name" name="name" required value={formData.name} onChange={handleChange} className={inputClass} placeholder="John Doe" />
                                </div>
                                <div>
                                    <label htmlFor="phone" className="block text-xs font-medium text-charcoal-600 mb-1">Phone *</label>
                                    <input type="tel" id="phone" name="phone" required value={formData.phone} onChange={handleChange} className={inputClass} placeholder="+91 9876543210" />
                                </div>
                            </div>
                            <div>
                                <label htmlFor="email" className="block text-xs font-medium text-charcoal-600 mb-1">Email</label>
                                <input type="email" id="email" name="email" value={formData.email} onChange={handleChange} className={inputClass} placeholder="john@example.com" />
                            </div>
                            <div>
                                <label htmlFor="message" className="block text-xs font-medium text-charcoal-600 mb-1">Message *</label>
                                <textarea id="message" name="message" required rows="5" value={formData.message} onChange={handleChange} className={`${inputClass} resize-none`} placeholder="Tell us about your project..."></textarea>
                            </div>
                            <button type="submit" disabled={isSubmitting}
                                className={`w-full btn-primary flex items-center justify-center gap-2 ${isSubmitting ? 'opacity-70 cursor-not-allowed' : ''}`}>
                                {isSubmitting ? 'Sending...' : 'Send Message'}
                                {!isSubmitting && <Send className="w-4 h-4" />}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Contact;
