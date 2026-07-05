<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Transaction;

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
