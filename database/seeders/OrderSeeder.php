<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Order;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Order::count() === 0) {
            // Create 30 Orders, each containing 1 to 4 random line items
            Order::factory(30)
                ->has(OrderItem::factory()->count(rand(1, 4)), 'items')
                ->create();
        }
    }
}
