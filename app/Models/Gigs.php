<?php

namespace App\Models;

use App\Models\User;
use App\Services\PublicUploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gigs extends Model {

    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_FILLED = 'filled';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id',
        'gig_type',
        'status',
        'title',
        'email',
        'description',
        'tag',
        'salary',
        'image',
    ];

    // List all added gigs
    
    // List gig by ID
    // public static function find($id){
    //     $gigs =self::all();
        
    //     foreach($gigs as $gig){
    //         if($gig['id']==$id){
    //             return $gig;
    //         }
    //     }
    // }

    public function scopeFilter($query, array $filters){
        if($filters['tag'] ?? false){
            $query->where('tag','like', '%' . request('tag') .'%');
        }

        if($filters['type'] ?? false){
            $query->where('gig_type','like', '%' . request('type') .'%')
            ->orWhere('gig_type','like', '%' . request('type') .'%');
        }

        if($filters['search'] ?? false){
            $search = request('search');

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('tag', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('gig_type', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requests(){
        return $this->hasMany(Requests::class, 'gig_id');
    }
    public function tasks()
    {
        return $this->hasMany(Tasks::class, 'gig_id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'gig_id');
    }

    public function media()
    {
        return $this->hasMany(GigMedia::class, 'gig_id')->orderBy('sort_order');
    }

    public function images()
    {
        return $this->hasMany(GigMedia::class, 'gig_id')
            ->where('type', GigMedia::TYPE_IMAGE)
            ->orderBy('sort_order');
    }

    public function attachments()
    {
        return $this->hasMany(GigMedia::class, 'gig_id')
            ->where('type', GigMedia::TYPE_ATTACHMENT)
            ->orderBy('sort_order');
    }

    public function imageUrl(): ?string
    {
        return PublicUploadService::url($this->image);
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->image) {
            return PublicUploadService::url($this->image);
        }

        $first = $this->relationLoaded('media')
            ? $this->media->where('type', GigMedia::TYPE_IMAGE)->first()
            : $this->images()->first();

        return $first ? PublicUploadService::url($first->path) : null;
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isGig(): bool
    {
        return $this->gig_type === 'gig';
    }

    public function isJob(): bool
    {
        return $this->gig_type === 'job';
    }

    /** Fiverr-style services stay open; jobs close after one hire. */
    public function allowsMultipleOrders(): bool
    {
        return $this->isGig();
    }
}
