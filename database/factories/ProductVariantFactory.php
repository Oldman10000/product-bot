<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL']),
            'color' => fake()->colorName(),
            'sku' => fake()->unique()->randomNumber(9),
            'price' => fake()->randomFloat(2, 0, 50),
            // 'product_id' => Product::factory()->create()->id,
        ];
    }
}
