<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TeamTeaTime\Forum\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name,
            'description' => fake()->text,
            'accepts_threads' => 1,
            'thread_count' => 0,
            'post_count' => 0,
            'is_private' => 0,
            'color_light_mode' => '#007BFF',
            'color_dark_mode' => '#007BFF',
        ];
    }
}
