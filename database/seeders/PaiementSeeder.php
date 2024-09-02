<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Paiement;

class PaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Paiement::factory()->count(10)->create();
    }
}
