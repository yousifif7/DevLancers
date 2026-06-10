<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->longText('scope_of_work')->nullable()->after('content');
            $table->longText('terms')->nullable()->after('scope_of_work');
            $table->date('start_date')->nullable()->after('terms');
            $table->date('end_date')->nullable()->after('start_date');
            $table->timestamp('worker_accepted_at')->nullable()->after('end_date');
            $table->timestamp('client_accepted_at')->nullable()->after('worker_accepted_at');
        });

        foreach (DB::table('tasks')->get() as $task) {
            $status = match (true) {
                $task->status === 'Done' && !$task->payment_flag => 'cancelled',
                $task->status === 'Done' && $task->payment_flag => 'completed',
                default => 'active',
            };

            DB::table('tasks')->where('id', $task->id)->update([
                'scope_of_work' => $task->content,
                'terms' => 'Standard DevLancer contract terms. Payment upon client approval.',
                'status' => $status,
                'worker_accepted_at' => in_array($status, ['active', 'completed']) ? now() : null,
                'client_accepted_at' => in_array($status, ['active', 'completed']) ? now() : null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'scope_of_work',
                'terms',
                'start_date',
                'end_date',
                'worker_accepted_at',
                'client_accepted_at',
            ]);
        });
    }
};
