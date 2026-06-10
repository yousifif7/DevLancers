<?php

use App\Models\Gigs;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GigController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DeliverableController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SupportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// View main page (Upwork-style search: jobs/gigs or talent)
Route::get('/', [SearchController::class, 'index']);

Route::get('/guide', [App\Http\Controllers\GuideController::class, 'index'])->name('guide');

Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Allow: /guide',
        'Allow: /gigs/',
        'Allow: /users/',
        '',
        'Disallow: /admin',
        'Disallow: /login',
        'Disallow: /signup',
        'Disallow: /tasks/',
        'Disallow: /user/',
        'Disallow: /gigs/create',
        'Disallow: /gigs/profile',
        'Disallow: /connect/',
        '',
        'Sitemap: ' . url('/sitemap.xml'),
    ];

    return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
})->name('robots');

// Create a new gig
Route::get('/gigs/create', [GigController::class , 'create'])->middleware('auth');

//Store a gig after creating it
Route::post('/gigs', [GigController::class , 'store'])->middleware('auth');

// Edit an existed gig
Route::get('/gigs/{gig}/edit', [GigController::class , 'edit'])->middleware('auth');

// To confirm an update
Route::put('gigs/{gig}', [GigController::class , 'update'])->middleware('auth');

// Delete a gig
Route::post('gigs/{gig}', [GigController::class , 'destroy'])->middleware('auth');

Route::post('/gigs/media/{media}/delete', [GigController::class, 'destroyMedia'])->middleware('auth');

//Show profile for each user
Route::get('/gigs/profile', [GigController::class , 'profile'])->middleware('auth')->name('profile');

// show a gig by ID
Route::get('/gigs/{gig}', [GigController::class , 'show']);


// Show new registeration form
Route::get('/signup', [UserController::class , 'index'])->middleware('guest');

//create new user
Route::post('/users', [UserController::class , 'store']);

//Show user profile
Route::get('/users/{id}', [UserController::class , 'userProfile'])->middleware('auth');

//log out user
Route::post('/logout', [UserController::class , 'logout']);

//show Log in form
Route::get('/login', [UserController::class , 'login'])->name('login')->middleware('guest');

// Update an profile
Route::put('/users/{user}', [UserController::class , 'update'])->middleware('auth');

//To check user to log in
Route::post('/users/authenticate', [UserController::class , 'authenticate']);

// Return user notifications
Route::get('/user/notifications/{id}', [UserController::class , 'notifications'])->middleware("auth");

//Return user sent messages
Route::get('/user/sent/{id}', [UserController::class , 'sent'])->middleware("auth");

// Delete a message
Route::post('request/{id}', [RequestsController::class , 'destroy'])->middleware('auth');

//Redirect to reply page (legacy → live chat)
Route::get('/reply/{msg}', [RequestsController::class , 'submitMsg'])->middleware('auth');

// Live chat
Route::get('/user/chats/{id}', [ChatController::class, 'index'])->middleware('auth');
Route::get('/user/chats/{id}/with/{user}', [ChatController::class, 'show'])->middleware('auth');
Route::get('/api/chat/{user}/messages', [ChatController::class, 'fetchMessages'])->middleware('auth');
Route::post('/api/chat/{user}/send', [ChatController::class, 'send'])->middleware('auth');
Route::post('/api/chat/{user}/read', [ChatController::class, 'markRead'])->middleware('auth');
Route::get('/api/poll', [ChatController::class, 'pollCounts'])->middleware('auth');

// Delete all messages
Route::post('request/sent/{id}', [RequestsController::class , 'destroyAllSent'])->middleware('auth');
Route::post('request/recieved/{id}', [RequestsController::class , 'destroyAllRecieved'])->middleware('auth');

//Store a gig after creating it
Route::post('/requests', [RequestsController::class , 'store'])->middleware('auth');

//return user tasks
Route::get('/user/tasks/{id}', [TasksController::class , 'tasks'])->middleware("auth");

// proposals
Route::get('/user/proposals/{id}', [ProposalController::class, 'inbox'])->middleware('auth');
Route::get('/user/my-proposals/{id}', [ProposalController::class, 'myProposals'])->middleware('auth');
Route::get('/gigs/{gig}/proposals', [ProposalController::class, 'forGig'])->middleware('auth');
Route::post('/gigs/{gig}/proposals', [ProposalController::class, 'store'])->middleware('auth');
Route::post('/gigs/{gig}/close', [ProposalController::class, 'closeGig'])->middleware('auth');
Route::post('/proposals/{proposal}/status', [ProposalController::class, 'updateStatus'])->middleware('auth');
Route::post('/proposals/{proposal}/withdraw', [ProposalController::class, 'withdraw'])->middleware('auth');
Route::post('/proposals/{proposal}/hire', [ProposalController::class, 'hire'])->middleware('auth');

