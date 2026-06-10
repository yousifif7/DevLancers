<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gig_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->longText('cover_letter');
            $table->decimal('bid_amount', 10, 2);
            $table->unsignedInteger('delivery_days');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['gig_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
