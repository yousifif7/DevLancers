<?php

namespace App\Models;

use App\Services\PublicUploadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GigMedia extends Model
{
    use HasFactory;

    public const TYPE_IMAGE = 'image';
    public const TYPE_ATTACHMENT = 'attachment';

    protected $fillable = [
        'gig_id',
        'type',
        'path',
        'original_name',
        'sort_order',
    ];

    public function gig()
    {
        return $this->belongsTo(Gigs::class, 'gig_id');
    }

    public function url(): ?string
    {
        return PublicUploadService::url($this->path);
    }

    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }
}
