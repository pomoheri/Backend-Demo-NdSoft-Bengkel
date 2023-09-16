<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CarBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Toyota'],
            ['name' => 'Honda'],
            ['name' => 'Daihatsu'],
            ['name' => 'Mitsubishi'],
            ['name' => 'Nissan'],
            ['name' => 'Suzuki'],
            ['name' => 'Mazda'],
            ['name' => 'Kia'],
            ['name' => 'Hyundai'],
            ['name' => 'Wuling'],
            ['name' => 'DFSK'],
            ['name' => 'Isuzu'],
            ['name' => 'Mercedes-Benz'],
            ['name' => 'BMW'],
            ['name' => 'Audi'],
            ['name' => 'Lexus']
        ];
        foreach ($data as $value) {
            CarBrand::create($value);
        }
    }
}
