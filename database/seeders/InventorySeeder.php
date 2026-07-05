<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Location;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $locations = Location::all();

        if ($products->isEmpty() || $locations->isEmpty()) {
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
