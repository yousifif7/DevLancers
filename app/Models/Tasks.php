<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id','gig_id','status','owner','content','price','payment_flag'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gig(){
        return $this->belongsTo(Gigs::class, 'gig_id');
    }
}
