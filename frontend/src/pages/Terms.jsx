import Seo from '../components/Seo';

const Terms = () => (
    <>
        <Seo
            title="Terms of Service — Codemistry"
            description="Read Codemistry's Terms of Service governing the use of our website and web & app development services."
            canonical="https://codemistry.in/terms"
        />
        <div className="pt-28 pb-20 bg-white min-h-screen">
            <div className="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8">
                <h1 className="text-4xl font-display font-bold text-charcoal-950 mb-3">Terms of Service</h1>
                <p className="text-sm text-charcoal-500 mb-10">Last updated: 16 May 2026</p>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">1. Acceptance of Terms</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        By accessing <strong>codemistry.in</strong> or engaging Codemistry's services, you agree to be bound by
                        these Terms of Service. If you do not agree, please do not use our website or services.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">2. Services Provided</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        Codemistry provides web development, mobile app development, custom CRM development, AI integration, and
                        related digital services. Specific deliverables, timelines, and pricing are defined in individual project
                        agreements or proposals.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">3. Payment Terms</h2>
                    <ul className="list-disc pl-5 space-y-2 text-charcoal-700 leading-relaxed">
                        <li>Projects are typically billed in milestones (e.g., 40% advance, 30% mid-project, 30% on delivery).</li>
                        <li>All prices are in Indian Rupees (INR) and include applicable GST.</li>
                        <li>Late payments beyond 7 days may result in work being paused.</li>
                        <li>Refunds are subject to the stage of project completion at the time of cancellation.</li>
                    </ul>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">4. Intellectual Property</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        Upon full payment, the client receives full ownership of all custom code and assets developed specifically
                        for their project. Third-party libraries, frameworks, and tools used remain under their respective licences.
                        Codemistry retains the right to showcase the project in its portfolio unless explicitly requested otherwise.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">5. Client Responsibilities</h2>
                    <ul className="list-disc pl-5 space-y-2 text-charcoal-700 leading-relaxed">
                        <li>Provide accurate project requirements and timely feedback.</li>
                        <li>Supply all required content (text, images, branding) within agreed timelines.</li>
                        <li>Ensure you have the legal right to use any third-party content you provide to us.</li>
                    </ul>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">6. Limitation of Liability</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        Codemistry's total liability to you for any claim arising from our services shall not exceed the total amount
                        paid by you for the specific service giving rise to the claim. We are not liable for indirect, incidental, or
                        consequential damages.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">7. Confidentiality</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        Both parties agree to keep confidential any proprietary information, business data, or trade secrets shared
                        during the course of the engagement, and not to disclose such information to third parties.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">8. Termination</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        Either party may terminate a project engagement with 14 days' written notice. The client is liable for payment
                        for all work completed up to the termination date. Codemistry will deliver all completed work upon receipt
                        of outstanding payments.
                    </p>
                </section>

                <section className="mb-8">
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">9. Governing Law</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        These Terms are governed by the laws of India. Any disputes shall be subject to the exclusive jurisdiction
                        of courts in West Bengal, India.
                    </p>
                </section>

                <section>
                    <h2 className="text-xl font-display font-bold text-charcoal-950 mb-3">10. Contact Us</h2>
                    <p className="text-charcoal-700 leading-relaxed">
                        For questions about these Terms, contact:<br />
                        <strong>Codemistry</strong><br />
                        Email: <a href="mailto:codemistry359@gmail.com" className="text-brand-600 underline underline-offset-2">codemistry359@gmail.com</a><br />
                        Phone: <a href="tel:+918910710136" className="text-brand-600 underline underline-offset-2">+91 89107 10136</a>
                    </p>
                </section>
            </div>
        </div>
    </>
);

export default Terms;
