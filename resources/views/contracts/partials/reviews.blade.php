@php
    $viewerId = Auth::id();
    $myReview = $task->reviewFrom($viewerId);
    $otherReview = $task->reviewFrom($task->otherPartyId($viewerId));
    $bothSubmitted = $task->bothPartiesSubmitted();
    $canReview = $task->canReview($viewerId);
    $canSeeOther = $task->canSeeOtherPartyReview($viewerId);
    $otherName = $task->isWorker($viewerId)
        ? ($task->ownerUser->name ?? 'Client')
        : $task->user->name;

    $statusBadge = fn ($review) => match ($review->status) {
        'pending' => ['warning', 'Pending admin approval'],
        'approved' => ['success', 'Approved'],
        'denied' => ['danger', 'Denied by admin'],
        default => ['secondary', ucfirst($review->status)],
    };
@endphp

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong><i class="fa-solid fa-star"></i> Reviews</strong>
        @if ($task->bothReviewsApproved())
            <span class="badge bg-success">Both published</span>
        @elseif ($bothSubmitted)
            <span class="badge bg-info">Awaiting admin approval</span>
        @elseif ($myReview)
            <span class="badge bg-info">Waiting for {{ $otherName }}</span>
        @elseif ($task->isFullyPaid())
            <span class="badge bg-warning text-dark">Review period open</span>
        @endif
    </div>
    <div class="card-body">

        @if ($task->isFullyPaid())
            <div class="alert alert-light border small mb-4">
                <strong>How reviews work</strong>:
                <ul class="mb-0 mt-2 ps-3">
                    <li>Reviews unlock after the contract is <strong>fully paid</strong>.</li>
                    <li>Each party rates the other — reviews stay <strong>private</strong> until both submit.</li>
                    <li>All reviews are <strong>moderated by admin</strong> before they appear on profiles.</li>
                    <li>Once both reviews are submitted and approved, you can read each other's feedback here.</li>
                </ul>
            </div>
        @endif

        @if ($canReview)
            <div class="dl-review-banner mb-4">
                <h6 class="fw-semibold mb-1">Rate your experience with {{ $otherName }}</h6>
                <p class="mb-0 small">Your review is sent to admin for approval before it is published.</p>
            </div>
            <form method="POST" action="/tasks/{{ $task->id }}/reviews">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Your rating</label>
                    @include('partials.star-rating', ['idPrefix' => 'contract-' . $task->id])
                    @error('rating')<p class="text-danger small">{{ $message }}</p>@enderror
                </div>
                <textarea class="form-control mb-3" name="comment" rows="3"
                    placeholder="What went well? What could be improved? (optional)">{{ old('comment') }}</textarea>
                @error('review')<p class="text-danger small">{{ $message }}</p>@enderror
                <button class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Submit Review</button>
            </form>
        @endif

        @if ($myReview)
            @php [$badgeColor, $badgeLabel] = $statusBadge($myReview); @endphp
            <div class="border rounded p-3 mb-3 {{ $canReview ? 'mt-4' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                    <strong>Your review</strong>
                    <div>
                        <span class="badge bg-{{ $badgeColor }}">{{ $badgeLabel }}</span>
                        <span class="badge bg-secondary">{{ $myReview->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @if (!$myReview->isDenied())
                    <div class="text-warning mb-1" aria-label="{{ $myReview->rating }} stars">
                        @for ($s = 1; $s <= 5; $s++)
                            <span class="{{ $s <= $myReview->rating ? '' : 'dl-star-empty' }}">★</span>
                        @endfor
                    </div>
                    @if ($myReview->comment)
                        <p class="mb-0 small">{{ $myReview->comment }}</p>
                    @else
                        <p class="mb-0 small text-muted">No written feedback.</p>
                    @endif
                @else
                    <p class="text-danger small mb-0">This review was not approved. You may submit a new one above.</p>
                @endif
            </div>
        @endif

        @if ($canSeeOther && $otherReview)
            <div class="border rounded p-3 bg-light">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong>{{ $otherReview->reviewer->name }}'s review</strong>
                    <span class="badge bg-secondary">{{ $otherReview->created_at->diffForHumans() }}</span>
                </div>
                <div class="text-warning mb-1">
                    @for ($s = 1; $s <= 5; $s++)
                        <span class="{{ $s <= $otherReview->rating ? '' : 'dl-star-empty' }}">★</span>
                    @endfor
                </div>
                @if ($otherReview->comment)
                    <p class="mb-0 small">{{ $otherReview->comment }}</p>
                @else
                    <p class="mb-0 small text-muted">No written feedback.</p>
                @endif
            </div>
        @elseif ($myReview && $otherReview && $bothSubmitted && !$otherReview->isApproved())
            <div class="alert alert-info small mb-0">
                <i class="fa-solid fa-hourglass-half"></i>
                {{ $otherName }} submitted a review — it is pending admin approval. You'll see it here once approved.
            </div>
        @elseif ($myReview && !$bothSubmitted)
            <div class="alert alert-info small mb-0">
                <i class="fa-solid fa-lock"></i>
                {{ $otherName }} hasn't submitted their review yet. You'll see it here once they do and admin approves both.
            </div>
        @elseif (!$myReview && $otherReview && !$bothSubmitted)
            <div class="alert alert-info small mb-0">
                <i class="fa-solid fa-lock"></i>
                {{ $otherName }} has submitted a review. Submit yours to unlock visibility after admin approval.
            </div>
        @endif

        @if (!$task->isFullyPaid() && !$myReview && !$otherReview)
            <p class="text-muted small mb-0">
                <i class="fa-solid fa-clock"></i>
                Reviews unlock after this contract is fully paid and completed.
            </p>
        @endif
    </div>
</div>
