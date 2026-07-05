<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Product;
/**
 * @extends Factory<Transaction>
 */
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'product_id' => Product::inRandomOrder()->value('id') ?? Product::factory(),
            'location_id' => Location::inRandomOrder()->value('id') ?? Location::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'type' => $type,
            'quantity' => $quantity,
            'reference' => strtoupper(fake()->bothify('REF-####??')),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
