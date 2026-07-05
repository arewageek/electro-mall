<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . fake()->unique()->bothify('########-????'),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'status' => fake()->randomElement(['pending', 'processing', 'picked', 'shipped', 'delivered', 'cancelled']),
            'total_amount' => fake()->randomFloat(2, 20, 5000),
            'shipping_address' => fake()->address(),
        ];
    }
}
