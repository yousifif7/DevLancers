<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\User;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = collect([
            $this->entry(url('/'), now(), 'daily', '1.0'),
            $this->entry(url('/guide'), now()->subDay(), 'monthly', '0.8'),
            $this->entry(url('/?scope=talent'), now(), 'daily', '0.9'),
            $this->entry(url('/?scope=jobs'), now(), 'daily', '0.9'),
        ]);

        $listingUrls = Gigs::where('status', Gigs::STATUS_OPEN)
            ->select('id', 'updated_at')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($gig) => $this->entry(url('/gigs/' . $gig->id), $gig->updated_at, 'weekly', '0.7'));

        $talentUrls = User::where('acc_type', 1)
            ->select('id', 'updated_at')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($user) => $this->entry(url('/users/' . $user->id), $user->updated_at, 'weekly', '0.6'));

        $urls = $urls->merge($listingUrls)->merge($talentUrls);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    private function entry(string $loc, $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
