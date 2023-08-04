<?php

use App\Models\Gigs;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GigController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequestsController;

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
// View main page
Route::get('/', [GigController::class, 'index']);

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

//Show profile for each user
Route::get('/gigs/profile', [GigController::class , 'profile'])->middleware('auth');

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

//Redirect to reply page
Route::get('/reply/{msg}', [RequestsController::class , 'submitMsg'])->middleware('auth');

// Delete all messages
Route::post('request/sent/{id}', [RequestsController::class , 'destroyAllSent'])->middleware('auth');
Route::post('request/recieved/{id}', [RequestsController::class , 'destroyAllRecieved'])->middleware('auth');

//Store a gig after creating it
Route::post('/requests', [RequestsController::class , 'store'])->middleware('auth');




