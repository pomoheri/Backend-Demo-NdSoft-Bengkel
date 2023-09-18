<?php

namespace Database\Seeders;

use App\Models\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Super Admin'],
            ['name' => 'Admin'],
            ['name' => 'Sparepart'],
            ['name' => 'Teknisi Bengkel']
        ];
        foreach ($data as $value) {
            Roles::create($value);
        }
    }
}
