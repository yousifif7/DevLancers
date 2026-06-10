<?php

namespace App\Support;

use App\Models\Gigs;
use App\Models\User;
use Illuminate\Support\Str;

class SeoMeta
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $canonical = null,
        public ?string $image = null,
        public string $type = 'website',
        public bool $index = true,
        public ?string $keywords = null,
        public array $jsonLd = [],
    ) {}

    public static function defaults(): self
    {
        return new self(
            title: config('seo.default_title'),
            description: config('seo.default_description'),
            canonical: url('/'),
            image: asset(config('seo.default_image')),
            keywords: config('seo.default_keywords'),
            jsonLd: [self::organizationSchema(), self::websiteSchema()],
        );
    }

    public static function home(?string $scope = null, ?string $query = null): self
    {
        $params = array_filter(['scope' => $scope, 'q' => $query]);
        $canonical = url('/' . ($params ? '?' . http_build_query($params) : ''));

        if ($scope === 'talent' && $query) {
            return new self(
                title: 'Developer Freelancers: ' . $query,
                description: 'Find developer freelancers matching "' . $query . '". Browse Laravel, PHP, full-stack, and API experts on DevLancer.',
                canonical: $canonical,
                image: asset(config('seo.default_image')),
                keywords: $query . ', developer freelancer, hire developer, ' . config('seo.default_keywords'),
                jsonLd: [self::organizationSchema(), self::websiteSchema()],
            );
        }

        if ($scope === 'talent') {
            return new self(
                title: 'Find Developer Freelancers & Talent',
                description: 'Search vetted developer freelancers. Compare ratings, skills, and portfolios. Hire Laravel, PHP, JavaScript, and full-stack experts.',
                canonical: $canonical,
                image: asset(config('seo.default_image')),
                keywords: 'find developer freelancer, hire programmer, laravel expert, ' . config('seo.default_keywords'),
                jsonLd: [self::organizationSchema(), self::websiteSchema()],
            );
        }

        if ($query) {
            return new self(
                title: 'Developer Jobs & Gigs: ' . $query,
                description: 'Browse developer jobs and gigs matching "' . $query . '". Remote contracts for Laravel, PHP, APIs, and full-stack development on DevLancer.',
                canonical: $canonical,
                image: asset(config('seo.default_image')),
                keywords: $query . ', developer job, freelance gig, ' . config('seo.default_keywords'),
                jsonLd: [self::organizationSchema(), self::websiteSchema()],
            );
        }

        return new self(
            title: config('seo.default_title'),
            description: config('seo.default_description'),
            canonical: url('/'),
            image: asset(config('seo.default_image')),
            keywords: config('seo.default_keywords'),
            jsonLd: [self::organizationSchema(), self::websiteSchema()],
        );
    }

    public static function gig(Gigs $gig): self
    {
        $gig->loadMissing('user');
        $typeLabel = $gig->isGig() ? 'Developer gig' : 'Developer job';
        $desc = Str::limit(strip_tags($gig->description), 155);
        $tags = Str::limit(str_replace(',', ', ', $gig->tag), 80);

        return new self(
            title: $gig->title . ' – ' . ucfirst($gig->gig_type),
            description: "{$typeLabel}: {$desc} Budget \${$gig->salary}. Skills: {$tags}. Posted on DevLancer.",
            canonical: url('/gigs/' . $gig->id),
            image: $gig->thumbnailUrl() ?? asset(config('seo.default_image')),
            type: 'article',
            keywords: $gig->tag . ', developer freelance, ' . $gig->title,
            jsonLd: array_filter([
                self::organizationSchema(),
                self::jobPostingSchema($gig),
            ]),
        );
    }

    public static function user(User $user, array $stats = []): self
    {
        $role = $user->isWorker() ? 'Developer freelancer' : 'Client';
        $bio = $user->bio ? Str::limit(strip_tags($user->bio), 120) : '';
        $rating = $stats['average_rating'] ?? null;
        $ratingText = $rating ? " Rated {$rating}/5." : '';

        return new self(
            title: $user->name . ' – ' . ($user->isWorker() ? 'Developer Profile' : 'Client Profile'),
            description: "{$role} on DevLancer.{$ratingText} {$bio} View skills, reviews, and open to hire.",
            canonical: url('/users/' . $user->id),
            image: asset(config('seo.default_image')),
            type: 'profile',
            keywords: $user->name . ', developer freelancer, hire developer, DevLancer profile',
            jsonLd: [
                self::organizationSchema(),
                self::personSchema($user, $stats),
            ],
        );
    }

    public static function guide(): self
    {
        return new self(
            title: 'How DevLancer Works – Guide for Developers & Clients',
            description: 'Learn how to use DevLancer: post developer jobs, offer gigs, hire freelancers, manage contracts, milestone payments, reviews, and disputes.',
            canonical: url('/guide'),
            image: asset(config('seo.default_image')),
            keywords: 'how to use DevLancer, freelance developer guide, hire developers guide',
            jsonLd: [self::organizationSchema(), self::faqSchema()],
        );
    }

    public static function privatePage(string $title, ?string $description = null): self
    {
        return new self(
            title: $title,
            description: $description ?? config('seo.default_description'),
            canonical: url()->current(),
            image: asset(config('seo.default_image')),
            index: false,
        );
    }

    public function fullTitle(): string
    {
        $site = config('seo.site_name');
        $tagline = config('seo.tagline');

        return ($this->title ?? config('seo.default_title')) . ' | ' . $site . ' – ' . $tagline;
    }

    public function robotsContent(): string
    {
        return $this->index ? 'index, follow, max-image-preview:large' : 'noindex, nofollow';
    }

    private static function organizationSchema(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => url('/') . '#organization',
            'name' => config('seo.site_name'),
            'url' => url('/'),
            'logo' => asset(config('seo.default_image')),
            'description' => config('seo.default_description'),
            'sameAs' => array_filter([config('seo.twitter_handle') ? 'https://twitter.com/' . ltrim(config('seo.twitter_handle'), '@') : null]),
        ];
    }

    private static function websiteSchema(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'url' => url('/'),
            'name' => config('seo.site_name'),
            'description' => config('seo.default_description'),
            'publisher' => ['@id' => url('/') . '#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/') . '?scope=jobs&q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    private static function jobPostingSchema(Gigs $gig): ?array
    {
        if ($gig->status !== Gigs::STATUS_OPEN) {
            return null;
        }

        $org = [
            '@type' => 'Organization',
            'name' => $gig->user?->name ?? config('seo.site_name'),
        ];

        return [
            '@type' => 'JobPosting',
            'title' => $gig->title,
            'description' => strip_tags($gig->description),
            'datePosted' => $gig->created_at?->toIso8601String(),
            'employmentType' => 'CONTRACTOR',
            'jobLocationType' => 'TELECOMMUTE',
            'applicantLocationRequirements' => [
                '@type' => 'Country',
                'name' => 'Worldwide',
            ],
            'hiringOrganization' => $org,
            'baseSalary' => [
                '@type' => 'MonetaryAmount',
                'currency' => 'USD',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'value' => (float) $gig->salary,
                    'unitText' => 'PROJECT',
                ],
            ],
            'identifier' => [
                '@type' => 'PropertyValue',
                'name' => config('seo.site_name'),
                'value' => (string) $gig->id,
            ],
            'url' => url('/gigs/' . $gig->id),
            'skills' => array_map('trim', explode(',', $gig->tag)),
        ];
    }

    private static function personSchema(User $user, array $stats): array
    {
        $schema = [
            '@type' => 'Person',
            'name' => $user->name,
            'url' => url('/users/' . $user->id),
            'description' => $user->bio ? strip_tags($user->bio) : null,
            'jobTitle' => $user->isWorker() ? 'Software Developer' : 'Client',
        ];

        if (!empty($stats['average_rating'])) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $stats['average_rating'],
                'bestRating' => 5,
                'ratingCount' => $stats['reviews_count'] ?? 1,
            ];
        }

        return array_filter($schema);
    }

    private static function faqSchema(): array
    {
        return [
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'What is DevLancer?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'DevLancer is a freelance marketplace built for developers. Workers offer gigs and apply to jobs; clients hire talent for Laravel, PHP, full-stack, and API projects.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'What is the difference between a gig and a job?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Gigs are services posted by developers and can be ordered many times. Jobs are projects posted by clients and typically hire one developer.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'How do payments work on DevLancer?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Contracts support single payment on completion or milestone-based payments. Clients pay via Stripe after approving deliverables.',
                    ],
                ],
            ],
        ];
    }
}
