<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('proposal_id')->nullable()->after('request_id')->constrained()->nullOnDelete();
            $table->unique('proposal_id');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['proposal_id']);
            $table->dropUnique(['proposal_id']);
            $table->dropColumn('proposal_id');
        });
    }
};
