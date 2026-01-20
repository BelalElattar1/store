<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 500),
            'stock' => fake()->numberBetween(0, 100),
            'count_rating' => fake()->numberBetween(1, 5),
            'avg_rating' => fake()->randomFloat(2, 1, 5),
            'discount' => fake()->numberBetween(0, 30),
            'category_id' => \App\Models\Category::factory(),
            'images' => json_encode([fake()->imageUrl(), fake()->imageUrl()]),
        ];
    }
}
