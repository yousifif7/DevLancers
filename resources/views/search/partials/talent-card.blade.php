@php
    use App\Support\SearchHighlight;
    $stats = $worker->profile_stats ?? [];
    $tags = collect();
    if ($worker->skills) {
        foreach (explode(',', $worker->skills) as $tag) {
            if (trim($tag)) {
                $tags->push(trim($tag));
            }
        }
    }
    foreach ($worker->gigs ?? [] as $gig) {
        foreach (explode(',', $gig->tag ?? '') as $tag) {
            if (trim($tag)) {
                $tags->push(trim($tag));
            }
        }
    }
    $tags = $tags->unique()->take(10);
    $minRate = $worker->gigs->min('salary');
    $initials = collect(explode(' ', $worker->name))->map(fn ($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
@endphp

<div class="dl-search-card dl-search-talent-card mb-3">
    <div class="d-flex gap-3">
        <div class="dl-search-avatar flex-shrink-0">{{ $initials }}</div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h5 class="dl-search-card-title mb-0">
                        <a href="/users/{{ $worker->id }}">{!! SearchHighlight::mark($worker->name, $q) !!}</a>
                    </h5>
                    @if ($worker->headline)
                        <p class="small fw-semibold text-primary mb-1 mt-1">{!! SearchHighlight::mark($worker->headline, $q) !!}</p>
                    @elseif ($worker->bio)
                        <p class="small text-muted mb-1 mt-1">{!! SearchHighlight::mark(Str::limit($worker->bio, 120), $q) !!}</p>
                    @endif
                </div>
                <div class="text-end small">
                    @if ($worker->hourly_rate)
                        <div class="fw-semibold">${{ number_format((float) $worker->hourly_rate, 0) }}/hr</div>
                    @elseif ($minRate)
                        <div class="fw-semibold">${{ number_format((float) $minRate, 0) }}+</div>
                        <div class="text-muted">from gigs</div>
                    @endif
                </div>
            </div>

            <div class="dl-search-meta small mb-2">
                @if ($worker->address)
                    <span class="text-muted"><i class="fa-solid fa-location-dot"></i> {!! SearchHighlight::mark($worker->address, $q) !!}</span>
                @endif
                @if (!empty($stats['average_rating']))
                    <span class="text-warning ms-2">{{ str_repeat('★', (int) round($stats['average_rating'])) }}</span>
                    <span>{{ $stats['average_rating'] }}</span>
                    <span class="text-muted">({{ $stats['reviews_count'] }} reviews)</span>
                @endif
                @if ($worker->experience_years !== null)
                    <span class="text-muted ms-2">{{ $worker->experience_years }}+ yrs exp</span>
                @endif
                @if (!empty($stats['contracts_completed']))
                    <span class="text-muted ms-2">{{ $stats['contracts_completed'] }} jobs done</span>
                @endif
                @if (!empty($stats['total_earned']))
                    <span class="text-muted ms-2">${{ number_format($stats['total_earned'], 0) }} earned</span>
                @endif
            </div>

            @if ($tags->isNotEmpty())
                <div class="d-flex flex-wrap gap-1 mb-2">
                    @foreach ($tags as $tag)
                        <a href="/?scope=talent&q={{ urlencode($tag) }}" class="dl-search-skill">{!! SearchHighlight::mark($tag, $q) !!}</a>
                    @endforeach
                </div>
            @endif

            @if ($worker->gigs->isNotEmpty())
                <div class="small">
                    <strong class="text-muted">Services:</strong>
                    @foreach ($worker->gigs->take(3) as $gig)
                        <a href="/gigs/{{ $gig->id }}" class="ms-1">{!! SearchHighlight::mark($gig->title, $q) !!}</a>@if (!$loop->last),@endif
                    @endforeach
                </div>
            @endif

            <div class="mt-2">
                <a href="/users/{{ $worker->id }}" class="btn btn-sm btn-outline-primary">View profile</a>
                @auth
                    @if (Auth::id() !== $worker->id)
                        <a href="/user/chats/{{ Auth::id() }}/with/{{ $worker->id }}" class="btn btn-sm btn-outline-success">Contact</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>
