<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $locations = Location::all();

        if (Inventory::count() > 0 || $products->isEmpty() || $locations->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            // Assign each product to 1-3 distinct random locations
            $randomLocations = $locations->random(rand(1, 3));

            foreach ($randomLocations as $location) {
                Inventory::factory()->create([
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                ]);
            }
        }
    }
}
