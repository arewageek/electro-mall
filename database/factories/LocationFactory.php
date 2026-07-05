<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $zone = fake()->randomElement(['A', 'B', 'C', 'D']);
        $aisle = (string) fake()->numberBetween(1, 20);
        $rack = 'R'.fake()->numberBetween(1, 10);
        $shelf = 'S'.fake()->numberBetween(1, 5);
        $bin = 'B'.fake()->numberBetween(1, 5);

        $barcode = "LOC-{$zone}-{$aisle}-{$rack}-{$shelf}-{$bin}";

        return [
            'zone' => $zone,
            'aisle' => $aisle,
            'rack' => $rack,
            'shelf' => $shelf,
            'bin' => $bin,
            'barcode' => $barcode,
        ];
    }
}
