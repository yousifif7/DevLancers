<?php

namespace App\Services;

use App\Models\Gigs;
use App\Models\GigMedia;
use Illuminate\Http\Request;

class GigMediaService
{
    public static function syncFromRequest(Gigs $gig, Request $request, bool $isUpdate = false): void
    {
        if ($request->hasFile('images')) {
            $sort = (int) $gig->media()->where('type', GigMedia::TYPE_IMAGE)->max('sort_order');
            foreach ($request->file('images') as $file) {
                $sort++;
                $path = PublicUploadService::store($file, 'gigs/images');
                GigMedia::create([
                    'gig_id' => $gig->id,
                    'type' => GigMedia::TYPE_IMAGE,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'sort_order' => $sort,
                ]);

                if (!$gig->image) {
                    $gig->update(['image' => $path]);
                }
            }
        }

        if ($request->hasFile('image') && !$request->hasFile('images')) {
            $path = PublicUploadService::store($request->file('image'), 'gigs/images');
            if ($isUpdate && $gig->image) {
                PublicUploadService::delete($gig->image);
            }
            $sort = (int) $gig->media()->where('type', GigMedia::TYPE_IMAGE)->max('sort_order') + 1;
            GigMedia::create([
                'gig_id' => $gig->id,
                'type' => GigMedia::TYPE_IMAGE,
                'path' => $path,
                'original_name' => $request->file('image')->getClientOriginalName(),
                'sort_order' => $sort,
            ]);
            $gig->update(['image' => $path]);
        }

        if ($request->hasFile('attachments')) {
            $sort = (int) $gig->media()->where('type', GigMedia::TYPE_ATTACHMENT)->max('sort_order');
            foreach ($request->file('attachments') as $file) {
                $sort++;
                GigMedia::create([
                    'gig_id' => $gig->id,
                    'type' => GigMedia::TYPE_ATTACHMENT,
                    'path' => PublicUploadService::store($file, 'gigs/files'),
                    'original_name' => $file->getClientOriginalName(),
                    'sort_order' => $sort,
                ]);
            }
        }
    }

    public static function deleteAllForGig(Gigs $gig): void
    {
        foreach ($gig->media as $media) {
            PublicUploadService::delete($media->path);
            $media->delete();
        }

        PublicUploadService::delete($gig->image);
    }

    public static function deleteMedia(GigMedia $media): void
    {
        PublicUploadService::delete($media->path);
        $gig = $media->gig;

        if ($gig && $gig->image === $media->path) {
            $next = $gig->media()
                ->where('type', GigMedia::TYPE_IMAGE)
                ->where('id', '!=', $media->id)
                ->orderBy('sort_order')
                ->first();
            $gig->update(['image' => $next?->path]);
        }

        $media->delete();
    }
}
