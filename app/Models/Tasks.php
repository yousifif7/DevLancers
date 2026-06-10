<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_DISPUTED = 'disputed';

    public const PAYMENT_SINGLE = 'single';
    public const PAYMENT_MILESTONES = 'milestones';

    protected $fillable = [
        'user_id',
        'gig_id',
        'request_id',
        'proposal_id',
        'status',
        'owner',
        'content',
        'scope_of_work',
        'terms',
        'start_date',
        'end_date',
        'worker_accepted_at',
        'client_accepted_at',
        'price',
        'payment_structure',
        'milestones_agreed_client_at',
        'milestones_agreed_worker_at',
        'payment_flag',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'worker_accepted_at' => 'datetime',
        'client_accepted_at' => 'datetime',
        'milestones_agreed_client_at' => 'datetime',
        'milestones_agreed_worker_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function gig()
    {
        return $this->belongsTo(Gigs::class, 'gig_id');
    }

    public function request()
    {
        return $this->belongsTo(Requests::class, 'request_id');
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'task_id');
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class, 'task_id');
    }

    public function latestDeliverable()
    {
        return $this->hasOne(Deliverable::class, 'task_id')->latestOfMany();
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'task_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'task_id');
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class, 'task_id');
    }

    public function usesMilestones(): bool
    {
        return $this->milestonePlanIsActive()
            || $this->milestones()->where('status', '!=', Milestone::STATUS_PROPOSED)->exists();
    }

    public function isNegotiatingMilestones(): bool
    {
        return !$this->milestonePlanIsActive()
            && ($this->payment_structure === self::PAYMENT_MILESTONES || $this->milestones()->exists());
    }

    public function milestonePlanIsActive(): bool
    {
        return $this->payment_structure === self::PAYMENT_MILESTONES
            && $this->milestones_agreed_client_at
            && $this->milestones_agreed_worker_at;
    }

    public function hasUserAgreedToMilestones(int $userId): bool
    {
        if ($this->isClient($userId)) {
            return (bool) $this->milestones_agreed_client_at;
        }

        if ($this->isWorker($userId)) {
            return (bool) $this->milestones_agreed_worker_at;
        }

        return false;
    }

    public function otherPartyHasAgreedToMilestones(int $userId): bool
    {
        if ($this->isClient($userId)) {
            return (bool) $this->milestones_agreed_worker_at;
        }

        if ($this->isWorker($userId)) {
            return (bool) $this->milestones_agreed_client_at;
        }

        return false;
    }

    public function canManageMilestonePlan(int $userId): bool
    {
        if (!$this->isParticipant($userId)) {
            return false;
        }

        if (!in_array($this->status, [self::STATUS_DRAFT, self::STATUS_ACTIVE])) {
            return false;
        }

        return !$this->milestones()
            ->whereNotIn('status', [Milestone::STATUS_PROPOSED])
            ->exists();
    }

    public function canAgreeToMilestonePlan(int $userId): bool
    {
        return $this->canManageMilestonePlan($userId)
            && $this->milestones()->exists()
            && $this->milestoneBudgetIsBalanced()
            && !$this->hasUserAgreedToMilestones($userId);
    }

    public function milestoneBudgetIsBalanced(): bool
    {
        return abs($this->milestonesTotal() - (float) $this->price) < 0.01;
    }

    public function milestoneBudgetRemaining(): float
    {
        return max(0, (float) $this->price - $this->milestonesTotal());
    }

    public function resetMilestoneAgreements(): void
    {
        $this->update([
            'milestones_agreed_client_at' => null,
            'milestones_agreed_worker_at' => null,
        ]);
    }

    public function activateMilestonePlan(): void
    {
        $this->milestones()
            ->where('status', Milestone::STATUS_PROPOSED)
            ->update(['status' => Milestone::STATUS_PENDING]);

        $this->update(['payment_structure' => self::PAYMENT_MILESTONES]);
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()
            ->where('status', Payment::STATUS_COMPLETED)
            ->sum('amount');
    }

    public function isFullyPaid(): bool
    {
        return $this->totalPaid() >= (float) $this->price;
    }

    public function isWorker(int $userId): bool
    {
        return (int) $this->user_id === $userId;
    }

    public function isClient(int $userId): bool
    {
        return (int) $this->owner === $userId;
    }

    public function isParticipant(int $userId): bool
    {
        return $this->isWorker($userId) || $this->isClient($userId);
    }

    public function bothPartiesAccepted(): bool
    {
        return $this->worker_accepted_at && $this->client_accepted_at;
    }

    public function canAccept(int $userId): bool
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return false;
        }

        if ($this->isWorker($userId)) {
            return !$this->worker_accepted_at;
        }

        if ($this->isClient($userId)) {
            return !$this->client_accepted_at;
        }

        return false;
    }

    public function canSubmitDeliverable(int $userId): bool
    {
        if ($this->usesMilestones()) {
            return false;
        }

        if (!$this->isWorker($userId)) {
            return false;
        }

        if (!in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_IN_REVIEW])) {
            return false;
        }

        $latest = $this->latestDeliverable;

        return !$latest || $latest->status === Deliverable::STATUS_REVISION_REQUESTED;
    }

    public function canApproveDeliverable(int $userId): bool
    {
        return !$this->usesMilestones()
            && $this->isClient($userId)
            && $this->status === self::STATUS_IN_REVIEW
            && $this->latestDeliverable?->isSubmitted();
    }

    public function canRequestRevision(int $userId): bool
    {
        return $this->canApproveDeliverable($userId);
    }

    public function canPay(): bool
    {
        if ($this->usesMilestones() || $this->payment_flag || $this->isFullyPaid()) {
            return false;
        }

        return $this->status === self::STATUS_COMPLETED;
    }

    public function canAddMilestones(int $userId): bool
    {
        return $this->canManageMilestonePlan($userId)
            && $this->milestoneBudgetRemaining() >= 0.01;
    }

    public function milestonesTotal(): float
    {
        return (float) $this->milestones()->sum('amount');
    }

    public function canReview(int $userId): bool
    {
        if (!$this->isParticipant($userId) || !$this->isFullyPaid()) {
            return false;
        }

        return !$this->reviews()
            ->where('reviewer_id', $userId)
            ->whereIn('status', [Review::STATUS_PENDING, Review::STATUS_APPROVED])
            ->exists();
    }

    public function hasUserReviewed(int $userId): bool
    {
        return $this->reviews()
            ->where('reviewer_id', $userId)
            ->whereIn('status', [Review::STATUS_PENDING, Review::STATUS_APPROVED])
            ->exists();
    }

    public function bothPartiesSubmitted(): bool
    {
        return $this->reviews()
            ->whereIn('status', [Review::STATUS_PENDING, Review::STATUS_APPROVED])
            ->distinct()
            ->count('reviewer_id') >= 2;
    }

    public function bothPartiesReviewed(): bool
    {
        return $this->bothPartiesSubmitted();
    }

    public function bothReviewsApproved(): bool
    {
        return $this->reviews()->where('status', Review::STATUS_APPROVED)->count() >= 2;
    }

    public function reviewFrom(int $userId): ?Review
    {
        return $this->reviews->firstWhere('reviewer_id', $userId);
    }

    public function otherPartyId(int $userId): ?int
    {
        if ($this->isWorker($userId)) {
            return (int) $this->owner;
        }

        if ($this->isClient($userId)) {
            return (int) $this->user_id;
        }

        return null;
    }

    public function canSeeOtherPartyReview(int $viewerId): bool
    {
        if (!$this->hasUserReviewed($viewerId) || !$this->bothPartiesSubmitted()) {
            return false;
        }

        $otherId = $this->otherPartyId($viewerId);
        $otherReview = $this->reviewFrom($otherId);

        return $otherReview && $otherReview->isApproved();
    }

    public function canOpenDispute(int $userId): bool
    {
        return $this->isParticipant($userId)
            && in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_IN_REVIEW])
            && !$this->dispute;
    }

    public function canCancel(int $userId): bool
    {
        return $this->isClient($userId)
            && in_array($this->status, [self::STATUS_DRAFT, self::STATUS_ACTIVE]);
    }

    public function revieweeFor(int $userId): ?int
    {
        if ($this->isWorker($userId)) {
            return (int) $this->owner;
        }

        if ($this->isClient($userId)) {
            return (int) $this->user_id;
        }

        return null;
    }

    public function reviewTypeFor(int $userId): ?string
    {
        if ($this->isClient($userId)) {
            return Review::TYPE_CLIENT_TO_WORKER;
        }

        if ($this->isWorker($userId)) {
            return Review::TYPE_WORKER_TO_CLIENT;
        }

        return null;
    }

    public function activateIfReady(): void
    {
        if ($this->bothPartiesAccepted() && $this->status === self::STATUS_DRAFT) {
            $this->update(['status' => self::STATUS_ACTIVE]);
        }
    }

    public function markFullyPaidIfReady(): void
    {
        if ($this->isFullyPaid()) {
            $this->update([
                'payment_flag' => 1,
                'status' => self::STATUS_COMPLETED,
            ]);
        }
    }
}
