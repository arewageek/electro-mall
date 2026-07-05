<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Location;
/**
 * @extends Factory<Inventory>
 */
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'product_id' => Product::inRandomOrder()->value('id') ?? Product::factory(),
            'location_id' => Location::inRandomOrder()->value('id') ?? Location::factory(),
            'quantity' => fake()->numberBetween(0, 500),
        ];
    }
}
