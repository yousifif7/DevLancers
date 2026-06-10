<?php

namespace Database\Seeders;

use App\Models\Deliverable;
use App\Models\Dispute;
use App\Models\GigMedia;
use App\Models\Gigs;
use App\Models\Milestone;
use App\Models\Payment;
use App\Models\Proposal;
use App\Models\Requests;
use App\Models\Review;
use App\Models\SupportTicket;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class LaunchDataSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make(env('SEED_PASSWORD', 'password123'));

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@devlancer.com',
            'password' => $password,
            'gender' => 'Other',
            'bio' => 'DevLancer platform administrator.',
            'address' => 'Remote',
            'acc_type' => 2,
            'is_admin' => true,
        ]);

        $client = User::create([
            'name' => 'Mike Johnson',
            'email' => 'client@devlancer.com',
            'password' => $password,
            'gender' => 'Male',
            'bio' => 'Startup founder building SaaS products. I hire vetted developers for web apps and APIs.',
            'address' => 'New York, USA',
            'acc_type' => 2,
        ]);

        $client2 = User::create([
            'name' => 'Lisa Park',
            'email' => 'client2@devlancer.com',
            'password' => $password,
            'gender' => 'Female',
            'bio' => 'Marketing agency owner. We outsource development and design for client projects.',
            'address' => 'London, UK',
            'acc_type' => 2,
        ]);

        $worker = User::create([
            'name' => 'Sarah Chen',
            'email' => 'worker@devlancer.com',
            'password' => $password,
            'gender' => 'Female',
            'bio' => 'Full-stack developer focused on Laravel, APIs, and clean architecture. I ship production-ready code with tests and documentation.',
            'headline' => 'Senior Laravel Developer | REST APIs & SaaS',
            'skills' => 'Laravel, PHP, MySQL, REST API, Vue.js, Stripe, TDD',
            'experience_years' => 5,
            'education' => 'B.Sc. Computer Science',
            'certifications' => 'Laravel Certified Developer',
            'portfolio_url' => 'https://example.com/sarah-chen',
            'github_url' => 'https://github.com/example/sarahchen',
            'hourly_rate' => 55,
            'address' => 'Remote',
            'acc_type' => 1,
        ]);

        $worker2 = User::create([
            'name' => 'Ahmed Hassan',
            'email' => 'worker2@devlancer.com',
            'password' => $password,
            'gender' => 'Male',
            'bio' => 'UI/UX designer and frontend developer. I turn product ideas into polished, accessible interfaces.',
            'headline' => 'UI/UX Designer & Frontend Developer',
            'skills' => 'Figma, UI Design, UX Research, React, Vue.js, Tailwind CSS',
            'experience_years' => 4,
            'education' => 'BA Graphic Design',
            'certifications' => 'Google UX Design Certificate',
            'portfolio_url' => 'https://example.com/ahmed-hassan',
            'github_url' => 'https://github.com/example/ahmedhassan',
            'hourly_rate' => 45,
            'address' => 'Cairo, Egypt',
            'acc_type' => 1,
        ]);

        $worker3 = User::create([
            'name' => 'James Okonkwo',
            'email' => 'worker3@devlancer.com',
            'password' => $password,
            'gender' => 'Male',
            'bio' => 'DevOps and backend engineer. Docker, CI/CD, and scalable cloud deployments.',
            'headline' => 'DevOps Engineer | AWS & Laravel Hosting',
            'skills' => 'AWS, Docker, CI/CD, Linux, Nginx, Laravel Forge, Redis',
            'experience_years' => 6,
            'education' => 'M.Sc. Information Systems',
            'certifications' => 'AWS Solutions Architect Associate',
            'portfolio_url' => 'https://example.com/james-okonkwo',
            'github_url' => 'https://github.com/example/jokonkwo',
            'hourly_rate' => 60,
            'address' => 'Lagos, Nigeria',
            'acc_type' => 1,
        ]);

        $gigLaravel = $this->createListing($worker, [
            'gig_type' => 'gig',
            'status' => Gigs::STATUS_OPEN,
            'title' => 'Laravel REST API Development',
            'tag' => 'Laravel,PHP,API,REST,MySQL',
            'description' => 'I will build a secure REST API with Laravel: authentication (Sanctum), validation, pagination, Swagger docs, and deployment notes. Ideal for mobile apps or SPA frontends.',
            'salary' => 450,
        ], ['gig-logo_default_1024x1024.png', 'gigs_panel.png'], true);

        $gigDesign = $this->createListing($worker2, [
            'gig_type' => 'gig',
            'status' => Gigs::STATUS_OPEN,
            'title' => 'Modern UI/UX Design for Web Apps',
            'tag' => 'Design,UI,UX,Figma,Prototyping',
            'description' => 'Professional UI/UX for web applications. Wireframes, high-fidelity mockups, interactive prototype, and a reusable design system in Figma.',
            'salary' => 350,
        ], ['job.png', 'job1.png'], true);

        $gigDevOps = $this->createListing($worker3, [
            'gig_type' => 'gig',
            'status' => Gigs::STATUS_OPEN,
            'title' => 'Deploy & Host Your Laravel App on AWS',
            'tag' => 'DevOps,AWS,Docker,CI/CD,Laravel',
            'description' => 'Production deployment for Laravel: EC2 or ECS, RDS, Redis, SSL, backups, and GitHub Actions CI/CD pipeline.',
            'salary' => 500,
        ], ['gigs_logo.png'], false);

        $jobEcommerce = $this->createListing($client, [
            'gig_type' => 'job',
            'status' => Gigs::STATUS_OPEN,
            'title' => 'Build E-commerce Website with Laravel',
            'tag' => 'Laravel,E-commerce,Stripe,Admin Panel',
            'description' => 'Full e-commerce platform: product catalog with filters, cart, Stripe checkout, order management, and admin panel. Prefer 4–6 weeks; milestones welcome.',
            'salary' => 800,
        ], ['job1.png'], true);

        $jobLanding = $this->createListing($client, [
            'gig_type' => 'job',
            'status' => Gigs::STATUS_OPEN,
            'title' => 'Landing Page for SaaS Product',
            'tag' => 'Landing Page,HTML,CSS,JavaScript,SEO',
            'description' => 'Responsive, conversion-focused landing page for our SaaS startup. Hero, features, pricing, testimonials, and contact form. Must score 90+ on Lighthouse.',
            'salary' => 250,
        ], ['job.png'], false);

        $jobMobile = $this->createListing($client2, [
            'gig_type' => 'job',
            'status' => Gigs::STATUS_OPEN,
            'title' => 'React Native App Bug Fixes & Performance',
            'tag' => 'React Native,JavaScript,Mobile,Performance',
            'description' => 'Existing React Native app needs crash fixes, list performance improvements, and push notification setup. 2-week sprint.',
            'salary' => 600,
        ], ['gigs_panel.png'], false);

        $jobFilled = $this->createListing($client2, [
            'gig_type' => 'job',
            'status' => Gigs::STATUS_FILLED,
            'title' => 'WordPress Blog Migration to Laravel',
            'tag' => 'WordPress,Laravel,Migration,SEO',
            'description' => 'Migrate WordPress blog (~200 posts) to a custom Laravel CMS. Preserve URLs, redirects, and SEO metadata.',
            'salary' => 500,
        ], ['job1.png'], false);

        $proposalSarah = Proposal::create([
            'gig_id' => $jobEcommerce->id,
            'user_id' => $worker->id,
            'cover_letter' => 'Hi Mike! I have shipped three Laravel e-commerce projects with Stripe. I propose a milestone plan: catalog first, then checkout, then admin. Delivery in 5 weeks.',
            'bid_amount' => 750,
            'delivery_days' => 35,
            'status' => Proposal::STATUS_PENDING,
        ]);

        Proposal::create([
            'gig_id' => $jobEcommerce->id,
            'user_id' => $worker2->id,
            'cover_letter' => 'I can own the storefront UX and work alongside your backend developer for a polished buyer experience.',
            'bid_amount' => 650,
            'delivery_days' => 40,
            'status' => Proposal::STATUS_SHORTLISTED,
        ]);

        Proposal::create([
            'gig_id' => $jobLanding->id,
            'user_id' => $worker->id,
            'cover_letter' => 'I will build a fast Blade + Tailwind landing page with analytics, SEO meta, and a working contact form in one week.',
            'bid_amount' => 220,
            'delivery_days' => 7,
            'status' => Proposal::STATUS_PENDING,
        ]);

        Proposal::create([
            'gig_id' => $jobMobile->id,
            'user_id' => $worker3->id,
            'cover_letter' => 'I have optimized React Native apps for production. I can profile crashes and improve FlatList performance within your two-week window.',
            'bid_amount' => 580,
            'delivery_days' => 14,
            'status' => Proposal::STATUS_PENDING,
        ]);

        Proposal::create([
            'gig_id' => $gigLaravel->id,
            'user_id' => $client->id,
            'cover_letter' => 'We need a mobile backend API. Your Laravel portfolio is a great fit — can you start next week?',
            'bid_amount' => 420,
            'delivery_days' => 14,
            'status' => Proposal::STATUS_PENDING,
        ]);

        $acceptedProposal = Proposal::create([
            'gig_id' => $jobFilled->id,
            'user_id' => $worker->id,
            'cover_letter' => 'I have migrated WordPress to Laravel before with full URL preservation and SEO redirects.',
            'bid_amount' => 480,
            'delivery_days' => 21,
            'status' => Proposal::STATUS_ACCEPTED,
        ]);

        Proposal::create([
            'gig_id' => $jobFilled->id,
            'user_id' => $worker2->id,
            'cover_letter' => 'Happy to support design assets during the migration if needed.',
            'bid_amount' => 400,
            'delivery_days' => 25,
            'status' => Proposal::STATUS_REJECTED,
        ]);

        $draftTask = Tasks::create([
            'user_id' => $worker->id,
            'gig_id' => $gigLaravel->id,
            'proposal_id' => null,
            'status' => Tasks::STATUS_DRAFT,
            'owner' => $client->id,
            'content' => 'API development for mobile app backend.',
            'scope_of_work' => 'REST API with Sanctum auth, CRUD endpoints, tests, and Swagger documentation.',
            'terms' => 'Payment upon client approval of deliverables.',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'client_accepted_at' => now(),
            'price' => 420,
            'payment_flag' => 0,
        ]);

        $activeTask = Tasks::create([
            'user_id' => $worker->id,
            'gig_id' => $jobFilled->id,
            'proposal_id' => $acceptedProposal->id,
            'status' => Tasks::STATUS_ACTIVE,
            'owner' => $client2->id,
            'content' => $acceptedProposal->cover_letter,
            'scope_of_work' => 'WordPress to Laravel CMS migration with URL and SEO preservation.',
            'terms' => 'Payment upon client approval. Delivery within 21 days.',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(16)->toDateString(),
            'worker_accepted_at' => now()->subDays(4),
            'client_accepted_at' => now()->subDays(5),
            'price' => 480,
            'payment_structure' => Tasks::PAYMENT_MILESTONES,
            'milestones_agreed_client_at' => now()->subDays(4),
            'milestones_agreed_worker_at' => now()->subDays(4),
            'payment_flag' => 0,
        ]);

        Milestone::create([
            'task_id' => $activeTask->id,
            'proposed_by' => $client2->id,
            'sort_order' => 1,
            'title' => 'Content export & schema',
            'description' => 'Export all posts, categories, and media from WordPress.',
            'amount' => 160,
            'due_date' => now()->addDays(5),
            'status' => Milestone::STATUS_APPROVED,
        ]);

        Milestone::create([
            'task_id' => $activeTask->id,
            'proposed_by' => $worker->id,
            'sort_order' => 2,
            'title' => 'Laravel CMS build',
            'description' => 'Admin panel, post editor, and public blog templates.',
            'amount' => 200,
            'due_date' => now()->addDays(12),
            'status' => Milestone::STATUS_PENDING,
        ]);

        Milestone::create([
            'task_id' => $activeTask->id,
            'proposed_by' => $worker->id,
            'sort_order' => 3,
            'title' => 'Redirects & launch',
            'description' => '301 redirects, QA, and production deployment.',
            'amount' => 120,
            'due_date' => now()->addDays(16),
            'status' => Milestone::STATUS_PROPOSED,
        ]);

        Deliverable::create([
            'task_id' => $activeTask->id,
            'user_id' => $worker->id,
            'notes' => 'WordPress export completed. Attached spreadsheet mapping old URLs to new Laravel routes. Ready for your review on milestone 1.',
            'link' => 'https://example.com/migration-preview',
            'file_path' => $this->seedTextAttachment('migration-url-map.txt', 'URL mapping for WordPress → Laravel migration (seed file).'),
            'status' => Deliverable::STATUS_SUBMITTED,
        ]);

        $disputedTask = Tasks::create([
            'user_id' => $worker->id,
            'gig_id' => $jobEcommerce->id,
            'proposal_id' => $proposalSarah->id,
            'status' => Tasks::STATUS_DISPUTED,
            'owner' => $client->id,
            'content' => $proposalSarah->cover_letter,
            'scope_of_work' => 'Build e-commerce platform with Laravel and Stripe integration.',
            'terms' => 'Payment upon milestone approval.',
            'start_date' => now()->subDays(12)->toDateString(),
            'end_date' => now()->addDays(23)->toDateString(),
            'worker_accepted_at' => now()->subDays(11),
            'client_accepted_at' => now()->subDays(12),
            'price' => 750,
            'payment_flag' => 0,
        ]);

        Dispute::create([
            'task_id' => $disputedTask->id,
            'opened_by' => $client->id,
            'reason' => 'Milestone 1 deliverables are incomplete: product filters and admin catalog are missing. We agreed on a 5-week timeline but week 2 scope is not met. Requesting admin mediation.',
            'status' => Dispute::STATUS_OPEN,
        ]);

        Tasks::create([
            'user_id' => $worker->id,
            'gig_id' => $jobLanding->id,
            'proposal_id' => null,
            'status' => Tasks::STATUS_COMPLETED,
            'owner' => $client->id,
            'content' => 'Landing page delivered — all sections, mobile responsive, Lighthouse 92.',
            'scope_of_work' => 'Responsive SaaS landing page with hero, features, pricing, and contact form.',
            'terms' => 'Payment upon approval.',
            'start_date' => now()->subDays(14)->toDateString(),
            'end_date' => now()->subDays(7)->toDateString(),
            'worker_accepted_at' => now()->subDays(14),
            'client_accepted_at' => now()->subDays(14),
            'price' => 250,
            'payment_flag' => 0,
        ]);

        $paidTask = Tasks::create([
            'user_id' => $worker2->id,
            'gig_id' => $gigDesign->id,
            'proposal_id' => null,
            'status' => Tasks::STATUS_COMPLETED,
            'owner' => $client->id,
            'content' => 'Complete UI/UX design package delivered in Figma with component library.',
            'scope_of_work' => 'Dashboard UI design with component library and handoff notes.',
            'terms' => 'Payment upon approval.',
            'start_date' => now()->subDays(30)->toDateString(),
            'end_date' => now()->subDays(20)->toDateString(),
            'worker_accepted_at' => now()->subDays(30),
            'client_accepted_at' => now()->subDays(30),
            'price' => 350,
            'payment_flag' => 1,
        ]);

        Payment::create([
            'task_id' => $paidTask->id,
            'user_id' => $client->id,
            'amount' => 350,
            'currency' => 'USD',
            'status' => Payment::STATUS_COMPLETED,
            'paid_at' => now()->subDays(18),
        ]);

        Review::create([
            'task_id' => $paidTask->id,
            'reviewer_id' => $client->id,
            'reviewee_id' => $worker2->id,
            'rating' => 5,
            'comment' => 'Excellent design work! Ahmed delivered ahead of schedule and was very communicative throughout.',
            'type' => Review::TYPE_CLIENT_TO_WORKER,
            'status' => Review::STATUS_APPROVED,
        ]);

        Review::create([
            'task_id' => $paidTask->id,
            'reviewer_id' => $worker2->id,
            'reviewee_id' => $client->id,
            'rating' => 5,
            'comment' => 'Clear requirements and fast feedback. Would work with Mike again.',
            'type' => Review::TYPE_WORKER_TO_CLIENT,
            'status' => Review::STATUS_APPROVED,
        ]);

        $pendingReviewTask = Tasks::create([
            'user_id' => $worker3->id,
            'gig_id' => $gigDevOps->id,
            'proposal_id' => null,
            'status' => Tasks::STATUS_COMPLETED,
            'owner' => $client->id,
            'content' => 'AWS deployment and CI/CD pipeline completed successfully.',
            'scope_of_work' => 'Laravel app on AWS with GitHub Actions deploy pipeline.',
            'terms' => 'Payment upon approval.',
            'start_date' => now()->subDays(20)->toDateString(),
            'end_date' => now()->subDays(10)->toDateString(),
            'worker_accepted_at' => now()->subDays(20),
            'client_accepted_at' => now()->subDays(20),
            'price' => 500,
            'payment_flag' => 1,
        ]);

        Payment::create([
            'task_id' => $pendingReviewTask->id,
            'user_id' => $client->id,
            'amount' => 500,
            'currency' => 'USD',
            'status' => Payment::STATUS_COMPLETED,
            'paid_at' => now()->subDays(8),
        ]);

        Review::create([
            'task_id' => $pendingReviewTask->id,
            'reviewer_id' => $client->id,
            'reviewee_id' => $worker3->id,
            'rating' => 4,
            'comment' => 'Solid DevOps work. Pipeline is running smoothly — waiting on worker review before it goes public.',
            'type' => Review::TYPE_CLIENT_TO_WORKER,
            'status' => Review::STATUS_PENDING,
        ]);

        $chatPairs = [
            [$client->id, $worker->id, $client->name, 'Hi Sarah! I saw your proposal on the e-commerce job. Can we discuss milestones?'],
            [$worker->id, $client->id, $worker->name, 'Hi Mike! Absolutely — I suggest catalog, checkout, then admin. I can start Monday.'],
            [$client->id, $worker->id, $client->name, 'That works. I will review your proposal and shortlist you today.'],
            [$admin->id, $worker->id, $admin->name, 'Welcome to DevLancer! Complete your profile to appear in talent search.'],
            [$worker->id, $admin->id, $worker->name, 'Thanks! Profile and gigs are updated.'],
            [$client2->id, $worker2->id, $client2->name, 'Hi Ahmed, love your design gig portfolio. Are you free next month?'],
            [$worker2->id, $client2->id, $worker2->name, 'Hi Lisa! Yes — send project details and timeline.'],
            [$client->id, $worker3->id, $client->name, 'James, the AWS deploy looks great. Leaving a review once you submit yours.'],
        ];

        foreach ($chatPairs as [$senderId, $receiverId, $senderName, $body]) {
            Requests::create([
                'user_id' => $senderId,
                'gig_id' => null,
                'reciever' => $receiverId,
                'sender' => $senderName,
                'message' => $body,
            ]);
        }

        Requests::create([
            'user_id' => $worker->id,
            'gig_id' => $jobEcommerce->id,
            'reciever' => $client->id,
            'sender' => $worker->name,
            'message' => 'Proposal submitted for the e-commerce project. Happy to walk through my milestone plan.',
        ]);

        SupportTicket::create([
            'user_id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'subject' => 'How do milestone payments work?',
            'message' => 'I want to split my e-commerce contract into milestones. Can both parties propose milestones before work starts?',
            'status' => SupportTicket::STATUS_OPEN,
        ]);

        Review::create([
            'task_id' => $disputedTask->id,
            'reviewer_id' => $worker->id,
            'reviewee_id' => $client->id,
            'rating' => 2,
            'comment' => 'Scope kept changing mid-sprint. Needs admin review before publishing.',
            'type' => Review::TYPE_WORKER_TO_CLIENT,
            'status' => Review::STATUS_PENDING,
        ]);

        $this->command?->info('');
        $this->command?->info('Launch data seeded successfully.');
        $this->command?->info('Password for all accounts: ' . env('SEED_PASSWORD', 'password123'));
        $this->command?->table(
            ['Role', 'Email', 'Name'],
            [
                ['Admin', 'admin@devlancer.com', 'Admin User'],
                ['Client', 'client@devlancer.com', 'Mike Johnson'],
                ['Client', 'client2@devlancer.com', 'Lisa Park'],
                ['Worker', 'worker@devlancer.com', 'Sarah Chen'],
                ['Worker', 'worker2@devlancer.com', 'Ahmed Hassan'],
                ['Worker', 'worker3@devlancer.com', 'James Okonkwo'],
            ]
        );
    }

    private function createListing(User $owner, array $data, array $imageFiles = [], bool $withBrief = false): Gigs
    {
        $gig = Gigs::create(array_merge([
            'user_id' => $owner->id,
            'email' => $owner->email,
        ], $data));

        foreach ($imageFiles as $index => $file) {
            $path = $this->copySeedAsset('gigs/images', $file, "gig{$gig->id}_{$index}_{$file}");
            GigMedia::create([
                'gig_id' => $gig->id,
                'type' => GigMedia::TYPE_IMAGE,
                'path' => $path,
                'original_name' => $file,
                'sort_order' => $index + 1,
            ]);

            if ($index === 0) {
                $gig->update(['image' => $path]);
            }
        }

        if ($withBrief) {
            $briefPath = $this->seedTextAttachment(
                "gig-{$gig->id}-brief.txt",
                "Project brief for: {$gig->title}\n\nBudget: \${$gig->salary}\nTags: {$gig->tag}\n\nSee listing description for full requirements."
            );

            GigMedia::create([
                'gig_id' => $gig->id,
                'type' => GigMedia::TYPE_ATTACHMENT,
                'path' => $briefPath,
                'original_name' => 'project-brief.txt',
                'sort_order' => 1,
            ]);
        }

        return $gig->fresh();
    }

    private function copySeedAsset(string $subdir, string $sourceBasename, string $destBasename): string
    {
        $source = public_path('images/' . $sourceBasename);
        $dir = public_path('uploads/' . $subdir);

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $dest = $dir . '/' . $destBasename;
        if (File::exists($source)) {
            File::copy($source, $dest);
        } else {
            File::put($dest, '');
        }

        return 'uploads/' . $subdir . '/' . $destBasename;
    }

    private function seedTextAttachment(string $filename, string $contents): string
    {
        $dir = public_path('uploads/gigs/files');

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($dir . '/' . $filename, $contents);

        return 'uploads/gigs/files/' . $filename;
    }
}
