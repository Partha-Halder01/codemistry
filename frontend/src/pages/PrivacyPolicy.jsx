import Seo from '../components/Seo';

const PrivacyPolicy = () => (
    <>
        <Seo
            title="Privacy Policy — Codemistry"
            description="Read Codemistry's Privacy Policy to understand how we collect, use, and protect your personal information when you use our website and services."
            canonical="https://codemistry.in/privacy"
        />
        <div className="pt-28 pb-20 bg-white min-h-screen">
            <div className="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8">
                <h1 className="text-4xl font-display font-bold text-charcoal-950 mb-3">Privacy Policy</h1>
                <p className="text-sm text-charcoal-500 mb-10">Last updated: 16 May 2026</p>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">1. Introduction</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        Codemistry ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we
                        collect, use, disclose, and safeguard your information when you visit <strong>codemistry.in</strong> or engage
                        our web and app development services.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">2. Information We Collect</h2>
                    <ul className="list-disc pl-5 space-y-2 text-charcoal-700 leading-relaxed">
                        <li><strong>Contact information</strong> — name, email address, and phone number submitted via our contact form.</li>
                        <li><strong>Project details</strong> — requirements or messages you share when inquiring about our services.</li>
                        <li><strong>Usage data</strong> — pages visited, time spent, browser type, and IP address collected automatically via analytics.</li>
                        <li><strong>AI chat history</strong> — messages sent through our on-site AI assistant to improve response quality.</li>
                    </ul>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">3. How We Use Your Information</h2>
                    <ul className="list-disc pl-5 space-y-2 text-charcoal-700 leading-relaxed">
                        <li>Respond to your inquiries and provide requested services.</li>
                        <li>Send project updates, invoices, and service-related communications.</li>
                        <li>Improve our website performance and user experience.</li>
                        <li>Comply with applicable Indian laws and regulations.</li>
                    </ul>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">4. Sharing of Information</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        We do not sell, trade, or rent your personal information to third parties. We may share data only with:
                    </p>
                    <ul className="list-disc pl-5 space-y-2 text-charcoal-700 leading-relaxed mt-2">
                        <li>Trusted service providers (e.g., hosting, payment gateways) under strict confidentiality agreements.</li>
                        <li>Law enforcement agencies when required by law.</li>
                    </ul>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">5. Cookies</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        We use essential cookies to maintain session state and analytics cookies (e.g., Google Analytics) to understand
                        visitor behaviour. You may disable cookies in your browser settings; this may affect site functionality.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">6. Data Security</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        We implement industry-standard security measures including HTTPS encryption, access controls, and secure server
                        configurations. However, no method of transmission over the internet is 100% secure.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">7. Your Rights</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        You have the right to request access to, correction of, or deletion of personal information we hold about you.
                        To exercise these rights, email us at <a href="mailto:codemistry359@gmail.com" className="text-brand-600 underline underline-offset-2">codemistry359@gmail.com</a>.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">8. Changes to This Policy</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated date.
                        Continued use of our website after changes constitutes acceptance of the revised policy.
                    </p>
                </section>

                <section>
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">9. Contact Us</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        For any privacy-related questions, please contact:<br />
                        <strong>Codemistry</strong><br />
                        Uttar Dinajpur, West Bengal, India<br />
                        Email: <a href="mailto:codemistry359@gmail.com" className="text-brand-600 underline underline-offset-2">codemistry359@gmail.com</a><br />
                        Phone: <a href="tel:+918967739189" className="text-brand-600 underline underline-offset-2">+91 89677 39189</a>
                    </p>
                </section>
            </div>
        </div>
    </>
);

export default PrivacyPolicy;
