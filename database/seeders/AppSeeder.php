<?php

namespace Database\Seeders;

use App\Models\App;
use App\Models\AppPricingPlan;
use Illuminate\Database\Seeder;

class AppSeeder extends Seeder
{
    public function run(): void
    {
        // PostMaid - Social Media Content Generator
        $postmaid = App::create([
            'slug' => 'postmaid',
            'name' => 'PostMaid',
            'description' => 'AI-powered social media content generator with smart scheduling. Generate posts, captions, and hashtags optimized for engagement using ML timeline predictions.',
            'tagline' => 'Never run out of content ideas again',
            'category' => 'social-media',
            'features' => [
                'AI-generated captions & hashtags',
                'Image generation with DALL-E',
                'Smart scheduling (ML timeline)',
                'Multi-platform (Instagram, TikTok, LinkedIn)',
                'Analytics & engagement tracking',
            ],
            'required_integrations' => ['openai', 'instagram', 'tiktok', 'linkedin'],
            'is_active' => true,
            'status' => 'beta',
            'sort_order' => 1,
        ]);

        AppPricingPlan::create([
            'app_id' => $postmaid->id,
            'name' => 'Solo',
            'monthly_price' => 29.00,
            'yearly_price' => 290.00, // 2 months free
            'features' => ['30 posts/month', '1 social account', 'Basic analytics'],
            'limits' => ['posts_per_month' => 30, 'accounts' => 1],
            'sort_order' => 1,
        ]);

        AppPricingPlan::create([
            'app_id' => $postmaid->id,
            'name' => 'Pro',
            'monthly_price' => 49.00,
            'yearly_price' => 490.00,
            'features' => ['100 posts/month', '5 social accounts', 'Advanced analytics', 'Priority support'],
            'limits' => ['posts_per_month' => 100, 'accounts' => 5],
            'sort_order' => 2,
        ]);

        AppPricingPlan::create([
            'app_id' => $postmaid->id,
            'name' => 'Business',
            'monthly_price' => 79.00,
            'yearly_price' => 790.00,
            'features' => ['Unlimited posts', 'Unlimited accounts', 'Team collaboration', 'White-label reports'],
            'limits' => ['posts_per_month' => -1, 'accounts' => -1], // -1 = unlimited
            'sort_order' => 3,
        ]);

        // VideoPlan - Video Content Planning Assistant
        $videoplan = App::create([
            'slug' => 'videoplan',
            'name' => 'VideoPlan',
            'description' => 'AI video content planner for creators. Generate complete video scripts, shot lists, equipment recommendations, and SEO-optimized titles/descriptions.',
            'tagline' => 'Plan your next viral video in minutes',
            'category' => 'video',
            'features' => [
                'AI script generation',
                'Shot list & storyboard',
                'Equipment recommendations',
                'SEO title & description',
                'Thumbnail ideas',
            ],
            'required_integrations' => ['openai'],
            'is_active' => true,
            'status' => 'beta',
            'sort_order' => 2,
        ]);

        AppPricingPlan::create([
            'app_id' => $videoplan->id,
            'name' => 'Solo',
            'monthly_price' => 19.00,
            'yearly_price' => 190.00,
            'features' => ['10 video plans/month', 'Basic templates', 'PDF export'],
            'limits' => ['videos_per_month' => 10],
            'sort_order' => 1,
        ]);

        AppPricingPlan::create([
            'app_id' => $videoplan->id,
            'name' => 'Pro',
            'monthly_price' => 39.00,
            'yearly_price' => 390.00,
            'features' => ['50 video plans/month', 'Advanced templates', 'Team collaboration'],
            'limits' => ['videos_per_month' => 50],
            'sort_order' => 2,
        ]);

        AppPricingPlan::create([
            'app_id' => $videoplan->id,
            'name' => 'Business',
            'monthly_price' => 79.00,
            'yearly_price' => 790.00,
            'features' => ['Unlimited video plans', 'Custom templates', 'Priority support'],
            'limits' => ['videos_per_month' => -1],
            'sort_order' => 3,
        ]);

        // ReferAIl - Referral Program Automation
        $referail = App::create([
            'slug' => 'referail',
            'name' => 'ReferAIl',
            'description' => 'AI-powered referral program automation. Smart email sequences, gamification, and persuasion techniques to turn customers into advocates.',
            'tagline' => 'Automate word-of-mouth growth',
            'category' => 'crm',
            'features' => [
                'AI-generated referral emails',
                'Gamification & rewards',
                'Tracking & analytics',
                'Zero commission (BYOK)',
                'CRM integration',
            ],
            'required_integrations' => ['openai', 'smtp'],
            'is_active' => true,
            'status' => 'coming_soon',
            'sort_order' => 3,
        ]);

        AppPricingPlan::create([
            'app_id' => $referail->id,
            'name' => 'Starter',
            'monthly_price' => 79.00,
            'yearly_price' => 790.00,
            'features' => ['500 referrals/month', '2 campaigns', 'Basic analytics'],
            'limits' => ['referrals_per_month' => 500],
            'sort_order' => 1,
        ]);

        AppPricingPlan::create([
            'app_id' => $referail->id,
            'name' => 'Growth',
            'monthly_price' => 149.00,
            'yearly_price' => 1490.00,
            'features' => ['2,000 referrals/month', 'Unlimited campaigns', 'Advanced analytics'],
            'limits' => ['referrals_per_month' => 2000],
            'sort_order' => 2,
        ]);

        AppPricingPlan::create([
            'app_id' => $referail->id,
            'name' => 'Enterprise',
            'monthly_price' => 299.00,
            'yearly_price' => 2990.00,
            'features' => ['Unlimited referrals', 'White-label', 'Dedicated support', 'Custom integrations'],
            'limits' => ['referrals_per_month' => -1],
            'sort_order' => 3,
        ]);

        // EmailScan - Email Validation & Cleanup
        $emailscan = App::create([
            'slug' => 'emailscan',
            'name' => 'EmailScan',
            'description' => 'AI-powered email list validation and cleanup. Remove bounces, catch-alls, and disposable emails to improve deliverability.',
            'tagline' => 'Keep your email list clean and deliverable',
            'category' => 'email',
            'features' => [
                'Bulk email validation',
                'Disposable email detection',
                'Catch-all removal',
                'Deliverability scoring',
                'CSV export',
            ],
            'required_integrations' => [],
            'is_active' => true,
            'status' => 'coming_soon',
            'sort_order' => 4,
        ]);

        AppPricingPlan::create([
            'app_id' => $emailscan->id,
            'name' => 'Basic',
            'monthly_price' => 29.00,
            'yearly_price' => 290.00,
            'features' => ['5,000 validations/month', 'Basic reporting'],
            'limits' => ['validations_per_month' => 5000],
            'sort_order' => 1,
        ]);

        AppPricingPlan::create([
            'app_id' => $emailscan->id,
            'name' => 'Pro',
            'monthly_price' => 79.00,
            'yearly_price' => 790.00,
            'features' => ['25,000 validations/month', 'API access', 'Advanced reporting'],
            'limits' => ['validations_per_month' => 25000],
            'sort_order' => 2,
        ]);

        AppPricingPlan::create([
            'app_id' => $emailscan->id,
            'name' => 'Enterprise',
            'monthly_price' => 199.00,
            'yearly_price' => 1990.00,
            'features' => ['Unlimited validations', 'Dedicated IP', 'Priority support'],
            'limits' => ['validations_per_month' => -1],
            'sort_order' => 3,
        ]);
    }
}
