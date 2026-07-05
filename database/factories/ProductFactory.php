<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
use App\Models\Category;
use App\Models\Supplier;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'supplier_id' => Supplier::inRandomOrder()->first()?->id ?? Supplier::factory(),
            'name' => ucwords($name),
            'slug' => str()->slug($name),
            'sku' => 'SKU-' . strtoupper(fake()->bothify('??####')),
            'barcode' => fake()->unique()->ean13(),
            'description' => fake()->paragraph(),
            'unit_price' => fake()->randomFloat(2, 10, 2500),
        ];
    }
}
