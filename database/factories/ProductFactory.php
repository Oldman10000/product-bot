<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = fake()->unique()->words(2, true);
        $slug = Str::slug($name);
        $description = fake()->sentence();
        // $category_id = Category::factory()->create()->id;

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            // 'category_id' => $category_id,
        ];
    }
}
