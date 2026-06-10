<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_TRANSFERRED = 'transferred';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'payment_id',
        'worker_id',
        'amount',
        'status',
        'stripe_transfer_id',
        'transferred_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transferred_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
