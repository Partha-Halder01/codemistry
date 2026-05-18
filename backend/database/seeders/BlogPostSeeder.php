<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $posts = [
            [
                'title'   => 'Web Development Cost in India 2026: A Complete Pricing Guide',
                'cover_image_path' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&q=70&fm=webp&auto=format',
                'excerpt' => 'A transparent breakdown of website development costs in India in 2026 — basic, business, and custom builds, with INR price ranges, GST notes, and what actually drives the bill up.',
                'meta_title' => 'Web Development Cost in India 2026 — INR Pricing Guide | Codemistry',
                'meta_description' => 'How much does a website cost in India in 2026? Real INR price ranges for static, business and custom websites — plus what affects the final quote.',
                'meta_keywords' => 'web development cost in India, website price India, web development pricing 2026, custom website cost India, website development company India',
                'tags' => ['Web Development', 'Pricing', 'India', 'Business'],
                'author_name' => 'Codemistry Team',
                'published_at' => $now->copy()->subDays(28),
                'content_css' => '.lede{font-size:1.05rem;color:#374151;line-height:1.7}.tag-pill{display:inline-block;padding:2px 10px;border-radius:9999px;background:#ecfdf5;color:#065f46;font-size:.8rem;font-weight:600;margin-right:6px}h2{margin-top:1.6rem}h3{margin-top:1.2rem}.note{background:#f8fafc;border-left:4px solid #10b981;padding:12px 16px;border-radius:6px;margin:16px 0}',
                'content_html' => <<<'HTML'
<p class="lede">If you are searching for the cost of web development in India in 2026, the honest answer is: <strong>it depends</strong> — but the ranges are predictable. This guide breaks down what businesses across Delhi, Mumbai, Bangalore, Kolkata and Tier-2 cities like Siliguri or Bhubaneswar actually pay, and what drives the difference.</p>

<p><span class="tag-pill">India</span><span class="tag-pill">INR pricing</span><span class="tag-pill">2026</span></p>

<h2>1. Static / Brochure Website</h2>
<p>A 4–6 page informational website for a small business — about us, services, contact, basic SEO. In India this typically falls between <strong>₹15,000 – ₹40,000</strong> in 2026.</p>

<h2>2. Business / Service Website</h2>
<p>10–20 pages, blog, lead-capture forms, integrated WhatsApp & Razorpay, on-page SEO and analytics. Expect <strong>₹40,000 – ₹1,20,000</strong> for a quality build by a credible Indian agency.</p>

<h2>3. Custom Web Application</h2>
<p>Dashboards, user accounts, payments, role-based admin — basically a SaaS-style product. Realistic 2026 budget: <strong>₹1,50,000 – ₹6,00,000+</strong>, depending on complexity and integrations (UPI, GST invoices, ONDC, ERP).</p>

<h2>What actually drives the price up?</h2>
<ul>
  <li><strong>Custom design</strong> vs. off-the-shelf templates</li>
  <li>Number of integrations (Razorpay, GSTN, Shiprocket, Tally, Zoho)</li>
  <li>Content production — copywriting and product photography</li>
  <li>Ongoing hosting + maintenance (₹1,500 – ₹8,000/month is normal)</li>
  <li>18% GST, where applicable, on top of the development quote</li>
</ul>

<div class="note"><strong>Tip:</strong> Always ask for a milestone-based quote — 25% advance, 25% on design approval, balance on launch. This is standard practice with reputable Indian agencies and protects both sides.</div>

<h2>How Codemistry prices projects</h2>
<p>At Codemistry we publish indicative ranges on every <a href="/services">service page</a> in INR, with a 25% advance and a 5% discount for full upfront payment. For a custom quote tailored to your business, <a href="/contact">contact us</a> — we typically respond within a few business hours.</p>
HTML
            ],
            [
                'title'   => 'How to Choose a Mobile App Development Company in India',
                'cover_image_path' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=1200&q=70&fm=webp&auto=format',
                'excerpt' => 'A buyer-side checklist for Indian SMBs and founders evaluating app development companies — what to ask, what to avoid, and how to compare quotes fairly.',
                'meta_title' => 'How to Choose a Mobile App Development Company in India | 2026 Guide',
                'meta_description' => 'Hiring an app developer in India? Use this 12-point checklist covering portfolio, INR pricing, post-launch support, IP ownership, GST, and more.',
                'meta_keywords' => 'mobile app development company India, hire app developer India, app development cost India, best app developers India 2026',
                'tags' => ['App Development', 'Hiring', 'India'],
                'author_name' => 'Codemistry Team',
                'published_at' => $now->copy()->subDays(21),
                'content_css' => '.lede{font-size:1.05rem;color:#374151;line-height:1.7}h2{margin-top:1.6rem}h3{margin-top:1.2rem}.checklist li{margin:6px 0}.callout{background:#fff7ed;border:1px solid #fed7aa;padding:14px 18px;border-radius:10px;margin:18px 0}',
                'content_html' => <<<'HTML'
<p class="lede">India has thousands of app development agencies — and quotes for the same brief can range from <strong>₹50,000 to ₹15 lakh</strong>. Here is the checklist we recommend founders use, regardless of which agency they pick.</p>

<h2>1. Verify a real portfolio (not just screenshots)</h2>
<p>Ask for <em>live</em> Play Store / App Store links. Anyone can show a Behance mockup. A real shipped app means real users, real reviews, and a team that has dealt with crashes at 2am.</p>

<h2>2. Native, hybrid or cross-platform?</h2>
<p>For most Indian SMBs in 2026, <strong>React Native</strong> or <strong>Flutter</strong> is the right call — single codebase for Android + iOS, ~40% cost saving. Insist on native only if you genuinely need it (heavy AR, gaming, high-performance camera).</p>

<h2>3. Demand a written scope and INR-priced milestones</h2>
<p>A good Indian agency will give you a milestone document with deliverables in plain English and prices in INR (excluding GST). Avoid agencies that quote a single lump sum with no breakdown.</p>

<h2>4. Check post-launch support terms</h2>
<ul class="checklist">
  <li>How long is bug-fix support included? (3 months is industry-standard in India)</li>
  <li>What is the hourly rate for changes after that?</li>
  <li>Will they help with Play Store / App Store submission?</li>
  <li>Who handles app updates when iOS/Android releases breaking changes?</li>
</ul>

<h2>5. IP ownership clause</h2>
<p>The contract <strong>must</strong> clearly state that source code, design files, and accounts (Play Console, Firebase, etc.) belong to you on full payment. This is non-negotiable.</p>

<div class="callout"><strong>Red flag:</strong> Any agency that refuses to put scope, IP and timeline in writing — walk away, no matter how cheap the quote.</div>

<h2>6. UPI, Razorpay & Indian payment integrations</h2>
<p>If your app needs payments, ask for prior experience integrating Razorpay, PhonePe, Cashfree or UPI Intent. Indian payment flows have edge cases (UPI mandates, RBI tokenisation rules) that overseas developers usually miss.</p>

<h2>The Codemistry difference</h2>
<p>We publish <a href="/services/app-development">our app development pricing</a> openly in INR, ship MVPs in 6–10 weeks, and hand over full source code and accounts on completion. <a href="/contact">Talk to us about your app idea</a>.</p>
HTML
            ],
            [
                'title'   => 'Building an E-commerce Website in India: Razorpay, UPI & GST Setup',
                'cover_image_path' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=1200&q=70&fm=webp&auto=format',
                'excerpt' => 'A practical walkthrough of launching an Indian e-commerce store in 2026 — from choosing a stack to wiring up Razorpay, UPI Intent, GSTN invoicing and Shiprocket.',
                'meta_title' => 'E-commerce Website in India 2026: Razorpay, UPI & GST Guide | Codemistry',
                'meta_description' => 'Step-by-step guide to building an e-commerce website in India — payment gateway setup (Razorpay, UPI), GST-compliant invoicing, and shipping integrations.',
                'meta_keywords' => 'ecommerce website India, Razorpay integration, UPI payment website, GST invoicing ecommerce, online store India',
                'tags' => ['E-commerce', 'Razorpay', 'UPI', 'GST', 'India'],
                'author_name' => 'Codemistry Team',
                'published_at' => $now->copy()->subDays(14),
                'content_css' => '.lede{font-size:1.05rem;color:#374151;line-height:1.7}h2{margin-top:1.6rem}.kbd{background:#0f172a;color:#e2e8f0;padding:2px 6px;border-radius:4px;font-family:monospace;font-size:.85rem}.steps{counter-reset:step;list-style:none;padding-left:0}.steps li{counter-increment:step;position:relative;padding:10px 0 10px 44px}.steps li::before{content:counter(step);position:absolute;left:0;top:8px;width:30px;height:30px;border-radius:50%;background:#10b981;color:white;display:flex;align-items:center;justify-content:center;font-weight:700}',
                'content_html' => <<<'HTML'
<p class="lede">India's e-commerce market is on track to cross <strong>$160B by 2028</strong>. If you are launching an online store in 2026, this is the practical setup we use at Codemistry for our Indian clients.</p>

<h2>1. Pick the right stack</h2>
<p>For most Indian SMBs we recommend either <strong>Shopify (with India payment apps)</strong> for speed-to-market, or a <strong>custom Laravel + React</strong> store when you need deeper customisation, B2B pricing, or GST compliance baked in.</p>

<h2>2. Razorpay — the default Indian gateway</h2>
<ol class="steps">
  <li>Create a Razorpay account, complete KYC (PAN, GST, bank account)</li>
  <li>Generate <span class="kbd">key_id</span> and <span class="kbd">key_secret</span></li>
  <li>Use the standard checkout SDK or the Orders API for server-side verification</li>
  <li>Always verify the signature on the webhook before marking an order as paid</li>
</ol>

<h2>3. UPI Intent — the Indian conversion booster</h2>
<p>For mobile checkout, UPI Intent (deep-linking into PhonePe / GPay / Paytm) typically lifts conversion by <strong>15–25%</strong> over standard card flows. Razorpay supports this out of the box; just pass the right <span class="kbd">method: 'upi', flow: 'intent'</span> options.</p>

<h2>4. GST-compliant invoicing</h2>
<ul>
  <li>Display HSN/SAC codes on every line item</li>
  <li>Split CGST + SGST for intra-state, IGST for inter-state</li>
  <li>Issue a tax invoice (not just a receipt) for B2B customers</li>
  <li>Generate e-invoice JSON if your turnover crosses ₹5 crore</li>
</ul>

<h2>5. Shipping — Shiprocket, Delhivery or self-pickup</h2>
<p>For most D2C brands, integrating Shiprocket gives you 15+ courier partners on one API and serviceability checks by pincode. We auto-assign couriers based on weight + COD/prepaid + zone.</p>

<h2>6. SEO from day one</h2>
<p>Indian buyers search in a mix of English and Hinglish. Optimise product pages for both — e.g. "kurta for women" + "ladies kurti online India". Add <strong>Product</strong> JSON-LD with INR pricing for rich results in Google.</p>

<p>Need an Indian e-commerce build that ships in 4–8 weeks with all of the above? <a href="/services">See our e-commerce service</a> or <a href="/contact">request a quote</a>.</p>
HTML
            ],
            [
                'title'   => 'Custom Software for Small Businesses in India: Build vs. Buy',
                'cover_image_path' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&q=70&fm=webp&auto=format',
                'excerpt' => 'When does it make sense for an Indian SMB to invest in custom software vs. paying monthly for SaaS? A frank comparison with realistic 2026 numbers.',
                'meta_title' => 'Custom Software for Indian SMBs: Build vs Buy in 2026 | Codemistry',
                'meta_description' => 'Should your Indian small business build custom software or use SaaS? A no-fluff comparison with INR costs, ROI math and case studies.',
                'meta_keywords' => 'custom software India, build vs buy software, SMB software India, business software development India, ERP custom India',
                'tags' => ['Custom Software', 'Small Business', 'India', 'SaaS'],
                'author_name' => 'Codemistry Team',
                'published_at' => $now->copy()->subDays(7),
                'content_css' => '.lede{font-size:1.05rem;color:#374151;line-height:1.7}h2{margin-top:1.6rem}table{width:100%;border-collapse:collapse;margin:18px 0}th,td{padding:10px 12px;border:1px solid #e5e7eb;text-align:left;font-size:.95rem}th{background:#f3f4f6;font-weight:700}',
                'content_html' => <<<'HTML'
<p class="lede">Every growing Indian SMB hits the same wall: Excel sheets and WhatsApp groups stop scaling. The next question is — do we buy off-the-shelf SaaS, or build custom software?</p>

<h2>The honest comparison</h2>
<table>
<thead><tr><th>Factor</th><th>SaaS (Zoho, Tally, etc.)</th><th>Custom Build</th></tr></thead>
<tbody>
<tr><td>Upfront cost</td><td>₹0 – ₹5,000/mo</td><td>₹2 – 8 lakh one-time</td></tr>
<tr><td>Time to value</td><td>Same day</td><td>6 – 12 weeks</td></tr>
<tr><td>Fits your exact workflow</td><td>~70%</td><td>100%</td></tr>
<tr><td>Ownership</td><td>Vendor</td><td>You</td></tr>
<tr><td>Scaling cost (50 → 500 users)</td><td>Linear, expensive</td><td>One-time, much cheaper at scale</td></tr>
</tbody>
</table>

<h2>When SaaS is the right answer</h2>
<ul>
  <li>You are under 20 employees and your processes are still changing weekly</li>
  <li>Your needs are standard (basic CRM, accounting, HR)</li>
  <li>Cash flow matters more than long-term ownership</li>
</ul>

<h2>When custom is the right answer</h2>
<ul>
  <li>Your workflow is your competitive advantage (and SaaS forces you to bend it)</li>
  <li>You pay > ₹50,000/month across multiple SaaS tools that don't talk to each other</li>
  <li>You need GST / TDS / tally-export / ONDC compliance baked in</li>
  <li>You want to eventually license the software to others in your industry</li>
</ul>

<h2>The hybrid path most Indian SMBs miss</h2>
<p>Start with SaaS for the boring parts (accounting, payroll). Build custom <em>only</em> for the workflow that makes your business different. We do this constantly for Codemistry clients in manufacturing, logistics and education.</p>

<p><a href="/contact">Tell us about your workflow</a> and we'll give you an honest recommendation — even if that means "stick with SaaS for now."</p>
HTML
            ],
            [
                'title'   => 'AI Integration for Indian Startups: Use Cases & Costs in 2026',
                'cover_image_path' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=1200&q=70&fm=webp&auto=format',
                'excerpt' => 'Practical AI use cases Indian founders are actually shipping in 2026 — chatbots, document automation, lead scoring — with real INR cost ranges and ROI examples.',
                'meta_title' => 'AI Integration for Indian Startups in 2026: Use Cases & Costs',
                'meta_description' => 'Real AI use cases for Indian businesses in 2026 — multilingual chatbots, GST document parsing, lead scoring — with INR cost ranges and ROI examples.',
                'meta_keywords' => 'AI integration India, AI for Indian startups, chatbot India, AI cost India, AI development company India, Gemini OpenAI India',
                'tags' => ['AI', 'Startups', 'India', 'Automation'],
                'author_name' => 'Codemistry Team',
                'published_at' => $now->copy()->subDays(2),
                'content_css' => '.lede{font-size:1.05rem;color:#374151;line-height:1.7}h2{margin-top:1.6rem}.usecase{background:#f0f9ff;border:1px solid #bae6fd;padding:16px;border-radius:12px;margin:14px 0}.usecase h3{margin:0 0 6px 0;color:#075985}',
                'content_html' => <<<'HTML'
<p class="lede">In 2026, "AI" is no longer a side project for Indian startups — it is a line item in the budget. Here are the integrations Codemistry is actually shipping for Indian clients, with realistic cost ranges and what they return.</p>

<h2>1. Multilingual customer support chatbots</h2>
<div class="usecase">
  <h3>What it does</h3>
  <p>Answers customer queries in English, Hindi, Bengali, Tamil — grounded on your product catalogue and FAQs. Hands off to a human only when needed.</p>
  <p><strong>Build cost (India, 2026):</strong> ₹40,000 – ₹1,20,000 + ₹2,000 – ₹8,000/mo in API costs (Gemini Flash-Lite, GPT-4o-mini)</p>
  <p><strong>Typical ROI:</strong> 60–80% reduction in tier-1 support tickets within 30 days.</p>
</div>

<h2>2. GST invoice + document parsing</h2>
<div class="usecase">
  <h3>What it does</h3>
  <p>Drop a PDF invoice or PO — AI extracts vendor, GSTIN, HSN, line items into your ERP. Replaces hours of manual data entry per accountant per day.</p>
  <p><strong>Build cost:</strong> ₹80,000 – ₹2,50,000 depending on accuracy targets and document types.</p>
</div>

<h2>3. Lead scoring & sales prioritisation</h2>
<div class="usecase">
  <h3>What it does</h3>
  <p>Reads each new lead's website, LinkedIn, email signature; scores buying intent; routes the hot ones to your sales team's WhatsApp instantly.</p>
  <p><strong>Build cost:</strong> ₹60,000 – ₹1,80,000 + enrichment API fees.</p>
</div>

<h2>4. Internal "ask anything" assistant</h2>
<div class="usecase">
  <h3>What it does</h3>
  <p>Trained on your SOPs, HR policies, product docs. Employees ask in natural language; it answers with citations. Massive time-saver for 50+ employee Indian companies.</p>
  <p><strong>Build cost:</strong> ₹1,00,000 – ₹3,00,000.</p>
</div>

<h2>Which model should an Indian startup use?</h2>
<ul>
  <li><strong>Gemini Flash-Lite</strong> — cheapest, great for high-volume chatbots and classification</li>
  <li><strong>GPT-4o-mini</strong> — strong reasoning at low INR cost, great default</li>
  <li><strong>Claude 3.5 Sonnet</strong> — when you need careful, long-context reasoning (legal, finance)</li>
  <li><strong>Open-source on your own GPU</strong> — only worth it above ~₹50,000/month in API spend</li>
</ul>

<h2>Cost-control tips for Indian builders</h2>
<ul>
  <li>Cache common prompts — easy 30–50% saving</li>
  <li>Use the smallest model that passes your eval suite</li>
  <li>Stream responses; users perceive them as 2× faster</li>
  <li>Track cost-per-conversation as a first-class metric</li>
</ul>

<p>Want AI in your product without burning capital? <a href="/services">See our AI integration service</a> or <a href="/contact">tell us what you want to automate</a>.</p>
HTML
            ],
        ];

        foreach ($posts as $row) {
            // Stable slug from title — so re-running the seeder updates the same
            // row instead of creating "-2", "-3" duplicates.
            $slug = Str::slug($row['title']);
            BlogPost::updateOrCreate(
                ['slug' => $slug],
                array_merge($row, [
                    'slug'   => $slug,
                    'status' => 'published',
                ])
            );
        }
    }
}
