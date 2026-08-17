<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RealisticDataSeeder extends Seeder
{
    public function run()
    {
        // Truncate tables (except users, roles, permissions)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('transactions')->truncate();
        DB::table('inventories')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('purchase_order_items')->truncate();
        DB::table('purchase_orders')->truncate();
        DB::table('products')->truncate();
        DB::table('locations')->truncate();
        DB::table('categories')->truncate();
        DB::table('suppliers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call(CategorySeeder::class);

        // Realistic Suppliers
        $suppliers = [
            'Samsung Electronics',
            'Apple Inc.',
            'Sony Corporation',
            'LG Electronics',
            'Dell Technologies',
            'HP Inc.',
            'Lenovo Group',
            'AsusTek Computer',
        ];

        foreach ($suppliers as $name) {
            Supplier::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)).'@example.com',
                'phone' => '+1 800 '.rand(100, 999).' '.rand(1000, 9999),
                'address' => rand(100, 9999).' Tech Park Blvd, Silicon Valley, CA',
            ]);
        }

        // Realistic Locations
        $zones = ['A', 'B', 'C', 'D'];
        foreach ($zones as $zone) {
            for ($aisle = 1; $aisle <= 5; $aisle++) {
                for ($rack = 1; $rack <= 3; $rack++) {
                    Location::create([
                        'zone' => $zone,
                        'aisle' => (string) $aisle,
                        'rack' => (string) $rack,
                        'barcode' => 'LOC-'.$zone.'-'.$aisle.'-'.$rack,
                    ]);
                }
            }
        }

        $allLocations = Location::all();

        // Realistic Products
        $productsData = [
            'Smartphones' => [
                ['name' => 'iPhone 15 Pro Max 256GB', 'price' => 1199.99, 'supplier' => 'Apple Inc.'],
                ['name' => 'iPhone 14 128GB', 'price' => 799.99, 'supplier' => 'Apple Inc.'],
                ['name' => 'Samsung Galaxy S24 Ultra', 'price' => 1299.99, 'supplier' => 'Samsung Electronics'],
                ['name' => 'Samsung Galaxy A54', 'price' => 449.99, 'supplier' => 'Samsung Electronics'],
                ['name' => 'Sony Xperia 1 V', 'price' => 1399.00, 'supplier' => 'Sony Corporation'],
            ],
            'Laptops & Computers' => [
                ['name' => 'MacBook Pro 16-inch M3 Max', 'price' => 3499.00, 'supplier' => 'Apple Inc.'],
                ['name' => 'MacBook Air M2 256GB', 'price' => 1099.00, 'supplier' => 'Apple Inc.'],
                ['name' => 'Dell XPS 15', 'price' => 1899.99, 'supplier' => 'Dell Technologies'],
                ['name' => 'Lenovo ThinkPad X1 Carbon', 'price' => 1599.00, 'supplier' => 'Lenovo Group'],
                ['name' => 'HP Spectre x360', 'price' => 1399.99, 'supplier' => 'HP Inc.'],
                ['name' => 'ASUS ROG Zephyrus G14', 'price' => 1449.99, 'supplier' => 'AsusTek Computer'],
            ],
            'Televisions' => [
                ['name' => 'Sony BRAVIA XR 65" OLED', 'price' => 2499.99, 'supplier' => 'Sony Corporation'],
                ['name' => 'LG C3 55" 4K Smart OLED', 'price' => 1499.99, 'supplier' => 'LG Electronics'],
                ['name' => 'Samsung 75" Neo QLED 8K', 'price' => 3999.99, 'supplier' => 'Samsung Electronics'],
            ],
            'Audio & Headphones' => [
                ['name' => 'AirPods Pro (2nd Gen)', 'price' => 249.00, 'supplier' => 'Apple Inc.'],
                ['name' => 'Sony WH-1000XM5 Noise Canceling', 'price' => 398.00, 'supplier' => 'Sony Corporation'],
                ['name' => 'Samsung Galaxy Buds 2 Pro', 'price' => 229.99, 'supplier' => 'Samsung Electronics'],
            ],
            'Gaming' => [
                ['name' => 'Sony PlayStation 5 Console', 'price' => 499.99, 'supplier' => 'Sony Corporation'],
                ['name' => 'ASUS ROG Ally Handheld', 'price' => 599.99, 'supplier' => 'AsusTek Computer'],
            ],
        ];

        $receivers = User::role(['receiving', 'manager', 'clerk', 'super_admin'])->get();
        $pickers = User::role(['picker', 'manager', 'clerk', 'super_admin'])->get();

        // Fallback to any user if spatie roles aren't loaded correctly during testing
        $allUsers = User::all();
        if ($receivers->isEmpty()) {
            $receivers = $allUsers;
        }
        if ($pickers->isEmpty()) {
            $pickers = $allUsers;
        }

        foreach ($productsData as $categoryName => $prods) {
            $category = Category::where('name', $categoryName)->first();
            if (! $category) {
                continue;
            }

            foreach ($prods as $p) {
                $supplier = Supplier::where('name', $p['supplier'])->first();
                if (! $supplier) {
                    continue;
                }

                $product = Product::create([
                    'category_id' => $category->id,
                    'supplier_id' => $supplier->id,
                    'name' => $p['name'],
                    'slug' => Str::slug($p['name']),
                    'sku' => strtoupper(Str::random(8)),
                    'barcode' => (string) rand(1000000000000, 9999999999999),
                    'description' => 'A high-quality '.$p['name'].' for everyday use.',
                    'unit_price' => $p['price'],
                ]);

                // Create Inventory & initial Receive transactions
                $stock = rand(10, 100);
                $loc = $allLocations->random();
                $receiver = $receivers->random();

                Inventory::create([
                    'product_id' => $product->id,
                    'location_id' => $loc->id,
                    'quantity' => $stock,
                ]);

                Transaction::create([
                    'product_id' => $product->id,
                    'location_id' => $loc->id,
                    'user_id' => $receiver->id,
                    'type' => 'receive',
                    'quantity' => $stock,
                    'reference' => 'PO-'.rand(1000, 9999),
                    'notes' => 'Initial stock intake',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                // Randomly add a 'pick' transaction to simulate activity
                $pickQty = rand(1, 5);
                if ($stock > $pickQty) {
                    $picker = $pickers->random();
                    Inventory::where('product_id', $product->id)->decrement('quantity', $pickQty);
                    Transaction::create([
                        'product_id' => $product->id,
                        'location_id' => $loc->id,
                        'user_id' => $picker->id,
                        'type' => 'pick',
                        'quantity' => -$pickQty,
                        'reference' => 'ORD-'.rand(1000, 9999),
                        'notes' => 'Order fulfillment',
                        'created_at' => now()->subDays(rand(0, 10)),
                    ]);
                }
            }
        }
    }
}
