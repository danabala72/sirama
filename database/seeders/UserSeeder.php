<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'username' => 'admin',
            'email' => null,
            'password' => Hash::make('Qwerty1234'),
            'role_id' => 1,
        ]);

        User::create([
            'id' => 2,
            'username' => '1234567890',
            'email' => null,
            'password' => Hash::make('Qwerty1234'),
            'role_id' => 3,
        ]);
    }
}
