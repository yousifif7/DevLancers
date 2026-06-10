@php
    use App\Support\SearchHighlight;
@endphp

<div class="dl-search-card mb-3">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
        <div class="small text-muted">
            Posted {{ $gig->created_at->diffForHumans() }}
            · {{ $gig->proposals_count }} {{ Str::plural('proposal', $gig->proposals_count) }}
        </div>
        <div>
            @if ($gig->gig_type === 'gig')
                <span class="list-gig">Gig</span>
            @else
                <span class="list-job">Job</span>
            @endif
        </div>
    </div>

    <h5 class="dl-search-card-title mb-2">
        <a href="/gigs/{{ $gig->id }}">{!! SearchHighlight::mark($gig->title, $q) !!}</a>
    </h5>

    <div class="dl-search-meta small mb-2">
        @if ($gig->user)
            <a href="/users/{{ $gig->user_id }}" class="text-decoration-none">
                <i class="fa-solid fa-user"></i> {!! SearchHighlight::mark($gig->user->name, $q) !!}
            </a>
            @if ($gig->gig_type === 'job' && $gig->user->averageRating())
                <span class="text-warning ms-2">{{ str_repeat('★', (int) round($gig->user->averageRating())) }}</span>
                <span class="text-muted">{{ $gig->user->averageRating() }}</span>
            @endif
            @if ($gig->user->address)
                <span class="text-muted ms-2"><i class="fa-solid fa-location-dot"></i> {{ $gig->user->address }}</span>
            @endif
        @endif
    </div>

    <p class="small mb-2">
        <strong>Fixed-price:</strong> ${{ number_format((float) $gig->salary, 0) }}
    </p>

    <p class="dl-search-snippet small text-muted mb-2">
        {!! SearchHighlight::mark(Str::limit($gig->description, 220), $q) !!}
    </p>

    @php $tags = array_filter(array_map('trim', explode(',', $gig->tag))); @endphp
    @if (count($tags))
        <div class="d-flex flex-wrap gap-1">
            @foreach (array_slice($tags, 0, 8) as $tag)
                <a href="/?scope=jobs&q={{ urlencode($tag) }}" class="dl-search-skill">{!! SearchHighlight::mark($tag, $q) !!}</a>
            @endforeach
        </div>
    @endif
</div>
