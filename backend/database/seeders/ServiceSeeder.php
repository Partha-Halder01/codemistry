<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServicePricing;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing services and pricings
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Service::truncate();
        ServicePricing::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $services = [
            [
                'name' => 'Web Development',
                'description' => 'Custom-built, responsive, and high-performance websites tailored to your brand. From simple portfolios to complex e-commerce platforms, we build for growth.',
                'full_price' => 30000,
                'deposit_price' => 5000,
                'features' => "Responsive Modern Design\nSEO Optimized\nSpeed Optimization\nSecure Coding\nContact Form Integration\nDomain & Hosting Not Included",
                'rating' => 4.9,
                'is_featured' => true,
                'cover_image_path' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&q=80',
                'cta_image_path' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=1200&q=80',
                'faq' => [
                    ['q' => 'How long does it take?', 'a' => 'A typical landing page takes 1-2 weeks, while a full multi-page website takes 3-4 weeks depending on complexity.'],
                    ['q' => 'Can I update the content myself?', 'a' => 'Yes, we provide easy-to-use CMS (Content Management System) integration so you can manage your site without coding knowledge.'],
                    ['q' => 'Are domain and hosting included?', 'a' => 'No, domain and hosting costs are separate. We specify this to ensure you have full ownership of your accounts, but we guide you through the setup.'],
                    ['q' => 'Is the website mobile-friendly?', 'a' => 'Absolutely. Every website we build is "Mobile First," ensuring it looks and works perfectly on all devices.']
                ],
                'pricings' => [
                    [
                        'plan_name' => 'Ecommerce',
                        'price' => 6000,
                        'end_price' => 15000,
                        'is_popular' => false,
                        'features' => ['Product Catalog', 'Payment Gateway Ready', 'Basic SEO', 'Responsive Design', 'Order Inquiry/Checkout Flow', 'Domain & Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Business',
                        'price' => 10000,
                        'end_price' => 30000,
                        'is_popular' => true,
                        'features' => ['Up to 5 Pages', 'CMS (Self-editable)', 'Advanced SEO', 'Blog Section', 'Social Media Links', 'Domain & Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Portfolio',
                        'price' => 5000,
                        'end_price' => 15000,
                        'is_popular' => false,
                        'features' => ['Personal/Agency Portfolio', 'Showcase Pages', 'Contact Form', 'Mobile Responsive', 'Fast Loading Layout', 'Domain & Hosting NOT Included']
                    ]
                ]
            ],
            [
                'name' => 'App Development',
                'description' => 'Transform your ideas into powerful mobile applications. We build native and cross-platform (iOS & Android) apps that scale with your business.',
                'full_price' => 70000,
                'deposit_price' => 15000,
                'features' => "iOS & Android Support\nCustom UI/UX Design\nReal-time Notifications\nAPI Integration\nApp Store Submission\nDomain & Hosting Not Included",
                'rating' => 5.0,
                'is_featured' => true,
                'cover_image_path' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&q=80',
                'cta_image_path' => 'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=1200&q=80',
                'faq' => [
                    ['q' => 'Will my app work on both Android and iOS?', 'a' => 'Yes, we use modern cross-platform frameworks like React Native or Flutter to ensure your app runs perfectly on both platforms from a single codebase.'],
                    ['q' => 'Do you handle the App Store submission?', 'a' => 'Yes, we guide you through the process of creating developer accounts and handle the technical part of the submission to Apple and Google.'],
                    ['q' => 'What happens if I need updates later?', 'a' => 'We offer maintenance plans for regular updates, or you can hire us for specific feature additions as your user base grows.'],
                    ['q' => 'Are server costs included?', 'a' => 'No, mobile apps usually require a backend server. We\'ll help you choose the best provider (AWS, Firebase, etc.) based on your needs.']
                ],
                'pricings' => [
                    [
                        'plan_name' => 'MVP',
                        'price' => 25000,
                        'end_price' => 40000,
                        'is_popular' => false,
                        'features' => ['Core Features Only', 'Single Platform', 'Clean UI', 'Basic Backend', '3-month Support', 'Server/Domain NOT Included']
                    ],
                    [
                        'plan_name' => 'Standard',
                        'price' => 40000,
                        'end_price' => 70000,
                        'is_popular' => true,
                        'features' => ['iOS & Android Both', 'User Authentication', 'Push Notifications', 'API Integrations', 'Store Submission', 'Server/Domain NOT Included']
                    ],
                    [
                        'plan_name' => 'Advanced',
                        'price' => 70000,
                        'end_price' => 120000,
                        'is_popular' => false,
                        'features' => ['Real-time Features', 'Custom Admin Panel', 'Advanced Analytics', 'Cloud Storage Integration', 'Scalable Backend', 'Server/Domain NOT Included']
                    ]
                ]
            ],
            [
                'name' => 'Custom CRM',
                'description' => 'Streamline your business workflows with a tailor-made CRM. Manage leads, track sales, and automate customer interactions effortlessly.',
                'full_price' => 60000,
                'deposit_price' => 12000,
                'features' => "Lead Tracking\nCustom Sales Pipeline\nAutomated Email Alerts\nRole-based Access\nData Analytics Dashboard\nDomain & Hosting Not Included",
                'rating' => 4.8,
                'is_featured' => true,
                'cover_image_path' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&q=80',
                'cta_image_path' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
                'faq' => [
                    ['q' => 'Is my data secure?', 'a' => 'Security is our priority. We use high-level encryption and secure server architectures to ensure your business data is protected.'],
                    ['q' => 'Can I import my existing data?', 'a' => 'Yes, we can help you migrate your data from spreadsheets or other CRMs into your new custom solution.'],
                    ['q' => 'Can I integrate with WhatsApp?', 'a' => 'Absolutely. We can integrate WhatsApp API to automate notifications and client communication directly from the CRM.'],
                    ['q' => 'Is there a monthly fee?', 'a' => 'Since we build custom software for you, there are no per-user license fees. You only pay for your own server hosting.']
                ],
                'pricings' => [
                    [
                        'plan_name' => 'Basic',
                        'price' => 15000,
                        'end_price' => 30000,
                        'is_popular' => false,
                        'features' => ['Lead Management', 'Dashboard', 'User Roles', 'Contact Management', 'Simple Reporting', 'Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Pro',
                        'price' => 30000,
                        'end_price' => 60000,
                        'is_popular' => true,
                        'features' => ['Sales Pipeline', 'Email Integration', 'Performance Reports', 'Custom Fields', 'Task Automation', 'Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Enterprise',
                        'price' => 60000,
                        'end_price' => 100000,
                        'is_popular' => false,
                        'features' => ['Unlimited Automation', 'Third-party Integrations', 'Advanced Analytics', 'Multi-department Support', 'Priority Training', 'Hosting NOT Included']
                    ]
                ]
            ],
            [
                'name' => 'AI Integration',
                'description' => 'Leverage the power of Artificial Intelligence to automate tasks and enhance user experiences. From smart chatbots to data analytics, we integrate AI into your workflow.',
                'full_price' => 45000,
                'deposit_price' => 9000,
                'features' => "AI Chatbots\nText Generation\nData Insights\nImage Processing AI\nCustom Training\nAI API Costs Not Included",
                'rating' => 4.9,
                'is_featured' => true,
                'cover_image_path' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&q=80',
                'cta_image_path' => 'https://images.unsplash.com/photo-1531746790731-6c087fecd05a?w=1200&q=80',
                'faq' => [
                    ['q' => 'Which AI models do you use?', 'a' => 'We integrate with industry leaders like OpenAI (GPT-4), Anthropic (Claude), and Meta (Llama), depending on your specific requirements.'],
                    ['q' => 'How much do AI APIs cost?', 'a' => 'AI API costs are consumption-based (pay-as-you-go). We optimize your prompts to keep these costs as low as possible.'],
                    ['q' => 'Can the AI learn my business data?', 'a' => 'Yes, we use RAG (Retrieval-Augmented Generation) so the AI can answer questions based specifically on your documents, PDFs, or website data.'],
                    ['q' => 'Is it compatible with my website?', 'a' => 'Yes, we can add AI features to almost any modern website or application via secure API connections.']
                ],
                'pricings' => [
                    [
                        'plan_name' => 'Chatbot',
                        'price' => 12000,
                        'end_price' => 25000,
                        'is_popular' => false,
                        'features' => ['Smart Customer Support', 'FAQ Knowledge Base', 'Basic Conversation Logic', 'Lead Capture Bot', 'Website Widget', 'AI API/Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Automation',
                        'price' => 25000,
                        'end_price' => 45000,
                        'is_popular' => true,
                        'features' => ['Workflow Automation', 'AI Data Processing', 'Auto-Email Replies', 'Image Generation Integration', 'CRM Sync', 'AI API/Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Custom AI',
                        'price' => 45000,
                        'end_price' => 80000,
                        'is_popular' => false,
                        'features' => ['Fine-tuned Models', 'Complex Multi-step Agents', 'Proprietary Data Training', 'Voice AI Integration', 'Custom Infrastructure', 'AI API/Hosting NOT Included']
                    ]
                ]
            ],
            [
                'name' => 'Website Management',
                'description' => 'Stay focused on your business while we handle your website. Regular updates, security monitoring, and performance optimization to keep your site running smooth.',
                'full_price' => 5999,
                'deposit_price' => 1500,
                'features' => "Monthly Updates\nSecurity Checks\nFull Backups\nPerformance Optimization\nPriority Support\nDomain & Hosting Not Included",
                'rating' => 4.7,
                'is_featured' => false,
                'cover_image_path' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80',
                'cta_image_path' => 'https://images.unsplash.com/photo-1454165833762-0265129b0021?w=1200&q=80',
                'faq' => [
                    ['q' => 'What if my site goes down?', 'a' => 'Our uptime monitoring alerts us immediately. We investigate and fix hosting or code issues as a priority under our management plans.'],
                    ['q' => 'How often do you update the site?', 'a' => 'We perform security and plugin updates weekly, and content updates whenever you request them (frequency depends on your plan).'],
                    ['q' => 'Do you provide reports?', 'a' => 'Yes, Standard and Premium plan users receive monthly reports covering security, performance, and traffic overview.'],
                    ['q' => 'Is this a hosting plan?', 'a' => 'No, this is a MANAGEMENT service. You still pay your host directly for server space, and we ensure everything on that server is optimized.']
                ],
                'pricings' => [
                    [
                        'plan_name' => 'Basic',
                        'price' => 1999,
                        'is_popular' => false,
                        'features' => ['Uptime Monitoring', 'Speed Optimization', 'Security Patches', 'Monthly Backups', 'Email Support', 'Domain/Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Standard',
                        'price' => 3999,
                        'is_popular' => true,
                        'features' => ['Content Updates', 'Analytics Reports', 'Technical Support', 'Broken Link Fixes', 'Image Optimization', 'Domain/Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Premium',
                        'price' => 7999,
                        'is_popular' => false,
                        'features' => ['Priority 24/7 Support', 'SEO Health Checks', 'Form Testing', 'Database Maintenance', 'Custom Performance Tuning', 'Domain/Hosting NOT Included']
                    ]
                ]
            ],
            [
                'name' => 'Website Updation',
                'description' => 'Give your legacy website a modern makeover. We modernize the UI, optimize performance, and ensure your site meets current web standards.',
                'full_price' => 18000,
                'deposit_price' => 4000,
                'features' => "Modern UI Refresh\nMobile Responsiveness\nPerformance Audit\nNew Feature Integration\nSEO Overhaul\nDomain & Hosting Not Included",
                'rating' => 4.8,
                'is_featured' => false,
                'cover_image_path' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&q=80',
                'cta_image_path' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&q=80',
                'faq' => [
                    ['q' => 'Can you update my old WordPress site?', 'a' => 'Yes, we can modernize both WordPress sites and custom-coded legacy applications while preserving your existing data.'],
                    ['q' => 'Will my site be down during updates?', 'a' => 'We usually work on a staging (private) version of your site and only "swap" it with the live version once you approve the changes.'],
                    ['q' => 'Will my SEO ranking drop?', 'a' => 'On the contrary, our updates focus on speed and mobile-friendliness, which typically leads to an improvement in search engine rankings.'],
                    ['q' => 'Can you change my logo and branding too?', 'a' => 'Yes, we can rebranding your entire digital presence as part of our UI Refresh plan.']
                ],
                'pricings' => [
                    [
                        'plan_name' => 'UI Refresh',
                        'price' => 8000,
                        'end_price' => 18000,
                        'is_popular' => true,
                        'features' => ['Modern Layout Design', 'Mobile Optimization', 'New Components/Icons', 'Font/Color Updates', 'Smooth Animations', 'Domain/Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Speed Boost',
                        'price' => 5000,
                        'end_price' => 12000,
                        'is_popular' => false,
                        'features' => ['Core Web Vitals Fix', 'Image Compression', 'Caching Setup', 'Code Minification', 'Loading Time Reduction', 'Domain/Hosting NOT Included']
                    ],
                    [
                        'plan_name' => 'Feature Add-on',
                        'price' => 12000,
                        'end_price' => 25000,
                        'is_popular' => false,
                        'features' => ['New Custom Pages', 'Payment Integration', 'Third-party API Setup', 'Social Media Feeds', 'Advanced Analytics', 'Domain/Hosting NOT Included']
                    ]
                ]
            ]
        ];

        foreach ($services as $sData) {
            $pricings = $sData['pricings'];
            unset($sData['pricings']);

            $sData['slug'] = Str::slug($sData['name']);
            $service = Service::create($sData);

            foreach ($pricings as $pData) {
                $pData['service_id'] = $service->id;
                ServicePricing::create($pData);
            }
        }
    }
}
