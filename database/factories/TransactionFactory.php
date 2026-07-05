<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
use App\Models\Product;
use App\Models\Location;
use App\Models\User;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['receive', 'pick', 'move', 'count_adjustment']);
        $quantity = ($type === 'pick') ? fake()->numberBetween(-100, -1) : fake()->numberBetween(1, 100);
        
        return [
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'location_id' => Location::inRandomOrder()->first()?->id ?? Location::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'type' => $type,
            'quantity' => $quantity,
            'reference' => strtoupper(fake()->bothify('REF-####??')),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
