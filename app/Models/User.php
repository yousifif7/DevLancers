<?php

namespace App\Models;

use App\Services\UserStatsService;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'bio',
        'headline',
        'skills',
        'experience_years',
        'education',
        'certifications',
        'portfolio_url',
        'github_url',
        'hourly_rate',
        'address',
        'acc_type',
        'is_admin',
        'stripe_connect_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function gigs(){
        return $this->hasMany(Gigs::class, 'user_id');
    }

    public function requests(){
        return $this->hasMany(Requests::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'user_id');
    }

    public function ownedTasks()
    {
        return $this->hasMany(Tasks::class, 'owner');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'user_id');
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class, 'worker_id');
    }

    public function averageRating(): ?float
    {
        $avg = $this->reviewsReceived()->approved()->avg('rating');

        return $avg ? round($avg, 1) : null;
    }

    public function approvedReviewsCount(): int
    {
        return $this->reviewsReceived()->approved()->count();
    }

    public function hasStripeConnect(): bool
    {
        return !empty($this->stripe_connect_id);
    }

    public function isWorker(): bool
    {
        return (int) $this->acc_type === 1;
    }

    public function isClient(): bool
    {
        return (int) $this->acc_type === 2;
    }

    public function canProposeOn(Gigs $gig): bool
    {
        if ((int) $gig->user_id === $this->id || !$gig->isOpen()) {
            return false;
        }

        return ($gig->gig_type === 'job' && $this->isWorker())
            || ($gig->gig_type === 'gig' && $this->isClient());
    }

    public function stats(): array
    {
        return UserStatsService::for($this);
    }
}