// contracts & deliverables
Route::get('/tasks/{task}', [ContractController::class, 'show'])->middleware('auth');
Route::post('/tasks/{task}/accept', [ContractController::class, 'accept'])->middleware('auth');
Route::post('/tasks/{task}/cancel', [ContractController::class, 'cancel'])->middleware('auth');
Route::post('/tasks/{task}/deliverables', [DeliverableController::class, 'store'])->middleware('auth');
Route::post('/deliverables/{deliverable}/approve', [DeliverableController::class, 'approve'])->middleware('auth');
Route::post('/deliverables/{deliverable}/revision', [DeliverableController::class, 'requestRevision'])->middleware('auth');
Route::get('/user/reviews/pending/{id}', [ReviewController::class, 'pending'])->middleware('auth');
Route::post('/tasks/{task}/reviews', [ReviewController::class, 'store'])->middleware('auth');
Route::post('/tasks/{task}/milestones', [MilestoneController::class, 'store'])->middleware('auth');
Route::post('/tasks/{task}/milestones/enable', [MilestoneController::class, 'enableMilestones'])->middleware('auth');
Route::post('/tasks/{task}/milestones/single', [MilestoneController::class, 'switchToSingle'])->middleware('auth');
Route::post('/tasks/{task}/milestones/agree', [MilestoneController::class, 'agreePlan'])->middleware('auth');
Route::post('/tasks/{task}/milestones/withdraw-agreement', [MilestoneController::class, 'withdrawAgreement'])->middleware('auth');
Route::put('/milestones/{milestone}', [MilestoneController::class, 'update'])->middleware('auth');
Route::delete('/milestones/{milestone}', [MilestoneController::class, 'destroy'])->middleware('auth');
Route::post('/milestones/{milestone}/submit', [MilestoneController::class, 'submit'])->middleware('auth');
Route::post('/milestones/{milestone}/approve', [MilestoneController::class, 'approve'])->middleware('auth');
Route::post('/milestones/{milestone}/revision', [MilestoneController::class, 'requestRevision'])->middleware('auth');
Route::post('/tasks/{task}/dispute', [DisputeController::class, 'store'])->middleware('auth');

// alerts (in-app notifications)
Route::get('/user/alerts/{id}', [AlertController::class, 'index'])->middleware('auth');
Route::post('/user/alerts/{id}/read/{notificationId}', [AlertController::class, 'markRead'])->middleware('auth');
Route::post('/user/alerts/{id}/read-all', [AlertController::class, 'markAllRead'])->middleware('auth');

// stripe connect
Route::get('/connect/onboard', [StripeConnectController::class, 'onboard'])->name('connect.onboard')->middleware('auth');
Route::get('/connect/refresh', [StripeConnectController::class, 'refresh'])->name('connect.refresh')->middleware('auth');

// support
Route::get('/support', [SupportController::class, 'index'])->name('support');
Route::post('/support', [SupportController::class, 'store']);

// admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/gigs', [AdminController::class, 'gigs']);
    Route::get('/disputes', [AdminController::class, 'disputes']);
    Route::get('/support', [AdminController::class, 'support']);
    Route::get('/settings', [AdminController::class, 'settings']);
    Route::post('/settings', [AdminController::class, 'updateSettings']);
    Route::post('/disputes/{dispute}/resolve', [AdminController::class, 'resolveDispute']);
    Route::post('/support/{ticket}/reply', [AdminController::class, 'replyTicket']);
    Route::get('/reviews', [AdminController::class, 'reviews']);
    Route::post('/reviews/{review}/approve', [AdminController::class, 'approveReview']);
    Route::post('/reviews/{review}/deny', [AdminController::class, 'denyReview']);
    Route::put('/reviews/{review}', [AdminController::class, 'updateReview']);
    Route::delete('/reviews/{review}', [AdminController::class, 'destroyReview']);
});

//payment routes
Route::get('/checkout', [App\Http\Controllers\StripeController::class, 'checkout'])->name('checkout')->middleware('auth');
Route::post('/session', [App\Http\Controllers\StripeController::class, 'session'])->name('session')->middleware('auth');
Route::get('/success', [App\Http\Controllers\StripeController::class, 'success'])->name('success')->middleware('auth');
Route::post('/stripe/webhook', [App\Http\Controllers\StripeController::class, 'webhook'])->name('stripe.webhook');






