<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Electronics',
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'contact_person' => fake()->name(),
            'address' => fake()->address(),
        ];
    }
}
