<?php

namespace Database\Seeders;

use App\Models\Menus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['parent_id' => null, 'name' => 'Dashboard', 'icon' => 'fa fa-dashboard', 'url' => '/dashboard', 'order' => 1],
            ['parent_id' => null, 'name' => 'Profile', 'icon' => 'fa fa-profile', 'url' => '/profile', 'order' => 2],
            ['parent_id' => null, 'name' => 'Manajemen Pengguna', 'icon' => null, 'url' => null, 'order' => 3],
            ['parent_id' => 3, 'name' => 'Manajemen Role', 'icon' => 'fa fa-tasks', 'url' => '/manajemen-role', 'order' => 1],
            ['parent_id' => 3, 'name' => 'Manajemen Menu', 'icon' => 'fa fa-menus', 'url' => '/manajemen-menus', 'order' => 2],
            ['parent_id' => 3, 'name' => 'Manajemen User', 'icon' => 'fa fa-users', 'url' => '/manajemen-users', 'order' => 3]
        ];

        foreach ($data as $key => $value) {
            Menus::create($value);
        }
    }
}
