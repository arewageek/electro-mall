<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\PurchaseOrder;
/**
 * @extends Factory<PurchaseOrderItem>
 */
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ordered = fake()->numberBetween(10, 100);
        $received = fake()->numberBetween(0, $ordered);

        return [
            'purchase_order_id' => PurchaseOrder::inRandomOrder()->first()?->id ?? PurchaseOrder::factory(),
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'quantity_ordered' => $ordered,
            'quantity_received' => $received,
            'unit_price' => fake()->randomFloat(2, 5, 1000),
        ];
    }
}
