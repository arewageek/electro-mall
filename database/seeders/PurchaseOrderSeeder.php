<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (PurchaseOrder::count() === 0) {
            // Create 15 Purchase Orders, each with 1 to 5 random PO Items attached
            PurchaseOrder::factory(15)
                ->has(PurchaseOrderItem::factory()->count(rand(1, 5)), 'items')
                ->create();
        }
    }
}
