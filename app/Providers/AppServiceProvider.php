<?php

namespace App\Providers;

use App\Models\Tasks;
use App\Models\Proposal;
use App\Models\Requests;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator as PaginationPaginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::unguard();
        PaginationPaginator::useBootstrapFive();

        View::composer('layout', function ($view) {
            $data = ['siteName' => 'DevLancer'];

            if (Schema::hasTable('site_settings')) {
                $data['siteName'] = SiteSetting::get('site_name', 'DevLancer');
            }

            if (Auth::check()) {
                $userId = Auth::id();
                $data['msgCount'] = Requests::where('reciever', $userId)->whereNull('read_at')->count();
                $data['alertCount'] = Auth::user()->unreadNotifications()->count();
                $data['proposalCount'] = Proposal::whereHas('gig', fn ($q) => $q->where('user_id', $userId))
                    ->whereIn('status', ['pending', 'shortlisted'])->count();
                $data['pendingReviewCount'] = Tasks::where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhere('owner', $userId);
                })
                    ->with('payments')
                    ->get()
                    ->filter(fn ($t) => $t->canReview($userId))
                    ->count();
            }

            $view->with($data);
        });
    }
}
