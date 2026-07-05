<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
 */
use App\Models\Product;
use App\Models\Location;

class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'location_id' => Location::inRandomOrder()->first()?->id ?? Location::factory(),
            'quantity' => fake()->numberBetween(0, 500),
        ];
    }
}
