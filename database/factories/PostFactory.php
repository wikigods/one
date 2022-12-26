<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cover' => null,
            'title' => $title = fake()->sentence(1),
            'slug' => str()->slug($title),
            'excerpt'=> fake()->paragraph(),
            'content'=> fake()->paragraph(),
            'published_at' => null,
            'category_id' => Category::factory()
        ];
    }
}
