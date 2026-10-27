<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Courier::create([
            'name' => 'Budiono Hadi Agung',
            'email' => 'budiono.hadi@example.com',
            'phone_number' => '08111223344',
            'vehicle_type' => 'Motorcycle',
            'license_plate' => 'B 1234 ABC',
            'level' => 2,
            'is_active' => true,
        ]);

        \App\Models\Courier::create([
            'name' => 'Budi Sudarsono',
            'email' => 'budi.sudarsono@example.com',
            'phone_number' => '08122334455',
            'vehicle_type' => 'Car',
            'license_plate' => 'B 5678 DEF',
            'level' => 3,
            'is_active' => true,
        ]);

        \App\Models\Courier::factory(18)->create();
    }
}
