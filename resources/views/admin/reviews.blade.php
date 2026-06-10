@extends('layout')

@section('title')
    | Admin Reviews
@endsection

@section('content')
<div class="container py-4">
    @include('admin.partials.nav')

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4 class="fw-semibold mb-0">Reviews</h4>
        <div class="d-flex gap-2 flex-wrap">
            <a href="/admin/reviews" class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
            <a href="/admin/reviews?status=pending" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}">
                Pending ({{ $pendingCount }})
            </a>
            <a href="/admin/reviews?status=approved" class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-secondary' }}">
                Approved ({{ $approvedCount }})
            </a>
            <a href="/admin/reviews?status=denied" class="btn btn-sm {{ $status === 'denied' ? 'btn-danger' : 'btn-outline-secondary' }}">
                Denied ({{ $deniedCount }})
            </a>
        </div>
    </div>

    @forelse ($reviews as $review)
        @php
            $statusColors = [
                'pending' => 'warning',
                'approved' => 'success',
                'denied' => 'danger',
            ];
        @endphp
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <span class="badge bg-{{ $statusColors[$review->status] ?? 'secondary' }}">{{ ucfirst($review->status) }}</span>
                    <span class="badge bg-secondary ms-1">{{ str_replace('_', ' ', $review->type) }}</span>
                    <span class="text-warning ms-2">{{ str_repeat('★', $review->rating) }}</span>
                </div>
                <small class="text-muted">{{ $review->created_at->format('M d, Y g:i A') }}</small>
            </div>
            <div class="card-body">
                <div class="row mb-3 small">
                    <div class="col-md-4">
                        <strong>Reviewer:</strong>
                        <a href="/users/{{ $review->reviewer_id }}">{{ $review->reviewer->name }}</a>
                    </div>
                    <div class="col-md-4">
                        <strong>Reviewee:</strong>
                        <a href="/users/{{ $review->reviewee_id }}">{{ $review->reviewee->name }}</a>
                    </div>
                    <div class="col-md-4">
                        <strong>Contract:</strong>
                        <a href="/tasks/{{ $review->task_id }}">#{{ $review->task_id }}</a>
                        @if ($review->task?->gig)
                            <span class="text-muted"> — {{ $review->task->gig->title }}</span>
                        @endif
                    </div>
                </div>

                @if ($review->comment)
                    <blockquote class="border-start border-3 ps-3 mb-3">{{ $review->comment }}</blockquote>
                @else
                    <p class="text-muted small mb-3">No comment provided.</p>
                @endif

                <div class="d-flex flex-wrap gap-2 mb-2">
                    @if ($review->isPending())
                        <form method="POST" action="/admin/reviews/{{ $review->id }}/approve" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i> Approve</button>
                        </form>
                        <form method="POST" action="/admin/reviews/{{ $review->id }}/deny" class="d-inline"
                            onsubmit="return confirm('Deny this review? The reviewer can submit again.');">
                            @csrf
                            <button class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i> Deny</button>
                        </form>
                    @elseif ($review->isDenied())
                        <form method="POST" action="/admin/reviews/{{ $review->id }}/approve" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Approve anyway</button>
                        </form>
                    @elseif ($review->isApproved())
                        <form method="POST" action="/admin/reviews/{{ $review->id }}/deny" class="d-inline"
                            onsubmit="return confirm('Deny this review? It will be hidden from public profiles.');">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm">Revoke (deny)</button>
                        </form>
                    @endif

                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse"
                        data-bs-target="#editReview{{ $review->id }}">
                        <i class="fa-solid fa-pencil"></i> Edit
                    </button>

                    <form method="POST" action="/admin/reviews/{{ $review->id }}" class="d-inline"
                        onsubmit="return confirm('Permanently delete this review?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                    </form>
                </div>

                <div class="collapse" id="editReview{{ $review->id }}">
                    <form method="POST" action="/admin/reviews/{{ $review->id }}" class="border rounded p-3 bg-light mt-2">
                        @csrf
                        @method('PUT')
                        <div class="row g-2 mb-2">
                            <div class="col-md-3">
                                <label class="form-label small">Rating</label>
                                <select class="form-select form-select-sm" name="rating" required>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ $review->rating == $i ? 'selected' : '' }}>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Status</label>
                                <select class="form-select form-select-sm" name="status" required>
                                    <option value="pending" {{ $review->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $review->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="denied" {{ $review->status === 'denied' ? 'selected' : '' }}>Denied</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Comment</label>
                            <textarea class="form-control form-control-sm" name="comment" rows="3">{{ $review->comment }}</textarea>
                        </div>
                        <button class="btn btn-primary btn-sm">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="dl-card p-5 text-center text-muted">
            <i class="fa-solid fa-star fa-2x mb-3"></i>
            <p>No reviews found{{ $status ? ' with status: ' . $status : '' }}.</p>
        </div>
    @endforelse

    {{ $reviews->links() }}
</div>
@endsection
