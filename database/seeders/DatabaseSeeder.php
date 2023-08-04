<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Gigs;
use Illuminate\Database\Seeder;
use Database\Factories\GigsFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user= \App\Models\User::factory()->create([
            'name' => 'John Doe',
            'email' => 'email@email.com',
            'password' => '12345678'
        ]);
        Gigs::factory(6)->create([
            'user_id' => $user->id,
        ]);

        // Gigs::create([
        //     'title' => 'Test User',
        //     'tag'=> 'laravel, java',
        //     'description'=>'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. ',
        //     'email' => 'test@example.com',
        // ]);
        // Gigs::create([
        //     'title' => 'Senior developer',
        //     'tag'=> 'laravel, java',
        //     'description'=>'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.  ',
        //     'email' => 'test@example.com',
        // ]);
        // Gigs::create([
        //     'title' => 'Wev developer',
        //     'tag'=> 'laravel, java',
        //     'description'=>'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. ',
        //     'email' => 'test@example.com',
        // ]);
    }
}
