<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TeamTeaTime\Forum\Models\Post;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thread_id' => ThreadFactory::new(),
            'author_id' => 0,
            'post_id' => null,
            'content' => fake()->text,
        ];
    }
}
