<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'post_id' => fake()->numberBetween(1, 200),
            'user_id' => fake()->numberBetween(1, 2),
            'body' => fake()->sentences(6, true),
        ];
    }
}
