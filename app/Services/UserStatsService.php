<?php

namespace App\Services;

use App\Models\Gigs;
use App\Models\User;
use App\Models\Tasks;
use App\Models\Payment;
use App\Models\Proposal;

class UserStatsService
{
    public static function for(User $user): array
    {
        return $user->isWorker()
            ? self::workerStats($user)
            : self::clientStats($user);
    }

    public static function workerStats(User $user): array
    {
        $contracts = Tasks::where('user_id', $user->id);
        $proposals = Proposal::where('user_id', $user->id);

        $submitted = (clone $proposals)->count();
        $accepted = (clone $proposals)->where('status', Proposal::STATUS_ACCEPTED)->count();

        return [
            'role' => 'worker',
            'total_earned' => (float) Payment::where('status', Payment::STATUS_COMPLETED)
                ->whereHas('task', fn ($q) => $q->where('user_id', $user->id))
                ->sum('amount'),
            'total_spent' => 0,
            'contracts_total' => (clone $contracts)->count(),
            'contracts_active' => (clone $contracts)->whereIn('status', [
                Tasks::STATUS_DRAFT,
                Tasks::STATUS_ACTIVE,
                Tasks::STATUS_IN_REVIEW,
            ])->count(),
            'contracts_completed' => (clone $contracts)->where('status', Tasks::STATUS_COMPLETED)->count(),
            'proposals_submitted' => $submitted,
            'proposals_accepted' => $accepted,
            'proposal_success_rate' => $submitted > 0 ? (int) round(($accepted / $submitted) * 100) : null,
            'proposals_received' => 0,
            'listings_posted' => Gigs::where('user_id', $user->id)->count(),
            'jobs_posted' => Gigs::where('user_id', $user->id)->where('gig_type', 'job')->count(),
            'open_listings' => Gigs::where('user_id', $user->id)->where('status', Gigs::STATUS_OPEN)->count(),
            'hires_made' => 0,
            'reviews_count' => $user->approvedReviewsCount(),
            'average_rating' => $user->averageRating(),
            'member_since' => $user->created_at,
        ];
    }

    public static function clientStats(User $user): array
    {
        $contracts = Tasks::where('owner', $user->id);
        $proposalsReceived = Proposal::whereHas('gig', fn ($q) => $q->where('user_id', $user->id));

        return [
            'role' => 'client',
            'total_earned' => 0,
            'total_spent' => (float) Payment::where('user_id', $user->id)
                ->where('status', Payment::STATUS_COMPLETED)
                ->sum('amount'),
            'contracts_total' => (clone $contracts)->count(),
            'contracts_active' => (clone $contracts)->whereIn('status', [
                Tasks::STATUS_DRAFT,
                Tasks::STATUS_ACTIVE,
                Tasks::STATUS_IN_REVIEW,
            ])->count(),
            'contracts_completed' => (clone $contracts)->where('status', Tasks::STATUS_COMPLETED)->count(),
            'proposals_submitted' => Proposal::where('user_id', $user->id)->count(),
            'proposals_accepted' => 0,
            'proposal_success_rate' => null,
            'proposals_received' => (clone $proposalsReceived)->count(),
            'listings_posted' => Gigs::where('user_id', $user->id)->count(),
            'jobs_posted' => Gigs::where('user_id', $user->id)->where('gig_type', 'job')->count(),
            'open_listings' => Gigs::where('user_id', $user->id)->where('status', Gigs::STATUS_OPEN)->count(),
            'hires_made' => (clone $proposalsReceived)->where('status', Proposal::STATUS_ACCEPTED)->count(),
            'reviews_count' => $user->approvedReviewsCount(),
            'average_rating' => $user->averageRating(),
            'member_since' => $user->created_at,
        ];
    }
}
