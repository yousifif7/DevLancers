<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\User;
use App\Models\Review;
use App\Models\SiteSetting;
use App\Support\SeoMeta;
use App\Services\UserStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->input('scope', 'jobs');
        if (!in_array($scope, ['jobs', 'talent'], true)) {
            $scope = 'jobs';
        }

        $q = trim($request->input('q', $request->input('search', '')));

        return $scope === 'talent'
            ? $this->talentResults($request, $q)
            : $this->jobsResults($request, $q);
    }

    private function jobsResults(Request $request, string $q)
    {
        $sort = $request->input('sort', 'newest');
        $type = $request->input('type');

        $query = Gigs::query()
            ->where('status', Gigs::STATUS_OPEN)
            ->with(['user'])
            ->withCount('proposals');

        if ($type === 'gig' || $type === 'job') {
            $query->where('gig_type', $type);
        }

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', '%' . $q . '%')
                    ->orWhere('tag', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $q . '%'));
            });
        } elseif ($request->filled('tag')) {
            $query->where('tag', 'like', '%' . $request->tag . '%');
        }

        $query = match ($sort) {
            'budget_asc' => $query->orderBy('salary'),
            'budget_desc' => $query->orderByDesc('salary'),
            'proposals' => $query->orderByDesc('proposals_count'),
            default => $query->latest(),
        };

        $results = $query->paginate(10)->withQueryString();

        return view('search.index', [
            'scope' => 'jobs',
            'q' => $q,
            'results' => $results,
            'sort' => $sort,
            'type' => $type,
            'seoMeta' => SeoMeta::home('jobs', $q !== '' ? $q : null),
            'heroTitle' => Schema::hasTable('site_settings') ? SiteSetting::get('hero_title') : 'Find the perfect developer for your project',
            'heroSubtitle' => Schema::hasTable('site_settings') ? SiteSetting::get('hero_subtitle') : 'Browse developer jobs, gigs, and freelance talent. Laravel, PHP, full-stack & more.',
        ]);
    }

    private function talentResults(Request $request, string $q)
    {
        $sort = $request->input('sort', 'rating');

        $query = User::query()
            ->where('acc_type', 1)
            ->withCount([
                'gigs as open_gigs_count' => fn ($g) => $g->where('status', Gigs::STATUS_OPEN),
            ])
            ->with(['gigs' => fn ($g) => $g->where('status', Gigs::STATUS_OPEN)->select('id', 'user_id', 'title', 'tag', 'salary')]);

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', '%' . $q . '%')
                    ->orWhere('bio', 'like', '%' . $q . '%')
                    ->orWhere('headline', 'like', '%' . $q . '%')
                    ->orWhere('skills', 'like', '%' . $q . '%')
                    ->orWhere('education', 'like', '%' . $q . '%')
                    ->orWhere('address', 'like', '%' . $q . '%')
                    ->orWhereHas('gigs', function ($gigQuery) use ($q) {
                        $gigQuery->where('status', Gigs::STATUS_OPEN)
                            ->where(function ($g) use ($q) {
                                $g->where('title', 'like', '%' . $q . '%')
                                    ->orWhere('tag', 'like', '%' . $q . '%')
                                    ->orWhere('description', 'like', '%' . $q . '%');
                            });
                    });
            });
        }

        if ($sort === 'earned') {
            $query->withSum(['tasks as earned_sum' => fn ($t) => $t->where('status', 'completed')], 'price')
                ->orderByDesc('earned_sum');
        } elseif ($sort === 'name') {
            $query->orderBy('name');
        } else {
            $query->withAvg(['reviewsReceived as avg_rating' => fn ($r) => $r->where('status', Review::STATUS_APPROVED)], 'rating')
                ->orderByDesc('avg_rating')
                ->orderBy('name');
        }

        $results = $query->paginate(10)->withQueryString();

        $results->getCollection()->transform(function ($user) {
            $user->profile_stats = UserStatsService::for($user);

            return $user;
        });

        return view('search.index', [
            'scope' => 'talent',
            'q' => $q,
            'results' => $results,
            'sort' => $sort,
            'type' => null,
            'seoMeta' => SeoMeta::home('talent', $q !== '' ? $q : null),
            'heroTitle' => 'Find developer talent',
            'heroSubtitle' => 'Search freelance developers by name, skills, location, or the services they offer.',
        ]);
    }
}
