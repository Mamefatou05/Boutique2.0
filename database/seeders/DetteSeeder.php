<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dette;


class DetteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Dette::factory()->count(10)->create();
    }
}
