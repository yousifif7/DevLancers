<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $defaults = [
            'site_name' => 'DevLancer',
            'site_tagline' => 'Connect with talented freelancers and clients',
            'support_email' => 'support@devlancer.com',
            'allow_registration' => '1',
            'maintenance_mode' => '0',
            'hero_title' => 'Find the perfect freelancer for your project',
            'hero_subtitle' => 'Browse jobs and gigs, submit proposals, and collaborate securely.',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('site_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
