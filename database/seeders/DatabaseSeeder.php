<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Category::factory(12)->create()->each(function ($category) {

            Product::factory(10)->create(['category_id' => $category->id])->each(function ($product) {

                ProductVariant::factory(rand(3, 5))->create(['product_id' => $product->id]);
            });
        });
    }
}
