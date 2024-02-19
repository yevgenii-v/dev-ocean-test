<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\Post;
use Database\Factories\PostFactory;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $this->call(RoleSeeder::class);
//        $this->call(UserSeeder::class);
//        Post::factory(200)->create();

        Comment::factory(1000)->create();
    }
}
