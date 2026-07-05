<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate a history of 200 warehouse transactions if none exist
        if (Transaction::count() === 0) {
            Transaction::factory(200)->create();
        }
    }
}
