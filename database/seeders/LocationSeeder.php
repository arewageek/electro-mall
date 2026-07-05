<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 20 distinct warehouse locations if none exist
        if (Location::count() === 0) {
            Location::factory(20)->create();
        }
    }
}
