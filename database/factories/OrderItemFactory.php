<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
use App\Models\Order;
use App\Models\Product;

class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::inRandomOrder()->first()?->id ?? Order::factory(),
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'quantity' => fake()->numberBetween(1, 10),
            'unit_price' => fake()->randomFloat(2, 10, 2000),
        ];
    }
}
