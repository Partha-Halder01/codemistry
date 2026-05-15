<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class DummyServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $s1 = Service::create([
            'name' => 'Web Application Development',
            'description' => 'Fast, scalable, and secure custom web applications using modern stacks like React and Laravel.',
            'full_price' => 15000000,
            'deposit_price' => 5000000,
            'features' => "React Frontend\nLaravel Backend\nDatabase Design\nAPI Integration\nResponsive UI",
            'cover_image_path' => 'https://images.unsplash.com/photo-1547658719-da2b51169166?q=80&w=1964&auto=format&fit=crop',
            'faq' => [
                ['q' => 'How long does a typical project take?', 'a' => 'Most custom applications take 6 to 12 weeks from planning to launch.'],
                ['q' => 'Do you provide ongoing support?', 'a' => 'Yes, we offer maintenance contracts to ensure your app stays updated.']
            ]
        ]);
        $s1->pricings()->create(['plan_name' => 'Basic App', 'price' => 5000000, 'features' => ['Up to 5 Pages', 'Basic Auth', '1 Month Support'], 'is_popular' => false]);
        $s1->pricings()->create(['plan_name' => 'Pro Web App', 'price' => 15000000, 'features' => ['Unlimited Pages', 'Complex Logic & Roles', 'Custom API', '6 Month Support'], 'is_popular' => true]);

        $s2 = Service::create([
            'name' => 'E-Commerce Solutions',
            'description' => 'High-converting online stores with custom payment gateways, inventory sync, and admin dashboards.',
            'full_price' => 25000000,
            'deposit_price' => 10000000,
            'features' => "Custom Storefront\nPayment Gateway\nInventory Sync\nAdmin Panel\nSEO Optimized",
            'cover_image_path' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?q=80&w=1932&auto=format&fit=crop',
            'faq' => [
                ['q' => 'Which payment gateways do you support?', 'a' => 'We support Stripe, Razorpay, PayPal, and offline bank transfers.'],
                ['q' => 'Can I manage products myself?', 'a' => 'Absolutely. We provide a fully featured admin panel to manage products, orders, and coupons.']
            ]
        ]);
        $s2->pricings()->create(['plan_name' => 'Standard Store', 'price' => 10000000, 'features' => ['Standard Theme', '1 Payment Gateway', 'Up to 100 Products'], 'is_popular' => false]);
        $s2->pricings()->create(['plan_name' => 'Enterprise Store', 'price' => 30000000, 'features' => ['Fully Custom React UI', 'Multi-Gateway', 'Unlimited Products', 'Advanced Analytics'], 'is_popular' => true]);

        $s3 = Service::create([
            'name' => 'Custom CRM Development',
            'description' => 'Tailored customer relationship management systems built specifically for your workflow.',
            'full_price' => 35000000,
            'deposit_price' => 15000000,
            'features' => "Lead Tracking\nCustom Workflows\nEmail Integration\nReporting Dashboard\nTeam Roles",
            'cover_image_path' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop',
            'faq' => [
                ['q' => 'Is it cloud-based?', 'a' => 'Yes, our CRMs are deployed on scalable cloud infrastructure.'],
            ]
        ]);
        $s3->pricings()->create(['plan_name' => 'CRM Setup', 'price' => 35000000, 'features' => ['Custom Modules', 'Automations', 'Role Management'], 'is_popular' => true]);

        $s4 = Service::create([
            'name' => 'AI Integration',
            'description' => 'Incorporate smart AI features, chatbots, and localized automation directly into your apps.',
            'full_price' => 8000000,
            'deposit_price' => 4000000,
            'features' => "Smart Chatbot\nAutomated Responses\nData Analysis\nLLM Integration",
            'cover_image_path' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=2070&auto=format&fit=crop',
            'faq' => [
                ['q' => 'What models do you use?', 'a' => 'We primarily utilize OpenAI and Claude APIs depending on the use case.']
            ]
        ]);
        $s4->pricings()->create(['plan_name' => 'Chatbot Plugin', 'price' => 8000000, 'features' => ['Trained on your data', 'Website widget', 'Analytics logs'], 'is_popular' => true]);
    }
}
