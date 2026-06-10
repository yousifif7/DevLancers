<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('acc_type');
            $table->string('stripe_connect_id')->nullable()->after('is_admin');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('milestone_id')->nullable()->after('task_id')->constrained()->nullOnDelete();
        });

        if (\Illuminate\Support\Facades\DB::table('users')->exists()) {
            \Illuminate\Support\Facades\DB::table('users')->orderBy('id')->limit(1)->update(['is_admin' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['milestone_id']);
            $table->dropColumn('milestone_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'stripe_connect_id']);
        });
    }
};
