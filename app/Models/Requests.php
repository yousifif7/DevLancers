<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'gig_id', 'reciever', 'sender', 'message', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'reciever');
    }

    public function gig()
    {
        return $this->belongsTo(Gigs::class, 'gig_id');
    }

    public function task()
    {
        return $this->hasOne(Tasks::class, 'request_id');
    }
}
