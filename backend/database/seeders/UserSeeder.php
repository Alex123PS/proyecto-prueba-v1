<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin Global
        $super = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@superadmin.com',
            'password' => Hash::make('password12345'),
            'estado' => 'activo',
        ]);
        $super->assignRole('super_admin');

        // computo
        $computo = User::create([
            'name' => 'computo',
            'email' => 'computo@computo.com',
            'password' => Hash::make('passworda'),
            'estado' => 'activo',
        ]);
        $computo->assignRole('computo');

        // secretaria
        $secretaria = User::create([
            'name' => 'secretaria',
            'email' => 'secretaria@secretaria.com',
            'password' => Hash::make('passwordb'),
            'estado' => 'activo',
        ]);
        $secretaria->assignRole('secretaria');
    }
}
