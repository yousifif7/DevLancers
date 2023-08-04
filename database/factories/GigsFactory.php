<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gigs>
 */
class GigsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'=> $this->faker->jobTitle(),
            'tag'=> 'Laravel, API, Backend',
            'description'=> $this->faker->paragraph(),
            'email'=> $this->faker->unique()->email(),
        ];
    }
}
