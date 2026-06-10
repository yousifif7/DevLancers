<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => config('seo.site_name', 'DevLancer'),
            'site_tagline' => config('seo.tagline', 'Freelance Marketplace for Developers'),
            'support_email' => env('MAIL_FROM_ADDRESS', 'support@devlancer.com'),
            'allow_registration' => '1',
            'maintenance_mode' => '0',
            'hero_title' => 'Hire developers. Find dev jobs & gigs.',
            'hero_subtitle' => 'DevLancer connects businesses with Laravel, PHP, and full-stack freelancers. Post jobs, offer services, manage contracts, milestones, and reviews — all in one place.',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value);
        }
    }
}
