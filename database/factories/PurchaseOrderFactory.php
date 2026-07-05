<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
/**
 * @extends Factory<PurchaseOrder>
 */
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::inRandomOrder()->value('id') ?? Supplier::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'po_number' => 'PO-'.fake()->unique()->bothify('########-????'),
            'status' => fake()->randomElement(['draft', 'submitted', 'partially_received', 'received', 'cancelled']),
            'expected_delivery_date' => fake()->dateTimeBetween('now', '+1 month'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
