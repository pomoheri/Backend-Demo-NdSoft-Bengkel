<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Super Admin', 'username' => 'superadmin', 'email' => 'superadmin@int.com', 'phone' => null, 'address' => null, 'role_id' => 1, 'is_active' => 1, 'password' =>  Hash::make("default123")],
            ['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@int.com', 'phone' => null, 'address' => null, 'role_id' => 2, 'is_active' => 1, 'password' =>  Hash::make("default123")],
            ['name' => 'Admin Sparepart', 'username' => 'sparepart', 'email' => 'sparepart@int.com', 'phone' => null, 'address' => null, 'role_id' => 3, 'is_active' => 1, 'password' =>  Hash::make("default123")],
            ['name' => 'Teknisi', 'username' => 'teknisi', 'email' => 'teknisibengkel@int.com', 'phone' => null, 'address' => null, 'role_id' => 4, 'is_active' => 1, 'password' =>  Hash::make("default123")],
        ];

        foreach ($data as $value) {
            User::create($value);
        }
    }
}
