<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'gig_id',
        'user_id',
        'cover_letter',
        'bid_amount',
        'delivery_days',
        'status',
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
        'delivery_days' => 'integer',
    ];

    public function gig()
    {
        return $this->belongsTo(Gigs::class, 'gig_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->hasOne(Tasks::class, 'proposal_id');
    }

    public function isActionable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_SHORTLISTED]);
    }
}
