<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role as ModelRole;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
     public function run()
     {
         // Insérez les rôles définis dans l'énumération
         ModelRole::create(['name' => Role::BOUTIQUIER()]);
         ModelRole::create(['name' => Role::ADMIN()]);
     }
}
