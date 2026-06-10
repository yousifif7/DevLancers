<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    public const STATUS_PROPOSED = 'proposed';
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'task_id',
        'proposed_by',
        'sort_order',
        'title',
        'description',
        'amount',
        'due_date',
        'status',
        'delivery_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function task()
    {
        return $this->belongsTo(Tasks::class, 'task_id');
    }

    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'milestone_id');
    }

    public function isProposed(): bool
    {
        return $this->status === self::STATUS_PROPOSED;
    }

    public function canEdit(int $userId): bool
    {
        return $this->isProposed() && $this->task->canManageMilestonePlan($userId);
    }

    public function canSubmit(int $userId): bool
    {
        return (int) $this->task->user_id === $userId
            && $this->status === self::STATUS_PENDING
            && $this->task->status === Tasks::STATUS_ACTIVE;
    }

    public function canApprove(int $userId): bool
    {
        return (int) $this->task->owner === $userId
            && $this->status === self::STATUS_SUBMITTED;
    }

    public function canPay(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && (!$this->payment || $this->payment->status !== Payment::STATUS_COMPLETED);
    }
}
