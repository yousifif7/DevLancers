<?php

namespace App\Services;

use App\Models\Gigs;
use App\Models\Tasks;
use App\Models\Proposal;

class GigStatsService
{
    public static function for(Gigs $gig): array
    {
        $proposals = $gig->proposals();

        return [
            'proposals_total' => (clone $proposals)->count(),
            'proposals_pending' => (clone $proposals)->where('status', Proposal::STATUS_PENDING)->count(),
            'proposals_shortlisted' => (clone $proposals)->where('status', Proposal::STATUS_SHORTLISTED)->count(),
            'proposals_accepted' => (clone $proposals)->where('status', Proposal::STATUS_ACCEPTED)->count(),
            'avg_bid' => (float) ((clone $proposals)->avg('bid_amount') ?? 0),
            'min_bid' => (float) ((clone $proposals)->min('bid_amount') ?? 0),
            'max_bid' => (float) ((clone $proposals)->max('bid_amount') ?? 0),
            'contracts' => Tasks::where('gig_id', $gig->id)->count(),
            'budget' => (float) $gig->salary,
        ];
    }
}
