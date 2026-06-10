<?php

namespace App\Http\Controllers;

use App\Models\Gigs;
use App\Models\User;
use App\Models\Requests;
use App\Services\UserStatsService;
use App\Support\SeoMeta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('registeration.signup', [
            'seoMeta' => SeoMeta::privatePage('Sign Up', 'Create a DevLancer account as a developer freelancer or client.'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $form=$request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique("users", "email")],
            'password' => ['required ', 'confirmed', 'min:8' ],
            'acc_type' => 'required',
            'gender' => 'required',
        ]);
        $form['password'] = bcrypt($form['password']);

        $user =User::create($form);
        Auth()->login($user);
        return redirect('/')->with('message','User created and logged in succesfully! ');
    }

    //Log user out 
    public function logout(Request $request){
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message','You have been logged out succesfully! ');
        
    }

    //Log user out 
    public function login(){

        return view('registeration.login', [
            'seoMeta' => SeoMeta::privatePage('Log In', 'Sign in to your DevLancer developer freelance account.'),
        ]);
    }

    //Authenticate user to log in
    public function authenticate(Request $request){
        $form=$request->validate([
            'email' => ['required', 'email'],
            'password' => 'required ',
        ]);
       if(auth()->attempt($form)){
            $request->session()->regenerate();

            return redirect('/')->with('message','You have been logged in succesfully! '); 
       }

       return back()->withErrors(['email'=> 'Invalid Email'])->onlyInput('email');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if($user->id != Auth::user()->id){
            abort(404,'Unauthorized action');
        }

        $request->validate([
            'name' => 'required',
            'gender' => 'nullable|string',
            'headline' => 'nullable|string|max:120',
            'skills' => 'nullable|string|max:2000',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'education' => 'nullable|string|max:2000',
            'certifications' => 'nullable|string|max:2000',
            'portfolio_url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'hourly_rate' => 'nullable|numeric|min:0|max:10000',
        ]);

        $user->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'bio' => $request->bio,
            'address' => $request->address,
            'headline' => $request->headline,
            'skills' => $request->skills,
            'experience_years' => $request->experience_years,
            'education' => $request->education,
            'certifications' => $request->certifications,
            'portfolio_url' => $request->portfolio_url,
            'github_url' => $request->github_url,
            'hourly_rate' => $request->hourly_rate,
        ]);
        return redirect('/gigs/profile')->with('message','Profile updated successfully!');
    }
    
    //show user profile
    public function userProfile($id)
    {
        $user = User::findOrFail($id);

        return view('registeration.user', [
            'user' => $user,
            'gigs' => Gigs::where('user_id', $user->id)->withCount('proposals')->latest()->get(),
            'reviews' => $user->reviewsReceived()->approved()->with(['reviewer', 'task'])->latest()->get(),
            'averageRating' => $user->averageRating(),
            'stats' => UserStatsService::for($user),
            'seoMeta' => SeoMeta::user($user, UserStatsService::for($user)),
        ]);
    }

    public function notifications($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $messages = Requests::where('reciever', Auth::id())
            ->with(['gig', 'user', 'task'])
            ->latest()
            ->get();

        return view('messages.notifications', [
            'user' => Auth::user(),
            'messages' => $messages,
        ]);
    }

    public function sent($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $messages = Requests::where('user_id', Auth::id())
            ->with(['gig', 'receiverUser'])
            ->latest()
            ->get();

        return view('messages.sent', [
            'user' => Auth::user(),
            'messages' => $messages,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
