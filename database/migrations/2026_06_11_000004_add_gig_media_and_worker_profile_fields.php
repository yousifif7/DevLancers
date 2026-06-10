<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gig_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gig_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // image | attachment
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('headline')->nullable()->after('bio');
            $table->text('skills')->nullable()->after('headline');
            $table->unsignedTinyInteger('experience_years')->nullable()->after('skills');
            $table->text('education')->nullable()->after('experience_years');
            $table->text('certifications')->nullable()->after('education');
            $table->string('portfolio_url')->nullable()->after('certifications');
            $table->string('github_url')->nullable()->after('portfolio_url');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('github_url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gig_media');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'headline',
                'skills',
                'experience_years',
                'education',
                'certifications',
                'portfolio_url',
                'github_url',
                'hourly_rate',
            ]);
        });
    }
};
