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
            'name' => ucfirst(fake()->word()) . ' ' . ucfirst(fake()->word()),
            'price' => fake()->randomFloat(2, 0, 100),
            'stock' => fake()->randomNumber(2),
            'description' => fake()->text(50),
            'image' => fake()->imageUrl(),
            'sku' => strtoupper(fake()->unique()->word()) . fake()->unique()->numberBetween(1000, 9999),
            'barcode' => fake()->unique()->ean13(),

        ];
    }
}
