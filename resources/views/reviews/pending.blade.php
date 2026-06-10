@extends('layout')

@section('title')
    | Pending Reviews
@endsection

@section('content')
<div class="container py-4" style="max-width:800px">
    <h4 class="fw-semibold mb-2">Leave a Review</h4>

    <div class="alert alert-light border small mb-4">
        <strong>How it works</strong>
        <ol class="mb-0 mt-2 ps-3">
            <li>Reviews appear here after a contract is <strong>fully paid</strong>.</li>
            <li>You rate the other person (1–5 stars) and optionally leave a comment.</li>
            <li>Your review stays <strong>hidden</strong> from them until they submit theirs — same for you.</li>
            <li>Admin <strong>approves</strong> each review before it appears on public profiles.</li>
            <li>Once both reviews are submitted and approved, you can read each other's feedback on the contract page.</li>
        </ol>
    </div>

    @forelse ($tasks as $task)
        @php
            $otherName = $task->isWorker(Auth::id())
                ? ($task->ownerUser->name ?? 'Client')
                : $task->user->name;
        @endphp
        <div class="dl-card p-4 mb-3">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <strong>Contract #{{ $task->id }}</strong>
                    @if ($task->gig)<span class="text-muted"> — {{ $task->gig->title }}</span>@endif
                </div>
                <span class="badge bg-success">Paid</span>
            </div>
            <p class="mb-3">
                Rate <strong>{{ $otherName }}</strong>
            </p>
            <form method="POST" action="/tasks/{{ $task->id }}/reviews">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Your rating</label>
                    @include('partials.star-rating', ['idPrefix' => 'pending-' . $task->id])
                    @error('rating')<p class="text-danger small">{{ $message }}</p>@enderror
                </div>
                <textarea class="form-control mb-3" name="comment" rows="3"
                    placeholder="Share your experience (optional)">{{ old('comment') }}</textarea>
                @error('review')<p class="text-danger small">{{ $message }}</p>@enderror
                <button class="regbtn">Submit Review</button>
                <a href="/tasks/{{ $task->id }}" class="dl-btn dl-btn-outline ms-2">View Contract</a>
            </form>
        </div>
    @empty
        <div class="dl-card p-5 text-center text-muted">
            <i class="fa-solid fa-star fa-2x mb-3" style="color:#f59e0b"></i>
            <p class="mb-1">No pending reviews right now.</p>
            <p class="small mb-0">When a contract is fully paid, you'll be able to rate the other party here.</p>
        </div>
    @endforelse
</div>
@endsection
