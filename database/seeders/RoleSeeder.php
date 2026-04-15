<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'role' => 'Admin'],
            ['id' => 2, 'role' => 'Asesor'],
            ['id' => 3, 'role' => 'Mahasiswa'],
        ];

        foreach($roles as $role){
            Role::create($role);
        }
    }
}
