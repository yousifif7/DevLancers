@extends('layout')

@section('title')
    | {{ $scope === 'talent' ? 'Find Talent' : 'Browse Jobs' }}
@endsection

@section('content')
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="dl-hero mb-4">
        <h1 class="h2">{{ $heroTitle }}</h1>
        <p>{{ $heroSubtitle }}</p>
    </div>

    {{-- Upwork-style search bar --}}
    <form method="GET" action="/" class="dl-search-bar mb-4">
        <div class="input-group input-group-lg">
            <input type="search" class="form-control" name="q" value="{{ $q }}"
                placeholder="{{ $scope === 'talent' ? 'Search talent by name, skills, location...' : 'Search jobs, gigs, skills...' }}"
                aria-label="Search">
            <select class="form-select dl-search-scope" name="scope" aria-label="Search type">
                <option value="jobs" {{ $scope === 'jobs' ? 'selected' : '' }}>Jobs & Gigs</option>
                <option value="talent" {{ $scope === 'talent' ? 'selected' : '' }}>Talent</option>
            </select>
            <button class="btn search-btn px-4" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
        </div>
    </form>

    <div class="row g-4">
        {{-- Sidebar filters --}}
        <div class="col-lg-3">
            <div class="dl-card p-3">
                <h6 class="fw-semibold mb-3">Filters</h6>

                @if ($scope === 'jobs')
                    <p class="small fw-semibold mb-2">Listing type</p>
                    <div class="d-flex flex-column gap-1 mb-3">
                        <a href="/?{{ http_build_query(array_merge(request()->except('type', 'page'), ['type' => null])) }}"
                            class="dl-search-filter {{ !$type ? 'active' : '' }}">All</a>
                        <a href="/?{{ http_build_query(array_merge(request()->except('page'), ['type' => 'job'])) }}"
                            class="dl-search-filter {{ $type === 'job' ? 'active' : '' }}">Jobs only</a>
                        <a href="/?{{ http_build_query(array_merge(request()->except('page'), ['type' => 'gig'])) }}"
                            class="dl-search-filter {{ $type === 'gig' ? 'active' : '' }}">Gigs only</a>
                    </div>
                @else
                    <p class="small text-muted mb-0">Showing freelancers (workers). Search by name, bio, location, or skills from their gigs.</p>
                @endif
            </div>
        </div>

        {{-- Results --}}
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    @if ($q !== '')
                        <strong>{{ $results->total() }}</strong>
                        <span class="text-muted">{{ $scope === 'talent' ? 'freelancers' : 'results' }} for "{{ $q }}"</span>
                    @else
                        <span class="text-muted">{{ $scope === 'talent' ? 'All talent' : 'All open listings' }}</span>
                        <strong class="ms-1">({{ $results->total() }})</strong>
                    @endif
                </div>
                <form method="GET" action="/" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="scope" value="{{ $scope }}">
                    @if ($q)<input type="hidden" name="q" value="{{ $q }}">@endif
                    @if ($type)<input type="hidden" name="type" value="{{ $type }}">@endif
                    <label class="small text-muted mb-0">Sort:</label>
                    <select class="form-select form-select-sm" name="sort" onchange="this.form.submit()" style="width:auto">
                        @if ($scope === 'jobs')
                            <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="proposals" {{ ($sort ?? '') === 'proposals' ? 'selected' : '' }}>Most proposals</option>
                            <option value="budget_desc" {{ ($sort ?? '') === 'budget_desc' ? 'selected' : '' }}>Budget: high to low</option>
                            <option value="budget_asc" {{ ($sort ?? '') === 'budget_asc' ? 'selected' : '' }}>Budget: low to high</option>
                        @else
                            <option value="rating" {{ ($sort ?? 'rating') === 'rating' ? 'selected' : '' }}>Best rating</option>
                            <option value="earned" {{ ($sort ?? '') === 'earned' ? 'selected' : '' }}>Most earned</option>
                            <option value="name" {{ ($sort ?? '') === 'name' ? 'selected' : '' }}>Name A–Z</option>
                        @endif
                    </select>
                </form>
            </div>

            @if ($results->count())
                @if ($scope === 'talent')
                    @foreach ($results as $worker)
                        @include('search.partials.talent-card', ['worker' => $worker, 'q' => $q])
                    @endforeach
                @else
                    @foreach ($results as $gig)
                        @include('search.partials.job-card', ['gig' => $gig, 'q' => $q])
                    @endforeach
                @endif
                <div class="mt-3">{{ $results->links() }}</div>
            @else
                <div class="dl-card p-5 text-center text-muted">
                    <i class="fa-solid fa-search fa-2x mb-3"></i>
                    <h5 class="text-dark">No {{ $scope === 'talent' ? 'freelancers' : 'listings' }} found</h5>
                    <p class="mb-0">Try different keywords or switch to {{ $scope === 'talent' ? 'Jobs & Gigs' : 'Talent' }} search.</p>
                </div>
            @endif
        </div>
    </div>

    <button type="button" class="dl-scroll-top" id="dlScrollTop" aria-label="Scroll to top" title="Back to top">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
@endsection

@section('scripts')
<script>
(function () {
    var btn = document.getElementById('dlScrollTop');
    if (!btn) return;

    var toggle = function () {
        btn.classList.toggle('visible', window.scrollY > 320);
    };

    window.addEventListener('scroll', toggle, { passive: true });
    toggle();

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>
@endsection
