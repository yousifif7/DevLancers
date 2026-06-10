<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_RESOLVED = 'resolved';

    protected $fillable = [
        'task_id',
        'opened_by',
        'reason',
        'status',
        'resolution_notes',
    ];

    public function task()
    {
        return $this->belongsTo(Tasks::class, 'task_id');
    }

    public function opener()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }
}
