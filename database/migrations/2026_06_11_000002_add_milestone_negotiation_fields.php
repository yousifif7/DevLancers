<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('payment_structure')->default('single')->after('price');
            $table->timestamp('milestones_agreed_client_at')->nullable()->after('payment_structure');
            $table->timestamp('milestones_agreed_worker_at')->nullable()->after('milestones_agreed_client_at');
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->foreignId('proposed_by')->nullable()->after('task_id')->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0)->after('proposed_by');
        });
    }

    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropForeign(['proposed_by']);
            $table->dropColumn(['proposed_by', 'sort_order']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'payment_structure',
                'milestones_agreed_client_at',
                'milestones_agreed_worker_at',
            ]);
        });
    }
};
