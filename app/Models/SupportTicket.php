<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    public const STATUS_OPEN = 'open';
    public const STATUS_REPLIED = 'replied';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id', 'name', 'email', 'subject', 'message', 'status', 'admin_reply',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
