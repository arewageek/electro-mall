<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Smartphones' => 'Mobile phones and accessories.',
            'Laptops & Computers' => 'Notebooks, desktops, and peripherals.',
            'Tablets' => 'iPads, Android tablets, and e-readers.',
            'Home Appliances' => 'Refrigerators, microwaves, washing machines.',
            'Televisions' => 'Smart TVs, OLED, QLED, and home theater systems.',
            'Audio & Headphones' => 'Speakers, soundbars, earbuds, and headphones.',
            'Wearables' => 'Smartwatches and fitness trackers.',
            'Gaming' => 'Consoles, controllers, and gaming accessories.',
            'Networking' => 'Routers, switches, and modems.',
            'Cameras' => 'DSLRs, mirrorless cameras, and lenses.',
        ];

        foreach ($categories as $name => $description) {
            Category::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => str()->slug($name),
                    'description' => $description,
                ]
            );
        }
    }
}
