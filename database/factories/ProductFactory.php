<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
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
        $price = $this->faker->numberBetween(100, 5000) * 1000;
        $sale_price = $this->faker->boolean(70) ? $price * $this->faker->numberBetween(50, 95) / 100 : null;

        return [
            'name' => $this->faker->words(3, true) . ' ' . $this->faker->regexify('[A-Z0-9]{3,5}'),
            'slug' => $this->faker->unique()->slug,
            'sku' => $this->faker->unique()->regexify('[A-Z0-9]{8,12}'),
            'price' => $price,
            'sale_price' => $sale_price,
            'short_description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(3, true),
            'thumbnail' => 'https://placehold.co/400x400/2a5298/ffffff.png?text=Product',
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id ?? null,
            'brand_id' => \App\Models\Brand::inRandomOrder()->first()->id ?? null,
            'rating_average' => $this->faker->randomFloat(2, 3, 5),
            'reviews_count' => $this->faker->numberBetween(0, 500),
            'status' => 'active',
            'published_at' => now(),
        ];
    }
}
