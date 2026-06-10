<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    public function tasks($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $user = Auth::user();

        if ($user->acc_type == 1) {
            $tasks = Tasks::where('user_id', $user->id)
                ->with(['gig', 'user', 'proposal', 'latestDeliverable', 'milestones'])
                ->latest()
                ->get();

            return view('/tasks/wotasks', ['user' => $user, 'tasks' => $tasks]);
        }

        $tasks = Tasks::where('owner', $user->id)
            ->with(['gig', 'user', 'proposal', 'latestDeliverable', 'milestones'])
            ->latest()
            ->get();

        return view('/tasks/cltasks', ['user' => $user, 'tasks' => $tasks]);
    }
}
