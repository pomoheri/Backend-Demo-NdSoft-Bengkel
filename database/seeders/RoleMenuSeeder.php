<?php

namespace Database\Seeders;

use App\Models\RoleMenu;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['role_id' => 1, 'menu_id' => 1],
            ['role_id' => 1, 'menu_id' => 2],
            ['role_id' => 1, 'menu_id' => 3],
            ['role_id' => 1, 'menu_id' => 4],
            ['role_id' => 1, 'menu_id' => 5],
            ['role_id' => 1, 'menu_id' => 6],

            ['role_id' => 2, 'menu_id' => 1],
            ['role_id' => 2, 'menu_id' => 2],

            ['role_id' => 3, 'menu_id' => 1],
            ['role_id' => 3, 'menu_id' => 2],

            ['role_id' => 4, 'menu_id' => 1],
            ['role_id' => 4, 'menu_id' => 2],
        ];

        foreach ($data as $key => $value) {
            RoleMenu::create($value);
        }
    }
}
