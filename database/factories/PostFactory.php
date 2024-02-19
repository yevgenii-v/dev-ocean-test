<?php

namespace Database\Factories;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->sentences(6, true),
            'user_id' => fake()->numberBetween(1, 2),
            'published_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'deleted_at' => null,
        ];
    }
}
