<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('request_id')->nullable()->after('gig_id')->constrained('requests')->nullOnDelete();
            $table->unique('request_id');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['request_id']);
            $table->dropUnique(['request_id']);
            $table->dropColumn('request_id');
        });
    }
};
